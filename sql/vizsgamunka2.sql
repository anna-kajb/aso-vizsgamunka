-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Máj 02. 20:19
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `vizsgamunka2`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('admin','user') DEFAULT 'user',
  `verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`, `verification_code`, `created_at`, `updated_at`, `role`, `verified`) VALUES
(7, 'admin1', 'vandavarasdi2002@gmail.com', '$2y$10$mQtoquDq/TpODi13lsavQ.BeM256stQyp99qj8EV0A1GokYBfq7q6', NULL, '2025-04-19 08:52:35', '2025-04-19 08:53:11', 'admin', 1),
(8, 'admin2', 'kajbanna21@gmail.com', '$2y$10$k789FQilN13jnWWcX0Ksjeyp6kdezhpaMgSAmNJiM/rpI/ebJLH2C', 'LYNA1LQC', '2025-04-19 10:50:24', '2025-04-19 10:50:24', 'admin', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `muvelet` varchar(255) DEFAULT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `muvelet`, `datum`) VALUES
(1, 7, 'Új admin hozzáadása: admin2 (kajbanna21@gmail.com)', '2025-04-19 10:50:24');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `adomanyok`
--

CREATE TABLE `adomanyok` (
  `id` int(11) NOT NULL,
  `felhasznalonev` varchar(255) NOT NULL,
  `kutyamenhely_nev` varchar(255) NOT NULL,
  `utalt_osszeg` decimal(10,2) NOT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `adomanyok`
--

INSERT INTO `adomanyok` (`id`, `felhasznalonev`, `kutyamenhely_nev`, `utalt_osszeg`, `datum`) VALUES
(2, '', 'Misina', 5.00, '2025-04-24 13:49:56'),
(3, '', 'Menhely az Állatokért', 1.00, '2025-04-24 15:01:54'),
(4, '', 'Pécs Környéki Állatmentő Alapítvány', 1.00, '2025-04-24 15:09:16'),
(5, '', 'Pécs Környéki Állatmentő Alapítvány', 1.00, '2025-04-24 15:20:26'),
(6, 'Ismeretlen', 'Tappancs Állatvédő Alapítvány', 1.00, '2025-04-25 12:15:55'),
(7, 'Noname_1479', 'Emberek az Állatokért Alapítvány', 1.00, '2025-04-25 12:41:03');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `kapcsolat`
--

CREATE TABLE `kapcsolat` (
  `id` int(11) NOT NULL,
  `felhasznalo_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `uzenet` text NOT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp(),
  `valaszolt` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `kapcsolat`
--

INSERT INTO `kapcsolat` (`id`, `felhasznalo_id`, `email`, `uzenet`, `datum`, `valaszolt`) VALUES
(1, NULL, 'kajbanna21@gmail.com', 'Próba üzenet 2', '2025-04-24 14:38:06', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(250) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(100) NOT NULL,
  `verification_code` varchar(8) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `profile_pic` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `verification_code`, `verified`, `profile_pic`) VALUES
(6, 'vandavarasdi2002@gmail.com', 'Vanda', '$2y$10$lT4412eiNk7XI8saLk0xk.R9uUWLsiTK4viVvgkLXyJKPOA.t3fvy', '', 1, '07-once.jpg'),
(7, 'anon_1895@example.com', 'Noname_1895', '$2y$10$jpeOAB5BE0PSfdwkpA9K4uxGoTDrSK0dbMZE.AcSPK8gsNVVRb6n6', '', 1, 'default.png'),
(8, 'anon_9528@example.com', 'Noname_9528', '$2y$10$4NprW5ZBm89enTWBwVOpIuFiXUc5VnU66JDfDArd59TGpPieUQjTO', '', 1, 'default.png'),
(9, 'anon_4839@example.com', 'Noname_4839', '$2y$10$4IxYDu7AZl6n6LWMshO3yOXFU3up83XbL77v/VN18c8hcN66jhaMi', '', 1, 'default.png'),
(10, 'anon_2749@example.com', 'Noname_2749', '$2y$10$KhNkux6maLgjD8rSiLIX8eRh/A3HKL3r5JiLn5jCLCv8PXZpYBjNu', '', 1, 'default.png'),
(11, 'anon_6577@example.com', 'Noname_6577', '$2y$10$HvZT4glDbm.OnJS/DhYr/u8SaGpQ5Mts9MSsGHamf/HYbtsK/OpIq', '', 1, 'default.png'),
(12, 'anon_2942@example.com', 'Noname_2942', '$2y$10$R.cSzs.kAP8XGiPA4M3ZE.Ck/zH97c3SFov1eDZ.YxSwNB8hXT2qm', '', 1, 'default.png'),
(13, 'anon_3724@example.com', 'Noname_3724', '$2y$10$prtAbuSss253nZSrwsr8/uNRDnDpZJePv1LdeFHdfxxOrEhvpn0PC', '', 1, 'default.png'),
(14, 'anon_9994@example.com', 'Noname_9994', '$2y$10$ItYzLbwN5iBNhMqQwxHxeePestGiqgPenlYOfOFEA5mi6M5xLyxYC', '', 1, 'default.png'),
(15, 'anon_1479@example.com', 'Noname_1479', '$2y$10$wH6cVRYgOZKF2xdmpl1oEeaogPn/Vd7j3XHt3wyb.wPC3KPhyzQ0C', '', 1, 'default.png'),
(16, 'anon_6585@example.com', 'Noname_6585', '$2y$10$Hh906HcajWm9ptrYcJrcPuwxUCgCPLR/IjMaa0HXcLG6OOKG1gj0K', '', 1, 'default.png'),
(17, 'anon_6509@example.com', 'Noname_6509', '$2y$10$SjN2HOrsJQ1GPuFxKtZdauXaxF.XArWHHDVPLYc6iJXRdmFUpthc2', '', 1, 'default.png'),
(18, 'anon_3772@example.com', 'Noname_3772', '$2y$10$eeySHrumeUNsZq1taTd59.YJGzLsyJHLHywkYH2teqDTSTEhuCvb6', '', 1, 'default.png'),
(19, 'anon_9567@example.com', 'Noname_9567', '$2y$10$6TPJpNgVh9sKUC09rvvHsOSEn5TZEZoLNuurmJ3ziyqlWEsAtgd3O', '', 1, 'default.png'),
(20, 'anon_7789@example.com', 'Noname_7789', '$2y$10$m5QGj.dcaG6Zie.SSN42OudpbFBGqtB4nkkGleFMoJSZIfPXQVXFe', '', 1, 'default.png'),
(21, 'anon_2295@example.com', 'Noname_2295', '$2y$10$VeJEqojJ4I/icm.hjKnPFeL4E8DOIqu3Y0yVnf0o/m/is75uaJgAO', '', 1, 'default.png');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- A tábla indexei `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- A tábla indexei `adomanyok`
--
ALTER TABLE `adomanyok`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `kapcsolat`
--
ALTER TABLE `kapcsolat`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT a táblához `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `adomanyok`
--
ALTER TABLE `adomanyok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT a táblához `kapcsolat`
--
ALTER TABLE `kapcsolat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
