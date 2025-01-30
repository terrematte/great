<?php

ini_set('max_execution_time', 5000); 

require_once 'formulae/Atom.php';
require_once 'formulae/Node.php';
require_once 'formulae/Connective.php';
require_once 'formulae/Formula.php';
require_once 'formulae/FormulaGenerator.php';
require_once 'formulae/FormulaChecker.php';


function sat($formula) {
    $formula = escapeshellarg($formula);
    $output = `echo $formula | ./limboole -s`;
    return preg_match('/^% SATISFIABLE/', $output) >= 1;
}

function follows($premises, $conclusion) {
    $s = implode('&', $premises) . '&!' . $conclusion;
    return ! sat($s);
}

function relevant($premises, $conclusion) {
	$premise_atoms = array();
	$conclusion_atoms = array();
	$numRelevPrems=0;
	$pattern = '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/'; //Regular expression to check a valid "variable name"
	
	preg_match_all($pattern, $conclusion, $conclusion_atoms); 

	
	foreach ($premises as $key => $premise ) {
		$isRelevant=false;
		
		preg_match_all($pattern, $premise, $premise_atoms); 
		
		foreach ($premise_atoms[0] as $k => $p_atoms) { 
			
			if (in_array($p_atoms, $conclusion_atoms[0])) {
				$isRelevant=true;
			}	
		}
		
		if ( $isRelevant == true){
			$numRelevPrems++;
		}
	}

	if ($numRelevPrems < sizeof($premises)) {
		return false;
	} else {
		return true;
	}
}

function valid($formula) {
    $formula = escapeshellarg($formula);
    $output = `echo $formula | ./limboole`;
    return preg_match('/^% VALID/', $output) >= 1; 
}

function contingency($formula) {
    return (boolean) sat($formula) && (! valid($formula));
}

function list_contingency($formulas) {
    $s = implode('&', $formulas);
    return contingency($s);
}

function superfluous($p, $premises, $conclusion) {
    $premises_without_p = array_filter($premises, function ($q) use ($p) {
            return $q !== $p;
        });

    return follows($premises_without_p, $conclusion);
}

function has_superfluous_premises($exercise) {
    if (!$exercise['is_valid'])
        return false;

    foreach($exercise['premises'] as $p)
        if (superfluous($p, $exercise['premises'], $exercise['conclusion']))
            return true;

    return false;
}

function new_exercise($num_premises, $new_formula) {
    $exercise['premises'] = array();
    for ($i = 0; $i < $num_premises; $i++)
        array_push($exercise['premises'], $new_formula());

    $exercise['conclusion'] = $new_formula();

    $exercise['is_valid'] = follows($exercise['premises'], $exercise['conclusion']);

    return $exercise;
}

function generate_exercises($num_valid, $num_invalid, $num_premises, $exercise_is_fit, $new_formula) {
    $valid = array();
    $invalid = array();
    $discarded_valid = array();
    $discarded_invalid = array();

    $discarded_not_fit = array();

    while (count($valid) < $num_valid || count($invalid) < $num_invalid) {
        $exercise = new_exercise($num_premises, $new_formula);

        while (!$exercise_is_fit($exercise)) {
            array_push($discarded_not_fit, $exercise);
            $exercise = new_exercise($num_premises, $new_formula);
        }

        if ($exercise['is_valid']) {
            if(count($valid) < $num_valid) array_push($valid, $exercise);
            else array_push($discarded_valid, $exercise);
        }
        else {
            if(count($invalid) < $num_invalid) array_push($invalid, $exercise);
            else array_push($discarded_invalid, $exercise);
        }
    }

    $exercises = array('valid' => $valid,
                       'invalid' => $invalid,
                       'discarded_valid' => $discarded_valid,
                       'discarded_invalid' => $discarded_invalid,
                       'discarded_not_fit' => $discarded_not_fit);

    $stats = array('num_gen_valid' => count($valid),
                   'num_gen_invalid' => count($invalid),
                   'num_discarded_valid' => count($discarded_valid),
                   'num_discarded_invalid' => count($discarded_invalid),
                   'num_discarded_not_fit' => count($discarded_not_fit),
                   'num_req_valid' => $num_valid,
                   'num_req_invalid' => $num_invalid);

    $stats['num_gen'] = $res['num_gen_valid'] + $res['num_gen_invalid'];
    $stats['num_discarded'] = $res['num_discarded_valid'] + $res['num_discarded_invalid'] + $res['num_discarded_not_fit'];
    $stats['num_req'] = $res['num_req_valid'] + $res['num_req_invalid'];

    return array('exercises' => $exercises, 'stats' => $stats);
}

