echo '{
    "convert_to": "tex",
    "parameters": {
        "list_students": ["Hilbert", "Gentzen", "Newton"],
        "professor": "Aristotles",
        "course": "Introduction to Logic",
        "semester": "2025.1",
        "code": "SBL101",
        "graduate": "Logic",
        "titulo": "Homework 1"
    }
}' > temp.json

jq -s '.[0] * .[1]' temp.json output.json > temp2.json
mv temp2.json output.json
rm -f temp.json
