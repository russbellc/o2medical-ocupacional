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
        } else if (field.endDateField) {
            var end = Ext.getCmp(field.endDateField);
            if (!end.minValue || (date.getTime() != end.minValue.getTime())) {
                end.setMinValue(date);
                end.validate();
            }
        }
        return true;
    }
});

Ext.ns('mod.laboratorio');
mod.laboratorio = {
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
                acction: 'list_paciente',
                format: 'json'
            },
            listeners: {
                'beforeload': function () {
                    this.baseParams.columna = mod.laboratorio.descripcion.getValue();
                }
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['adm', 'adm_foto', 'st', 'tfi_desc', 'emp_desc', 'pac_ndoc', 'pac_ndoc', 'edad', 'puesto', 'tfi_desc', 'nombre', 'ape', 'nom', 'pac_sexo', 'fecha', 'nro_examenes']
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
            typeAhead: false, editable: false,
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
                this.buscador, '->',
                '|', {
                    text: 'Reporte x Fecha',
                    iconCls: 'reporte',
                    handler: function () {
                        mod.laboratorio.rfecha.init(null);
                    }
                }, '|'
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
                    var admi = record.get('adm');
                    if (record.get('st') >= 1) {
                        console.log(record);
                        mod.laboratorio.formatos.init(record);
                    } else {
                        console.log(admi);
                        mod.laboratorio.formatos.init(record);
                    }
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    if (record.get('st') == "1") {
                        new Ext.menu.Menu({
                            items: [{
                                    text: 'INFORME DE LABORATORIO N°: <B>' + record.get('adm') + '<B>',
                                    iconCls: 'reporte',
                                    handler: function () {
                                        if (record.get('st') >= 1) {
                                            new Ext.Window({
                                                title: 'INFORME DE LABORATORIO N° ' + record.get('adm'),
                                                width: 800,
                                                height: 600,
                                                maximizable: true,
                                                modal: true,
                                                closeAction: 'close',
                                                resizable: true,
                                                html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_laboratorio&sys_report=formato_laboratorio&adm=" + record.get('adm') + "'></iframe>"
                                            }).show();
                                        } else {
                                            Ext.MessageBox.alert('Observaciones', 'El paciente no fue registrado correctamente');
                                        }
                                    }
                                }]
                        }).showAt(event.xy);
                    }
                }
            },
            autoExpandColumn: 'aud_emp',
            columns: [{
                    header: 'ST',
                    width: 25,
                    sortable: true,
                    dataIndex: 'st',
                    renderer: function renderIcon(val) {
                        if (val == 0) {
                            return  '<img src="<[images]>/nuevo.png" title="REGISTRAR" height="15">';
                        } else if (val == 1) {
                            return  '<img src="<[images]>/saveIcon.png" title="GUARDADO" height="15">';
                        }
                    }
                }, {
                    header: 'N° FICHA',
                    width: 60,
                    sortable: true,
                    dataIndex: 'adm'
                }, {
                    header: 'Número DNI',
                    dataIndex: 'pac_ndoc',
                    width: 80
                }, {
                    header: 'NOMBRE',
                    width: 240,
                    dataIndex: 'nombre'
                }, {
                    header: 'Edad',
                    width: 35,
                    id: 'edad',
                    dataIndex: 'edad'
                }, {
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
                }, {
                    id: 'aud_emp',
                    header: 'EMPRESA',
                    dataIndex: 'emp_desc',
                    width: 250
                }, {
                    id: 'tfi_desc',
                    header: 'TIPO DE FICHA',
                    dataIndex: 'tfi_desc',
                    width: 125
                }, {
                    header: 'FECHA DE ADMISIÓN',
                    dataIndex: 'fecha',
                    width: 165
                }
            ],
            viewConfig: {
                getRowClass: function (record, index) {
                    var st = record.get('st');
                    if (st == '0') {
                        return  'child-row';
                    } else if (st == '1') {
                        return  'child-blue';
                    }
                }
            }

        });
    }
};
mod.laboratorio.formatos = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        this.st.load();
        this.win.show();
        if (this.record.get('adm_foto') == 1) {
            mod.laboratorio.formatos.imgStore.removeAll();
            var store = mod.laboratorio.formatos.imgStore;
            var record = new store.recordType({
                id: '',
                foto: "<[sys_images]>/fotos/" + this.record.get('adm') + ".png"
            });
            store.add(record);
        }
    },
    crea_stores: function () {
        this.imgStore = new Ext.data.ArrayStore({
            id: 0,
            fields: ['id', 'foto'],
            data: [['01', '<[sys_images]>/fotos/foto.png']]
        });
        this.st = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_formatos',
                format: 'json'
            },
            listeners: {
                'beforeload': function () {
                    this.baseParams.adm = mod.laboratorio.formatos.record.get('adm');
                }
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['adm', 'ex_desc', 'pk_id', 'ex_id', 'st', 'usu', 'fech', 'id', 'pac_sexo']
        });
    },
    crea_controles: function () {
        var tpl = new Ext.XTemplate(
                '<tpl for=".">',
                '<div class="thumb"><img width="196" height="263" src="{foto}" title="{id}"></div>',
                '</tpl>'
                );
        this.odont = new Ext.DataView({
            autoScroll: true,
            id: 'dientes_view',
            store: this.imgStore,
            tpl: tpl,
            autoHeight: false,
            height: 290,
            multiSelect: false,
            overClass: 'x-view-over',
            itemSelector: 'div.thumb-wrap2',
            emptyText: 'No hay foto disponible'
        });
        this.paginador = new Ext.PagingToolbar({
            pageSize: 30,
            store: this.st,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2} Formatos',
            emptyMsg: 'No Existe Registros',
            plugins: new Ext.ux.ProgressBarPager()
        });
        this.dt_grid = new Ext.grid.GridPanel({
            store: this.st,
            region: 'west',
            border: false,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            bbar: this.paginador,
            height: 263,
            listeners: {
                rowclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record = grid.getStore().getAt(rowIndex);//
                    if (record.get('ex_id') == 21) {//HEMOGRAMA COMPLETO
                        mod.laboratorio.hemograma.init(record);
                    } else if (record.get('ex_id') == 22) {//GRUPO SANGUINEO Y FACTOR
                        mod.laboratorio.grupo_factor.init(record);
                    } else if (record.get('ex_id') == 37) {//EXAMEN COMPLETO DE ORINA
                        mod.laboratorio.exa_orina.init(record);
                    } else if (record.get('ex_id') == 48) {//PERFIL 10 DROGAS
//                        mod.laboratorio.anexo_16a.init(record);
                    } else if (record.get('ex_id') == 56) {//PERFIL LIPÍDICO (COLESTEROL, HDL, LDL, TRIGLICERIDOS)
                        mod.laboratorio.perfil_lipido.init(record);
                    } else if (record.get('ex_id') == 48 || record.get('ex_id') == 62) {//PERFIL LIPÍDICO (COLESTEROL, HDL, LDL, TRIGLICERIDOS) 
                        mod.laboratorio.drogas_10.init(record);
                    } else {
                        mod.laboratorio.examenPRE.init(record);//
                    }
//                    mod.laboratorio.examenPRE.init(record);//
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    if (mod.laboratorio.formatos.record.get('st') == "1") {
                        new Ext.menu.Menu({
                            items: [{
                                    text: 'INFORME DE LABORATORIO N°: <B>' + record.get('adm') + '<B>',
                                    iconCls: 'reporte',
                                    handler: function () {
                                        if (record.get('st') >= 1) {
                                            new Ext.Window({
                                                title: 'INFORME DE LABORATORIO N° ' + record.get('adm'),
                                                width: 800,
                                                height: 600,
                                                maximizable: true,
                                                modal: true,
                                                closeAction: 'close',
                                                resizable: true,
                                                html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_laboratorio&sys_report=formato_laboratorio&adm=" + record.get('adm') + "'></iframe>"
                                            }).show();
                                        } else {
                                            Ext.MessageBox.alert('Observaciones', 'El paciente no fue registrado correctamente');
                                        }
                                    }
                                }]
                        }).showAt(event.xy);
                    }
                }
            },
            autoExpandColumn: 'cuest_desc',
            columns: [
                new Ext.grid.RowNumberer(),
                {
                    header: 'X',
                    width: 25,
                    sortable: true,
                    dataIndex: 'st',
                    renderer: function renderIcon(val) {
                        if (val > 0) {
                            return  '<img src="<[images]>/saveIcon.png" title="GUARDADO" height="14">';
                        } else {
                            return  '<img src="<[images]>/nuevo.png" title="REGISTRAR" height="14">';
                        }
                    }
                }, {
                    id: 'cuest_desc',
                    header: 'EXAMENES',
                    dataIndex: 'ex_desc'
                }, {
                    header: 'USUARIO',
                    dataIndex: 'usu',
                    width: 70
                }, {
                    header: 'FECHA DE REGISTRO',
                    dataIndex: 'fech',
                    width: 140
                }
            ], viewConfig: {
                getRowClass: function (record, index) {
                    var st = record.get('st');
                    if (st == 'null') {
                        return  'child-row';
                    } else if (st == '1') {
                        return  'child-blue';
                    }
                }
            }
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            layout: 'column',
            items: [{
                    columnWidth: .20,
                    border: false,
                    layout: 'form',
                    items: [this.odont]
                }, {
                    columnWidth: .25,
//                    border: false,
                    layout: 'form',
                    items: [new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="color:#267ED7;padding: 5px 0px 2px 5px;font-size: 13px;"><b>APELLIDOS:</b></div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' + this.record.get('ape') + '</div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="color:#267ED7;padding: 2px 0px 2px 5px;font-size: 13px;"><b>NOMBRES:</b></div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' + this.record.get('nom') + '</div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="color:#267ED7;padding: 2px 0px 2px 5px;font-size: 13px;"><b>NRO DE DOCUMENTO:</b></div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' + this.record.get('pac_ndoc') + '</div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="color:#267ED7;padding: 2px 0px 2px 5px;font-size: 13px;"><b>EDAD:</b></div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' + this.record.get('edad') + ' AÑOS</div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="color:#267ED7;padding: 2px 0px 2px 5px;font-size: 13px;"><b>TIPO DE PERFIL:</b></div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' + this.record.get('tfi_desc') + '</div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="color:#267ED7;padding: 2px 0px 2px 5px;font-size: 13px;"><b>ACTIVIDAD LABORAL:</b></div>'
                        }), new Ext.Panel({
                            columnWidth: .95,
                            border: false,
                            html: '<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' + this.record.get('puesto') + '</div>'
                        }), {html: '</br>', border: false}]
                }, {
                    columnWidth: .55,
                    border: false,
                    layout: 'form',
                    items: [this.dt_grid]
                }]
        });
        this.win = new Ext.Window({
            width: 1000,
            height: 300,
            modal: true,
            title: 'EXAMEN DE LABORATORIO: ' + this.record.get('nombre'),
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

mod.laboratorio.examenPRE = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        if (this.record.get('st') >= 1) {
            this.cargar_data();
        } else {
            this.cargar_referencias();
        }
        this.win.show();
    },
    cargar_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_examenLab',
                format: 'json',
                adm: mod.laboratorio.examenPRE.record.get('adm'),
                examen: mod.laboratorio.examenPRE.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.laboratorio.anexo_16a.val_medico.setValue(r.val_medico);
//                mod.laboratorio.anexo_16a.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    cargar_referencias: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_lab_exam_conf',
                format: 'json',
                examen: mod.laboratorio.examenPRE.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
            }
        });
    },
    crea_stores: function () {
        this.st_m_lab_exam_resultado = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_m_lab_exam_resultado',
                format: 'json',
                examen: mod.laboratorio.examenPRE.record.get('ex_id')
            },
            fields: ['m_lab_exam_resultado'],
            root: 'data'
        });
    },
    crea_controles: function () {
//m_lab_orina_color
        this.Tpl_m_lab_exam_resultado = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><b>{m_lab_exam_resultado}</b></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.m_lab_exam_resultado = new Ext.form.ComboBox({
            store: this.st_m_lab_exam_resultado,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.Tpl_m_lab_exam_resultado,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            allowBlank: false,
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'm_lab_exam_resultado',
            displayField: 'm_lab_exam_resultado',
            valueField: 'm_lab_exam_resultado',
            typeAhead: false,
            triggerAction: 'all',
//            fieldLabel: '<b>COLOR</b>',
            mode: 'remote',
            width: 190
        });
//labc_uni
        this.labc_uni = new Ext.form.TextField({
            name: 'labc_uni',
            disabled: true,
            width: 60
        });
//labc_valor
        this.labc_valor = new Ext.form.TextField({
            name: 'labc_valor',
            disabled: true,
            width: 200
        });

        this.modificar = new Ext.form.Checkbox({
            checked: false,
            name: 'modificar',
            listeners: {
                check: function (chk, checked) {
                    if (checked) {
                        mod.laboratorio.examenPRE.labc_uni.enable();
                        mod.laboratorio.examenPRE.labc_valor.enable();
                    } else {
                        mod.laboratorio.examenPRE.labc_uni.disable();
                        mod.laboratorio.examenPRE.labc_valor.disable();
                    }
                }
            }
        });

        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
//            frame: true,
            layout: 'column',
            bodyStyle: 'padding:2px 2px 2px 2px;',
            labelWidth: 99,
//            labelAlign: 'top',
            items: [{
                    xtype: 'panel', border: false,
                    columnWidth: .999,
                    labelWidth: 1,
                    bodyStyle: 'padding:15px 20px 0px 20px;',
                    items: [{
                            xtype: 'fieldset',
                            title: 'EXAMEN DE ' + this.record.get('ex_desc'),
                            items: [{
                                    xtype: 'compositefield',
                                    items: [{
                                            xtype: 'displayfield',
                                            value: '<center><b>RESULTADO</b></center>',
                                            width: 195
                                        }, {
                                            xtype: 'displayfield',
                                            value: '<center><b>UNIDAD</b></center>',
                                            width: 20
                                        }, {
                                            xtype: 'displayfield',
                                            value: '<center><b>RANGO DE REFERENCIA</b></center>',
                                            width: 230
                                        }]
                                }
                                , {
                                    xtype: 'compositefield',
//                                    fieldLabel: this.record.get('ex_desc'),
                                    bodyStyle: 'padding:3px;',
                                    items: [
                                        this.m_lab_exam_resultado,
                                        this.labc_uni,
                                        this.labc_valor,
                                        this.modificar
                                    ]
                                }
                            ]
                        }]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.laboratorio.examenPRE.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record.get('st') >= 1) ? 'update_exaLab' : 'save_exaLab'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
                                if (action.result.success === true) {
                                    if (action.result.total === 1) {
//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
                                        mod.laboratorio.formatos.st.reload();
                                        mod.laboratorio.st.reload();
                                        mod.laboratorio.examenPRE.win.el.unmask();
                                        mod.laboratorio.examenPRE.win.close();
                                    } else if (action.result.total === 0) {
                                        mod.laboratorio.examenPRE.win.el.unmask();
                                        mod.laboratorio.examenPRE.win.close();
                                    }
                                } else {
                                    Ext.Msg.show({
                                        title: 'Error',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR,
                                        msg: 'Problemas con el registro.'
                                    });
                                }
                            },
                            failure: function (form, action) {
                                mod.laboratorio.examenPRE.win.el.unmask();
                                mod.laboratorio.examenPRE.win.close();
                                mod.laboratorio.formatos.st.reload();
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
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 570,
            height: 200,
            border: false,
            modal: true,
            title: 'REGISTRO DE EXAMEN: ' + this.record.get('ex_desc'),
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};

mod.laboratorio.hemograma = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        if (this.record.get('st') >= 1) {
            this.cargar_data();
        } else {
            this.cargar_referencias();
        }
        this.win.show();
    },
    cargar_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_lab_hemograma',
                format: 'json',
                adm: mod.laboratorio.hemograma.record.get('adm')
