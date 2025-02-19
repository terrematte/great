echo '{
    "convert_to": "utf8",
    "parameters": {
        "list_students": [],
        "professor": "",
        "course": "",
        "semester": "",
        "code": "",
        "graduate": "",
        "titulo": ""
    }
}' > temp.json

jq -s '.[0] * .[1]' temp.json output.json > temp2.json
mv temp2.json output.json
rm -f temp.json
