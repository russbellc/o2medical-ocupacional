
--
-- Estructura de tabla para la tabla mod_odonto
--

CREATE TABLE mod_odonto (
  m_odonto_id int(11) NOT NULL,
  m_odonto_adm int(11) NOT NULL,
  m_odonto_sede int(1) NOT NULL,
  m_odonto_usu varchar(15) NOT NULL,
  m_odonto_fech_reg datetime NOT NULL,
  m_odonto_fech_update datetime DEFAULT NULL,
  m_odonto_st int(1) NOT NULL,
  m_odonto_examen int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indices de la tabla mod_odonto
--
ALTER TABLE mod_odonto
  ADD PRIMARY KEY (m_odonto_id);

--
-- AUTO_INCREMENT de la tabla mod_odonto
--
ALTER TABLE mod_odonto
  MODIFY m_odonto_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


-----------------------------------------------------------------------------
-----------------------------------------------------------------------------
-----------------------------------------------------------------------------


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pieza`
--

CREATE TABLE `pieza` (
  `piez_id` int(11) NOT NULL,
  `piez_diente` int(1) DEFAULT NULL,
  `piez_nro` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `pieza`
--

INSERT INTO `pieza` (`piez_id`, `piez_diente`, `piez_nro`) VALUES
(1, 18, 1),
(2, 18, 2),
(3, 18, 3),
(4, 18, 4),
(5, 18, 5),
(6, 17, 1),
(7, 17, 2),
(8, 17, 3),
(9, 17, 4),
(10, 17, 5),
(11, 16, 1),
(12, 16, 2),
(13, 16, 3),
(14, 16, 4),
(15, 16, 5),
(16, 15, 1),
(17, 15, 2),
(18, 15, 3),
(19, 15, 4),
(20, 15, 5),
(21, 14, 1),
(22, 14, 2),
(23, 14, 3),
(24, 14, 4),
(25, 14, 5),
(26, 13, 1),
(27, 13, 2),
(28, 13, 3),
(29, 13, 4),
(30, 13, 5),
(31, 12, 1),
(32, 12, 2),
(33, 12, 3),
(34, 12, 4),
(35, 12, 5),
(36, 11, 1),
(37, 11, 2),
(38, 11, 3),
(39, 11, 4),
(40, 11, 5),
(41, 21, 1),
(42, 21, 2),
(43, 21, 3),
(44, 21, 4),
(45, 21, 5),
(46, 22, 1),
(47, 22, 2),
(48, 22, 3),
(49, 22, 4),
(50, 22, 5),
(51, 23, 1),
(52, 23, 2),
(53, 23, 3),
(54, 23, 4),
(55, 23, 5),
(56, 24, 1),
(57, 24, 2),
(58, 24, 3),
(59, 24, 4),
(60, 24, 5),
(61, 25, 1),
(62, 25, 2),
(63, 25, 3),
(64, 25, 4),
(65, 25, 5),
(66, 26, 1),
(67, 26, 2),
(68, 26, 3),
(69, 26, 4),
(70, 26, 5),
(71, 27, 1),
(72, 27, 2),
(73, 27, 3),
(74, 27, 4),
(75, 27, 5),
(76, 28, 1),
(77, 28, 2),
(78, 28, 3),
(79, 28, 4),
(80, 28, 5),
(81, 55, 1),
(82, 55, 2),
(83, 55, 3),
(84, 55, 4),
(85, 55, 5),
(86, 54, 1),
(87, 54, 2),
(88, 54, 3),
(89, 54, 4),
(90, 54, 5),
(91, 53, 1),
(92, 53, 2),
(93, 53, 3),
(94, 53, 4),
(95, 53, 5),
(96, 52, 1),
(97, 52, 2),
(98, 52, 3),
(99, 52, 4),
(100, 52, 5),
(101, 51, 1),
(102, 51, 2),
(103, 51, 3),
(104, 51, 4),
(105, 51, 5),
(106, 61, 1),
(107, 61, 2),
(108, 61, 3),
(109, 61, 4),
(110, 61, 5),
(111, 62, 1),
(112, 62, 2),
(113, 62, 3),
(114, 62, 4),
(115, 62, 5),
(116, 63, 1),
(117, 63, 2),
(118, 63, 3),
(119, 63, 4),
(120, 63, 5),
(121, 64, 1),
(122, 64, 2),
(123, 64, 3),
(124, 64, 4),
(125, 64, 5),
(126, 65, 1),
(127, 65, 2),
(128, 65, 3),
(129, 65, 4),
(130, 65, 5),
(131, 85, 1),
(132, 85, 2),
(133, 85, 3),
(134, 85, 4),
(135, 85, 5),
(136, 84, 1),
(137, 84, 2),
(138, 84, 3),
(139, 84, 4),
(140, 84, 5),
(141, 83, 1),
(142, 83, 2),
(143, 83, 3),
(144, 83, 4),
(145, 83, 5),
(146, 82, 1),
(147, 82, 2),
(148, 82, 3),
(149, 82, 4),
(150, 82, 5),
(151, 81, 1),
(152, 81, 2),
(153, 81, 3),
(154, 81, 4),
(155, 81, 5),
(156, 71, 1),
(157, 71, 2),
(158, 71, 3),
(159, 71, 4),
(160, 71, 5),
(161, 72, 1),
(162, 72, 2),
(163, 72, 3),
(164, 72, 4),
(165, 72, 5),
(166, 73, 1),
(167, 73, 2),
(168, 73, 3),
(169, 73, 4),
(170, 73, 5),
(171, 74, 1),
(172, 74, 2),
(173, 74, 3),
(174, 74, 4),
(175, 74, 5),
(176, 75, 1),
(177, 75, 2),
(178, 75, 3),
(179, 75, 4),
(180, 75, 5),
(181, 48, 1),
(182, 48, 2),
(183, 48, 3),
(184, 48, 4),
(185, 48, 5),
(186, 47, 1),
(187, 47, 2),
(188, 47, 3),
(189, 47, 4),
(190, 47, 5),
(191, 46, 1),
(192, 46, 2),
(193, 46, 3),
(194, 46, 4),
(195, 46, 5),
(196, 45, 1),
(197, 45, 2),
(198, 45, 3),
(199, 45, 4),
(200, 45, 5),
(201, 44, 1),
(202, 44, 2),
(203, 44, 3),
(204, 44, 4),
(205, 44, 5),
(206, 43, 1),
(207, 43, 2),
(208, 43, 3),
(209, 43, 4),
(210, 43, 5),
(211, 42, 1),
(212, 42, 2),
(213, 42, 3),
(214, 42, 4),
(215, 42, 5),
(216, 41, 1),
(217, 41, 2),
(218, 41, 3),
(219, 41, 4),
(220, 41, 5),
(221, 31, 1),
(222, 31, 2),
(223, 31, 3),
(224, 31, 4),
(225, 31, 5),
(226, 32, 1),
(227, 32, 2),
(228, 32, 3),
(229, 32, 4),
(230, 32, 5),
(231, 33, 1),
(232, 33, 2),
(233, 33, 3),
(234, 33, 4),
(235, 33, 5),
(236, 34, 1),
(237, 34, 2),
(238, 34, 3),
(239, 34, 4),
(240, 34, 5),
(241, 35, 1),
(242, 35, 2),
(243, 35, 3),
(244, 35, 4),
(245, 35, 5),
(246, 36, 1),
(247, 36, 2),
(248, 36, 3),
(249, 36, 4),
(250, 36, 5),
(251, 37, 1),
(252, 37, 2),
(253, 37, 3),
(254, 37, 4),
(255, 37, 5),
(256, 38, 1),
(257, 38, 2),
(258, 38, 3),
(259, 38, 4),
(260, 38, 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pieza`
--
ALTER TABLE `pieza`
  ADD PRIMARY KEY (`piez_id`),
  ADD KEY `dientes_idx` (`piez_diente`),
  ADD KEY `piez_nro` (`piez_nro`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pieza`
--
ALTER TABLE `pieza`
  MODIFY `piez_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;



--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------


--
-- Estructura de tabla para la tabla `dientes`
--

CREATE TABLE `dientes` (
  `dient_nro` int(2) NOT NULL,
  `dient_ord` int(2) DEFAULT NULL,
  `dient_tipo` varchar(1) DEFAULT NULL,
  `dient_pose` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `dientes`
--

INSERT INTO `dientes` (`dient_nro`, `dient_ord`, `dient_tipo`, `dient_pose`) VALUES
(11, 7, 'A', 4),
(12, 6, 'A', 4),
(13, 5, 'A', 4),
(14, 4, 'P', 3),
(15, 3, 'P', 2),
(16, 2, 'P', 1),
(17, 1, 'P', 1),
(18, 0, 'P', 1),
(21, 8, 'A', 4),
(22, 9, 'A', 4),
(23, 10, 'A', 4),
(24, 11, 'P', 3),
(25, 12, 'P', 2),
(26, 13, 'P', 1),
(27, 14, 'P', 1),
(28, 15, 'P', 1),
(31, 44, 'A', 7),
(32, 45, 'A', 7),
(33, 46, 'A', 7),
(34, 47, 'P', 6),
(35, 48, 'P', 6),
(36, 49, 'P', 5),
(37, 50, 'P', 5),
(38, 51, 'P', 5),
(41, 43, 'A', 7),
(42, 42, 'A', 7),
(43, 41, 'A', 7),
(44, 40, 'P', 6),
(45, 39, 'P', 6),
(46, 38, 'P', 5),
(47, 37, 'P', 5),
(48, 36, 'P', 5),
(51, 20, 'A', 4),
(52, 19, 'A', 4),
(53, 18, 'A', 4),
(54, 17, 'P', 1),
(55, 16, 'P', 1),
(61, 21, 'A', 4),
(62, 22, 'A', 4),
(63, 23, 'A', 4),
(64, 24, 'P', 1),
(65, 25, 'P', 1),
(71, 31, 'A', 7),
(72, 32, 'A', 7),
(73, 33, 'A', 7),
(74, 34, 'P', 5),
(75, 35, 'P', 5),
(81, 30, 'A', 7),
(82, 29, 'A', 7),
(83, 28, 'A', 7),
(84, 27, 'P', 5),
(85, 26, 'P', 5);

--
-- Indices de la tabla `dientes`
--
ALTER TABLE `dientes`
  ADD PRIMARY KEY (`dient_nro`);


-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------

--
-- Estructura de tabla para la tabla `grama_pieza`
--

CREATE TABLE `grama_pieza` (
  `gramap_id` int(11) NOT NULL,
  `gramap_adm` varchar(45) DEFAULT NULL,
  `gramap_usu` varchar(45) DEFAULT NULL,
  `gramap_fech` datetime DEFAULT NULL,
  `gramap_diente` int(2) DEFAULT NULL,
  `gramap_pieza` int(1) NOT NULL,
  `gramap_diag` int(2) NOT NULL,
  `gramap_fondo` varchar(10) NOT NULL,
  `gramap_borde` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indices de la tabla `grama_pieza`
--
ALTER TABLE `grama_pieza`
  ADD PRIMARY KEY (`gramap_id`),
  ADD KEY `pieza_idx` (`gramap_diente`);
--
-- AUTO_INCREMENT de la tabla `grama_pieza`
--
ALTER TABLE `grama_pieza`
  MODIFY `gramap_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------
--
-- Estructura de tabla para la tabla `grama_diente`
--

CREATE TABLE `grama_diente` (
  `gramad_id` int(11) NOT NULL,
  `gramad_adm` int(11) NOT NULL,
  `gramad_usu` varchar(20) NOT NULL,
  `gramad_fech` datetime NOT NULL,
  `gramad_diente` int(2) DEFAULT NULL,
  `gramad_diag_raiz` int(2) DEFAULT NULL,
  `gramad_diag_coro` varchar(2) DEFAULT NULL,
  `gramad_diag_text` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indices de la tabla `grama_diente`
--
ALTER TABLE `grama_diente`
  ADD PRIMARY KEY (`gramad_id`),
  ADD KEY `gramad_diente` (`gramad_diente`);

--
-- AUTO_INCREMENT de la tabla `grama_diente`
--
ALTER TABLE `grama_diente`
  MODIFY `gramad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------


--
-- Estructura de tabla para la tabla `odonto_recomendacion`
--

CREATE TABLE `odonto_recomendacion` (
  `odonto_reco_id` int(11) NOT NULL,
  `odonto_reco_adm` varchar(50) NOT NULL,
  `odonto_reco_fech` datetime NOT NULL,
  `odonto_reco_usu` varchar(50) NOT NULL,
  `odonto_reco_st` int(1) NOT NULL,
  `odonto_reco_desc` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indices de la tabla `odonto_recomendacion`
--
ALTER TABLE `odonto_recomendacion`
  ADD PRIMARY KEY (`odonto_reco_id`);
  
--
-- AUTO_INCREMENT de la tabla `odonto_recomendacion`
--
ALTER TABLE `odonto_recomendacion`
  MODIFY `odonto_reco_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------

--
-- Estructura de tabla para la tabla `trata`
--

CREATE TABLE `odonto_tratamiento` (
  `odonto_trata_id` int(11) NOT NULL,
  `odonto_trata_adm` varchar(50) NOT NULL,
  `odonto_trata_fech` datetime NOT NULL,
  `odonto_trata_usu` varchar(50) NOT NULL,
  `odonto_trata_st` int(1) NOT NULL,
  `odonto_trata_desc` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indices de la tabla `trata`
--
ALTER TABLE `odonto_tratamiento`
  ADD PRIMARY KEY (`odonto_trata_id`);

--
-- AUTO_INCREMENT de la tabla `trata`
--
ALTER TABLE `odonto_tratamiento`
  MODIFY `odonto_trata_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------

--
-- Estructura de tabla para la tabla `examen_odonto`
--

CREATE TABLE `examen_odonto` (
  `exa_id` int(2) NOT NULL,
  `exa_desc` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `examen_odonto`
--

INSERT INTO `examen_odonto` (`exa_id`, `exa_desc`) VALUES
(1, 'CARIES'),
(2, 'RESTAURACION'),
(3, 'DIENTE AUSENTE'),
(4, 'DIENTE PARA EXTRAER'),
(5, 'FRACTURA'),
(6, 'DIENTE ECTOPICO'),
(7, 'PLACA DENTAL'),
(8, 'PUENTE'),
(9, 'CORONA EN BUEN ESTADO'),
(10, 'CORONA EN MAL ESTADO'),
(11, 'MOVILIDAD'),
(12, 'DIENTE DISCROMICO'),
(13, 'OTRAS PATOLOGIAS');

--
-- Indices de la tabla `examen_odonto`
--
ALTER TABLE `examen_odonto`
  ADD PRIMARY KEY (`exa_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `examen_odonto`
--
ALTER TABLE `examen_odonto`
  MODIFY `exa_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------


--
-- Estructura de tabla para la tabla `grama_pato`
--

CREATE TABLE `grama_pato` (
  `gpato_id` int(11) NOT NULL,
  `gpato_adm` int(11) NOT NULL,
  `gpato_usu` varchar(45) DEFAULT NULL,
  `gpato_fech` datetime DEFAULT NULL,
  `gpato_diente` int(2) DEFAULT NULL,
  `gpato_desc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indices de la tabla `grama_pato`
--
ALTER TABLE `grama_pato`
  ADD PRIMARY KEY (`gpato_id`),
  ADD KEY `gpato_idx` (`gpato_diente`);

--
-- AUTO_INCREMENT de la tabla `grama_pato`
--
ALTER TABLE `grama_pato`
  MODIFY `gpato_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