//                ,anexo_16a_exa: mod.laboratorio.hemograma.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.laboratorio.hemograma.val_medico.setValue(r.val_medico);
//                mod.laboratorio.hemograma.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    cargar_referencias: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_lab_hemo_conf',
                format: 'json'
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
            }
        });
    },
    crea_stores: function () {

    },
    crea_controles: function () {

//m_lab_hemo_hemoglobina
        this.m_lab_hemo_hemoglobina = new Ext.form.TextField({
            fieldLabel: '<b>HEMOGLOBINA</b>',
            name: 'm_lab_hemo_hemoglobina',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_hematocrito
        this.m_lab_hemo_hematocrito = new Ext.form.TextField({
            fieldLabel: '<b>HEMATOCRITO</b>',
            name: 'm_lab_hemo_hematocrito',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_hematies
        this.m_lab_hemo_hematies = new Ext.form.TextField({
            fieldLabel: '<b>HEMATIES</b>',
            name: 'm_lab_hemo_hematies',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_plaquetas
        this.m_lab_hemo_plaquetas = new Ext.form.TextField({
            fieldLabel: '<b>PLAQUETAS</b>',
            name: 'm_lab_hemo_plaquetas',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_leucocitos
        this.m_lab_hemo_leucocitos = new Ext.form.TextField({
            fieldLabel: '<b>LEUCOCITOS TOTALES</b>',
            name: 'm_lab_hemo_leucocitos',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_monocitos
        this.m_lab_hemo_monocitos = new Ext.form.TextField({
            fieldLabel: '<b>MONOCITOS</b>',
            name: 'm_lab_hemo_monocitos',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_linfocitos
        this.m_lab_hemo_linfocitos = new Ext.form.TextField({
            fieldLabel: '<b>LINFOCITOS</b>',
            name: 'm_lab_hemo_linfocitos',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_eosinofilos
        this.m_lab_hemo_eosinofilos = new Ext.form.TextField({
            fieldLabel: '<b>EOSINOFILOS</b>',
            name: 'm_lab_hemo_eosinofilos',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_abastonados
        this.m_lab_hemo_abastonados = new Ext.form.TextField({
            fieldLabel: '<b>ABASTONADOS</b>',
            name: 'm_lab_hemo_abastonados',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_basofilos
        this.m_lab_hemo_basofilos = new Ext.form.TextField({
            fieldLabel: '<b>BASÓFILOS</b>',
            name: 'm_lab_hemo_basofilos',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_neutrofilos
        this.m_lab_hemo_neutrofilos = new Ext.form.TextField({
            fieldLabel: '<b>NEUTROFILOS SEGMENTADOS</b>',
            name: 'm_lab_hemo_neutrofilos',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_hemo_obs
        this.m_lab_hemo_obs = new Ext.form.TextArea({
            name: 'm_lab_hemo_obs',
            fieldLabel: '<b>OBSERVACIONES</b>',
            anchor: '99%',
            height: 40
        });

//CONFIG

//m_hemo_rango_hemoglobina
        this.m_hemo_rango_hemoglobina = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_hemoglobina',
            disabled: true,
            width: 170
        });
//m_hemo_unid_hemoglobina
        this.m_hemo_unid_hemoglobina = new Ext.form.TextField({
            //fieldLabel: '<b>unidades</b>',
            name: 'm_hemo_unid_hemoglobina',
            disabled: true,
            width: 60
        });
//m_hemo_rango_hematocrito
        this.m_hemo_rango_hematocrito = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_hematocrito',
            disabled: true,
            width: 170
        });
//m_hemo_unid_hematocrito
        this.m_hemo_unid_hematocrito = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_unid_hematocrito',
            disabled: true,
            width: 60
        });
//m_hemo_rango_hematies
        this.m_hemo_rango_hematies = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_hematies',
            disabled: true,
            width: 170
        });
//m_hemo_unid_hematies
        this.m_hemo_unid_hematies = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_unid_hematies',
            disabled: true,
            width: 60
        });
//m_hemo_rango_plaquetas
        this.m_hemo_rango_plaquetas = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_plaquetas',
            disabled: true,
            width: 170
        });
//m_hemo_unid_plaquetas
        this.m_hemo_unid_plaquetas = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_unid_plaquetas',
            disabled: true,
            width: 60
        });
//m_hemo_rango_leucocitos
        this.m_hemo_rango_leucocitos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_leucocitos',
            disabled: true,
            width: 170
        });
//m_hemo_unid_leucocitos
        this.m_hemo_unid_leucocitos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_unid_leucocitos',
            disabled: true,
            width: 60
        });
//m_hemo_rango_monocitos
        this.m_hemo_rango_monocitos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_monocitos',
            disabled: true,
            width: 170
        });
//m_hemo_unid_monocitos
        this.m_hemo_unid_monocitos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_unid_monocitos',
            disabled: true,
            width: 60
        });
//m_hemo_rango_linfocitos
        this.m_hemo_rango_linfocitos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_linfocitos',
            disabled: true,
            width: 170
        });
