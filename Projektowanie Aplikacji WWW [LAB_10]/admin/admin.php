<?php
session_start();
include('../cfg.php');
include('kategorie.php');

/* błąd logowania */
$blad_logowania = '';

/* Obsługa logowania */
if (isset($_POST['login_email'], $_POST['login_pass'])) {
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['zalogowany'] = 1;
    } else {
        $blad_logowania = 'Błędny login lub hasło';
    }
}

if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] != 1) {
    echo '<!DOCTYPE html><html lang="pl"><head><meta charset="UTF-8"><link rel="stylesheet" href="../css/admin.css"></head><body>';
    echo FormularzLogowania($blad_logowania);
    echo '</body></html>';
    exit;
}

// --- LOGIKA BAZY DANYCH (Zapisywanie) ---

// 1. Zapis edycji podstrony - POPRAWIONE o status
if (isset($_POST['edytuj_submit'], $_GET['id'])) {
    $id_edytuj = (int)$_GET['id'];
    $nowy_tytul = mysqli_real_escape_string($link, $_POST['page_title']);
    $nowa_tresc = mysqli_real_escape_string($link, $_POST['page_content']);
    $nowy_status = isset($_POST['status']) ? 1 : 0; // Obsługa checkboxa
    mysqli_query($link, "UPDATE page_list SET page_title='$nowy_tytul', page_content='$nowa_tresc', status='$nowy_status' WHERE id='$id_edytuj' LIMIT 1");
    header('Location: admin.php'); exit;
}

// 2. Dodawanie nowej podstrony - POPRAWIONE o status
if (isset($_POST['dodaj_submit'])) {
    $tytul = mysqli_real_escape_string($link, $_POST['add_page_title']);
    $tresc = mysqli_real_escape_string($link, $_POST['add_page_content']);
    $status = isset($_POST['add_status']) ? 1 : 0; // Obsługa checkboxa
    mysqli_query($link, "INSERT INTO page_list (page_title, page_content, status) VALUES ('$tytul', '$tresc', '$status') LIMIT 1");
    header('Location: admin.php'); exit;
}

// 3. Usuwanie podstrony
if (isset($_GET['funkcja'], $_GET['id']) && $_GET['funkcja'] == 'usun') {
    $id_del = (int)$_GET['id'];
    mysqli_query($link, "DELETE FROM page_list WHERE id='$id_del' LIMIT 1");
    header('Location: admin.php'); exit;
}

// 4. Logika dodawania kategorii
if (isset($_POST['kat_dodaj_submit'])) {
    $nazwa = mysqli_real_escape_string($link, $_POST['kat_nazwa']);
    $matka = (int)$_POST['kat_matka'];
    mysqli_query($link, "INSERT INTO kategorie (nazwa, matka) VALUES ('$nazwa', '$matka') LIMIT 1");
    header('Location: admin.php?funkcja=kategorie');
    exit;
}

// 5. Usuwanie kategorii
if (isset($_GET['funkcja'], $_GET['akcja'], $_GET['id']) && $_GET['funkcja'] == 'kategorie' && $_GET['akcja'] == 'usun') {
    $id_kat_del = (int)$_GET['id'];
    mysqli_query($link, "DELETE FROM kategorie WHERE id = $id_kat_del LIMIT 1");
    header('Location: admin.php?funkcja=kategorie');
    exit;
}

/* Formularze i funkcje widoku */
function FormularzLogowania($blad = '') {
    return '<div class="logowanie"><form method="post"><input type="text" name="login_email" placeholder="Login" required /><input type="password" name="login_pass" placeholder="Hasło" required /><p class="blad_logowania">'.$blad.'</p><button type="submit">Zaloguj</button></form></div>';
}

function ListaPodstron() {
    global $link;
    $result = mysqli_query($link, "SELECT id, page_title FROM page_list ORDER BY id DESC LIMIT 100");
    echo '<div class="table-container"><table><tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>';
    while($row = mysqli_fetch_array($result)) {
        echo '<tr><td>'.$row['id'].'</td><td>'.$row['page_title'].'</td><td><a href="admin.php?funkcja=edytuj&id='.$row['id'].'" class="edit">Edytuj</a> <a href="admin.php?funkcja=usun&id='.$row['id'].'" class="delete">Usuń</a></td></tr>';
    }
    echo '</table><a href="admin.php?funkcja=dodaj" class="btn-add">Dodaj nową stronę</a></div>';
}

function EdytujPodstrone($id) {
    global $link;
    $row = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM page_list WHERE id = '$id' LIMIT 1"));
    $checked = ($row['status'] == 1) ? 'checked' : ''; // Sprawdzenie statusu
    
    return '
    <div class="edycja">
        <h1>Edycja: '.$row['page_title'].'</h1>
        <form method="post">
            <label>Tytuł:</label><br>
            <input type="text" name="page_title" value="'.htmlspecialchars($row['page_title']).'" style="width:100%;" /><br><br>
            <label>Treść:</label><br>
            <textarea name="page_content" rows="15" style="width:100%;">'.htmlspecialchars($row['page_content']).'</textarea><br><br>
            <label><input type="checkbox" name="status" '.$checked.' /> Strona aktywna</label><br><br>
            <input type="submit" name="edytuj_submit" value="Zapisz" />
            <a href="admin.php">Anuluj</a>
        </form>
    </div>';
}

function DodajNowaPodstrone() {
    return '
    <div class="dodawanie">
        <h1>Dodaj nową stronę</h1>
        <form method="post">
            <label>Tytuł:</label><br>
            <input type="text" name="add_page_title" style="width:100%;" required /><br><br>
            <label>Treść:</label><br>
            <textarea name="add_page_content" rows="15" style="width:100%;"></textarea><br><br>
            <label><input type="checkbox" name="add_status" checked /> Strona aktywna</label><br><br>
            <input type="submit" name="dodaj_submit" value="Dodaj stronę" />
            <a href="admin.php">Anuluj</a>
        </form>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Administratora</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php
$funkcja_get = $_GET['funkcja'] ?? '';
$akcja_get = $_GET['akcja'] ?? '';

$czy_kategorie = ($funkcja_get == 'kategorie' || $akcja_get == 'dodaj_kat');
$active_strony = !$czy_kategorie ? 'active' : '';
$active_kat = $czy_kategorie ? 'active' : '';

echo '
<div class="admin-nav">
    <a href="admin.php" class="nav-item '.$active_strony.'">Zarządzaj Stronami</a>
    <a href="admin.php?funkcja=kategorie" class="nav-item '.$active_kat.'">Zarządzaj Kategoriami Sklepu</a>
</div>';

if ($czy_kategorie) {
    if ($akcja_get == 'dodaj_kat') {
        echo FormularzDodawaniaKategorii();
    } else {
        ZarzadzajKategoriami(); 
    }
} else {
    if ($funkcja_get == 'edytuj' && isset($_GET['id'])) echo EdytujPodstrone($_GET['id']);
    elseif ($funkcja_get == 'dodaj') echo DodajNowaPodstrone();
    else ListaPodstron();
}
?>
</body>
</html>