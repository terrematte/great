curl -X POST http://127.0.0.1:8000/generate.php -H "Content-Type: application/json" -d '{
    "num_students": 3,
    "num_exercises": 11,
    "num_premises": 3,
    "conectives": ["and", "or"],
    "atoms": ["P", "Q"],
    "compl_min": 2,
    "compl_max": 5,
    "num_valid": 11,
    "num_invalid": 0
}' | jq . > output.json


