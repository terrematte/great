LIMMAT_DIR 			= limmat
LIMBOOLE_DIR 		= limboole-0.2
EXAMPLEREQUEST_DIR 	= example_requests
EXPORTING_DIR	 	= exporting

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
	git push

	PR_URL=$(shell gh pr list --base main --head beta --json url --jq '.[0].url')
	@if [ ! -z "$(PR_URL)" ]; then \
	  echo "Closing existing pull request: $(PR_URL)"; \
	  gh pr close $(PR_URL); \
	fi
	
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
	
