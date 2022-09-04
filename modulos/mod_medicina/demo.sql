
--
-- Estructura de tabla para la tabla mod_adj_informes
--


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE mod_adj_informes (
add_id int(11) NOT NULL,
add_adm int(11) NOT NULL,
add_servicio varchar(100) DEFAULT NULL,
add_detalle varchar(400) DEFAULT NULL,
add_especialidad varchar(100) DEFAULT NULL,
add_st int(11) DEFAULT 1,
add_fech_reg datetime(3) NOT NULL DEFAULT current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE mod_adj_informes
ADD PRIMARY KEY (add_id);

ads
--
-- AUTO_INCREMENT de la tabla mod_adj_informes
--
ALTER TABLE mod_adj_informes
MODIFY add_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;