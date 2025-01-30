<?php
require_once 'Node.php';
class Atom extends Node {
	function __construct($content) {
		$this->content = $content;
	}
	function toInfixNotation() {
		return $this->content;
	}
	function toFunctionalNotation() {
		return $this->content;
	}
	function toPolishNotation() {
		return $this->content;
	}
}
?>
