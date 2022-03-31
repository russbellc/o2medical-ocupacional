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

Ext.ns('mod.oftalmo');
mod.oftalmo = {
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
                    this.baseParams.columna = mod.oftalmo.descripcion.getValue();
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
//        this.tbar = new Ext.Toolbar({
//            items: ['Buscar:', this.descripcion,
//                this.buscador, '->',
//                '|', {
//                    text: 'Reporte x Fecha',
//                    iconCls: 'reporte',
//                    handler: function () {
//                        mod.oftalmo.rfecha.init(null);
//                    }
//                }, '|'
//            ]
//        });
        this.dt_grid = new Ext.grid.GridPanel({
            store: this.st,
//            tbar: this.tbar,
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
                    if (record.get('st') >= 1) {
                        mod.oftalmo.formatos.init(record);
                    } else {
                        mod.oftalmo.formatos.init(record);
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
mod.oftalmo.formatos = {
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
            mod.oftalmo.formatos.imgStore.removeAll();
            var store = mod.oftalmo.formatos.imgStore;
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
                    this.baseParams.adm = mod.oftalmo.formatos.record.get('adm');
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
                    if (record.get('ex_id') == 11) {//examen psicologia
                        mod.oftalmo.oftalmo_oftalmo.init(record);
                    } else {
                        mod.oftalmo.oftalmo_pred.init(record);//
                    }
//                    mod.oftalmo.oftalmo_pred.init(record);//
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    if (record.get('st') == "1") {
                        if (record.get('ex_id') == 11) {   //PSICOLOGIA - LAS BAMBAS
                            new Ext.menu.Menu({
                                items: [{
                                        text: 'INFORME OFTALMOLOGIA N°: <B>' + record.get('adm') + '<B>',
                                        iconCls: 'reporte',
                                        handler: function () {
                                            if (record.get('st') >= 1) {
                                                new Ext.Window({
                                                    title: 'INFORME OFTALMOLOGIA N° ' + record.get('adm'),
                                                    width: 800,
                                                    height: 600,
                                                    maximizable: true,
                                                    modal: true,
                                                    closeAction: 'close',
                                                    resizable: true,
                                                    html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_oftalmologia&sys_report=formato_oftalmo&adm=" + record.get('adm') + "'></iframe>"
                                                }).show();
                                            } else {
                                                Ext.MessageBox.alert('Observaciones', 'El paciente no fue registrado correctamente');
                                            }
                                        }
                                    }]
                            }).showAt(event.xy);
                        } else if (record.get('ex_id') == 7) {   //PSICOLOGIA - LAS BAMBAS
                            new Ext.menu.Menu({
                                items: [{
                                        text: 'EXAMEN PSICOLOGICO N°: <B>' + record.get('adm') + '<B>',
                                        iconCls: 'reporte',
                                        handler: function () {
                                            if (record.get('st') >= 1) {
                                                new Ext.Window({
                                                    title: 'EXAMEN PSICOLOGICO N° ' + record.get('adm'),
                                                    width: 800,
                                                    height: 600,
                                                    maximizable: true,
                                                    modal: true,
                                                    closeAction: 'close',
                                                    resizable: true,
                                                    html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_psicologia&sys_report=formato_oftalmo_oftalmo&adm=" + record.get('adm') + "'></iframe>"
                                                }).show();
                                            } else {
                                                Ext.MessageBox.alert('Observaciones', 'El paciente no fue registrado correctamente');
                                            }
                                        }
                                    }]
                            }).showAt(event.xy);
                        }
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
            title: 'EXAMEN DE MÉDICINA: ' + this.record.get('nombre'),
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

mod.oftalmo.oftalmo_pred = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_controles();
        if (this.record !== null) {
            this.cargar_data();
        }
        this.win.show();
    },
    cargar_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_oftalmo_pred',
                format: 'json',
                adm: mod.oftalmo.oftalmo_pred.record.get('adm'),
                examen: mod.oftalmo.oftalmo_pred.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.oftalmo.anexo_16a.val_medico.setValue(r.val_medico);
//                mod.oftalmo.anexo_16a.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_controles: function () {
        this.m_oft_pred_resultado = new Ext.form.TextField({
            fieldLabel: '<b>RESULTADO DEL EXAMEN</b>',
            allowBlank: false,
            name: 'm_oft_pred_resultado',
            anchor: '96%'
        });
        this.m_oft_pred_observaciones = new Ext.form.TextArea({
            fieldLabel: '<b>OBSERVACIONES</b>',
            name: 'm_oft_pred_observaciones',
            anchor: '99%',
            height: 40
        });
        this.m_oft_pred_diagnostico = new Ext.form.TextArea({
            fieldLabel: '<b>DIAGNOSTICO</b>',
            name: 'm_oft_pred_diagnostico',
            anchor: '99%',
            height: 40
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
                    items: [this.m_oft_pred_resultado]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_oft_pred_observaciones]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_oft_pred_diagnostico]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.oftalmo.oftalmo_pred.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record.get('st') >= 1) ? 'update_oftalmo_pred' : 'save_oftalmo_pred'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function () {
//                                Ext.MessageBox.alert('En hora buena', 'El servicio se registro correctamente');
                                mod.oftalmo.formatos.st.reload();
                                mod.oftalmo.st.reload();
                                mod.oftalmo.oftalmo_pred.win.el.unmask();
                                mod.oftalmo.oftalmo_pred.win.close();
                            },
                            failure: function (form, action) {
                                mod.oftalmo.oftalmo_pred.win.el.unmask();
                                mod.oftalmo.oftalmo_pred.win.close();
                                mod.oftalmo.formatos.st.reload();
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
            width: 500,
            height: 320,
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

mod.oftalmo.oftalmo_oftalmo = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        this.list_diag.load();
        this.list_reco.load();
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
                acction: 'load_oftalmo_oftalmo',
                format: 'json',
                adm: mod.oftalmo.oftalmo_oftalmo.record.get('adm')
//                ,examen: mod.oftalmo.oftalmo_oftalmo.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.oftalmo.oftalmo_oftalmo.val_medico.setValue(r.val_medico);
//                mod.oftalmo.oftalmo_oftalmo.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_stores: function () {

        this.list_diag = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_diag',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['diag_ofta_id', 'diag_ofta_adm', 'diag_ofta_desc'],
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.adm = mod.oftalmo.oftalmo_oftalmo.record.get('adm');
                }
            }
        });
        this.list_reco = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_reco',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['reco_ofta_id', 'reco_ofta_adm', 'reco_ofta_desc'],
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.adm = mod.oftalmo.oftalmo_oftalmo.record.get('adm');
                }
            }
        });
    },
    crea_controles: function () {


        //oftalmo_correctores
        this.m_oft_oftalmo_correctores = new Ext.form.RadioGroup({
            fieldLabel: '<b>CORRECTORES OPTICOS AL MOMENTO DEL EXAMEN</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: 'SI', name: 'm_oft_oftalmo_correctores', inputValue: 'SI', checked: true},
                {boxLabel: 'NO', name: 'm_oft_oftalmo_correctores', inputValue: 'NO'}
            ]
        });
        //m_oft_oftalmo_anamnesis
        this.m_oft_oftalmo_anamnesis = new Ext.form.TextArea({
            name: 'm_oft_oftalmo_anamnesis',
            fieldLabel: '<b>ANAMNESIS Y ANTECEDENTES</b>',
            anchor: '98%',
            value: 'NINGUNA',
            height: 60
        });
        //m_oft_oftalmo_patologia
        this.m_oft_oftalmo_patologia = new Ext.form.TextField({
            fieldLabel: '<b>PATOLOGIA OFTALMOLOGICA ACTUAL</b>',
            name: 'm_oft_oftalmo_patologia',
            value: 'NORMAL',
            anchor: '95%'
        });
        //m_oft_oftalmo_campos_v_od
        this.m_oft_oftalmo_campos_v_od = new Ext.form.TextField({
            fieldLabel: '<b>OD</b>',
            name: 'm_oft_oftalmo_campos_v_od',
            value: 'NORMAL',
            anchor: '95%'
        });
        //m_oft_oftalmo_campos_v_oi
        this.m_oft_oftalmo_campos_v_oi = new Ext.form.TextField({
            fieldLabel: '<b>OI</b>',
            name: 'm_oft_oftalmo_campos_v_oi',
            value: 'NORMAL',
            anchor: '95%'
        });
        //m_oft_oftalmo_tonometria_od
        this.m_oft_oftalmo_tonometria_od = new Ext.form.TextField({
            fieldLabel: '<b>OD</b>',
            name: 'm_oft_oftalmo_tonometria_od',
            value: '-',
            anchor: '95%'
        });
        //m_oft_oftalmo_tonometria_oi
        this.m_oft_oftalmo_tonometria_oi = new Ext.form.TextField({
            fieldLabel: '<b>OI</b>',
            name: 'm_oft_oftalmo_tonometria_oi',
            value: '-',
            anchor: '95%'
        });
        //m_oft_oftalmo_ishihara
        this.m_oft_oftalmo_ishihara = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [
                    ["NORMAL", 'NORMAL'],
                    ['DISCROMATOPSIA LEVE', 'DISCROMATOPSIA LEVE'],
                    ["DISCROMATOPSIA MODERADA", 'DISCROMATOPSIA MODERADA'],
                    ["DISCROMATOPSIA SEVERA", 'DISCROMATOPSIA SEVERA']
                ]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_ishihara',
            fieldLabel: '<b>TEST DE ISHIHARA</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NORMAL');
                    descripcion.setRawValue('NORMAL');
                }
            }
        });
        //m_oft_oftalmo_anexos
        this.m_oft_oftalmo_anexos = new Ext.form.TextField({
            fieldLabel: '<b>ANEXOS</b>',
            name: 'm_oft_oftalmo_anexos',
            value: '-',
            anchor: '95%'
        });
        //m_oft_oftalmo_campimetria
        this.m_oft_oftalmo_campimetria = new Ext.form.TextField({
            fieldLabel: '<b>CAMPIMETRIA</b>',
            name: 'm_oft_oftalmo_campimetria',
            value: 'CONSERVADA',
            anchor: '95%'
        });
        //m_oft_oftalmo_motilidad
        this.m_oft_oftalmo_motilidad = new Ext.form.TextField({
            fieldLabel: '<b>MOTILIDAD OCULAR</b>',
            name: 'm_oft_oftalmo_motilidad',
            value: 'CONSERVADOS AO',
            anchor: '95%'
        });
        //m_oft_oftalmo_fondo_od
        this.m_oft_oftalmo_fondo_od = new Ext.form.TextField({
            fieldLabel: '<b>OD</b>',
            name: 'm_oft_oftalmo_fondo_od',
            value: '-',
            anchor: '95%'
        });
        //m_oft_oftalmo_fondo_oi
        this.m_oft_oftalmo_fondo_oi = new Ext.form.TextField({
            fieldLabel: '<b>OI</b>',
            name: 'm_oft_oftalmo_fondo_oi',
            value: '-',
            anchor: '95%'
        });
        //m_oft_oftalmo_sincorrec_vlejos_od
        this.m_oft_oftalmo_sincorrec_vlejos_od = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_sincorrec_vlejos_od',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('20/20');
                    descripcion.setRawValue('20/20');
                }
            }
        });
        //m_oft_oftalmo_sincorrec_vlejos_oi
        this.m_oft_oftalmo_sincorrec_vlejos_oi = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_sincorrec_vlejos_oi',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('20/20');
                    descripcion.setRawValue('20/20');
                }
            }
        });
        //m_oft_oftalmo_sincorrec_vcerca_od
        this.m_oft_oftalmo_sincorrec_vcerca_od = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]

            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_sincorrec_vcerca_od',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('20/20');
                    descripcion.setRawValue('20/20');
                }
            }
        });
        //m_oft_oftalmo_sincorrec_vcerca_oi
        this.m_oft_oftalmo_sincorrec_vcerca_oi = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_sincorrec_vcerca_oi',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('20/20');
                    descripcion.setRawValue('20/20');
                }
            }
        });
        //m_oft_oftalmo_sincorrec_binocular
        this.m_oft_oftalmo_sincorrec_binocular = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_sincorrec_binocular',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('20/20');
                    descripcion.setRawValue('20/20');
                }
            }
        });
        //m_oft_oftalmo_concorrec_vlejos_od
        this.m_oft_oftalmo_concorrec_vlejos_od = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_concorrec_vlejos_od',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('-');
                    descripcion.setRawValue('-');
                }
            }
        });
        //m_oft_oftalmo_concorrec_vlejos_oi
        this.m_oft_oftalmo_concorrec_vlejos_oi = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_concorrec_vlejos_oi',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('-');
                    descripcion.setRawValue('-');
                }
            }
        });
        //m_oft_oftalmo_concorrec_vcerca_od
        this.m_oft_oftalmo_concorrec_vcerca_od = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_concorrec_vcerca_od',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('-');
                    descripcion.setRawValue('-');
                }
            }
        });
        //m_oft_oftalmo_concorrec_vcerca_oi
        this.m_oft_oftalmo_concorrec_vcerca_oi = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_concorrec_vcerca_oi',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('-');
                    descripcion.setRawValue('-');
                }
            }
        });
        //m_oft_oftalmo_concorrec_binocular
        this.m_oft_oftalmo_concorrec_binocular = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_concorrec_binocular',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('-');
                    descripcion.setRawValue('-');
                }
            }
        });
        //m_oft_oftalmo_esteno_vlejos_od
        this.m_oft_oftalmo_esteno_vlejos_od = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_esteno_vlejos_od',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('-');
                    descripcion.setRawValue('-');
                }
            }
        });
        //m_oft_oftalmo_esteno_vlejos_oi
        this.m_oft_oftalmo_esteno_vlejos_oi = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_esteno_vlejos_oi',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('-');
                    descripcion.setRawValue('-');
                }
            }
        });
        //m_oft_oftalmo_esteno_vcerca_od
        this.m_oft_oftalmo_esteno_vcerca_od = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_esteno_vcerca_od',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('-');
                    descripcion.setRawValue('-');
                }
            }
        });
        //m_oft_oftalmo_esteno_vcerca_oi
        this.m_oft_oftalmo_esteno_vcerca_oi = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["20/400", '20/400'], ["20/200", '20/200'], ["20/100", '20/100'], ["20/80", '20/80'], ["20/70", '20/70'], ["20/60", '20/60'], ["20/50", '20/50'], ["20/40", '20/40'], ["20/30", '20/30'], ["20/25", '20/25'], ["20/20", '20/20'], ["CD", 'CD'], ["MM", 'MM'], ["NPL", 'NPL'], ["-", '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_esteno_vcerca_oi',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('-');
                    descripcion.setRawValue('-');
                }
            }
        });
        //m_oft_oftalmo_esteropsia
        this.m_oft_oftalmo_esteropsia = new Ext.form.TextField({
            fieldLabel: '<b>PRUEBA DE ESTEREOPSIS(%)</b>',
            name: 'm_oft_oftalmo_esteropsia',
            maskRe: /[\d]/,
            minLength: 1,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '90%'
        });


        this.m_oft_oftalmo_esteropsia_od = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["NORMAL", 'NORMAL'], ['ANORMAL', 'ANORMAL']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_esteropsia_od',
            fieldLabel: '<b>OD</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NORMAL');
                    descripcion.setRawValue('NORMAL');
                }
            }
        });

        this.m_oft_oftalmo_esteropsia_oi = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["NORMAL", 'NORMAL'], ['ANORMAL', 'ANORMAL']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_esteropsia_oi',
            fieldLabel: '<b>OI</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NORMAL');
                    descripcion.setRawValue('NORMAL');
                }
            }
        });
