# Great 

Great is a exercise generator for propositional conjectures.


# How to execute

Requires: make, php, gcc

```
# Da raiz do projeto

# Gerar liblimmat.a
cd limmat
CC=gcc ./configure
make

# Volta a raiz do projeto
cd ..

# Gerar binario do limboole
cd limboole
make

# Volta a raiz do projeto

cd ..

# Executa o projeto

php -S 127.0.0.1:8000
```
