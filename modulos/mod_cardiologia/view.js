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

Ext.ns('mod.cardiologia');
mod.cardiologia = {
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
                    this.baseParams.columna = mod.cardiologia.descripcion.getValue();
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
//                        mod.cardiologia.rfecha.init(null);
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
                        mod.cardiologia.formatos.init(record);
                    } else {
                        mod.cardiologia.formatos.init(record);
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
mod.cardiologia.formatos = {
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
            mod.cardiologia.formatos.imgStore.removeAll();
            var store = mod.cardiologia.formatos.imgStore;
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
                    this.baseParams.adm = mod.cardiologia.formatos.record.get('adm');
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
                    if (record.get('ex_id') == 3) {//examen psicologia
                        mod.cardiologia.cardio_ekg.init(record);
                    } else {
                        mod.cardiologia.cardio_pred.init(record);//
                    }
//                    mod.cardiologia.cardio_pred.init(record);//
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    if (record.get('st') == "1") {
                        if (record.get('ex_id') == 3) {   //PSICOLOGIA - LAS BAMBAS
                            new Ext.menu.Menu({
                                items: [{
                                        text: 'INFORME ELECTROCARDIOGRAMA N°: <B>' + record.get('adm') + '<B>',
                                        iconCls: 'reporte',
                                        handler: function () {
                                            if (record.get('st') >= 1) {
                                                new Ext.Window({
                                                    title: 'INFORME ELECTROCARDIOGRAMA N° ' + record.get('adm'),
                                                    width: 800,
                                                    height: 600,
                                                    maximizable: true,
                                                    modal: true,
                                                    closeAction: 'close',
                                                    resizable: true,
                                                    html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_cardiologia&sys_report=formato_ekg&adm=" + record.get('adm') + "'></iframe>"
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
            title: 'EXAMEN DE CARDIOLOGIA: ' + this.record.get('nombre'),
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

mod.cardiologia.cardio_pred = {
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
                acction: 'load_cardio_pred',
                format: 'json',
                adm: mod.cardiologia.cardio_pred.record.get('adm'),
                examen: mod.cardiologia.cardio_pred.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.cardiologia.anexo_16a.val_medico.setValue(r.val_medico);
//                mod.cardiologia.anexo_16a.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_controles: function () {
        this.m_cardio_pred_resultado = new Ext.form.TextField({
            fieldLabel: '<b>RESULTADO DEL EXAMEN</b>',
            allowBlank: false,
            name: 'm_cardio_pred_resultado',
            anchor: '96%'
        });
        this.m_cardio_pred_observaciones = new Ext.form.TextArea({
            fieldLabel: '<b>OBSERVACIONES</b>',
            name: 'm_cardio_pred_observaciones',
            anchor: '99%',
            height: 40
        });
        this.m_cardio_pred_diagnostico = new Ext.form.TextArea({
            fieldLabel: '<b>DIAGNOSTICO</b>',
            name: 'm_cardio_pred_diagnostico',
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
                    items: [this.m_cardio_pred_resultado]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_cardio_pred_observaciones]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_cardio_pred_diagnostico]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.cardiologia.cardio_pred.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record.get('st') >= 1) ? 'update_cardio_pred' : 'save_cardio_pred'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function () {
//                                Ext.MessageBox.alert('En hora buena', 'El servicio se registro correctamente');
                                mod.cardiologia.formatos.st.reload();
                                mod.cardiologia.st.reload();
                                mod.cardiologia.cardio_pred.win.el.unmask();
                                mod.cardiologia.cardio_pred.win.close();
                            },
                            failure: function (form, action) {
                                mod.cardiologia.cardio_pred.win.el.unmask();
                                mod.cardiologia.cardio_pred.win.close();
                                mod.cardiologia.formatos.st.reload();
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

mod.cardiologia.cardio_ekg = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        this.list_conclusion.load();
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
                acction: 'load_cardio_ekg',
                format: 'json',
                adm: mod.cardiologia.cardio_ekg.record.get('adm')
//                ,examen: mod.cardiologia.cardio_ekg.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.cardiologia.cardio_ekg.val_medico.setValue(r.val_medico);
//                mod.cardiologia.cardio_ekg.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_stores: function () {

        this.list_conclusion = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_conclusion',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['conclusion_cardio_id', 'conclusion_cardio_adm', 'conclusion_cardio_desc'],
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.adm = mod.cardiologia.cardio_ekg.record.get('adm');
                }
            }
        });
    },
    crea_controles: function () {

//m_car_ekg_frec_auricular
        this.m_car_ekg_frec_auricular = new Ext.form.TextField({
            fieldLabel: '<b>FRECUENCIA AURICULAR</b>',
            name: 'm_car_ekg_frec_auricular',
            value: '-',
            anchor: '95%'
        });
//m_car_ekg_frec_ventricular
        this.m_car_ekg_frec_ventricular = new Ext.form.TextField({
            fieldLabel: '<b>FRECUENCIA VENTRICULAR</b>',
            name: 'm_car_ekg_frec_ventricular',
            value: '-',
            anchor: '95%'
        });
//m_car_ekg_ritmo
        this.m_car_ekg_ritmo = new Ext.form.TextField({
            fieldLabel: '<b>RITMO</b>',
            name: 'm_car_ekg_ritmo',
            value: 'SINUSAL',
            anchor: '95%'
        });
//m_car_ekg_intervalo_p_r
        this.m_car_ekg_intervalo_p_r = new Ext.form.TextField({
            fieldLabel: '<b>INTERVALO P-R</b>',
            name: 'm_car_ekg_intervalo_p_r',
            value: '',
            anchor: '95%'
        });
//m_car_ekg_qrs
        this.m_car_ekg_qrs = new Ext.form.TextField({
            fieldLabel: '<b>QRS</b>',
            name: 'm_car_ekg_qrs',
            value: '',
            anchor: '95%'
        });
//m_car_ekg_q_t
        this.m_car_ekg_q_t = new Ext.form.TextField({
            fieldLabel: '<b>Q-T</b>',
            name: 'm_car_ekg_q_t',
            value: '',
            anchor: '95%'
        });
//m_car_ekg_ap
        this.m_car_ekg_ap = new Ext.form.TextField({
            fieldLabel: '<b>AP</b>',
            name: 'm_car_ekg_ap',
            value: '',
            anchor: '95%'
        });
//m_car_ekg_a_qrs
        this.m_car_ekg_a_qrs = new Ext.form.TextField({
            fieldLabel: '<b>A, QRS</b>',
            name: 'm_car_ekg_a_qrs',
            value: '',
            anchor: '95%'
        });
//m_car_ekg_at
        this.m_car_ekg_at = new Ext.form.TextField({
            fieldLabel: '<b>A.T</b>',
            name: 'm_car_ekg_at',
            value: '',
            anchor: '95%'
        });
//m_car_ekg_onda_p
        this.m_car_ekg_onda_p = new Ext.form.TextField({
            fieldLabel: '<b>ONDAS P</b>',
            name: 'm_car_ekg_onda_p',
            value: 'NORMALES',
            anchor: '95%'
        });
//m_car_ekg_complejos_qrs
        this.m_car_ekg_complejos_qrs = new Ext.form.TextField({
            fieldLabel: '<b>COMPLEJOS QRS</b>',
            name: 'm_car_ekg_complejos_qrs',
            value: 'NORMALES',
            anchor: '95%'
        });
//m_car_ekg_segmento_s_t
        this.m_car_ekg_segmento_s_t = new Ext.form.TextField({
            fieldLabel: '<b>SEGMENTOS S-T</b>',
            name: 'm_car_ekg_segmento_s_t',
            value: 'NORMALES',
            anchor: '95%'
        });
//m_car_ekg_onda_t
        this.m_car_ekg_onda_t = new Ext.form.TextField({
            fieldLabel: '<b>ONDAS T</b>',
            name: 'm_car_ekg_onda_t',
            value: 'NORMALES',
            anchor: '95%'
        });
//m_car_ekg_quindina
        this.m_car_ekg_quindina = new Ext.form.TextField({
            fieldLabel: '<b>QUINDINA</b>',
            name: 'm_car_ekg_quindina',
            value: '-',
            anchor: '95%'
        });
//m_car_ekg_otros_hallazgo
        this.m_car_ekg_otros_hallazgo = new Ext.form.TextField({
            fieldLabel: '<b>HALLAZGOS</b>',
            name: 'm_car_ekg_otros_hallazgo',
            value: '-',
            anchor: '95%'
        });
//m_car_ekg_antecedentes 
        this.m_car_ekg_antecedentes = new Ext.form.TextField({
            fieldLabel: '<b>ANTECEDENTES</b>',
            name: 'm_car_ekg_antecedentes',
            value: 'NINGUNO',
            anchor: '95%'
        });
//m_car_ekg_sintomas
        this.m_car_ekg_sintomas = new Ext.form.TextField({
            fieldLabel: '<b>SINTOMAS</b>',
            name: 'm_car_ekg_sintomas',
            value: 'NO',
            anchor: '95%'
        });
//m_car_ekg_descripcion
        this.m_car_ekg_descripcion = new Ext.form.TextField({
            fieldLabel: '<b>DESCRIPCION</b>',
            name: 'm_car_ekg_descripcion',
            value: '-',
            anchor: '95%'
        });




        this.tbar3 = new Ext.Toolbar({
            items: ['<b style="color:#000000;">CONCLUSIONES</b>',
                '-', {
                    text: 'Nuevo',
                    iconCls: 'nuevo',
                    handler: function () {
                        var record = mod.cardiologia.cardio_ekg.record;
                        mod.cardiologia.conclusiones.init(null);
                    }
                }
            ]
        });
        this.dt_grid3 = new Ext.grid.GridPanel({
            store: this.list_conclusion,
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
                    mod.cardiologia.conclusiones.init(record2);
                }
            },
            autoExpandColumn: 'diag_desc',
            columns: [
                new Ext.grid.RowNumberer(), {
                    id: 'diag_desc',
                    header: 'CONCLUSIONES',
                    dataIndex: 'conclusion_cardio_desc'
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
                    title: '<b>--->  ELECTROCARDIOGRAMA </b>',
                    iconCls: 'demo2',
                    layout: 'column',
                    autoScroll: true,
                    border: false,
                    bodyStyle: 'padding:10px 10px 20px 10px;',
//                    labelWidth: 60,
                    items: [
                        {
                            columnWidth: .33,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_frec_auricular]
                        }, {
                            columnWidth: .33,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_frec_ventricular]
                        }, {
                            columnWidth: .34,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_ritmo]
                        }, {
                            columnWidth: .33,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_intervalo_p_r]
                        }, {
                            columnWidth: .33,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_qrs]
                        }, {
                            columnWidth: .34,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_q_t]
                        }, {
                            columnWidth: .33,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_ap]
                        }, {
                            columnWidth: .33,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_a_qrs]
                        }, {
                            columnWidth: .34,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_at]
                        }, {
                            columnWidth: .33,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_onda_p]
                        }, {
                            columnWidth: .33,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_complejos_qrs]
                        }, {
                            columnWidth: .34,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_segmento_s_t]
                        }, {
                            columnWidth: .33,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_onda_t]
                        }, {
                            columnWidth: .33,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_quindina]
                        }, {
                            columnWidth: .34,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_otros_hallazgo]
                        }, {
                            columnWidth: .999,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_antecedentes]
                        }, {
                            columnWidth: .999,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_sintomas]
                        }, {
                            columnWidth: .999,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_car_ekg_descripcion]
                        }, {
                            columnWidth: .999,
                            labelWidth: 1,
                            labelAlign: 'top',
                            layout: 'form',
                            border: false,
                            items: [this.dt_grid3]
                        }
                    ]
                }
            ],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.cardiologia.cardio_ekg.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({params: {
                                acction: (this.record.get('st') >= 1) ? 'update_cardio_ekg' : 'save_cardio_ekg'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
                                if (action.result.success === true) {
                                    if (action.result.total === 1) {
//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
                                        mod.cardiologia.formatos.st.reload();
                                        mod.cardiologia.st.reload();
                                        mod.cardiologia.cardio_ekg.win.el.unmask();
                                        mod.cardiologia.cardio_ekg.win.close();
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
                                mod.cardiologia.cardio_ekg.win.el.unmask();
                                mod.cardiologia.cardio_ekg.win.close();
                                mod.cardiologia.formatos.st.reload();
                                switch (action.failureType) {
                                    case Ext.form.Action.CLIENT_INVALID:
                                        Ext.Msg.alert('Failure', 'Existen valores Invalidos');
                                        break;
                                    case Ext.form.Action.CONNECT_FAILURE:
                                        Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
                                        break;
                                    case Ext.form.Action.SERVER_INVALID:
                                        Ext.Msg.alert('Failure Error mik', action.result.error);
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
            width: 800,
            height: 500,
            border: false,
            modal: true,
            title: 'EXAMEN EKG: ',
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};

mod.cardiologia.conclusiones = {
    record2: null,
    win: null,
    frm: null,
    conclusion_cardio_tipo: null,
    conclusion_cardio_desc: null,
    init: function (r) {
        this.record2 = r;
        this.crea_stores();
        this.st_busca_conclu.load();
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
                acction: 'load_conclu',
                format: 'json',
                conclusion_cardio_adm: this.record2.get('conclusion_cardio_adm'),
                conclusion_cardio_id: this.record2.get('conclusion_cardio_id')
            },
            scope: this, success: function (frm, action) {
                r = action.result.data;
            }});
    },
    crea_stores: function () {
        this.st_busca_conclu = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'st_busca_conclu',
                format: 'json'
            },
            fields: ['conclusion_cardio_desc'],
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
        this.conclusion_cardio_tipo = new Ext.form.RadioGroup({
            fieldLabel: '<b>TIPO DE DIAGNOSTICO</b>',
            itemCls: 'x-check-group-alt',
            columns: 1,
            items: [
                {boxLabel: 'TEXTUAL', name: 'conclusion_cardio_tipo', inputValue: '1', checked: true,
                    handler: function (values, checkbox) {
                        if (checkbox == true) {
                            mod.cardiologia.conclusiones.conclusion_cardio_desc.enable();
                            mod.cardiologia.conclusiones.conclusion_cardio_cie.disable();
                            mod.cardiologia.conclusiones.conclusion_cardio_cie.getValue('');
                            mod.cardiologia.conclusiones.conclusion_cardio_cie.setRawValue('');
                        }
                    }},
                {boxLabel: 'CIE 10', name: 'conclusion_cardio_tipo', inputValue: '2',
                    handler: function (values, checkbox) {
                        if (checkbox == true) {
                            mod.cardiologia.conclusiones.conclusion_cardio_cie.enable();
                            mod.cardiologia.conclusiones.conclusion_cardio_desc.disable();
                            mod.cardiologia.conclusiones.conclusion_cardio_desc.setValue('');
                            mod.cardiologia.conclusiones.conclusion_cardio_desc.setRawValue('');
                        }
                    }}
            ]
        });
        this.conclusion_cardio_desc = new Ext.form.ComboBox({
            store: this.st_busca_conclu,
            hiddenName: 'conclusion_cardio_desc',
            displayField: 'conclusion_cardio_desc',
//            disabled: true,
            valueField: 'conclusion_cardio_desc',
            minChars: 1,
            validateOnBlur: true,
            forceSelection: false,
            autoSelect: false,
            allowBlank: true,
            enableKeyEvents: true,
            selectOnFocus: false,
            fieldLabel: '<b>CONCLUSIONES</b>',
            typeAhead: false,
            hideTrigger: true,
            triggerAction: 'all',
            mode: 'local',
            anchor: '99%'});
        this.conclusion_cardio_cie = new Ext.form.ComboBox({
            store: this.list_cie10,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.cie10Tpl,
            disabled: true,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'conclusion_cardio_cie',
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
                    items: [this.conclusion_cardio_tipo]
                }, {
                    columnWidth: .75,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.conclusion_cardio_desc]
                }, {
                    columnWidth: .75,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.conclusion_cardio_cie]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.cardiologia.conclusiones.win.el.mask('Guardando…', 'x-mask-loading');
                        var metodo;
                        var conclusion_cardio_id;
                        if (this.record2 !== null) {
                            metodo = 'update';
                            conclusion_cardio_id = mod.cardiologia.conclusiones.record2.get('conclusion_cardio_id');
                        } else {
                            metodo = 'save';
                            conclusion_cardio_id = '1';
                        }

                        this.frm.getForm().submit({params: {
                                acction: metodo + '_conclu'
                                , conclusion_cardio_adm: mod.cardiologia.cardio_ekg.record.get('adm')
                                , conclusion_cardio_id: conclusion_cardio_id
                            },
                            success: function (form, action) {
                                obj = Ext.util.JSON.decode(action.response.responseText);
                                //                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
                                mod.cardiologia.conclusiones.win.el.unmask();
                                mod.cardiologia.cardio_ekg.list_conclusion.reload();
                                mod.cardiologia.conclusiones.win.close();
                            },
                            failure: function (form, action) {
                                mod.cardiologia.conclusiones.win.el.unmask();
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
                                mod.cardiologia.cardio_ekg.list_conclusion.reload();
                                mod.cardiologia.conclusiones.win.close();
                            }
                        });
                    }
                }]});

        this.win = new Ext.Window({
            width: 680,
            height: 180,
            modal: true,
            title: 'REGISTRO DE CONCLUSIONES',
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

Ext.onReady(mod.cardiologia.init, mod.cardiologia);