<?php

class Tvrtka
{
	protected $id_tvrtke, $naziv_tvrtke, $kapital_tvrtke, $cijena_dionice;

	function __construct( $id_tvrtke, $naziv_tvrtke, $kapital_tvrtke, $cijena_dionice)
	{
		$this->id_tvrtke = $id_tvrtke;
		$this->naziv_tvrtke = $naziv_tvrtke;
		$this->kapital_tvrtke = $kapital_tvrtke;
		$this->cijena_dionice = $cijena_dionice;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }

}

?>
