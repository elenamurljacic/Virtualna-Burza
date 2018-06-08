<?php

// Manualno inicijaliziramo bazu ako vec nije.
require_once 'db.class.php';

$db = DB::getConnection();

$has_tables = false;

try
{
	$st = $db->prepare(
		'SHOW TABLES LIKE :tblname'
	);

	$st->execute( array( 'tblname' => 'korisnici' ) );
	if( $st->rowCount() > 0 )
		$has_tables = true;

	$st->execute( array( 'tblname' => 'tvrtka' ) );
	if( $st->rowCount() > 0 )
		$has_tables = true;

	$st->execute( array( 'tblname' => 'kupoprodaje' ) );
	if( $st->rowCount() > 0 )
		$has_tables = true;

	$st->execute( array( 'tblname' => 'vlasnistvo' ) );
	if( $st->rowCount() > 0 )
		$has_tables = true;

	$st->execute( array( 'tblname' => 'promjene_cijena' ) );
	if( $st->rowCount() > 0 )
		$has_tables = true;
}
catch( PDOException $e ) { exit( "PDO error [show tables]: " . $e->getMessage() ); }


if( $has_tables )
{
	exit( 'Tablice korisnici / tvrtka / kupoprodaje / vlasnistvo / promjene_cijena vec postoje. Obrisite ih pa probajte ponovno.' );
}


