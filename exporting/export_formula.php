<?php
header('Content-Type: application/json');

$valid_types    = ['tex'];

function logAndReturnError($message, $data = null) {
    $logFile = "error_log.txt";

    $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message; // Save to personalized log
    error_log("Error: " . $message);

    if ($data !== null) {
        $logMessage2 = "\n[" . date('Y-m-d H:i:s') . '] JSON Request: ' . json_encode($data);
        $logMessage .= $logMessage2;
        // error_log("Full request data: " . json_encode($data)); // Dont think spamming with error messages in terminal helps
        error_log("Check exporting/error_log.txt file for more details");
    }

    file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND); // Save to personalized log

    die(
        json_encode([
            'status' => 'error', 
            'message' => $message
        ])
    );
}

function escapeLogicalSymbols($text) {
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

function jsonToTex($exercises, $course, $professor, $semester, $code, $registration, $student, $graduate, $titulo) {
    $imports = "
    \\documentclass{lib/unichristusdoc}\n
    \\usepackage{amsmath}\n
    \\usepackage[utf8]{inputenc}\n
    \\usepackage[portuguese]{babel}\n";

    $cabecalho = "
    \\def\\course{" . $course . "}\n
    \\def\\prof{" . $professor . "}\n
    \\def\\semester{" . $semester . "}\n
    \\def\\codeCourse{" . $code . "}\n
    \\def\\registration{" . $registration . "}\n
    \\def\\student{" . $student . "}\n
    \\def\\graduate{" . $graduate . "}\n
    \\def\\theme{" . $titulo . "}\n";

    $instrucoes = "
    \\makeheader\n
    \\fbox{\n
    \\parbox{\\textwidth}{\n
    \\begin{minipage}{\\textwidth}\n
    \\makeinstructions\n
    {\n
    \\begin{instlist}\n
    \\item Preencha o cabeçalho da folha pergunta com seus dados.\n
    \\item Todas as folhas respostas devem conter o nome a a matrícula do aluno.\n
    \\item O preenchimento das respostas deve ser feito utilizando caneta (preta ou azul).\n
    \\end{instlist}\n
    }\n
    \\end{minipage}\n
    }\n
    }\n";
    
    $latexContent  = $imports;
    $latexContent .= $cabecalho;
    $latexContent .= "\\begin{document}\n";
    $latexContent .= $instrucoes;

    # Add $jsonData['exercises']['invalid'] to mess with the students?
    foreach (array_merge($exercises['valid']) as $exercise) {
        $latexContent .= "\\vspace{1cm}";
        $latexContent .= "\\problem ";
        $latexContent .= "Verifique se $ " . escapeLogicalSymbols($exercise['conclusion']) . " $ pode ser concluído partindo das premisas: \n\n";
        foreach ($exercise['premises'] as $premise) {
            $latexContent .= "\\subproblem $ " . escapeLogicalSymbols($premise) . " $\n\n";
        }
    }

    $latexContent .= "\\end{document}";

    return $latexContent;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jsonData = json_decode(file_get_contents('php://input'), true);

    if ($jsonData) {
        // Going to implement soon
        $convert_to_this_type = strtolower($jsonData['convert_to'] ?? 'tex');

        if (!in_array($convert_to_this_type, $valid_types)) {
            logAndReturnError('Invalid conversion type ' . $convert_to_this_type, $jsonData);
        }

        $exercises = $jsonData['exercises'] ?? null;  # OMG I TYPED JsonData aaaarrrrgggg

        if (!is_array($exercises) || empty($exercises)) {
            logAndReturnError('Exercises property is missing or empty ' . $exercises, $jsonData);
        }

        $parameters = $jsonData['parameters'] ?? [];

        // error_log(json_encode($jsonData));

        $course       = $parameters['course'] ?? "Fundamentos de Matemática da Computação IV";
        $professor    = $parameters['professor'] ?? "";
        $semester     = $parameters['semester'] ?? "2025.1";
        $code         = $parameters['code'] ?? "IMD0000";
        $registration = $parameters['registration'] ?? "";
        $list_students= $parameters['list_students'] ?? [""];
        $graduate     = $parameters['graduate'] ?? "Bacharel em Tecnologia da Informação";
        $titulo       = $parameters['titulo'] ?? "Insira o nome aqui";

        // error_log("Parameters" . json_encode($parameters));

        $source_code_of_file    = "";
        $file_name              = "output";
        $compiled_file_name     = 'output.pdf';

        $listof_compiled_pdfs = [];
        $listof_source_code_of_file = [];

        $valid_exercises = $exercises['valid'] ?? [];
        $num_students = count($list_students);
        $exercises_for_student = [];

        if ($num_students > 0) {
            $exercises_per_student  = intdiv(count($valid_exercises), $num_students);
            $remaining_exercises    = count($valid_exercises) % $num_students; 

            $distributed_exercises  = array_slice($valid_exercises, 0, $exercises_per_student * $num_students); // Keep only divisible exercises
            $chunked_exercises      = array_chunk($distributed_exercises, $exercises_per_student);

            foreach ($list_students as $index => $student) {
                $exercises_for_student[$student] = ['valid' => $chunked_exercises[$index] ?? []];
            }
        } else {
            $exercises_for_student[""] = ['valid' => $valid_exercises];
        }

        foreach ($list_students as $student) {
            if ($convert_to_this_type === "tex") {
                // error_log("tex Conversion Type for student: " . $student);
                
                $source_code_of_file = jsonToTex($exercises_for_student[$student], $course, $professor, $semester, $code, $registration, $student, $graduate, $titulo);
                $listof_source_code_of_file[] = $source_code_of_file;
                
                $source_code_file_name = 'exercises_' . $student . '.tex';
                file_put_contents($source_code_file_name, $source_code_of_file);
                
                $compiled_file_name = 'exercises_' . $student . '.pdf';
                exec("pdflatex -interaction=nonstopmode -output-directory=" . escapeshellarg(dirname(__FILE__)) . " -jobname=" . escapeshellarg(pathinfo($compiled_file_name, PATHINFO_FILENAME)) . " " . escapeshellarg($source_code_file_name));
                
                if (file_exists($compiled_file_name)) {
                    // error_log("Compiled PDF found: " . $compiled_file_name);
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
        }        

        if (!empty($listof_compiled_pdfs)) {
            echo json_encode([
                'status'            => 'success',
                'message'           => 'Compiled files generated',
                'compiled_files'    => $listof_compiled_pdfs,
                'source_codes'      => $listof_source_code_of_file
            ]);
        } else {
            echo json_encode([
                'status'    => 'error',
                'message'   => 'Compiled file generation failed for all students'
            ]);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    }

    // error_log("Cleanup");
    $command = "make -C " . escapeshellarg(dirname(__FILE__)) . " clean-latex";
    exec($command, $_, $ret_val);

} else {
    echo json_encode([
        'status'    => 'error', 
        'message'   => 'Only POST requests are allowed'
    ]);
}

?>
