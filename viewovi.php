<?php

require_once 'funkcije.php';

function crtaj_header()
{
	?>
	<!DOCTYPE html>
	<html>
	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Virtualna burza</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="view.css">
  </head>
	<body>
		<?php
}

function crtaj_footer()
{
	?>
	</body>
	</html>
	<?php
}


function crtaj_formaZaLogin($errorMsg = '')
{
  ?>
  <!DOCTYPE html>
  <html lang="en" dir="ltr">
      <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
          <title>Virtualna burza</title>
          <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
          <link href="login.css" rel="stylesheet">
          <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
      </head>
      <body class="text-center">

      <form method="post" action="<?php echo htmlentities( $_SERVER["PHP_SELF"] ); ?>" class="form-signin">
          <h1 class="">Dobrodošli u virtualnu burzu!</h1>
          <br>

          <label for="username">Korisničko ime:</label>
          <input type="text" name="username" class="form-control"/>
          <br>

          <label for="password">Lozinka:</label>
          <input type="password" name="password" class="form-control"/>
          <br>

          <button type="submit" class="btn btn-primary">Ulogiraj se!</button>

          <small class="form-text text-muted">
        		Ako nemate korisnički račun, otvorite ga <a href="novi.php">ovdje</a>.
        	</small>
          <br>
  	<?php
                  if($errorMsg !== '')
                  {
										?>
                    <div class="alert alert-warning">
                      <strong><?php echo $errorMsg; ?></strong>
                    </div>
                    <?php
                  }
               ?>
      </form>

      </body>
  </html>
<?php
}

function crtaj_formaZaNovogKorisnika($errorMsg = '')
{
  ?>

  <!DOCTYPE html>
  <html lang="en" dir="ltr">
      <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Virtualna burza</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <link href="regist.css" rel="stylesheet">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
      </head>
      <body class="text-center">
          <form method="post" action="<?php echo htmlentities( $_SERVER["PHP_SELF"] ); ?>" class="form-signin">
              <h1>Dobrodošli u virtualnu burzu!</h1>
              <br>
              <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control"/>
              </div>
              <br>
              <div class="row">
                <div class="col-sm-6 form-group">
                  <label for="ime">Ime:</label>
                  <input type="text" name="ime" value="" class="form-control">
                </div>
                <div class="col-sm-6 form-group">
                  <label for="prezime">Prezime:</label>
                  <input type="text" name="prezime" value="" class="form-control">
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-sm-6 form-group">
                  <label for="username">Korisničko ime:</label>
                  <input type="text" name="username" class="form-control"/>
                </div>
                <div class="col-sm-6 form-group">
                  <label for="password">Lozinka:</label>
                  <input type="password" name="password" class="form-control"/>
                </div>
              </div>

              <button type="submit" class="btn btn-primary">Registriraj se</button>
              <br>

              <small class="form-text text-muted">
            		Povratak na login <a href="burza.php">ovdje</a>.
            	</small>

              <?php
                  if( $errorMsg !== '' )
                  {
										?>
                    <div class="alert alert-warning">
                      <strong><?php echo $errorMsg; ?></strong>
                    </div>
                    <?php
                  }
               ?>
          </form>
      </body>
  </html>
  <?php
}

function crtaj_izbornik()
{

	?>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">Virtualna burza</a>
			</div>
			<ul class="nav navbar-nav">
				<li><a href="burza.php">Moj portfolio</a></li>
				<li><a href="dionice.php">Dionice</a></li>
				<li><a href="lista_korisnika.php">Lista korisnika</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="burza.php"><span class="glyphicon glyphicon-user"></span> <?php if( isset ($_SESSION['username']) ) echo $_SESSION['username'];?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
			</ul>
		</div>
	</nav>
	<br>
	<br>
	<br>
	<?php
}


function crtaj_zahvalaNaPrijavi( $errorMsg = '' )
{
	crtaj_header();
	?>
<div class="container">
	<p>
		Zahvaljujemo na prijavi. Da biste dovršili registraciju, kliknite na link u mailu kojeg smo Vam poslali.
	</p>

	<p>
		Povratak na <a href="burza.php">početnu stranicu</a>.
	</p>
</div>

	<?php
	crtaj_footer();
}


function crtaj_zahvalaNaRegistraciji( $errorMsg = '' )
{
	crtaj_header();
	?>
	<div class="container">
		<p>
			Registracija je uspješno provedena.<br />
			Sada se možete ulogirati na <a href="burza.php">početnoj stranici</a>.
		</p>
	</div>

	<?php
	crtaj_footer();
}


