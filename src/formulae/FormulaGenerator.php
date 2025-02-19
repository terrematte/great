<?php
class FormulaGenerator {
	public $connectives = array();
	public $atoms = array();
	private $connectives_left = 0;
	private $root;
	public function __construct($connectives, $atoms) {
		$this->connectives = $connectives;
		$this->atoms = $atoms;
	}

	public function generateFormula($complexity) {		
		if ($complexity < 0) {
			return;
			//erro aqui ;P
		}
		
		//Gerar!
		$this->connectives_left = $complexity;
		if ($this->connectives_left == 0) $this->root = $this->atoms[array_rand($this->atoms,1)]; //Any atom
		else {
			$this->root = $this->rConnective();
			$this->connectives_left--;
			$this->recursiveGenerator($this->root);
		}
		
		return new Formula($this->root);
	}
	private function recursiveGenerator(&$root) {
		if ($root instanceof Atom) return;
		if ($this->connectives_left == 0) { //Nao há mais conectivos para serem adicionados, então, completar com átomos
			if ($root instanceof Connective) {
				for($i = $root->arity - count($root->children); $i > 0; $i--) {
					$root->children[] = $this->atoms[array_rand($this->atoms,1)];			
				}
			}
		}
		else { //Nao é atomo, e ainda temos conectivos para adicionar
			if ($this->connectives_left > $root->arity) $min = $root->arity;
			else $min = $this->connectives_left;
			$connectives_to_add = rand(1,$min); //Calculando o numero de conectivos que serao adicionados ao conectivo atual nessa chamada
			
			for($i = 0; $i < $connectives_to_add; $i++) { //Adicionando os conectivos
				$x = $this->rConnective();
				$root->children[] = $x;
				$this->connectives_left--;
			}

			for($i = count($root->children); $i < $root->arity; $i++) { //Completando o conectivo atual com atomos
				$root->children[] = $this->atoms[array_rand($this->atoms,1)];	
			}
			
			shuffle($root->children); //Elarmhabando
			
			foreach($root->children as $child) {
				$this->recursiveGenerator($child);
			}
		}
		
	}
	private function rConnective() {
		$i = $this->connectives[rand(0,count($this->connectives)-1)];
		return new Connective($i->infix, $i->functional, $i->polish, $i->arity, $i->order);
	}
}
?>
