<?php


class Korisnik
{
	protected $id_korisnika, $username, $ime, $prezime, $kapital_korisnika, $email;

	function __construct( $id, $username, $ime, $prezime, $email, $kapital )
	{
		$this->id_korisnika = $id;
		$this->username = $username;
    $this->ime = $ime;
    $this->prezime = $prezime;
		$this->email = $email;
    $this->kapital_korisnika = $kapital;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
