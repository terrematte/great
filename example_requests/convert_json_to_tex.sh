#!/bin/bash

curl -X POST http://127.0.0.1:8000/exporting/export_formula.php \
     -H "Content-Type: application/json" \
     --data-binary @output.json | jq . > latex_response.json

if jq -e 'has("compiled_files")' latex_response.json > /dev/null; then
    jq -r '.compiled_files[]' latex_response.json | while read pdf_data; do
        PDF_NAME=$(echo "$pdf_data" | sha256sum | cut -d ' ' -f 1).pdf
        echo "$pdf_data" | base64 --decode > "$PDF_NAME"
        echo "Decoded PDF saved as: $PDF_NAME"
    done
    rm -f output.json
else
    echo "Error: 'compiled_files' is missing in the response."
fi

# Dont need the .tex for now
# if jq -e 'has("source_codes")' latex_response.json > /dev/null; then
#     jq -r '.source_codes' latex_response.json > response.tex
# else
#     echo "Error: 'source_codes' is missing in the response."
# fi

rm -f latex_response.json