//m_hemo_unid_linfocitos
        this.m_hemo_unid_linfocitos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_unid_linfocitos',
            disabled: true,
            width: 60
        });
//m_hemo_rango_eosinofilos
        this.m_hemo_rango_eosinofilos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_eosinofilos',
            disabled: true,
            width: 170
        });
//m_hemo_unid_eosinofilos
        this.m_hemo_unid_eosinofilos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_unid_eosinofilos',
            disabled: true,
            width: 60
        });
//m_hemo_rango_abastonados
        this.m_hemo_rango_abastonados = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_abastonados',
            disabled: true,
            width: 170
        });
//m_hemo_unid_abastonados
        this.m_hemo_unid_abastonados = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_unid_abastonados',
            disabled: true,
            width: 60
        });
//m_hemo_rango_basofilos
        this.m_hemo_rango_basofilos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_basofilos',
            disabled: true,
            width: 170
        });
//m_hemo_unid_basofilos
        this.m_hemo_unid_basofilos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_unid_basofilos',
            disabled: true,
            width: 60
        });
//m_hemo_rango_neutrofilos
        this.m_hemo_rango_neutrofilos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_rango_neutrofilos',
            disabled: true,
            width: 170
        });
//m_hemo_unid_neutrofilos
        this.m_hemo_unid_neutrofilos = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'm_hemo_unid_neutrofilos',
            disabled: true,
            width: 60
        });
        this.total = new Ext.form.TextField({
            //fieldLabel: '<b>rango de referencia</b>',
            name: 'total',
            id: 'total',
            readOnly: true,
            width: 80
        });

        this.modificar = new Ext.form.RadioGroup({
            fieldLabel: '',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NO', name: 'modificar', inputValue: 'NO', checked: true},
                {boxLabel: 'SI', name: 'modificar', inputValue: 'SI',
                    handler: function (value, checkbox) {
                        if (checkbox == true) {
                            mod.laboratorio.hemograma.m_hemo_rango_hemoglobina.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_hemoglobina.enable();
                            mod.laboratorio.hemograma.m_hemo_rango_hematocrito.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_hematocrito.enable();
                            mod.laboratorio.hemograma.m_hemo_rango_hematies.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_hematies.enable();
                            mod.laboratorio.hemograma.m_hemo_rango_plaquetas.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_plaquetas.enable();
                            mod.laboratorio.hemograma.m_hemo_rango_leucocitos.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_leucocitos.enable();
                            mod.laboratorio.hemograma.m_hemo_rango_monocitos.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_monocitos.enable();
                            mod.laboratorio.hemograma.m_hemo_rango_linfocitos.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_linfocitos.enable();
                            mod.laboratorio.hemograma.m_hemo_rango_eosinofilos.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_eosinofilos.enable();
                            mod.laboratorio.hemograma.m_hemo_rango_abastonados.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_abastonados.enable();
                            mod.laboratorio.hemograma.m_hemo_rango_basofilos.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_basofilos.enable();
                            mod.laboratorio.hemograma.m_hemo_rango_neutrofilos.enable();
                            mod.laboratorio.hemograma.m_hemo_unid_neutrofilos.enable();
                        } else if (checkbox == false) {
                            mod.laboratorio.hemograma.m_hemo_rango_hemoglobina.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_hemoglobina.disable();
                            mod.laboratorio.hemograma.m_hemo_rango_hematocrito.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_hematocrito.disable();
                            mod.laboratorio.hemograma.m_hemo_rango_hematies.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_hematies.disable();
                            mod.laboratorio.hemograma.m_hemo_rango_plaquetas.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_plaquetas.disable();
                            mod.laboratorio.hemograma.m_hemo_rango_leucocitos.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_leucocitos.disable();
                            mod.laboratorio.hemograma.m_hemo_rango_monocitos.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_monocitos.disable();
                            mod.laboratorio.hemograma.m_hemo_rango_linfocitos.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_linfocitos.disable();
                            mod.laboratorio.hemograma.m_hemo_rango_eosinofilos.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_eosinofilos.disable();
                            mod.laboratorio.hemograma.m_hemo_rango_abastonados.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_abastonados.disable();
                            mod.laboratorio.hemograma.m_hemo_rango_basofilos.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_basofilos.disable();
                            mod.laboratorio.hemograma.m_hemo_rango_neutrofilos.disable();
                            mod.laboratorio.hemograma.m_hemo_unid_neutrofilos.disable();
                        }
                    }}
            ]
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            border: false,
            layout: 'accordion',
            layoutConfig: {
                titleCollapse: true,
                animate: true,
                hideCollapseTool: true
            },
            items: [{
                    title: '<b>--->  HEMOGRAMA COMPLETO</b>',
                    iconCls: 'demo2',
                    layout: 'column',
                    autoScroll: true,
                    border: false,
                    bodyStyle: 'padding:10px 10px 20px 10px;',
                    items: [
                        {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            labelWidth: 99,
                            bodyStyle: 'padding:2px 15px 0px 22px;',
                            items: [{
                                    xtype: 'fieldset',
                                    title: 'EXAMENES',
                                    items: [{
                                            xtype: 'compositefield',
                                            items: [{
                                                    xtype: 'displayfield',
                                                    value: '<center><b>RESULTADO</b></center>',
                                                    width: 87
                                                }, {
                                                    xtype: 'displayfield',
                                                    value: '<center><b>UNIDAD</b></center>',
                                                    width: 15
                                                }, {
                                                    xtype: 'displayfield',
                                                    value: '<center><b>RANGO DE REFERENCIA</b></center>',
                                                    width: 250
                                                }]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'HEMOGLOBINA',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_hemoglobina,
                                                this.m_hemo_unid_hemoglobina,
                                                this.m_hemo_rango_hemoglobina
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'HEMATOCRITO',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_hematocrito,
                                                this.m_hemo_unid_hematocrito,
                                                this.m_hemo_rango_hematocrito
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'HEMATIES',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_hematies,
                                                this.m_hemo_unid_hematies,
                                                this.m_hemo_rango_hematies
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'PLAQUETAS',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_plaquetas,
                                                this.m_hemo_unid_plaquetas,
                                                this.m_hemo_rango_plaquetas
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'LEUCOCITOS TOTAL',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_leucocitos,
                                                this.m_hemo_unid_leucocitos,
                                                this.m_hemo_rango_leucocitos
                                            ]
                                        }
                                    ]
                                }]
                        },
                        {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            labelWidth: 99,
                            bodyStyle: 'padding:2px 15px 0px 22px;',
                            items: [{
                                    xtype: 'fieldset',
                                    title: 'FORMULA LEUCOSITARIA',
                                    items: [{
                                            xtype: 'compositefield',
                                            items: [{
                                                    xtype: 'displayfield',
                                                    value: '<center><b>RESULTADO</b></center>',
                                                    width: 87
                                                }, {
                                                    xtype: 'displayfield',
                                                    value: '<center><b>UNIDAD</b></center>',
                                                    width: 15
                                                }, {
                                                    xtype: 'displayfield',
                                                    value: '<center><b>RANGO DE REFERENCIA</b></center>',
                                                    width: 250
                                                }]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'MONOCITOS',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_monocitos,
                                                this.m_hemo_unid_monocitos,
                                                this.m_hemo_rango_monocitos
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'LINFOCITOS',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_linfocitos,
                                                this.m_hemo_unid_linfocitos,
                                                this.m_hemo_rango_linfocitos
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'EOSINAFILOS',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_eosinofilos,
                                                this.m_hemo_unid_eosinofilos,
                                                this.m_hemo_rango_eosinofilos
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'ABASTONADOS',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_abastonados,
                                                this.m_hemo_unid_abastonados,
                                                this.m_hemo_rango_abastonados
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'BASOFILOS',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_basofilos,
                                                this.m_hemo_unid_basofilos,
                                                this.m_hemo_rango_basofilos
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'NEUTROFILOS SEGMENTADOS',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_hemo_neutrofilos,
                                                this.m_hemo_unid_neutrofilos,
                                                this.m_hemo_rango_neutrofilos
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: '<b>TOTAL</b>',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.total
                                            ]
                                        }
                                    ]
                                }]
                        }, {
                            columnWidth: .50,
                            labelAlign: 'top',
                            border: false,
                            layout: 'form',
                            labelWidth: 60,
                            bodyStyle: 'padding:0px 10px 0px 20px;',
                            items: [this.m_lab_hemo_obs]
                        }, {
                            xtype: 'panel', border: false,
                            labelWidth: 60,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 15px 0px 22px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: '<b>¿DESEA MODIFICAR LOS CAMPOS BLOQUEADOS?</b>',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 60,
                                            items: [this.modificar]
                                        }]
                                }]
                        }
                    ]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.laboratorio.hemograma.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({params: {
                                acction: (this.record.get('st') >= 1) ? 'update_lab_hemograma' : 'save_lab_hemograma'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
                                if (action.result.success === true) {
                                    if (action.result.total === 1) {
                                        mod.laboratorio.formatos.st.reload();
                                        mod.laboratorio.st.reload();
                                        mod.laboratorio.hemograma.win.el.unmask();
                                        mod.laboratorio.hemograma.win.close();
                                    } else if (action.result.total === 0) {
                                        mod.laboratorio.hemograma.win.el.unmask();
                                        mod.laboratorio.hemograma.win.close();
                                    }
                                } else {
                                    Ext.Msg.show({
                                        title: 'Error',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR,
                                        msg: 'Problemas con el registro.'
                                    });
                                }
                            },
                            failure: function (form, action) {
                                mod.laboratorio.hemograma.win.el.unmask();
                                mod.laboratorio.hemograma.win.close();
                                mod.laboratorio.formatos.st.reload();
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
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 1000,
            height: 440,
            border: false,
            modal: true,
            title: 'EXAMEN DE LABORATORIO HEMOGRAMA COMPLETO: ',
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};

mod.laboratorio.exa_orina = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        if (this.record.get('st') >= 1) {
            this.cargar_data();
        }
        this.win.show();
    },
    cargar_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_exa_orina',
                format: 'json',
                adm: mod.laboratorio.exa_orina.record.get('adm')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.laboratorio.exa_orina.val_medico.setValue(r.val_medico);
//                mod.laboratorio.exa_orina.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_stores: function () {
        this.st_m_lab_orina_color = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_m_lab_orina_color',
                format: 'json'
            },
            fields: ['m_lab_orina_color'],
            root: 'data'
        });

        this.st_m_lab_orina_aspecto = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_m_lab_orina_aspecto',
                format: 'json'
            },
            fields: ['m_lab_orina_aspecto'],
            root: 'data'
        });

        this.st_m_lab_orina_cristales = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_m_lab_orina_cristales',
                format: 'json'
            },
            fields: ['m_lab_orina_cristales'],
            root: 'data'
        });

        this.st_m_lab_orina_germenes = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_m_lab_orina_germenes',
                format: 'json'
            },
            fields: ['m_lab_orina_germenes'],
            root: 'data'
        });

        this.st_m_lab_orina_cel_epitelia = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_m_lab_orina_cel_epitelia',
                format: 'json'
            },
            fields: ['m_lab_orina_cel_epitelia'],
            root: 'data'
        });

        this.st_m_lab_orina_cilindros = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_m_lab_orina_cilindros',
                format: 'json'
            },
            fields: ['m_lab_orina_cilindros'],
            root: 'data'
        });
        this.st_m_lab_orina_otros = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_m_lab_orina_otros',
                format: 'json'
            },
            fields: ['m_lab_orina_otros'],
            root: 'data'
        });
    },
    crea_controles: function () {

//m_lab_orina_color
        this.Tpl_m_lab_orina_color = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><b>{m_lab_orina_color}</b></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.m_lab_orina_color = new Ext.form.ComboBox({
            store: this.st_m_lab_orina_color,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.Tpl_m_lab_orina_color,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'm_lab_orina_color',
            displayField: 'm_lab_orina_color',
            valueField: 'm_lab_orina_color',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>COLOR</b>',
            mode: 'remote',
            anchor: '95%'
        });
//m_lab_orina_aspecto
        this.Tpl_m_lab_orina_aspecto = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><b>{m_lab_orina_aspecto}</b></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.m_lab_orina_aspecto = new Ext.form.ComboBox({
            store: this.st_m_lab_orina_aspecto,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.Tpl_m_lab_orina_aspecto,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'm_lab_orina_aspecto',
            displayField: 'm_lab_orina_aspecto',
            valueField: 'm_lab_orina_aspecto',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>ASPECTO</b>',
            mode: 'remote',
            anchor: '95%'
        });
//m_lab_orina_ph
        this.m_lab_orina_ph = new Ext.form.TextField({
            fieldLabel: '<b>PH</b>',
            name: 'm_lab_orina_ph',
            //value: 'NIEGA',
            anchor: '95%'
        });
//m_lab_orina_densidad
        this.m_lab_orina_densidad = new Ext.form.TextField({
            fieldLabel: '<b>DENSIDAD</b>',
            name: 'm_lab_orina_densidad',
            //value: 'NIEGA',
            anchor: '95%'
        });
//m_lab_orina_glucosa
        this.m_lab_orina_glucosa = new Ext.form.TextField({
            fieldLabel: '<b>GLUCOSA</b>',
            name: 'm_lab_orina_glucosa',
            value: 'NEGATIVO',
            anchor: '95%'
        });
//m_lab_orina_urobilino
        this.m_lab_orina_urobilino = new Ext.form.TextField({
            fieldLabel: '<b>UROBILINÓGENO</b>',
            name: 'm_lab_orina_urobilino',
            value: 'NEGATIVO',
            anchor: '95%'
        });
//m_lab_orina_proteinas
        this.m_lab_orina_proteinas = new Ext.form.TextField({
            fieldLabel: '<b>PROTEINAS</b>',
            name: 'm_lab_orina_proteinas',
            value: 'NEGATIVO',
            anchor: '95%'
        });
//m_lab_orina_nitritos
        this.m_lab_orina_nitritos = new Ext.form.TextField({
            fieldLabel: '<b>NITRITOS</b>',
            name: 'm_lab_orina_nitritos',
            value: 'NEGATIVO',
            anchor: '95%'
        });
//m_lab_orina_bilirrubina
        this.m_lab_orina_bilirrubina = new Ext.form.TextField({
            fieldLabel: '<b>BILIRRUBINA</b>',
            name: 'm_lab_orina_bilirrubina',
            value: 'NEGATIVO',
            anchor: '95%'
        });
//m_lab_orina_hemoglobina
        this.m_lab_orina_hemoglobina = new Ext.form.TextField({
            fieldLabel: '<b>HEMOGLOBINA</b>',
            name: 'm_lab_orina_hemoglobina',
            value: 'NEGATIVO',
            anchor: '95%'
        });
//m_lab_orina_acido_ascorbi
        this.m_lab_orina_acido_ascorbi = new Ext.form.TextField({
            fieldLabel: '<b>ACIDO ASCORBICO</b>',
            name: 'm_lab_orina_acido_ascorbi',
            value: 'NEGATIVO',
            anchor: '95%'
        });
//m_lab_orina_esterasa_leuco
        this.m_lab_orina_esterasa_leuco = new Ext.form.TextField({
            fieldLabel: '<b>ESTERASA LEUCOSITARIA</b>',
            name: 'm_lab_orina_esterasa_leuco',
            value: 'NEGATIVO',
            anchor: '95%'
        });
//m_lab_orina_cuerpo_certoni
        this.m_lab_orina_cuerpo_certoni = new Ext.form.TextField({
            fieldLabel: '<b>CUERPOS CETÓNICOS</b>',
            name: 'm_lab_orina_cuerpo_certoni',
            value: 'NEGATIVO',
            anchor: '95%'
        });
//m_lab_orina_leucocitos
        this.m_lab_orina_leucocitos = new Ext.form.TextField({
            fieldLabel: '<b>LEUCOCITOS</b>',
            name: 'm_lab_orina_leucocitos',
//            value: 'NEGATIVO',
            anchor: '95%'
        });
//m_lab_orina_hematies
        this.m_lab_orina_hematies = new Ext.form.TextField({
            fieldLabel: '<b>HEMATIES</b>',
            name: 'm_lab_orina_hematies',
//            value: 'NEGATIVO',
            anchor: '95%'
        });

//m_lab_orina_cristales

        this.Tpl_m_lab_orina_cristales = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><b>{m_lab_orina_cristales}</b></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.m_lab_orina_cristales = new Ext.form.ComboBox({
            store: this.st_m_lab_orina_cristales,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.Tpl_m_lab_orina_cristales,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'm_lab_orina_cristales',
            displayField: 'm_lab_orina_cristales',
            valueField: 'm_lab_orina_cristales',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>CRISTALES</b>',
            mode: 'remote',
            anchor: '95%'
        });
//m_lab_orina_germenes

        this.Tpl_m_lab_orina_germenes = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><b>{m_lab_orina_germenes}</b></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.m_lab_orina_germenes = new Ext.form.ComboBox({
            store: this.st_m_lab_orina_germenes,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.Tpl_m_lab_orina_germenes,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'm_lab_orina_germenes',
            displayField: 'm_lab_orina_germenes',
            valueField: 'm_lab_orina_germenes',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>GÉRMENES</b>',
            mode: 'remote',
            anchor: '95%'
        });
//m_lab_orina_cel_epitelia
        this.Tpl_m_lab_orina_cel_epitelia = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><b>{m_lab_orina_cel_epitelia}</b></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.m_lab_orina_cel_epitelia = new Ext.form.ComboBox({
            store: this.st_m_lab_orina_cel_epitelia,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.Tpl_m_lab_orina_cel_epitelia,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'm_lab_orina_cel_epitelia',
            displayField: 'm_lab_orina_cel_epitelia',
            valueField: 'm_lab_orina_cel_epitelia',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>CÉLULAS EPITELIALES</b>',
            mode: 'remote',
            anchor: '95%'
        });
//m_lab_orina_cilindros
        this.Tpl_m_lab_orina_cilindros = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><b>{m_lab_orina_cilindros}</b></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.m_lab_orina_cilindros = new Ext.form.ComboBox({
            store: this.st_m_lab_orina_cilindros,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.Tpl_m_lab_orina_cilindros,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'm_lab_orina_cilindros',
            displayField: 'm_lab_orina_cilindros',
            valueField: 'm_lab_orina_cilindros',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>CILINDROS</b>',
            mode: 'remote',
            anchor: '95%'
        });
//m_lab_orina_otros
        this.Tpl_m_lab_orina_otros = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><b>{m_lab_orina_otros}</b></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.m_lab_orina_otros = new Ext.form.ComboBox({
            store: this.st_m_lab_orina_otros,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.Tpl_m_lab_orina_otros,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'm_lab_orina_otros',
            displayField: 'm_lab_orina_otros',
            valueField: 'm_lab_orina_otros',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>OTROS</b>',
            mode: 'remote',
            anchor: '95%'
        });
//m_lab_orina_observaciones
        this.m_lab_orina_observaciones = new Ext.form.TextArea({
            name: 'm_lab_orina_observaciones',
            fieldLabel: '<b>OBSERVACIONES</b>',
            anchor: '95%',
            height: 35
        });


        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            border: false,
            layout: 'accordion',
            layoutConfig: {
                titleCollapse: true,
                animate: true,
                hideCollapseTool: true
            },
            items: [{
                    title: '<b>--->  EXAMEN DE LABORATORIO EN ORINA COMPLETO</b>',
                    iconCls: 'demo2',
                    layout: 'column',
                    autoScroll: true,
                    border: false,
                    bodyStyle: 'padding:10px 10px 20px 10px;',
                    labelWidth: 60,
                    items: [{
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'MACROSCOPICO',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 60,
                                            items: [this.m_lab_orina_color]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 60,
                                            items: [this.m_lab_orina_aspecto]
                                        }]
                                }, {
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'SEDIMENTO URINARIO',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 85,
                                            items: [this.m_lab_orina_leucocitos]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 85,
                                            items: [this.m_lab_orina_hematies]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 85,
                                            items: [this.m_lab_orina_cristales]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 85,
                                            items: [this.m_lab_orina_germenes]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 85,
                                            items: [this.m_lab_orina_cel_epitelia]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 85,
                                            items: [this.m_lab_orina_cilindros]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 85,
                                            items: [this.m_lab_orina_otros]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            labelWidth: 60,
                                            items: [this.m_lab_orina_observaciones]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'EXAMEN QUÍMICO',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_ph]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_densidad]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_glucosa]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_urobilino]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_proteinas]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_nitritos]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_bilirrubina]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_hemoglobina]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_acido_ascorbi]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_esterasa_leuco]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            labelWidth: 110,
                                            items: [this.m_lab_orina_cuerpo_certoni]
                                        }]
                                }]
                        }]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.laboratorio.exa_orina.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({params: {
                                acction: (this.record.get('st') >= 1) ? 'update_lab_orina' : 'save_lab_orina'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
                                if (action.result.success === true) {
                                    if (action.result.total === 1) {
                                        mod.laboratorio.formatos.st.reload();
                                        mod.laboratorio.st.reload();
                                        mod.laboratorio.exa_orina.win.el.unmask();
                                        mod.laboratorio.exa_orina.win.close();
                                    } else if (action.result.total === 0) {
                                        mod.laboratorio.exa_orina.win.el.unmask();
                                        mod.laboratorio.exa_orina.win.close();
                                    }
                                } else {
                                    Ext.Msg.show({
                                        title: 'Error',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR,
                                        msg: 'Problemas con el registro.'
                                    });
                                }
                            },
                            failure: function (form, action) {
                                mod.laboratorio.exa_orina.win.el.unmask();
                                mod.laboratorio.exa_orina.win.close();
                                mod.laboratorio.formatos.st.reload();
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
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 600,
            height: 510,
            border: false,
            modal: true,
            title: 'EXAMEN DE ORINA COMPLETO: ',
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};

mod.laboratorio.perfil_lipido = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        if (this.record.get('st') >= 1) {
            this.cargar_data();
        } else {
            this.cargar_referencias();
        }
        this.win.show();
    },
    cargar_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_lab_p_lipido',
                format: 'json',
                adm: mod.laboratorio.perfil_lipido.record.get('adm')
//                ,anexo_16a_exa: mod.laboratorio.perfil_lipido.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.laboratorio.perfil_lipido.val_medico.setValue(r.val_medico);
//                mod.laboratorio.perfil_lipido.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    cargar_referencias: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_lab_p_lipido_conf',
                format: 'json'
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
            }
        });
    },
    crea_stores: function () {

    },
    crea_controles: function () {

//m_lab_p_lipido_colesterol_hdl
        this.m_lab_p_lipido_colesterol_hdl = new Ext.form.TextField({
            fieldLabel: '<b>colesterol</b>',
            name: 'm_lab_p_lipido_colesterol_hdl',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_p_lipido_colesterol_ldl
        this.m_lab_p_lipido_colesterol_ldl = new Ext.form.TextField({
            fieldLabel: '<b>colesterol</b>',
            name: 'm_lab_p_lipido_colesterol_ldl',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_p_lipido_colesterol_vldl
        this.m_lab_p_lipido_colesterol_vldl = new Ext.form.TextField({
            fieldLabel: '<b>colesterol</b>',
            name: 'm_lab_p_lipido_colesterol_vldl',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_p_lipido_colesterol_total
        this.m_lab_p_lipido_colesterol_total = new Ext.form.TextField({
            fieldLabel: '<b>colesterol</b>',
            name: 'm_lab_p_lipido_colesterol_total',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_p_lipido_trigliceridos
        this.m_lab_p_lipido_trigliceridos = new Ext.form.TextField({
            fieldLabel: '<b>colesterol</b>',
            name: 'm_lab_p_lipido_trigliceridos',
            width: 80,
            maskRe: /[0-9.]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            }
        });
//m_lab_p_lipido_riesg_coronario
        this.m_lab_p_lipido_riesg_coronario = new Ext.form.TextField({
            name: 'm_lab_p_lipido_riesg_coronario',
            width: 200
        });
//m_hemo_rango_hemoglobina
        this.m_hemo_rango_hemoglobina = new Ext.form.TextField({
            name: 'm_hemo_rango_hemoglobina',
            disabled: true,
            width: 170
        });
//m_hemo_unid_hemoglobina
        this.m_hemo_unid_hemoglobina = new Ext.form.TextField({
            name: 'm_hemo_unid_hemoglobina',
            disabled: true,
            width: 60
        });
//m_hemo_rango_hematocrito
        this.m_hemo_rango_hematocrito = new Ext.form.TextField({
            name: 'm_hemo_rango_hematocrito',
            disabled: true,
            width: 170
        });
//m_p_lipido_unid_colesterol_hdl
        this.m_p_lipido_unid_colesterol_hdl = new Ext.form.TextField({
            name: 'm_p_lipido_unid_colesterol_hdl',
            disabled: true,
            width: 60
        });
//m_p_lipido_refe_colesterol_hdl
        this.m_p_lipido_refe_colesterol_hdl = new Ext.form.TextField({
            name: 'm_p_lipido_refe_colesterol_hdl',
            disabled: true,
            width: 170
        });
//m_p_lipido_meto_colesterol_hdl
        this.m_p_lipido_meto_colesterol_hdl = new Ext.form.TextField({
            name: 'm_p_lipido_meto_colesterol_hdl',
            disabled: true,
            width: 100
        });
//m_p_lipido_unid_colesterol_ldl
        this.m_p_lipido_unid_colesterol_ldl = new Ext.form.TextField({
            name: 'm_p_lipido_unid_colesterol_ldl',
            disabled: true,
            width: 60
        });
//m_p_lipido_refe_colesterol_ldl
        this.m_p_lipido_refe_colesterol_ldl = new Ext.form.TextField({
            name: 'm_p_lipido_refe_colesterol_ldl',
            disabled: true,
            width: 170
        });
//m_p_lipido_meto_colesterol_ldl
        this.m_p_lipido_meto_colesterol_ldl = new Ext.form.TextField({
            name: 'm_p_lipido_meto_colesterol_ldl',
            disabled: true,
            width: 100
        });
//m_p_lipido_unid_colesterol_vldl
        this.m_p_lipido_unid_colesterol_vldl = new Ext.form.TextField({
            name: 'm_p_lipido_unid_colesterol_vldl',
            disabled: true,
            width: 60
        });
//m_p_lipido_refe_colesterol_vldl
        this.m_p_lipido_refe_colesterol_vldl = new Ext.form.TextField({
            name: 'm_p_lipido_refe_colesterol_vldl',
            disabled: true,
            width: 170
        });
//m_p_lipido_meto_colesterol_vldl
        this.m_p_lipido_meto_colesterol_vldl = new Ext.form.TextField({
            name: 'm_p_lipido_meto_colesterol_vldl',
            disabled: true,
            width: 100
        });
//
//m_p_lipido_unid_colesterol_total
        this.m_p_lipido_unid_colesterol_total = new Ext.form.TextField({
            name: 'm_p_lipido_unid_colesterol_total',
            disabled: true,
            width: 60
        });
//m_p_lipido_refe_colesterol_total
        this.m_p_lipido_refe_colesterol_total = new Ext.form.TextField({
            name: 'm_p_lipido_refe_colesterol_total',
            disabled: true,
            width: 170
        });
//m_p_lipido_meto_colesterol_total
        this.m_p_lipido_meto_colesterol_total = new Ext.form.TextField({
            name: 'm_p_lipido_meto_colesterol_total',
            disabled: true,
            width: 100
        });
//
//m_p_lipido_unid_trigliceridos
        this.m_p_lipido_unid_trigliceridos = new Ext.form.TextField({
            name: 'm_p_lipido_unid_trigliceridos',
            disabled: true,
            width: 60
        });
//m_p_lipido_refe_trigliceridos
        this.m_p_lipido_refe_trigliceridos = new Ext.form.TextField({
            name: 'm_p_lipido_refe_trigliceridos',
            disabled: true,
            width: 170
        });
//m_p_lipido_meto_trigliceridos
        this.m_p_lipido_meto_trigliceridos = new Ext.form.TextField({
            name: 'm_p_lipido_meto_trigliceridos',
            disabled: true,
            width: 100
        });

        this.modificar = new Ext.form.RadioGroup({
            fieldLabel: '',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NO', name: 'modificar', inputValue: 'NO', checked: true},
                {boxLabel: 'SI', name: 'modificar', inputValue: 'SI',
                    handler: function (value, checkbox) {
                        if (checkbox == true) {
                            mod.laboratorio.perfil_lipido.m_p_lipido_unid_colesterol_hdl.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_refe_colesterol_hdl.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_meto_colesterol_hdl.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_unid_colesterol_ldl.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_refe_colesterol_ldl.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_meto_colesterol_ldl.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_unid_colesterol_vldl.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_refe_colesterol_vldl.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_meto_colesterol_vldl.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_unid_colesterol_total.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_refe_colesterol_total.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_meto_colesterol_total.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_unid_trigliceridos.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_refe_trigliceridos.enable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_meto_trigliceridos.enable();
                        } else if (checkbox == false) {
                            mod.laboratorio.perfil_lipido.m_p_lipido_unid_colesterol_hdl.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_refe_colesterol_hdl.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_meto_colesterol_hdl.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_unid_colesterol_ldl.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_refe_colesterol_ldl.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_meto_colesterol_ldl.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_unid_colesterol_vldl.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_refe_colesterol_vldl.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_meto_colesterol_vldl.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_unid_colesterol_total.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_refe_colesterol_total.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_meto_colesterol_total.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_unid_trigliceridos.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_refe_trigliceridos.disable();
                            mod.laboratorio.perfil_lipido.m_p_lipido_meto_trigliceridos.disable();
                        }
                    }}
            ]
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            border: false,
            layout: 'accordion',
            layoutConfig: {
                titleCollapse: true,
                animate: true,
                hideCollapseTool: true
            },
            items: [{
                    title: '<b>--->  EXAMEN PERFIL LIPIDICO</b>',
                    iconCls: 'demo2',
                    layout: 'column',
                    autoScroll: true,
                    border: false,
                    bodyStyle: 'padding:10px 10px 20px 10px;',
                    items: [
                        {
                            xtype: 'panel', border: false,
                            columnWidth: .999,
                            labelWidth: 120,
                            bodyStyle: 'padding:2px 15px 0px 22px;',
                            items: [{
                                    xtype: 'fieldset',
                                    title: 'EXAMENES',
                                    items: [{
                                            xtype: 'compositefield',
                                            items: [{
                                                    xtype: 'displayfield',
                                                    value: '<center><b>RESULTADO</b></center>',
                                                    width: 87
                                                }, {
                                                    xtype: 'displayfield',
                                                    value: '<center><b>UNIDAD</b></center>',
                                                    width: 15
                                                }, {
                                                    xtype: 'displayfield',
                                                    value: '<center><b>RANGO DE REFERENCIA</b></center>',
                                                    width: 230
                                                }, {
                                                    xtype: 'displayfield',
                                                    value: '<center><b>METODO</b></center>',
                                                    width: 2
                                                }]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'COLESTEROL HDL',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_p_lipido_colesterol_hdl,
                                                this.m_p_lipido_unid_colesterol_hdl,
                                                this.m_p_lipido_refe_colesterol_hdl,
                                                this.m_p_lipido_meto_colesterol_hdl
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'COLESTEROL LDL',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_p_lipido_colesterol_ldl,
                                                this.m_p_lipido_unid_colesterol_ldl,
                                                this.m_p_lipido_refe_colesterol_ldl,
                                                this.m_p_lipido_meto_colesterol_ldl
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'COLESTEROL VLDL',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_p_lipido_colesterol_vldl,
                                                this.m_p_lipido_unid_colesterol_vldl,
                                                this.m_p_lipido_refe_colesterol_vldl,
                                                this.m_p_lipido_meto_colesterol_vldl
                                            ]
                                        }
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'COLESTEROL TOTAL',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_p_lipido_colesterol_total,
                                                this.m_p_lipido_unid_colesterol_total,
                                                this.m_p_lipido_refe_colesterol_total,
                                                this.m_p_lipido_meto_colesterol_total
                                            ]
                                        }//
                                        , {
                                            xtype: 'compositefield',
                                            fieldLabel: 'TRIGLICERIDOS',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_p_lipido_trigliceridos,
                                                this.m_p_lipido_unid_trigliceridos,
                                                this.m_p_lipido_refe_trigliceridos,
                                                this.m_p_lipido_meto_trigliceridos
                                            ]
                                        }, {
                                            xtype: 'compositefield',
                                            fieldLabel: 'RIESGO CORONARIO',
                                            bodyStyle: 'padding:3px;',
                                            items: [
                                                this.m_lab_p_lipido_riesg_coronario
                                            ]
                                        }
                                    ]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            labelWidth: 60,
                            columnWidth: .999,
                            bodyStyle: 'padding:2px 15px 0px 22px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: '<b>¿DESEA MODIFICAR LOS CAMPOS BLOQUEADOS?</b>',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 60,
                                            items: [this.modificar]
                                        }]
                                }]
                        }
                    ]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.laboratorio.perfil_lipido.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({params: {
                                acction: (this.record.get('st') >= 1) ? 'update_lab_p_lipido' : 'save_lab_p_lipido'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
                                if (action.result.success === true) {
                                    if (action.result.total === 1) {
                                        mod.laboratorio.formatos.st.reload();
                                        mod.laboratorio.st.reload();
                                        mod.laboratorio.perfil_lipido.win.el.unmask();
                                        mod.laboratorio.perfil_lipido.win.close();
                                    } else if (action.result.total === 0) {
                                        mod.laboratorio.perfil_lipido.win.el.unmask();
                                        mod.laboratorio.perfil_lipido.win.close();
                                    }
                                } else {
                                    Ext.Msg.show({
                                        title: 'Error',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR,
                                        msg: 'Problemas con el registro.'
                                    });
                                }
                            },
                            failure: function (form, action) {
                                mod.laboratorio.perfil_lipido.win.el.unmask();
                                mod.laboratorio.perfil_lipido.win.close();
                                mod.laboratorio.formatos.st.reload();
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
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 650,
            height: 400,
            border: false,
            modal: true,
            title: 'EXAMEN PERFIL LIPIDICO: ',
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};

mod.laboratorio.grupo_factor = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_controles();
        if (this.record.get('st') >= 1) {
            this.cargar_data();
        }
        this.win.show();
    },
    cargar_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_examenLab',
                format: 'json',
                adm: mod.laboratorio.grupo_factor.record.get('adm'),
                examen: mod.laboratorio.grupo_factor.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.laboratorio.anexo_16a.val_medico.setValue(r.val_medico);
//                mod.laboratorio.anexo_16a.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_controles: function () {
        this.m_lab_exam_resultado = new Ext.form.RadioGroup({
            fieldLabel: '<b>' + this.record.get('ex_desc') + '</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'A RH -', name: 'm_lab_exam_resultado', inputValue: 'A RH -'},
                {boxLabel: 'A RH +', name: 'm_lab_exam_resultado', inputValue: 'A RH +'},
                {boxLabel: 'AB RH -', name: 'm_lab_exam_resultado', inputValue: 'AB RH -'},
                {boxLabel: 'AB RH +', name: 'm_lab_exam_resultado', inputValue: 'AB RH +'},
                {boxLabel: 'B RH -', name: 'm_lab_exam_resultado', inputValue: 'B RH -'},
                {boxLabel: 'B RH +', name: 'm_lab_exam_resultado', inputValue: 'B RH +'},
                {boxLabel: 'O RH -', name: 'm_lab_exam_resultado', inputValue: 'O RH -'},
                {boxLabel: 'O RH +', name: 'm_lab_exam_resultado', inputValue: 'O RH +', checked: true}
            ]
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            frame: true,
            layout: 'column',
            bodyStyle: 'padding:2px 2px 2px 2px;',
            labelWidth: 99,
            labelAlign: 'top',
            items: [{
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_lab_exam_resultado]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.laboratorio.grupo_factor.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record.get('st') >= 1) ? 'update_exaLab' : 'save_exaLab'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
                                if (action.result.success === true) {
                                    if (action.result.total === 1) {
                                        mod.laboratorio.formatos.st.reload();
                                        mod.laboratorio.st.reload();
                                        mod.laboratorio.grupo_factor.win.el.unmask();
                                        mod.laboratorio.grupo_factor.win.close();
                                    } else if (action.result.total === 0) {
                                        mod.laboratorio.grupo_factor.win.el.unmask();
                                        mod.laboratorio.grupo_factor.win.close();
                                    }
                                } else {
                                    Ext.Msg.show({
                                        title: 'Error',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR,
                                        msg: 'Problemas con el registro.'
                                    });
                                }
                            },
                            failure: function (form, action) {
                                mod.laboratorio.grupo_factor.win.el.unmask();
                                mod.laboratorio.grupo_factor.win.close();
                                mod.laboratorio.formatos.st.reload();
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
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 220,
            height: 220,
            modal: true,
            title: 'REGISTRO DE EXAMENES',
            border: false,
            maximizable: true,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};

mod.laboratorio.drogas_10 = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        if (this.record.get('st') >= 1) {
            this.cargar_data();
        }
        this.win.show();
    },
    cargar_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_exa_drogas_01',
                format: 'json',
                adm: mod.laboratorio.drogas_10.record.get('adm'),
                examen: mod.laboratorio.drogas_10.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
            }
        });
    },
    crea_stores: function () { },
    crea_controles: function () {
//m_lab_drogas_10_cocaina
        this.m_lab_drogas_10_cocaina = new Ext.form.RadioGroup({
            fieldLabel: '<b>COCAINA</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NEGATIVO', name: 'm_lab_drogas_10_cocaina', inputValue: 'NEGATIVO', checked: true},
                {boxLabel: 'POSITIVO', name: 'm_lab_drogas_10_cocaina', inputValue: 'POSITIVO'}
            ]
        });
