-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql112.infinityfree.com
-- Tempo de geração: 22/10/2025 às 19:29
-- Versão do servidor: 11.4.7-MariaDB
-- Versão do PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `if0_40084354_rastreamento_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome_completo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `administradores`
--

INSERT INTO `administradores` (`id`, `usuario`, `senha`, `nome_completo`) VALUES
(1, 'Muniz', '$2y$10$UjXVcnxbeSmrnRL0y/rQj.K0S3gwgeNzLYKNvSdqUtVQnzx8md7Wq', 'Muniz'),
(2, 'Galego', '$2y$10$iG.kL.dD4n.J8t.bY9v.w0F.qP5o3H.u7I6j5c.m2X.s4N.t1O.zE', 'Galego'),
(3, 'Geral', '$2y$10$f.l.o.w.e.r.s.t.a.l.k.b.e.a.u.t.i.f.u.l.l.y.C.k.b.o', 'Acesso Geral');

-- --------------------------------------------------------

--
-- Estrutura para tabela `administrators`
--

CREATE TABLE `administrators` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome_completo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `administrators`
--

INSERT INTO `administrators` (`id`, `usuario`, `senha`, `nome_completo`) VALUES
(1, 'Muniz', '$2y$10$jvlmy62JYrMkcpHisZRuYeqe72uJSOBOWQtN8TNTlbybQFnYeZkwi', 'Muniz'),
(2, 'Galego', '$2y$10$4GXoO8u6Ph0AOtCMhLVNTuaZ9GOyS9G56IKprFieU9tEhMW9Rih0K', 'Galego'),
(3, 'Geral', '$2y$10$LS2J1uDPdH2F3Pgoz14Oo.OJHSZpr1ZKFaw1l/EAVBb2MDgUZk02.', 'Acesso Geral');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`) VALUES
(1, 'endereco_origem', '-23.3958, -47.0019 ');

-- --------------------------------------------------------

--
-- Estrutura para tabela `depoimentos`
--

