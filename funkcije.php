<?php
require_once 'db.class.php';
require_once 'korisnik.class.php';
require_once 'transakcija.class.php';
require_once 'tvrtka.class.php';
require_once 'vlasnistvo.class.php';
require_once 'cijena.class.php';


//vraca listu 4 najbogatija korisnika
  function vratiListuNajbogatijih()
  {
    $arr = array(); // tu cemo spremati objekte tipa Korisnik

    $db = DB::getConnection();
    try {
      $st = $db->prepare( 'SELECT id_korisnika, username, ime, prezime, email, kapital_korisnika FROM korisnici ORDER BY kapital_korisnika DESC' );
      $st->execute();
    }
    catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}
    echo "<br />";

    $br = 0;

		while( $row = $st->fetch() )
		{
      $br++;
			$arr[] = new Korisnik( $row['id_korisnika'], $row['username'], $row['ime'], $row['prezime'], $row['email'], $row['kapital_korisnika'] );
      if( $br == 4) break;
    }
		return $arr;
  }

  // vaca rank ulogiranog korisnika
  function mojRank ()
  {
    $arr = array(); // tu cemo spremati objekte tipa Korisnik

    $db = DB::getConnection();
    try {
      $st = $db->prepare( 'SELECT id_korisnika, username, ime, prezime, email, kapital_korisnika FROM korisnici ORDER BY kapital_korisnika DESC' );
      $st->execute();
    }
    catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}
    echo "<br />";

    $br = 0;
    $temp = 0;

    while($row = $st->fetch() )
    {
      if( $temp === 0) // samo prvi put ulazi u ovaj if
      {
        $prethodni = $row['kapital_korisnika']; // sad je u $prethodni spremljen kapital najbogatijeg (ili jednog od najbogatijih -> ako ima vise kor s jednakim kapitalom)
        $temp = 1;
        $br++; // sad je $br = 1
      }

      if( $temp === 1) // svaki put osim prvog puta ulazi u ovaj if
      {
        // povecaj brojac samo ako je $prethodni (kapital prethodnog kor.) strogo veci od kapitala trenutno promatranog korisnika
        if ($prethodni > $row['kapital_korisnika'] )
          $br++;
        $prethodni = $row['kapital_korisnika'];
      }

      if( $_SESSION['id_user'] === $row['id_korisnika']) return $br;
    }
  }

  // vraća polje svih korisnika (tipa Korisnik)
  function getAllUsers( )
  {
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'SELECT id_korisnika, username, ime, prezime, email, kapital_korisnika FROM korisnici' );
      $st->execute();
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    // $arr je polje elemenata tipa Korisnik
    $arr = array();
    while( $row = $st->fetch() )
    {
      $arr[] = new Korisnik( $row['id_korisnika'], $row['username'], $row['ime'], $row['prezime'], $row['email'], $row['kapital_korisnika'] );
    }
    return $arr;
  }

  // funkcija koja vraća korisnika (tip Korisnik) s traženim id-om
  function getUserById( $id )
  {
    // u $arr spremimo polje svih korisnika i onda prođemo po tom polju i nađemo korisnika s traženim id-om
    // inače vraća null
    $arr = getAllUsers();

    foreach ($arr as $u )
    {
      if ( $u->id_korisnika === $id )
        return $u;
    }
    return null;
  }

  // vraca trenutni kapital ulogiranog korisnika
  function mojKapital()
  {
    $ja = getUserById( $_SESSION['id_user']); // objekt tipa korisnik
    return $ja->kapital_korisnika;
  }

  // vraca ukupnu zaradu korisnika s id-om $id_user
  function ukupnaZaradaKorisnika( $id_user )
  {
    $uk_zarada = 0;

    $db = DB::getConnection();
    try {
      $st=$db->prepare("SELECT * FROM kupoprodaje WHERE id_kupca=:id_kupca OR id_prodavaca=:id_prodavaca ");
      $st->execute( array('id_kupca' => $id_user, 'id_prodavaca' => $id_user ));
    }
    catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}
    echo "<br />";
    while ($row = $st->fetch(PDO::FETCH_ASSOC))
    {
      if($id_user == $row['id_kupca'])
        $uk_zarada = $uk_zarada - $row['cijena_dionice']*$row['kolicina_dionica'];
      if($id_user == $row['id_prodavaca'])
        $uk_zarada = $uk_zarada + $row['cijena_dionice']*$row['kolicina_dionica'];
    }
    return $uk_zarada;
  }


  // vraca danasnju zaradu
  function dnevnaZaradaKorisnika( $id_user )
  {
    $dn_zarada = 0;
    $date = date('Y-m-d H:i:s');
/*
    $dijelovi = explode( ' ', $date );
    $danasnji_dan = $dijelovi[0];
    echo $danasnji_dan . "<br/>";
*/
    $db = DB::getConnection();

    // dohvatimo transakcije u kojima je sudjelovao korisnik, ali
    try {
      $st=$db->prepare('SELECT * FROM kupoprodaje WHERE (id_kupca=:id_user OR id_prodavaca=:id_user) AND (vrijeme_transakcije >= CURDATE() AND vrijeme_transakcije < CURDATE() + INTERVAL 1 DAY) ');
      $st->execute( array('id_user' => $id_user) );
    }
    catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}
    echo "<br />";

    while ($row = $st->fetch(PDO::FETCH_ASSOC))
    {
      if($id_user === $row['id_kupca'])
        $dn_zarada = $dn_zarada - $row['cijena_dionice']*$row['kolicina_dionica'];
      if($id_user === $row['id_prodavaca'])
        $dn_zarada = $dn_zarada + $row['cijena_dionice']*$row['kolicina_dionica'];
    }
    return $dn_zarada;
  }


  // vraca polje Transakcija u kojima sudjeluje ulogirani korisnik
  function transakcijeKorisnika( $id_user )
  {
    $db = DB::getConnection();
    try {
      $st=$db->prepare('SELECT * FROM kupoprodaje WHERE id_kupca =:id_user OR id_prodavaca =:id_user');
      $st->execute( array('id_user' => $id_user ));
    }
    catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}
    echo "<br />";

    $arr = array();

    while ($row = $st->fetch())
    {
      $arr[] = new Transakcija($row['id_kupca'], $row['id_prodavaca'], $row['id_tvrtke'], $row['vrijeme_transakcije'], $row['cijena_dionice'], $row['kolicina_dionica'] );
    }
    return $arr;
  }

  // vraca polje objekata tipa Cijena -> u tom polju su sadrzane sve promjene
  //cijena dionica tvrtke s id-om $id_tvrtke
  function povijestCijenaDionice($id_tvrtke)
  {
    $db = DB::getConnection();
    try {
      $st=$db->prepare('SELECT * FROM promjene_cijena WHERE id_tvrtke =:id_tvrtke');
      $st->execute( array('id_tvrtke' => $id_tvrtke ));
    }
    catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}
    echo "<br />";

    $arr = array();

    while ($row = $st->fetch() )
    {
      $arr[] = new Cijena($row['id_tvrtke'], $row['vrijeme_promjene'], $row['cijena']);
    }
    return $arr;
  }

  // vraca polje svih tvrtki u bazi (polje elemenata tipa Tvrtka)
  function dohvatiSveTvrtke()
  {
    $db = DB::getConnection();
    try {
      $st=$db->prepare('SELECT * FROM tvrtka');
      $st->execute();
    }
    catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}
    echo "<br />";

    $arr = array(); // polje ciji su elementi tipa Tvrtka

    while( $row = $st->fetch() )
    {
      $arr[] = new Tvrtka( $row['id_tvrtke'], $row['naziv_tvrtke'], $row['kapital_tvrtke'], $row['cijena_dionice'] );
    }
    return $arr;
  }

  function dohvatiNaziveSvihTvrtki()
  {
    $arr = dohvatiSveTvrtke();
    $ime = array(); // polje imena tvrtki

    foreach( $arr as $tvrtka )
    {
      array_push($ime, $tvrtka->naziv_tvrtke);
    }
    return $ime;
  }

  // vraca tvrtku s id-om $id
  function getTvrtkaById( $id )
  {
    $arr = dohvatiSveTvrtke();
    foreach ($arr as $t )
    {
      if ( $id == $t->id_tvrtke ) return $t;
    }
    return null;
  }

  function getNazivTvrtkeById ( $id )
  {
    $t = getTvrtkaById( $id );
    if ($t === null) return null;
    return $t->naziv_tvrtke;
  }

  function getIdKorisnikaByImePrezime( $ime, $prezime )
  {
    $arr = getAllUsers();

    foreach ($arr as $u )
    {
      if ( $u->ime === $ime && $u->prezime === $prezime )
        return $u->id_korisnika;
    }
    return null;

  }

  function getAllVlasnistvo()
  {
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'SELECT * FROM vlasnistvo' );
      $st->execute();
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    // $arr je polje elemenata tipa Korisnik
    $arr = array();
    while( $row = $st->fetch() )
    {
      $arr[] = new Vlasnistvo( $row['id_korisnika'], $row['id_tvrtke'], $row['kolicina_dionica'] );
    }
    return $arr;
  }

  function vlasnistvoKorisnika( $id_user )
  {
    $db = DB::getConnection();
    try {
      $st=$db->prepare('SELECT * FROM vlasnistvo WHERE id_korisnika=:id_korisnika ');
      $st->execute( array('id_korisnika' => $id_user ));
    }
    catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}
    echo "<br />";

    $arr = array();

    while ($row = $st->fetch())
    {
      $arr[] = new Vlasnistvo( $row['id_korisnika'], $row['id_tvrtke'], $row['kolicina_dionica']);
    }
    return $arr;
  }

  function vlasnistvoKorisnikaUTvrtki( $id_user, $id_tvrtke )
  {
    $db = DB::getConnection();
    try {
      $st=$db->prepare('SELECT * FROM vlasnistvo WHERE id_korisnika=:id_korisnika AND id_tvrtke=:id_tvrtke ');
      $st->execute( array('id_korisnika' => $id_user, 'id_tvrtke' => $id_tvrtke ));
    }
    catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}
    echo "<br />";

    while ($row = $st->fetch() )
      $v = $row;

    return $v;
  }


  // sto sve moramo mijenjati u bazi kad dođe do transakcije?
  //
  // Kad ulogirana osoba kupi/proda dionice, moramo:
  // --> u tablicu kupoprodaje dodati novi redak
  // --> u tablici korisnici kod ulogiranog korisnika umanjiti/uvecati kapital za cijena_dionice*kolicina_dionica
  // --> u tablici vlasnistvo:
  //         dodati novi redak (ako je ulogirani kupio dionice tvrtke cije dionice dosad nije imao),
  //    odn. uvecati kolicina_dionica (ako je ulogirani kupio dionice tvrtke cije dionice vec posjeduje),
  //    odn. obrisati redak (ako je prodao sve dionice neke tvrtke koje su bile u njegovom vlasnistvu),
  //    odn. umanjiti kolicina_dionica (ako je prodao dio dionica (ne sve) tvrtke koje su u njegovom vlasnistvu)


  function dodajTransakciju( $id_kupca, $id_prodavaca, $id_tvrtke, $vrijeme_transakcije, $cijena_dionice, $kolicina_dionica )
  {
    // provjera postoje li ti korisnici,odn.tvrtke:

    if( ( getUserById($id_kupca) === null && getTvrtkaById($id_kupca) === null) ||
        ( getUserById($id_prodavaca) === null && getTvrtkaById($id_prodavaca) === null) )
        throw new Exception( 'dodajTransakciju :: Korisnik/tvrtka sa zadanim id ne postoji.' );

    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'INSERT INTO kupoprodaje( id_kupca, id_prodavaca, id_tvrtke, vrijeme_transakcije, cijena_dionice, kolicina_dionica)
                            VALUES (:id_kupca, :id_prodavaca, :id_tvrtke, :vrijeme_transakcije, :cijena_dionice, :kolicina_dionica)' );
      $st->execute( array( 'id_kupca' => $id_kupca, 'id_prodavaca' => $id_prodavaca, 'id_tvrtke' => $id_tvrtke, 'vrijeme_transakcije' => $vrijeme_transakcije, 'cijena_dionice' => $cijena_dionice, 'kolicina_dionica' => $kolicina_dionica ) );
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
  }

  function uvecajKapital( $id_korisnika, $cijena_dionice, $kolicina_dionica )
  {
    if( getUserById($id_korisnika) === null )
        throw new Exception( 'uvecajKapital :: Korisnik sa zadanim id ne postoji.' );

    $novi_kapital = getUserById($id_korisnika)->kapital_korisnika + $cijena_dionice*$kolicina_dionica;
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'UPDATE korisnici SET kapital_korisnika =' . $novi_kapital . ' WHERE id_korisnika =' . $id_korisnika );
      $st->execute();
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
  }


  function umanjiKapital( $id_korisnika, $cijena_dionice, $kolicina_dionica )
  {
    if( getUserById($id_korisnika) === null )
        throw new Exception( 'uvecajKapital :: Korisnik sa zadanim id ne postoji.' );

    $novi_kapital = getUserById($id_korisnika)->kapital_korisnika - $cijena_dionice*$kolicina_dionica;
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'UPDATE korisnici SET kapital_korisnika =' . $novi_kapital . ' WHERE id_korisnika =' . $id_korisnika );
      $st->execute();
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
  }


  function dodajVlasnistvo( $id_korisnika, $id_tvrtke, $kolicina_dionica )
  {
    if( getUserById($id_korisnika) === null || getTvrtkaById($id_tvrtke) === null )
        throw new Exception( 'dodajTransakciju :: Korisnik/tvrtka sa zadanim id ne postoji.' );

    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'INSERT INTO vlasnistvo( id_korisnika, id_tvrtke, kolicina_dionica)
                            VALUES (:id_korisnika, :id_tvrtke, :kolicina_dionica)' );
      $st->execute( array( 'id_korisnika' => $id_korisnika, 'id_tvrtke' => $id_tvrtke, 'kolicina_dionica' => $kolicina_dionica ) );
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
  }

  function uvecajKolicinuDionicaUVlasnistvo( $id_korisnika, $id_tvrtke, $dodajDionica )
  {
    if( getUserById($id_korisnika) === null || getTvrtkaById($id_tvrtke) === null )
        throw new Exception( 'uvecajKolicinuDionicaUVlasnistvo :: Korisnik/tvrtka sa zadanim id ne postoji.' );

    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'UPDATE vlasnistvo SET kolicina_dionica = kolicina_dionica+' . $dodajDionica . ' WHERE id_korisnika =' . $id_korisnika . ' AND id_tvrtke =' . $id_tvrtke );
      $st->execute();
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
  }


  function umanjiKolicinuDionicaUVlasnistvo( $id_korisnika, $id_tvrtke, $oduzmiDionica )
  {
    if( getUserById($id_korisnika) === null || getTvrtkaById($id_tvrtke) === null )
        throw new Exception( 'umanjiKolicinuDionicaUVlasnistvo :: Korisnik/tvrtka sa zadanim id ne postoji.' );

    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'UPDATE vlasnistvo SET kolicina_dionica = kolicina_dionica-' . $oduzmiDionica . ' WHERE id_korisnika =' . $id_korisnika . ' AND id_tvrtke =' . $id_tvrtke );
      $st->execute();
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
  }


  function obrisiVlasnistvo ( $id_korisnika, $id_tvrtke )
  {
    if( getUserById($id_korisnika) === null || getTvrtkaById($id_tvrtke) === null )
        throw new Exception( 'dodajTransakciju :: Korisnik/tvrtka sa zadanim id ne postoji.' );

    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'DELETE FROM vlasnistvo WHERE id_korisnika=:id_user AND id_tvrtke=:id_followed_user' );
      $st->execute( array( 'id_korisnika' => $id_korisnika, 'id_tvrtke' => $id_tvrtke ) );
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
  }

  // vraca polje objekata tipa Vlasnistvo gdje je id_tvrtke = $id_tvrtke
  function vlasnistvoDionicaTvrtke( $id_tvrtke )
  {
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'SELECT * FROM vlasnistvo WHERE id_tvrtke=:id_tvrtke' );
      $st->execute( array('id_tvrtke' => $id_tvrtke) );
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr = array();
    while( $row = $st->fetch() )
    {
      $arr[] = new Vlasnistvo( $row['id_korisnika'], $row['id_tvrtke'], $row['kolicina_dionica'] );
    }
    return $arr;
  }





    // -------------------------------------------------------------
    // -------------------------------------------------------------
    /*
        foreach ($ime as $value)
        {
            $j = 1;
              ?>
              <br />
              <form action="test.php" method="post">
                <input type="submit" name="<?php echo $j; ?>" value="<?php echo $value; ?>" />
              </form>
              <?php
            $j++;
         }

         // provjera koji je gumb stisnut --> taj dio ide u obradi_dionice_info.php
        for( $j = 1; $j <= $koliko; $j++ )
        {
            if( isset($_POST["$j"]) )
            {

              //za prvi stisnuti button ispise informacije
              //ali ne ispisuje ponovno kad stisneš na neki drugi button i ne znam u čemu je problem
              //vjerojatno sto je action="test.php", a treba bit nešto drugo. Kako nemam kompletan kod, tesko mi je tako

                    echo "Ime tvrtke je: " . $ime[$j-1];
                    echo "<br /> Cijena dionice je: " . $dionica[$j-1];
                    echo "<br /> Broj dionica je: " . ( $kapital[$j-1] / $dionica[$j-1] ) . "<br />";

                    try {
                      $st1=$db->prepare('SELECT * FROM promjene_cijena');
                      $st1->execute();
                    }
                    catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}
                    echo "<br />";

                    echo "Promjene cijena su: <br />";

                    $q = 0;

                    while ($row = $st1->fetch(PDO::FETCH_ASSOC))
                    {
                      if($j == $row['id_tvrtke']){
                          echo $row['vrijeme_promjene'] . "<br />";
                          $q = 1;
                      }
                    }

                    if($q == 0)
                      echo "Nema promjena!<br>";

                    echo "Zavrsili s ispisom promjene cijena! <br />";
            }
        }
    */




?>
