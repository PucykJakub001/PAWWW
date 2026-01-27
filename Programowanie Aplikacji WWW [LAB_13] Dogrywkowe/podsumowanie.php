<?php

// Generowanie podsumowania przedmiotów w koszyku
// Wylistowanie przedmiotów, obliczenie sumy całkowitej do zapłaty z podatkiem

session_start();
require_once('cfg.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: logowanie.php");
    exit;
}

$suma_brutto = 0;
$produkty_list = "";
$zamowienie_zlozone = false;

// Obliczanie wartości koszyka
$max_count = $_SESSION['count'] ?? 0;

for ($i = 1; $i <= $max_count; $i++) {
    if (isset($_SESSION[$i.'_0'])) {
        $id_p = $_SESSION[$i.'_1']; 
        $ile = $_SESSION[$i.'_2'];  

        $res = mysqli_query($link, "SELECT tytul, cena_netto, podatek_vat FROM produkty WHERE id = '$id_p' LIMIT 1");
        if ($prod = mysqli_fetch_assoc($res)) {
            $cena_brutto_jednostkowa = $prod['cena_netto'] * (1 + ($prod['podatek_vat'] / 100));
            $razem_brutto = $cena_brutto_jednostkowa * $ile;
            
            $suma_brutto += $razem_brutto;
            $produkty_list .= "- " . $prod['tytul'] . " (x$ile) - " . number_format($razem_brutto, 2) . " zł\n";
        }
    }
}

// Zapisanie do bazy zamówienia z domyślnym statusem "Nowe"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['potwierdzam']) && $suma_brutto > 0) {
    $uzytkownik_id = (int)$_SESSION['user_id'];
    $produkty_db = mysqli_real_escape_string($link, $produkty_list);
    
    $sql = "INSERT INTO zamowienia (id_uzytkownika, produkty, suma_brutto, status_zamowienia) 
            VALUES ($uzytkownik_id, '$produkty_db', $suma_brutto, 'Nowe')";
    
    if (mysqli_query($link, $sql)) {
        
        // WYsyłanie maila do użytkownika o potwierdzeniu zamówienia
        $user_res = mysqli_query($link, "SELECT email, login FROM uzytkownicy WHERE id = $uzytkownik_id LIMIT 1");
        if ($user_data = mysqli_fetch_assoc($user_res)) {
            $to = $user_data['email'];
            $login_user = $user_data['login'];
            $subject = "Potwierdzenie zamowienia - Retro Shop";
            
            $message = "Witaj $login_user!\r\n\r\n";
            $message .= "Dziękujemy za złożenie zamówienia w naszym sklepie.\r\n";
            $message .= "Oto lista Twoich produktów:\r\n";
            $message .= $produkty_list; 
            $message .= "\r\nŁączna kwota do zapłaty: " . number_format($suma_brutto, 2) . " zł\r\n";
            $message .= "\r\nStatus zamówienia: Nowe\r\n";
            $message .= "Zajmiemy się nim niezwłocznie!\r\n\r\nPozdrawiamy,\r\nEkipa Retro Shop";

            // naglówki pocztowe
            $headers = "From: projektsklepretro@wp.pl\r\n";
            $headers .= "Reply-To: projektsklepretro@wp.pl\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

            // Wysłanie maila
            @mail($to, $subject, $message, $headers);
        }

        // czyszczenie koszyka po zrealizowanej transakcji
        for ($i = 1; $i <= $max_count; $i++) {
            unset($_SESSION[$i.'_0'], $_SESSION[$i.'_1'], $_SESSION[$i.'_2'], $_SESSION[$i.'_3']);
        }
        unset($_SESSION['count']);
        
        $zamowienie_zlozone = true;
    } else {
        die("Błąd SQL: " . mysqli_error($link));
    }
}
?>

<!--kod HTML-->
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Podsumowanie - Retro Shop</title>
    <link rel="stylesheet" href="css/sklep.css">
</head>
<body>
    <div class="summary-container">
        <h1>Podsumowanie Zamówienia</h1>

        <?php if ($zamowienie_zlozone): ?>
            <div class="alert-success">
                <strong>Sukces!</strong> Zamówienie zostało zapisane w bazie danych.
            </div>
            <p class="link-center"><a href="sklep.php">Wróć do sklepu</a></p>

        <?php elseif ($suma_brutto > 0): ?>
            <div class="summary-list">
                <pre><?php echo $produkty_list; ?></pre>
                <hr>
                <h2 class="total-price">Łącznie (Brutto): <?php echo number_format($suma_brutto, 2); ?> zł</h2>
            </div>

            <form method="POST">
                <input type="submit" name="potwierdzam" value="ZAMAWIAM I PŁACĘ" class="btn-order">
            </form>
            <p class="link-center"><a href="sklep.php">Anuluj</a></p>

        <?php else: ?>
            <p>Twój koszyk jest pusty.</p>
            <a href="sklep.php" class="link-center">Wróć do zakupów</a>
        <?php endif; ?>
    </div>
</body>
</html>