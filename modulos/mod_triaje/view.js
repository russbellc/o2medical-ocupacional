
Ext.ns('mod.triaje');
mod.triaje = {
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
                    this.baseParams.columna = mod.triaje.descripcion.getValue();
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
//                        mod.triaje.rfecha.init(null);
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
                        mod.triaje.formatos.init(record);
                    } else {
                        mod.triaje.formatos.init(record);
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
mod.triaje.formatos = {
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
            mod.triaje.formatos.imgStore.removeAll();
            var store = mod.triaje.formatos.imgStore;
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
                    this.baseParams.adm = mod.triaje.formatos.record.get('adm');
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
                    if (record.get('ex_id') == 1) {//examen triaje
                        mod.triaje.triaje_triaje.init(record);
                    } else {
                        mod.triaje.triaje_pred.init(record);//
                    }
//                    mod.triaje.triaje_pred.init(record);//
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
                                                    html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_triaje&sys_report=formato_psico_informe&adm=" + record.get('adm') + "'></iframe>"
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
                                                    html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_triaje&sys_report=formato_psico_examen&adm=" + record.get('adm') + "'></iframe>"
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

mod.triaje.triaje_pred = {
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
                acction: 'load_triaje_pred',
                format: 'json',
                adm: mod.triaje.triaje_pred.record.get('adm'),
                examen: mod.triaje.triaje_pred.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.triaje.anexo_16a.val_medico.setValue(r.val_medico);
//                mod.triaje.anexo_16a.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_controles: function () {
        this.m_tri_pred_resultado = new Ext.form.TextField({
            fieldLabel: '<b>RESULTADO DEL EXAMEN</b>',
            allowBlank: false,
            name: 'm_tri_pred_resultado',
            anchor: '96%'
        });
        this.m_tri_pred_observaciones = new Ext.form.TextArea({
            fieldLabel: '<b>OBSERVACIONES</b>',
            name: 'm_tri_pred_observaciones',
            anchor: '99%',
            height: 40
        });
        this.m_tri_pred_diagnostico = new Ext.form.TextArea({
            fieldLabel: '<b>DIAGNOSTICO</b>',
            name: 'm_tri_pred_diagnostico',
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
                    items: [this.m_tri_pred_resultado]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_tri_pred_observaciones]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.m_tri_pred_diagnostico]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.triaje.triaje_pred.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record.get('st') >= 1) ? 'update_triaje_pred' : 'save_triaje_pred'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function () {
//                                Ext.MessageBox.alert('En hora buena', 'El servicio se registro correctamente');
                                mod.triaje.formatos.st.reload();
                                mod.triaje.st.reload();
                                mod.triaje.triaje_pred.win.el.unmask();
                                mod.triaje.triaje_pred.win.close();
                            },
                            failure: function (form, action) {
                                mod.triaje.triaje_pred.win.el.unmask();
                                mod.triaje.triaje_pred.win.close();
                                mod.triaje.formatos.st.reload();
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

mod.triaje.triaje_triaje = {
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
                acction: 'load_triaje_triaje',
                format: 'json',
                adm: mod.triaje.triaje_triaje.record.get('adm')
//                ,examen: mod.triaje.triaje_triaje.record.get('ex_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
//                mod.triaje.triaje_triaje.val_medico.setValue(r.val_medico);
//                mod.triaje.triaje_triaje.val_medico.setRawValue(r.medico_nom);
            }
        });
    },
    crea_stores: function () {

    },
    crea_controles: function () {
//m_tri_triaje_talla
        this.m_tri_triaje_talla = new Ext.form.TextField({
            id: 'm_tri_triaje_talla',
            name: 'm_tri_triaje_talla',
            fieldLabel: '<b>Talla (Mts)</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_peso
        this.m_tri_triaje_peso = new Ext.form.TextField({
            id: 'm_tri_triaje_peso',
            name: 'm_tri_triaje_peso',
            fieldLabel: '<b>Peso (Kg)</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            },
            listeners: {
                render: function (editorObject) {
                    editorObject.getEl().on({
                        'change': function (event, target, scope) {
                            var editorObject = scope;
                            var Peso = target.value;
                            var Talla = mod.triaje.triaje_triaje.m_tri_triaje_talla.getValue();
                            if (Talla != '') {
                                var IMC = Peso / (Talla * Talla);
                                let redondeo = Math.round((IMC + Number.EPSILON) * 100) / 100
//                                console.log(IMC);
//                                console.log(Math.round(IMC * 100) / 100);
//                                console.log(Math.round((IMC + Number.EPSILON) * 100) / 100);
                                mod.triaje.triaje_triaje.m_tri_triaje_imc.setValue(redondeo);
                                if (IMC < 16.00) {
                                    mod.triaje.triaje_triaje.m_tri_triaje_nutricion_dx.setValue('BAJO PESO (DELSADEZ SEVERA)');
                                } else if (IMC >= 16.00 && IMC <= 16.99) {
                                    mod.triaje.triaje_triaje.m_tri_triaje_nutricion_dx.setValue('BAJO PESO (DELSADEZ MODERADA)');
                                } else if (IMC >= 17.00 && IMC <= 18.49) {
                                    mod.triaje.triaje_triaje.m_tri_triaje_nutricion_dx.setValue('BAJO PESO (DELSADEZ ACEPTABLE)');
                                } else if (IMC >= 18.50 && IMC <= 24.99) {
                                    mod.triaje.triaje_triaje.m_tri_triaje_nutricion_dx.setValue('PESO NORMAL');
                                } else if (IMC >= 25.00 && IMC <= 29.99) {
                                    mod.triaje.triaje_triaje.m_tri_triaje_nutricion_dx.setValue('SOBREPESO (RIESGO)');
                                } else if (IMC >= 30.00 && IMC <= 34.99) {
                                    mod.triaje.triaje_triaje.m_tri_triaje_nutricion_dx.setValue('OBESO TIPO I (RIESGO MODERADO)');
                                } else if (IMC >= 35.00 && IMC <= 39.99) {
                                    mod.triaje.triaje_triaje.m_tri_triaje_nutricion_dx.setValue('OBESO TIPO II (RIESGO SEVERO)');
                                } else if (IMC >= 40) {
                                    mod.triaje.triaje_triaje.m_tri_triaje_nutricion_dx.setValue('OBESO TIPO III (RIESGO MUY SEVERO)');
                                }
                            }
                        },
                        scope: editorObject
                    });
                }
            },
            anchor: '85%'
        });
//m_tri_triaje_imc
        this.m_tri_triaje_imc = new Ext.form.TextField({
            id: 'm_tri_triaje_imc',
            name: 'm_tri_triaje_imc',
            fieldLabel: '<b>IMC (Kg/m2)</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            readOnly: true,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_perim_cintura
        this.m_tri_triaje_perim_cintura = new Ext.form.TextField({
            id: 'm_tri_triaje_perim_cintura',
            name: 'm_tri_triaje_perim_cintura',
            fieldLabel: '<b>PERIMETRO DE CINTURA</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_perim_cadera
        this.m_tri_triaje_perim_cadera = new Ext.form.TextField({
            id: 'm_tri_triaje_perim_cadera',
            name: 'm_tri_triaje_perim_cadera',
            fieldLabel: '<b>PERIMETRO DE CADERA</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            },
            listeners: {
                render: function (editorObject) {
                    editorObject.getEl().on({
                        'keyup': function (event, target, scope) {
                            var editorObject = scope;
                            var CADERA = target.value;
                            var CINTURA = mod.triaje.triaje_triaje.m_tri_triaje_perim_cintura.getValue();
                            if (CINTURA != '') {
                                var ICC = CINTURA / CADERA;
                                let redondeo = Math.round((ICC + Number.EPSILON) * 100) / 100;
//                                console.log(IMC);
//                                console.log(Math.round(IMC * 100) / 100);
//                                console.log(Math.round((IMC + Number.EPSILON) * 100) / 100);
                                mod.triaje.triaje_triaje.m_tri_triaje_icc.setValue(redondeo);
                            }
                        },
                        scope: editorObject
                    });
                }
            },
            anchor: '85%'
        });
//m_tri_triaje_icc
        this.m_tri_triaje_icc = new Ext.form.TextField({
            id: 'm_tri_triaje_icc',
            name: 'm_tri_triaje_icc',
            fieldLabel: '<b>ICC</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            readOnly: true,
            autoCreate: {
                tag: "input",
                maxlength: 6,
                minLength: 1,
                type: "text",
                size: "6",
                autocomplete: "off"
            },
            anchor: '75%'
        });
//m_tri_triaje_nutricion_dx
        this.m_tri_triaje_nutricion_dx = new Ext.form.TextField({
            fieldLabel: '<b>CLASIFICACIÓN DEL RESULTADO IMC</b>',
            name: 'm_tri_triaje_nutricion_dx',
            //value: 'NIEGA',
            anchor: '95%'
        });
//m_tri_triaje_pa_sistolica
        this.m_tri_triaje_pa_sistolica = new Ext.form.TextField({
            name: 'm_tri_triaje_pa_sistolica',
            fieldLabel: '<b>P.A. SISTÓLICA</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_pa_diastolica
        this.m_tri_triaje_pa_diastolica = new Ext.form.TextField({
            name: 'm_tri_triaje_pa_diastolica',
            fieldLabel: '<b>P.A. DIASTÓLICA</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_fc
        this.m_tri_triaje_fc = new Ext.form.TextField({
            name: 'm_tri_triaje_fc',
            fieldLabel: '<b>F.C. (x\')</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '80%'
        });
//m_tri_triaje_fr
        this.m_tri_triaje_fr = new Ext.form.TextField({
            name: 'm_tri_triaje_fr',
            fieldLabel: '<b>F.R. (x\')</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '80%'
        });
//m_tri_triaje_temperatura
        this.m_tri_triaje_temperatura = new Ext.form.TextField({
            name: 'm_tri_triaje_temperatura',
            fieldLabel: '<b>Temperatura C°</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_saturacion
        this.m_tri_triaje_saturacion = new Ext.form.TextField({
            name: 'm_tri_triaje_saturacion',
            fieldLabel: '<b>Saturacion (%)</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_perimt_toraxico
        this.m_tri_triaje_perimt_toraxico = new Ext.form.TextField({
            name: 'm_tri_triaje_perimt_toraxico',
            fieldLabel: '<b>Perímetro Toráxico (cm)</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_maxi_inspiracion
        this.m_tri_triaje_maxi_inspiracion = new Ext.form.TextField({
            name: 'm_tri_triaje_maxi_inspiracion',
            fieldLabel: '<b>Máxima inspiración (cm)</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_expira_forzada
        this.m_tri_triaje_expira_forzada = new Ext.form.TextField({
            name: 'm_tri_triaje_expira_forzada',
            fieldLabel: '<b>Espiración Forzada (cm)</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_perimt_abdominal
        this.m_tri_triaje_perimt_abdominal = new Ext.form.TextField({
            name: 'm_tri_triaje_perimt_abdominal',
            fieldLabel: '<b>Perímetro Abdominal (cm)</b>',
            maskRe: /[0-9.]/,
            minLength: 1,
            allowBlank: false,
            autoCreate: {
                tag: "input",
                maxlength: 3,
                minLength: 1,
                type: "text",
                size: "3",
                autocomplete: "off"
            },
            anchor: '85%'
        });
//m_tri_triaje_fur
        this.m_tri_triaje_fur = new Ext.form.DateField({
            name: 'm_tri_triaje_fur',
            fieldLabel: '<b>F.U.R</b>',
            disabled: ((mod.triaje.formatos.record.get('pac_sexo') == 'F') ? false : true),
//            allowBlank: false,
            anchor: '85%',
            format: 'd-m-Y',
//            emptyText: 'Dia-Mes-Año',
            listeners: {
//                render: function (datefield) {
//                    datefield.setValue(new Date());
//                }
            }
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
                    title: '<b>--->  TRIAJE</b>',
                    iconCls: 'demo2',
                    layout: 'column',
                    autoScroll: true,
                    border: false,
                    bodyStyle: 'padding:10px 10px 20px 10px;',
                    labelWidth: 60,
                    items: [
                        {
                            xtype: 'panel', border: false,
                            columnWidth: .999,
                            bodyStyle: 'padding:2px 15px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'TRIAJE - ANTROPOMETRIA',
//                                    labelWidth: 220,
                                    items: [{
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_talla]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_peso]
                                        }, {
                                            columnWidth: .34,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_imc]
                                        }, {
                                            columnWidth: .66,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_nutricion_dx]
                                        }, {
                                            columnWidth: .34,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_fur]
                                        }, {
                                            columnWidth: .41,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_perim_cintura]
                                        }, {
                                            columnWidth: .41,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_perim_cadera]
                                        }, {
                                            columnWidth: .18,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_icc]
                                        }]
                                }]
                        }
                        , {
                            xtype: 'panel', border: false,
                            columnWidth: .999,
                            bodyStyle: 'padding:2px 15px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'SIGNOS VITALES',
//                                    labelWidth: 220,
                                    items: [{
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_pa_sistolica]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_pa_diastolica]
                                        }, {
                                            columnWidth: .34,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_fc]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_temperatura]
                                        }, {
                                            columnWidth: .33,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_saturacion]
                                        }, {
                                            columnWidth: .34,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_fr]
                                        }]
                                }]
                        }
                        , {
                            xtype: 'panel', border: false,
                            columnWidth: .999,
                            bodyStyle: 'padding:2px 15px 0px 5px;',
                            items: [{
                                    xtype: 'fieldset', layout: 'column',
                                    title: 'PERIMETRO TORAXICO',
//                                    labelWidth: 220,
                                    items: [{
                                            columnWidth: .50,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_perimt_toraxico]
                                        }, {
                                            columnWidth: .50,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_maxi_inspiracion]
                                        }, {
                                            columnWidth: .50,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_expira_forzada]
                                        }, {
                                            columnWidth: .50,
                                            border: false,
                                            layout: 'form',
//                                            labelWidth: 140,
                                            labelAlign: 'top',
                                            items: [this.m_tri_triaje_perimt_abdominal]
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
                        mod.triaje.triaje_triaje.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({params: {
                                acction: (this.record.get('st') >= 1) ? 'update_triaje_triaje' : 'save_triaje_triaje'
                                , id: this.record.get('id')
                                , adm: this.record.get('adm')
                                , ex_id: this.record.get('ex_id')
                            },
                            success: function (form, action) {
                                if (action.result.success === true) {
                                    if (action.result.total === 1) {
//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
                                        mod.triaje.formatos.st.reload();
                                        mod.triaje.st.reload();
                                        mod.triaje.triaje_triaje.win.el.unmask();
                                        mod.triaje.triaje_triaje.win.close();
                                    } else {
//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
                                        mod.triaje.formatos.st.reload();
                                        mod.triaje.st.reload();
                                        mod.triaje.triaje_triaje.win.el.unmask();
                                        mod.triaje.triaje_triaje.win.close();
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
                                mod.triaje.triaje_triaje.win.el.unmask();
                                mod.triaje.triaje_triaje.win.close();
                                mod.triaje.formatos.st.reload();
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
            width: 550,
            height: 583,
            border: false,
            modal: true,
            title: 'TRIAJE: ',
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};


Ext.onReady(mod.triaje.init, mod.triaje);