//m_oft_oftalmo_vision_color_od
        this.m_oft_oftalmo_vision_color_od = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_vision_color_od',
            fieldLabel: '<b>OD</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('SI');
                    descripcion.setRawValue('SI');
                }
            }
        });
//m_oft_oftalmo_vision_color_oi
        this.m_oft_oftalmo_vision_color_oi = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_vision_color_oi',
            fieldLabel: '<b>OI</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('SI');
                    descripcion.setRawValue('SI');
                }
            }
        });
//m_oft_oftalmo_ref_pupilar_od
        this.m_oft_oftalmo_ref_pupilar_od = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_ref_pupilar_od',
            fieldLabel: '<b>OD</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('SI');
                    descripcion.setRawValue('SI');
                }
            }
        });
//m_oft_oftalmo_ref_pupilar_oi
        this.m_oft_oftalmo_ref_pupilar_oi = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_ref_pupilar_oi',
            fieldLabel: '<b>OI</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('SI');
                    descripcion.setRawValue('SI');
                }
            }
        });
//m_oft_oftalmo_discromatopsia
        this.m_oft_oftalmo_discromatopsia = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_discromatopsia',
            fieldLabel: '<b>DISCROMATOPSIA</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NO');
                    descripcion.setRawValue('NO');
                }
            }
        });
