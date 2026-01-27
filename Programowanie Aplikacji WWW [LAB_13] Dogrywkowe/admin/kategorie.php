<?php

// zarządzanie kategoriami w sklepie
function ZarzadzajKategoriami() {
    global $link;

    echo '<div class="table-container">';
    echo '<h1>Kategorie Sklepu Retro</h1>';
    echo '<a href="admin.php?funkcja=kategorie&akcja=dodaj_kat" class="btn-add">Dodaj nową markę / model</a>';

    echo '<table>';
    echo '<tr><th>ID</th><th>Nazwa (Marka / Model)</th><th>Akcje</th></tr>';

    $res_matki = mysqli_query($link, "SELECT * FROM kategorie WHERE matka = 0 LIMIT 50");
    while ($matka = mysqli_fetch_assoc($res_matki)) {
        echo '<tr style="background-color: #333; font-weight: bold;">';
        echo '<td>'.$matka['id'].'</td>';
        echo '<td style="text-align: left; padding-left: 20px;">' . strtoupper($matka['nazwa']) . '</td>';
        echo '<td><a href="admin.php?funkcja=kategorie&akcja=usun&id='.$matka['id'].'" class="delete">Usuń markę</a></td>';
        echo '</tr>';

        $id_m = $matka['id'];
        $res_dzieci = mysqli_query($link, "SELECT * FROM kategorie WHERE matka = $id_m LIMIT 50");
        while ($dziecko = mysqli_fetch_assoc($res_dzieci)) {
            // Wyświetlanie wiersza modelu (wcięcie i inny kolor dla odróżnienia od marki)
            echo '<tr>';
            echo '<td>'.$dziecko['id'].'</td>';
            echo '<td style="text-align: left; padding-left: 50px; color: #aaa;">— ' . $dziecko['nazwa'] . '</td>';
            echo '<td><a href="admin.php?funkcja=kategorie&akcja=usun&id='.$dziecko['id'].'" class="delete">Usuń model</a></td>';
            echo '</tr>';
        }
    }
    echo '</table></div>';
}



function FormularzDodawaniaKategorii() {
    global $link;
    
    $res = mysqli_query($link, "SELECT id, nazwa FROM kategorie WHERE matka = 0");
    
    $opcje = '<option value="0">--- Nowa Marka (Główna) ---</option>';
    while($row = mysqli_fetch_array($res)) {
        $opcje .= '<option value="'.$row['id'].'">'.$row['nazwa'].'</option>';
    }

    return '
    <div class="dodawanie">
        <h1>Dodaj Pozycję do Bazy</h1>
        <form method="post" action="admin.php?funkcja=kategorie">
            <label>Nazwa modelu/marki:</label>
            <input type="text" name="kat_nazwa" required style="width: 100%; padding: 10px; margin: 10px 0; background: #2a2a2a; color: white; border: 1px solid #444; border-radius: 5px;" />
            
            <label>Rodzic (Marka):</label>
            <select name="kat_matka" style="width: 100%; padding: 10px; margin: 10px 0; background: #2a2a2a; color: white; border: 1px solid #444; border-radius: 5px;">
                '.$opcje.'
            </select>
            
            <div style="text-align: center; margin-top: 20px;">
                <input type="submit" name="kat_dodaj_submit" value="Zatwierdź i dodaj" />
                <br><br>
                <a href="admin.php?funkcja=kategorie" style="color: #888;">Anuluj i wróć</a>
            </div>
        </form>
    </div>';
}
?>