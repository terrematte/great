clean: TYPES_TO_REMOVE = aux log pdf tex utf8 tsv

clean:
	find . -type f \( $(foreach ext,$(TYPES_TO_REMOVE), -name "*.$(ext)" -o) -false \) ! -name "error_log.log" -exec rm -f {} \;
	# Potatoes are brown