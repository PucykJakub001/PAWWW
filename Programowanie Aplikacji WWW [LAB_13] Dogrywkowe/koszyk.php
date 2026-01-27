<?php
// Funkcja odpowiedzialna za zarządzanie koszykiem

function addToCart($id_prod, $ile_sztuk) {
    if (!isset($_SESSION['count'])) {
        $_SESSION['count'] = 1;
    } else {
        $_SESSION['count']++;
    }

    $nr = $_SESSION['count'];
    
    $_SESSION[$nr.'_0'] = $nr;           // Identyfikator wpisu w koszyku
    $_SESSION[$nr.'_1'] = $id_prod;      // ID produktu z bazy danych
    $_SESSION[$nr.'_2'] = $ile_sztuk;    // Ilość sztuk
    $_SESSION[$nr.'_3'] = time();        // Timestamp dodania
}

// Funkcja usuwająca element z koszyka
function removeFromCart($nr) {
    unset($_SESSION[$nr.'_0']);
    unset($_SESSION[$nr.'_1']);
    unset($_SESSION[$nr.'_2']);
    unset($_SESSION[$nr.'_3']); //
}

// Funkcja wyświetlająca zawartość koszyka
function showCart() {
    global $link;
    $suma_brutto = 0;

    echo '<div class="koszyk-sekcja">';
    echo '<h1>Twój Koszyk</h1>';
    echo '<table>
            <tr>
                <th>Produkt</th>
                <th>Ilość</th>
                <th>Cena Netto</th>
                <th>VAT</th>
                <th>Suma Brutto</th>
                <th>Akcje</th>
            </tr>';

    for ($i = 1; $i <= ($_SESSION['count'] ?? 0); $i++) {
        if (isset($_SESSION[$i.'_0'])) {
            $id_p = $_SESSION[$i.'_1'];
            $ile = $_SESSION[$i.'_2'];

            $res = mysqli_query($link, "SELECT tytul, cena_netto, podatek_vat FROM produkty WHERE id = '$id_p' LIMIT 1");
            $prod = mysqli_fetch_assoc($res);

            // Obliczanie ceny brutto
            $cena_brutto_jednostkowa = $prod['cena_netto'] * (1 + ($prod['podatek_vat'] / 100));
            $razem_brutto = $cena_brutto_jednostkowa * $ile;
            $suma_brutto += $razem_brutto;

            echo '<tr>
                    <td>'.$prod['tytul'].'<br><small>Dodano: '.date('H:i:s', $_SESSION[$i.'_3']).'</small></td>
                    <td>
                        <form method="post" action="sklep.php?akcja=edytuj&nr='.$i.'">
                            <input type="number" name="nowa_ilosc" value="'.$ile.'" min="1" style="width:45px;">
                            <input type="submit" value="Zmień" class="btn-small">
                        </form>
                    </td>
                    <td>'.number_format($prod['cena_netto'], 2).' zł</td>
                    <td>'.$prod['podatek_vat'].'%</td>
                    <td>'.number_format($razem_brutto, 2).' zł</td>
                    <td><a href="sklep.php?akcja=usun&nr='.$i.'" class="btn-usun">Usuń</a></td>
                </tr>';
        }
    }

    echo '</table>';
    echo '<div class="suma-brutto">Łącznie do zapłaty: '.number_format($suma_brutto, 2).' zł</div>';
    echo '<br><a href="podsumowanie.php" style="background: #2c3e50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block;">PRZEJDŹ DO PODSUMOWANIA</a>';
    echo '</div>';
}
?>