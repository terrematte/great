curl -X POST http://127.0.0.1:8000/generate.php \
-H "Content-Type: application/json" \
-d '{
    "num_exercises": 3,
    "num_premises": 3,
    "conectives": ["and", "or"],
    "atoms": ["P", "Q"],
    "compl_min": 2,
    "compl_max": 3,
    "num_valid": 2,
    "num_invalid": 1,
    "restrictions": ["no_superfluous_premises_allowed"]
}' | jq . > output.txt
