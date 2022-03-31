<?php

class model extends core {

    public function list_pac() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 90;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $columna = isset($_POST['columna']) ? $_POST['columna'] : NULL;
        $sede = $this->user->con_sedid;
        $query = isset($_POST['query']) ? sprintf($_POST['query']) : NULL;
        $sql1 = "SELECT
            adm_id, if(LENGTH(emp_acro)>3,emp_acro,emp_desc)emp_acro
            ,concat(pac_appat,' ',pac_apmat,' ',pac_nombres)as nombre
            ,TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE) as edad
            ,pac_sexo,concat(adm_puesto,' - ',adm_area)as puesto,tfi_desc,adm_usu, adm_aptitud,adm_fech, adm_foto
            FROM
            admision
            inner join paciente on adm_pac=pac_id
            inner join pack on adm_ruta=pk_id
            inner join empresa on pk_emp=emp_id
            left join tficha on pk_perfil=tfi_id 
            where adm_sede=$sede ";
        if (!is_null($columna) && !is_null($query)) {
            if ($columna == "1") {
                $sql1 .= "and adm_id=$query";
            } else if ($columna == "2") {
                $sql1 .= "and pac_ndoc=$query";
            } else if ($columna == "3") {
                $sql1 .= "and concat(pac_appat,' ',pac_apmat,' ',pac_nombres) like '%$query%'";
            } else if ($columna == "4") {
                $sql1 .= "and emp_desc like '%$query%'";
            } else if ($columna == "5") {
                $sql1 .= "and tfi_desc LIKE '%$query%'";
            }
        }
        $sql1 .= " order by adm_id DESC;";
        $sql = $this->sql($sql1);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function load_data_adm() {
        $query = "SELECT
            adm_id,emp_id, concat(emp_id,' - ', if(LENGTH(emp_acro)>3,emp_acro,emp_desc)) empresa
            ,pac_id, concat(pac_ndoc,' - ', pac_appat,' ',pac_apmat,', ',pac_nombres)as adm_pac
            ,CONCAT(TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE),' Años') as edad
            ,if(pac_sexo='M','MASCULINO','FEMENINO') sexo,pac_cel cell, adm_tficha,tfi_desc
            ,if(LENGTH(adm_area)>2,adm_area,' ') adm_area,adm_foto
            ,if(LENGTH(adm_puesto)>2,adm_puesto,' ') adm_puesto,adm_ruta,pk_precio,pk_desc
            FROM admision
            inner join paciente on pac_id=adm_pac
            inner join pack on pk_id=adm_ruta
            inner join empresa on emp_id=pk_emp
            inner join tficha on tfi_id=adm_tficha
            where
            adm_id=:adm_id";
        $q = $this->sql($query, array(':adm_id' => $_POST['adm_id']));
        foreach ($q->data as $k => $value) {
            $value->totaletra = $this->numtoletras(round($value->total, 2));
        }//adm_foto
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function list_paciente() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 50;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $sede = $this->user->con_sedid;
        $query = isset($_POST['query']) ? sprintf($_POST['query']) : NULL;
        $sql1 = ("SELECT 
            pac_id, pac_ndoc, pac_sexo, pac_cel, pac_fecha, pac_domdir,
            concat(pac_appat,' ',pac_apmat,', ',pac_nombres)as nombre
            ,pac_correo,pac_usu
            ,TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE) as edad
            FROM paciente 
            where pac_sedid='$sede' ");
        if (!is_null($query)) {
            $sql1 .= " and concat(pac_ndoc,pac_appat,' ',pac_apmat,' ',pac_nombres) like '%$query%'";
            $sql1 .= " order by pac_id DESC;";
            $sql = $this->sql($sql1);
            $sql->data = array_slice($sql->data, $start, $limit);
        } else {
            $sql1 .= " order by pac_id DESC;";
            $sql = $this->sql($sql1);
            $sql->data = array_slice($sql->data, $start, $limit);
        }
        return $sql;
    }

    public function list_empre() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT
            emp_id, emp_desc
            , concat(emp_id,' - ', if(LENGTH(emp_acro)>3,emp_acro,emp_desc)) empresa
            FROM empresa
            where
            concat(emp_id, emp_acro, emp_desc)like'%$query%';");
        return $sql;
    }

    public function list_ginstruccion() {
        return $this->sql("SELECT * FROM ginstruccion where gi_id > 7");
    }

    public function list_profecion() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT
            UPPER(pac_profe) as pac_profe
            FROM paciente
            where
            pac_profe like '%$query%'
            group by pac_profe");
        return $sql;
    }

    public function list_refere() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT ref_id,ref_especialidad, concat(ref_appat,' ', ref_apmat,', ', ref_nom) nombres
                , concat(ref_appat,' ', ref_apmat,', ', ref_nom, ' - ',ref_especialidad) todo
                FROM referente
                where
                concat(ref_appat, ref_apmat, ref_nom, ref_especialidad)like'%$query%';");
        return $sql;
    }

    public function list_servis() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $empresa = isset($_POST['empresa']) ? $_POST['empresa'] : NULL;
        $sexo = isset($_POST['sexo']) ? $_POST['sexo'] : NULL;
        $sql = $this->sql("SELECT
                serv_id, serv_desc, area_desc,tarf_precio
                FROM empresas
                inner join tarifario on tarf_emp=seg_id
                inner join servicios on serv_id=tarf_serv
                inner join area on serv_area=area_id
                where
                serv_sexo IN ('$sexo', 'A') and
                seg_id=$empresa and
                concat(serv_desc, area_desc)like'%$query%';");
        return $sql;
    }

    public function numeroletra() {
        $total = $_POST['total'];
        return array('success' => true, "letra" => $this->numtoletras(round($total, 2)));
    }

    public function list_detalles() {
        $adm_id = isset($_POST['adm_id']) ? $_POST['adm_id'] : NULL;
        $sql = $this->sql("SELECT
                det_id, det_serv, adm_emp, serv_desc det_serv_desc,area_desc
                FROM admision
                inner join det_adm on det_adm=adm_id
                inner join servicios on serv_id=det_serv
                inner join area on area_id=serv_area
                where
                adm_id=$adm_id;");
        foreach ($sql->data as $i => $value) {
            $empresa = $value->adm_emp;
            $servicio = $value->det_serv;
            $q = "SELECT
                    tarf_id, tarf_precio
                    FROM tarifario
                    where
                    tarf_emp=$empresa and
                    tarf_serv=$servicio;";
            $sql2 = $this->sql($q);
            foreach ($sql2->data as $k => $values) {
                $value->det_precio = round($values->tarf_precio, 2);
            }
        }
        return $sql;
    }

    public function list_Tficha() {
        $sql = $this->sql("SELECT * FROM tficha where tfi_id > 0");
        return $sql;
    }

    public function list_pacient() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("
            SELECT pac_id, pac_ndoc, concat(pac_appat,' ', pac_apmat,', ', pac_nombres) nombres
            ,concat( TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE),' Años') edad
            , pac_cel cell,if(pac_sexo='M','MASCULINO','FEMENINO') sexo
                , concat(pac_ndoc,' - ',pac_appat,' ', pac_apmat,', ', pac_nombres) todo
                FROM paciente
            where
            concat(pac_appat, pac_apmat, pac_nombres, pac_ndoc)like'%$query%';");
        return $sql;
    }

    public function list_referente() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $q = "SELECT
            ref_id, CONCAT(ref_appat,' ', ref_apmat,', ', ref_nom) nombres
            , ref_especialidad, ref_usu, ref_fech, ref_cell, ref_correo
            FROM referente ORDER BY ref_appat, ref_apmat, ref_nom;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_emp() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 50;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $query = isset($_POST['query']) ? sprintf($_POST['query']) : NULL;
        $sql1 = "SELECT
                emp_id, emp_usu, emp_fech, emp_desc, emp_acro, emp_telf, emp_estado, emp_direc
                FROM empresa";
        if (!is_null($query)) {
            $sql1 .= " where emp_desc like '%$query%' or emp_id like '%$query%' or emp_acro like '%$query%'";
        }
        $sql1 .= " order by Date_format(emp_fech,'%Y') desc,Date_format(emp_fech,'%m') desc,Date_format(emp_fech,'%d') desc
                ,Date_format(emp_fech,'%h') desc,Date_format(emp_fech,'%i') desc,Date_format(emp_fech,'%p') desc;"; //%h:%i %p
        $sql = $this->sql($sql1);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function departamento() {
        return $this->sql("SELECT dep_id, dep_desc
                FROM departamento order by dep_desc;");
    }

    public function provincia() {
        return $this->sql(sprintf("SELECT prov_id, prov_depid, prov_desc
                FROM provincia where prov_depid='%s' order by prov_desc", (isset($_POST['dep_id'])) ? $_POST['dep_id'] : ''));
    }

    public function distrito() {
        return $this->sql(sprintf("SELECT dis_id, dis_provid, dis_desc
                FROM distrito where dis_provid='%s' order by dis_desc", (isset($_POST['prov_id'])) ? $_POST['prov_id'] : ''));
    }

    public function tdocumento() {
        return $this->sql("SELECT tdoc_id, tdoc_desc FROM tdocumento;");
    }

    public function st_busca_nombres() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT
                            UPPER(pac_nombres) as pac_nombres
                            FROM paciente
                            where
                            pac_nombres like '%$query%'
                            group by pac_nombres");
        return $sql;
    }

    public function st_busca_dni() {
        $query = isset($_POST['pac_ndoc']) ? $_POST['pac_ndoc'] : NULL;
        $sql = $this->sql("SELECT pac_ndoc, concat(pac_ndoc,' - ',pac_appat,' ', pac_apmat,', ', pac_nombres) todo
            ,concat( TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE),' Años') edad
            , pac_cel cell,if(pac_sexo='M','MASCULINO','FEMENINO') sexo
                            FROM paciente
                            where
                            pac_ndoc='$query';");
        return array('success' => true, "data" => $sql->data[0]);
    }

    public function st_busca_area() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT
            UPPER(adm_area) as adm_area
            FROM admision
            where
            adm_area like '%$query%'
            group by adm_area");
        return $sql;
    }

    public function st_busca_puesto() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT
            UPPER(adm_puesto) as adm_puesto
            FROM admision
            where
            adm_puesto like '%$query%'
            group by adm_puesto");
        return $sql;
    }

    public function st_busca_appaterno() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT
                            UPPER(pac_appat) as pac_appat
                            FROM paciente
                            where
                            pac_appat like '%$query%'
                            group by pac_appat");
        return $sql;
    }

    public function st_busca_apmaterno() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT
                            UPPER(pac_apmat) as pac_apmat
                            FROM paciente
                            where
                            pac_apmat like '%$query%'
                            group by pac_apmat");
        return $sql;
    }

    public function load_data_pac() {
        $query = 'SELECT
                pac_tdocid,tdoc_desc , pac_ndoc, pac_appat, pac_apmat, pac_nombres
                , pac_sexo, pac_fech_nac, pac_cel, pac_correo
                ,CONCAT(TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE)," AÑOS") as edad
                , pac_ubigeo, dis_desc
                , prov_id, prov_desc
                , dep_id, dep_desc
                , pac_domdir,pac_ubigeo,pac_domdisid
                ,pac_giid,gi_desc,pac_profe,pac_ecid,pac_tip_tel
                FROM paciente
                left join distrito on dis_id=pac_domdisid
                left join provincia on prov_id=dis_provid
                left join departamento on dep_id=prov_depid
                inner join tdocumento on tdoc_id=pac_tdocid
                inner join profesion on pac_ecid=pro_id
                inner join ginstruccion on pac_giid=gi_id
                where
                pac_id=:pac_id';
        $q = $this->sql($query, array(':pac_id' => $_POST['pac_id']));
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_adm() {
        $params = array();
        $params[':adm_pac'] = $_POST['adm_pac'];
        $params[':adm_ruta'] = $_POST['adm_ruta'];
        $params[':adm_tficha'] = $_POST['adm_tficha'];
        $params[':adm_usu'] = $this->user->us_id;
        $params[':adm_st'] = '1';
        $params[':adm_sede'] = $this->user->con_sedid;
        $params[':adm_puesto'] = $_POST['adm_puesto'];
        $params[':adm_area'] = $_POST['adm_area'];
        $params[':adm_foto'] = ($_POST['adm_foto'] == 1) ? 1 : 0;
        $this->begin();
        $paciente = $_POST['adm_pac'];
        $ruta = $_POST['adm_ruta'];
        $verifica = $this->sql("
                    SELECT
                    adm_id AS adm,adm_pac
                    FROM admision
                    WHERE
                    adm_fech BETWEEN concat(Date_format(now(),'%Y-%m-%d'),' 00:00:00')
                    AND concat(Date_format(now(),'%Y-%m-%d'),' 23:59:59')
                    and adm_pac='$paciente' and adm_ruta='$ruta';");

        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "adm" => $verifica->data[0]->adm, "error" => 'Paciente ya fue registrado N° Hoja de Ruta : ' . $verifica->data[0]->adm);
        } else {
            $q = 'insert into admision
                values 
                (null,
                :adm_pac,
                :adm_ruta,
                :adm_tficha,
                :adm_usu,
                now(),
                :adm_st,
                :adm_sede,
                UPPER(:adm_puesto),
                UPPER(:adm_area),
                :adm_foto,
                null,null,null);';
            $query = $this->sql($q, $params);
            if ($query->success) {
                $id = $this->getId();
                if ($_POST['adm_foto'] == 1) {
                    $upload_dir = "images/fotos/";
                    $img = $_POST['foto_desc'];
                    unlink("images/fotos/" . $id . ".png");
                    $img = str_replace('data:image/png;base64,', '', $img);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $file = $upload_dir . $id . ".png";
                    $success = file_put_contents($file, $data);

                    $this->commit();
                    return array('success' => true, "data" => $id);
                } else {
                    $this->commit();
                    return array('success' => true, "data" => $id);
                }
            } else {
                $this->rollback();
                return array('success' => false, "error" => $query);
            }
        }
    }

    public function update_adm() {
        $params = array();
        $params[':adm_id'] = $_POST['adm_id'];
        $params[':adm_pac'] = $_POST['adm_pac'];
        $params[':adm_ruta'] = $_POST['adm_ruta'];
        $params[':adm_tficha'] = $_POST['adm_tficha'];
        $params[':adm_usu'] = $this->user->us_id;
        $params[':adm_sede'] = $this->user->con_sedid;
        $params[':adm_puesto'] = $_POST['adm_puesto'];
        $params[':adm_area'] = $_POST['adm_area'];
        $params[':adm_foto'] = ($_POST['adm_foto'] == 1) ? 1 : 0;
        $this->begin();
        $q = 'Update admision set
                adm_pac=:adm_pac,
                adm_ruta=:adm_ruta,
                adm_tficha=:adm_tficha,
                adm_usu=:adm_usu,
                adm_puesto=UPPER(:adm_puesto),
                adm_area=UPPER(:adm_area),
                adm_foto=:adm_foto
                WHERE adm_id=:adm_id and adm_sede=:adm_sede';
        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $id = $_POST['adm_id'];
            if (strlen($_POST['foto_desc']) !== 0) {
                $upload_dir = "images/fotos/";
                $img = $_POST['foto_desc'];
                unlink("images/fotos/" . $id . ".png");
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                $file = $upload_dir . $id . ".png";
                $success = file_put_contents($file, $data);

                $adm = $_POST['adm_id'];
                $this->commit();
                return array('success' => true, 'data' => $adm);
            } else {
                $this->commit();
                return array('success' => true, 'data' => $id);
            }
        } else {
            $this->rollback();
        }
    }

    public function save_pac() {
        $params = array();
        $params[':pac_usu'] = $this->user->us_id;
        $params[':pac_tdocid'] = $_POST['pac_tdoc'];
        $params[':pac_ndoc'] = $_POST['pac_ndoc'] . $_POST['pac_ndoc_ext'];
        $params[':pac_appat'] = $_POST['pac_appat'];
        $params[':pac_apmat'] = $_POST['pac_apmat'];
        $params[':pac_nombres'] = $_POST['pac_nombres'];
        $params[':pac_sexo'] = $_POST['pac_sexo'];
        $params[':pac_ecid'] = $_POST['pac_ecid'];
        $params[':pac_giid'] = $_POST['pac_giid'];
        //
        $timestamp = strtotime($_POST['pac_fech_nac']);
        $params[':pac_fech_nac'] = date('Y-m-d', $timestamp);
        //
        $params[':pac_profe'] = $_POST['pac_profe'];
        $params[':pac_tip_tel'] = $_POST['pac_tip_tel'];
        $params[':pac_cel'] = $_POST['pac_cel'];
        $params[':pac_correo'] = $_POST['pac_correo'];
        $params[':pac_ubigeo'] = $_POST['pac_ubigeo'];
        $params[':pac_domdisid'] = $_POST['pac_domdisid'];
        $params[':pac_domdir'] = $_POST['pac_domdir'];
        $params[':pac_sedid'] = $this->user->con_sedid;
        $this->begin();
        $dni = $_POST['pac_ndoc'];
        $verifica = $this->sql("SELECT pac_ndoc, concat(pac_appat,' ',pac_apmat,', ',pac_nombres)as nombres FROM paciente where pac_ndoc='$dni';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado: ' . $verifica->data[0]->nombres);
        } else {
            $sexo = ($_POST['pac_sexo'] == 'M') ? 'MASCULINO' : 'FEMENINO';
            $nombres = $_POST['pac_ndoc'] . $_POST['pac_ndoc_ext'] . ' - ' . strtoupper($_POST['pac_appat'] . ' ' . $_POST['pac_apmat'] . ', ' . $_POST['pac_nombres']);
            $q = "insert into paciente 
                values 
                (null, 
                :pac_usu, 
                :pac_tdocid, 
                :pac_ndoc,
                upper(:pac_nombres),
                upper(:pac_appat),
                upper(:pac_apmat),
                :pac_sexo,
                :pac_cel,
                :pac_correo,
                :pac_ecid,
                :pac_giid,
                now(),
                :pac_fech_nac,
                :pac_domdisid,
                upper(:pac_domdir),
                :pac_sedid,
                upper(:pac_profe),
                :pac_tip_tel,
                :pac_ubigeo);";
            $sql1 = $this->sql($q, $params);

            if ($sql1->success) {
                $id = $this->getId();
                $this->commit();
                return array('success' => true, "data" => $id, "nombres" => $nombres, "sexo" => $sexo);
            } else {
                $this->rollback();
            }
        }
    }

    public function update_pac() {
        $params = array();
        $params[':pac_id'] = $_POST['pac_id'];
        $params[':pac_usu'] = $this->user->us_id;

        $params[':pac_tdocid'] = $_POST['pac_tdoc'];
        $params[':pac_ndoc'] = $_POST['pac_ndoc'] . $_POST['pac_ndoc_ext'];
        $params[':pac_appat'] = $_POST['pac_appat'];
        $params[':pac_apmat'] = $_POST['pac_apmat'];
        $params[':pac_nombres'] = $_POST['pac_nombres'];
        $params[':pac_sexo'] = $_POST['pac_sexo'];
        $params[':pac_ecid'] = $_POST['pac_ecid'];
        $params[':pac_giid'] = $_POST['pac_giid'];
        //
        $timestamp = strtotime($_POST['pac_fech_nac']);
        $params[':pac_fech_nac'] = date('Y-m-d', $timestamp);
        //
        $params[':pac_profe'] = $_POST['pac_profe'];
        $params[':pac_tip_tel'] = $_POST['pac_tip_tel'];
        $params[':pac_cel'] = $_POST['pac_cel'];
        $params[':pac_correo'] = $_POST['pac_correo'];
        $params[':pac_ubigeo'] = $_POST['pac_ubigeo'];
        $params[':pac_domdisid'] = $_POST['pac_domdisid'];
        $params[':pac_domdir'] = $_POST['pac_domdir'];
        $this->transaction();
        $q = 'Update paciente set
                pac_usu=:pac_usu,
                pac_fecha=now(),
                pac_tdocid=:pac_tdocid,
                pac_ndoc=:pac_ndoc,
                pac_appat=UPPER(:pac_appat),
                pac_apmat=UPPER(:pac_apmat),
                pac_nombres=UPPER(:pac_nombres),
                pac_sexo=:pac_sexo,
                pac_ecid=:pac_ecid,
                pac_giid=:pac_giid,
                pac_fech_nac=:pac_fech_nac,
                pac_profe=:pac_profe,
                pac_tip_tel=:pac_tip_tel,
                pac_cel=:pac_cel,
                pac_correo=:pac_correo,
                pac_ubigeo=:pac_ubigeo,
                pac_domdisid=:pac_domdisid,
                pac_domdir=UPPER(:pac_domdir)
                where pac_id=:pac_id;';

        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $sexo = ($_POST['pac_sexo'] == 'M') ? 'MASCULINO' : 'FEMENINO';
            $pac_id = $_POST['pac_id'];
            $nombres = $_POST['pac_ndoc'] . $_POST['pac_ndoc_ext'] . ' - ' . strtoupper($_POST['pac_appat'] . ' ' . $_POST['pac_apmat'] . ', ' . $_POST['pac_nombres']);
            $this->commit();
            return array('success' => true, "data" => $pac_id, "nombres" => $nombres, "sexo" => $sexo);
        } else {
            $this->rollback();
        }
    }

    public function list_perfil() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 100;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $pk_emp = $_POST['emp_id'];
        $tficha = isset($_POST['tfi_id']) ? (strlen($_POST['tfi_id']) > 0) ? " and tfi_id=" . $_POST['tfi_id'] : null : null;
        $pk_sede = isset($_POST['sede_id']) ? (strlen($_POST['sede_id']) > 0) ? " and pk_sede=" . $_POST['sede_id'] : null : null;
        $pk_cargo = isset($_POST['cargo_id']) ? (strlen($_POST['cargo_id']) > 0) ? " and pk_cargo=" . $_POST['cargo_id'] : null : null;
        $q = "SELECT 
                pk_id, pk_usu, pk_fech, pk_desc, pk_emp, sede_desc,cargo_desc
                ,tfi_desc,tfi_id, pk_precio, pk_estado
                FROM pack
                inner join tficha on pk_perfil=tfi_id
                inner join empresa_cargos on pk_cargo=cargo_id
                inner join empresa_sede on pk_sede=sede_id
                where pk_emp=$pk_emp and pk_estado=1
                $pk_sede
                $pk_cargo
                $tficha
                ORDER BY Date_format(pk_fech,'%Y') desc,Date_format(pk_fech,'%m') desc,Date_format(pk_fech,'%d') desc
                ,Date_format(pk_fech,'%h') desc,Date_format(pk_fech,'%i') desc,Date_format(pk_fech,'%p') desc; ;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_perfil2() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 100;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $pk_id = $_POST['pk_id'];
        $q = "SELECT
                dpk_pkid,ex_id, ex_desc,ar_desc, dpk_usu, dpk_fech, dpk_precio
                FROM dpack
                inner join examen on ex_id=dpk_exid
                inner join area on ar_id=ex_arid
                where
                dpk_pkid=$pk_id
                order by ar_id,ex_desc asc;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_sede() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $sede_emp = $_POST['emp_id'];
        $q = "SELECT sede_id, sede_emp, sede_desc
                FROM empresa_sede
                where sede_emp='$sede_emp';";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_cargo() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $cargo_emp = $_POST['emp_id'];
        $q = "SELECT cargo_id, cargo_emp, cargo_desc
                FROM empresa_cargos
                where cargo_emp='$cargo_emp';";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function load_data_emp() {
        $query = 'SELECT
            seg_ruc, seg_empr
            , seg_acro, seg_contac, seg_repres
            , seg_ubigeo,dis_desc
            , prov_id, prov_desc
            , dep_id, dep_desc
            , seg_direc
            FROM empresas
            inner join distrito on dis_id=seg_ubigeo
            inner join provincia on prov_id=dis_provid
            inner join departamento on dep_id=prov_depid
            where
            seg_ruc=:ruc';
        $q = $this->sql($query, array(':ruc' => $_POST['ruc']));
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_emp() {
        $params = array();
        $params[':seg_usu'] = $this->user->us_id;
        $params[':seg_ruc'] = $_POST['seg_ruc'];
        $params[':seg_empr'] = $_POST['seg_empr'];
        $params[':seg_acro'] = $_POST['seg_acro'];
        $params[':seg_contac'] = $_POST['seg_contac'];
        $params[':seg_repres'] = $_POST['seg_repres'];
        $params[':seg_ubigeo'] = $_POST['seg_ubigeo'];
        $params[':seg_direc'] = $_POST['seg_direc'];
        $this->transaction();

        $seg_ruc = $_POST['seg_ruc'];
        $verifica = $this->sql("SELECT
                                seg_usu
                                FROM empresas
                                where
                                seg_ruc=$seg_ruc;");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Empresa ya fue registrado por ' . $verifica->data[0]->seg_usu);
        } else {
            $q = 'insert into empresas 
                values 
                (null,
                :seg_usu,
                 now(),
                :seg_ruc,
                UPPER(:seg_empr),
                UPPER(:seg_acro),
                UPPER(:seg_contac),
                UPPER(:seg_repres),
                :seg_ubigeo,
                UPPER(:seg_direc))';
            $sql1 = $this->sql($q, $params);

            if ($sql1->success) {
                $this->commit();
                return $sql1;
            } else {
                $this->rollback();
                return array('success' => FALSE);
            }
        }
    }

    public function update_emp() {
        $params = array();
        $params[':seg_usu'] = $this->user->us_id;
        $params[':seg_id'] = $_POST['emp'];
        $params[':seg_ruc'] = $_POST['seg_ruc'];
        $params[':seg_empr'] = $_POST['seg_empr'];
        $params[':seg_acro'] = $_POST['seg_acro'];
        $params[':seg_contac'] = $_POST['seg_contac'];
        $params[':seg_repres'] = $_POST['seg_repres'];
        $params[':seg_ubigeo'] = $_POST['seg_ubigeo'];
        $params[':seg_direc'] = $_POST['seg_direc'];
        $q = 'Update empresas set                
                seg_usu	=:seg_usu,
                seg_fech=now(),
                seg_ruc=:seg_ruc,
                seg_empr=UPPER(:seg_empr),
                seg_acro=UPPER(:seg_acro),
                seg_contac=UPPER(:seg_contac),
                seg_repres=UPPER(:seg_repres),
                seg_ubigeo=:seg_ubigeo,
                seg_direc=UPPER(:seg_direc)
                where seg_id=:seg_id;';
        return $this->sql($q, $params);
    }

    public function save_ref() {
        $params = array();
        $params[':ref_usu'] = $this->user->us_id;
        $params[':ref_appat'] = $_POST['ref_appat'];
        $params[':ref_apmat'] = $_POST['ref_apmat'];
        $params[':ref_nom'] = $_POST['ref_nom'];
        $params[':ref_especialidad'] = $_POST['ref_especialidad'];
        $params[':ref_cell'] = $_POST['ref_cell'];
        $params[':ref_correo'] = $_POST['ref_correo'];
        $this->transaction();

        $q = 'insert into referente 
                values 
                (null,
                :ref_usu,
                 now(),
                UPPER(:ref_appat),
                UPPER(:ref_apmat),
                UPPER(:ref_nom),
                UPPER(:ref_especialidad),
                :ref_cell,
                :ref_correo)';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $id = $this->getId();
            $nombres = strtoupper($_POST['ref_appat'] . ' ' . $_POST['ref_apmat'] . ', ' . $_POST['ref_nom'] . ' - ' . $_POST['ref_especialidad']);
            $this->commit();
            return array('success' => true, "data" => $id, "nombre" => $nombres);
        } else {
            $this->rollback();
            return array('success' => FALSE);
        }
    }

    public function update_ref() {
        $params = array();
        $params[':ref_id'] = $_POST['ref_id'];
        $params[':ref_usu'] = $this->user->us_id;
        $params[':ref_appat'] = $_POST['ref_appat'];
        $params[':ref_apmat'] = $_POST['ref_apmat'];
        $params[':ref_nom'] = $_POST['ref_nom'];
        $params[':ref_especialidad'] = $_POST['ref_especialidad'];
        $params[':ref_cell'] = $_POST['ref_cell'];
        $params[':ref_correo'] = $_POST['ref_correo'];
        $this->transaction();
        $q = 'Update referente set
                ref_usu=:ref_usu,
                ref_fech=now(),
                ref_appat=UPPER(:ref_appat),
                ref_apmat=UPPER(:ref_apmat),
                ref_nom=UPPER(:ref_nom),
                ref_especialidad=UPPER(:ref_especialidad),
                ref_cell=:ref_cell,
                ref_correo=:ref_correo
                where ref_id=:ref_id;';

        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $ref_id = $_POST['ref_id'];
            $nombres = strtoupper($_POST['ref_appat'] . ' ' . $_POST['ref_apmat'] . ', ' . $_POST['ref_nom'] . ' - ' . $_POST['ref_especialidad']);
            $this->commit();
            return array('success' => true, "data" => $ref_id, "nombre" => $nombres);
        } else {
            $this->rollback();
            return array('success' => FALSE);
        }
    }

    public function load_data_ref() {
        $query = 'SELECT
            ref_appat, ref_apmat, ref_nom, ref_especialidad, ref_cell, ref_correo
            FROM referente
            where
            ref_id=:ref_id';
        $q = $this->sql($query, array(':ref_id' => $_POST['ref_id']));
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function report($adm) {
        $sql = "SELECT adm_id
                ,emp_desc,pac_nombres,pac_appat,pac_apmat,tfi_desc
                ,concat(adm_area,' - ', adm_puesto) puesto,tdoc_desc,pac_ndoc,pac_cel,pac_sexo,pac_id,adm_foto
                ,Date_format(adm_fech,'%d-%m-%Y %h:%i %p') AS adm_fech
                ,TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE) as edad
                ,pac_domdir,dis_desc,prov_desc,dep_desc
                FROM admision
                inner join paciente on adm_pac=pac_id
                left join tdocumento on pac_tdocid=tdoc_id
                inner join pack on adm_ruta=pk_id
                inner join empresa on pk_emp=emp_id
                inner join tficha on adm_tficha=tfi_id
                left join distrito on pac_domdisid=dis_id
                left join provincia on dis_provid=prov_id
                left join departamento on prov_depid=dep_id
                where adm_id=$adm";
        return $this->sql($sql);
    }

    public function area($adm_id, $sexo) {
        $sql = "SELECT ar_id, ar_desc, ex_id, ex_desc
            FROM admision a
            inner join pack on adm_ruta=pk_id
            inner join dpack on pk_id=dpk_pkid
            inner join examen on dpk_exid=ex_id
            inner join area on ex_arid=ar_id
            where
                if('M'='$sexo', ex_id not in(57,50) and adm_id=$adm_id, adm_id=$adm_id)
            order by ar_id,ex_desc";
        $area = $this->sql($sql);
        return $area;
    }

    public function reporte_servicios($adm_id) {
        $sql = $this->sql("SELECT
                det_id, det_serv, adm_emp, serv_desc det_serv_desc,area_desc
                FROM admision
                inner join det_adm on det_adm=adm_id
                inner join servicios on serv_id=det_serv
                inner join area on area_id=serv_area
                where
                adm_id=$adm_id;");
        foreach ($sql->data as $i => $value) {
            $empresa = $value->adm_emp;
            $servicio = $value->det_serv;
            $q = "SELECT
                    tarf_id, tarf_precio
                    FROM tarifario
                    where
                    tarf_emp=$empresa and
                    tarf_serv=$servicio;";
            $sql2 = $this->sql($q);
            foreach ($sql2->data as $k => $values) {
                $value->det_precio = round($values->tarf_precio, 2);
            }
        }
        return $sql;
    }

}
