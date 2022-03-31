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
Ext.ns('mod.empresa');
mod.empresa = {
    panel: null,
    paginador: null,
    buscador: null,
    tbar: null,
    dt_grid: null,
    init: function () {
        this.crea_stores();
        this.crea_controles();
        this.st.load();
        this.panel.render('<[view]>');
    },
    crea_stores: function () {
        this.st = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_emp',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['emp_id', 'emp_usu', 'emp_fech', 'emp_desc', 'emp_acro', 'emp_telf', 'emp_estado', 'emp_direc']
        });
    },
    crea_controles: function () {
        this.paginador = new Ext.PagingToolbar({
            pageSize: 50,
            store: this.st,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2} Empresas',
            emptyMsg: 'No Existe Registros',
            plugins: new Ext.ux.ProgressBarPager()
        });
        this.buscador = new Ext.ux.form.SearchField({
            width: 250,
            fieldLabel: 'Nombre',
            store: this.st,
            id: 'search_query',
            emptyText: 'Ingrese el RUC o la Razon Social'
        });
        this.tbar = new Ext.Toolbar({
            items: ['Buscar Empresa:',
                this.buscador, '->', '-', {
                    text: 'Nuevo',
                    iconCls: 'nuevo',
                    handler: function () {
                        mod.empresa.nuevo.init(null);
                    }
                }
            ]
        });
        this.dt_grid = new Ext.grid.GridPanel({
            store: this.st,
            region: 'west',
            border: false,
            tbar: this.tbar,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            bbar: this.paginador,
            height: 495,
            listeners: {
                rowdblclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record = grid.getStore().getAt(rowIndex);
                    mod.empresa.modificar.init(record);
//                    if (record.get('dpk_exid') == 178) {
//                        mod.empresa.nuevo312.init(record);
//                    } else if (record.get('dpk_exid') == 21) {
//                        mod.empresa.nuevo7c.init(record);
//                        mod.empresa.nuevo7c.llena_mmg(record.get('adm'));
//                    }
                }
            },
            autoExpandColumn: 'emp_desc',
            columns: [{
                    header: 'ST',
                    width: 25,
                    sortable: true,
                    dataIndex: 'emp_estado',
                    renderer: function renderIcon(val) {
                        if (val == 'ACTIVO') {
                            return  '<img src="<[sys_images]>/view.png" title="REGISTRAR" height="15">';
                        } else {
                            return  '<img src="<[sys_images]>/icon_cancel.png" title="GUARDADO" height="15">';
                        }
                    }
                }, {
                    header: 'RUC',
                    width: 80,
                    sortable: true,
                    dataIndex: 'emp_id'
                }, {
                    id: 'emp_desc',
                    header: 'RAZON SOCIAL',
                    dataIndex: 'emp_desc'
                }, {
                    header: 'NOMBRE COMERCIAL',
                    dataIndex: 'emp_acro',
                    width: 250
                }, {
                    header: 'TELEFONO',
                    dataIndex: 'emp_telf',
                    width: 200
                }, {
                    header: 'USUARIO',
                    dataIndex: 'emp_usu',
                    width: 70
                }, {
                    header: 'FECHA DE REGISTRO',
                    dataIndex: 'emp_fech',
                    width: 130
                }
            ]
        });
        this.panel = new Ext.form.FormPanel({
            anchor: '99%',
            layout: 'column',
            height: 500,
            border: false,
            items: [this.dt_grid]
        });
    }
};
mod.empresa.nuevo = {
    frm: null,
    win: null,
    emp_id: null,
    emp_desc: null,
    emp_acro: null,
    emp_telf: null,
    emp_estado: null,
    emp_direc: null,
    init: function () {
        this.crea_controles();
        this.crea_stores();
        this.win.show();
    },
    verifica_ruc: function () {
        Ext.Ajax.request({
            url: '<[controller]>',
            params: {
                acction: 'st_buca_ruc',
                format: 'json',
                ruc: mod.empresa.nuevo.emp_id.getValue()
            },
            success: function (response, opts) {

                var dato = Ext.decode(response.responseText);
                if (dato.total > 0) {
                    Ext.MessageBox.show({
                        title: 'WARNING',
                        msg: 'Ya hay un registro de esta Empresa',
                        buttons: Ext.MessageBox.OK,
                        animEl: 'mb9',
                        icon: Ext.MessageBox.WARNING
                    });
                    mod.empresa.st.reload({
                        params: {
                            query: mod.empresa.nuevo.emp_id.getValue()
                        }
                    });
                    mod.empresa.nuevo.win.close();
                } else {
                    Ext.Ajax.request({
                        url: 'system/sunat/demo.php',
                        dataType: "json",
                        params: {
//                            acction: 'st_buca_ruc',
                            format: 'json',
                            ruc: mod.empresa.nuevo.emp_id.getValue()
                        },
                        success: function (resp) {
                            var obj = Ext.decode(resp.responseText);
                            mod.empresa.nuevo.emp_id.setValue(obj.RUC);
                            mod.empresa.nuevo.emp_desc.setValue(obj.RazonSocial);
                            mod.empresa.nuevo.emp_acro.setValue(obj.NombreComercial);
                            mod.empresa.nuevo.emp_telf.setValue(obj.Telefono);
                            mod.empresa.nuevo.emp_estado.setValue(obj.Estado);
                            mod.empresa.nuevo.emp_direc.setValue(obj.Direccion);
//                            Ext.MessageBox.alert('LiveCode', obj.Tipo);
                        },
                        failure: function (form, action) {
                            switch (action.failureType) {
                                case Ext.form.Action.CLIENT_INVALID:
                                    Ext.Msg.alert('Failure', 'Existen valores Invalidos');
                                    break;
                                case Ext.form.Action.CONNECT_FAILURE:
                                    Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
                                    break;
                                case Ext.form.Action.SERVER_INVALID:
                                    Ext.Msg.alert('Failure', action.result.error);
                                    break;
                                default:
                                    Ext.Msg.alert('Failure', action.result.error);
                            }
                        }
                    });
                }
            },
            failure: function (form, action) {
                switch (action.failureType) {
                    case Ext.form.Action.CLIENT_INVALID:
                        Ext.Msg.alert('Failure', 'Existen valores Invalidos');
                        break;
                    case Ext.form.Action.CONNECT_FAILURE:
                        Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
                        break;
                    case Ext.form.Action.SERVER_INVALID:
                        Ext.Msg.alert('Failure', action.result.error);
                        break;
                    default:
                        Ext.Msg.alert('Failure', action.result.error);
                }
            }
        });
    },
    crea_controles: function () {
        this.emp_id = new Ext.form.TextField({
            fieldLabel: '<b>R.U.C.</b>',
            allowBlank: false,
            name: 'emp_id',
            maskRe: /[\d]/,
            minLength: 8,
            autoCreate: {
                tag: "input",
                maxlength: 11,
                minLength: 8,
                type: "text",
                size: "11",
                autocomplete: "off"
            },
            listeners: {
                /*change: function (combo, registro, indice) {
                    mod.empresa.nuevo.verifica_ruc();
//                    Ext.MessageBox.alert('ALERTA', 'change');
                },
                select: function (combo, registro, indice) {
                    mod.empresa.nuevo.verifica_ruc();
//                    Ext.MessageBox.alert('ALERTA', 'select');
                },*/
                specialkey: function (f, e) {
                    if (e.getKey() == e.TAB) {
//                        Ext.MessageBox.alert('ALERTA', 'TAB');
                        mod.empresa.nuevo.verifica_ruc();
                    } else if (e.getKey() == e.ENTER) {
//                        Ext.MessageBox.alert('ALERTA', 'ENTER');
                        mod.empresa.nuevo.verifica_ruc();
                    }
                }
            },
            anchor: '95%'
        });
        this.emp_desc = new Ext.form.TextField({
            fieldLabel: '<b>RAZON SOCIAL</b>',
            allowBlank: false,
            name: 'emp_desc',
            anchor: '95%'
        });
        this.emp_acro = new Ext.form.TextField({
            fieldLabel: '<b>ACRONIMO O NOMBRE COMERCIAL</b>',
            allowBlank: false,
            name: 'emp_acro',
            anchor: '95%'
        });
        this.emp_telf = new Ext.form.TextField({
            fieldLabel: '<b>TELEFONO O CELULAR</b>',
            allowBlank: false,
            name: 'emp_telf',
            anchor: '90%'
        });
        this.emp_estado = new Ext.form.TextField({
            fieldLabel: '<b>ESTADO</b>',
            allowBlank: false,
            name: 'emp_estado',
            anchor: '90%'
        });
        this.emp_direc = new Ext.form.TextField({
            fieldLabel: '<b>DIRECCION</b>',
            allowBlank: false,
            name: 'emp_direc',
            anchor: '95%'
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
                    items: [this.emp_id]
                }, {
                    columnWidth: .999,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.emp_desc]
                }, {
                    columnWidth: .999,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.emp_acro]
                }, {
                    columnWidth: .50,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.emp_telf]
                }, {
                    columnWidth: .50,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.emp_estado]
                }, {
                    columnWidth: .999,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.emp_direc]
                }
            ],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.empresa.nuevo.win.el.mask('Guardando…', 'x-mask-loading');

                        this.frm.getForm().submit({
                            params: {
                                acction: 'save_empresa'
                            },
                            success: function (form, action) {
                                obj = Ext.util.JSON.decode(action.response.responseText);
                                Ext.MessageBox.alert('En hora buena', 'La empresa se registro correctamente');
                                mod.empresa.nuevo.win.el.unmask();
                                mod.empresa.st.reload();
                                mod.empresa.nuevo.win.close();
                            },
                            failure: function (form, action) {
                                mod.empresa.nuevo.win.el.unmask();
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
                                mod.empresa.st.reload({
                                    params: {
                                        query: action.result.emp_id
                                    }
                                });
                                mod.empresa.nuevo.win.close();
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 600,
            height: 350,
            modal: true,
            title: 'REGISTRO DE EMPRESA',
            border: false,
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    },
    crea_stores: function () {

    }
};
mod.empresa.modificar = {
    frm: null,
    win: null,
    record: null,
    emp_id: null,
    emp_desc: null,
    emp_acro: null,
    emp_telf: null,
    emp_estado: null,
    emp_direc: null,
    dt_grid2: null,
    list_sede: null,
    tbar2: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        this.list_sede.load();
        this.list_cargo.load();
        this.list_perfil.load();
        this.list_area.load();
        this.win.show();
        if (this.record !== null) {
            this.carga_data();
        }
    },
    carga_data: function () {
        this.frm.getForm().load({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            params: {
                acction: 'load_empresa',
                format: 'json',
                emp_id: this.record.get('emp_id')
            },
            scope: this,
            success: function (frm, action) {
                r = action.result.data;
            }
        });
    },
    crea_stores: function () {
        this.list_cargo = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_cargo',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.emp_id = mod.empresa.modificar.record.get('emp_id');
                }
            },
            fields: ['cargo_id', 'cargo_emp', 'cargo_desc']
        });
        this.list_sede = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_sede',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.emp_id = mod.empresa.modificar.record.get('emp_id');
                }
            },
            fields: ['sede_id', 'sede_emp', 'sede_desc']
        });
        this.list_perfil = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_perfil',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.emp_id = mod.empresa.modificar.record.get('emp_id');
