Ext.apply(Ext.form.VTypes, {
    daterange: function (val, field) {
        var date = field.parseDate(val);

        if (!date) {
            return false;
        }
        if (field.startDateField) {
            var start = Ext.getCmp(field.startDateField);
            if (!start.maxValue || (date.getTime() != start.maxValue.getTime())) {
                start.setMaxValue(date);
                start.validate();
            }
        }
        else if (field.endDateField) {
            var end = Ext.getCmp(field.endDateField);
            if (!end.minValue || (date.getTime() != end.minValue.getTime())) {
                end.setMinValue(date);
                end.validate();
            }
        }
        /*
         * Always return true since we're only using this vtype to set the
         * min/max allowed values (these are tested for after the vtype test)
         */
        return true;
    }
});
Ext.ns('mod.espiro');
mod.espiro = {
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
                acction: 'list_espiro',
                format: 'json'
            },
            listeners: {
                'beforeload': function () {
                    this.baseParams.columna = mod.espiro.descripcion.getValue();
                }
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['adm', 'pdf', 'st_id', 'tfi_desc', 'emp_desc', 'pac_ndoc', 'nombre', 'pac_sexo', 'fecha', 'usu', 'esp_id', 'tri_talla', 'tri_peso', 'tri_img']
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
        this.buscadores = new Ext.ux.form.SearchField({
            width: 250,
            fieldLabel: 'Nombre',
            id: 'search_query',
            emptyText: 'Ingrese dato a buscar...',
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
            items: ['Buscar:',
                this.descripcion,
                this.buscadores, '->',
                {
                    text: '<b>Cuestionario</b>',
                    iconCls: 'reporte',
                    handler: function () {
//                        alert("Ok");
                        mod.espiro.report.init(null);
                        //mod.hruta.nuevo.init(null);
                    }
                }, '|', {
                    text: 'Reporte x Fecha',
                    iconCls: 'reporte',
                    handler: function () {
                        mod.espiro.rfecha.init(null);
                    }
                }
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
                    mod.espiro.nuevo.init(record);
                    mod.espiro.nuevo.llena_mmg(record.get('adm'));
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    if (record.get('st_id') >= 1) {
                        if (record.get('pdf') != "1") {
                            new Ext.menu.Menu({
                                items: [{
                                        text: '<b>Subir Archivo</b>',
                                        iconCls: 'upload',
                                        handler: function () {
                                            mod.espiro.upload.init(record);
                                        }
                                    }
                                ]
                            }).showAt(event.xy);
                        } else {
                            new Ext.menu.Menu({
                                items: [{
                                        text: 'Reporte N°: <B>' + record.get('adm') + '<B>',
                                        iconCls: 'reporte',
                                        handler: function () {
                                            mod.espiro.munici.init(record);
                                        }
                                    }
                                ]
                            }).showAt(event.xy);
                        }
                    } else {

                    }
                }
            },
            autoExpandColumn: 'aud_emp',
//            items:[this.buscador],
            columns: [{
                    header: 'ST',
                    width: 25,
                    sortable: true,
                    dataIndex: 'st_id',
                    renderer: function renderIcon(val) {
                        if (val == 0) {
                            return  '<img src="<[images]>/nuevo.png" title="REGISTRAR" height="15">';
                        } else if (val > 0) {
                            return  '<img src="<[images]>/saveIcon.png" title="GUARDADO" height="15">';
                        }
                    }
                },
                {
                    header: 'N° FICHA',
                    width: 60,
                    sortable: true,
                    dataIndex: 'adm'
                },
                {
                    header: 'NOMBRE',
                    width: 230,
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
                    dataIndex: 'emp_desc',
                    width: 250
                },
                {
                    id: 'tfi_desc',
                    header: 'TIPO DE FICHA',
                    dataIndex: 'tfi_desc',
                    width: 110
                },
                {
                    id: 'tri_usu',
                    header: 'USUARIO',
                    dataIndex: 'usu',
                    width: 70
                },
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
                    dataIndex: 'fecha',
                    width: 130
                }
            ],
            viewConfig: {
                getRowClass: function (record, index) {
                    var st = record.get('st_id');
                    if (st == '0') {
                        return  'child-row';
                    } else if (st == '1') {
                        return  'child-blue';
                    } else if (st == '2') {
                        return  'adult-row';
                    }
                }
            }

        });
    }
},
mod.espiro.upload = {
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
                }
                , {
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
                                mod.espiro.upload.win.close();
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
                                url: 'system/espiro-upload.php',
                                success: function (fp, o) {
                                    Ext.Ajax.request({
                                        waitMsg: 'Recuperando Informacion...',
                                        waitTitle: 'Espere',
                                        url: '<[controller]>',
                                        params: {
                                            acction: 'load_pdf',
                                            format: 'json',
                                            adm_id: mod.espiro.upload.record.get('adm')
                                        },
                                        success: function (response, opts) {
                                            Ext.MessageBox.alert('LiveCode', o.result.file);
                                            mod.espiro.st.reload();
                                            mod.espiro.upload.win.close();
                                        }, failure: function () {
                                            mod.espiro.st.reload();
                                            mod.espiro.upload.win.close();
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
};
mod.espiro.munici = {
    win: null,
    record: null,
    init: function (r) {
        var params = "adm=" + r.get('adm');
        this.record = r;
        this.win = new Ext.Window({
            title: 'Espirometria :' + this.record.get('nombre'),
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
            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_espiro&sys_report=municipalidad&" + params + "'></iframe>"
        });
        this.win.show();
    }
};
mod.espiro.rfecha = {
    win: null,
    frm: null,
    f_inicio: null,
    f_final: null,
    st_empre: null,
    resultTpl: null,
    cboEmpre: null,
    init: function () {
        this.crea_store();
        this.crea_controles();
        this.win.show();
    },
    crea_store: function () {
        this.st_empre = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_empre',
                format: 'json'
            },
            fields: ['emp_id', 'emp_desc', 'emp_acro'],
            root: 'data'
        });
    },
    crea_controles: function () {

        this.resultTpl = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '{emp_id}',
                '<h3><span>{emp_acro}</span><br />{emp_desc}</h3>',
                '</div>',
                '</div></tpl>'
                );
        this.cboEmpre = new Ext.form.ComboBox({
            store: this.st_empre,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.resultTpl,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 3,
            hiddenName: 'cboEmpre',
            displayField: 'emp_desc',
            valueField: 'emp_id',
//            allowBlank: false,
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: 'Empresa',
            mode: 'remote',
            anchor: '100%'
        });

        this.f_inicio = new Ext.form.DateField({
            fieldLabel: 'Fecha Inicio',
            format: 'Y-m-d',
            id: 'startdt',
            vtype: 'daterange',
            endDateField: 'enddt',
            value: new Date(),
            name: 'f_inicio',
            allowBlank: false
        });
        this.f_final = new Ext.form.DateField({
            fieldLabel: 'Fecha Final',
            format: 'Y-m-d',
            id: 'enddt',
            vtype: 'daterange',
            startDateField: 'startdt',
            value: new Date(),
            name: 'f_final',
            allowBlank: false
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            frame: true,
            layout: 'column',
            bodyStyle: 'padding:10px 20px 10px 20px;',
            labelWidth: 80,
            items: [{
                    columnWidth: .50,
                    border: false,
                    layout: 'form',
                    items: [this.f_inicio]
                }, {
                    columnWidth: .50,
                    border: false,
                    layout: 'form',
                    items: [this.f_final]
                }, {html: '</br>'}, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.cboEmpre]
                }],
            buttons: [{
                    text: 'Reporte Excel',
                    iconCls: 'excel',
                    handler: function () {
                        mod.espiro.rfecha.win.el.mask('Guardando…', 'x-mask-loading');
                        var params = "fini=" + mod.espiro.rfecha.f_inicio.getRawValue() + "&ffinal=" + mod.espiro.rfecha.f_final.getRawValue() + "&empresa=" + mod.espiro.rfecha.cboEmpre.getValue();
                        new Ext.Window({
                            title: 'Referencia',
                            width: 100,
                            height: 100,
                            maximizable: true,
                            modal: true,
                            closeAction: 'close',
                            resizable: true,
                            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadexcel&sys_modname=mod_espiro&sys_report=reporte&" + params + "'></iframe>"

                        }).show();
                        mod.espiro.rfecha.win.el.unmask();
                        mod.espiro.rfecha.win.close();

                    }
                }, '-', {
                    text: 'Reporte PDF',
                    iconCls: 'reporte',
                    handler: function () {
                        mod.espiro.rfecha.win.el.mask('Guardando…', 'x-mask-loading');
                        var params = "fini=" + mod.espiro.rfecha.f_inicio.getRawValue() + "&ffinal=" + mod.espiro.rfecha.f_final.getRawValue() + "&empresa=" + mod.espiro.rfecha.cboEmpre.getValue();
                        new Ext.Window({
                            title: 'Referencia',
                            width: 800,
                            height: 600,
                            maximizable: true,
                            modal: true,
                            closeAction: 'close',
                            resizable: true,
                            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_espiro&sys_report=reportefecha&" + params + "'></iframe>"

                        }).show();
                        mod.espiro.rfecha.win.el.unmask();
                        mod.espiro.rfecha.win.close();

                    }
                }]
        });
        this.win = new Ext.Window({
            width: 500,
            height: 150,
            modal: true,
            title: 'Reporte por Fecha',
            border: false,
            collapsible: true,
            maximizable: true,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};
mod.espiro.reportbarra = {
    win: null,
    init: function () {
        this.crea_store();
        this.crea_controles();
        this.win.show();
    },
    crea_store: function () {

    },
    crea_controles: function (adm_id) {
//        var params = "adm=" + adm_id;

        this.win = new Ext.Window({
            title: 'barras',
            width: 800,
            height: 600,
            maximizable: true,
            modal: true,
            closeAction: 'close',
            resizable: true,
            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_espiro&sys_report=barras'></iframe>"
        });
    }
};
mod.espiro.report = {
    win: null,
    init: function () {
        this.crea_store();
        this.crea_controles();
        this.win.show();
    },
    crea_store: function () {

    },
    crea_controles: function (adm_id) {
//        var params = "adm=" + adm_id;

        this.win = new Ext.Window({
            title: 'Referencia',
            width: 800,
            height: 600,
            maximizable: true,
            modal: true,
            closeAction: 'close',
            resizable: true,
            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_espiro&sys_report=reporte'></iframe>"
        });
    }
};
mod.espiro.nuevo = {
    frm: null,
    win: null,
    peso: null,
    talla: null,
    imc: null,
    esp_fum: null,
    esp_vital: null,
    esp_recom: null,
    esp_cie10: null,
    esp_diag: null,
    cie10Tpl: null,
    st_cie10: null,
    st_fuma: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.st_fuma.reload();
        this.diag_espiro.load();
        this.st_busca_conclu_5.load();
        this.st_busca_recome_5.load();
        this.st_busca_conclu_4.load();
        this.st_busca_recome_4.load();
        this.st_busca_conclu_3.load();
        this.st_busca_recome_3.load();
        this.st_busca_conclu_2.load();
        this.st_busca_recome_2.load();
        this.st_busca_conclu_1.load();
        this.st_busca_recome_1.load();
        this.crea_controles();
        if (this.record !== null) {
            this.carga_data();
        }
        this.win.show();
    },
    llena_mmg: function (adm) {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_mmg_espiro',
                format: 'json',
                adm: adm
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
            }
        });
    },
    carga_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_espiro',
                format: 'json',
                adm: this.record.get('adm')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;

                this.esp_cie10.setValue(r.esp_cie10);
                this.esp_cie10.setRawValue(r.esp_cie1002);
                this.st_cie10.load();

                this.esp_fum.setValue(r.esp_fum);
                this.esp_fum.setRawValue(r.esp_fum02);
                this.st_fuma.load();
            }
        });
    },
    crea_stores: function () {

        this.st_busca_diag = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_diag',
                format: 'json'
            },
            fields: ['mmg_espiro_diag'],
            root: 'data'
        });


        this.st_busca_conclu_5 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_conclu_5',
                format: 'json'
            },
            fields: ['mmg_espiro_conclu_5'],
            root: 'data'
        });
        this.st_busca_recome_5 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_recome_5',
                format: 'json'
            },
            fields: ['mmg_espiro_recome_5'],
            root: 'data'
        });
        this.st_busca_conclu_4 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_conclu_4',
                format: 'json'
            },
            fields: ['mmg_espiro_conclu_4'],
            root: 'data'
        });
        this.st_busca_recome_4 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_recome_4',
                format: 'json'
            },
            fields: ['mmg_espiro_recome_4'],
            root: 'data'
        });
        this.st_busca_conclu_3 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_conclu_3',
                format: 'json'
            },
            fields: ['mmg_espiro_conclu_3'],
            root: 'data'
        });
        this.st_busca_recome_3 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_recome_3',
                format: 'json'
            },
            fields: ['mmg_espiro_recome_3'],
            root: 'data'
        });
        this.st_busca_conclu_2 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_conclu_2',
                format: 'json'
            },
            fields: ['mmg_espiro_conclu_2'],
            root: 'data'
        });
        this.st_busca_recome_2 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_recome_2',
                format: 'json'
            },
            fields: ['mmg_espiro_recome_2'],
            root: 'data'
        });
        this.st_busca_conclu_1 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_conclu_1',
                format: 'json'
            },
            fields: ['mmg_espiro_conclu_1'],
            root: 'data'
        });
        this.st_busca_recome_1 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_recome_1',
                format: 'json'
            },
            fields: ['mmg_espiro_recome_1'],
            root: 'data'
        });
        this.diag_espiro = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'diag_espiro',
                format: 'json'
            },
            fields: ['esp_diag'],
            root: 'data'
        });
        this.st_cie10 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_cie10',
                format: 'json'
            },
            fields: ['cie4_id', 'cie4_cie3id', 'cie4_desc'],
            root: 'data'
        });
        this.st_fuma = new Ext.data.JsonStore({
            remoteSort: true,
            url: '<[controller]>',
            baseParams: {
                acction: 'list_fuma',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['con_ids', 'cod_desc']
        });
    },
    crea_controles: function () {
        //tri_talla, tri_peso, tri_img
        this.peso = new Ext.form.NumberField({
            fieldLabel: 'Peso',
            readOnly: true,
            value: this.record.get('tri_talla'),
            anchor: '95%'
        });
        this.talla = new Ext.form.NumberField({
            fieldLabel: 'Talla',
            readOnly: true,
            value: this.record.get('tri_peso'),
            anchor: '95%'
        });
        this.imc = new Ext.form.NumberField({
            fieldLabel: 'IMC',
            value: this.record.get('tri_img'),
            readOnly: true,
            anchor: '95%'
        });
//esp_fum:null,
        this.esp_fum = new Ext.form.ComboBox({
            typeAhead: true,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'local',
            store: this.st_fuma,
            //editable: false,
            forceSelection: true,
            hiddenName: 'esp_fum',
            fieldLabel: 'Fumador',
            name: 'esp_fum',
            valueField: 'con_ids',
            displayField: 'cod_desc',
            anchor: '90%',
            listeners: {
                afterrender: function (combo) {
                    combo.setValue(47);// El ID de la opción por defecto setRawValue
                    combo.setRawValue('No');// El ID de la opción por defecto setRawValue
                }
            }
        });
//esp_vital:null,
        this.esp_vital = new Ext.form.TextField({
            fieldLabel: 'Capacidad Vital',
            name: 'esp_vital',
            anchor: '95%',
            style: {
                //textTransform: "uppercase"
            }
        });
//esp_recom:null,
        this.esp_recom = new Ext.form.TextArea({
            fieldLabel: 'Recomendaciones',
            name: 'esp_recom',
            style: {
                //textTransform: "uppercase"
            },
            id: 'esp_recom',
            anchor: '95%',
            height: 50
        });
//esp_cie10:null,
//          'cie4_id', 'cie4_cie3id', 'cie4_desc'
        this.cie10Tpl = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '{cie4_id}',
                '<h3><span><p>{cie4_desc}</p></span></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.esp_cie10 = new Ext.form.ComboBox({
            store: this.st_cie10,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.cie10Tpl,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 3,
//          'cie4_id', 'cie4_cie3id', 'cie4_desc'
            hiddenName: 'esp_cie10',
            displayField: 'cie4_desc',
            valueField: 'cie4_id',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: 'Cie 10',
            mode: 'remote',
            style: {
                //textTransform: "uppercase"
            },
            anchor: '100%'
        });
