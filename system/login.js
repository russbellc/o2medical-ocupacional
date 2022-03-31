
Ext.ns("QoDesk.login");
QoDesk.login = {
    win: null,
    frm: null,
    txt_user: null,
    txt_pass: null,
    init: function () {
        this.crea_controles();
        this.win.show();
    },
    crea_controles: function () {
        this.txt_user = new Ext.form.TextField({
            fieldLabel: 'Usuarios',
            allowBlank: false,
            emptyText: 'Usuario...',
            cls: 'txt-user',
            name: 'user'
        });
        this.txt_pass = new Ext.form.TextField({
            fieldLabel: 'Password',
            allowBlank: false,
            blankText: 'Clave no puede ser Vacio',
            name: 'pass',
            inputType: 'password',
            cls: 'txt-password',
            emptyText: 'Clave...'
        });
        this.frm = new Ext.form.FormPanel({
            region: 'center',
            waitMsgTarget: true,
            bodyStyle: "padding-top: 30px; padding-left:15px;",
            monitorValid: true,
            border: false,
            labelWidth: 70,
            items: [this.txt_user, this.txt_pass],
            buttons: [{
                    text: 'Ingresar',
                    iconCls: 'login_btn',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        this.frm.getForm().submit({
                            waitMsg: 'Validando',
                            url: 'code/sys_login/json',
                            success: function (form, action) { // si salio satisfactorio el logeo
                                var data = Ext.util.JSON.decode(action.response.responseText);
                                if (data.success == true) {
                                    if (data.total > 1) {
                                        Ext.Msg.alert('Bienvenido', action.result.msg, function () {
                                            QoDesk.login.choice.init(data);
                                        });
                                    } else {
//                                        Ext.Msg.alert('Bienvenido',action.result.msg, function() {
                                        location.reload();
//                                        });
                                    }
                                } else {
                                    Ext.Msg.show({// Muestra mensaje de error
                                        title: 'Error',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR,
                                        msg: data.msg
                                    });
                                }
                            },
                            failure: function (form, action) { //si fallo el logeo
                                switch (action.failureType) {
                                    case Ext.form.Action.CLIENT_INVALID:
                                        Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                                        break;
                                    case Ext.form.Action.CONNECT_FAILURE:
                                        Ext.Msg.alert('Failure', 'Ajax communication failed');
                                        break;
                                    case Ext.form.Action.SERVER_INVALID:
                                        Ext.Msg.alert('Advertencia', action.result.error + ' </br>Revice su usuario y contraseña.');
                                        break;
                                    default:
                                        Ext.Msg.alert('Failure', action.result.error);
                                }
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 400,
            height: 180,
            title: 'Acceso de Usuario Autorizado',
            layout: 'border',
            maximizable: false,
            draggable: true,
            closable: false,
            resizable: false,
            constrain: true,
            monitorValid: true,
            items: [{
                    region: 'west',
                    width: 100,
                    height: 112,
                    html: '<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false"><img alt="" src="images/s.gif" class="logo position" /></div>',
                    border: false
                }, this.frm]
        });
    }
};
QoDesk.login.choice = {
    data: null,
    cbo: null,
    win: null,
    dv_choice: null,
    txt_user: null,
    st_dg: null,
    init: function (data) {
        this.data = data;
        this.crea_stores();
        this.crea_controles();
        this.win.show();
        this.st_dg.loadData(data);
    },
    crea_controles: function (data) {
        var tplProduct = new Ext.XTemplate(
                '<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false">',
                '</br><center><tpl for=".">',
                '<div class="thumb-wrap" id="{key}">',
                '<div class="thumb"><img src="{logo}" title="{emp_rs}" width="200" style="padding: 15px 5px 15px 5px"></div>',
                '<span class="x-editable">{emp_rs}</span></div>',
                '</tpl></center>',
                '<div class="x-clear"></div>',
                '</div>'
                );
        this.dv_choice = new Ext.DataView({
            autoScroll: true,
            id: 'images-view',
            store: this.st_dg,
            tpl: tplProduct,
            autoHeight: false,
            height: 200,
            multiSelect: false,
            overClass: 'x-view-over',
            itemSelector: 'div.thumb-wrap',
            emptyText: 'No hay Sedes para mostrar',
            listeners: {
                click: function (dataview, index, item, e) {
                    var r = dataview.store.getAt(index);
                    console.log(r.get('key'));
                    Ext.Ajax.request({
                        url: 'code/sys_login2/json',
                        params: {
                            acction: 'sys_login2',
                            key: r.get('key')
                        },
                        success: function (form, action) {
                            location.reload();
                        }, failure: function (form, action) { //si fallo 
                            switch (action.failureType) {
                                case Ext.form.Action.CLIENT_INVALID:
                                    Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                                    break;
                                case Ext.form.Action.CONNECT_FAILURE:
                                    Ext.Msg.alert('Failure', 'Ajax communication failed');
                                    break;
                                case Ext.form.Action.SERVER_INVALID:
                                    Ext.Msg.alert('Failure', action.result.msg);
                            }
//                            if (action.failureType == 'server') {
//                                var data = Ext.JSON.decode(action.response.responseText);
//                                Ext.Msg.show({// Muestra mensaje de error
//                                    title: 'Error',
//                                    buttons: Ext.MessageBox.OK,
//                                    icon: Ext.MessageBox.ERROR,
//                                    msg: data.msg
//                                });
//                            }
                        }
                    });
                }

            }
        });
        this.cbo = new Ext.form.ComboBox({
            hiddenName: 'cbo-documento',
            id: 'documento',
            valueField: 'cod_tipo',
            displayField: 'emp_rs',
            fieldLabel: 'Tipo Documento',
            emptyText: 'Seleccione el Tipo de Documento...',
            triggerAction: 'all',
            mode: 'local',
            allowBlank: false,
            width: 220,
            store: this.st_dg
        });
        this.win = new Ext.Window({
            //            layout:'fit',
            width: 500,
            height: 200,
            closable: false,
            resizable: false,
            plain: true,
            border: false,
            items: this.dv_choice
        });
    },
    crea_stores: function () {
        this.st_dg = new Ext.data.Store({
            remoteSort: false,
            reader: new Ext.data.JsonReader({
                root: 'data',
                fields: [{
                        name: 'con_usuid'
                    }, {
                        name: 'con_sedid'
                    }, {
                        name: 'con_perid'
                    }, {
                        name: 'sed_empid'
                    }, {
                        name: 'sed_direccion'
                    }, {
                        name: 'emp_rs'
                    }, {
                        name: 'key'
                    }, {
                        name: 'logo'
                    }]
            })
        });

    }
};
Ext.onReady(QoDesk.login.init, QoDesk.login);