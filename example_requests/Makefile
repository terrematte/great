

generate-latex: clean-latex
	./default_request.sh
	./add_json_parameters.sh
	./convert_json_to_tex.sh
	$(MAKE) clean-temp-files

clean-temp-files:
	rm -f *.log
	rm -f *.aux
	rm -f *.json
	rm -f base64_pdf

clean-latex: clean-temp-files
	rm -f *.pdf
	rm -f *.tex
