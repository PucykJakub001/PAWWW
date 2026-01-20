<?php

function PokazPodstrone($id)
{
    global $link;


    $id_clear = htmlspecialchars($id); 
    $query = "SELECT * FROM page_list WHERE id='$id_clear' AND status='1' LIMIT 1"; 
    $result = mysqli_query($link, $query); 
    $row = mysqli_fetch_array($result);

    if(empty($row['id']))
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