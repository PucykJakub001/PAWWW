<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$idp = $_GET['idp'];

if ($idp == '') { 
    $strona = 'html_compnents/glowna_zawartosc_index.html';
} elseif ($idp == 'kontakt') { 
    $strona = 'html_compnents/contact.html';
} elseif ($idp == 'oferta') { 
    $strona = 'html_compnents/3_offer.html'; 
} elseif ($idp == 'oferta_basic') { 
    $strona = 'html_compnents/basic.html'; 
} elseif ($idp == 'oferta_extended') { 
    $strona = 'html_compnents/extended.html'; 
} elseif ($idp == 'oferta_plus') { 
    $strona = 'html_compnents/extended_plus.html'; 
} elseif ($idp == 'filmy') { 
    $strona = 'html_compnents/filmy.html'; 
} else {
    $strona = 'html_compnents/glowna_zawartosc_index.html'; 
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
            <a href="index.php?idp=filmy">Filmy</a>
        </div>

    </nav>

<?php include($strona); ?>
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
