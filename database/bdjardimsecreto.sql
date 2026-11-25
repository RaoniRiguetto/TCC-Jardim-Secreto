-- Jardim Secreto Hotel - Script do Banco de Dados (dados de exemplo fictícios)

CREATE DATABASE IF NOT EXISTS `bdjardimsecreto` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bdjardimsecreto`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Estrutura da tabela `tbcategoria`
-- --------------------------------------------------------

CREATE TABLE `tbcategoria` (
  `id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbcategoria` (`id`, `status`, `nome`) VALUES
(11, 1, 'Padrão'),
(12, 1, 'Executivo'),
(13, 1, 'Premium'),
(14, 1, 'Básico');

-- --------------------------------------------------------
-- Estrutura da tabela `tbfuncionario`
-- --------------------------------------------------------

CREATE TABLE `tbfuncionario` (
  `id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `nome` varchar(200) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `telefone` varchar(11) NOT NULL,
  `dataNascimento` date NOT NULL,
  `email` varchar(200) NOT NULL,
  `senha` varchar(50) NOT NULL,
  `dataAdmissao` date NOT NULL,
  `dataDemissao` date DEFAULT NULL,
  `cep` varchar(8) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `endereco` varchar(100) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `uf` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbfuncionario` (`id`, `status`, `nome`, `cpf`, `telefone`, `dataNascimento`, `email`, `senha`, `dataAdmissao`, `dataDemissao`, `cep`, `numero`, `endereco`, `bairro`, `cidade`, `uf`) VALUES
(2, 1, 'Administrador Exemplo', '52998224725', '44999990001', '1998-05-10', 'admin@jardimsecreto.com', '12345678', '2025-01-15', NULL, '87502450', '100', 'Rua das Flores', 'Centro', 'Umuarama', 'PR'),
(3, 1, 'Atendente Exemplo', '11144477735', '44999990002', '2000-01-21', 'atendente@jardimsecreto.com', '12345678', '2025-07-17', NULL, '87502450', '24', 'Avenida Brasil', 'Jardim Lisboa', 'Umuarama', 'PR');

-- --------------------------------------------------------
-- Estrutura da tabela `tbquarto`
-- --------------------------------------------------------

CREATE TABLE `tbquarto` (
  `id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'disponivel',
  `nome` varchar(200) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `ocupacao` int(11) NOT NULL,
  `area` int(11) NOT NULL,
  `valorDiaria` double(10,2) NOT NULL,
  `descricao` varchar(500) NOT NULL,
  `caminhoImg` varchar(500) NOT NULL,
  `fkCategoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbquarto` (`id`, `status`, `nome`, `numero`, `ocupacao`, `area`, `valorDiaria`, `descricao`, `caminhoImg`, `fkCategoria`) VALUES
(16, 'ocupado', 'Quarto Básico Solteiro', '101', 1, 22, 239.00, 'Quarto aconchegante de solteiro com cama confortável, mesa de trabalho e Wi-Fi gratuito.', 'uploads/img_69313f4d71889_20251204085909.png', 11),
(17, 'ocupado', 'Quarto Premium Casal', '201', 2, 25, 347.00, 'Quarto de casal ideal para estadias curtas, equipado com frigobar, ar-condicionado e TV de 32".', 'uploads/img_69313f8912601_20251204090009.png', 13),
(18, 'disponivel', 'Suíte Casal Executivo', '202', 2, 28, 358.00, 'Suíte executiva espaçosa com vista para o jardim e ambiente silencioso.', 'uploads/img_6931767d7dc9f_20251204125437.png', 12),
(19, 'disponivel', 'Quarto Família', '203', 4, 35, 400.00, 'Acomodação ampla para famílias, com duas camas de casal e varanda privativa.', 'uploads/img_693189d1160ea_20251204141705.png', 13);

-- --------------------------------------------------------
-- Estrutura da tabela `tbreserva`
-- --------------------------------------------------------