//m_lab_drogas_10_marihuana
        this.m_lab_drogas_10_marihuana = new Ext.form.RadioGroup({
            fieldLabel: '<b>MARIHUANA</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NEGATIVO', name: 'm_lab_drogas_10_marihuana', inputValue: 'NEGATIVO', checked: true},
                {boxLabel: 'POSITIVO', name: 'm_lab_drogas_10_marihuana', inputValue: 'POSITIVO'}
            ]
        });
//m_lab_drogas_10_benzodiazepina
        this.m_lab_drogas_10_benzodiazepina = new Ext.form.RadioGroup({
            fieldLabel: '<b>BENZODIAZEPINA</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NEGATIVO', name: 'm_lab_drogas_10_benzodiazepina', inputValue: 'NEGATIVO', checked: true},
                {boxLabel: 'POSITIVO', name: 'm_lab_drogas_10_benzodiazepina', inputValue: 'POSITIVO'}
            ]
        });
//m_lab_drogas_10_barbiturico
        this.m_lab_drogas_10_barbiturico = new Ext.form.RadioGroup({
            fieldLabel: '<b>BARBITURICO</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NEGATIVO', name: 'm_lab_drogas_10_barbiturico', inputValue: 'NEGATIVO', checked: true},
                {boxLabel: 'POSITIVO', name: 'm_lab_drogas_10_barbiturico', inputValue: 'POSITIVO'}
            ]
        });
