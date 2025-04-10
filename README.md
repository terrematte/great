# GREAT

**GREAT** is an exercise generator for propositional conjectures, proofs in natural deduction, and refutation tasks. It helps automate the creation of logical exercises, assisting in practice and learning.

---

## Prerequisites

- `make` – for building the necessary components.
- `php` – to run the PHP development server.
- `gcc` – the GNU Compiler Collection, used for compiling C code.
- `docker` – easier deploy. 

Solve it with `make install-dependences`

---

## Setup Instructions - `Docker`

### 0. **Do it all with a single command**
```bash
make docker
```

### 1. **Build `great-app` docker image**
```bash
docker build -t great-app .
```

It'll cleanup the folders then execute the setup/buildup routines below:

### 2. **Run the docker in `great-app-container`**
```bash
docker run -d -p 8080:80 --name great-app-container great-app
```

### 3. **`Stopping` and `cleaning` files**
```bash
docker stop great-app-container
docker rm great-app-container
```

---

## Setup Instructions - `Local Server`

### 0. **Do it all with a single command**
```bash
make
```

### 1. **Build the `picosat` binary**

```bash
cd src/sat/picosat
CC=gcc ./configure
make
cd ../../..
```

### 2. **Build the `limboole1.2` binary**
```bash
cd src/sat/limboole1.2
make
cd ../../..
```

### 3. **Start the `php` server**

```bash
php -S 127.0.0.1:8000
```

---

## `Cleanup` and `Interaction`

### Cleanse
```bash
make clean-all
```
Includes docker cleanup commands too.

### UI
```
http://localhost:8080/index.html
```

### Exercises
```
http://localhost:8080/generate.php
```

---

## Making Requests

Some scripts with default POST requests can be found at exporting/example_requests/, and some of them are:

### 1. Simple exercises generation request
```bash
curl -X POST http://127.0.0.1:8080/generate.php \
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
}'
```

Note: It may take a while to execute

### 2. Simple conversion of exercises .json to .tex/.pdf

```bash
curl -X POST http://127.0.0.1:8080/exporting/export_formula.php \
     -H "Content-Type: application/json" \
     --data-binary @exercises.json | jq . > latex_response.json

jq -r '.pdf_content' latex_response.json > base64_pdf

base64 -d base64_pdf > latex.pdf
```

The server compile the .pdf internally and if you want only the .tex content:

```bash
jq -r '.tex_content' latex_response.json > response.tex
```




test
