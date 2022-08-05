<?php

require 'core.php';

//require 'core.php';

final class loader extends core {

    private $_modpath;
    private $_rmodpath;
    private $_modfile;
    private $_vfile;
    private $_sfile;
    private $_mfile;
    private $_modname;
    private $_report;
    private $_excel;

    private function actividad() {
        $_SESSION[__sesname]['activo'] = mktime();
    }

    public function verify() {
        $response = array();
        $_SESSION["time"] = time();
        if (time() - $_SESSION["time"] < 28800) {
//            echo 'no ha pasado una hora';
            $response['success'] = true;
        } else {
//            echo 'ha pasado mas de una hora';
            session_unset(__sesname);
            session_destroy();
            $response['success'] = false;
        }
        echo json_encode($response);
    }

    public function __construct() {
        parent::__construct();
        $this->_modname = (isset($_REQUEST['sys_modname'])) ? $_REQUEST['sys_modname'] : md5(__sesname);
        $this->_rmodpath = sprintf("%s/%s", __modpath, $this->_modname);
        $this->_modpath = "../{$this->_rmodpath}";
        $this->_modfile = $this->_modpath . "/model.php";
        $this->_vfile = $this->_modpath . "/view.js";
        $this->_sfile = $this->_modpath . "/style.css";
        $this->_mfile = $this->_modpath . "/model.php"; //sys_report
        $this->_report = $this->_modpath . "/reporte/" . (isset($_REQUEST['sys_report']) ? $_REQUEST['sys_report'] : '') . '.php';
        $this->_excel = $this->_modpath . "/excel/" . (isset($_REQUEST['sys_report']) ? $_REQUEST['sys_report'] : '') . '.php';
        $this->_txt = $this->_modpath . "/txt/" . (isset($_REQUEST['sys_report']) ? $_REQUEST['sys_report'] : '') . '.php';
    }

    public function sys_style() {
        $v = $this->sys_verifica();
        require ($v['success']) ? "app.css" : "login.css";
    }

    public function sys_loadstyle() {
        $_s = file_get_contents($this->_sfile);
        $_s = str_replace('<[images]>', "../../" . $this->_modpath . "/images", $_s);
        $_s = str_replace('<[sys_images]>', "../../../images", $_s);
        return $_s;
    }

    public function sys_loadreport() {
//        echo $this->_report;
        $v = $this->sys_verifica();
        if ($v['success'] && file_exists($this->_report)) {
            require'../extras/tcpdf/config/lang/spa.php';
            require'../extras/tcpdf/tcpdf.php';
            require_once('../extras/fpdi/fpdi.php');
//            require'../extras/fpdi/fpdf.php';
//            require'../extras/fpdi/fpdi.php';
//            require'../extras/tcpdf_import.php';
            require $this->_mfile;
            require $this->_report;
        } else {
            echo "no existe archivo";
        }
    }

    public function sys_loadexcel() {
        $v = $this->sys_verifica();
        if ($v['success'] && file_exists($this->_excel)) {
            require'../extras/excel/PHPExcel.php';
            require $this->_mfile;
            require $this->_excel;
        } else {
            echo "No existe archivo Excel " . $this->_mfile . " ---- " . $this->_report . " ---- " . $this->_excel . " ---- " . $v['success'];
        }
    }

    public function sys_load_txt() {
        $v = $this->sys_verifica();
        if ($v['success'] && file_exists($this->_txt)) {
            require $this->_mfile;
            require $this->_txt;
        } else {
            echo "No existe archivo txt " . $this->_mfile . " ---- " . $this->_report . " ---- " . $this->_txt . " ---- " . $v['success'];
        }
    }

