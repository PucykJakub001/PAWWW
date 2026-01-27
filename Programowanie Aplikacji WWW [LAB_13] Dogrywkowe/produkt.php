<?php

// Generowanie podstron dla produktów.

session_start();
require_once('cfg.php');
require_once('koszyk.php');

// 1. Pobranie ID z adresu URL i zabezpieczenie przed SQL Injection (rzutowanie na int)
$id_produktu = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_produktu <= 0) {
    header('Location: sklep.php');
    exit;
}

// 2. Pobranie danych o konkretnym produkcie
$query = "SELECT * FROM produkty WHERE id = $id_produktu AND status_dostepnosci = 1 LIMIT 1";
$result = mysqli_query($link, $query);
$produkt = mysqli_fetch_assoc($result);

// Jeśli produkt nie istnieje w bazie
if (!$produkt) {
    die("<h2>Błąd: Produkt o podanym ID nie został znaleziony w naszym archiwum retro.</h2><a href='sklep.php'>Powrót do sklepu</a>");
}
?>

<!--kod HTML-->

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $produkt['tytul']; ?> - Retro Shop</title>
    <link rel="stylesheet" href="css/sklep.css">
    <style>
        .produkt-detale { max-width: 900px; margin: 30px auto; display: flex; gap: 30px; background: #fff; padding: 20px; border: 1px solid #ddd; }
        .foto-duze { flex: 1; }
        .foto-duze img { width: 100%; border: 5px solid #eee; }
        .info-duze { flex: 1; }
        .cena-duza { font-size: 24px; color: #e74c3c; font-weight: bold; margin: 20px 0; }
        .powrot { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #3498db; }
    </style>
</head>
<body>

<header class="main-header">
    <h1>Szczegóły Produktu</h1>
    <p>Przeglądasz kartę katalogową: <?php echo htmlspecialchars($produkt['tytul']); ?></p>
</header>

<div class="produkt-detale">
    <div class="foto-duze">
        <a href="sklep.php" class="powrot">&larr; Wróć do listy produktów</a>
        <?php if(!empty($produkt['zdjecie_link'])): ?>
            <img src="<?php echo $produkt['zdjecie_link']; ?>" alt="Foto produktu">
        <?php else: ?>
            <div style="width:100%; height:300px; background:#eee; display:flex; align-items:center; justify-content:center;">Brak zdjęcia</div>
        <?php endif; ?>
    </div>

    <div class="info-duze">
        <h2><?php echo htmlspecialchars($produkt['tytul']); ?></h2>
        <p style="color: #777;">Producent: <strong><?php echo htmlspecialchars($produkt['producent']); ?></strong></p>
        <hr>
        <div class="opis-pelny">
            <h4>Opis techniczny:</h4>
            <?php echo nl2br(htmlspecialchars($produkt['opis'])); ?>
        </div>
        
        <div class="cena-duza">
            Cena: <?php echo number_format($produkt['cena_netto'], 2); ?> zł netto
            <br><span style="font-size: 14px; color: #999;">+ VAT (<?php echo $produkt['podatek_vat']; ?>%)</span>
        </div>

        <p>Dostępność: <strong><?php echo $produkt['ilosc_magazyn']; ?> szt.</strong></p>

        <form method="post" action="sklep.php?akcja=dodaj">
            <input type="hidden" name="id_prod" value="<?php echo $produkt['id']; ?>">
            Ilość: <input type="number" name="ile" value="1" min="1" max="<?php echo $produkt['ilosc_magazyn']; ?>" style="width: 60px; padding: 5px;">
            <input type="submit" value="Dodaj do koszyka" class="btn-koszyk" style="padding: 10px 20px; cursor: pointer;">
        </form>
    </div>
</div>

</body>
</html>