<?php

require_once 'funkcije.php';
session_start();

$br_tvrtki = count( dohvatiSveTvrtke() );

for( $i = 1; $i <= $br_tvrtki; $i++)
{
  if( isset( $_POST[$i]) ){
    $_SESSION['tvrtka'] = getTvrtkaById( $i );
    break;
  }
}

function imaLiDovoljnoNovca( $a, $b ) // vraća true ako ima dovoljno, inace false
{
  $db = DB::getConnection();
  try {
    $st = $db->prepare( 'SELECT kapital_korisnika FROM korisnici WHERE id_korisnika=:id_korisnika' );
    $st->execute(array('id_korisnika' => $_SESSION['id_user']));
  }
  catch(PDOException $e) {exit('Greska u bazi: ' . $e->getMessage());}

  $row = $st->fetch();
  $c = $a * $b;

  if( $row['kapital_korisnika'] < $c ) return false;
  else
    return true;
}


function kupnjaMoguca( $kol_dion ) // vraca true ako postoji taj prodavac (sama tvrtka ili user koji posjeduje dionice te tvrtke)
                        // i $kol_dion <= broj raspolozivih dionica; inace false
{
  // prvo provjerimo je li prodavac tvrtka:
  if ( $_POST['koga'] === $_SESSION['tvrtka']->naziv_tvrtke )
  {
    $vlasnistvo = vlasnistvoDionicaTvrtke( $_SESSION['tvrtka']->id_tvrtke  ); // polje objekata tipa Vlasnistvo
    $br = 0;
    foreach ($vlasnistvo as $v ) {
      $br = $br + $v->kolicina_dionica;
    }
    $broj = $_SESSION['tvrtka']->kapital_tvrtke / $_SESSION['tvrtka']->cijena_dionice;
    $raspolozivih = $broj - $br;

    if ( $kol_dion <= $raspolozivih ) return true;
    else return false;
  }

  // ako nije tvrtka, moramo provjeriti je li korisnik cije je ime i prezime uneseno u $_POST['koga'] u vlasnistvu dionica te tvrtke
  $parts = explode(' ', $_POST['koga']);
  $vlasnistvo = vlasnistvoDionicaTvrtke( $_SESSION['tvrtka']->id_tvrtke );
  foreach( $vlasnistvo as $v )
  {
    $kor = getUserById( $v->id_korisnika );
    if( $kor->ime === $parts[0] && $kor->prezime === $parts[1] && $kol_dion <= $v->kolicina_dionica ) return true;
  }
  return false;
}

if ( !isset ( $_POST['koliko']) || !isset ( $_POST['koga']) )
{
  header( 'Location: dionice.php' );
}
if( !preg_match( '/^[0-9]{1,4}$/', $_POST['koliko'] ) || !preg_match( '/^[a-zA-Z]{2,20}$/', $_POST['koga'] ) )
{
  header( 'Location: dionice.php' );
}

// Kad ulogirana osoba kupi dionice, moramo:
// --> u tablicu kupoprodaje:  dodati novi redak
// --> u tablici korisnici:  kod ulogiranog korisnika umanjiti kapital za cijena_dionice*kolicina_dionica,
//                          a kod podavaca uvecati kapital za cijena_dionice*kolicina_dionica
// --> u tablici vlasnistvo:
//         dodati novi redak za ulogiranog(ako je ulogirani kupio dionice tvrtke cije dionice dosad nije imao),
//    odn. uvecati kolicina_dionica za ulogiranog (ako je ulogirani kupio dionice tvrtke cije dionice vec posjeduje),
//        obrisati redak za prodavaca (ako je prodavac prodao sve dionice neke tvrtke koje su bile u njegovom vlasnistvu),
//    odn. umanjiti kolicina_dionica  za prodavaca (ako je prodavac prodao dio dionica (ne sve) tvrtke koje su u njegovom vlasnistvu)


if( isset($_POST['koliko']) && isset($_POST['koga']))
{
  if( kupnjaMoguca( $_POST['koliko'] ) === false )
  {
    $_SESSION['error_nedovoljno_dionica'] = 1;
    header( 'Location: dionice.php' );
  }

  elseif( imaLiDovoljnoNovca( $_SESSION['tvrtka']->cijena_dionice, $_POST['koliko'] ) === false )
  {
    $_SESSION['error_nedovoljno_novca'] = 1;
    header( 'Location: dionice.php' );
  }

  // dakle, kupnja je moguca
  else
  {
    // ako kupuje od same tvrtke
    if ( $_POST['koga'] == $_SESSION['tvrtka']->naziv_tvrtke )
    {
      $id_prodavaca = $_SESSION['tvrtka']->id_tvrtke;
      $od_tvrtke = 1;
    }

    // ako kupuje od drugog korisnika
    else
    {
      $parts = explode(' ', $_POST['koga']);
      $id_prodavaca = getIdKorisnikaByImePrezime( $parts[0], $parts[1]);
      $od_tvrtke = 0;
    }

    // dodajemo novu Transakciju
    dodajTransakciju($_SESSION['id_user'], $id_prodavaca, $_SESSION['tvrtka']->id_tvrtke, date("Y-m-d H:i:s"), $_SESSION['tvrtka']->cijena_dionice, $_POST['koliko']);

    // kod ulogiranog korisnika umanjimo kapital za $_SESSION['tvrtka']->cijena_dionice * $_POST['koliko']
    umanjiKapital( $_SESSION['id_user'], $_SESSION['tvrtka']->cijena_dionice, $_POST['koliko'] );
    // kod podavaca uvecati kapital za $_SESSION['tvrtka']->cijena_dionice * $_POST['koliko']
    if($od_tvrtke === 0) uvecajKapital( $id_prodavaca, $_SESSION['tvrtka']->cijena_dionice, $_POST['koliko']);


    $arr = vlasnistvoKorisnika( $_SESSION['id_user'] );
    $uvecali = 0;
    foreach( $arr as $v )
    {
      // ako je ulogirani kupio dionice tvrtke cije dionice vec posjeduje:
      if ($v->id_tvrtke == $_SESSION['tvrtka']->id_tvrtke )
      {
        uvecajKolicinuDionicaUVlasnistvo( $_SESSION['id_user'], $v->id_tvrtke, $_POST['koliko'] );
        $uvecali = 1;
        break;
      }
    }
    // ako dosad nije posjedovao dionice te tvrtke:
    if( $uvecali === 0 )
    {
      // dodajemo novi redak u vlasnistvo
      dodajVlasnistvo( $_SESSION['id_user'], $_SESSION['tvrtka']->id_tvrtke,  $_POST['koliko'] );
    }

    // moramo smanjiti kol.dionica kod prodavaca (ako kupujemo od korisnika)
    // ako se kupuje od tvrtke,ne treba, to ce se izracunati tamo gdje treba
    if( $od_tvrtke === 0 )
      umanjiKolicinuDionicaUVlasnistvo( $id_prodavaca, $_SESSION['tvrtka']->id_tvrtke , $_POST['koliko']);

    //obrisati redak ako je prodavac prodao sve dionice neke tvrtke koje su bile u njegovom vlasnistvu
    $vl = vlasnistvoKorisnikaUTvrtki( $_SESSION['id_user'], $_SESSION['tvrtka']->id_tvrtke );

    if ( $vl->kolicina_dionica === 0 )
      obrisiVlasnistvo( $_SESSION['id_user'], $_SESSION['tvrtka']->id_tvrtke );

  }
}

header( 'Location: dionice.php' );



?>
