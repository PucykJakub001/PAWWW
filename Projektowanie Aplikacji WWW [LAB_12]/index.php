<?php
include('cfg.php');
include('showpage.php');

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$idp = $_GET['idp'];

if ($idp == '' || $idp == 'glowna') { 
    $id_strony = 1; 
} elseif ($idp == 'oferta') { // $idp == 'nazwa strony' pochodzi z index.php ::61
    $id_strony = 2; 
} elseif ($idp == 'oferta_basic') { // $idp == 'nazwa strony' pochodzi z 3_offer.html ::21
    $id_strony = 3; 
} elseif ($idp == 'oferta_extended') {  // $idp == 'nazwa strony' pochodzi z 3_offer.html ::32
    $id_strony = 4; 
} elseif ($idp == 'oferta_plus') { // $idp == 'nazwa strony' pochodzi z 3_offer.html ::45
    $id_strony = 5; 
} elseif ($idp == 'filmy') { // $idp == 'nazwa strony' pochodzi z index.php ::63
    $id_strony = 6;
} elseif ($idp == 'kontakt') { // $idp == 'nazwa strony' pochodzi z index.php ::62
    $id_strony = 7;
} elseif ($idp == 'sklep') { 
    $id_strony = 8; 
} elseif ($idp == 'nazwa') { 
    $id_strony = 18; 
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <link rel="stylesheet" href="css/3_offer.css">
    <link rel="stylesheet" href="css/style.css">


    <meta name="Author" content="Jakub Pucyk" /> 
    <title>Free_space cloud systems </title>
</head>
<body>

    <nav>
        <div class="obrazek-logo">
            <a href="index.php">
                <img src="images/logo.png" alt="logo picture" width="200" height="40">
            </a>
        </div>

        <div class="menu-lista-rozwijana">
                <select onchange="location = this.value;">
                <option></option>
                <option value="lab2/zegar_zmiana_tla.html">zegar_zmiana_tla</option>
                <option value="lab3/prostokaty.html">jQuerry</option>
                <option>opcja 3</option>
            </select>
        </div>

        <div class="hub-about-me">
            <a href="index.php?idp=oferta">poznaj ofertę</a>
            <a href="index.php?idp=kontakt">kontakt</a>
            <a href="index.php?idp=filmy">filmy</a>
            <a href="admin/admin.php">admin</a>
            <a href="contact.php">kontakt 2</a>
            <a href="sklep.php">Sklep Retro</a>
        </div>

    </nav>

<?php echo PokazPodstrone($id_strony); ?>
<!--
    <section class="hero">
        <div class="hero-text">
            <h1>Magazyny danych w chumrach oraz chumry obliczeniowe</h1>
            <p>Rozwiązania chmurowe zapewniają wysoki poziom bezpieczeństwa danych, automatyczne kopie zapasowe oraz dużą elastyczność — użytkownik może w każdej chwili zwiększyć lub zmniejszyć dostępną przestrzeń. Magazyny chmurowe są wykorzystywane zarówno przez osoby prywatne, jak i firmy, które przechowują w nich dokumenty, zdjęcia, filmy czy dane projektowe.</p>
        </div>
        <div class="hero-image"></div>
    </section>
-->
    <footer>
        <p>skorzystaj z naszej promocji -25% na pierwszy zakup</p>
    </footer>

<?php
$nr_indeksu = '175381';
$nrGrupy = 'isi2';
echo 'Autor: Jakub Pucyk ' . $nr_indeksu . ' grupa ' . $nrGrupy . ' <br /><br />';
?>

</body>
</html>