//esp_diag:null,
        this.EmplTpl = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '{esp_diag}',
                '</div>',
                '</div></tpl>'
                );
        this.esp_diag = new Ext.form.ComboBox({
            store: this.diag_espiro,
            loadingText: 'Searching...',
            pageSize: 10,
            hideTrigger: true,
            tpl: this.EmplTpl,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            hiddenName: 'esp_diag',
            displayField: 'esp_diag',
            valueField: 'esp_diag',
            minChars: 1,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            fieldLabel: '<b>Diagnostico</b>',
            typeAhead: false,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });

        this.mmg_espiro_FVC = new Ext.form.TextField({
            fieldLabel: 'FVC 1st',
            name: 'mmg_espiro_FVC',
            anchor: '95%'
        });

        this.mmg_espiro_FEV1 = new Ext.form.TextField({
            fieldLabel: 'FEV1 1st',
            name: 'mmg_espiro_FEV1',
            anchor: '95%'
        });

        this.mmg_espiro_FEV1_FVC = new Ext.form.TextField({
            fieldLabel: 'FEV1/FVC 1st',
            name: 'mmg_espiro_FEV1_FVC',
            anchor: '95%'
        });

        this.mmg_espiro_PEF = new Ext.form.TextField({
            fieldLabel: 'PEF 1st',
            name: 'mmg_espiro_PEF',
            anchor: '95%'
        });

        this.mmg_espiro_FEF2575 = new Ext.form.TextField({
            fieldLabel: 'FEF2575 1st',
            name: 'mmg_espiro_FEF2575',
            anchor: '95%'
        });


        this.cie10Tpl = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><b>{mmg_espiro_diag}</b></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.mmg_espiro_diag_1 = new Ext.form.ComboBox({
            store: this.st_busca_diag,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.cie10Tpl,
//            disabled: true,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 2,
            hiddenName: 'mmg_espiro_diag_1',
            displayField: 'mmg_espiro_diag',
            valueField: 'mmg_espiro_diag',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>Cie 10</b>',
            mode: 'remote',
            anchor: '95%'
        });

        this.mmg_espiro_conclu_1 = new Ext.form.ComboBox({
            store: this.st_busca_conclu_1,
            hiddenName: 'mmg_espiro_conclu_1',
            displayField: 'mmg_espiro_conclu_1',
            valueField: 'mmg_espiro_conclu_1',
            minChars: 2,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>CONCLUSIÓN</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });

        this.mmg_espiro_recome_1 = new Ext.form.ComboBox({
            store: this.st_busca_recome_1,
            hiddenName: 'mmg_espiro_recome_1',
            displayField: 'mmg_espiro_recome_1',
            valueField: 'mmg_espiro_recome_1',
            minChars: 2,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>RECOMENDACIÓN</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });


        this.mmg_espiro_diag_2 = new Ext.form.ComboBox({
            store: this.st_busca_diag,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.cie10Tpl,
//            disabled: true,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 2,
            hiddenName: 'mmg_espiro_diag_2',
            displayField: 'mmg_espiro_diag',
            valueField: 'mmg_espiro_diag',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>Cie 10</b>',
            mode: 'remote',
            anchor: '95%'
        });

        this.mmg_espiro_conclu_2 = new Ext.form.ComboBox({
            store: this.st_busca_conclu_2,
            hiddenName: 'mmg_espiro_conclu_2',
            displayField: 'mmg_espiro_conclu_2',
            valueField: 'mmg_espiro_conclu_2',
            minChars: 2,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>CONCLUSIÓN</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });

        this.mmg_espiro_recome_2 = new Ext.form.ComboBox({
            store: this.st_busca_recome_2,
            hiddenName: 'mmg_espiro_recome_2',
            displayField: 'mmg_espiro_recome_2',
            valueField: 'mmg_espiro_recome_2',
            minChars: 2,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>RECOMENDACIÓN</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });

        this.mmg_espiro_diag_3 = new Ext.form.ComboBox({
            store: this.st_busca_diag,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.cie10Tpl,
//            disabled: true,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 2,
            hiddenName: 'mmg_espiro_diag_3',
            displayField: 'mmg_espiro_diag',
            valueField: 'mmg_espiro_diag',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>Cie 10</b>',
            mode: 'remote',
            anchor: '95%'
        });

        this.mmg_espiro_conclu_3 = new Ext.form.ComboBox({
            store: this.st_busca_conclu_3,
            hiddenName: 'mmg_espiro_conclu_3',
            displayField: 'mmg_espiro_conclu_3',
            valueField: 'mmg_espiro_conclu_3',
            minChars: 2,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>CONCLUSIÓN</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });

        this.mmg_espiro_recome_3 = new Ext.form.ComboBox({
            store: this.st_busca_recome_3,
            hiddenName: 'mmg_espiro_recome_3',
            displayField: 'mmg_espiro_recome_3',
            valueField: 'mmg_espiro_recome_3',
            minChars: 2,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>RECOMENDACIÓN</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });

        this.mmg_espiro_diag_4 = new Ext.form.ComboBox({
            store: this.st_busca_diag,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.cie10Tpl,
//            disabled: true,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 2,
            hiddenName: 'mmg_espiro_diag_4',
            displayField: 'mmg_espiro_diag',
            valueField: 'mmg_espiro_diag',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>Cie 10</b>',
            mode: 'remote',
            anchor: '95%'
        });

        this.mmg_espiro_conclu_4 = new Ext.form.ComboBox({
            store: this.st_busca_conclu_4,
            hiddenName: 'mmg_espiro_conclu_4',
            displayField: 'mmg_espiro_conclu_4',
            valueField: 'mmg_espiro_conclu_4',
            minChars: 2,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>CONCLUSIÓN</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });

        this.mmg_espiro_recome_4 = new Ext.form.ComboBox({
            store: this.st_busca_recome_4,
            hiddenName: 'mmg_espiro_recome_4',
            displayField: 'mmg_espiro_recome_4',
            valueField: 'mmg_espiro_recome_4',
            minChars: 2,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>RECOMENDACIÓN</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });

        this.mmg_espiro_diag_5 = new Ext.form.ComboBox({
            store: this.st_busca_diag,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.cie10Tpl,
//            disabled: true,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 2,
            hiddenName: 'mmg_espiro_diag_5',
            displayField: 'mmg_espiro_diag',
            valueField: 'mmg_espiro_diag',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>Cie 10</b>',
            mode: 'remote',
            anchor: '95%'
        });

        this.mmg_espiro_conclu_5 = new Ext.form.ComboBox({
            store: this.st_busca_conclu_5,
            hiddenName: 'mmg_espiro_conclu_5',
            displayField: 'mmg_espiro_conclu_5',
            valueField: 'mmg_espiro_conclu_5',
            minChars: 2,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>CONCLUSIÓN</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });

        this.mmg_espiro_recome_5 = new Ext.form.ComboBox({
            store: this.st_busca_recome_5,
            hiddenName: 'mmg_espiro_recome_5',
            displayField: 'mmg_espiro_recome_5',
            valueField: 'mmg_espiro_recome_5',
            minChars: 2,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>RECOMENDACIÓN</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '95%'
        });

        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
