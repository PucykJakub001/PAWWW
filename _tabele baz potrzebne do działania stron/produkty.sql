-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sty 20, 2026 at 11:44 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moja_strona`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `produkty`
--

CREATE TABLE `produkty` (
  `id` int(11) NOT NULL,
  `tytul` varchar(255) NOT NULL,
  `opis` text DEFAULT NULL,
  `data_utworzenia` datetime DEFAULT current_timestamp(),
  `data_modyfikacji` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `data_wygasniecia` datetime DEFAULT NULL,
  `cena_netto` decimal(10,2) NOT NULL,
  `podatek_vat` decimal(5,2) DEFAULT 23.00,
  `ilosc_magazyn` int(11) DEFAULT 0,
  `status_dostepnosci` int(11) DEFAULT 1,
  `kategoria_id` int(11) DEFAULT NULL,
  `gabaryt` varchar(50) DEFAULT NULL,
  `zdjecie_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produkty`
--

INSERT INTO `produkty` (`id`, `tytul`, `opis`, `data_utworzenia`, `data_modyfikacji`, `data_wygasniecia`, `cena_netto`, `podatek_vat`, `ilosc_magazyn`, `status_dostepnosci`, `kategoria_id`, `gabaryt`, `zdjecie_link`) VALUES
(3, 'Amiga 500', 'sprzedam tanio ', '2026-01-20 18:07:15', '2026-01-20 18:37:14', '2026-01-20 18:37:00', 12222.00, 23.00, 5, 1, 9, '100x100x30', 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Amiga500_system.jpg/1280px-Amiga500_system.jpg'),
(4, 'macintosh 128k (2077)', 'macintosh sprzedam tanio. Stan ok ', '2026-01-20 18:17:06', '2026-01-20 18:36:54', '2026-01-21 18:17:00', 5000.00, 23.00, 1, 1, 14, '140x100x30', 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Amiga500_system.jpg/1280px-Amiga500_system.jpg'),
(6, 'macintosh 128k (1983) - STAN IGIEŁKA', 'iofhiafgaweiofgaf', '2026-01-20 18:44:57', '2026-01-20 18:45:06', '2026-01-22 18:44:00', 1111.00, 23.00, 1, 1, 14, 'no taki średni', 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Amiga500_system.jpg/1280px-Amiga500_system.jpg');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `produkty`
--
ALTER TABLE `produkty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategoria_id` (`kategoria_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `produkty`
--
ALTER TABLE `produkty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `produkty`
--
ALTER TABLE `produkty`
  ADD CONSTRAINT `produkty_ibfk_1` FOREIGN KEY (`kategoria_id`) REFERENCES `kategorie` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
