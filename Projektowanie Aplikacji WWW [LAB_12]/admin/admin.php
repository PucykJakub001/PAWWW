<?php
/**
 * PANEL ADMINISTRATORA - GŁÓWNY PLIK ZARZĄDZAJĄCY (CMS + SKLEP)
 * Obsługuje logowanie, zarządzanie podstronami, kategoriami oraz produktami.
 */

session_start();
include('../cfg.php');      // Dane połączenia z bazą i dane logowania
include('kategorie.php'); // Funkcje zarządzania drzewem kategorii
include('produkty.php');  // Funkcje zarządzania produktami (Lab 11)

/* Inicjalizacja zmiennej błędu logowania */
$blad_logowania = '';   

/**
 * MECHANIZM LOGOWANIA
 * Sprawdza dane z formularza POST z danymi zdefiniowanymi w pliku cfg.php
 */
if (isset($_POST['login_email'], $_POST['login_pass'])) {
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['zalogowany'] = 1;
    } else {
        $blad_logowania = 'Błędny login lub hasło';
    }
}

/**
 * ZABEZPIECZENIE DOSTĘPU
 * Jeśli użytkownik nie jest zalogowany, wyświetlany jest tylko formularz logowania.
 */
if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] != 1) {
    echo '<!DOCTYPE html><html lang="pl"><head><meta charset="UTF-8"><link rel="stylesheet" href="../css/admin.css"></head><body>';
    echo FormularzLogowania($blad_logowania);
    echo '</body></html>';
    exit;
}

// --- LOGIKA BAZY DANYCH (PRZETWARZANIE FORMULARZY I AKCJI GET) ---

/**
 * 1. Zapis edycji istniejącej podstrony (CMS)
 */
if (isset($_POST['edytuj_submit'], $_GET['id'])) {
    $id_edytuj = (int)$_GET['id'];
    $nowy_tytul = mysqli_real_escape_string($link, $_POST['page_title']);
    $nowa_tresc = mysqli_real_escape_string($link, $_POST['page_content']);
    $nowy_status = isset($_POST['status']) ? 1 : 0;
    mysqli_query($link, "UPDATE page_list SET page_title='$nowy_tytul', page_content='$nowa_tresc', status='$nowy_status' WHERE id='$id_edytuj' LIMIT 1");
    header('Location: admin.php'); exit;
}

/**
 * 2. Dodawanie nowej podstrony do bazy (CMS)
 */
if (isset($_POST['dodaj_submit'])) {
    $tytul = mysqli_real_escape_string($link, $_POST['add_page_title']);
    $tresc = mysqli_real_escape_string($link, $_POST['add_page_content']);
    $status = isset($_POST['add_status']) ? 1 : 0;
    mysqli_query($link, "INSERT INTO page_list (page_title, page_content, status) VALUES ('$tytul', '$tresc', '$status') LIMIT 1");
    header('Location: admin.php'); exit;
}

/**
 * 3. Usuwanie podstrony (CMS)
 */
if (isset($_GET['funkcja'], $_GET['id']) && $_GET['funkcja'] == 'usun') {
    $id_del = (int)$_GET['id'];
    mysqli_query($link, "DELETE FROM page_list WHERE id='$id_del' LIMIT 1");
    header('Location: admin.php'); exit;
}

/**
 * 4. Logika dodawania nowej kategorii (Sklep)
 */
if (isset($_POST['kat_dodaj_submit'])) {
    $nazwa = mysqli_real_escape_string($link, $_POST['kat_nazwa']);
    $matka = (int)$_POST['kat_matka'];
    mysqli_query($link, "INSERT INTO kategorie (nazwa, matka) VALUES ('$nazwa', '$matka') LIMIT 1");
    header('Location: admin.php?funkcja=kategorie');
    exit;
}

/**
 * 5. Usuwanie kategorii (Sklep)
 */
if (isset($_GET['funkcja'], $_GET['akcja'], $_GET['id']) && $_GET['funkcja'] == 'kategorie' && $_GET['akcja'] == 'usun') {
    $id_kat_del = (int)$_GET['id'];
    mysqli_query($link, "DELETE FROM kategorie WHERE id = $id_kat_del LIMIT 1");
    header('Location: admin.php?funkcja=kategorie');
    exit;
}

/**
 * 6. Logika dodawania nowego produktu (Zadanie 1, Lab 11)
 * Zapisuje komplet danych produktu wraz z linkiem do zdjęcia i datą wygaśnięcia.
 */
