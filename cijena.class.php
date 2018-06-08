<?php

class Cijena
{
	protected $id_tvrtke, $vrijeme_promjene, $cijena;

	function __construct( $id_tvrtke, $vrijeme_promjene, $cijena )
	{
		$this->id_tvrtke = $id_tvrtke;
		$this->vrijeme_promjene = $vrijeme_promjene;
		$this->cijena = $cijena;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
