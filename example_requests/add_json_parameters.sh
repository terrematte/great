echo '{
    "convert_to": "tex",
    "parameters": {
        "list_students": ["Mario", "Luigy"],
        "professor": "John Smith the Third",
        "course": "Introduction to Logic",
        "semester": "2025.1",
        "code": "IMD1324",
        "graduate": "Tecnologia da Informação",
        "titulo": "Lista de exercícios 1"
    }
}' > temp.json

jq -s '.[0] * .[1]' temp.json output.json > temp2.json
mv temp2.json output.json
rm -f temp.json
