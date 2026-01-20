<?php
session_start();
require_once('cfg.php');
require_once('koszyk.php');

// Obsługa akcji koszyka
if (isset($_GET['akcja'])) {
    if ($_GET['akcja'] == 'dodaj' && isset($_POST['id_prod'])) {
        addToCart($_POST['id_prod'], $_POST['ile']);
        header('Location: sklep.php'); exit;
    }
    if ($_GET['akcja'] == 'usun' && isset($_GET['nr'])) {
        removeFromCart($_GET['nr']);
        header('Location: sklep.php'); exit;
    }
    if ($_GET['akcja'] == 'edytuj' && isset($_GET['nr']) && isset($_POST['nowa_ilosc'])) {
        $_SESSION[$_GET['nr'].'_2'] = (int)$_POST['nowa_ilosc'];
        header('Location: sklep.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Retro Shop - Sklep</title>
    <link rel="stylesheet" href="css/sklep.css">
</head>

<body>

<header class="main-header">
    <h1>Retro Computer Sklep</h1>
    <p>Serwis aukcyjny prowadzący sprzedaż komputerów z minionej epoki</p>
    <p>Wspierani przez: <a class=link_main href="index.php">free_space Cloud Systems</a></p>
</header>

<div class="produkty-grid">
    <?php
    $dzis = date('Y-m-d H:i:s');
    // Pobieranie produktów z uwzględnieniem statusu i dostępności 
    $query = "SELECT * FROM produkty WHERE status_dostepnosci = 1 AND ilosc_magazyn > 0 AND (data_wygasniecia > '$dzis' OR data_wygasniecia = '0000-00-00 00:00:00')";
    $result = mysqli_query($link, $query);

    while($row = mysqli_fetch_assoc($result)) {
        echo '<div class="produkt-karta">';
        
        // Miniaturka produktu
        if(!empty($row['zdjecie_link'])) {
            echo '<img src="'.$row['zdjecie_link'].'" alt="foto">';
        }
        
        echo '<h3>'.$row['tytul'].'</h3>';
        
        // Pełny opis produktu pobrany z bazy
        echo '<p class="opis-prod">'.htmlspecialchars($row['opis']).'</p>';
        
        // Data wygaśnięcia oferty
        if ($row['data_wygasniecia'] != '0000-00-00 00:00:00') {
            echo '<p class="data-wyg">Ważne do: '.$row['data_wygasniecia'].'</p>';
        } else {
            echo '<p class="data-wyg">Oferta stała</p>';
        }

        echo '<p class="cena">'.number_format($row['cena_netto'], 2).' zł netto</p>';
        
        // Formularz dodawania do koszyka 
        echo '<form method="post" action="sklep.php?akcja=dodaj">
                <input type="hidden" name="id_prod" value="'.$row['id'].'">
                <div style="margin-bottom: 10px;">
                    Ilość: <input type="number" name="ile" value="1" min="1" max="'.$row['ilosc_magazyn'].'" style="width: 50px;">
                </div>
                <input type="submit" value="Dodaj do koszyka" class="btn-koszyk">
              </form>';
        echo '</div>';
    }
    ?>
</div>

<?php 
    // Wyświetlanie koszyka na dole strony
    showCart(); 
?>

</body>
</html>