CREATE TABLE `depoimentos` (
  `id` int(11) NOT NULL,
  `nome_cliente` varchar(150) NOT NULL,
  `texto` text NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `aprovado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `depoimentos`
--

INSERT INTO `depoimentos` (`id`, `nome_cliente`, `texto`, `foto_url`, `data_criacao`, `aprovado`) VALUES
(4, 'Bruno Martins', 'Vi a Copart em uma rede social e hesitei. Mas o Ismael foi muito paciente, me apresentou diversos casos de sucesso e vídeos que me deram segurança. Realizei o pagamento e, felizmente, o veículo chegou em minha casa sem atrasos. Aprovado!', 'uploads/depoimento_68f964a3aa242-cliente-7.png', '2025-10-22 23:11:31', 0),
(5, 'Diego Fernandes', 'Tinha um certo receio em comprar um veículo de leilão, mas a forma como o Ismael conduziu todo o processo na Copart foi exemplar. Ele me deu todas as garantias, paguei e o veículo chegou. É uma empresa séria e com um ótimo atendimento.', 'uploads/depoimento_68f964c403685-cliente-6.png', '2025-10-22 23:12:04', 0),
(7, 'Felipe Almeida', 'Fui apresentado à Copart e ao Ismael por um conhecido. No início, a desconfiança era grande, principalmente sobre pagar antecipadamente. No entanto, a transparência e o profissionalismo me convenceram. Paguei e o veículo chegou em perfeitas condições. Recomendo a todos!', 'uploads/depoimento_68f96552ae3d1-cliente-5.png', '2025-10-22 23:14:26', 0),
(8, 'Heitor Borges', 'Quando vi o anúncio da Copart no Facebook, confesso que tive minhas dúvidas sobre leilões. Mas o atendimento do Ismael me deu a confiança que eu precisava. Ele foi muito claro e transparente. Fiz o pagamento e, para minha surpresa, o veículo chegou em perfeito estado. Recomendo a experiência!', 'uploads/depoimento_68f9661ed02e7-cliente-1.png', '2025-10-22 23:17:50', 0),
(9, 'Ana Clara S.', 'Eu sempre achei que comprar em leilão era complicado e arriscado. A equipe da Copart, principalmente o Ismael, desfez essa imagem. Depois de ver os vídeos explicativos e outros depoimentos, decidi confiar. Paguei e o carro foi entregue sem nenhum problema. Estou muito satisfeita!', 'uploads/depoimento_68f966396c5d0-cliente-3.png', '2025-10-22 23:18:17', 0),
(10, 'Roberto Fernando', 'Minha busca por um veículo me levou até a Copart, e a princípio, fiquei bastante desconfiada. O Ismael foi super atencioso, tirou todas as minhas dúvidas e me mostrou a seriedade da empresa. Confiei, paguei e o carro chegou como prometido. Uma ótima experiência de compra!', 'uploads/depoimento_68f9666a6612c-cliente-2.png', '2025-10-22 23:19:06', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `telefone` varchar(50) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`id`, `nome`, `telefone`, `foto_url`) VALUES
(1, 'ISMAEL PALMA', '(11) 94124-6667', 'uploads/68f9594abeaf1-Captura de tela 2025-10-06 095005.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_movimentacao`
--

CREATE TABLE `historico_movimentacao` (
  `id` int(11) NOT NULL,
  `id_veiculo` int(11) NOT NULL,
  `timestamp_evento` datetime NOT NULL,
  `descricao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `motoristas`
--

CREATE TABLE `motoristas` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `endereco_comercial` varchar(255) DEFAULT NULL,
  `habilitacao` varchar(50) DEFAULT NULL,
  `data_admissao` date DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT 'imagens/motorista_padrao.png',
  `status` varchar(100) DEFAULT 'SITUAÇÃO REGULAR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `motoristas`
--

INSERT INTO `motoristas` (`id`, `nome`, `endereco_comercial`, `habilitacao`, `data_admissao`, `foto_url`, `status`) VALUES
(1, 'Leandro Teixeira Fernandes', 'Rua Gregório Lazzarini, 14 - Jardim Três Marias, São Paulo - SP, 08331-130', 'D', '2018-07-20', 'imagens/motorista_exemplo.jpg', 'SITUAÇÃO REGULAR');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rastreamentos`
--

CREATE TABLE `rastreamentos` (
  `id` int(11) NOT NULL,
  `id_veiculo` int(11) NOT NULL,
  `endereco_origem` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `localizacao_atual` varchar(255) NOT NULL,
  `prazo_entrega` date DEFAULT NULL,
  `id_motorista` int(11) DEFAULT NULL,
  `ultima_atualizacao` datetime NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `progresso` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `rastreamentos`
--

INSERT INTO `rastreamentos` (`id`, `id_veiculo`, `endereco_origem`, `status`, `localizacao_atual`, `prazo_entrega`, `id_motorista`, `ultima_atualizacao`, `latitude`, `longitude`, `progresso`) VALUES
(10, 2, NULL, 'VEICULO EMBARCADO', '-23.3958, -47.0019 ', NULL, NULL, '2025-10-01 23:27:52', NULL, NULL, 0),
(15, 13, NULL, 'EM TRÂNSITO', 'Estrada dos Romeiros, 3000, Pirapora do Bom Jesus, SP', NULL, NULL, '0000-00-00 00:00:00', '-23.40876280', '-46.95770080', 0),
(17, 16, NULL, 'AGUARDANDO LIBERAÇÃO', 'Estr. dos Romeiros, 3000  Pirapora do Bom Jesus - SP, 06550-000', NULL, NULL, '0000-00-00 00:00:00', '-23.40743950', '-46.98645740', 0),
(25, 24, NULL, 'AGUARDANDO LIBERAÇÃO', 'Estr. dos Romeiros, 3000 - Pirapora do Bom Jesus - SP', NULL, NULL, '0000-00-00 00:00:00', '-23.40876280', '-46.95770080', 0),
(26, 25, NULL, 'AGUARDANDO LIBERAÇÃO', '', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL, 0),
(36, 36, NULL, 'AGUARDANDO PAGAMENTO', 'Estr. dos Romeiros, 300, Pirapora do Bom Jesus - SP', NULL, NULL, '0000-00-00 00:00:00', '-23.40575790', '-46.99364510', 0),
(37, 37, NULL, 'VEICULO EMBARCADO', 'R. Projetada, Mogi das Cruzes', NULL, NULL, '0000-00-00 00:00:00', '-23.67032500', '-46.18794010', 0),
(38, 38, NULL, 'AGUARDANDO PAGAMENTO DA NOTA FISCAL', 'R. 2, 300 - Village do Lago I, Montes Claros ', NULL, NULL, '0000-00-00 00:00:00', '-16.68106870', '-43.82897410', 0),
(39, 39, NULL, 'AGUARDANDO LIBERAÇÃO', 'Estr. dos Romeiros, 300, Pirapora do Bom Jesus - SP', NULL, NULL, '0000-00-00 00:00:00', '-23.39792910', '-47.00650160', 0),
(40, 40, NULL, 'AGUARDANDO PAGAMENTO', 'Estr. dos Romeiros, 3000 -Pirapora do Bom Jesus', NULL, NULL, '0000-00-00 00:00:00', '-23.40575790', '-46.99364510', 99),
(41, 41, NULL, 'AGUARDANDO PAGAMENTO DOCUMENTAÇÃO', 'Estrada dos Romeiros, 3000, Pirapora do Bom Jesus, SP', NULL, NULL, '0000-00-00 00:00:00', '-23.40575790', '-46.99364510', 95);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp(),
  `id_admin` int(11) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `senha`, `endereco`, `data_cadastro`, `id_admin`, `telefone`) VALUES
(12, 'Josimar Ferreira', '17100017100', '$2y$10$wvkugRUgd6CE7mPY.mwwBupquw3F6245zTdrDlvix31X1mam2NPu.', 'Av. Litorânea, 10 - Praia do Calhau, São Luís - MA, 65071-377', '2025-10-02 22:47:36', NULL, NULL),
(17, 'Lidomar da Silva Carvalho', '01105458342', '$2y$10$My/TyYfeHGO9PWDOJ1HlGecJ2O/Ka4GTf/kiei/PQOo.Siaq8SMuu', 'PV CAETANO S/N ZONA RURAL, 64755-000 - JACOBINA DO PIAUI - PI', '2025-10-06 05:33:07', 1, '(89) 9434-7760'),
(18, 'GLAUDICIR CECHETT', '61412970059', '$2y$10$0QIkWZeHDSpysk0LLmwf0eCkkrH1fQQkz4IIzT4h/cSp3l8WMraGm', 'RUA JACARANDA, 163 COPAS VERDES - ERECHIM, RS', '2025-10-10 13:51:45', 1, '(49) 8897-4220'),
(19, 'FRANCISCO ALVES LEANDRO ', '47184795172', '$2y$10$ZJFe8UEwz0yWIfQoRoNnruc4f.YjXTTIIzPMnkNi51oumqLXsGO06', 'Q 46 CJ H LT 12, BRAZLANDIA -  DF, CEP 72.700-000', '2025-10-16 08:46:42', 1, '(61) 9985-9332'),
(21, 'ORLEI JOSE FERNANDES', '01623077052', '$2y$10$7L4w.NF/mqP211RV087e0OvazN21Nn8Hk5TvijjCl5Y.trdhaEiXq', 'RUA LUIZ PISSET, 644 - JACUTINGA, RS - 99730-000', '2025-10-22 12:10:51', 1, '(54) 9270-6175');

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

CREATE TABLE `veiculos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `icone_url` varchar(255) DEFAULT 'imagens/icon_car_default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculos`
--

INSERT INTO `veiculos` (`id`, `id_usuario`, `placa`, `modelo`, `icone_url`) VALUES
(2, 6, 'EAF7B58', 'HONDA CIVIC EXL 2009', 'imagens/icon_car_default.png'),
(13, 9, 'ABB0000', 'Honda Civic', 'imagens/logos/honda.png'),
(16, 11, '123ABC', 'HONDA CIVIC EXL 2009', 'imagens/logos/honda.png'),
(24, 12, '171MAUA', 'VW SAVEIRO CL 1.8 1994', 'imagens/logos/vw.png'),
(25, 12, '171SP', 'HONDA CIVIC ELX 2014 FLEX', 'imagens/logos/mercedes.png'),
(36, 16, '45653453', 'SAVEIRO CL', 'imagens/logos/vw.png'),
(37, 14, 'ABC5657', 'FORD FIESTA SEDAN 1.6 AUT', 'imagens/logos/ford.png'),
(38, 17, 'ABD345', 'VW/SAVEIRO 1995 AZUL CL', 'imagens/logos/vw.png'),
(39, 18, 'AAAA6565', 'FIAT FIORINO FURGÃO 2006 BRANCA', ''),
(40, 19, 'ASADFF', 'SAVEIRO CL 1.8 AZUL 1995', 'imagens/logos/vw.png'),
(41, 21, 'ASADFDGF', 'VW SAVEIRO CL 1.8 AZUL 1995', 'imagens/logos/vw.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vitrine_veiculos`
--

CREATE TABLE `vitrine_veiculos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `ano_fabricacao` int(4) DEFAULT NULL,
  `ano_modelo` int(4) DEFAULT NULL,
  `quilometragem` int(11) DEFAULT NULL,
  `cambio` varchar(50) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `opcionais` text DEFAULT NULL,
  `foto_principal` varchar(255) NOT NULL,
  `fotos_galeria` text DEFAULT NULL,
  `status` enum('disponivel','vendido') NOT NULL DEFAULT 'disponivel'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Índices de tabela `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `depoimentos`
--
ALTER TABLE `depoimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `historico_movimentacao`
--
ALTER TABLE `historico_movimentacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_veiculo` (`id_veiculo`);

--
-- Índices de tabela `motoristas`
--
ALTER TABLE `motoristas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `rastreamentos`
--
ALTER TABLE `rastreamentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_veiculo` (`id_veiculo`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `veiculos`
--
ALTER TABLE `veiculos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `placa` (`placa`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `vitrine_veiculos`
--
ALTER TABLE `vitrine_veiculos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `administrators`
--
ALTER TABLE `administrators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `depoimentos`
--
ALTER TABLE `depoimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `historico_movimentacao`
--
ALTER TABLE `historico_movimentacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `motoristas`
--
ALTER TABLE `motoristas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `rastreamentos`
--
ALTER TABLE `rastreamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de tabela `vitrine_veiculos`
--
ALTER TABLE `vitrine_veiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para dumps de tabelas
--

--
-- Restrições para tabelas `rastreamentos`
--
ALTER TABLE `rastreamentos`
  ADD CONSTRAINT `fk_rastreamento_veiculo` FOREIGN KEY (`id_veiculo`) REFERENCES `veiculos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
