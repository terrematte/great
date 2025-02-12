<?php
header('Content-Type: application/json');

function escapeLogicalSymbols($text) {
    $replacements = [
        '&' => '\\land',
        '|' => '\\lor',
        '!' => '\\neg',
        '<->' => '\\leftrightarrow',
        '->' => '\\rightarrow',
        '=' => '\\equiv',
        
        '$' => '\\$',
        '%' => '\\%',
        '#' => '\\#',
        '_' => '\\_',
        '^' => '\\^',
        '~' => '\\sim',
        '<' => '\\textless',
        '>' => '\\textgreater',
    ];

    if ($text[0] === '(' && $text[strlen($text) - 1] === ')') {
        $text = substr($text, 1, -1);
    }

    return strtr($text, $replacements);
}

function jsonToTex($jsonData) {
    $imports = "
    \\documentclass{lib/unichristusdoc}\n
    \\usepackage{amsmath}\n
    \\usepackage[utf8]{inputenc}\n
    \\usepackage[portuguese]{babel}\n";

    $cabecalho = "
    \\def\\course{Fundamentos de Matemática da Computação III}\n
    \\def\\prof{John Smith}\n
    \\def\\semester{2025.1}\n
    \\def\\codeCourse{IMD1234}\n
    \\def\\registration{}\n
    \\def\\student{}\n
    \\def\\graduate{Tecnologia da Informação}\n
    \\def\\theme{Lista de exercícios 1}\n";

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
    foreach (array_merge($jsonData['exercises']['valid']) as $exercise) {
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
        $course     = $jsonData['course'] ?? "Fundamentos de Matemática da Computação IV"
        $professor  = $jsonData['professor'] ?? ""
        $semester   = $jsonData['semester'] ?? "2025.1"
        $code       = $jsonData['code'] ?? "IMD0000"
        $graduate   = $jsonData['graduate'] ?? "Bacharel em Tecnologia da Informação"
        $titulo     = $jsonData['titulo'] ?? "Insira o nome aqui"

        $latexContent = jsonToTex($jsonData);

        $texFileName = 'exercises.tex';
        file_put_contents($texFileName, $latexContent);

        $outputFile = 'exercises.pdf';
        exec("pdflatex -interaction=nonstopmode -output-directory=" . dirname(__FILE__) . " " . $texFileName);

        if (file_exists($outputFile)) {
            $pdfContent = base64_encode(file_get_contents($outputFile));
            echo json_encode(['status' => 'success', 'message' => 'PDF generated', 'pdf_content' => $pdfContent, 'tex_content' => $latexContent]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'PDF generation failed']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Only POST requests are allowed']);
}
?>
