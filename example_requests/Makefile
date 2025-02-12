

generate-latex: clean-latex
	./default_request.sh
	./test_latex_output_treatement.sh
	./test_latex_conversion.sh
	$(MAKE) clean-temp-files

clean-temp-files:
	rm -f *.log
	rm -f *.aux
	rm -f *.json
	rm -f base64_pdf

clean-latex: clean-temp-files
	rm -f *.pdf
	rm -f *.tex
