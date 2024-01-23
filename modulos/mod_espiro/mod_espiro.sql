
--
-- Estructura de tabla para la tabla mod_espiro
--

CREATE TABLE mod_espiro (
  m_espiro_id int(11) NOT NULL,
  m_espiro_adm int(11) NOT NULL,
  m_espiro_sede int(1) NOT NULL,
  m_espiro_usu varchar(15) NOT NULL,
  m_espiro_fech_reg datetime NOT NULL,
  m_espiro_fech_update datetime DEFAULT NULL,
  m_espiro_st int(1) NOT NULL,
  m_espiro_examen int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indices de la tabla mod_espiro
--
ALTER TABLE mod_espiro
  ADD PRIMARY KEY (m_espiro_id);

--
-- AUTO_INCREMENT de la tabla mod_espiro
--
ALTER TABLE mod_espiro
  MODIFY m_espiro_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;



--
-- Estructura de tabla para la tabla mod_espiro_metria
--

CREATE TABLE mod_espiro_metria (
  m_espiro_metria_id int(11) NOT NULL,
  m_espiro_metria_adm int(11) NOT NULL,

  m_espiro_metria_fuma varchar(2) DEFAULT NULL,
  m_espiro_metria_cap_vital varchar(30) DEFAULT NULL,

  m_espiro_metria_FVC varchar(10) DEFAULT NULL,
  m_espiro_metria_FEV1 varchar(10) DEFAULT NULL,
  m_espiro_metria_FEV1_FVC varchar(10) DEFAULT NULL,
  m_espiro_metria_PEF varchar(10) DEFAULT NULL,
  m_espiro_metria_FEF2575 varchar(10) DEFAULT NULL,

  m_espiro_metria_recomendacion varchar(400) DEFAULT NULL,
  m_espiro_metria_conclusion varchar(400) DEFAULT NULL,
  m_espiro_metria_cie10 varchar(300) DEFAULT NULL,
  m_espiro_metria_diag varchar(300) DEFAULT NULL,
  
  m_espiro_metria_pdf int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indices de la tabla mod_espiro_metria
--

ALTER TABLE mod_espiro_metria
  ADD PRIMARY KEY (m_espiro_metria_id);

--
-- AUTO_INCREMENT de la tabla mod_espiro_metria
--
ALTER TABLE mod_espiro_metria
  MODIFY m_espiro_metria_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
