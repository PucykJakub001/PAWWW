<?php
/**
 * Ten plik zawiera dane dostępowe do bazy danych oraz parametry
 * autoryzacji administratora. Jest dołączany do innych skryptów (np. contact.php).
 */

// Parametry połączenia z bazą danych
$dbhost = 'localhost'; 
$dbuser = 'root';      
$dbpass = '';          
$baza   = 'moja_strona'; 

/**
 * Nawiązanie połączenia z serwerem MySQL
 * Wykorzystuje bibliotekę mysqli 
 */
$link = mysqli_connect($dbhost, $dbuser, $dbpass); 

// Sprawdzenie poprawności połączenia
if (!$link) {
    echo '<b>Błąd: Przerwane połączenie z serwerem bazy danych.</b>'; 
}

/**
 * Wybór konkretnej bazy danych
 * Powiązanie: Zmienna $link jest wymagana do wykonania tej operacji 
 */
if (!mysqli_select_db($link, $baza)) {
    echo 'Błąd: Nie udało się wybrać wskazanej bazy danych: ' . $baza; 
}

/**
 * Dane autoryzacyjne administratora
 */
$login = "admin";
$pass  = "admin";

/**
 * Zabezpieczenie przed CODE INJECTION 
 * Funkcja pomocnicza do filtrowania danych wejściowych z $_GET i $_POST
 */
function FiltrujDane($dane) {
    global $link;
    // Usuwanie zbędnych spacji i znaków specjalnych HTML
    $dane = trim($dane);
    $dane = htmlspecialchars($dane);
    // Zabezpieczenie przed SQL Injection dla zapytań SQL 
    $dane = mysqli_real_escape_string($link, $dane);
    return $dane;
}

?>