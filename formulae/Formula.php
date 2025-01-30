<?php
require_once 'Node.php';
class Formula {
	public $root;
	function __construct($root) {
		$this->root = $root;

		// Verificar inconsistencias na formula :)
		// $this->verifyConsistence();
	}
	public function toInfixNotation() {
		return $this->root->toInfixNotation();
	}
	public function toFunctionalNotation() {
		return $this->root->toFunctionalNotation();
	}
	public function verifyWFF() {
		return $this->verifyWFFRec($this->root);
	}
	private function verifyWFFRec($root) {
		if ($root instanceof Atom) return true;
		$i = count($root->children);
		if ($i != $root->arity) return false;
		foreach ($root->children as $child) {
			if (!$this->verifyWFFRec($child)) {
				return false;
			}
		}
		return true;
	}
}
?>