//m_oft_oftalmo_tipo
        this.m_oft_oftalmo_tipo = new Ext.form.TextField({
            fieldLabel: '<b>TIPO</b>',
            allowBlank: false,
            name: 'm_oft_oftalmo_tipo',
            value: '-',
            anchor: '95%'
        });
//m_oft_oftalmo_verde
        this.m_oft_oftalmo_verde = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_verde',
            fieldLabel: '<b>VERDE</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NO');
                    descripcion.setRawValue('NO');
                }
            }
        });
//m_oft_oftalmo_amarillo
        this.m_oft_oftalmo_amarillo = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_amarillo',
            fieldLabel: '<b>AMARILLO</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NO');
                    descripcion.setRawValue('NO');
                }
            }
        });
//m_oft_oftalmo_rojo
        this.m_oft_oftalmo_rojo = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_rojo',
            fieldLabel: '<b>ROJO</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NO');
                    descripcion.setRawValue('NO');
                }
            }
        });
//m_oft_oftalmo_ametropia
        this.m_oft_oftalmo_ametropia = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_ametropia',
            fieldLabel: '<b>AMETROPIA</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NO');
                    descripcion.setRawValue('NO');
                }
            }
        });
//m_oft_oftalmo_conjuntivitis
        this.m_oft_oftalmo_conjuntivitis = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_conjuntivitis',
            fieldLabel: '<b>CONJUNTIVITIS</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NO');
                    descripcion.setRawValue('NO');
                }
            }
        });
