<?php
require_once('cfg.php');

$komunikat = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = mysqli_real_escape_string($link, $_POST['login']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $pass  = password_hash($_POST['pass'], PASSWORD_DEFAULT); // Bezpieczne haszowanie
    $token = bin2hex(random_bytes(16)); // Generowanie tokena

    // Sprawdzanie czy login lub email istnieje w bazie danych
    $check = mysqli_query($link, "SELECT id FROM uzytkownicy WHERE login='$login' OR email='$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $komunikat = "<p style='color:red;'>Login lub e-mail jest już zajęty!</p>";
    } else {
        $sql = "INSERT INTO uzytkownicy (login, email, haslo, token, status) VALUES ('$login', '$email', '$pass', '$token', 0)";
        
        // generowanie maila z linkiem aktywacyjnym konta
        if (mysqli_query($link, $sql)) {
            $to = $email;
            $subject = "Aktywuj konto w Sklepie Retro";
            $link_akt = "http://localhost/PROJEKT/aktywacja.php?kod=" . $token;
            
            $message = "Witaj $login!\r\n\r\n";
            $message .= "Dziękujemy za rejestrację. Aby aktywować konto, kliknij w link poniżej:\r\n";
            $message .= $link_akt;

            $headers = "From: projektsklepretro@wp.pl\r\n";
            $headers .= "Reply-To: projektsklepretro@wp.pl\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

            if (mail($to, $subject, $message, $headers)) {
                $komunikat = "<p style='color:green;'>Konto założone! Sprawdź e-mail na WP.pl, aby aktywować konto.</p>";
            } else {
                $komunikat = "<p style='color:orange;'>Konto założone, ale nie udało się wysłać maila. Sprawdź konfigurację sendmail.</p>";
            }
        }
    }
}
?>
<!--kod HTML-->

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja - Retro Shop</title>
    <link rel="stylesheet" href="css/sklep.css">
</head>
<body>

<div class="reg-form">
    <h2>Zarejestruj się</h2>
    <?php echo $komunikat; ?>
    <form method="POST" action="rejestracja.php">
        <input type="text" placeholder="login" name="login" required>
        
        <input type="email" placeholder="email" name="email" required>
        
        <input type="password" placeholder="haslo" name="pass" required>
        
        <input type="submit" value="STWÓRZ KONTO" class="btn-reg">
    </form>
    <p><a href="sklep.php">Powrót do sklepu</a></p>
</div>

</body>
</html>