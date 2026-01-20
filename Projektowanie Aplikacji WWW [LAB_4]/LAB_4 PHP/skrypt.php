<?php
    $nr_indeksu = "1234567";
    $nrGrupy = "X";

    $a = 2;
    $b = 1;

    $c = 10; 

    echo "Jan Kowalski ".$nr_indeksu." grupa ".$nrGrupy." <br /><br />";
    echo ">>> Zastosowanie metody include() <br />";

    // echo "jest: $z1 $z2 $z3"; // warnings

    include 'zmienne.php';

    echo "jest: $z1 <br />";
    echo "jest: $z2 <br />";
    echo "jest: $z3 <br />";

    echo ">>> Zastosowanie metody require_once() <br />";

    $s = require_once('f1.php');
    echo "\n" . $s . "<br />";

    $s = require_once('f1.php');
    echo "\n" . $s . "<br />";

    echo ">>> Zastosowanie warunku if <br />";

    if ($a > $b)
        echo("a jest większe od b <br/>");
    
    echo ">>> Zastosowanie warunku else <br />";

    if ($a > $b)
        echo("a jest większe od b <br/>");
    else 
        echo ("b jest większe od a <br/>");

    echo ">>> Zastosowanie warunku elseif <br />";

    if ($a > $b) {
        echo ("a jest większe od b <br/>");
    }
    elseif ($a == $b){
        echo ("a i b są równe <br/>");
    }
    else {
        echo ("a jest mniejsze od b <br/>"); 
    }

    echo ">>> Zastosowanie warunku switch <br />";

    // c = 10
    switch($c) {

        case 0:
            echo "c = 0 <br />";
            break;
        case 5:
            echo "c = 5 <br />";
            break;
        case 10:
            echo "c = 10 <br />";
            break;
    }

    echo ">>> Zastosowanie pętli while <br />";  

    while ($b <= 10){
        echo $b++ ."<br />";
    }

    echo ">>> Zastosowanie pętli for <br />";

    for ($b = 1; $b <= 10; $b++){
        echo $b . "<br />";
    }
	
	echo ">>> Zastosowanie zmiennych \$_GET, \$_POST, \$_SESSION <br />";

	echo ">>> Zmienna \$_GET <br />";

	// przykład: plik.php?imie=Jan&wiek=20
	if (isset($_GET['imie']) && isset($_GET['wiek'])) {
		echo "Imię (GET): " . $_GET['imie'] . "<br />";
		echo "Wiek (GET): " . $_GET['wiek'] . "<br />";
	} else {
		echo "Brak danych GET <br />";
	}

	echo "<br />";

	echo ">>> Zmienna \$_POST <br />";

	if (isset($_POST['login'])) {
		echo "Login (POST): " . $_POST['login'] . "<br />";
	} else {
		echo "Brak danych POST <br />";
	}

	echo "<br />";

	echo ">>> Zmienna \$_SESSION <br />";

	session_start();

	$_SESSION['uzytkownik'] = "Jan Kowalski";

	echo "Użytkownik (SESSION): " . $_SESSION['uzytkownik'] . "<br />";
    
?>