function gen($params) {
    $total = intval($params['num_exercises']);

    $cutoff = rand(0, $total);
    $no_superfluous_premises_allowed = false;
    $must_be_relevant = false;
    $premise_conjunction_must_be_contingent = false;

    foreach ($params['restrictions'] as $restr) {
        if ($restr === 'same_proportion')
            $cutoff = floor($total / 2);
        elseif ($restr === 'no_superfluous_premises_allowed')
            $no_superfluous_premises_allowed = true;
        elseif ($restr === 'must_be_relevant')
            $must_be_relevant = true;

        elseif ($restr === 'premise_conjunction_must_be_contingent')
            $premise_conjunction_must_be_contingent = true;
            
        elseif ($restr === 'refutable_provable'){
			$refutable_provable = true;
			$only_refutable = false; 
			$only_provable = false;  
			$num_valid = $cutoff;
			$num_invalid = $total - $cutoff;
		}
        elseif ($restr === 'only_refutable'){
			$refutable_provable = false;
			$only_refutable = true; 
			$only_provable = false;      
			$num_valid = 0;
			$num_invalid = $total;
		}                   
        elseif ($restr === 'only_provable'){
			$refutable_provable = false;
			$only_refutable = false; 
			$only_provable = true;           
			$num_valid = $total;
			$num_invalid = 0;
		}
    }

   


    $num_premises = $params['num_premises'];


    $conectives = array();

    foreach ($params['conectives'] as $con) {
        if ($con === 'and')
            array_push($conectives, new Connective("&","and","K",2,0));
        elseif ($con === 'or')
            array_push($conectives, new Connective("|","or","A",2,0));
        elseif ($con === 'imp')
            array_push($conectives, new Connective("->","imp","C",2,0));
        elseif ($con === 'biimp')
            array_push($conectives, new Connective("<->","biimp","B",2,0));
        elseif ($con === 'not')
            array_push($conectives, new Connective("!","not","N",1,0));
    }

    $atoms = array();

    foreach ($params['atoms'] as $atm) {
        array_push($atoms, new Atom($atm));
    }

    $fgenerator = new FormulaGenerator($conectives, $atoms);

    $compl_min = intval($params['compl_min']);
    $compl_max = intval($params['compl_max']);



    return generate_exercises(
        $num_valid, $num_invalid, $num_premises,
        function ($exercise) use($no_superfluous_premises_allowed, $must_be_relevant, $premise_conjunction_must_be_contingent) {
            if ($no_superfluous_premises_allowed && has_superfluous_premises($exercise))
                return false;

            if ($must_be_relevant && !relevant($exercise['premises'], $exercise['conclusion']))
                return false;

            if ($premise_conjunction_must_be_contingent && !list_contingency($exercise['premises']))
                return false;

            return true;
        },
        function () use ($fgenerator, $compl_min, $compl_max) {
            $complex = rand($compl_min, $compl_max);
            $formula = $fgenerator->generateFormula($complex)->toInfixNotation(); 

            while (contingency($formula) !=  1):
                $formula = $fgenerator->generateFormula($complex)->toInfixNotation(); 
            endwhile;

            return $formula; 
        });
}

set_time_limit(0);

$handle = fopen('php://input','r');
$jsonInput = fgets($handle);
$decoded = json_decode($jsonInput,true);

$time_a = microtime(true);

$ex = gen($decoded);

$time_b = microtime(true);

$response = array();

$response['exercises'] = $ex['exercises'];
$response['stats'] = $ex['stats'];
$response['request'] = $decoded;

$response['time'] = $time_b - $time_a;

die(json_encode($response));

?>

