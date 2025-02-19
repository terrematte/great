<?php
abstract class Node {
	public $content;
	public $value;

	public function isAtom() {
		return ($this instanceof Atom);
	}
	
	/* Nao implementado ainda :)
	public function __toString() {
		$t = new WFFTranslator();
		return $t->showFormulaInfix( $this );
	}
	*/
}
?>