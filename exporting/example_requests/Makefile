

generate-latex: clean-files
	./default_request.sh
	./add_tex_parameters.sh
	./convert_json_to_tex.sh
	$(MAKE) clean-temp-files

generate-utf8:
	./default_request.sh
	./add_utf8_parameters.sh
	./convert_json_to_utf8.sh
	$(MAKE) clean-temp-files

clean-temp-files:
	rm -f *.log
	rm -f *.aux
	rm -f *.json
	rm -f base64_pdf

clean-files: clean-temp-files
	rm -f *.pdf
	rm -f *.tex
	rm -f *.utf8
