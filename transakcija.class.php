<?php

class Transakcija
{
	protected $id_kupca, $id_prodavaca, $id_tvrtke, $vrijeme_transakcije, $cijena_dionice, $kolicina_dionica;

	function __construct(  $id_kupca, $id_prodavaca, $id_tvrtke, $vrijeme_transakcije, $cijena_dionice, $kolicina_dionica )
	{
		$this->id_kupca = $id_kupca;
		$this->id_prodavaca = $id_prodavaca;
		$this->id_tvrtke = $id_tvrtke;
		$this->vrijeme_transakcije = $vrijeme_transakcije;
		$this->cijena_dionice = $cijena_dionice;
    $this->kolicina_dionica = $kolicina_dionica;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
