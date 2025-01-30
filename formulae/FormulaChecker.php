<?php
class FormulaChecker {
	private $connectives;
	public $connectiveInfixPattern;
	public $connectiveFunctionalPattern;
	public function __construct($connectives) {
		$this->connectives = $connectives;
		$this->connectiveInfixPattern = "(";
		$this->connectiveFunctionalPattern = "(";
		$i = count($connectives);
		foreach ($connectives as $op) {
			
			$i--;
			
			/*
			$op->infix =
				str_replace(array(".","+","^","$","?","*","|"),array("\\.","\\+","\\^","\\$","\\?","\\*","\\|"),$op->infix);
			$this->connectiveInfixPattern .= $op->infix;
			if ($i != 0) $this->connectiveInfixPattern .= "|";
			else $this->connectiveInfixPattern .= ")";
			$op->children = array();
			 */
			
			if ($i != 0) $this->connectiveFunctionalPattern .= $op->functional . "|";
			else $this->connectiveFunctionalPattern .= ")";
			
		}
	}
	public function getInfixFormula($string) {
		$id = "[a-zA-Z_][a-zA-Z0-9_]+";
		$oper = $this->connectiveInfixPattern;
		$anyformula =  "(\\(.*?\\)|$id)";
		if (preg_match("/^$id$/",$string)) return Atom($string);
		if (preg_match("/\\(.*?$oper$anyformula\\)/",$string,$matches)) {
			//Array ( [0] => ((p3^p3)&p2&(p3->p4)) [1] => & [2] => (p3->p4) )
			$root = $this->getInfixConnective($matches[1]);
			$operandos = preg_split("/$matches[1]/",substr($matches[0],1,-1));
			print_r($operandos);
		}
	}
	
	public function getFunctionalFormula($string) {
		$id = "[a-zA-Z_][a-zA-Z0-9_]+"; //regular expression to IDs
		$func = $this->connectiveFunctionalPattern; //regular expression to functions
		
		if (preg_match("/^$id$/",$string)) return Atom($string); //base case
		if (preg_match("/^$func\\((.*?)\\)$/",$string,$matches)) {
			print_r($matches);
		} 
	}
	private function getInfixConnective($infixn) {
		foreach ($this->connectives as $con) {
			if ($con->infix == $infixn) return $con;
		}
		throw new Exception("Conectivo desconhecido: $infixn");
	}
}
?>