CREATE TABLE `tbreserva` (
  `id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'reservado',
  `dataChekIn` date NOT NULL,
  `dataChekOut` date NOT NULL,
  `horaReserva` timestamp NOT NULL DEFAULT current_timestamp(),
  `qteAdultos` int(11) NOT NULL,
  `qteCriancas` int(11) NOT NULL,
  `valor` double NOT NULL,
  `qteParcelas` int(11) NOT NULL,
  `formaPagamento` varchar(50) NOT NULL,
  `fkQuarto` int(11) NOT NULL,
  `fkUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbreserva` (`id`, `status`, `dataChekIn`, `dataChekOut`, `horaReserva`, `qteAdultos`, `qteCriancas`, `valor`, `qteParcelas`, `formaPagamento`, `fkQuarto`, `fkUsuario`) VALUES
(3, 'hospedado', '2025-12-04', '2025-12-05', '2025-12-04 08:01:50', 1, 0, 239, 1, 'debito', 16, 7),
(4, 'hospedado', '2025-12-04', '2025-12-09', '2025-12-04 08:02:50', 2, 0, 1735, 6, 'credito', 17, 7),
(5, 'reservado', '2025-12-04', '2025-12-10', '2025-12-04 11:59:06', 1, 0, 2040.6, 1, 'pix', 18, 7),
(6, 'reservado', '2025-12-05', '2025-12-08', '2025-12-04 12:45:38', 1, 0, 1020.3, 1, 'pix', 18, 7),
(7, 'reservado', '2025-12-10', '2025-12-17', '2025-12-04 13:13:34', 1, 0, 1673, 1, 'debito', 16, 9);

-- --------------------------------------------------------
-- Estrutura da tabela `tbusuario`
-- --------------------------------------------------------

CREATE TABLE `tbusuario` (
  `id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `nome` varchar(200) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `telefone` varchar(11) NOT NULL,
  `dataNascimento` date NOT NULL,
  `email` varchar(200) NOT NULL,
  `senha` varchar(50) NOT NULL,
  `cep` varchar(8) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `endereco` varchar(100) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `uf` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbusuario` (`id`, `status`, `nome`, `cpf`, `telefone`, `dataNascimento`, `email`, `senha`, `cep`, `numero`, `endereco`, `bairro`, `cidade`, `uf`) VALUES
(7, 1, 'Cliente Exemplo', '39053344705', '44999991111', '2000-04-16', 'cliente@jardimsecreto.com', '12345678', '87502450', '228', 'Rua das Palmeiras', 'Centro', 'Umuarama', 'PR'),
(9, 1, 'Mariana Costa', '71428793860', '44999992222', '1997-05-24', 'mariana@exemplo.com', '12345678', '87509300', '240', 'Rua das Margaridas', 'Jardim América', 'Umuarama', 'PR');

-- --------------------------------------------------------
-- Índices e Chaves Estrangeiras
-- --------------------------------------------------------

ALTER TABLE `tbcategoria` ADD PRIMARY KEY (`id`);
ALTER TABLE `tbfuncionario` ADD PRIMARY KEY (`id`);
ALTER TABLE `tbquarto` ADD PRIMARY KEY (`id`), ADD KEY `fkCategoria` (`fkCategoria`);
ALTER TABLE `tbreserva` ADD PRIMARY KEY (`id`), ADD KEY `fkQuarto` (`fkQuarto`), ADD KEY `fkUsuario` (`fkUsuario`);
ALTER TABLE `tbusuario` ADD PRIMARY KEY (`id`);

ALTER TABLE `tbcategoria` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `tbfuncionario` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `tbquarto` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
ALTER TABLE `tbreserva` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `tbusuario` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `tbquarto`
  ADD CONSTRAINT `fkCategoria` FOREIGN KEY (`fkCategoria`) REFERENCES `tbcategoria` (`id`);

ALTER TABLE `tbreserva`
  ADD CONSTRAINT `fkQuarto` FOREIGN KEY (`fkQuarto`) REFERENCES `tbquarto` (`id`),
  ADD CONSTRAINT `fkUsuario` FOREIGN KEY (`fkUsuario`) REFERENCES `tbusuario` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
