<?php
session_start();
include('../cfg.php');

/*błąd logowania*/
$blad_logowania = '';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Administratora - CMS</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php
/*Formularz logowania*/
function FormularzLogowania($blad = '') {
    return '
    <div class="logowanie">
        <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
            <input type="text" name="login_email" placeholder="Login" required />
            <input type="password" name="login_pass" placeholder="Hasło" required />

            <p class="blad_logowania">'.$blad.'</p>

            <button type="submit" name="xl_submit">Zaloguj</button>
        </form>
    </div>';
}

/*generator listy podstron*/
function ListaPodstron() {
    global $link;
    $query = "SELECT id, page_title FROM page_list ORDER BY id DESC LIMIT 100";
    $result = mysqli_query($link, $query);

    echo '<div class="table-container">';
    echo '<table border="1" cellpadding="5">
            <tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>';
    while($row = mysqli_fetch_array($result)) {
        echo '<tr>
                <td>'.$row['id'].'</td>
                <td>'.$row['page_title'].'</td>
                <td>
                    <a href="admin.php?funkcja=edytuj&id='.$row['id'].'" class="edit"> Edytuj </a> 
                    <a href="admin.php?funkcja=usun&id='.$row['id'].'" class="delete"> Usuń </a>
                </td>
              </tr>';
    }
    echo '</table>';
    echo '<a href="admin.php?funkcja=dodaj" class="btn-add">Dodaj nową</a>'; 
    echo '</div>';

}

/*Edytor podstrony */
function EdytujPodstrone($id) {
    global $link;
    $query = "SELECT * FROM page_list WHERE id = '$id' LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);

    return '
    <div class="edycja">
        <h1>Edycja: '.$row['page_title'].' (ID: '.$id.')</h1>
        <form method="post" action="admin.php?funkcja=edytuj&id='.$id.'">
            <label>Tytuł:</label><br />
            <input type="text" name="page_title" value="'.htmlspecialchars($row['page_title']).'" style="width:500px;" /><br /><br />
            
            <label>Treść HTML:</label><br />
            <textarea name="page_content" rows="20" cols="100">'.htmlspecialchars($row['page_content']).'</textarea><br /><br />
            
            <label>Status (1-aktywna, 0-ukryta):</label>
            <input type="checkbox" name="status" '.($row['status'] == 1 ? 'checked' : '').' /><br /><br />
            
            <input type="submit" name="edytuj_submit" value="Zapisz zmiany" />
            <a href="admin.php">Wróć do listy</a>
        </form>
    </div>';
}

/* Kreator nowej podstrony */
function DodajNowaPodstrone() {
    return '
    <div class="dodawanie">
        <h1>Dodaj nową podstronę</h1>
        <form method="post" action="admin.php?funkcja=dodaj">
            <label>Tytuł strony:</label><br />
            <input type="text" name="add_page_title" style="width:500px;" required /><br /><br />
            
            <label>Treść strony (HTML):</label><br />
            <textarea name="add_page_content" rows="20" cols="100"></textarea><br /><br />
            
            <label>Status (aktywna):</label>
            <input type="checkbox" name="add_status" checked /><br /><br />
            
            <input type="submit" name="dodaj_submit" value="Dodaj podstronę" />
            <a href="admin.php">Wróć do listy</a>
        </form>
    </div>';
}

/* okno logowania*/
if (isset($_POST['login_email'], $_POST['login_pass'])) {
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['zalogowany'] = 1;
    } else {
        $blad_logowania = 'Błędny login lub hasło';
    }
}

/*komunikat o błędnym logowaniu*/
if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] != 1) {
    echo FormularzLogowania($blad_logowania);
    exit;
}


/*Logika */

// Zapis edycji
if (isset($_POST['edytuj_submit'], $_GET['id'])) {
    $id_edytuj = (int)$_GET['id'];
    $nowy_tytul = mysqli_real_escape_string($link, $_POST['page_title']);
    $nowa_tresc = mysqli_real_escape_string($link, $_POST['page_content']);
    $nowy_status = isset($_POST['status']) ? 1 : 0;

    $query = "UPDATE page_list 
              SET page_title='$nowy_tytul', page_content='$nowa_tresc', status='$nowy_status'
              WHERE id='$id_edytuj' LIMIT 1";

    if (mysqli_query($link, $query)) {
        echo '<p style="color:green;">ZAPISANO POPRAWNIE!</p>';
    }
}

// Dodawanie strony
if (isset($_POST['dodaj_submit'])) {
    $tytul = mysqli_real_escape_string($link, $_POST['add_page_title']);
    $tresc = mysqli_real_escape_string($link, $_POST['add_page_content']);
    $status = isset($_POST['add_status']) ? 1 : 0;

    $query = "INSERT INTO page_list (page_title, page_content, status)
              VALUES ('$tytul', '$tresc', '$status') LIMIT 1";

    if (mysqli_query($link, $query)) {
        echo '<p style="color:green;">NOWA STRONA DODANA!</p>';
    }
}

// Usuwanie
if (isset($_GET['funkcja'], $_GET['id']) && $_GET['funkcja'] == 'usun') {
    $id_del = (int)$_GET['id'];
    mysqli_query($link, "DELETE FROM page_list WHERE id='$id_del' LIMIT 1");
    echo '<p style="color:blue;">Podstrona o ID '.$id_del.' została usunięta.</p>';
}

/* Widoki */
if (isset($_GET['funkcja'])) {
    if ($_GET['funkcja'] == 'edytuj' && isset($_GET['id'])) {
        echo EdytujPodstrone($_GET['id']);
    } elseif ($_GET['funkcja'] == 'dodaj') {
        echo DodajNowaPodstrone();
    } else {
        ListaPodstron();
    }
} else {
    ListaPodstron();
}
?>
</body>
</html>
