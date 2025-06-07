-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 07/06/2025 às 20:51
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pizzaria`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `cpf` varchar(11) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `endereco` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id`, `cpf`, `nome`, `telefone`, `endereco`) VALUES
(1, '47848280870', 'Richard de Souza', '19999766519', 'Rua José Maria de Souza Gomide, 60'),
(5, '47848280871', 'ADEMILSON PINTO DOS ANJOS', '19994506777', 'Rua José Maria de Souza Gomide, 60'),
(6, '23423423459', 'Luis Andrade', '19994506777', 'Rua do pau');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `data_hora` datetime DEFAULT current_timestamp(),
  `quantidade` int(11) NOT NULL,
  `valor_total` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido`
--

INSERT INTO `pedido` (`id`, `id_cliente`, `data_hora`, `quantidade`, `valor_total`) VALUES
(4, 1, '2025-05-24 13:57:32', 1, 15.00),
(5, 1, '2025-05-24 13:57:36', 4, 640.00),
(6, 1, '2025-05-24 14:35:10', 3, 70.00),
(7, 1, '2025-05-24 15:43:23', 7, 1260.00),
(8, 1, '2025-05-31 13:54:27', 1, 24.00),
(9, 5, '2025-05-31 14:52:43', 2, 95.00),
(10, 6, '2025-06-04 07:51:03', 2, 215.00),
(11, 5, '2025-06-04 07:51:14', 2, 240.00),
(12, 1, '2025-06-05 08:15:20', 3, 320.00),
(13, 1, '2025-06-07 14:42:13', 1, 15.00),
(14, 1, '2025-06-07 14:42:30', 1, 90.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_sabores`
--

CREATE TABLE `pedido_sabores` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_sabor` int(11) DEFAULT NULL,
  `tamanho` enum('Pequena','Média','Grande') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_sabores`
--

INSERT INTO `pedido_sabores` (`id`, `id_pedido`, `id_sabor`, `tamanho`) VALUES
(70, 8, 1, 'Grande'),
(77, 6, 1, 'Pequena'),
(78, 6, 1, 'Pequena'),
(79, 6, 5, 'Pequena'),
(101, 5, 6, 'Grande'),
(102, 5, 6, 'Grande'),
(103, 5, 6, 'Grande'),
(104, 5, 6, 'Grande'),
(107, 9, 1, 'Pequena'),
(108, 9, 6, 'Pequena'),
(109, 7, 1, 'Pequena'),
(110, 7, 1, 'Pequena'),
(111, 7, 1, 'Pequena'),
(112, 7, 1, 'Pequena'),
(113, 7, 4, 'Grande'),
(114, 7, 4, 'Grande'),
(115, 7, 4, 'Grande'),
(116, 10, 1, 'Pequena'),
(117, 10, 4, 'Pequena'),
(118, 11, 4, 'Pequena'),
(119, 11, 5, 'Pequena'),
(122, 12, 4, 'Pequena'),
(123, 12, 5, 'Pequena'),
(124, 12, 6, 'Pequena'),
(126, 13, 1, 'Pequena'),
(127, 14, 7, 'Pequena'),
(128, 4, 1, 'Pequena');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sabores_pizzas`
--

CREATE TABLE `sabores_pizzas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `valor` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sabores_pizzas`
--

INSERT INTO `sabores_pizzas` (`id`, `nome`, `descricao`, `valor`) VALUES
(1, 'Mussarela', 'Boa', 15.00),
(4, 'Calabresa', 'Boa tbm', 200.00),
(5, 'Peperoni', 'Boazinha', 40.00),
(6, 'Portuguita', '+-', 80.00),
(7, 'Frango com catuba', 'Boa hein', 90.00),
(8, 'Leclerc', 'Boa', 120.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`) VALUES
(1, 'Rick', 'a@a.a', '$2y$10$js7uinpY18YWDGFGKUxVG.8MJViiNtMGrue7FwPvrXhTYb5Hkl/jG'),
(2, 'MARIA CATARINA DE ALCANTARA', 'ycristhian23@gmail.com', '$2y$10$sAZb4fYtUgPjDpadHl9NdeM7uDDOvpz.fwHPRwAp3a/VkFfVbb2mC'),
(3, 'Ingridy veia sim', 'ycristhian24@gmail.com', '$2y$10$eqe2VH8QJ.EPJbbFHLFi2OMFfS1NaUZ2Ps5d.VMlX99wSyNdIUIbu'),
(4, 'roger', 'ycristhian25@gmail.com', '$2y$10$ZtH7YltaNhTdsdbE6XQ6UuI3/FDIUmuI.vsw9aBHh/k4Q4rI2DVJa');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `pedido_sabores`
--
ALTER TABLE `pedido_sabores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_sabor` (`id_sabor`);

--
-- Índices de tabela `sabores_pizzas`
--
ALTER TABLE `sabores_pizzas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `pedido_sabores`
--
ALTER TABLE `pedido_sabores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT de tabela `sabores_pizzas`
--
ALTER TABLE `sabores_pizzas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id`);

--
-- Restrições para tabelas `pedido_sabores`
--
ALTER TABLE `pedido_sabores`
  ADD CONSTRAINT `pedido_sabores_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `pedido_sabores_ibfk_2` FOREIGN KEY (`id_sabor`) REFERENCES `sabores_pizzas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
