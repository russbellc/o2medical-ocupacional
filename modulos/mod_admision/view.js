Ext.ns("mod.admision");
mod.admision = {
	panel: null,
	detalle: null,
	paginador: null,
	buscador: null,
	tbar: null,
	dt_grid: null,
	init: function () {
		this.crea_stores();
		this.crea_controles();
		this.st.load();
		this.panel.render("<[view]>");
	},
	crea_stores: function () {
		this.st = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_pac",
				format: "json",
			},
			listeners: {
				beforeload: function () {
					this.baseParams.columna = mod.admision.descripcion.getValue();
				},
			},
			//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=reporte&adm=1003
			root: "data",
			totalProperty: "total",
			fields: [
				"adm_id",
				"emp_acro",
				"nombre",
				"edad",
				"pac_sexo",
				"puesto",
				"tfi_desc",
				"adm_usu",
				"adm_aptitud",
				"adm_factura",
				"adm_fech",
				"adm_foto",
			],
		});
	},
	crea_controles: function () {
		this.paginador = new Ext.PagingToolbar({
			pageSize: 90,
			store: this.st,
			displayInfo: true,
			displayMsg: "Mostrando {0} - {1} de {2} Pacientes",
			emptyMsg: "No Existe Registros",
			plugins: new Ext.ux.ProgressBarPager(),
		});
		this.buscador = new Ext.ux.form.SearchField({
			width: 250,
			fieldLabel: "Nombre",
			store: this.st,
			id: "search_query",
			emptyText: "Ingrese dato a buscar...",
		});
		this.descripcion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1", "NRO DE FILIACIÓN"],
					["2", "DNI"],
					["3", "APELLIDOS Y NOMBRES"],
					["4", "EMPRESA O RUC"],
					["5", "TIPO DE FICHA"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue(1);
					descripcion.setRawValue("Nro Filiacion");
				},
			},
		});
		this.tbar = new Ext.Toolbar({
			items: [
				"Buscar por: ",
				this.descripcion,
				this.buscador,
				"->",
				"-",
				{
					text: "Reporte x Fecha",
					iconCls: "reporte",
					handler: function () {
						mod.hruta.rfecha.init(null);
					},
				},
				"-",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.admision.registro.init(null);
					},
				},
			],
		});
		this.dt_grid = new Ext.grid.GridPanel({
			store: this.st,
			tbar: this.tbar,
			loadMask: true,
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			bbar: this.paginador,
			height: 500,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var record = grid.getStore().getAt(rowIndex);
					mod.admision.registro.init(record);
				},
				rowcontextmenu: function (grid, index, event) {
					event.stopEvent();
					var record = grid.getStore().getAt(index);
					new Ext.menu.Menu({
						items: [
							{
								text: "<b>HOJA DE RUTA N° " + record.get("adm_id") + "</b>",
								iconCls: "reporte",
								handler: function () {
									new Ext.Window({
										title: "HOJA DE RUTA N° " + record.get("adm_id"),
										width: 800,
										height: 600,
										maximizable: true,
										modal: true,
										closeAction: "close",
										resizable: true,
										html:
											"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_admision&sys_report=reporte&adm=" +
											record.get("adm_id") +
											"'></iframe>",
									}).show();
								},
							},
						],
					}).showAt(event.xy);
				},
			},
			autoExpandColumn: "emp_descri",
			columns: [
				{
					header: "<CENTER></CENTER>",
					width: 25,
					sortable: true,
					dataIndex: "adm_foto",
					renderer: function renderIcon(val) {
						if (val == "1") {
							return '<CENTER><img src="<[images]>/user-icon.png" ttitle="FOTO" height="13"></CENTER>';
						} else {
							return "<CENTER>-</CENTER>";
						}
					},
				},
				{
					header: "N°F",
					width: 40,
					dataIndex: "adm_id",
				},
				{
					header: "NOMBRE",
					width: 230,
					dataIndex: "nombre",
				},
				{
					header: "SX",
					width: 25,
					sortable: true,
					dataIndex: "pac_sexo",
					renderer: function renderIcon(val) {
						if (val == "M") {
							return '<center><img src="<[images]>/male.png" title="Masculino" height="13"></center>';
						} else if (val == "F") {
							return '<center><img src="<[images]>/fema.png" title="Femenino" height="13"></center>';
						}
					},
				},
				{
					header: "EDAD",
					width: 40,
					id: "edad",
					dataIndex: "edad", //emp_acro
				},
				{
					header: "PUESTO LABORAL",
					width: 150,
					sortable: true,
					dataIndex: "puesto",
				},
				{
					header: "EMPRESA ACRONIMO",
					id: "emp_descri",
					dataIndex: "emp_acro", //emp_acro
				},
				{
					header: "TIPO DE FICHA",
					width: 110,
					dataIndex: "tfi_desc",
				},
				{
					header: "USUARIO",
					width: 60,
					dataIndex: "adm_usu",
				},
				{
					header: "APTITUD",
					dataIndex: "adm_aptitud",
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
				{
					header: "FECHA",
					width: 140,
					dataIndex: "adm_fech", //emp_desc
				},
			],
			viewConfig: {
				getRowClass: function (record, index) {
					// var st = record.get("adm_aptitud");
					// if (st == "APTO") {
					// 	return "child-row";
					// } else if (st == "APTO CON OBSERVACIONES") {
					// 	return "child-row";
					// } else if (st == "APTO CON RESTRICCIÓN") {
					// 	return "child-row";
					// } else if (st == "NO APTO TEMPORAL") {
					// 	return "child-row";
					// } else if (st == "NO APTO DEFINITIVO") {
					// 	return "child-row";
					// } else if (st == "EN PROCESO DE VALIDACION") {
					// 	return "child-row";
					// } else {
					// 	return "";
					// }
				},
			},
		});
		this.panel = new Ext.form.FormPanel({
			anchor: "99%",
			layout: "column",
			height: 500,
			border: false,
			//            title: 'Lista de Cajas',
			items: [
				{
					columnWidth: 0.999,
					border: false,
					layout: "form",
					items: [this.dt_grid],
				},
				//                ,{
				//                    columnWidth: .15,
				//                    border: false,
				//                    layout: 'form',
				//                    items: [this.detalle]
				//                }
			],
		});
	},
};
mod.admision.foto = {
	win: null,
	frm: null,
	init: function () {
		this.crea_controles();
		this.win.show();
	},
	crea_controles: function () {
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			frame: true,
			monitorValid: true,
			autoLoad: {
				url: "../librerias/scriptcam/firme.htm",
				scripts: true,
			},
			items: [],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.admision.registro.foto_desc.setValue(
							$.scriptcam.getFrameAsBase64()
						);
						mod.admision.registro.imgStore.removeAll();
						var store = mod.admision.registro.imgStore;
						var record = new store.recordType({
							id: "",
							foto: "data:image/png;base64," + $.scriptcam.getFrameAsBase64(),
						});
						store.add(record);
						mod.admision.registro.adm_foto.setValue("1");
						mod.admision.foto.win.close();
					},
				},
			],
		});
		this.win = new Ext.Window({
			title: "Foto DNI: ",
			height: 510,
			width: 348,
			modal: true,
			border: false,
			frame: true,
			layout: "fit",
			items: [this.frm],
		});
	},
};
mod.admision.registro = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.list_Tficha.load();
		this.st_busca_area.load();
		this.st_busca_puesto.load();
		this.crea_controles();
		if (this.record !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		Ext.Ajax.request({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			url: "<[controller]>",
			params: {
				acction: "load_data_adm",
				format: "json",
				adm_id: mod.admision.registro.record.get("adm_id"),
			},
			success: function (response, opts) {
				var dato = Ext.decode(response.responseText);
				if (dato.success == true) {
					mod.admision.registro.frm.getForm().loadRecord(dato);

					mod.admision.registro.adm_emp.setValue(dato.data.emp_id);
					mod.admision.registro.adm_emp.setRawValue(dato.data.empresa);
					mod.admision.registro.st_empre.load();

					mod.admision.registro.adm_pac.setValue(dato.data.pac_id);
					mod.admision.registro.adm_pac.setRawValue(dato.data.adm_pac);

					mod.admision.registro.adm_tficha.setValue(dato.data.adm_tficha);
					mod.admision.registro.adm_tficha.setRawValue(dato.data.tfi_desc);

					mod.admision.registro.list_perfil.load();

					mod.admision.registro.list_perfil2.reload({
						params: {
							pk_id: dato.data.adm_ruta,
						},
					});

					mod.admision.registro.desc.setValue(dato.data.pk_desc);
					mod.admision.registro.tficha.setValue(dato.data.tfi_desc);

					var total = parseFloat(dato.data.pk_precio);

					Ext.Ajax.request({
						waitMsg: "Recuperando Informacion...",
						waitTitle: "Espere",
						url: "<[controller]>",
						params: {
							acction: "numeroletra",
							format: "json",
							total: dato.data.pk_precio,
						},
						success: function (response, opts) {
							var dato = Ext.decode(response.responseText);
							if (dato.success == true) {
								mod.admision.registro.dpk_precio.setValue(total.toFixed(2));
								mod.admision.registro.totaletra.setValue(dato.letra);
							}
						},
					});

					//                    data: [['01', '<[sys_images]>/fotos/foto.png']]
					if (dato.data.adm_foto == 1) {
						mod.admision.registro.imgStore.removeAll();
						var store = mod.admision.registro.imgStore;
						var record = new store.recordType({
							id: "",
							foto: "<[sys_images]>/fotos/" + dato.data.adm_id + ".png",
						});
						store.add(record);
					}
					mod.admision.registro.adm_foto.setValue(dato.data.adm_foto);

					//                    mod.admision.registro.total.setValue(dato.data.total);
					//                    mod.admision.registro.totaletra.setValue(dato.data.totaletra);

					//                    mod.admision.registro.st.load({
					//                        params: {
					//                            adm_id: mod.admision.registro.record.get('adm_id')
					//                        }
					//                    });
					//                    mod.admision.registro.adm_examen.enable();
				}
			},
		});
	},
	crea_stores: function () {
		this.imgStore = new Ext.data.ArrayStore({
			id: 0,
			fields: ["id", "foto"],
			data: [["01", "<[sys_images]>/fotos/foto.png"]],
		});
		this.st_empre = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_empre",
				format: "json",
			},
			fields: ["emp_id", "emp_desc", "empresa"],
			root: "data",
		});
		this.list_pacient = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_pacient",
				format: "json",
			},
			fields: ["pac_id", "pac_ndoc", "nombres", "todo", "sexo", "cell", "edad"],
			root: "data",
		});
		this.list_Tficha = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_Tficha",
				format: "json",
			},
			fields: ["tfi_id", "tfi_desc"],
			root: "data",
		});
		this.st_busca_area = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_area",
				format: "json",
			},
			fields: ["adm_area"],
			root: "data",
		});
		this.st_busca_puesto = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_puesto",
				format: "json",
			},
			fields: ["adm_puesto"],
			root: "data",
		});
		this.list_perfil = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_perfil",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.emp_id = mod.admision.registro.adm_emp.getValue();
				},
			},
			fields: [
				"pk_id",
				"pk_usu",
				"pk_fech",
				"pk_desc",
				"pk_emp",
				"sede_desc",
				"cargo_desc",
				"tfi_id",
				"tfi_desc",
				"pk_precio",
				"pk_estado",
				"horas",
			],
		});
		this.list_cargo = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_cargo",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.emp_id = mod.admision.registro.adm_emp.getValue();
				},
			},
			fields: ["cargo_id", "cargo_emp", "cargo_desc"],
		});
		this.list_sede = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_sede",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.emp_id = mod.admision.registro.adm_emp.getValue();
				},
			},
			fields: ["sede_id", "sede_emp", "sede_desc"],
		});
		this.list_perfil2 = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_perfil2",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"dpk_pkid",
				"ex_id",
				"ex_desc",
				"ar_desc",
				"dpk_usu",
				"dpk_fech",
				"dpk_precio",
			],
		});
	},
	formato_numero: function (
		numero,
		decimales,
		separador_decimal,
		separador_miles
	) {
		numero = parseFloat(numero);
		if (isNaN(numero)) {
			return "";
		}

		if (decimales !== undefined) {
			// Redondeamos
			numero = numero.toFixed(decimales);
		}

		// Convertimos el punto en separador_decimal
		numero = numero
			.toString()
			.replace(".", separador_decimal !== undefined ? separador_decimal : ",");
		if (separador_miles) {
			// Añadimos los separadores de miles
			var miles = new RegExp("(-?[0-9]+)([0-9]{3})");
			while (miles.test(numero)) {
				numero = numero.replace(miles, "$1" + separador_miles + "$2");
			}
		}

		return numero;
	},
	update_Total: function () {
		var total = 0;
		Ext.each(mod.admision.registro.list_perfil2.data.items, function (op, i) {
			total += parseFloat(op.data.dpk_precio);
		});
		Ext.Ajax.request({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			url: "<[controller]>",
			params: {
				acction: "numeroletra",
				format: "json",
				total: total,
			},
			success: function (response, opts) {
				var dato = Ext.decode(response.responseText);
				if (dato.success == true) {
					mod.admision.registro.dpk_precio.setValue(total.toFixed(2));
					mod.admision.registro.totaletra.setValue(dato.letra);
				}
			},
		});
	},
	crea_controles: function () {
		var tpl = new Ext.XTemplate(
			'<tpl for=".">',
			//                '<br>',
			'<div class="thumb"><img height="155" src="{foto}" title="{id}"></div>',
			"</tpl>"
		);
		this.odont = new Ext.DataView({
			autoScroll: true,
			id: "dientes_view",
			store: this.imgStore,
			tpl: tpl,
			autoHeight: false,
			height: 160,
			multiSelect: false,
			overClass: "x-view-over",
			itemSelector: "div.thumb-wrap2",
			emptyText: "No hay foto disponible",
		});
		this.adm_foto = new Ext.form.Hidden({
			fieldLabel: "<b>id</b>",
			name: "adm_foto",
			readOnly: true,
			anchor: "70%",
		});
		this.foto_desc = new Ext.form.Hidden({
			fieldLabel: "<b>Foto</b>",
			name: "foto_desc",
			readOnly: true,
			anchor: "90%",
		});
		this.resultTpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"{emp_id}",
			"<h3><span>{empresa}</span></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.resultpac = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"Nro Doc.: {pac_ndoc}",
			'<h3><span><img src="<[sys_images]>/{sexo}.png" title="{sexo}" height="13"> - {nombres}</span></h3>',
			"</div>",
			"</div></tpl>"
		);
		this.resultref = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"{ref_especialidad}",
			"<h3><span>{nombres}</span></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.resultExa = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"{area_desc}",
			"<h3><span>{serv_desc}</span></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.adm_emp = new Ext.form.ComboBox({
			store: this.st_empre,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.resultTpl,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 3,
			hiddenName: "adm_emp",
			displayField: "empresa",
			valueField: "emp_id",
			allowBlank: false,
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>RUC - Empresa</b>",
			emptyText: "RUC - Empresa",
			mode: "remote",
			anchor: "95%",
			listeners: {
				scope: this,
				afterrender: function (combo) {
					//                    combo.setValue('1');// El ID de la opción por defecto setRawValue
					//                    combo.setRawValue('1 - PARTICULAR');// El ID de la opción por defecto setRawValue
					//                    this.provincia.load();
				},
				select: function (combo, registro, indice) {
					mod.admision.registro.edad.setValue("");
					mod.admision.registro.sexo.setValue("");
					mod.admision.registro.cell.setValue("");
					mod.admision.registro.dpk_precio.setValue("");
					mod.admision.registro.totaletra.setValue("");
					mod.admision.registro.adm_ruta.setValue("");
					mod.admision.registro.desc.setValue("");
					mod.admision.registro.tficha.setValue("");
					mod.admision.registro.adm_pac.clearValue();
					mod.admision.registro.adm_tficha.clearValue();
					mod.admision.registro.sede_desc.clearValue();
					mod.admision.registro.cargo_desc.clearValue();
					mod.admision.registro.adm_area.clearValue();
					mod.admision.registro.adm_puesto.clearValue();
					mod.admision.registro.list_perfil2.removeAll();
					mod.admision.registro.list_perfil.load();
					mod.admision.registro.list_sede.load();
					mod.admision.registro.list_cargo.load();
				},
			},
		});
		this.adm_pac = new Ext.form.ComboBox({
			store: this.list_pacient,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.resultpac,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "adm_pac",
			displayField: "todo",
			valueField: "pac_id",
			allowBlank: false,
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DNI - Paciente</b>",
			emptyText: "DNI - Paciente",
			mode: "remote",
			anchor: "95%",
			listeners: {
				scope: this,
				specialkey: function (combo, e) {
					if (e.getKey() == e.ENTER) {
						//                        var recordSelected = combo.getStore();
						//                        console.table(recordSelected);
						//                        mod.admision.registro.adm_pac.getValue('');
						//                        mod.admision.registro.adm_pac.setRawValue('');

						//                        mod.admision.nuevo.pac_tdoc.setValue(mod.admision.registro.adm_pac.getValue(''));
						//                        mod.admision.nuevo.pac_tdoc.setRawValue(dato.data.tdoc_desc);
						console.log(mod.admision.registro.adm_pac.getValue());
						//                        console.log(recordSelected.totalLength);
						//                        mod.admision.nuevo.verifica_dni2();
					}
				},
				select: function (combo, registro, indice) {
					var recordSelected = combo.getStore().getAt(indice);
					mod.admision.registro.edad.setValue(recordSelected.get("edad"));
					mod.admision.registro.sexo.setValue(recordSelected.get("sexo"));
					mod.admision.registro.cell.setValue(recordSelected.get("cell"));
					mod.admision.registro.dpk_precio.setValue("");
					mod.admision.registro.totaletra.setValue("");
				},
			},
		});
		this.edad = new Ext.form.TextField({
			fieldLabel: "<b>Edad</b>",
			name: "edad",
			readOnly: true,
			anchor: "95%",
		});
		this.sexo = new Ext.form.TextField({
			fieldLabel: "<b>Sexo</b>",
			name: "sexo",
			readOnly: true,
			anchor: "95%",
		});
		this.cell = new Ext.form.TextField({
			fieldLabel: "<b>Cell/Telf</b>",
			name: "cell",
			readOnly: true,
			anchor: "95%",
			maskRe: /[\d]/,
		});

		//        this.adm_area = new Ext.form.ComboBox({
		//            store: this.st_busca_area,
		//            hiddenName: 'adm_area',
		//            displayField: 'adm_area',
		//            id: 'adm_area',
		//            allowBlank: false,
		//            name: 'adm_area',
		//            valueField: 'adm_area',
		//            minChars: 1,
		//            validateOnBlur: true,
		//            forceSelection: false,
		//            autoSelect: false,
		//            enableKeyEvents: true,
		//            selectOnFocus: false,
		//            fieldLabel: '<b>Area Laboral</b>',
		//            typeAhead: false,
		//            hideTrigger: true,
		//            triggerAction: 'all',
		//            mode: 'local',
		//            emptyText: 'Area laboral del paciente...',
		//            anchor: '95%'
		//        });
		this.adm_tficha = new Ext.form.ComboBox({
			store: this.list_Tficha,
			hiddenName: "adm_tficha",
			displayField: "tfi_desc",
			valueField: "tfi_id",
			//            allowBlank: false,
			typeAhead: false,
			editable: false,
			triggerAction: "all",
			fieldLabel: "<b>Tipo de Ficha</b>",
			mode: "local",
			anchor: "92%",
			listeners: {
				scope: this,
				select: function (combo, registro, indice) {
					mod.admision.registro.dpk_precio.setValue("");
					mod.admision.registro.totaletra.setValue("");
					mod.admision.registro.adm_ruta.setValue("");
					mod.admision.registro.desc.setValue("");
					mod.admision.registro.tficha.setValue("");
					mod.admision.registro.list_perfil2.removeAll();
					mod.admision.registro.list_perfil.reload({
						params: {
							sede_id: mod.admision.registro.sede_desc.getValue(),
							cargo_id: mod.admision.registro.cargo_desc.getValue(),
							tfi_id: mod.admision.registro.adm_tficha.getValue(),
						},
					});
				},
			},
		});
		this.sede_desc = new Ext.form.ComboBox({
			store: this.list_sede,
			hiddenName: "sede_desc",
			displayField: "sede_desc",
			valueField: "sede_id",
			fieldLabel: "<b>Sede/Sucursal</b>",
			typeAhead: false,
			editable: false,
			triggerAction: "all",
			anchor: "95%",
			mode: "remote",
			listeners: {
				scope: this,
				select: function (combo, registro, indice) {
					mod.admision.registro.dpk_precio.setValue("");
					mod.admision.registro.totaletra.setValue("");
					mod.admision.registro.adm_ruta.setValue("");
					mod.admision.registro.desc.setValue("");
					mod.admision.registro.tficha.setValue("");
					mod.admision.registro.list_perfil2.removeAll();
					mod.admision.registro.list_perfil.reload({
						params: {
							sede_id: mod.admision.registro.sede_desc.getValue(),
							cargo_id: mod.admision.registro.cargo_desc.getValue(),
							tfi_id: mod.admision.registro.adm_tficha.getValue(),
						},
					});
				},
			},
		});
		this.cargo_desc = new Ext.form.ComboBox({
			store: this.list_cargo,
			hiddenName: "cargo_desc",
			displayField: "cargo_desc",
			valueField: "cargo_id",
			typeAhead: false,
			editable: false,
			triggerAction: "all",
			fieldLabel: "<b>Cargo/Puesto</b>",
			mode: "remote",
			anchor: "92%",
			listeners: {
				scope: this,
				select: function (combo, registro, indice) {
					mod.admision.registro.dpk_precio.setValue("");
					mod.admision.registro.totaletra.setValue("");
					mod.admision.registro.adm_ruta.setValue("");
					mod.admision.registro.desc.setValue("");
					mod.admision.registro.tficha.setValue("");
					mod.admision.registro.list_perfil2.removeAll();
					mod.admision.registro.list_perfil.reload({
						params: {
							sede_id: mod.admision.registro.sede_desc.getValue(),
							cargo_id: mod.admision.registro.cargo_desc.getValue(),
							tfi_id: mod.admision.registro.adm_tficha.getValue(),
						},
					});
				},
			},
		});
		this.adm_puesto = new Ext.form.ComboBox({
			store: this.st_busca_puesto,
			hiddenName: "adm_puesto",
			displayField: "adm_puesto",
			id: "adm_puesto",
			allowBlank: false,
			name: "adm_puesto",
			valueField: "adm_puesto",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>Puesto Laboral</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			emptyText: "Puesto laboral del paciente...",
			anchor: "95%",
		});
		this.adm_area = new Ext.form.ComboBox({
			store: this.st_busca_area,
			hiddenName: "adm_area",
			displayField: "adm_area",
			id: "adm_area",
			allowBlank: false,
			name: "adm_area",
			valueField: "adm_area",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>Area Laboral</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			emptyText: "Area laboral del paciente...",
			anchor: "95%",
		});
		this.adm_ruta = new Ext.form.TextField({
			fieldLabel: "<b>ID</b>",
			name: "adm_ruta",
			maskRe: /[\d]/,
			allowBlank: false,
			readOnly: true,
			anchor: "30%",
			width: 30,
		});
		////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////
		this.paginador3 = new Ext.PagingToolbar({
			pageSize: 100,
			store: this.list_perfil,
			displayInfo: true,
			displayMsg: "Hay {0} - {1} de {2} PERFILES",
			emptyMsg: "No Existe Registros",
			plugins: new Ext.ux.ProgressBarPager(),
		});
		this.desc = new Ext.form.TextField({
			fieldLabel: "<b>GRUPO</b>",
			name: "desc",
			maskRe: /[\d]/,
			readOnly: true,
			anchor: "95%",
			width: 250,
		});
		this.tficha = new Ext.form.TextField({
			fieldLabel: "<b>TIPO DE FICHA</b>",
			name: "tficha",
			maskRe: /[\d]/,
			readOnly: true,
			anchor: "95%",
			width: 160,
		});
		this.dt_grid3 = new Ext.grid.GridPanel({
			store: this.list_perfil,
			region: "west",
			border: true,
			tbar: ["<b>LISTA DE RUTAS DE LA EMPRESA</b>"], //cargo_desc
			loadMask: true,
			iconCls: "icon-grid",
			listeners: {
				rowclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var record = grid.getStore().getAt(rowIndex);
					var total = parseFloat(record.get("pk_precio"));
					mod.admision.registro.adm_ruta.setValue(record.get("pk_id"));
					mod.admision.registro.desc.setValue(record.get("pk_desc"));
					mod.admision.registro.tficha.setValue(record.get("tfi_desc"));
					mod.admision.registro.adm_tficha.setValue(record.get("tfi_id"));
					mod.admision.registro.adm_tficha.setRawValue(record.get("tfi_desc"));
					mod.admision.registro.dpk_precio.setValue("");
					mod.admision.registro.totaletra.setValue("");
					mod.admision.registro.list_perfil2.reload({
						params: {
							pk_id: record.get("pk_id"),
						},
					});
					Ext.Ajax.request({
						waitMsg: "Recuperando Informacion...",
						waitTitle: "Espere",
						url: "<[controller]>",
						params: {
							acction: "numeroletra",
							format: "json",
							total: total,
						},
						success: function (response, opts) {
							var dato = Ext.decode(response.responseText);
							if (dato.success == true) {
								mod.admision.registro.dpk_precio.setValue(total.toFixed(2));
								mod.admision.registro.totaletra.setValue(dato.letra);
							}
						},
					});
				},
			},
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			bbar: this.paginador3,
			height: 354,
			autoExpandColumn: "pk_desc",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "pk_desc",
					header: "RUTA",
					dataIndex: "pk_desc",
				},
				{
					header: "SEDE",
					dataIndex: "sede_desc",
					width: 125,
				},
				{
					header: "CARGO",
					dataIndex: "cargo_desc",
					width: 125,
				},
				{
					header: "PERFIL",
					dataIndex: "tfi_desc",
					width: 110,
				},
			],
		});
		////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////
		this.dpk_precio = new Ext.form.TextField({
			name: "dpk_precio",
			width: 60,
			readOnly: true,
			fieldStyle: "background-color: #ddd; background-image: none;",
		});
		this.totaletra = new Ext.form.TextField({
			name: "totaletra",
			width: 400,
			readOnly: true,
		});
		this.bbar = new Ext.Toolbar({
			items: [
				"-",
				"<B>SON: </B>",
				this.totaletra,
				//                '-', '<B>CANCELADO: </B>', this.chec_estado, '-', '<B>A CUENTA: </B>', this.adm_cuenta,
				"->",
				"-",
				"<B>TOTAL: </B>",
				this.dpk_precio,
				"-",
			],
		});
		this.pk_estado = new Ext.form.RadioGroup({
			fieldLabel: "<b>Estado</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ACTIVADO",
					checked: true,
					name: "pk_estado",
					inputValue: "1",
				},
				{ boxLabel: "BLOQUEADO", name: "pk_estado", inputValue: "0" },
			],
		});
		this.dt_grid = new Ext.grid.GridPanel({
			store: this.list_perfil2,
			region: "west",
			border: true,
			tbar: [
				" <b>RUTA:</b> ",
				this.desc,
				"-",
				" <b>TIPO DE FICHA:</b> ",
				this.tficha,
			], //cargo_desc
			bbar: this.bbar,
			loadMask: true,
			iconCls: "icon-grid",
			height: 354,
			autoExpandColumn: "ex_desc",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "ex_desc",
					header: "EXAMEN",
					dataIndex: "ex_desc",
				},
				{
					header: "AREA",
					dataIndex: "ar_desc",
					width: 150,
				},
				{
					xtype: "numbercolumn",
					format: "S/ 0.00",
					header: "PRECIO",
					dataIndex: "dpk_precio",
					width: 80,
				},
			],
		});
		////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			items: [
				{
					xtype: "panel",
					layout: "column",
					border: false,
					columnWidth: 0.7,
					items: [
						this.foto_desc,
						this.adm_foto,
						{
							columnWidth: 0.17,
							border: false,
							layout: "form",
							items: [this.odont],
						},
						{
							columnWidth: 0.73,
							border: false,
							layout: "form",
							labelWidth: 99,
							items: [this.adm_emp],
						},
						{
							columnWidth: 0.1,
							border: false,
							layout: "form",
							items: [
								{
									xtype: "button",
									id: "empresa_ico",
									disabled: false,
									iconCls: "list",
									text: "Lista Emp.",
									tooltip: "Lista de Empresas",
									handler: function () {
										mod.admision.empresa.init(null);
									},
								},
							],
						},
						{
							columnWidth: 0.63,
							border: false,
							layout: "form",
							labelWidth: 99,
							items: [this.adm_pac],
						},
						{
							columnWidth: 0.1,
							border: false,
							layout: "form",
							items: [
								{
									xtype: "button",
									id: "adm_pac_icos",
									disabled: false,
									iconCls: "add",
									//                                    text: 'Agregar',
									text: "Registrar",
									tooltip: "Registrar Paciente",
									handler: function () {
										mod.admision.nuevo.init(null);
									},
								},
							],
						},
						{
							columnWidth: 0.1,
							border: false,
							layout: "form",
							items: [
								{
									xtype: "button",
									id: "adm_pac_ico",
									disabled: false,
									iconCls: "list",
									text: "Lista Pac.",
									tooltip: "Lista de Päcientes",
									handler: function () {
										mod.admision.pacientes.init(null);
									},
								},
							],
						},
						{
							columnWidth: 0.27,
							border: false,
							layout: "form",
							labelWidth: 99,
							items: [this.edad],
						},
						{
							columnWidth: 0.27,
							border: false,
							layout: "form",
							labelWidth: 50,
							items: [this.sexo],
						},
						{
							columnWidth: 0.28,
							border: false,
							layout: "form",
							labelWidth: 60,
							items: [this.cell],
						},
						{
							columnWidth: 0.41,
							border: false,
							layout: "form",
							labelWidth: 99,
							items: [this.adm_tficha],
						},
						{
							columnWidth: 0.42,
							border: false,
							layout: "form",
							labelWidth: 99,
							items: [this.sede_desc],
						},
						{
							columnWidth: 0.41,
							border: false,
							layout: "form",
							labelWidth: 99,
							items: [this.cargo_desc],
						},
						{
							columnWidth: 0.42,
							border: false,
							layout: "form",
							labelWidth: 90,
							items: [this.adm_area],
						},
						{
							columnWidth: 0.12,
							border: false,
							layout: "form",
							//                            bodyStyle: 'padding:15px 8px 0 0;', this.adm_ruta  this.adm_area
							items: [
								{
									xtype: "button",
									id: "btn-select-foto",
									text: "WEBCAM",
									disabled: false,
									tooltip: "CAMARA WEB",
									iconCls: "cam",
									handler: function () {
										mod.admision.foto.init();
									},
								},
							],
						},
						{
							columnWidth: 0.46,
							border: false,
							layout: "form",
							labelWidth: 110,
							items: [this.adm_puesto],
						},
						{
							columnWidth: 0.25,
							border: false,
							layout: "form",
							labelWidth: 25,
							items: [this.adm_ruta],
						},
					],
				},
				{
					columnWidth: 0.3,
					border: false,
					layout: "form",
					items: [
						{
							xtype: "fieldset",
							items: [
								{
									columnWidth: 0.99,
									border: false,
									layout: "form",
									bodyStyle:
										"background: #267ED7;color:#ffffff;padding: 8px 5px 8px 5px;font-size: 15px;",
									html: "<center><B>R.U.C. N° 000000000000</B></center>",
								},
								{
									columnWidth: 0.99,
									border: false,
									layout: "form",
									bodyStyle:
										"background: #D3E1F1;color:#267ED7;padding: 22px 5px 22px 5px;font-size: 20px;",
									html: "<center><B>CENTRO MÉDICO OCUPACIONAL OPTIMA S.A.C.</B></center>",
									//                                    html: '<center><B>....</B></center>'
								},
							],
						},
					],
				},
				{
					columnWidth: 0.5,
					layout: "form",
					border: false,
					items: [this.dt_grid3],
				},
				{
					columnWidth: 0.5,
					layout: "form",
					border: false,
					items: [this.dt_grid],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.admision.registro.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction: this.record !== null ? "update_adm" : "save_adm",
								format: "json",
								adm_id:
									this.record !== null
										? mod.admision.registro.record.get("adm_id")
										: "",
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								Ext.MessageBox.alert(
									"En hora buena",
									"Se registro exitosamente"
								);
								mod.admision.st.reload();
								mod.admision.registro.win.el.unmask();
								mod.admision.registro.win.close();
								Ext.Msg.show({
									title: "HOJA DE RUTA",
									msg: "DESEA IMPRIMIR LA HOJA DE RUTA N°" + obj.data + "?",
									buttons: Ext.Msg.YESNO,
									fn: function (opt) {
										if (opt == "yes") {
											var web;
											if (!web) {
												web = new Ext.Window({
													title: "HOJA DE RUTA",
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_admision&sys_report=reporte&adm=" +
														obj.data +
														"'></iframe>",
												});
											}
											web.show();
										}
										if (opt == "no") {
										}
									},
								});
							},
							failure: function (form, action) {
								//                                console.log(metodo);
								mod.admision.registro.win.el.unmask();
								switch (action.failureType) {
									case Ext.form.Action.CLIENT_INVALID:
										Ext.Msg.alert("Failure", "Existen valores Invalidos");
										break;
									case Ext.form.Action.CONNECT_FAILURE:
										Ext.Msg.alert(
											"Failure",
											"Error de comunicacion con servidor"
										);
										break;
									case Ext.form.Action.SERVER_INVALID:
										Ext.Msg.alert("Failure mik", action.result.error);
										mod.admision.st.reload();
										mod.admision.registro.win.close();
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
							},
						});
					},
				},
			],
		});
		this.win = new Ext.Window({
			width: 1200,
			height: 600,
			modal: true,
			title: "ADMISIÓN",
			border: false,
			collapsible: true,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
mod.admision.pacientes = {
	win: null,
	frm: null,
	init: function () {
		this.crea_stores();
		this.crea_controles();
		this.st.load();
		this.win.show();
	},
	cargar_data: function () {},
	crea_stores: function () {
		this.st = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_paciente",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"pac_id",
				"pac_ndoc",
				"pac_sexo",
				"pac_cel",
				"pac_fech",
				"pac_domdir",
				"nombre",
				"pac_correo",
				"pac_usu",
				"edad",
			],
		});
	},
	crea_controles: function () {
		this.paginador = new Ext.PagingToolbar({
			pageSize: 50,
			store: this.st,
			displayInfo: true,
			displayMsg: "Mostrando {0} - {1} de {2} Empresas",
			emptyMsg: "No Existe Registros",
			plugins: new Ext.ux.ProgressBarPager(),
		});

		this.buscador = new Ext.ux.form.SearchField({
			width: 250,
			fieldLabel: "Nombre",
			store: this.st,
			id: "search_query",
			emptyText: "Buscar por DNI o Apellidos y Nombres",
		});

		this.tbar = new Ext.Toolbar({
			items: [
				"Buscar:",
				this.buscador,
				"->",
				"-",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.admision.nuevo.init(null);
					},
				},
			],
		});

		this.dt_grid = new Ext.grid.GridPanel({
			store: this.st,
			region: "west",
			border: false,
			tbar: this.tbar,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			bbar: this.paginador,
			height: 462,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var record = grid.getStore().getAt(rowIndex);
					mod.admision.nuevo.init(record);
				},
			},
			autoExpandColumn: "pac_dir",
			columns: [
				{
					header: "H.C",
					dataIndex: "pac_id",
					width: 60,
				},
				{
					header: "Número DNI",
					dataIndex: "pac_ndoc",
					width: 80,
				},
				{
					header: "Sexo",
					width: 50,
					sortable: true,
					dataIndex: "pac_sexo",
					renderer: function renderIcon(val) {
						if (val == "M") {
							return '<CENTER><img src="<[images]>/male.png" ttitle="Masculino" height="13"></CENTER>';
						} else if (val == "F") {
							return '<CENTER><img src="<[images]>/fema.png" title="Femenino" height="13"></CENTER>';
						}
					},
				},
				{
					id: "pac_dir",
					header: "Nombre",
					dataIndex: "nombre",
					width: 250,
				},
				{
					header: "Edad",
					width: 35,
					id: "edad",
					dataIndex: "edad",
				},
				{
					header: "Celular",
					dataIndex: "pac_cel",
					width: 75,
				},
				{
					header: "Usuario",
					dataIndex: "pac_usu",
					width: 60,
				},
				{
					header: "Fecha Admisión",
					dataIndex: "pac_fech",
					width: 140,
				},
			],
		});

		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			layout: "column",
			items: [
				{
					columnWidth: 0.999,
					border: false,
					layout: "form",
					items: [this.dt_grid],
				},
			],
		});
		this.win = new Ext.Window({
			width: 900,
			height: 500,
			modal: true,
			title: "LISTA DE PACIENTES",
			border: false,
			collapsible: true,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
mod.admision.nuevo = {
	pac_tdoc: null,
	pac_ndoc: null,
	pac_appat: null,
	pac_apmat: null,
	pac_nombres: null,
	pac_sexo: null,
	pac_fech_nac: null,
	pac_cel: null,
	pac_correo: null,
	pac_ubigeo: null,
	pac_direc: null,
	record: null,
	win: null,
	frm: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.crea_controles();
		this.departamento.load();
		this.tdocumento.load();
		this.st_busca_nombres.load();
		this.st_busca_appaterno.load();
		this.st_busca_apmaterno.load();
		this.list_profecion.load();
		this.list_ginstruccion.load();
		if (this.record !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	verifica_dni: function () {
		Ext.Ajax.request({
			url: "<[controller]>",
			params: {
				acction: "st_busca_dni",
				format: "json",
				pac_ndoc:
					mod.admision.nuevo.pac_ndoc.getValue() +
					mod.admision.nuevo.pac_ndoc_ext.getValue(),
			},
			success: function (response, opts) {
				var dato = Ext.decode(response.responseText);
				//                Ext.MessageBox.alert('LiveCode', dato.data.pac_ndoc.length + ' - - ' + dato.data.pac_ndoc);
				if (dato.data == null) {
				} else {
					Ext.MessageBox.show({
						title: "WARNING",
						msg: "Paciente ya Tiene Historia Clinica",
						buttons: Ext.MessageBox.OK,
						animEl: "mb9",
						icon: Ext.MessageBox.WARNING,
					}); //
					mod.admision.nuevo.win.close();
					mod.admision.pacientes.win.close();
					mod.admision.registro.adm_pac.clearValue();
					mod.admision.registro.adm_pac.focus();
					mod.admision.registro.edad.setValue(dato.data.edad);
					mod.admision.registro.sexo.setValue(dato.data.sexo);
					mod.admision.registro.cell.setValue(dato.data.cell);
					mod.admision.registro.adm_pac.setValue(dato.pac_ndoc);
					mod.admision.registro.adm_pac.setRawValue(dato.data.todo);
				}
			},
			failure: function (form, action) {
				switch (action.failureType) {
					case Ext.form.Action.CLIENT_INVALID:
						Ext.Msg.alert("Failure", "Existen valores Invalidos");
						break;
					case Ext.form.Action.CONNECT_FAILURE:
						Ext.Msg.alert("Failure", "Error de comunicacion con servidor");
						break;
					case Ext.form.Action.SERVER_INVALID:
						Ext.Msg.alert("Failure", action.result.error);
						break;
					default:
						Ext.Msg.alert("Failure", action.result.error);
				}
			},
		});
		//     Ext.MessageBox.alert('Alerta', 'Seleccione la parte Careada');
	},
	verifica_dni2: function () {
		Ext.Ajax.request({
			url: "<[controller]>",
			params: {
				acction: "st_busca_dni",
				format: "json",
				pac_ndoc:
					mod.admision.nuevo.pac_ndoc.getValue() +
					mod.admision.nuevo.pac_ndoc_ext.getValue(),
			},
			success: function (response, opts) {
				var dato = Ext.decode(response.responseText);
				Ext.MessageBox.alert(
					"LiveCode",
					dato.data.pac_ndoc.length + " - - " + dato.data.pac_ndoc
				);
				if (dato.data == null) {
					if (mod.admision.nuevo.pac_ndoc.getValue().length == 8) {
						mod.admision.nuevo.win.el.mask(
							"Buscando datos en la RENIEC…",
							"x-mask-loading"
						);
						Ext.Ajax.request({
							waitMsg: "Recuperando Informacion...",
							waitTitle: "Espere",
							url: "system/reniec/ejemplo.php",
							dataType: "json",
							params: {
								//                            acction: 'st_buca_ruc',
								format: "json",
								dni:
									mod.admision.nuevo.pac_ndoc.getValue() +
									mod.admision.nuevo.pac_ndoc_ext.getValue(),
							},
							success: function (resp) {
								mod.admision.nuevo.win.el.mask("Guardando…", "x-mask-loading");
								var obj = Ext.decode(resp.responseText);
								mod.admision.nuevo.pac_ndoc.setValue(obj.DNI);
								mod.admision.nuevo.pac_appat.setValue(obj.ape_pat);
								mod.admision.nuevo.pac_apmat.setValue(obj.ape_mat);
								mod.admision.nuevo.pac_nombres.setValue(obj.nom);
								mod.admision.nuevo.win.el.unmask();
								//                            Ext.MessageBox.alert('LiveCode', dato.data.pac_ndoc.length);
							},
							failure: function (form, action) {
								switch (action.failureType) {
									case Ext.form.Action.CLIENT_INVALID:
										Ext.Msg.alert("Failure", "Existen valores Invalidos");
										break;
									case Ext.form.Action.CONNECT_FAILURE:
										Ext.Msg.alert(
											"Failure",
											"Error de comunicacion con servidor"
										);
										break;
									case Ext.form.Action.SERVER_INVALID:
										Ext.Msg.alert("Failure", action.result.error);
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
							},
						});
					}
				} else {
					Ext.MessageBox.show({
						title: "WARNING",
						msg: "Paciente ya Tiene Historia Clinica",
						buttons: Ext.MessageBox.OK,
						animEl: "mb9",
						icon: Ext.MessageBox.WARNING,
					}); //
					mod.admision.nuevo.win.close();
					mod.admision.registro.adm_pac.clearValue();
					mod.admision.registro.adm_pac.focus();
					mod.admision.registro.edad.setValue(dato.data.edad);
					mod.admision.registro.sexo.setValue(dato.data.sexo);
					mod.admision.registro.cell.setValue(dato.data.cell);
					mod.admision.registro.adm_pac.setValue(dato.pac_ndoc);
					mod.admision.registro.adm_pac.setRawValue(dato.data.todo);
					mod.admision.pacientes.win.close();
				}
			},
			failure: function (form, action) {
				switch (action.failureType) {
					case Ext.form.Action.CLIENT_INVALID:
						Ext.Msg.alert("Failure", "Existen valores Invalidos");
						break;
					case Ext.form.Action.CONNECT_FAILURE:
						Ext.Msg.alert("Failure", "Error de comunicacion con servidor");
						break;
					case Ext.form.Action.SERVER_INVALID:
						Ext.Msg.alert("Failure", action.result.error);
						break;
					default:
						Ext.Msg.alert("Failure", action.result.error);
				}
			},
		});
		//     Ext.MessageBox.alert('Alerta', 'Seleccione la parte Careada');
	},
	cargar_data: function () {
		Ext.Ajax.request({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			url: "<[controller]>",
			params: {
				acction: "load_data_pac",
				format: "json",
				pac_id: mod.admision.nuevo.record.get("pac_id"),
			},
			success: function (response, opts) {
				var dato = Ext.decode(response.responseText);
				if (dato.success == true) {
					mod.admision.nuevo.frm.getForm().loadRecord(dato);
					mod.admision.nuevo.pac_giid.setValue(dato.data.pac_giid);
					mod.admision.nuevo.pac_giid.setRawValue(dato.data.gi_desc);

					mod.admision.nuevo.pac_tdoc.setValue(dato.data.pac_tdocid);
					mod.admision.nuevo.pac_tdoc.setRawValue(dato.data.tdoc_desc);
					if (dato.data.pac_tdocid > 0) {
						mod.admision.nuevo.pac_ndoc_ext.setValue(dato.data.pac_ndoc);
						mod.admision.nuevo.pac_ndoc_ext.enable();
						mod.admision.nuevo.pac_ndoc.disable();
						mod.admision.nuevo.pac_ndoc.setValue("");
					}

					mod.admision.nuevo.pac_depa.setValue(dato.data.dep_id);
					mod.admision.nuevo.pac_depa.setRawValue(dato.data.dep_desc);

					mod.admision.nuevo.pac_prov.setValue(dato.data.prov_id);
					mod.admision.nuevo.pac_prov.setRawValue(dato.data.prov_desc);

					mod.admision.nuevo.pac_domdisid.setValue(dato.data.pac_domdisid);
					mod.admision.nuevo.pac_domdisid.setRawValue(dato.data.dis_desc);
					mod.admision.nuevo.distrito.load();
				}
			},
		});
	},
	crea_stores: function () {
		this.tdocumento = new Ext.data.JsonStore({
			root: "data",
			url: "<[controller]>",
			baseParams: {
				acction: "tdocumento",
				format: "json",
			},
			fields: ["tdoc_id", "tdoc_desc"],
		});
		this.departamento = new Ext.data.JsonStore({
			root: "data",
			url: "<[controller]>",
			baseParams: {
				acction: "departamento",
				format: "json",
			},
			fields: ["dep_id", "dep_desc"],
		});
		this.provincia = new Ext.data.JsonStore({
			root: "data",
			url: "<[controller]>",
			baseParams: {
				acction: "provincia",
				format: "json",
			},
			fields: ["prov_id", "prov_depid", "prov_desc"],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.dep_id = mod.admision.nuevo.pac_depa.getValue();
				},
			},
		});
		this.distrito = new Ext.data.JsonStore({
			root: "data",
			url: "<[controller]>",
			baseParams: {
				acction: "distrito",
				format: "json",
			},
			fields: ["dis_id", "dis_prov", "dis_desc"],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.prov_id = mod.admision.nuevo.pac_prov.getValue();
				},
			},
		});
		this.list_ginstruccion = new Ext.data.JsonStore({
			root: "data",
			url: "<[controller]>",
			baseParams: {
				acction: "list_ginstruccion",
				format: "json",
			},
			fields: ["gi_id", "gi_desc"],
		});
		this.list_profecion = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_profecion",
				format: "json",
			},
			fields: ["pac_profe"],
			root: "data",
		});
		this.st_busca_nombres = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_nombres",
				format: "json",
			},
			fields: ["pac_nombres"],
			root: "data",
		});
		this.st_busca_appaterno = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_appaterno",
				format: "json",
			},
			fields: ["pac_appat"],
			root: "data",
		});
		this.st_busca_apmaterno = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_apmaterno",
				format: "json",
			},
			fields: ["pac_apmat"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.pac_tdoc = new Ext.form.ComboBox({
			store: this.tdocumento,
			hiddenName: "pac_tdoc",
			editable: false,
			displayField: "tdoc_desc",
			valueField: "tdoc_id",
			allowBlank: false,
			blankText: "Tipo de documento no puede ser Vacio",
			typeAhead: true,
			triggerAction: "all",
			fieldLabel: "<b>Documento</b>",
			mode: "remote",
			anchor: "95%",
			listeners: {
				select: function (combo, registro, indice) {
					if (registro.get("tdoc_id") == 0) {
						mod.admision.nuevo.pac_ndoc.enable();
						mod.admision.nuevo.pac_ndoc_ext.disable();
						mod.admision.nuevo.pac_ndoc_ext.setValue("");
						mod.admision.nuevo.pac_ndoc.setValue("");
					} else if (registro.get("tdoc_id") > 0) {
						mod.admision.nuevo.pac_ndoc.disable();
						mod.admision.nuevo.pac_ndoc_ext.enable();
						mod.admision.nuevo.pac_ndoc_ext.setValue("");
						mod.admision.nuevo.pac_ndoc.setValue("");
					}
				},
				afterrender: function (combo) {
					combo.setValue(0); // El ID de la opción por defecto setRawValue
					combo.setRawValue("DNI"); // El ID de la opción por defecto setRawValue
					mod.admision.nuevo.pac_ndoc.enable();
				},
			},
			emptyText: "Tipo de documento...",
		});
		this.pac_ndoc = new Ext.form.TextField({
			fieldLabel: "<b>Número del DNI</b>",
			name: "pac_ndoc",
			maskRe: /[\d]/,
			minLength: 8,
			autoCreate: {
				tag: "input",
				maxlength: 8,
				minLength: 8,
				type: "text",
				size: "8",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			readOnly: this.record !== null ? true : false,
			listeners: {
				change: function (combo, registro, indice) {
					mod.admision.nuevo.verifica_dni();
				},
				select: function (combo, registro, indice) {
					mod.admision.nuevo.verifica_dni();
				},
				specialkey: function (f, e) {
					if (e.getKey() == e.TAB) {
						mod.admision.nuevo.verifica_dni();
					} else if (e.getKey() == e.ENTER) {
						mod.admision.nuevo.verifica_dni2();
					}
				},
			},
			anchor: "90%",
		});
		this.pac_ndoc_ext = new Ext.form.TextField({
			fieldLabel: "<b>Número del Documento</b>",
			name: "pac_ndoc_ext",
			//maskRe: /[\d]/,
			minLength: 8,
			autoCreate: {
				tag: "input",
				maxlength: 14,
				minLength: 8,
				type: "text",
				size: "8",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			readOnly: this.record !== null ? true : false,
			listeners: {
				change: function (combo, registro, indice) {
					mod.admision.nuevo.verifica_dni();
					//                    Ext.MessageBox.alert('ALERTA', 'change');
				},
				select: function (combo, registro, indice) {
					mod.admision.nuevo.verifica_dni();
					//                    Ext.MessageBox.alert('ALERTA', 'select');
				},
				specialkey: function (f, e) {
					if (e.getKey() == e.TAB) {
						//                        Ext.MessageBox.alert('ALERTA', 'TAB');
						mod.admision.nuevo.verifica_dni();
					} else if (e.getKey() == e.ENTER) {
						//                        Ext.MessageBox.alert('ALERTA', 'ENTER');
						mod.admision.nuevo.verifica_dni();
					}
				},
			},
			anchor: "90%",
		});
		this.pac_correo = new Ext.form.TextField({
			fieldLabel: "<B>Correo Electronico</B>",
			vtype: "email",
			name: "pac_correo",
			//            allowBlank: false,
			anchor: "90%",
		});
		this.pac_appat = new Ext.form.ComboBox({
			store: this.st_busca_appaterno,
			hiddenName: "pac_appat",
			displayField: "pac_appat",
			valueField: "pac_appat",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			allowBlank: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>Apellido Paterno</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			style: {
				textTransform: "uppercase",
			},
			emptyText: "Apellido Paterno...",
			listeners: {
				select: function () {
					//                    mod.adm.nuevo.llena_datos(mod.adm.nuevo.txt_nombre.getValue());
				},
			},
			anchor: "88%",
		});
		this.pac_apmat = new Ext.form.ComboBox({
			store: this.st_busca_apmaterno,
			hiddenName: "pac_apmat",
			displayField: "pac_apmat",
			valueField: "pac_apmat",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			allowBlank: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>Apellido Materno</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			style: {
				textTransform: "uppercase",
			},
			emptyText: "Apellido Materno...",
			listeners: {
				select: function () {
					//                    mod.adm.nuevo.llena_datos(mod.adm.nuevo.txt_nombre.getValue());
				},
			},
			anchor: "88%",
		});
		this.pac_nombres = new Ext.form.ComboBox({
			store: this.st_busca_nombres,
			hiddenName: "pac_nombres",
			displayField: "pac_nombres",
			valueField: "pac_nombres",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			allowBlank: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>Nombres</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			style: {
				textTransform: "uppercase",
			},
			emptyText: "Nombre...",
			listeners: {
				select: function () {
					//                    mod.adm.nuevo.llena_datos(mod.adm.nuevo.txt_nombre.getValue());
				},
			},
			anchor: "88%",
		});
		this.pac_sexo = new Ext.form.RadioGroup({
			fieldLabel: "<b>Sexo</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{
					boxLabel: "Masculino",
					checked: true,
					name: "pac_sexo",
					inputValue: "M",
				},
				{ boxLabel: "Femenino", name: "pac_sexo", inputValue: "F" },
			],
		});
		this.pac_ecid = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["pac_ecid", "ec_desc"],
				data: [
					["1", "SOLTERO(A)"],
					["2", "CASADO(A)"],
					["3", "VIUDO(A)"],
					["4", "DIVORCIADO(A)"],
					["5", "CONVIVIENTE"],
				],
			}),
			hiddenName: "pac_ecid",
			fieldLabel: "<b>Estado Civil</b>",
			displayField: "ec_desc",
			valueField: "pac_ecid",
			typeAhead: true,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Estado Civil...",
			anchor: "90%",
			selectOnFocus: true,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue(1);
					descripcion.setRawValue("SOLTERO(A)");
				},
			},
		});
		this.pac_giid = new Ext.form.ComboBox({
			store: this.list_ginstruccion,
			hiddenName: "pac_giid",
			displayField: "gi_desc",
			valueField: "gi_id",
			editable: false,
			allowBlank: false,
			typeAhead: true,
			triggerAction: "all",
			fieldLabel: "<b>Grado de Instruccion</b>",
			mode: "local",
			anchor: "85%",
			emptyText: "Grado de instrucción...",
		});
		this.pac_fech_nac = new Ext.form.DateField({
			fieldLabel: "<b>Fecha de Nacimiento</b>",
			allowBlank: false,
			name: "pac_fech_nac",
			format: "d-m-Y",
			anchor: "90%",
			emptyText: "Dia-Mes-Año",
			listeners: {
				change: function (datefield, registro, indice) {
					var nace = datefield.getValue();
					var dia = nace.getDate;
					var mes = nace.getMonth();
					var ano = nace.getFullYear();

					var fecha_hoy = new Date();
					var ahora_dia = fecha_hoy.getDate;
					var ahora_mes = fecha_hoy.getMonth();
					var ahora_ano = fecha_hoy.getFullYear();

					var edad = ahora_ano + 1900 - ano;
					if (ahora_mes < mes) {
						edad--;
					}
					if (mes == ahora_mes && ahora_dia < dia) {
						edad--;
					}
					if (edad > 1900) {
						edad -= 1900;
					}

					mod.admision.nuevo.edad.setValue(edad + " Años");
				},
				select: function (datefield, registro, indice) {
					var nace = datefield.getValue();
					var dia = nace.getDate;
					var mes = nace.getMonth();
					var ano = nace.getFullYear();

					var fecha_hoy = new Date();
					var ahora_dia = fecha_hoy.getDate;
					var ahora_mes = fecha_hoy.getMonth();
					var ahora_ano = fecha_hoy.getFullYear();

					var edad = ahora_ano + 1900 - ano;
					if (ahora_mes < mes) {
						edad--;
					}
					if (mes == ahora_mes && ahora_dia < dia) {
						edad--;
					}
					if (edad > 1900) {
						edad -= 1900;
					}
					mod.admision.nuevo.edad.setValue(edad + " Años");
				},
				specialkey: function (f, e) {
					if (e.getKey() == e.TAB) {
						var nace = mod.admision.nuevo.pac_fech_nac.getValue();
						var dia = nace.getDate;
						var mes = nace.getMonth();
						var ano = nace.getFullYear();

						var fecha_hoy = new Date();
						var ahora_dia = fecha_hoy.getDate;
						var ahora_mes = fecha_hoy.getMonth();
						var ahora_ano = fecha_hoy.getFullYear();

						var edad = ahora_ano + 1900 - ano;
						if (ahora_mes < mes) {
							edad--;
						}
						if (mes == ahora_mes && ahora_dia < dia) {
							edad--;
						}
						if (edad > 1900) {
							edad -= 1900;
						}

						mod.admision.nuevo.edad.setValue(edad + " Años");
					} else if (e.getKey() == e.ENTER) {
						var nace = mod.admision.nuevo.pac_fech_nac.getValue();
						var dia = nace.getDate;
						var mes = nace.getMonth();
						var ano = nace.getFullYear();

						var fecha_hoy = new Date();
						var ahora_dia = fecha_hoy.getDate;
						var ahora_mes = fecha_hoy.getMonth();
						var ahora_ano = fecha_hoy.getFullYear();

						var edad = ahora_ano + 1900 - ano;
						if (ahora_mes < mes) {
							edad--;
						}
						if (mes == ahora_mes && ahora_dia < dia) {
							edad--;
						}
						if (edad > 1900) {
							edad -= 1900;
						}

						//                        if (edad > 2 && edad <= 11) {
						mod.admision.nuevo.edad.setValue(edad + " Años");
						//                        } else {
						//                            mod.admision.nuevo.win.close();
						//                            Ext.MessageBox.alert('La edad excede', 'La edad excede en : ' + edad + ' Años.');
						//                        }
					}
				},
			},
		});
		this.edad = new Ext.form.TextField({
			fieldLabel: "<b>Edad</b>",
			name: "edad",
			readOnly: true,
			anchor: "90%",
		});
		this.pac_profe = new Ext.form.ComboBox({
			store: this.list_profecion,
			hiddenName: "pac_profe",
			displayField: "pac_profe",
			id: "pac_profe",
			allowBlank: false,
			name: "pac_profe",
			valueField: "pac_profe",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>Profesión</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			emptyText: "¿Cual es su Profesión...?",
			anchor: "90%",
		});
		this.pac_tip_tel = new Ext.form.ComboBox({
			fieldLabel: "<b>TIPO DE TELEFONO</b>",
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1", "CASA"],
					["2", "MOVIL"],
					["3", "TRABAJO"],
					["4", "EMERGENCIA"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			editable: false,
			hiddenName: "pac_tip_tel",
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue(2);
					descripcion.setRawValue("MOVIL");
				},
			},
		});
		this.pac_cel = new Ext.form.TextField({
			fieldLabel: "<b>Celular</b>",
			name: "pac_cel",
			anchor: "90%",
			maskRe: /[\d]/,
			minLength: 9,
			autoCreate: {
				tag: "input",
				maxlength: 9,
				minLength: 6,
				type: "text",
				size: "9",
				autocomplete: "off",
			},
		});
		this.pac_ubigeo = new Ext.form.TextField({
			fieldLabel: "<b>Ubigeo Nacimiento</b>",
			name: "pac_ubigeo",
			maskRe: /[\d]/,
			minLength: 6,
			autoCreate: {
				tag: "input",
				maxlength: 6,
				minLength: 6,
				type: "text",
				size: "6",
				autocomplete: "off",
			},
			allowBlank: false,
			anchor: "85%",
		});
		this.pac_depa = new Ext.form.ComboBox({
			store: this.departamento,
			hiddenName: "pac_depa",
			displayField: "dep_desc",
			valueField: "dep_id",
			minChars: 1,
			fieldLabel: "<b>Departamento</b>",
			editable: false,
			allowBlank: false,
			typeAhead: true,
			triggerAction: "all",
			mode: "local",
			style: {
				textTransform: "uppercase",
			},
			listeners: {
				scope: this,
				select: function (combo, registro, indice) {
					this.pac_prov.clearValue();
					this.pac_domdisid.clearValue();
					this.pac_prov.focus();
					this.provincia.load();
				},
				afterrender: function (combo) {
					combo.setValue("07"); // El ID de la opción por defecto setRawValue
					combo.setRawValue("CUSCO"); // El ID de la opción por defecto setRawValue
					this.provincia.load();
				},
			},
			anchor: "90%",
			emptyText: "Departamento...",
		});
		this.pac_prov = new Ext.form.ComboBox({
			store: this.provincia,
			hiddenName: "pac_prov",
			displayField: "prov_desc",
			valueField: "prov_id",
			minChars: 1,
			fieldLabel: "<b>Provincia</b>",
			editable: false,
			allowBlank: false,
			typeAhead: true,
			triggerAction: "all",
			mode: "local",
			style: {
				textTransform: "uppercase",
			},
			listeners: {
				scope: this,
				select: function (combo, registro, indice) {
					this.pac_domdisid.clearValue();
					this.pac_domdisid.focus();
					this.distrito.load();
				},
			},
			anchor: "90%",
		});
		this.pac_domdisid = new Ext.form.ComboBox({
			store: this.distrito,
			hiddenName: "pac_domdisid",
			displayField: "dis_desc",
			valueField: "dis_id",
			minChars: 1,
			fieldLabel: "<b>Distrito</b>",
			editable: false,
			allowBlank: false,
			typeAhead: true,
			triggerAction: "all",
			mode: "local",
			style: {
				textTransform: "uppercase",
			},
			anchor: "90%",
		});
		this.pac_domdir = new Ext.form.TextField({
			fieldLabel: "<B>DIRECCION</B>",
			name: "pac_domdir",
			//            allowBlank: false,
			anchor: "97%",
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			labelAlign: "top",
			items: [
				{
					columnWidth: 0.25,
					border: false,
					layout: "form",
					items: [this.pac_tdoc],
				},
				{
					columnWidth: 0.2,
					border: false,
					layout: "form",
					items: [this.pac_ndoc],
				},
				{
					columnWidth: 0.25,
					border: false,
					layout: "form",
					items: [this.pac_ndoc_ext],
				},
				{
					columnWidth: 0.3,
					border: false,
					layout: "form",
					items: [this.pac_correo],
				},
				{
					columnWidth: 0.15,
					border: false,
					layout: "form",
					items: [this.pac_sexo],
				},
				{
					columnWidth: 0.28,
					border: false,
					layout: "form",
					items: [this.pac_appat],
				},
				{
					columnWidth: 0.28,
					border: false,
					layout: "form",
					items: [this.pac_apmat],
				},
				{
					columnWidth: 0.28,
					border: false,
					layout: "form",
					items: [this.pac_nombres],
				},
				{
					columnWidth: 0.2,
					border: false,
					layout: "form",
					items: [this.pac_ecid],
				},
				{
					columnWidth: 0.3,
					border: false,
					layout: "form",
					items: [this.pac_giid],
				},
				{
					columnWidth: 0.2,
					border: false,
					layout: "form",
					items: [this.pac_fech_nac],
				},
				{
					columnWidth: 0.15,
					border: false,
					layout: "form",
					items: [this.edad],
				},
				{
					columnWidth: 0.35,
					border: false,
					layout: "form",
					items: [this.pac_profe],
				},
				{
					columnWidth: 0.3,
					border: false,
					layout: "form",
					items: [this.pac_tip_tel],
				},
				{
					columnWidth: 0.2,
					border: false,
					layout: "form",
					items: [this.pac_cel],
				},
				{
					columnWidth: 0.15,
					border: false,
					layout: "form",
					items: [this.pac_ubigeo], //
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.999,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<B>UBICACION ACTUAL DONDE VIVE</B>",
							items: [
								{
									columnWidth: 0.33,
									border: false,
									layout: "form",
									items: [this.pac_depa],
								},
								{
									columnWidth: 0.33,
									border: false,
									layout: "form",
									items: [this.pac_prov],
								},
								{
									columnWidth: 0.33,
									border: false,
									layout: "form",
									items: [this.pac_domdisid],
								},
								{
									columnWidth: 0.98,
									border: false,
									layout: "form",
									items: [this.pac_domdir],
								},
							],
						},
					],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.admision.nuevo.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction: this.record !== null ? "update_pac" : "save_pac",
								pac_id: this.record !== null ? this.record.get("pac_id") : "",
								pac_domdisid2: mod.admision.registro.adm_pac.getRawValue(),
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								Ext.MessageBox.alert(
									"En hora buena",
									"El paciente se registro correctamente"
								);
								mod.admision.registro.adm_pac.setValue(obj.data);
								mod.admision.registro.adm_pac.setRawValue(obj.nombres);
								mod.admision.registro.edad.setValue(
									mod.admision.nuevo.edad.getValue()
								);
								mod.admision.registro.sexo.setValue(obj.sexo);
								mod.admision.registro.cell.setValue(
									mod.admision.nuevo.pac_cel.getValue()
								);
								//                                mod.admision.registro.adm_examen.enable();
								//                                mod.admision.registro.list_servis.reload();
								mod.admision.nuevo.win.el.unmask();
								mod.admision.nuevo.win.close();
								mod.admision.pacientes.win.close();
							},
							failure: function (form, action) {
								mod.admision.nuevo.win.el.unmask();
								switch (action.failureType) {
									case Ext.form.Action.CLIENT_INVALID:
										Ext.Msg.alert("Failure", "Existen valores Invalidos");
										mod.admision.nuevo.win.close();
										break;
									case Ext.form.Action.CONNECT_FAILURE:
										Ext.Msg.alert(
											"Failure",
											"Error de comunicacion con servidor"
										);
										mod.admision.nuevo.win.close();
										break;
									case Ext.form.Action.SERVER_INVALID:
										Ext.Msg.alert("Failure mik", action.result.error);
										mod.admision.nuevo.win.close();
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
										mod.admision.nuevo.win.close();
								}
							},
						});
					},
				},
			],
			//
		});
		this.win = new Ext.Window({
			width: 900,
			height: 430,
			modal: true,
			title: "ADMISIÓN",
			border: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			//            closeAction: 'hide',
			//            plain: true,
			items: [this.frm],
		});
	},
};
mod.admision.empresa = {
	win: null,
	frm: null,
	init: function () {
		this.crea_stores();
		this.crea_controles();
		this.st.load();
		this.win.show();
	},
	cargar_data: function () {},
	crea_stores: function () {
		this.st = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_emp",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"emp_id",
				"emp_usu",
				"emp_fech",
				"emp_desc",
				"emp_acro",
				"emp_telf",
				"emp_estado",
				"emp_direc",
			],
		});
	},
	crea_controles: function () {
		this.paginador = new Ext.PagingToolbar({
			pageSize: 50,
			store: this.st,
			displayInfo: true,
			displayMsg: "Mostrando {0} - {1} de {2} Empresas",
			emptyMsg: "No Existe Registros",
			plugins: new Ext.ux.ProgressBarPager(),
		});
		this.buscador = new Ext.ux.form.SearchField({
			width: 250,
			fieldLabel: "Nombre",
			store: this.st,
			id: "search_query",
			emptyText: "Buscar por RUC o Nombre de la Empresa",
		});
		this.tbar = new Ext.Toolbar({
			items: ["Buscar:", this.buscador],
		});
		this.dt_grid = new Ext.grid.GridPanel({
			store: this.st,
			region: "west",
			border: false,
			tbar: this.tbar,
			bbar: this.paginador,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			height: 462,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var record = grid.getStore().getAt(rowIndex);
					mod.admision.registro.adm_emp.setValue(record.get("emp_id"));
					mod.admision.registro.adm_emp.setRawValue(
						record.get("emp_id") + " - " + record.get("emp_desc")
					);
					mod.admision.empresa.win.close();
				},
			},
			autoExpandColumn: "emp_desc",
			columns: [
				{
					header: "RUC",
					width: 80,
					sortable: true,
					dataIndex: "emp_id",
				},
				{
					id: "emp_desc",
					header: "RAZON SOCIAL",
					dataIndex: "emp_desc",
				},
				{
					header: "NOMBRE COMERCIAL",
					dataIndex: "emp_acro",
					width: 250,
				},
				{
					header: "TELEFONO",
					dataIndex: "emp_telf",
					width: 200,
				},
				{
					header: "FECHA DE REGISTRO",
					dataIndex: "emp_fech",
					width: 130,
				},
			],
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			layout: "column",
			items: [
				{
					columnWidth: 0.999,
					border: false,
					layout: "form",
					items: [this.dt_grid],
				},
			],
		});
		this.win = new Ext.Window({
			width: 900,
			height: 500,
			modal: true,
			title: "LISTA DE EMPRESAS",
			border: false,
			collapsible: true,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
mod.admision.report = {
	win: null,
	init: function (adm_id) {
		this.crea_controles(adm_id);
		this.win.show();
	},
	crea_controles: function (adm_id) {
		var params = "adm=" + adm_id;
		this.win = new Ext.Window({
			title: "HOJA DE FILIACION N° " + adm_id,
			width: 800,
			height: 600,
			maximizable: true,
			modal: true,
			closeAction: "close",
			resizable: true,
			html:
				"<iframe width='100%' height='100%' src='system/os-load.php?sys_acction=sys_loadreport&sys_modname=mod_admision&sys_report=reporte&" +
				params +
				"'></iframe>",
		});
	},
};
Ext.onReady(mod.admision.init, mod.admision);
