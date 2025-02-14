curl -X POST http://127.0.0.1:8000/generate.php -H "Content-Type: application/json" -d '{
    "num_students": 1,
    "num_exercises": 10,
    "num_premises": 2,
    "conectives": ["and", "or", "not", "imp", "biimp"],
    "atoms": ["P", "Q", "R", "S"],
    "compl_min": 2,
    "compl_max": 6,
    "num_valid": 10,
    "num_invalid": 0,
    "restrictions": [
        "same_proportion",
        "must_be_relevant",
        "no_superfluous_premises_allowed",
        "premise_conjunction_must_be_contingent"
    ]
}' | jq . > output.json


