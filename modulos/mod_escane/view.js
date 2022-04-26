Ext.ns('mod.escaner');
mod.escaner = {
    dt_grid: null,
    paginador: null,
    tbar: null,
    st: null,
    init: function () {
        this.crea_store();
        this.crea_controles();
        this.dt_grid.render('<[view]>');
        this.st.load();
    },
    crea_store: function () {
        this.st = new Ext.data.JsonStore({
            remoteSort: true,
            url: '<[controller]>',
            baseParams: {
                acction: 'list_resumen',
                format: 'json'
            },
            listeners: {
                'beforeload': function () {
                    this.baseParams.columna = mod.escaner.descripcion.getValue();
                }
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['adm', 'st_id', 'pdf', 'tfi_desc', 'emp_acro', 'pac_ndoc', 'nombre', 'pac_sexo', 'FECHA', 'usu', 'val_aptitu', 'edad', 'reg_id']
        });
    },
    crea_controles: function () {
        this.paginador = new Ext.PagingToolbar({
            pageSize: 30,
            store: this.st,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2} Empleados',
            emptyMsg: 'No Existe Registros',
            plugins: new Ext.ux.ProgressBarPager()

        });
        this.buscador = new Ext.ux.form.SearchField({
            width: 250,
            fieldLabel: 'Nombre',
            id: 'search_query',
            emptyText: 'Ingrese Datos',
            store: this.st
        });
        this.descripcion = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [['1', 'Nro Filiacion'], ['2', 'DNI'], ["3", 'Apellidos y Nombres'], ["4", 'Empresa o RUC'], ["5", 'Tipo de Ficha']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue(1);
                    descripcion.setRawValue('Nro Filiacion');
                }
            }
        });
        this.tbar = new Ext.Toolbar({
            items: ['Buscar:', this.descripcion,
                this.buscador, '->'
            ]
        });
        this.dt_grid = new Ext.grid.GridPanel({
            store: this.st,
            tbar: this.tbar,
            loadMask: true,
            height: 500,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 95
            }),
            bbar: this.paginador,
            listeners: {
                rowdblclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record = grid.getStore().getAt(rowIndex);
                    if (record.get('pdf') != "1") {
                    } else {
//                        var rg = record.get('val_aptitu');
//                        if (rg == 'APTO' | rg == 'APTO CON RESTRICCIONES' | rg == 'APTO CON OBSERVACIONES' | rg == 'NO APTO' | rg == 'RETIRO') 
//                        {
                            
//                        }
                        if (record.get('val_aptitu') == 'Apto') {
                            mod.escaner.munici.init(record);
                        } else if (record.get('val_aptitu') == 'Apto Con Restricciones') {
                            mod.escaner.munici.init(record);
                        } else if (record.get('val_aptitu') == 'Apto Con Observacion') {
                            mod.escaner.munici.init(record);
                        } else if (record.get('val_aptitu') == 'No Apto') {
                            mod.escaner.munici.init(record);
                        } else if (record.get('val_aptitu') == 'Retiro') {
                            mod.escaner.munici.init(record);
                        } else {
                            mod.escaner.munici.init(record);
//                            Ext.MessageBox.alert('LIVECODE INC.', 'El paciente no fue VALIDADO');
                        }
                    }
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    var rg = record.get('val_aptitu');
//                    if (rg == 'APTO' | rg == 'APTO CON RESTRICCIONES' | rg == 'APTO CON OBSERVACIONES' | rg == 'NO APTO' | rg == 'RETIRO')
//                    {
                        if (record.get('pdf') != "1") {
                            new Ext.menu.Menu({
                                items: [{
                                        text: '<b>Subir Archivo</b>',
                                        iconCls: 'upload',
                                        handler: function () {
                                            mod.escaner.upload.init(record);
                                        }
                                    }
                                ]
                            }).showAt(event.xy);
                        } else {
                            new Ext.menu.Menu({
                                items: [{
                                        text: '<b>PDF</b>',
                                        iconCls: 'reporte',
                                        handler: function () {
                                            mod.escaner.municipa.init(record);
                                        }
                                    }
                                ]
                            }).showAt(event.xy);
                        }
//                    }
                }
            },
            autoExpandColumn: 'aud_emp',
            columns: [{
                    header: 'ST',
                    width: 25,
                    sortable: true,
                    dataIndex: 'pdf',
                    renderer: function renderIcon(val) {
                        if (val == 0 || val == 2) {
                            return  '<img src="<[images]>/not.png" title="No visto" height="15">';
                        } else if (val == 1) {
                            return  '<img src="<[images]>/view.png" title="Visto" height="15">';
                        }
                    }
                },
                {
                    header: 'N° FICHA',
                    width: 60,
                    sortable: true,
                    dataIndex: 'adm'
                }, {
                    header: 'DNI',
                    width: 60,
                    dataIndex: 'pac_ndoc'
                },
                {
                    header: 'NOMBRE',
                    width: 250,
                    dataIndex: 'nombre'
                },
                {
                    header: 'SEXO',
                    width: 40,
                    sortable: true,
                    dataIndex: 'pac_sexo',
                    renderer: function renderIcon(val) {
                        if (val == 'M') {
                            return  '<center><img src="<[images]>/male.png" title="Masculino" height="15"></center>';
                        } else if (val == 'F') {
                            return  '<center><img src="<[images]>/fema.png" title="Femenino" height="15"></center>';
                        }
                    }
                },
                {
                    id: 'aud_emp',
                    header: 'EMPRESA',
                    dataIndex: 'emp_acro'
//                    width: 250
                },
                {
                    id: 'tfi_desc',
                    header: 'TIPO DE FICHA',
                    dataIndex: 'tfi_desc',
                    align: 'center',
                    width: 130
                }, 
                {
					header: "APTITUD",
					dataIndex: "val_aptitu",
					align: "center",
					width: 151,
					renderer: function (val, meta, record) {
                        if (val == 'APTO') {
                            meta.css = 'stkGreen';
                        } else if (val == 'APTO CON OBSERVACIONES') {
                            meta.css = 'stkYellow';
                        } else if (val == 'APTO CON RESTRICCIÓN') {
                            meta.css = 'stkYellow';
                            return val;
                        } else if (val == 'NO APTO TEMPORAL') {
                            meta.css = 'stkRed';
                        } else if (val == 'NO APTO DEFINITIVO') {
                            meta.css = 'stkRed';
                        } else if (val == 'EN PROCESO DE VALIDACION') {
                            meta.css = 'stkBlak';
                        } else if (val == 'NO APTO TEMPORAL') {
                            meta.css = 'stkblue';
                        } else {
                            return  '<b><center><h3>N/R</h3></center></b>';
                        }
                        return '<b><center><h3>' + val + '</h3></center></b>';
                    }
				},
                // {
//                {
//                    id: 'tri_usu',
//                    header: 'USUARIO',
//                    align: 'center',
//                    dataIndex: 'mikail',
//                    width: 60
//                },
                {
                    header: '<center>PDF</center>',
                    dataIndex: 'pdf',
                    align: 'center',
                    width: 70,
                    renderer: function (val, meta, record) {
                        if (val == '1') {
                            meta.css = 'stkGreen';
                            return val = 'Cargado';
                        } else if (val == '0') {
                            meta.css = 'stkRed';
                            return val = 'No Cargado';
                        }
                    }
                }, {
                    header: 'FECHA DE ADMISIÓN',
                    dataIndex: 'FECHA',
                    width: 160
                }
            ],
            viewConfig: {
                getRowClass: function (record, index) {
                    var st = record.get('pdf');
                    if (st == '0') {
                        return  'child-row';
                    } else if (st == '2') {
                        return  'child-blue';
                    } else if (st == '1') {
                        return  'adult-row';
                    }
                }
            }

        });
    }
};
mod.escaner.upload = {
    frm: null,
    win: null,
    record: null,
    archivo: null,
    init: function (r) {
        this.record = r;
        this.crea_controles();
        this.win.show();
    },
    crea_controles: function () {
        this.frm = new Ext.FormPanel({
            region: 'center',
            monitorValid: true,
            fileUpload: true,
            width: 500,
            frame: true,
            autoHeight: true,
            bodyStyle: 'padding: 10px 10px 0 10px;',
            labelWidth: 50,
            defaults: {
                anchor: '95%',
                allowBlank: false,
                msgTarget: 'side'
            },
            items: [{
                    xtype: 'textfield',
                    fieldLabel: 'Nro H',
                    name: 'name',
                    readOnly: true,
                    value: this.record.get('adm')
                }, {
                    xtype: 'fileuploadfield',
                    id: 'form-file',
                    emptyText: 'Seleccione el archivo...',
                    fieldLabel: 'Adjuntar',
                    name: 'photo-path',
                    buttonText: '',
                    buttonCfg: {
                        iconCls: 'upload-icon'
                    },
                    listeners: {
                        'fileselected': function (fb, v) {
                            if (fb.value.match(/.(pdf)$/)) {
//                        alert("pdf");
                            }
                            else {
                                Ext.MessageBox.alert('Ojo', '<center>Archivo no Permitido... </br>este archivo tiene que ser PDF carge de nuevo.</center>');
                                mod.escaner.upload.win.close();
                            }
                        }
                    }
                }
            ],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        if (this.frm.getForm().isValid()) {
                            this.frm.getForm().submit({
                                waitMsg: 'Cargando archivo adjunto...',
                                url: 'system/escaner-upload.php',
                                success: function (fp, o) {
                                    Ext.Ajax.request({
                                        waitMsg: 'Recuperando Informacion...',
                                        waitTitle: 'Espere',
                                        url: '<[controller]>',
                                        params: {
                                            acction: 'load_pdf',
                                            format: 'json',
                                            adm_id: mod.escaner.upload.record.get('adm')
                                        },
                                        success: function (response, opts) {
                                            Ext.MessageBox.alert('LiveCode', o.result.file);
                                            mod.escaner.st.reload();
                                            mod.escaner.upload.win.close();
                                        }, failure: function () {
                                            mod.escaner.st.reload();
                                            mod.escaner.upload.win.close();
                                        }
                                    });
                                },
                                failure: function (fp, o) {
                                    Ext.MessageBox.alert('LiveCode Problemas', o.result.file);
                                    mod.espiro.st.reload();
                                    mod.espiro.upload.win.close();
                                }
                            });
                        }
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 600,
            height: 130,
            modal: true,
            title: 'Nombres: ' + this.record.get('nombre') + '  DNI: ' + this.record.get('pac_ndoc'),
            border: false,
            collapsible: false,
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
},
mod.escaner.munici = {
    win: null,
    record: null,
    init: function (r) {
        var params = "adm=" + r.get('adm');
        this.record = r;
        this.win = new Ext.Window({
            title: 'RESUMEN :' + this.record.get('nombre'),
            width: 700,
            height: 600,
            border: false,
            collapsible: false,
            draggable: true,
            closable: true,
            maximizable: true,
            modal: true,
            closeAction: 'close',
            resizable: true,
            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_escane&sys_report=municipalidad&" + params + "'></iframe>"
        });
        this.win.show();
    }
};
mod.escaner.municipa = {
    win: null,
    record: null,
    init: function (r) {
        var params = "adm=" + r.get('adm');
        this.record = r;
        this.win = new Ext.Window({
            title: 'RESUMEN :' + this.record.get('nombre'),
            width: 700,
            height: 600,
            border: false,
            collapsible: false,
            draggable: true,
            closable: true,
            maximizable: true,
            modal: true,
            closeAction: 'close',
            resizable: true,
            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_escane&sys_report=municipalidad&" + params + "'></iframe>"
        });
        this.win.show();
    }
};
mod.escaner.nuevo = {
    win: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        this.win.show();
    },
    crea_stores: function () {

    },
    crea_controles: function () {
        this.win = new Ext.Window({
            title: 'Apellidos y Nombres : ' + this.record.get('nombre'),
            width: 800,
            height: 600,
            maximizable: true,
            modal: true,
            closeAction: 'close',
            resizable: true,
            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_auditoria&sys_report=reporte&adm=" + this.record.get('adm') + "'></iframe>"
        });
    }
};
mod.escaner.reporte = {
    win: null,
    record: null,
    init: function (r) {
        var params = "adm=" + r.get('adm');
        this.record = r;
        this.win = new Ext.Window({
            title: 'RESUMEN :' + this.record.get('nombre'),
            width: 700,
            height: 600,
            border: false,
            collapsible: false, draggable: true,
            closable: true, maximizable: true,
            modal: true,
            closeAction: 'close',
            resizable: true,
            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_auditoria&sys_report=reporte&" + params + "'></iframe>"
        });
        this.win.show();
    }
};
Ext.onReady(mod.escaner.init, mod.escaner);