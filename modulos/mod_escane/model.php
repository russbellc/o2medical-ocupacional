<?php

class model extends core
{

    public function list_resumen()
    {
        $usuario = $this->user->us_id;
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $columna = isset($_POST['columna']) ? $_POST['columna'] : NULL;
        $query = isset($_POST['query']) ? sprintf($_POST['query']) : NULL;
        $sql1 = ("SELECT
                    adm_id as adm,adm_foto
                    #,IF((adm_pdf) = 1,IF((reg_st)=1,1,2),0) as st_id
                    ,tfi_desc,emp_desc, pac_ndoc
                    ,adm_fech AS FECHA
                    ,concat(pac_appat,' ',pac_apmat,' ',pac_nombres)as nombre,pac_sexo
                    ,'admin' as usu
                    ,adm_aptitud as val_aptitu
                    ,concat(TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE),' AÑOS') as edad
                    , if((adm_pdf)=1,1,0) pdf
                    #,reg_id
                    FROM admision
                    inner join paciente on adm_pac=pac_id
                    inner join pack on adm_ruta=pk_id
                    left join empresa on pk_emp=emp_id
                    left join tficha on adm_tficha=tfi_id
                    #left join reg_escaneados on reg_adm=adm_id
                    ");

        $verifica = $this->sql("SELECT acc_st, acc_emp FROM acceso_empresa where acc_usu='$usuario';");


        if ($verifica->total > 0) {
            if ($verifica->data[0]->acc_st == "1") {
                $and = 'where ';
            } else {
                $string = "";
                $total = $verifica->total;
                for ($i = 0, $size = $total; $i < $size; ++$i) {
                    if ($size - 1 == $i) {
                        $coma = '';
                    } else {
                        $coma = ', ';
                    }
                    $string = $string . $verifica->data[$i]->acc_emp . $coma;
                }
                $sql1 .= " where emp_id IN ($string) ";
                $and = 'and';
            }
            if (!is_null($columna) && !is_null($query)) {
                if ($columna == "1") {
                    $sql1 .= "$and adm_id=$query";
                } else if ($columna == "2") {
                    $sql1 .= "and pac_ndoc=$query";
                } else if ($columna == "3") {
                    $sql1 .= "$and concat(pac_appat,' ',pac_apmat,' ',pac_nombres) like '%$query%'";
                } else if ($columna == "4") {
                    $sql1 .= "$and concat(emp_acro,emp_desc,emp_id) like '%$query%'";
                } else if ($columna == "5") {
                    $sql1 .= "$and tfi_desc LIKE '%$query%'";
                }
                $sql1 .= " and DATE(adm_fech)>=date('2015-02-03 00:00:00') group by adm_id order by adm_id DESC;";
            } else {
                $sql1 .= " and DATE(adm_fech)>=date('2015-02-03 00:00:00') group by adm_id order by adm_id DESC;";
            }

            $sql = $this->sql($sql1);
            foreach ($sql->data as $i => $value) {
                $adm_id = $value->adm;
                $nro_examenes = $value->nro_examenes;
                $verifica = $this->sql("SELECT count(m_medicina_adm)total FROM mod_medicina where m_medicina_adm=$adm_id;");
                $total = $verifica->data[0]->total;
                $value->st_id = ($nro_examenes == $total) ? '1' : '0';
            }
            $sql->data = array_slice($sql->data, $start, $limit);
            return $sql;
        } else {
            return array('success' => false, "error" => 'Usuario no tiene accseso : ' . $usuario);
        }
    }

    public function load_pdf()
    {
        $params = array();
        $params[':adm_id'] = $_POST['adm_id'];
        $q = ' UPDATE admision SET adm_pdf=1 WHERE adm_id=:adm_id;';
        return $this->sql($q, $params);
    }

    public function save()
    {
        $params = array();
        $params[':reg_adm'] = $_POST['adm_id'];
        $params[':reg_st'] = '1';
        $this->begin();
        $adm = $_POST['adm_id'];
        $verifica = $this->sql("SELECT reg_id, reg_adm, reg_st FROM reg_escaneados where reg_adm=$adm;");

        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue Visto: ' . $verifica->data[0]->reg_adm);
        } else {

            $q = 'INSERT INTO reg_escaneados
            VALUES (NULL,
            :reg_adm,
            :reg_st);';
            $query = $this->sql($q, $params);
            if ($query->success) {
                //                $id = $this->getId();
                $params = array();
                $params[':his_reg'] = $adm;
                $params[':his_usu'] = $this->user->us_id;
                $q2 = 'INSERT INTO histo_escaneados
                        VALUES (NULL,
                        :his_reg,
                        :his_usu,
                        now())';
                $this->commit();
                return $this->sql($q2, $params);
            } else {
                $this->rollback();
            }
        }
    }

    public function update()
    {
        $params = array();
        $params[':reg_adm'] = $_POST['adm_id'];
        $params[':reg_st'] = '1';
        $this->begin();
        $adm = $_POST['adm_id'];
        $usu = $this->user->us_id;
        $verifica = $this->sql("SELECT his_id, his_reg, his_usu FROM histo_escaneados where his_reg=$adm and his_usu='$usu';");

        if ($verifica->total > 0) {
            $params = array();
            $params[':his_reg'] = $adm;
            $params[':his_usu'] = $this->user->us_id;
            $q2 = 'UPDATE histo_escaneados SET
                   his_fech= now()
                WHERE
                his_reg=:his_reg and his_usu=:his_usu;';
            $this->commit();
            return $this->sql($q2, $params);
        } else {
            $params = array();
            $params[':his_reg'] = $adm;
            $params[':his_usu'] = $this->user->us_id;
            $q2 = 'INSERT INTO histo_escaneados
                        VALUES (NULL,
                        :his_reg,
                        :his_usu,
                        now())';
            $this->commit();
            return $this->sql($q2, $params);
        }
    }

    public function municip()
    {
        $usu = $this->user->us_id;
        $q = "SELECT
            acc_usu, acc_tipe
            FROM acceso_empresa
            where
            acc_usu='$usu'";
        return $this->sql($q);
    }
}
