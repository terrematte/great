<?php
header('Content-Type: application/json');

$valid_types = ['tex', 'utf8'];

function logAndReturnError($message, $data = null) {
    $logFile = "error_log.txt";

    $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    error_log("Error: " . $message);

    if ($data !== null) {
        $logMessage2 = "\n[" . date('Y-m-d H:i:s') . '] JSON Request: ' . json_encode($data);
        $logMessage .= $logMessage2;
        error_log("Check exporting/error_log.txt file for more details");
    }

    file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);

    die(
        json_encode([
            'status' => 'error', 
            'message' => $message
        ])
    );
}

function convertSymbolsToUnicode($text) {
    $replacements = [
        '&'     => '\u2227',  // AND (∧)
        '|'     => '\u2228',  // OR (∨)
        '!'     => '\u00AC',  // NOT (¬)
        '<->'   => '\u21D4',  // Logical biconditional (↔)
        '->'    => '\u2192',  // Logical implication (→)
        '='     => '\u2261',  // Identical (≡)
    ];

    if ($text[0] === '(' && $text[strlen($text) - 1] === ')') {
        $text = substr($text, 1, -1);
    }

    return strtr($text, $replacements);
}

function jsonToUtf8($exercises) {
    $output = "Exercise List\n\n";

    foreach ($exercises['valid'] as $index => $exercise) {
        $output .= "Exercise " . ($index + 1) . ":\n";
        foreach ($exercise['premises'] as $i => $premise) {
            $output .= "  Premise " . ($i + 1) . ": " . convertSymbolsToUnicode($premise) . "\n";
        }
        $output .= "  Conclusion: " . convertSymbolsToUnicode($exercise['conclusion']) . "\n\n";
    }

    return $output;
}

function convertSymbolsToLatex($text) {
    $replacements = [
        '&'     => '\\land',
        '|'     => '\\lor',
        '!'     => '\\neg',
        '<->'   => '\\leftrightarrow',
        '->'    => '\\rightarrow',
        '='     => '\\equiv',
        
        '$'     => '\\$',
        '%'     => '\\%',
        '#'     => '\\#',
        '_'     => '\\_',
        '^'     => '\\^',
        '~'     => '\\sim',
        '<'     => '\\textless',
        '>'     => '\\textgreater',
    ];

    if ($text[0] === '(' && $text[strlen($text) - 1] === ')') {
        $text = substr($text, 1, -1);
    }

    return strtr($text, $replacements);
}

