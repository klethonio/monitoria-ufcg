-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 11-Jan-2023 às 16:21
-- Versão do servidor: 5.7.33
-- versão do PHP: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `monitoria`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `act_admin`
--

CREATE TABLE `act_admin` (
  `login` varchar(255) NOT NULL,
  `pswd` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gmail` varchar(255) NOT NULL,
  `pswd_gmail` varchar(255) NOT NULL,
  `max_size_files` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `act_admin`
--

INSERT INTO `act_admin` (`login`, `pswd`, `email`, `gmail`, `pswd_gmail`, `max_size_files`) VALUES
('admin', '4297fzITMzITMd7a93', 'admin@admin.com', 'gmail@gmail.com', 'ZTQ1ZGY1NGUzZjMyNDg=', 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `act_config_class`
--

CREATE TABLE `act_config_class` (
  `id` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `discipline_id` int(11) NOT NULL,
  `period` varchar(255) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `expiration` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `act_disciplines`
--

CREATE TABLE `act_disciplines` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `act_languages`
--

CREATE TABLE `act_languages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `hl_ref` varchar(255) NOT NULL,
  `extension` varchar(255) NOT NULL,
  `readable` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `act_lists`
--

CREATE TABLE `act_lists` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `num` float NOT NULL,
  `num_exe` int(2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `act_ordered`
--

CREATE TABLE `act_ordered` (
  `id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `num` int(2) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `end_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `act_sent`
--

CREATE TABLE `act_sent` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `corrected_by` int(11) DEFAULT NULL,
  `grade` float DEFAULT NULL,
  `notes` text,
  `date` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `act_users`
--

CREATE TABLE `act_users` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `register` int(11) NOT NULL,
  `pswd` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `born_date` date NOT NULL,
  `level` int(1) NOT NULL DEFAULT '2',
  `status` int(1) NOT NULL DEFAULT '0',
  `code` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `act_config_class`
--
ALTER TABLE `act_config_class`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `act_disciplines`
--
ALTER TABLE `act_disciplines`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `act_languages`
--
ALTER TABLE `act_languages`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `act_lists`
--
ALTER TABLE `act_lists`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `act_ordered`
--
ALTER TABLE `act_ordered`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `act_sent`
--
ALTER TABLE `act_sent`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `act_users`
--
ALTER TABLE `act_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `act_config_class`
--
ALTER TABLE `act_config_class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `act_disciplines`
--
ALTER TABLE `act_disciplines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `act_languages`
--
ALTER TABLE `act_languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `act_lists`
--
ALTER TABLE `act_lists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `act_ordered`
--
ALTER TABLE `act_ordered`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `act_sent`
--
ALTER TABLE `act_sent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `act_users`
--
ALTER TABLE `act_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