//m_oft_oftalmo_ojo_rojo
        this.m_oft_oftalmo_ojo_rojo = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_ojo_rojo',
            fieldLabel: '<b>OJO ROJO</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NO');
                    descripcion.setRawValue('NO');
                }
            }
        });
//m_oft_oftalmo_catarata
        this.m_oft_oftalmo_catarata = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_catarata',
            fieldLabel: '<b>CATARATAS</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NO');
                    descripcion.setRawValue('NO');
                }
            }
        });
//m_oft_oftalmo_nistagmos
        this.m_oft_oftalmo_nistagmos = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_nistagmos',
            fieldLabel: '<b>NISTAGMON</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NO');
                    descripcion.setRawValue('NO');
                }
            }
        });
//m_oft_oftalmo_pterigion
        this.m_oft_oftalmo_pterigion = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                fields: ['campo', 'descripcion'],
                data: [["SI", 'SI'], ['NO', 'NO'], ['-', '-']]
            }),
            displayField: 'descripcion',
            valueField: 'campo',
            hiddenName: 'm_oft_oftalmo_pterigion',
            fieldLabel: '<b>PTERIGION</b>',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione...',
            selectOnFocus: true,
            anchor: '90%',
            width: 100,
            listeners: {
                afterrender: function (descripcion) {
                    descripcion.setValue('NO');
                    descripcion.setRawValue('NO');
                }
            }
        });