//                    this.baseParams.sede_desc = mod.empresa.modificar.sede_desc.getValue();
                }
            },
            fields: ['pk_id', 'pk_usu', 'pk_fech', 'pk_desc', 'pk_emp', 'sede_desc', 'cargo_desc', 'tfi_desc', 'pk_precio', 'pk_estado', 'horas']
        });
        this.list_area = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_area',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['ar_id', 'ar_desc']
        });
        this.list_examen = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_examen',
                format: 'json'
            },
            listeners: {
                'beforeload': function () {
                    this.baseParams.area = mod.empresa.modificar.numero.getValue();
                }
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['ex_id', 'ex_arid', 'ex_desc', 'ex_tarif']
        });
    },
    crea_controles: function () {
        this.emp_id = new Ext.form.TextField({
            fieldLabel: '<b>R.U.C.</b>',
            allowBlank: false,
            name: 'emp_id',
            maskRe: /[\d]/,
            minLength: 8,
            autoCreate: {
                tag: "input",
                maxlength: 11,
                minLength: 8,
                type: "text",
                size: "11",
                autocomplete: "off"
            },
            readOnly: (this.record !== null) ? true : false,
            anchor: '95%'
        });
        this.emp_desc = new Ext.form.TextArea({
            fieldLabel: '<b>RAZON SOCIAL</b>',
            allowBlank: false,
            name: 'emp_desc',
            readOnly: (this.record !== null) ? true : false,
            anchor: '95%',
            height: 80
        });
        this.emp_acro = new Ext.form.TextField({
            fieldLabel: '<b>*ACRONIMO O NOMBRE COMERCIAL (EDITABLE)</b>',
            allowBlank: false,
            name: 'emp_acro',
            anchor: '95%'
        });
        this.emp_telf = new Ext.form.TextField({
            fieldLabel: '<b>TELEFONO O CELULAR</b>',
            allowBlank: false,
            readOnly: (this.record !== null) ? true : false,
            name: 'emp_telf',
            anchor: '90%'
        });
        this.emp_estado = new Ext.form.TextField({
            fieldLabel: '<b>ESTADO</b>',
            allowBlank: false,
            name: 'emp_estado',
            readOnly: (this.record !== null) ? true : false,
            anchor: '90%'
        });
        this.emp_direc = new Ext.form.TextArea({
            fieldLabel: '<b>DIRECCION</b>',
            allowBlank: false,
            name: 'emp_direc',
            readOnly: (this.record !== null) ? true : false,
            anchor: '95%',
            height: 100
        });
        this.paginador = new Ext.PagingToolbar({
            pageSize: 30,
            store: this.list_sede,
            displayInfo: true,
            displayMsg: 'Hay {0} - {1} de {2} Sedes',
            emptyMsg: 'No Existe Registros'
//            ,plugins: new Ext.ux.ProgressBarPager()
        });
        this.tbar2 = new Ext.Toolbar({
            items: [
                '->', '-', {
                    text: 'Nuevo',
                    iconCls: 'nuevo',
                    handler: function () {
                        var record = mod.empresa.modificar.record;
                        mod.empresa.sede.init(record);
                    }
                }
            ]
        });
        this.dt_grid2 = new Ext.grid.GridPanel({
            store: this.list_sede,
            region: 'west',
            border: true,
            tbar: this.tbar2,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            bbar: this.paginador,
            height: 208,
//            listeners: {
//                rowdblclick: function (grid, rowIndex, e) {
////                    e.stopEvent();
////                    var record2 = grid.getStore().getAt(rowIndex);
////                    mod.empresa.sede.init(record2);
//                }
//            },
            autoExpandColumn: 'sede_desc',
            columns: [
                new Ext.grid.RowNumberer(), {
                    id: 'sede_desc',
                    header: 'SEDE O SUCURSAL',
                    dataIndex: 'sede_desc',
                    width: 250
                }
            ]
        });
        this.paginador4 = new Ext.PagingToolbar({
            pageSize: 30,
            store: this.list_cargo,
            displayInfo: true,
            displayMsg: 'Hay {0} - {1} de {2} cargos',
            emptyMsg: 'No Existe Registros'
//            ,plugins: new Ext.ux.ProgressBarPager()
        });
        this.tbar4 = new Ext.Toolbar({
            items: [
                '->', '-', {
                    text: 'Nuevo',
                    iconCls: 'nuevo',
                    handler: function () {
                        var record = mod.empresa.modificar.record;
                        mod.empresa.cargo.init(record);
                    }
                }
            ]
        });
        this.dt_grid4 = new Ext.grid.GridPanel({
            store: this.list_cargo,
            region: 'west',
            border: true,
            tbar: this.tbar4,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            bbar: this.paginador4,
            height: 240,
//            listeners: {
//                rowdblclick: function (grid, rowIndex, e) {
////                    e.stopEvent();
////                    var record2 = grid.getStore().getAt(rowIndex);
////                    mod.empresa.sede.init(record2);
//                }
//            },
            autoExpandColumn: 'cargo_desc',
            columns: [
                new Ext.grid.RowNumberer(), {
                    id: 'cargo_desc',
                    header: 'CARGOS DE LA EMPRESA',
                    dataIndex: 'cargo_desc',
                    width: 250
                }
            ]
        });
        this.paginador3 = new Ext.PagingToolbar({
            pageSize: 30,
            store: this.list_perfil,
            displayInfo: true,
            displayMsg: 'Hay {0} - {1} de {2} PERFILES',
            emptyMsg: 'No Existe Registros'
            , plugins: new Ext.ux.ProgressBarPager()
        });
        this.btn_pack = new Ext.Button({
            text: 'Nuevo',
            disabled: true,
            iconCls: 'nuevo',
            handler: function () {
                mod.empresa.rutas.init();
            }
        });
        this.btn_pack2 = new Ext.Button({
            text: 'Nuevo',
            disabled: true,
            iconCls: 'nuevo',
            handler: function () {
                mod.empresa.rutas.init();
            }
        });
        this.tbar3 = new Ext.Toolbar({
            items: [
                '->', '-', this.btn_pack
            ]
        });
        this.dt_grid3 = new Ext.grid.GridPanel({
            store: this.list_perfil,
            region: 'west',
            border: false,
//            tbar: this.tbar3,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            bbar: this.paginador3,
            height: 505,
            listeners: {
                rowdblclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record2 = grid.getStore().getAt(rowIndex);
                    mod.empresa.rutasEdit.init(record2);
//                    mod.empresa.rutas.init(record2);
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    new Ext.menu.Menu({
                        items: [{
                                text: 'Editar',
                                iconCls: 'editar',
                                handler: function () {
                                    mod.empresa.rutasEdit.init(record);
                                }
                            }, {
                                text: 'Imprimir Ruta',
                                iconCls: 'reporte'
                            }]
                    }).showAt(event.xy);
                }
            },
            autoExpandColumn: 'pk_desc',
            columns: [
                new Ext.grid.RowNumberer(), {
                    header: 'ST',
                    width: 25,
                    sortable: true,
                    dataIndex: 'pk_estado',
                    renderer: function renderIcon(val) {
                        if (val == '1') {
                            return  '<img src="<[sys_images]>/view.png" title="ACTIVO" height="15">';
                        } else {
                            return  '<img src="<[sys_images]>/icon_cancel.png" title="DESHABILITADO" height="15">';
                        }
                    }
                }, {
                    id: 'pk_desc',
                    header: 'RUTA',
                    dataIndex: 'pk_desc',
                    width: 250
                }, {
                    header: 'SEDE',
                    dataIndex: 'sede_desc',
                    width: 150
                }, {
                    header: 'CARGO',
                    dataIndex: 'cargo_desc',
                    width: 150
                }, {
                    header: 'PERFIL',
                    dataIndex: 'tfi_desc',
                    width: 125
                }, {
                    header: 'USUARIO',
                    width: 60,
                    dataIndex: 'pk_usu'
                }, {
                    header: 'FECHA DE REGISTRO',
                    width: 140,
                    dataIndex: 'pk_fech'//emp_desc
                }, {
                    xtype: 'numbercolumn',
                    format: 'S/ 0.00',
                    header: 'PRECIO DE LA RUTA',
                    width: 120,
                    dataIndex: 'pk_precio'
//                    format: 'S./ 0.00'
                }
            ]
        });
        this.sede_desc = new Ext.form.ComboBox({
            store: this.list_sede,
            hiddenName: 'sede_desc',
            displayField: 'sede_desc',
            valueField: 'sede_id',
            allowBlank: false,
            typeAhead: false,
            editable: false,
            triggerAction: 'all',
            fieldLabel: 'SEDE O SUCURSAL',
            mode: 'remote',
            width: 200,
            listeners: {
                scope: this,
                select: function (combo, registro, indice) {
//                    this.btn_pack.enable();
                    this.cargo_desc.enable();
                    this.cargo_desc.clearValue();
                    this.list_cargo.load();
//                    this.list_perfil.load();
                    mod.empresa.modificar.list_perfil.reload({
                        params: {
                            sede_id: mod.empresa.modificar.sede_desc.getValue(),
                            cargo_id: mod.empresa.modificar.cargo_desc.getValue()
                        }
                    });
                }

            }
        });
        this.cargo_desc = new Ext.form.ComboBox({
            store: this.list_cargo,
            hiddenName: 'cargo_desc',
            displayField: 'cargo_desc',
            valueField: 'cargo_id',
            allowBlank: false,
            typeAhead: false,
            editable: false,
            disabled: true,
            triggerAction: 'all',
            fieldLabel: 'CARGO O PUESTO',
            mode: 'remote',
            width: 250,
            listeners: {
                scope: this,
                select: function (combo, registro, indice) {
                    this.btn_pack.enable();
                    this.list_perfil.load();
                    mod.empresa.modificar.list_perfil.reload({
                        params: {
                            sede_id: mod.empresa.modificar.sede_desc.getValue(),
                            cargo_id: mod.empresa.modificar.cargo_desc.getValue()
                        }
                    });
                }
            }
        });


        this.sede_desc2 = new Ext.form.ComboBox({
            store: this.list_sede,
            hiddenName: 'sede_desc2',
            displayField: 'sede_desc',
            valueField: 'sede_id',
            allowBlank: false,
            typeAhead: false,
            editable: false,
            triggerAction: 'all',
            fieldLabel: 'SEDE O SUCURSAL',
            mode: 'remote',
            width: 200,
            listeners: {
                scope: this,
                select: function (combo, registro, indice) {
//                    this.btn_pack.enable();
                    this.cargo_desc2.enable();
                    this.cargo_desc2.clearValue();
                    this.list_cargo.load();
//                    this.list_perfil.load();
                    mod.empresa.modificar.list_perfil.reload({
                        params: {
                            sede_id: mod.empresa.modificar.sede_desc2.getValue(),
                            cargo_id: mod.empresa.modificar.cargo_desc2.getValue()
                        }
                    });
                }

            }
        });
        this.cargo_desc2 = new Ext.form.ComboBox({
            store: this.list_cargo,
            hiddenName: 'cargo_desc2',
            displayField: 'cargo_desc',
            valueField: 'cargo_id',
            allowBlank: false,
            typeAhead: false,
            editable: false,
            disabled: true,
            triggerAction: 'all',
            fieldLabel: 'CARGO O PUESTO',
            mode: 'remote',
            width: 250,
            listeners: {
                scope: this,
                select: function (combo, registro, indice) {
                    this.btn_pack.enable();
                    this.list_perfil.load();
                    mod.empresa.modificar.list_perfil.reload({
                        params: {
                            sede_id: mod.empresa.modificar.sede_desc2.getValue(),
                            cargo_id: mod.empresa.modificar.cargo_desc2.getValue()
                        }
                    });
                }
            }
        });
        this.paginador4 = new Ext.PagingToolbar({
            pageSize: 30,
            store: this.list_perfil,
            displayInfo: true,
            displayMsg: 'Hay {0} - {1} de {2} PERFILES',
            emptyMsg: 'No Existe Registros'
            , plugins: new Ext.ux.ProgressBarPager()
        });
        this.dt_grid5 = new Ext.grid.GridPanel({
            store: this.list_perfil,
            region: 'west',
            border: false,
//            tbar: this.tbar3,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            bbar: this.paginador4,
            height: 505,
            listeners: {
                rowdblclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record2 = grid.getStore().getAt(rowIndex);
                    mod.empresa.rutasEdit.init(record2);
//                    mod.empresa.rutas.init(record2);
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    new Ext.menu.Menu({
                        items: [{
                                text: 'Editar',
                                iconCls: 'editar',
                                handler: function () {
                                    mod.empresa.rutasEdit.init(record);
                                }
                            }, {
                                text: 'Imprimir Cotizacion',
                                iconCls: 'reporte'
                            }]
                    }).showAt(event.xy);
                }
            },
            autoExpandColumn: 'pk_desc',
            columns: [
                new Ext.grid.RowNumberer(), {
                    header: 'ST',
                    width: 25,
                    sortable: true,
                    dataIndex: 'pk_estado',
                    renderer: function renderIcon(val) {
                        if (val == '1') {
                            return  '<img src="<[sys_images]>/view.png" title="ACTIVO" height="15">';
                        } else {
                            return  '<img src="<[sys_images]>/icon_cancel.png" title="DESHABILITADO" height="15">';
                        }
                    }
                }, {
                    id: 'pk_desc',
                    header: 'RUTA',
                    dataIndex: 'pk_desc',
                    width: 250
                }, {
                    header: 'SEDE',
                    dataIndex: 'sede_desc',
                    width: 150
                }, {
                    header: 'CARGO',
                    dataIndex: 'cargo_desc',
                    width: 150
                }, {
                    header: 'PERFIL',
                    dataIndex: 'tfi_desc',
                    width: 125
                }, {
                    header: 'USUARIO',
                    width: 60,
                    dataIndex: 'pk_usu'
                }, {
                    header: 'FECHA DE REGISTRO',
                    width: 140,
                    dataIndex: 'pk_fech'//emp_desc
                }, {
                    xtype: 'numbercolumn',
                    format: 'S/ 0.00',
                    header: 'PRECIO DE LA RUTA',
                    width: 120,
                    dataIndex: 'pk_precio'
//                    format: 'S./ 0.00'
                }
            ]
        });

        this.paginador5 = new Ext.PagingToolbar({
            pageSize: 50,
            store: this.list_examen,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2} Examenes',
            emptyMsg: 'No Existe Registros',
            plugins: new Ext.ux.ProgressBarPager()
        });
        this.numero = new Ext.form.TextField({
            fieldLabel: '<b>GRUPO</b>',
            name: 'numero',
            maskRe: /[\d]/,
            readOnly: true,
            anchor: '30%',
            width: 50
        });
        this.desc = new Ext.form.TextField({
            fieldLabel: '<b>GRUPO</b>',
            name: 'desc',
            maskRe: /[\d]/,
            readOnly: true,
            anchor: '95%',
            width: 300
        });
        this.tbar5 = new Ext.Toolbar({
            items: ['ID: ', this.numero, ' GRUPO: ', this.desc, '->', {
                    text: 'NUEVO EXAMEN',
                    iconCls: 'nuevo',
                    scope: this,
                    handler: function () {
                        if (mod.empresa.modificar.numero.getValue() == 10) {
                            mod.empresa.examenLab.init(null);
                        } else {
                            mod.empresa.examen.init(null);
                        }
                    }
                }
            ]
        });
        this.tbar16 = new Ext.Toolbar({
            items: ['<b>AREAS MÉDICAS</b>'
            ]
        });

        this.dt_grid_area = new Ext.grid.GridPanel({
            store: this.list_area,
            region: 'west',
            tbar: this.tbar16,
            border: false,
            loadMask: true,
            iconCls: 'icon-grid',
            listeners: {
                rowclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record = grid.getStore().getAt(rowIndex);
                    mod.empresa.modificar.dt_grid_examen.enable();
                    mod.empresa.modificar.numero.setValue(record.get('ar_id'));
                    mod.empresa.modificar.desc.setValue(record.get('ar_desc'));
                    mod.empresa.modificar.list_examen.load();
                }
            },
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            height: 540,
            autoExpandColumn: 'ar_desc',
            columns: [
                new Ext.grid.RowNumberer(),
                {
                    header: 'AREAS MÉDICAS',
                    id: 'ar_desc',
                    dataIndex: 'ar_desc',
                    width: 80
                }
            ]
        });
        this.dt_grid_examen = new Ext.grid.GridPanel({
            store: this.list_examen,
            region: 'west',
            disabled: true,
            border: false,
            tbar: this.tbar5,
            bbar: this.paginador5,
            loadMask: true,
            iconCls: 'icon-grid',
            listeners: {
                rowdblclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record = grid.getStore().getAt(rowIndex);
                    if (mod.empresa.modificar.numero.getValue() == 10) {
                        mod.empresa.examenLab.init(record);
                    } else {
                        mod.empresa.examen.init(record);
                    }
//                    mod.empresa.examen.init(record);
                }
            },
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            height: 540,
            autoExpandColumn: 'ex_desc',
            columns: [
                new Ext.grid.RowNumberer(),
                {
                    id: 'ex_desc',
                    header: 'EXAMEN MÉDICO',
                    dataIndex: 'ex_desc'
                }, {
                    xtype: 'numbercolumn',
                    format: 'S/ 0.00',
                    id: 'ex_tarif',
                    header: 'PRECIO REALTIVO',
                    dataIndex: 'ex_tarif',
                    width: 250
                }
            ]
        });

        this.tab = new Ext.TabPanel({
            bodyStyle: 'padding:10px 10px 20px 10px;',
            labelWidth: 75,
            border: false,
            activeTab: 0,
            items: [{
                    title: 'DATOS DE LA EMPRESA',
                    xtype: 'panel',
                    layout: 'column',
                    labelAlign: 'top',
                    items: [
                        {
                            xtype: 'panel',
                            columnWidth: .60
                            , border: false,
                            bodyStyle: 'padding:0 15px 0px 0;',
                            items: [{
                                    xtype: 'fieldset',
                                    title: 'DATOS DE LA EMPRESA',
                                    items: [new Ext.Panel({
                                            border: false,
                                            columnWidth: .99,
                                            layout: 'form',
                                            items: [this.emp_id]
                                        }), new Ext.Panel({
                                            border: false,
                                            columnWidth: .99,
                                            layout: 'form',
                                            items: [this.emp_desc]
                                        }), new Ext.Panel({
                                            border: false,
                                            columnWidth: .99,
                                            layout: 'form',
                                            items: [this.emp_acro]
                                        }), new Ext.Panel({
                                            border: false,
                                            columnWidth: .99,
                                            layout: 'form',
                                            items: [this.emp_telf]
                                        }), new Ext.Panel({
                                            border: false,
                                            columnWidth: .99,
                                            layout: 'form',
                                            items: [this.emp_estado]
                                        }), new Ext.Panel({
                                            border: false,
                                            columnWidth: .99,
                                            layout: 'form',
                                            items: [this.emp_direc]
                                        }), {
                                            columnWidth: .10,
                                            border: false,
                                            layout: 'form',
                                            bodyStyle: 'padding:15px 8px 0 0;',
                                            items: [{
                                                    xtype: 'button',
                                                    text: 'Guardar',
                                                    iconCls: 'guardar',
                                                    disabled: false,
                                                    handler: function () {
//                                                        funcion ajax
                                                    }
                                                }]
                                        }]
                                }]
                        },
                        {
                            xtype: 'panel',
                            columnWidth: .40
                            , border: false,
                            bodyStyle: 'padding:0 15px 10px 0;',
                            items: [{
                                    xtype: 'fieldset',
                                    title: 'SEDES O SUCURSALES',
                                    items: [new Ext.Panel({
                                            border: false,
                                            columnWidth: .99,
                                            layout: 'form',
                                            items: [this.dt_grid2]
                                        })]
                                }]
                        },
                        {
                            xtype: 'panel',
                            columnWidth: .40
                            , border: false,
                            bodyStyle: 'padding:0 15px 10px 0;',
                            items: [{
                                    xtype: 'fieldset',
                                    title: 'CARGOS Y PUESTOS',
                                    items: [new Ext.Panel({
                                            border: false,
                                            columnWidth: .99,
                                            layout: 'form',
                                            items: [this.dt_grid4]
                                        })]
                                }]
                        }]
                }, {
                    title: 'ASIGNACION DE PROTOCOLOS',
                    xtype: 'panel',
                    layout: 'column',
                    labelAlign: 'top',
                    items: [{
                            xtype: 'panel',
                            columnWidth: .999,
                            border: true,
                            tbar: ['SEDE O SUCURSAL:', this.sede_desc, '   CARGO O PUESTO:', this.cargo_desc, '->', '-', this.btn_pack], //cargo_desc
//                            bodyStyle: 'padding:0 15px 10px 0;', 
                            items: this.dt_grid3
                        }]
                }
//                , {
//                    title: 'COTIZACIONES',
//                    xtype: 'panel',
//                    layout: 'column',
//                    labelAlign: 'top',
//                    items: [{
//                            xtype: 'panel',
//                            columnWidth: .999,
//                            border: true,
//                            tbar: ['SEDE O SUCURSAL:', this.sede_desc2, '   CARGO O PUESTO:', this.cargo_desc2, '->', '-', this.btn_pack2], //cargo_desc
////                            bodyStyle: 'padding:0 15px 10px 0;', 
//                            items: this.dt_grid5
//                        }]
//                }
                , {
                    title: 'AREAS Y EXAMENES',
                    xtype: 'panel',
                    layout: 'column',
                    labelAlign: 'top',
                    items: [
                        {
                            columnWidth: .25,
                            border: true,
                            layout: 'form',
                            items: [this.dt_grid_area]
                        }, {
                            columnWidth: .75,
                            border: true,
                            layout: 'form',
                            items: [this.dt_grid_examen]
                        }

                    ]
                }
//                , {
//                    title: 'ASIGNACION DE USUARIOS',
//                    xtype: 'panel',
//                    layout: 'column',
//                    labelAlign: 'top',
//                    items: []
//                }
            ]
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
//            frame: true,
//            layout: 'column',
//            bodyStyle: 'padding:5px;',
//            labelWidth: 99,
            items: [this.tab]
        });
        this.win = new Ext.Window({
            width: 1000,
            height: 620,
            modal: true,
            title: 'DETALLES DE LA EMPRESA: ' + this.record.get('emp_acro'),
            border: false,
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};
mod.empresa.sede = {
    frm: null,
    win: null,
    sede_emp: null,
    sede_desc: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_controles();
        this.crea_stores();
        this.win.show();
    },
    crea_controles: function () {
        this.sede_emp = new Ext.form.Hidden({
            name: 'sede_emp',
            value: this.record.get('emp_id')
        });
        this.sede_desc = new Ext.form.TextField({
            fieldLabel: '<b>SEDE O SUCURSAL</b>',
            allowBlank: false,
            name: 'sede_desc',
            anchor: '95%'
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
                    items: [this.sede_emp]
                }, {
                    columnWidth: .999,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.sede_desc]
                }
            ],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.empresa.sede.win.el.mask('Guardando…', 'x-mask-loading');

                        this.frm.getForm().submit({
                            params: {
                                acction: 'save_sede'
                            },
                            success: function (form, action) {
                                obj = Ext.util.JSON.decode(action.response.responseText);
//                                Ext.MessageBox.alert('En hora buena', 'La sede se registro correctamente');
                                mod.empresa.sede.win.el.unmask();
                                mod.empresa.modificar.list_sede.reload();
                                mod.empresa.sede.win.close();
                            },
                            failure: function (form, action) {
                                mod.empresa.sede.win.el.unmask();
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
                                mod.empresa.modificar.list_sede.reload();
                                mod.empresa.sede.win.close();
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 600,
            height: 130,
            modal: true,
            title: 'REGISTRO DE SEDES',
            border: false,
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    },
    crea_stores: function () {

    }
};
mod.empresa.cargo = {
    frm: null,
    win: null,
    cargo_emp: null,
    cargo_desc: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_controles();
        this.crea_stores();
        this.win.show();
    },
    crea_controles: function () {
        this.cargo_emp = new Ext.form.Hidden({
            name: 'cargo_emp',
            value: this.record.get('emp_id')
        });
        this.cargo_desc = new Ext.form.TextField({
            fieldLabel: '<b>CARGO O PUESTO</b>',
            allowBlank: false,
            name: 'cargo_desc',
            anchor: '95%'
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            frame: true,
            layout: 'column',
            bodyStyle: 'padding:5px;',
            labelWidth: 99,
            items: [{
                    columnWidth: .999,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.cargo_emp]
                }, {
                    columnWidth: .999,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.cargo_desc]
                }
            ],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.empresa.cargo.win.el.mask('Guardando…', 'x-mask-loading');

                        this.frm.getForm().submit({
                            params: {
                                acction: 'save_cargo'
                            },
                            success: function (form, action) {
                                obj = Ext.util.JSON.decode(action.response.responseText);
//                                Ext.MessageBox.alert('En hora buena', 'La cargo se registro correctamente');
                                mod.empresa.cargo.win.el.unmask();
                                mod.empresa.modificar.list_cargo.reload();
                                mod.empresa.cargo.win.close();
                            },
                            failure: function (form, action) {
                                mod.empresa.cargo.win.el.unmask();
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
                                mod.empresa.modificar.list_cargo.reload();
                                mod.empresa.cargo.win.close();
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 600,
            height: 130,
            modal: true,
            title: 'REGISTRO DE CARGOS',
            border: false,
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    },
    crea_stores: function () {

    }
};

mod.empresa.examen = {
    serv_med: null,
    serv_med2: null,
    serv_desc: null,
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
        Ext.Ajax.request({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            url: '<[controller]>',
            params: {
                acction: 'load_exame',
                format: 'json',
                ex_id: mod.empresa.examen.record.get('ex_id')
            },
            success: function (response, opts) {
                var dato = Ext.decode(response.responseText);
                if (dato.success == true) {
                    mod.empresa.examen.frm.getForm().loadRecord(dato);
                }
            }
        });
    },
    crea_stores: function () {

    },
    crea_controles: function () {
        this.ex_desc = new Ext.form.TextField({
            fieldLabel: '<b>EXAMEN</b>',
            allowBlank: false,
            name: 'ex_desc',
            id: 'ex_desc',
            anchor: '94%'
        });
        this.ex_tarif = new Ext.form.NumberField({
            fieldLabel: '<b>PRECIO RELATIVO</b>',
            allowBlank: false,
            value: '0',
            name: 'ex_tarif',
            anchor: '90%',
            renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
                Ext.util.Format.numberRenderer(value, '0.00');
                return value;
            }
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
                    columnWidth: .80,
                    border: false,
                    layout: 'form',
                    items: [this.ex_desc]
                }, {
                    columnWidth: .20,
                    border: false,
                    layout: 'form',
                    items: [this.ex_tarif]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.empresa.examen.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record !== null) ? 'update_exa' : 'save_exa'
                                , ex_id: (this.record !== null) ? this.record.get('ex_id') : ''
                                , area: mod.empresa.modificar.numero.getValue()
                            },
                            success: function () {
                                Ext.MessageBox.alert('En hora buena', 'El servicio se registro correctamente');
                                mod.empresa.modificar.list_examen.reload();
                                mod.empresa.examen.win.el.unmask();
                                mod.empresa.examen.win.close();
                            },
                            failure: function (form, action) {
                                mod.empresa.examen.win.el.unmask();
                                switch (action.failureType) {
                                    case Ext.form.Action.CLIENT_INVALID:
                                        Ext.Msg.alert('Failure', 'Existen valores Invalidos');
                                        break;
                                    case Ext.form.Action.CONNECT_FAILURE:
                                        Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
                                        break;
                                    case Ext.form.Action.SERVER_INVALID:
                                        Ext.Msg.alert('Failure mik', action.result.error);
                                        mod.empresa.examen.win.close();
                                        break;
                                    default:
                                        Ext.Msg.alert('Failure', action.result.error);
                                }
                                mod.empresa.modificar.list_examen.reload();
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 700,
            height: 130,
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
mod.empresa.examenLab = {
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
        Ext.Ajax.request({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            url: '<[controller]>',
            params: {
                acction: 'load_exameLab',
                format: 'json',
                ex_id: mod.empresa.examenLab.record.get('ex_id')
            },
            success: function (response, opts) {
                var dato = Ext.decode(response.responseText);
                if (dato.success == true) {
                    mod.empresa.examenLab.frm.getForm().loadRecord(dato);
                }
            }
        });
    },
    crea_stores: function () {

    },
    crea_controles: function () {
        this.ex_desc = new Ext.form.TextField({
            fieldLabel: '<b>EXAMEN</b>',
            allowBlank: false,
            name: 'ex_desc',
            id: 'ex_desc',
            anchor: '96%'
        });
        this.ex_tarif = new Ext.form.NumberField({
            fieldLabel: '<b>PRECIO RELATIVO</b>',
            allowBlank: false,
            name: 'ex_tarif',
            value: '0',
            anchor: '87%',
            renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
                Ext.util.Format.numberRenderer(value, '0.00');
                return value;
            }
        });
        this.labc_uni = new Ext.form.TextField({
            fieldLabel: '<b>UNIDAD DE MEDIDA</b>',
            name: 'labc_uni',
            anchor: '94%'
        });
        this.labc_valor = new Ext.form.TextArea({
            fieldLabel: '<b>VALORES NORMALES</b>',
            name: 'labc_valor',
            anchor: '99%',
            height: 70
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
                    items: [this.ex_desc]
                }, {
                    columnWidth: .30,
                    border: false,
                    layout: 'form',
                    items: [this.ex_tarif]
                }, {
                    columnWidth: .70,
                    border: false,
                    layout: 'form',
                    items: [this.labc_uni]
                }, {
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    items: [this.labc_valor]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.empresa.examenLab.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record !== null) ? 'update_exaLab' : 'save_exaLab'
                                , ex_id: (this.record !== null) ? this.record.get('ex_id') : ''
                                , area: mod.empresa.modificar.numero.getValue()
                            },
                            success: function () {
                                Ext.MessageBox.alert('En hora buena', 'El servicio se registro correctamente');
                                mod.empresa.modificar.list_examen.reload();
                                mod.empresa.examenLab.win.el.unmask();
                                mod.empresa.examenLab.win.close();
                            },
                            failure: function (form, action) {
                                mod.empresa.examenLab.win.el.unmask();
                                switch (action.failureType) {
                                    case Ext.form.Action.CLIENT_INVALID:
                                        Ext.Msg.alert('Failure', 'Existen valores Invalidos');
                                        break;
                                    case Ext.form.Action.CONNECT_FAILURE:
                                        Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
                                        break;
                                    case Ext.form.Action.SERVER_INVALID:
                                        Ext.Msg.alert('Failure mik', action.result.error);
                                        mod.empresa.examenLab.win.close();
                                        break;
                                    default:
                                        Ext.Msg.alert('Failure', action.result.error);
                                }
                                mod.empresa.modificar.list_examen.reload();
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 500,
            height: 280,
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
Ext.ns('mod.empresa.examenes');
mod.empresa.rutas = {
    win: null,
    frm: null,
    record: null,
    gp_exam1: null,
    list_exam: null,
    gp_exam2: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.list_exam.load();
        this.list_tficha.load();
        mod.empresa.rutas.update_Total();
        this.crea_controles();
        this.win.show();
    },
    crea_stores: function () {
        this.list_exam = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_exam',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['id', 'ar_desc', 'ex_desc', 'ex_tarif']
        });
        this.list_tficha = new Ext.data.JsonStore({
            root: 'data',
            url: '<[controller]>',
            baseParams: {
                acction: 'list_tficha',
                format: 'json'
            },
            fields: ['tfi_id', 'tfi_desc']
        });
        this.list_exam2 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_exam2',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['ex_desc', 'ar_desc', 'id', 'ex_tarif'],
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.pack = mod.empresa.rutas.dpack.getValue();
                }
            }
        });
    },
    initDrag: function (v) {
        v.dragZone = new Ext.dd.DragZone(v.getEl(), {
            getDragData: function (e) {
                var sourceEl = e.getTarget(v.getView().rowSelector);
                if (sourceEl) {
                    d = sourceEl.cloneNode(true);
                    d.id = Ext.id();
                    return v.dragData = {
                        sourceEl: sourceEl,
                        repairXY: Ext.fly(sourceEl).getXY(),
                        ddel: d,
                        origen: v.id,
                        registro: v.getSelectionModel().getSelections()[0]
                    };
                }
            },
            getRepairXY: function () {
                return this.dragData.repairXY;
            }
        });
    },
    initDrop1: function (v) {
        var d = new Ext.dd.DropTarget(v.getView().scroller, {
            ddGroup: 'dd-prod1',
            notifyDrop: function (ddSource, e, data) {
                var records = ddSource.dragData.selections;
                Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
                v.store.add(records);
                v.store.sort('name', 'ASC');
                mod.empresa.rutas.update_Total();
                return true;
            }
        });
    },
    initDrop2: function (v) {
        var d = new Ext.dd.DropTarget(v.getView().scroller, {
            ddGroup: 'dd-prod2',
            notifyDrop: function (ddSource, e, data) {
                var records = ddSource.dragData.selections;
                Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
                v.store.add(records);
                v.store.sort('name', 'ASC');
                mod.empresa.rutas.update_Total();
                return true;
            }
        });
    },
    update_Total: function () {
        var total = 0;
        Ext.each(mod.empresa.rutas.list_exam2.data.items, function (op, i) {
            total += parseFloat(op.data.ex_tarif);
        });
        Ext.Ajax.request({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            url: '<[controller]>',
            params: {
                acction: 'numeroletra',
                format: 'json',
                total: total
            },
            success: function (response, opts) {
                var dato = Ext.decode(response.responseText);
                if (dato.success == true) {
                    mod.empresa.rutas.total.setValue(total.toFixed(2));
                    mod.empresa.rutas.totaletra.setValue(dato.letra);
                }
            }
        });
    },
    crea_controles: function () {
        this.paginador1 = new Ext.PagingToolbar({
            pageSize: 50,
            store: this.list_exam,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2} Registros',
            emptyMsg: 'No Existe Registros',
            plugins: new Ext.ux.ProgressBarPager()
        });
        this.buscar = new Ext.ux.form.SearchField({
            store: this.list_exam,
            width: 250,
//            style: {//textTransform: "uppercase"},
            emptyText: 'Descripción o Area...'
        });
        this.tbar1 = new Ext.Toolbar({
            items: ['Buscar', this.buscar]
        });
        this.gp_exam1 = new Ext.grid.GridPanel({
            ddGroup: 'dd-prod2',
            tbar: this.tbar1,
            bbar: this.paginador1,
            store: this.list_exam,
            plugins: new Ext.ux.PanelResizer({
                minHeight: 95
            }),
            border: true,
            height: 521,
            cls: 'dd-target',
            enableDragDrop: true,
            stripeRows: true,
            autoExpandColumn: 'pro-desc',
            columns: [//'ex_desc','ar_desc'
                {
                    id: 'id',
                    header: 'ID',
                    dataIndex: 'id',
                    width: 30
                }, {
                    id: 'pro-desc',
                    header: 'DESCRIPCIÓN',
                    dataIndex: 'ex_desc'
                }, {
                    header: 'AREA',
                    dataIndex: 'ar_desc',
                    width: 130
                }, {
                    xtype: 'numbercolumn',
                    format: 'S/ 0.00',
                    header: 'PRECIO',
                    dataIndex: 'ex_tarif',
                    width: 70
                }],
            listeners: {
                render: function (v) {
                    mod.empresa.rutas.initDrop1(v);
                }
            },
            loadMask: true
        });
        this.pack_desc = new Ext.form.TextField({
            fieldLabel: 'TITULO',
            allowBlank: false,
            name: 'pack_desc',
            anchor: '90%'
        });
        this.pack_tficha = new Ext.form.ComboBox({
            store: this.list_tficha,
            hiddenName: 'pack_tficha',
            fieldLabel: 'TIPO DE FICHA',
            allowBlank: false,
            displayField: 'tfi_desc',
            valueField: 'tfi_id',
            minChars: 1,
            typeAhead: false,
            editable: false, //tfi_id, tfi_desc
            triggerAction: 'all',
            mode: 'local',
            anchor: '85%'
        });
        this.total = new Ext.form.TextField({
            name: 'total',
            width: 60,
            readOnly: true,
            fieldStyle: 'background-color: #ddd; background-image: none;'
        });
        this.totaletra = new Ext.form.TextField({
            name: 'totaletra',
            width: 430,
            readOnly: true
        });
        this.bbar = new Ext.Toolbar({
            items: ['-', '<B>SON: </B>', this.totaletra,
//                '-', '<B>CANCELADO: </B>', this.chec_estado, '-', '<B>A CUENTA: </B>', this.adm_cuenta,
                '->', '-', '<B>TOTAL: </B>', this.total, '-']
        });
        this.editor = new Ext.ux.grid.RowEditor({
            saveText: 'Modificar'
        });
        this.editor.on({
            scope: this,
            afteredit: function (roweditor, changes, record, rowIndex) {
                mod.empresa.rutas.update_Total();
                //your save logic here - might look something like this:
//                Ext.Ajax.request({
//                    url: record.phantom ? '/users' : '/users/' + record.get('user_id'),
//                    method: record.phantom ? 'POST' : 'PUT',
//                    params: changes,
//                    success: function () {
//                        //post-processing here - this might include reloading the grid if there are calculated fields
//                    }
//                });
            }
        });
        this.gp_exam2 = new Ext.grid.GridPanel({
            ddGroup: 'dd-prod1',
            tbar: ['->', '-', {
                    text: 'Limpiar',
                    iconCls: 'limpiar',
                    handler: function () {
                        mod.empresa.rutas.list_exam.load();
                        mod.empresa.rutas.list_exam2.removeAll();
                        mod.empresa.rutas.update_Total();
                    }
                }, '-', {
                    text: 'Guardar',
                    iconCls: 'guardar',
                    handler: function () {
                        var conta = mod.empresa.rutas.pack_desc.getValue().length;
                        var conta2 = mod.empresa.rutas.pack_tficha.getValue().length;
                        if (conta >= 1 && conta2 >= 1) {
//                            var conta = length(mod.empresa.rutas.pack_desc.getValue());
                            mod.empresa.rutas.gp_exam2.el.mask('Guardando…', 'x-mask-loading');
                            var total = 0;
                            var sel = '';
                            var ex_id = 0;
                            Ext.each(mod.empresa.rutas.list_exam2.data.items, function (op, i) {
                                total += parseFloat(op.data.ex_tarif);
                                sel += op.data.id + ":" + op.data.ex_tarif + ",";
                                ex_id += parseFloat(op.data.id);
                            });
                            if (ex_id > 0)
                            {
                                sel = sel.substring(0, sel.length - 1);
                                Ext.Ajax.request({
                                    url: '<[controller]>',
                                    params: {
                                        acction: 'savePack',
                                        format: 'json',
                                        pk_desc: mod.empresa.rutas.pack_desc.getValue(),
                                        pk_perfil: mod.empresa.rutas.pack_tficha.getValue(),
                                        pk_emp: mod.empresa.modificar.record.get('emp_id'),
                                        pk_sede: mod.empresa.modificar.sede_desc.getValue(),
                                        pk_cargo: mod.empresa.modificar.cargo_desc.getValue(),
                                        pk_precio: total.toFixed(2),
                                        exId: sel
                                    },
                                    success: function () {
                                        Ext.Msg.alert('Ok', 'Operacion Realizada con Exito!!', function () {
                                            mod.empresa.rutas.win.close();
                                            mod.empresa.modificar.list_perfil.reload({
                                                params: {
                                                    sede_id: mod.empresa.modificar.sede_desc.getValue(),
                                                    cargo_id: mod.empresa.modificar.cargo_desc.getValue()
                                                }
                                            });
//                                            mod.empresa.modificar.list_perfil.reload();
                                            mod.empresa.rutas.gp_exam2.el.unmask();
                                        });
                                    },
                                    failure: function (form, action) {
                                        switch (action.failureType) {
                                            case Ext.form.Action.CLIENT_INVALID:
                                                Ext.Msg.alert('Failure', 'Existen valores Invalidos');
                                                break;
                                            case Ext.form.Action.CONNECT_FAILURE:
                                                Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
                                                break;
                                            case Ext.form.Action.SERVER_INVALID:
                                                Ext.Msg.alert('Failure', action.result.error);
                                                break;
                                            default:
                                                Ext.Msg.alert('Failure', action.result.error);
                                        }
                                    }
                                });
                            } else {
                                Ext.Msg.alert('Error', 'Debe Seleccionar al menos 1 examen');
                                mod.empresa.rutas.gp_exam2.el.unmask();
                            }
                        } else {
                            Ext.Msg.alert('Error ', 'No tiene TITULO o no esta asignado el TIPO DE FICHA');
                            mod.empresa.rutas.gp_exam2.el.unmask();
                        }
                    }
                }, '-'],
            bbar: this.bbar,
            store: this.list_exam2,
            height: 480,
            cls: 'dd-target',
            enableDragDrop: true,
            stripeRows: true,
            plugins: [this.editor],
            autoExpandColumn: 'pro-desc',
            columns: [//'ex_desc','ar_desc'
                {
                    id: 'id',
                    header: 'ID',
                    dataIndex: 'id',
                    width: 30
                }, {
                    id: 'pro-desc',
                    header: 'DESCRIPCIÓN',
                    dataIndex: 'ex_desc'
                }, {
                    header: 'AREA',
                    dataIndex: 'ar_desc',
                    width: 150
                }, {
                    xtype: 'numbercolumn',
                    format: 'S/ 0.00',
                    header: 'PRECIO',
                    dataIndex: 'ex_tarif',
                    width: 70,
                    editor: {
                        xtype: 'numberfield'
                    }
                }],
            listeners: {
                render: function (v) {
                    mod.empresa.rutas.initDrop2(v);
                }
            }
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            frame: true,
            layout: 'column',
            bodyStyle: 'padding:2px 2px 2px 2px;',
//            labelAlign: 'top',
            items: [{
                    columnWidth: .45,
                    border: false,
                    layout: 'form',
                    bodyStyle: 'padding:0 15px 0px 0;',
                    items: [{
                            xtype: 'fieldset',
                            title: 'ORIGEN',
                            items: this.gp_exam1
                        }]
//                    items: [this.ex_desc]
                }, {
                    columnWidth: .55,
                    border: false,
                    layout: 'form',
                    bodyStyle: 'padding:0 15px 0px 0;',
                    items: [{
                            xtype: 'fieldset',
                            title: 'DESTINO',
                            layout: 'column',
//                            labelAlign: 'top',
                            items: [{
                                    columnWidth: .50,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 50,
                                    items: [this.pack_desc]//pk_ord
                                }, {
                                    columnWidth: .50,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 90,
                                    items: [this.pack_tficha]//pk_ord
                                }
                                , {
                                    columnWidth: .99,
                                    border: false,
                                    layout: 'form',
                                    items: [this.gp_exam2]//pk_ord
                                }
                            ]
//                            items: this.gp_exam1
                        }]
                }]
        });
        this.win = new Ext.Window({
            width: 1200,
            height: 620,
            modal: true,
            title: 'RELACION DE RUTAS:',
            border: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};
mod.empresa.rutasEdit = {
    win: null,
    frm: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.list_perfil2.load();
        this.crea_controles();
        this.win.show();
        if (this.record !== null) {
            this.carga_data();
        }
    },
    carga_data: function () {
        Ext.Ajax.request({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            url: '<[controller]>',
            params: {
                acction: 'load_data_ruta',
                format: 'json',
                pk_id: mod.empresa.rutasEdit.record.get('pk_id')
            },
            success: function (response, opts) {
                var dato = Ext.decode(response.responseText);
                if (dato.success == true) {
                    mod.empresa.rutasEdit.frm.getForm().loadRecord(dato);
                    mod.empresa.rutasEdit.update_Total();
                }
            }
        });
    },
    crea_stores: function () {
        this.list_perfil2 = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_perfil2',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.pk_id = mod.empresa.rutasEdit.record.get('pk_id');
                }
            },
            fields: ['dpk_pkid', 'ex_id', 'ex_desc', 'ar_desc', 'dpk_usu', 'dpk_fech', 'dpk_precio']
        });
    },
    update_Total: function () {
        var total = 0;
        Ext.each(this.list_perfil2.data.items, function (op, i) {
            total += parseFloat(op.data.dpk_precio);
        });
        Ext.Ajax.request({
            waitMsg: 'Recuperando Informacion...',
            waitTitle: 'Espere',
            url: '<[controller]>',
            params: {
                acction: 'numeroletra',
                format: 'json',
                total: total
            },
            success: function (response, opts) {
                var dato = Ext.decode(response.responseText);
                if (dato.success == true) {
                    mod.empresa.rutasEdit.dpk_precio.setValue(total.toFixed(2));
                    mod.empresa.rutasEdit.totaletra.setValue(dato.letra);
                }
            }
        });
    },
    crea_controles: function () {
        this.sede_desc = new Ext.form.TextField({
            fieldLabel: 'SEDE',
            allowBlank: false,
            name: 'sede_desc',
            anchor: '90%',
            readOnly: true
        });
        this.cargo_desc = new Ext.form.TextField({
            fieldLabel: 'CARGO',
            allowBlank: false,
            name: 'cargo_desc',
            anchor: '90%',
            readOnly: true
        });
        this.tfi_desc = new Ext.form.TextField({
            fieldLabel: 'PERFIL',
            allowBlank: false,
            name: 'tfi_desc',
            anchor: '90%',
            readOnly: true
        });
        this.pk_desc = new Ext.form.TextField({
            fieldLabel: '<b>TITULO</b>',
            allowBlank: false,
            name: 'pk_desc',
            //readOnly: (mod.empresa.rutasEdit.record.get('horas') > 24) ? true : false,
            anchor: '90%'
        });
        this.dpk_precio = new Ext.form.TextField({
            name: 'dpk_precio',
            width: 60,
            readOnly: true,
            fieldStyle: 'background-color: #ddd; background-image: none;'
        });
        this.totaletra = new Ext.form.TextField({
            name: 'totaletra',
            width: 400,
            readOnly: true
        });
        this.bbar = new Ext.Toolbar({
            items: ['-', '<B>SON: </B>', this.totaletra,
//                '-', '<B>CANCELADO: </B>', this.chec_estado, '-', '<B>A CUENTA: </B>', this.adm_cuenta,
                '->', '-', '<B>TOTAL: </B>', this.dpk_precio, '-']
        });
        this.editor = new Ext.ux.grid.RowEditor({
            saveText: 'Modificar'
        });
        this.editor.on({
            scope: this,
            afteredit: function (roweditor, changes, record, rowIndex) {
                if (mod.empresa.rutasEdit.record.get('horas') <= 24) {
                    var total = 0;
                    Ext.each(this.list_perfil2.data.items, function (op, i) {
                        total += parseFloat(op.data.dpk_precio);
                    });
                    Ext.Ajax.request({
                        waitMsg: 'Recuperando Informacion...',
                        waitTitle: 'Espere',
                        url: '<[controller]>',
                        params: {
                            acction: 'update_rutaExa',
                            format: 'json',
                            dpk_pkid: record.data.dpk_pkid,
                            dpk_exid: record.data.ex_id,
                            dpk_precio: changes.dpk_precio,
                            total: total
                        },
                        success: function (response, opts) {
                            mod.empresa.modificar.list_perfil.reload();
                            mod.empresa.rutasEdit.list_perfil2.reload();
                            mod.empresa.rutasEdit.update_Total();
                        }
                    });
                    mod.empresa.rutasEdit.update_Total();
//                Ext.Msg.alert('Error ', changes.dpk_precio + ' / ' + record.data.ex_id + ' / ' + record.data.dpk_pkid);
                } else {
                    Ext.Msg.alert('Alerta', 'Ya transcurrieron las 24 horas. </br>El sistema no almacenara las modificaciones realizadas.');
                }
            }
        });
        this.pk_estado = new Ext.form.RadioGroup({
            fieldLabel: '<b>Estado</b>',
            itemCls: 'x-check-group-alt',
            columns: 2,
            items: [
                {boxLabel: 'ACTIVADO', checked: true, name: 'pk_estado', inputValue: '1'},
                {boxLabel: 'BLOQUEADO', name: 'pk_estado', inputValue: '0'}
            ]
        });
        this.dt_grid3 = new Ext.grid.GridPanel({
            store: this.list_perfil2,
            region: 'west',
            border: true,
            tbar: [{
                    text: 'AGREGAR EXAMEN',
                    iconCls: 'nuevo',
                    handler: function () {
                        if (mod.empresa.rutasEdit.record.get('horas') <= 24) {
                            var records = mod.empresa.rutasEdit.record;
                            mod.empresa.addExamen.init(records);
                        } else {
                            Ext.Msg.alert('Alerta', 'Ya transcurrieron las 24 horas. No puede agregar un examen');
                        }
                    }
                }, '->', '-', {
                    text: 'Actualizar Datos',
                    iconCls: 'guardar',
                    handler: function () {
                        Ext.Ajax.request({
                            waitMsg: 'Recuperando Informacion...',
                            waitTitle: 'Espere',
                            url: '<[controller]>',
                            params: {
                                acction: 'update_pk',
                                format: 'json',
                                pk_id: mod.empresa.rutasEdit.record.get('pk_id'),
                                pk_estado: mod.empresa.rutasEdit.pk_estado.getValue().inputValue,
                                pk_desc: mod.empresa.rutasEdit.pk_desc.getValue()
                            },
                            success: function (response, opts) {
                                mod.empresa.modificar.list_perfil.reload();
                            }
                        });
                    }
                }, '-', {
                    text: 'Cerrar Ventana',
                    iconCls: 'eliminar',
                    handler: function () {
                        mod.empresa.rutasEdit.win.close();
                    }
                }],
            bbar: this.bbar,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: [this.editor],
            height: 470,
            autoExpandColumn: 'ex_desc',
            columns: [
                new Ext.grid.RowNumberer(), {
                    id: 'ex_desc',
                    header: 'EXAMEN',
                    dataIndex: 'ex_desc'
                }, {
                    header: 'AREA',
                    dataIndex: 'ar_desc',
                    width: 120
                }, {
                    header: 'USUARIO',
                    width: 60,
                    dataIndex: 'dpk_usu'
                }, {
                    header: 'FECHA DE REGISTRO',
                    width: 130,
                    dataIndex: 'dpk_fech'//emp_desc
                }, {
                    xtype: 'numbercolumn',
                    format: 'S/ 0.00',
                    header: 'PRECIO',
                    dataIndex: 'dpk_precio',
                    width: 70,
                    editor: {
                        xtype: 'numberfield'
                    }
                }, {
                    header: 'pk',
                    width: 60,
                    dataIndex: 'dpk_pkid'
                }, {
                    header: 'ex_id',
                    width: 60,
                    dataIndex: 'ex_id'
                }
            
            //dpk_pkid: record.data.dpk_pkid,
                           // dpk_exid: record.data.ex_id,
            ]
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
//            frame: true,
            layout: 'column',
            bodyStyle: 'padding:2px 2px 2px 2px;',
            items: [{
                    columnWidth: .99,
                    border: false,
                    layout: 'form',
                    bodyStyle: 'padding:5px;',
                    items: [{
                            xtype: 'fieldset',
                            title: 'RUTA',
                            layout: 'column',
//                            labelAlign: 'top',
                            items: [{
                                    columnWidth: .33,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 50,
                                    items: [this.sede_desc]//pk_ord this.pk_estado
                                }, {
                                    columnWidth: .33,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 50,
                                    items: [this.cargo_desc]//pk_ord
                                }, {
                                    columnWidth: .33,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 50,
                                    items: [this.pk_estado]//pk_ord
                                }, {
                                    columnWidth: .50,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 50,
                                    items: [this.tfi_desc]//pk_ord
                                }, {
                                    columnWidth: .50,
                                    border: false,
                                    layout: 'form',
                                    labelWidth: 50,
                                    items: [this.pk_desc]//pk_ord
                                }
                            ]
                        }
                        , {
                            columnWidth: .99,
                            border: false,
                            layout: 'form',
                            labelWidth: .90,
                            items: [this.dt_grid3]//pk_ord
                        }
                    ]
                }]
        });
        this.win = new Ext.Window({
            width: 800,
            height: 620,
            modal: true,
            title: 'EDITAR RELACION DE RUTAS: ',
            border: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};

mod.empresa.addExamen = {
    frm: null,
    win: null,
    cargo_emp: null,
    cargo_desc: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.list_exa.load();
        this.crea_controles();
        this.win.show();
    },
    crea_stores: function () {
        this.list_exa = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_exa',
                format: 'json'
            },
            fields: ['ex_id', 'ar_desc', 'ex_desc', 'ex_tarif'],
            root: 'data'
        });
    },
    crea_controles: function () {
        this.resultTpl = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '{ex_desc}',
                '<h4><span>{ar_desc}</span></h4>',
                '</div>',
                '</div></tpl>'
                );

        this.cboExamen = new Ext.form.ComboBox({
            store: this.list_exa,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.resultTpl,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'cboExamen',
            displayField: 'ex_desc',
            valueField: 'ex_id',
            allowBlank: false,
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>Examen</b>',
            mode: 'remote',
            anchor: '95%',
            listeners: {
                scope: this,
                select: function (combo, registro, indice) {
                    var recordSelected = combo.getStore().getAt(indice);
                    this.ex_tarif.setValue(recordSelected.get('ex_tarif'));
                }
            }
        });
        this.ex_tarif = new Ext.form.NumberField({
            fieldLabel: '<b>PRECIO</b>',
            allowBlank: false,
            name: 'ex_tarif',
            anchor: '90%',
            renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
                Ext.util.Format.numberRenderer(value, '0.00');
                return value;
            }
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            frame: true,
            layout: 'column',
            bodyStyle: 'padding:5px;',
            labelWidth: 50,
            items: [{
                    columnWidth: .70,
                    border: false,
//                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.cboExamen]
                }, {
                    columnWidth: .30,
                    border: false,
//                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.ex_tarif]
                }
            ],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.empresa.addExamen.win.el.mask('Guardando…', 'x-mask-loading');
                        var total = 0;
                        Ext.each(mod.empresa.rutasEdit.list_perfil2.data.items, function (op, i) {
                            total += parseFloat(op.data.dpk_precio);
                        });
                        var totales = total + mod.empresa.addExamen.ex_tarif.getValue();
                        this.frm.getForm().submit({
                            params: {
                                acction: 'save_addExamen',
                                pk_id: this.record.get('pk_id'),
                                total: total
                            },
                            success: function (form, action) {
                                obj = Ext.util.JSON.decode(action.response.responseText);
//                                Ext.MessageBox.alert('En hora buena', 'La cargo se registro correctamente');
                                mod.empresa.addExamen.win.el.unmask();
                                mod.empresa.addExamen.win.close();

                                mod.empresa.modificar.list_perfil.reload();
                                mod.empresa.rutasEdit.list_perfil2.reload();
                                Ext.Ajax.request({
                                    waitMsg: 'Recuperando Informacion...',
                                    waitTitle: 'Espere',
                                    url: '<[controller]>',
                                    params: {
                                        acction: 'numeroletra',
                                        format: 'json',
                                        total: totales
                                    },
                                    success: function (response, opts) {
                                        var dato = Ext.decode(response.responseText);
                                        if (dato.success == true) {
                                            mod.empresa.rutasEdit.dpk_precio.setValue(totales.toFixed(2));
                                            mod.empresa.rutasEdit.totaletra.setValue(dato.letra);
                                        }
                                    }
                                });
                            },
                            failure: function (form, action) {
                                mod.empresa.addExamen.win.el.unmask();
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
                                mod.empresa.rutasEdit.list_perfil2.reload();
                                mod.empresa.addExamen.win.close();
                            }
                        });
                    }
                }]
        });
        this.win = new Ext.Window({
            width: 600,
            height: 130,
            modal: true,
            title: 'AGREGAR EXAMEN',
            border: false,
            maximizable: false,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    }
};
Ext.onReady(mod.empresa.init, mod.empresa);