

generate-latex: clean-latex
	./default_request
	./test_latex_output_treatement
	./test_latex_conversion

clean-temp-files:
	rm -f *.log
	rm -f *.aux
	rm -f *.tex
	rm -f *.json
	rm -f base64_pdf

clean-latex: clean-temp-files
	rm -f *.pdf

