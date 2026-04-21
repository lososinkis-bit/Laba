-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 16 2026 г., 13:53
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `architectural bureau`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL,
  `img_path` varchar(45) NOT NULL DEFAULT 'no_img.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `img_path`) VALUES
(1, 'architectural design', 'no_img.jpg'),
(2, 'interior design', 'no_img.jpg'),
(3, 'landscape design and improvement', 'no_img.jpg'),
(4, '3D visualization and modeling', 'no_img.jpg'),
(5, 'facade design and reconstruction', 'no_img.jpg'),
(6, 'facade lighting', 'no_img.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `design`
--

CREATE TABLE `design` (
  `id` int(11) UNSIGNED NOT NULL,
  `img_path` varchar(45) NOT NULL DEFAULT 'no_img.png',
  `name` varchar(45) NOT NULL,
  `id_brand` int(10) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `cost` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `design`
--

INSERT INTO `design` (`id`, `img_path`, `name`, `id_brand`, `description`, `cost`) VALUES
(1, 'no_img.png', 'house', 1, 'Частный, загородный, дачный, индивидуальный, усадьба, резиденция и тп.', 100000),
(2, 'no_img.png', 'bath complex', 1, 'Сооружение бани, сауны, хаммама, банного комплекса, гостевого дома и тп.', 100000),
(3, 'no_img.png', 'building', 1, 'Офисные, торговые, развлекательные, спортивные, образовательные, жилые комплексы и тп.', 100000),
(4, 'no_img.png', 'SAF', 1, 'Павильоны, арт-объекты, стеллы, фонтаны, беседки, детские игровые комплексы и тп.', 100000),
(5, 'no_img.png', 'facade design', 1, NULL, 100000),
(6, 'no_img.png', 'residential interior', 2, NULL, 100000),
(7, 'no_img.png', 'commercial interior', 2, NULL, 100000),
(8, 'no_img.png', 'individual land plot', 3, 'Дачный, загородный, частный и тп.', 100000),
(9, 'no_img.png', 'public area', 3, 'Парк, турбаза, сквер, кемпинг, набережная, бульвар, площадь, площадь, придомовая территория ЖК, пляж территория спортивного сооружения и прочее', 100000),
(10, 'no_img.png', 'house', 4, NULL, 100000),
(11, 'no_img.png', 'building', 4, NULL, 100000),
(12, 'no_img.png', 'territory', 4, NULL, 100000),
(13, 'no_img.png', 'interior', 4, NULL, 100000),
(14, 'no_img.png', '', 5, NULL, 100000),
(15, 'q_img.png', 'architectural lighting', 6, NULL, 100000),
(16, 'q_img.png', 'decorative/New Year\'s lighting', 6, NULL, 100000);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `design`
--
ALTER TABLE `design`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_brand` (`id_brand`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `design`
--
ALTER TABLE `design`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `design`
--
ALTER TABLE `design`
  ADD CONSTRAINT `foreig_key_1` FOREIGN KEY (`id_brand`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