//m_lab_drogas_10_anphetamina
        this.m_lab_drogas_10_anphetamina = new Ext.form.RadioGroup({
            fieldLabel: '<b>ANPHETAMINA</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NEGATIVO', name: 'm_lab_drogas_10_anphetamina', inputValue: 'NEGATIVO', checked: true},
                {boxLabel: 'POSITIVO', name: 'm_lab_drogas_10_anphetamina', inputValue: 'POSITIVO'}
            ]
        });
//m_lab_drogas_10_metadona
        this.m_lab_drogas_10_metadona = new Ext.form.RadioGroup({
            fieldLabel: '<b>METADONA</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NEGATIVO', name: 'm_lab_drogas_10_metadona', inputValue: 'NEGATIVO', checked: true},
                {boxLabel: 'POSITIVO', name: 'm_lab_drogas_10_metadona', inputValue: 'POSITIVO'}
            ]
        });
//m_lab_drogas_10_methamphentamina
        this.m_lab_drogas_10_methamphentamina = new Ext.form.RadioGroup({
            fieldLabel: '<b>METHAMPHENTAMINA 1000</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NEGATIVO', name: 'm_lab_drogas_10_methamphentamina', inputValue: 'NEGATIVO', checked: true},
                {boxLabel: 'POSITIVO', name: 'm_lab_drogas_10_methamphentamina', inputValue: 'POSITIVO'}
            ]
        });
