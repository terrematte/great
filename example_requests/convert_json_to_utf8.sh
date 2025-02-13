#!/bin/bash

curl -X POST http://127.0.0.1:8000/exporting/export_formula.php \
     -H "Content-Type: application/json" \
     --data-binary @output.json | jq . > utf8_response.json

if jq -e 'has("source_codes")' utf8_response.json > /dev/null; then
    jq -r '.source_codes[]' utf8_response.json > response.utf8
    echo "UTF-8 source code saved as: exercises_to_utf8.utf8"
else
    echo "Error: 'source_codes' is missing in the response."
fi

rm -f utf8_response.json
rm -f output.json