<?php

// Plik odpowiedzialny za zabijanie sesji (ten plik jest potrzebny do wylogowywania użytkownika)

session_start();
session_destroy();
header("Location: sklep.php");
exit;
?>