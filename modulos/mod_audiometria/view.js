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

Ext.ns("mod.audio");
mod.audio = {
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
					this.baseParams.columna = mod.audio.descripcion.getValue();
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
		//        this.tbar = new Ext.Toolbar({
		//            items: ['Buscar:', this.descripcion,
		//                this.buscador, '->',
		//                '|', {
		//                    text: 'Reporte x Fecha',
		//                    iconCls: 'reporte',
		//                    handler: function () {
		//                        mod.audio.rfecha.init(null);
		//                    }
		//                }, '|'
		//            ]
		//        });
		this.dt_grid = new Ext.grid.GridPanel({
			store: this.st,
			//            tbar: this.tbar,
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
						mod.audio.formatos.init(record);
					} else {
						mod.audio.formatos.init(record);
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
mod.audio.formatos = {
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
			mod.audio.formatos.imgStore.removeAll();
			var store = mod.audio.formatos.imgStore;
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
					this.baseParams.adm = mod.audio.formatos.record.get("adm");
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
						mod.audio.audio_audio.init(record);
					} else {
						mod.audio.audio_pred.init(record); //
					}
					//                    mod.audio.audio_pred.init(record);//
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
											"FORMATO AUDIOMETRIA N°: <B>" + record.get("adm") + "<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title: "FORMATO AUDIOMETRIA N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_audiometria&sys_report=formato_audiometria&adm=" +
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

mod.audio.audio_pred = {
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
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_rayosx_pred",
				format: "json",
				adm: mod.audio.audio_pred.record.get("adm"),
				examen: mod.audio.audio_pred.record.get("ex_id"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.audio.anexo_16a.val_medico.setValue(r.val_medico);
				//                mod.audio.anexo_16a.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_controles: function () {
		this.m_audio_pred_resultado = new Ext.form.TextField({
			fieldLabel: "<b>RESULTADO DEL EXAMEN</b>",
			allowBlank: false,
			name: "m_audio_pred_resultado",
			anchor: "96%",
		});
		this.m_audio_pred_observaciones = new Ext.form.TextArea({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_audio_pred_observaciones",
			anchor: "99%",
			height: 40,
		});
		this.m_audio_pred_diagnostico = new Ext.form.TextArea({
			fieldLabel: "<b>DIAGNOSTICO</b>",
			name: "m_audio_pred_diagnostico",
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
					items: [this.m_audio_pred_resultado],
				},
				{
					columnWidth: 0.99,
					border: false,
					layout: "form",
					items: [this.m_audio_pred_observaciones],
				},
				{
					columnWidth: 0.99,
					border: false,
					layout: "form",
					items: [this.m_audio_pred_diagnostico],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.audio.audio_pred.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_audio_pred"
										: "save_audio_pred",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function () {
								//                                Ext.MessageBox.alert('En hora buena', 'El servicio se registro correctamente');
								mod.audio.formatos.st.reload();
								mod.audio.st.reload();
								mod.audio.audio_pred.win.el.unmask();
								mod.audio.audio_pred.win.close();
							},
							failure: function (form, action) {
								mod.audio.audio_pred.win.el.unmask();
								mod.audio.audio_pred.win.close();
								mod.audio.formatos.st.reload();
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

mod.audio.audio_audio = {
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
	crea_audiograma_oseo: function (Tname = "", Tvalue = "") {
		let m_a_audio_oseo_125_od = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_125_od.getValue()
		);
		let m_a_audio_oseo_250_od = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_250_od.getValue()
		);
		let m_a_audio_oseo_500_od = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_500_od.getValue()
		);
		let m_a_audio_oseo_1000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_1000_od.getValue()
		);
		let m_a_audio_oseo_2000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_2000_od.getValue()
		);
		let m_a_audio_oseo_3000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_3000_od.getValue()
		);
		let m_a_audio_oseo_4000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_4000_od.getValue()
		);
		let m_a_audio_oseo_6000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_6000_od.getValue()
		);
		let m_a_audio_oseo_8000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_8000_od.getValue()
		);

		let m_a_audio_oseo_125_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_125_oi.getValue()
		);
		let m_a_audio_oseo_250_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_250_oi.getValue()
		);
		let m_a_audio_oseo_500_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_500_oi.getValue()
		);
		let m_a_audio_oseo_1000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_1000_oi.getValue()
		);
		let m_a_audio_oseo_2000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_2000_oi.getValue()
		);
		let m_a_audio_oseo_3000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_3000_oi.getValue()
		);
		let m_a_audio_oseo_4000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_4000_oi.getValue()
		);
		let m_a_audio_oseo_6000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_6000_oi.getValue()
		);
		let m_a_audio_oseo_8000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_oseo_8000_oi.getValue()
		);

		switch (Tname) {
			case "m_a_audio_oseo_125_od":
				m_a_audio_oseo_125_od = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_250_od":
				m_a_audio_oseo_250_od = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_500_od":
				m_a_audio_oseo_500_od = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_1000_od":
				m_a_audio_oseo_1000_od = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_2000_od":
				m_a_audio_oseo_2000_od = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_3000_od":
				m_a_audio_oseo_3000_od = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_4000_od":
				m_a_audio_oseo_4000_od = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_6000_od":
				m_a_audio_oseo_6000_od = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_8000_od":
				m_a_audio_oseo_8000_od = parseInt(Tvalue);
				break;

			case "m_a_audio_oseo_125_oi":
				m_a_audio_oseo_125_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_250_oi":
				m_a_audio_oseo_250_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_500_oi":
				m_a_audio_oseo_500_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_1000_oi":
				m_a_audio_oseo_1000_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_2000_oi":
				m_a_audio_oseo_2000_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_3000_oi":
				m_a_audio_oseo_3000_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_4000_oi":
				m_a_audio_oseo_4000_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_6000_oi":
				m_a_audio_oseo_6000_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_oseo_8000_oi":
				m_a_audio_oseo_8000_oi = parseInt(Tvalue);
				break;

			default:
				break;
		}
		var arreglo02 = [
			[125, m_a_audio_oseo_125_od, m_a_audio_oseo_125_oi],
			[250, m_a_audio_oseo_250_od, m_a_audio_oseo_250_oi],
			[500, m_a_audio_oseo_500_od, m_a_audio_oseo_500_oi],
			[1000, m_a_audio_oseo_1000_od, m_a_audio_oseo_1000_oi],
			[2000, m_a_audio_oseo_2000_od, m_a_audio_oseo_2000_oi],
			[3000, m_a_audio_oseo_3000_od, m_a_audio_oseo_3000_oi],
			[4000, m_a_audio_oseo_4000_od, m_a_audio_oseo_4000_oi],
			[6000, m_a_audio_oseo_6000_od, m_a_audio_oseo_6000_oi],
			[8000, m_a_audio_oseo_8000_od, m_a_audio_oseo_8000_oi],
		];
		change_data2(arreglo02);
		const audiograma_img_oseo = document.querySelector(".array01");
		mod.audio.audio_audio.audiograma_oseo.setValue(
			audiograma_img_oseo.textContent
		);
		// console.log(audiograma_img.textContent);
	},
	crea_audiograma: function (Tname = "", Tvalue = "") {
		let m_a_audio_aereo_125_od = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_125_od.getValue()
		);
		let m_a_audio_aereo_250_od = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_250_od.getValue()
		);
		let m_a_audio_aereo_500_od = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_500_od.getValue()
		);
		let m_a_audio_aereo_1000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_1000_od.getValue()
		);
		let m_a_audio_aereo_2000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_2000_od.getValue()
		);
		let m_a_audio_aereo_3000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_3000_od.getValue()
		);
		let m_a_audio_aereo_4000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_4000_od.getValue()
		);
		let m_a_audio_aereo_6000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_6000_od.getValue()
		);
		let m_a_audio_aereo_8000_od = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_8000_od.getValue()
		);

		let m_a_audio_aereo_125_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_125_oi.getValue()
		);
		let m_a_audio_aereo_250_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_250_oi.getValue()
		);
		let m_a_audio_aereo_500_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_500_oi.getValue()
		);
		let m_a_audio_aereo_1000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_1000_oi.getValue()
		);
		let m_a_audio_aereo_2000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_2000_oi.getValue()
		);
		let m_a_audio_aereo_3000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_3000_oi.getValue()
		);
		let m_a_audio_aereo_4000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_4000_oi.getValue()
		);
		let m_a_audio_aereo_6000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_6000_oi.getValue()
		);
		let m_a_audio_aereo_8000_oi = parseInt(
			mod.audio.audio_audio.m_a_audio_aereo_8000_oi.getValue()
		);

		switch (Tname) {
			case "m_a_audio_aereo_125_od":
				m_a_audio_aereo_125_od = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_250_od":
				m_a_audio_aereo_250_od = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_500_od":
				m_a_audio_aereo_500_od = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_1000_od":
				m_a_audio_aereo_1000_od = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_2000_od":
				m_a_audio_aereo_2000_od = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_3000_od":
				m_a_audio_aereo_3000_od = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_4000_od":
				m_a_audio_aereo_4000_od = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_6000_od":
				m_a_audio_aereo_6000_od = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_8000_od":
				m_a_audio_aereo_8000_od = parseInt(Tvalue);
				break;

			case "m_a_audio_aereo_125_oi":
				m_a_audio_aereo_125_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_250_oi":
				m_a_audio_aereo_250_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_500_oi":
				m_a_audio_aereo_500_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_1000_oi":
				m_a_audio_aereo_1000_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_2000_oi":
				m_a_audio_aereo_2000_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_3000_oi":
				m_a_audio_aereo_3000_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_4000_oi":
				m_a_audio_aereo_4000_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_6000_oi":
				m_a_audio_aereo_6000_oi = parseInt(Tvalue);
				break;
			case "m_a_audio_aereo_8000_oi":
				m_a_audio_aereo_8000_oi = parseInt(Tvalue);
				break;

			default:
				break;
		}
		var arreglo01 = [
			[125, m_a_audio_aereo_125_od, m_a_audio_aereo_125_oi],
			[250, m_a_audio_aereo_250_od, m_a_audio_aereo_250_oi],
			[500, m_a_audio_aereo_500_od, m_a_audio_aereo_500_oi],
			[1000, m_a_audio_aereo_1000_od, m_a_audio_aereo_1000_oi],
			[2000, m_a_audio_aereo_2000_od, m_a_audio_aereo_2000_oi],
			[3000, m_a_audio_aereo_3000_od, m_a_audio_aereo_3000_oi],
			[4000, m_a_audio_aereo_4000_od, m_a_audio_aereo_4000_oi],
			[6000, m_a_audio_aereo_6000_od, m_a_audio_aereo_6000_oi],
			[8000, m_a_audio_aereo_8000_od, m_a_audio_aereo_8000_oi],
		];
		change_data(arreglo01);
		const audiograma_img = document.querySelector(".array02");
		mod.audio.audio_audio.audiograma_aereo.setValue(audiograma_img.textContent);
		// console.log(audiograma_img.textContent);
	},
	cargar_data: function () {
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_audio_audio",
				format: "json",
				adm: mod.audio.audio_audio.record.get("adm"),
				//                ,examen: mod.audio.audio_audio.record.get('ex_id')
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				// const audiograma_img = document.querySelector(".chart_div");
				// audiograma_img.innerHTML =
				// 	'<img src="<[sys_images]>/audio/audiograma_aereo" + this.record.get("adm") + ".png">';
				setTimeout(() => {
					this.crea_audiograma((Tname = "nada"), (Tvalue = ""));
					this.crea_audiograma_oseo((Tname = "nada"), (Tvalue = ""));
				}, 2000);
				//                mod.audio.audio_audio.val_medico.setValue(r.val_medico);
				//                mod.audio.audio_audio.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_stores: function () {
		this.st_m_a_audio_diag_aereo_od = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_a_audio_diag_aereo_od",
				format: "json",
			},
			fields: ["m_a_audio_diag_aereo_od"],
			root: "data",
		});
		this.st_m_a_audio_diag_aereo_oi = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_a_audio_diag_aereo_oi",
				format: "json",
			},
			fields: ["m_a_audio_diag_aereo_oi"],
			root: "data",
		});
		this.st_m_a_audio_diag_osteo_od = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_a_audio_diag_osteo_od",
				format: "json",
			},
			fields: ["m_a_audio_diag_osteo_od"],
			root: "data",
		});
		this.st_m_a_audio_diag_osteo_oi = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_a_audio_diag_osteo_oi",
				format: "json",
			},
			fields: ["m_a_audio_diag_osteo_oi"],
			root: "data",
		});
		this.st_m_a_audio_kclokhoff = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_m_a_audio_kclokhoff",
				format: "json",
			},
			fields: ["m_a_audio_kclokhoff"],
			root: "data",
		});
	},
	crea_controles: function () {
		//m_a_audio_ocupacion
		this.m_a_audio_ocupacion = new Ext.form.TextField({
			fieldLabel: "<b>OCUPACIÓN</b>",
			name: "m_a_audio_ocupacion",
			anchor: "95%",
		});
		//m_a_audio_anios
		this.m_a_audio_anios = new Ext.form.TextField({
			fieldLabel: "<b>AÑOS</b>",
			name: "m_a_audio_anios",
			anchor: "95%",
		});
		//m_a_audio_horas_expo
		this.m_a_audio_horas_expo = new Ext.form.TextField({
			fieldLabel: "<b>HORAS DE EXPOSICIÓN</b>",
			name: "m_a_audio_horas_expo",
			anchor: "95%",
		});
		//m_a_audio_ruido_laboral
		this.m_a_audio_ruido_laboral = new Ext.form.RadioGroup({
			fieldLabel: "<b>APRECIACION DEL RUIDO EN AREA LABORAL</b>",
			items: [
				{
					boxLabel: "Ruido no Molesto",
					name: "m_a_audio_ruido_laboral",
					inputValue: "Ruido no Molesto",
				},
				{
					boxLabel: "Ruido Moderado",
					name: "m_a_audio_ruido_laboral",
					inputValue: "Ruido Moderado",
					checked: true,
				},
				{
					boxLabel: "Ruido Muy Intenso",
					name: "m_a_audio_ruido_laboral",
					inputValue: "Ruido Muy Intenso",
				},
			],
		});
		//m_a_audio_antece_familiar
		this.m_a_audio_antece_familiar = new Ext.form.RadioGroup({
			fieldLabel: "<b>ANTECEDENTES FAMILIAR</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_familiar", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_familiar",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_antece_familiar_coment
		this.m_a_audio_antece_familiar_coment = new Ext.form.TextField({
			fieldLabel: "<b>COMENTARIOS</b>",
			name: "m_a_audio_antece_familiar_coment",
			anchor: "95%",
		});
		//m_a_audio_antece_01
		this.m_a_audio_antece_01 = new Ext.form.RadioGroup({
			fieldLabel: "<b>CONSUMO DE TABACO</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_01", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_01",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_antece_02
		this.m_a_audio_antece_02 = new Ext.form.RadioGroup({
			fieldLabel: "<b>SERVICIO MILITAR</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_02", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_02",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_antece_03
		this.m_a_audio_antece_03 = new Ext.form.RadioGroup({
			fieldLabel:
				"<b>HOBBIES CON EXPOSICIÓN A RUIDO: TIRO, DISCOTECAS, AUDIFONOS</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_03", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_03",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_antece_04
		this.m_a_audio_antece_04 = new Ext.form.RadioGroup({
			fieldLabel: "<b>EXPOSICIÓN LABORAL A QUÍMICOS</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_04", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_04",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_antece_05
		this.m_a_audio_antece_05 = new Ext.form.RadioGroup({
			fieldLabel: "<b>INFECCION DE OÍDO: OMA, OTITIS CRÓNICA</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_05", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_05",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_antece_06
		this.m_a_audio_antece_06 = new Ext.form.RadioGroup({
			fieldLabel: "<b>USO DE OTOTOXICOS</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_06", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_06",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_antece_07
		this.m_a_audio_antece_07 = new Ext.form.RadioGroup({
			fieldLabel: "<b>TRAUMATISMO ENCÉFALO CRANEANO, MENINGITIS</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_07", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_07",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_antece_08
		this.m_a_audio_antece_08 = new Ext.form.RadioGroup({
			fieldLabel: "<b>TRAUMA ACUSTICO</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_08", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_08",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_antece_09
		this.m_a_audio_antece_09 = new Ext.form.RadioGroup({
			fieldLabel: "<b>SARAMPION</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_09", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_09",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_antece_10
		this.m_a_audio_antece_10 = new Ext.form.RadioGroup({
			fieldLabel: "<b>PAROTIDITIS</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_antece_10", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_antece_10",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_sintoma_01
		this.m_a_audio_sintoma_01 = new Ext.form.RadioGroup({
			fieldLabel: "<b>DISMINUCION DE LA AUDICION</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_sintoma_01", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_sintoma_01",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_sintoma_02
		this.m_a_audio_sintoma_02 = new Ext.form.RadioGroup({
			fieldLabel: "<b>DOLOR DE OIDOS</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_sintoma_02", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_sintoma_02",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_sintoma_03
		this.m_a_audio_sintoma_03 = new Ext.form.RadioGroup({
			fieldLabel: "<b>ZUMBIDOS, ACÚFENOS</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_sintoma_03", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_sintoma_03",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_sintoma_04
		this.m_a_audio_sintoma_04 = new Ext.form.RadioGroup({
			fieldLabel: "<b>MAREOS, VERTIGO</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_sintoma_04", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_sintoma_04",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_sintoma_05
		this.m_a_audio_sintoma_05 = new Ext.form.RadioGroup({
			fieldLabel: "<b>INFECCION DE OIDO</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_sintoma_05", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_sintoma_05",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_sintoma_06
		this.m_a_audio_sintoma_06 = new Ext.form.RadioGroup({
			fieldLabel: "<b>EXPOSICION RECIENTE A RUIDOS (18hrs)</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_sintoma_06", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_sintoma_06",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_sintoma_07
		this.m_a_audio_sintoma_07 = new Ext.form.RadioGroup({
			fieldLabel: "<b>OTRAS</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_sintoma_07", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_sintoma_07",
					inputValue: "NO",
					checked: true,
				},
			],
		});

		//m_a_audio_sintoma_07_desc
		this.m_a_audio_sintoma_07_desc = new Ext.form.TextField({
			fieldLabel: "<b>DE EXISTIR SINTOMATOLOGIA, TIEMPO DE ENFERMEDAD</b>",
			name: "m_a_audio_sintoma_07_desc",
			value: "-",
			anchor: "95%",
		});

		//m_a_audio_tapones
		this.m_a_audio_tapones = new Ext.form.RadioGroup({
			fieldLabel: "<b>TAPONES</b>",
			items: [
				{ boxLabel: "NUNCA", name: "m_a_audio_tapones", inputValue: "NUNCA" },
				{
					boxLabel: "OCASIONAL",
					name: "m_a_audio_tapones",
					inputValue: "OCASIONAL",
				},
				{
					boxLabel: "SIEMPRE",
					name: "m_a_audio_tapones",
					inputValue: "SIEMPRE",
				},
			],
		});
		//m_a_audio_orejeras
		this.m_a_audio_orejeras = new Ext.form.RadioGroup({
			fieldLabel: "<b>OREJERAS</b>",
			items: [
				{ boxLabel: "NUNCA", name: "m_a_audio_orejeras", inputValue: "NUNCA" },
				{
					boxLabel: "OCASIONAL",
					name: "m_a_audio_orejeras",
					inputValue: "OCASIONAL",
				},
				{
					boxLabel: "SIEMPRE",
					name: "m_a_audio_orejeras",
					inputValue: "SIEMPRE",
				},
			],
		});
		//m_a_audio_nariz
		this.m_a_audio_nariz = new Ext.form.RadioGroup({
			fieldLabel: "<b>NARIZ</b>",
			items: [
				{
					boxLabel: "NORMAL",
					name: "m_a_audio_nariz",
					inputValue: "NORMAL",
					checked: true,
				},
				{ boxLabel: "ANORMAL", name: "m_a_audio_nariz", inputValue: "ANORMAL" },
			],
		});
		//m_a_audio_nariz_esp
		this.m_a_audio_nariz_esp = new Ext.form.TextField({
			fieldLabel: "<b>ESPECIFICAR</b>",
			name: "m_a_audio_nariz_esp",
			value: "-",
			anchor: "95%",
		});
		//m_a_audio_orofaringe
		this.m_a_audio_orofaringe = new Ext.form.RadioGroup({
			fieldLabel: "<b>OROFARINGE</b>",
			items: [
				{
					boxLabel: "NORMAL",
					name: "m_a_audio_orofaringe",
					inputValue: "NORMAL",
					checked: true,
				},
				{
					boxLabel: "ANORMAL",
					name: "m_a_audio_orofaringe",
					inputValue: "ANORMAL",
				},
			],
		});
		//m_a_audio_orofaringe_esp
		this.m_a_audio_orofaringe_esp = new Ext.form.TextField({
			fieldLabel: "<b>ESPECIFICAR</b>",
			name: "m_a_audio_orofaringe_esp",
			value: "-",
			anchor: "95%",
		});
		//m_a_audio_oido
		this.m_a_audio_oido = new Ext.form.RadioGroup({
			fieldLabel: "<b>OIDO</b>",
			items: [
				{
					boxLabel: "NORMAL",
					name: "m_a_audio_oido",
					inputValue: "NORMAL",
					checked: true,
				},
				{ boxLabel: "ANORMAL", name: "m_a_audio_oido", inputValue: "ANORMAL" },
			],
		});
		//m_a_audio_oido_esp
		this.m_a_audio_oido_esp = new Ext.form.TextField({
			fieldLabel: "<b>ESPECIFICAR</b>",
			name: "m_a_audio_oido_esp",
			value: "-",
			anchor: "95%",
		});
		//m_a_audio_otros
		this.m_a_audio_otros = new Ext.form.RadioGroup({
			fieldLabel: "<b>OIDO</b>",
			items: [
				{
					boxLabel: "NORMAL",
					name: "m_a_audio_otros",
					inputValue: "NORMAL",
					checked: true,
				},
				{ boxLabel: "ANORMAL", name: "m_a_audio_otros", inputValue: "ANORMAL" },
			],
		});
		//m_a_audio_otros_esp
		this.m_a_audio_otros_esp = new Ext.form.TextField({
			fieldLabel: "<b>ESPECIFICAR</b>",
			name: "m_a_audio_otros_esp",
			value: "-",
			anchor: "95%",
		});
		//m_a_audio_otos_triangulo_od

		this.m_a_audio_otos_triangulo_od = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["TRIANGULO DE LUZ PRESENTE", "TRIANGULO DE LUZ PRESENTE"],
					["TRIANGULO DE LUZ NO PRESENTE", "TRIANGULO DE LUZ NO PRESENTE"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_a_audio_otos_triangulo_od",
			fieldLabel: "<b>TRIANGULO DE LUZ</b>",
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
		//m_a_audio_otos_perfora_od
		this.m_a_audio_otos_perfora_od = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO PERFORACIONES", "NO PERFORACIONES"],
					["CON PERFORACIONES", "CON PERFORACIONES"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_a_audio_otos_perfora_od",
			fieldLabel: "<b>PERFORACIONES</b>",
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
		//m_a_audio_otos_abomba_od
		this.m_a_audio_otos_abomba_od = new Ext.form.RadioGroup({
			fieldLabel: "<b>ABOMBAMIENTO</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_otos_abomba_od", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_otos_abomba_od",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_otos_serumen_od
		this.m_a_audio_otos_serumen_od = new Ext.form.RadioGroup({
			fieldLabel: "<b>SERUMEN</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_otos_serumen_od", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_otos_serumen_od",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_otos_triangulo_oi
		this.m_a_audio_otos_triangulo_oi = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["TRIANGULO DE LUZ PRESENTE", "TRIANGULO DE LUZ PRESENTE"],
					["TRIANGULO DE LUZ NO PRESENTE", "TRIANGULO DE LUZ NO PRESENTE"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_a_audio_otos_triangulo_oi",
			fieldLabel: "<b>TRIANGULO DE LUZ</b>",
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
		//m_a_audio_otos_perfora_oi

		this.m_a_audio_otos_perfora_oi = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO PERFORACIONES", "NO PERFORACIONES"],
					["CON PERFORACIONES", "CON PERFORACIONES"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_a_audio_otos_perfora_oi",
			fieldLabel: "<b>PERFORACIONES</b>",
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
		//m_a_audio_otos_abomba_oi
		this.m_a_audio_otos_abomba_oi = new Ext.form.RadioGroup({
			fieldLabel: "<b>ABOMBAMIENTO</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_otos_abomba_oi", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_otos_abomba_oi",
					inputValue: "NO",
					checked: true,
				},
			],
		});
		//m_a_audio_otos_serumen_oi
		this.m_a_audio_otos_serumen_oi = new Ext.form.RadioGroup({
			fieldLabel: "<b>SERUMEN</b>",
			items: [
				{ boxLabel: "SI", name: "m_a_audio_otos_serumen_oi", inputValue: "SI" },
				{
					boxLabel: "NO",
					name: "m_a_audio_otos_serumen_oi",
					inputValue: "NO",
					checked: true,
				},
			],
		});

		//m_a_audio_otos_permeable_od
		this.m_a_audio_otos_permeable_od = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["CAE PERMEABLE", "CAE PERMEABLE"],
					["CAE NO PERMEABLE", "CAE NO PERMEABLE"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_a_audio_otos_permeable_od",
			fieldLabel: "<b>PERMEABILIDAD</b>",
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
		//m_a_audio_otos_retraccion_od
		this.m_a_audio_otos_retraccion_od = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO RETRACCION", "NO RETRACCION"],
					["CON RETRACCION", "CON RETRACCION"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_a_audio_otos_retraccion_od",
			fieldLabel: "<b>RETRACCION</b>",
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
		//
		//
		//m_a_audio_otos_permeable_oi
		this.m_a_audio_otos_permeable_oi = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["CAE PERMEABLE", "CAE PERMEABLE"],
					["CAE NO PERMEABLE", "CAE NO PERMEABLE"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_a_audio_otos_permeable_oi",
			fieldLabel: "<b>PERMEABILIDAD</b>",
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
		//m_a_audio_otos_retraccion_oi
		this.m_a_audio_otos_retraccion_oi = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NO RETRACCION", "NO RETRACCION"],
					["CON RETRACCION", "CON RETRACCION"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_a_audio_otos_retraccion_oi",
			fieldLabel: "<b>RETRACCION</b>",
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

		// this.audiograma = new Ext.form.TextArea({
		// 	fieldLabel: "<b>audiograma</b>",
		// 	name: "audiograma",
		// 	value: "-",
		// 	anchor: "95%",
		// 	height: 100,
		// });
		this.audiograma_aereo = new Ext.form.Hidden({
			fieldLabel: "<b>id</b>",
			name: "audiograma_aereo",
		});
		this.audiograma_oseo = new Ext.form.Hidden({
			fieldLabel: "<b>id</b>",
			name: "audiograma_oseo",
		});

		//m_a_audio_aereo_125_od
		this.m_a_audio_aereo_125_od = new Ext.form.TextField({
			name: "m_a_audio_aereo_125_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_250_od
		this.m_a_audio_aereo_250_od = new Ext.form.TextField({
			name: "m_a_audio_aereo_250_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_500_od
		this.m_a_audio_aereo_500_od = new Ext.form.TextField({
			name: "m_a_audio_aereo_500_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_1000_od
		this.m_a_audio_aereo_1000_od = new Ext.form.TextField({
			name: "m_a_audio_aereo_1000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_2000_od
		this.m_a_audio_aereo_2000_od = new Ext.form.TextField({
			name: "m_a_audio_aereo_2000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_3000_od
		this.m_a_audio_aereo_3000_od = new Ext.form.TextField({
			name: "m_a_audio_aereo_3000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_4000_od
		this.m_a_audio_aereo_4000_od = new Ext.form.TextField({
			name: "m_a_audio_aereo_4000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_6000_od
		this.m_a_audio_aereo_6000_od = new Ext.form.TextField({
			name: "m_a_audio_aereo_6000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_8000_od
		this.m_a_audio_aereo_8000_od = new Ext.form.TextField({
			name: "m_a_audio_aereo_8000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_125_oi
		this.m_a_audio_aereo_125_oi = new Ext.form.TextField({
			name: "m_a_audio_aereo_125_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_250_oi
		this.m_a_audio_aereo_250_oi = new Ext.form.TextField({
			name: "m_a_audio_aereo_250_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_500_oi
		this.m_a_audio_aereo_500_oi = new Ext.form.TextField({
			name: "m_a_audio_aereo_500_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_1000_oi
		this.m_a_audio_aereo_1000_oi = new Ext.form.TextField({
			name: "m_a_audio_aereo_1000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_2000_oi
		this.m_a_audio_aereo_2000_oi = new Ext.form.TextField({
			name: "m_a_audio_aereo_2000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_3000_oi
		this.m_a_audio_aereo_3000_oi = new Ext.form.TextField({
			name: "m_a_audio_aereo_3000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_4000_oi
		this.m_a_audio_aereo_4000_oi = new Ext.form.TextField({
			name: "m_a_audio_aereo_4000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_6000_oi
		this.m_a_audio_aereo_6000_oi = new Ext.form.TextField({
			name: "m_a_audio_aereo_6000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_aereo_8000_oi
		this.m_a_audio_aereo_8000_oi = new Ext.form.TextField({
			name: "m_a_audio_aereo_8000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma(target.name, target.value);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_125_od
		this.m_a_audio_oseo_125_od = new Ext.form.TextField({
			name: "m_a_audio_oseo_125_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_250_od
		this.m_a_audio_oseo_250_od = new Ext.form.TextField({
			name: "m_a_audio_oseo_250_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_500_od
		this.m_a_audio_oseo_500_od = new Ext.form.TextField({
			name: "m_a_audio_oseo_500_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_1000_od
		this.m_a_audio_oseo_1000_od = new Ext.form.TextField({
			name: "m_a_audio_oseo_1000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_2000_od
		this.m_a_audio_oseo_2000_od = new Ext.form.TextField({
			name: "m_a_audio_oseo_2000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_3000_od
		this.m_a_audio_oseo_3000_od = new Ext.form.TextField({
			name: "m_a_audio_oseo_3000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_4000_od
		this.m_a_audio_oseo_4000_od = new Ext.form.TextField({
			name: "m_a_audio_oseo_4000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_6000_od
		this.m_a_audio_oseo_6000_od = new Ext.form.TextField({
			name: "m_a_audio_oseo_6000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_8000_od
		this.m_a_audio_oseo_8000_od = new Ext.form.TextField({
			name: "m_a_audio_oseo_8000_od",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_125_oi
		this.m_a_audio_oseo_125_oi = new Ext.form.TextField({
			name: "m_a_audio_oseo_125_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_250_oi
		this.m_a_audio_oseo_250_oi = new Ext.form.TextField({
			name: "m_a_audio_oseo_250_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_500_oi
		this.m_a_audio_oseo_500_oi = new Ext.form.TextField({
			name: "m_a_audio_oseo_500_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_1000_oi
		this.m_a_audio_oseo_1000_oi = new Ext.form.TextField({
			name: "m_a_audio_oseo_1000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_2000_oi
		this.m_a_audio_oseo_2000_oi = new Ext.form.TextField({
			name: "m_a_audio_oseo_2000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_3000_oi
		this.m_a_audio_oseo_3000_oi = new Ext.form.TextField({
			name: "m_a_audio_oseo_3000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_4000_oi
		this.m_a_audio_oseo_4000_oi = new Ext.form.TextField({
			name: "m_a_audio_oseo_4000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_6000_oi
		this.m_a_audio_oseo_6000_oi = new Ext.form.TextField({
			name: "m_a_audio_oseo_6000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_oseo_8000_oi
		this.m_a_audio_oseo_8000_oi = new Ext.form.TextField({
			name: "m_a_audio_oseo_8000_oi",
			minLength: 1,
			autoCreate: {
				tag: "input",
				maxlength: 3,
				minLength: 1,
				type: "text",
				size: "3",
				autocomplete: "off",
			},
			width: 80,
			listeners: {
				render: function (editorObject) {
					editorObject.getEl().on({
						blur: function (event, target, scope) {
							mod.audio.audio_audio.crea_audiograma_oseo(
								target.name,
								target.value
							);
						},
						scope: editorObject,
					});
				},
			},
		});
		//m_a_audio_diag_aereo_od
		this.Tpl_m_a_audio_diag_aereo_od = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_a_audio_diag_aereo_od}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_a_audio_diag_aereo_od = new Ext.form.ComboBox({
			store: this.st_m_a_audio_diag_aereo_od,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_a_audio_diag_aereo_od,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_a_audio_diag_aereo_od",
			displayField: "m_a_audio_diag_aereo_od",
			valueField: "m_a_audio_diag_aereo_od",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DIAGNOSTICO OD</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_a_audio_diag_aereo_oi
		this.Tpl_m_a_audio_diag_aereo_oi = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_a_audio_diag_aereo_oi}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_a_audio_diag_aereo_oi = new Ext.form.ComboBox({
			store: this.st_m_a_audio_diag_aereo_oi,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_a_audio_diag_aereo_oi,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_a_audio_diag_aereo_oi",
			displayField: "m_a_audio_diag_aereo_oi",
			valueField: "m_a_audio_diag_aereo_oi",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DIAGNOSTICO OI</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_a_audio_diag_osteo_od
		this.Tpl_m_a_audio_diag_osteo_od = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_a_audio_diag_osteo_od}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_a_audio_diag_osteo_od = new Ext.form.ComboBox({
			store: this.st_m_a_audio_diag_osteo_od,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_a_audio_diag_osteo_od,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_a_audio_diag_osteo_od",
			displayField: "m_a_audio_diag_osteo_od",
			valueField: "m_a_audio_diag_osteo_od",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DIAGNOSTICO OD</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_a_audio_diag_osteo_oi
		this.Tpl_m_a_audio_diag_osteo_oi = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_a_audio_diag_osteo_oi}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_a_audio_diag_osteo_oi = new Ext.form.ComboBox({
			store: this.st_m_a_audio_diag_osteo_oi,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_a_audio_diag_osteo_oi,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_a_audio_diag_osteo_oi",
			displayField: "m_a_audio_diag_osteo_oi",
			valueField: "m_a_audio_diag_osteo_oi",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>DIAGNOSTICO OI</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_a_audio_kclokhoff
		this.Tpl_m_a_audio_kclokhoff = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><b>{m_a_audio_kclokhoff}</b></h3>",
			"</div>",
			"</div></tpl>"
		);
		this.m_a_audio_kclokhoff = new Ext.form.ComboBox({
			store: this.st_m_a_audio_kclokhoff,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.Tpl_m_a_audio_kclokhoff,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 1,
			hiddenName: "m_a_audio_kclokhoff",
			displayField: "m_a_audio_kclokhoff",
			valueField: "m_a_audio_kclokhoff",
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>CLASIFICACION DE KCLOKHOFF MOD.</b>",
			mode: "remote",
			anchor: "95%",
		});
		//m_a_audio_comentarios
		this.m_a_audio_comentarios = new Ext.form.TextArea({
			name: "m_a_audio_comentarios",
			fieldLabel: "<b>COMENTARIOS</b>",
			value: "-",
			anchor: "99%",
			height: 40,
		});

		this.empresa = new Ext.form.TextField({
			fieldLabel: "<b>EMPRESA</b>",
			name: "empresa",
			value: mod.audio.formatos.record.get("emp_desc"),
			anchor: "95%",
		});

		this.grafico_od = new Ext.Panel({
			anchor: "95%",
			border: false,
			monitorValid: true,
			autoLoad: {
				url: "./extras/audiometria.html",
				scripts: true,
			},
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
					title: "<b>--->  AUDIOMETRIA - CUESTIONARIO</b>",
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
									title: "EMPRESA ACTUAL - EXPOSICION LABORAL",
									items: [
										{
											columnWidth: 0.35,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.empresa],
										},
										{
											columnWidth: 0.35,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_a_audio_ocupacion],
										},
										{
											columnWidth: 0.1,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_a_audio_anios],
										},
										{
											columnWidth: 0.2,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_a_audio_horas_expo],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 310,
											//labelAlign: 'top',
											items: [this.m_a_audio_ruido_laboral],
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
									title: "SINTOMAS ACTUALES",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_sintoma_01],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_sintoma_02],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_sintoma_03],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_sintoma_04],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_sintoma_05],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_sintoma_06],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_sintoma_07],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelWidth: 350,
											labelAlign: "top",
											items: [this.m_a_audio_sintoma_07_desc],
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
									title: "ANTECEDENTES RELACIONADOS",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_antece_01],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_antece_02],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_antece_03],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_antece_04],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_antece_05],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_antece_06],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_antece_07],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_antece_08],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_antece_09],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 350,
											//labelAlign: 'top',
											items: [this.m_a_audio_antece_10],
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
									title: "USO DE EPP AUDITIVO",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 80,
											//labelAlign: 'top',
											items: [this.m_a_audio_tapones],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelWidth: 80,
											//labelAlign: 'top',
											items: [this.m_a_audio_orejeras],
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
									title:
										"ALGUN FAMILIAR(PADRES, HERMANOS, TIOS O ABUELOS) QUE SUFRAN O HAYAN SUFRIDO DE SORDERA",
									items: [
										{
											columnWidth: 0.4,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_a_audio_antece_familiar],
										},
										{
											columnWidth: 0.6,
											border: false,
											layout: "form",
											labelAlign: "top",
											items: [this.m_a_audio_antece_familiar_coment],
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
									title: "OTOSCOPIA OD",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 150,
											items: [this.m_a_audio_otos_triangulo_od],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 150,
											items: [this.m_a_audio_otos_perfora_od],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 150,
											items: [this.m_a_audio_otos_permeable_od],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 150,
											items: [this.m_a_audio_otos_retraccion_od],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 150,
											items: [this.m_a_audio_otos_abomba_od],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 150,
											items: [this.m_a_audio_otos_serumen_od],
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
									title: "OTOSCOPIA OI",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 150,
											items: [this.m_a_audio_otos_triangulo_oi],
										},
										{
											columnWidth: 0.999,
											border: false,
											labelWidth: 150,
											layout: "form",
											//labelAlign: 'top',
											items: [this.m_a_audio_otos_perfora_oi],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 150,
											items: [this.m_a_audio_otos_permeable_oi],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 150,
											items: [this.m_a_audio_otos_retraccion_oi],
										},
										{
											columnWidth: 0.999,
											border: false,
											labelWidth: 150,
											layout: "form",
											//labelAlign: 'top',
											items: [this.m_a_audio_otos_abomba_oi],
										},
										{
											columnWidth: 0.999,
											border: false,
											labelWidth: 150,
											layout: "form",
											//labelAlign: 'top',
											items: [this.m_a_audio_otos_serumen_oi],
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
									title: "NARIZ",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 120,
											items: [this.m_a_audio_nariz],
										},
										{
											columnWidth: 0.999,
											border: false,
											labelWidth: 120,
											layout: "form",
											//labelAlign: 'top',
											items: [this.m_a_audio_nariz_esp],
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
									title: "OROFARINGE",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 120,
											items: [this.m_a_audio_orofaringe],
										},
										{
											columnWidth: 0.999,
											border: false,
											labelWidth: 120,
											layout: "form",
											//labelAlign: 'top',
											items: [this.m_a_audio_orofaringe_esp],
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
									title: "OIDO",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 120,
											items: [this.m_a_audio_oido],
										},
										{
											columnWidth: 0.999,
											border: false,
											labelWidth: 120,
											layout: "form",
											//labelAlign: 'top',
											items: [this.m_a_audio_oido_esp],
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
									title: "OTROS",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//labelAlign: 'top',
											labelWidth: 120,
											items: [this.m_a_audio_otros],
										},
										{
											columnWidth: 0.999,
											border: false,
											labelWidth: 120,
											layout: "form",
											//labelAlign: 'top',
											items: [this.m_a_audio_otros_esp],
										},
									],
								},
							],
						},
					],
				},
				{
					title: "<b>--->  AUDIOMETRIA VIA AEREA - DIAGNOSTICOS</b>",
					iconCls: "demo2",
					layout: "column",
					autoScroll: true,
					border: false,
					bodyStyle: "padding:10px 10px 20px 10px;",
					//                    labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.7,
							// bodyStyle: "padding:2px 22px 0px 5px;",

							// border: true,
							monitorValid: true,
							autoLoad: {
								url: "./extras/audiometria.html",
								scripts: true,
							},
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.28,
							labelWidth: 35,
							bodyStyle: "padding:2px 15px 0px 22px;",
							items: [
								{
									xtype: "fieldset",
									title: "VIA AEREA",
									items: [
										{
											xtype: "compositefield",
											items: [
												{
													xtype: "displayfield",
													value: "<center><b>OIDO DERECHO</b></center>",
													width: 87,
												},
												{
													xtype: "displayfield",
													value: "<center><b>OIDO IZQUIERDO</b></center>",
													width: 15,
												},
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>125</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_aereo_125_od,
												this.m_a_audio_aereo_125_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>250</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_aereo_250_od,
												this.m_a_audio_aereo_250_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>500</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_aereo_500_od,
												this.m_a_audio_aereo_500_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>1000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_aereo_1000_od,
												this.m_a_audio_aereo_1000_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>2000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_aereo_2000_od,
												this.m_a_audio_aereo_2000_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>3000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_aereo_3000_od,
												this.m_a_audio_aereo_3000_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>4000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_aereo_4000_od,
												this.m_a_audio_aereo_4000_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>6000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_aereo_6000_od,
												this.m_a_audio_aereo_6000_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>8000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_aereo_8000_od,
												this.m_a_audio_aereo_8000_oi,
											],
										},
									],
								}, //audiograma
								{
									columnWidth: 0.28,
									border: false,
									layout: "form",
									labelAlign: "top",
									//labelWidth: 120,
									items: [this.audiograma_aereo],
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
									title: "DIAGNOSTICO AEREA",
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											labelAlign: "top",
											//labelWidth: 120,
											items: [this.m_a_audio_diag_aereo_od],
										},
										{
											columnWidth: 0.999,
											border: false,
											//labelWidth: 120,
											layout: "form",
											labelAlign: "top",
											items: [this.m_a_audio_diag_aereo_oi],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 22px 0px 2px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "--",
									items: [
										{
											columnWidth: 1,
											border: false,
											layout: "form",
											labelAlign: "top",
											//labelWidth: 120,
											items: [this.m_a_audio_kclokhoff],
										},
										{
											columnWidth: 1,
											border: false,
											//labelWidth: 120,
											layout: "form",
											labelAlign: "top",
											items: [this.m_a_audio_comentarios],
										},
									],
								},
							],
						},
					],
				},

				{
					title: "<b>--->  AUDIOMETRIA VIA OSEO - DIAGNOSTICOS</b>",
					iconCls: "demo2",
					layout: "column",
					autoScroll: true,
					border: false,
					bodyStyle: "padding:10px 10px 20px 10px;",
					//                    labelWidth: 60,
					items: [
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.7,
							monitorValid: true,
							autoLoad: {
								url: "./extras/audio_oseo.html",
								scripts: true,
							},
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.28,
							labelWidth: 35,
							bodyStyle: "padding:2px 15px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									title: "VIA OSEO",
									items: [
										{
											xtype: "compositefield",
											items: [
												{
													xtype: "displayfield",
													value: "<center><b>OIDO DERECHO</b></center>",
													width: 87,
												},
												{
													xtype: "displayfield",
													value: "<center><b>OIDO IZQUIERDO</b></center>",
													width: 15,
												},
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>125</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_oseo_125_od,
												this.m_a_audio_oseo_125_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>250</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_oseo_250_od,
												this.m_a_audio_oseo_250_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>500</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_oseo_500_od,
												this.m_a_audio_oseo_500_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>1000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_oseo_1000_od,
												this.m_a_audio_oseo_1000_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>2000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_oseo_2000_od,
												this.m_a_audio_oseo_2000_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>3000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_oseo_3000_od,
												this.m_a_audio_oseo_3000_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>4000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_oseo_4000_od,
												this.m_a_audio_oseo_4000_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>6000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_oseo_6000_od,
												this.m_a_audio_oseo_6000_oi,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>8000</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_a_audio_oseo_8000_od,
												this.m_a_audio_oseo_8000_oi,
											],
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
									title: "DIAGNOSTICO OSEO",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelAlign: "top",
											//labelWidth: 120,
											items: [this.m_a_audio_diag_osteo_od],
										},
										{
											columnWidth: 0.5,
											border: false,
											//labelWidth: 120,
											layout: "form",
											labelAlign: "top",
											items: [this.m_a_audio_diag_osteo_oi],
										},
									],
								},
								{
									columnWidth: 0.28,
									border: false,
									layout: "form",
									labelAlign: "top",
									//labelWidth: 120,
									items: [this.audiograma_oseo],
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
						mod.audio.audio_audio.win.el.mask("Guardando…", "x-mask-loading");
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_audio_audio"
										: "save_audio_audio",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								if (action.result.success === true) {
									if (action.result.total === 1) {
										mod.audio.formatos.st.reload();
										mod.audio.st.reload();
										mod.audio.audio_audio.win.el.unmask();
										mod.audio.audio_audio.win.close();
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
								mod.audio.audio_audio.win.el.unmask();
								mod.audio.audio_audio.win.close();
								mod.audio.formatos.st.reload();
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
			width: 1100,
			height: 600,
			border: false,
			modal: true,
			title: "EXAMEN AUDIOMETRIA: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

Ext.onReady(mod.audio.init, mod.audio);
