#!/usr/bin/env bash

RUNS=${1:-10}

for file in ./tests/*.json; do
    filename=$(basename "$file" .json)
    echo "Benchmarking $filename"
    hyperfine --warmup 1 --runs $RUNS \
    --export-markdown "./results/${filename}-result.md" \
    --export-json "./results/${filename}-result.json" \
    --command-name "limmat+limboole" "curl -X POST -H \"Content-Type: application/json\" -d @$file http://localhost:8010/generate.php" \
    --command-name "limmat+picosat" "curl -X POST -H \"Content-Type: application/json\" -d @$file http://localhost:8020/generate.php" \
    --command-name "limmat+lingeling" "curl -X POST -H \"Content-Type: application/json\" -d @$file http://localhost:8030/generate.php"
done
