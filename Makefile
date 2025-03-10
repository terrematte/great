PICOSAT_DIR 		= picosat
LIMBOOLE_DIR 		= limboole1.2
EXAMPLEREQUEST_DIR 	= example_requests
EXPORTING_DIR	 	= exporting

all: build-picosat build-limboole start-server

 install-dependences:
 	sudo apt update
 	sudo apt install -y make gcc
 	sudo apt install -y php-cli php-mbstring php-xml php-curl php-zip php-bcmath
 	python -m pip install --upgrade pip
 	pip install -r requirements.txt
 
 update-branch: 
 	make clean-all
 	git add .
 	git commit -m "update branch commit"
 	git push
 
build-picosat:
	cd $(PICOSAT_DIR) && ./configure && make && cd ..

build-limboole:
	cd $(LIMBOOLE_DIR) && ./configure.sh --picosat && make && cd ..

start-server:
	clear
	php -S 127.0.0.1:8000
	echo "cabou!!"

clean-all:
	cd $(PICOSAT_DIR) &&	make clean && cd ..
	
	cd $(LIMBOOLE_DIR) && make clean && cd ..

	cd $(EXAMPLEREQUEST_DIR) && make clean-files && cd ..
	cd $(EXPORTING_DIR) && make clean && cd ..
	