    public function sys_loadview() {
        $file = "";
        $v = $this->sys_verifica();
        if ($v['success']) {
            if (file_exists($this->_vfile)) {
                $file = file_get_contents($this->_vfile);
                $file = str_replace('<[view]>', md5($this->_modname), $file);
                $file = str_replace('<[images]>', $this->_rmodpath . "/images", $file);
                $file = str_replace('<[sys_images]>', "images", $file);
                $file = str_replace('<[controller]>', "system/loader.php?sys_acction=sys_loadmodel&sys_modname={$this->_modname}", $file);
                $file = str_replace('<[report]>', "system/loader.php?sys_acction=sys_loadreport&sys_modname={$this->_modname}&format=pdf", $file);
                $file = str_replace('<[excel]>', "system/loader.php?sys_acction=sys_loadexcel&sys_modname={$this->_modname}&format=excel", $file);
            } else {
                $file = <<<script
            alert("La Vista No Existe");
script;
            }
        } else {
            $file = <<<script
            Ext.MessageBox.alert('Alerta', 'No tiene Acceso para ingresar al sistema');location.reload();
script;
        }

        return $file;
    }

    public function sys_loadmodel() {
        if (file_exists($this->_mfile)) {
            include $this->_mfile;
            $_sysm = new model();
            return $_sysm->{$_POST['acction']}();
        } else {
            return array();
        }
    }

    public function sys_loadmod() {
        $v = $this->sys_verifica();
        $return = "";
        if ($v['success']) {
            $return.='<script type="text/javascript" src="code/sys_loadview/js/sys_modname=' . $_REQUEST['sys_modname'] . '"></script>';
            $return.=file_exists($this->_sfile) ? '<link TYPE="text/css" href="code/sys_loadstyle/css/sys_modname=' . $_REQUEST['sys_modname'] . '" rel="stylesheet" type="text/css" />' : '';
            $return.="<div id='" . md5($_REQUEST['sys_modname']) . "'>";
            include('script type="text/javascript" src="..modulos/mod_mf/view.js"></script>');

            "</div>";
        } else {
            $return.="<script>Ext.MessageBox.alert('Alerta', 'No tiene Acceso para ingresar al Modulo');location.reload();</script>";
        }

        return $return;
    }

    public function load_modname() {
        $v = $this->sys_verifica();
        $return = "";
        if ($v['success']) {
            $return.="<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=" . $_REQUEST['valida_mod'] . "&sys_report=reporte&adm=" . $_REQUEST['adm_id'] . "'></iframe>";
        } else {
            $return.="<script>Ext.MessageBox.alert('Alerta', 'No tiene Acceso para ingresar al Modulo');location.reload();</script>";
        }
        return $return;
    }

    public function sys_script() {
        $v = $this->sys_verifica();
        require ($v['success']) ? "app.js" : "login.js";
    }

    public function sys_logout() {
        if (isset($_SESSION[__sesname])) {
            unset($_SESSION[__sesname]);
        }
        return array("success" => true);
    }

    public function sys_getlogo() {
        return '<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false"><center><IMG SRC="images/' . $this->user->emp_id . '/logo.svg" style="padding: 0 0 0 0" width="190" ></center></div>';
    }

