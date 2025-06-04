-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Cze 04, 2025 at 07:29 PM
-- Wersja serwera: 8.0.42
-- Wersja PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `to_projekt`
--

DELIMITER $$
--
-- Procedury
--
CREATE DEFINER=`root`@`%` PROCEDURE `prc_add_order` (IN `p_company` VARCHAR(100), IN `p_from` VARCHAR(100), IN `p_to` VARCHAR(100), IN `p_cargo` VARCHAR(100), IN `p_weight` FLOAT)   BEGIN
    DECLARE v_company INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;

    -- 1. Sprawdź, czy firma istnieje
    SELECT ID_company
      INTO v_company
      FROM company
     WHERE name_company = p_company
     LIMIT 1;

    -- 2. Jeśli nie istnieje, wstaw nową i pobierz jej ID
    IF v_company IS NULL THEN
        INSERT INTO company(name_company)
             VALUES(p_company);
        SET v_company = LAST_INSERT_ID();
    END IF;

    -- 3. Dodaj rekord do order_list
    INSERT INTO order_list
        (ID_company, location_from, location_to, cargo, weight)
    VALUES
        (v_company, p_from, p_to, p_cargo, p_weight);
        
    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `company`
--

CREATE TABLE `company` (
  `ID_company` int NOT NULL,
  `name_company` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Zrzut danych tabeli `company`
--

INSERT INTO `company` (`ID_company`, `name_company`) VALUES
(1, 'Trzask GMBH'),
(2, 'NaWrotki Sp. z o.o.'),
(3, 'test');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `driver`
--

CREATE TABLE `driver` (
  `ID_driver` int NOT NULL,
  `name_driver` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Zrzut danych tabeli `driver`
--

INSERT INTO `driver` (`ID_driver`, `name_driver`) VALUES
(1, 'Janusz Mikke'),
(2, 'Władysław Kamysz');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `events`
--

CREATE TABLE `events` (
  `id` bigint NOT NULL,
  `type` varchar(100) NOT NULL,
  `payload` json NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Zrzut danych tabeli `events`
--

INSERT INTO `events` (`id`, `type`, `payload`, `created_at`) VALUES
(1, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"TEST\", \"pickup\": \"TEST\", \"delivery\": \"TEST\"}', '2025-06-02 22:47:17'),
(2, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"TEST\", \"pickup\": \"0.002\", \"delivery\": \"TEST\"}', '2025-06-03 20:56:37'),
(3, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"TEST\", \"pickup\": \"NaWrotki Sp. z o.o \", \"delivery\": \"TEST\"}', '2025-06-03 21:11:48'),
(4, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"test\", \"pickup\": \"test\", \"weight\": \"0.001\", \"company\": \"test\", \"delivery\": \"test\"}', '2025-06-03 21:12:54'),
(5, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"etet\", \"pickup\": \"tet\", \"weight\": \"0.001\", \"company\": \"TEST\", \"delivery\": \"etet\"}', '2025-06-03 21:25:41'),
(6, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"etet\", \"pickup\": \"tet\", \"weight\": \"0.001\", \"company\": \"TEST\", \"delivery\": \"etet\"}', '2025-06-03 21:26:02'),
(7, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"ww\", \"pickup\": \"ww\", \"weight\": \"0.001\", \"company\": \"test\", \"delivery\": \"ww\"}', '2025-06-03 21:26:10'),
(8, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"w\", \"pickup\": \"w\", \"weight\": \"0.001\", \"company\": \"t\", \"delivery\": \"w\"}', '2025-06-03 21:27:03'),
(9, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"w\", \"pickup\": \"w\", \"weight\": \"0.001\", \"company\": \"w\", \"delivery\": \"w\"}', '2025-06-03 21:28:04'),
(10, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"w\", \"pickup\": \"w\", \"weight\": \"0.001\", \"company\": \"w\", \"delivery\": \"w\"}', '2025-06-03 21:28:10'),
(11, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"s\", \"pickup\": \"s\", \"weight\": \"0.001\", \"company\": \"s\", \"delivery\": \"s\"}', '2025-06-03 21:29:25'),
(12, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"test\", \"pickup\": \"test\", \"weight\": \"0.001\", \"company\": \"test\", \"delivery\": \"test\"}', '2025-06-03 21:40:23'),
(13, 'App\\Event\\OrderValidated', '{\"orderId\": \"1\"}', '2025-06-03 21:55:26'),
(14, 'App\\Event\\OrderValidated', '{\"orderId\": \"1\"}', '2025-06-03 21:58:03'),
(15, 'App\\Event\\DriverAssigned', '{\"orderId\": \"1\", \"driverId\": \"1\"}', '2025-06-03 22:22:10'),
(16, 'App\\Event\\DriverAssigned', '{\"orderId\": \"1\", \"driverId\": \"1\"}', '2025-06-03 22:28:56'),
(17, 'App\\Event\\DriverAssigned', '{\"orderId\": \"1\", \"driverId\": \"1\"}', '2025-06-03 22:30:08'),
(18, 'App\\Event\\OrderValidated', '{\"orderId\": \"2\"}', '2025-06-03 22:32:21'),
(19, 'App\\Event\\OrderSubmitted', '{\"cargo\": \"52\", \"pickup\": \"22\", \"weight\": \"0.005\", \"company\": \"test\", \"delivery\": \"42\"}', '2025-06-03 22:54:50'),
(20, 'App\\Event\\DriverAssigned', '{\"orderId\": \"2\", \"driverId\": \"2\"}', '2025-06-03 22:55:33'),
(21, 'App\\Event\\OrderValidated', '{\"orderId\": \"3\"}', '2025-06-03 22:55:37'),
(22, 'App\\Event\\DriverAssigned', '{\"orderId\": \"2\", \"driverId\": \"2\"}', '2025-06-03 22:55:58'),
(23, 'App\\Event\\DriverAssigned', '{\"orderId\": \"2\", \"driverId\": \"2\"}', '2025-06-03 22:58:13'),
(24, 'App\\Event\\OrderValidated', '{\"price\": 0.48, \"orderId\": \"4\"}', '2025-06-04 18:14:12'),
(25, 'App\\Event\\OrderValidated', '{\"price\": 1000, \"orderId\": \"1\"}', '2025-06-04 18:16:31'),
(26, 'App\\Event\\OrderValidated', '{\"price\": 1500, \"orderId\": \"2\"}', '2025-06-04 18:17:09'),
(27, 'App\\Event\\OrderAccepted', '{\"orderId\": \"2\"}', '2025-06-04 18:37:42'),
(28, 'App\\Event\\OrderValidated', '{\"price\": 1500, \"orderId\": \"4\"}', '2025-06-04 18:38:03');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_list`
--

CREATE TABLE `order_list` (
  `ID_order` int NOT NULL,
  `ID_status` int NOT NULL DEFAULT '1',
  `ID_company` int NOT NULL,
  `location_from` varchar(100) NOT NULL,
  `location_to` varchar(100) NOT NULL,
  `cargo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `weight` float NOT NULL,
  `price` float DEFAULT NULL,
  `ID_driver` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Zrzut danych tabeli `order_list`
--

INSERT INTO `order_list` (`ID_order`, `ID_status`, `ID_company`, `location_from`, `location_to`, `cargo`, `weight`, `price`, `ID_driver`, `created_at`) VALUES
(1, 2, 1, 'Berlin', 'Warszawa', 'skrzydło wiatraku', 40000, 1000, 1, '2025-06-02 22:19:02'),
(2, 3, 2, 'Warszawa', 'Kraków', 'wrotki', 22000, 1500, NULL, '2025-06-02 22:47:17'),
(3, 1, 3, 'test', 'test', 'test', 0.001, NULL, NULL, '2025-06-03 21:40:23'),
(4, 2, 3, '22', '42', '52', 0.005, 1500, NULL, '2025-06-03 22:54:50');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `status`
--

CREATE TABLE `status` (
  `ID_status` int NOT NULL,
  `name_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Zrzut danych tabeli `status`
--

INSERT INTO `status` (`ID_status`, `name_status`) VALUES
(1, 'Utworzone'),
(2, 'Zatwierdzone przez spedytora'),
(3, 'Zaakceptowane przez klienta');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`ID_company`);

--
-- Indeksy dla tabeli `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`ID_driver`);

--
-- Indeksy dla tabeli `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`);

--
-- Indeksy dla tabeli `order_list`
--
ALTER TABLE `order_list`
  ADD PRIMARY KEY (`ID_order`),
  ADD KEY `ID_status` (`ID_status`),
  ADD KEY `ID_driver` (`ID_driver`),
  ADD KEY `ID_company` (`ID_company`);

--
-- Indeksy dla tabeli `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`ID_status`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `company`
--
ALTER TABLE `company`
  MODIFY `ID_company` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `driver`
--
ALTER TABLE `driver`
  MODIFY `ID_driver` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT dla tabeli `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT dla tabeli `order_list`
--
ALTER TABLE `order_list`
  MODIFY `ID_order` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `status`
--
ALTER TABLE `status`
  MODIFY `ID_status` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `order_list`
--
ALTER TABLE `order_list`
  ADD CONSTRAINT `order_list_ibfk_1` FOREIGN KEY (`ID_status`) REFERENCES `status` (`ID_status`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
