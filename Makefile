LIMMAT_DIR 		= src/sat/limmat
LIMBOOLE_DIR 		= src/sat/limboole-0.2
EXAMPLEREQUEST_DIR 	= example_requests
EXPORTING_DIR	 	= exporting

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

	rm -f src/sat/limboole
	rm -f src/sat/limmat/limmat

	cd $(EXAMPLEREQUEST_DIR) && make clean-files && cd ..
	cd $(EXPORTING_DIR) && make clean && cd ..
	
