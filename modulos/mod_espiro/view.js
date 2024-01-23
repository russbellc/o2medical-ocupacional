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

Ext.ns("mod.espiro");
mod.espiro = {
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
					this.baseParams.columna = mod.espiro.descripcion.getValue();
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
						mod.espiro.rfecha.init(null);
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
					if (record.get("st") >= 1) {
						mod.espiro.formatos.init(record);
					} else {
						mod.espiro.formatos.init(record);
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
mod.espiro.formatos = {
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
			mod.espiro.formatos.imgStore.removeAll();
			var store = mod.espiro.formatos.imgStore;
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
					this.baseParams.adm = mod.espiro.formatos.record.get("adm");
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
				rowclick: function (grid, rowIndex, e) {
					e.stopEvent();
					var record = grid.getStore().getAt(rowIndex); //
					if (record.get("ex_id") == 2) {
						//examen psicologia
						mod.espiro.espiro_espiro.init(record);
						mod.espiro.espiro_espiro.llena_medico(record.get("adm"));
					} else {
						mod.espiro.espiro_pred.init(record); //
					}
					//                    mod.espiro.espiro_pred.init(record);//
				},
				rowcontextmenu: function (grid, index, event) {
					event.stopEvent();
					var record = grid.getStore().getAt(index);
					if (record.get("st") == "1") {
						if (record.get("ex_id") == 2) {
							//PSICOLOGIA - LAS BAMBAS
							new Ext.menu.Menu({
								items: [
									{
										text:
											"FORMATO espiroMETRIA N°: <B>" +
											record.get("adm") +
											"<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title: "FORMATO espiroMETRIA N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_espirometria&sys_report=formato_espirometria&adm=" +
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
					id: "cuest_desc",
					header: "EXAMENES",
					dataIndex: "ex_desc",
				},
				{
					header: "USUARIO",
					dataIndex: "usu",
					width: 70,
				},
				{
					header: "FECHA DE REGISTRO",
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
			title: "EXAMEN ESPIROMETRICO: " + this.record.get("nombre"),
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

mod.espiro.espiro_pred = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_store(r);
		this.crea_controles();
		if (this.record !== null) {
			this.cargar_data();
		}
		this.win.show();
	},
	crea_store: function (r) {
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
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_espiro_metria",
				format: "json",
				adm: mod.espiro.espiro_pred.record.get("adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.espiro.anexo_16a.val_medico.setValue(r.val_medico);
				//                mod.espiro.anexo_16a.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_controles: function () {
		//TRIAJE
		this.peso = new Ext.form.NumberField({
			fieldLabel: "Peso",
			readOnly: true,
			value: this.record.get("tri_talla"),
			anchor: "95%",
		});
		this.talla = new Ext.form.NumberField({
			fieldLabel: "Talla",
			readOnly: true,
			value: this.record.get("tri_peso"),
			anchor: "95%",
		});
		this.imc = new Ext.form.NumberField({
			fieldLabel: "IMC",
			value: this.record.get("tri_img"),
			readOnly: true,
			anchor: "95%",
		});
		//ESPIROMETRIA
		this.m_espiro_metria_fuma = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO", "NO"],
					["SI", "SI"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_espiro_metria_fuma",
			fieldLabel: "<b>Fumador</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "85%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO");
					descripcion.setRawValue("NO");
				},
			},
		});
		this.m_espiro_metria_cap_vital = new Ext.form.TextField({
			fieldLabel: "<b>Capacidad Vital</b>",
			allowBlank: false,
			name: "m_espiro_metria_cap_vital",
			anchor: "85%",
		});
		this.m_espiro_metria_FVC = new Ext.form.TextField({
			fieldLabel: "<b>FVC</b>",
			allowBlank: false,
			name: "m_espiro_metria_FVC",
			anchor: "80%",
		});
		this.m_espiro_metria_FEV1 = new Ext.form.TextField({
			fieldLabel: "<b>FEV1</b>",
			allowBlank: false,
			name: "m_espiro_metria_FEV1",
			anchor: "80%",
		});
		this.m_espiro_metria_FEV1_FVC = new Ext.form.TextField({
			fieldLabel: "<b>FEV1/FVC</b>",
			allowBlank: false,
			name: "m_espiro_metria_FEV1_FVC",
			anchor: "80%",
		});
		this.m_espiro_metria_PEF = new Ext.form.TextField({
			fieldLabel: "<b>PEF</b>",
			allowBlank: false,
			name: "m_espiro_metria_PEF",
			anchor: "80%",
		});
		this.m_espiro_metria_FEF2575 = new Ext.form.TextField({
			fieldLabel: "<b>FEF 25-75%</b>",
			allowBlank: false,
			name: "m_espiro_metria_FEF2575",
			anchor: "80%",
		});

		//m_espiro_metria_recomendacion
		this.m_espiro_metria_recomendacion = new Ext.form.TextArea({
			fieldLabel: "<b>RECOMENDACIONES</b>",
			name: "m_espiro_metria_recomendacion",
			anchor: "99%",
			height: 40,
		});
		//m_espiro_metria_conclusion
		this.m_espiro_metria_conclusion = new Ext.form.TextArea({
			fieldLabel: "<b>CONCLUSIONES</b>",
			name: "m_espiro_metria_conclusion",
			anchor: "99%",
			height: 40,
		});
		this.cie10Tpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"{cie4_id}",
			"<h3><span><p>{cie4_desc}</p></span></h3>",
			"</div>",
			"</div></tpl>"
		);
		//m_espiro_metria_cie10
		this.m_espiro_metria_cie10 = new Ext.form.ComboBox({
			store: this.list_cie10,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.cie10Tpl,
			//            disabled: true,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_espiro_metria_cie10",
			displayField: "cie4_desc",
			valueField: "cie4_desc",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>CIE-10</b>",
			mode: "remote",
			style: {
				textTransform: "uppercase",
			},
			anchor: "100%",
		});
		// this.m_espiro_metria_cie10 = new Ext.form.TextArea({
		// 	fieldLabel: "<b>CIE-10</b>",
		// 	name: "m_espiro_metria_cie10",
		// 	anchor: "99%",
		// 	height: 40,
		// });
		//m_espiro_metria_diag
		this.m_espiro_metria_diag = new Ext.form.TextArea({
			fieldLabel: "<b>DIAGNOSTICO</b>",
			name: "m_espiro_metria_diag",
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
					xtype: "panel",
					border: false,
					columnWidth: 0.5,
					bodyStyle: "padding:2px 22px 0px 5px;",
					items: [
						{
							xtype: "fieldset",
							// columnWidth: 1,
							title: "Datos Triaje",
							layout: "column",
							items: [
								{
									columnWidth: 0.35,
									border: false,
									layout: "form",
									items: [this.talla],
								},
								{
									columnWidth: 0.33,
									border: false,
									layout: "form",
									items: [this.peso],
								},
								{
									columnWidth: 0.32,
									border: false,
									layout: "form",
									items: [this.imc],
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
							// columnWidth: 1,
							title: "CAPACIDAD VITAL",
							layout: "column",
							items: [
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_fuma],
								},
								{
									columnWidth: 0.5,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_cap_vital],
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
							// columnWidth: 1,
							title: "LECTURA ESPIROMETRICA",
							layout: "column",
							items: [
								{
									columnWidth: 0.2,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_FVC],
								},
								{
									columnWidth: 0.2,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_FEV1],
								},
								{
									columnWidth: 0.2,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_FEV1_FVC],
								},
								{
									columnWidth: 0.2,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_PEF],
								},
								{
									columnWidth: 0.2,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_FEF2575],
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
							// columnWidth: 1,
							title: "RESULTADOS DEL EXAMEN ESPIROMETRICO",
							layout: "column",
							items: [
								{
									columnWidth: 1,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_recomendacion],
								},
								{
									columnWidth: 1,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_conclusion],
								},
								{
									columnWidth: 1,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_cie10],
								},
								{
									columnWidth: 1,
									border: false,
									layout: "form",
									items: [this.m_espiro_metria_diag],
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
						mod.espiro.espiro_pred.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_espiro_metria"
										: "save_espiro_metria",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function () {
								//Ext.MessageBox.alert('En hora buena', 'El servicio se registro correctamente');
								mod.espiro.formatos.st.reload();
								mod.espiro.st.reload();
								mod.espiro.espiro_pred.win.el.unmask();
								mod.espiro.espiro_pred.win.close();
							},
							failure: function (form, action) {
								mod.espiro.espiro_pred.win.el.unmask();
								mod.espiro.espiro_pred.win.close();
								mod.espiro.formatos.st.reload();
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
			width: 600,
			height: 600,
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
Ext.onReady(mod.espiro.init, mod.espiro);
