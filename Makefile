# PICOSAT_DIR 		= /var/www/html/src/sat/picosat
# LIMBOOLE_DIR 		= /var/www/html/src/sat/limboole1.2
PICOSAT_DIR 		= src/sat/picosat
LIMBOOLE_DIR 		= src/sat/limboole1.2
EXPORTING_DIR	 	= exporting
EXAMPLEREQUEST_DIR 	= $(EXPORTING_DIR)/example_requests

LIMBOOLE_PATH      := /var/www/html/src/sat/limboole1.2/limboole
whoami 			   := $(shell whoami)
export LIMBOOLE_PATH

all: build start-server
docker: setup-env setup-docker start-docker
build: build-picosat build-limboole

install-dependences:
	sudo apt update
	sudo apt install -y make gcc
	sudo apt install -y php-cli php-mbstring php-xml php-curl php-zip php-bcmath
	python -m pip install --upgrade pip
	pip install -r requirements.txt

setup-env: 
	-sudo chown -R $(whoami):$(whoami) .
# Não descobri como faz isso funcionar pro Dockerfile. Obs1: eu desisto.
	@echo "Limboole path is: $(LIMBOOLE_PATH)"

setup-docker:
	make clean-all
	sudo docker build -t great-app .
# sudo docker build --build-arg LIMBOOLE_PATH="$(LIMBOOLE_PATH)" -t great-app .

start-docker:
	sudo docker run -d -p 8080:80 --name great-app-container great-app

update-branch: 
	make clean-all
	git add .
	git commit -m "update branch commit"
	git push
 
build-picosat:
	cd $(PICOSAT_DIR) && ./configure && make && cd ..

build-limboole: build-picosat
	cd $(LIMBOOLE_DIR) && ./configure.sh --picosat && make && cd ..

start-server:
	@if ! [ -f src/sat/limboole1.2/limboole ]; then \
		echo "Error: Limboole is not set."; \
		exit 1; \
	fi
	clear
	@php -S 127.0.0.1:8080
	@echo "cabou!!"

clean-all:
	-sudo rm -rf Backups/
	-sudo rm -rf blob_storage/
	-sudo rm -rf Dictionaries/
	-sudo docker stop great-app-container
	-sudo docker rm great-app-container
	-$(MAKE) -C $(PICOSAT_DIR) clean
	-$(MAKE) -C $(LIMBOOLE_DIR) clean
	-$(MAKE) -C $(EXAMPLEREQUEST_DIR) clean-files
	-$(MAKE) -C $(EXPORTING_DIR) clean
	