//m_lab_drogas_10_mdma
        this.m_lab_drogas_10_mdma = new Ext.form.RadioGroup({
            fieldLabel: '<b>MDMA (XTC)</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NEGATIVO', name: 'm_lab_drogas_10_mdma', inputValue: 'NEGATIVO', checked: true},
                {boxLabel: 'POSITIVO', name: 'm_lab_drogas_10_mdma', inputValue: 'POSITIVO'}
            ]
        });
//m_lab_drogas_10_morphina
        this.m_lab_drogas_10_morphina = new Ext.form.RadioGroup({
            fieldLabel: '<b>MORPHINA 300</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NEGATIVO', name: 'm_lab_drogas_10_morphina', inputValue: 'NEGATIVO', checked: true},
                {boxLabel: 'POSITIVO', name: 'm_lab_drogas_10_morphina', inputValue: 'POSITIVO'}
            ]
        });
//m_lab_drogas_10_phecyclidine
        this.m_lab_drogas_10_phecyclidine = new Ext.form.RadioGroup({
            fieldLabel: '<b>PHECYCLIDINE</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'NEGATIVO', name: 'm_lab_drogas_10_phecyclidine', inputValue: 'NEGATIVO', checked: true},
                {boxLabel: 'POSITIVO', name: 'm_lab_drogas_10_phecyclidine', inputValue: 'POSITIVO'}
            ]
        });

        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
