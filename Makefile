LIMMAT_DIR 			= limmat
LIMBOOLE_DIR 		= limboole-0.2
EXAMPLEREQUEST_DIR 	= example_requests
EXPORTING_DIR	 	= exporting

all: clean-all build-limmat build-limboole start-server

update-branch: 
	make clean-all
	git add .
	git commit -m "update branch commit"
	git push

install-dependences:
	sudo apt update
	sudo apt install -y make gcc
	sudo apt install -y php-cli php-mbstring php-xml php-curl php-zip php-bcmath
	python -m pip install --upgrade pip
	pip install -r requirements.txt

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

	cd $(EXAMPLEREQUEST_DIR) && make clean-files && cd ..
	cd $(EXPORTING_DIR) && make clean && cd ..
	
