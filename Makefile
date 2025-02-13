LIMMAT_DIR 			= limmat
LIMBOOLE_DIR 		= limboole-0.2
EXAMPLEREQUEST_DIR 	= example_requests
EXPORTING_DIR	 	= exporting
#potatoalan
all: clean-all build-limmat build-limboole start-server

update-main: 
	git switch main
	make clean-all
	git add .
	git commit -m "update branch commit"
	git push

pr-beta-main:
	git switch beta
	git commit --allow-empty -m "Test empty commit"
	gh pr create --base main --head beta --title "Merge beta into main" --body "Automated pull request from Makefile"
	git switch main

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
	
