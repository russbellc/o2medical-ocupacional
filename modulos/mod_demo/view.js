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

Ext.ns('mod.psicologia');
mod.psicologia = {
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
                    this.baseParams.columna = mod.psicologia.descripcion.getValue();
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
//                        mod.psicologia.rfecha.init(null);
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
                        mod.psicologia.formatos.init(record);
                    } else {
                        mod.psicologia.formatos.init(record);
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
mod.psicologia.formatos = {
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
            mod.psicologia.formatos.imgStore.removeAll();
            var store = mod.psicologia.formatos.imgStore;
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
                    this.baseParams.adm = mod.psicologia.formatos.record.get('adm');
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
                    if (record.get('ex_id') == 58) {//PSICOLOGIA - LAS BAMBAS
                        mod.psicologia.inform_psicologia.init(record);
                    } else if (record.get('ex_id') == 7) {//examen psicologia
//                        mod.psicologia.anexo_16a.init(record);
                    } else {
                        mod.psicologia.examenPRE.init(record);//
                    }
//                    mod.psicologia.examenPRE.init(record);//
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    if (record.get('st') == "1") {
                        if (record.get('ex_id') == 58) {   //PSICOLOGIA - LAS BAMBAS
                            new Ext.menu.Menu({
                                items: [{
                                        text: 'INFORME PSICOLOGICO N°: <B>' + record.get('adm') + '<B>',
                                        iconCls: 'reporte',
                                        handler: function () {
                                            if (record.get('st') >= 1) {
                                                new Ext.Window({
                                                    title: 'INFORME PSICOLOGICO N° ' + record.get('adm'),
                                                    width: 800,
                                                    height: 600,
                                                    maximizable: true,
                                                    modal: true,
                                                    closeAction: 'close',
                                                    resizable: true,
                                                    html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=formato_16a&adm=" + record.get('adm') + "'></iframe>"
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
                                                    html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=formato_16a&adm=" + record.get('adm') + "'></iframe>"
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

mod.psicologia.examenPRE = {
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
                acction: 'load_examenLab',
                format: 'json',
                adm: mod.psicologia.examenPRE.record.get('adm'),
                examen: mod.psicologia.examenPRE.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.psicologia.anexo_16a.val_medico.setValue(r.val_medico);
//                mod.psicologia.anexo_16a.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_controles: function () {
        this.m_lab_exam_resultado = new Ext.form.TextField({
            fieldLabel: '<b>RESULTADO DEL EXAMEN</b>',
            allowBlank: false,
            name: 'm_lab_exam_resultado',
            anchor: '96%'
        });
        this.m_lab_exam_observaciones = new Ext.form.TextArea({
            fieldLabel: '<b>OBSERVACIONES</b>',
            name: 'm_lab_exam_observaciones',
            anchor: '99%',
            height: 40
        });
        this.m_lab_exam_diagnostico = new Ext.form.TextArea({
            fieldLabel: '<b>DIAGNOSTICO</b>',
            name: 'm_lab_exam_diagnostico',
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
                    items: [this.m_lab_exam_resultado]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_lab_exam_observaciones]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_lab_exam_diagnostico]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.psicologia.examenPRE.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record.get('st') >= 1) ? 'update_exaLab' : 'save_exaLab'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function () {
//                                Ext.MessageBox.alert('En hora buena', 'El servicio se registro correctamente');
                                mod.psicologia.formatos.st.reload();
                                mod.psicologia.st.reload();
                                mod.psicologia.examenPRE.win.el.unmask();
                                mod.psicologia.examenPRE.win.close();
                            },
                            failure: function (form, action) {
                                mod.psicologia.examenPRE.win.el.unmask();
                                mod.psicologia.examenPRE.win.close();
                                mod.psicologia.formatos.st.reload();
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

mod.psicologia.inform_psicologia = {
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
                acction: 'load_psico_informe',
                format: 'json',
                adm: mod.psicologia.inform_psicologia.record.get('adm')
//                ,examen: mod.psicologia.inform_psicologia.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.psicologia.inform_psicologia.val_medico.setValue(r.val_medico);
//                mod.psicologia.inform_psicologia.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_stores: function () {

    },
    crea_controles: function () {
//m_psico_inf_capac_intelectual
        this.m_psico_inf_capac_intelectual = new Ext.form.RadioGroup({
            fieldLabel: '<b>CAPACIDAD INTELECTUAL</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            items: [
                {boxLabel: 'BAJO', name: 'm_psico_inf_capac_intelectual', inputValue: 'BAJO'},
                {boxLabel: 'MEDIO', name: 'm_psico_inf_capac_intelectual', inputValue: 'MEDIO', checked: true},
                {boxLabel: 'ALTO', name: 'm_psico_inf_capac_intelectual', inputValue: 'ALTO'}
            ]
        });
//m_psico_inf_aten_concentracion
        this.m_psico_inf_aten_concentracion = new Ext.form.RadioGroup({
            fieldLabel: '<b>ATENCIÓN Y CONCENTRACION</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            items: [
                {boxLabel: 'BAJO', name: 'm_psico_inf_aten_concentracion', inputValue: 'BAJO'},
                {boxLabel: 'MEDIO', name: 'm_psico_inf_aten_concentracion', inputValue: 'MEDIO', checked: true},
                {boxLabel: 'ALTO', name: 'm_psico_inf_aten_concentracion', inputValue: 'ALTO'}
            ]
        });
//m_psico_inf_orient_espacial
        this.m_psico_inf_orient_espacial = new Ext.form.RadioGroup({
            fieldLabel: '<b>CONCENTRACION ESPACIAL</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            items: [
                {boxLabel: 'BAJO', name: 'm_psico_inf_orient_espacial', inputValue: 'BAJO'},
                {boxLabel: 'MEDIO', name: 'm_psico_inf_orient_espacial', inputValue: 'MEDIO', checked: true},
                {boxLabel: 'ALTO', name: 'm_psico_inf_orient_espacial', inputValue: 'ALTO'}
            ]
        });
//m_psico_inf_pers_htp
        this.m_psico_inf_pers_htp = new Ext.form.TextField({
            fieldLabel: '<b>PERSONALIDAD HTP</b>',
            name: 'm_psico_inf_pers_htp',
            anchor: '95%'
        });
//m_psico_inf_pers_salamanca
        this.m_psico_inf_pers_salamanca = new Ext.form.TextField({
            fieldLabel: '<b>PERSONALIDAD SALAMANCA</b>',
            name: 'm_psico_inf_pers_salamanca',
            anchor: '95%'
        });
//m_psico_inf_intel_emocional
        this.m_psico_inf_intel_emocional = new Ext.form.TextField({
            fieldLabel: '<b>INTELIGENCIA EMOCIONAL</b>',
            name: 'm_psico_inf_intel_emocional',
            anchor: '95%'
        });
//m_psico_inf_caracterologia
        this.m_psico_inf_caracterologia = new Ext.form.TextField({
            fieldLabel: '<b>CARACTEROLOGIA</b>',
            name: 'm_psico_inf_caracterologia',
            anchor: '95%'
        });
//m_psico_inf_alturas
        this.m_psico_inf_alturas = new Ext.form.TextField({
            fieldLabel: '<b>ALTURAS</b>',
            name: 'm_psico_inf_alturas',
            anchor: '95%'
        });
//m_psico_inf_esp_confinados
        this.m_psico_inf_esp_confinados = new Ext.form.TextField({
            fieldLabel: '<b>ESPACIOS CONFINADOS</b>',
            name: 'm_psico_inf_esp_confinados',
            anchor: '95%'
        });
//m_psico_inf_otros
        this.m_psico_inf_otros = new Ext.form.TextField({
            fieldLabel: '<b>OTROS</b>',
            name: 'm_psico_inf_otros',
            anchor: '95%'
        });
//m_psico_inf_precis_destre_reac
        this.m_psico_inf_precis_destre_reac = new Ext.form.TextField({
            fieldLabel: '<b>PRECISION, DESTREZA, REACCION</b>',
            name: 'm_psico_inf_precis_destre_reac',
            anchor: '95%'
        });
//m_psico_inf_antici_bim_mono
        this.m_psico_inf_antici_bim_mono = new Ext.form.TextField({
            fieldLabel: '<b>ANTICIPACION, BIMANUAL, MONOTONIA</b>',
            name: 'm_psico_inf_antici_bim_mono',
            anchor: '95%'
        });
//m_psico_inf_actitud_f_trans
        this.m_psico_inf_actitud_f_trans = new Ext.form.TextField({
            fieldLabel: '<b>ACTITUD FRENTE AL TRANSITO</b>',
            name: 'm_psico_inf_actitud_f_trans',
            anchor: '95%'
        });
//m_psico_inf_resultados
        this.m_psico_inf_resultados = new Ext.form.TextArea({
            name: 'm_psico_inf_resultados',
            fieldLabel: '<b>FORTALEZAS</b>',
            anchor: '99%',
            height: 80
        });
//m_psico_inf_debilidades
        this.m_psico_inf_debilidades = new Ext.form.TextArea({
            name: 'm_psico_inf_debilidades',
            fieldLabel: '<b>DEBILIDADES</b>',
            anchor: '99%',
            value: 'NINGUNA',
            height: 70
        });
//m_psico_inf_conclusiones
        this.m_psico_inf_conclusiones = new Ext.form.TextArea({
            name: 'm_psico_inf_conclusiones',
            fieldLabel: '<b>CONCLUSIONES</b>',
            anchor: '99%',
            value: 'El paciente ' + mod.psicologia.formatos.record.get('nombre') + '; se encuentra APTO para laborar.',
            height: 70
        });
//m_psico_inf_recomendaciones
        this.m_psico_inf_recomendaciones = new Ext.form.TextArea({
            name: 'm_psico_inf_recomendaciones',
            fieldLabel: '<b>RECOMENDACIONES</b>',
            anchor: '99%',
            value: 'NINGUNA',
            height: 160
        });
//m_psico_inf_puesto_trabajo
        this.m_psico_inf_puesto_trabajo = new Ext.form.RadioGroup({
            fieldLabel: '<b>PUESTO DE TRABAJO</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            items: [
                {boxLabel: 'APTO', name: 'm_psico_inf_puesto_trabajo', inputValue: 'APTO', checked: true},
                {boxLabel: 'OBSERVADO', name: 'm_psico_inf_puesto_trabajo', inputValue: 'OBSERVADO'},
                {boxLabel: 'NO APTO', name: 'm_psico_inf_puesto_trabajo', inputValue: 'NO APTO'}
            ]
        });
//m_psico_inf_brigadista
        this.m_psico_inf_brigadista = new Ext.form.RadioGroup({
            fieldLabel: '<b>BRIGADISTA</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            items: [
                {boxLabel: 'APTO', name: 'm_psico_inf_brigadista', inputValue: 'APTO'},
                {boxLabel: 'OBSERVADO', name: 'm_psico_inf_brigadista', inputValue: 'OBSERVADO'},
                {boxLabel: 'NO APLICA', name: 'm_psico_inf_brigadista', inputValue: 'NO APLICA', checked: true}
            ]
        });
//m_psico_inf_conduc_equip_liviano
        this.m_psico_inf_conduc_equip_liviano = new Ext.form.RadioGroup({
            fieldLabel: '<b>CONDUCCION DE EQUIPO LIVIANO</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            items: [
                {boxLabel: 'APTO', name: 'm_psico_inf_conduc_equip_liviano', inputValue: 'APTO'},
                {boxLabel: 'NO APTO', name: 'm_psico_inf_conduc_equip_liviano', inputValue: 'NO APTO'},
                {boxLabel: 'NO APLICA', name: 'm_psico_inf_conduc_equip_liviano', inputValue: 'NO APLICA', checked: true}
            ]
        });
//m_psico_inf_conduc_equip_pesado
        this.m_psico_inf_conduc_equip_pesado = new Ext.form.RadioGroup({
            fieldLabel: '<b>CONDUCCION DE EQUIPO PESADO</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            items: [
                {boxLabel: 'APTO', name: 'm_psico_inf_conduc_equip_pesado', inputValue: 'APTO'},
                {boxLabel: 'NO APTO', name: 'm_psico_inf_conduc_equip_pesado', inputValue: 'NO APTO'},
                {boxLabel: 'NO APLICA', name: 'm_psico_inf_conduc_equip_pesado', inputValue: 'NO APLICA', checked: true}
            ]
        });
//m_psico_inf_trabajo_altura
        this.m_psico_inf_trabajo_altura = new Ext.form.RadioGroup({
            fieldLabel: '<b>TRABAJO EN ALTURA A +180 mtrs</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            items: [
                {boxLabel: 'APTO', name: 'm_psico_inf_trabajo_altura', inputValue: 'APTO'},
                {boxLabel: 'NO APTO', name: 'm_psico_inf_trabajo_altura', inputValue: 'NO APTO'},
                {boxLabel: 'NO APLICA', name: 'm_psico_inf_trabajo_altura', inputValue: 'NO APLICA', checked: true}
            ]
        });
//m_psico_inf_trab_esp_confinado
        this.m_psico_inf_trab_esp_confinado = new Ext.form.RadioGroup({
            fieldLabel: '<b>CONDUCCION DE EQUIPO PESADO</b>',
            itemCls: 'x-check-group-alt',
            columns: 3,
            items: [
                {boxLabel: 'APTO', name: 'm_psico_inf_trab_esp_confinado', inputValue: 'APTO'},
                {boxLabel: 'NO APTO', name: 'm_psico_inf_trab_esp_confinado', inputValue: 'NO APTO'},
                {boxLabel: 'NO APLICA', name: 'm_psico_inf_trab_esp_confinado', inputValue: 'NO APLICA', checked: true}
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
                    title: '<b>--->  COMPETENCIAS PSICOLOGICAS- RESULTADOS - DEBILIDADES - CONCLUSIONES - RECOMENDACIONES - APTITUD</b>',
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
                                    title: 'COGNITIVAS',
                                    labelWidth: 150,
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_capac_intelectual]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_aten_concentracion]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_orient_espacial]
                                        }]
                                }, {
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'TEMORES',
                                    labelWidth: 150,
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_alturas]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_esp_confinados]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_otros]
                                        }]
                                }, {
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelAlign: 'top',
                                    items: [this.m_psico_inf_resultados]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'AFECTIVAS',
                                    labelWidth: 150,
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_pers_htp]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_pers_salamanca]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_intel_emocional]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_caracterologia]
                                        }]
                                }, {
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'PSICOTECNICO',
                                    labelWidth: 150,
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_precis_destre_reac]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_antici_bim_mono]
                                        }]
                                }, {
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'TIPO DE CONDUCTA',
                                    labelWidth: 150,
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_actitud_f_trans]
                                        }]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelAlign: 'top',
                                    items: [this.m_psico_inf_debilidades]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .50,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelAlign: 'top',
                                    items: [this.m_psico_inf_conclusiones]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .40,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    columnWidth: .999,
                                    border: false,
                                    layout: 'form',
                                    labelAlign: 'top',
                                    items: [this.m_psico_inf_recomendaciones]
                                }]
                        }, {
                            xtype: 'panel', border: false,
                            columnWidth: .60,
                            bodyStyle: 'padding:2px 22px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'COGNITIVAS',
                                    labelWidth: 220,
                                    items: [{
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_puesto_trabajo]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_brigadista]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_conduc_equip_liviano]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_conduc_equip_pesado]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_trabajo_altura]
                                        }, {
                                            columnWidth: .999,
                                            border: false,
                                            layout: 'form',
//                                            labelAlign: 'top',
                                            items: [this.m_psico_inf_trab_esp_confinado]
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
                        mod.psicologia.inform_psicologia.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({params: {
                                acction: (this.record.get('st') >= 1) ? 'update_psico_informe' : 'save_psico_informe'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
//                                obj = Ext.util.JSON.decode(action.response.responseText);
//                                Ext.MessageBox.alert('En hora buena', 'Se registro correctamente');
                                mod.psicologia.formatos.st.reload();
                                mod.psicologia.st.reload();
                                mod.psicologia.inform_psicologia.win.el.unmask();
                                mod.psicologia.inform_psicologia.win.close();
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
            height: 500,
            border: false,
            modal: true,
            title: 'EXAMEN PSICOLOGICO: ',
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};


Ext.onReady(mod.psicologia.init, mod.psicologia);