if (isset($_POST['prod_dodaj_submit'])) {
    $tytul = mysqli_real_escape_string($link, $_POST['prod_tytul']);
    $opis = mysqli_real_escape_string($link, $_POST['prod_opis']);
    $cena = (float)$_POST['prod_cena_netto'];
    $vat = (float)$_POST['prod_vat'];
    $ilosc = (int)$_POST['prod_ilosc'];
    $kat = (int)$_POST['prod_kategoria'];
    $gabaryt = mysqli_real_escape_string($link, $_POST['prod_gabaryt']);
    $foto = mysqli_real_escape_string($link, $_POST['prod_foto']);
    $data_wyg = $_POST['prod_data_wyg'] ?: '0000-00-00 00:00:00';

    $query = "INSERT INTO produkty (tytul, opis, cena_netto, podatek_vat, ilosc_magazyn, kategoria_id, gabaryt, zdjecie_link, data_wygasniecia, status_dostepnosci) 
              VALUES ('$tytul', '$opis', '$cena', '$vat', '$ilosc', '$kat', '$gabaryt', '$foto', '$data_wyg', 1)";
    
    mysqli_query($link, $query);
    header('Location: admin.php?funkcja=produkty');
    exit;
}

/**
 * 7. Logika usuwania produktu ze sklepu
 */
if (isset($_GET['funkcja'], $_GET['akcja'], $_GET['id']) && $_GET['funkcja'] == 'produkty' && $_GET['akcja'] == 'usun_prod') {
    $id_prod = (int)$_GET['id'];
    mysqli_query($link, "DELETE FROM produkty WHERE id = $id_prod LIMIT 1");
    header('Location: admin.php?funkcja=produkty');
    exit;
}

/**
 * 8. Logika edycji produktu (Lab 11)
 * Aktualizuje dane oraz ustawia automatycznie 'data_modyfikacji' na obecny czas.
 */
