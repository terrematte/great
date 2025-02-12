LIMMAT_DIR = limmat
LIMBOOLE_DIR = limboole-0.2

all: clean-all build-limmat build-limboole start-server

build-limmat:
	cd $(LIMMAT_DIR) &&	CC=gcc ./configure && make && cd ..

build-limboole:
	cd $(LIMBOOLE_DIR) && make && cd ..

start-server:
	clear
	php -S 127.0.0.1:8000
	echo "cabou!!"

clean-all:
	cd $(LIMMAT_DIR) &&	make clean && cd ..
	
	cd $(LIMBOOLE_DIR) && make clean && cd ..

	rm -f limboole
	rm -f limmat/limmat

