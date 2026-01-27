<?php

// Skrypt odpowiedzialny za aktywację konta użytkownika po zarajestrowaniu się w systemie
// Na maila podaneg przy rejestracji przychodzi link aktywacyjny. Po kliknięciu w ten link konto sotaje aktywowane
// (w bazie danych w tabeli użytkownicy, pole status zmienia wartość z 0 na 1).

require_once('cfg.php');

if (isset($_GET['kod'])) {
    $kod = mysqli_real_escape_string($link, $_GET['kod']);
    
    // Sprawdzamy czy wygenerowany token faktycznie istnieje.
    $check = mysqli_query($link, "SELECT id FROM uzytkownicy WHERE token = '$kod' LIMIT 1");
    
    if (mysqli_num_rows($check) > 0) {
        // Aktywacja konta + czyszczenie tokena.
        mysqli_query($link, "UPDATE uzytkownicy SET status = 1, token = '' WHERE token = '$kod'");
        echo "<h2>Konto aktywne! Mozesz sie teraz zalogowac.</h2>";
        echo "<a href='index.php'>Wroc do strony glownej</a>";
    } else {
        echo "Nieprawidlowy lub zuzyty kod aktywacyjny.";
    }
}
?>