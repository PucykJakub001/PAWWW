<link rel="stylesheet" href="css/contact_ver2.css">

<?php
include('cfg.php'); 

function PokazKontakt($komunikat = "") {
    $status = "";
    
    // Komunikat o niepowodzeniu
    if (!empty($komunikat)) {
        $status = '<div class="status-msg">' . $komunikat . '</div>';
    }

    $wynik = '
    <div class="formularz-kontakt">
        <h2>Kontakt</h2>
        ' . $status . '
        <form method="post" action="'.$_SERVER['PHP_SELF'].'?idp=kontakt">
            <table class="tabela-kontakt">
                <tr><td><input type="text" name="temat" placeholder="Temat" class="input-pole" /></td></tr>
                <tr><td><input type="text" name="email" placeholder="Twój E-mail" class="input-pole" /></td></tr>
                <tr><td><textarea name="tresc" placeholder="Wiadomość..." class="textarea-pole"></textarea></td></tr>
                <tr><td><input type="submit" name="wyslij_kontakt" value="Wyślij wiadomość" class="button-wyslij" /></td></tr>
            </table>
        </form>
        <hr />
        <form method="post" action="'.$_SERVER['PHP_SELF'].'?idp=kontakt">
            <input type="submit" name="przypomnij_haslo" value="Zapomniałem hasła" class="button-przypomnij" />
        </form>
    </div>';
    
    return $wynik;
}

function WyslijMailKontakt($odbiorca) {
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo PokazKontakt("Błąd: Nie wypełniłeś wszystkich pól!");
    } else {
        $mail['subject']    = FiltrujDane($_POST['temat']);
        $mail['body']       = FiltrujDane($_POST['tresc']);
        $mail['sender']     = FiltrujDane($_POST['email']);
        $mail['reciptient'] = $odbiorca;

        $header  = "From: Formularz kontaktowy <".$mail['sender'].">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
        $header .= "X-Sender: <".$mail['sender'].">\n";
        $header .= "X-Mailer: PRapwww mail 1.2\n";
        $header .= "X-Priority: 3\n";
        $header .= "Return-Path: <".$mail['sender'].">\n";

        if (@mail($mail['reciptient'], $mail['subject'], $mail['body'], $header)) {
            echo PokazKontakt("Wiadomość została wysłana!");
        } else {
            echo PokazKontakt("Błąd: Serwer poczty nie odpowiada.");
        }
    }
}

function PrzypomnijHaslo($odbiorca_admin) {
    global $pass, $login; 
    
    if (isset($pass)) {
        $mail['subject'] = "Odzyskiwanie hasła - System CMS";
        $mail['body']    = "Twoje dane do logowania:\nLogin: " . $login . "\nHasło: " . $pass;
        $mail['sender']  = "system@twojastrona.pl";
        
        $header = "From: System CMS <".$mail['sender'].">\nMIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\n";

        if (@mail($odbiorca_admin, $mail['subject'], $mail['body'], $header)) {
            echo PokazKontakt("Hasło zostało wysłane na maila admina.");
        } else {
            echo PokazKontakt("Błąd wysyłki hasła (brak połączenia z serwerem SMTP).");
        }
    }
}

if (isset($_POST['wyslij_kontakt'])) {
    WyslijMailKontakt("j.pucyk12@gmail.com"); 
} else if (isset($_POST['przypomnij_haslo'])) {
    PrzypomnijHaslo("j.pucyk12@gmail.com");
} else {
    echo PokazKontakt();
}
?>