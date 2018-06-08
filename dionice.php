<?php

// ova skripta se izvrsava kad se na izborniku odabere "Dionice".

// ona mora davati listu tvrtki čije su dionice na tržištu (nazivi tvrtki iz tablice tvrtka)

require_once 'db.class.php';
require_once 'korisnik.class.php';
require_once 'transakcija.class.php';
require_once 'tvrtka.class.php';
require_once 'vlasnistvo.class.php';
require_once 'cijena.class.php';
require_once 'viewovi.php';

session_start();

listaTvrtki();

 ?>
