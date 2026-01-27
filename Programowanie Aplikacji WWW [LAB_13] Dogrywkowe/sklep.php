<?php

// panel sklepu

session_start();
require_once('cfg.php');
require_once('koszyk.php');

// Obsługa koszyka
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

<!--kod HTML-->

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Retro Shop - Sklep</title>
    <link rel="stylesheet" href="css/sklep.css">
</head>

<body>

<div class="user-info-bar">
    <div>
        <span>Retro Computer Sklep - Witamy!</span>
    </div>
    <div>
        <?php if (isset($_SESSION['user_login'])): ?>
            Zalogowany jako: <strong><?php echo $_SESSION['user_login']; ?></strong>
            <a href="moje_zamowienia.php" style="color: #f1c40f; margin-left: 15px;">Moje zmowówienia</a> 
            <a href="logout.php" class="logout">Wyloguj</a>
        <?php else: ?>
            Jesteś gościem. 
            <a href="logowanie.php">Zaloguj się</a> 
            <a href="rejestracja.php">Zarejestruj</a>
        <?php endif; ?>
    </div>
</div>

<header class="main-header">
    <h1>Retro Computer Sklep</h1>
    <p>Serwis aukcyjny prowadzący sprzedaż komputerów z minionej epoki</p>
    <p>Wspierani przez: <a class="link_main" href="index.php">free_space Cloud Systems</a></p>
</header>

<section class="wyszukiwarka">
    <form method="GET" action="sklep.php" style="display: contents;">
        <div>
            <label>Model:</label><br>
            <input type="text" name="fraza" placeholder="np. 128k" value="<?php echo htmlspecialchars($_GET['fraza'] ?? ''); ?>">
        </div>
        <div>
            <label>Marka:</label><br>
            <select name="marka">
                <option value="">Wszystkie</option>
                <option value="Apple" <?php if(($_GET['marka']??'')=='Apple') echo 'selected'; ?>>Apple / Macintosh</option>
                <option value="Commodore" <?php if(($_GET['marka']??'')=='Commodore') echo 'selected'; ?>>Commodore</option>
                <option value="Atari" <?php if(($_GET['marka']??'')=='Atari') echo 'selected'; ?>>Atari</option>
                <option value="IBM" <?php if(($_GET['marka']??'')=='IBM') echo 'selected'; ?>>IBM</option>
            </select>
        </div>
        <div>
            <label>Cena od:</label><br>
            <input type="number" name="min" placeholder="0" style="width: 80px;" value="<?php echo $_GET['min'] ?? ''; ?>">
        </div>
        <div>
            <label>Cena do:</label><br>
            <input type="number" name="max" placeholder="9999" style="width: 80px;" value="<?php echo $_GET['max'] ?? ''; ?>">
        </div>
        <input type="submit" value="Filtruj ofertę" class="btn-szukaj">
        <a href="sklep.php" style="font-size: 12px; color: #666;">Wyczyść filtry</a>
    </form>
</section>

<div class="produkty-grid">
    <?php
    $dzis = date('Y-m-d H:i:s');
    $query = "SELECT * FROM produkty WHERE status_dostepnosci = 1 AND ilosc_magazyn > 0 AND (data_wygasniecia > '$dzis' OR data_wygasniecia = '0000-00-00 00:00:00')";

    if (!empty($_GET['fraza'])) {
        $f = mysqli_real_escape_string($link, $_GET['fraza']);
        $query .= " AND tytul LIKE '%$f%'";
    }
    if (!empty($_GET['marka'])) {
        $m = mysqli_real_escape_string($link, $_GET['marka']);
        $query .= " AND producent = '$m'";
    }
    if (!empty($_GET['min'])) {
        $min = (float)$_GET['min'];
        $query .= " AND cena_netto >= $min";
    }
    if (!empty($_GET['max'])) {
        $max = (float)$_GET['max'];
        $query .= " AND cena_netto <= $max";
    }

    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo '<div class="produkt-karta">';
            if(!empty($row['zdjecie_link'])) { echo '<img src="'.$row['zdjecie_link'].'" alt="foto">'; }
            echo '<h3>'.$row['tytul'].'</h3>';
            echo '<p style="font-size: 0.8em; color: #777;">Marka: '.$row['producent'].'</p>';
            echo '<p class="opis-prod">'.htmlspecialchars($row['opis']).'</p>';
            echo '<a href="produkt.php?id='.$row['id'].'" style="display:block; margin: 10px 0; font-size: 0.9em; color: #2980b9;">Zobacz szczegóły</a>';

            if ($row['data_wygasniecia'] != '0000-00-00 00:00:00') {
                echo '<p class="data-wyg">Ważne do: '.$row['data_wygasniecia'].'</p>';
            } else {
                echo '<p class="data-wyg">Oferta stała</p>';
            }

            echo '<p class="cena">'.number_format($row['cena_netto'], 2).' zł netto</p>';
            if (isset($_SESSION['user_login'])) {
                echo '<form method="post" action="sklep.php?akcja=dodaj">
                        <input type="hidden" name="id_prod" value="'.$row['id'].'">
                        <div style="margin-bottom: 10px;">
                            Ilość: <input type="number" name="ile" value="1" min="1" max="'.$row['ilosc_magazyn'].'" style="width: 50px;">
                        </div>
                        <input type="submit" value="Dodaj do koszyka" class="btn-koszyk">
                      </form>';
            } else {
                echo '<div style="margin-top:10px;">
                        <p style="font-size:11px; color: #e67e22;">Zaloguj się, aby kupić</p>
                        <a href="logowanie.php" class="btn-szukaj" style="text-decoration:none; display:inline-block; font-size:12px;">Logowanie</a>
                      </div>';
            }
            echo '</div>';
        }
    } else {
        echo "<p style='grid-column: 1/-1; text-align: center;'>Brak produktów spełniających kryteria wyszukiwania.</p>";
    }
    ?>
</div>

<?php showCart(); ?>

</body>
</html>