//m_oft_oftalmo_ampliacion
        this.m_oft_oftalmo_ampliacion = new Ext.form.TextField({
            fieldLabel: '<b>AMPLIACIÓN</b>',
            allowBlank: false,
            name: 'm_oft_oftalmo_ampliacion',
            value: '-',
            anchor: '95%'
        });


        this.tbar3 = new Ext.Toolbar({
            items: ['<b style="color:#000000;">DIAGNOSTICOS</b>',
                '-', {
                    text: 'Nuevo',
                    iconCls: 'nuevo',
                    handler: function () {
                        var record = mod.oftalmo.oftalmo_oftalmo.record;
                        mod.oftalmo.diagnostico.init(null);
                    }
                }
            ]
        });
        this.dt_grid3 = new Ext.grid.GridPanel({
            store: this.list_diag,
            region: 'west',
            border: true,
            tbar: this.tbar3,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            height: 260,
            listeners: {
                rowdblclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record2 = grid.getStore().getAt(rowIndex);
                    mod.oftalmo.diagnostico.init(record2);
                }
            },
            autoExpandColumn: 'diag_desc',
            columns: [
                new Ext.grid.RowNumberer(), {
                    id: 'diag_desc',
                    header: 'DIAGNOSTICOS',
                    dataIndex: 'diag_ofta_desc'
                }
            ]
        });

        this.tbar4 = new Ext.Toolbar({
            items: ['->', '<b style="color:#000000;">RECOMENDACIONES Y OBSERVACIONES</b>',
                '-', {
                    text: 'Nuevo',
                    iconCls: 'nuevo',
                    handler: function () {
                        var record = mod.oftalmo.oftalmo_oftalmo.record;
                        mod.oftalmo.recomendaciones.init(null);
                    }
                }
            ]
        });
        this.dt_grid4 = new Ext.grid.GridPanel({
            store: this.list_reco,
            region: 'west',
            border: true,
            tbar: this.tbar4,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            height: 260,
            listeners: {
                rowdblclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record2 = grid.getStore().getAt(rowIndex);
                    mod.oftalmo.recomendaciones.init(record2);
                }
            },
            autoExpandColumn: 'reco_desc',
            columns: [
                new Ext.grid.RowNumberer(), {
                    id: 'reco_desc',
                    header: 'RECOMENDACIONES',
                    dataIndex: 'reco_ofta_desc'
                }
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
            items: [
                {
                    title: '<b>--->  EVALUACIÓN OFTALMOLOGICA </b>',
                    iconCls: 'demo2',
                    layout: 'column',
                    autoScroll: true,
                    border: false,
                    bodyStyle: 'padding:10px 10px 20px 10px;',
//                    labelWidth: 60,
                    items: [
                        {
                            xtype: 'panel', border: false,
                            columnWidth: .999,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
//                                    title: 'ANAMNESIS Y ANTECEDENTES',
                                    items: [{
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_correctores]
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_patologia]
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_anamnesis]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .33,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'CAMPOS VISUALES',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_campos_v_od]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_campos_v_oi]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .33,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'TONOMETRIA',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_tonometria_od]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_tonometria_oi]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .34,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'FONDO DE OJO',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_fondo_od]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_fondo_oi]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'MOTILIDAD',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            html: '<center><img width="320" src="<[sys_images]>/oftalmo/motilidad.svg"></center>'
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_motilidad]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'ANEXOS OCULARES',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            html: '<center><img width="250" src="<[sys_images]>/oftalmo/anexo.svg"></center>'
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_anexos]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'CAMPIMETRIA',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_campimetria]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .33,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'SIN CORREGIR',
                                    items: [
                                        {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:5px 0;width:98px;height:30px;float:left;"></div>\n\
                                                   <div style="padding:5px 0;width:82px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>Ojo Der.</h3></div>\n\
                                                   <div style="padding:5px 0;width:82px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>Ojo Izq.</h3></div>\n\
                                                   '
                                        }, {
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>Vision Lejos:</h3></div>'
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_sincorrec_vlejos_od]
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_sincorrec_vlejos_oi]
                                        }, {
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>Vision Cerca:</h3></div>'
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_sincorrec_vcerca_od]
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_sincorrec_vcerca_oi]
                                        }, {
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>Binocular:</h3></div>'
                                        }, {
                                            columnWidth: .60,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_sincorrec_binocular]
                                        }
                                    ]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .33,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'CORREGIDA',
                                    items: [
                                        {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:5px 0;width:98px;height:30px;float:left;"></div>\n\
                                                   <div style="padding:5px 0;width:82px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>Ojo Der.</h3></div>\n\
                                                   <div style="padding:5px 0;width:82px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>Ojo Izq.</h3></div>\n\
                                                   '
                                        }, {
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>Vision Lejos:</h3></div>'
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_concorrec_vlejos_od]
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_concorrec_vlejos_oi]
                                        }, {
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>Vision Cerca:</h3></div>'
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_concorrec_vcerca_od]
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_concorrec_vcerca_oi]
                                        }, {
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>Binocular:</h3></div>'
                                        }, {
                                            columnWidth: .60,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_concorrec_binocular]
                                        }
                                    ]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .33,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'CON ESTENOPEICO',
                                    items: [
                                        {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:5px 0;width:98px;height:30px;float:left;"></div>\n\
                                                   <div style="padding:5px 0;width:82px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>Ojo Der.</h3></div>\n\
                                                   <div style="padding:5px 0;width:82px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>Ojo Izq.</h3></div>\n\
                                                   '
                                        }, {
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>Vision Lejos:</h3></div>'
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_esteno_vlejos_od]
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_esteno_vlejos_oi]
                                        }, {
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>Vision Cerca:</h3></div>'
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_esteno_vcerca_od]
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_esteno_vcerca_oi]
                                        }
                                    ]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'PRUEBA DE ESTEROPSIA',
                                    items: [{
                                            columnWidth: .60,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            html: '<center><img width="220" src="<[sys_images]>/oftalmo/titmus.png"></center>'
                                        }, {
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            html: '<center><img width="150" src="<[sys_images]>/oftalmo/mosca1.png"></center>'
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_esteropsia_od]
                                        }, {
                                            columnWidth: .30,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_esteropsia_oi]
                                        }, {
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_esteropsia]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'TEST DE ISHIHARA',
                                    items: [{
                                            columnWidth: .40,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            html: '<center><img width="176" src="<[sys_images]>/oftalmo/ishihara.png"></center>'
                                        }, {
                                            xtype: 'panel', border: false,
                                            columnWidth: .60,
                                            bodyStyle: 'padding:2px 15px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: '',
                                                    items: [{
                                                            columnWidth: .999,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_oft_oftalmo_tipo]
                                                        }, {
                                                            columnWidth: .60,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_oft_oftalmo_discromatopsia]
                                                        }, {
                                                            columnWidth: .40,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_oft_oftalmo_verde]
                                                        }, {
                                                            columnWidth: .60,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_oft_oftalmo_amarillo]
                                                        }, {
                                                            columnWidth: .40,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_oft_oftalmo_rojo]
                                                        }]
                                                }]
                                        }, {
                                            columnWidth: .50,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_ishihara]
                                        }, {
                                            xtype: 'panel', border: false,
                                            columnWidth: .50,
                                            bodyStyle: 'padding:2px 15px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: 'VISIÓN DE COLORES',
                                                    items: [{
                                                            columnWidth: .50,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_oft_oftalmo_vision_color_od]
                                                        }, {
                                                            columnWidth: .50,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_oft_oftalmo_vision_color_oi]
                                                        }]
                                                }]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .20,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'REFLEJO PUPILAR',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_ref_pupilar_od]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_ref_pupilar_oi]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .80,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'EXAMENES AUXILIARES',
                                    items: [{
                                            columnWidth: .25,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_ametropia]
                                        }, {
                                            columnWidth: .25,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_conjuntivitis]
                                        }, {
                                            columnWidth: .25,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_ojo_rojo]
                                        }, {
                                            columnWidth: .25,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_catarata]
                                        }, {
                                            columnWidth: .25,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_nistagmos]
                                        }, {
                                            columnWidth: .25,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_pterigion]
                                        }, {
                                            columnWidth: .50,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_oft_oftalmo_ampliacion]
                                        }]
                                }]
                        }, {
                            columnWidth: .50,
                            labelWidth: 1,
                            labelAlign: 'top',
                            layout: 'form',
                            border: false,
                            items: [this.dt_grid3]
                        }, {
                            columnWidth: .50,
                            labelWidth: 1,
                            labelAlign: 'top',
                            layout: 'form',
                            border: false,
                            items: [this.dt_grid4]
                        }]
                }
            ],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.oftalmo.oftalmo_oftalmo.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({params: {
                                acction: (this.record.get('st') >= 1) ? 'update_oftalmo_oftalmo' : 'save_oftalmo_oftalmo'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
//                                obj = Ext.util.JSON.decode(action.response.responseText);
//                                Ext.MessageBox.alert('En hora buena', 'Se registro correctamente');
                                mod.oftalmo.formatos.st.reload();
                                mod.oftalmo.st.reload();
                                mod.oftalmo.oftalmo_oftalmo.win.el.unmask();
                                mod.oftalmo.oftalmo_oftalmo.win.close();
                            },
                            failure: function (form, action) {
                                mod.laboratorio.inform_psicologia.win.el.unmask();
                                mod.laboratorio.inform_psicologia.win.close();
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
            height: 600,
            border: false,
            modal: true,
            title: 'EXAMEN OFTALMOLOGICO: ',
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};

mod.oftalmo.diagnostico = {
    record2: null,
    win: null,
    frm: null, diag_ofta_tipo: null,
    diag_ofta_desc: null,
    init: function (r) {
        this.record2 = r;
        this.crea_stores();
        this.st_busca_diag.load();
        this.crea_controles();
        if (this.record2 !== null) {
            this.cargar_data();
        }
        this.win.show();
    },
    cargar_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_diag',
                format: 'json',
                diag_ofta_id: this.record2.get('diag_ofta_id'),
                diag_ofta_adm: this.record2.get('diag_ofta_adm')
            },
            scope: this, success: function (frm, action) {
                r = action.result.data;
            }});
    },
    crea_stores: function () {
        this.st_busca_diag = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_diag',
                format: 'json'
            },
            fields: ['diag_ofta_desc'],
            root: 'data'});
        this.list_cie10 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_cie10',
                format: 'json'
            },
            fields: ['cie4_id', 'cie4_cie3id', 'cie4_desc'],
            root: 'data'
        });
    },
    crea_controles: function () {
        this.cie10Tpl = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><b>{cie4_desc}</b></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.diag_ofta_tipo = new Ext.form.RadioGroup({
            fieldLabel: '<b>TIPO DE DIAGNOSTICO</b>',
            itemCls: 'x-check-group-alt',
            columns: 1,
            items: [
                {boxLabel: 'TEXTUAL', name: 'diag_ofta_tipo', inputValue: '1', checked: true,
                    handler: function (values, checkbox) {
                        if (checkbox == true) {
                            mod.oftalmo.diagnostico.diag_ofta_desc.enable();
                            mod.oftalmo.diagnostico.diag_ofta_cie.disable();
                            mod.oftalmo.diagnostico.diag_ofta_cie.getValue('');
                            mod.oftalmo.diagnostico.diag_ofta_cie.setRawValue('');
                        }
                    }},
                {boxLabel: 'CIE 10', name: 'diag_ofta_tipo', inputValue: '2',
                    handler: function (values, checkbox) {
                        if (checkbox == true) {
                            mod.oftalmo.diagnostico.diag_ofta_cie.enable();
                            mod.oftalmo.diagnostico.diag_ofta_desc.disable();
                            mod.oftalmo.diagnostico.diag_ofta_desc.setValue('');
                            mod.oftalmo.diagnostico.diag_ofta_desc.setRawValue('');
                        }
                    }}
            ]
        });
        this.diag_ofta_desc = new Ext.form.ComboBox({
            store: this.st_busca_diag,
            hiddenName: 'diag_ofta_desc',
            displayField: 'diag_ofta_desc',
//            disabled: true,
            valueField: 'diag_ofta_desc',
            minChars: 1,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>DIAGNOSTICO</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '99%'});
        this.diag_ofta_cie = new Ext.form.ComboBox({
            store: this.list_cie10,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.cie10Tpl,
            disabled: true,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'diag_ofta_cie',
            displayField: 'cie4_desc',
            valueField: 'cie4_desc',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>Cie 10</b>',
            mode: 'remote',
            style: {
                textTransform: "uppercase"
            },
            anchor: '100%'
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            frame: true,
            layout: 'column',
            bodyStyle: 'padding:10px;',
            labelWidth: 99,
            items: [
                {
                    columnWidth: .25,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.diag_ofta_tipo]
                }, {
                    columnWidth: .75,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.diag_ofta_desc]
                }, {
                    columnWidth: .75,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.diag_ofta_cie]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.oftalmo.diagnostico.win.el.mask('Guardando…', 'x-mask-loading');
                        var metodo;
                        var diag_ofta_id;
                        if (this.record2 !== null) {
                            metodo = 'update';
                            diag_ofta_id = mod.oftalmo.diagnostico.record2.get('diag_ofta_id');
                        } else {
                            metodo = 'save';
                            diag_ofta_id = '1';
                        }

                        this.frm.getForm().submit({params: {
                                acction: metodo + '_diag'
                                , diag_ofta_adm: mod.oftalmo.oftalmo_oftalmo.record.get('adm')
                                , diag_ofta_id: diag_ofta_id
                            },
                            success: function (form, action) {
                                obj = Ext.util.JSON.decode(action.response.responseText);
                                //                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
                                mod.oftalmo.diagnostico.win.el.unmask();
                                mod.oftalmo.oftalmo_oftalmo.list_diag.reload();
                                mod.oftalmo.diagnostico.win.close();
                            },
                            failure: function (form, action) {
                                mod.oftalmo.diagnostico.win.el.unmask();
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
                                mod.oftalmo.oftalmo_oftalmo.list_diag.reload();
                                mod.oftalmo.diagnostico.win.close();
                            }
                        });
                    }
                }]});

        this.win = new Ext.Window({
            width: 680,
            height: 180,
            modal: true,
            title: 'REGISTRO DE DIAGNOSTICOS',
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
mod.oftalmo.recomendaciones = {
    record2: null,
    win: null,
    frm: null,
    reco_ofta_desc: null,
    init: function (r) {
        this.record2 = r;
        this.crea_stores();
        this.st_busca_reco.load();
        this.crea_controles();
        if (this.record2 !== null) {
            this.cargar_data();
        }
        this.win.show();
    },
    cargar_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_reco',
                format: 'json',
                reco_ofta_id: this.record2.get('reco_ofta_id'),
                reco_ofta_adm: this.record2.get('reco_ofta_adm')
            },
            scope: this, success: function (frm, action) {
                r = action.result.data;
            }});
    },
    crea_stores: function () {
        this.st_busca_reco = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_reco',
                format: 'json'
            },
            fields: ['reco_ofta_desc'],
            root: 'data'
        });
    },
    crea_controles: function () {
        this.resultTpl = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><span>{reco_ofta_desc}</span></h3>',
                '</div>',
                '</div></tpl>'
                );

        this.reco_ofta_desc = new Ext.form.ComboBox({
            store: this.st_busca_reco,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.resultTpl,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 3,
            hiddenName: 'reco_ofta_desc',
            displayField: 'reco_ofta_desc',
            valueField: 'reco_ofta_desc',
            allowBlank: false,
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>RECOMENDACIONES</b>',
            mode: 'local',
            anchor: '99%'
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            frame: true,
            layout: 'column',
            bodyStyle: 'padding:5px;',
            labelWidth: 99,
            items: [
                {
                    columnWidth: .999,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.reco_ofta_desc]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.oftalmo.recomendaciones.win.el.mask('Guardando…', 'x-mask-loading');
                        var metodo;
                        var reco_ofta_id;
                        if (this.record2 !== null) {
                            metodo = 'update';
                            reco_ofta_id = mod.oftalmo.recomendaciones.record2.get('reco_ofta_id');
                        } else {
                            metodo = 'save';
                            reco_ofta_id = '';
                        }

                        this.frm.getForm().submit({params: {
                                acction: metodo + '_reco'
                                , reco_ofta_adm: mod.oftalmo.oftalmo_oftalmo.record.get('adm')
                                , reco_ofta_id: reco_ofta_id
                            },
                            success: function (form, action) {
                                obj = Ext.util.JSON.decode(action.response.responseText);
                                //                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
                                mod.oftalmo.recomendaciones.win.el.unmask();
                                mod.oftalmo.oftalmo_oftalmo.list_reco.reload();
                                mod.oftalmo.recomendaciones.win.close();
                            },
                            failure: function (form, action) {
                                mod.oftalmo.recomendaciones.win.el.unmask();
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
                                mod.oftalmo.oftalmo_oftalmo.list_reco.reload();
                                mod.oftalmo.recomendaciones.win.close();
                            }
                        });
                    }
                }]});

        this.win = new Ext.Window({
            width: 950,
            height: 140,
            modal: true,
            title: 'REGISTRO DE RECOMENDACIONES',
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

Ext.onReady(mod.oftalmo.init, mod.oftalmo);