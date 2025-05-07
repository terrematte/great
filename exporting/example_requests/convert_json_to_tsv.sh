#!/bin/bash

curl -X POST http://127.0.0.1:8080/exporting/export_formula.php \
     -H "Content-Type: application/json" \
     --data-binary @output.json | jq . > tsv_response.json

if jq -e 'has("source_codes")' tsv_response.json > /dev/null; then
    jq -r '.source_codes[]' tsv_response.json > response.tsv
    echo "tsv source code saved as: exercises_to_tsv.tsv"
else
    echo "Error: 'source_codes' is missing in the response."
fi

rm -f tsv_response.json
rm -f output.json
