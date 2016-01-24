-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Янв 20 2016 г., 13:34
-- Версия сервера: 10.1.9-MariaDB
-- Версия PHP: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `database_cms`
--

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `path` text NOT NULL,
  `share_link` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `files`
--

INSERT INTO `files` (`id`, `id_user`, `path`, `share_link`) VALUES
(17, 3, '../upload/yurii/Chrysanthemum.jpg', 'O2YBxO9LFS'),
(18, 3, '../upload/yurii/Desert.jpg', 'XCBQ3ZkQq3'),
(19, 3, '../upload/yurii/Hydrangeas.jpg', 'rWDxFXNK7d'),
(20, 3, '../upload/yurii/Jellyfish.jpg', '7rveZ06jSy'),
(21, 3, '../upload/yurii/Tulips.jpg', '5sqfuearTM'),
(22, 3, '../upload/yurii/eula.1028.txt', 'fN-MP2lVk6'),
(23, 3, '../upload/yurii/eula.1040.txt', 'Ke5nxobvsR');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `authKey` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `login`, `pass`, `email`, `authKey`) VALUES
(1, 'admin', '$2y$13$XFfamDlpuf/q5zGayyUs1u8eSeZ.R5OhZhu1646fuf3Oq/WhLh.JO', 'admin@admin.admin', 'drwIKnawCRSEuL_G0lf2TH92h93Tk8qZ'),
(3, 'yurii', '$2y$13$LBgrznCGcJo0xAg9rybdie73vzgEmrGr3lHeyjuUlFwuym6uKndAm', 'yurii@yurii.yurii', 'H2LwEdnYwjWf_5B0s6cEERRZi9zGUs_o');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
