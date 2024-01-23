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
Ext.ns('mod.odonto');
mod.odonto = {
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
                acction: 'list_oftalmo',
                format: 'json'
            },
            listeners: {
                'beforeload': function () {
                    this.baseParams.columna = mod.odonto.descripcion.getValue();
                }
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['adm', 'st_id', 'tfi_desc', 'emp_desc', 'pac_ndoc', 'nombre', 'pac_sexo', 'caries', 'obturada1', 'ausente', 'extraer', 'fecha', 'usu', 'odo_id', 'valida']
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
                this.buscador, '->',
                '|', {
                    text: 'Reporte x Fecha',
                    iconCls: 'reporte',
                    handler: function () {
                        mod.odonto.rfecha.init(null);
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
//                    var admi = record.get('adm');
                    if (record.get('st_id') >= 1) {
                        mod.odonto.nuevo.init(record);
                    } else {
//                        console.log(admi);
                        mod.odonto.nuevo.init(record);
                    }
                },
                rowcontextmenu: function (grid, index, event) {
                    event.stopEvent();
                    var record = grid.getStore().getAt(index);
                    if (record.get('st_id') == "1") {
                        new Ext.menu.Menu({
                            items: [{
                                    text: 'INFORME N°: <B>' + record.get('adm') + '<B>',
                                    iconCls: 'odo1',
                                    handler: function () {
                                        if (record.get('st_id') >= 1) {
                                            mod.odonto.reporte.nuevo.init(record);
                                        } else {
                                            Ext.MessageBox.alert('Observaciones', 'El paciente no fue registrado correctamente');
                                        }
                                    }
                                }, {
                                    text: 'INFORME N°: <B>' + record.get('adm') + '<B>',
                                    iconCls: 'odo2',
                                    handler: function () {
                                        if (record.get('st_id') >= 1) {
                                            mod.odonto.reporte2.nuevo.init(record);
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
//            items:[this.buscador],
            columns: [{
                    header: 'ST',
                    width: 25,
                    sortable: true,
                    dataIndex: 'st_id',
                    renderer: function renderIcon(val) {
                        if (val == 0) {
                            return  '<img src="<[images]>/nuevo.png" title="REGISTRAR" height="15">';
                        } else if (val == 1) {
                            return  '<img src="<[images]>/saveIcon.png" title="GUARDADO" height="15">';
                        }
                    }
                },
                {
                    header: 'N° FICHA',
                    width: 60,
                    sortable: true,
                    dataIndex: 'adm'
                }, {
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
Ext.ns('mod.odonto.reporte.nuevo');
mod.odonto.rfecha = {
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
                        mod.odonto.rfecha.win.el.mask('Guardando…', 'x-mask-loading');
                        var params = "fini=" + mod.odonto.rfecha.f_inicio.getRawValue() + "&ffinal=" + mod.odonto.rfecha.f_final.getRawValue() + "&empresa=" + mod.odonto.rfecha.cboEmpre.getValue();
                        new Ext.Window({
                            title: 'Referencia',
                            width: 200,
                            height: 200,
                            maximizable: true,
                            modal: true,
                            closeAction: 'close',
                            resizable: true,
                            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadexcel&sys_modname=mod_odonto&sys_report=reporte&" + params + "'></iframe>"

                        }).show();
                        mod.odonto.rfecha.win.el.unmask();
                        mod.odonto.rfecha.win.close();

                    }
                }, '-', {
                    text: 'Reporte PDF',
                    iconCls: 'reporte',
                    handler: function () {
                        mod.odonto.rfecha.win.el.mask('Guardando…', 'x-mask-loading');
                        var params = "fini=" + mod.odonto.rfecha.f_inicio.getRawValue() + "&ffinal=" + mod.odonto.rfecha.f_final.getRawValue() + "&empresa=" + mod.odonto.rfecha.cboEmpre.getValue();
                        new Ext.Window({
                            title: 'Referencia',
                            width: 800,
                            height: 600,
                            maximizable: true,
                            modal: true,
                            closeAction: 'close',
                            resizable: true,
                            html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_odonto&sys_report=reportefecha&" + params + "'></iframe>"

                        }).show();
                        mod.odonto.rfecha.win.el.unmask();
                        mod.odonto.rfecha.win.close();

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
mod.odonto.reporte.nuevo = {
    win: null,
    record: null,
    init: function (r) {
        var params = "adm=" + r.get('adm');
//        var params = "adm=2";
        this.record = r,
//                console.log(r);
                this.win = new Ext.Window({
                    title: 'ODONTOGRAMA :' + this.record.get('nombre'),
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
                    html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_odonto&sys_report=reporte&" + params + "'></iframe>"
                });
        this.win.show();
    }
};
Ext.ns('mod.odonto.reporte2.nuevo');
mod.odonto.reporte2.nuevo = {
    win: null,
    record: null,
    init: function (r) {
        var params = "adm=" + r.get('adm');
//        var params = "adm=2";
        this.record = r,
//                console.log(r);
                this.win = new Ext.Window({
                    title: 'ODONTOGRAMA :' + this.record.get('nombre'),
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
                    html: "<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_odonto&sys_report=reporte2&" + params + "'></iframe>"
                });
        this.win.show();
    }
};
mod.odonto.nuevo = {
    frm: null,
    win: null,
    record: null,
    init: function (r) {
        this.record = r;
        this.crea_stores();
        this.crea_controles();
        this.st_dientes.load();
        this.st_dientes2.load();
        this.st_dientes3.load();
        this.st_dientes4.load();
        this.list_pato.load();
        this.load_diag.load();
        this.list_trata.load();//
        this.list_reco.load();//
        if (this.record !== null) {
            this.carga_data();
        }
        this.win.show();
    },
    crea_stores: function () {
        this.st_dientes = new Ext.data.JsonStore({
            remoteSort: true,
            url: '<[controller]>',
            baseParams: {
                acction: 'carga_dientes',
                adm: this.record.get('adm'),
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['placa', 'barra1', 'barra11', 'barra2', 'barra22', 'barra3', 'barra33', 'corona', 'dt_diag_raiz', 'dt_diag_coro', 'dt_diag_text', 'pfondo1', 'pfondo2', 'pfondo3', 'pfondo4', 'pfondo5', 'pborde1', 'pborde2', 'pborde3', 'pborde4', 'pborde5', 'dient_nro', 'dient_ord', 'piez_desc', 'pz1', 'pz2', 'pz3', 'pz4', 'pz5']
        });
        this.list_trata = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_trata',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['trata_id', 'trata_desc', 'trata_adm'],
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.adm = mod.odonto.nuevo.record.get('adm');
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
            fields: ['reco_id', 'reco_desc', 'reco_adm'],
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.adm = mod.odonto.nuevo.record.get('adm');
                }
            }
        });
        this.list_pato = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'list_pato',
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['gpato_diente', 'gpato_desc'],
            listeners: {
                'beforeload': function (store, options) {
                    this.baseParams.adm = mod.odonto.nuevo.record.get('adm');
                }
            }
        });
        this.st_dientes2 = new Ext.data.JsonStore({
            remoteSort: true,
            url: '<[controller]>',
            baseParams: {
                acction: 'carga_dientes2',
                adm: this.record.get('adm'),
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['placa', 'barra1', 'barra11', 'barra2', 'barra22', 'barra3', 'barra33', 'corona', 'dt_diag_raiz', 'dt_diag_coro', 'dt_diag_text', 'pfondo1', 'pfondo2', 'pfondo3', 'pfondo4', 'pfondo5', 'pborde1', 'pborde2', 'pborde3', 'pborde4', 'pborde5', 'dient_nro', 'dient_ord', 'piez_desc', 'pz1', 'pz2', 'pz3', 'pz4', 'pz5']
        });
        this.st_dientes3 = new Ext.data.JsonStore({
            remoteSort: true,
            url: '<[controller]>',
            baseParams: {
                acction: 'carga_dientes3',
                adm: this.record.get('adm'),
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['placa', 'barra1', 'barra11', 'barra2', 'barra22', 'barra3', 'barra33', 'corona', 'dt_diag_raiz', 'dt_diag_coro', 'dt_diag_text', 'pfondo1', 'pfondo2', 'pfondo3', 'pfondo4', 'pfondo5', 'pborde1', 'pborde2', 'pborde3', 'pborde4', 'pborde5', 'dient_nro', 'dient_ord', 'piez_desc', 'pz1', 'pz2', 'pz3', 'pz4', 'pz5']
        });
        this.st_dientes4 = new Ext.data.JsonStore({
            remoteSort: true,
            url: '<[controller]>',
            baseParams: {
                acction: 'carga_dientes4',
                adm: this.record.get('adm'),
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['placa', 'barra1', 'barra11', 'barra2', 'barra22', 'barra3', 'barra33', 'corona', 'dt_diag_raiz', 'dt_diag_coro', 'dt_diag_text', 'pfondo1', 'pfondo2', 'pfondo3', 'pfondo4', 'pfondo5', 'pborde1', 'pborde2', 'pborde3', 'pborde4', 'pborde5', 'dient_nro', 'dient_ord', 'piez_desc', 'pz1', 'pz2', 'pz3', 'pz4', 'pz5']
        });
        this.load_diag = new Ext.data.JsonStore({
            remoteSort: true,
            url: '<[controller]>',
            baseParams: {
                acction: 'load_diag',
//                pac_id: this.record.get('adm'),
                format: 'json'
            },
            root: 'data',
            totalProperty: 'total',
            fields: ['exa_id', 'exa_desc']
        });
    },
    crea_controles: function () {

        var tpl = new Ext.XTemplate(
                '<tpl for=".">',
                '<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false">',
                '<DIV class="thumb-wrap2">',
                '<DIV style="height: 19px; width: 45px;border: 1px dotted;">',
                '<center style="font-weight: bold; color:blue;font-size: 14px;">{dt_diag_text}</center>',
                '</DIV>',
                '<DIV style="height: 64px; width: 45px;">',
                '<DIV style="height: 19px; width: 45px;">',
                '<center><b>{dient_nro}</b></center>',
                '</DIV>',
                '<DIV style="height: 45px; width: 45px;">',
                '<svg width="45" height="45px" stroke-width="2">',
                '<!-- "1" --><path fill="{pfondo1}" stroke="{pborde1}" id="{dient_nro}" onclick="mod.odonto.nuevo.cargar_pieza_1(id);" stroke-width="1.5" d="M22.194,13.117c5.162,0,9.343,4.181,9.343,9.341 c0,5.12-4.181,9.301-9.343,9.301c-5.119,0-9.3-4.181-9.3-9.301C12.893,17.298,17.074,13.117,22.194,13.117z"/>',
                '<!-- "2" --><path fill="{pfondo2}" stroke="{pborde2}" id="{dient_nro}" onclick="mod.odonto.nuevo.cargar_pieza_2(id);" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M35.333,9.319c-7.251-7.208-18.982-7.208-26.235,0l0,0l6.526,6.527l0,0c3.626-3.625,9.514-3.625,13.181,0L35.333,9.319z"/>',
                '<!-- "3" --><path fill="{pfondo3}" stroke="{pborde3}" id="{dient_nro}" onclick="mod.odonto.nuevo.cargar_pieza_3(id);" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M9.097,35.556c-7.253-7.252-7.253-18.984,0-26.237l0,0l6.526,6.527l0,0c-3.668,3.67-3.668,9.556,0,13.183L9.097,35.556z"/>',
                '<!-- "4" --><path fill="{pfondo4}" stroke="{pborde4}" id="{dient_nro}" onclick="mod.odonto.nuevo.cargar_pieza_4(id);" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M35.333,9.319c7.21,7.253,7.21,18.985,0,26.237l0,0l-6.526-6.527l0,0c3.627-3.627,3.627-9.513,0-13.183L35.333,9.319z"/>',
                '<!-- "5" --><path fill="{pfondo5}" stroke="{pborde5}" id="{dient_nro}" onclick="mod.odonto.nuevo.cargar_pieza_5(id);" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M9.097,35.556c7.253,7.251,18.984,7.251,26.235,0l0,0l-6.526-6.527l0,0c-3.667,3.669-9.556,3.669-13.181,0L9.097,35.556z"/>',
                '<!-- "O" corona -->',
                '<path fill="none" stroke="{corona}" stroke-width="3" d="M22.194,1.603c11.527,0,20.856,9.327,20.856,20.855c0,11.484-9.33,20.856-20.856,20.856 c-11.484,0-20.855-9.373-20.855-20.856C1.338,10.93,10.709,1.603,22.194,1.603z"/>',
                '<!-- "C" placa -->',
                '<path fill="none" stroke="{placa}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M2.393,15.946c2.729-8.314,10.561-14.342,19.8-14.342c9.241,0,17.073,6.028,19.799,14.343"/>',
                '<!-- "X" extraer, curacion y fractura-->',
                '<line fill="none" stroke="{barra1}" stroke-opacity="{barra11}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" x1="2.314" y1="42.339" x2="42.575" y2="2.079"/>',
                '<line fill="none" stroke="{barra2}" stroke-opacity="{barra22}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" x1="42.328" y1="42.564" x2="2.068" y2="2.303"/>',
                '<!-- "-" PUENTE -->',
                '<line fill="none" stroke="{barra3}" stroke-opacity="{barra33}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" x1="54.078" y1="22.161" x2="0" y2="22.16"/>',
                '</svg>',
                '</DIV>',
                '</DIV>',
                '</DIV>',
                '</DIV>',
                '</tpl>'
                );

        var tpl2 = new Ext.XTemplate(
                '<tpl for=".">',
                '<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false">',
                '<DIV class="thumb-wrap2">',
                '<DIV style="height: 64px; width: 45px;">',
                '<DIV style="height: 45px; width: 45px;">',
                '<svg width="45" height="45px" stroke-width="2">',
                '<!-- "1" --><path fill="{pfondo1}" stroke="{pborde1}" id="{dient_nro}" onclick="mod.odonto.nuevo.cargar_pieza_1(id);" stroke-width="1.5" d="M22.194,13.117c5.162,0,9.343,4.181,9.343,9.341 c0,5.12-4.181,9.301-9.343,9.301c-5.119,0-9.3-4.181-9.3-9.301C12.893,17.298,17.074,13.117,22.194,13.117z"/>',
                '<!-- "2" --><path fill="{pfondo2}" stroke="{pborde2}" id="{dient_nro}" onclick="mod.odonto.nuevo.cargar_pieza_2(id);" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M35.333,9.319c-7.251-7.208-18.982-7.208-26.235,0l0,0l6.526,6.527l0,0c3.626-3.625,9.514-3.625,13.181,0L35.333,9.319z"/>',
                '<!-- "3" --><path fill="{pfondo3}" stroke="{pborde3}" id="{dient_nro}" onclick="mod.odonto.nuevo.cargar_pieza_3(id);" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M9.097,35.556c-7.253-7.252-7.253-18.984,0-26.237l0,0l6.526,6.527l0,0c-3.668,3.67-3.668,9.556,0,13.183L9.097,35.556z"/>',
                '<!-- "4" --><path fill="{pfondo4}" stroke="{pborde4}" id="{dient_nro}" onclick="mod.odonto.nuevo.cargar_pieza_4(id);" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M35.333,9.319c7.21,7.253,7.21,18.985,0,26.237l0,0l-6.526-6.527l0,0c3.627-3.627,3.627-9.513,0-13.183L35.333,9.319z"/>',
                '<!-- "5" --><path fill="{pfondo5}" stroke="{pborde5}" id="{dient_nro}" onclick="mod.odonto.nuevo.cargar_pieza_5(id);" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M9.097,35.556c7.253,7.251,18.984,7.251,26.235,0l0,0l-6.526-6.527l0,0c-3.667,3.669-9.556,3.669-13.181,0L9.097,35.556z"/>',
                '<!-- "O" -->',
                '<path fill="none" stroke="{corona}" stroke-width="3" d="M22.194,1.603c11.527,0,20.856,9.327,20.856,20.855c0,11.484-9.33,20.856-20.856,20.856 c-11.484,0-20.855-9.373-20.855-20.856C1.338,10.93,10.709,1.603,22.194,1.603z"/>',
                '<!-- "C" -->',
                '<path fill="none" stroke="{placa}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M2.393,15.946c2.729-8.314,10.561-14.342,19.8-14.342c9.241,0,17.073,6.028,19.799,14.343"/>',
                '<!-- "X" extraer y curacion -->',
                '<line fill="none" stroke="{barra1}" stroke-opacity="{barra11}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" x1="2.314" y1="42.339" x2="42.575" y2="2.079"/>',
                '<line fill="none" stroke="{barra2}" stroke-opacity="{barra22}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" x1="42.328" y1="42.564" x2="2.068" y2="2.303"/>',
                '<!-- "-" PUENTE -->',
                '<line fill="none" stroke="{barra3}" stroke-opacity="{barra33}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" x1="54.078" y1="22.161" x2="0" y2="22.16"/>',
                '</svg>',
                '</DIV>',
                '<DIV style="height: 19px; width: 45px;">',
                '<center><b>{dient_nro}</b></center>',
                '</DIV>',
                '</DIV>',
                '<DIV style="height: 19px; width: 45px;border: 1px dotted;">',
                '<center style="font-weight: bold; color:blue;font-size: 14px;">{dt_diag_text}</center>',
                '</DIV>',
                '</DIV>',
                '</DIV>',
                '</tpl>'
                );

        this.odont1 = new Ext.DataView({
            autoScroll: true,
            id: 'dientes_view',
            store: this.st_dientes,
            tpl: tpl,
            autoHeight: false,
            height: 100,
            multiSelect: false,
            overClass: 'x-view-over',
            itemSelector: 'div.thumb-wrap2',
            emptyText: 'No hay dientes para mostrar',
            listeners: {
                dblclick: function (dataview, index, item, e) {
                    var rec = dataview.store.getAt(index);
                    var diag_id = mod.odonto.nuevo.id_examen.getValue();
                    var diag = mod.odonto.nuevo.examen.getValue();
                    if (diag_id > 0) {
                        var pac_id = mod.odonto.nuevo.record.get('adm');
                        if (diag_id == 1 || diag_id == 2) {

                        } else {
                            var gramad_diag_raiz;
                            var gramad_diag_coro;
                            var gramad_diag_text;
                            if (diag_id == 3) {       //DIENTE AUSENTE
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 4) {//DIENTE PARA EXTRAER
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 5) {//FRACTURA
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 6) {//DIENTE ECTOPICO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'E';
                            } else if (diag_id == 7) {//PLACA DENTAL
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 8) {//PUENTE
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 9) {//CORONA EN BUEN ESTADO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 10) {//CORONA EN MAL ESTADO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 11) {//MOVILIDAD
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'M1';
                            } else if (diag_id == 12) {//DIENTE DISCROMICO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'DIS';
                            }
                            Ext.Ajax.request({
                                url: '<[controller]>',
                                params: {
                                    acction: 'grama_diente',
                                    format: 'json',
                                    pac_id: pac_id,
                                    diente: rec.get('dient_nro'),
                                    gramad_diag_raiz: gramad_diag_raiz,
                                    gramad_diag_coro: gramad_diag_coro,
                                    gramad_diag_text: gramad_diag_text
                                },
                                success: function () {
                                    mod.odonto.nuevo.st_dientes.load();
                                    mod.odonto.nuevo.st_dientes2.load();
                                    mod.odonto.nuevo.st_dientes3.load();
                                    mod.odonto.nuevo.st_dientes4.load();
                                    (diag_id == 13) ? mod.odonto.cie10.init(mod.odonto.nuevo.record.get('adm')) : '';
                                    (diag_id == 13) ? mod.odonto.cie10.odo_pieza.setValue(rec.get('dient_nro')) : '';
                                    (diag_id == 13) ? mod.odonto.cie10.odo_pieza.getEl().dom.setAttribute('readOnly', true) : '';
//                    mod.odonto.nuevo.examen.setValue(r.get('exa_desc'));   readOnly: true,

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
                    }
                },
                contextmenu: function (dataview, index, item, e) {
                    var rec = dataview.store.getAt(index);
                    new Ext.menu.Menu({
                        items: [{
                                text: 'Limpiar',
                                iconCls: 'limpiar',
                                handler: function () {
                                    Ext.Ajax.request({
                                        url: '<[controller]>',
                                        params: {
                                            format: 'json',
                                            acction: 'delete2',
                                            pac_id: mod.odonto.nuevo.record.get('adm'),
                                            dient_nro: rec.get('dient_nro')
                                        },
                                        success: function () {
                                            mod.odonto.nuevo.st_dientes.load();
                                            mod.odonto.nuevo.st_dientes2.load();
                                            mod.odonto.nuevo.st_dientes3.load();
                                            mod.odonto.nuevo.st_dientes4.load();
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
                            }]
                    }).showAt(e.xy);
                },
                stopEvent: true
            }
        });
        this.odont2 = new Ext.DataView({
            autoScroll: true,
            id: 'dientes_view1',
            store: this.st_dientes2,
            tpl: tpl,
            autoHeight: false,
            height: 100,
            multiSelect: false,
            overClass: 'x-view-over',
            itemSelector: 'div.thumb-wrap2',
            emptyText: 'No hay dientes para mostrar',
            listeners: {
                dblclick: function (dataview, index, item, e) {
                    var rec = dataview.store.getAt(index);
                    var diag_id = mod.odonto.nuevo.id_examen.getValue();
                    var diag = mod.odonto.nuevo.examen.getValue();
                    if (diag_id > 0) {
                        var pac_id = mod.odonto.nuevo.record.get('adm');
                        if (diag_id == 1 || diag_id == 2) {

                        } else {
                            var gramad_diag_raiz;
                            var gramad_diag_coro;
                            var gramad_diag_text;
                            if (diag_id == 3) {       //DIENTE AUSENTE
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 4) {//DIENTE PARA EXTRAER
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 5) {//FRACTURA
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 6) {//DIENTE ECTOPICO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'E';
                            } else if (diag_id == 7) {//PLACA DENTAL
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 8) {//PUENTE
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 9) {//CORONA EN BUEN ESTADO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 10) {//CORONA EN MAL ESTADO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 11) {//MOVILIDAD
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'M1';
                            } else if (diag_id == 12) {//DIENTE DISCROMICO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'DIS';
                            }
                            Ext.Ajax.request({
                                url: '<[controller]>',
                                params: {
                                    acction: 'grama_diente',
                                    format: 'json',
                                    pac_id: pac_id,
                                    diente: rec.get('dient_nro'),
                                    gramad_diag_raiz: gramad_diag_raiz,
                                    gramad_diag_coro: gramad_diag_coro,
                                    gramad_diag_text: gramad_diag_text
                                },
                                success: function () {
                                    mod.odonto.nuevo.st_dientes.load();
                                    mod.odonto.nuevo.st_dientes2.load();
                                    mod.odonto.nuevo.st_dientes3.load();
                                    mod.odonto.nuevo.st_dientes4.load();
                                    (diag_id == 13) ? mod.odonto.cie10.init(mod.odonto.nuevo.record.get('adm')) : '';
                                    (diag_id == 13) ? mod.odonto.cie10.odo_pieza.setValue(rec.get('dient_nro')) : '';
                                    (diag_id == 13) ? mod.odonto.cie10.odo_pieza.getEl().dom.setAttribute('readOnly', true) : '';
//                    mod.odonto.nuevo.examen.setValue(r.get('exa_desc'));   readOnly: true,

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
                    }
                },
                contextmenu: function (dataview, index, item, e) {
                    var rec = dataview.store.getAt(index);
                    new Ext.menu.Menu({
                        items: [{
                                text: 'Limpiar',
                                iconCls: 'limpiar',
                                handler: function () {
                                    Ext.Ajax.request({
                                        url: '<[controller]>',
                                        params: {
                                            format: 'json',
                                            acction: 'delete2',
                                            pac_id: mod.odonto.nuevo.record.get('adm'),
                                            dient_nro: rec.get('dient_nro')
                                        },
                                        success: function () {
                                            mod.odonto.nuevo.st_dientes.load();
                                            mod.odonto.nuevo.st_dientes2.load();
                                            mod.odonto.nuevo.st_dientes3.load();
                                            mod.odonto.nuevo.st_dientes4.load();
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
                            }]
                    }).showAt(e.xy);
                },
                stopEvent: true
            }
        });
        this.odont3 = new Ext.DataView({
            autoScroll: true,
            id: 'dientes_view2',
            store: this.st_dientes3,
            tpl: tpl2,
            autoHeight: false,
            height: 100,
            multiSelect: false,
            overClass: 'x-view-over',
            itemSelector: 'div.thumb-wrap2',
            emptyText: 'No hay dientes para mostrar',
            listeners: {
                dblclick: function (dataview, index, item, e) {
                    var rec = dataview.store.getAt(index);
                    var diag_id = mod.odonto.nuevo.id_examen.getValue();
                    var diag = mod.odonto.nuevo.examen.getValue();
                    if (diag_id > 0) {
                        var pac_id = mod.odonto.nuevo.record.get('adm');
                        if (diag_id == 1 || diag_id == 2) {

                        } else {
                            var gramad_diag_raiz;
                            var gramad_diag_coro;
                            var gramad_diag_text;
                            if (diag_id == 3) {       //DIENTE AUSENTE
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 4) {//DIENTE PARA EXTRAER
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 5) {//FRACTURA
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 6) {//DIENTE ECTOPICO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'E';
                            } else if (diag_id == 7) {//PLACA DENTAL
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 8) {//PUENTE
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 9) {//CORONA EN BUEN ESTADO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 10) {//CORONA EN MAL ESTADO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 11) {//MOVILIDAD
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'M1';
                            } else if (diag_id == 12) {//DIENTE DISCROMICO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'DIS';
                            }
                            Ext.Ajax.request({
                                url: '<[controller]>',
                                params: {
                                    acction: 'grama_diente',
                                    format: 'json',
                                    pac_id: pac_id,
                                    diente: rec.get('dient_nro'),
                                    gramad_diag_raiz: gramad_diag_raiz,
                                    gramad_diag_coro: gramad_diag_coro,
                                    gramad_diag_text: gramad_diag_text
                                },
                                success: function () {
                                    mod.odonto.nuevo.st_dientes.load();
                                    mod.odonto.nuevo.st_dientes2.load();
                                    mod.odonto.nuevo.st_dientes3.load();
                                    mod.odonto.nuevo.st_dientes4.load();
                                    (diag_id == 13) ? mod.odonto.cie10.init(mod.odonto.nuevo.record.get('adm')) : '';
                                    (diag_id == 13) ? mod.odonto.cie10.odo_pieza.setValue(rec.get('dient_nro')) : '';
                                    (diag_id == 13) ? mod.odonto.cie10.odo_pieza.getEl().dom.setAttribute('readOnly', true) : '';
//                    mod.odonto.nuevo.examen.setValue(r.get('exa_desc'));   readOnly: true,

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
                    }
                },
                contextmenu: function (dataview, index, item, e) {
                    var rec = dataview.store.getAt(index);
                    new Ext.menu.Menu({
                        items: [{
                                text: 'Limpiar',
                                iconCls: 'limpiar',
                                handler: function () {
                                    Ext.Ajax.request({
                                        url: '<[controller]>',
                                        params: {
                                            format: 'json',
                                            acction: 'delete2',
                                            pac_id: mod.odonto.nuevo.record.get('adm'),
                                            dient_nro: rec.get('dient_nro')
                                        },
                                        success: function () {
                                            mod.odonto.nuevo.st_dientes.load();
                                            mod.odonto.nuevo.st_dientes2.load();
                                            mod.odonto.nuevo.st_dientes3.load();
                                            mod.odonto.nuevo.st_dientes4.load();
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
                            }]
                    }).showAt(e.xy);
                },
                stopEvent: true
            }
        });
        this.odont4 = new Ext.DataView({
            autoScroll: true,
            id: 'dientes_view3',
            store: this.st_dientes4,
            tpl: tpl2,
            autoHeight: false,
            height: 100,
            multiSelect: false,
            overClass: 'x-view-over',
            itemSelector: 'div.thumb-wrap2',
            emptyText: 'No hay dientes para mostrar',
            listeners: {
                dblclick: function (dataview, index, item, e) {
                    var rec = dataview.store.getAt(index);
                    var diag_id = mod.odonto.nuevo.id_examen.getValue();
                    var diag = mod.odonto.nuevo.examen.getValue();
                    if (diag_id > 0) {
                        var pac_id = mod.odonto.nuevo.record.get('adm');
                        if (diag_id == 1 || diag_id == 2) {

                        } else {
                            var gramad_diag_raiz;
                            var gramad_diag_coro;
                            var gramad_diag_text;
                            if (diag_id == 3) {       //DIENTE AUSENTE
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 4) {//DIENTE PARA EXTRAER
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 5) {//FRACTURA
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 6) {//DIENTE ECTOPICO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'E';
                            } else if (diag_id == 7) {//PLACA DENTAL
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 8) {//PUENTE
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 9) {//CORONA EN BUEN ESTADO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 10) {//CORONA EN MAL ESTADO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = '';
                            } else if (diag_id == 11) {//MOVILIDAD
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'M1';
                            } else if (diag_id == 12) {//DIENTE DISCROMICO
                                gramad_diag_raiz = diag_id;
                                gramad_diag_coro = '';
                                gramad_diag_text = 'DIS';
                            }
                            Ext.Ajax.request({
                                url: '<[controller]>',
                                params: {
                                    acction: 'grama_diente',
                                    format: 'json',
                                    pac_id: pac_id,
                                    diente: rec.get('dient_nro'),
                                    gramad_diag_raiz: gramad_diag_raiz,
                                    gramad_diag_coro: gramad_diag_coro,
                                    gramad_diag_text: gramad_diag_text
                                },
                                success: function () {
                                    mod.odonto.nuevo.st_dientes.load();
                                    mod.odonto.nuevo.st_dientes2.load();
                                    mod.odonto.nuevo.st_dientes3.load();
                                    mod.odonto.nuevo.st_dientes4.load();
                                    (diag_id == 13) ? mod.odonto.cie10.init(mod.odonto.nuevo.record.get('adm')) : '';
                                    (diag_id == 13) ? mod.odonto.cie10.odo_pieza.setValue(rec.get('dient_nro')) : '';
                                    (diag_id == 13) ? mod.odonto.cie10.odo_pieza.getEl().dom.setAttribute('readOnly', true) : '';
//                    mod.odonto.nuevo.examen.setValue(r.get('exa_desc'));   readOnly: true,

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
                    }
                },
                contextmenu: function (dataview, index, item, e) {
                    var rec = dataview.store.getAt(index);
                    new Ext.menu.Menu({
                        items: [{
                                text: 'Limpiar',
                                iconCls: 'limpiar',
                                handler: function () {
                                    Ext.Ajax.request({
                                        url: '<[controller]>',
                                        params: {
                                            format: 'json',
                                            acction: 'delete2',
                                            pac_id: mod.odonto.nuevo.record.get('adm'),
                                            dient_nro: rec.get('dient_nro')
                                        },
                                        success: function () {
                                            mod.odonto.nuevo.st_dientes.load();
                                            mod.odonto.nuevo.st_dientes2.load();
                                            mod.odonto.nuevo.st_dientes3.load();
                                            mod.odonto.nuevo.st_dientes4.load();
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
                            }]
                    }).showAt(e.xy);
                },
                stopEvent: true
            }
        });
        
        this.tplmenu = new Ext.XTemplate(
                '<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false">',
                '<div class="thumb-wrap" id="{exa_desc}">',
                '<tpl for=".">',
                '<div id="imagenes" class="_men"><IMG SRC="<[images]>/odo/ico{exa_id}.png" title="{exa_desc}">',
                '<p style="margin:0 0 4px 0;">{exa_desc}</p>',
                '</div>',
                '</tpl>',
                '</div>',
                '</div>'
                );
        this.id_examen = new Ext.form.TextField({
            fieldLabel: '<b style="color:white;">ID</b>',
            name: 'id_examen',
            maskRe: /[\d]/,
            readOnly: true,
            anchor: '95%'
        });
        this.examen = new Ext.form.TextField({
            fieldLabel: '<b style="color:white;">EXAMEN SELECCIONADO</b>',
            name: 'examen',
            readOnly: true,
            anchor: '95%'
        });
        this.restauracion = new Ext.form.ComboBox({
            typeAhead: true,
            triggerAction: 'all',
            lazyRender: true,
            disabled: true,
            mode: 'local',
            allowBlank: false,
            hiddenName: 'restauracion',
            fieldLabel: '<b style="color:white;">Tipo de restauracion</b>',
            store: new Ext.data.ArrayStore({
                id: 0,
                fields: [
                    'id',
                    'desc'
                ],
                data: [['AM', 'Amalgama'], ['R', 'Resina'], ['IV', 'Ionómero de Vidrio']]
            }),
            listeners: {
                scope: this,
                afterrender: function (combo) {
                    combo.setValue('R');// El ID de la opción por defecto setRawValue
                    combo.setRawValue('Resina');// El ID de la opción por defecto setRawValue
                }
            },
            valueField: 'id',
            displayField: 'desc',
            anchor: '90%'
        });
        this.dv_menu = new Ext.DataView({
            store: this.load_diag,
            tpl: this.tplmenu,
            autoHeight: true,
            multiSelect: true,
            height: 200,
            overClass: 'x-view-over',
            itemSelector: 'div._men',
            emptyText: '',
            listeners: {
                click: function (dataview, index, item, e) {
                    var r = dataview.store.getAt(index);
                    mod.odonto.nuevo.id_examen.setValue(r.get('exa_id'));
                    mod.odonto.nuevo.examen.setValue(r.get('exa_desc'));
                    if (r.get('exa_id') == 2) {
                        mod.odonto.nuevo.restauracion.enable();
                    } else {
                        mod.odonto.nuevo.restauracion.disable();
                    }
                }
            }
        });
        this.tbar1 = new Ext.Toolbar({
            items: ['<b style="color:#000000;">OTRAS PATOLOGIAS:</b>'
            ]
        });
        this.dt_grid1 = new Ext.grid.GridPanel({
            store: this.list_pato,
            region: 'west',
            border: true,
            loadMask: true,
            tbar: this.tbar1,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            height: 400,
            autoExpandColumn: 'gpato_desc',
            columns: [
                new Ext.grid.RowNumberer(),
                {
                    id: 'gpato_diente',
                    header: 'N° DIENTE',
                    dataIndex: 'gpato_diente',
                    width: 70
                }, {
                    id: 'gpato_desc',
                    dataIndex: 'gpato_desc',
                    header: 'OTRAS PATOLOGIAS'
                }
            ]
        });

        this.tbar2 = new Ext.Toolbar({
            items: ['<b style="color:#000000;">TRATAMIENTOS:</b>', '->',
                '-', {
                    text: 'Nuevo',
                    iconCls: 'nuevo',
                    handler: function () {
                        var record = mod.odonto.nuevo.record;
                        mod.odonto.trata.init(null);
                    }
                }
            ]
        });
        this.dt_grid2 = new Ext.grid.GridPanel({
            store: this.list_trata,
            region: 'west',
            border: false,
            tbar: this.tbar2,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            height: 200,
            listeners: {
                rowdblclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record2 = grid.getStore().getAt(rowIndex);//mod.odonto.nuevo.record
                    mod.odonto.trata.init(record2);
                }
            },
            autoExpandColumn: 'reco_desc',
            columns: [
                new Ext.grid.RowNumberer(), {
                    id: 'reco_desc',
                    header: 'TRATAMIENTO',
                    dataIndex: 'trata_desc'
                }
            ]
        });
        this.tbar3 = new Ext.Toolbar({
            items: ['<b style="color:#000000;">RECOMENDACIONES  Y OBSERVACIONES:</b>', '->',
                '-', {
                    text: 'Nuevo',
                    iconCls: 'nuevo',
                    handler: function () {
                        var record = mod.odonto.nuevo.record;
                        mod.odonto.reco.init(null);
                    }
                }
            ]
        });
        this.dt_grid3 = new Ext.grid.GridPanel({
            store: this.list_reco,
            region: 'west',
            border: false,
            tbar: this.tbar3,
            loadMask: true,
            iconCls: 'icon-grid',
            plugins: new Ext.ux.PanelResizer({
                minHeight: 100
            }),
            height: 170,
            listeners: {
                rowdblclick: function (grid, rowIndex, e) {
                    e.stopEvent();
                    var record2 = grid.getStore().getAt(rowIndex);//mod.odonto.nuevo.record
                    mod.odonto.reco.init(record2);
                }
            },
            autoExpandColumn: 'reco_desc',
            columns: [
                new Ext.grid.RowNumberer(), {
                    id: 'reco_desc',
                    header: 'RECOMENDACIONES Y OBSERVACIONES',
                    dataIndex: 'reco_desc'
                }
            ]
        });
        this.frm = new Ext.FormPanel({
            region: 'center',
            url: '<[controller]>',
            monitorValid: true,
            border: false,
            frame: true,
            layout: 'accordion',
            layoutConfig: {
                titleCollapse: true,
                animate: true,
                hideCollapseTool: true
            },
            items: [
                {
                    title: '<b>--->  ODONTOGRAMA</b>',
                    iconCls: 'demo2',
                    layout: 'column',
//                    border: false,
                    bodyStyle: 'padding: 0 0 0 3px;',
                    labelWidth: 60,
                    items: [
                        {
                            xtype: 'panel',
                            layout: 'column',
                            columnWidth: .67,
                            border: false,
                            items: [{
                                    columnWidth: .99,
                                    border: false,
                                    layout: 'form',
                                    items: [this.odont1]
                                }, {
                                    columnWidth: .18,
                                    border: false,
                                    layout: 'form',
                                    html: '<div style="color:rgba(0,56,147,0);">. . . . . . . diente . . . . . . . . </div>'
                                }, {
                                    columnWidth: .82,
                                    border: false,
                                    layout: 'form',
                                    items: [this.odont2]
                                }, {
                                    columnWidth: .18,
                                    border: false,
                                    layout: 'form',
                                    html: '<div style="color:rgba(0,56,147,0);">. . . . . . . diente . . . . . . . . </div>'
                                }, {
                                    columnWidth: .82,
                                    border: false,
                                    layout: 'form',
                                    items: [this.odont3]
                                }, {
                                    columnWidth: .99,
                                    border: false,
                                    layout: 'form',
                                    items: [this.odont4]
                                }]
                        }, {
                            xtype: 'panel',
                            layout: 'column',
                            border: false,
                            bodyStyle: 'padding: 0 0 0 5px;background-color: rgba(0,56,147,0.8);',
                            columnWidth: .33,
                            labelWidth: 60,
                            items: [{
                                    columnWidth: .10,
                                    border: false,
                                    layout: 'form',
                                    labelAlign: 'top',
                                    items: [this.id_examen]
                                }, {
                                    columnWidth: .53,
                                    border: false,
                                    layout: 'form',
                                    labelAlign: 'top',
                                    items: [this.examen]
                                }, {
                                    columnWidth: .37,
                                    border: false,
                                    layout: 'form',
                                    labelAlign: 'top',
                                    items: [this.restauracion]
                                }, {
                                    columnWidth: .99,
                                    border: false,
                                    layout: 'form',
                                    items: [this.dv_menu]
                                }]
                        }
                    ]
                }, {
                    title: '<b>--->  TRATAMIENTO Y OBSERVACIÓN</b>',
                    iconCls: 'demo2',
                    layout: 'column',
//                    border: false,
                    bodyStyle: 'padding:10px 10px 20px 10px;',
                    labelWidth: 60,
                    items: [{
                            columnWidth: .40,
                            labelWidth: 1,
                            labelAlign: 'top',
                            layout: 'form',
                            border: true,
                            bodyStyle: 'padding:0 5px 0 0;',
                            items: [this.dt_grid1]
                        }, {
                            columnWidth: .60,
                            labelWidth: 1,
                            labelAlign: 'top', layout: 'form', border: false,
                            items: [this.dt_grid3]
                        }, {
                            columnWidth: .60,
                            labelWidth: 1,
                            labelAlign: 'top', layout: 'form', border: false,
                            items: [this.dt_grid2]
                        }]
                }
            ],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.odonto.nuevo.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({
                            params: {
                                acction: (this.record.get('st_id') >= 1) ? 'update_odonto' : 'save_odonto'
                                , adm: mod.odonto.nuevo.record.get('adm')
                            },
                            success: function (form, action) {
                                mod.odonto.nuevo.win.el.unmask();
                                mod.odonto.nuevo.win.close();
                                mod.odonto.st.load();
                            },
                            failure: function (form, action) {
                                mod.odonto.nuevo.win.el.unmask();
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
            width: 1180,
            height: 520,
            modal: true,
            iconCls: 'win',
            title: 'ODONTOGRAMA :' + this.record.get('nombre'),
            border: false,
            collapsible: true,
            maximizable: true,
            resizable: false,
            draggable: true,
            closable: true,
            layout: 'border',
            items: [this.frm]
        });
    },
    cargar_pieza_1: function (id) {
        var diag_id = mod.odonto.nuevo.id_examen.getValue();
        var diag = mod.odonto.nuevo.examen.getValue();
        if (diag_id > 0) {
            var pac_id = this.record.get('adm');
            if (diag_id == 1 || diag_id == 2) {
                var fondo;
                var borde;
                if (diag_id == 1) {//caries
                    fondo = 'red';
                    borde = 'black';
                } else if (diag_id == 2) {//RESTAURACION
                    fondo = 'blue';
                    borde = 'black';
                    var restauracion = mod.odonto.nuevo.restauracion.getValue();
                }
                Ext.Ajax.request({
                    url: '<[controller]>',
                    params: {
                        acction: 'grama_pieza',
                        format: 'json',
                        pac_id: pac_id,
                        restauracion: (restauracion !== null) ? restauracion : '',
                        pieza: '1',
                        diente: id,
                        diag: diag_id,
                        fondo: fondo,
                        borde: borde
                    },
                    success: function () {
                        mod.odonto.nuevo.st_dientes.load();
                        mod.odonto.nuevo.st_dientes2.load();
                        mod.odonto.nuevo.st_dientes3.load();
                        mod.odonto.nuevo.st_dientes4.load();
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
//                Ext.MessageBox.alert('En hora buena', id + ' pieza 1 ' + diag + ' adm= ' + adm);
            }
        }
    },
    cargar_pieza_2: function (id) {
        var diag_id = mod.odonto.nuevo.id_examen.getValue();
        var diag = mod.odonto.nuevo.examen.getValue();
        if (diag_id > 0) {
            var pac_id = this.record.get('adm');
            if (diag_id == 1 || diag_id == 2) {
                var fondo;
                var borde;
                if (diag_id == 1) {//caries
                    fondo = 'red';
                    borde = 'black';
                } else if (diag_id == 2) {//RESTAURACION
                    fondo = 'blue';
                    borde = 'black';
                    var restauracion = mod.odonto.nuevo.restauracion.getValue();
                }
                Ext.Ajax.request({
                    url: '<[controller]>',
                    params: {
                        acction: 'grama_pieza',
                        format: 'json',
                        pac_id: pac_id,
                        restauracion: (restauracion !== null) ? restauracion : '',
                        pieza: '2',
                        diente: id,
                        diag: diag_id,
                        fondo: fondo,
                        borde: borde
                    },
                    success: function () {
                        mod.odonto.nuevo.st_dientes.load();
                        mod.odonto.nuevo.st_dientes2.load();
                        mod.odonto.nuevo.st_dientes3.load();
                        mod.odonto.nuevo.st_dientes4.load();
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
//                Ext.MessageBox.alert('En hora buena', id + ' pieza 1 ' + diag + ' adm= ' + adm);
            }
        }
    },
    cargar_pieza_3: function (id) {
        var diag_id = mod.odonto.nuevo.id_examen.getValue();
        var diag = mod.odonto.nuevo.examen.getValue();
        if (diag_id > 0) {
            var pac_id = this.record.get('adm');
            if (diag_id == 1 || diag_id == 2) {
                var fondo;
                var borde;
                if (diag_id == 1) {//caries
                    fondo = 'red';
                    borde = 'black';
                } else if (diag_id == 2) {//RESTAURACION
                    fondo = 'blue';
                    borde = 'black';
                    var restauracion = mod.odonto.nuevo.restauracion.getValue();
                }
                Ext.Ajax.request({
                    url: '<[controller]>',
                    params: {
                        acction: 'grama_pieza',
                        format: 'json',
                        pac_id: pac_id,
                        restauracion: (restauracion !== null) ? restauracion : '',
                        pieza: '3',
                        diente: id,
                        diag: diag_id,
                        fondo: fondo,
                        borde: borde
                    },
                    success: function () {
                        mod.odonto.nuevo.st_dientes.load();
                        mod.odonto.nuevo.st_dientes2.load();
                        mod.odonto.nuevo.st_dientes3.load();
                        mod.odonto.nuevo.st_dientes4.load();
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
//                Ext.MessageBox.alert('En hora buena', id + ' pieza 1 ' + diag + ' adm= ' + adm);
            }
        }
    },
    cargar_pieza_4: function (id) {
        var diag_id = mod.odonto.nuevo.id_examen.getValue();
        var diag = mod.odonto.nuevo.examen.getValue();
        if (diag_id > 0) {
            var pac_id = this.record.get('adm');
            if (diag_id == 1 || diag_id == 2) {
                var fondo;
                var borde;
                if (diag_id == 1) {//caries
                    fondo = 'red';
                    borde = 'black';
                } else if (diag_id == 2) {//RESTAURACION
                    fondo = 'blue';
                    borde = 'black';
                    var restauracion = mod.odonto.nuevo.restauracion.getValue();
                }
                Ext.Ajax.request({
                    url: '<[controller]>',
                    params: {
                        acction: 'grama_pieza',
                        format: 'json',
                        pac_id: pac_id,
                        restauracion: (restauracion !== null) ? restauracion : '',
                        pieza: '4',
                        diente: id,
                        diag: diag_id,
                        fondo: fondo,
                        borde: borde
                    },
                    success: function () {
                        mod.odonto.nuevo.st_dientes.load();
                        mod.odonto.nuevo.st_dientes2.load();
                        mod.odonto.nuevo.st_dientes3.load();
                        mod.odonto.nuevo.st_dientes4.load();
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
//                Ext.MessageBox.alert('En hora buena', id + ' pieza 1 ' + diag + ' adm= ' + adm);
            }
        }
    },
    cargar_pieza_5: function (id) {
        var diag_id = mod.odonto.nuevo.id_examen.getValue();
        var diag = mod.odonto.nuevo.examen.getValue();
        if (diag_id > 0) {
            var pac_id = this.record.get('adm');
            if (diag_id == 1 || diag_id == 2) {
                var fondo;
                var borde;
                if (diag_id == 1) {//caries
                    fondo = 'red';
                    borde = 'black';
                } else if (diag_id == 2) {//RESTAURACION
                    fondo = 'blue';
                    borde = 'black';
                    var restauracion = mod.odonto.nuevo.restauracion.getValue();
                }
                Ext.Ajax.request({
                    url: '<[controller]>',
                    params: {
                        acction: 'grama_pieza',
                        format: 'json',
                        pac_id: pac_id,
                        restauracion: (restauracion !== null) ? restauracion : '',
                        pieza: '5',
                        diente: id,
                        diag: diag_id,
                        fondo: fondo,
                        borde: borde
                    },
                    success: function () {
                        mod.odonto.nuevo.st_dientes.load();
                        mod.odonto.nuevo.st_dientes2.load();
                        mod.odonto.nuevo.st_dientes3.load();
                        mod.odonto.nuevo.st_dientes4.load();
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
//                Ext.MessageBox.alert('En hora buena', id + ' pieza 1 ' + diag + ' adm= ' + adm);
            }
        }
    }
};
mod.odonto.trata = {
    record2: null,
    win: null,
    frm: null,
    reco_desc: null,
    init: function (r) {
        this.record2 = r;
        this.crea_stores();
        this.busca_trata.load();
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
                acction: 'load_trata',
                format: 'json',
                trata_id: this.record2.get('trata_id'),
                trata_adm: this.record2.get('trata_adm')
            },
            scope: this, success: function (frm, action) {
                r = action.result.data;
            }});
    },
    crea_stores: function () {
        this.busca_trata = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'busca_trata',
                format: 'json'
            },
            fields: ['trata_desc'],
            root: 'data'
        });
    },
    crea_controles: function () {
        this.resultTpl = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><span>{trata_desc}</span></h3>',
                '</div>',
                '</div></tpl>'
                );

        this.trata_desc = new Ext.form.ComboBox({
            store: this.busca_trata,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.resultTpl,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 3,
            hiddenName: 'trata_desc',
            displayField: 'trata_desc',
            valueField: 'trata_desc',
            allowBlank: false,
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>TRATAMIENTO</b>',
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
                    items: [this.trata_desc]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.odonto.trata.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({params: {
                                acction: (this.record2 !== null) ? 'update_trata' : 'save_trata'
                                , trata_adm: mod.odonto.nuevo.record.get('adm')
                                , trata_id: (this.record2 !== null) ? mod.odonto.trata.record2.get('trata_id') : ''
                            },
                            success: function (form, action) {
                                mod.odonto.trata.win.el.unmask();
                                mod.odonto.nuevo.list_trata.reload();
                                mod.odonto.trata.win.close();
                            },
                            failure: function (form, action) {
                                mod.odonto.trata.win.el.unmask();
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
                                mod.odonto.trata.win.close();
                            }
                        });
                    }
                }]});
        this.win = new Ext.Window({
            width: 950,
            height: 140,
            modal: true,
            title: 'REGISTRO DE TRATAMIENTOS',
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
mod.odonto.reco = {
    record2: null,
    win: null,
    frm: null,
    reco_desc: null,
    init: function (r) {
        this.record2 = r;
        this.crea_stores();
        this.busca_reco.load();
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
                reco_id: this.record2.get('reco_id'),
                reco_adm: this.record2.get('reco_adm')
            },
            scope: this, success: function (frm, action) {
                r = action.result.data;
            }});
    },
    crea_stores: function () {
        this.busca_reco = new Ext.data.JsonStore({
            url: '<[controller]>',
            baseParams: {
                acction: 'busca_reco',
                format: 'json'
            },
            fields: ['reco_desc'],
            root: 'data'
        });
    },
    crea_controles: function () {
        this.resultTpl = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '<h3><span>{reco_desc}</span></h3>',
                '</div>',
                '</div></tpl>'
                );

        this.reco_desc = new Ext.form.ComboBox({
            store: this.busca_reco,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.resultTpl,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 3,
            hiddenName: 'reco_desc',
            displayField: 'reco_desc',
            valueField: 'reco_desc',
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
                    items: [this.reco_desc]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        mod.odonto.reco.win.el.mask('Guardando…', 'x-mask-loading');
                        this.frm.getForm().submit({params: {
                                acction: (this.record2 !== null) ? 'update_reco' : 'save_reco'
                                , reco_adm: mod.odonto.nuevo.record.get('adm')
                                , reco_id: (this.record2 !== null) ? mod.odonto.reco.record2.get('reco_id') : ''
                            },
                            success: function (form, action) {
                                mod.odonto.reco.win.el.unmask();
                                mod.odonto.nuevo.list_reco.reload();
                                mod.odonto.reco.win.close();
                            },
                            failure: function (form, action) {
                                mod.odonto.reco.win.el.unmask();
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
                                mod.odonto.reco.win.close();
                            }
                        });
                    }
                }]});
        this.win = new Ext.Window({
            width: 950,
            height: 140,
            modal: true,
            title: 'REGISTRO DE OBSERVACIÓNES Y RECOMENDACIÓNES',
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
mod.odonto.cie10 = {
    win: null,
    init: function (r) {
        this.crea_store(r);
        this.crea_controles(r);
//        this.st.load();
        this.win.show();
    },
    crea_store: function (r) {
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
    crea_controles: function (r) {


        this.cie10Tpl = new Ext.XTemplate(
                '<tpl for="."><div class="search-item">',
                '<div class="div-table-col">',
                '{cie4_id}',
                '<h3><span><p>{cie4_desc}</p></span></h3>',
                '</div>',
                '</div></tpl>'
                );
        this.odo_pieza = new Ext.form.TextField({
            fieldLabel: '<b>N° Diente</b>',
            name: 'odo_pieza',
            emptyText: 'N° Diente',
            allowBlank: false,
            maskRe: /[\d]/,
            minLength: 2,
            autoCreate: {
                tag: "input",
                maxlength: 2,
                minLength: 2,
                size: "2",
                autocomplete: "off"
            },
            blankText: 'Numero de Diente',
            anchor: '90%'
        });
        this.odo_cie1 = new Ext.form.ComboBox({
            store: this.list_cie10,
            loadingText: 'Searching...',
            pageSize: 10,
            tpl: this.cie10Tpl,
//            disabled: true,
            hideTrigger: true,
            itemSelector: 'div.search-item',
            selectOnFocus: true,
            minChars: 1,
            hiddenName: 'odo_cie1',
            displayField: 'cie4_desc',
            valueField: 'cie4_desc',
            typeAhead: false,
            triggerAction: 'all',
            fieldLabel: '<b>Otras Patologias</b>',
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
            border: false,
            layout: 'column',
            frame: true,
            bodyStyle: 'padding:10px;',
            labelWidth: 99,
            items: [{
                    columnWidth: .999,
                    border: false,
                    layout: 'form',
                    items: [this.odo_pieza]
                }, {
                    columnWidth: .999,
                    border: false,
                    labelAlign: 'top',
                    layout: 'form',
                    items: [this.odo_cie1]
                }],
            buttons: [{
                    text: 'Guardar',
                    iconCls: 'guardar',
                    formBind: true,
                    scope: this,
                    handler: function () {
                        this.frm.getForm().submit({
                            params: {
                                acction: 'savepato'
                                , adm: r
                            },
                            success: function (form, action) {
//                                obj = Ext.util.JSON.decode(action.response.responseText);
//                                mod.odonto.trata.win.el.unmask();
                                mod.odonto.nuevo.list_pato.reload();
                                mod.odonto.cie10.win.close();
                            },
                            failure: function (form, action) {
                                mod.odonto.cie10.win.el.unmask();
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
            title: 'OTRAS PATOLOGIAS',
            width: 600,
            height: 170,
            modal: true,
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
//
//mod.odonto.nuevo2 = {
//    odont: null,
//    st_diente: null,
//    st_dientes: null,
//    modulos: null,
//    frm: null,
//    win: null,
//    st_diagnostico: null,
//    grilla: null,
//    conclusion: null,
//    recomendaciones: null,
//    odo_caries: null,
//    odo_ausent: null,
//    odo_extrae1: null,
//    odo_obturad: null,
//    odo_calculos: null,
//    odon_placa: null,
//    odo_gingivi: null,
//    st_calculo: null,
//    st_placa: null,
//    st_gingi: null,
//    odo_restaur: null,
//    odo_extrae2: null,
//    odo_desc: null,
//    odo_obsr: null,
//    propiedades: null,
//    record: null,
//    init: function (r) {
//        this.record = r;
//        this.crea_stores();
//        this.crea_controles();
//        this.st_dientes.load();//odon_gingi, odon_placa,odon_calculo
//        this.st_calculo.load();
//        this.st_placa.load();
//        this.st_gingi.load();
//        this.st_diagnostico.load();
//        if (this.record !== null) {
//            this.carga_data();
//        }
//        this.win.show();
//    },
//    carga_data: function () {
//        this.frm.getForm().load({
//            waitMsg: 'Recuperando Informacion...',
//            waitTitle: 'Espere',
//            params: {
//                acction: 'load_odonto',
//                format: 'json',
//                adm: this.record.get('adm'),
//                odo_id: this.record.get('odo_id')
//            },
//            scope: this,
//            success: function (frm, action) {
//                r = action.result.data;//odo_calculos,odon_placa,odo_gingivi
//
////                this.odo_calculos.setValue(r.odo_calculos);
////                this.odo_calculos.setRawValue(r.odo_calculos02);
////                
////                this.odon_placa.setValue(r.odon_placa);
////                this.odon_placa.setRawValue(r.odon_placa02);
////                
////                this.odo_gingivi.setValue(r.odo_gingivi);
////                this.odo_gingivi.setRawValue(r.odo_gingivi02);
//            }
//        });
//    },
//    carga_detalles: function () {
//        this.frm.getForm().load({
////            waitMsg: 'Recuperando Informacion...',
////            waitTitle: 'Espere',
//            params: {
//                acction: 'diagnostico',
//                format: 'json',
//                dient_adm: mod.odonto.nuevo.record.get('adm')
//            },
//            scope: this,
//            success: function (frm, action) {
//                r = action.result.data;
//                //    "caries1","ausente1","extraer1"     odo_caries  odo_ausent  odo_extrae1       odo_restaur odo_extrae2  obturada1
//                mod.odonto.nuevo.odo_caries.setValue(r.caries1);
//                mod.odonto.nuevo.odo_ausent.setValue(r.ausente1);
//                mod.odonto.nuevo.odo_obturad.setValue(r.obturada1);
//                mod.odonto.nuevo.odo_extrae1.setValue(r.extraer1);
//                mod.odonto.nuevo.odo_restaur.setValue(r.caries1);
//                mod.odonto.nuevo.odo_extrae2.setValue(r.extraer1);
//            }
//        });
//    },
//    crea_stores: function () {
//        this.st_diagnostico = new Ext.data.JsonStore({
//            remoteSort: true,
//            url: '<[controller]>',
//            baseParams: {
//                acction: 'list_diag',
//                format: 'json'
//            },
//            root: 'data',
//            totalProperty: 'total',
//            fields: ['con_ids', 'cod_desc']
//        });
//        this.st_dientes = new Ext.data.JsonStore({
//            remoteSort: true,
//            url: '<[controller]>',
//            baseParams: {
//                acction: 'load_dientes',
//                adm: this.record.get('adm'),
//                format: 'json'
//            },
//            root: 'data',
//            totalProperty: 'total',
//            fields: ['img_diag', 'st', 'pieza_nro', 'ps1', 'ps2', 'ps3', 'ps4', 'ps5', 'dient_id']
//        });
//
//
//        this.st_calculo = new Ext.data.JsonStore({//odon_gingi, odon_placa,odon_calculo
//            remoteSort: true,
//            url: '<[controller]>',
//            baseParams: {
//                acction: 'odon_calculo',
//                format: 'json'
//            },
//            root: 'data',
//            totalProperty: 'total',
//            fields: ['con_ids', 'cod_desc']
//        });
//        this.st_placa = new Ext.data.JsonStore({
//            remoteSort: true,
//            url: '<[controller]>',
//            baseParams: {
//                acction: 'odon_placa',
//                format: 'json'
//            },
//            root: 'data',
//            totalProperty: 'total',
//            fields: ['con_ids', 'cod_desc']
//        });
//        this.st_gingi = new Ext.data.JsonStore({
//            remoteSort: true,
//            url: '<[controller]>',
//            baseParams: {
//                acction: 'odon_gingi',
//                format: 'json'
//            },
//            root: 'data',
//            totalProperty: 'total',
//            fields: ['con_ids', 'cod_desc']
//        });
//    },
//    crea_controles: function () {
//        this.grilla = new Ext.grid.GridPanel({
//            store: this.st_diagnostico,
//            loadMask: true,
//            height: 250,
//            iconCls: 'icon-grid',
//            plugins: new Ext.ux.PanelResizer({
//                minHeight: 95
//            }),
//            listeners: {
//                rowclick: function (grid, rowIndex, e) {
//                    var rec = grid.getStore().getAt(rowIndex);
//                    mod.odonto.nuevo.data = '';
//                    mod.odonto.nuevo.desc = '';
//                    mod.odonto.nuevo.data = rec.get('con_ids');
//                    mod.odonto.nuevo.desc = rec.get('cod_desc');
////                    console.log(record);
////                    Ext.MessageBox.alert('En hora buena', ' ' + mod.odonto.nuevo.data + ' ' + mod.odonto.nuevo.desc);
//                }
//            },
//            autoExpandColumn: 'cod_desc',
//            colModel: new Ext.grid.ColumnModel({
//                columns: [
//                    {
//                        header: 'ST',
//                        width: 25,
//                        sortable: true,
//                        dataIndex: 'con_ids',
//                        renderer: function renderIcon(val) {
//                            if (val == 27) {
//                                return  '<img src="<[images]>/caries.png" title="Caries" height="15">';
//                            } else if (val == 28) {
//                                return  '<img src="<[images]>/restauracion.png" title="Restauración - Amalgama" height="15">';
//                            } else if (val == 29) {
//                                return  '<img src="<[images]>/ausente.png" title="Diente Ausente" height="15">';
//                            } else if (val == 30) {
//                                return  '<img src="<[images]>/extraer.png" title="Diente para Extraer" height="15">';
//                            } else if (val == 31) {
//                                return  '<img src="<[images]>/cbueno.png" title="Corona Buen Estado" height="15">';
//                            } else if (val == 32) {
//                                return  '<img src="<[images]>/cmalo.png" title="Corona Mal Estado" height="15">';
//                            } else if (val == 33) {
//                                return  '<img src="<[images]>/placa.png" title="Placa Dental" height="15">';
//                            } else if (val == 34) {
//                                return  '<img src="<[images]>/puente.png" title="Puente" height="15">';
//                            }
//                        }
//                    }, {
//                        header: 'Examen',
//                        width: 150,
//                        dataIndex: 'cod_desc',
//                        id: 'cod_desc'
//                    }
//                ]
//            })
//        });
//        var menu = new Ext.menu.Menu({
//            items: [{
//                    text: 'Limpiar',
//                    iconCls: 'limpiar',
//                    handler: function () {
//                        Ext.Ajax.request({
//                            url: '<[controller]>',
//                            params: {
//                                format: 'json',
//                                acction: 'delete',
//                                adm: mod.odonto.nuevo.record.get('adm'),
//                                dient_diente: mod.odonto.nuevo.nro_diente.getValue()
//                            },
//                            success: function () {
//                                mod.odonto.nuevo.st_dientes.load();
//                                mod.odonto.nuevo.carga_detalles();
//                            },
//                            failure: function (form, action) {
//                                switch (action.failureType) {
//                                    case Ext.form.Action.CLIENT_INVALID:
//                                        Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                        break;
//                                    case Ext.form.Action.CONNECT_FAILURE:
//                                        Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                        break;
//                                    case Ext.form.Action.SERVER_INVALID:
//                                        Ext.Msg.alert('Failure', action.result.error);
//                                        break;
//                                    default:
//                                        Ext.Msg.alert('Failure', action.result.error);
//                                }
//                            }
//                        });
//                    }
//                }]
//        });
//        var tpl = new Ext.XTemplate(
//                '<tpl for=".">',
//                '<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false">',
//                '<DIV class="thumb-wrap2">',
//                '<DIV class="thumb" style="height: 157px; width: 42px ;background-image: url(<[images]>/dientes/{pieza_nro}.png);">',
//                '<img src="<[images]>/dientes/{img_diag}.png"/>',
//                '</DIV>',
//                '<DIV style="height: 42px; width: 42px;">',
//                '<DIV style="border: 1px dotted;margin:0 13px 0 13px; height: 13px; width: 13px; background-color:{ps1};"></DIV>',
//                '<DIV style="border: 1px dotted;float:left;height: 13px; width: 12px; background-color:{ps2};"></DIV>',
//                '<DIV style="border: 1px dotted;float:left;height: 13px; width: 12px; background-color:{ps3};"></DIV>',
//                '<DIV style="border: 1px dotted;float:left;height: 13px; width: 12px; background-color:{ps4};"></DIV>',
//                '<DIV style="border: 1px dotted;float:left;margin:0 13px 0 13px; height: 13px; width: 13px; background-color:{ps5};"></DIV>',
//                '</DIV>',
//                '</DIV>',
//                '</DIV>',
//                '</tpl>',
//                '<div class="x-clear"></div>'
//                );
//        this.odont = new Ext.DataView({
//            autoScroll: true,
//            id: 'dientes_view',
//            store: this.st_dientes,
//            tpl: tpl,
//            autoHeight: false,
//            height: 440,
//            multiSelect: false,
//            overClass: 'x-view-over',
//            itemSelector: 'div.thumb-wrap2',
//            emptyText: 'No hay dientes para mostrar',
//            listeners: {
//                click: function (dataview, index, item, e) {
//                    var rec = dataview.store.getAt(index);
//                    var st = rec.get('st');
//
//                    var metodo;
//                    if (st == 1) {
//                        metodo = 'update';
//                    } else {
//                        if (mod.odonto.nuevo.record.get('st_id') == 1) {
//                            metodo = 'save_d';
//                        } else {
//                            metodo = 'save_o_d';
//                        }
//                    }
//                    mod.odonto.nuevo.nro_diente.setValue(rec.get('pieza_nro'));
//                    mod.odonto.nuevo.nro_examen.setValue(mod.odonto.nuevo.data);
//                },
//                dblclick: function (dataview, index, item, e) {
//                    var rec = dataview.store.getAt(index);
//                    var st = rec.get('st');
//
//                    var metodo;
//                    if (st == 1) {
//                        metodo = 'update';
//                    } else {
//                        if (mod.odonto.nuevo.record.get('st_id') == 1) {
//                            metodo = 'save_d';
//                        } else {
//                            metodo = 'save_o_d';
//                        }
//                    }
//
//                    if (mod.odonto.nuevo.data == null) {
//                        Ext.MessageBox.alert('Alerta', 'Seleccione un Examen');
//                        mod.odonto.nuevo.diente_pz1.disabled();
//                        mod.odonto.nuevo.diente_pz2.disabled();
//                        mod.odonto.nuevo.diente_pz3.disabled();
//                        mod.odonto.nuevo.diente_pz4.disabled();
//                        mod.odonto.nuevo.diente_pz5.disabled();
//                    } else {
//                        if (mod.odonto.nuevo.data == 27) {
//                            mod.odonto.nuevo.nro_diente.setValue(rec.get('pieza_nro'));
//                            mod.odonto.nuevo.nro_examen.setValue(mod.odonto.nuevo.data);
//                            mod.odonto.nuevo.diente_pz1.enable();
//                            mod.odonto.nuevo.diente_pz2.enable();
//                            mod.odonto.nuevo.diente_pz3.enable();
//                            mod.odonto.nuevo.diente_pz4.enable();
//                            mod.odonto.nuevo.diente_pz5.enable();
//                            Ext.Ajax.request({
//                                url: '<[controller]>',
//                                params: {
//                                    acction: metodo + '_diag',
//                                    format: 'json',
//                                    dient_id: rec.get('dient_id'),
//                                    dient_adm: mod.odonto.nuevo.record.get('adm'),
//                                    dient_diente: rec.get('pieza_nro'),
//                                    dient_diag: mod.odonto.nuevo.data
//                                },
//                                success: function () {
//                                    mod.odonto.nuevo.st_dientes.load();
//
//                                    mod.odonto.nuevo.carga_detalles();
//                                },
//                                failure: function (form, action) {
//                                    switch (action.failureType) {
//                                        case Ext.form.Action.CLIENT_INVALID:
//                                            Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                            break;
//                                        case Ext.form.Action.CONNECT_FAILURE:
//                                            Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                            break;
//                                        case Ext.form.Action.SERVER_INVALID:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                            break;
//                                        default:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                    }
//                                }
//                            });
////                            Ext.MessageBox.alert('Alerta', 'Seleccione la parte Careada');
//                        } else if (mod.odonto.nuevo.data == 28) {
//                            mod.odonto.nuevo.nro_diente.setValue(rec.get('pieza_nro'));
//                            mod.odonto.nuevo.nro_examen.setValue(mod.odonto.nuevo.data);
//                            mod.odonto.nuevo.diente_pz1.enable();
//                            mod.odonto.nuevo.diente_pz2.enable();
//                            mod.odonto.nuevo.diente_pz3.enable();
//                            mod.odonto.nuevo.diente_pz4.enable();
//                            mod.odonto.nuevo.diente_pz5.enable();
//                            Ext.Ajax.request({
//                                url: '<[controller]>',
//                                params: {
//                                    acction: metodo + '_diag',
//                                    format: 'json',
//                                    dient_id: rec.get('dient_id'),
//                                    dient_adm: mod.odonto.nuevo.record.get('adm'),
//                                    dient_diente: rec.get('pieza_nro'),
//                                    dient_diag: mod.odonto.nuevo.data
//                                },
//                                success: function () {
//                                    mod.odonto.nuevo.st_dientes.load();
//
//                                    mod.odonto.nuevo.carga_detalles();
//                                },
//                                failure: function (form, action) {
//                                    switch (action.failureType) {
//                                        case Ext.form.Action.CLIENT_INVALID:
//                                            Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                            break;
//                                        case Ext.form.Action.CONNECT_FAILURE:
//                                            Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                            break;
//                                        case Ext.form.Action.SERVER_INVALID:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                            break;
//                                        default:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                    }
//                                }
//                            });
////                            Ext.MessageBox.alert('Alerta', 'Seleccione la parte a Restaurar');
//                        } else if (mod.odonto.nuevo.data == 29) {
//                            mod.odonto.nuevo.nro_diente.setValue(rec.get('pieza_nro'));
//                            mod.odonto.nuevo.nro_examen.setValue(mod.odonto.nuevo.data);
//                            mod.odonto.nuevo.diente_pz1.disable();
//                            mod.odonto.nuevo.diente_pz2.disable();
//                            mod.odonto.nuevo.diente_pz3.disable();
//                            mod.odonto.nuevo.diente_pz4.disable();
//                            mod.odonto.nuevo.diente_pz5.disable();
//                            Ext.Ajax.request({
//                                url: '<[controller]>',
//                                params: {
//                                    acction: metodo + '_diag',
//                                    format: 'json',
//                                    dient_id: rec.get('dient_id'),
//                                    dient_adm: mod.odonto.nuevo.record.get('adm'),
//                                    dient_diente: rec.get('pieza_nro'),
//                                    dient_diag: mod.odonto.nuevo.data
//                                },
//                                success: function () {
//                                    mod.odonto.nuevo.st_dientes.load();
//
//                                    mod.odonto.nuevo.carga_detalles();
//                                },
//                                failure: function (form, action) {
//                                    switch (action.failureType) {
//                                        case Ext.form.Action.CLIENT_INVALID:
//                                            Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                            break;
//                                        case Ext.form.Action.CONNECT_FAILURE:
//                                            Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                            break;
//                                        case Ext.form.Action.SERVER_INVALID:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                            break;
//                                        default:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                    }
//                                }
//                            });
////                            Ext.MessageBox.alert('Alerta', '29');
//                        } else if (mod.odonto.nuevo.data == 30) {
//                            mod.odonto.nuevo.nro_diente.setValue(rec.get('pieza_nro'));
//                            mod.odonto.nuevo.nro_examen.setValue(mod.odonto.nuevo.data);
//                            mod.odonto.nuevo.diente_pz1.disable();
//                            mod.odonto.nuevo.diente_pz2.disable();
//                            mod.odonto.nuevo.diente_pz3.disable();
//                            mod.odonto.nuevo.diente_pz4.disable();
//                            mod.odonto.nuevo.diente_pz5.disable();
//                            Ext.Ajax.request({
//                                url: '<[controller]>',
//                                params: {
//                                    acction: metodo + '_diag',
//                                    format: 'json',
//                                    dient_id: rec.get('dient_id'),
//                                    dient_adm: mod.odonto.nuevo.record.get('adm'),
//                                    dient_diente: rec.get('pieza_nro'),
//                                    dient_diag: mod.odonto.nuevo.data
//                                },
//                                success: function () {
//                                    mod.odonto.nuevo.st_dientes.load();
//
//                                    mod.odonto.nuevo.carga_detalles();
//                                },
//                                failure: function (form, action) {
//                                    switch (action.failureType) {
//                                        case Ext.form.Action.CLIENT_INVALID:
//                                            Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                            break;
//                                        case Ext.form.Action.CONNECT_FAILURE:
//                                            Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                            break;
//                                        case Ext.form.Action.SERVER_INVALID:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                            break;
//                                        default:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                    }
//                                }
//                            });
//                        } else if (mod.odonto.nuevo.data == 31) {
//                            mod.odonto.nuevo.nro_diente.setValue(rec.get('pieza_nro'));
//                            mod.odonto.nuevo.nro_examen.setValue(mod.odonto.nuevo.data);
//                            mod.odonto.nuevo.diente_pz1.disable();
//                            mod.odonto.nuevo.diente_pz2.disable();
//                            mod.odonto.nuevo.diente_pz3.disable();
//                            mod.odonto.nuevo.diente_pz4.disable();
//                            mod.odonto.nuevo.diente_pz5.disable();
//                            Ext.Ajax.request({
//                                url: '<[controller]>',
//                                params: {
//                                    acction: metodo + '_diag',
//                                    format: 'json',
//                                    dient_id: rec.get('dient_id'),
//                                    dient_adm: mod.odonto.nuevo.record.get('adm'),
//                                    dient_diente: rec.get('pieza_nro'),
//                                    dient_diag: mod.odonto.nuevo.data
//                                },
//                                success: function () {
//                                    mod.odonto.nuevo.st_dientes.load();
//
//                                    mod.odonto.nuevo.carga_detalles();
//                                },
//                                failure: function (form, action) {
//                                    switch (action.failureType) {
//                                        case Ext.form.Action.CLIENT_INVALID:
//                                            Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                            break;
//                                        case Ext.form.Action.CONNECT_FAILURE:
//                                            Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                            break;
//                                        case Ext.form.Action.SERVER_INVALID:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                            break;
//                                        default:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                    }
//                                }
//                            });
//                        } else if (mod.odonto.nuevo.data == 32) {
//                            mod.odonto.nuevo.nro_diente.setValue(rec.get('pieza_nro'));
//                            mod.odonto.nuevo.nro_examen.setValue(mod.odonto.nuevo.data);
//                            mod.odonto.nuevo.diente_pz1.disable();
//                            mod.odonto.nuevo.diente_pz2.disable();
//                            mod.odonto.nuevo.diente_pz3.disable();
//                            mod.odonto.nuevo.diente_pz4.disable();
//                            mod.odonto.nuevo.diente_pz5.disable();
//                            Ext.Ajax.request({
//                                url: '<[controller]>',
//                                params: {
//                                    acction: metodo + '_diag',
//                                    format: 'json',
//                                    dient_id: rec.get('dient_id'),
//                                    dient_adm: mod.odonto.nuevo.record.get('adm'),
//                                    dient_diente: rec.get('pieza_nro'),
//                                    dient_diag: mod.odonto.nuevo.data
//                                },
//                                success: function () {
//                                    mod.odonto.nuevo.st_dientes.load();
//
//                                    mod.odonto.nuevo.carga_detalles();
//                                },
//                                failure: function (form, action) {
//                                    switch (action.failureType) {
//                                        case Ext.form.Action.CLIENT_INVALID:
//                                            Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                            break;
//                                        case Ext.form.Action.CONNECT_FAILURE:
//                                            Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                            break;
//                                        case Ext.form.Action.SERVER_INVALID:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                            break;
//                                        default:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                    }
//                                }
//                            });
//                        } else if (mod.odonto.nuevo.data == 33) {
//                            mod.odonto.nuevo.nro_diente.setValue(rec.get('pieza_nro'));
//                            mod.odonto.nuevo.nro_examen.setValue(mod.odonto.nuevo.data);
//                            mod.odonto.nuevo.diente_pz1.disable();
//                            mod.odonto.nuevo.diente_pz2.disable();
//                            mod.odonto.nuevo.diente_pz3.disable();
//                            mod.odonto.nuevo.diente_pz4.disable();
//                            mod.odonto.nuevo.diente_pz5.disable();
//                            Ext.Ajax.request({
//                                url: '<[controller]>',
//                                params: {
//                                    acction: metodo + '_diag',
//                                    format: 'json',
//                                    dient_id: rec.get('dient_id'),
//                                    dient_adm: mod.odonto.nuevo.record.get('adm'),
//                                    dient_diente: rec.get('pieza_nro'),
//                                    dient_diag: mod.odonto.nuevo.data
//                                },
//                                success: function () {
//                                    mod.odonto.nuevo.st_dientes.load();
//
//                                    mod.odonto.nuevo.carga_detalles();
//                                },
//                                failure: function (form, action) {
//                                    switch (action.failureType) {
//                                        case Ext.form.Action.CLIENT_INVALID:
//                                            Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                            break;
//                                        case Ext.form.Action.CONNECT_FAILURE:
//                                            Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                            break;
//                                        case Ext.form.Action.SERVER_INVALID:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                            break;
//                                        default:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                    }
//                                }
//                            });
//                        } else if (mod.odonto.nuevo.data == 34) {
//                            mod.odonto.nuevo.nro_diente.setValue(rec.get('pieza_nro'));
//                            mod.odonto.nuevo.nro_examen.setValue(mod.odonto.nuevo.data);
//                            mod.odonto.nuevo.diente_pz1.disable();
//                            mod.odonto.nuevo.diente_pz2.disable();
//                            mod.odonto.nuevo.diente_pz3.disable();
//                            mod.odonto.nuevo.diente_pz4.disable();
//                            mod.odonto.nuevo.diente_pz5.disable();
//                            Ext.Ajax.request({
//                                url: '<[controller]>',
//                                params: {
//                                    acction: metodo + '_diag',
//                                    format: 'json',
//                                    dient_id: rec.get('dient_id'),
//                                    dient_adm: mod.odonto.nuevo.record.get('adm'),
//                                    dient_diente: rec.get('pieza_nro'),
//                                    dient_diag: mod.odonto.nuevo.data
//                                },
//                                success: function () {
//                                    mod.odonto.nuevo.st_dientes.load();
//
//                                    mod.odonto.nuevo.carga_detalles();
//                                },
//                                failure: function (form, action) {
//                                    switch (action.failureType) {
//                                        case Ext.form.Action.CLIENT_INVALID:
//                                            Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                            break;
//                                        case Ext.form.Action.CONNECT_FAILURE:
//                                            Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                            break;
//                                        case Ext.form.Action.SERVER_INVALID:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                            break;
//                                        default:
//                                            Ext.Msg.alert('Failure', action.result.error);
//                                    }
//                                }
//                            });
//                        }
//                    }
////                        Ext.MessageBox.alert('En hora buena', 'Diente :' + record.get('desc') + ' Diagnostico: ' + mod.odonto.nuevo.data + ' ' + mod.odonto.nuevo.desc);
//                },
//                contextmenu: function (dataview, index, item, e) {
//                    menu.showAt(e.getXY());
//                },
//                stopEvent: true
//            }
//        });
////    "caries1","ausente1","extraer1"     odo_caries  odo_ausent  odo_extrae1       odo_restaur odo_extrae2
//        this.odo_caries = new Ext.form.NumberField({
//            fieldLabel: '# CARIES',
//            name: 'odo_caries',
//            readOnly: true,
////            allowBlank: false,
//            anchor: '95%',
//            value: this.record.get('caries')
//        });
////odo_ausent:null,
//        this.odo_ausent = new Ext.form.NumberField({
//            fieldLabel: '# D. AUSENTES',
//            name: 'odo_ausent',
//            readOnly: true,
////            allowBlank: false,
//            anchor: '95%',
//            value: this.record.get('ausente')
//        });
////odo_extrae1:null,
//        this.odo_extrae1 = new Ext.form.NumberField({
//            fieldLabel: '# D. POR EXTRAER',
//            name: 'odo_extrae1',
//            readOnly: true,
////            allowBlank: false,
//            anchor: '95%',
//            value: this.record.get('extraer')
//        });
////odo_obturad:null,
//        this.odo_obturad = new Ext.form.NumberField({
//            fieldLabel: '# D. OBTURADAS',
//            name: 'odo_obturad',
////            allowBlank: false,
//            anchor: '95%',
//            value: this.record.get('obturada1')
//        });
////odo_calculos:null,aaa
//        this.odo_calculos = new Ext.form.ComboBox({////         odo_calculos, odon_placa,odo_gingivi,    odo_desc,odo_obsr
//            typeAhead: true,
//            triggerAction: 'all',
//            lazyRender: true,
//            mode: 'local',
//            store: this.st_calculo,
//            forceSelection: true,
//            hiddenName: 'odo_calculos',
//            fieldLabel: 'Calculos Dentales',
//            name: 'odo_calculos',
//            valueField: 'con_ids',
//            displayField: 'cod_desc',
//            anchor: '95%',
//            //editable: false,
//            listeners: {
//                afterrender: function (combo) {
//                    combo.setValue(173);// El ID de la opción por defecto setRawValue
//                    combo.setRawValue('NO');// El ID de la opción por defecto setRawValue
//                }
//            }
//        });
////odon_placa:null,
//        this.odon_placa = new Ext.form.ComboBox({
//            typeAhead: true,
//            triggerAction: 'all',
//            lazyRender: true,
//            mode: 'local',
//            store: this.st_placa,
//            forceSelection: true,
//            hiddenName: 'odon_placa',
//            fieldLabel: 'Placa Bacteriana',
//            name: 'odon_placa',
//            valueField: 'con_ids',
//            displayField: 'cod_desc',
//            anchor: '95%',
//            //editable: false,
//            listeners: {
//                afterrender: function (combo) {
//                    combo.setValue(175);// El ID de la opción por defecto setRawValue
//                    combo.setRawValue('NO');// El ID de la opción por defecto setRawValue
//                }
//            }
//        });
////odo_gingivi:null,
//        this.odo_gingivi = new Ext.form.ComboBox({
//            typeAhead: true,
//            triggerAction: 'all',
//            lazyRender: true,
//            mode: 'local',
//            store: this.st_gingi,
//            forceSelection: true,
//            hiddenName: 'odo_gingivi',
//            fieldLabel: 'Gingivitis',
//            name: 'odo_gingivi',
//            valueField: 'con_ids',
//            displayField: 'cod_desc',
//            anchor: '99%',
//            //editable: false,
//            listeners: {
//                afterrender: function (combo) {
//                    combo.setValue(177);// El ID de la opción por defecto setRawValue
//                    combo.setRawValue('NO');// El ID de la opción por defecto setRawValue
//                }
//            }
//        });
////odo_restaur:null,   caries,ausente,extraer  odo_restaur odo_extrae2
//        this.odo_restaur = new Ext.form.NumberField({
//            fieldLabel: '# D. RESTAURAR',
//            name: 'odo_restaur',
//            readOnly: true,
//            anchor: '95%',
//            value: this.record.get('caries')
//        });
////odo_extrae2:null,
//        this.odo_extrae2 = new Ext.form.NumberField({
//            fieldLabel: '# D. EXTRAER',
//            name: 'odo_extrae2',
//            readOnly: true,
//            anchor: '95%',
//            value: this.record.get('extraer')
//        });
////odo_desc:null,
//        this.odo_desc = new Ext.form.TextArea({
////            fieldLabel: 'Conclusion',
//            name: 'odo_desc',
//            style: {
//                //textTransform: "uppercase"
//            },
//            id: 'odo_desc',
//            width: '95%',
//            height: 80
//        });
////odo_obsr:null,  odo_desc,odo_obsr
//        this.odo_obsr = new Ext.form.TextArea({
//            fieldLabel: 'OBSERVACIONES',
//            name: 'odo_obsr',
//            style: {
//                //textTransform: "uppercase"
//            },
//            id: 'odo_obsr',
//            width: '95%',
//            height: 70
//        });
//
//        //nro_diente
//        this.nro_diente = new Ext.form.NumberField({
//            fieldLabel: 'Dt',
//            name: 'nro_diente',
//            readOnly: true,
//            anchor: '95%'
//        });
//
//        this.nro_examen = new Ext.form.NumberField({
//            fieldLabel: 'Exa',
//            name: 'nro_examen',
//            readOnly: true,
//            anchor: '95%'
//        });
//
//        this.diente_pz1 = new Ext.Button({
//            text: '1',
//            height: 35,
//            disabled: true,
//            width: 35,
//            formBind: true,
//            handler: function () {
//                Ext.Ajax.request({
//                    url: '<[controller]>',
//                    params: {
//                        format: 'json',
//                        acction: 'update_pz1',
//                        adm: mod.odonto.nuevo.record.get('adm'),
//                        dient_diente: mod.odonto.nuevo.nro_diente.getValue(),
//                        dient_diag: mod.odonto.nuevo.nro_examen.getValue()
//                    },
//                    success: function () {
//                        mod.odonto.nuevo.st_dientes.load();
//                        mod.odonto.nuevo.carga_detalles();
//                    },
//                    failure: function (form, action) {
//                        switch (action.failureType) {
//                            case Ext.form.Action.CLIENT_INVALID:
//                                Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                break;
//                            case Ext.form.Action.CONNECT_FAILURE:
//                                Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                break;
//                            case Ext.form.Action.SERVER_INVALID:
//                                Ext.Msg.alert('Failure', action.result.error);
//                                break;
//                            default:
//                                Ext.Msg.alert('Failure', action.result.error);
//                        }
//                    }
//                });
//            }
//        });
//        this.diente_pz2 = new Ext.Button({
//            text: '2',
//            height: 35,
//            disabled: true,
//            width: 35,
//            formBind: true,
//            handler: function () {
//                Ext.Ajax.request({
//                    url: '<[controller]>',
//                    params: {
//                        format: 'json',
//                        acction: 'update_pz2',
//                        adm: mod.odonto.nuevo.record.get('adm'),
//                        dient_diente: mod.odonto.nuevo.nro_diente.getValue(),
//                        dient_diag: mod.odonto.nuevo.nro_examen.getValue()
//                    },
//                    success: function () {
//                        mod.odonto.nuevo.st_dientes.load();
//                        mod.odonto.nuevo.carga_detalles();
//                    },
//                    failure: function (form, action) {
//                        switch (action.failureType) {
//                            case Ext.form.Action.CLIENT_INVALID:
//                                Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                break;
//                            case Ext.form.Action.CONNECT_FAILURE:
//                                Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                break;
//                            case Ext.form.Action.SERVER_INVALID:
//                                Ext.Msg.alert('Failure', action.result.error);
//                                break;
//                            default:
//                                Ext.Msg.alert('Failure', action.result.error);
//                        }
//                    }
//                });
//            }
//        });
//        this.diente_pz3 = new Ext.Button({
//            text: '3',
//            height: 35,
//            disabled: true,
//            width: 35,
//            formBind: true,
//            handler: function () {
//                Ext.Ajax.request({
//                    url: '<[controller]>',
//                    params: {
//                        format: 'json',
//                        acction: 'update_pz3',
//                        adm: mod.odonto.nuevo.record.get('adm'),
//                        dient_diente: mod.odonto.nuevo.nro_diente.getValue(),
//                        dient_diag: mod.odonto.nuevo.nro_examen.getValue()
//                    },
//                    success: function () {
//                        mod.odonto.nuevo.st_dientes.load();
//                        mod.odonto.nuevo.carga_detalles();
//                    },
//                    failure: function (form, action) {
//                        switch (action.failureType) {
//                            case Ext.form.Action.CLIENT_INVALID:
//                                Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                break;
//                            case Ext.form.Action.CONNECT_FAILURE:
//                                Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                break;
//                            case Ext.form.Action.SERVER_INVALID:
//                                Ext.Msg.alert('Failure', action.result.error);
//                                break;
//                            default:
//                                Ext.Msg.alert('Failure', action.result.error);
//                        }
//                    }
//                });
//            }
//        });
//        this.diente_pz4 = new Ext.Button({
//            text: '4',
//            height: 35,
//            disabled: true,
//            width: 35,
//            formBind: true,
//            handler: function () {
//                Ext.Ajax.request({
//                    url: '<[controller]>',
//                    params: {
//                        format: 'json',
//                        acction: 'update_pz4',
//                        adm: mod.odonto.nuevo.record.get('adm'),
//                        dient_diente: mod.odonto.nuevo.nro_diente.getValue(),
//                        dient_diag: mod.odonto.nuevo.nro_examen.getValue()
//                    },
//                    success: function () {
//                        mod.odonto.nuevo.st_dientes.load();
//                        mod.odonto.nuevo.carga_detalles();
//                    },
//                    failure: function (form, action) {
//                        switch (action.failureType) {
//                            case Ext.form.Action.CLIENT_INVALID:
//                                Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                break;
//                            case Ext.form.Action.CONNECT_FAILURE:
//                                Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                break;
//                            case Ext.form.Action.SERVER_INVALID:
//                                Ext.Msg.alert('Failure', action.result.error);
//                                break;
//                            default:
//                                Ext.Msg.alert('Failure', action.result.error);
//                        }
//                    }
//                });
//            }
//        });
//        this.diente_pz5 = new Ext.Button({
//            text: '5',
//            height: 35,
//            disabled: true,
//            width: 35,
//            formBind: true,
//            handler: function () {
//                Ext.Ajax.request({
//                    url: '<[controller]>',
//                    params: {
//                        format: 'json',
//                        acction: 'update_pz5',
//                        adm: mod.odonto.nuevo.record.get('adm'),
//                        dient_diente: mod.odonto.nuevo.nro_diente.getValue(),
//                        dient_diag: mod.odonto.nuevo.nro_examen.getValue()
//                    },
//                    success: function () {
//                        mod.odonto.nuevo.st_dientes.load();
//                        mod.odonto.nuevo.carga_detalles();
//                    },
//                    failure: function (form, action) {
//                        switch (action.failureType) {
//                            case Ext.form.Action.CLIENT_INVALID:
//                                Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                break;
//                            case Ext.form.Action.CONNECT_FAILURE:
//                                Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                break;
//                            case Ext.form.Action.SERVER_INVALID:
//                                Ext.Msg.alert('Failure', action.result.error);
//                                break;
//                            default:
//                                Ext.Msg.alert('Failure', action.result.error);
//                        }
//                    }
//                });
//            }
//        });
//
//
//
//        this.modulos = new Ext.Panel({
//            region: 'west',
//            border: false,
////            width: 250,
//            collapsible: true,
//            collapsed: true,
//            bodyStyle: 'background-color: rgba(1,40,92,0);',
//            items: [new Ext.Panel({
//                    border: false,
////                    bodyStyle: 'padding:20px;',
//                    labelWidth: 75,
//                    labelAlign: 'top',
//                    bodyStyle: 'background-color: rgba(1,40,92,0.9);padding:5px 20px 20px 20px;color:#ffffff;',
//                    height: 450,
//                    items: [{
//                            xtype: 'panel',
//                            html: '<center><h1>DIAGNOSTICO<h1></center>',
//                            layout: 'column',
//                            items: [{
//                                    columnWidth: .50,
//                                    border: false,
//                                    layout: 'form',
//                                    items: [this.odo_caries]
//                                }, {
//                                    columnWidth: .50,
//                                    border: false,
//                                    layout: 'form',
//                                    items: [this.odo_ausent]
//                                }, {
//                                    columnWidth: .50,
//                                    border: false,
//                                    layout: 'form',
//                                    items: [this.odo_extrae1]
//                                }, {
//                                    columnWidth: .50,
//                                    border: false,
//                                    layout: 'form',
//                                    items: [this.odo_obturad]
//                                }, {
//                                    columnWidth: .50,
//                                    border: false,
//                                    layout: 'form',
//                                    items: [this.odo_calculos]
//                                }, {
//                                    columnWidth: .50,
//                                    border: false,
//                                    layout: 'form',
//                                    items: [this.odon_placa]
//                                }, {
//                                    columnWidth: .99,
//                                    border: false,
//                                    layout: 'form',
//                                    items: [this.odo_gingivi]
//                                }]
//                        }
//                        , {
//                            xtype: 'panel',
//                            html: '</br><center><h1>TRATAMIENTO<h1></center>',
//                            layout: 'column',
//                            items: [{
//                                    columnWidth: .50,
//                                    border: false,
//                                    layout: 'form',
//                                    items: [this.odo_restaur]
//                                }, {
//                                    columnWidth: .50,
//                                    border: false,
//                                    layout: 'form',
//                                    items: [this.odo_extrae2]
//                                }, {
//                                    columnWidth: .99,
//                                    border: false,
//                                    layout: 'form',
//                                    items: [this.odo_desc]
//                                }]
//                        }
//                    ]
//                })]
//        });
//
//        this.frm = new Ext.FormPanel({
//            region: 'center',
//            url: '<[controller]>',
//            monitorValid: true,
//            frame: true, border: false,
//            items: [
//                new Ext.Panel({
////                    bodyStyle: 'padding:10px;',
//                    labelWidth: 75,
//                    border: false,
//                    labelAlign: 'top',
//                    activeTab: 0,
//                    items: [{
//                            xtype: 'panel',
//                            layout: 'column',
//                            items: [{
//                                    xtype: 'panel',
//                                    columnWidth: .75,
//                                    items: [this.odont]
//                                }, {
//                                    xtype: 'panel',
//                                    columnWidth: .25,
//                                    items: [this.modulos,
//                                        this.grilla,
//                                        new Ext.Panel({
//                                            border: false,
//                                            columnWidth: .99,
//                                            items: [{
//                                                    xtype: 'fieldset',
//                                                    layout: 'column',
//                                                    title: 'Piezas',
//                                                    items: [
//                                                        new Ext.Panel({
//                                                            border: false,
//                                                            columnWidth: .15,
//                                                            layout: 'form',
////                                                            height: 65,
//                                                            html: '<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false"><img src="images/dientes/00000-1.png" /></div>',
//                                                            items: []//this.med_od, ]
//                                                        }), new Ext.Panel({
//                                                            border: false,
//                                                            columnWidth: .09,
//                                                            layout: 'form',
//                                                            items: [this.nro_diente]
//                                                        }), new Ext.Panel({
//                                                            border: false,
//                                                            columnWidth: .09,
//                                                            layout: 'form',
//                                                            items: [this.nro_examen]
//                                                        }), new Ext.Panel({
//                                                            border: false,
//                                                            columnWidth: .13,
//                                                            layout: 'form',
//                                                            items: [this.diente_pz1]
//                                                        }), new Ext.Panel({
//                                                            border: false,
//                                                            columnWidth: .13,
//                                                            layout: 'form',
//                                                            items: [this.diente_pz2]
//                                                        }), new Ext.Panel({
//                                                            border: false,
//                                                            columnWidth: .13,
//                                                            layout: 'form',
//                                                            items: [this.diente_pz3]
//                                                        }), new Ext.Panel({
//                                                            border: false,
//                                                            columnWidth: .13,
//                                                            layout: 'form',
//                                                            items: [this.diente_pz4]
//                                                        }), new Ext.Panel({
//                                                            border: false,
//                                                            columnWidth: .13,
//                                                            layout: 'form',
//                                                            items: [this.diente_pz5]
//                                                        }), new Ext.Panel({
//                                                            border: false,
//                                                            columnWidth: .99,
//                                                            layout: 'form',
//                                                            items: [this.odo_obsr]
//                                                        })]// this.odo_obsr
//                                                }]
//                                        })
////                                        , new Ext.Panel({
////                                            border: false,
////                                            columnWidth: .99,
////                                            layout: 'form',
////                                            items: [this.recomendaciones, ]
////                                        })
//                                    ]
//                                }]
//                        }]
//                })
//            ],
//            buttons: [{
//                    text: 'Guardar',
//                    iconCls: 'guardar',
//                    formBind: true,
//                    scope: this,
//                    handler: function () {
//                        mod.odonto.nuevo.win.el.mask('Guardando…', 'x-mask-loading');
//                        var metodo;
//                        if (this.record.get('st_id') >= 1) {
//                            metodo = 'update';
//                        } else {
//                            metodo = 'save';
//                        }
//                        this.frm.getForm().submit({
//                            params: {
//                                acction: metodo
//                                , adm: this.record.get('adm')
//                                , odo_id: this.record.get('odo_id')
//                            },
//                            success: function () {
//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
//                                mod.odonto.st.reload();
//                                mod.odonto.nuevo.win.el.unmask();
//                                mod.odonto.nuevo.win.close();
//                            },
//                            failure: function (form, action) {
//                                console.log(metodo);
//                                mod.odonto.nuevo.win.el.unmask();
//                                switch (action.failureType) {
//                                    case Ext.form.Action.CLIENT_INVALID:
//                                        Ext.Msg.alert('Failure', 'Existen valores Invalidos');
//                                        break;
//                                    case Ext.form.Action.CONNECT_FAILURE:
//                                        Ext.Msg.alert('Failure', 'Error de comunicacion con servidor');
//                                        break;
//                                    case Ext.form.Action.SERVER_INVALID:
//                                        Ext.Msg.alert('Failure mik', action.result.error);
//                                        break;
//                                    default:
//                                        Ext.Msg.alert('Failure', action.result.error);
//                                }
//                            }
//                        });
//                    }
//                }
//            ]
//        }),
//                this.win = new Ext.Window({
//                    width: 1240,
//                    height: 520,
//                    modal: true,
//                    title: 'ODONTOGRAMA :' + this.record.get('nombre'),
//                    border: false,
//                    collapsible: true,
//                    maximizable: true,
//                    resizable: false,
//                    draggable: true,
//                    closable: true,
//                    layout: 'border',
////            closeAction: 'hide',
////            plain: true,
//                    items: [this.frm]
//                });
//    }
//};
Ext.onReady(mod.odonto.init, mod.odonto);