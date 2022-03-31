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

Ext.ns('mod.rayosx');
mod.rayosx = {
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
                    this.baseParams.columna = mod.rayosx.descripcion.getValue();
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
//                        mod.rayosx.rfecha.init(null);
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
                        mod.rayosx.formatos.init(record);
                    } else {
                        mod.rayosx.formatos.init(record);
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
mod.rayosx.formatos = {
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
            mod.rayosx.formatos.imgStore.removeAll();
            var store = mod.rayosx.formatos.imgStore;
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
                    this.baseParams.adm = mod.rayosx.formatos.record.get('adm');
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
                    if (record.get('ex_id') == 13) {//examen psicologia
                        mod.rayosx.rayosx_rayosx.init(record);
                    } else {
                        mod.rayosx.rayosx_pred.init(record);//
                    }
//                    mod.rayosx.rayosx_pred.init(record);//
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    if (record.get('st') == "1") {
                        if (record.get('ex_id') == 13) {   //PSICOLOGIA - LAS BAMBAS
                            new Ext.menu.Menu({
                                items: [{
                                        text: 'INFORME RAYOS X N°: <B>' + record.get('adm') + '<B>',
                                        iconCls: 'reporte',
                                        handler: function () {
                                            if (record.get('st') >= 1) {
                                                new Ext.Window({
                                                    title: 'INFORME RAYOS X N° ' + record.get('adm'),
                                                    width: 800,
                                                    height: 600,
                                                    maximizable: true,
                                                    modal: true,
                                                    closeAction: 'close',
                                                    resizable: true,
                                                    html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_rayosx&sys_report=formato_rayosx&adm=" + record.get('adm') + "'></iframe>"
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

mod.rayosx.rayosx_pred = {
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
                acction: 'load_rayosx_pred',
                format: 'json',
                adm: mod.rayosx.rayosx_pred.record.get('adm'),
                examen: mod.rayosx.rayosx_pred.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.rayosx.anexo_16a.val_medico.setValue(r.val_medico);
//                mod.rayosx.anexo_16a.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_controles: function () {
        this.m_rx_pred_resultado = new Ext.form.TextField({
            fieldLabel: '<b>RESULTADO DEL EXAMEN</b>',
            allowBlank: false,
            name: 'm_rx_pred_resultado',
            anchor: '96%'
        });
        this.m_rx_pred_observaciones = new Ext.form.TextArea({
            fieldLabel: '<b>OBSERVACIONES</b>',
            name: 'm_rx_pred_observaciones',
            anchor: '99%',
            height: 40
        });
        this.m_rx_pred_diagnostico = new Ext.form.TextArea({
            fieldLabel: '<b>DIAGNOSTICO</b>',
            name: 'm_rx_pred_diagnostico',
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
                    items: [this.m_rx_pred_resultado]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_rx_pred_observaciones]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_rx_pred_diagnostico]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.rayosx.rayosx_pred.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record.get('st') >= 1) ? 'update_rayosx_pred' : 'save_rayosx_pred'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function () {
//                                Ext.MessageBox.alert('En hora buena', 'El servicio se registro correctamente');
                                mod.rayosx.formatos.st.reload();
                                mod.rayosx.st.reload();
                                mod.rayosx.rayosx_pred.win.el.unmask();
                                mod.rayosx.rayosx_pred.win.close();
                            },
                            failure: function (form, action) {
                                mod.rayosx.rayosx_pred.win.el.unmask();
                                mod.rayosx.rayosx_pred.win.close();
                                mod.rayosx.formatos.st.reload();
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

mod.rayosx.rayosx_rayosx = {
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
                acction: 'load_rayosx_rayosx',
                format: 'json',
                adm: mod.rayosx.rayosx_rayosx.record.get('adm')
//                ,examen: mod.rayosx.rayosx_rayosx.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.rayosx.rayosx_rayosx.val_medico.setValue(r.val_medico);
//                mod.rayosx.rayosx_rayosx.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_stores: function () {

    },
    crea_controles: function () {
//m_rx_rayosx_n_placa
        this.m_rx_rayosx_n_placa = new Ext.form.TextField({
            fieldLabel: '<b>Nro de placa</b>',
            name: 'm_rx_rayosx_n_placa',
            value: this.record.get('adm'),
            maskRe: /[\d]/,
            anchor: '90%'
        });
//m_rx_rayosx_lector
        this.m_rx_rayosx_lector = new Ext.form.TextField({
            fieldLabel: '<b>Lector</b>',
            name: 'm_rx_rayosx_lector',
//            readOnly: true,
            anchor: '95%'
        });
//m_rx_rayosx_fech_lectura
        this.m_rx_rayosx_fech_lectura = new Ext.form.DateField({
            fieldLabel: '<b>Fecha de Lectura</b>',
            name: 'm_rx_rayosx_fech_lectura',
            format: 'Y-m-d',
            value: new Date(),
            anchor: '90%'
        });
//m_rx_rayosx_calidad
        this.m_rx_rayosx_calidad = new Ext.form.RadioGroup({
            fieldLabel: '<b>Calidad Radiografica</b>',
            itemCls: 'x-check-group-alt',
            columns: 1,
            vertical: true,
            items: [
                {boxLabel: 'Buena', name: 'm_rx_rayosx_calidad', inputValue: 'Buena'},
                {boxLabel: 'Aceptable', name: 'm_rx_rayosx_calidad', checked: true, inputValue: 'Aceptable'},
                {boxLabel: 'Baja Calidad', name: 'm_rx_rayosx_calidad', inputValue: 'Baja Calidad'},
                {boxLabel: 'Inaceptable', name: 'm_rx_rayosx_calidad', inputValue: 'Inaceptable'}
            ]
        });
//m_rx_rayosx_causas
        this.m_rx_rayosx_causas = new Ext.form.RadioGroup({
            fieldLabel: '<b>Causas</b>',
            itemCls: 'x-check-group-alt',
            columns: 4,
            vertical: true,
            items: [
                {boxLabel: 'Sobre Exposicion', name: 'm_rx_rayosx_causas', inputValue: 'Sobre Exposicion'},
                {boxLabel: 'Sub Exposicion', name: 'm_rx_rayosx_causas', inputValue: 'Sub Exposicion'},
                {boxLabel: 'Posicion Centrado', name: 'm_rx_rayosx_causas', checked: true, inputValue: 'Posicion Centrado'},
                {boxLabel: 'Inspiracion Insuficiente', name: 'm_rx_rayosx_causas', inputValue: 'Inspiracion Insuficiente'},
                {boxLabel: 'Escapulas', name: 'm_rx_rayosx_causas', inputValue: 'Escapulas'},
                {boxLabel: 'Artefacto', name: 'm_rx_rayosx_causas', inputValue: 'Artefacto'},
                {boxLabel: 'Otros', name: 'm_rx_rayosx_causas', inputValue: 'Otros'}
            ]
        });
//m_rx_rayosx_coment_tec
        this.m_rx_rayosx_coment_tec = new Ext.form.TextField({
            fieldLabel: '<b>Comentarios sobre defectos tecnicos</b>',
            name: 'm_rx_rayosx_coment_tec',
            anchor: '95%'
        });
        this.m_rx_rayosx_zona_a_der = new Ext.form.CheckboxGroup({
            fieldLabel: '<b>Derecho</b>',
            itemCls: 'x-check-group-alt',
            columns: 1,
            items: [
                {boxLabel: 'Superior', name: 'm_rx_rayosx_zona_a_sup_der', inputValue: '1'},
                {boxLabel: 'Medio', name: 'm_rx_rayosx_zona_a_med_der', inputValue: '1'},
                {boxLabel: 'Inferior', name: 'm_rx_rayosx_zona_a_inf_der', inputValue: '1'}
            ]
        });

        this.m_rx_rayosx_zona_a_izq = new Ext.form.CheckboxGroup({
            fieldLabel: '<b>Izquierdo</b>',
            itemCls: 'x-check-group-alt',
            columns: 1,
            items: [
                {boxLabel: 'Superior', name: 'm_rx_rayosx_zona_a_sup_izq', inputValue: '1'},
                {boxLabel: 'Medio', name: 'm_rx_rayosx_zona_a_med_izq', inputValue: '1'},
                {boxLabel: 'Inferior', name: 'm_rx_rayosx_zona_a_inf_izq', inputValue: '1'}
            ]
        });
//m_rx_rayosx_zona_a_sup_der
//m_rx_rayosx_zona_a_sup_izq
//m_rx_rayosx_zona_a_med_der
//m_rx_rayosx_zona_a_med_izq
//m_rx_rayosx_zona_a_inf_der
//m_rx_rayosx_zona_a_inf_izq
//
//m_rx_rayosx_profusion
        this.m_rx_rayosx_profusion = new Ext.form.RadioGroup({
            fieldLabel: '<b>Profusión</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0/-', name: 'm_rx_rayosx_profusion', inputValue: '0/-'},
                {boxLabel: '1/0', name: 'm_rx_rayosx_profusion', inputValue: '1/0'},
                {boxLabel: '2/1', name: 'm_rx_rayosx_profusion', inputValue: '2/1'},
                {boxLabel: '3/2', name: 'm_rx_rayosx_profusion', inputValue: '3/2'},
                {boxLabel: '0/0', name: 'm_rx_rayosx_profusion', checked: true, inputValue: '0/0'},
                {boxLabel: '1/1', name: 'm_rx_rayosx_profusion', inputValue: '1/1'},
                {boxLabel: '2/2', name: 'm_rx_rayosx_profusion', inputValue: '2/2'},
                {boxLabel: '3/3', name: 'm_rx_rayosx_profusion', inputValue: '3/3'},
                {boxLabel: '0/1', name: 'm_rx_rayosx_profusion', inputValue: '0/1'},
                {boxLabel: '1/2', name: 'm_rx_rayosx_profusion', inputValue: '1/2'},
                {boxLabel: '1/2', name: 'm_rx_rayosx_profusion', inputValue: '1/2'},
                {boxLabel: '3/+', name: 'm_rx_rayosx_profusion', inputValue: '3/+'}
            ]
        });

//m_rx_rayosx_forma_tama_pri
        this.m_rx_rayosx_forma_tama_pri = new Ext.form.RadioGroup({
            fieldLabel: '<b>Primaria</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            vertical: true,
            items: [
                {boxLabel: 'p', name: 'm_rx_rayosx_forma_tama_pri', inputValue: 'p'},
                {boxLabel: 's', name: 'm_rx_rayosx_forma_tama_pri', inputValue: 's'},
                {boxLabel: 'q', name: 'm_rx_rayosx_forma_tama_pri', inputValue: 'q'},
                {boxLabel: 't', name: 'm_rx_rayosx_forma_tama_pri', inputValue: 't'},
                {boxLabel: 'r', name: 'm_rx_rayosx_forma_tama_pri', inputValue: 'r'},
                {boxLabel: 'u', name: 'm_rx_rayosx_forma_tama_pri', inputValue: 'u'}
            ]
        });
//m_rx_rayosx_forma_tama_sec
        this.m_rx_rayosx_forma_tama_sec = new Ext.form.RadioGroup({
            fieldLabel: '<b>Secundaria</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            vertical: true,
            items: [
                {boxLabel: 'p', name: 'm_rx_rayosx_forma_tama_sec', inputValue: 'p'},
                {boxLabel: 's', name: 'm_rx_rayosx_forma_tama_sec', inputValue: 's'},
                {boxLabel: 'q', name: 'm_rx_rayosx_forma_tama_sec', inputValue: 'q'},
                {boxLabel: 't', name: 'm_rx_rayosx_forma_tama_sec', inputValue: 't'},
                {boxLabel: 'r', name: 'm_rx_rayosx_forma_tama_sec', inputValue: 'r'},
                {boxLabel: 'u', name: 'm_rx_rayosx_forma_tama_sec', inputValue: 'u'}
            ]
        });
//m_rx_rayosx_opacidad
        this.m_rx_rayosx_opacidad = new Ext.form.RadioGroup({
            fieldLabel: '<b>Opacidad Grande</b>',
            itemCls: 'x-check-group-alt',
            columns: 1,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_opacidad', inputValue: '0', checked: true},
                {boxLabel: 'A', name: 'm_rx_rayosx_opacidad', inputValue: 'A'},
                {boxLabel: 'B', name: 'm_rx_rayosx_opacidad', inputValue: 'B'},
                {boxLabel: 'C', name: 'm_rx_rayosx_opacidad', inputValue: 'C'}
            ]
        });
//m_rx_rayosx_anormal_pleural
        this.m_rx_rayosx_anormal_pleural = new Ext.form.RadioGroup({
            fieldLabel: '<b>ANORMALIDADES PLEURALES (sI NO HAY ANORMALIDADES PASE A SIMBOLOS*)</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            vertical: true,
            items: [
                {boxLabel: 'NO', name: 'm_rx_rayosx_anormal_pleural', inputValue: 'NO', checked: true},
                {boxLabel: 'SI', name: 'm_rx_rayosx_anormal_pleural', inputValue: 'SI'}
            ]
        });
//m_rx_rayosx_sitio_pared
        this.m_rx_rayosx_sitio_pared = new Ext.form.RadioGroup({
            fieldLabel: '<b>Pared torácica de perfil</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_sitio_pared', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_sitio_pared', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_sitio_pared', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_sitio_pared_calci
        this.m_rx_rayosx_sitio_pared_calci = new Ext.form.RadioGroup({
            fieldLabel: '<b>Pared torácica de perfil</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_sitio_pared_calci', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_sitio_pared_calci', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_sitio_pared_calci', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_sitio_frente
        this.m_rx_rayosx_sitio_frente = new Ext.form.RadioGroup({
            fieldLabel: '<b>De Frente</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_sitio_frente', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_sitio_frente', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_sitio_frente', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_sitio_frente_calci
        this.m_rx_rayosx_sitio_frente_calci = new Ext.form.RadioGroup({
            fieldLabel: '<b>De Frente</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_sitio_frente_calci', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_sitio_frente_calci', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_sitio_frente_calci', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_sitio_diagra
        this.m_rx_rayosx_sitio_diagra = new Ext.form.RadioGroup({
            fieldLabel: '<b>Diafragma</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_sitio_diagra', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_sitio_diagra', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_sitio_diagra', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_sitio_diagra_calci
        this.m_rx_rayosx_sitio_diagra_calci = new Ext.form.RadioGroup({
            fieldLabel: '<b>Diafragma</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_sitio_diagra_calci', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_sitio_diagra_calci', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_sitio_diagra_calci', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_sitio_otros
        this.m_rx_rayosx_sitio_otros = new Ext.form.RadioGroup({
            fieldLabel: '<b>Otro(s) sitio(s)</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_sitio_otros', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_sitio_otros', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_sitio_otros', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_sitio_otros_calci
        this.m_rx_rayosx_sitio_otros_calci = new Ext.form.RadioGroup({
            fieldLabel: '<b>Otro(s) sitio(s)</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_sitio_otros_calci', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_sitio_otros_calci', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_sitio_otros_calci', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_sitio_oblite_calci
        this.m_rx_rayosx_sitio_oblite_calci = new Ext.form.RadioGroup({
            fieldLabel: '<b>Obliteracion del angulo costofrenico</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_sitio_oblite_calci', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_sitio_oblite_calci', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_sitio_oblite_calci', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_exten_0D
        this.m_rx_rayosx_exten_0D = new Ext.form.RadioGroup({
//            fieldLabel: '<b>Obliteracion del angulo costofrenico</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_exten_0D', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_exten_0D', inputValue: 'D'}
            ]
        });
//m_rx_rayosx_exten_0D_123
        this.m_rx_rayosx_exten_0D_123 = new Ext.form.RadioGroup({
//            fieldLabel: '<b>Obliteracion del angulo costofrenico</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '1', name: 'm_rx_rayosx_exten_0D_123', inputValue: '1'},
                {boxLabel: '2', name: 'm_rx_rayosx_exten_0D_123', inputValue: '2'},
                {boxLabel: '3', name: 'm_rx_rayosx_exten_0D_123', inputValue: '3'}
            ]
        });
//m_rx_rayosx_exten_0I
        this.m_rx_rayosx_exten_0I = new Ext.form.RadioGroup({
//            fieldLabel: '<b>Obliteracion del angulo costofrenico</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_exten_0I', inputValue: '0'},
                {boxLabel: 'I', name: 'm_rx_rayosx_exten_0I', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_exten_0I_123
        this.m_rx_rayosx_exten_0I_123 = new Ext.form.RadioGroup({
//            fieldLabel: '<b>Obliteracion del angulo costofrenico</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '1', name: 'm_rx_rayosx_exten_0I_123', inputValue: '1'},
                {boxLabel: '2', name: 'm_rx_rayosx_exten_0I_123', inputValue: '2'},
                {boxLabel: '3', name: 'm_rx_rayosx_exten_0I_123', inputValue: '3'}
            ]
        });
//m_rx_rayosx_ancho_D_abc
        this.m_rx_rayosx_ancho_D_abc = new Ext.form.RadioGroup({
            fieldLabel: '<b>D</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: 'a', name: 'm_rx_rayosx_ancho_D_abc', inputValue: 'a'},
                {boxLabel: 'b', name: 'm_rx_rayosx_ancho_D_abc', inputValue: 'b'},
                {boxLabel: 'c', name: 'm_rx_rayosx_ancho_D_abc', inputValue: 'c'}
            ]
        });
//m_rx_rayosx_ancho_I_abc
        this.m_rx_rayosx_ancho_I_abc = new Ext.form.RadioGroup({
            fieldLabel: '<b>I</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: 'a', name: 'm_rx_rayosx_ancho_I_abc', inputValue: 'a'},
                {boxLabel: 'b', name: 'm_rx_rayosx_ancho_I_abc', inputValue: 'b'},
                {boxLabel: 'c', name: 'm_rx_rayosx_ancho_I_abc', inputValue: 'c'}
            ]
        });
//m_rx_rayosx_pared_perfil
        this.m_rx_rayosx_pared_perfil = new Ext.form.RadioGroup({
            fieldLabel: '<b>De Perfil</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_pared_perfil', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_pared_perfil', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_pared_perfil', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_pared_frente
        this.m_rx_rayosx_pared_frente = new Ext.form.RadioGroup({
            fieldLabel: '<b>De frente</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_pared_frente', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_pared_frente', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_pared_frente', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_calci_perfil
        this.m_rx_rayosx_calci_perfil = new Ext.form.RadioGroup({
            fieldLabel: '<b>De Perfil</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_calci_perfil', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_calci_perfil', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_calci_perfil', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_calci_frente
        this.m_rx_rayosx_calci_frente = new Ext.form.RadioGroup({
            fieldLabel: '<b>De frente</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_calci_frente', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_calci_frente', inputValue: 'D'},
                {boxLabel: 'I', name: 'm_rx_rayosx_calci_frente', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_engro_exten_0D
        this.m_rx_rayosx_engro_exten_0D = new Ext.form.RadioGroup({
//            fieldLabel: '<b>Obliteracion del angulo costofrenico</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_engro_exten_0D', inputValue: '0'},
                {boxLabel: 'D', name: 'm_rx_rayosx_engro_exten_0D', inputValue: 'D'}
            ]
        });
//m_rx_rayosx_engro_exten_0D_123
        this.m_rx_rayosx_engro_exten_0D_123 = new Ext.form.RadioGroup({
//            fieldLabel: '<b>Obliteracion del angulo costofrenico</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '1', name: 'm_rx_rayosx_engro_exten_0D_123', inputValue: '1'},
                {boxLabel: '2', name: 'm_rx_rayosx_engro_exten_0D_123', inputValue: '2'},
                {boxLabel: '3', name: 'm_rx_rayosx_engro_exten_0D_123', inputValue: '3'}
            ]
        });
//m_rx_rayosx_engro_exten_0I
        this.m_rx_rayosx_engro_exten_0I = new Ext.form.RadioGroup({
//            fieldLabel: '<b>Obliteracion del angulo costofrenico</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '0', name: 'm_rx_rayosx_engro_exten_0I', inputValue: '0'},
                {boxLabel: 'I', name: 'm_rx_rayosx_engro_exten_0I', inputValue: 'I'}
            ]
        });
//m_rx_rayosx_engro_exten_0I_123
        this.m_rx_rayosx_engro_exten_0I_123 = new Ext.form.RadioGroup({
//            fieldLabel: '<b>Obliteracion del angulo costofrenico</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            vertical: true,
            items: [
                {boxLabel: '1', name: 'm_rx_rayosx_engro_exten_0I_123', inputValue: '1'},
                {boxLabel: '2', name: 'm_rx_rayosx_engro_exten_0I_123', inputValue: '2'},
                {boxLabel: '3', name: 'm_rx_rayosx_engro_exten_0I_123', inputValue: '3'}
            ]
        });
//m_rx_rayosx_engro_ancho_D_abc
        this.m_rx_rayosx_engro_ancho_D_abc = new Ext.form.RadioGroup({
            fieldLabel: '<b>D</b>',
            itemCls: 'x-check-group-alt',
            columns: 1,
            vertical: true,
            items: [
                {boxLabel: 'a', name: 'm_rx_rayosx_engro_ancho_D_abc', inputValue: 'a'},
                {boxLabel: 'b', name: 'm_rx_rayosx_engro_ancho_D_abc', inputValue: 'b'},
                {boxLabel: 'c', name: 'm_rx_rayosx_engro_ancho_D_abc', inputValue: 'c'}
            ]
        });
//m_rx_rayosx_engro_ancho_I_abc
        this.m_rx_rayosx_engro_ancho_I_abc = new Ext.form.RadioGroup({
            fieldLabel: '<b>I</b>',
            itemCls: 'x-check-group-alt',
            columns: 1,
            vertical: true,
            items: [
                {boxLabel: 'a', name: 'm_rx_rayosx_engro_ancho_I_abc', inputValue: 'a'},
                {boxLabel: 'b', name: 'm_rx_rayosx_engro_ancho_I_abc', inputValue: 'b'},
                {boxLabel: 'c', name: 'm_rx_rayosx_engro_ancho_I_abc', inputValue: 'c'}
            ]
        });
//m_rx_rayosx_simbolo
        this.m_rx_rayosx_simbolo = new Ext.form.RadioGroup({
            fieldLabel: '<b>SIMBOLO</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            vertical: true,
            items: [
                {boxLabel: 'NO', name: 'm_rx_rayosx_simbolo', inputValue: 'NO', checked: true},
                {boxLabel: 'SI', name: 'm_rx_rayosx_simbolo', inputValue: 'SI'}
            ]
        });

        this.m_rx_rayosx_simbolo_det = new Ext.form.CheckboxGroup({
            fieldLabel: '<b>Marque las opciones</b>',
            itemCls: 'x-check-group-alt',
            columns: 15,
            items: [
                {boxLabel: 'aa', name: 'm_rx_rayosx_aa', inputValue: 'aa'},
                {boxLabel: 'at', name: 'm_rx_rayosx_at', inputValue: 'at'},
                {boxLabel: 'ax', name: 'm_rx_rayosx_ax', inputValue: 'ax'},
                {boxLabel: 'bu', name: 'm_rx_rayosx_bu', inputValue: 'bu'},
                {boxLabel: 'ca', name: 'm_rx_rayosx_ca', inputValue: 'ca'},
                {boxLabel: 'cg', name: 'm_rx_rayosx_cg', inputValue: 'cg'},
                {boxLabel: 'cn', name: 'm_rx_rayosx_cn', inputValue: 'cn'},
                {boxLabel: 'co', name: 'm_rx_rayosx_co', inputValue: 'co'},
                {boxLabel: 'cp', name: 'm_rx_rayosx_cp', inputValue: 'cp'},
                {boxLabel: 'cv', name: 'm_rx_rayosx_cv', inputValue: 'cv'},
                {boxLabel: 'di', name: 'm_rx_rayosx_di', inputValue: 'di'},
                {boxLabel: 'ef', name: 'm_rx_rayosx_ef', inputValue: 'ef'},
                {boxLabel: 'em', name: 'm_rx_rayosx_em', inputValue: 'em'},
                {boxLabel: 'es', name: 'm_rx_rayosx_es', inputValue: 'es'},
                {boxLabel: 'od', name: 'm_rx_rayosx_od', inputValue: 'od'},
                {boxLabel: 'fr', name: 'm_rx_rayosx_fr', inputValue: 'fr'},
                {boxLabel: 'hi', name: 'm_rx_rayosx_hi', inputValue: 'hi'},
                {boxLabel: 'ho', name: 'm_rx_rayosx_ho', inputValue: 'ho'},
                {boxLabel: 'id', name: 'm_rx_rayosx_ids', inputValue: 'id'},
                {boxLabel: 'ih', name: 'm_rx_rayosx_ih', inputValue: 'ih'},
                {boxLabel: 'kl', name: 'm_rx_rayosx_kl', inputValue: 'kl'},
                {boxLabel: 'me', name: 'm_rx_rayosx_me', inputValue: 'me'},
                {boxLabel: 'pa', name: 'm_rx_rayosx_pa', inputValue: 'pa'},
                {boxLabel: 'pb', name: 'm_rx_rayosx_pb', inputValue: 'pb'},
                {boxLabel: 'pi', name: 'm_rx_rayosx_pi', inputValue: 'pi'},
                {boxLabel: 'px', name: 'm_rx_rayosx_px', inputValue: 'px'},
                {boxLabel: 'ra', name: 'm_rx_rayosx_ra', inputValue: 'ra'},
                {boxLabel: 'rp', name: 'm_rx_rayosx_rp', inputValue: 'rp'},
                {boxLabel: 'tb', name: 'm_rx_rayosx_tb', inputValue: 'tb'}
            ]
        });

//m_rx_rayosx_coment
        this.m_rx_rayosx_coment = new Ext.form.TextArea({
            name: 'm_rx_rayosx_coment',
            fieldLabel: 'Comentarios',
            anchor: '100%',
            height: 60,
            style: {
                //textTransform: "uppercase"
            }
        });
//m_rx_rayosx_obs
        this.m_rx_rayosx_obs = new Ext.form.TextArea({
            name: 'm_rx_rayosx_obs',
            fieldLabel: '<b>DESCRIBIR ANORMALIDADES ENCONTRADAS</b>',
            anchor: '98%',
            height: 60,
            style: {
                //textTransform: "uppercase"
            }
        });
//m_rx_rayosx_concluciones
        this.m_rx_rayosx_concluciones = new Ext.form.TextArea({
            name: 'm_rx_rayosx_concluciones',
            fieldLabel: '<b>CONCLUSIONES RADIOGRAFICAS</b>',
            anchor: '98%',
            height: 60,
            style: {
                //textTransform: "uppercase"
            }
        });

//m_rx_rayosx_vertice
        this.m_rx_rayosx_vertice = new Ext.form.TextField({
            fieldLabel: '<b>VERTICE</b>',
            allowBlank: false,
            name: 'm_rx_rayosx_vertice',
            value: 'SIN ALTERACIONES',
            anchor: '90%'
        });
//m_rx_rayosx_mediastinos
        this.m_rx_rayosx_mediastinos = new Ext.form.TextField({
            fieldLabel: '<b>MEDIASTINOS</b>',
            allowBlank: false,
            name: 'm_rx_rayosx_mediastinos',
            value: 'NORMALES',
            anchor: '90%'
        });
//m_rx_rayosx_camp_pulmo
        this.m_rx_rayosx_camp_pulmo = new Ext.form.TextField({
            fieldLabel: '<b>CAMPOS PULMONARES</b>',
            allowBlank: false,
            name: 'm_rx_rayosx_camp_pulmo',
            value: 'PARENQUIMA PULMONAR CONSERVADO',
            anchor: '90%'
        });
//m_rx_rayosx_silueta_card
        this.m_rx_rayosx_silueta_card = new Ext.form.TextField({
            fieldLabel: '<b>SILUETA CARDIOVASCULAR</b>',
            allowBlank: false,
            name: 'm_rx_rayosx_silueta_card',
            value: 'DIMENSIONES NORMALES',
            anchor: '90%'
        });
//m_rx_rayosx_hilos
        this.m_rx_rayosx_hilos = new Ext.form.TextField({
            fieldLabel: '<b>HILOS</b>',
            allowBlank: false,
            name: 'm_rx_rayosx_hilos',
            value: 'CONSERVADO',
            anchor: '90%'
        });
//m_rx_rayosx_senos
        this.m_rx_rayosx_senos = new Ext.form.TextField({
            fieldLabel: '<b>SENOS</b>',
            allowBlank: false,
            name: 'm_rx_rayosx_senos',
            value: 'LIBRES, NO EFUSION PLEURAL.',
            anchor: '90%'
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
                    title: '<b>--->  I. CALIDAD RADIOGRAFICA  -  II. ANORMALIDADES PARENQUIMOSAS </b>',
                    iconCls: 'demo2',
                    layout: 'column',
                    autoScroll: true,
                    border: false,
                    bodyStyle: 'padding:10px 10px 20px 10px;',
//                    labelWidth: 60,
                    items: [{
                            xtype: 'panel', border: false,
                            columnWidth: .25,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'CALIDAD RADIOGRAFICA',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_calidad]
                                        }]
                                }]
                        }, {
                            columnWidth: .20,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_rx_rayosx_n_placa]
                        }, {
                            columnWidth: .35,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_rx_rayosx_lector]
                        }, {
                            columnWidth: .20,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_rx_rayosx_fech_lectura]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .75,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'CAUSAS',
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_causas]
                                        }]
                                }]
                        }, {
                            columnWidth: .75,
                            border: false,
                            layout: 'form',
                            labelAlign: 'top',
                            items: [this.m_rx_rayosx_coment_tec]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .999,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: '    II. ANORMALIDADES PARENQUIMOSAS (si NO hay anormalidades parenquimosas pase a III. A. Pleurales)',
                                    items: [{
                                            xtype: 'panel', border: false,
                                            columnWidth: .25,
                                            bodyStyle: 'padding:2px 22px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: '2.1 Zonas afectadas (marque)',
                                                    items: [{
                                                            columnWidth: .50,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_zona_a_der]
                                                        }, {
                                                            columnWidth: .50,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_zona_a_izq]
                                                        }]
                                                }]
                                        }, {
                                            xtype: 'panel', border: false,
                                            columnWidth: .30,
                                            bodyStyle: 'padding:2px 22px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: '2.2 Profusión',
                                                    items: [{
                                                            columnWidth: .999,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_profusion]
                                                        }]
                                                }]
                                        }, {
                                            xtype: 'panel', border: false,
                                            columnWidth: .25,
                                            bodyStyle: 'padding:2px 22px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: '2.3 Forma y tamaño',
                                                    items: [{
                                                            columnWidth: .50,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_forma_tama_pri]
                                                        }, {
                                                            columnWidth: .50,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_forma_tama_sec]
                                                        }]
                                                }]
                                        }, {
                                            xtype: 'panel', border: false,
                                            columnWidth: .20,
                                            bodyStyle: 'padding:2px 22px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: '2.4 Opacidad grande',
                                                    items: [{
                                                            columnWidth: .999,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_opacidad]
                                                        }]
                                                }]
                                        }]
                                }]
                        }]
                }, {
                    title: '<b>--->  III. ANORMALIDADES PLEURALES  -  IV. SIMBOLOS</b>',
                    iconCls: 'demo2',
                    layout: 'column',
                    autoScroll: true,
                    border: false,
                    bodyStyle: 'padding:10px 10px 20px 10px;',
                    labelWidth: 60,
                    items: [{
                            xtype: 'panel', border: false,
                            columnWidth: .999,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'III. ANORMALIDADES PLEURALES',
                                    items: [
                                        {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 60,
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_anormal_pleural]
                                        }, {
                                            xtype: 'panel', border: false,
                                            columnWidth: .30,
                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: 'PLACAS PLEURALES - SITIO',
                                                    items: [{
                                                            columnWidth: .99,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_sitio_pared]
                                                        }, {
                                                            columnWidth: .99,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_sitio_frente]
                                                        }, {
                                                            columnWidth: .99,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_sitio_diagra]
                                                        }, {
                                                            columnWidth: .99,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_sitio_otros]
                                                        }]
                                                }]
                                        }, {
                                            xtype: 'panel', border: false,
                                            columnWidth: .30,
                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: 'PLACAS PLEURALES - CALCIFICACION',
                                                    items: [{
                                                            columnWidth: .99,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_sitio_pared_calci]
                                                        }, {
                                                            columnWidth: .99,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_sitio_frente_calci]
                                                        }, {
                                                            columnWidth: .99,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_sitio_diagra_calci]
                                                        }, {
                                                            columnWidth: .99,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_sitio_otros_calci]
                                                        }, {
                                                            columnWidth: .99,
                                                            border: false,
                                                            layout: 'form',
                                                            labelAlign: 'top',
                                                            items: [this.m_rx_rayosx_sitio_oblite_calci]
                                                        }]
                                                }]
                                        }, {
                                            xtype: 'panel', border: false,
                                            columnWidth: .40,
                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: 'PLACAS PLEURALES - EXTENCION',
                                                    items: [{
                                                            xtype: 'panel', border: false,
                                                            columnWidth: .50,
                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                            items: [{
                                                                    xtype: 'fieldset', layout: 'column',
                                                                    items: [{
                                                                            columnWidth: .999,
                                                                            border: false,
                                                                            layout: 'form',
                                                                            labelAlign: 'top',
                                                                            items: [this.m_rx_rayosx_exten_0D]
                                                                        }, {
                                                                            columnWidth: .999,
                                                                            border: false,
                                                                            layout: 'form',
                                                                            labelAlign: 'top',
                                                                            items: [this.m_rx_rayosx_exten_0D_123]
                                                                        }]
                                                                }]
                                                        }, {
                                                            xtype: 'panel', border: false,
                                                            columnWidth: .50,
                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                            items: [{
                                                                    xtype: 'fieldset', layout: 'column',
                                                                    items: [{
                                                                            columnWidth: .999,
                                                                            border: false,
                                                                            layout: 'form',
                                                                            labelAlign: 'top',
                                                                            items: [this.m_rx_rayosx_exten_0I]
                                                                        }, {
                                                                            columnWidth: .999,
                                                                            border: false,
                                                                            layout: 'form',
                                                                            labelAlign: 'top',
                                                                            items: [this.m_rx_rayosx_exten_0I_123]
                                                                        }]
                                                                }]
                                                        }]
                                                }]
                                        }, {
                                            xtype: 'panel', border: false,
                                            columnWidth: .40,
                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: 'PLACAS PLEURALES - ANCHO',
                                                    items: [{
                                                            xtype: 'panel', border: false,
                                                            columnWidth: .50,
                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                            items: [{
                                                                    xtype: 'fieldset', layout: 'column',
                                                                    title: 'D',
                                                                    items: [{
                                                                            columnWidth: .999,
                                                                            border: false,
                                                                            layout: 'form',
                                                                            labelAlign: 'top',
                                                                            items: [this.m_rx_rayosx_ancho_D_abc]
                                                                        }]
                                                                }]
                                                        }, {
                                                            xtype: 'panel', border: false,
                                                            columnWidth: .50,
                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                            items: [{
                                                                    xtype: 'fieldset', layout: 'column',
                                                                    title: 'I',
                                                                    items: [{
                                                                            columnWidth: .999,
                                                                            border: false,
                                                                            layout: 'form',
                                                                            labelAlign: 'top',
                                                                            items: [this.m_rx_rayosx_ancho_I_abc]
                                                                        }]
                                                                }]
                                                        }]
                                                }]
                                        }, {
                                            xtype: 'panel', border: false,
                                            columnWidth: .999,
                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                            items: [{
                                                    xtype: 'fieldset', layout: 'column',
                                                    title: '3.2 Engrosamiento difuso de la pleura',
                                                    items: [{
                                                            xtype: 'panel', border: false,
                                                            columnWidth: .25,
                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                            items: [{
                                                                    xtype: 'fieldset', layout: 'column',
                                                                    title: 'Pared Toracica',
                                                                    items: [{
                                                                            columnWidth: .99,
                                                                            border: false,
                                                                            layout: 'form',
                                                                            labelAlign: 'top',
                                                                            items: [this.m_rx_rayosx_pared_perfil]
                                                                        }, {
                                                                            columnWidth: .99,
                                                                            border: false,
                                                                            layout: 'form',
                                                                            labelAlign: 'top',
                                                                            items: [this.m_rx_rayosx_pared_frente]
                                                                        }]
                                                                }]
                                                        }, {
                                                            xtype: 'panel', border: false,
                                                            columnWidth: .25,
                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                            items: [{
                                                                    xtype: 'fieldset', layout: 'column',
                                                                    title: 'Calcificacion',
                                                                    items: [{
                                                                            columnWidth: .99,
                                                                            border: false,
                                                                            layout: 'form',
                                                                            labelAlign: 'top',
                                                                            items: [this.m_rx_rayosx_calci_perfil]
                                                                        }, {
                                                                            columnWidth: .99,
                                                                            border: false,
                                                                            layout: 'form',
                                                                            labelAlign: 'top',
                                                                            items: [this.m_rx_rayosx_calci_frente]
                                                                        }]
                                                                }]
                                                        }, {
                                                            xtype: 'panel', border: false,
                                                            columnWidth: .25,
                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                            labelWidth: 2,
                                                            items: [{
                                                                    xtype: 'fieldset', layout: 'column',
                                                                    title: 'EXTENCION',
                                                                    items: [{
                                                                            xtype: 'panel', border: false,
                                                                            columnWidth: .50,
                                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                                            items: [{
                                                                                    xtype: 'fieldset', layout: 'column',
                                                                                    items: [{
                                                                                            columnWidth: .999,
                                                                                            border: false,
                                                                                            layout: 'form',
//                                                                                            labelAlign: 'top',
                                                                                            items: [this.m_rx_rayosx_engro_exten_0D]
                                                                                        }, {
                                                                                            columnWidth: .999,
                                                                                            border: false,
                                                                                            layout: 'form',
//                                                                                            labelAlign: 'top',
                                                                                            items: [this.m_rx_rayosx_engro_exten_0D_123]
                                                                                        }]
                                                                                }]
                                                                        }, {
                                                                            xtype: 'panel', border: false,
                                                                            columnWidth: .50,
                                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                                            items: [{
                                                                                    xtype: 'fieldset', layout: 'column',
                                                                                    items: [{
                                                                                            columnWidth: .999,
                                                                                            border: false,
                                                                                            layout: 'form',
//                                                                                            labelAlign: 'top',
                                                                                            items: [this.m_rx_rayosx_engro_exten_0I]
                                                                                        }, {
                                                                                            columnWidth: .999,
                                                                                            border: false,
                                                                                            layout: 'form',
//                                                                                            labelAlign: 'top',
                                                                                            items: [this.m_rx_rayosx_engro_exten_0I_123]
                                                                                        }]
                                                                                }]
                                                                        }]
                                                                }]
                                                        }, {
                                                            xtype: 'panel', border: false,
                                                            columnWidth: .25,
                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                            labelWidth: 2,
                                                            items: [{
                                                                    xtype: 'fieldset', layout: 'column',
                                                                    title: 'ANCHO',
                                                                    items: [{
                                                                            xtype: 'panel', border: false,
                                                                            columnWidth: .50,
                                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                                            items: [{
                                                                                    xtype: 'fieldset', layout: 'column',
                                                                                    title: 'D',
                                                                                    items: [{
                                                                                            columnWidth: .999,
                                                                                            border: false,
                                                                                            layout: 'form',
                                                                                            labelAlign: 'top',
                                                                                            items: [this.m_rx_rayosx_engro_ancho_D_abc]
                                                                                        }]
                                                                                }]
                                                                        }, {
                                                                            xtype: 'panel', border: false,
                                                                            columnWidth: .50,
                                                                            bodyStyle: 'padding:2px 10px 0px 5px;',
                                                                            items: [{
                                                                                    xtype: 'fieldset', layout: 'column',
                                                                                    title: 'I',
                                                                                    items: [{
                                                                                            columnWidth: .999,
                                                                                            border: false,
                                                                                            layout: 'form',
                                                                                            labelAlign: 'top',
                                                                                            items: [this.m_rx_rayosx_engro_ancho_I_abc]
                                                                                        }]
                                                                                }]
                                                                        }]
                                                                }]
                                                        }]
                                                }]
                                        }
                                    ]
                                }
                            ]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .999,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'IV. SIMBOLOS',
                                    items: [
                                        {
                                            columnWidth: .50,
                                            border: false,
                                            layout: 'form',
                                            labelWidth: 60,
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_simbolo]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_simbolo_det]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_coment]
                                        }
                                    ]
                                }
                            ]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .99,
                            bodyStyle: 'padding:2px 5px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: '',
                                    items: [{
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_vertice]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_mediastinos]
                                        }, {
                                            columnWidth: .34,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_camp_pulmo]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_silueta_card]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_hilos]
                                        }, {
                                            columnWidth: .34,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_senos]
                                        }, {
                                            columnWidth: .50,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_obs]
                                        }, {
                                            columnWidth: .50,
                                            border: false,
                                            layout: 'form',
                                            labelAlign: 'top',
                                            items: [this.m_rx_rayosx_concluciones]
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
                        mod.rayosx.rayosx_rayosx.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({params: {
                                acction: (this.record.get('st') >= 1) ? 'update_rayosx_rayosx' : 'save_rayosx_rayosx'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
                                if (action.result.success === true) {
                                    if (action.result.total === 1) {
//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
                                        mod.rayosx.formatos.st.reload();
                                        mod.rayosx.st.reload();
                                        mod.rayosx.rayosx_rayosx.win.el.unmask();
                                        mod.rayosx.rayosx_rayosx.win.close();
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
                                mod.rayosx.rayosx_rayosx.win.el.unmask();
                                mod.rayosx.rayosx_rayosx.win.close();
                                mod.rayosx.formatos.st.reload();
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
            title: 'EXAMEN RAYOS X: ',
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};


Ext.onReady(mod.rayosx.init, mod.rayosx);