    public function sys_usuperfiles() {
        $sql = $this->sql("SELECT men_id,men_desc,con_usuid  FROM sys_control
        inner join sys_perfil on con_perid=per_id
        inner join sys_dperfil on per_id=dpe_perid
        inner join sys_menu on dpe_menid=men_id
        and con_usuid='{$this->user->us_id}'
        and con_sedid='{$this->user->con_sedid}'
        and men_smenid is null where men_ord>=20 order by men_ord ");
        return $sql;
    }

    public function sys_usuperfiles11() {
        $sql = $this->sql("SELECT men_id,men_desc,con_usuid  FROM sys_control
        inner join sys_perfil on con_perid=per_id
        inner join sys_dperfil on per_id=dpe_perid
        inner join sys_menu on dpe_menid=men_id
        and con_usuid='{$this->user->us_id}'
        and con_sedid='{$this->user->con_sedid}'
        and men_smenid is null where men_ord<=20 order by men_ord limit 13");
        return $sql;
    }

    public function sys_menu() {
        if (isset($_SESSION[__sesname])) {
            $a = ($this->user->per_id == 0) ? "SELECT * FROM sys_menu where men_smenid is null order by men_ord" : "SELECT men.* FROM sys_menu men inner join sys_dperfil on men_id=dpe_perid  where men_smenid is null and dpe_perid={$this->user->per_id} order by men_ord ";
            $result = $this->sql($a);
            foreach ($result->data as $i => &$row) {
                $row->icon = sprintf("modulos/%s/icon.png", $row->men_id);
            }
            return $result;
        } else
            return array();
    }

    public function sys_load_module() {

        if (is_dir($this->_modpath)) {
            if (file_exists($this->_modfile)) {
                require $this->_modfile;
                if (class_exists("model")) {
                    $sys_model = new model();
                    if (is_subclass_of($sys_model, "core")) {
                        if (method_exists($sys_model, $_REQUEST['sys_method'])) {
                            $_modr = new ReflectionMethod($sys_model, $_REQUEST['sys_method']);
                            if ($_modr->isPublic()) {
                                return $sys_model->{$_REQUEST['sys_method']}();
                            } else {
                                echo "el metodo no es publico";
                            }
                        } else
                            echo "El metodo no existe";
                    }
                    else {
                        echo "La clase model no exiende de la clase core";
                    }
                } else
                    echo "No existe clase model";
            } else {
                echo "No existe Model";
            }
        } else
            echo "No existe modulo";
    }

    //verifica  si exite lavariable de secion
    public function sys_verifica() {
        return array('success' => isset($this->user));
    }

//perfil del usuario cuando tiene mas de 2 sedes 
    public function sys_perfiles() {

        $response = array();
        $t = $this->sql("SELECT emp_rs, sed_direccion FROM sys_empresa inner join sys_sede on sed_empid=emp_id and sed_id='2' and sed_empid='1'");

        return $t;
    }

    public function sys_login2() {
        $this->sys_logout();
        $response = array();
        if (isset($_POST['key'])) {
            list($usu_id, $sed_id, $per_id, $emp_id) = explode('-', base64_decode($_POST['key']));
            $query = "select con_usuid,concat(usu_nombres,' ', usu_appat) as nombres,
                    per_desc,per_id, con_sedid, con_perid, sed_clinic, sed_empid,
                    sed_nombre, sed_direccion,emp_rs from sys_control
                    inner join sys_sede on con_sedid=sed_id && sed_st=1
                    inner join sys_empresa on sed_empid=emp_id 
                    inner join sys_usuario on con_usuid = usu_id 
                    inner join sys_perfil on con_perid=per_id
                    && emp_st=1
                    where con_st=1 && con_usuid='$usu_id' && 
                    con_sedid='$sed_id' && con_perid='$per_id'";
            $v = $this->sql($query);
            if ($v->success && $v->total == 1) {
                $response['success'] = true;
                $response['msg'] = "Ok";

                $verifica = $this->sql("SELECT access_st, access_emp, access_perfil FROM acceso_modulo where access_usu='$usu_id';");
                if ($verifica->total > 0) {
                    $acceso = $verifica->data[0]->access_st;
                    if ($verifica->data[0]->access_st == "1") {
                        $empresas = "";
                    } else {
                        $string = "";
                        $total = $verifica->total;
                        for ($i = 0, $size = $total; $i < $size; ++$i) {
                            if ($size - 1 == $i) {
                                $coma = '';
                            } else {
                                $coma = ', ';
                            }
                            $string = $string . $verifica->data[$i]->access_emp . $coma;
                        }
                        $empresas.=$string;
                    }
                }
                $p = array();
                $p['usu_id'] = $v->data[0]->con_usuid;
                $p['usu_nom'] = $v->data[0]->nombres;
                $p['sed_id'] = $v->data[0]->con_sedid; //per_desc
                $p['per_desc'] = $v->data[0]->per_desc; //per_id
                $p['per_id'] = $v->data[0]->con_perid;
                $p['emp_id'] = $v->data[0]->sed_empid;
                $p['acceso'] = $acceso;
                $p['empresas'] = $empresas;
                $p['emp_rs'] = $v->data[0]->emp_rs;
                $p['sed_direccion'] = $v->data[0]->sed_direccion;
                $p['sed_clinic'] = $v->data[0]->sed_clinic;
                $p['sed_nombre'] = $v->data[0]->sed_nombre;
                $_SESSION[__sesname]['activo'] = mktime();
                $_SESSION[__sesname] = $p;
            } elseif ($v->success && $v->total != 1) {
                $response['success'] = FALSE;
                $response['msg'] = "El Usuario no  existe";
            } else {
                $response['success'] = FALSE;
                $response['msg'] = $v->error;
            }
        } else
            $response = array('success' => FALSE, "msg" => "No existe Variable");
        return $response;
    }

    public function sys_header() {
        if (isset($_SESSION[__sesname])) {
            $l = array("logo" => 'images/' . $this->user->emp_id . '/logo.png');
            return $l;
        } else
            return array();
    }

    public function sys_infouser() {
        $colorTx = "#FFFFFF";
        $tamanoTx = 2;
        $html = "

<link rel='shortcut icon' href='images/favicon.ico'>
<div class='post-body entry-content' expr:id='&quot;post-body-&quot; + data:post.id' itemprop='articleBody' oncontextmenu='return false' ondragstart='return false' onmousedown='return false' onselectstart='return false'>
            <div title='{$this->user->per_desc}' style='height: 75px;width: 265px; font-size: 12px; font-weight: bold; background-color: rgba(1,40,92,0); color: #ffffff;'>
                <div style='width: 60px; float: left; margin:10px 0 0 0;'>
                <img src='images/administrador/{$this->user->per_id}.png' alt='{$this->user->per_desc}' title='{$this->user->per_desc}' width='55' height='55' />
                </div>
                <div style='float: left; padding-left: 0px; margin-top: 7px;width: 145px;'>
                <table width='260' border='0' cellpadding='0' cellspacing='0'>
                        <tr>
                                <td width='35'>
                                <div align='left'>Nombre</div>
                                </td>
                                <td width='4'>:</td>
                                <td width='200'>
                                <div align='left'><strong>{$this->user->us_nom}</strong></div>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                <div align='left'>Usuario</div>
                                </td>
                                <td>:</td>
                                <td>
                                <div align='left'><strong>{$this->user->us_id}</strong></div>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                <div align='left'>Perfil</div>
                                </td>
                                <td>:</td>
                                <td>
                                <div align='left'>{$this->user->per_desc}</div>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                <div align='left'>Empresa</div>
                                </td>
                                <td>:</td>
                                <td>
                                <div align='left'><strong>{$this->user->sed_clinic}</strong></div>
                                </td>
                        </tr>
                </table>
                </div>
                </div>
</div>
       ";

        return $html;
    }

    public function sys_login() {
        $this->sys_logout();
        $response = array();
        if (isset($_POST['user']) && isset($_POST['pass'])) {
            $logeo = "SELECT * FROM sys_usuario where usu_id=:usid && usu_pass=md5(:uspass)";
            $usuarios = $this->sql($logeo, array(':usid' => $_POST['user'], ":uspass" => $_POST['pass']));
            if ($usuarios->success && $usuarios->total == 1) {
                $perfil = "select con_usuid,concat(usu_nombres,' ', usu_appat) as nombres
                            ,per_desc,per_id,sed_clinic,sed_nombre, con_sedid, con_perid
                            , sed_empid, sed_direccion,emp_rs 
                            from sys_control 
                            inner join sys_perfil on con_perid=per_id 
                            inner join sys_sede on con_sedid=sed_id && sed_st=1 
                            inner join sys_empresa on sed_empid=emp_id 
                            inner join sys_usuario on con_usuid = usu_id && emp_st=1 
                            where 
                            con_st=1 and con_usuid=:user;";
                $perfiles = $this->sql($perfil, array(":user" => $usuarios->data[0]->usu_id));
                if ($perfiles->total == 0)
                    $response = array('success' => false, "error" => "El usuario esta desactivado. Comuníquese con el Administrador");
                else {
                    foreach ($perfiles->data as $i => &$r) {
                        $r->key = base64_encode(sprintf("%s-%d-%d-%d", $r->con_usuid, $r->con_sedid, $r->con_perid, $r->sed_empid));
                        $r->logo = sprintf("images/%s/empresa.svg", $r->sed_empid);
                    }
                    if ($perfiles->total == 1) {
                        $perfiles->data[0]->logo = sprintf("images/%s/header.png", $r->sed_empid);
                        $response = array('success' => true, "total" => $perfiles->total, "data" => $perfiles->data,
                            "error" => sprintf("Bienvenido %s %s", $usuarios->data[0]->usu_nombres, $usuarios->data[0]->usu_appat));
                        $user = $perfiles->data[0]->con_usuid;
                        $verifica = $this->sql("SELECT access_st, access_emp, access_perfil FROM acceso_modulo where access_usu='$user';");
                        if ($verifica->total > 0) {
                            $acceso = $verifica->data[0]->access_st;
                            if ($verifica->data[0]->access_st == "1") {
                                $empresas = '';
                            } else {
                                $string = "";
                                $total = $verifica->total;
                                for ($i = 0, $size = $total; $i < $size; ++$i) {
                                    if ($size - 1 == $i) {
                                        $coma = '';
                                    } else {
                                        $coma = ', ';
                                    }
                                    $string = $string . $verifica->data[$i]->access_emp . $coma;
                                }
                                $empresas.=$string;
                            }
                        }
                        $p = array();
                        $p['usu_id'] = $perfiles->data[0]->con_usuid;
                        $p['usu_nom'] = $perfiles->data[0]->nombres;
                        $p['sed_id'] = $perfiles->data[0]->con_sedid;
                        $p['per_id'] = $perfiles->data[0]->con_perid;
                        $p['emp_id'] = $perfiles->data[0]->sed_empid;
                        $p['emp_rs'] = $perfiles->data[0]->emp_rs;
                        $p['per_desc'] = $perfiles->data[0]->per_desc; //per_id
                        $p['per_id'] = $perfiles->data[0]->per_id; //per_id
                        $p['acceso'] = $acceso;
                        $p['empresas'] = $empresas;
                        $p['sed_direccion'] = $perfiles->data[0]->sed_direccion;
                        $p['sed_clinic'] = $perfiles->data[0]->sed_clinic;
                        $p['sed_nombre'] = $perfiles->data[0]->sed_nombre;
                        $_SESSION[__sesname]['activo'] = mktime();
                        $_SESSION[__sesname] = $p;
                    } elseif ($perfiles->total > 1) {
                        $response = array('success' => true, 'msg' => sprintf("Bienvenido %s %s", $usuarios->data[0]->usu_nombres, $usuarios->data[0]->usu_appat), "total" => $perfiles->total, "data" => $perfiles->data);
                    }
                }
            } elseif ($usuarios->success) {
                $response = array('success' => false, "error" => "El Usuario No Existe");
            } else {
                $response = array('success' => false, "error" => $usuarios->error);
            }
        } else {
            $response = array('success' => false, "error" => "No existe Variables");
        }

        return $response;
    }

    public function getLogo() {
        return sprintf("images/%s/header.png", $_SESSION[__sesname]['sed_id']);
    }

}

if (isset($_REQUEST['sys_acction']) && !empty($_REQUEST['sys_acction'])) {
    $_c = new loader();
    if (method_exists($_c, $_REQUEST['sys_acction'])) {
        $_r = new ReflectionMethod($_c, $_REQUEST['sys_acction']);
        if (!$_r->isPublic()) {
            throw new Exception(sprintf("El m&eacute;todo %s() no es p&uacute;blico", $_REQUEST['sys_acction']));
        } else {
            $_r = $_c->{$_REQUEST['sys_acction']}();
            if (isset($_REQUEST['format']) && !empty($_REQUEST['format'])) {
                require 'format.php';
                $_f = new format($_r);
                try {
                    $_f->{$_REQUEST['format']}();
                } catch (Exception $exc) {
                    echo $exc->getMessage();
                }
            } else {
                if (is_object($_r) || is_array($_r)) {
                    header('content-type: application/json');
                    echo json_encode($_r);
                } else {
                    header("Content-Type: text/plain");
                    echo $_r;
                }
            }
        }
    } else
        throw new Exception(sprintf("El m&eacute;todo %s() no existe", $_REQUEST['sys_acction']));
}
else {
    throw new Exception("Este documento no se puede Visualizar");
}
