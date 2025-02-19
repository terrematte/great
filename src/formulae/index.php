<html >
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type">
<title>Testando php (:</title>
<style type="text/css">
body{font-family:"Consolas",Times,serif;font-size:13}
</style>
</head>
<body>
<?php


require_once 'Atom.php';
require_once 'Node.php';
require_once 'Connective.php';
require_once 'Formula.php';
require_once 'FormulaGenerator.php';
require_once 'FormulaChecker.php';



echo "<h2> Fórmulas na sintaxe do ProofWeb</h2>";

	

$p1 = new Atom("A");
$p2 = new Atom("B");
$p3 = new Atom("C");
$and = new Connective(" /\ ","and","K",2,0);
$or = new Connective(" \/ ","or","A",2,0);
$imp = new Connective("->","imp","C",2,0);
$bii = new Connective("<->","bii","B",2,0);
$not = new Connective("~","not","N",1,0);

$fgenerator = new FormulaGenerator(array($and,$or,$imp,$not), array($p1,$p2,$p3));
$numero_formulas = 20;

$complexity_min = 4;
$complexity_max = 4;

$min_symbols = 3;
$max_symbols = 6;

	
for ($i = 0; $i < $numero_formulas; $i++) {
	echo "<br/>";
	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max));
	echo $formula->toInfixNotation();
}


echo "<br/><br/><br/>";

echo "<h2> Fórmulas na sintaxe do Limboole</h2>";


$p0 = new Atom("o");
$p1 = new Atom("p");
$p2 = new Atom("q");
$p3 = new Atom("r");
$p4 = new Atom("s");
$p5 = new Atom("t");
$p6 = new Atom("u");
$p6 = new Atom("v");
$p8 = new Atom("x");
$p9 = new Atom("z");
$and = new Connective("&","and","K",2,0);
$or = new Connective("|","or","A",2,0);
$imp = new Connective("->","imp","C",2,0);
$imp = new Connective("<->","bii","B",2,0);
$not = new Connective("!","not","N",1,0);


$fgenerator = new FormulaGenerator(array($and,$or,$imp,$not), array($p0,$p1,$p2,$p3,$p4,$p5,$p6));
$numero_formulas = 20;

$complexity_min = 25;
$complexity_max = 30;


for ($i = 0; $i < $numero_formulas; $i++) {
	echo "<br/>";
	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max));
	echo $formula->toInfixNotation();
}


echo "<br/><br/><br/>";

echo "<h2> Fórmulas em Latex</h2>";



$p1 = new Atom("\alpha");
$p2 = new Atom("\beta");
$p3 = new Atom("\gamma");

$and = new Connective("\land","and","K",2,0);
$or = new Connective("\\vee ","or","A",2,0);
$imp = new Connective("\to","imp","C",2,0);
$imp = new Connective("\leftrightarrow","bii","B",2,0);
$not = new Connective("\\neg","not","N",1,0);


$fgenerator = new FormulaGenerator(array($and,$or,$imp,$not), array($p1,$p2,$p3));
$numero_formulas = 20;

$complexity_min = 4;
$complexity_max = 4;


	
for ($i = 0; $i < $numero_formulas; $i++) {
	echo "<br/>";
	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max));
	echo $formula->toInfixNotation();
}


?>




</body>
</html>