function jsonToTex($exercises, $course, $professor, $semester, $code, $registration, $student, $graduate, $title) {
    $imports = "
    \\documentclass{lib/unichristusdoc}\n
    \\usepackage{amsmath}\n
    \\usepackage[utf8]{inputenc}\n
    \\usepackage[english]{babel}\n";

    $header = "
    \\def\\course{" . $course . "}\n
    \\def\\prof{" . $professor . "}\n
    \\def\\semester{" . $semester . "}\n
    \\def\\codeCourse{" . $code . "}\n
    \\def\\registration{" . $registration . "}\n
    \\def\\student{" . $student . "}\n
    \\def\\graduate{" . $graduate . "}\n
    \\def\\theme{" . $title . "}\n";

    $instructions = "
    \\makeheader\n
    \\fbox{\n
    \\parbox{\\textwidth}{\n
    \\begin{minipage}{\\textwidth}\n
    \\makeinstructions\n
    {\n
    \\begin{instlist}\n
    \\item Fill out the question sheet header with your details.\n
    \\item All answer sheets must contain the student's name and ID.\n
    \\item Answers must be filled out using a pen (black or blue).\n
    \\end{instlist}\n
    }\n
    \\end{minipage}\n
    }\n
    }\n";
    
    $latexContent  = $imports;
    $latexContent .= $header;
    $latexContent .= "\\begin{document}\n";
    $latexContent .= $instructions;

    foreach (array_merge($exercises['valid']) as $exercise) {
        $latexContent .= "\\vspace{1cm}";
        $latexContent .= "\\problem ";
        $latexContent .= "Verify if $ " . convertSymbolsToLatex($exercise['conclusion']) . " $ can be concluded from the premises: \n\n";
        foreach ($exercise['premises'] as $premise) {
            $latexContent .= "\\subproblem $ " . convertSymbolsToLatex($premise) . " $\n\n";
        }
    }

    $latexContent .= "\\end{document}";

    return $latexContent;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jsonData = json_decode(file_get_contents('php://input'), true);

    if ($jsonData) {
        $convert_to_this_type = strtolower($jsonData['convert_to'] ?? 'tex');

        if (!in_array($convert_to_this_type, $valid_types)) {
            logAndReturnError('Invalid conversion type ' . $convert_to_this_type, $jsonData);
        }

        $exercises = $jsonData['exercises'] ?? null;

        if (!is_array($exercises) || empty($exercises)) {
            logAndReturnError('Exercises property is missing or empty', $jsonData);
        }

        $parameters = $jsonData['parameters'] ?? [];

        $course       = $parameters['course'] ?? "Fundamentals of Computer Mathematics IV";
        $professor    = $parameters['professor'] ?? "";
        $semester     = $parameters['semester'] ?? "2025.1";
        $code         = $parameters['code'] ?? "IMD0000";
        $registration = $parameters['registration'] ?? "";
        
        $list_students = $parameters['list_students'] ?? [""];
        if (!is_array($list_students) || empty($list_students) || $list_students === [""]) {
            $list_students = [" "];
        }

        $graduate     = $parameters['graduate'] ?? "Bachelor in Information Technology";
        $title       = $parameters['title'] ?? "Insert title here";

        $listof_compiled_pdfs = [];
        $listof_source_code_of_file = [];

        $valid_exercises = $exercises['valid'] ?? [];
        $num_students = count($list_students);
        $exercises_for_student = [];

        if ($num_students > 0) {
            $exercises_per_student  = intdiv(count($valid_exercises), $num_students);
            $remaining_exercises    = count($valid_exercises) % $num_students; 

            $distributed_exercises  = array_slice($valid_exercises, 0, $exercises_per_student * $num_students);
            $chunked_exercises      = array_chunk($distributed_exercises, $exercises_per_student);

            foreach ($list_students as $index => $student) {
                $exercises_for_student[$student] = ['valid' => $chunked_exercises[$index] ?? []];
            }
        } else {
            $exercises_for_student[""] = ['valid' => $valid_exercises];
        }

        foreach ($list_students as $student) {
            if ($convert_to_this_type === "tex") {
                $source_code_of_file = jsonToTex($exercises_for_student[$student], $course, $professor, $semester, $code, $registration, $student, $graduate, $title);
                $listof_source_code_of_file[] = $source_code_of_file;
                
                $source_code_file_name = 'exercises_' . $student . '.tex';
                file_put_contents($source_code_file_name, $source_code_of_file);
                
                $compiled_file_name = 'exercises_' . $student . '.pdf';
                exec("pdflatex -interaction=nonstopmode -output-directory=" . escapeshellarg(dirname(__FILE__)) . " -jobname=" . escapeshellarg(pathinfo($compiled_file_name, PATHINFO_FILENAME)) . " " . escapeshellarg($source_code_file_name));
                
                if (file_exists($compiled_file_name)) {
                    $compiled_base64_content = base64_encode(file_get_contents($compiled_file_name));
                    
                    $padding = strlen($compiled_base64_content) % 4;
                    if ($padding > 0) {
                        $compiled_base64_content .= str_repeat('=', 4 - $padding);
                    }
                    
                    $listof_compiled_pdfs[] = $compiled_base64_content;
                } else {
                    $listof_compiled_pdfs[] = null;
                }
            }
            elseif ($convert_to_this_type === "utf8") {
                $listof_source_code_of_file[] = jsonToUtf8($exercises_for_student[$student]);
            }
        }        

        if (!empty($listof_compiled_pdfs)) {
            echo json_encode([
                'status'            => 'success',
                'message'           => 'Compiled files generated',
                'compiled_files'    => $listof_compiled_pdfs,
                'source_codes'      => $listof_source_code_of_file
            ]);
        }   
        elseif (!empty($listof_source_code_of_file)) {
            echo json_encode([
                'status'            => 'success',
                'message'           => 'Source codes generated',
                'compiled_files'    => [],
                'source_codes'      => $listof_source_code_of_file
            ]);
        }   
        else {
            echo json_encode([
                'status'    => 'error',
                'message'   => 'Compiled file generation failed for all students'
            ]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    }

    $command = "make -C " . escapeshellarg(dirname(__FILE__)) . " clean";
    exec($command, $_, $ret_val);
} else {
    echo json_encode([
        'status'    => 'error', 
        'message'   => 'Only POST requests are allowed'
    ]);
}
?>
