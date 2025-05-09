curl -X POST http://127.0.0.1:8080/generate.php -H "Content-Type: application/json" -d '{
    "code": "Logic",
    "num_exercises": 9,
    "num_students": 3,
    "atoms": [
        "P",
        "Q",
        "R",
				"S"
    ],
    "compl_min": 2,
    "compl_max": 4,
		"num_valid": 9,
    "num_premises": 3,
    "conectives": [
        "not",
        "or",
        "and",
        "imp",
				"biimp"
    ],
    "restrictions": [
        "same_proportion",
        "must_be_relevant",
        "no_superfluous_premises_allowed",
        "premise_conjunction_must_be_contingent",
        "refutable_provable"
    ],
    "course": "Logic"
}' | jq . > output.json
