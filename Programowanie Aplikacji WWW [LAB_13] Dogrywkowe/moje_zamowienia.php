<?php

// Plik odpowiedzialny za pokazanie użytkonikowi jego zamówień.
// Lista zamówień jest pobierana z bazy i wyświetlana na stronie za pomocą HTML

session_start();
require_once('cfg.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: logowanie.php");
    exit;
}

// segeregowanie zamówień po datach, by pokazać użytkonikowi jego ostatnie zamówienia.

$user_id = (int)$_SESSION['user_id'];
$query = "SELECT * FROM zamowienia WHERE id_uzytkownika = $user_id ORDER BY data_zamowienia DESC";
$result = mysqli_query($link, $query);
?>

<!-- STrona w HTML-->
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje Zamówienia - Retro Shop</title>
    <link rel="stylesheet" href="css/sklep.css">
</head>
<body>

<div style="max-width: 900px; margin: 30px auto; padding: 20px;">
    <h1>Twoja Historia Zamówień</h1>
    <p><a href="sklep.php">← Powrót do sklepu</a></p>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Nr</th>
                    <th>Data</th>
                    <th>Produkty</th>
                    <th>Suma Brutto</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $row['data_zamowienia']; ?></td>
                        <td><pre style="font-size: 0.85em; margin:0;"><?php echo $row['produkty']; ?></pre></td>
                        <td><strong><?php echo number_format($row['suma_brutto'], 2); ?> zł</strong></td>
                        <td><span class="status-tag"><?php echo $row['status_zamowienia']; ?></span></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nie dokonałeś jeszcze żadnych zakupów.</p>
    <?php endif; ?>
</div>

</body>
</html>