//            frame: true,
            layout: 'column',
            bodyStyle: 'padding:2px 2px 2px 2px;',
            labelWidth: 99,
            labelAlign: 'top',
            items: [{
                    xtype: 'panel', border: false,
                    columnWidth: .50,
                    labelWidth: 1,
                    bodyStyle: 'padding:15px 20px 0px 20px;',
                    items: [{
                            xtype: 'fieldset', layout: 'column',
                            title: 'EXAMEN',
                            items: [{
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 99,
                                    items: [this.m_lab_drogas_10_cocaina]
                                }, {
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 99,
                                    items: [this.m_lab_drogas_10_marihuana]
                                }, {
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 99,
                                    items: [this.m_lab_drogas_10_benzodiazepina]
                                }, {
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 99,
                                    items: [this.m_lab_drogas_10_barbiturico]
                                }, {
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 99,
                                    items: [this.m_lab_drogas_10_anphetamina]
                                }]
                        }]
                }, {
                    xtype: 'panel', border: false,
                    columnWidth: .50,
                    labelWidth: 1,
                    bodyStyle: 'padding:15px 20px 0px 20px;',
                    items: [{
                            xtype: 'fieldset', layout: 'column',
                            title: 'EXAMEN',
                            items: [{
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 99,
                                    items: [this.m_lab_drogas_10_metadona]
                                }, {
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 99,
                                    items: [this.m_lab_drogas_10_methamphentamina]
                                }, {
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 99,
                                    items: [this.m_lab_drogas_10_mdma]
                                }, {
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 99,
                                    items: [this.m_lab_drogas_10_morphina]
                                }, {
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 99,
                                    items: [this.m_lab_drogas_10_phecyclidine]
                                }]
                        }]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.laboratorio.drogas_10.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record.get('st') >= 1) ? 'update_lab_drogas_01' : 'save_lab_drogas_01'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
                                if (action.result.success === true) {
                                    if (action.result.total === 1) {
//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
                                        mod.laboratorio.formatos.st.reload();
                                        mod.laboratorio.st.reload();
                                        mod.laboratorio.drogas_10.win.el.unmask();
                                        mod.laboratorio.drogas_10.win.close();
                                    } else if (action.result.total === 0) {
                                        mod.laboratorio.drogas_10.win.el.unmask();
                                        mod.laboratorio.drogas_10.win.close();
                                    }
                                } else {
                                    Ext.Msg.show({
                                        title: 'Error',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR,
                                        msg: 'Problemas con el registro.'
                                    });
                                }
                            },
                            failure: function (form, action) {
                                mod.laboratorio.drogas_10.win.el.unmask();
                                mod.laboratorio.drogas_10.win.close();
                                mod.laboratorio.formatos.st.reload();
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
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 570,
            height: 420,
            border: false,
            modal: true,
            title: 'REGISTRO DE EXAMEN: ' + this.record.get('ex_desc'),
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};

Ext.onReady(mod.laboratorio.init, mod.laboratorio);