function listaTvrtki( )
{
	crtaj_header();
	crtaj_izbornik();
  //lista tvrtki
  $tvrtke = dohvatiSveTvrtke();
  $i = 0;
  foreach ($tvrtke as $tvrtka) //$tvrtke je array od varijabli tipa tvrtka(class)
  {

    $i++;
    ?>
    <div class="container">
      <h2><?php echo $tvrtka->naziv_tvrtke; ?></h2>
      <p>Cijena dionice: <?php echo $tvrtka->cijena_dionice; ?></p>
      <p>Broj dionica: <?php $broj = intval($tvrtka->kapital_tvrtke/$tvrtka->cijena_dionice); echo $broj; ?></p>

      <div class="table-responsive">
        <label for="povijest">Povijest cijena:</label>
        <table name="povijest" class="table">
          <tr>
            <th>Vrijeme promjene</th>
            <th>Cijena</th>
          </tr>
          <?php
            $povijest = povijestCijenaDionice( $tvrtka->id_tvrtke ); // polje objekata tipa Cijena
            foreach ($povijest as $promjena)
            {
							?>
              <tr>
              <td><?php echo $promjena->vrijeme_promjene; ?></td>
              <td><?php echo $promjena->cijena.' kn'; ?></td>
              </tr>
              <?php
            }
            ?>
        </table>
      </div>

      <?php
      // prvo navodimo samu tu tvrtku i broj njenih raspolozivih dionica
      // a onda sve korisnike koji posjeduju dionice te tvrtke, te broj dionica te tvrtke koji oni posjeduju

      $vlasnistvo = vlasnistvoDionicaTvrtke( $tvrtka->id_tvrtke ); // polje objekata tipa Vlasnistvo
      $br = 0;
      foreach ($vlasnistvo as $v ) {
        $br = $br + $v->kolicina_dionica;
      }
      $broj = intval($tvrtka->kapital_tvrtke / $tvrtka->cijena_dionice);
      $raspolozivih = $broj - $br;
      echo '<p>' . $tvrtka->naziv_tvrtke . ' ima ' . $raspolozivih . ' raspoloživih dionica.</p>';
      echo '<br/><br/>';
      ?>

      <label for="kor">Korisnici:</label>
      <ul name="kor">
        <?php

        foreach ($vlasnistvo as $v)
        {
          $korisnik = getUserById( $v->id_korisnika );
					if( $korisnik != null){
          echo '<li>'.$korisnik->ime.' '.$korisnik->prezime.' ima '.$v->kolicina_dionica.' dionica ove tvrtke.</li>';

					}
        }
         ?>
      </ul>
      <form class="" action="obradi_kupnju.php" method="post">
        <p>Kupi <input type="text" name="koliko"> dionica od <input type="text" name="koga">.  <button type="submit" name="<?php echo $i;?>" class="btn btn-primary">Kupi!</button></p>
      </form>
    </div>
    <br>
    <?php // ovo zadnje ide na obradu u obradi_kupnju.php
	}
	if (isset( $_SESSION['error_nedovoljno_dionica'] ) && $_SESSION['error_nedovoljno_dionica'] === 1)
	{
		?>
		<script>
			alert("Nema dovoljno raspoloživih dionica.");
		</script>
		<?php
		$_SESSION['error_nedovoljno_dionica'] = 0;
	}

	if (isset( $_SESSION['error_nedovoljno_novca'] ) && $_SESSION['error_nedovoljno_novca'] === 1)
	{
		?>
		<script>
			alert("Nemate dovoljno novca za kupnju.");
		</script>
		<?php
		$_SESSION['error_nedovoljno_novca'] = 0;
	}
	crtaj_footer();
}



function crtaj_ulogiraniKorisnik()
{
	crtaj_header();
	crtaj_izbornik();
  $kor = getUserById( $_SESSION['id_user']);
?>
<div class="container">
	<h3> <?php
		echo $kor->ime . " " . $kor->prezime; ?>
	</h3>
	<br/>
  <p>Ukupna zarada: <?php echo ukupnaZaradaKorisnika( $_SESSION['id_user'] ) . ' kn'; ?></p><br>
  <p>Dnevna zarada: <?php echo dnevnaZaradaKorisnika( $_SESSION['id_user']) . ' kn' ?></p><br>
<div class="table-responsive">
  <label for="transakcije">Povijest transakcija:</label>
  <table name="transkacije" class="table">
    <tr>
      <th>Kupac</th>
      <th>Prodavač</th>
      <th>Tvrtka</th>
      <th>Vrijeme transakcije</th>
      <th>Cijena dionice</th>
      <th>Količina dionica</th>
    </tr>
    <?php
    $transakcije = transakcijeKorisnika ($_SESSION['id_user']);
    foreach ($transakcije as $transakcija)
    {
      $kupac = getUserById( $transakcija->id_kupca);
      $ime_kupca = $kupac->ime;
      $prezime_kupca = $kupac->prezime;
      $kupac = $ime_kupca . " " . $prezime_kupca;
			$tvrtka = getNazivTvrtkeById($transakcija->id_tvrtke);

      $prod = getUserById( $transakcija->id_prodavaca);
      if( $prod === null )
        $prodavac = getNazivTvrtkeById( $transakcija->id_prodavaca );
      else
      {
        $ime_prod = $prod->ime;
        $prezime_prod = $prod->prezime;
        $prodavac = $ime_prod . " " . $prezime_prod;
      }
      ?>

      <tr>
        <td><?php echo $kupac; ?></td>
        <td><?php echo $prodavac; ?></td>
		<td><?php echo $tvrtka; ?></td>
        <td><?php echo $transakcija->vrijeme_transakcije; ?></td>
        <td><?php echo $transakcija->cijena_dionice.' kn'; ?></td>
        <td><?php echo $transakcija->kolicina_dionica; ?></td>
      </tr>
      <?php
    }
	  ?>
	  </table>
	</div>
	</div>

	<?php
	crtaj_footer();
}


function listaKorisnika()
{
	crtaj_header();
	crtaj_izbornik();
  //lista korisnika
  ?>
  <div class="container">
    <p>Vaš rank je:<?php echo mojRank(); ?></p>
    <label for="pet">Najbogatiji korisnici:</label>
    <table border=0 class="rounded-list" name="najbogatiji">
      <tr>
        <td>
          <ol>
            <?php
            $arr = vratiListuNajbogatijih();
            foreach ($arr as $a)
            {
              echo '<li>'.$a->ime.' '.$a->prezime.'</li>';
            }
             ?>
          </ol>
        </td>
      </tr>
  </div>
  <?php
	crtaj_footer();
}
 ?>
