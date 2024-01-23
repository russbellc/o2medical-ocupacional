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

Ext.ns("mod.auditoria");
mod.auditoria = {
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
					this.baseParams.columna = mod.auditoria.descripcion.getValue();
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
			items: ["Buscar:", this.descripcion, this.buscador, "->", "|"],
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
						mod.auditoria.formatos.init(record);
					} else {
						mod.auditoria.formatos.init(record);
					}
				},
			},
			autoExpandColumn: "aud_emp",
			columns: [
				// {
				// 	header: "ST",
				// 	width: 25,
				// 	sortable: true,
				// 	dataIndex: "st",
				// 	renderer: function renderIcon(val) {
				// 		if (val == 0) {
				// 			return '<img src="<[images]>/nuevo.png" title="REGISTRAR" height="15">';
				// 		} else if (val == 1) {
				// 			return '<img src="<[images]>/saveIcon.png" title="GUARDADO" height="15">';
				// 		}
				// 	},
				// },
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
					// if (st == "0") {
					// 	return "child-row";
					// } else if (st == "1") {
					// 	return "child-blue";
					// }
				},
			},
		});
	},
};
mod.auditoria.formatos = {
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
			mod.auditoria.formatos.imgStore.removeAll();
			var store = mod.auditoria.formatos.imgStore;
			var record = new store.recordType({
				id: "1",
				pdf: "mod_admision&sys_report=reporte",
				nombre: "FICHA DE ADMISIÓN",
			});
			store.add(record);
		}
	},
	crea_stores: function () {
		this.st = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_formatos",
				format: "json",
			},
			listeners: {
				beforeload: function () {
					this.baseParams.adm = mod.auditoria.formatos.record.get("adm");
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
				"ar_desc",
				"ex_formato",
				"estado",
			],
		});
	},
	crea_controles: function () {
		var tpl = new Ext.XTemplate(
			'<tpl for=".">',
			'<div class="thumb"><iframe width="100%" height="600" src="system/loader.php?sys_acction=sys_loadreport&sys_modname={pdf}&adm={adm}"></iframe></div>',
			"</tpl>"
		);
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
			height: 463,
			listeners: {
				rowclick: function (grid, rowIndex, event) {
					event.stopEvent();
					var record = grid.getStore().getAt(rowIndex);

					mod.auditoria.auditar.init(record);
				},
				rowcontextmenu: function (grid, index, event) {
					event.stopEvent();
					var record = grid.getStore().getAt(index);
					new Ext.menu.Menu({
						items: [
							{
								text: "AUDITAR: <B>" + record.get("ex_desc") + "<B>",
								iconCls: "add",
								handler: function () {
									mod.auditoria.auditar.init(record);
								},
							},
						],
					}).showAt(event.xy);
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
						if (val == null) {
							return '<img src="<[images]>/nuevo.png" title="REGISTRAR" height="14">';
						} else {
							return '<img src="<[images]>/saveIcon.png" title="GUARDADO" height="14">';
						}
					},
				},
				{
					id: "cuest_desc",
					header: "EXAMENES",
					dataIndex: "ex_desc",
				},
				{
					header: "ESTADO",
					dataIndex: "estado",
					align: "center",
					width: 90,
					renderer: function (val, meta, record) {
						if (val == "AUDITADO") {
							meta.css = "stkGreen";
						} else if (val == "OBSERVADO") {
							meta.css = "stkRed";
						} else {
							return "<b><center><h3>N/R</h3></center></b>";
						}
						return "<b><center><h3>" + val + "</h3></center></b>";
					},
				},
				{
					header: "Servicio",
					dataIndex: "ar_desc",
					width: 130,
				},
			],
			viewConfig: {
				getRowClass: function (record, index) {
					var st = record.get("st");
					if (st == null) {
						return "stkYellow";
					} else if (st == 1) {
						return "";
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
				// {
				// 	columnWidth: 0.65,
				// 	border: false,
				// 	layout: "form",
				// 	items: [this.detalle],
				// },
				{
					columnWidth: 0.99,
					border: false,
					layout: "form",
					items: [this.dt_grid],
				},
			],
		});
		this.win = new Ext.Window({
			width: 600,
			height: 500,
			modal: true,
			title: "AUDITORIA MÉDICA DEL PACIENTE: " + this.record.get("nombre"),
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

mod.auditoria.auditar = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.crea_controles();
		this.list_mod_auditoria_detalle.load();
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
				acction: "load_auditoria",
				format: "json",
				m_auditor_id: mod.auditoria.auditar.record.get("id"),
				m_auditor_id: mod.auditoria.auditar.record.get("id"),
				//                ,examen: mod.auditoria.auditar.record.get('ex_id')
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.auditoria.auditar.val_medico.setValue(r.val_medico);
				//                mod.auditoria.auditar.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_stores: function () {
		this.list_mod_auditoria_detalle = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_mod_auditoria_detalle",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"m_ad_id", "m_ad_id", "m_ad_obs"
			],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.m_ad_adm = mod.auditoria.auditar.record.get("adm");
					this.baseParams.m_ad_examen = mod.auditoria.auditar.record.get("ex_id");
				},
			},
		});
	},
	crea_controles: function () {
		// m_auditor_st
		this.m_auditor_estado = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>ESTADO DE LA AUDITORIA DE " + this.record.get("ex_desc") + "</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "AUDITADO",
					name: "m_auditor_estado",
					inputValue: "AUDITADO",
					checked: true,
				},
				{
					boxLabel: "OBSERVADO",
					name: "m_auditor_estado",
					inputValue: "OBSERVADO",
				},
			],
		});

		this.detalle = new Ext.Panel({
			frame: true,
			region: "center",
			height: 600,
			border: false,
			anchor: "99%",
			html:
				"<iframe width='100%' height='520' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=" +
				this.record.get("ex_formato") +
				"&adm=" +
				this.record.get("adm") +
				"'></iframe>",
		});

		this.tbar4 = new Ext.Toolbar({
			items: [
				"->",
				'<b style="color:#000000;">DETALLE LAS OBSERVACIONES</b>',
				"-",
				{
					text: "Nuevo",
					iconCls: "nuevo",
					handler: function () {
						mod.auditoria.detalle_auditar.init(null);
					},
				},
			],
		});
		this.dt_grid4 = new Ext.grid.GridPanel({
			store: this.list_mod_auditoria_detalle,
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
					mod.auditoria.detalle_auditar.init(record2);
				},
			},
			autoExpandColumn: "reco_desc",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "reco_desc",
					header: "OBSERVACIONES",
					dataIndex: "m_ad_obs",
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
					columnWidth: 0.65,
					labelWidth: 1,
					labelAlign: "top",
					layout: "form",
					border: false,
					items: [this.detalle],
				},
				{
					xtype: "panel",
					border: false,
					columnWidth: 0.35,
					bodyStyle: "padding:2px 22px 0px 0px;",
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
									labelWidth: 60,
									items: [this.m_auditor_estado],
								},
								{
									columnWidth: 0.999,
									border: false,
									layout: "form",
									labelAlign: "top",
									labelWidth: 60,
									items: [this.dt_grid4],
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
						mod.auditoria.auditar.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") == 1
										? "update_auditoria"
										: "save_auditoria",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								if (action.result.success === true) {
									if (action.result.total === 1) {
										//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
										mod.auditoria.formatos.st.reload();
										mod.auditoria.st.reload();
										mod.auditoria.auditar.win.el.unmask();
										mod.auditoria.auditar.win.close();
									}
								} else {
									Ext.Msg.show({
										title: "Error",
										buttons: Ext.MessageBox.OK,
										icon: Ext.MessageBox.ERROR,
										msg: "Problemas con el registro.",
									});
								}
							},
							failure: function (form, action) {
								mod.auditoria.auditar.win.el.unmask();
								mod.auditoria.auditar.win.close();
								mod.auditoria.formatos.st.reload();
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
			width: 1250,
			height: 600,
			border: false,
			modal: true,
			title: "AUDITORIA MÉDICA: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.auditoria.detalle_auditar = {
	record2: null,
	win: null,
	frm: null,
	conclu_confi_psico_desc: null,
	init: function (r) {
		this.record2 = r;
		this.crea_stores();
		this.st_busca_mod_auditoria_detalle.load();
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
				acction: "load_conclu_confi_psico",
				format: "json",
				m_ad_id: this.record2.get("m_ad_id"),
				m_ad_adm: this.record2.get("m_ad_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_mod_auditoria_detalle = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_mod_auditoria_detalle",
				format: "json",
			},
			fields: ["m_ad_obs"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.resultTpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><span>{m_ad_obs}</span></h3>",
			"</div>",
			"</div></tpl>"
		);

		this.m_ad_obs = new Ext.form.ComboBox({
			store: this.st_busca_mod_auditoria_detalle,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.resultTpl,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 3,
			hiddenName: "m_ad_obs",
			displayField: "m_ad_obs",
			valueField: "m_ad_obs",
			allowBlank: false,
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>OBSERVACIÓN</b>",
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
					items: [this.m_ad_obs],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.auditoria.detalle_auditar.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						var metodo;
						var m_ad_id;
						if (this.record2 !== null) {
							metodo = "update";
							m_ad_id = mod.auditoria.detalle_auditar.record2.get(
								"m_ad_id"
							);
						} else {
							metodo = "save";
							m_ad_id = "";
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_mod_auditoria_detalle",
								m_ad_adm: mod.auditoria.auditar.record.get("adm"),
								m_ad_examen: mod.auditoria.auditar.record.get("ex_id"),
								m_ad_id: m_ad_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.auditoria.detalle_auditar.win.el.unmask();
								mod.auditoria.auditar.list_mod_auditoria_detalle.reload();
								mod.auditoria.detalle_auditar.win.close();
							},
							failure: function (form, action) {
								mod.auditoria.detalle_auditar.win.el.unmask();
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
								mod.auditoria.auditar.list_mod_auditoria_detalle.reload();
								mod.auditoria.detalle_auditar.win.close();
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

Ext.onReady(mod.auditoria.init, mod.auditoria);
