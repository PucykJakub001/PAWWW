<?php

// pokazywanie strony z bazy danych i pokazywanie jej na podstwie ID

function PokazPodstrone($id)
{
    global $link;

    $id_clear = (int)$id; 
    $id_safe  = mysqli_real_escape_string($link, $id_clear);

    $query  = "SELECT page_content FROM page_list WHERE id='$id_safe' AND status='1' LIMIT 1"; 
    $result = mysqli_query($link, $query); 
    $row    = mysqli_fetch_array($result);

    if (empty($row['page_content']))
    {
        $web = '[ Strona o podanym ID nie istnieje lub jest obecnie nieaktywna ]'; 
    }
    else
    {
        $web = $row['page_content']; 
    }

    return $web;
}

?>