<?php

function PokazProdukty() {
    global $link;
    // Pobieranie wszystkich produktów od najnowszych
    $query = "SELECT * FROM produkty ORDER BY id DESC";
    $result = mysqli_query($link, $query);

    echo '<div class="table-container">';
    echo '<h1>Zarządzanie Produktami</h1>';
    echo '<a href="admin.php?funkcja=produkty&akcja=dodaj_prod" class="btn-add">Dodaj nowy produkt</a>';
    echo '<table>
            <tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Cena Netto</th>
                <th>Ilość</th>
                <th>Status (Logika)</th>
                <th>Akcje</th>
            </tr>';
    
    // Iteracja przez każdy produkt w bazie
    while($row = mysqli_fetch_array($result)) {
        $dzis = date('Y-m-d H:i:s');
        $czy_dostepny = true;

        // 1. Sprawdzenie statusu ogólnego 
        if ($row['status_dostepnosci'] == 0) $czy_dostepny = false;
        // 2. Sprawdzenie czy towar jest w magazynie 
        if ($row['ilosc_magazyn'] <= 0) $czy_dostepny = false;
        // 3. Sprawdzenie czy oferta nie wygasła 
        if ($row['data_wygasniecia'] !== '0000-00-00 00:00:00' && $row['data_wygasniecia'] < $dzis) $czy_dostepny = false;

        // Określenie wizualnego statusu
        $status_text = $czy_dostepny ? '<span style="color:green;">Dostępny</span>' : '<span style="color:red;">Niedostępny / Wygasł</span>';

        echo '<tr>
                <td>'.$row['id'].'</td>
                <td>'.$row['tytul'].'</td>
                <td>'.$row['cena_netto'].' zł</td>
                <td>'.$row['ilosc_magazyn'].' szt.</td>
                <td>'.$status_text.'</td>
                <td>
                    <a href="admin.php?funkcja=produkty&akcja=edytuj_prod&id='.$row['id'].'" class="edit">Edytuj</a>
                    <a href="admin.php?funkcja=produkty&akcja=usun_prod&id='.$row['id'].'" class="delete">Usuń</a>
                </td>
              </tr>';
    }
    echo '</table></div>';
}

function FormularzDodawaniaProduktu() {
    global $link;
    
    // Pobieranie kategorii (tylko podkategorie, czyli matka != 0)
    $kat_res = mysqli_query($link, "SELECT id, nazwa FROM kategorie WHERE matka != 0");
    $opcje_kat = '';
    while($k = mysqli_fetch_assoc($kat_res)) {
        $opcje_kat .= '<option value="'.$k['id'].'">'.$k['nazwa'].'</option>';
    }

    return '
    <div class="dodawanie">
        <h1>Dodaj Nowy Produkt</h1>
        <form method="post" action="admin.php?funkcja=produkty">

            <label>Tytuł produktu:</label>
            <input type="text" name="prod_tytul" required />

            <label>Producent:</label>
            <input type="text" name="prod_producent" placeholder="np. Commodore, Apple, Atari" required />

            <label>Opis:</label>
            <textarea name="prod_opis" rows="5"></textarea>

            <label>Cena netto:</label>
            <input type="number" step="0.01" name="prod_cena_netto" required />

            <label>Podatek VAT (%):</label>
            <input type="number" step="0.01" name="prod_vat" value="23.00" />

            <label>Ilość w magazynie:</label>
            <input type="number" name="prod_ilosc" value="1" />

            <label>Kategoria:</label>
            <select name="prod_kategoria">'.$opcje_kat.'</select>

            <label>Data wygaśnięcia (opcjonalnie):</label>
            <input type="datetime-local" name="prod_data_wyg" />

            <label>Gabaryt produktu:</label>
            <input type="text" name="prod_gabaryt" placeholder="np. Mały, Klasa A" />

            <label>Link do zdjęcia:</label>
            <input type="text" name="prod_foto" placeholder="http://..." />

            <input type="submit" name="prod_dodaj_submit" value="Zapisz produkt" />
            <a href="admin.php?funkcja=produkty" style="text-align:center; display:block; margin-top:10px; color:#aaa;">Anuluj</a>
        </form>
    </div>';
}


function EdytujProdukt($id) {
    global $link;
    // Pobranie danych wybranego produktu
    $row = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM produkty WHERE id = '$id' LIMIT 1"));
    
    // Pobieranie kategorii i zaznaczenie obecnie wybranej
    $kat_res = mysqli_query($link, "SELECT id, nazwa FROM kategorie WHERE matka != 0");
    $opcje_kat = '';
    while($k = mysqli_fetch_assoc($kat_res)) {
        $selected = ($k['id'] == $row['kategoria_id']) ? 'selected' : '';
        $opcje_kat .= '<option value="'.$k['id'].'" '.$selected.'>'.$k['nazwa'].'</option>';
    }

    // Formatowanie daty wygaśnięcia z bazy 
    $data_wyg_format = ($row['data_wygasniecia'] && $row['data_wygasniecia'] != '0000-00-00 00:00:00') 
        ? date('Y-m-d\TH:i', strtotime($row['data_wygasniecia'])) 
        : '';

return '
    <div class="edycja">
        <h1>Edytuj Produkt: '.$row['tytul'].'</h1>
        <form method="post" action="admin.php?funkcja=produkty&id='.$id.'">
            <label>Tytuł:</label>
            <input type="text" name="edit_prod_tytul" value="'.htmlspecialchars($row['tytul']).'" required />
            <label>Producent:</label>
            <input type="text" name="edit_prod_producent" value="'.htmlspecialchars($row['producent']).'" required />
            <label>Opis:</label><textarea name="edit_prod_opis" rows="5">'.htmlspecialchars($row['opis']).'</textarea>
            <label>Cena netto:</label><input type="number" step="0.01" name="edit_prod_cena" value="'.$row['cena_netto'].'" />
            <label>Podatek VAT:</label><input type="number" step="0.01" name="edit_prod_vat" value="'.$row['podatek_vat'].'" />
            <label>Ilość:</label><input type="number" name="edit_prod_ilosc" value="'.$row['ilosc_magazyn'].'" />
            <label>Kategoria:</label><select name="edit_prod_kategoria">'.$opcje_kat.'</select>
            <label>Gabaryt:</label><input type="text" name="edit_prod_gabaryt" value="'.$row['gabaryt'].'" />
            <label>Link do zdjęcia:</label><input type="text" name="edit_prod_foto" value="'.$row['zdjecie_link'].'" />
            
            <label>Data wygaśnięcia:</label>
            <input type="datetime-local" name="edit_prod_data_wyg" value="'.$data_wyg_format.'" />
            
            <div style="margin-top:20px; text-align:center;">
                <input type="submit" name="prod_edytuj_submit" value="Zapisz zmiany" />
                <br><a href="admin.php?funkcja=produkty">Anuluj</a>
            </div>
        </form>
    </div>';
}
?>