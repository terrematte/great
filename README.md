# GREAT

**GREAT** is an exercise generator for propositional conjectures, proofs in natural deduction, and refutation tasks. It helps automate the creation of logical exercises, assisting in practice and learning.

---

## Prerequisites

- `make` – for building the necessary components.
- `php` – to run the PHP development server.
- `gcc` – the GNU Compiler Collection, used for compiling C code.

---

## Setup Instructions

### 1. **Build the `liblimmat.a` Library**

```bash
cd limmat
CC=gcc ./configure
make
```

### 2. **Build the `limboole` binary**

```bash
cd ../limboole
make
```

### 3. **Start the php server**

```bash
cd ..
php -S 127.0.0.1:8000
```

## Making Requests

Here's a example request you can use to test the functionalities of the server

### 1. Simple exercises generation request
```bash
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
}'
```

Note: It may take a while to execute

### 2. Simple conversion of exercises .json to .tex/.pdf

```bash
jq -r '.pdf_content' response.json > base64_pdf

base64 -d base64_pdf > latex.pdf
```

And if you want only the .tex content:

```bash
jq -r '.tex_content' latex_response.json > response.tex
```
