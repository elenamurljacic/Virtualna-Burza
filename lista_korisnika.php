<?php

// ova skripta se izvrsava kad se na izborniku odabere "Lista korisnika".

// ona mora davati listu najbogatijih korisnika i rank ulogiranog korisnika

require_once 'db.class.php';
require_once 'korisnik.class.php';
require_once 'transakcija.class.php';
require_once 'tvrtka.class.php';
require_once 'vlasnistvo.class.php';
require_once 'cijena.class.php';
require_once 'viewovi.php';

session_start();

listaKorisnika();

 ?>
