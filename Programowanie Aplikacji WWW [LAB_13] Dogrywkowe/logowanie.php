<?php

// plk odpowiedzialny za logowanie się użytkownika na swoje konto

session_start();
require_once('cfg.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = mysqli_real_escape_string($link, $_POST['login']);
    $pass  = $_POST['pass'];

    // Pobieranie danych użytkownika z bazy danych

    $sql = "SELECT * FROM uzytkownicy WHERE login = '$login' LIMIT 1";
    $result = mysqli_query($link, $sql);
    $user = mysqli_fetch_assoc($result);

    // sprawdzanie czy konto jest aktywowanie oraz hasło.

    if ($user) {
        // 1. Sprawdzamy status aktywacji
        if ($user['status'] == 0) {
            $error = "Twoje konto nie zostało jeszcze aktywowane. Sprawdź e-mail!";
        } 
        // 2. Sprawdzamy hasło (porównujemy wpisane z haszem w bazie)
        elseif (password_verify($pass, $user['haslo'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['login'];
            
            header("Location: sklep.php"); // Przekierowanie po sukcesie
            exit;
        } else {
            $error = "Błędne hasło."; // Komunikat, gdy użytkonik poda złe hasło
        }
    } else {
        $error = "Użytkownik nie istnieje."; // Komunikat gdy nie znajdziemy takiego użytkownika w bazie
    }
}
?>
<!-- Kod HTML odpowiedzialny za generowanie formularzu z logowaniem -->
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie - Retro Shop</title>
    <link rel="stylesheet" href="css/sklep.css">
</head>
<body>

<div class="login-form">
    <h2>Zaloguj się</h2>
    <?php if($error) echo "<div class='error-box'>$error</div>"; ?>
    
    <form method="POST">
        <input type="text" name="login" placeholder="Login" required>
        <input type="password" name="pass" placeholder="Hasło" required>
        <input type="submit" value="WEJDŹ DO SKLEPU" class="btn-login">
    </form>
    <p style="font-size: 12px; text-align: center;">Nie masz konta? <a href="rejestracja.php">Zarejestruj się</a></p>
</div>

</body>
</html>