try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS korisnici (' .
		'id_korisnika int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'username varchar(50) NOT NULL,' .
		'password_hash varchar(255) NOT NULL,'.
		'ime varchar(50) NOT NULL,' .
		'prezime varchar(50) NOT NULL,' .
		'kapital_korisnika int NOT NULL,' .
		'email varchar(50) NOT NULL,' .
		'registration_sequence varchar(20) NOT NULL,' .
		'has_registered int NOT NULL)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create korisnici]: " . $e->getMessage() ); }

echo "Napravio tablicu korisnici.<br />";

try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS tvrtka (' .
		'id_tvrtke int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'naziv_tvrtke varchar(50) NOT NULL,' .
		'kapital_tvrtke int NOT NULL,' .
		'cijena_dionice int NOT NULL)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create tvrtka]: " . $e->getMessage() ); }

echo "Napravio tablicu tvrtka.<br />";


try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS promjene_cijena (' .
		'id_tvrtke int NOT NULL,' .
		'vrijeme_promjene DATETIME NOT NULL,' .
		'cijena int NOT NULL,' .
		'PRIMARY KEY(id_tvrtke, vrijeme_promjene))'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create promjene_cijena]: " . $e->getMessage() ); }

echo "Napravio tablicu promjene_cijena.<br />";

try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS kupoprodaje (' .
		'id_kupca int NOT NULL,' .
		'id_prodavaca int NOT NULL,' .
		'id_tvrtke int NOT NULL,' .
		'vrijeme_transakcije DATETIME NOT NULL,' .
		'cijena_dionice int NOT NULL,' .
		'kolicina_dionica int NOT NULL,' .
		'PRIMARY KEY(id_kupca, id_prodavaca, vrijeme_transakcije))'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create kupoprodaje]: " . $e->getMessage() ); }

echo "Napravio tablicu kupoprodaje.<br />";

try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS vlasnistvo (' .
		'id_korisnika int NOT NULL,' .
		'id_tvrtke int NOT NULL,' .
		'kolicina_dionica int NOT NULL,' .
		'PRIMARY KEY(id_korisnika, id_tvrtke))'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create vlasnistvo]: " . $e->getMessage() ); }

echo "Napravio tablicu vlasnistvo.<br />";

// Ubaci neke korisnike unutra
try
{
	$st = $db->prepare( 'INSERT INTO korisnici(id_korisnika, username, password_hash, ime, prezime, kapital_korisnika, email, has_registered) VALUES (:id_korisnika, :username, :password, :ime, :prezime, :kapital, \'a@b.com\', \'1\')' );

	$st->execute( array( 'id_korisnika' => 101, 'username' => 'klesic', 'password' => password_hash( 'kLesic', PASSWORD_DEFAULT ), 'ime' => 'Klara', 'prezime' => 'Lesic', 'kapital' => '200000') );
	$st->execute( array( 'id_korisnika' => 102, 'username' => 'emurlj', 'password' => password_hash( 'eMurlj', PASSWORD_DEFAULT ), 'ime' => 'Elena', 'prezime' => 'Murljacic', 'kapital' => '200000') );
	$st->execute( array( 'id_korisnika' => 103, 'username' => 'kapetro', 'password' => password_hash( 'kaPetro', PASSWORD_DEFAULT ), 'ime' => 'Katarina', 'prezime' => 'Petrovic', 'kapital' => '200000') );
	$st->execute( array( 'id_korisnika' => 104, 'username' => 'manekic', 'password' => password_hash( 'maNekic', PASSWORD_DEFAULT ), 'ime' => 'Maja', 'prezime' => 'Nekic', 'kapital' => '200000') );

}
catch( PDOException $e ) { exit( "PDO error [insert korisnici]: " . $e->getMessage() ); }

echo "Ubacio u tablicu korisnici.<br />";


try
{
	$st = $db->prepare( 'INSERT INTO tvrtka(naziv_tvrtke, kapital_tvrtke, cijena_dionice) VALUES (:naziv, :kapital_tvrtke, :cijena_dionice)' );

	$st->execute( array( 'naziv' => 'INA', 'kapital_tvrtke' => '5000000', 'cijena_dionice' => '2000') );
	$st->execute( array( 'naziv' => 'MOL', 'kapital_tvrtke' => '3000000', 'cijena_dionice' => '1500') );
	$st->execute( array( 'naziv' => 'Podravka', 'kapital_tvrtke' => '2000000', 'cijena_dionice' => '950') );
	$st->execute( array( 'naziv' => 'Tesla', 'kapital_tvrtke' => '6000000', 'cijena_dionice' => '2500') );
}
catch( PDOException $e ) { exit( "PDO error [insert tvrtka]: " . $e->getMessage() ); }

echo "Ubacio u tablicu tvrtka.<br />";

try
{
	$st = $db->prepare( 'INSERT INTO promjene_cijena(id_tvrtke, vrijeme_promjene, cijena) VALUES (:id_tvrtke, :vrijeme_promjene, :cijena)' );

	$st->execute( array( 'id_tvrtke' => '1', 'vrijeme_promjene' => '2018-04-17 12:45:00', 'cijena' => '2005') );
	$st->execute( array( 'id_tvrtke' => '1', 'vrijeme_promjene' => '2018-04-19 19:23:45', 'cijena' => '1990') );
	$st->execute( array( 'id_tvrtke' => '1', 'vrijeme_promjene' => '2018-05-19 18:23:45', 'cijena' => '2500') );
	$st->execute( array( 'id_tvrtke' => '2', 'vrijeme_promjene' => '2018-05-02 07:23:45', 'cijena' => '1700') );
	$st->execute( array( 'id_tvrtke' => '3', 'vrijeme_promjene' => '2018-05-30 02:20:45', 'cijena' => '1000') );
	$st->execute( array( 'id_tvrtke' => '3', 'vrijeme_promjene' => '2018-06-02 22:03:40', 'cijena' => '900') );
	$st->execute( array( 'id_tvrtke' => '4', 'vrijeme_promjene' => '2018-05-24 09:10:54', 'cijena' => '2050') );
	$st->execute( array( 'id_tvrtke' => '2', 'vrijeme_promjene' => '2018-05-19 19:03:12', 'cijena' => '1600') );
	$st->execute( array( 'id_tvrtke' => '4', 'vrijeme_promjene' => '2018-05-12 15:20:45', 'cijena' => '2300') );
	$st->execute( array( 'id_tvrtke' => '3', 'vrijeme_promjene' => '2018-06-05 18:23:45', 'cijena' => '1000') );
}
catch( PDOException $e ) { exit( "PDO error [insert promjene_cijena]: " . $e->getMessage() ); }

echo "Ubacio u tablicu promjene_cijena.<br />";

/*
try
{
	$st = $db->prepare( 'INSERT INTO kupoprodaje(id_kupca, id_prodavaca, vrijeme_transakcije, cijena_dionice, kolicina_dionica) VALUES (:id_kupca, :id_prodavaca, :vrijeme_transakcije, :cijena_dionice, :kolicina_dionica)' );

	$st->execute( array( 'id_kupca' => '102', 'id_prodavaca' => '1', 'vrijeme_transakcije' => '2018-04-17 12:40:00', 'cijena_dionice' => '2000', 'kolicina_dionica' => '5') );

}
catch( PDOException $e ) { exit( "PDO error [insert kupoprodaje]: " . $e->getMessage() ); }

echo "Ubacio u tablicu kupoprodaje.<br />";

try
{
	$st = $db->prepare( 'INSERT INTO vlasnistvo(id_korisnika, id_tvrtke, kolicina_dionica) VALUES (:id_korisnika, :id_tvrtke, :kolicina_dionica)' );

	$st->execute( array( 'id_korisnika' => '2', 'id_tvrtke' => '1', 'kolicina_dionica' => '5') );

}
catch( PDOException $e ) { exit( "PDO error [insert vlasnistvo]: " . $e->getMessage() ); }

echo "Ubacio u tablicu vlasnistvo.<br />";
*/
?>
