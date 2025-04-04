LIMBOOLE_BACKEND = lingeling
LIMBOOLE_BACKEND_DIR  = src/sat/lingeling
LIMBOOLE_DIR 		= src/sat/limboole1.2
EXPORTING_DIR	 	= exporting
EXAMPLEREQUEST_DIR 	= $(EXPORTING_DIR)/example_requests

build: build-limboole-backend build-limboole
all: build-limboole-backend build-limboole start-server

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
 
build-limboole-backend:
	cd $(LIMBOOLE_BACKEND_DIR) && ./configure.sh && make && cd ..

build-limboole: build-limboole-backend
	cd $(LIMBOOLE_DIR) && ./configure.sh --$(LIMBOOLE_BACKEND) && make && cd ..

start-server:
	clear
	php -S 127.0.0.1:8000
	echo "cabou!!"

clean-all:
	-$(MAKE) -C $(PICOSAT_DIR) clean
	-$(MAKE) -C $(LIMBOOLE_DIR) clean
	-$(MAKE) -C $(EXAMPLEREQUEST_DIR) clean-files
	-$(MAKE) -C $(EXPORTING_DIR) clean
	
