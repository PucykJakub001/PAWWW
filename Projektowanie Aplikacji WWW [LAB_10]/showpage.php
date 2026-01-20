<?php
/**
 * Ten plik odpowiada za pobieranie treści z bazy danych na podstawie ID 
 * i wyświetlanie jej użytkownikowi.
 */

/**
 * Metoda pobiera zawartość strony z bazy danych.
 */
function PokazPodstrone($id)
{
    // Użycie zmiennej z pliku cfg.php
    global $link;

    $id_clear = (int)$id; 
    $id_safe  = mysqli_real_escape_string($link, $id_clear);

    /**
     * Parametr LIMIT
     * Zapytanie SQL pobiera treść strony tylko gdy jest ona aktywna (status = 1).
     * LIMIT 1 zapewnia przerwanie przeszukiwania bazy po znalezieniu rekordu.
     */
    $query  = "SELECT page_content FROM page_list WHERE id='$id_safe' AND status='1' LIMIT 1"; 
    $result = mysqli_query($link, $query); 
    $row    = mysqli_fetch_array($result);

    // Sprawdzenie, czy zapytanie zwróciło wynik
    if (empty($row['page_content']))
    {
        // Czytelny komunikat dla użytkownika w przypadku braku strony
        $web = '[ Strona o podanym ID nie istnieje lub jest obecnie nieaktywna ]'; 
    }
    else
    {
        /**
         * Pobranie właściwej treści strony. 
         * Formatowanie: zachowanie wcięć ułatwia analizę kodu.
         */
        $web = $row['page_content']; 
    }

    return $web;
}

?>