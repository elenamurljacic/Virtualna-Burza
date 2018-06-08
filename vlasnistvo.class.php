<?php

class Vlasnistvo
{
	protected $id_korisnika, $id_tvrtke, $kolicina_dionica;

	function __construct( $id_korisnika, $id_tvrtke, $kolicina_dionica )
	{
		$this->id_korisnika = $id_korisnika;
		$this->id_tvrtke = $id_tvrtke;
		$this->kolicina_dionica = $kolicina_dionica;

	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }

}

?>