if (isset($_POST['prod_edytuj_submit']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $tytul = mysqli_real_escape_string($link, $_POST['edit_prod_tytul']);
    $opis = mysqli_real_escape_string($link, $_POST['edit_prod_opis']);
    $cena = (float)$_POST['edit_prod_cena'];
    $vat = (float)$_POST['edit_prod_vat'];
    $ilosc = (int)$_POST['edit_prod_ilosc'];
    $kat = (int)$_POST['edit_prod_kategoria'];
    $gabaryt = mysqli_real_escape_string($link, $_POST['edit_prod_gabaryt']);
    $foto = mysqli_real_escape_string($link, $_POST['edit_prod_foto']);
    
    // Konwersja daty z formularza na format zrozumiały dla MySQL
    $data_wyg = !empty($_POST['edit_prod_data_wyg']) 
        ? date('Y-m-d H:i:s', strtotime($_POST['edit_prod_data_wyg'])) 
        : '0000-00-00 00:00:00';

    $query = "UPDATE produkty SET 
                tytul='$tytul', 
                opis='$opis', 
                cena_netto='$cena', 
                podatek_vat='$vat', 
                ilosc_magazyn='$ilosc', 
                kategoria_id='$kat', 
                gabaryt='$gabaryt', 
                zdjecie_link='$foto', 
                data_wygasniecia='$data_wyg',
                data_modyfikacji = NOW()
              WHERE id=$id LIMIT 1";
    
    mysqli_query($link, $query);
    header('Location: admin.php?funkcja=produkty'); 
    exit;
}

/* --- FUNKCJE GENERUJĄCE WIDOKI --- */

/**
 * Wyświetla prosty formularz logowania.
 */
function FormularzLogowania($blad = '') {
    return '<div class="logowanie"><form method="post"><input type="text" name="login_email" placeholder="Login" required /><input type="password" name="login_pass" placeholder="Hasło" required /><p class="blad_logowania">'.$blad.'</p><button type="submit">Zaloguj</button></form></div>';
}

/**
 * Wyświetla tabelę z listą podstron CMS.
 */
function ListaPodstron() {
    global $link;
    $result = mysqli_query($link, "SELECT id, page_title FROM page_list ORDER BY id DESC LIMIT 100");
    echo '<div class="table-container"><table><tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>';
    while($row = mysqli_fetch_array($result)) {
        echo '<tr><td>'.$row['id'].'</td><td>'.$row['page_title'].'</td><td><a href="admin.php?funkcja=edytuj&id='.$row['id'].'" class="edit">Edytuj</a> <a href="admin.php?funkcja=usun&id='.$row['id'].'" class="delete">Usuń</a></td></tr>';
    }
    echo '</table><a href="admin.php?funkcja=dodaj" class="btn-add">Dodaj nową stronę</a></div>';
}

/**
 * Generuje formularz edycji treści podstrony (CMS).
 * Wykorzystuje klasy checkbox-fix dla poprawnego wyglądu pola statusu.
 */
function EdytujPodstrone($id) {
    global $link;
    $row = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM page_list WHERE id = '$id' LIMIT 1"));
    $checked = ($row['status'] == 1) ? 'checked' : '';
    
    return '
    <div class="edycja">
        <h1>Edycja: '.$row['page_title'].'</h1>
        <form method="post">
            <label>Tytuł:</label>
            <input type="text" name="page_title" value="'.htmlspecialchars($row['page_title']).'" />
            
            <label>Treść:</label>
            <textarea name="page_content" rows="15">'.htmlspecialchars($row['page_content']).'</textarea>
            
            <label class="label-inline">
                <input type="checkbox" name="status" '.$checked.' class="checkbox-fix" /> 
                Strona aktywna
            </label>
            
            <input type="submit" name="edytuj_submit" value="Zapisz" />
            <a href="admin.php" style="text-align:center; display:block; margin-top:10px;">Anuluj</a>
        </form>
    </div>';
}

/**
 * Generuje pusty formularz do tworzenia nowej podstrony (CMS).
 */
function DodajNowaPodstrone() {
    return '
    <div class="dodawanie">
        <h1>Dodaj nową stronę</h1>
        <form method="post">
            <label>Tytuł:</label>
            <input type="text" name="add_page_title" required />
            
            <label>Treść:</label>
            <textarea name="add_page_content" rows="15"></textarea>
            
            <label class="label-inline">
                <input type="checkbox" name="add_status" checked class="checkbox-fix" /> 
                Strona aktywna
            </label>
            
            <input type="submit" name="dodaj_submit" value="Dodaj stronę" />
            <a href="admin.php" style="text-align:center; display:block; margin-top:10px;">Anuluj</a>
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
/**
 * NAWIGACJA I ROUTING PANELU
 * Wykrywa aktywne sekcje na podstawie parametrów GET i zarządza menu.
 */
$funkcja_get = $_GET['funkcja'] ?? '';
$akcja_get = $_GET['akcja'] ?? '';

// Określenie, która sekcja jest aktualnie otwarta
$czy_kategorie = ($funkcja_get == 'kategorie' || $akcja_get == 'dodaj_kat');
$czy_produkty  = ($funkcja_get == 'produkty'   || $akcja_get == 'dodaj_prod' || $akcja_get == 'edytuj_prod');
$czy_strony    = (!$czy_kategorie && !$czy_produkty);

// Klasy CSS dla aktywnego elementu menu
$active_strony = $czy_strony ? 'active' : '';
$active_kat    = $czy_kategorie ? 'active' : '';
$active_prod   = $czy_produkty ? 'active' : '';

echo '
<div class="admin-nav">
    <a href="admin.php" class="nav-item '.$active_strony.'">Zarządzaj Stronami</a>
    <a href="admin.php?funkcja=kategorie" class="nav-item '.$active_kat.'">Zarządzaj Kategoriami Sklepu</a>
    <a href="admin.php?funkcja=produkty" class="nav-item '.$active_prod.'">Zarządzaj Produktami</a>
</div>';

/**
 * WYŚWIETLANIE ODPOWIEDNIEGO WIDOKU (CONTENTU)
 * Zależnie od wybranej sekcji w menu i akcji (dodaj/edytuj/lista).
 */
if ($czy_produkty) {
    // Widoki sekcji Produkty
    if ($akcja_get == 'dodaj_prod') echo FormularzDodawaniaProduktu();
    elseif ($akcja_get == 'edytuj_prod' && isset($_GET['id'])) echo EdytujProdukt($_GET['id']);
    else PokazProdukty();
} elseif ($czy_kategorie) {
    // Widoki sekcji Kategorie
    if ($akcja_get == 'dodaj_kat') {
        echo FormularzDodawaniaKategorii();
    } else {
        ZarzadzajKategoriami(); 
    }
} else {
    // Widoki sekcji Strony CMS
    if ($funkcja_get == 'edytuj' && isset($_GET['id'])) echo EdytujPodstrone($_GET['id']);
    elseif ($funkcja_get == 'dodaj') echo DodajNowaPodstrone();
    else ListaPodstron();
}
?>
</body>
</html>