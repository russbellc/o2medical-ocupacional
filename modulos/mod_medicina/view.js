Ext.apply(Ext.form.VTypes, {
	daterange: function (val, field) {
		var date = field.parseDate(val);
		if (!date) {
			return false;
		}
		if (field.startDateField) {
			var start = Ext.getCmp(field.startDateField);
			if (!start.maxValue || date.getTime() != start.maxValue.getTime()) {
				start.setMaxValue(date);
				start.validate();
			}
		} else if (field.endDateField) {
			var end = Ext.getCmp(field.endDateField);
			if (!end.minValue || date.getTime() != end.minValue.getTime()) {
				end.setMinValue(date);
				end.validate();
			}
		}
		return true;
	},
});

Ext.ns("mod.medicina");
mod.medicina = {
	dt_grid: null,
	paginador: null,
	tbar: null,
	st: null,
	init: function () {
		this.crea_store();
		this.crea_controles();
		this.dt_grid.render("<[view]>");
		this.st.load();
	},
	crea_store: function () {
		this.st = new Ext.data.JsonStore({
			remoteSort: true,
			url: "<[controller]>",
			baseParams: {
				acction: "list_paciente",
				format: "json",
			},
			listeners: {
				beforeload: function () {
					this.baseParams.columna = mod.medicina.descripcion.getValue();
				},
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"adm",
				"adm_foto",
				"st",
				"tfi_desc",
				"emp_desc",
				"pac_ndoc",
				"pac_ndoc",
				"edad",
				"puesto",
				"tfi_desc",
				"nombre",
				"ape",
				"nom",
				"pac_sexo",
				"fecha",
				"adm_aptitud",
				"nro_examenes",
			],
		});
	},
	crea_controles: function () {
		this.paginador = new Ext.PagingToolbar({
			pageSize: 30,
			store: this.st,
			displayInfo: true,
			displayMsg: "Mostrando {0} - {1} de {2} Empleados",
			emptyMsg: "No Existe Registros",
			plugins: new Ext.ux.ProgressBarPager(),
		});
		this.buscador = new Ext.ux.form.SearchField({
			width: 250,
			fieldLabel: "Nombre",
			id: "search_query",
			emptyText: "Ingrese dato a buscar...",
			store: this.st,
		});
		this.descripcion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1", "Nro Filiacion"],
					["2", "DNI"],
					["3", "Apellidos y Nombres"],
					["4", "Empresa o RUC"],
					["5", "Tipo de Ficha"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			typeAhead: false,
			editable: false,
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
				"Buscar:",
				this.descripcion,
				this.buscador,
				"->",
				"|",
				{
					text: "Reporte x Fecha",
					iconCls: "reporte",
					handler: function () {
						mod.medicina.rfecha.init(null);
					},
				},
				"|",
			],
		});
		this.dt_grid = new Ext.grid.GridPanel({
			store: this.st,
			tbar: this.tbar,
			loadMask: true,
			height: 500,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 95,
			}),
			bbar: this.paginador,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var record = grid.getStore().getAt(rowIndex);
					var admi = record.get("adm");
					if (record.get("st") >= 1) {
						console.log(record);
						mod.medicina.formatos.init(record);
					} else {
						console.log(admi);
						mod.medicina.formatos.init(record);
					}
				},
				rowcontextmenu: function (grid, index, event) {
					event.stopEvent();
					var record = grid.getStore().getAt(index);
					if (record.get("st") == 1) {
						new Ext.menu.Menu({
							items: [
								{
									text: "HOJA RESUMEN N°: <B>" + record.get("adm") + "<B>",
									iconCls: "reporte",
									handler: function () {
										if (record.get("st") >= 1) {
											new Ext.Window({
												title: "HOJA RESUMEN N° " + record.get("adm"),
												width: 800,
												height: 600,
												maximizable: true,
												modal: true,
												closeAction: "close",
												resizable: true,
												html:
													"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=formato_resumen&adm=" +
													record.get("adm") +
													"'></iframe>",
											}).show();
										} else {
											Ext.MessageBox.alert(
												"Observaciones",
												"El paciente no fue registrado correctamente"
											);
										}
									},
								},
							],
						}).showAt(event.xy);
					}
				},
			},
			autoExpandColumn: "aud_emp",
			columns: [
				{
					header: "ST",
					width: 25,
					sortable: true,
					dataIndex: "st",
					renderer: function renderIcon(val) {
						if (val == 0) {
							return '<img src="<[images]>/nuevo.png" title="REGISTRAR" height="15">';
						} else if (val == 1) {
							return '<img src="<[images]>/saveIcon.png" title="GUARDADO" height="15">';
						}
					},
				},
				{
					header: "N° FICHA",
					width: 60,
					sortable: true,
					dataIndex: "adm",
				},
				{
					header: "Número DNI",
					dataIndex: "pac_ndoc",
					width: 80,
				},
				{
					header: "NOMBRE",
					width: 240,
					dataIndex: "nombre",
				},
				{
					header: "Edad",
					width: 35,
					id: "edad",
					dataIndex: "edad",
				},
				{
					header: "SEXO",
					width: 40,
					sortable: true,
					dataIndex: "pac_sexo",
					renderer: function renderIcon(val) {
						if (val == "M") {
							return '<center><img src="<[images]>/male.png" title="Masculino" height="15"></center>';
						} else if (val == "F") {
							return '<center><img src="<[images]>/fema.png" title="Femenino" height="15"></center>';
						}
					},
				},
				{
					id: "aud_emp",
					header: "EMPRESA",
					dataIndex: "emp_desc",
					width: 250,
				},
				{
					id: "tfi_desc",
					header: "TIPO DE FICHA",
					dataIndex: "tfi_desc",
					width: 125,
				},
				{
					header: "APTITUD",
					dataIndex: "adm_aptitud",
					align: "center",
					width: 151,
					renderer: function (val, meta, record) {
						if (val == "APTO") {
							meta.css = "stkGreen";
						} else if (val == "APTO CON OBSERVACIONES") {
							meta.css = "stkYellow";
						} else if (val == "APTO CON RESTRICCIÓN") {
							meta.css = "stkYellow";
							return val;
						} else if (val == "NO APTO TEMPORAL") {
							meta.css = "stkRed";
						} else if (val == "NO APTO DEFINITIVO") {
							meta.css = "stkRed";
						} else if (val == "EN PROCESO DE VALIDACION") {
							meta.css = "stkBlak";
						} else if (val == "NO APTO TEMPORAL") {
							meta.css = "stkblue";
						} else {
							return "<b><center><h3>N/R</h3></center></b>";
						}
						return "<b><center><h3>" + val + "</h3></center></b>";
					},
				},
				{
					header: "FECHA DE ADMISIÓN",
					dataIndex: "fecha",
					width: 165,
				},
			],
			viewConfig: {
				getRowClass: function (record, index) {
					var st = record.get("st");
					if (st == "0") {
						return "child-row";
					} else if (st == "1") {
						return "child-blue";
					}
				},
			},
		});
	},
};
mod.medicina.rfecha = {
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
			url: "<[controller]>",
			baseParams: {
				acction: "list_empre",
				format: "json",
			},
			fields: ["emp_id", "emp_desc", "emp_acro"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.resultTpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"{emp_id}",
			"<h3><span>{emp_acro}</span><br />{emp_desc}</h3>",
			"</div>",
			"</div></tpl>"
		);
		this.cboEmpre = new Ext.form.ComboBox({
			store: this.st_empre,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.resultTpl,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 3,
			hiddenName: "cboEmpre",
			displayField: "emp_desc",
			valueField: "emp_id",
			//            allowBlank: false,
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "Empresa",
			mode: "remote",
			anchor: "100%",
		});
		this.f_inicio = new Ext.form.DateField({
			fieldLabel: "Fecha Inicio",
			format: "Y-m-d",
			id: "startdt",
			vtype: "daterange",
			endDateField: "enddt",
			value: new Date(),
			name: "f_inicio",
			allowBlank: false,
		});
		this.f_final = new Ext.form.DateField({
			fieldLabel: "Fecha Final",
			format: "Y-m-d",
			id: "enddt",
			vtype: "daterange",
			startDateField: "startdt",
			value: new Date(),
			name: "f_final",
			allowBlank: false,
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px 20px 10px 20px;",
			labelWidth: 80,
			items: [
				{
					columnWidth: 0.5,
					border: false,
					layout: "form",
					items: [this.f_inicio],
				},
				{
					columnWidth: 0.5,
					border: false,
					layout: "form",
					items: [this.f_final],
				},
				{ html: "</br>" },
				{
					columnWidth: 0.99,
					border: false,
					layout: "form",
					items: [this.cboEmpre],
				},
			],
			buttons: [
				{
					text: "Reporte Excel",
					iconCls: "excel",
					handler: function () {
						mod.medicina.rfecha.win.el.mask("Guardando…", "x-mask-loading");
						var params =
							"fini=" +
							mod.medicina.rfecha.f_inicio.getRawValue() +
							"&ffinal=" +
							mod.medicina.rfecha.f_final.getRawValue() +
							"&empresa=" +
							mod.medicina.rfecha.cboEmpre.getValue();
						new Ext.Window({
							title: "Referencia",
							width: 200,
							height: 200,
							maximizable: true,
							modal: true,
							closeAction: "close",
							resizable: true,
							html:
								"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadexcel&sys_modname=mod_senso&sys_report=reporte&" +
								params +
								"'></iframe>",
						}).show();
						mod.medicina.rfecha.win.el.unmask();
						mod.medicina.rfecha.win.close();
					},
				},
				"-",
				{
					text: "Reporte PDF",
					iconCls: "reporte",
					handler: function () {
						mod.medicina.rfecha.win.el.mask("Guardando…", "x-mask-loading");
						var params =
							"fini=" +
							mod.medicina.rfecha.f_inicio.getRawValue() +
							"&ffinal=" +
							mod.medicina.rfecha.f_final.getRawValue() +
							"&empresa=" +
							mod.medicina.rfecha.cboEmpre.getValue();
						new Ext.Window({
							title: "Referencia",
							width: 800,
							height: 600,
							maximizable: true,
							modal: true,
							closeAction: "close",
							resizable: true,
							html:
								"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_senso&sys_report=reportefecha&" +
								params +
								"'></iframe>",
						}).show();
						mod.medicina.rfecha.win.el.unmask();
						mod.medicina.rfecha.win.close();
					},
				},
			],
		});
		this.win = new Ext.Window({
			width: 500,
			height: 150,
			modal: true,
			title: "Reporte por Fecha",
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
mod.medicina.formatos = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.crea_controles();
		this.st.load();
		this.win.show();
		if (this.record.get("adm_foto") == 1) {
			mod.medicina.formatos.imgStore.removeAll();
			var store = mod.medicina.formatos.imgStore;
			var record = new store.recordType({
				id: "",
				foto: "<[sys_images]>/fotos/" + this.record.get("adm") + ".png",
			});
			store.add(record);
		}
	},
	crea_stores: function () {
		this.imgStore = new Ext.data.ArrayStore({
			id: 0,
			fields: ["id", "foto"],
			data: [["01", "<[sys_images]>/fotos/foto.png"]],
		});
		this.st = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_formatos",
				format: "json",
			},
			listeners: {
				beforeload: function () {
					this.baseParams.adm = mod.medicina.formatos.record.get("adm");
				},
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"adm",
				"ex_desc",
				"pk_id",
				"ex_id",
				"st",
				"usu",
				"fech",
				"id",
				"pac_sexo",
			],
		});
	},
	crea_controles: function () {
		var tpl = new Ext.XTemplate(
			'<tpl for=".">',
			'<div class="thumb"><img width="196" height="263" src="{foto}" title="{id}"></div>',
			"</tpl>"
		);
		this.odont = new Ext.DataView({
			autoScroll: true,
			id: "dientes_view",
			store: this.imgStore,
			tpl: tpl,
			autoHeight: false,
			height: 290,
			multiSelect: false,
			overClass: "x-view-over",
			itemSelector: "div.thumb-wrap2",
			emptyText: "No hay foto disponible",
		});
		this.paginador = new Ext.PagingToolbar({
			pageSize: 30,
			store: this.st,
			displayInfo: true,
			displayMsg: "Mostrando {0} - {1} de {2} Formatos",
			emptyMsg: "No Existe Registros",
			plugins: new Ext.ux.ProgressBarPager(),
		});
		this.dt_grid = new Ext.grid.GridPanel({
			store: this.st,
			region: "west",
			border: false,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			bbar: this.paginador,
			height: 263,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var record = grid.getStore().getAt(rowIndex);
					if (record.get("ex_id") == 20) {
						//NUEVO EXAMEN DE MEDICINA ANEXO 16
						mod.medicina.nuevoAnexo16.init(record);
					} else if (record.get("ex_id") == 19) {
						//EXAMEN DE MEDICINA ANEXO 312
						mod.medicina.anexo312.init(record);
						mod.medicina.anexo312.llena_conclusiones(record.get("adm"));
					} else if (record.get("ex_id") == 39) {
						//ANTECEDENTES OCUPACIONALES NUEVO ANEXO 16
						mod.medicina.antecedentes16.init(record);
					} else if (record.get("ex_id") == 40) {
						//ANTECEDENTES OCUPACIONALES ANEXO 16
						mod.medicina.antece16_viejo.init(record);
					} else if (record.get("ex_id") == 41) {
						//EXAMEN OSTEO MUSCULAR
						mod.medicina.nuevoOsteo.init(record);
					} else if (record.get("ex_id") == 15) {
						//EXAMEN MUSCULO ESQUELETICO
						mod.medicina.musculo.init(record);
					} else if (record.get("ex_id") == 42) {
						//EXAMEN ANEXO 16A - PERFIL VISITA
						mod.medicina.anexo_16a.init(record);
					} else if (record.get("ex_id") == 60) {
						//EXAMEN MENEJO
						mod.medicina.medicina_manejo.init(record);
					} else {
						mod.medicina.examenPRE.init(record); //
					}
				},
				rowcontextmenu: function (grid, index, event) {
					event.stopEvent();
					var record = grid.getStore().getAt(index);
					if (record.get("st") == "1") {
						if (record.get("ex_id") == 20) {
							new Ext.menu.Menu({
								items: [
									{
										text:
											"Nuevo Anexo 16 Informe N°: <B>" +
											record.get("adm") +
											"<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title:
														"Nuevo Anexo 16 Informe N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=" +
														record.get("adm") +
														"'></iframe>",
												}).show();
											} else {
												Ext.MessageBox.alert(
													"Observaciones",
													"El paciente no fue registrado correctamente"
												);
											}
										},
									},
								],
							}).showAt(event.xy);
						} else if (record.get("ex_id") == 19) {
							new Ext.menu.Menu({
								items: [
									{
										text:
											"Anexo 312 Informe N°: <B>" + record.get("adm") + "<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title: "Anexo 312 Informe N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=formato312&adm=" +
														record.get("adm") +
														"'></iframe>",
												}).show();
											} else {
												Ext.MessageBox.alert(
													"Observaciones",
													"El paciente no fue registrado correctamente"
												);
											}
										},
									},
								],
							}).showAt(event.xy);
						} else if (record.get("ex_id") == 39) {
							new Ext.menu.Menu({
								items: [
									{
										text:
											"Antecedentes Laborales N°: <B>" +
											record.get("adm") +
											"<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title:
														"Antecedentes Laborales N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=antecede_16&adm=" +
														record.get("adm") +
														"'></iframe>",
												}).show();
											} else {
												Ext.MessageBox.alert(
													"Observaciones",
													"El paciente no fue registrado correctamente"
												);
											}
										},
									},
								],
							}).showAt(event.xy);
						} else if (record.get("ex_id") == 40) {
							new Ext.menu.Menu({
								items: [
									{
										text:
											"Antecedentes Laborales N°: <B>" +
											record.get("adm") +
											"<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title:
														"Antecedentes Laborales N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=formato_antecede&adm=" +
														record.get("adm") +
														"'></iframe>",
												}).show();
											} else {
												Ext.MessageBox.alert(
													"Observaciones",
													"El paciente no fue registrado correctamente"
												);
											}
										},
									},
								],
							}).showAt(event.xy);
						} else if (record.get("ex_id") == 41) {
							//formato_osteo_muscular
							new Ext.menu.Menu({
								items: [
									{
										text:
											"EXAMEN OSTEO MUSCULAR N°: <B>" +
											record.get("adm") +
											"<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title:
														"EXAMEN OSTEO MUSCULAR N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=formato_osteo_musc&adm=" +
														record.get("adm") +
														"'></iframe>",
												}).show();
											} else {
												Ext.MessageBox.alert(
													"Observaciones",
													"El paciente no fue registrado correctamente"
												);
											}
										},
									},
								],
							}).showAt(event.xy);
						} else if (record.get("ex_id") == 42) {
							//anexo 16A formato_osteo_musc
							new Ext.menu.Menu({
								items: [
									{
										text: "Anexo 16A N°: <B>" + record.get("adm") + "<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title: "Anexo 16A N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=formato_16a&adm=" +
														record.get("adm") +
														"'></iframe>",
												}).show();
											} else {
												Ext.MessageBox.alert(
													"Observaciones",
													"El paciente no fue registrado correctamente"
												);
											}
										},
									},
								],
							}).showAt(event.xy);
						} else if (record.get("ex_id") == 60) {
							//anexo 16A formato_osteo_musc
							new Ext.menu.Menu({
								items: [
									{
										text:
											"FORMATO PARA MANEJO N°: <B>" + record.get("adm") + "<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title: "FORMATO PARA MANEJO N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=formato_manejo&adm=" +
														record.get("adm") +
														"'></iframe>",
												}).show();
											} else {
												Ext.MessageBox.alert(
													"Observaciones",
													"El paciente no fue registrado correctamente"
												);
											}
										},
									},
								],
							}).showAt(event.xy);
						}
					}
				},
			},
			autoExpandColumn: "cuest_desc",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					header: "X",
					width: 25,
					sortable: true,
					dataIndex: "st",
					renderer: function renderIcon(val) {
						if (val > 0) {
							return '<img src="<[images]>/saveIcon.png" title="GUARDADO" height="14">';
						} else {
							return '<img src="<[images]>/nuevo.png" title="REGISTRAR" height="14">';
						}
					},
				},
				{
					header: "Ex-id",
					dataIndex: "ex_id",
					width: 50,
				},
				{
					id: "cuest_desc",
					header: "EXAMENES",
					dataIndex: "ex_desc",
				},
				//ex_id
				{
					header: "USUARIO",
					dataIndex: "usu",
					width: 70,
				},
				{
					header: "FECHA DE ADMISIÓN",
					dataIndex: "fech",
					width: 140,
				},
			],
			viewConfig: {
				getRowClass: function (record, index) {
					var st = record.get("st");
					if (st == "null") {
						return "child-row";
					} else if (st == "1") {
						return "child-blue";
					}
				},
			},
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			layout: "column",
			items: [
				{
					columnWidth: 0.2,
					border: false,
					layout: "form",
					items: [this.odont],
				},
				{
					columnWidth: 0.25,
					//                    border: false,
					layout: "form",
					items: [
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html: '<div style="color:#267ED7;padding: 5px 0px 2px 5px;font-size: 13px;"><b>APELLIDOS:</b></div>',
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html:
								'<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' +
								this.record.get("ape") +
								"</div>",
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html: '<div style="color:#267ED7;padding: 2px 0px 2px 5px;font-size: 13px;"><b>NOMBRES:</b></div>',
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html:
								'<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' +
								this.record.get("nom") +
								"</div>",
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html: '<div style="color:#267ED7;padding: 2px 0px 2px 5px;font-size: 13px;"><b>NRO DE DOCUMENTO:</b></div>',
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html:
								'<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' +
								this.record.get("pac_ndoc") +
								"</div>",
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html: '<div style="color:#267ED7;padding: 2px 0px 2px 5px;font-size: 13px;"><b>EDAD:</b></div>',
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html:
								'<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' +
								this.record.get("edad") +
								" AÑOS</div>",
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html: '<div style="color:#267ED7;padding: 2px 0px 2px 5px;font-size: 13px;"><b>TIPO DE PERFIL:</b></div>',
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html:
								'<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' +
								this.record.get("tfi_desc") +
								"</div>",
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html: '<div style="color:#267ED7;padding: 2px 0px 2px 5px;font-size: 13px;"><b>ACTIVIDAD LABORAL:</b></div>',
						}),
						new Ext.Panel({
							columnWidth: 0.95,
							border: false,
							html:
								'<div style="padding: 2px 0px 2px 5px;font-size: 13px;">' +
								this.record.get("puesto") +
								"</div>",
						}),
						{ html: "</br>", border: false },
					],
				},
				{
					columnWidth: 0.55,
					border: false,
					layout: "form",
					items: [this.dt_grid],
				},
			],
		});
		this.win = new Ext.Window({
			width: 1000,
			height: 300,
			modal: true,
			title: "EXAMEN DE MÉDICINA: " + this.record.get("nombre"),
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
//
mod.medicina.nuevoAnexo16 = {
	win: null,
	frm: null,
	record: null,
	m_med_contac_nom: null,
	m_med_contac_parent: null,
	m_med_contac_telf: null,
	m_med_puesto_postula: null,
	m_med_area: null,
	m_med_puesto_actual: null,
	m_med_tiempo: null,
	m_med_eq_opera: null,
	m_med_fech_ingreso: null,
	m_med_reubicacion: null,
	m_med_tip_opera: null,
	m_med_minerales: null,
	m_med_altura_lab: null,
	m_med_rl_bio1: null,
	m_med_rl_psico: null,
	m_med_rl_ergo1: null,
	m_med_rl_ergo2: null,
	m_med_rl_ergo3: null,
	m_med_rl_ergo4: null,
	m_med_rl_ergo5: null,
	m_med_rl_fisico1: null,
	m_med_rl_fisico2: null,
	m_med_rl_fisico3: null,
	m_med_rl_fisico4: null,
	m_med_rl_fisico5: null,
	m_med_rl_fisico6: null,
	m_med_rl_fisico7: null,
	m_med_rl_fisico8: null,
	m_med_rl_fisico9: null,
	m_med_rl_fisico10: null,
	m_med_rl_psico1: null,
	m_med_rl_psico2: null,
	m_med_rl_psico3: null,
	m_med_rl_psico4: null,
	m_med_rl_quimi1: null,
	m_med_rl_quimi2: null,
	m_med_rl_quimi3: null,
	m_med_rl_quimi4: null,
	m_med_rl_quimi5: null,
	m_med_rl_quimi6: null,
	m_med_rl_quimi7: null,
	m_med_muj_fur: null,
	m_med_muj_rc: null,
	m_med_muj_g: null,
	m_med_muj_p: null,
	m_med_muj_ult_pap: null,
	m_med_muj_resul: null,
	m_med_muj_mac: null,
	m_med_muj_a: null,
	m_med_muj_b: null,
	m_med_muj_c: null,
	m_med_muj_d: null,
	m_med_muj_e: null,
	m_med_cardio_op01: null,
	m_med_cardio_op02: null,
	m_med_cardio_desc02: null,
	m_med_cardio_op03: null,
	m_med_cardio_desc03: null,
	m_med_cardio_op04: null,
	m_med_cardio_desc04: null,
	m_med_cardio_op05: null,
	m_med_cardio_desc05: null,
	m_med_cardio_op06: null,
	m_med_cardio_desc06: null,
	m_med_cardio_op07: null,
	m_med_cardio_desc07: null,
	m_med_cardio_op08: null,
	m_med_cardio_desc08: null,
	m_med_cardio_op09: null,
	m_med_cardio_desc09: null,
	m_med_cardio_op10: null,
	m_med_cardio_desc10: null,
	m_med_cardio_op11: null,
	m_med_cardio_desc11: null,
	m_med_cardio_op12: null,
	m_med_cardio_desc12: null,
	m_med_cardio_op13: null,
	m_med_cardio_desc13: null,
	m_med_cardio_op14: null,
	m_med_cardio_desc14: null,
	m_med_cardio_op15: null,
	m_med_cardio_desc15: null,
	m_med_cardio_op16: null,
	m_med_cardio_desc16: null,
	m_med_cardio_op17: null,
	m_med_cardio_desc17: null,
	m_med_cardio_op18: null,
	m_med_cardio_desc18: null,
	m_osteo_cuello_dura_3meses: null,
	m_med_alcohol: null,
	m_med_coca: null,
	m_med_fam_papa: null,
	m_med_fam_mama: null,
	m_med_fam_herma: null,
	m_med_fam_hijos: null,
	m_med_fam_h_vivos: null,
	m_med_fam_h_muertos: null,
	m_med_fam_infarto55: null,
	m_med_fam_infarto65: null,
	m_med_piel_desc: null,
	m_med_piel_dx: null,
	m_med_cabeza_desc: null,
	m_med_cabeza_dx: null,
	m_med_cuello_desc: null,
	m_med_cuello_dx: null,
	m_med_nariz_desc: null,
	m_med_nariz_dx: null,
	m_med_boca_desc: null,
	m_med_boca_dx: null,
	m_med_oido_der01: null,
	m_med_oido_der02: null,
	m_med_oido_der03: null,
	m_med_oido_der04: null,
	m_med_oido_izq01: null,
	m_med_oido_izq02: null,
	m_med_oido_izq03: null,
	m_med_oido_izq04: null,
	m_med_torax_desc: null,
	m_med_torax_dx: null,
	m_med_corazon_desc: null,
	m_med_corazon_dx: null,
	m_med_mamas_derecho: null,
	m_med_mamas_izquier: null,
	m_med_pulmon_desc: null,
	m_med_pulmon_dx: null,
	m_med_osteo_aptitud: null,
	m_med_osteo_desc: null,
	m_med_abdomen: null,
	m_med_abdomen_desc: null,
	m_med_pru_sup_der: null,
	m_med_pru_med_der: null,
	m_med_pru_inf_der: null,
	m_med_ppl_der: null,
	m_med_pru_sup_izq: null,
	m_med_pru_med_izq: null,
	m_med_pru_inf_izq: null,
	m_med_ppl_izq: null,
	m_med_tacto: null,
	m_med_tacto_desc: null,
	m_med_anillos: null,
	m_med_anillos_desc: null,
	m_med_hernia: null,
	m_med_hernia_desc: null,
	m_med_varices: null,
	m_med_varices_desc: null,
	m_med_genitales_desc: null,
	m_med_genitales_dx: null,
	m_med_ganglios_desc: null,
	m_med_ganglios_dx: null,
	m_med_lenguaje_desc: null,
	m_med_lenguaje_dx: null,
	m_med_aptitud: null,
	m_med_fech_val: null,
	m_med_medico_ocupa: null,
	m_med_medico_auditor: null,
	m_312_fech_vence: null,
	m_312_time_aptitud: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.list_diag.load();
		this.list_obs.load();
		this.list_restric.load();
		this.list_inter.load();
		this.list_recom.load();
		this.crea_controles();
		if (this.record.get("st") >= 1) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_nuevoAnexo16",
				format: "json",
				ficha7c_adm: mod.medicina.nuevoAnexo16.record.get("adm"),
				ficha7c_exa: mod.medicina.nuevoAnexo16.record.get("ex_id"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.medicina.nuevoAnexo16.val_medico.setValue(r.val_medico);
				//                mod.medicina.nuevoAnexo16.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_stores: function () {
		this.st_busca_puesto_postula = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_puesto_postula",
				format: "json",
			},
			fields: ["m_med_puesto_postula"],
			root: "data",
		});
		this.st_busca_area = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_area",
				format: "json",
			},
			fields: ["m_med_area"],
			root: "data",
		});
		this.st_busca_puesto_actual = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_puesto_actual",
				format: "json",
			},
			fields: ["m_med_puesto_actual"],
			root: "data",
		});
		this.st_busca_eq_opera = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_eq_opera",
				format: "json",
			},
			fields: ["m_med_eq_opera"],
			root: "data",
		});
		this.st_busca_piel_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_piel_desc",
				format: "json",
			},
			fields: ["m_med_piel_desc"],
			root: "data",
		});
		this.st_busca_piel_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_piel_dx",
				format: "json",
			},
			fields: ["m_med_piel_dx"],
			root: "data",
		});
		this.st_busca_cabeza_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_cabeza_desc",
				format: "json",
			},
			fields: ["m_med_cabeza_desc"],
			root: "data",
		});
		this.st_busca_cabeza_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_cabeza_dx",
				format: "json",
			},
			fields: ["m_med_cabeza_dx"],
			root: "data",
		});
		this.st_busca_cuello_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_cuello_desc",
				format: "json",
			},
			fields: ["m_med_cuello_desc"],
			root: "data",
		});
		this.st_busca_cuello_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_cuello_dx",
				format: "json",
			},
			fields: ["m_med_cuello_dx"],
			root: "data",
		});
		this.st_busca_nariz_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_nariz_desc",
				format: "json",
			},
			fields: ["m_med_nariz_desc"],
			root: "data",
		});
		this.st_busca_nariz_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_nariz_dx",
				format: "json",
			},
			fields: ["m_med_nariz_dx"],
			root: "data",
		});
		this.st_busca_boca_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_boca_desc",
				format: "json",
			},
			fields: ["m_med_boca_desc"],
			root: "data",
		});
		this.st_busca_boca_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_boca_dx",
				format: "json",
			},
			fields: ["m_med_boca_dx"],
			root: "data",
		});
		this.st_busca_torax_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_torax_desc",
				format: "json",
			},
			fields: ["m_med_torax_desc"],
			root: "data",
		});
		this.st_busca_torax_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_torax_dx",
				format: "json",
			},
			fields: ["m_med_torax_dx"],
			root: "data",
		});
		this.st_busca_corazon_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_corazon_desc",
				format: "json",
			},
			fields: ["m_med_corazon_desc"],
			root: "data",
		});
		this.st_busca_corazon_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_corazon_dx",
				format: "json",
			},
			fields: ["m_med_corazon_dx"],
			root: "data",
		});
		this.st_busca_mamas_derecho = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_mamas_derecho",
				format: "json",
			},
			fields: ["m_med_mamas_derecho"],
			root: "data",
		});
		this.st_busca_mamas_izquier = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_mamas_izquier",
				format: "json",
			},
			fields: ["m_med_mamas_izquier"],
			root: "data",
		});
		this.st_busca_pulmon_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_pulmon_desc",
				format: "json",
			},
			fields: ["m_med_pulmon_desc"],
			root: "data",
		});
		this.st_busca_pulmon_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_pulmon_dx",
				format: "json",
			},
			fields: ["m_med_pulmon_dx"],
			root: "data",
		});
		this.st_busca_abdomen = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_abdomen",
				format: "json",
			},
			fields: ["m_med_abdomen"],
			root: "data",
		});
		this.st_busca_abdomen_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_abdomen_desc",
				format: "json",
			},
			fields: ["m_med_abdomen_desc"],
			root: "data",
		});
		this.st_busca_tacto_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_tacto_desc",
				format: "json",
			},
			fields: ["m_med_tacto_desc"],
			root: "data",
		});
		this.st_busca_anillos_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_anillos_desc",
				format: "json",
			},
			fields: ["m_med_anillos_desc"],
			root: "data",
		});
		this.st_busca_hernia_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_hernia_desc",
				format: "json",
			},
			fields: ["m_med_hernia_desc"],
			root: "data",
		});
		this.st_busca_varices_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_varices_desc",
				format: "json",
			},
			fields: ["m_med_hernia_desc"],
			root: "data",
		});
		this.st_busca_genitales_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_genitales_desc",
				format: "json",
			},
			fields: ["m_med_genitales_desc"],
			root: "data",
		});
		this.st_busca_genitales_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_genitales_dx",
				format: "json",
			},
			fields: ["m_med_genitales_dx"],
			root: "data",
		});
		this.st_busca_ganglios_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_ganglios_desc",
				format: "json",
			},
			fields: ["m_med_ganglios_desc"],
			root: "data",
		});
		this.st_busca_ganglios_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_ganglios_dx",
				format: "json",
			},
			fields: ["m_med_ganglios_dx"],
			root: "data",
		});
		this.st_busca_lenguaje_desc = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_lenguaje_desc",
				format: "json",
			},
			fields: ["m_med_lenguaje_desc"],
			root: "data",
		});
		this.st_busca_lenguaje_dx = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_lenguaje_dx",
				format: "json",
			},
			fields: ["m_med_lenguaje_dx"],
			root: "data",
		});
		this.list_diag = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_diag",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: ["diag_id", "diag_adm", "diag_desc"],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm = mod.medicina.nuevoAnexo16.record.get("adm");
				},
			},
		});
		this.list_obs = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_obs",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: ["obs_id", "obs_adm", "obs_desc", "obs_plazo"],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm = mod.medicina.nuevoAnexo16.record.get("adm");
				},
			},
		});
		this.list_restric = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_restric",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: ["restric_id", "restric_adm", "restric_desc", "restric_plazo"],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm = mod.medicina.nuevoAnexo16.record.get("adm");
				},
			},
		});
		this.list_inter = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_inter",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: ["inter_id", "inter_adm", "inter_desc", "inter_plazo"],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm = mod.medicina.nuevoAnexo16.record.get("adm");
				},
			},
		});
		this.list_recom = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_recom",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: ["recom_id", "recom_adm", "recom_desc", "recom_plazo"],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm = mod.medicina.nuevoAnexo16.record.get("adm");
				},
			},
		});
	},
	crea_controles: function () {
		//m_med_contac_nom
		this.m_med_contac_nom = new Ext.form.TextField({
			fieldLabel: "<b>NOMBRES Y APELLIDOS</b>",
			name: "m_med_contac_nom",
			anchor: "95%",
		});
		//m_med_contac_parent
		this.m_med_contac_parent = new Ext.form.TextField({
			fieldLabel: "<b>PARENTESCO</b>",
			name: "m_med_contac_parent",
			anchor: "95%",
		});
		//m_med_contac_telf
		this.m_med_contac_telf = new Ext.form.TextField({
			fieldLabel: "<b>CELULAR</b>",
			name: "m_med_contac_telf",
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
		//m_med_puesto_postula

		this.Tpl_m_med_puesto_postula = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_puesto_postula}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_puesto_postula = new Ext.form.ComboBox({
			store: this.st_busca_puesto_postula,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_puesto_postula,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_puesto_postula",
			displayField: "m_med_puesto_postula",
			valueField: "m_med_puesto_postula",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>PUESTO AL QUE POSTULA</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_area
		this.Tpl_m_med_area = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_area}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_area = new Ext.form.ComboBox({
			store: this.st_busca_area,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_area,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_area",
			displayField: "m_med_area",
			valueField: "m_med_area",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>AREA DE TRABAJO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_puesto_actual
		this.Tpl_m_med_puesto_actual = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_puesto_actual}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_puesto_actual = new Ext.form.ComboBox({
			store: this.st_busca_puesto_actual,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_puesto_actual,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_puesto_actual",
			displayField: "m_med_puesto_actual",
			valueField: "m_med_puesto_actual",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>PUESTO ACTUAL</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_tiempo
		this.m_med_tiempo = new Ext.form.TextField({
			fieldLabel: "<b>TIEMPO EN SU PUESTO ACTUAL</b>",
			name: "m_med_tiempo",
			anchor: "95%",
		});
		//m_med_eq_opera
		this.Tpl_m_med_eq_opera = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_eq_opera}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_eq_opera = new Ext.form.ComboBox({
			store: this.st_busca_eq_opera,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_eq_opera,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_eq_opera",
			displayField: "m_med_eq_opera",
			valueField: "m_med_eq_opera",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>EQUIPO QUE OPERA</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_fech_ingreso
		this.m_med_fech_ingreso = new Ext.form.DateField({
			fieldLabel: "<b>FECHA DE INGRESO A EMPRESA</b>",
			name: "m_med_fech_ingreso",
			allowBlank: true,
			anchor: "90%",
			format: "d-m-Y",
			emptyText: "Dia-Mes-Año",
			listeners: {
				render: function (datefield) {
					datefield.setValue(new Date());
				},
			},
		});
		//m_med_reubicacion
		this.m_med_reubicacion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_reubicacion",
			fieldLabel: "<b>REUBICACIÓN</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_med_tip_opera
		this.m_med_tip_opera = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SUPERFICIE", "SUPERFICIE"],
					["CONCENTRADORA", "CONCENTRADORA"],
					["SUBSUELO", "SUBSUELO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_tip_opera",
			fieldLabel: "<b>TIPO DE OPERACION</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("SUPERFICIE");
					descripcion.setRawValue("SUPERFICIE");
				},
			},
		});
		//m_med_minerales
		this.m_med_minerales = new Ext.form.TextField({
			fieldLabel: "<b>MINERALES EXPLOTADOS</b>",
			name: "m_med_minerales",
			anchor: "95%",
		});
		//m_med_altura_lab
		this.m_med_altura_lab = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["HASTA 3000 m", "HASTA 3000 m"],
					["3001 A 3500 m", "3001 A 3500 m"],
					["3501 A 4000 m", "3501 A 4000 m"],
					["4001 A 4500 m", "4001 A 4500 m"],
					["MAS DE 4501 m", "MAS DE 4501 m"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_altura_lab",
			fieldLabel: "<b>ALTURA DE LABOR</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("4001 A 4500 m");
					descripcion.setRawValue("4001 A 4500 m");
				},
			},
		});
		//m_med_rl_bio1
		this.m_med_rl_bio1 = new Ext.form.RadioGroup({
			fieldLabel: "<b>RIESGO BIOLOGICO</b>",
			items: [
				{ boxLabel: "SI", name: "m_med_rl_bio1", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_med_rl_bio1",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_med_rl_ergo1
		this.m_med_rl_ergo = new Ext.form.CheckboxGroup({
			fieldLabel: "<b>RIESGO ERGONOMICOS</b>",
			itemCls: "x-check-group-alt",
			columns: 5,
			items: [
				{ boxLabel: "TURNOS", name: "m_med_rl_ergo1", inputValue: "TURNOS" },
				{ boxLabel: "CARGAS", name: "m_med_rl_ergo2", inputValue: "CARGAS" },
				{
					boxLabel: "MOVIMIENTOS REPETITIVOS",
					name: "m_med_rl_ergo3",
					inputValue: "MOVIMIENTOS REPETITIVOS",
				},
				{ boxLabel: "PVD", name: "m_med_rl_ergo4", inputValue: "PVD" },
				{ boxLabel: "OTROS", name: "m_med_rl_ergo5", inputValue: "OTROS" },
			],
		});
		//m_med_rl_fisico1
		this.m_med_rl_fisico = new Ext.form.CheckboxGroup({
			fieldLabel: "<b>RIESGO FISICO</b>",
			itemCls: "x-check-group-alt",
			columns: 5,
			items: [
				{ boxLabel: "RUIDO", name: "m_med_rl_fisico1", inputValue: "RUIDO" },
				{
					boxLabel: "PRECIONES",
					name: "m_med_rl_fisico2",
					inputValue: "PRECIONES",
				},
				{
					boxLabel: "TEMPERATURA",
					name: "m_med_rl_fisico3",
					inputValue: "TEMPERATURA",
				},
				{
					boxLabel: "ILUMINACION",
					name: "m_med_rl_fisico4",
					inputValue: "ILUMINACION",
				},
				{
					boxLabel: "VIBRACION TOTAL",
					name: "m_med_rl_fisico5",
					inputValue: "VIBRACION TOTAL",
				},
				{
					boxLabel: "VIBRACION SEGMENTARIA",
					name: "m_med_rl_fisico6",
					inputValue: "VIBRACION SEGMENTARIA",
				},
				{
					boxLabel: "RADIACION IONIZANTE Y NO IONIZANTE",
					name: "m_med_rl_fisico7",
					inputValue: "RADIACION IONIZANTE Y NO IONIZANTE",
				},
				{
					boxLabel: "TEMPERATURAS EXTREMAS",
					name: "m_med_rl_fisico8",
					inputValue: "TEMPERATURAS EXTREMAS",
				},
				{
					boxLabel: "RADIACION INFRAROJA Y ULTRAVIOLETA",
					name: "m_med_rl_fisico9",
					inputValue: "RADIACION INFRAROJA Y ULTRAVIOLETA",
				},
				{ boxLabel: "OTROS", name: "m_med_rl_fisico10", inputValue: "OTROS" },
			],
		});
		//m_med_rl_psico1
		this.m_med_rl_psico = new Ext.form.CheckboxGroup({
			fieldLabel: "<b>RIESGO PSICOSOCIALES</b>",
			itemCls: "x-check-group-alt",
			columns: 4,
			items: [
				{ boxLabel: "STRESS", name: "m_med_rl_psico1", inputValue: "STRESS" },
				{
					boxLabel: "FATIGA LABORAL",
					name: "m_med_rl_psico2",
					inputValue: "FATIGA LABORAL",
				},
				{
					boxLabel: "MONOTONIA",
					name: "m_med_rl_psico3",
					inputValue: "MONOTONIA",
				},
				{ boxLabel: "OTROS", name: "m_med_rl_psico4", inputValue: "OTROS" },
			],
		});
		//m_med_rl_quimi1
		this.m_med_rl_quimi = new Ext.form.CheckboxGroup({
			fieldLabel: "<b>RIESGO QUIMICOS</b>",
			itemCls: "x-check-group-alt",
			columns: 7,
			items: [
				{
					boxLabel: "GASES Y VAPORES",
					name: "m_med_rl_quimi1",
					inputValue: "GASES Y VAPORES",
				},
				{ boxLabel: "POLVOS", name: "m_med_rl_quimi2", inputValue: "POLVOS" },
				{
					boxLabel: "LIQUIDOS",
					name: "m_med_rl_quimi3",
					inputValue: "LIQUIDOS",
				},
				{
					boxLabel: "DISOLVENTES",
					name: "m_med_rl_quimi4",
					inputValue: "DISOLVENTES",
				},
				{
					boxLabel: "SOLVENTES",
					name: "m_med_rl_quimi5",
					inputValue: "SOLVENTES",
				},
				{
					boxLabel: "METALES PESADOS",
					name: "m_med_rl_quimi6",
					inputValue: "METALES PESADOS",
				},
				{ boxLabel: "OTROS", name: "m_med_rl_quimi7", inputValue: "OTROS" },
			],
		});
		//m_med_muj_fur
		this.m_med_muj_fur = new Ext.form.DateField({
			name: "m_med_muj_fur",
			fieldLabel: "<b>F.U.R</b>",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			allowBlank: false,
			anchor: "90%",
			format: "d-m-Y",
			emptyText: "Dia-Mes-Año",
			listeners: {
				//                render: function (datefield) {
				//                    datefield.setValue(new Date());
				//                }
			},
		});
		//m_med_muj_rc
		this.m_med_muj_rc = new Ext.form.TextField({
			fieldLabel: "<b>RC</b>",
			name: "m_med_muj_rc",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			anchor: "95%",
		});
		//m_med_muj_g
		this.m_med_muj_g = new Ext.form.TextField({
			fieldLabel: "<b>G</b>",
			name: "m_med_muj_g",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			anchor: "95%",
		});
		//m_med_muj_p
		this.m_med_muj_p = new Ext.form.TextField({
			fieldLabel: "<b>P</b>",
			name: "m_med_muj_p",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			anchor: "95%",
		});
		//m_med_muj_ult_pap
		this.m_med_muj_ult_pap = new Ext.form.DateField({
			fieldLabel: "<b>ULTIMO PAP</b>",
			name: "m_med_muj_ult_pap",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			//allowBlank: false,
			anchor: "90%",
			format: "d-m-Y",
			emptyText: "Dia-Mes-Año",
			listeners: {
				//                render: function (datefield) {
				//                    datefield.setValue(new Date());
				//                }
			},
		});
		//m_med_muj_resul
		this.m_med_muj_resul = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1", "NORMAL"],
					["2", "ANORMAL"],
					["3", "NUNCA SE HIZO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_muj_resul",
			fieldLabel: "<b>RESULTADOS</b>",
			allowBlank: false,
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue(3);
					descripcion.setRawValue("NUNCA SE HIZO");
				},
			},
		});
		//m_med_muj_mac
		this.m_med_muj_mac = new Ext.form.RadioGroup({
			fieldLabel: "<b>MAC</b>",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			items: [
				{ boxLabel: "Si", name: "m_med_muj_mac", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_muj_mac",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_muj_obs
		this.m_med_muj_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_med_muj_obs",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			anchor: "95%",
		});
		//m_med_muj_a
		this.m_med_muj_a = new Ext.form.TextField({
			fieldLabel:
				"<b>a</b> nº total embarzos, incluye abortos, molas hidatiformes y embarazos ectopicos",
			name: "m_med_muj_a",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			anchor: "95%",
		});
		//m_med_muj_b
		this.m_med_muj_b = new Ext.form.TextField({
			fieldLabel: "<b>b</b> nº total de recien nacidos a termino",
			name: "m_med_muj_b",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			anchor: "95%",
		});
		//m_med_muj_c
		this.m_med_muj_c = new Ext.form.TextField({
			fieldLabel: "<b>c</b> nº total de recien nacidos prematuros",
			name: "m_med_muj_c",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			anchor: "95%",
		});
		//m_med_muj_d
		this.m_med_muj_d = new Ext.form.TextField({
			fieldLabel: "<b>d</b> nº total de abortos",
			name: "m_med_muj_d",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			anchor: "95%",
		});
		//m_med_muj_e
		this.m_med_muj_e = new Ext.form.TextField({
			fieldLabel: "<b>e</b> nº total de hijos vivos actualmente",
			name: "m_med_muj_e",
			disabled: this.record.get("pac_sexo") == "F" ? false : true,
			anchor: "95%",
		});
		//m_med_cardio_op01
		this.m_med_cardio_op01 = new Ext.form.RadioGroup({
			fieldLabel: "<b>ACTUALMENTE FUMA 01 CIGARRILLO A MAS AL DIA?</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_cardio_op01", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_cardio_op01",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_op02
		this.m_med_cardio_op02 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>¿HA TENIDO ALGUN TIPO DE ATAQUE, CONVULSION, PERDIDA DE CONOCIMIENTO O EPILEPSIA?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op02",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc02.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc02.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc02.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op02",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc02
		this.m_med_cardio_desc02 = new Ext.form.TextArea({
			name: "m_med_cardio_desc02",
			disabled: true, //pac_sexo
			fieldLabel: "<b>¿CUANDO, TRATAMIENTO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op03
		this.m_med_cardio_op03 = new Ext.form.RadioGroup({
			fieldLabel: "<b>¿UD. SUFRE O HA SUFRIDO DE PRESION ARTERIAL ALTA?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op03",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc03.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc03.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc03.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op03",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc03
		this.m_med_cardio_desc03 = new Ext.form.TextArea({
			name: "m_med_cardio_desc03",
			disabled: true,
			fieldLabel: "<b>¿CUANDO, TRATAMIENTO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op04
		this.m_med_cardio_op04 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>¿HA SUFRIDO ALGUN TIPO DE TRASTORNO MENTAL / PSIQUIATRICO?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op04",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc04.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc04.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc04.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op04",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc04
		this.m_med_cardio_desc04 = new Ext.form.TextArea({
			name: "m_med_cardio_desc04",
			disabled: true,
			fieldLabel: "<b>¿CUANDO, TRATAMIENTO / DOSIS?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op05
		this.m_med_cardio_op05 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>¿HA SUFRIDO DE ALGUN TRASTORNO DE SUEÑO?. ¿HA REQUERIDO PASTILLAS PARA DORMIR?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op05",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc05.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc05.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc05.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op05",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc05
		this.m_med_cardio_desc05 = new Ext.form.TextArea({
			name: "m_med_cardio_desc05",
			disabled: true,
			fieldLabel: "<b>¿CUANDO, TRATAMIENTO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op06
		this.m_med_cardio_op06 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>¿HA SUFRIDO DE BRONQUITIS, OTROS PROBLEMAS RESPITORIOS EN LOS ULTIMOS 06 MESES?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op06",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc06.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc06.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc06.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op06",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc06
		this.m_med_cardio_desc06 = new Ext.form.TextArea({
			name: "m_med_cardio_desc06",
			disabled: true,
			fieldLabel: "<b>¿CUAL, CUANDO, POR CUANTO TIEMPO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op07
		this.m_med_cardio_op07 = new Ext.form.RadioGroup({
			fieldLabel: "<b>¿ALGUNA HISTORIA DE DIABETES EN LA FAMILIA?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op07",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc07.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc07.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc07.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op07",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc07
		this.m_med_cardio_desc07 = new Ext.form.TextArea({
			name: "m_med_cardio_desc07",
			disabled: true,
			fieldLabel: "<b>¿QUIEN?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op08
		this.m_med_cardio_op08 = new Ext.form.RadioGroup({
			fieldLabel: "<b>¿ALGUNA HISTORIA DE ENFERMEDAD RENAL?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op08",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc08.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc08.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc08.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op08",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc08
		this.m_med_cardio_desc08 = new Ext.form.TextArea({
			name: "m_med_cardio_desc08",
			disabled: true,
			fieldLabel: "<b>¿CUANDO, TRATAMIENTO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op09
		this.m_med_cardio_op09 = new Ext.form.RadioGroup({
			fieldLabel: "<b>¿HA ESTADO ANTES SOBRE LO 4000 m DE ALTURA?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op09",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc09.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc09.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc09.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op09",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc09
		this.m_med_cardio_desc09 = new Ext.form.TextArea({
			name: "m_med_cardio_desc09",
			disabled: true,
			fieldLabel: "<b>¿DONDE, CUANDO, ALGUN PROBLEMA?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op10
		this.m_med_cardio_op10 = new Ext.form.RadioGroup({
			fieldLabel: "<b>¿HA SIDO OPERADO DE / POR ALGO?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op10",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc10.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc10.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc10.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op10",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc10
		this.m_med_cardio_desc10 = new Ext.form.TextArea({
			name: "m_med_cardio_desc10",
			disabled: true,
			fieldLabel: "<b>¿CAUSA, CUANDO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op11
		this.m_med_cardio_op11 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>¿ALGUNA HISTORIA DE ANEMIA?, ¿SE ENCUENTRA EMBARAZADA?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op11",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc11.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc11.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc11.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op11",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc11
		this.m_med_cardio_desc11 = new Ext.form.TextArea({
			name: "m_med_cardio_desc11",
			disabled: true,
			fieldLabel: "<b>¿CUAL, TRATAMIENTO?. ¿SI?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op12
		this.m_med_cardio_op12 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>¿ALGUNA HISTORIA DE ENFERMEDAD DE COAGULACION O TROMBOSIS?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op12",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc12.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc12.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc12.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op12",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc12
		this.m_med_cardio_desc12 = new Ext.form.TextArea({
			name: "m_med_cardio_desc12",
			disabled: true,
			fieldLabel: "<b>¿CUAL, TRATAMIENTO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op13
		this.m_med_cardio_op13 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>¿UD. SUFRE DE DOLOR DE PECHO O FALTA DE AIRE AL ESFUERZO?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op13",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc13.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc13.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc13.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op13",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc13
		this.m_med_cardio_desc13 = new Ext.form.TextArea({
			name: "m_med_cardio_desc13",
			disabled: true,
			fieldLabel: "<b>¿CUANDO, TRATAMIENTO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op14
		this.m_med_cardio_op14 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>¿UD. SUFRE DE PROBLEMAS CARDIACOS, ANGINA, USA MARCAPASOS?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op14",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc14.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc14.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc14.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op14",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc14
		this.m_med_cardio_desc14 = new Ext.form.TextArea({
			name: "m_med_cardio_desc05",
			disabled: true,
			fieldLabel: "<b>¿CUAL, TRATAMIENTO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op15
		this.m_med_cardio_op15 = new Ext.form.RadioGroup({
			fieldLabel: "<b>¿HA SUFRIDO DE RETINOPATIA O GLAUCOMA?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op15",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc15.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc15.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc15.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op15",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc15
		this.m_med_cardio_desc15 = new Ext.form.TextArea({
			name: "m_med_cardio_desc15",
			disabled: true,
			fieldLabel: "<b>¿CUANDO, TRATAMIENTO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op16
		this.m_med_cardio_op16 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>¿SE LE HA DIAGNOSTICADO OBESIDAD MORBIDA (IMC>35 Kg/m2)</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op16",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc16.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc16.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc16.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op16",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc16
		this.m_med_cardio_desc16 = new Ext.form.TextArea({
			name: "m_med_cardio_desc16",
			disabled: true,
			fieldLabel: "<b>¿CUANDO, TRATAMIENTO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op17
		this.m_med_cardio_op17 = new Ext.form.RadioGroup({
			fieldLabel: "<b>¿ESTA TOMANDO ALGUN MEDICAMENTO ACTUALMENTE?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op17",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc17.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc17.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc17.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op17",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc17
		this.m_med_cardio_desc17 = new Ext.form.TextArea({
			name: "m_med_cardio_desc17",
			disabled: true,
			fieldLabel: "<b>¿CUAL(ES), EN QUE DOSIS?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_cardio_op18
		this.m_med_cardio_op18 = new Ext.form.RadioGroup({
			fieldLabel: "<b>¿HA TENIDO ALGUN OTRO PROBEMA DE SALUD?</b>",
			items: [
				{
					boxLabel: "Si",
					name: "m_med_cardio_op18",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc18.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevoAnexo16.m_med_cardio_desc18.disable();
							mod.medicina.nuevoAnexo16.m_med_cardio_desc18.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_med_cardio_op18",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_cardio_desc18
		this.m_med_cardio_desc18 = new Ext.form.TextArea({
			name: "m_med_cardio_desc18",
			disabled: true,
			fieldLabel: "<b>¿CUAL, TRATAMIENTO?</b>",
			anchor: "99%",
			height: 40,
		});
		//m_med_tabaco
		this.m_med_tabaco = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NADA", "NADA"],
					["POCO", "POCO"],
					["HABITUAL", "HABITUAL"],
					["EXCESIVO", "EXCESIVO"],
				],
			}),
			fieldLabel: "<b>TABACO</b>",
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_tabaco",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NADA");
					descripcion.setRawValue("NADA");
				},
			},
		});
		//m_med_alcohol
		this.m_med_alcohol = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NADA", "NADA"],
					["POCO", "POCO"],
					["HABITUAL", "HABITUAL"],
					["EXCESIVO", "EXCESIVO"],
				],
			}),
			fieldLabel: "<b>ALCOHOL</b>",
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_alcohol",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NADA");
					descripcion.setRawValue("NADA");
				},
			},
		});
		//m_med_coca
		this.m_med_coca = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NADA", "NADA"],
					["POCO", "POCO"],
					["HABITUAL", "HABITUAL"],
					["EXCESIVO", "EXCESIVO"],
				],
			}),
			fieldLabel: "<b>COCA</b>",
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_coca",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NADA");
					descripcion.setRawValue("NADA");
				},
			},
		});
		//m_med_fam_papa
		this.m_med_fam_papa = new Ext.form.TextField({
			fieldLabel: "<b>PAPÁ</b>",
			name: "m_med_fam_papa",
			value: "-",
			anchor: "95%",
		});
		//m_med_fam_mama
		this.m_med_fam_mama = new Ext.form.TextField({
			fieldLabel: "<b>MAMÁ</b>",
			name: "m_med_fam_mama",
			value: "-",
			anchor: "95%",
		});
		//m_med_fam_herma
		this.m_med_fam_herma = new Ext.form.TextField({
			fieldLabel: "<b>HERMANO</b>",
			name: "m_med_fam_herma",
			value: "-",
			anchor: "95%",
		});
		//m_med_fam_hijos
		this.m_med_fam_hijos = new Ext.form.RadioGroup({
			fieldLabel: "<b>¿TIENE HIJOS?</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_fam_hijos", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_fam_hijos",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_fam_h_vivos
		this.m_med_fam_h_vivos = new Ext.form.TextField({
			fieldLabel: "<b>N° DE HIJOS VIVOS</b>",
			name: "m_med_fam_h_vivos",
			value: "-",
			anchor: "95%",
		});
		//m_med_fam_h_muertos
		this.m_med_fam_h_muertos = new Ext.form.TextField({
			fieldLabel: "<b>N° DE HIJOS MUERTOS</b>",
			name: "m_med_fam_h_muertos",
			value: "-",
			anchor: "95%",
		});
		//m_med_fam_infarto55
		this.m_med_fam_infarto55 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>SU PADRE O HERMANO HA TENIDO UN CUADRO DE INFARTO DE MIOCARDIO (ATAQUE AL CORAZON) ANTES DE LOS 55 AÑOS.</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_fam_infarto55", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_fam_infarto55",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_fam_infarto65
		this.m_med_fam_infarto65 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>SU MADRE O HERMANA HA TENIDO UN CUADRO DE INFARTO DE MIOCARDIO (ATAQUE AL CORAZON) ANTES DE LOS 65 AÑOS.</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_fam_infarto65", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_fam_infarto65",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_piel_desc
		this.Tpl_m_med_piel_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_piel_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_piel_desc = new Ext.form.ComboBox({
			store: this.st_busca_piel_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_piel_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_piel_desc",
			displayField: "m_med_piel_desc",
			valueField: "m_med_piel_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_piel_dx
		this.Tpl_m_med_piel_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_piel_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_piel_dx = new Ext.form.ComboBox({
			store: this.st_busca_piel_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_piel_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_piel_dx",
			displayField: "m_med_piel_dx",
			valueField: "m_med_piel_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_cabeza_desc
		this.Tpl_m_med_cabeza_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_cabeza_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_cabeza_desc = new Ext.form.ComboBox({
			store: this.st_busca_cabeza_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_cabeza_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_cabeza_desc",
			displayField: "m_med_cabeza_desc",
			valueField: "m_med_cabeza_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_cabeza_dx
		this.Tpl_m_med_cabeza_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_cabeza_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_cabeza_dx = new Ext.form.ComboBox({
			store: this.st_busca_cabeza_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_cabeza_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_cabeza_dx",
			displayField: "m_med_cabeza_dx",
			valueField: "m_med_cabeza_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_cuello_desc
		this.Tpl_m_med_cuello_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_cuello_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_cuello_desc = new Ext.form.ComboBox({
			store: this.st_busca_cuello_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_cuello_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_cuello_desc",
			displayField: "m_med_cuello_desc",
			valueField: "m_med_cuello_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_cuello_dx
		this.Tpl_m_med_cuello_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_cuello_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_cuello_dx = new Ext.form.ComboBox({
			store: this.st_busca_cuello_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_cuello_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_cuello_dx",
			displayField: "m_med_cuello_dx",
			valueField: "m_med_cuello_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_nariz_desc
		this.Tpl_m_med_nariz_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_nariz_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_nariz_desc = new Ext.form.ComboBox({
			store: this.st_busca_nariz_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_nariz_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_nariz_desc",
			displayField: "m_med_nariz_desc",
			valueField: "m_med_nariz_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_nariz_dx
		this.Tpl_m_med_nariz_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_nariz_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_nariz_dx = new Ext.form.ComboBox({
			store: this.st_busca_nariz_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_nariz_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_nariz_dx",
			displayField: "m_med_nariz_dx",
			valueField: "m_med_nariz_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_boca_desc
		this.Tpl_m_med_boca_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_boca_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_boca_desc = new Ext.form.ComboBox({
			store: this.st_busca_boca_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_boca_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_boca_desc",
			displayField: "m_med_boca_desc",
			valueField: "m_med_boca_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_boca_dx
		this.m_med_boca_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_boca_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_boca_dx = new Ext.form.ComboBox({
			store: this.st_busca_boca_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_boca_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_boca_dx",
			displayField: "m_med_boca_dx",
			valueField: "m_med_boca_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_oido_der01
		this.m_med_oido_der01 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["CAE PERMEABLE", "CAE PERMEABLE"],
					["CAE NO PERMEABLE", "CAE NO PERMEABLE"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_oido_der01",
			fieldLabel: "PERMEABILIDAD",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("CAE PERMEABLE");
					descripcion.setRawValue("CAE PERMEABLE");
				},
			},
		});
		//m_med_oido_der02
		this.m_med_oido_der02 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO RETRACCION", "NO RETRACCION"],
					["CON RETRACCION", "CON RETRACCION"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_oido_der02",
			fieldLabel: "RETRACCION",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO RETRACCION");
					descripcion.setRawValue("NO RETRACCION");
				},
			},
		});
		//m_med_oido_der03
		this.m_med_oido_der03 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO PERFORACIONES", "NO PERFORACIONES"],
					["CON PERFORACIONES", "CON PERFORACIONES"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_oido_der03",
			fieldLabel: "PERFORACIONES",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO PERFORACIONES");
					descripcion.setRawValue("NO PERFORACIONES");
				},
			},
		});
		//m_med_oido_der04
		this.m_med_oido_der04 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["TRIANGULO DE LUZ PRESENTE", "TRIANGULO DE LUZ PRESENTE"],
					["TRIANGULO DE LUZ NO PRESENTE", "TRIANGULO DE LUZ NO PRESENTE"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_oido_der04",
			fieldLabel: "TRIANGULO DE LUZ",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("TRIANGULO DE LUZ PRESENTE");
					descripcion.setRawValue("TRIANGULO DE LUZ PRESENTE");
				},
			},
		});
		//m_med_oido_izq01
		this.m_med_oido_izq01 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["CAE PERMEABLE", "CAE PERMEABLE"],
					["CAE NO PERMEABLE", "CAE NO PERMEABLE"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_oido_izq01",
			fieldLabel: "PERMEABILIDAD",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("CAE PERMEABLE");
					descripcion.setRawValue("CAE PERMEABLE");
				},
			},
		});
		//m_med_oido_izq02
		this.m_med_oido_izq02 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO RETRACCION", "NO RETRACCION"],
					["CON RETRACCION", "CON RETRACCION"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_oido_izq02",
			fieldLabel: "RETRACCION",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO RETRACCION");
					descripcion.setRawValue("NO RETRACCION");
				},
			},
		});
		//m_med_oido_izq03
		this.m_med_oido_izq03 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO PERFORACIONES", "NO PERFORACIONES"],
					["CON PERFORACIONES", "CON PERFORACIONES"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_oido_izq03",
			fieldLabel: "PERFORACIONES",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO PERFORACIONES");
					descripcion.setRawValue("NO PERFORACIONES");
				},
			},
		});
		//m_med_oido_izq04
		this.m_med_oido_izq04 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["TRIANGULO DE LUZ PRESENTE", "TRIANGULO DE LUZ PRESENTE"],
					["TRIANGULO DE LUZ NO PRESENTE", "TRIANGULO DE LUZ NO PRESENTE"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_oido_izq04",
			fieldLabel: "TRIANGULO DE LUZ",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("TRIANGULO DE LUZ PRESENTE");
					descripcion.setRawValue("TRIANGULO DE LUZ PRESENTE");
				},
			},
		});
		//m_med_torax_desc
		this.Tpl_m_med_torax_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_torax_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_torax_desc = new Ext.form.ComboBox({
			store: this.st_busca_torax_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_torax_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_torax_desc",
			displayField: "m_med_torax_desc",
			valueField: "m_med_torax_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_torax_dx
		this.Tpl_m_med_torax_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_torax_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_torax_dx = new Ext.form.ComboBox({
			store: this.st_busca_torax_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_torax_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_torax_dx",
			displayField: "m_med_torax_dx",
			valueField: "m_med_torax_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_corazon_desc
		this.Tpl_m_med_corazon_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_corazon_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_corazon_desc = new Ext.form.ComboBox({
			store: this.st_busca_corazon_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_corazon_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_corazon_desc",
			displayField: "m_med_corazon_desc",
			valueField: "m_med_corazon_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_corazon_dx
		this.Tpl_m_med_corazon_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_corazon_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_corazon_dx = new Ext.form.ComboBox({
			store: this.st_busca_corazon_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_corazon_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_corazon_dx",
			displayField: "m_med_corazon_dx",
			valueField: "m_med_corazon_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_mamas_derecho
		this.Tpl_m_med_mamas_derecho = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_mamas_derecho}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_mamas_derecho = new Ext.form.ComboBox({
			store: this.st_busca_mamas_derecho,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_mamas_derecho,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_mamas_derecho",
			displayField: "m_med_mamas_derecho",
			valueField: "m_med_mamas_derecho",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>MAMA DERECHA</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_mamas_izquier
		this.Tpl_m_med_mamas_izquier = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_mamas_izquier}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_mamas_izquier = new Ext.form.ComboBox({
			store: this.st_busca_mamas_izquier,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_mamas_izquier,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_mamas_izquier",
			displayField: "m_med_mamas_izquier",
			valueField: "m_med_mamas_izquier",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>MAMA IZQUIERDA</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_pulmon_desc
		this.Tpl_m_med_pulmon_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_pulmon_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_pulmon_desc = new Ext.form.ComboBox({
			store: this.st_busca_pulmon_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_pulmon_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_pulmon_desc",
			displayField: "m_med_pulmon_desc",
			valueField: "m_med_pulmon_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_pulmon_dx
		this.Tpl_m_med_pulmon_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_pulmon_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_pulmon_dx = new Ext.form.ComboBox({
			store: this.st_busca_pulmon_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_pulmon_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_pulmon_dx",
			displayField: "m_med_pulmon_dx",
			valueField: "m_med_pulmon_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_osteo_aptitud
		this.m_med_osteo_aptitud = new Ext.form.TextField({
			fieldLabel: "<b>APTITUD</b>",
			disabled: true,
			name: "m_med_osteo_aptitud",
			anchor: "95%",
		});
		//m_med_osteo_desc
		this.m_med_osteo_desc = new Ext.form.TextArea({
			name: "m_med_osteo_desc",
			fieldLabel: "<b>DESCRIPCIÓN</b>",
			value: "-",
			anchor: "99%",
			height: 40,
		});
		//m_med_abdomen
		this.Tpl_m_med_abdomen = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_abdomen}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_abdomen = new Ext.form.ComboBox({
			store: this.st_busca_abdomen,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_abdomen,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_abdomen",
			displayField: "m_med_abdomen",
			valueField: "m_med_abdomen",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>ABDOMEN</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_abdomen_desc
		this.Tpl_m_med_abdomen_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_abdomen_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_abdomen_desc = new Ext.form.ComboBox({
			store: this.st_busca_abdomen_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_abdomen_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_abdomen_desc",
			displayField: "m_med_abdomen_desc",
			valueField: "m_med_abdomen_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_pru_sup_der
		this.m_med_pru_sup_der = new Ext.form.RadioGroup({
			fieldLabel: "<b>PRU SUP DER</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_pru_sup_der", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_pru_sup_der",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_pru_med_der
		this.m_med_pru_med_der = new Ext.form.RadioGroup({
			fieldLabel: "<b>PRU MED DER</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_pru_med_der", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_pru_med_der",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_pru_inf_der
		this.m_med_pru_inf_der = new Ext.form.RadioGroup({
			fieldLabel: "<b>PRU INF DER</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_pru_inf_der", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_pru_inf_der",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_ppl_der
		this.m_med_ppl_der = new Ext.form.RadioGroup({
			fieldLabel: "<b>PPL DER</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_ppl_der", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_ppl_der",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_pru_sup_izq
		this.m_med_pru_sup_izq = new Ext.form.RadioGroup({
			fieldLabel: "<b>PRU SUP IZQ</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_pru_sup_izq", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_pru_sup_izq",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_pru_med_izq
		this.m_med_pru_med_izq = new Ext.form.RadioGroup({
			fieldLabel: "<b>PRU MED IZQ</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_pru_med_izq", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_pru_med_izq",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_pru_inf_izq
		this.m_med_pru_inf_izq = new Ext.form.RadioGroup({
			fieldLabel: "<b>PRU INF IZQ</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_pru_inf_izq", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_pru_inf_izq",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_ppl_izq
		this.m_med_ppl_izq = new Ext.form.RadioGroup({
			fieldLabel: "<b>PPL IZQ</b>",
			items: [
				{ boxLabel: "Si", name: "m_med_ppl_izq", inputValue: "Si" },
				{
					boxLabel: "No",
					name: "m_med_ppl_izq",
					inputValue: "No",
					checked: true,
				},
			],
		});
		//m_med_tacto
		this.m_med_tacto = new Ext.form.RadioGroup({
			fieldLabel: "<b>TACTO RECTAL</b>",
			items: [
				{
					boxLabel: "NO SE HIZO",
					name: "m_med_tacto",
					inputValue: "NO SE HIZO",
					checked: true,
				},
				{ boxLabel: "SE HIZO", name: "m_med_tacto", inputValue: "SE HIZO" },
			],
		});
		//m_med_tacto_desc
		this.Tpl_m_med_tacto_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_tacto_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_tacto_desc = new Ext.form.ComboBox({
			store: this.st_busca_tacto_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_tacto_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_tacto_desc",
			displayField: "m_med_tacto_desc",
			valueField: "m_med_tacto_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_anillos
		this.m_med_anillos = new Ext.form.RadioGroup({
			fieldLabel: "<b>ANILLOS INGUINALES</b>",
			items: [
				{ boxLabel: "SI", name: "m_med_anillos", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_med_anillos",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_med_anillos_desc
		this.Tpl_m_med_anillos_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_anillos_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_anillos_desc = new Ext.form.ComboBox({
			store: this.st_busca_anillos_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_anillos_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_anillos_desc",
			displayField: "m_med_anillos_desc",
			valueField: "m_med_anillos_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_hernia
		this.m_med_hernia = new Ext.form.RadioGroup({
			fieldLabel: "<b>HERNIAS</b>",
			items: [
				{ boxLabel: "SI", name: "m_med_hernia", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_med_hernia",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_med_hernia_desc
		this.Tpl_m_med_hernia_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_hernia_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_hernia_desc = new Ext.form.ComboBox({
			store: this.st_busca_hernia_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_hernia_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_hernia_desc",
			displayField: "m_med_hernia_desc",
			valueField: "m_med_hernia_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_varices
		this.m_med_varices = new Ext.form.RadioGroup({
			fieldLabel: "<b>VARICES</b>",
			items: [
				{ boxLabel: "SI", name: "m_med_varices", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_med_varices",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_med_varices_desc
		this.Tpl_m_med_varices_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_varices_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_varices_desc = new Ext.form.ComboBox({
			store: this.st_busca_varices_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_varices_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_varices_desc",
			displayField: "m_med_varices_desc",
			valueField: "m_med_varices_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_genitales_desc
		this.Tpl_m_med_genitales_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_genitales_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_genitales_desc = new Ext.form.ComboBox({
			store: this.st_busca_genitales_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_genitales_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_genitales_desc",
			displayField: "m_med_genitales_desc",
			valueField: "m_med_genitales_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_genitales_dx
		this.Tpl_m_med_genitales_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_genitales_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_genitales_dx = new Ext.form.ComboBox({
			store: this.st_busca_genitales_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_genitales_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_genitales_dx",
			displayField: "m_med_genitales_dx",
			valueField: "m_med_genitales_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_ganglios_desc
		this.Tpl_m_med_ganglios_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_ganglios_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_ganglios_desc = new Ext.form.ComboBox({
			store: this.st_busca_ganglios_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_ganglios_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_ganglios_desc",
			displayField: "m_med_ganglios_desc",
			valueField: "m_med_ganglios_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_ganglios_dx
		this.Tpl_m_med_ganglios_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_ganglios_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_ganglios_dx = new Ext.form.ComboBox({
			store: this.st_busca_ganglios_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_ganglios_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_ganglios_dx",
			displayField: "m_med_ganglios_dx",
			valueField: "m_med_ganglios_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_lenguaje_desc
		this.Tpl_m_med_lenguaje_desc = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_lenguaje_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_lenguaje_desc = new Ext.form.ComboBox({
			store: this.st_busca_lenguaje_desc,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_lenguaje_desc,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_lenguaje_desc",
			displayField: "m_med_lenguaje_desc",
			valueField: "m_med_lenguaje_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DESCRIPCION</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_lenguaje_dx
		this.Tpl_m_med_lenguaje_dx = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_med_lenguaje_dx}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_med_lenguaje_dx = new Ext.form.ComboBox({
			store: this.st_busca_lenguaje_dx,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_med_lenguaje_dx,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_med_lenguaje_dx",
			displayField: "m_med_lenguaje_dx",
			valueField: "m_med_lenguaje_dx",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DX DIAGNOSTICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_med_aptitud
		this.m_med_aptitud = new Ext.form.RadioGroup({
			fieldLabel: "<b>APTITUD</b>",
			itemCls: "x-check-group-alt",
			columns: 7,
			items: [
				{ boxLabel: "APTO", name: "m_med_aptitud", inputValue: "APTO" },
				{
					boxLabel: "APTO CON OBSERVACIONES",
					name: "m_med_aptitud",
					inputValue: "APTO CON OBSERVACIONES",
				},
				{
					boxLabel: "APTO CON RESTRICCIÓN",
					name: "m_med_aptitud",
					inputValue: "APTO CON RESTRICCIÓN",
				},
				{
					boxLabel: "NO APTO TEMPORAL",
					name: "m_med_aptitud",
					inputValue: "NO APTO TEMPORAL",
				},
				{
					boxLabel: "NO APTO DEFINITIVO",
					name: "m_med_aptitud",
					inputValue: "NO APTO DEFINITIVO",
				},
				//{boxLabel: 'RETIRO', name: 'm_med_aptitud', inputValue: 'RETIRO'},
				//{boxLabel: 'OBSERVADO', name: 'm_med_aptitud', inputValue: 'OBSERVADO'},
				{
					boxLabel: "EN PROCESO DE VALIDACION",
					name: "m_med_aptitud",
					inputValue: "EN PROCESO DE VALIDACION",
					checked: true,
				},
			],
		});
		//m_med_fech_val
		this.m_med_fech_val = new Ext.form.DateField({
			fieldLabel: "<b>FECHA DE VALIDACION</b>",
			name: "m_med_fech_val",
			allowBlank: false,
			anchor: "90%",
			format: "d-m-Y",
			emptyText: "Dia-Mes-Año",
			listeners: {
				render: function (datefield) {
					datefield.setValue(new Date());
				},
			},
		});
		//m_med_medico_ocupa(REGISTROS MEDIANTE LOGICA EN PHP)
		//m_med_medico_auditor(REGISTROS MEDIANTE LOGICA EN PHP)

		//DIAGNOSTICOS
		this.val_audio_od = new Ext.form.TextField({
			fieldLabel: "<b>AUDIOMETRIA OD</b>",
			disabled: true,
			name: "val_audio_od",
			anchor: "95%",
		});
		this.val_audio_oi = new Ext.form.TextField({
			fieldLabel: "<b>AUDIOMETRIA OI</b>",
			disabled: true,
			name: "val_audio_oi",
			anchor: "95%",
		});
		this.val_audio_clas = new Ext.form.TextField({
			fieldLabel: "<b>AUDIOMETRIA CLASIFICACIÓN</b>",
			disabled: true,
			name: "val_audio_clas",
			anchor: "98%",
		});
		this.val_oftalmo = new Ext.form.TextField({
			fieldLabel: "<b>OFTALMOLOGIA</b>",
			disabled: true,
			name: "val_oftalmo",
			anchor: "95%",
		});
		this.val_espiro = new Ext.form.TextField({
			fieldLabel: "<b>ESPIROMETRIA</b>",
			disabled: true,
			name: "val_espiro",
			anchor: "95%",
		});
		this.val_cardio = new Ext.form.TextField({
			fieldLabel: "<b>CARDIOVASCULAR</b>",
			disabled: true,
			name: "val_cardio",
			anchor: "95%",
		});
		this.val_respira = new Ext.form.TextField({
			fieldLabel: "<b>RESPIRATORIO</b>",
			disabled: true,
			name: "val_respira",
			anchor: "95%",
		});
		this.val_osteo = new Ext.form.TextField({
			fieldLabel: "<b>OSTEO MUSCULAR</b>",
			disabled: true,
			name: "val_osteo",
			anchor: "95%",
		});
		this.val_nutri = new Ext.form.TextField({
			fieldLabel: "<b>NUTRICIONAL</b>",
			disabled: true,
			name: "val_nutri",
			anchor: "95%",
		});
		//OTROS DIAGNOSTICOS
		this.tbar1 = new Ext.Toolbar({
			items: [
				'<b style="color:#000000;">OTROS DIAGNOSTICOS</b>',
				"-",
				"->",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.diagnostico.init(null);
					},
				},
			],
		});
		this.dt_grid1 = new Ext.grid.GridPanel({
			store: this.list_diag,
			region: "west",
			border: true,
			tbar: this.tbar1,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			height: 260,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var rec = grid.getStore().getAt(rowIndex);
					mod.medicina.diagnostico.init(rec);
				},
			},
			autoExpandColumn: "diag_desc",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "diag_desc",
					header: "DIAGNOSTICOS",
					dataIndex: "diag_desc",
				},
			],
		});
		//OBSERVACIONES
		this.tbar2 = new Ext.Toolbar({
			items: [
				'<b style="color:#000000;">OBSERVACIONES</b>',
				"-",
				"->",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.observacion.init(null);
					},
				},
			],
		});
		this.dt_grid2 = new Ext.grid.GridPanel({
			store: this.list_obs,
			region: "west",
			border: true,
			tbar: this.tbar2,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			height: 260,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var rec = grid.getStore().getAt(rowIndex);
					mod.medicina.observacion.init(rec);
				},
			},
			autoExpandColumn: "diag",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					header: "OBSERVACIONES",
					width: 430,
					dataIndex: "obs_desc",
				},
				{
					id: "diag",
					header: "PLAZO",
					dataIndex: "obs_plazo",
				},
			],
		});
		//RESTRICCIONES
		this.tbar3 = new Ext.Toolbar({
			items: [
				'<b style="color:#000000;">RESTRICCIONES</b>',
				"-",
				"->",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.restricciones.init(null);
					},
				},
			],
		});
		this.dt_grid3 = new Ext.grid.GridPanel({
			store: this.list_restric,
			region: "west",
			border: true,
			tbar: this.tbar3,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			height: 260,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var rec = grid.getStore().getAt(rowIndex);
					mod.medicina.restricciones.init(rec);
				},
			},
			autoExpandColumn: "diag",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					header: "RESTRICCIONES",
					width: 430,
					dataIndex: "restric_desc",
				},
				{
					id: "diag",
					header: "PLAZO",
					dataIndex: "restric_plazo",
				},
			],
		});
		//INTERCONSULTAS
		this.tbar4 = new Ext.Toolbar({
			items: [
				'<b style="color:#000000;">INTERCONSULTAS</b>',
				"-",
				"->",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.interconsultas.init(null);
					},
				},
			],
		});
		this.dt_grid4 = new Ext.grid.GridPanel({
			store: this.list_inter,
			region: "west",
			border: true,
			tbar: this.tbar4,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			height: 260,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var rec = grid.getStore().getAt(rowIndex);
					mod.medicina.interconsultas.init(rec);
				},
			},
			autoExpandColumn: "diag",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					header: "INTERCONSULTAS",
					width: 430,
					dataIndex: "inter_desc",
				},
				{
					id: "diag",
					header: "PLAZO",
					dataIndex: "inter_plazo",
				},
			],
		});
		//RECOMENDACIONES E INDICACIONES
		this.tbar5 = new Ext.Toolbar({
			items: [
				'<b style="color:#000000;">RECOMENDACIONES E INDICACIONES</b>',
				"-",
				"->",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.recomendaciones.init(null);
					},
				},
			],
		});
		this.dt_grid5 = new Ext.grid.GridPanel({
			store: this.list_recom,
			region: "west",
			border: true,
			tbar: this.tbar5,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			height: 260,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var rec = grid.getStore().getAt(rowIndex);
					mod.medicina.recomendaciones.init(rec);
				},
			},
			autoExpandColumn: "diag",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "diag",
					header: "RECOMENDACIONES E INDICACIONES",
					dataIndex: "recom_desc",
				},
				{
					header: "PLAZO",
					width: 100,
					dataIndex: "recom_plazo",
				},
			],
		});
		//FRM ANEXO 16
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			border: false,
			layout: "accordion",
			layoutConfig: {
				titleCollapse: true,
				animate: true,
				hideCollapseTool: true,
			},
			items: [
				{
					title:
						"<b>--->  ESTADO OCUPACIONAL - RIESGOS OCUPACIONALES - EXAMEN MUJERES - ANTECEDENTES LABORALES - HABITOS - ANTECEDENTES FAMILIARES</b>",
					iconCls: "demo2",
					layout: "column",
					autoScroll: true,
					border: false,
					bodyStyle: "padding:10px 10px 20px 10px;",
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.35,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "PERSONA DE CONTACTO EN CASOS DE ACCIDENTES:",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_contac_nom],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_contac_parent],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_contac_telf],
										},
									],
								},
							],
						},
						{
							columnWidth: 0.33,
							border: false,
							labelAlign: "top",
							layout: "form",
							items: [this.m_med_puesto_postula],
						},
						{
							columnWidth: 0.32,
							border: false,
							labelAlign: "top",
							layout: "form",
							items: [this.m_med_area],
						},
						{
							columnWidth: 0.33,
							border: false,
							labelAlign: "top",
							layout: "form",
							items: [this.m_med_puesto_actual],
						},
						{
							columnWidth: 0.32,
							border: false,
							labelAlign: "top",
							layout: "form",
							items: [this.m_med_eq_opera],
						},
						{
							columnWidth: 0.33,
							border: false,
							labelAlign: "top",
							layout: "form",
							items: [this.m_med_tiempo],
						},
						{
							columnWidth: 0.2,
							border: false,
							labelAlign: "top",
							layout: "form",
							items: [this.m_med_fech_ingreso],
						},
						{
							columnWidth: 0.12,
							border: false,
							labelAlign: "top",
							layout: "form",
							items: [this.m_med_reubicacion],
						},
						{
							columnWidth: 0.2,
							border: false,
							labelAlign: "top",
							layout: "form",
							items: [this.m_med_tip_opera],
						},
						{
							columnWidth: 0.5,
							border: false,
							labelAlign: "top",
							layout: "form",
							items: [this.m_med_minerales],
						},
						{
							columnWidth: 0.3,
							border: false,
							labelAlign: "top",
							layout: "form",
							items: [this.m_med_altura_lab],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "RIESGOS OCUPACIONALES",
									items: [
										{
											columnWidth: 0.2,
											border: false,
											bodyStyle: "padding:10px 0px 0px 0px;",
											labelAlign: "top",
											layout: "form",
											items: [this.m_med_rl_bio1],
										},
										{
											columnWidth: 0.8,
											border: false,
											bodyStyle: "padding:10px 0px 0px 0px;",
											labelAlign: "top",
											layout: "form",
											items: [this.m_med_rl_psico],
										},
										{
											columnWidth: 0.999,
											border: false,
											bodyStyle: "padding:0px 0px 0px 0px;",
											labelAlign: "top",
											layout: "form",
											items: [this.m_med_rl_ergo],
										},
										{
											columnWidth: 0.999,
											border: false,
											bodyStyle: "padding:0px 0px 0px 0px;",
											labelAlign: "top",
											layout: "form",
											items: [this.m_med_rl_fisico],
										},
										{
											columnWidth: 0.999,
											border: false,
											bodyStyle: "padding:0px 0px 0px 0px;",
											labelAlign: "top",
											layout: "form",
											items: [this.m_med_rl_quimi],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:0px 2px 0px 10px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXAMENES DE MUJER:",
									items: [
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_muj_fur],
										},
										{
											columnWidth: 0.11,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_muj_rc],
										},
										{
											columnWidth: 0.11,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_muj_g],
										},
										{
											columnWidth: 0.11,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_muj_p],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_muj_ult_pap],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_muj_resul],
										},
										{
											columnWidth: 0.14,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_muj_mac],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_muj_obs],
										},
										{
											//
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:0px 5px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "FORMULA OBSTETRICA:",
													items: [
														{
															columnWidth: 0.5,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_med_muj_a],
														},
														{
															columnWidth: 0.5,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_med_muj_b],
														},
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_med_muj_c],
														},
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_med_muj_d],
														},
														{
															columnWidth: 0.34,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_med_muj_e],
														},
													],
												},
											],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.2,
							bodyStyle: "padding:2px 5px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "HABITOS",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_tabaco],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_alcohol],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_coca],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.8,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "ANTECEDENTES FAMILIARES",
									items: [
										{
											columnWidth: 0.7,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_fam_papa],
										},
										{
											columnWidth: 0.3,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_fam_hijos],
										},
										{
											columnWidth: 0.7,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_fam_mama],
										},
										{
											columnWidth: 0.3,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_fam_h_vivos],
										},
										{
											columnWidth: 0.7,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_fam_herma],
										},
										{
											columnWidth: 0.3,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_fam_h_muertos],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_fam_infarto55],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_fam_infarto65],
										},
									],
								},
							],
						},
					],
				},
				{
					title: "<b>--->  ANTECEDENTES CARDIOVASCULARES</b>",
					iconCls: "demo2",
					layout: "column",
					autoScroll: true,
					border: false,
					bodyStyle: "padding:10px 10px 20px 10px;",
					labelWidth: 60,
					items: [
						{
							columnWidth: 0.4,
							border: false,
							layout: "form",
							labelAlign: "top",
							bodyStyle: "padding:10px 10px 10px 25px;",
							items: [this.m_med_cardio_op01],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op02],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc02],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op03],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc03],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op04],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc04],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op05],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc05],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op06],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc06],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op07],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc07],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op08],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc08],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op09],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc09],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op10],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc10],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op11],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc11],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op12],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc12],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op13],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc13],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op14],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc14],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op15],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc15],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op16],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc16],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op17],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc17],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									//                                    title: '',
									items: [
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_op18],
										},
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cardio_desc18],
										},
									],
								},
							],
						},
					],
				},
				{
					title: "<b>--->  EXAMEN FISICO</b>",
					iconCls: "demo2",
					layout: "column",
					border: false,
					autoScroll: true,
					bodyStyle: "padding:10px 10px 20px 10px;",
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 25px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "PIEL",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_piel_desc],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_piel_dx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "CABEZA",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cabeza_desc],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cabeza_dx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "CUELLO",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cuello_desc],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_cuello_dx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "NARIZ",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_nariz_desc],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_nariz_dx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "BOCA, AMIGDALAS, FARINGE, LARINGE",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_boca_desc],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_boca_dx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "OIDOS Y TIMPANOS OIDO DERECHO",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_oido_der01],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_oido_der02],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_oido_der03],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_oido_der04],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "OIDOS Y TIMPANOS OIDO IZQUIERDO",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_oido_izq01],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_oido_izq02],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_oido_izq03],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_oido_izq04],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "TORAX",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_torax_desc],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_torax_dx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "CORAZON",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_corazon_desc],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_corazon_dx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "MAMAS",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_mamas_derecho],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_mamas_izquier],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "PULMONES",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_pulmon_desc],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_pulmon_dx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "OSTEO - MUSCULAR",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_osteo_aptitud],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_osteo_desc],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "ABDOMEN",
									items: [
										{
											columnWidth: 0.4,
											border: false,
											layout: "form",
											labelAlign: "top", //m_med_abdomen m_med_abdomen_desc
											items: [this.m_med_abdomen],
										},
										{
											columnWidth: 0.6,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_abdomen_desc],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "ABDOMEN DERECHO",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_pru_sup_der],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_pru_med_der],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_pru_inf_der],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_ppl_der],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "ABDOMEN IZQUIERDO",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_pru_sup_izq],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_pru_med_izq],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_pru_inf_izq],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_ppl_izq],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXAMEN DE TACTO RECTAL",
									items: [
										{
											columnWidth: 0.4,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_tacto],
										},
										{
											columnWidth: 0.6,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_tacto_desc],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXAMEN ANILLOS INGUINALES / CRURALES",
									items: [
										{
											columnWidth: 0.4,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_anillos],
										},
										{
											columnWidth: 0.6,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_anillos_desc],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "HERNIAS",
									items: [
										{
											columnWidth: 0.4,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_hernia],
										},
										{
											columnWidth: 0.6,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_hernia_desc],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "VARICES",
									items: [
										{
											columnWidth: 0.4,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_varices],
										},
										{
											columnWidth: 0.6,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_varices_desc],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "GENITALES (TESTICULOS)",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_genitales_desc],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_genitales_dx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "GANGLIOS",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_ganglios_desc],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_ganglios_dx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 20px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "LENGUAJE, ATENCION, ORIENTACION",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_lenguaje_desc],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_lenguaje_dx],
										},
									],
								},
							],
						},
					],
				},
				{
					title: "<b>--->  VALIDACIÓN</b>",
					iconCls: "demo2",
					layout: "column",
					border: false,
					autoScroll: true,
					bodyStyle: "padding:10px 10px 20px 10px;", //m_med_aptitud
					labelWidth: 60,
					items: [
						{
							columnWidth: 0.999,
							border: false,
							layout: "form",
							labelAlign: "top",
							items: [this.m_med_aptitud],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 25px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "DIAGNOSTICOS",
									items: [
										{
											columnWidth: 0.17,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_med_fech_val],
										},
										{
											columnWidth: 0.83,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.val_audio_clas],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.val_audio_od],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.val_audio_oi],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.val_oftalmo],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.val_espiro],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.val_cardio],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.val_respira],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.val_osteo],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.val_nutri],
										},
									],
								},
							],
						},
						{
							columnWidth: 0.5,
							border: true,
							layout: "form",
							labelAlign: "top",
							items: [this.dt_grid1],
						},
						{
							columnWidth: 0.5,
							border: true,
							layout: "form",
							labelAlign: "top",
							items: [this.dt_grid2],
						},
						{
							columnWidth: 0.5,
							border: true,
							layout: "form",
							labelAlign: "top",
							items: [this.dt_grid3],
						},
						{
							columnWidth: 0.5,
							border: true,
							layout: "form",
							labelAlign: "top",
							items: [this.dt_grid4],
						},
						{
							columnWidth: 0.999,
							border: true,
							layout: "form",
							labelAlign: "top",
							items: [this.dt_grid5],
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
						mod.medicina.nuevoAnexo16.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_nuevoAnexo16"
										: "save_nuevoAnexo16",
								adm: this.record.get("adm"),
								id: this.record.get("id"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								Ext.MessageBox.alert(
									"En hora buena",
									"Se registro correctamente"
								);
								mod.medicina.nuevoAnexo16.win.el.unmask();
								mod.medicina.formatos.st.reload();
								mod.medicina.nuevoAnexo16.win.close();
							},
							failure: function (form, action) {
								mod.medicina.nuevoAnexo16.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.formatos.st.reload();
								mod.medicina.nuevoAnexo16.win.close();
							},
						});
					},
				},
			],
		});
		this.win = new Ext.Window({
			width: 1200,
			height: 630,
			border: false,
			modal: true,
			title: "EXAMEN FORMATO ANEXO 16: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
//MEDICINA 312
mod.medicina.anexo312 = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.crea_controles();

		this.load_medico.load();
		this.load_medico_auditor.load();
		this.list_recom.load();
		if (this.record.get("st") >= 1) {
			this.cargar_data();
		}
		this.win.show();
	},
	llena_conclusiones: function (adm) {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_conclusiones",
				format: "json",
				adm: adm,
				st: this.record.get("st"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	cargar_data: function () {
		Ext.Ajax.request({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			url: "<[controller]>",
			params: {
				acction: "load_anexo312",
				format: "json",
				adm: mod.medicina.anexo312.record.get("adm"),
				exa: mod.medicina.anexo312.record.get("ex_id"),
			},
			success: function (response, opts) {
				var dato = Ext.decode(response.responseText);
				if (dato.success == true) {
					mod.medicina.anexo312.frm.getForm().loadRecord(dato);

					// mod.medicina.anexo312.m_312_medico_ocupa.setValue(
					// 	dato.data.m_312_medico_ocupa
					// );
					// mod.medicina.anexo312.m_312_medico_ocupa.setRawValue(
					// 	dato.data.m_312_medico_ocupa_nom
					// );
					// mod.medicina.anexo312.load_medico.load();

					// mod.medicina.anexo312.adm_emp.setValue(dato.data.emp_id);
					// mod.medicina.anexo312.adm_emp.setRawValue(dato.data.empresa);
					// mod.medicina.anexo312.st_empre.load();

					// mod.medicina.anexo312.adm_pac.setValue(dato.data.pac_id);
					// mod.medicina.anexo312.adm_pac.setRawValue(dato.data.adm_pac);

					// mod.medicina.anexo312.desc.setValue(dato.data.pk_desc);
					// mod.medicina.anexo312.tficha.setValue(dato.data.tfi_desc);
				}
			},
		});
		// this.frm.getForm().load({
		// 	waitMsg: "Recuperando Informacion...Carga data",
		// 	waitTitle: "Espere",
		// 	params: {
		// 		acction: "load_anexo312",
		// 		format: "json",
		// 		adm: mod.medicina.anexo312.record.get("adm"),
		// 		exa: mod.medicina.anexo312.record.get("ex_id"),
		// 	},
		// 	scope: this,
		// 	success: function (frm, action) {
		// 		r = action.result.data;
		// 		this.m_312_medico_ocupa.setValue(r.m_312_medico_ocupa);
		// 		this.m_312_medico_ocupa.setRawValue(r.m_312_medico_ocupa_nom);
		// 		this.load_medico.load();
		// 	},
		// });
	},
	crea_stores: function () {
		this.list_cie10 = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_cie10",
				format: "json",
			},
			fields: ["cie4_id", "cie4_cie3id", "cie4_desc"],
			root: "data",
		});
		this.load_medico = new Ext.data.JsonStore({
			remoteSort: true,
			url: "<[controller]>",
			baseParams: { acction: "load_medico", format: "json" },
			root: "data",
			totalProperty: "total",
			fields: ["medico_id", "nombre"],
		});
		this.load_medico_auditor = new Ext.data.JsonStore({
			remoteSort: true,
			url: "<[controller]>",
			baseParams: { acction: "load_medico_auditor", format: "json" },
			root: "data",
			totalProperty: "total",
			fields: ["medico_id", "nombre"],
		});
		this.list_recom = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_recom",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: ["recom_id", "recom_adm", "recom_desc", "recom_plazo"],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm = mod.medicina.anexo312.record.get("adm");
				},
			},
		});
	},
	crea_controles: function () {
		// m_312_residencia
		this.m_312_residencia = new Ext.form.RadioGroup({
			fieldLabel: "<b>Residencia en lugar de trabajo</b>",
			//            itemCls: 'x-check-group-alt',
			//            columns: 1,
			items: [
				{
					boxLabel: "Si",
					name: "m_312_residencia",
					inputValue: "Si",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.anexo312.m_312_tiempo.enable();
						} else if (checkbox == false) {
							mod.medicina.anexo312.m_312_tiempo.disable();
							mod.medicina.anexo312.m_312_tiempo.reset();
						}
					},
				},
				{
					boxLabel: "No",
					name: "m_312_residencia",
					inputValue: "No",
					checked: true,
				},
			],
		});
		// m_312_tiempo
		this.m_312_tiempo = new Ext.form.TextField({
			fieldLabel: "Tiempo de residencia(años)",
			name: "m_312_tiempo",
			minLength: 1,
			disabled: true,
			allowBlank: false,
			autoCreate: {
				//restricts user to 20 chars max, cannot enter 21st char
				tag: "input",
				maxlength: 2,
				minLength: 1,
				type: "text",
				size: "2",
				autocomplete: "off",
			},
			anchor: "95%",
		});

		// m_312_seguro
		this.m_312_seguro = new Ext.form.RadioGroup({
			fieldLabel: "<b>Seguro</b>",
			//            itemCls: 'x-check-group-alt',
			//            columns: 1,
			items: [
				{
					boxLabel: "ESSALUD",
					name: "m_312_seguro",
					inputValue: "ESSALUD",
				},
				{ boxLabel: "EPS", name: "m_312_seguro", inputValue: "EPS" },
				{ boxLabel: "SCTR", name: "m_312_seguro", inputValue: "SCTR" },
				{ boxLabel: "SIS", name: "m_312_seguro", inputValue: "SIS" },
				{ boxLabel: "OTRO", name: "m_312_seguro", inputValue: "OTRO" },
				{
					boxLabel: "NIEGA",
					name: "m_312_seguro",
					inputValue: "NIEGA",
					checked: true,
				},
			],
		});

		// m_312_nhijos
		this.m_312_nhijos = new Ext.form.TextField({
			fieldLabel: "N° total de hijos vivos",
			name: "m_312_nhijos",
			minLength: 1,
			autoCreate: {
				//restricts user to 20 chars max, cannot enter 21st char
				tag: "input",
				maxlength: 2,
				minLength: 1,
				type: "text",
				size: "2",
				autocomplete: "off",
			},
			anchor: "95%",
		});
		// m_312_dependiente
		this.m_312_dependiente = new Ext.form.TextField({
			fieldLabel: "N° Dependientes",
			name: "m_312_dependiente",
			minLength: 1,
			autoCreate: {
				//restricts user to 20 chars max, cannot enter 21st char
				tag: "input",
				maxlength: 2,
				minLength: 1,
				type: "text",
				size: "2",
				autocomplete: "off",
			},
			anchor: "95%",
		});

		this.m_312_pato = new Ext.form.CheckboxGroup({
			// fieldLabel: "<b>PATOLOGIAS|ENFERMEDADES</b>",
			itemCls: "x-check-group-alt",
			columns: 7,
			items: [
				{ boxLabel: "<b>IMA</b>", name: "m_312_pato_ima", inputValue: "1" },
				{ boxLabel: "<b>HTA</b>", name: "m_312_pato_hta", inputValue: "1" },
				{ boxLabel: "<b>ACV</b>", name: "m_312_pato_acv", inputValue: "1" },
				{
					boxLabel: "<b>TBC</b>",
					name: "m_312_pato_tbc",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.anexo312.m_312_pato_tbc_fecha.enable();
							mod.medicina.anexo312.m_312_pato_tbc_tratamiento.enable();
						} else if (checkbox == false) {
							mod.medicina.anexo312.m_312_pato_tbc_fecha.disable();
							mod.medicina.anexo312.m_312_pato_tbc_fecha.reset();
							mod.medicina.anexo312.m_312_pato_tbc_tratamiento.disable();
							mod.medicina.anexo312.m_312_pato_tbc_tratamiento.reset();
						}
					},
				},
				{ boxLabel: "<b>ETS</b>", name: "m_312_pato_ets", inputValue: "1" },
				{ boxLabel: "<b>VIH</b>", name: "m_312_pato_vih", inputValue: "1" },
				{ boxLabel: "<b>TEC</b>", name: "m_312_pato_tec", inputValue: "1" },
				{ boxLabel: "<b>ASMA</b>", name: "m_312_pato_asma", inputValue: "1" },
				{
					boxLabel: "<b>BRONQUITIS</b>",
					name: "m_312_pato_bronquitis",
					inputValue: "1",
				},
				{
					boxLabel: "<b>DIABETES</b>",
					name: "m_312_pato_diabetes",
					inputValue: "1",
				},
				{
					boxLabel: "<b>HEPATITIS</b>",
					name: "m_312_pato_hepatitis",
					inputValue: "1",
				},
				{
					boxLabel: "<b>HERNIA</b>",
					name: "m_312_pato_hernia",
					inputValue: "1",
				},
				{
					boxLabel: "<b>LUMBALGIA</b>",
					name: "m_312_pato_lumbalgia",
					inputValue: "1",
				},
				{
					boxLabel: "<b>TIFOIDEA</b>",
					name: "m_312_pato_tifoidea",
					inputValue: "1",
				},
				{
					boxLabel: "<b>NEOPLASIAS</b>",
					name: "m_312_pato_neoplasias",
					inputValue: "1",
				},
				{
					boxLabel: "<b>QUEMADURAS</b>",
					name: "m_312_pato_quemaduras",
					inputValue: "1",
				},
				{
					boxLabel: "<b>DISCOPATIAS</b>",
					name: "m_312_pato_discopatias",
					inputValue: "1",
				},
				{
					boxLabel: "<b>CONVULCIONES</b>",
					name: "m_312_pato_convulciones",
					inputValue: "1",
				},
				{
					boxLabel: "<b>GASTRITIS</b>",
					name: "m_312_pato_gastritis",
					inputValue: "1",
				},
				{
					boxLabel: "<b>ULCERAS</b>",
					name: "m_312_pato_ulceras",
					inputValue: "1",
				},
				{
					boxLabel: "<b>ENF PSIQUIAT.</b>",
					name: "m_312_pato_enf_psiquia",
					inputValue: "1",
				},
				{
					boxLabel: "<b>ENF CARDIOV.</b>",
					name: "m_312_pato_enf_cardio",
					inputValue: "1",
				},
				{
					boxLabel: "<b>ENF OCULAR</b>",
					name: "m_312_pato_enf_ocular",
					inputValue: "1",
				},
				{
					boxLabel: "<b>ENF REUMAT</b>",
					name: "m_312_pato_enf_reuma",
					inputValue: "1",
				},
				{
					boxLabel: "<b>ENF PULMONARES</b>",
					name: "m_312_pato_enf_pulmon",
					inputValue: "1",
				},
				{
					boxLabel: "<b>ALT DE LA PIEL</b>",
					name: "m_312_pato_alt_piel",
					inputValue: "1",
				},
				{
					boxLabel: "<b>TENDINITIS</b>",
					name: "m_312_pato_tendinitis",
					inputValue: "1",
				},
				{
					boxLabel: "<b>FRACTURA</b>",
					name: "m_312_pato_fractura",
					inputValue: "1",
				},
				{
					boxLabel: "<b>ANEMIA</b>",
					name: "m_312_pato_anemia",
					inputValue: "1",
				},
				{
					boxLabel: "<b>OBESIDAD</b>",
					name: "m_312_pato_obesidad",
					inputValue: "1",
				},
				{
					boxLabel: "<b>ALERGIAS</b>",
					name: "m_312_pato_alergias",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.anexo312.m_312_pato_alergias_desc.enable();
						} else if (checkbox == false) {
							mod.medicina.anexo312.m_312_pato_alergias_desc.disable();
							mod.medicina.anexo312.m_312_pato_alergias_desc.reset();
						}
					},
				},
				{
					boxLabel: "<b>DISLIPIDEMIA</b>",
					name: "m_312_pato_dislipidem",
					inputValue: "1",
				},
				{
					boxLabel: "<b>INTOXICACIONES</b>",
					name: "m_312_pato_intoxica",
					inputValue: "1",
				},
				{
					boxLabel: "<b>CIRUGIAS</b>",
					name: "m_312_pato_cirugia",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.anexo312.m_312_pato_cirugia_desc.enable();
						} else if (checkbox == false) {
							mod.medicina.anexo312.m_312_pato_cirugia_desc.disable();
							mod.medicina.anexo312.m_312_pato_cirugia_desc.reset();
						}
					},
				},
				{ boxLabel: "<b>OTROS</b>", name: "m_312_pato_otros", inputValue: "1" },
			],
		});

		// m_312_pato_cirugia_desc
		this.m_312_pato_cirugia_desc = new Ext.form.TextArea({
			name: "m_312_pato_cirugia_desc",
			fieldLabel: "<b>CIRUGIAS REALIZADAS</b>",
			anchor: "95%",
			disabled: true,
			value: "NIEGA",
			height: 50,
		});
		// m_312_pato_tbc_fecha
		this.m_312_pato_tbc_fecha = new Ext.form.DateField({
			name: "m_312_pato_tbc_fecha",
			fieldLabel: "<b>FECHA QUE TUVO TUBERCULOSIS</b>",
			allowBlank: false,
			disabled: true,
			anchor: "90%",
			format: "d-m-Y",
			emptyText: "Dia-Mes-Año",
		});
		// m_312_pato_tbc_tratamiento
		this.m_312_pato_tbc_tratamiento = new Ext.form.TextField({
			fieldLabel: "<b>¿COMPLETO SU TRATAMIENTO?</b>",
			name: "m_312_pato_tbc_tratamiento",
			disabled: true,
			anchor: "95%",
		});
		// m_312_pato_alergias_desc
		this.m_312_pato_alergias_desc = new Ext.form.TextArea({
			name: "m_312_pato_alergias_desc",
			disabled: true,
			fieldLabel: "<b>ESPECIFICAR ALERGIAS</b>",
			value: "NIEGA",
			anchor: "95%",
			height: 50,
		});
		// m_312_pato_observaciones
		this.m_312_pato_observaciones = new Ext.form.TextArea({
			name: "m_312_pato_observaciones",
			fieldLabel: "<b>OBSERVACIONES</b>",
			anchor: "95%",
			height: 50,
		});

		// m_312_alcohol_tipo
		this.m_312_alcohol_tipo = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["-", "-"],
					["CALIENTITO", "CALIENTITO"],
					["CERVEZA", "CERVEZA"],
					["PISCO", "PISCO"],
					["RON", "RON"],
					["YONKE", "YONKE"],
					["OTRO", "OTRO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			fieldLabel: "<b>ALCOHOL TIPO</b>",
			hiddenName: "m_312_alcohol_tipo",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		// m_312_alcohol_cantidad
		this.m_312_alcohol_cantidad = new Ext.form.TextField({
			fieldLabel: "<b>CANTIDAD</b>",
			name: "m_312_alcohol_cantidad",
			value: "-",
			anchor: "95%",
		});
		// m_312_alcohol_fre
		this.m_312_alcohol_fre = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NADA", "NADA"],
					["POCO", "POCO"],
					["HABITUAL", "HABITUAL"],
					["EXCESIVO", "EXCESIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			fieldLabel: "<b>ALCOHOL FRECUENCIA</b>",
			hiddenName: "m_312_alcohol_fre",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NADA");
					descripcion.setRawValue("NADA");
				},
			},
		});
		// m_312_tabaco_tipo
		this.m_312_tabaco_tipo = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["-", "-"],
					["CIGARRO", "CIGARRO"],
					["PURO", "PURO"],
					["PIPA", "PIPA"],
					["OTRO", "OTRO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_312_tabaco_tipo",
			fieldLabel: "<b>TABACO TIPO</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		// m_312_tabaco_cantidad
		this.m_312_tabaco_cantidad = new Ext.form.TextField({
			fieldLabel: "<b>CANTIDAD</b>",
			name: "m_312_tabaco_cantidad",
			value: "-",
			anchor: "95%",
		});
		// m_312_tabaco_fre
		this.m_312_tabaco_fre = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NADA", "NADA"],
					["POCO", "POCO"],
					["HABITUAL", "HABITUAL"],
					["EXCESIVO", "EXCESIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			fieldLabel: "<b>TABACO FRECUENCIA</b>",
			hiddenName: "m_312_tabaco_fre",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NADA");
					descripcion.setRawValue("NADA");
				},
			},
		});
		// m_312_drogas_tipo
		this.m_312_drogas_tipo = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["-", "-"],
					["ANFETAMINA", "ANFETAMINA"],
					["BZD", "BZD"],
					["COCAINA", "COCAINA"],
					["EXTASIS", "EXTASIS"],
					["MARIHUANA", "MARIHUANA"],
					["OTROS", "OTROS"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			fieldLabel: "<b>DROGAS TIPO</b>",
			hiddenName: "m_312_drogas_tipo",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		// m_312_drogas_cantidad
		this.m_312_drogas_cantidad = new Ext.form.TextField({
			fieldLabel: "<b>CANTIDAD</b>",
			name: "m_312_drogas_cantidad",
			value: "-",
			anchor: "95%",
		});
		// m_312_drogas_fre
		this.m_312_drogas_fre = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NADA", "NADA"],
					["POCO", "POCO"],
					["HABITUAL", "HABITUAL"],
					["EXCESIVO", "EXCESIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			fieldLabel: "<b>DROGAS FRECUENCIA</b>",
			hiddenName: "m_312_drogas_fre",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NADA");
					descripcion.setRawValue("NADA");
				},
			},
		});
		// m_312_medicamentos
		this.m_312_medicamentos = new Ext.form.TextArea({
			name: "m_312_medicamentos",
			fieldLabel: "<b>MEDICAMENTOS</b>",
			value: "NIEGA",
			anchor: "98%",
			height: 115,
		});
		// m_312_padre
		this.m_312_padre = new Ext.form.TextField({
			fieldLabel: "<b>ESTADO DEL PADRE</b>",
			name: "m_312_padre",
			anchor: "95%",
		});
		// m_312_madre
		this.m_312_madre = new Ext.form.TextField({
			fieldLabel: "<b>ESTADO DE LA MADRE</b>",
			name: "m_312_madre",
			anchor: "95%",
		});
		// m_312_conyuge
		this.m_312_conyuge = new Ext.form.TextField({
			fieldLabel: "<b>ESTADO DEL CONYUGE</b>",
			name: "m_312_conyuge",
			anchor: "95%",
		});
		// m_312_hijo_vivo
		this.m_312_hijo_vivo = new Ext.form.TextField({
			fieldLabel: "<b>N° HIJOS VIVOS</b>",
			name: "m_312_hijo_vivo",
			minLength: 1,
			autoCreate: {
				//restricts user to 20 chars max, cannot enter 21st char
				tag: "input",
				maxlength: 2,
				minLength: 1,
				type: "text",
				size: "2",
				autocomplete: "off",
			},
			anchor: "95%",
		});
		// m_312_hijo_fallecido
		this.m_312_hijo_fallecido = new Ext.form.TextField({
			fieldLabel: "<b>N° HIJOS FALLECIDOS</b>",
			name: "m_312_hijo_fallecido",
			minLength: 1,
			autoCreate: {
				//restricts user to 20 chars max, cannot enter 21st char
				tag: "input",
				maxlength: 2,
				minLength: 1,
				type: "text",
				size: "2",
				autocomplete: "off",
			},
			anchor: "95%",
		});

		// m_312_anamnesis
		this.m_312_anamnesis = new Ext.form.TextArea({
			name: "m_312_anamnesis",
			fieldLabel: "<b>ANAMNESIS</b>",
			value: "NO REFIERE SINTOMAS AL MOMENTO DEL EXAMEN",
			anchor: "97%",
			height: 50,
		});
		// m_312_ectoscopia
		this.m_312_ectoscopia = new Ext.form.TextArea({
			name: "m_312_ectoscopia",
			fieldLabel: "<b>ECTOSCOPIA</b>",
			value: "NORMAL",
			anchor: "97%",
			height: 50,
		});
		// m_312_est_mental
		this.m_312_est_mental = new Ext.form.TextArea({
			name: "m_312_est_mental",
			fieldLabel: "<b>ESTADO MENTAL</b>",
			value: "LOTEP",
			anchor: "97%",
			height: 50,
		});

		// m_312_piel
		this.m_312_piel = new Ext.form.TextArea({
			name: "m_312_piel",
			fieldLabel: "<b>PIEL</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_cabeza
		this.m_312_cabeza = new Ext.form.TextArea({
			name: "m_312_cabeza",
			fieldLabel: "<b>CABEZA</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_oidos
		this.m_312_oidos = new Ext.form.TextArea({
			name: "m_312_oidos",
			fieldLabel: "<b>OIDOS</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_nariz
		this.m_312_nariz = new Ext.form.TextArea({
			name: "m_312_nariz",
			fieldLabel: "<b>NARIZ</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_boca
		this.m_312_boca = new Ext.form.TextArea({
			name: "m_312_boca",
			fieldLabel: "<b>BOCA</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_faringe
		this.m_312_faringe = new Ext.form.TextArea({
			name: "m_312_faringe",
			fieldLabel: "<b>FARINGE</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_cuello
		this.m_312_cuello = new Ext.form.TextArea({
			name: "m_312_cuello",
			fieldLabel: "<b>CUELLO</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_respiratorio
		this.m_312_respiratorio = new Ext.form.TextArea({
			name: "m_312_respiratorio",
			fieldLabel: "<b>AP. RESPIRATORIO</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_cardiovascular
		this.m_312_cardiovascular = new Ext.form.TextArea({
			name: "m_312_cardiovascular",
			fieldLabel: "<b>AP. CARDIOVASCULAR</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_digestivo
		this.m_312_digestivo = new Ext.form.TextArea({
			name: "m_312_digestivo",
			fieldLabel: "<b>AP. DIGESTIVO</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_genitou
		this.m_312_genitou = new Ext.form.TextArea({
			name: "m_312_genitou",
			fieldLabel: "<b>AP. GENITOURINARIO</b>",
			value: "DIFERIDO",
			anchor: "95%",
			height: 50,
		});
		// m_312_locomotor
		this.m_312_locomotor = new Ext.form.TextArea({
			name: "m_312_locomotor",
			fieldLabel: "<b>LOCOMOTOR</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_marcha
		this.m_312_marcha = new Ext.form.TextArea({
			name: "m_312_marcha",
			fieldLabel: "<b>MARCHA</b>",
			value: "CONSERVADO",
			anchor: "95%",
			height: 50,
		});
		// m_312_columna
		this.m_312_columna = new Ext.form.TextArea({
			name: "m_312_columna",
			fieldLabel: "<b>COLUMNA</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_mi_superi
		this.m_312_mi_superi = new Ext.form.TextArea({
			name: "m_312_mi_superi",
			fieldLabel: "<b>MIEMBRO SUPERIOR</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_mi_inferi
		this.m_312_mi_inferi = new Ext.form.TextArea({
			name: "m_312_mi_inferi",
			fieldLabel: "<b>MIEMBRO INFERIOR</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_linfatico
		this.m_312_linfatico = new Ext.form.TextArea({
			name: "m_312_linfatico",
			fieldLabel: "<b>SIS. LINFATICO</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_nervio
		this.m_312_nervio = new Ext.form.TextArea({
			name: "m_312_nervio",
			fieldLabel: "<b>SIS. NERVIOSO</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_osteomuscular
		this.m_312_osteomuscular = new Ext.form.TextArea({
			name: "m_312_osteomuscular",
			fieldLabel: "<b>OSTEOMUSCULAR</b>",
			value: "NORMAL",
			anchor: "95%",
			height: 50,
		});
		// m_312_ef_observaciones
		this.m_312_ef_observaciones = new Ext.form.TextArea({
			name: "m_312_ef_observaciones",
			fieldLabel: "<b>OBSERVACIONES</b>",
			anchor: "95%",
			height: 50,
		});

		// m_312_conclu_psico
		this.m_312_conclu_psico = new Ext.form.TextArea({
			name: "m_312_conclu_psico",
			fieldLabel: "<b>CONCLUSION PSICOLOGIA</b>",
			anchor: "95%",
			height: 50,
		});
		// m_312_conclu_rx
		this.m_312_conclu_rx = new Ext.form.TextArea({
			name: "m_312_conclu_rx",
			fieldLabel: "<b>CONCLUSION RAYOS X</b>",
			anchor: "95%",
			height: 50,
		});
		// m_312_conclu_lab
		this.m_312_conclu_lab = new Ext.form.TextArea({
			name: "m_312_conclu_lab",
			fieldLabel: "<b>CONCLUSION LABORATORIO</b>",
			anchor: "95%",
			height: 50,
		});
		// m_312_conclu_audio
		this.m_312_conclu_audio = new Ext.form.TextArea({
			name: "m_312_conclu_audio",
			fieldLabel: "<b>CONCLUSION AUDIOMETRIA</b>",
			anchor: "95%",
			height: 50,
		});
		// m_312_conclu_espiro
		this.m_312_conclu_espiro = new Ext.form.TextArea({
			name: "m_312_conclu_espiro",
			fieldLabel: "<b>CONCLUSION ESPIROMETRIA</b>",
			anchor: "95%",
			height: 50,
		});
		// m_312_conclu_otros
		this.m_312_conclu_otros = new Ext.form.TextArea({
			name: "m_312_conclu_otros",
			fieldLabel: "<b>OTRAS CONCLUSIONES</b>",
			anchor: "95%",
			height: 50,
		});

		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"{cie4_id}",
			"<h3><span><p>{cie4_desc}</p></span></h3>",
			"</div>",
			"</div></tpl>"
		);

		// m_312_diag_cie1
		this.m_312_diag_cie1 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_312_diag_cie1",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		// m_312_diag_st1
		this.m_312_diag_st1 = new Ext.form.RadioGroup({
			fieldLabel: "<b> </b>",
			//            itemCls: 'x-check-group-alt',
			//            columns: 1,
			items: [
				{ boxLabel: "P", name: "m_312_diag_st1", inputValue: "P" },
				{ boxLabel: "D", name: "m_312_diag_st1", inputValue: "D" },
				{ boxLabel: "R", name: "m_312_diag_st1", inputValue: "R" },
			],
		});
		// m_312_diag_cie2
		this.m_312_diag_cie2 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_312_diag_cie2",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		// m_312_diag_st2
		this.m_312_diag_st2 = new Ext.form.RadioGroup({
			fieldLabel: "<b> </b>",
			//            itemCls: 'x-check-group-alt',
			//            columns: 1,
			items: [
				{ boxLabel: "P", name: "m_312_diag_st2", inputValue: "P" },
				{ boxLabel: "D", name: "m_312_diag_st2", inputValue: "D" },
				{ boxLabel: "R", name: "m_312_diag_st2", inputValue: "R" },
			],
		});
		// m_312_diag_cie3
		this.m_312_diag_cie3 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_312_diag_cie3",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		// m_312_diag_st3
		this.m_312_diag_st3 = new Ext.form.RadioGroup({
			fieldLabel: "<b> </b>",
			//            itemCls: 'x-check-group-alt',
			//            columns: 1,
			items: [
				{ boxLabel: "P", name: "m_312_diag_st3", inputValue: "P" },
				{ boxLabel: "D", name: "m_312_diag_st3", inputValue: "D" },
				{ boxLabel: "R", name: "m_312_diag_st3", inputValue: "R" },
			],
		});
		// m_312_diag_cie4
		this.m_312_diag_cie4 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_312_diag_cie4",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		// m_312_diag_st4
		this.m_312_diag_st4 = new Ext.form.RadioGroup({
			fieldLabel: "<b> </b>",
			//            itemCls: 'x-check-group-alt',
			//            columns: 1,
			items: [
				{ boxLabel: "P", name: "m_312_diag_st4", inputValue: "P" },
				{ boxLabel: "D", name: "m_312_diag_st4", inputValue: "D" },
				{ boxLabel: "R", name: "m_312_diag_st4", inputValue: "R" },
			],
		});
		// m_312_diag_cie5
		this.m_312_diag_cie5 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_312_diag_cie5",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		// m_312_diag_st5
		this.m_312_diag_st5 = new Ext.form.RadioGroup({
			fieldLabel: "<b> </b>",
			//            itemCls: 'x-check-group-alt',
			//            columns: 1,
			items: [
				{ boxLabel: "P", name: "m_312_diag_st5", inputValue: "P" },
				{ boxLabel: "D", name: "m_312_diag_st5", inputValue: "D" },
				{ boxLabel: "R", name: "m_312_diag_st5", inputValue: "R" },
			],
		});
		// m_312_diag_cie6
		this.m_312_diag_cie6 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_312_diag_cie6",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		// m_312_diag_st6
		this.m_312_diag_st6 = new Ext.form.RadioGroup({
			fieldLabel: "<b> </b>",
			//            itemCls: 'x-check-group-alt',
			//            columns: 1,
			items: [
				{ boxLabel: "P", name: "m_312_diag_st6", inputValue: "P" },
				{ boxLabel: "D", name: "m_312_diag_st6", inputValue: "D" },
				{ boxLabel: "R", name: "m_312_diag_st6", inputValue: "R" },
			],
		});
		// m_312_diag_cie7
		this.m_312_diag_cie7 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_312_diag_cie7",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		// m_312_diag_st7
		this.m_312_diag_st7 = new Ext.form.RadioGroup({
			fieldLabel: "<b> </b>",
			//            itemCls: 'x-check-group-alt',
			//            columns: 1,
			items: [
				{ boxLabel: "P", name: "m_312_diag_st7", inputValue: "P" },
				{ boxLabel: "D", name: "m_312_diag_st7", inputValue: "D" },
				{ boxLabel: "R", name: "m_312_diag_st7", inputValue: "R" },
			],
		});
		// m_312_diag_cie8
		this.m_312_diag_cie8 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_312_diag_cie8",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		// m_312_diag_st8
		this.m_312_diag_st8 = new Ext.form.RadioGroup({
			fieldLabel: "<b> </b>",
			//            itemCls: 'x-check-group-alt',
			//            columns: 1,
			items: [
				{ boxLabel: "P", name: "m_312_diag_st8", inputValue: "P" },
				{ boxLabel: "D", name: "m_312_diag_st8", inputValue: "D" },
				{ boxLabel: "R", name: "m_312_diag_st8", inputValue: "R" },
			],
		});

		// m_312_medico_ocupa
		this.m_312_medico_ocupa = new Ext.form.ComboBox({
			typeAhead: true,
			triggerAction: "all",
			lazyRender: true,
			allowBlank: false,
			mode: "local",
			store: this.load_medico,
			forceSelection: true,
			hiddenName: "m_312_medico_ocupa",
			fieldLabel: "<b>MÉDICO EVALUADOR</b>",
			name: "m_312_medico_ocupa",
			valueField: "medico_id",
			displayField: "nombre",
			anchor: "90%",
		});
		// m_312_medico_auditor
		this.m_312_medico_auditor = new Ext.form.ComboBox({
			typeAhead: true,
			triggerAction: "all",
			lazyRender: true,
			allowBlank: false,
			mode: "local",
			store: this.load_medico_auditor,
			forceSelection: true,
			hiddenName: "m_312_medico_auditor",
			fieldLabel: "<b>MÉDICO AUDITOR</b>",
			name: "m_312_medico_auditor",
			valueField: "medico_id",
			displayField: "nombre",
			anchor: "90%",
		});

		this.m_312_restricciones = new Ext.form.TextArea({
			name: "m_312_restricciones",
			fieldLabel: "<b>RESTRICCIONES</b>",
			anchor: "95%",
			height: 120,
		});
		this.m_312_observaciones = new Ext.form.TextArea({
			name: "m_312_observaciones",
			fieldLabel: "<b>OBSERVACIONES</b>",
			anchor: "95%",
			height: 120,
		});

		this.m_312_aptitud = new Ext.form.RadioGroup({
			fieldLabel: "<b>APTITUD</b>",
			itemCls: "x-check-group-alt",
			columns: 8,
			items: [
				{ boxLabel: "APTO", name: "m_312_aptitud", inputValue: "APTO" },
				{
					boxLabel: "APTO CON RESTRICCIONES",
					name: "m_312_aptitud",
					inputValue: "APTO CON RESTRICCIONES",
				},
				{
					boxLabel: "APTO CON OBSERVACIONES",
					name: "m_312_aptitud",
					inputValue: "APTO CON OBSERVACIONES",
				},
				{ boxLabel: "NO APTO", name: "m_312_aptitud", inputValue: "NO APTO" },
				{
					boxLabel: "NO APTO TEMPORAL",
					name: "m_312_aptitud",
					inputValue: "NO APTO TEMPORAL",
				},
				{ boxLabel: "RETIRO", name: "m_312_aptitud", inputValue: "RETIRO" },
				{
					boxLabel: "OBSERVADO",
					name: "m_312_aptitud",
					inputValue: "OBSERVADO",
				},
				{
					boxLabel: "EN PROCESO DE VALIDACION",
					name: "m_312_aptitud",
					inputValue: "EN PROCESO DE VALIDACION",
					checked: true,
				},
			],
		});

		this.m_312_fech_val = new Ext.form.DateField({
			fieldLabel: "<b>FECHA INICIO</b>",
			name: "m_312_fech_val",
			id: "startdt2",
			vtype: "daterange",
			endDateField: "enddt2",
			format: "Y-m-d",
			anchor: "90%",
			listeners: {
				render: function (datefield) {
					datefield.setValue(new Date());
					mod.medicina.anexo312.m_312_time_aptitud.setValue("0");
				},
			},
		});
		this.m_312_fech_vence = new Ext.form.DateField({
			fieldLabel: "<b>FECHA TERMINO</b>",
			name: "m_312_fech_vence",
			readOnly: true,
			id: "enddt2",
			vtype: "daterange",
			startDateField: "startdt2",
			format: "Y-m-d",
			anchor: "90%",
			listeners: {
				render: function (datefield) {
					datefield.setValue(new Date());
				},
			},
		});
		this.m_312_time_aptitud = new Ext.form.NumberField({
			fieldLabel: "<b>TIEMPO DE APTITUD LABORAL</b>",
			name: "m_312_time_aptitud",
			id: "m_312_time_aptitud",
			allowBlank: false,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 4,
				minLength: 1,
				type: "text",
				size: "4",
				autocomplete: "off",
			},
			anchor: "90%",
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						keyup: function (event, target, scope) {
							var editorObject = scope;
							var da = parseInt(target.value);
							var t = Ext.getCmp("startdt2").getValue();
							t.setDate(t.getDate() + da);
							da >= 0
								? Ext.getCmp("enddt2").setValue(t)
								: Ext.getCmp("enddt2").setValue(
										Ext.getCmp("startdt2").getValue()
								  );
						},
						scope: editorObject,
					});
				},
			},
		});
		this.val_medico = new Ext.form.ComboBox({
			typeAhead: true,
			triggerAction: "all",
			lazyRender: true,
			allowBlank: false,
			mode: "local",
			store: this.load_medico,
			forceSelection: true,
			hiddenName: "val_medico",
			fieldLabel: "MÉDICO EVALUADOR",
			name: "val_medico",
			valueField: "medico_id",
			displayField: "nombre",
			anchor: "95%",
		});

		//RECOMENDACIONES E INDICACIONES
		this.tbar5 = new Ext.Toolbar({
			items: [
				'<b style="color:#000000;">RECOMENDACIONES E INDICACIONES</b>',
				"-",
				"->",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.recomen_312.init(null);
					},
				},
			],
		});
		this.dt_grid5 = new Ext.grid.GridPanel({
			store: this.list_recom,
			region: "west",
			border: true,
			tbar: this.tbar5,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			height: 260,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var rec = grid.getStore().getAt(rowIndex);
					mod.medicina.recomen_312.init(rec);
				},
			},
			autoExpandColumn: "diag",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "diag",
					header: "RECOMENDACIONES E INDICACIONES",
					dataIndex: "recom_desc",
				},
				{
					header: "PLAZO",
					width: 100,
					dataIndex: "recom_plazo",
				},
			],
		});

		/////////////////////////////////////////////////////////////////////////
		//CONCLUSIONES

		this.psicologia = new Ext.form.TextArea({
			name: "psicologia",
			fieldLabel:
				"<b>PSICOLOGIA *(solo es para informacion no se imprimira en el formato 312)</b>",
			readOnly: true,
			anchor: "95%",
			height: 50,
		});
		this.rx = new Ext.form.TextArea({
			name: "rx",
			fieldLabel:
				"<b>RAYOS X *(solo es para informacion no se imprimira en el formato 312)</b>",
			readOnly: true,
			anchor: "95%",
			height: 50,
		});
		this.laboratorio = new Ext.form.TextArea({
			name: "laboratorio",
			fieldLabel:
				"<b>LABORATORIO *(solo es para informacion no se imprimira en el formato 312)</b>",
			readOnly: true,
			anchor: "95%",
			height: 50,
		});
		this.audiometria = new Ext.form.TextArea({
			name: "audiometria",
			fieldLabel:
				"<b>AUDIOMETRIA *(solo es para informacion no se imprimira en el formato 312)</b>",
			readOnly: true,
			anchor: "95%",
			height: 50,
		});
		this.espirometia = new Ext.form.TextArea({
			name: "espirometia",
			fieldLabel:
				"<b>ESPIROMETRIA *(solo es para informacion no se imprimira en el formato 312)</b>",
			readOnly: true,
			anchor: "95%",
			height: 50,
		});
		this.otros = new Ext.form.TextArea({
			name: "otros",
			fieldLabel:
				"<b>OTROS *(solo es para informacion no se imprimira en el formato 312)</b>",
			readOnly: true,
			anchor: "95%",
			height: 50,
		});

		//FRM ANEXO 16
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			border: false,
			layout: "accordion",
			layoutConfig: {
				titleCollapse: true,
				animate: true,
				hideCollapseTool: true,
			},
			items: [
				{
					title:
						"<b>--->  ANTECEDENTES PERSONALES || PATOLOGIAS || ENFERMEDADES || HABITOS NOCIBOS || FAMILIARES</b>",
					iconCls: "demo2",
					layout: "column",
					border: false,
					autoScroll: true,
					bodyStyle: "padding:10px 10px 20px 10px;", //m_med_aptitud
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									// title: "PERSONA DE CONTACTO EN CASOS DE ACCIDENTES:",
									items: [
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_residencia],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_tiempo],
										},
										{
											columnWidth: 0.4,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_seguro],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_nhijos],
										},
										{
											columnWidth: 0.1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_dependiente],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "PATOLOGIAS | ENFERMEDADES:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_pato],
										},

										{
											xtype: "panel",
											border: false,
											columnWidth: 0.25,
											bodyStyle: "padding:2px 22x 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "SOLO SI TUVO TUBERCULOSIS:",
													items: [
														{
															columnWidth: 1,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_312_pato_tbc_fecha],
														},
														{
															columnWidth: 1,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_312_pato_tbc_tratamiento],
														},
													],
												},
											],
										},
										{
											columnWidth: 0.04,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: "-",
										},
										{
											columnWidth: 0.35,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_pato_alergias_desc],
										},
										{
											columnWidth: 0.36,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_pato_cirugia_desc],
										},
										{
											columnWidth: 0.04,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: "-",
										},
										{
											columnWidth: 0.71,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_pato_observaciones],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.25,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "ALCOHOL:",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_alcohol_tipo],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_alcohol_cantidad],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_alcohol_fre],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.25,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "TABACO:",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_tabaco_tipo],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_tabaco_cantidad],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_tabaco_fre],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.25,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "DROGAS:",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_drogas_tipo],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_drogas_cantidad],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_drogas_fre],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.25,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "MEDICAMENTOS:",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_medicamentos],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "ANTECEDENTES FAMILIARES:",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_padre],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_madre],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_conyuge],
										},
										{
											columnWidth: 0.25,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_hijo_vivo],
										},
										{
											columnWidth: 0.25,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_hijo_fallecido],
										},
									],
								},
							],
						},
					],
				},
				{
					title: "<b>--->  EXAMEN FISICO</b>",
					iconCls: "demo2",
					layout: "column",
					border: false,
					autoScroll: true,
					bodyStyle: "padding:10px 10px 20px 10px;", //m_med_aptitud
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXAMEN FISICO:",
									items: [
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_anamnesis],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_ectoscopia],
										},
										{
											columnWidth: 0.34,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_est_mental],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_piel],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_cabeza],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_oidos],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_nariz],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_boca],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_faringe],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_cuello],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_respiratorio],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_cardiovascular],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_digestivo],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_genitou],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_locomotor],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_marcha],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_columna],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_mi_superi],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_mi_inferi],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_linfatico],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_nervio],
										},
										{
											columnWidth: 0.33,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_osteomuscular],
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_ef_observaciones],
										},
									],
								},
							],
						},
					],
				},
				{
					title: "<b>--->  VALIDACIÓN - CONCLUSIONES - DIAGNOSTICOS</b>",
					iconCls: "demo2",
					layout: "column",
					border: false,
					autoScroll: true,
					bodyStyle: "padding:10px 10px 20px 10px;", //m_med_aptitud
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "CONCLUSIONES PSICOLOGICAS:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.psicologia],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_conclu_psico],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "CONCLUSIONES RAYOS X:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.rx],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_conclu_rx],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "CONCLUSIONES LABORATORIO:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.laboratorio],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_conclu_lab],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "CONCLUSIONES AUDIOMETRIA:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.audiometria],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_conclu_audio],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "CONCLUSIONES ESPIROMETRIA:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.espirometia],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_conclu_espiro],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "OTRAS CONCLUSIONES:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.otros],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_conclu_otros],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.6,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "DIAGNOSTICO MÉDICO OCUPACIONAL:",
									items: [
										{
											columnWidth: 0.8,
											border: false,
											layout: "form",
											items: [this.m_312_diag_cie1],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelWidth: 10,
											items: [this.m_312_diag_st1],
										},
										{
											columnWidth: 0.8,
											border: false,
											layout: "form",
											items: [this.m_312_diag_cie2],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelWidth: 10,
											items: [this.m_312_diag_st2],
										},
										{
											columnWidth: 0.8,
											border: false,
											layout: "form",
											items: [this.m_312_diag_cie3],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelWidth: 10,
											items: [this.m_312_diag_st3],
										},
										{
											columnWidth: 0.8,
											border: false,
											layout: "form",
											items: [this.m_312_diag_cie4],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelWidth: 10,
											items: [this.m_312_diag_st4],
										},
									],
								},
								{
									xtype: "fieldset",
									layout: "column",
									title: "OTROS DIAGNOSTICOS:",
									items: [
										{
											columnWidth: 0.8,
											border: false,
											layout: "form",
											items: [this.m_312_diag_cie5],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelWidth: 10,
											items: [this.m_312_diag_st5],
										},
										{
											columnWidth: 0.8,
											border: false,
											layout: "form",
											items: [this.m_312_diag_cie6],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelWidth: 10,
											items: [this.m_312_diag_st6],
										},
										{
											columnWidth: 0.8,
											border: false,
											layout: "form",
											items: [this.m_312_diag_cie7],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelWidth: 10,
											items: [this.m_312_diag_st7],
										},
										{
											columnWidth: 0.8,
											border: false,
											layout: "form",
											items: [this.m_312_diag_cie8],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelWidth: 10,
											items: [this.m_312_diag_st8],
										},
									],
								},
							],
						},

						{
							columnWidth: 0.4,
							border: false,
							layout: "form",
							labelAlign: "top",
							items: [this.m_312_restricciones],
						},

						{
							columnWidth: 0.4,
							border: false,
							layout: "form",
							labelAlign: "top",
							items: [this.m_312_observaciones],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_aptitud],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "",
									items: [
										{
											columnWidth: 0.1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_fech_val],
										},
										{
											columnWidth: 0.1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_fech_vence],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_time_aptitud],
										},
										{
											columnWidth: 0.3,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_medico_ocupa],
										},
										{
											columnWidth: 0.3,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_312_medico_auditor],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.dt_grid5],
										},
									],
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
						mod.medicina.anexo312.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_anexo312"
										: "save_anexo312",
								adm: this.record.get("adm"),
								id: this.record.get("id"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								Ext.MessageBox.alert(
									"En hora buena",
									"Se registro correctamente"
								);
								mod.medicina.anexo312.win.el.unmask();
								mod.medicina.formatos.st.reload();
								mod.medicina.anexo312.win.close();
							},
							failure: function (form, action) {
								mod.medicina.anexo312.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.formatos.st.reload();
								mod.medicina.anexo312.win.close();
							},
						});
					},
				},
			],
		});
		this.win = new Ext.Window({
			width: 1200,
			height: 630,
			border: false,
			modal: true,
			title: "EXAMEN FORMATO ANEXO 312: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

//MEDICINA musculo
mod.medicina.musculo = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.crea_controles();
		if (this.record.get("st") >= 1) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		// Ext.Ajax.request({
		// 	waitMsg: "Recuperando Informacion...",
		// 	waitTitle: "Espere",
		// 	url: "<[controller]>",
		// 	params: {
		// 		acction: "load_musculo",
		// 		format: "json",
		// 		adm: mod.medicina.musculo.record.get("adm"),
		// 		exa: mod.medicina.musculo.record.get("ex_id"),
		// 	},
		// 	success: function (response, opts) {
		// 		var dato = Ext.decode(response.responseText);
		// 		if (dato.success == true) {
		// 			mod.medicina.musculo.frm.getForm().loadRecord(dato);
		// 			// mod.medicina.musculo.m_312_medico_ocupa.setValue(
		// 			// 	dato.data.m_312_medico_ocupa
		// 			// );
		// 			// mod.medicina.musculo.m_312_medico_ocupa.setRawValue(
		// 			// 	dato.data.m_312_medico_ocupa_nom
		// 			// );
		// 			// mod.medicina.musculo.load_medico.load();
		// 			// mod.medicina.musculo.adm_emp.setValue(dato.data.emp_id);
		// 			// mod.medicina.musculo.adm_emp.setRawValue(dato.data.empresa);
		// 			// mod.medicina.musculo.st_empre.load();
		// 			// mod.medicina.musculo.adm_pac.setValue(dato.data.pac_id);
		// 			// mod.medicina.musculo.adm_pac.setRawValue(dato.data.adm_pac);
		// 			// mod.medicina.musculo.desc.setValue(dato.data.pk_desc);
		// 			// mod.medicina.musculo.tficha.setValue(dato.data.tfi_desc);
		// 		}
		// 	},
		// });
	},
	crea_stores: function () {
		this.list_cie10 = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_cie10",
				format: "json",
			},
			fields: ["cie4_id", "cie4_cie3id", "cie4_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		//m_musc_flexi_ptos
		this.m_musc_flexi_ptos = new Ext.form.TextField({
			fieldLabel: "<b>Puntos</b>",
			maskRe: /[\d]/,
			name: "m_musc_flexi_ptos",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 1,
				minLength: 1,
				type: "text",
				size: "1",
				autocomplete: "off",
			},
			value: "1",
			width: 24,
		});
		//m_musc_flexi_obs
		this.m_musc_flexi_obs = new Ext.form.TextArea({
			name: "m_musc_flexi_obs",
			fieldLabel: "<b>Observaciones</b>",
			value: "NO",
			anchor: "100%",
			height: 50,
		});
		//m_musc_cadera_ptos
		this.m_musc_cadera_ptos = new Ext.form.TextField({
			fieldLabel: "<b>Puntos</b>",
			maskRe: /[\d]/,
			name: "m_musc_cadera_ptos",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 1,
				minLength: 1,
				type: "text",
				size: "1",
				autocomplete: "off",
			},
			value: "1",
			width: 24,
		});
		//m_musc_cadera_obs
		this.m_musc_cadera_obs = new Ext.form.TextArea({
			name: "m_musc_cadera_obs",
			fieldLabel: "<b>Observaciones</b>",
			value: "NO",
			anchor: "100%",
			height: 50,
		});
		//m_musc_muslo_ptos
		this.m_musc_muslo_ptos = new Ext.form.TextField({
			fieldLabel: "<b>Puntos</b>",
			maskRe: /[\d]/,
			name: "m_musc_muslo_ptos",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 1,
				minLength: 1,
				type: "text",
				size: "1",
				autocomplete: "off",
			},
			value: "1",
			width: 24,
		});
		//m_musc_muslo_obs
		this.m_musc_muslo_obs = new Ext.form.TextArea({
			name: "m_musc_muslo_obs",
			fieldLabel: "<b>Observaciones</b>",
			value: "NO",
			anchor: "100%",
			height: 50,
		});
		//m_musc_abdom_ptos
		this.m_musc_abdom_ptos = new Ext.form.TextField({
			fieldLabel: "<b>Puntos</b>",
			maskRe: /[\d]/,
			name: "m_musc_abdom_ptos",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 1,
				minLength: 1,
				type: "text",
				size: "1",
				autocomplete: "off",
			},
			value: "1",
			width: 24,
		});
		//m_musc_abdom_obs
		this.m_musc_abdom_obs = new Ext.form.TextArea({
			name: "m_musc_abdom_obs",
			fieldLabel: "<b>Observaciones</b>",
			value: "NO",
			anchor: "100%",
			height: 50,
		});
		//m_musc_abduc_180_ptos
		this.m_musc_abduc_180_ptos = new Ext.form.TextField({
			fieldLabel: "<b>Puntos</b>",
			maskRe: /[\d]/,
			name: "m_musc_abduc_180_ptos",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 1,
				minLength: 1,
				type: "text",
				size: "1",
				autocomplete: "off",
			},
			value: "1",
			width: 24,
		});
		//m_musc_abduc_180_dolor
		this.m_musc_abduc_180_dolor = new Ext.form.TextArea({
			name: "m_musc_abduc_180_dolor",
			fieldLabel: "<b>Observaciones</b>",
			value: "NO",
			anchor: "95%",
			height: 50,
		});
		//m_musc_abduc_80_ptos
		this.m_musc_abduc_80_ptos = new Ext.form.TextField({
			fieldLabel: "<b>Puntos</b>",
			maskRe: /[\d]/,
			name: "m_musc_abduc_80_ptos",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 1,
				minLength: 1,
				type: "text",
				size: "1",
				autocomplete: "off",
			},
			value: "1",
			width: 24,
		});
		//m_musc_abduc_80_dolor
		this.m_musc_abduc_80_dolor = new Ext.form.TextArea({
			name: "m_musc_abduc_80_dolor",
			fieldLabel: "<b>Observaciones</b>",
			value: "NO",
			anchor: "95%",
			height: 50,
		});
		//m_musc_rota_exter_ptos
		this.m_musc_rota_exter_ptos = new Ext.form.TextField({
			fieldLabel: "<b>Puntos</b>",
			maskRe: /[\d]/,
			name: "m_musc_rota_exter_ptos",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 1,
				minLength: 1,
				type: "text",
				size: "1",
				autocomplete: "off",
			},
			value: "1",
			width: 24,
		});
		//m_musc_rota_exter_dolor
		this.m_musc_rota_exter_dolor = new Ext.form.TextArea({
			name: "m_musc_rota_exter_dolor",
			fieldLabel: "<b>Observaciones</b>",
			value: "NO",
			anchor: "95%",
			height: 50,
		});
		//m_musc_rota_inter_ptos
		this.m_musc_rota_inter_ptos = new Ext.form.TextField({
			fieldLabel: "<b>Puntos</b>",
			maskRe: /[\d]/,
			name: "m_musc_rota_inter_ptos",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 1,
				minLength: 1,
				type: "text",
				size: "1",
				autocomplete: "off",
			},
			value: "1",
			width: 24,
		});
		//m_musc_rota_inter_dolor
		this.m_musc_rota_inter_dolor = new Ext.form.TextArea({
			name: "m_musc_rota_inter_dolor",
			fieldLabel: "<b>Observaciones</b>",
			value: "NO",
			anchor: "95%",
			height: 50,
		});
		//m_musc_ra_obs
		this.m_musc_ra_obs = new Ext.form.TextArea({
			name: "m_musc_ra_obs",
			fieldLabel: "<b>Observaciones</b>",
			value: "NIEGA",
			anchor: "95%",
			height: 50,
		});

		//m_musc_aptitud
		this.m_musc_aptitud = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>Segun la evaluacion de capacidad fisica, el medico que suscribe CERTIFICA que el trabajador:</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{
					boxLabel: "No tiene limitaciones funcionales",
					name: "m_musc_aptitud",
					inputValue: "No tiene limitaciones funcionales",
					checked: true,
				},
				{
					boxLabel: "Tiene limitaciones funcionales",
					name: "m_musc_aptitud",
					inputValue: "Tiene limitaciones funcionales",
				},
			],
		});
		//m_musc_col_cevical_desvia_lateral
		this.m_musc_col_cevical_desvia_lateral = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["Normal", "Normal"],
					["Concavidad derecha", "Concavidad derecha"],
					["Concavidad izquierda", "Concavidad izquierda"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_desvia_lateral",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("Normal");
					descripcion.setRawValue("Normal");
				},
			},
		});
		//m_musc_col_cevical_desvia_antero
		this.m_musc_col_cevical_desvia_antero = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["Normal", "Normal"],
					["Aumentada", "Aumentada"],
					["Disminuida", "Disminuida"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_desvia_antero",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("Normal");
					descripcion.setRawValue("Normal");
				},
			},
		});
		//m_musc_col_cevical_palpa_apofisis
		this.m_musc_col_cevical_palpa_apofisis = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_palpa_apofisis",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_col_cevical_palpa_contractura
		this.m_musc_col_cevical_palpa_contractura = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_palpa_contractura",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_col_dorsal_desvia_lateral
		this.m_musc_col_dorsal_desvia_lateral = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["Normal", "Normal"],
					["Concavidad derecha", "Concavidad derecha"],
					["Concavidad izquierda", "Concavidad izquierda"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_desvia_lateral",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("Normal");
					descripcion.setRawValue("Normal");
				},
			},
		});
		//m_musc_col_dorsal_desvia_antero
		this.m_musc_col_dorsal_desvia_antero = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["Normal", "Normal"],
					["Aumentada", "Aumentada"],
					["Disminuida", "Disminuida"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_desvia_antero",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("Normal");
					descripcion.setRawValue("Normal");
				},
			},
		});
		//m_musc_col_dorsal_palpa_apofisis
		this.m_musc_col_dorsal_palpa_apofisis = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_palpa_apofisis",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_col_dorsal_palpa_contractura
		this.m_musc_col_dorsal_palpa_contractura = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_palpa_contractura",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_col_lumbar_desvia_lateral
		this.m_musc_col_lumbar_desvia_lateral = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["Normal", "Normal"],
					["Concavidad derecha", "Concavidad derecha"],
					["Concavidad izquierda", "Concavidad izquierda"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_desvia_lateral",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("Normal");
					descripcion.setRawValue("Normal");
				},
			},
		});
		//m_musc_col_lumbar_desvia_antero
		this.m_musc_col_lumbar_desvia_antero = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["Normal", "Normal"],
					["Aumentada", "Aumentada"],
					["Disminuida", "Disminuida"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_desvia_antero",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("Normal");
					descripcion.setRawValue("Normal");
				},
			},
		});
		//m_musc_col_lumbar_palpa_apofisis
		this.m_musc_col_lumbar_palpa_apofisis = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_palpa_apofisis",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_col_lumbar_palpa_contractura
		this.m_musc_col_lumbar_palpa_contractura = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_palpa_contractura",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});

		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////
		//m_musc_col_cevical_flexion
		this.m_musc_col_cevical_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_cevical_exten
		this.m_musc_col_cevical_exten = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_exten",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_cevical_lat_izq
		this.m_musc_col_cevical_lat_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_lat_izq",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_cevical_lat_der
		this.m_musc_col_cevical_lat_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_lat_der",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_cevical_rota_izq
		this.m_musc_col_cevical_rota_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_rota_izq",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_cevical_rota_der
		this.m_musc_col_cevical_rota_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_rota_der",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_cevical_irradia
		this.m_musc_col_cevical_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_col_cevical_alt_masa
		this.m_musc_col_cevical_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_cevical_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_col_dorsal_flexion
		this.m_musc_col_dorsal_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_dorsal_exten
		this.m_musc_col_dorsal_exten = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_exten",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_dorsal_lat_izq
		this.m_musc_col_dorsal_lat_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_lat_izq",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_dorsal_lat_der
		this.m_musc_col_dorsal_lat_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_lat_der",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_dorsal_rota_izq
		this.m_musc_col_dorsal_rota_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_rota_izq",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_dorsal_rota_der
		this.m_musc_col_dorsal_rota_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_rota_der",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_dorsal_irradia
		this.m_musc_col_dorsal_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_col_dorsal_alt_masa
		this.m_musc_col_dorsal_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_dorsal_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_col_lumbar_flexion
		this.m_musc_col_lumbar_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_lumbar_exten
		this.m_musc_col_lumbar_exten = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_exten",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_lumbar_lat_izq
		this.m_musc_col_lumbar_lat_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_lat_izq",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_lumbar_lat_der
		this.m_musc_col_lumbar_lat_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_lat_der",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_lumbar_rota_izq
		this.m_musc_col_lumbar_rota_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_rota_izq",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_lumbar_rota_der
		this.m_musc_col_lumbar_rota_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_rota_der",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_col_lumbar_irradia
		this.m_musc_col_lumbar_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_col_lumbar_alt_masa
		this.m_musc_col_lumbar_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_col_lumbar_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_hombro_der_abduccion
		this.m_musc_hombro_der_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_der_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_der_aduccion
		this.m_musc_hombro_der_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_der_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_der_flexion
		this.m_musc_hombro_der_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_der_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_der_extencion
		this.m_musc_hombro_der_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_der_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_der_rota_exter
		this.m_musc_hombro_der_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_der_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_der_rota_inter
		this.m_musc_hombro_der_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_der_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_der_irradia
		this.m_musc_hombro_der_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_der_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_hombro_der_alt_masa
		this.m_musc_hombro_der_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_der_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_hombro_izq_abduccion
		this.m_musc_hombro_izq_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_izq_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_izq_aduccion
		this.m_musc_hombro_izq_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_izq_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_izq_flexion
		this.m_musc_hombro_izq_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_izq_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_izq_extencion
		this.m_musc_hombro_izq_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_izq_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_izq_rota_exter
		this.m_musc_hombro_izq_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_izq_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_izq_rota_inter
		this.m_musc_hombro_izq_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_izq_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_hombro_izq_irradia
		this.m_musc_hombro_izq_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_izq_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_hombro_izq_alt_masa
		this.m_musc_hombro_izq_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_hombro_izq_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_codo_der_abduccion
		this.m_musc_codo_der_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_der_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_der_aduccion
		this.m_musc_codo_der_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_der_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_der_flexion
		this.m_musc_codo_der_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_der_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_der_extencion
		this.m_musc_codo_der_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_der_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_der_rota_exter
		this.m_musc_codo_der_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_der_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_der_rota_inter
		this.m_musc_codo_der_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_der_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_der_irradia
		this.m_musc_codo_der_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_der_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_codo_der_alt_masa
		this.m_musc_codo_der_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_der_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_codo_izq_abduccion
		this.m_musc_codo_izq_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_izq_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_izq_aduccion
		this.m_musc_codo_izq_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_izq_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_izq_flexion
		this.m_musc_codo_izq_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_izq_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_izq_extencion
		this.m_musc_codo_izq_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_izq_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_izq_rota_exter
		this.m_musc_codo_izq_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_izq_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_izq_rota_inter
		this.m_musc_codo_izq_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_izq_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_codo_izq_irradia
		this.m_musc_codo_izq_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_izq_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_codo_izq_alt_masa
		this.m_musc_codo_izq_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_codo_izq_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_muneca_der_abduccion
		this.m_musc_muneca_der_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_der_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_der_aduccion
		this.m_musc_muneca_der_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_der_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_der_flexion
		this.m_musc_muneca_der_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_der_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_der_extencion
		this.m_musc_muneca_der_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_der_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_der_rota_exter
		this.m_musc_muneca_der_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_der_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_der_rota_inter
		this.m_musc_muneca_der_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_der_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_der_irradia
		this.m_musc_muneca_der_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_der_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_muneca_der_alt_masa
		this.m_musc_muneca_der_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_der_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_muneca_izq_abduccion
		this.m_musc_muneca_izq_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_izq_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_izq_aduccion
		this.m_musc_muneca_izq_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_izq_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_izq_flexion
		this.m_musc_muneca_izq_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_izq_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_izq_extencion
		this.m_musc_muneca_izq_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_izq_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_izq_rota_exter
		this.m_musc_muneca_izq_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_izq_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_izq_rota_inter
		this.m_musc_muneca_izq_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_izq_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_muneca_izq_irradia
		this.m_musc_muneca_izq_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_izq_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_muneca_izq_alt_masa
		this.m_musc_muneca_izq_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_muneca_izq_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_mano_der_abduccion
		this.m_musc_mano_der_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_der_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_der_aduccion
		this.m_musc_mano_der_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_der_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_der_flexion
		this.m_musc_mano_der_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_der_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_der_extencion
		this.m_musc_mano_der_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_der_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_der_rota_exter
		this.m_musc_mano_der_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_der_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_der_rota_inter
		this.m_musc_mano_der_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_der_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_der_irradia
		this.m_musc_mano_der_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_der_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_mano_der_alt_masa
		this.m_musc_mano_der_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_der_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_mano_izq_abduccion
		this.m_musc_mano_izq_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_izq_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_izq_aduccion
		this.m_musc_mano_izq_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_izq_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_izq_flexion
		this.m_musc_mano_izq_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_izq_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_izq_extencion
		this.m_musc_mano_izq_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_izq_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_izq_rota_exter
		this.m_musc_mano_izq_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_izq_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_izq_rota_inter
		this.m_musc_mano_izq_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_izq_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_mano_izq_irradia
		this.m_musc_mano_izq_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_izq_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_mano_izq_alt_masa
		this.m_musc_mano_izq_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_mano_izq_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_cadera_der_abduccion
		this.m_musc_cadera_der_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_der_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_der_aduccion
		this.m_musc_cadera_der_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_der_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_der_flexion
		this.m_musc_cadera_der_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_der_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_der_extencion
		this.m_musc_cadera_der_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_der_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_der_rota_exter
		this.m_musc_cadera_der_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_der_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_der_rota_inter
		this.m_musc_cadera_der_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_der_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_der_irradia
		this.m_musc_cadera_der_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_der_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_cadera_der_alt_masa
		this.m_musc_cadera_der_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_der_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_cadera_izq_abduccion
		this.m_musc_cadera_izq_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_izq_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_izq_aduccion
		this.m_musc_cadera_izq_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_izq_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_izq_flexion
		this.m_musc_cadera_izq_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_izq_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_izq_extencion
		this.m_musc_cadera_izq_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_izq_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_izq_rota_exter
		this.m_musc_cadera_izq_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_izq_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_izq_rota_inter
		this.m_musc_cadera_izq_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_izq_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_cadera_izq_irradia
		this.m_musc_cadera_izq_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_izq_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_cadera_izq_alt_masa
		this.m_musc_cadera_izq_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_cadera_izq_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_rodilla_der_abduccion
		this.m_musc_rodilla_der_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_der_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_der_aduccion
		this.m_musc_rodilla_der_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_der_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_der_flexion
		this.m_musc_rodilla_der_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_der_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_der_extencion
		this.m_musc_rodilla_der_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_der_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_der_rota_exter
		this.m_musc_rodilla_der_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_der_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_der_rota_inter
		this.m_musc_rodilla_der_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_der_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_der_irradia
		this.m_musc_rodilla_der_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_der_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_rodilla_der_alt_masa
		this.m_musc_rodilla_der_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_der_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_rodilla_izq_abduccion
		this.m_musc_rodilla_izq_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_izq_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_izq_aduccion
		this.m_musc_rodilla_izq_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_izq_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_izq_flexion
		this.m_musc_rodilla_izq_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_izq_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_izq_extencion
		this.m_musc_rodilla_izq_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_izq_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_izq_rota_exter
		this.m_musc_rodilla_izq_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_izq_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_izq_rota_inter
		this.m_musc_rodilla_izq_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_izq_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_rodilla_izq_irradia
		this.m_musc_rodilla_izq_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_izq_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_rodilla_izq_alt_masa
		this.m_musc_rodilla_izq_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_rodilla_izq_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_tobillo_der_abduccion
		this.m_musc_tobillo_der_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_der_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_der_aduccion
		this.m_musc_tobillo_der_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_der_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_der_flexion
		this.m_musc_tobillo_der_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_der_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_der_extencion
		this.m_musc_tobillo_der_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_der_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_der_rota_exter
		this.m_musc_tobillo_der_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_der_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_der_rota_inter
		this.m_musc_tobillo_der_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_der_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_der_irradia
		this.m_musc_tobillo_der_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_der_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_tobillo_der_alt_masa
		this.m_musc_tobillo_der_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_der_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_tobillo_izq_abduccion
		this.m_musc_tobillo_izq_abduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_izq_abduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_izq_aduccion
		this.m_musc_tobillo_izq_aduccion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_izq_aduccion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_izq_flexion
		this.m_musc_tobillo_izq_flexion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_izq_flexion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_izq_extencion
		this.m_musc_tobillo_izq_extencion = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_izq_extencion",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_izq_rota_exter
		this.m_musc_tobillo_izq_rota_exter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_izq_rota_exter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_izq_rota_inter
		this.m_musc_tobillo_izq_rota_inter = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["0", "0"],
					["1", "1"],
					["2", "2"],
					["3", "3"],
					["4", "4"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_izq_rota_inter",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("0");
					descripcion.setRawValue("0");
				},
			},
		});
		//m_musc_tobillo_izq_irradia
		this.m_musc_tobillo_izq_irradia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_izq_irradia",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_tobillo_izq_alt_masa
		this.m_musc_tobillo_izq_alt_masa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			// fieldLabel: "<b>nombre_combo</b>",
			hiddenName: "m_musc_tobillo_izq_alt_masa",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		//m_musc_colum_punto_ref
		this.m_musc_colum_punto_ref = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>Segun la evaluacion de capacidad fisica, el medico que suscribe CERTIFICA que el trabajador:</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{
					boxLabel: "Grado 0: Ausencia de signos y sintomas",
					name: "m_musc_colum_punto_ref",
					inputValue: "Grado 0: Ausencia de signos y sintomas",
					checked: true,
				},
				{
					boxLabel: "Grado 1: Contractura y/o dolor a la movilizacion",
					name: "m_musc_colum_punto_ref",
					inputValue: "Grado 1: Contractura y/o dolor a la movilizacion",
				},
				{
					boxLabel: "Grado 2: Grado1 mas dolor a la palpacion y/o persuacion",
					name: "m_musc_colum_punto_ref",
					inputValue: "Grado 2: Grado1 mas dolor a la palpacion y/o persuacion",
				},
				{
					boxLabel:
						"Grado 3: Grado 2 mas limitación funcional evidente clinicamente",
					name: "m_musc_colum_punto_ref",
					inputValue:
						"Grado 3: Grado 2 mas limitación funcional evidente clinicamente",
				},
				{
					boxLabel: "Grado 4: Dolor en reposo",
					name: "m_musc_colum_punto_ref",
					inputValue: "Grado 4: Dolor en reposo",
				},
			],
		});
		//m_musc_colum_aptitud
		this.m_musc_colum_aptitud = new Ext.form.RadioGroup({
			fieldLabel:"<b>VALORACIÓN</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "APTO",
					name: "m_musc_colum_aptitud",
					inputValue: "APTO",
					checked: true,
				},
				{
					boxLabel: "NO APTO",
					name: "m_musc_colum_aptitud",
					inputValue: "NO APTO",
				},
				{
					boxLabel: "EN OBSERVACION",
					name: "m_musc_colum_aptitud",
					inputValue: "EN OBSERVACION",
				},
			],
		});
		// this.m_musc_colum_aptitud = new Ext.form.ComboBox({
		// 	store: new Ext.data.ArrayStore({
		// 		fields: ["campo", "descripcion"],
		// 		data: [
		// 			["APTO", "APTO"],
		// 			["NO APTO", "NO APTO"],
		// 			["EN OBSERVACION", "EN OBSERVACION"],
		// 		],
		// 	}),
		// 	displayField: "descripcion",
		// 	valueField: "campo",
		// 	fieldLabel: "<b>VALORACIÓN</b>",
		// 	hiddenName: "m_musc_colum_aptitud",
		// 	allowBlank: false,
		// 	typeAhead: true,
		// 	mode: "local",
		// 	forceSelection: true,
		// 	triggerAction: "all",
		// 	emptyText: "Seleccione...",
		// 	selectOnFocus: true,
		// 	anchor: "90%",
		// 	width: 100,
		// 	listeners: {
		// 		afterrender: function (descripcion) {
		// 			descripcion.setValue("APTO");
		// 			descripcion.setRawValue("APTO");
		// 		},
		// 	},
		// });
		//m_musc_colum_desc
		this.m_musc_colum_desc = new Ext.form.TextArea({
			name: "m_musc_colum_desc",
			fieldLabel: "<b>DESCRIPION DE HALLAZGOS</b>",
			anchor: "95%",
			value: "SIN HALLAZGOS DE IMPORTANCIA",
			height: 100,
		});

		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"{cie4_id}",
			"<h3><span><p>{cie4_desc}</p></span></h3>",
			"</div>",
			"</div></tpl>"
		);

		//m_musc_diag_01
		this.m_musc_diag_01 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_musc_diag_01",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Diagnostico Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		//m_musc_conclu_01
		this.m_musc_conclu_01 = new Ext.form.TextArea({
			name: "m_musc_conclu_01",
			fieldLabel: "<b>CONCLUSIONES</b>",
			anchor: "95%",
			height: 50,
		});
		//m_musc_recom_01
		this.m_musc_recom_01 = new Ext.form.TextArea({
			name: "m_musc_recom_01",
			fieldLabel: "<b>RECOMENDACIONES</b>",
			anchor: "95%",
			height: 50,
		});
		//m_musc_diag_02
		this.m_musc_diag_02 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_musc_diag_02",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Diagnostico Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		//m_musc_conclu_02
		this.m_musc_conclu_02 = new Ext.form.TextArea({
			name: "m_musc_conclu_02",
			fieldLabel: "<b>CONCLUSIONES</b>",
			anchor: "95%",
			height: 50,
		});
		//m_musc_recom_02
		this.m_musc_recom_02 = new Ext.form.TextArea({
			name: "m_musc_recom_02",
			fieldLabel: "<b>RECOMENDACIONES</b>",
			anchor: "95%",
			height: 50,
		});
		//m_musc_diag_03
		this.m_musc_diag_03 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_musc_diag_03",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Diagnostico Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		//m_musc_conclu_03
		this.m_musc_conclu_03 = new Ext.form.TextArea({
			name: "m_musc_conclu_03",
			fieldLabel: "<b>CONCLUSIONES</b>",
			anchor: "95%",
			height: 50,
		});
		//m_musc_recom_03
		this.m_musc_recom_03 = new Ext.form.TextArea({
			name: "m_musc_recom_03",
			fieldLabel: "<b>RECOMENDACIONES</b>",
			anchor: "95%",
			height: 50,
		});

		//FRM ANEXO 16
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			border: false,
			layout: "accordion",
			layoutConfig: {
				titleCollapse: true,
				animate: true,
				hideCollapseTool: true,
			},
			items: [
				{
					title: "<b>--->  APTITUD DE ESPALDA Y RANGOS ARTICULARES</b>",
					iconCls: "demo2",
					layout: "column",
					border: false,
					autoScroll: true,
					bodyStyle: "padding:10px 10px 20px 10px;", //m_med_aptitud
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							labelWidth: 250,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									// layout: "column",
									title: "CONCLUSIONES PSICOLOGICAS:",
									items: [
										{
											xtype: "compositefield",
											items: [
												{
													xtype: "displayfield",
													value: "<b>Excelente: 1</b>",
													width: 115,
												},
												{
													xtype: "displayfield",
													value: "<b>Promedio: 2</b>",
													width: 115,
												},
												{
													xtype: "displayfield",
													value: "<b>Regular: 3</b>",
													width: 110,
												},
												{
													xtype: "displayfield",
													value: "<b>Pobre: 4</b>",
													width: 88,
												},
												{
													xtype: "displayfield",
													value: "Ptos",
													width: 25,
												},
												{
													xtype: "displayfield",
													value: "<center>Observaciones</center>",
													width: 150,
												},
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "Flexibilidad/Fuerza ABDOMEN",
											items: [
												{
													xtype: "button",
													iconCls: "flexi_1",
													handler: function () {
														mod.medicina.musculo.m_musc_flexi_ptos.setValue(1);
													},
												},
												{
													xtype: "button",
													iconCls: "flexi_2",
													handler: function () {
														mod.medicina.musculo.m_musc_flexi_ptos.setValue(2);
													},
												},
												{
													xtype: "button",
													iconCls: "flexi_3",
													handler: function () {
														mod.medicina.musculo.m_musc_flexi_ptos.setValue(3);
													},
												},
												{
													xtype: "button",
													iconCls: "flexi_4",
													handler: function () {
														mod.medicina.musculo.m_musc_flexi_ptos.setValue(4);
													},
												},
												this.m_musc_flexi_ptos,
												this.m_musc_flexi_obs,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "CADERA",
											items: [
												{
													xtype: "button",
													iconCls: "cadera_1",
													handler: function () {
														mod.medicina.musculo.m_musc_cadera_ptos.setValue(1);
													},
												},
												{
													xtype: "button",
													iconCls: "cadera_2",
													handler: function () {
														mod.medicina.musculo.m_musc_cadera_ptos.setValue(2);
													},
												},
												{
													xtype: "button",
													iconCls: "cadera_3",
													handler: function () {
														mod.medicina.musculo.m_musc_cadera_ptos.setValue(3);
													},
												},
												{
													xtype: "button",
													iconCls: "cadera_4",
													handler: function () {
														mod.medicina.musculo.m_musc_cadera_ptos.setValue(4);
													},
												},
												this.m_musc_cadera_ptos,
												this.m_musc_cadera_obs,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "MUSLO",
											items: [
												{
													xtype: "button",
													iconCls: "muslo_1",
													handler: function () {
														mod.medicina.musculo.m_musc_muslo_ptos.setValue(1);
													},
												},
												{
													xtype: "button",
													iconCls: "muslo_2",
													handler: function () {
														mod.medicina.musculo.m_musc_muslo_ptos.setValue(2);
													},
												},
												{
													xtype: "button",
													iconCls: "muslo_3",
													handler: function () {
														mod.medicina.musculo.m_musc_muslo_ptos.setValue(3);
													},
												},
												{
													xtype: "button",
													iconCls: "muslo_4",
													handler: function () {
														mod.medicina.musculo.m_musc_muslo_ptos.setValue(4);
													},
												},
												this.m_musc_muslo_ptos,
												this.m_musc_muslo_obs,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "ABDOMEN",
											items: [
												{
													xtype: "button",
													iconCls: "abdom_1",
													handler: function () {
														mod.medicina.musculo.m_musc_abdom_ptos.setValue(1);
													},
												},
												{
													xtype: "button",
													iconCls: "abdom_2",
													handler: function () {
														mod.medicina.musculo.m_musc_abdom_ptos.setValue(2);
													},
												},
												{
													xtype: "button",
													iconCls: "abdom_3",
													handler: function () {
														mod.medicina.musculo.m_musc_abdom_ptos.setValue(3);
													},
												},
												{
													xtype: "button",
													iconCls: "abdom_4",
													handler: function () {
														mod.medicina.musculo.m_musc_abdom_ptos.setValue(4);
													},
												},
												this.m_musc_abdom_ptos,
												this.m_musc_abdom_obs,
											],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							labelWidth: 250,
							columnWidth: 0.7,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									// layout: "column",
									title: "RANGOS ARTICULARES:",
									items: [
										{
											xtype: "compositefield",
											items: [
												{
													xtype: "displayfield",
													value: "<center><b>Optimo: 1</b></center>",
													width: 99,
												},
												{
													xtype: "displayfield",
													value: "<center><b>Limitado: 2</b></center>",
													width: 105,
												},
												{
													xtype: "displayfield",
													value: "<center><b>Muy Limitado: 3</b></center>",
													width: 110,
												},
												{
													xtype: "displayfield",
													value: "Ptos",
													width: 25,
												},
												{
													xtype: "displayfield",
													value: "<center>Dolor contra resistencia</center>",
													width: 150,
												},
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "Abduccion de hombro (Normal 0º - 180º)",
											items: [
												{
													xtype: "button",
													iconCls: "abduc_180_1",
													handler: function () {
														mod.medicina.musculo.m_musc_abduc_180_ptos.setValue(
															1
														);
													},
												},
												{
													xtype: "button",
													iconCls: "abduc_180_2",
													handler: function () {
														mod.medicina.musculo.m_musc_abduc_180_ptos.setValue(
															2
														);
													},
												},
												{
													xtype: "button",
													iconCls: "abduc_180_3",
													handler: function () {
														mod.medicina.musculo.m_musc_abduc_180_ptos.setValue(
															3
														);
													},
												},
												this.m_musc_abduc_180_ptos,
												this.m_musc_abduc_180_dolor,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "Abduccion de hombro (Normal 0º - 80º)",
											items: [
												{
													xtype: "button",
													iconCls: "abduc_80_1",
													handler: function () {
														mod.medicina.musculo.m_musc_abduc_80_ptos.setValue(
															1
														);
													},
												},
												{
													xtype: "button",
													iconCls: "abduc_80_2",
													handler: function () {
														mod.medicina.musculo.m_musc_abduc_80_ptos.setValue(
															2
														);
													},
												},
												{
													xtype: "button",
													iconCls: "abduc_80_3",
													handler: function () {
														mod.medicina.musculo.m_musc_abduc_80_ptos.setValue(
															3
														);
													},
												},
												this.m_musc_abduc_80_ptos,
												this.m_musc_abduc_80_dolor,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "Rotación externa (Normal 0º - 90º)",
											items: [
												{
													xtype: "button",
													iconCls: "rota_exter_1",
													handler: function () {
														mod.medicina.musculo.m_musc_rota_exter_ptos.setValue(
															1
														);
													},
												},
												{
													xtype: "button",
													iconCls: "rota_exter_2",
													handler: function () {
														mod.medicina.musculo.m_musc_rota_exter_ptos.setValue(
															2
														);
													},
												},
												{
													xtype: "button",
													iconCls: "rota_exter_3",
													handler: function () {
														mod.medicina.musculo.m_musc_rota_exter_ptos.setValue(
															3
														);
													},
												},
												this.m_musc_rota_exter_ptos,
												this.m_musc_rota_exter_dolor,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "Rotación externa de hombro interna",
											items: [
												{
													xtype: "button",
													iconCls: "rota_inter_1",
													handler: function () {
														mod.medicina.musculo.m_musc_rota_inter_ptos.setValue(
															1
														);
													},
												},
												{
													xtype: "button",
													iconCls: "rota_inter_2",
													handler: function () {
														mod.medicina.musculo.m_musc_rota_inter_ptos.setValue(
															2
														);
													},
												},
												{
													xtype: "button",
													iconCls: "rota_inter_3",
													handler: function () {
														mod.medicina.musculo.m_musc_rota_inter_ptos.setValue(
															3
														);
													},
												},
												this.m_musc_rota_inter_ptos,
												this.m_musc_rota_inter_dolor,
											],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.3,
							labelAlign: "top",
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "Aptitud y Observaciones:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_aptitud],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_ra_obs],
										},
									],
								},
							],
						},
					],
				},
				{
					title: "<b>--->  VALIDACIÓN - CONCLUSIONES - DIAGNOSTICOS</b>",
					iconCls: "demo2",
					layout: "column",
					border: false,
					autoScroll: true,
					bodyStyle: "padding:10px 10px 20px 10px;", //m_med_aptitud
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "COLUMNA VERTEBRAL:",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:5px 0;width:215px;height:30px;float:left;"></div>\n\
                                                   <div style="padding:5px 0;width:217px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>DESVIACIONES DEL EJE LATERAL</h3></div>\n\
                                                   <div style="padding:5px 0;width:217px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>DESVIACIONES DEL EJE ANTERO POSTERIOR</h3></div>\n\
                                                   <div style="padding:5px 0;width:217px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>APOFISIS ESPINOSAS DOLOROSAS</h3></div>\n\
                                                   <div style="padding:5px 0;width:217px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>CONTRACTURA MUSCULAR</h3></div>\n\
                                                   ',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>COLUMNA VERTEBRAL</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_desvia_lateral],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_desvia_antero],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_palpa_apofisis],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_palpa_contractura],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>COLUMNA DORSAL</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_desvia_lateral],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_desvia_antero],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_palpa_apofisis],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_palpa_contractura],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>COLUMNA LUMBAR</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_desvia_lateral],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_desvia_antero],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_palpa_apofisis],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_palpa_contractura],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "MOVILIDAD - DOLOR = EVOLUCION DINAMICA",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:5px 0;width:215px;height:30px;float:left;"></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>FLEXION</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>EXTENCION</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>LATERALIZACION IZQUIERDA</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>LATERALIZACION DERECHA</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>ROTACION IZQUIERDA</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>ROTACION DERECHA</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>IRRITACION</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>ALT. MASA MUSCULAR</h3></div>\n\
                                                   ',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>COLUMNA VERTEBRAL</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_exten],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_lat_izq],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_lat_der],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_rota_izq],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_rota_der],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_cevical_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>COLUMNA DORSAL</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_exten],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_lat_izq],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_lat_der],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_rota_izq],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_rota_der],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_dorsal_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>COLUMNA LUMBAR</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_exten],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_lat_izq],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_lat_der],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_rota_izq],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_rota_der],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_col_lumbar_alt_masa],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "MOVILIDAD - DOLOR = EVOLUCION DINAMICA DE ARTICULACIONES",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:5px 0;width:215px;height:30px;float:left;"></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>ABDUCCION</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>ADUCCION</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>FLEXION</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>EXTENCION</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>ROTACION INTERNA</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>ROTACION EXTERNA</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>IRRRITACION</h3></div>\n\
                                                   <div style="padding:5px 0;width:109px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>ALT. MASA MUSCULAR</h3></div>\n\
                                                   ',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>HOMBRO - DERECHO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_der_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_der_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_der_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_der_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_der_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_der_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_der_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_der_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>HOMBRO - IZQUIERDO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_izq_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_izq_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_izq_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_izq_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_izq_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_izq_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_izq_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_hombro_izq_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>CODO - DERECHO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_der_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_der_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_der_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_der_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_der_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_der_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_der_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_der_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>CODO - IZQUIERDO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_izq_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_izq_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_izq_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_izq_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_izq_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_izq_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_izq_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_codo_izq_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>MUÑECA - DERECHO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_der_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_der_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_der_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_der_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_der_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_der_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_der_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_der_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>MUÑECA - IZQUIERDO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_izq_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_izq_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_izq_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_izq_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_izq_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_izq_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_izq_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_muneca_izq_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>MANOS Y MUÑECA - DERECHO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_der_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_der_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_der_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_der_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_der_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_der_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_der_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_der_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>MANOS Y MUÑECA - IZQUIERDO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_izq_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_izq_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_izq_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_izq_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_izq_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_izq_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_izq_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_mano_izq_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>CADERA - DERECHO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_der_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_der_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_der_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_der_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_der_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_der_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_der_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_der_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>CADERA - IZQUIERDO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_izq_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_izq_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_izq_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_izq_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_izq_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_izq_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_izq_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_cadera_izq_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>RODILLA - DERECHO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_der_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_der_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_der_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_der_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_der_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_der_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_der_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_der_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>RODILLA - IZQUIERDO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_izq_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_izq_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_izq_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_izq_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_izq_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_izq_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_izq_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_rodilla_izq_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>TOBILLO - DERECHO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_der_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_der_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_der_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_der_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_der_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_der_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_der_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_der_alt_masa],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>TOBILLO - IZQUIERDO</h3></div>',
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_izq_abduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_izq_aduccion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_izq_flexion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_izq_extencion],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_izq_rota_exter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_izq_rota_inter],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_izq_irradia],
										},
										{
											columnWidth: 0.10,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_tobillo_izq_alt_masa],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 1,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "PUNTUACION DE REFERENCIA (SIGNOS Y SINTOMAS):",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_colum_punto_ref],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_colum_aptitud],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_colum_desc],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.333,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "DIAGNOSTICO - CONCLUSION - RECOMENDACION:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_diag_01],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_conclu_01],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_recom_01],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.333,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "DIAGNOSTICO - CONCLUSION - RECOMENDACION:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_diag_02],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_conclu_02],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_recom_02],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.333,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "DIAGNOSTICO - CONCLUSION - RECOMENDACION:",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_diag_03],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_conclu_03],
										},
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_musc_recom_03],
										},
									],
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
						mod.medicina.musculo.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_musculo"
										: "save_musculo",
								adm: this.record.get("adm"),
								id: this.record.get("id"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								Ext.MessageBox.alert(
									"En hora buena",
									"Se registro correctamente"
								);
								mod.medicina.musculo.win.el.unmask();
								mod.medicina.formatos.st.reload();
								mod.medicina.musculo.win.close();
							},
							failure: function (form, action) {
								mod.medicina.musculo.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.formatos.st.reload();
								mod.medicina.musculo.win.close();
							},
						});
					},
				},
			],
		});
		this.win = new Ext.Window({
			width: 1200,
			height: 630,
			border: false,
			modal: true,
			title: "EXAMEN MÚSCULO ESQUELÉTICO: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
//recomendaciones
mod.medicina.diagnostico = {
	record2: null,
	win: null,
	frm: null,
	diag_tipo: null,
	diag_desc: null,
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
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_diag",
				format: "json",
				diag_id: this.record2.get("diag_id"),
				diag_adm: this.record2.get("diag_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_diag = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_diag",
				format: "json",
			},
			fields: ["diag_desc"],
			root: "data",
		});
		this.list_cie10 = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_cie10",
				format: "json",
			},
			fields: ["cie4_id", "cie4_cie3id", "cie4_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{cie4_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.diag_tipo = new Ext.form.RadioGroup({
			fieldLabel: "<b>TIPO DE DIAGNOSTICO</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{
					boxLabel: "TEXTUAL",
					name: "diag_tipo",
					inputValue: "1",
					checked: true,
					handler: function (values, checkbox) {
						if (checkbox == true) {
							mod.medicina.diagnostico.diag_desc.enable();
							mod.medicina.diagnostico.diag_cie.disable();
							mod.medicina.diagnostico.diag_cie.getValue("");
							mod.medicina.diagnostico.diag_cie.setRawValue("");
						}
					},
				},
				{
					boxLabel: "CIE 10",
					name: "diag_tipo",
					inputValue: "2",
					handler: function (values, checkbox) {
						if (checkbox == true) {
							mod.medicina.diagnostico.diag_cie.enable();
							mod.medicina.diagnostico.diag_desc.disable();
							mod.medicina.diagnostico.diag_desc.setValue("");
							mod.medicina.diagnostico.diag_desc.setRawValue("");
						}
					},
				},
			],
		});
		this.diag_desc = new Ext.form.ComboBox({
			store: this.st_busca_diag,
			hiddenName: "diag_desc",
			displayField: "diag_desc",
			//            disabled: true,
			valueField: "diag_desc",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			allowBlank: true,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>DIAGNOSTICO</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			anchor: "99%",
		});
		this.diag_cie = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "diag_cie",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>Cie 10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.25,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.diag_tipo],
				},
				{
					columnWidth: 0.75,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.diag_desc],
				},
				{
					columnWidth: 0.75,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.diag_cie],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.diagnostico.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						var metodo;
						var diag_id;
						if (this.record2 !== null) {
							metodo = "update";
							diag_id = mod.medicina.diagnostico.record2.get("diag_id");
						} else {
							metodo = "save";
							diag_id = "1";
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_diag",
								diag_adm: mod.medicina.nuevoAnexo16.record.get("adm"),
								diag_id: diag_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.diagnostico.win.el.unmask();
								mod.medicina.nuevoAnexo16.list_diag.reload();
								mod.medicina.diagnostico.win.close();
							},
							failure: function (form, action) {
								mod.medicina.diagnostico.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.nuevoAnexo16.list_diag.reload();
								mod.medicina.diagnostico.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 680,
			height: 180,
			modal: true,
			title: "REGISTRO DE DIAGNOSTICOS",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
mod.medicina.observacion = {
	rec: null,
	win: null,
	frm: null,
	obs_desc: null,
	obs_plazo: null,
	init: function (r) {
		this.rec = r;
		this.crea_stores();
		this.st_busca_obs.load();
		this.crea_controles();
		if (this.rec !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_obs",
				format: "json",
				obs_id: this.rec.get("obs_id"),
				obs_adm: this.rec.get("obs_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_obs = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_obs",
				format: "json",
			},
			fields: ["obs_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{cie4_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.obs_plazo = new Ext.form.RadioGroup({
			fieldLabel: "<b>PLAZO</b>",
			itemCls: "x-check-group-alt",
			columns: 4,
			items: [
				{
					boxLabel: "NINGUNO(-)",
					name: "obs_plazo",
					inputValue: "-",
					checked: true,
				},
				{ boxLabel: "INMEDIATO", name: "obs_plazo", inputValue: "INMEDIATO" },
				{ boxLabel: "03 MESES", name: "obs_plazo", inputValue: "03 MESES" },
				{ boxLabel: "06 MESES", name: "obs_plazo", inputValue: "06 MESES" },
			],
		});
		this.obs_desc = new Ext.form.ComboBox({
			store: this.st_busca_obs,
			hiddenName: "obs_desc",
			displayField: "obs_desc",
			//            disabled: true,
			valueField: "obs_desc",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			allowBlank: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>OBSERVACIONES</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			anchor: "95%",
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.obs_desc],
				},
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.obs_plazo],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.observacion.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						var metodo;
						var obs_id;
						if (this.rec !== null) {
							metodo = "update";
							obs_id = mod.medicina.observacion.rec.get("obs_id");
						} else {
							metodo = "save";
							obs_id = "1";
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_obs",
								obs_adm: mod.medicina.nuevoAnexo16.record.get("adm"),
								obs_id: obs_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.observacion.win.el.unmask();
								mod.medicina.nuevoAnexo16.list_obs.reload();
								mod.medicina.observacion.win.close();
							},
							failure: function (form, action) {
								mod.medicina.observacion.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.nuevoAnexo16.list_obs.reload();
								mod.medicina.observacion.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 800,
			height: 180,
			modal: true,
			title: "REGISTRO DE OBSERVACIONES",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
mod.medicina.restricciones = {
	rec: null,
	win: null,
	frm: null,
	restric_desc: null,
	restric_plazo: null,
	init: function (r) {
		this.rec = r;
		this.crea_stores();
		this.st_busca_restric.load();
		this.crea_controles();
		if (this.rec !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_restric",
				format: "json",
				restric_id: this.rec.get("restric_id"),
				restric_adm: this.rec.get("restric_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_restric = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_restric",
				format: "json",
			},
			fields: ["restric_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{cie4_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.restric_plazo = new Ext.form.RadioGroup({
			fieldLabel: "<b>PLAZO</b>",
			itemCls: "x-check-group-alt",
			columns: 4,
			items: [
				{
					boxLabel: "NINGUNO",
					name: "restric_plazo",
					inputValue: "-",
					checked: true,
				},
				{
					boxLabel: "INMEDIATO",
					name: "restric_plazo",
					inputValue: "INMEDIATO",
				},
				{ boxLabel: "03 MESES", name: "restric_plazo", inputValue: "03 MESES" },
				{ boxLabel: "06 MESES", name: "restric_plazo", inputValue: "06 MESES" },
			],
		});
		this.restric_desc = new Ext.form.ComboBox({
			store: this.st_busca_restric,
			hiddenName: "restric_desc",
			displayField: "restric_desc",
			//            disabled: true,
			valueField: "restric_desc",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			allowBlank: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>RESTRICCIONES</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			anchor: "99%",
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.restric_desc],
				},
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.restric_plazo],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.restricciones.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						var metodo;
						var restric_id;
						if (this.rec !== null) {
							metodo = "update";
							restric_id = mod.medicina.restricciones.rec.get("restric_id");
						} else {
							metodo = "save";
							restric_id = "1";
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_restric",
								restric_adm: mod.medicina.nuevoAnexo16.record.get("adm"),
								restric_id: restric_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.restricciones.win.el.unmask();
								mod.medicina.nuevoAnexo16.list_restric.reload();
								mod.medicina.restricciones.win.close();
							},
							failure: function (form, action) {
								mod.medicina.restricciones.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.nuevoAnexo16.list_restric.reload();
								mod.medicina.restricciones.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 800,
			height: 180,
			modal: true,
			title: "REGISTRO DE RESTRICCIONES",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
mod.medicina.interconsultas = {
	rec: null,
	win: null,
	frm: null,
	inter_desc: null,
	inter_plazo: null,
	init: function (r) {
		this.rec = r;
		this.crea_stores();
		this.st_busca_inter.load();
		this.crea_controles();
		if (this.rec !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_inter",
				format: "json",
				inter_id: this.rec.get("inter_id"),
				inter_adm: this.rec.get("inter_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_inter = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_inter",
				format: "json",
			},
			fields: ["inter_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{cie4_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.inter_plazo = new Ext.form.RadioGroup({
			fieldLabel: "<b>PLAZO</b>",
			itemCls: "x-check-group-alt",
			columns: 4,
			items: [
				{
					boxLabel: "NINGUNO(-)",
					name: "inter_plazo",
					inputValue: "-",
					checked: true,
				},
				{ boxLabel: "INMEDIATO", name: "inter_plazo", inputValue: "INMEDIATO" },
				{ boxLabel: "03 MESES", name: "inter_plazo", inputValue: "03 MESES" },
				{ boxLabel: "06 MESES", name: "inter_plazo", inputValue: "06 MESES" },
			],
		});
		this.inter_desc = new Ext.form.ComboBox({
			store: this.st_busca_inter,
			hiddenName: "inter_desc",
			displayField: "inter_desc",
			valueField: "inter_desc",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			allowBlank: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>INTERCONSULTAS</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			anchor: "95%",
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.inter_desc],
				},
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.inter_plazo],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.interconsultas.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						var metodo;
						var inter_id;
						if (this.rec !== null) {
							metodo = "update";
							inter_id = mod.medicina.interconsultas.rec.get("inter_id");
						} else {
							metodo = "save";
							inter_id = "1";
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_inter",
								inter_adm: mod.medicina.nuevoAnexo16.record.get("adm"),
								inter_id: inter_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.interconsultas.win.el.unmask();
								mod.medicina.nuevoAnexo16.list_inter.reload();
								mod.medicina.interconsultas.win.close();
							},
							failure: function (form, action) {
								mod.medicina.interconsultas.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.nuevoAnexo16.list_inter.reload();
								mod.medicina.interconsultas.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 800,
			height: 180,
			modal: true,
			title: "REGISTRO DE INTERCONSULTAS",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
mod.medicina.recomendaciones = {
	rec: null,
	win: null,
	frm: null,
	recom_desc: null,
	recom_plazo: null,
	init: function (r) {
		this.rec = r;
		this.crea_stores();
		this.st_busca_recom.load();
		this.crea_controles();
		if (this.rec !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_recom",
				format: "json",
				recom_id: this.rec.get("recom_id"),
				recom_adm: this.rec.get("recom_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_recom = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_recom",
				format: "json",
			},
			fields: ["recom_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{cie4_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.recom_plazo = new Ext.form.RadioGroup({
			fieldLabel: "<b>PLAZO</b>",
			itemCls: "x-check-group-alt",
			columns: 4,
			items: [
				{
					boxLabel: "NINGUNO(-)",
					name: "recom_plazo",
					inputValue: "-",
					checked: true,
				},
				{ boxLabel: "INMEDIATO", name: "recom_plazo", inputValue: "INMEDIATO" },
				{ boxLabel: "03 MESES", name: "recom_plazo", inputValue: "03 MESES" },
				{ boxLabel: "06 MESES", name: "recom_plazo", inputValue: "06 MESES" },
			],
		});
		this.recom_desc = new Ext.form.ComboBox({
			store: this.st_busca_recom,
			hiddenName: "recom_desc",
			displayField: "recom_desc",
			//            disabled: true,
			valueField: "recom_desc",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			allowBlank: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>RECOMENDACIONES</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			anchor: "95%",
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.recom_desc],
				},
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.recom_plazo],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.recomendaciones.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						var metodo;
						var recom_id;
						if (this.rec !== null) {
							metodo = "update";
							recom_id = mod.medicina.recomendaciones.rec.get("recom_id");
						} else {
							metodo = "save";
							recom_id = "1";
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_recom",
								recom_adm: mod.medicina.nuevoAnexo16.record.get("adm"),
								recom_id: recom_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.recomendaciones.win.el.unmask();
								mod.medicina.nuevoAnexo16.list_recom.reload();
								mod.medicina.recomendaciones.win.close();
							},
							failure: function (form, action) {
								mod.medicina.recomendaciones.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.nuevoAnexo16.list_recom.reload();
								mod.medicina.recomendaciones.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 1000,
			height: 180,
			modal: true,
			title: "REGISTRO DE RECOMENDACIONES",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
mod.medicina.recomen_312 = {
	rec: null,
	win: null,
	frm: null,
	recom_desc: null,
	recom_plazo: null,
	init: function (r) {
		this.rec = r;
		this.crea_stores();
		this.st_busca_recom.load();
		this.crea_controles();
		if (this.rec !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_recom",
				format: "json",
				recom_id: this.rec.get("recom_id"),
				recom_adm: this.rec.get("recom_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_recom = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_recom",
				format: "json",
			},
			fields: ["recom_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{cie4_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.recom_plazo = new Ext.form.RadioGroup({
			fieldLabel: "<b>PLAZO</b>",
			itemCls: "x-check-group-alt",
			columns: 4,
			items: [
				{
					boxLabel: "NINGUNO(-)",
					name: "recom_plazo",
					inputValue: "-",
					checked: true,
				},
				{ boxLabel: "INMEDIATO", name: "recom_plazo", inputValue: "INMEDIATO" },
				{ boxLabel: "03 MESES", name: "recom_plazo", inputValue: "03 MESES" },
				{ boxLabel: "06 MESES", name: "recom_plazo", inputValue: "06 MESES" },
			],
		});
		this.recom_desc = new Ext.form.ComboBox({
			store: this.st_busca_recom,
			hiddenName: "recom_desc",
			displayField: "recom_desc",
			//            disabled: true,
			valueField: "recom_desc",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			allowBlank: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>RECOMENDACIONES</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			anchor: "95%",
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.recom_desc],
				},
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.recom_plazo],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.recomen_312.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						var metodo;
						var recom_id;
						if (this.rec !== null) {
							metodo = "update";
							recom_id = mod.medicina.recomen_312.rec.get("recom_id");
						} else {
							metodo = "save";
							recom_id = "1";
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_recom",
								recom_adm: mod.medicina.anexo312.record.get("adm"),
								recom_id: recom_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.recomen_312.win.el.unmask();
								mod.medicina.anexo312.list_recom.reload();
								mod.medicina.recomen_312.win.close();
							},
							failure: function (form, action) {
								mod.medicina.recomen_312.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.anexo312.list_recom.reload();
								mod.medicina.recomen_312.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 1000,
			height: 180,
			modal: true,
			title: "REGISTRO DE RECOMENDACIONES",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.medicina.examenPRE = {
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
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			url: "<[controller]>",
			params: {
				acction: "load_exameLab",
				format: "json",
				ex_id: mod.medicina.examenPRE.record.get("ex_id"),
			},
			success: function (response, opts) {
				var dato = Ext.decode(response.responseText);
				if (dato.success == true) {
					mod.medicina.examenPRE.frm.getForm().loadRecord(dato);
				}
			},
		});
	},
	crea_stores: function () {},
	crea_controles: function () {
		this.m_lab_desc_resultado = new Ext.form.TextField({
			fieldLabel: "<b>RESULTADO DEL EXAMEN</b>",
			allowBlank: false,
			name: "m_lab_desc_resultado",
			id: "m_lab_desc_resultado",
			anchor: "96%",
		});
		this.m_lab_desc_observaciones = new Ext.form.TextArea({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_lab_desc_diagnostico",
			anchor: "99%",
			height: 40,
		});
		this.m_lab_desc_diagnostico = new Ext.form.TextArea({
			fieldLabel: "<b>DIAGNOSTICO</b>",
			name: "m_lab_desc_diagnostico",
			anchor: "99%",
			height: 40,
		});

		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:2px 2px 2px 2px;",
			labelWidth: 99,
			labelAlign: "top",
			items: [
				{
					columnWidth: 0.99,
					border: false,
					layout: "form",
					items: [this.m_lab_desc_resultado],
				},
				{
					columnWidth: 0.99,
					border: false,
					layout: "form",
					items: [this.m_lab_desc_observaciones],
				},
				{
					columnWidth: 0.99,
					border: false,
					layout: "form",
					items: [this.m_lab_desc_diagnostico],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.examenPRE.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction: this.record !== null ? "update_exaLab" : "save_exaLab",
								ex_id: this.record !== null ? this.record.get("ex_id") : "",
								area: mod.medicina.modificar.numero.getValue(),
							},
							success: function () {
								Ext.MessageBox.alert(
									"En hora buena",
									"El servicio se registro correctamente"
								);
								mod.medicina.modificar.list_examen.reload();
								mod.medicina.examenPRE.win.el.unmask();
								mod.medicina.examenPRE.win.close();
							},
							failure: function (form, action) {
								mod.medicina.examenPRE.win.el.unmask();
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
										mod.medicina.examenPRE.win.close();
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.modificar.list_examen.reload();
							},
						});
					},
				},
			],
		});
		this.win = new Ext.Window({
			width: 500,
			height: 320,
			modal: true,
			title: "REGISTRO DE EXAMENES",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.medicina.antecedentes16 = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.crea_controles();
		this.st.load();
		this.win.show();
	},
	crea_stores: function () {
		this.st = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_ante16",
				format: "json",
			},
			listeners: {
				beforeload: function () {
					this.baseParams.adm = mod.medicina.formatos.record.get("adm");
				},
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"m_antec_16_id",
				"m_antec_16_adm",
				"m_antec_16_exa",
				"m_antec_16_ini_fech",
				"m_antec_16_fin_fech",
				"m_antec_16_ocupacion",
				"m_antec_16_empresa",
				"m_antec_16_actividad",
				"m_antec_16_area_trab",
				"m_antec_16_altitud",
				"m_antec_16_tipo_ope",
				"m_antec_16_time_trab",
			],
		});
	},
	crea_controles: function () {
		this.paginador = new Ext.PagingToolbar({
			pageSize: 30,
			store: this.st,
			displayInfo: true,
			displayMsg: "Mostrando {0} - {1} de {2} Formatos",
			emptyMsg: "No Existe Registros",
			plugins: new Ext.ux.ProgressBarPager(),
		});
		this.tbar = new Ext.Toolbar({
			items: [
				"-",
				{
					text: "AGREGAR UN NUEVO ANTECEDENTE",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.nuevo_antecedentes16.init(null);
					},
				},
				"-",
				{
					text: "GENERAR HOJA DE ANTECEDENTES",
					iconCls: "reporte",
					handler: function () {
						new Ext.Window({
							title:
								"Antecedentes Laborales N° " +
								mod.medicina.antecedentes16.record.get("adm"),
							width: 800,
							height: 600,
							maximizable: true,
							modal: true,
							closeAction: "close",
							resizable: true,
							html:
								"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=antecede_16&adm=" +
								mod.medicina.antecedentes16.record.get("adm") +
								"'></iframe>",
						}).show();
					},
				},
			],
		});
		this.dt_grid = new Ext.grid.GridPanel({
			store: this.st,
			tbar: this.tbar,
			region: "west",
			border: false,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			bbar: this.paginador,
			height: 263,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var record = grid.getStore().getAt(rowIndex);
					mod.medicina.nuevo_antecedentes16.init(record);
				},
			},
			autoExpandColumn: "cuest_desc",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					header: "INICIO",
					dataIndex: "m_antec_16_ini_fech",
					width: 70,
				},
				{
					header: "FIN",
					dataIndex: "m_antec_16_fin_fech",
					width: 70,
				},
				{
					header: "OCUPACION",
					dataIndex: "m_antec_16_ocupacion",
					width: 120,
				},
				{
					id: "cuest_desc",
					header: "EMPRESA",
					dataIndex: "m_antec_16_empresa",
				},
				{
					header: "ACTIVIDAD",
					dataIndex: "m_antec_16_actividad",
					width: 120,
				},
				{
					header: "AREA",
					dataIndex: "m_antec_16_area_trab",
					width: 120,
				},
				{
					header: "ALTITUD",
					dataIndex: "m_antec_16_altitud",
					width: 120,
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
			width: 800,
			height: 300,
			modal: true,
			title: "ANTECEDENTES OCUPACIONALES",
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
mod.medicina.nuevo_antecedentes16 = {
	rec: null,
	win: null,
	frm: null,
	init: function (r) {
		this.rec = r;
		this.crea_stores();
		this.crea_controles();
		if (this.rec !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_antec_16",
				format: "json",
				m_antec_16_id: this.rec.get("m_antec_16_id"),
				m_antec_16_adm: this.rec.get("m_antec_16_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_m_antec_16_ocupacion = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_antec_16_ocupacion",
				format: "json",
			},
			fields: ["m_antec_16_ocupacion"],
			root: "data",
		});
		this.st_m_antec_16_empresa = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_antec_16_empresa",
				format: "json",
			},
			fields: ["m_antec_16_empresa"],
			root: "data",
		});
		this.st_m_antec_16_actividad = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_antec_16_actividad",
				format: "json",
			},
			fields: ["m_antec_16_actividad"],
			root: "data",
		});
		this.st_m_antec_16_area_trab = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_antec_16_area_trab",
				format: "json",
			},
			fields: ["m_antec_16_area_trab"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.m_antec_16_ini_fech = new Ext.form.DateField({
			fieldLabel: "<b>FECHA DE INICIO</b>",
			format: "Y-m-d",
			id: "startdt",
			vtype: "daterange",
			endDateField: "enddt",
			value: new Date(),
			name: "m_antec_16_ini_fech",
			anchor: "95%",
			allowBlank: false,
		});
		this.m_antec_16_fin_fech = new Ext.form.DateField({
			fieldLabel: "<b>FECHA FINAL</b>",
			format: "Y-m-d",
			id: "enddt",
			vtype: "daterange",
			startDateField: "startdt",
			value: new Date(),
			name: "m_antec_16_fin_fech",
			anchor: "95%",
			allowBlank: false,
		});
		///////////////////////////m_antec_16_ocupacion
		this.Tpl_m_antec_16_ocupacion = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_antec_16_ocupacion}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_antec_16_ocupacion = new Ext.form.ComboBox({
			store: this.st_m_antec_16_ocupacion,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_antec_16_ocupacion,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_antec_16_ocupacion",
			displayField: "m_antec_16_ocupacion",
			valueField: "m_antec_16_ocupacion",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>OCUPACIÓN</b>",
			mode: "remote",
			anchor: "95%",
		});
		///////////////////////////m_antec_16_empresa
		this.Tpl_m_antec_16_empresa = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_antec_16_empresa}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_antec_16_empresa = new Ext.form.ComboBox({
			store: this.st_m_antec_16_empresa,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_antec_16_empresa,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_antec_16_empresa",
			displayField: "m_antec_16_empresa",
			valueField: "m_antec_16_empresa",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>EMPRESA</b>",
			mode: "remote",
			anchor: "95%",
		});
		///////////////////////////m_antec_16_actividad
		this.Tpl_m_antec_16_actividad = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_antec_16_actividad}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_antec_16_actividad = new Ext.form.ComboBox({
			store: this.st_m_antec_16_actividad,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_antec_16_actividad,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_antec_16_actividad",
			displayField: "m_antec_16_actividad",
			valueField: "m_antec_16_actividad",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>ACTIVIDAD</b>",
			mode: "remote",
			anchor: "95%",
		});
		///////////////////////////m_antec_16_area_trab
		this.Tpl_m_antec_16_area_trab = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_antec_16_area_trab}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_antec_16_area_trab = new Ext.form.ComboBox({
			store: this.st_m_antec_16_area_trab,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_antec_16_area_trab,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_antec_16_area_trab",
			displayField: "m_antec_16_area_trab",
			valueField: "m_antec_16_area_trab",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>AREA DE TRABAJO</b>",
			mode: "remote",
			anchor: "95%",
		});
		///////////////////////////m_antec_16_altitud
		this.m_antec_16_altitud = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 3000", "< 3000"],
					["3001 - 4000", "3001 - 4000"],
					["4001 - 4500", "4001 - 4500"],
					["> 4500", "> 4500"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_antec_16_altitud",
			fieldLabel: "<b>ALTITUD LABORAL</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("4001 - 4500");
					descripcion.setRawValue("4001 - 4500");
				},
			},
		});
		///////////////////////////m_antec_16_tipo_ope
		this.m_antec_16_tipo_ope = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SUPERFICIE", "SUPERFICIE"],
					["SUBTERRANEO", "SUBTERRANEO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_antec_16_tipo_ope",
			fieldLabel: "<b>OPERACION EN</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "85%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("SUPERFICIE");
					descripcion.setRawValue("SUPERFICIE");
				},
			},
		});
		///////////////////////////m_antec_16_time_trab
		this.m_antec_16_time_trab = new Ext.form.TextField({
			fieldLabel: "<b>TIEMPO DE TRABAJO</b>",
			name: "m_antec_16_time_trab",
			anchor: "95%",
			value: "0A + 0M",
		});
		///////////////////////////m_antec_16_tipo_ope
		this.antec_16_riesgos = new Ext.form.CheckboxGroup({
			fieldLabel: "<b>RIESGOS OCUPACIONALES</b>",
			itemCls: "x-check-group-alt",
			columns: 7,
			items: [
				{
					boxLabel: "<b>FISICO</b>",
					name: "m_antec_16_fisico_agen",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_fisico_hora.enable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_fisico_epp.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_fisico_hora.disable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_fisico_epp.disable();
						}
					},
				},
				{
					boxLabel: "<b>QUIMICO</b>",
					name: "m_antec_16_quimico_agen",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_quimico_hora.enable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_quimico_epp.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_quimico_hora.disable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_quimico_epp.disable();
						}
					},
				},
				{
					boxLabel: "<b>ELECTRICO</b>",
					name: "m_antec_16_electrico_agen",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_electrico_hora.enable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_electrico_epp.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_electrico_hora.disable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_electrico_epp.disable();
						}
					},
				},
				{
					boxLabel: "<b>ERGONOMICO</b>",
					name: "m_antec_16_ergo_agen",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_ergo_hora.enable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_ergo_epp.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_ergo_hora.disable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_ergo_epp.disable();
						}
					},
				},
				{
					boxLabel: "<b>BIOLOGICO</b>",
					name: "m_antec_16_biologico_agen",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_biologico_hora.enable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_biologico_epp.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_biologico_hora.disable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_biologico_epp.disable();
						}
					},
				},
				{
					boxLabel: "<b>PSICOSOCIAL</b>",
					name: "m_antec_16_psico_agen",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_psico_hora.enable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_psico_epp.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_psico_hora.disable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_psico_epp.disable();
						}
					},
				},
				{
					boxLabel: "<b>OTROS</b>",
					name: "m_antec_16_otros_agen",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_otros_hora.enable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_otros_epp.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antecedentes16.m_antec_16_otros_hora.disable();
							mod.medicina.nuevo_antecedentes16.m_antec_16_otros_epp.disable();
						}
					},
				},
			],
		});
		this.m_antec_16_fisico_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_16_fisico_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_16_fisico_epp = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_16_fisico_epp",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		////////////////////////////////////////////////////////////
		this.m_antec_16_quimico_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_16_quimico_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_16_quimico_epp = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_16_quimico_epp",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		////////////////////////////////////////////////////////////
		this.m_antec_16_electrico_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_16_electrico_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_16_electrico_epp = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_16_electrico_epp",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		////////////////////////////////////////////////////////////
		this.m_antec_16_ergo_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_16_ergo_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_16_ergo_epp = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_16_ergo_epp",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		////////////////////////////////////////////////////////////
		this.m_antec_16_biologico_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_16_biologico_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_16_biologico_epp = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_16_biologico_epp",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		////////////////////////////////////////////////////////////
		this.m_antec_16_psico_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_16_psico_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_16_psico_epp = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_16_psico_epp",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		////////////////////////////////////////////////////////////
		this.m_antec_16_otros_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_16_otros_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_16_otros_epp = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_16_otros_epp",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});

		this.m_antec_16_especificar = new Ext.form.TextArea({
			name: "m_antec_16_especificar",
			fieldLabel: "<b>ESPECIFICAR</b>",
			anchor: "95%",
			height: 50,
		});

		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.15,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_16_ini_fech],
				},
				{
					columnWidth: 0.15,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_16_fin_fech],
				},
				{
					columnWidth: 0.35,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_16_ocupacion],
				},
				{
					columnWidth: 0.35,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_16_empresa],
				},
				{
					columnWidth: 0.2,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_16_actividad],
				},
				{
					columnWidth: 0.2,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_16_area_trab],
				},
				{
					columnWidth: 0.2,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_16_altitud],
				},
				{
					columnWidth: 0.2,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_16_tipo_ope],
				},
				{
					columnWidth: 0.2,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_16_time_trab],
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
							//                            title: '<b>RIESGOS OCUPACIONALES</b>',
							items: [
								{
									columnWidth: 0.999,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.antec_16_riesgos],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>FISICOS</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_fisico_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_fisico_epp],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>QUIMICO</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_quimico_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_quimico_epp],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>ELECTRICO</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_electrico_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_electrico_epp],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>ERGONOMICO</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_ergo_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_ergo_epp],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>BIOLOGICO</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_biologico_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_biologico_epp],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>PSICOSOCIAL</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_psico_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_psico_epp],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>OTROS</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_otros_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_16_otros_epp],
								},
							],
						},
					],
				},
				{
					columnWidth: 0.5,
					border: false,
					bodyStyle: "margin: 0 0 0 15px;",
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_16_especificar],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.nuevo_antecedentes16.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						this.frm.getForm().submit({
							params: {
								acction:
									this.rec !== null
										? "update_nuevo_antecedentes16"
										: "save_nuevo_antecedentes16",
								adm: mod.medicina.antecedentes16.record.get("adm"),
								id: this.rec !== null ? this.rec.get("m_antec_16_id") : null,
								ex_id: mod.medicina.antecedentes16.record.get("ex_id"),
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.nuevo_antecedentes16.win.el.unmask();
								mod.medicina.antecedentes16.st.reload();
								mod.medicina.formatos.st.reload();
								mod.medicina.nuevo_antecedentes16.win.close();
							},
							failure: function (form, action) {
								mod.medicina.nuevo_antecedentes16.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.antecedentes16.st.reload();
								mod.medicina.formatos.st.reload();
								mod.medicina.nuevo_antecedentes16.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 900,
			height: 570,
			modal: true,
			title: "REGISTRO DE ANTECEDENTES OCUPACIONALES",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.medicina.antece16_viejo = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.crea_controles();
		this.st.load();
		this.win.show();
	},
	crea_stores: function () {
		this.st = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_antece_16v",
				format: "json",
			},
			listeners: {
				beforeload: function () {
					this.baseParams.adm = mod.medicina.formatos.record.get("adm");
				},
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"m_antec_id",
				"m_antec_adm",
				"m_antec_fech_ini",
				"m_antec_fech_fin",
				"m_antec_cargo",
				"m_antec_empresa",
				"m_antec_proyec",
				"m_antec_alti",
			],
		});
	},
	crea_controles: function () {
		this.paginador = new Ext.PagingToolbar({
			pageSize: 30,
			store: this.st,
			displayInfo: true,
			displayMsg: "Mostrando {0} - {1} de {2} Formatos",
			emptyMsg: "No Existe Registros",
			plugins: new Ext.ux.ProgressBarPager(),
		});
		this.tbar = new Ext.Toolbar({
			items: [
				"-",
				{
					text: "AGREGAR UN NUEVO ANTECEDENTE",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.nuevo_antece16_viejo.init(null);
					},
				},
				"-",
				{
					text: "GENERAR HOJA DE ANTECEDENTES",
					iconCls: "reporte",
					handler: function () {
						new Ext.Window({
							title:
								"Antecedentes Laborales N° " +
								mod.medicina.antece16_viejo.record.get("adm"),
							width: 800,
							height: 600,
							maximizable: true,
							modal: true,
							closeAction: "close",
							resizable: true,
							html:
								"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=formato_antecede&adm=" +
								mod.medicina.antece16_viejo.record.get("adm") +
								"'></iframe>",
						}).show();
					},
				},
			],
		});
		this.dt_grid = new Ext.grid.GridPanel({
			store: this.st,
			tbar: this.tbar,
			region: "west",
			border: false,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			bbar: this.paginador,
			height: 263,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var record = grid.getStore().getAt(rowIndex);
					mod.medicina.nuevo_antece16_viejo.init(record);
				},
			},
			autoExpandColumn: "cuest_desc",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					header: "INICIO",
					dataIndex: "m_antec_fech_ini",
					width: 70,
				},
				{
					header: "FIN",
					dataIndex: "m_antec_fech_fin",
					width: 70,
				},
				{
					header: "CARGO",
					dataIndex: "m_antec_cargo",
					width: 120,
				},
				{
					id: "cuest_desc",
					header: "EMPRESA",
					dataIndex: "m_antec_empresa",
				},
				{
					header: "ACTIVIDAD",
					dataIndex: "m_antec_proyec",
					width: 120,
				},
				{
					header: "ALTITUD",
					dataIndex: "m_antec_alti",
					width: 120,
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
			width: 800,
			height: 300,
			modal: true,
			title: "ANTECEDENTES OCUPACIONALES",
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
mod.medicina.nuevo_antece16_viejo = {
	rec: null,
	win: null,
	frm: null,
	init: function (r) {
		this.rec = r;
		this.crea_stores();
		this.crea_controles();
		if (this.rec !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_antece_16v",
				format: "json",
				m_antec_id: this.rec.get("m_antec_id"),
				m_antec_adm: this.rec.get("m_antec_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_m_antec_cargo = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_antec_cargo",
				format: "json",
			},
			fields: ["m_antec_cargo"],
			root: "data",
		});
		this.st_m_antec_empresa = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_antec_empresa",
				format: "json",
			},
			fields: ["m_antec_empresa"],
			root: "data",
		});
		this.st_m_antec_proyec = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_antec_proyec",
				format: "json",
			},
			fields: ["m_antec_proyec"],
			root: "data",
		});
		this.st_m_antec_retiro_cmedico = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_antec_retiro_cmedico",
				format: "json",
			},
			fields: ["m_antec_retiro_cmedico"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.m_antec_fech_ini = new Ext.form.DateField({
			fieldLabel: "<b>FECHA DE INICIO</b>",
			format: "Y-m-d",
			id: "startdt",
			vtype: "daterange",
			endDateField: "enddt",
			value: new Date(),
			name: "m_antec_fech_ini",
			anchor: "80%",
			allowBlank: false,
		});
		this.m_antec_fech_fin = new Ext.form.DateField({
			fieldLabel: "<b>FECHA FINAL</b>",
			format: "Y-m-d",
			id: "enddt",
			vtype: "daterange",
			startDateField: "startdt",
			value: new Date(),
			name: "m_antec_fech_fin",
			anchor: "80%",
			allowBlank: false,
		});
		///////////////////////////m_antec_cargo
		this.Tpl_m_antec_cargo = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_antec_cargo}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_antec_cargo = new Ext.form.ComboBox({
			store: this.st_m_antec_cargo,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_antec_cargo,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_antec_cargo",
			displayField: "m_antec_cargo",
			valueField: "m_antec_cargo",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>CARGO O PUESTO DE LABOR</b>",
			mode: "remote",
			anchor: "95%",
		});
		///////////////////////////m_antec_empresa
		this.Tpl_m_antec_empresa = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_antec_empresa}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_antec_empresa = new Ext.form.ComboBox({
			store: this.st_m_antec_empresa,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_antec_empresa,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_antec_empresa",
			displayField: "m_antec_empresa",
			valueField: "m_antec_empresa",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>EMPRESA O CONTRATA</b>",
			mode: "remote",
			anchor: "95%",
		});
		///////////////////////////m_antec_proyec
		this.Tpl_m_antec_proyec = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_antec_proyec}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_antec_proyec = new Ext.form.ComboBox({
			store: this.st_m_antec_proyec,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_antec_proyec,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_antec_proyec",
			displayField: "m_antec_proyec",
			valueField: "m_antec_proyec",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>PROYECTO O EMPRESA DE DESTINO</b>",
			mode: "remote",
			anchor: "95%",
		});
		///////////////////////////m_antec_alti
		this.m_antec_alti = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 3000", "< 3000"],
					["3001 - 4000", "3001 - 4000"],
					["4001 - 4500", "4001 - 4500"],
					["> 4500", "> 4500"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_antec_alti",
			fieldLabel: "<b>ALTITUD LABORAL</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("4001 - 4500");
					descripcion.setRawValue("4001 - 4500");
				},
			},
		});
		///////////////////////////m_antec_suelo
		this.m_antec_suelo = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SUPERFICIE", "SUPERFICIE"],
					["SUBTERRANEO", "SUBTERRANEO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_antec_suelo",
			fieldLabel: "<b>OPERACION EN</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "85%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("SUPERFICIE");
					descripcion.setRawValue("SUPERFICIE");
				},
			},
		});
		///////////////////////////m_antec_suelo
		this.antec_16_riesgos = new Ext.form.CheckboxGroup({
			fieldLabel: "<b>RIESGOS OCUPACIONALES</b>",
			itemCls: "x-check-group-alt",
			columns: 5,
			items: [
				{
					boxLabel: "<b>FISICO</b>",
					name: "m_antec_fisico",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antece16_viejo.m_antec_fisico_hora.enable();
							mod.medicina.nuevo_antece16_viejo.m_antec_fisico_uso.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antece16_viejo.m_antec_fisico_hora.disable();
							mod.medicina.nuevo_antece16_viejo.m_antec_fisico_uso.disable();
						}
					},
				},
				{
					boxLabel: "<b>QUIMICO</b>",
					name: "m_antec_quinico",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antece16_viejo.m_antec_quinico_hora.enable();
							mod.medicina.nuevo_antece16_viejo.m_antec_quinico_uso.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antece16_viejo.m_antec_quinico_hora.disable();
							mod.medicina.nuevo_antece16_viejo.m_antec_quinico_uso.disable();
						}
					},
				},
				{
					boxLabel: "<b>BIOLOGICO</b>",
					name: "m_antec_biologico",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antece16_viejo.m_antec_biologico_hora.enable();
							mod.medicina.nuevo_antece16_viejo.m_antec_biologico_uso.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antece16_viejo.m_antec_biologico_hora.disable();
							mod.medicina.nuevo_antece16_viejo.m_antec_biologico_uso.disable();
						}
					},
				},
				{
					boxLabel: "<b>ERGONOMICO</b>",
					name: "m_antec_ergonom",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antece16_viejo.m_antec_ergonom_hora.enable();
							mod.medicina.nuevo_antece16_viejo.m_antec_ergonom_uso.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antece16_viejo.m_antec_ergonom_hora.disable();
							mod.medicina.nuevo_antece16_viejo.m_antec_ergonom_uso.disable();
						}
					},
				},
				{
					boxLabel: "<b>OTROS</b>",
					name: "m_antec_otros",
					inputValue: "1",
					handler: function (value, checkbox) {
						if (checkbox == true) {
							mod.medicina.nuevo_antece16_viejo.m_antec_otros_hora.enable();
							mod.medicina.nuevo_antece16_viejo.m_antec_otros_uso.enable();
						} else if (checkbox == false) {
							mod.medicina.nuevo_antece16_viejo.m_antec_otros_hora.disable();
							mod.medicina.nuevo_antece16_viejo.m_antec_otros_uso.disable();
						}
					},
				},
			],
		});
		this.m_antec_fisico_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_fisico_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_fisico_uso = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_fisico_uso",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		////////////////////////////////////////////////////////////
		this.m_antec_quinico_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_quinico_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_quinico_uso = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_quinico_uso",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		////////////////////////////////////////////////////////////
		this.m_antec_ergonom_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_ergonom_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_ergonom_uso = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_ergonom_uso",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		////////////////////////////////////////////////////////////
		this.m_antec_biologico_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_biologico_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_biologico_uso = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_biologico_uso",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		////////////////////////////////////////////////////////////
		this.m_antec_otros_hora = new Ext.form.TextField({
			fieldLabel: "<b>Hrs. de EXP</b>",
			name: "m_antec_otros_hora",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});
		this.m_antec_otros_uso = new Ext.form.TextField({
			fieldLabel: "<b>% Uso EPP</b>",
			name: "m_antec_otros_uso",
			maskRe: /[\d]/,
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			allowBlank: false,
			disabled: true,
			anchor: "80%",
		});

		this.m_antec_obser = new Ext.form.TextArea({
			name: "m_antec_obser",
			fieldLabel: "<b>ESPECIFICAR</b>",
			anchor: "95%",
			height: 50,
		});

		this.m_antec_retiro_date = new Ext.form.DateField({
			fieldLabel: "<b>FECHA</b>",
			format: "Y-m-d",
			//            value: new Date(),
			name: "m_antec_retiro_date",
			anchor: "85%",
		});
		///////////////////////////m_antec_retiro_cmedico
		this.Tpl_m_antec_retiro_cmedico = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_antec_retiro_cmedico}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_antec_retiro_cmedico = new Ext.form.ComboBox({
			store: this.st_m_antec_retiro_cmedico,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_antec_retiro_cmedico,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_antec_retiro_cmedico",
			displayField: "m_antec_retiro_cmedico",
			valueField: "m_antec_retiro_cmedico",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>CENTRO MEDICO</b>",
			mode: "remote",
			anchor: "95%",
		});
		this.m_antec_retiro_desc = new Ext.form.TextField({
			fieldLabel: "<b>DESCRIBIR HALLAZGOS</b>",
			name: "m_antec_retiro_desc",
			anchor: "95%",
		});

		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.15,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_fech_ini],
				},
				{
					columnWidth: 0.15,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_fech_fin],
				},
				{
					columnWidth: 0.35,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_cargo],
				},
				{
					columnWidth: 0.35,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_empresa],
				},
				{
					columnWidth: 0.4,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_proyec],
				},
				{
					columnWidth: 0.3,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_alti],
				},
				{
					columnWidth: 0.3,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_suelo],
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
							//                            title: '<b>RIESGOS OCUPACIONALES</b>',
							items: [
								{
									columnWidth: 0.999,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.antec_16_riesgos],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>FISICOS</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_fisico_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_fisico_uso],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>QUIMICO</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_quinico_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_quinico_uso],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>BIOLOGICO</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_biologico_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_biologico_uso],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>ERGONOMICO</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_ergonom_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_ergonom_uso],
								},
							],
						},
					],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.33,
					bodyStyle: "padding:3px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>OTROS</b>",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_otros_hora],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_otros_uso],
								},
							],
						},
					],
				},
				{
					columnWidth: 0.33,
					border: false,
					bodyStyle: "margin: 0 0 0 15px;",
					labelAlign: "top",
					layout: "form",
					items: [this.m_antec_obser],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.999,
					bodyStyle: "padding:5px;",
					items: [
						{
							xtype: "fieldset",
							layout: "column",
							title: "<b>INFORMACION DE EXAMEN DE RETIRO</b>",
							items: [
								{
									columnWidth: 0.15,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_retiro_date],
								},
								{
									columnWidth: 0.43,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_retiro_cmedico],
								},
								{
									columnWidth: 0.42,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_antec_retiro_desc],
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
						mod.medicina.nuevo_antece16_viejo.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						this.frm.getForm().submit({
							params: {
								acction:
									this.rec !== null
										? "update_nuevo_antece16_viejo"
										: "save_nuevo_antece16_viejo",
								adm: mod.medicina.antece16_viejo.record.get("adm"),
								id: this.rec !== null ? this.rec.get("m_antec_id") : null,
								ex_id: mod.medicina.antece16_viejo.record.get("ex_id"),
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.nuevo_antece16_viejo.win.el.unmask();
								mod.medicina.antece16_viejo.st.reload();
								mod.medicina.formatos.st.reload();
								mod.medicina.nuevo_antece16_viejo.win.close();
							},
							failure: function (form, action) {
								mod.medicina.nuevo_antece16_viejo.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.antece16_viejo.st.reload();
								mod.medicina.formatos.st.reload();
								mod.medicina.nuevo_antece16_viejo.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 900,
			height: 570,
			modal: true,
			title: "REGISTRO DE ANTECEDENTES OCUPACIONALES",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.medicina.nuevoOsteo = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.list_osteo_conclu.load();
		this.crea_controles();
		if (this.record.get("st") >= 1) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_nuevoOsteo",
				format: "json",
				m_osteo_adm: mod.medicina.nuevoOsteo.record.get("adm"),
				m_osteo_exa: mod.medicina.nuevoOsteo.record.get("ex_id"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.medicina.nuevoOsteo.val_medico.setValue(r.val_medico);
				//                mod.medicina.nuevoOsteo.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_stores: function () {
		this.list_osteo_conclu = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_osteo_conclu",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: ["osteo_conclu_id", "osteo_conclu_adm", "osteo_conclu_desc"],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm = mod.medicina.nuevoOsteo.record.get("adm");
				},
			},
		});
	},
	crea_controles: function () {
		//m_osteo_trauma
		this.m_osteo_trauma = new Ext.form.TextArea({
			name: "m_osteo_trauma",
			fieldLabel: "<b>TRAUMÁTICOS</b>",
			anchor: "99%",
			height: 40,
		});
		//m_osteo_degenera
		this.m_osteo_degenera = new Ext.form.TextArea({
			name: "m_osteo_degenera",
			fieldLabel: "<b>DEGENERATIVOS</b>",
			anchor: "99%",
			height: 40,
		});
		//m_osteo_congeni
		this.m_osteo_congeni = new Ext.form.TextArea({
			name: "m_osteo_congeni",
			fieldLabel: "<b>CONGÉNITOS</b>",
			anchor: "99%",
			height: 40,
		});
		//m_osteo_quirur
		this.m_osteo_quirur = new Ext.form.TextArea({
			name: "m_osteo_quirur",
			fieldLabel: "<b>QUIRÚRGICOS</b>",
			anchor: "99%",
			height: 40,
		});
		//m_osteo_trata
		this.m_osteo_trata = new Ext.form.TextArea({
			name: "m_osteo_trata",
			fieldLabel: "<b>TRATAMIENTO ACTUAL</b>",
			anchor: "99%",
			height: 35,
		});
		/*===================================================================*/
		this.talla = new Ext.form.TextField({
			fieldLabel: "<b>TALLA</b>",
			name: "talla",
			readOnly: true,
			anchor: "95%",
		});
		this.peso = new Ext.form.TextField({
			fieldLabel: "<b>PESO</b>",
			name: "peso",
			readOnly: true,
			anchor: "95%",
		});
		this.imc = new Ext.form.TextField({
			fieldLabel: "<b>IMC</b>",
			name: "imc ",
			readOnly: true,
			anchor: "95%",
		});
		this.resultado = new Ext.form.TextField({
			fieldLabel: "<b>RESULTADO</b>",
			name: "resultado",
			readOnly: true,
			anchor: "95%",
		});
		/*===================================================================*/
		//m_osteo_cuello_dura_3meses
		this.m_osteo_cuello_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_cuello_time_ini
		this.m_osteo_cuello_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_cuello_dura_dolor
		this.m_osteo_cuello_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_cuello_recib_trata
		this.m_osteo_cuello_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_cuello_dias_trata
		this.m_osteo_cuello_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_espalda_a_dura_3meses
		this.m_osteo_espalda_a_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_espalda_a_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_espalda_a_time_ini
		this.m_osteo_espalda_a_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_espalda_a_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_espalda_a_dura_dolor
		this.m_osteo_espalda_a_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_espalda_a_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_espalda_a_recib_trata
		this.m_osteo_espalda_a_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_espalda_a_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_espalda_a_dias_trata
		this.m_osteo_espalda_a_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_espalda_a_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_espalda_b_dura_3meses
		this.m_osteo_espalda_b_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_espalda_b_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_espalda_b_time_ini
		this.m_osteo_espalda_b_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_espalda_b_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_espalda_b_dura_dolor
		this.m_osteo_espalda_b_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_espalda_b_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_espalda_b_recib_trata
		this.m_osteo_espalda_b_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_espalda_b_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_espalda_b_dias_trata
		this.m_osteo_espalda_b_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_espalda_b_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_hombro_d_dura_3meses
		this.m_osteo_hombro_d_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_d_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_hombro_d_time_ini
		this.m_osteo_hombro_d_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_d_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_hombro_d_dura_dolor
		this.m_osteo_hombro_d_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_d_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_hombro_d_recib_trata
		this.m_osteo_hombro_d_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_d_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_hombro_d_dias_trata
		this.m_osteo_hombro_d_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_d_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_hombro_i_dura_3meses
		this.m_osteo_hombro_i_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_i_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_hombro_i_time_ini
		this.m_osteo_hombro_i_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_i_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_hombro_i_dura_dolor
		this.m_osteo_hombro_i_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_i_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_hombro_i_recib_trata
		this.m_osteo_hombro_i_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_i_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_hombro_i_dias_trata
		this.m_osteo_hombro_i_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_i_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_codo_d_dura_3meses
		this.m_osteo_codo_d_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_d_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_codo_d_time_ini
		this.m_osteo_codo_d_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_d_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_codo_d_dura_dolor
		this.m_osteo_codo_d_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_d_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_codo_d_recib_trata
		this.m_osteo_codo_d_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_d_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_codo_d_dias_trata
		this.m_osteo_codo_d_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_d_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_codo_i_dura_3meses
		this.m_osteo_codo_i_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_i_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_codo_i_time_ini
		this.m_osteo_codo_i_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_i_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_codo_i_dura_dolor
		this.m_osteo_codo_i_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_i_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_codo_i_recib_trata
		this.m_osteo_codo_i_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_i_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_codo_i_dias_trata
		this.m_osteo_codo_i_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_i_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_mano_d_dura_3meses
		this.m_osteo_mano_d_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mano_d_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_mano_d_time_ini
		this.m_osteo_mano_d_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mano_d_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_mano_d_dura_dolor
		this.m_osteo_mano_d_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mano_d_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_mano_d_recib_trata
		this.m_osteo_mano_d_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mano_d_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_mano_d_dias_trata
		this.m_osteo_mano_d_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mano_d_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_mano_i_dura_3meses
		this.m_osteo_mano_i_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mano_i_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_mano_i_time_ini
		this.m_osteo_mano_i_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mano_i_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_mano_i_dura_dolor
		this.m_osteo_mano_i_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mano_i_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_mano_i_recib_trata
		this.m_osteo_mano_i_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mano_i_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_mano_i_dias_trata
		this.m_osteo_mano_i_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mano_i_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_muslo_d_dura_3meses
		this.m_osteo_muslo_d_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muslo_d_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_muslo_d_time_ini
		this.m_osteo_muslo_d_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muslo_d_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_muslo_d_dura_dolor
		this.m_osteo_muslo_d_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muslo_d_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_muslo_d_recib_trata
		this.m_osteo_muslo_d_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muslo_d_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_muslo_d_dias_trata
		this.m_osteo_muslo_d_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muslo_d_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_muslo_i_dura_3meses
		this.m_osteo_muslo_i_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muslo_i_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_muslo_i_time_ini
		this.m_osteo_muslo_i_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muslo_i_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_muslo_i_dura_dolor
		this.m_osteo_muslo_i_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muslo_i_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_muslo_i_recib_trata
		this.m_osteo_muslo_i_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muslo_i_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_muslo_i_dias_trata
		this.m_osteo_muslo_i_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muslo_i_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_rodilla_d_dura_3meses
		this.m_osteo_rodilla_d_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodilla_d_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_rodilla_d_time_ini
		this.m_osteo_rodilla_d_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodilla_d_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_rodilla_d_dura_dolor
		this.m_osteo_rodilla_d_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodilla_d_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_rodilla_d_recib_trata
		this.m_osteo_rodilla_d_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodilla_d_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_rodilla_d_dias_trata
		this.m_osteo_rodilla_d_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodilla_d_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_rodilla_i_dura_3meses
		this.m_osteo_rodilla_i_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodilla_i_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_rodilla_i_time_ini
		this.m_osteo_rodilla_i_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodilla_i_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_rodilla_i_dura_dolor
		this.m_osteo_rodilla_i_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodilla_i_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_rodilla_i_recib_trata
		this.m_osteo_rodilla_i_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodilla_i_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_rodilla_i_dias_trata
		this.m_osteo_rodilla_i_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodilla_i_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_pies_d_dura_3meses
		this.m_osteo_pies_d_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_pies_d_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_pies_d_time_ini
		this.m_osteo_pies_d_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_pies_d_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_pies_d_dura_dolor
		this.m_osteo_pies_d_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_pies_d_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_pies_d_recib_trata
		this.m_osteo_pies_d_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_pies_d_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_pies_d_dias_trata
		this.m_osteo_pies_d_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_pies_d_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_pies_i_dura_3meses
		this.m_osteo_pies_i_dura_3meses = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["1-7 DÍAS", "1-7 DÍAS"],
					["8-30 DÍAS", "8-30 DÍAS"],
					["30 DÍAS DISCONTINUOS", "30 DÍAS DISCONTINUOS"],
					["PERMANENTE", "PERMANENTE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_pies_i_dura_3meses",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_pies_i_time_ini
		this.m_osteo_pies_i_time_ini = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 MES", "<1 MES"],
					["1-3 MESES", "1-3 MESES"],
					["4-12 MESES", "4-12 MESES"],
					["1 AÑO A MAS", "1 AÑO A MAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_pies_i_time_ini",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_pies_i_dura_dolor
		this.m_osteo_pies_i_dura_dolor = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["<1 HORA", "<1 HORA"],
					["1-24 HORAS", "1-24 HORAS"],
					["1-7 DÍAS", "1-7 DÍAS"],
					["1-4 SEMANAS", "1-4 SEMANAS"],
					[">1 MES", ">1 MES"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_pies_i_dura_dolor",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_pies_i_recib_trata
		this.m_osteo_pies_i_recib_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_pies_i_recib_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_pies_i_dias_trata
		this.m_osteo_pies_i_dias_trata = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["< 1 SEMANA", "< 1 SEMANA"],
					["1 SEMANA", "1 SEMANA"],
					["15 DIAS", "15 DIAS"],
					["MAS DE 15 DIAS", "MAS DE 15 DIAS"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_pies_i_dias_trata",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_anames_obs
		this.m_osteo_anames_obs = new Ext.form.TextArea({
			name: "m_osteo_anames_obs",
			fieldLabel: "<b>OBSERVACIONES</b>",
			anchor: "96%",
			height: 35,
		});
		//m_osteo_lordo_cervic
		this.m_osteo_lordo_cervic = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["INCREMENTADA", "INCREMENTADA"],
					["DISMINUIDA", "DISMINUIDA"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_lordo_cervic",
			fieldLabel: "<b>LORDOSIS CERVICAL</b>",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NORMAL");
					descripcion.setRawValue("NORMAL");
				},
			},
		});
		//m_osteo_cifosis
		this.m_osteo_cifosis = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["INCREMENTADA", "INCREMENTADA"],
					["DISMINUIDA", "DISMINUIDA"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cifosis",
			fieldLabel: "<b>CIFOSIS DORSAL</b>",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NORMAL");
					descripcion.setRawValue("NORMAL");
				},
			},
		});
		//m_osteo_lordo_lumbar
		this.m_osteo_lordo_lumbar = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["INCREMENTADA", "INCREMENTADA"],
					["DISMINUIDA", "DISMINUIDA"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_lordo_lumbar",
			allowBlank: false,
			fieldLabel: "<b>LORDOSIS LUMBAR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NORMAL");
					descripcion.setRawValue("NORMAL");
				},
			},
		});
		//m_osteo_desvia_lat_halla
		this.m_osteo_desvia_lat_halla = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_desvia_lat_halla",
			allowBlank: false,
			fieldLabel: "<b>HALLAZGOS</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_desvia_lat_escolio
		this.m_osteo_desvia_lat_escolio = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["DORSAL", "DORSAL"],
					["LUMBAR", "LUMBAR"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_desvia_lat_escolio",
			allowBlank: false,
			fieldLabel: "<b>ESCOLIOSIS</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_apofisis
		this.m_osteo_apofisis = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_apofisis",
			allowBlank: false,
			fieldLabel: "<b>APÓFISIS ESPINOSAS DOLOROSAS</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_apofisis_obs
		this.m_osteo_apofisis_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_apofisis_obs",
			anchor: "95%",
		});
		//m_osteo_contra_musc_cervic
		this.m_osteo_contra_musc_cervic = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_contra_musc_cervic",
			allowBlank: false,
			fieldLabel: "<b>CONTRACTURA MUSCULAR CERVICAL</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_contra_musc_cervic_obs
		this.m_osteo_contra_musc_cervic_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_contra_musc_cervic_obs",
			anchor: "95%",
		});
		//m_osteo_contra_musc_lumbar
		this.m_osteo_contra_musc_lumbar = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_contra_musc_lumbar",
			allowBlank: false,
			fieldLabel: "<b>CONTRACTURA MUSCULAR LUMBAR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_osteo_contra_musc_lumbar_obs
		this.m_osteo_contra_musc_lumbar_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_contra_musc_lumbar_obs",
			anchor: "95%",
		});

		//GATURRO EMPIEZA AQUI==>
		//m_osteo_cuello_flex
		this.m_osteo_cuello_flex = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_flex",
			allowBlank: false,
			fieldLabel: "<b>FLEXIÓN (0-45°)</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cuello_flex_lat_d
		this.m_osteo_cuello_flex_lat_d = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_flex_lat_d",
			allowBlank: false,
			fieldLabel: "<b>FLEXIÓN LATERAL (0-45°) DER</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cuello_flex_lat_i
		this.m_osteo_cuello_flex_lat_i = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_flex_lat_i",
			allowBlank: false,
			fieldLabel: "<b>FLEXIÓN LATERAL (0-45°) IZQ</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cuello_ext
		this.m_osteo_cuello_ext = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_ext",
			allowBlank: false,
			fieldLabel: "<b>EXTENSIÓN (0-45°)</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cuello_ext_rot_d
		this.m_osteo_cuello_ext_rot_d = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_ext_rot_d",
			allowBlank: false,
			fieldLabel: "<b>ROTACIÓN (0-60°) DER</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cuello_ext_rot_i
		this.m_osteo_cuello_ext_rot_i = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cuello_ext_rot_i",
			allowBlank: false,
			fieldLabel: "<b>ROTACIÓN (0-60°) IZQ</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tronco_flex
		this.m_osteo_tronco_flex = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tronco_flex",
			allowBlank: false,
			fieldLabel: "<b>FLEXIÓN (0-80°,10CM)</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tronco_flex_lat_d
		this.m_osteo_tronco_flex_lat_d = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tronco_flex_lat_d",
			allowBlank: false,
			fieldLabel: "<b>FLEXIÓN LATERAL (0-35°) DER</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tronco_flex_lat_i
		this.m_osteo_tronco_flex_lat_i = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tronco_flex_lat_i",
			allowBlank: false,
			fieldLabel: "<b>FLEXIÓN LATERAL (0-35°) IZQ</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tronco_ext
		this.m_osteo_tronco_ext = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tronco_ext",
			allowBlank: false,
			fieldLabel: "<b>EXTENSIÓN (0-20-30°)</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tronco_ext_rot_d
		this.m_osteo_tronco_ext_rot_d = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tronco_ext_rot_d",
			allowBlank: false,
			fieldLabel: "<b>ROTACIÓN  (0-45°) DER</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tronco_ext_rot_i
		this.m_osteo_tronco_ext_rot_i = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tronco_ext_rot_i",
			allowBlank: false,
			fieldLabel: "<b>ROTACIÓN (0-45°) IZQ</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hiper_acor_f_coment
		this.m_osteo_hiper_acor_f_coment = new Ext.form.TextField({
			fieldLabel: "<b>HIPERMOVILIDAD/ACORTAMIENTOS, FUERZA MUSCULAR</b>",
			name: "m_osteo_hiper_acor_f_coment",
			anchor: "95%",
		});
		//m_osteo_hombro_flex_der
		this.m_osteo_hombro_flex_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_flex_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_flex_izq
		this.m_osteo_hombro_flex_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_flex_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_flex_fuerza
		this.m_osteo_hombro_flex_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_flex_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_flex_tono
		this.m_osteo_hombro_flex_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_flex_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_flex_color
		this.m_osteo_hombro_flex_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_flex_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_adu_h_der
		this.m_osteo_hombro_adu_h_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_adu_h_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_adu_h_izq
		this.m_osteo_hombro_adu_h_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_adu_h_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_adu_h_fuerza
		this.m_osteo_hombro_adu_h_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_adu_h_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_adu_h_tono
		this.m_osteo_hombro_adu_h_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_adu_h_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_adu_h_color
		this.m_osteo_hombro_adu_h_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_adu_h_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_ext_der
		this.m_osteo_hombro_ext_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_ext_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_ext_izq
		this.m_osteo_hombro_ext_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_ext_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_ext_fuerza
		this.m_osteo_hombro_ext_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_ext_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_ext_tono
		this.m_osteo_hombro_ext_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_ext_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_ext_color
		this.m_osteo_hombro_ext_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_ext_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_rot_in_der
		this.m_osteo_hombro_rot_in_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_rot_in_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_rot_in_izq
		this.m_osteo_hombro_rot_in_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_rot_in_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_rot_in_fuerza
		this.m_osteo_hombro_rot_in_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_rot_in_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_rot_in_tono
		this.m_osteo_hombro_rot_in_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_rot_in_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_rot_in_color
		this.m_osteo_hombro_rot_in_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_rot_in_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_abduc_der
		this.m_osteo_hombro_abduc_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_abduc_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_abduc_izq
		this.m_osteo_hombro_abduc_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_abduc_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_abduc_fuerza
		this.m_osteo_hombro_abduc_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_abduc_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_abduc_tono
		this.m_osteo_hombro_abduc_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_abduc_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_abduc_color
		this.m_osteo_hombro_abduc_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_abduc_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_rot_ex_der
		this.m_osteo_hombro_rot_ex_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_rot_ex_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_rot_ex_izq
		this.m_osteo_hombro_rot_ex_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_rot_ex_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_rot_ex_fuerza
		this.m_osteo_hombro_rot_ex_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_rot_ex_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_rot_ex_tono
		this.m_osteo_hombro_rot_ex_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_rot_ex_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_rot_ex_color
		this.m_osteo_hombro_rot_ex_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_rot_ex_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_abd_h_der
		this.m_osteo_hombro_abd_h_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_abd_h_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_abd_h_izq
		this.m_osteo_hombro_abd_h_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_abd_h_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_abd_h_fuerza
		this.m_osteo_hombro_abd_h_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_abd_h_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_abd_h_tono
		this.m_osteo_hombro_abd_h_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_abd_h_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_hombro_abd_h_color
		this.m_osteo_hombro_abd_h_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_hombro_abd_h_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_flex_der
		this.m_osteo_codo_flex_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_flex_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_flex_izq
		this.m_osteo_codo_flex_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_flex_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_flex_fuerza
		this.m_osteo_codo_flex_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_flex_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_flex_tono
		this.m_osteo_codo_flex_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_flex_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_flex_color
		this.m_osteo_codo_flex_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_flex_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});

		//m_osteo_codo_supina_der
		this.m_osteo_codo_supina_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_supina_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_supina_izq
		this.m_osteo_codo_supina_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_supina_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_supina_fuerza
		this.m_osteo_codo_supina_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_supina_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_supina_tono
		this.m_osteo_codo_supina_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_supina_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_supina_color
		this.m_osteo_codo_supina_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_supina_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_ext_der
		this.m_osteo_codo_ext_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_ext_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_ext_izq
		this.m_osteo_codo_ext_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_ext_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_ext_fuerza
		this.m_osteo_codo_ext_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_ext_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_ext_tono
		this.m_osteo_codo_ext_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_ext_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_ext_color
		this.m_osteo_codo_ext_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_ext_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_prona_der
		this.m_osteo_codo_prona_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_prona_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_prona_izq
		this.m_osteo_codo_prona_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_prona_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_prona_fuerza
		this.m_osteo_codo_prona_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_prona_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_prona_tono
		this.m_osteo_codo_prona_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_prona_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_codo_prona_color
		this.m_osteo_codo_prona_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_codo_prona_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_flex_der
		this.m_osteo_muneca_flex_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_flex_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_flex_izq
		this.m_osteo_muneca_flex_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_flex_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_flex_fuerza
		this.m_osteo_muneca_flex_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_flex_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_flex_tono
		this.m_osteo_muneca_flex_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_flex_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_flex_color
		this.m_osteo_muneca_flex_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_flex_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_des_cubi_der
		this.m_osteo_muneca_des_cubi_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_des_cubi_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_des_cubi_izq
		this.m_osteo_muneca_des_cubi_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_des_cubi_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_des_cubi_fuerza
		this.m_osteo_muneca_des_cubi_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_des_cubi_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_des_cubi_tono
		this.m_osteo_muneca_des_cubi_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_des_cubi_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_des_cubi_color
		this.m_osteo_muneca_des_cubi_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_des_cubi_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_ext_der
		this.m_osteo_muneca_ext_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_ext_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_ext_izq
		this.m_osteo_muneca_ext_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_ext_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_ext_fuerza
		this.m_osteo_muneca_ext_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_ext_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_ext_tono
		this.m_osteo_muneca_ext_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_ext_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_ext_color
		this.m_osteo_muneca_ext_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_ext_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_des_radi_der
		this.m_osteo_muneca_des_radi_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_des_radi_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_des_radi_izq
		this.m_osteo_muneca_des_radi_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_des_radi_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_des_radi_fuerza
		this.m_osteo_muneca_des_radi_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_des_radi_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_des_radi_tono
		this.m_osteo_muneca_des_radi_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_des_radi_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_muneca_des_radi_color
		this.m_osteo_muneca_des_radi_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_muneca_des_radi_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_sup_acor_fu_sen_coment
		this.m_osteo_sup_acor_fu_sen_coment = new Ext.form.TextField({
			fieldLabel:
				"<b>ACORTAMIENTOS/FLACIDECES, FUERZA MUSCULAR, SENSIBILIDAD</b>",
			name: "m_osteo_sup_acor_fu_sen_coment",
			anchor: "95%",
		});
		//m_osteo_cader_flex_der
		this.m_osteo_cader_flex_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_flex_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_flex_izq
		this.m_osteo_cader_flex_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_flex_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_flex_fuerza
		this.m_osteo_cader_flex_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_flex_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_flex_tono
		this.m_osteo_cader_flex_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_flex_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_flex_color
		this.m_osteo_cader_flex_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_flex_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_aduc_der
		this.m_osteo_cader_aduc_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_aduc_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_aduc_izq
		this.m_osteo_cader_aduc_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_aduc_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_aduc_fuerza
		this.m_osteo_cader_aduc_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_aduc_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_aduc_tono
		this.m_osteo_cader_aduc_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_aduc_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_aduc_color
		this.m_osteo_cader_aduc_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_aduc_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_ext_der
		this.m_osteo_cader_ext_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_ext_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_ext_izq
		this.m_osteo_cader_ext_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_ext_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_ext_fuerza
		this.m_osteo_cader_ext_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_ext_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_ext_tono
		this.m_osteo_cader_ext_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_ext_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_ext_color
		this.m_osteo_cader_ext_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_ext_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_rota_int_der
		this.m_osteo_cader_rota_int_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_rota_int_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_rota_int_izq
		this.m_osteo_cader_rota_int_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_rota_int_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_rota_int_fuerza
		this.m_osteo_cader_rota_int_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_rota_int_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_rota_int_tono
		this.m_osteo_cader_rota_int_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_rota_int_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_rota_int_color
		this.m_osteo_cader_rota_int_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_rota_int_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_abduc_der
		this.m_osteo_cader_abduc_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_abduc_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_abduc_izq
		this.m_osteo_cader_abduc_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_abduc_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_abduc_fuerza
		this.m_osteo_cader_abduc_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_abduc_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_abduc_tono
		this.m_osteo_cader_abduc_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_abduc_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_abduc_color
		this.m_osteo_cader_abduc_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_abduc_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_rota_ext_der
		this.m_osteo_cader_rota_ext_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_rota_ext_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_rota_ext_izq
		this.m_osteo_cader_rota_ext_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_rota_ext_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_rota_ext_fuerza
		this.m_osteo_cader_rota_ext_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_rota_ext_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_rota_ext_tono
		this.m_osteo_cader_rota_ext_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_rota_ext_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_cader_rota_ext_color
		this.m_osteo_cader_rota_ext_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_cader_rota_ext_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_flex_der
		this.m_osteo_rodill_flex_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_flex_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_flex_izq
		this.m_osteo_rodill_flex_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_flex_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_flex_fuerza
		this.m_osteo_rodill_flex_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_flex_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_flex_tono
		this.m_osteo_rodill_flex_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_flex_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_flex_color
		this.m_osteo_rodill_flex_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_flex_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_rota_tibi_der
		this.m_osteo_rodill_rota_tibi_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_rota_tibi_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_rota_tibi_izq
		this.m_osteo_rodill_rota_tibi_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_rota_tibi_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_rota_tibi_fuerza
		this.m_osteo_rodill_rota_tibi_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_rota_tibi_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_rota_tibi_tono
		this.m_osteo_rodill_rota_tibi_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_rota_tibi_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_rota_tibi_color
		this.m_osteo_rodill_rota_tibi_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_rota_tibi_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_ext_der
		this.m_osteo_rodill_ext_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_ext_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_ext_izq
		this.m_osteo_rodill_ext_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_ext_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_ext_fuerza
		this.m_osteo_rodill_ext_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_ext_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_ext_tono
		this.m_osteo_rodill_ext_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_ext_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_ext_color
		this.m_osteo_rodill_ext_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_rodill_ext_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_rodill_dorsi_der
		this.m_osteo_tobill_dorsi_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_dorsi_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_dorsi_izq
		this.m_osteo_tobill_dorsi_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_dorsi_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_dorsi_fuerza
		this.m_osteo_tobill_dorsi_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_dorsi_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_dorsi_tono
		this.m_osteo_tobill_dorsi_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_dorsi_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_dorsi_color
		this.m_osteo_tobill_dorsi_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_dorsi_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_inver_der
		this.m_osteo_tobill_inver_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_inver_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_inver_izq
		this.m_osteo_tobill_inver_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_inver_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_inver_fuerza
		this.m_osteo_tobill_inver_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_inver_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_inver_tono
		this.m_osteo_tobill_inver_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_inver_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_inver_color
		this.m_osteo_tobill_inver_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_inver_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_flex_plan_der
		this.m_osteo_tobill_flex_plan_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_flex_plan_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_flex_plan_izq
		this.m_osteo_tobill_flex_plan_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_flex_plan_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_flex_plan_fuerza
		this.m_osteo_tobill_flex_plan_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_flex_plan_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_flex_plan_tono
		this.m_osteo_tobill_flex_plan_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_flex_plan_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_flex_plan_color
		this.m_osteo_tobill_flex_plan_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_flex_plan_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_ever_der
		this.m_osteo_tobill_ever_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_ever_der",
			allowBlank: false,
			fieldLabel: "<b>DERECHO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_ever_izq
		this.m_osteo_tobill_ever_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_ever_izq",
			allowBlank: false,
			fieldLabel: "<b>IZQUIERDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_ever_fuerza
		this.m_osteo_tobill_ever_fuerza = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_ever_fuerza",
			allowBlank: false,
			fieldLabel: "<b>FUERZA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_ever_tono
		this.m_osteo_tobill_ever_tono = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_ever_tono",
			allowBlank: false,
			fieldLabel: "<b>TONO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_tobill_ever_color
		this.m_osteo_tobill_ever_color = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["N", "N"],
					["A", "A"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_tobill_ever_color",
			allowBlank: false,
			fieldLabel: "<b>COLOR</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("N");
					descripcion.setRawValue("N");
				},
			},
		});
		//m_osteo_inf_acor_fu_sen_coment
		this.m_osteo_inf_acor_fu_sen_coment = new Ext.form.TextField({
			fieldLabel:
				"<b>ACORTAMIENTOS/FLACIDECES, FUERZA MUSCULAR, SENSIBILIDAD </b>",
			name: "m_osteo_inf_acor_fu_sen_coment",
			anchor: "95%",
		});

		//m_osteo_test_jobe_der
		this.m_osteo_test_jobe_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_test_jobe_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_test_jobe_der_obs
		this.m_osteo_test_jobe_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_test_jobe_der_obs",
			anchor: "99%",
		});
		//m_osteo_test_jobe_izq
		this.m_osteo_test_jobe_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_test_jobe_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_test_jobe_izq_obs
		this.m_osteo_test_jobe_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_test_jobe_izq_obs",
			anchor: "99%",
		});
		//m_osteo_mani_apley_der
		this.m_osteo_mani_apley_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_apley_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_apley_der_obs
		this.m_osteo_mani_apley_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_apley_der_obs",
			anchor: "99%",
		});
		//m_osteo_mani_apley_izq
		this.m_osteo_mani_apley_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_apley_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_apley_izq_obs
		this.m_osteo_mani_apley_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_apley_izq_obs",
			anchor: "99%",
		});
		//m_osteo_palpa_epi_lat_der
		this.m_osteo_palpa_epi_lat_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_palpa_epi_lat_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_palpa_epi_lat_der_obs
		this.m_osteo_palpa_epi_lat_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_palpa_epi_lat_der_obs",
			anchor: "99%",
		});
		//m_osteo_palpa_epi_lat_izq
		this.m_osteo_palpa_epi_lat_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_palpa_epi_lat_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_palpa_epi_lat_izq_obs
		this.m_osteo_palpa_epi_lat_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_palpa_epi_lat_izq_obs",
			anchor: "99%",
		});
		//m_osteo_palpa_epi_med_der
		this.m_osteo_palpa_epi_med_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_palpa_epi_med_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_palpa_epi_med_der_obs
		this.m_osteo_palpa_epi_med_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_palpa_epi_med_der_obs",
			anchor: "99%",
		});
		//m_osteo_palpa_epi_med_izq
		this.m_osteo_palpa_epi_med_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_palpa_epi_med_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_palpa_epi_med_izq_obs
		this.m_osteo_palpa_epi_med_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_palpa_epi_med_izq_obs",
			anchor: "99%",
		});
		//m_osteo_test_phalen_der
		this.m_osteo_test_phalen_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_test_phalen_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_test_phalen_der_obs
		this.m_osteo_test_phalen_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_test_phalen_der_obs",
			anchor: "99%",
		});
		//m_osteo_test_phalen_izq
		this.m_osteo_test_phalen_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_test_phalen_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_test_phalen_izq_obs
		this.m_osteo_test_phalen_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_test_phalen_izq_obs",
			anchor: "99%",
		});
		//m_osteo_test_tinel_der
		this.m_osteo_test_tinel_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_test_tinel_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_test_tinel_der_obs
		this.m_osteo_test_tinel_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_test_tinel_der_obs",
			anchor: "99%",
		});
		//m_osteo_test_tinel_izq
		this.m_osteo_test_tinel_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_test_tinel_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_test_tinel_izq_obs
		this.m_osteo_test_tinel_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_test_tinel_izq_obs",
			anchor: "99%",
		});
		//m_osteo_test_finkelstein_der
		this.m_osteo_test_finkelstein_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_test_finkelstein_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_test_finkelstein_der_obs
		this.m_osteo_test_finkelstein_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_test_finkelstein_der_obs",
			anchor: "99%",
		});
		//m_osteo_test_finkelstein_izq
		this.m_osteo_test_finkelstein_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_test_finkelstein_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_test_finkelstein_izq_obs
		this.m_osteo_test_finkelstein_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_test_finkelstein_izq_obs",
			anchor: "99%",
		});

		//m_osteo_mani_lasegue_der
		this.m_osteo_mani_lasegue_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_lasegue_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_lasegue_der_obs
		this.m_osteo_mani_lasegue_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_lasegue_der_obs",
			anchor: "99%",
		});
		//m_osteo_mani_lasegue_izq
		this.m_osteo_mani_lasegue_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_lasegue_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_lasegue_izq_obs
		this.m_osteo_mani_lasegue_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_lasegue_izq_obs",
			anchor: "99%",
		});
		//m_osteo_mani_bradga_der
		this.m_osteo_mani_bradga_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_bradga_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_bradga_der_obs
		this.m_osteo_mani_bradga_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_bradga_der_obs",
			anchor: "99%",
		});
		//m_osteo_mani_bradga_izq
		this.m_osteo_mani_bradga_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_bradga_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_bradga_izq_obs
		this.m_osteo_mani_bradga_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_bradga_izq_obs",
			anchor: "99%",
		});
		//m_osteo_mani_thomas_der
		this.m_osteo_mani_thomas_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_thomas_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_thomas_der_obs
		this.m_osteo_mani_thomas_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_thomas_der_obs",
			anchor: "99%",
		});
		//m_osteo_mani_thomas_izq
		this.m_osteo_mani_thomas_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_thomas_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_thomas_izq_obs
		this.m_osteo_mani_thomas_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_thomas_izq_obs",
			anchor: "99%",
		});
		//m_osteo_mani_fabere_der
		this.m_osteo_mani_fabere_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_fabere_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_fabere_der_obs
		this.m_osteo_mani_fabere_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_fabere_der_obs",
			anchor: "99%",
		});
		//m_osteo_mani_fabere_izq
		this.m_osteo_mani_fabere_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_fabere_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_fabere_izq_obs
		this.m_osteo_mani_fabere_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_fabere_izq_obs",
			anchor: "99%",
		});
		//m_osteo_mani_varo_der
		this.m_osteo_mani_varo_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_varo_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_varo_der_obs
		this.m_osteo_mani_varo_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_varo_der_obs",
			anchor: "99%",
		});
		//m_osteo_mani_varo_izq
		this.m_osteo_mani_varo_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_varo_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_varo_izq_obs
		this.m_osteo_mani_varo_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_varo_izq_obs",
			anchor: "99%",
		});
		//m_osteo_mani_cajon_der
		this.m_osteo_mani_cajon_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_cajon_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_cajon_der_obs
		this.m_osteo_mani_cajon_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_cajon_der_obs",
			anchor: "99%",
		});
		//m_osteo_mani_cajon_izq
		this.m_osteo_mani_cajon_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_mani_cajon_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_mani_cajon_izq_obs
		this.m_osteo_mani_cajon_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_mani_cajon_izq_obs",
			anchor: "99%",
		});
		//m_osteo_refle_bicipi_der
		this.m_osteo_refle_bicipi_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_refle_bicipi_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_refle_bicipi_der_obs
		this.m_osteo_refle_bicipi_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_refle_bicipi_der_obs",
			anchor: "99%",
		});
		//m_osteo_refle_bicipi_izq
		this.m_osteo_refle_bicipi_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_refle_bicipi_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_refle_bicipi_izq_obs
		this.m_osteo_refle_bicipi_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_refle_bicipi_izq_obs",
			anchor: "99%",
		});
		//m_osteo_refle_trici_der
		this.m_osteo_refle_trici_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_refle_trici_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_refle_trici_der_obs
		this.m_osteo_refle_trici_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_refle_trici_der_obs",
			anchor: "99%",
		});
		//m_osteo_refle_trici_izq
		this.m_osteo_refle_trici_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_refle_trici_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_refle_trici_izq_obs
		this.m_osteo_refle_trici_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_refle_trici_izq_obs",
			anchor: "99%",
		});
		//m_osteo_refle_patelar_der
		this.m_osteo_refle_patelar_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_refle_patelar_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_refle_patelar_der_obs
		this.m_osteo_refle_patelar_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_refle_patelar_der_obs",
			anchor: "99%",
		});
		//m_osteo_refle_patelar_izq
		this.m_osteo_refle_patelar_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_refle_patelar_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_refle_patelar_izq_obs
		this.m_osteo_refle_patelar_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_refle_patelar_izq_obs",
			anchor: "99%",
		});
		//m_osteo_refle_aquilia_der
		this.m_osteo_refle_aquilia_der = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_refle_aquilia_der",
			allowBlank: false,
			fieldLabel: "<b>D</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_refle_aquilia_der_obs
		this.m_osteo_refle_aquilia_der_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_refle_aquilia_der_obs",
			anchor: "99%",
		});
		//m_osteo_refle_aquilia_izq
		this.m_osteo_refle_aquilia_izq = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NEGATIVO", "NEGATIVO"],
					["POSITIVO", "POSITIVO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_osteo_refle_aquilia_izq",
			allowBlank: false,
			fieldLabel: "<b>I</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NEGATIVO");
					descripcion.setRawValue("NEGATIVO");
				},
			},
		});
		//m_osteo_refle_aquilia_izq_obs
		this.m_osteo_refle_aquilia_izq_obs = new Ext.form.TextField({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_osteo_refle_aquilia_izq_obs",
			anchor: "99%",
		});
		//m_osteo_aptitud
		this.m_osteo_aptitud = new Ext.form.RadioGroup({
			fieldLabel: "<b>APTITUD</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{
					boxLabel: "APTO",
					name: "m_osteo_aptitud",
					inputValue: "APTO",
					checked: true,
				},
				{
					boxLabel: "APTO CON OBSERVACION",
					name: "m_osteo_aptitud",
					inputValue: "APTO CON OBSERVACION",
				},
				{ boxLabel: "NO APTO", name: "m_osteo_aptitud", inputValue: "NO APTO" },
			],
		});
		//        this.m_osteo_aptitud = new Ext.form.ComboBox({
		//            store: new Ext.data.ArrayStore({
		//                fields: ['campo', 'descripcion'],
		//                data: [["APTO", 'APTO'], ["APTO CON RESTRICCIONES", 'APTO CON RESTRICCIONES'], ["NO APTO", 'NO APTO']]
		//            }),
		//            displayField: 'descripcion',
		//            valueField: 'campo',
		//            hiddenName: 'm_osteo_aptitud',
		//            allowBlank: false,
		//            fieldLabel: '<b>APTITUD</b>',
		//            typeAhead: false, editable: false,
		//            mode: 'local',
		//            forceSelection: true,
		//            triggerAction: 'all',
		//            emptyText: 'Seleccione...',
		//            selectOnFocus: true,
		//            anchor: '90%',
		//            width: 100,
		//            listeners: {
		//                afterrender: function (descripcion) {
		//                    descripcion.setValue('APTO');
		//                    descripcion.setRawValue('APTO');
		//                }
		//            }
		//        });

		this.tbar4 = new Ext.Toolbar({
			items: [
				'<b style="color:#000000;">CONCLUSIONES Y RECOMENDACIONES</b>',
				"-",
				"->",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.osteo_conclusion.init(null);
					},
				},
			],
		});
		this.dt_grid4 = new Ext.grid.GridPanel({
			store: this.list_osteo_conclu,
			region: "west",
			border: true,
			tbar: this.tbar4,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			height: 260,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var rec = grid.getStore().getAt(rowIndex);
					mod.medicina.osteo_conclusion.init(rec);
				},
			},
			autoExpandColumn: "diag",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "diag",
					header: "CONCLUSIONES Y RECOMENDACIONES",
					dataIndex: "osteo_conclu_desc",
				},
			],
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			border: false,
			layout: "accordion",
			layoutConfig: {
				titleCollapse: true,
				animate: true,
				hideCollapseTool: true,
			},
			items: [
				{
					title:
						"<b>--->  ESTADO OCUPACIONAL - RIESGOS OCUPACIONALES - EXAMEN MUJERES - ANTECEDENTES LABORALES - HABITOS - ANTECEDENTES FAMILIARES</b>",
					iconCls: "demo2",
					layout: "column",
					autoScroll: true,
					border: false,
					bodyStyle: "padding:10px 10px 20px 10px;",
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.7,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "ANTECEDENTES MÚSCULO - ESQUELÉTICOS:",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_trauma],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_degenera],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_congeni],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_quirur],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_trata],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.3,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "VALORACION FISICA",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.talla],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.peso],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.imc],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.resultado],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "AMNANESIS",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:5px 0;width:173px;height:30px;float:left;"></div>\n\
                                                   <div style="padding:5px 0;width:177px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>DURACIÓN DE LAS MOLESTIAS EN LOS ULTIMOS 3 MESES</h3></div>\n\
                                                   <div style="padding:5px 0;width:137px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>TIEMPO DE INICIO DE MOLESTIAS</h3></div>\n\
                                                   <div style="padding:5px 0;width:137px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>DURACIÓN DE CADA EPISODIO  DE DOLOR</h3></div>\n\
                                                   <div style="padding:5px 0;width:137px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>RECIBIÓ TRATAMIENTO MÉDICO</h3></div>\n\
                                                   <div style="padding:5px 0;width:137px;height:30px;border: 1px solid #267ED7;text-align:center;float:left;"><h3>DÍAS DE TRATAMIENTO</h3></div>\n\
                                                   ',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;">\n\
                                                    <h3>CUELLO</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_cuello_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_cuello_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_cuello_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_cuello_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_cuello_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    ESPALDA ALTA</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_espalda_a_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_espalda_a_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_espalda_a_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_espalda_a_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_espalda_a_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    ESPALDA BAJA</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_espalda_b_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_espalda_b_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_espalda_b_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_espalda_b_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_espalda_b_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    HOMBRO DERECHO</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_hombro_d_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_hombro_d_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_hombro_d_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_hombro_d_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_hombro_d_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    HOMBRO IZQUIERDO</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_hombro_i_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_hombro_i_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_hombro_i_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_hombro_i_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_hombro_i_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    CODOS / ANTEBRAZOS DER</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_codo_d_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_codo_d_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_codo_d_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_codo_d_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_codo_d_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    CODOS / ANTEBRAZOS IZQ</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_codo_i_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_codo_i_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_codo_i_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_codo_i_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_codo_i_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    MUÑECA/MANO DER</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_mano_d_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_mano_d_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_mano_d_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_mano_d_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_mano_d_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    MUÑECA/MANO IZQ</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_mano_i_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_mano_i_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_mano_i_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_mano_i_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_mano_i_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    CADERAS/MUSLO DER</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_muslo_d_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_muslo_d_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_muslo_d_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_muslo_d_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_muslo_d_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    ANTEBRAZOS IZQ</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_muslo_i_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_muslo_i_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_muslo_i_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_muslo_i_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_muslo_i_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    RODILLA DER</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_rodilla_d_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_rodilla_d_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_rodilla_d_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_rodilla_d_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_rodilla_d_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    RODILLA IZQ</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_rodilla_i_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_rodilla_i_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_rodilla_i_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_rodilla_i_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_rodilla_i_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    TOBILLOS/PIES DER</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_pies_d_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_pies_d_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_pies_d_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_pies_d_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_pies_d_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="padding:16px 0;text-align:center;height:14px;border: 1px solid #267ED7;margin:0 10px;"><h3>\n\
                                                    TOBILLOS/PIES IZQ</h3></div>',
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_pies_i_dura_3meses],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_pies_i_time_ini],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_pies_i_dura_dolor],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_pies_i_recib_trata],
										},
										{
											columnWidth: 0.15,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_pies_i_dias_trata],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											html: '<div style="height:14px;margin:0 10px;"></div>',
										},
										{
											columnWidth: 0.8,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_anames_obs],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXAMEN FISICO -- COLUMNA VERTEBRAL",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.6,
											bodyStyle: "padding:2px 22px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title:
														"DESVIACION DEL EJE ANTERO - POSTERIOR  (EVALUACIÓN EN BIPEDESTACIÓN)",
													items: [
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_lordo_cervic],
														},
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cifosis],
														},
														{
															columnWidth: 0.34,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_lordo_lumbar],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.4,
											bodyStyle: "padding:2px 22px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "DESVIACIONES LATERALES (TEST DE ADAMS)",
													items: [
														{
															columnWidth: 0.5,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_desvia_lat_halla],
														},
														{
															columnWidth: 0.5,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_desvia_lat_escolio],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 22px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "PALPACIÓN DE COLUMNA ",
													items: [
														{
															columnWidth: 0.35,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_apofisis],
														},
														{
															columnWidth: 0.65,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_apofisis_obs],
														},
														{
															columnWidth: 0.35,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_contra_musc_cervic],
														},
														{
															columnWidth: 0.65,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_contra_musc_cervic_obs],
														},
														{
															columnWidth: 0.35,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_contra_musc_lumbar],
														},
														{
															columnWidth: 0.65,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_contra_musc_lumbar_obs],
														},
													],
												},
											],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title:
										"EXAMEN FISICO -- CUELLO Y TRONCO (EVALUAR LOS RANGOS  DE  MOVILIDAD ACTIVA Y PASIVA)",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 22px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "CUELLO",
													items: [
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cuello_flex],
														},
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cuello_flex_lat_d],
														},
														{
															columnWidth: 0.34,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cuello_ext_rot_d],
														},
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cuello_ext],
														},
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cuello_flex_lat_i],
														},
														{
															columnWidth: 0.34,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cuello_ext_rot_i],
														},
													],
												},
												{
													xtype: "fieldset",
													layout: "column",
													title: "TRONCO",
													items: [
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tronco_flex],
														},
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tronco_flex_lat_d],
														},
														{
															columnWidth: 0.34,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tronco_ext_rot_d],
														},
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tronco_ext],
														},
														{
															columnWidth: 0.33,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tronco_flex_lat_i],
														},
														{
															columnWidth: 0.34,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tronco_ext_rot_i],
														},
													],
												},
												{
													columnWidth: 0.99,
													border: false,
													layout: "form",
													labelAlign: "top",
													items: [this.m_osteo_hiper_acor_f_coment],
												},
											],
										},
									],
								},
							],
						},
					],
				},
				{
					title:
						"<b>--->  EXTREMIDADES SUPERIORES - EXTREMIDADES INFERIORES</b>",
					iconCls: "demo2",
					layout: "column",
					autoScroll: true,
					border: false,
					bodyStyle: "padding:10px 10px 20px 10px;",
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXTREMIDADES SUPERIORES -- HOMBROS",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "FLEXIÓN (0-180°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_flex_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_flex_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_flex_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_flex_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_flex_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "ADUCCIÓN HORIZONTAL (0-135°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_adu_h_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_adu_h_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_adu_h_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_adu_h_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_adu_h_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "EXTENSIÓN (0-60°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_ext_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_ext_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_ext_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_ext_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_ext_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "ROTACIÓN INTERNA (0-70°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_rot_in_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_rot_in_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_rot_in_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_rot_in_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_rot_in_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "ABDUCCIÓN (0-180°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_abduc_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_abduc_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_abduc_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_abduc_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_abduc_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "ROTACIÓN EXTERNA (0-90°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_rot_ex_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_rot_ex_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_rot_ex_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_rot_ex_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_rot_ex_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "ABDUCCIÓN HORIZONTAL (0-45°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_abd_h_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_abd_h_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_abd_h_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_abd_h_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_hombro_abd_h_color],
														},
													],
												},
											],
										},
									],
								},
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXTREMIDADES SUPERIORES -- CODO Y ANTEBRAZO",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "FLEXIÓN (0-150°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_flex_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_flex_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_flex_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_flex_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_flex_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "SUPINACIÓN (0-80°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_supina_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_supina_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_supina_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_supina_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_supina_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "EXTENSIÓN (0-180°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_ext_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_ext_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_ext_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_ext_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_ext_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "PRONACIÓN (0-80°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_prona_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_prona_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_prona_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_prona_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_codo_prona_color],
														},
													],
												},
											],
										},
									],
								},
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXTREMIDADES SUPERIORES -- MUÑECA",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "FLEXIÓN (0-80°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_flex_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_flex_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_flex_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_flex_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_flex_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "DESVIACIÓN CUBITAL (0-30°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_des_cubi_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_des_cubi_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_des_cubi_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_des_cubi_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_des_cubi_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "EXTENSIÓN (0-70°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_ext_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_ext_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_ext_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_ext_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_ext_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "DESVIACIÓN RADIAL (0-20°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_des_radi_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_des_radi_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_des_radi_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_des_radi_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_muneca_des_radi_color],
														},
													],
												},
											],
										},
									],
								},
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXTREMIDADES SUPERIORES -- COMENTARIOS",
									items: [
										{
											columnWidth: 0.99,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_sup_acor_fu_sen_coment],
										},
									],
								},
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXTREMIDADES INFERIORES -- CADERA",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "FLEXIÓN (0-120°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_flex_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_flex_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_flex_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_flex_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_flex_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "ADUCCIÓN  (0-30°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_aduc_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_aduc_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_aduc_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_aduc_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_aduc_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "EXTENSIÓN (0-30°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_ext_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_ext_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_ext_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_ext_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_ext_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "ROTACIÓN INTERNA (0-45°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_rota_int_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_rota_int_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_rota_int_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_rota_int_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_rota_int_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "ABDUCCIÓN (0-45°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_abduc_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_abduc_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_abduc_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_abduc_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_abduc_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: " ROTACIÓN EXTERNA (0-45°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_rota_ext_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_rota_ext_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_rota_ext_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_rota_ext_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_cader_rota_ext_color],
														},
													],
												},
											],
										},
									],
								},
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXTREMIDADES INFERIORES -- RODILLA",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "FLEXIÓN (0-135°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_flex_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_flex_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_flex_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_flex_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_flex_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "ROTACIÓN TIBIAL",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_rota_tibi_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_rota_tibi_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_rota_tibi_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_rota_tibi_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_rota_tibi_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "EXTENSIÓN (0-180°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_ext_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_ext_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_ext_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_ext_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_rodill_ext_color],
														},
													],
												},
											],
										},
									],
								},
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXTREMIDADES INFERIORES -- TOBILLO",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "DORSIFLEXIÓN (0-20°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_dorsi_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_dorsi_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_dorsi_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_dorsi_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_dorsi_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "INVERSIÓN (0-35°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_inver_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_inver_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_inver_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_inver_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_inver_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "FLEXIÓN PLANTAR (0-50°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_flex_plan_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_flex_plan_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_flex_plan_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_flex_plan_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_flex_plan_color],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.5,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "EVERSIÓN (0-15°)",
													items: [
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_ever_der],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_ever_izq],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_ever_fuerza],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_ever_tono],
														},
														{
															columnWidth: 0.2,
															border: false,
															layout: "form",
															labelAlign: "top",
															items: [this.m_osteo_tobill_ever_color],
														},
													],
												},
											],
										},
									],
								},
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXTREMIDADES INFERIORES -- COMENTARIOS",
									items: [
										{
											columnWidth: 0.99,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_osteo_inf_acor_fu_sen_coment],
										},
									],
								},
							],
						},
					],
				},
				{
					title:
						"<b>--->  EXTREMIDADES SUPERIORES E INFERIORES - TEST - MANIOBRAS - REFLEJO - CONCLUSION - APTITUD</b>",
					iconCls: "demo2",
					layout: "column",
					autoScroll: true,
					border: false,
					bodyStyle: "padding:10px 10px 20px 10px;",
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXTREMIDADES SUPERIORES",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title:
														"TEST DE JOBE ( ELEVAR BRAZOS CONTRA RESISTENCIA- PULGAR ABAJO)",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/1.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_test_jobe_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_test_jobe_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_test_jobe_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_test_jobe_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "MANIOBRA DE APLEY (TEST DEL RASCADO)",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/2.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_apley_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_apley_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_apley_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_apley_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "PALPACIÓN DE EPICÓNDILO LATERAL",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/3.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_palpa_epi_lat_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_palpa_epi_lat_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_palpa_epi_lat_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_palpa_epi_lat_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "PALPACIÓN DE EPICÓNDILO MEDIAL",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/4.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_palpa_epi_med_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_palpa_epi_med_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_palpa_epi_med_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_palpa_epi_med_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "TEST DE PHALEN (PALMAS 90°)",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/5.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_test_phalen_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_test_phalen_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_test_phalen_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_test_phalen_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "TEST DE TINEL (PERCUTIR MEDIANO)",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/6.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_test_tinel_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_test_tinel_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_test_tinel_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_test_tinel_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "TEST DE FINKELSTEIN",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/7.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_test_finkelstein_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_test_finkelstein_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_test_finkelstein_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_test_finkelstein_izq_obs],
														},
													],
												},
											],
										},
									],
								},
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXTREMIDADES INFERIORES",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "MANIOBRA DE LASEGUE",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/8.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_lasegue_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_lasegue_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_lasegue_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_lasegue_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "MANIOBRA DE BRADGARD",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: ".",
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_bradga_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_bradga_der_obs],
														},
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: ".",
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_bradga_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_bradga_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "MANIOBRA DE THOMAS",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: ".",
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_thomas_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_thomas_der_obs],
														},
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: ".",
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_thomas_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_thomas_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "MANIOBRA DE FABERE PATRICK",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/9.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_fabere_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_fabere_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_fabere_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_fabere_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "MANIOBRA DE VARO Y VALGO DOLOROSA",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: ".",
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_varo_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_varo_der_obs],
														},
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: ".",
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_varo_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_varo_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "MANIOBRA DE CAJON ANTERIOR DEL TOBILLO",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: ".",
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_cajon_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_cajon_der_obs],
														},
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: ".",
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_mani_cajon_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_mani_cajon_izq_obs],
														},
													],
												},
											],
										},
									],
								},
								{
									xtype: "fieldset",
									layout: "column",
									title: "EXAMEN NEUROLOGICO - VALORACION DE REFLEJOS",
									items: [
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "REFLEJO BICIPITAL",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/10.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_refle_bicipi_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_refle_bicipi_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_refle_bicipi_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_refle_bicipi_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "REFLEJO TRICIPITAL",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/11.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_refle_trici_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_refle_trici_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_refle_trici_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_refle_trici_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "REFLEJO PATELAR O ROTULIANO",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/12.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_refle_patelar_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_refle_patelar_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_refle_patelar_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_refle_patelar_izq_obs],
														},
													],
												},
											],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.999,
											bodyStyle: "padding:2px 10px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "REFLEJO AQUILIANO",
													items: [
														{
															columnWidth: 0.12,
															border: false,
															layout: "form",
															html: '<img width="90" src="<[sys_images]>/osteoMuscular/13.jpg">',
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_refle_aquilia_der],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_refle_aquilia_der_obs],
														},
														{
															columnWidth: 0.18,
															border: false,
															layout: "form",
															labelWidth: 20,
															items: [this.m_osteo_refle_aquilia_izq],
														},
														{
															columnWidth: 0.7,
															border: false,
															layout: "form",
															labelWidth: 99,
															items: [this.m_osteo_refle_aquilia_izq_obs],
														},
													],
												},
											],
										},
									],
								},
							],
						},
						{
							columnWidth: 0.65,
							border: true,
							layout: "form",
							labelAlign: "top",
							items: [this.dt_grid4],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.35,
							bodyStyle: "padding:2px 15px 0px 22px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											labelWidth: 60,
											items: [this.m_osteo_aptitud],
										},
									],
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
						mod.medicina.nuevoOsteo.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_nuevoOsteo"
										: "save_nuevoOsteo",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								Ext.MessageBox.alert(
									"En hora buena",
									"Se registro correctamente"
								);
								mod.medicina.nuevoOsteo.win.el.unmask();
								mod.medicina.formatos.st.reload();
								mod.medicina.nuevoOsteo.win.close();
							},
							failure: function (form, action) {
								mod.medicina.nuevoOsteo.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.formatos.st.reload();
								mod.medicina.nuevoOsteo.win.close();
							},
						});
					},
				},
			],
		});
		this.win = new Ext.Window({
			width: 1000,
			height: 600,
			border: false,
			modal: true,
			title: "EXAMEN OSTEO MUSCULAR: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.medicina.osteo_conclusion = {
	rec: null,
	win: null,
	frm: null,
	osteo_conclu_desc: null,
	init: function (r) {
		this.rec = r;
		this.crea_stores();
		this.st_busca_osteo_conclu.load();
		this.crea_controles();
		if (this.rec !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_osteo_conclu",
				format: "json",
				osteo_conclu_id: this.rec.get("osteo_conclu_id"),
				osteo_conclu_adm: this.rec.get("osteo_conclu_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_osteo_conclu = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_osteo_conclu",
				format: "json",
			},
			fields: ["osteo_conclu_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{osteo_conclu_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.osteo_conclu_desc = new Ext.form.ComboBox({
			store: this.st_busca_osteo_conclu,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "osteo_conclu_desc",
			displayField: "osteo_conclu_desc",
			valueField: "osteo_conclu_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>CONCLUSIONES Y RECOMENDACIONES</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.osteo_conclu_desc],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.osteo_conclusion.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						var metodo;
						var osteo_conclu_id;
						if (this.rec !== null) {
							metodo = "update";
							osteo_conclu_id =
								mod.medicina.osteo_conclusion.rec.get("osteo_conclu_id");
						} else {
							metodo = "save";
							osteo_conclu_id = null;
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_osteo_conclusion",
								osteo_conclu_adm: mod.medicina.nuevoOsteo.record.get("adm"),
								osteo_conclu_id: osteo_conclu_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								// Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.osteo_conclusion.win.el.unmask();
								mod.medicina.nuevoOsteo.list_osteo_conclu.reload();
								mod.medicina.osteo_conclusion.win.close();
							},
							failure: function (form, action) {
								mod.medicina.osteo_conclusion.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.nuevoOsteo.list_osteo_conclu.reload();
								mod.medicina.osteo_conclusion.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 700,
			height: 140,
			modal: true,
			title: "REGISTRO DE CONCLUSIONES Y RECOMENDACIONES",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.medicina.anexo_16a = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.list_16a_obs.load();
		this.crea_controles();
		if (this.record.get("st") >= 1) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_anexo_16a",
				format: "json",
				anexo_16a_adm: mod.medicina.anexo_16a.record.get("adm"),
				anexo_16a_exa: mod.medicina.anexo_16a.record.get("ex_id"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.medicina.anexo_16a.val_medico.setValue(r.val_medico);
				//                mod.medicina.anexo_16a.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_stores: function () {
		this.list_16a_obs = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_16a_obs",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: ["obs_16a_id", "obs_16a_adm", "obs_16a_desc", "obs_16a_plazo"],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm = mod.medicina.anexo_16a.record.get("adm");
				},
			},
		});
	},
	crea_controles: function () {
		//anexo_16a_seguros
		this.anexo_16a_seguros = new Ext.form.TextField({
			fieldLabel: "<b>COMPAÑIA DE SEGUROS</b>",
			name: "anexo_16a_seguros",
			anchor: "95%",
		});
		//anexo_16a_clinica
		this.anexo_16a_clinica = new Ext.form.TextField({
			fieldLabel: "<b>CLINICA/CENTRO MÉDICO</b>",
			name: "anexo_16a_clinica",
			anchor: "95%",
		});
		//anexo_16a_anfitrion
		this.anexo_16a_anfitrion = new Ext.form.TextField({
			fieldLabel: "<b>ANFITRION MINERA LAS BAMBAS</b>",
			name: "anexo_16a_anfitrion",
			anchor: "95%",
		});
		//anexo_16a_fech_visita
		this.anexo_16a_fech_visita = new Ext.form.DateField({
			fieldLabel: "<b>FECHA PROBABLE DE VISITA</b>",
			format: "Y-m-d",
			value: new Date(),
			name: "anexo_16a_fech_visita",
			anchor: "85%",
		});
		//anexo_16a_aptitud
		this.anexo_16a_aptitud = new Ext.form.RadioGroup({
			fieldLabel: "<b>APTITUD</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{
					boxLabel: "APTO",
					name: "anexo_16a_aptitud",
					inputValue: "APTO",
					checked: true,
				},
				//                {boxLabel: 'APTO CON RESTRICCIONES', name: 'anexo_16a_aptitud', inputValue: 'APTO CON RESTRICCIONES'},
				{
					boxLabel: "NO APTO",
					name: "anexo_16a_aptitud",
					inputValue: "NO APTO",
				},
			],
		});
		//anexo_16a_fech_evalua
		this.anexo_16a_fech_evalua = new Ext.form.DateField({
			fieldLabel: "<b>FECHA DE EVALUACION</b>",
			format: "Y-m-d",
			value: new Date(),
			name: "anexo_16a_fech_evalua",
			anchor: "85%",
		});
		//anexo_16a_vacuna
		this.anexo_16a_vacuna = new Ext.form.RadioGroup({
			fieldLabel: "<b>VACUNA CONTRA LA INFLUENCIA</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{ boxLabel: "SI", name: "anexo_16a_vacuna", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "anexo_16a_vacuna",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//grid de observaciones
		this.tbar5 = new Ext.Toolbar({
			items: [
				'<b style="color:#000000;">OBSERVACIONES</b>',
				"-",
				"->",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.obs_16a.init(null);
					},
				},
			],
		});
		this.dt_grid5 = new Ext.grid.GridPanel({
			store: this.list_16a_obs,
			region: "west",
			border: true,
			tbar: this.tbar5,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			height: 260,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var rec = grid.getStore().getAt(rowIndex);
					mod.medicina.obs_16a.init(rec);
				},
			},
			autoExpandColumn: "diag",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "diag",
					header: "OBSERVACIONES",
					dataIndex: "obs_16a_desc",
				},
				{
					header: "PLAZO",
					width: 100,
					dataIndex: "obs_16a_plazo",
				},
			],
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			border: false,
			layout: "accordion",
			layoutConfig: {
				titleCollapse: true,
				animate: true,
				hideCollapseTool: true,
			},
			items: [
				{
					title:
						"<b>--->  ANTECEDENTES MÉDICOS PERSONALES- OBSERVACIONES - APTITUD</b>",
					iconCls: "demo2",
					layout: "column",
					autoScroll: true,
					border: false,
					bodyStyle: "padding:10px 10px 20px 10px;",
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "DATOS ADICIONALES",
									items: [
										{
											columnWidth: 0.25,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.anexo_16a_seguros],
										},
										{
											columnWidth: 0.25,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.anexo_16a_clinica],
										},
										{
											columnWidth: 0.25,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.anexo_16a_anfitrion],
										},
										{
											columnWidth: 0.25,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.anexo_16a_fech_visita],
										},
									],
								},
							],
						},
						{
							columnWidth: 0.65,
							border: true,
							layout: "form",
							labelAlign: "top",
							items: [this.dt_grid5],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.35,
							bodyStyle: "padding:2px 15px 0px 22px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 60,
											items: [this.anexo_16a_aptitud],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.35,
							bodyStyle: "padding:2px 15px 0px 22px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "",
									labelAlign: "top",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 60,
											items: [this.anexo_16a_fech_evalua],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 60,
											items: [this.anexo_16a_vacuna],
										},
									],
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
						mod.medicina.anexo_16a.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_anexo_16a"
										: "save_anexo_16a",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								Ext.MessageBox.alert(
									"En hora buena",
									"Se registro correctamente"
								);
								mod.medicina.anexo_16a.win.el.unmask();
								mod.medicina.formatos.st.reload();
								mod.medicina.anexo_16a.win.close();
							},
							failure: function (form, action) {
								mod.medicina.anexo_16a.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.formatos.st.reload();
								mod.medicina.anexo_16a.win.close();
							},
						});
					},
				},
			],
		});
		this.win = new Ext.Window({
			width: 1000,
			height: 500,
			border: false,
			modal: true,
			title: "EXAMEN ANEXO 16A - PERFIL VISITA: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};
mod.medicina.obs_16a = {
	rec: null,
	win: null,
	frm: null,
	obs_16a_desc: null,
	obs_16a_plazo: null,
	init: function (r) {
		this.rec = r;
		this.crea_stores();
		this.st_busca_16a_obs.load();
		this.crea_controles();
		if (this.rec !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_16a_obs",
				format: "json",
				obs_16a_id: this.rec.get("obs_16a_id"),
				obs_16a_adm: this.rec.get("obs_16a_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_16a_obs = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_16a_obs",
				format: "json",
			},
			fields: ["obs_16a_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{cie4_desc}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.obs_16a_plazo = new Ext.form.RadioGroup({
			fieldLabel: "<b>PLAZO</b>",
			itemCls: "x-check-group-alt",
			columns: 4,
			items: [
				{
					boxLabel: "NINGUNO(-)",
					name: "obs_16a_plazo",
					inputValue: "-",
					checked: true,
				},
				{
					boxLabel: "INMEDIATO",
					name: "obs_16a_plazo",
					inputValue: "INMEDIATO",
				},
				{ boxLabel: "03 MESES", name: "obs_16a_plazo", inputValue: "03 MESES" },
				{ boxLabel: "06 MESES", name: "obs_16a_plazo", inputValue: "06 MESES" },
			],
		});
		this.obs_16a_desc = new Ext.form.ComboBox({
			store: this.st_busca_16a_obs,
			hiddenName: "obs_16a_desc",
			displayField: "obs_16a_desc",
			//            disabled: true,
			valueField: "obs_16a_desc",
			minChars: 1,
			validateOnBlur: true,
			forceSelection: false,
			autoSelect: false,
			allowBlank: false,
			enableKeyEvents: true,
			selectOnFocus: false,
			fieldLabel: "<b>OBSERVACIONES</b>",
			typeAhead: false,
			hideTrigger: true,
			triggerAction: "all",
			mode: "local",
			anchor: "95%",
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:10px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.obs_16a_desc],
				},
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.obs_16a_plazo],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.obs_16a.win.el.mask("Guardando…", "x-mask-loading");
						var metodo;
						var obs_16a_id;
						if (this.rec !== null) {
							metodo = "update";
							obs_16a_id = mod.medicina.obs_16a.rec.get("obs_16a_id");
						} else {
							metodo = "save";
							obs_16a_id = "1";
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_16a_obs",
								obs_16a_adm: mod.medicina.anexo_16a.record.get("adm"),
								obs_16a_id: obs_16a_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.obs_16a.win.el.unmask();
								mod.medicina.anexo_16a.list_16a_obs.reload();
								mod.medicina.obs_16a.win.close();
							},
							failure: function (form, action) {
								mod.medicina.obs_16a.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.anexo_16a.list_16a_obs.reload();
								mod.medicina.obs_16a.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 1000,
			height: 180,
			modal: true,
			title: "REGISTRO DE OBSERVACIONES",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.medicina.medicina_manejo = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.crea_controles();
		this.list_manejo_conclu.load();
		if (this.record.get("st") >= 1) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_medicina_manejo",
				format: "json",
				adm: mod.medicina.medicina_manejo.record.get("adm"),
				//                ,examen: mod.medicina.medicina_manejo.record.get('ex_id')
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.medicina.medicina_manejo.val_medico.setValue(r.val_medico);
				//                mod.medicina.medicina_manejo.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_stores: function () {
		this.list_manejo_conclu = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_manejo_conclu",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"conclu_conduc_med_id",
				"conclu_conduc_med_adm",
				"conclu_conduc_med_desc",
			],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm = mod.medicina.medicina_manejo.record.get("adm");
				},
			},
		});
	},
	crea_controles: function () {
		//m_med_manejo_mariposa
		this.m_med_manejo_mariposa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_mariposa",
			allowBlank: false,
			fieldLabel: "<b>STEREO TEST (TEST MARIPOSA)</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_colores
		this.m_med_manejo_colores = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_colores",
			allowBlank: false,
			fieldLabel: "<b>EVAL. DE COLORES (VERDE, AMARILLO, ROJO)</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_encandila
		this.m_med_manejo_encandila = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_encandila",
			allowBlank: false,
			fieldLabel: "<b>EVALUACION DE ENCANDILAMIENTO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_recupera
		this.m_med_manejo_recupera = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_recupera",
			allowBlank: false,
			fieldLabel: "<b>EVALUACION DE RECUPERACION</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_phoria
		this.m_med_manejo_phoria = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_phoria",
			allowBlank: false,
			fieldLabel: "<b>EVALUACION PHORIA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_perimetrica
		this.m_med_manejo_perimetrica = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_perimetrica",
			allowBlank: false,
			fieldLabel: "<b>EVALUACION PERIMETRICA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_bender
		this.m_med_manejo_bender = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_bender",
			allowBlank: false,
			fieldLabel: "<b>BENDER</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_bc4
		this.m_med_manejo_bc4 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_bc4",
			allowBlank: false,
			fieldLabel: "<b>BC 4 CUESTIONARIO DE ACTITUD FRENTE AL TRANSITO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_toulouse
		this.m_med_manejo_toulouse = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_toulouse",
			allowBlank: false,
			fieldLabel: "<b>TOULOUSE</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_entrevista
		this.m_med_manejo_entrevista = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_entrevista",
			allowBlank: false,
			fieldLabel: "<b>HOJA DE ENTREVISTA FORMATO ESTABLECIDO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_sensometrico
		this.m_med_manejo_sensometrico = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_sensometrico",
			allowBlank: false,
			fieldLabel: "<b>EX. PSICO SENSOMETRICO COMPLETO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_weschler
		this.m_med_manejo_weschler = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["NO CORRESPONDE", "NO CORRESPONDE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_weschler",
			allowBlank: false,
			fieldLabel: "<b>TEST LABERINTO-ESCALA WESCHLER</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_test_puntea
		this.m_med_manejo_test_puntea = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_test_puntea",
			allowBlank: false,
			fieldLabel: "<b>TEST DE PUNTEADO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_test_palanca
		this.m_med_manejo_test_palanca = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_test_palanca",
			allowBlank: false,
			fieldLabel: "<b>TEST DE PALANCA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_test_reactimetro
		this.m_med_manejo_test_reactimetro = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_test_reactimetro",
			allowBlank: false,
			fieldLabel: "<b>TEST DE REACTIMETRO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_test_laberinto
		this.m_med_manejo_test_laberinto = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_test_laberinto",
			allowBlank: false,
			fieldLabel: "<b>TEST DE DOBLE LABERINTO</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_test_bimanual
		this.m_med_manejo_test_bimanual = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_test_bimanual",
			allowBlank: false,
			fieldLabel: "<b>TEST BIMANUAL</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_test_anticipa
		this.m_med_manejo_test_anticipa = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_test_anticipa",
			allowBlank: false,
			fieldLabel: "<b>TEST DE ANTICIPACION</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_test_reac_multi
		this.m_med_manejo_test_reac_multi = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["NO CORRESPONDE", "NO CORRESPONDE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_test_reac_multi",
			allowBlank: false,
			fieldLabel: "<b>TEST DE REACCION MULTIPLE</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_med_manejo_test_monotimia
		this.m_med_manejo_test_monotimia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["NO CORRESPONDE", "NO CORRESPONDE"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_med_manejo_test_monotimia",
			allowBlank: false,
			fieldLabel: "<b>TEST DE RESISTENCIA A LA MONOTOMIA</b>",
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 100,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		this.m_med_manejo_tipo_equipo = new Ext.form.RadioGroup({
			fieldLabel: "<b>TIPO DE EQUIPO</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{
					boxLabel: "EQUIPO LIVIANO",
					name: "m_med_manejo_tipo_equipo",
					inputValue: "EQUIPO LIVIANO",
					checked: true,
				},
				{
					boxLabel: "EQUIPO PESADO",
					name: "m_med_manejo_tipo_equipo",
					inputValue: "EQUIPO PESADO",
				},
			],
		});
		//m_med_manejo_aptitud
		this.m_med_manejo_aptitud = new Ext.form.RadioGroup({
			fieldLabel: "<b>APTITUD</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{
					boxLabel: "APTO",
					name: "m_med_manejo_aptitud",
					inputValue: "APTO",
					checked: true,
				},
				{
					boxLabel: "APTO CON OBSERVACION",
					name: "m_med_manejo_aptitud",
					inputValue: "APTO CON OBSERVACION",
				},
				{
					boxLabel: "NO APTO",
					name: "m_med_manejo_aptitud",
					inputValue: "NO APTO",
				},
			],
		});

		this.tbar4 = new Ext.Toolbar({
			items: [
				"->",
				'<b style="color:#000000;">RECOMENDACIONES Y CONCLUSIONES</b>',
				"-",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.medicina.manejo_conclu.init(null);
					},
				},
			],
		});
		this.dt_grid4 = new Ext.grid.GridPanel({
			store: this.list_manejo_conclu,
			region: "west",
			border: true,
			tbar: this.tbar4,
			loadMask: true,
			iconCls: "icon-grid",
			plugins: new Ext.ux.PanelResizer({
				minHeight: 100,
			}),
			height: 260,
			listeners: {
				rowdblclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var record2 = grid.getStore().getAt(rowIndex);
					mod.medicina.manejo_conclu.init(record2);
				},
			},
			autoExpandColumn: "reco_desc",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "reco_desc",
					header: "CONCLUSIONES",
					dataIndex: "conclu_conduc_med_desc",
				},
			],
		});

		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			border: false,
			layout: "accordion",
			layoutConfig: {
				titleCollapse: true,
				animate: true,
				hideCollapseTool: true,
			},
			items: [
				{
					title: "<b>--->  EXAMEN OCUPACIONAL PARA MANEJO</b>",
					iconCls: "demo2",
					layout: "column",
					autoScroll: true,
					border: false,
					bodyStyle: "padding:10px 10px 20px 10px;",
					labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 300,
											//labelAlign: 'top',
											items: [this.m_med_manejo_mariposa],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 300,
											//labelAlign: 'top',
											items: [this.m_med_manejo_colores],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 300,
											//labelAlign: 'top',
											items: [this.m_med_manejo_encandila],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 300,
											//labelAlign: 'top',
											items: [this.m_med_manejo_recupera],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 300,
											//labelAlign: 'top',
											items: [this.m_med_manejo_phoria],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 300,
											//labelAlign: 'top',
											items: [this.m_med_manejo_perimetrica],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "PSICOLOGIA",
									labelWidth: 350,
									items: [
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_bender],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_bc4],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_toulouse],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_entrevista],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_sensometrico],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_weschler],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO PESADO</b>",
											width: 250,
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.999,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "PSICOTECNICO",
									labelWidth: 350,
									items: [
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_test_puntea],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_test_palanca],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_test_reactimetro],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_test_laberinto],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_test_bimanual],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_test_anticipa],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_test_reac_multi],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
										{
											columnWidth: 0.65,
											border: false,
											layout: "form",
											//labelAlign: 'top',labelWidth: 220,
											items: [this.m_med_manejo_test_monotimia],
										},
										{
											columnWidth: 0.35,
											xtype: "displayfield",
											value: "<b>EQUIPO LIVIANO / PESADO</b>",
											width: 250,
										},
									],
								},
							],
						},
						{
							columnWidth: 0.65,
							labelWidth: 1,
							labelAlign: "top",
							layout: "form",
							border: false,
							items: [this.dt_grid4],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.35,
							bodyStyle: "padding:2px 15px 0px 22px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											//                                            labelWidth: 60,
											items: [this.m_med_manejo_tipo_equipo],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											//                                            labelWidth: 60,
											items: [this.m_med_manejo_aptitud],
										},
									],
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
						mod.medicina.medicina_manejo.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_medicina_manejo"
										: "save_medicina_manejo",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								//                                obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'Se registro correctamente');
								mod.medicina.formatos.st.reload();
								mod.medicina.st.reload();
								mod.medicina.medicina_manejo.win.el.unmask();
								mod.medicina.medicina_manejo.win.close();
							},
							failure: function (form, action) {
								mod.medicina.medicina_manejo.win.el.unmask();
								mod.medicina.medicina_manejo.win.close();
								mod.medicina.formatos.st.reload();
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
			width: 1000,
			height: 500,
			border: false,
			modal: true,
			title: "EXAMEN OCUPACIONAL PARA MANEJO: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.medicina.manejo_conclu = {
	record2: null,
	win: null,
	frm: null,
	conclu_conduc_med_desc: null,
	init: function (r) {
		this.record2 = r;
		this.crea_stores();
		this.st_busca_manejo_conclu.load();
		this.crea_controles();
		if (this.record2 !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_manejo_conclu",
				format: "json",
				conclu_conduc_med_id: this.record2.get("conclu_conduc_med_id"),
				conclu_conduc_med_adm: this.record2.get("conclu_conduc_med_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_manejo_conclu = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_manejo_conclu",
				format: "json",
			},
			fields: ["conclu_conduc_med_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.resultTpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><span>{conclu_conduc_med_desc}</span></h3>",
			"</div>",
			"</div></tpl>"
		);

		this.conclu_conduc_med_desc = new Ext.form.ComboBox({
			store: this.st_busca_manejo_conclu,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.resultTpl,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 3,
			hiddenName: "conclu_conduc_med_desc",
			displayField: "conclu_conduc_med_desc",
			valueField: "conclu_conduc_med_desc",
			allowBlank: false,
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>CONCLUSION Y RECOMENDACIONES</b>",
			mode: "local",
			anchor: "99%",
		});
		this.frm = new Ext.FormPanel({
			region: "center",
			url: "<[controller]>",
			monitorValid: true,
			frame: true,
			layout: "column",
			bodyStyle: "padding:5px;",
			labelWidth: 99,
			items: [
				{
					columnWidth: 0.999,
					border: false,
					labelAlign: "top",
					layout: "form",
					items: [this.conclu_conduc_med_desc],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.medicina.manejo_conclu.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						var metodo;
						var conclu_conduc_med_id;
						if (this.record2 !== null) {
							metodo = "update";
							conclu_conduc_med_id = mod.medicina.manejo_conclu.record2.get(
								"conclu_conduc_med_id"
							);
						} else {
							metodo = "save";
							conclu_conduc_med_id = "";
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_manejo_conclu",
								conclu_conduc_med_adm:
									mod.medicina.medicina_manejo.record.get("adm"),
								conclu_conduc_med_id: conclu_conduc_med_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.medicina.manejo_conclu.win.el.unmask();
								mod.medicina.medicina_manejo.list_manejo_conclu.reload();
								mod.medicina.manejo_conclu.win.close();
							},
							failure: function (form, action) {
								mod.medicina.manejo_conclu.win.el.unmask();
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
										break;
									default:
										Ext.Msg.alert("Failure", action.result.error);
								}
								mod.medicina.medicina_manejo.list_manejo_conclu.reload();
								mod.medicina.manejo_conclu.win.close();
							},
						});
					},
				},
			],
		});

		this.win = new Ext.Window({
			width: 950,
			height: 140,
			modal: true,
			title: "REGISTRO DE CONCLUSION",
			border: false,
			maximizable: true,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

Ext.onReady(mod.medicina.init, mod.medicina);