//            frame: true,
            layout: 'accordion',
            layoutConfig: {
                titleCollapse: true,
                animate: true,
                hideCollapseTool: true
            },
            items: [{
                    title: 'INFORMACÍON DE ESPIROMETRIA',
                    iconCls: 'demo',
                    bodyStyle: 'padding:10px 0 3px 0;',
//                    labelWidth: 75,
                    columnWidth: .5,
                    layout: 'form',
//                    layout: 'column',
                    items: [{
                            xtype: 'fieldset',
                            columnWidth: 1,
                            title: 'Datos Triaje',
                            layout: 'column',
//                            labelAlign: 'top',
                            iconCls: 'homes',
                            autoHeight: true,
                            labelWidth: 55,
                            defaultType: 'textfield',
                            bodyStyle: 'padding:2px;',
                            items: [
                                new Ext.Panel({
                                    border: false,
                                    columnWidth: .35,
                                    layout: 'form',
                                    items: [this.talla, ]
                                }), new Ext.Panel({
                                    border: false,
                                    columnWidth: .33,
                                    layout: 'form',
                                    items: [this.peso, ]
                                }), new Ext.Panel({
                                    border: false,
                                    columnWidth: .32,
                                    layout: 'form',
                                    items: [this.imc, ]
                                })
                            ]
                        }, {
                            xtype: 'fieldset',
                            columnWidth: 1,
                            layout: 'column',
                            iconCls: 'homes',
                            autoHeight: true,
                            defaultType: 'textfield',
                            bodyStyle: 'padding:0px 5px 0 5px;',
                            items: [
                                new Ext.Panel({
                                    border: false,
                                    columnWidth: .50,
                                    layout: 'form',
                                    items: [this.esp_fum, ]
                                }), new Ext.Panel({
                                    border: false,
                                    columnWidth: .50,
                                    layout: 'form',
                                    items: [this.esp_vital, ]
                                }), new Ext.Panel({
                                    border: false,
                                    columnWidth: .99,
                                    layout: 'form',
                                    labelAlign: 'top',
                                    items: [this.esp_recom, ]
                                }), new Ext.Panel({
                                    border: false,
                                    columnWidth: .99,
                                    layout: 'form',
                                    items: [this.esp_cie10, ]
                                }), new Ext.Panel({
                                    border: false,
                                    columnWidth: .99,
                                    layout: 'form',
                                    labelAlign: 'top',
                                    items: [this.esp_diag, ]
                                })
                            ]
                        }, {
                            xtype: 'fieldset',
                            columnWidth: 1,
                            title: 'Lectura Espirometrica MMG',
                            layout: 'column',
                            iconCls: 'homes',
                            autoHeight: true,
                            labelAlign: 'top',
                            defaultType: 'textfield',
                            bodyStyle: 'padding:5px 5px 0 5px;',
                            items: [
                                new Ext.Panel({
                                    border: false,
                                    columnWidth: .20,
                                    layout: 'form',
                                    items: [this.mmg_espiro_FVC, ]
                                }), new Ext.Panel({
                                    border: false,
                                    columnWidth: .20,
                                    layout: 'form',
                                    items: [this.mmg_espiro_FEV1, ]
                                }), new Ext.Panel({
                                    border: false,
                                    columnWidth: .20,
                                    layout: 'form',
                                    labelAlign: 'top',
                                    items: [this.mmg_espiro_FEV1_FVC, ]
                                }), new Ext.Panel({
                                    border: false,
                                    columnWidth: .20,
                                    layout: 'form',
                                    items: [this.mmg_espiro_PEF, ]
                                }), new Ext.Panel({
                                    border: false,
                                    columnWidth: .20,
                                    layout: 'form',
                                    items: [this.mmg_espiro_FEF2575, ]
                                })
                            ]
                        }
                    ]
                }, {
                    title: '<b>--->  DIAGNOSTICO ESPIROMETRICO MMG</b>',
                    iconCls: 'demo2',
                    layout: 'column',
                    border: false,
                    bodyStyle: 'padding:10px 10px 20px 10px;',
                    labelWidth: 60,
                    items: [{
                            xtype: 'panel', border: false,
                            columnWidth: .99,
                            bodyStyle: 'padding:2px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'DIAGNÓSTICOS:',
                                    items: [{
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_diag_1]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_conclu_1]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_recome_1]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_diag_2]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_conclu_2]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_recome_2]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_diag_3]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_conclu_3]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_recome_3]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_diag_4]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_conclu_4]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_recome_4]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_diag_5]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_conclu_5]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.mmg_espiro_recome_5]
                                        }]
                                }]
                        }]
                }

            ],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.espiro.nuevo.win.el.mask('Guardando…', 'x-mask-loading');
                        var metodo;
                        if (this.record.get('st_id') >= 1) {
                            metodo = 'update';
                        } else {
                            metodo = 'save';
                        }
                        this.frm.getForm().submit({
                            params: {
                                acction: metodo
                                , adm: this.record.get('adm')
                                , esp_id: this.record.get('esp_id')
                            },
                            success: function () {
                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
                                mod.espiro.nuevo.win.el.unmask();
                                mod.espiro.st.reload();
                                mod.espiro.nuevo.win.close();
                            },
                            failure: function (form, action) {
                                console.log(metodo);
                                mod.espiro.nuevo.win.el.unmask();
                                switch (action.failureType) {
                                    case Ext.form.Action.CLIENT_INVALID:
                                        Ext.Msg.alert('Failure', 'Existen valores Invalidos');
                                        break;
                                    case Ext.form.Action.CONNECT_FAILURE:
                                        Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
                                        break;
                                    case Ext.form.Action.SERVER_INVALID:
                                        Ext.Msg.alert('Failure mik', action.result.error);
                                        break;
                                    default:
                                        Ext.Msg.alert('Failure', action.result.error);
                                }
                                mod.espiro.st.reload();
                                mod.espiro.nuevo.win.close();
                            }
                        });
                    }
                }
            ]
        });
        this.win = new Ext.Window({
            width: 600,
            height: 500,
            modal: true,
            title: 'ESPIROMETRIAS' + '------NOMBRE : ' + this.record.get('nombre'),
            border: false,
            collapsible: true,
            maximizable: true,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
//            closeAction: 'hide',
//            plain: true,
            items: [this.frm]
        });
    }
};
Ext.onReady(mod.espiro.init, mod.espiro);