<?php

// Plik zawierający dane logowania do konta admina, od bzy danych + parametry

// Parametry połączenia z bazą danych

$dbhost = 'localhost'; 
$dbuser = 'root';      
$dbpass = '';          
$baza   = 'moja_strona'; 

// Nawiązanie połącznie za bazą danych

$link = mysqli_connect($dbhost, $dbuser, $dbpass); 

// Sprawdzenie poprawności połączenia

if (!$link) {
    echo '<b>Błąd: Przerwane połączenie z serwerem bazy danych.</b>'; 
}

// Wybór bazy danych

if (!mysqli_select_db($link, $baza)) {
    echo 'Błąd: Nie udało się wybrać wskazanej bazy danych: ' . $baza; 
}

// dane logowania do konta administratora (zarządzanie stronami oraz sklepem)

$login = "admin";
$pass  = "admin";

// zabezpieczenie przed CODE INJECTION

function FiltrujDane($dane) {
    global $link; 
    $dane = trim($dane); // Usuwanie zbędnych spacji
    $dane = htmlspecialchars($dane); // Ususawanie zbędnych znaków HTML 
    $dane = mysqli_real_escape_string($link, $dane); // Zabezpieczenie przed SQL Injection
    return $dane;
}

?>