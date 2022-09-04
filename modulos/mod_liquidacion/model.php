<?php

class model extends core {

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

    public function st_buca_ruc() {
        $query = isset($_POST['ruc']) ? $_POST['ruc'] : NULL;
        $sql = $this->sql("SELECT emp_id
                            FROM empresa
                            where
                            emp_id='$query'
                            group by emp_id;");
        return $sql;
    }

    public function load_empresa() {
        $query = 'SELECT
emp_id, emp_usu, emp_fech, emp_desc
,emp_acro, emp_telf, emp_estado, emp_direc
FROM empresa
where emp_id=:emp_id';
        $q = $this->sql($query, array(':emp_id' => $_POST['emp_id']));
        return array('success' => true, 'data' => $q->data[0]);
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

    public function list_perfil() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $pk_emp = $_POST['emp_id'];
        $pk_sede = isset($_POST['sede_id']) ? (strlen($_POST['sede_id']) > 0) ? " and pk_sede=" . $_POST['sede_id'] : null : null;
        $pk_cargo = isset($_POST['cargo_id']) ? (strlen($_POST['cargo_id']) > 0) ? " and pk_cargo=" . $_POST['cargo_id'] : null : null;
        $q = "SELECT 
                pk_id, pk_usu, pk_fech, pk_desc, pk_emp, sede_desc,cargo_desc
                ,tfi_desc, pk_precio, pk_estado
                FROM pack
                inner join tficha on pk_perfil=tfi_id
                inner join empresa_cargos on pk_cargo=cargo_id
                inner join empresa_sede on pk_sede=sede_id
                where pk_emp=$pk_emp
                $pk_sede
                $pk_cargo
                ORDER BY Date_format(pk_fech,'%Y') desc,Date_format(pk_fech,'%m') desc,Date_format(pk_fech,'%d') desc
                ,Date_format(pk_fech,'%h') desc,Date_format(pk_fech,'%i') desc,Date_format(pk_fech,'%p') desc; ;";
        $sql = $this->sql($q);
        foreach ($sql->data as $k => $value) {
            $fech = $value->pk_fech;
            $temp1 = strtotime(date("Y-m-d H:i:s")); //segs desde fecha unix
            $temp2 = strtotime($fech); //segs desde la fecha unix
            $diferencia = abs($temp1 - $temp2); //abs=valor absoluto :D
            $value->horas = floor($diferencia / 60 / 60);
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function save_empresa() {
        $params = array();
        $params[':emp_id'] = $_POST['emp_id'];
        $params[':emp_usu'] = $this->user->us_id;
        $params[':emp_desc'] = $_POST['emp_desc'];
        $params[':emp_acro'] = $_POST['emp_acro'];
        $params[':emp_telf'] = $_POST['emp_telf'];
        $params[':emp_estado'] = $_POST['emp_estado'];
        $params[':emp_direc'] = $_POST['emp_direc'];
        $this->begin();

        $ruc = $_POST['emp_id'];
        $verifica = $this->sql("SELECT emp_id, emp_desc ruc FROM empresa where emp_id=$ruc;");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'La empresa ya fue registrada: ' . $verifica->data[0]->ruc, "emp_id" => $verifica->data[0]->emp_id);
        } else {
            $q = 'INSERT INTO empresa VALUES 
               (:emp_id,
                :emp_usu,
                now(),
                UPPER(:emp_desc),
                UPPER(:emp_acro),
                :emp_telf,
                UPPER(:emp_estado),
                UPPER(:emp_direc))';
            $query = $this->sql($q, $params);
            if ($query->success) {
                $this->commit();
                return $query;
            } else {
                $this->rollback();
            }
        }
    }

    public function save_sede() {
        $params = array();
        $params[':sede_emp'] = $_POST['sede_emp'];
        $params[':sede_desc'] = $_POST['sede_desc'];
        $this->begin();
        $q = 'INSERT INTO empresa_sede VALUES 
               (null,
                :sede_emp,
                UPPER(:sede_desc))';
        $query = $this->sql($q, $params);
        if ($query->success) {
            $this->commit();
            return $query;
        } else {
            $this->rollback();
        }
    }

    public function save_cargo() {
        $params = array();
        $params[':cargo_emp'] = $_POST['cargo_emp'];
        $params[':cargo_desc'] = $_POST['cargo_desc'];
        $this->begin();
        $q = 'INSERT INTO empresa_cargos VALUES 
               (null,
                :cargo_emp,
                UPPER(:cargo_desc))';
        $query = $this->sql($q, $params);
        if ($query->success) {
            $this->commit();
            return $query;
        } else {
            $this->rollback();
        }
    }

    public function list_area() {
        $sql = $this->sql("SELECT ar_id, ar_desc FROM area order by ar_id;");
        return $sql;
    }

    public function list_examen() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 50;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $area = $_POST['area'];
        $q = "SELECT ex_id, ex_arid, ex_desc, ex_tarif
                FROM examen
                where ex_arid='$area' order by ex_desc;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function load_exame() {
        $query = "SELECT
            ex_id, ex_arid, ex_desc, ex_tarif
            FROM examen
            where
            ex_id=:ex_id"; //serv_med, serv_med2, serv_desc
        $q = $this->sql($query, array(':ex_id' => $_POST['ex_id']));
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_exa() {
        $params = array();
        $params[':ex_arid'] = $_POST['area'];
        $params[':ex_desc'] = $_POST['ex_desc'];
        $params[':ex_tarif'] = $_POST['ex_tarif'];
        $this->transaction();

        $ex_desc = $_POST['ex_desc'];
        $verifica = $this->sql("SELECT ex_id, ex_desc FROM examen where ex_desc='$ex_desc';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'No se pudo registrar. Porque el examen <b>' . $verifica->data[0]->ex_desc . '</b> ya fue registrado anteriormente.', "ex_id" => $verifica->data[0]->ex_id);
        } else {
            $q = 'insert into examen 
                values 
                (null,
                :ex_arid,
                UPPER(:ex_desc),
                :ex_tarif)';
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

    public function update_exa() {
        $params = array(); //ex_id
        $params[':ex_id'] = $_POST['ex_id'];
        $params[':ex_desc'] = $_POST['ex_desc'];
        $params[':ex_tarif'] = $_POST['ex_tarif'];
        $this->transaction();
        $q = 'update examen set   
                ex_tarif=:ex_tarif,
                ex_desc=UPPER(:ex_desc)
                where ex_id=:ex_id;';
        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $this->commit();
            return $sql1;
        } else {
            $this->rollback();
            return array('success' => FALSE);
        }
    }

    public function load_exameLab() {
        $query = "SELECT
            ex_id, ex_arid, ex_desc, ex_tarif,labc_uni,labc_valor
            FROM examen
            left join lab_conf on labc_ex=ex_id
            where
            ex_id=:ex_id"; //serv_med, serv_med2, serv_desc
        $q = $this->sql($query, array(':ex_id' => $_POST['ex_id']));
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_exaLab() {
        $params = array();
        $params[':ex_arid'] = $_POST['area'];
        $params[':ex_desc'] = $_POST['ex_desc'];
        $params[':ex_tarif'] = $_POST['ex_tarif'];
        $this->transaction();

        $ex_desc = $_POST['ex_desc'];
        $verifica = $this->sql("SELECT ex_id, ex_desc FROM examen where ex_desc='$ex_desc';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'No se pudo registrar. Porque el examen <b>' . $verifica->data[0]->ex_desc . '</b> ya fue registrado anteriormente.', "ex_id" => $verifica->data[0]->ex_id);
        } else {
            $q = 'insert into examen 
                values 
                (null,
                :ex_arid,
                UPPER(:ex_desc),
                :ex_tarif)';
            $sql1 = $this->sql($q, $params);
            if ($sql1->success) {
                $labc_ex = $this->getId();
                $labc_uni = $_POST['labc_uni'];
                $labc_valor = $_POST['labc_valor'];
                $q2 = "INSERT INTO lab_conf VALUES 
                        ('$labc_ex',
                        '$labc_uni',
                        '$labc_valor');";
                $query2 = $this->sql($q2);
                $this->commit();
                return $query2;
            } else {
                $this->rollback();
                return array('success' => FALSE);
            }
        }
    }

    public function update_exaLab() {
        $params = array(); //ex_id
        $params[':ex_id'] = $_POST['ex_id'];
        $params[':ex_desc'] = $_POST['ex_desc'];
        $params[':ex_tarif'] = $_POST['ex_tarif'];
        $this->transaction();
        $q = 'update examen set   
                ex_tarif=:ex_tarif,
                ex_desc=UPPER(:ex_desc)
                where ex_id=:ex_id;';
        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $labc_ex = $_POST['ex_id'];
            $labc_uni = $_POST['labc_uni'];
            $labc_valor = $_POST['labc_valor'];
            $q2 = "update lab_conf set   
                labc_uni='$labc_uni',
                labc_valor='$labc_valor'
                where labc_ex=$labc_ex;";
            $query2 = $this->sql($q2);
            $this->commit();
            return $query2;
        } else {
            $this->rollback();
            return array('success' => FALSE);
        }
    }

    public function list_exam() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 50;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $consul = "SELECT ex_id as id, ar_desc, ex_desc, ex_tarif
                    FROM examen
                    inner join area on ar_id=ex_arid
                    where ex_desc like '%$query%' || ar_desc like '%$query%' order by ex_arid,ex_desc";
        $arr = $this->sql($consul);
        $arr->data = array_slice($arr->data, $start, $limit);
        return array('success' => true, 'data' => $arr->data, 'total' => $arr->total);
    }

    public function list_tficha() {
        return $this->sql("select tfi_id, tfi_desc from tficha");
    }

    public function list_exam2() {
        return $this->sql(sprintf("SELECT dpk_pkid, ex_id as id, ex_desc, ar_desc, pk_desc,pk_estado
                        FROM examen
                        inner join dpack on dpk_exid=ex_id
                        inner join area on ar_id=ex_arid
                        inner join pack on pk_id=dpk_pkid
                        where dpk_pkid='%s' order by ex_desc;", (isset($_POST['pack']) && !empty($_POST['pack'])) ? $_POST['pack'] : null));
    }

    public function numeroletra() {
        $total = $_POST['total'];
        return array('success' => true, "letra" => $this->numtoletras(round($total, 2)));
    }

    public function savePack() {
        $params = array();
        $params[':pk_usu'] = $this->user->us_id;
        $params[':pk_desc'] = (isset($_POST['pk_desc'])) ? $_POST['pk_desc'] : NULL;
        $params[':pk_emp'] = (isset($_POST['pk_emp'])) ? $_POST['pk_emp'] : NULL;
        $params[':pk_sede'] = (isset($_POST['pk_sede'])) ? $_POST['pk_sede'] : NULL;
        $params[':pk_cargo'] = (isset($_POST['pk_cargo'])) ? $_POST['pk_cargo'] : null;
        $params[':pk_perfil'] = (isset($_POST['pk_perfil'])) ? $_POST['pk_perfil'] : NULL;
        $params[':pk_precio'] = $_POST['pk_precio'];

        $this->begin();
        $exa = explode(',', $_POST['exId']);
        $usu = $this->user->us_id;
        $pk_desc = $_POST['pk_desc'];
        $pk_emp = $_POST['pk_emp'];
        $pk_sede = $_POST['pk_sede'];
        $pk_cargo = $_POST['pk_cargo'];
        $_v = $this->sql("select pk_desc from pack where pk_desc='$pk_desc' && pk_emp='$pk_emp' && pk_sede='$pk_sede' && pk_cargo='$pk_cargo';");
        if ($_v->total > 0) {
            $this->rollback();
            return array('success' => FALSE, 'error' => 'Ya existe un perfil con este nombre ' . $_v->data[0]->pk_desc);
        } elseif ($_v->success) {
            $pack = $this->sql('insert into pack 
                values(null,:pk_usu, now(),
                    :pk_desc, :pk_emp, :pk_sede, :pk_cargo, :pk_perfil, :pk_precio, 1)', $params);
            if ($pack->success) {
                $id = $this->getId();
                $sql = '';
                $cont_exa = (count($exa) - 1);
                foreach ($exa as $nro => $r) {
                    $exa2 = explode(':', $r);
                    $counter = 0;
                    foreach ($exa2 as $r2) {
                        $counter++;
                        if ($counter > 1) {
                            $precio = $r2;
                        } else {
                            $exaid = $r2;
                        }
                    }
                    $sql .= "($id,$exaid,$precio,'$usu',now())";
                    $sql .= ($cont_exa == $nro) ? ";" : ",";
                }
                $sql = 'Insert into dpack values' . $sql;
                $consul = $this->sql($sql);
                if ($consul->success) {
                    $this->commit();
                    return array('success' => TRUE, 'data' => $consul);
                } else {
                    $this->rollback();
                    return array('success' => FALSE, 'error' => $consul);
                }
            } else {
                $this->rollback();
                return array('success' => FALSE, 'error' => $pack);
            }
        } else {
            $this->rollback();
            return array('success' => FALSE, 'error' => $_v);
        }
    }

    public function load_data_ruta() {
        $query = "SELECT pk_desc, sede_desc,cargo_desc, tfi_desc, pk_precio, pk_estado
            FROM pack
            inner join tficha on pk_perfil=tfi_id
            inner join empresa_cargos on pk_cargo=cargo_id
            inner join empresa_sede on pk_sede=sede_id
            where pk_id=:pk_id;";
        $q = $this->sql($query, array(':pk_id' => $_POST['pk_id']));
//        foreach ($q->data as $k => $value) {
//            $value->totaletra = $this->numtoletras(round($value->pk_precio, 2));
//        }
        return array('success' => true, 'data' => $q->data[0]);
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

    public function update_rutaExa() {
        $params = array(); //ex_id
        $params[':dpk_pkid'] = $_POST['dpk_pkid'];
        $params[':dpk_exid'] = $_POST['dpk_exid'];
        $params[':dpk_precio'] = $_POST['dpk_precio'];
        $params[':dpk_usu'] = $this->user->us_id;
        $this->transaction();
        $q = 'update dpack set 
                dpk_precio=:dpk_precio,
                dpk_usu=:dpk_usu,
                dpk_fech=now()
                where dpk_pkid=:dpk_pkid and dpk_exid=:dpk_exid;';
        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $dpk_pkid = $_POST['dpk_pkid'];
            $total = $_POST['total'];
            $q2 = "update pack set   
                pk_precio=$total
                where pk_id=$dpk_pkid;";
            $query2 = $this->sql($q2);
            $this->commit();
            return $query2;
        } else {
            $this->rollback();
            return array('success' => FALSE);
        }
    }

    public function list_exa() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT
                    ex_id, ar_desc, ex_desc, ex_tarif
                    FROM examen
                    inner join area on ar_id=ex_arid
                    where
                    concat(ar_desc, ex_desc) like '%$query%'
                    order by ar_id,ex_desc;");
        return $sql;
    }

    public function save_addExamen() {
        $params = array(); //ex_id
        $params[':dpk_pkid'] = $_POST['pk_id'];
        $params[':dpk_exid'] = $_POST['cboExamen'];
        $params[':dpk_precio'] = $_POST['ex_tarif'];
        $params[':dpk_usu'] = $this->user->us_id;
        $this->transaction();

        $pk_id = $_POST['pk_id'];
        $dpk_exid = $_POST['cboExamen'];
        $_v = $this->sql("select * from dpack where dpk_exid='$pk_id' and dpk_exid='$dpk_exid';");
        if ($_v->success && $_v->total >= 1) {
            $this->rollback();
            return array('success' => FALSE, 'error' => 'Ya existe un examen con este nombre');
        } elseif ($_v->success) {
            $q = 'insert into dpack values
            (:dpk_pkid, :dpk_exid, :dpk_precio, :dpk_usu, now());';
            $sql1 = $this->sql($q, $params);
            if ($sql1->success) {
                $pk_id = $_POST['pk_id'];
                $total = ($_POST['total'] + $_POST['ex_tarif']);
                $q2 = "update pack set   
                pk_precio=$total
                where pk_id=$pk_id;";
                $query2 = $this->sql($q2);
                $this->commit();
                return $query2;
            } else {
                $this->rollback();
                return array('success' => FALSE);
            }
        }
    }

    public function update_pk() {
        $params = array(); //ex_id
        $params[':pk_id'] = $_POST['pk_id'];
        $params[':pk_estado'] = $_POST['pk_estado'];
        $params[':pk_desc'] = $_POST['pk_desc'];
        $this->transaction();
        $q = 'update pack set
                pk_estado=:pk_estado,
                pk_desc=:pk_desc
                where pk_id=:pk_id;';
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
