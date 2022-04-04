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

Ext.ns("mod.psicologia");
mod.psicologia = {
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
					this.baseParams.columna = mod.psicologia.descripcion.getValue();
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
						mod.psicologia.formatos.init(record);
					} else {
						mod.psicologia.formatos.init(record);
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
		if (this.record.get("adm_foto") == 1) {
			mod.psicologia.formatos.imgStore.removeAll();
			var store = mod.psicologia.formatos.imgStore;
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
					this.baseParams.adm = mod.psicologia.formatos.record.get("adm");
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
					if (record.get("ex_id") == 58) {
						//PSICOLOGIA - LAS BAMBAS
						mod.psicologia.inform_psicologia.init(record);
					} else if (record.get("ex_id") == 7) {
						//examen psicologia
						mod.psicologia.examen_psicologia.init(record);
					} else if (record.get("ex_id") == 59) {
						//examen ALTURA
						mod.psicologia.psicologia_altura.init(record);
					} else if (record.get("ex_id") == 69) {
						//EXAMEN PARA ESPACIOS CONFINADOS
						mod.psicologia.psico_confinados.init(record);
					} else {
						mod.psicologia.examenPRE.init(record); //
					}
					//                    mod.psicologia.examenPRE.init(record);//
				},
				rowcontextmenu: function (grid, index, event) {
					event.stopEvent();
					var record = grid.getStore().getAt(index);
					if (record.get("st") == "1") {
						if (record.get("ex_id") == 58) {
							//PSICOLOGIA - LAS BAMBAS
							new Ext.menu.Menu({
								items: [
									{
										text:
											"INFORME PSICOLOGICO N°: <B>" + record.get("adm") + "<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title: "INFORME PSICOLOGICO N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_psicologia&sys_report=formato_psico_informe&adm=" +
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
						} else if (record.get("ex_id") == 7) {
							//PSICOLOGIA - LAS BAMBAS
							new Ext.menu.Menu({
								items: [
									{
										text:
											"EXAMEN PSICOLOGICO N°: <B>" + record.get("adm") + "<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title: "EXAMEN PSICOLOGICO N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_psicologia&sys_report=formato_psico_examen&adm=" +
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
						} else if (record.get("ex_id") == 59) {
							//PSICOLOGIA - LAS BAMBAS
							new Ext.menu.Menu({
								items: [
									{
										text:
											"EXAMEN PSICOLOGICO N°: <B>" + record.get("adm") + "<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title: "EXAMEN PSICOLOGICO N° " + record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_psicologia&sys_report=formato_altura180&adm=" +
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
						} else if (record.get("ex_id") == 69) {
							//PSICOLOGIA - LAS BAMBAS
							new Ext.menu.Menu({
								items: [
									{
										text:
											"EXAMEN PARA ESPACIOS CONFINADOS N°: <B>" +
											record.get("adm") +
											"<B>",
										iconCls: "reporte",
										handler: function () {
											if (record.get("st") >= 1) {
												new Ext.Window({
													title:
														"EXAMEN OCUPACIONAL PARA ESPACIOS CONFINADOS N° " +
														record.get("adm"),
													width: 800,
													height: 600,
													maximizable: true,
													modal: true,
													closeAction: "close",
													resizable: true,
													html:
														"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_psicologia&sys_report=formato_altura180&adm=" +
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
					/////////////////////////////////////////////
					/////////////////////////////////////////////
					/////////////////////////////////////////////
					new Ext.menu.Menu({
						items: [
							{
								text:
									"EXAMEN PARA ESPACIOS CONFINADOS N°: <B>" +
									record.get("adm") +
									"<B>",
								iconCls: "reporte",
								handler: function () {
									new Ext.Window({
										title:
											"EXAMEN OCUPACIONAL PARA ESPACIOS CONFINADOS N° " +
											record.get("adm"),
										width: 800,
										height: 600,
										maximizable: true,
										modal: true,
										closeAction: "close",
										resizable: true,
										html:
											"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_psicologia&sys_report=formato_esp_confinados&adm=" +
											record.get("adm") +
											"'></iframe>",
									}).show();
								},
							},
						],
					}).showAt(event.xy);
				},
				/////////////////////////////////////////////
				/////////////////////////////////////////////
				/////////////////////////////////////////////
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
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_examenLab",
				format: "json",
				adm: mod.psicologia.examenPRE.record.get("adm"),
				examen: mod.psicologia.examenPRE.record.get("ex_id"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.psicologia.anexo_16a.val_medico.setValue(r.val_medico);
				//                mod.psicologia.anexo_16a.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_controles: function () {
		this.m_lab_exam_resultado = new Ext.form.TextField({
			fieldLabel: "<b>RESULTADO DEL EXAMEN</b>",
			allowBlank: false,
			name: "m_lab_exam_resultado",
			anchor: "96%",
		});
		this.m_lab_exam_observaciones = new Ext.form.TextArea({
			fieldLabel: "<b>OBSERVACIONES</b>",
			name: "m_lab_exam_observaciones",
			anchor: "99%",
			height: 40,
		});
		this.m_lab_exam_diagnostico = new Ext.form.TextArea({
			fieldLabel: "<b>DIAGNOSTICO</b>",
			name: "m_lab_exam_diagnostico",
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
					items: [this.m_lab_exam_resultado],
				},
				{
					columnWidth: 0.99,
					border: false,
					layout: "form",
					items: [this.m_lab_exam_observaciones],
				},
				{
					columnWidth: 0.99,
					border: false,
					layout: "form",
					items: [this.m_lab_exam_diagnostico],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.psicologia.examenPRE.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1 ? "update_exaLab" : "save_exaLab",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
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

mod.psicologia.inform_psicologia = {
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
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_psico_informe",
				format: "json",
				adm: mod.psicologia.inform_psicologia.record.get("adm"),
				//                ,examen: mod.psicologia.inform_psicologia.record.get('ex_id')
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.psicologia.inform_psicologia.val_medico.setValue(r.val_medico);
				//                mod.psicologia.inform_psicologia.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_stores: function () {},
	crea_controles: function () {
		//m_psico_inf_capac_intelectual
		this.m_psico_inf_capac_intelectual = new Ext.form.RadioGroup({
			fieldLabel: "<b>CAPACIDAD INTELECTUAL</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "BAJO",
					name: "m_psico_inf_capac_intelectual",
					inputValue: "BAJO",
				},
				{
					boxLabel: "MEDIO",
					name: "m_psico_inf_capac_intelectual",
					inputValue: "MEDIO",
				},
				{
					boxLabel: "ALTO",
					name: "m_psico_inf_capac_intelectual",
					inputValue: "ALTO",
				},
			],
		});
		//m_psico_inf_aten_concentracion
		this.m_psico_inf_aten_concentracion = new Ext.form.RadioGroup({
			fieldLabel: "<b>ATENCIÓN Y CONCENTRACION</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "BAJO",
					name: "m_psico_inf_aten_concentracion",
					inputValue: "BAJO",
				},
				{
					boxLabel: "MEDIO",
					name: "m_psico_inf_aten_concentracion",
					inputValue: "MEDIO",
				},
				{
					boxLabel: "ALTO",
					name: "m_psico_inf_aten_concentracion",
					inputValue: "ALTO",
				},
			],
		});
		//m_psico_inf_orient_espacial
		this.m_psico_inf_orient_espacial = new Ext.form.RadioGroup({
			fieldLabel: "<b>CONCENTRACION ESPACIAL</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "BAJO",
					name: "m_psico_inf_orient_espacial",
					inputValue: "BAJO",
				},
				{
					boxLabel: "MEDIO",
					name: "m_psico_inf_orient_espacial",
					inputValue: "MEDIO",
				},
				{
					boxLabel: "ALTO",
					name: "m_psico_inf_orient_espacial",
					inputValue: "ALTO",
				},
			],
		});
		//m_psico_inf_pers_htp
		this.m_psico_inf_pers_htp = new Ext.form.TextField({
			fieldLabel: "<b>PERSONALIDAD HTP</b>",
			name: "m_psico_inf_pers_htp",
			anchor: "95%",
		});
		//m_psico_inf_pers_salamanca
		this.m_psico_inf_pers_salamanca = new Ext.form.TextField({
			fieldLabel: "<b>PERSONALIDAD SALAMANCA</b>",
			name: "m_psico_inf_pers_salamanca",
			anchor: "95%",
		});
		//m_psico_inf_intel_emocional
		this.m_psico_inf_intel_emocional = new Ext.form.TextField({
			fieldLabel: "<b>INTELIGENCIA EMOCIONAL</b>",
			name: "m_psico_inf_intel_emocional",
			anchor: "95%",
		});
		//m_psico_inf_caracterologia
		this.m_psico_inf_caracterologia = new Ext.form.TextField({
			fieldLabel: "<b>CARACTEROLOGIA</b>",
			name: "m_psico_inf_caracterologia",
			anchor: "95%",
		});
		//m_psico_inf_alturas
		this.m_psico_inf_alturas = new Ext.form.TextField({
			fieldLabel: "<b>ALTURAS</b>",
			name: "m_psico_inf_alturas",
			anchor: "95%",
		});
		//m_psico_inf_esp_confinados
		this.m_psico_inf_esp_confinados = new Ext.form.TextField({
			fieldLabel: "<b>ESPACIOS CONFINADOS</b>",
			name: "m_psico_inf_esp_confinados",
			anchor: "95%",
		});
		//m_psico_inf_otros
		this.m_psico_inf_otros = new Ext.form.TextField({
			fieldLabel: "<b>OTROS</b>",
			name: "m_psico_inf_otros",
			anchor: "95%",
		});
		//m_psico_inf_precis_destre_reac
		this.m_psico_inf_precis_destre_reac = new Ext.form.TextField({
			fieldLabel: "<b>PRECISION, DESTREZA, REACCION</b>",
			name: "m_psico_inf_precis_destre_reac",
			anchor: "95%",
		});
		//m_psico_inf_antici_bim_mono
		this.m_psico_inf_antici_bim_mono = new Ext.form.TextField({
			fieldLabel: "<b>ANTICIPACION, BIMANUAL, MONOTONIA</b>",
			name: "m_psico_inf_antici_bim_mono",
			anchor: "95%",
		});
		//m_psico_inf_actitud_f_trans
		this.m_psico_inf_actitud_f_trans = new Ext.form.TextField({
			fieldLabel: "<b>ACTITUD FRENTE AL TRANSITO</b>",
			name: "m_psico_inf_actitud_f_trans",
			anchor: "95%",
		});
		//m_psico_inf_resultados
		this.m_psico_inf_resultados = new Ext.form.TextArea({
			name: "m_psico_inf_resultados",
			fieldLabel: "<b>FORTALEZAS</b>",
			anchor: "99%",
			height: 80,
		});
		//m_psico_inf_debilidades
		this.m_psico_inf_debilidades = new Ext.form.TextArea({
			name: "m_psico_inf_debilidades",
			fieldLabel: "<b>DEBILIDADES</b>",
			anchor: "99%",
			value: "NINGUNA",
			height: 70,
		});
		//m_psico_inf_conclusiones
		this.m_psico_inf_conclusiones = new Ext.form.TextArea({
			name: "m_psico_inf_conclusiones",
			fieldLabel: "<b>CONCLUSIONES</b>",
			anchor: "99%",
			value:
				"El paciente " +
				mod.psicologia.formatos.record.get("nombre") +
				"; se encuentra APTO para laborar.",
			height: 70,
		});
		//m_psico_inf_recomendaciones
		this.m_psico_inf_recomendaciones = new Ext.form.TextArea({
			name: "m_psico_inf_recomendaciones",
			fieldLabel: "<b>RECOMENDACIONES</b>",
			anchor: "99%",
			value: "NINGUNA",
			height: 160,
		});
		//m_psico_inf_puesto_trabajo
		this.m_psico_inf_puesto_trabajo = new Ext.form.RadioGroup({
			fieldLabel: "<b>PUESTO DE TRABAJO</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "APTO",
					name: "m_psico_inf_puesto_trabajo",
					inputValue: "APTO",
					checked: true,
				},
				{
					boxLabel: "OBSERVADO",
					name: "m_psico_inf_puesto_trabajo",
					inputValue: "OBSERVADO",
				},
				{
					boxLabel: "NO APTO",
					name: "m_psico_inf_puesto_trabajo",
					inputValue: "NO APTO",
				},
			],
		});
		//m_psico_inf_brigadista
		this.m_psico_inf_brigadista = new Ext.form.RadioGroup({
			fieldLabel: "<b>BRIGADISTA</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "APTO",
					name: "m_psico_inf_brigadista",
					inputValue: "APTO",
				},
				{
					boxLabel: "OBSERVADO",
					name: "m_psico_inf_brigadista",
					inputValue: "OBSERVADO",
				},
				{
					boxLabel: "NO APLICA",
					name: "m_psico_inf_brigadista",
					inputValue: "NO APLICA",
					checked: true,
				},
			],
		});
		//m_psico_inf_conduc_equip_liviano
		this.m_psico_inf_conduc_equip_liviano = new Ext.form.RadioGroup({
			fieldLabel: "<b>CONDUCCION DE EQUIPO LIVIANO</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "APTO",
					name: "m_psico_inf_conduc_equip_liviano",
					inputValue: "APTO",
				},
				{
					boxLabel: "NO APTO",
					name: "m_psico_inf_conduc_equip_liviano",
					inputValue: "NO APTO",
				},
				{
					boxLabel: "NO APLICA",
					name: "m_psico_inf_conduc_equip_liviano",
					inputValue: "NO APLICA",
					checked: true,
				},
			],
		});
		//m_psico_inf_conduc_equip_pesado
		this.m_psico_inf_conduc_equip_pesado = new Ext.form.RadioGroup({
			fieldLabel: "<b>CONDUCCION DE EQUIPO PESADO</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "APTO",
					name: "m_psico_inf_conduc_equip_pesado",
					inputValue: "APTO",
				},
				{
					boxLabel: "NO APTO",
					name: "m_psico_inf_conduc_equip_pesado",
					inputValue: "NO APTO",
				},
				{
					boxLabel: "NO APLICA",
					name: "m_psico_inf_conduc_equip_pesado",
					inputValue: "NO APLICA",
					checked: true,
				},
			],
		});
		//m_psico_inf_trabajo_altura
		this.m_psico_inf_trabajo_altura = new Ext.form.RadioGroup({
			fieldLabel: "<b>TRABAJO EN ALTURA A +180 mtrs</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "APTO",
					name: "m_psico_inf_trabajo_altura",
					inputValue: "APTO",
				},
				{
					boxLabel: "NO APTO",
					name: "m_psico_inf_trabajo_altura",
					inputValue: "NO APTO",
				},
				{
					boxLabel: "NO APLICA",
					name: "m_psico_inf_trabajo_altura",
					inputValue: "NO APLICA",
					checked: true,
				},
			],
		});
		//m_psico_inf_trab_esp_confinado
		this.m_psico_inf_trab_esp_confinado = new Ext.form.RadioGroup({
			fieldLabel: "<b>TRABAJO EN ESPACIO CONFINADO</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "APTO",
					name: "m_psico_inf_trab_esp_confinado",
					inputValue: "APTO",
				},
				{
					boxLabel: "NO APTO",
					name: "m_psico_inf_trab_esp_confinado",
					inputValue: "NO APTO",
				},
				{
					boxLabel: "NO APLICA",
					name: "m_psico_inf_trab_esp_confinado",
					inputValue: "NO APLICA",
					checked: true,
				},
			],
		});

		//m_psico_inf_grieger
		this.m_psico_inf_grieger = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["-", "-"],
					["NERVIOSO", "NERVIOSO"],
					["SENTIMENTAL", "SENTIMENTAL"],
					["COLERICO", "COLERICO"],
					["PASIONAL", "PASIONAL"],
					["SANGUINEO", "SANGUINEO"],
					["FLEMATICO", "FLEMATICO"],
					["AMORFO", "AMORFO"],
					["APATICO", "APATICO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_inf_grieger",
			fieldLabel: "<b>GRIEGER</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("-");
					descripcion.setRawValue("-");
				},
			},
		});
		//m_psico_inf_htp
		this.m_psico_inf_htp = new Ext.form.RadioGroup({
			fieldLabel: "<b>HTP</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{ boxLabel: "ESTABLE", name: "m_psico_inf_htp", inputValue: "ESTABLE" },
				{
					boxLabel: "INESTABLE",
					name: "m_psico_inf_htp",
					inputValue: "INESTABLE",
				},
			],
		});
		//m_psico_inf_raven
		this.m_psico_inf_raven = new Ext.form.RadioGroup({
			fieldLabel: "<b>RAVEN</b>",
			itemCls: "x-check-group-alt",
			columns: 4,
			items: [
				{ boxLabel: "ALTO", name: "m_psico_inf_raven", inputValue: "ALTO" },
				{
					boxLabel: "PROMEDIO",
					name: "m_psico_inf_raven",
					inputValue: "PROMEDIO",
				},
				{ boxLabel: "BAJO", name: "m_psico_inf_raven", inputValue: "BAJO" },
			],
		});
		//m_psico_inf_laberinto
		this.m_psico_inf_laberinto = new Ext.form.RadioGroup({
			fieldLabel: "<b>LABERINTO</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{ boxLabel: "ALTO", name: "m_psico_inf_laberinto", inputValue: "ALTO" },
				{
					boxLabel: "MEDIO",
					name: "m_psico_inf_laberinto",
					inputValue: "MEDIO",
				},
				{ boxLabel: "BAJO", name: "m_psico_inf_laberinto", inputValue: "BAJO" },
			],
		});
		//m_psico_inf_bender
		this.m_psico_inf_bender = new Ext.form.RadioGroup({
			fieldLabel: "<b>BENDER</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{ boxLabel: "ALTO", name: "m_psico_inf_bender", inputValue: "ALTO" },
				{ boxLabel: "MEDIO", name: "m_psico_inf_bender", inputValue: "MEDIO" },
				{ boxLabel: "BAJO", name: "m_psico_inf_bender", inputValue: "BAJO" },
			],
		});
		//m_psico_inf_bc4
		this.m_psico_inf_bc4 = new Ext.form.RadioGroup({
			fieldLabel: "<b>BC4</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "CONDUCTA SEGURA",
					name: "m_psico_inf_bc4",
					inputValue: "CONDUCTA SEGURA",
				},
				{
					boxLabel: "CONDUCTA INSEGURA",
					name: "m_psico_inf_bc4",
					inputValue: "CONDUCTA INSEGURA",
				},
			],
		});
		//m_psico_inf_precision
		this.m_psico_inf_precision = new Ext.form.RadioGroup({
			fieldLabel: "<b>PRECISION</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{ boxLabel: "ALTO", name: "m_psico_inf_precision", inputValue: "ALTO" },
				{
					boxLabel: "MEDIO",
					name: "m_psico_inf_precision",
					inputValue: "MEDIO",
				},
				{ boxLabel: "BAJO", name: "m_psico_inf_precision", inputValue: "BAJO" },
			],
		});
		//m_psico_inf_destreza
		this.m_psico_inf_destreza = new Ext.form.RadioGroup({
			fieldLabel: "<b>DESTREZA</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{ boxLabel: "ALTO", name: "m_psico_inf_destreza", inputValue: "ALTO" },
				{
					boxLabel: "MEDIO",
					name: "m_psico_inf_destreza",
					inputValue: "MEDIO",
				},
				{ boxLabel: "BAJO", name: "m_psico_inf_destreza", inputValue: "BAJO" },
			],
		});
		//m_psico_inf_reaccion
		this.m_psico_inf_reaccion = new Ext.form.RadioGroup({
			fieldLabel: "<b>REACCION</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{ boxLabel: "ALTO", name: "m_psico_inf_reaccion", inputValue: "ALTO" },
				{
					boxLabel: "MEDIO",
					name: "m_psico_inf_reaccion",
					inputValue: "MEDIO",
				},
				{ boxLabel: "BAJO", name: "m_psico_inf_reaccion", inputValue: "BAJO" },
			],
		});
		//m_psico_inf_toulous
		this.m_psico_inf_toulous = new Ext.form.RadioGroup({
			fieldLabel: "<b>TOULOUSE</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{ boxLabel: "ALTO", name: "m_psico_inf_toulous", inputValue: "ALTO" },
				{ boxLabel: "MEDIO", name: "m_psico_inf_toulous", inputValue: "MEDIO" },
				{ boxLabel: "BAJO", name: "m_psico_inf_toulous", inputValue: "BAJO" },
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
						"<b>--->  COMPETENCIAS PSICOLOGICAS- RESULTADOS - DEBILIDADES - CONCLUSIONES - RECOMENDACIONES - APTITUD</b>",
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
							columnWidth: 0.67,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "PERSONALIDAD",
									labelWidth: 80,
									items: [
										{
											columnWidth: 0.4,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_grieger],
										},
										{
											columnWidth: 0.6,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_htp],
										},
									],
								},
							],
						},

						{
							xtype: "panel",
							border: false,
							columnWidth: 0.33,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "ORGANICIDAD",
									labelWidth: 80,
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_bender],
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
									title: "PSICOTECNICO",
									labelWidth: 90,
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_precision],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_destreza],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_reaccion],
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
									title: "INTELIGENCIA",
									labelWidth: 90,
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_raven],
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
									title: "MOTRICIDAD",
									labelWidth: 90,
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_laberinto],
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
									title: "SEGURIDAD",
									labelWidth: 50,
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_bc4],
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
									title: "ATENCION - CONCENTRACION",
									labelWidth: 90,
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_toulous],
										},
									],
								},
							],
						},
						//SOLO CORRECCIONES DR JUAN DONGO
						//SOLO CORRECCIONES DR JUAN DONGO
						//SOLO CORRECCIONES DR JUAN DONGO
						//                        , {
						//                            xtype: 'panel', border: false,
						//                            columnWidth: .50,
						//                            bodyStyle: 'padding:2px 22px 0px 5px;',
						//                            items: [{
						//                                    xtype: 'fieldset', layout: 'column',
						//                                    title: 'COGNITIVAS',
						//                                    labelWidth: 150,
						//                                    items: [{
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_capac_intelectual]
						//                                        }, {
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_aten_concentracion]
						//                                        }, {
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_orient_espacial]
						//                                        }]
						//                                }, {
						//                                    xtype: 'fieldset', layout: 'column',
						//                                    title: 'TEMORES',
						//                                    labelWidth: 150,
						//                                    items: [{
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_alturas]
						//                                        }, {
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_esp_confinados]
						//                                        }, {
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_otros]
						//                                        }]
						//                                }, {
						//                                    columnWidth: .999,
						//                                    border: false,
						//                                    layout: 'form',
						//                                    labelAlign: 'top',
						//                                    items: [this.m_psico_inf_resultados]
						//                                }]
						//                        }, {
						//                            xtype: 'panel', border: false,
						//                            columnWidth: .50,
						//                            bodyStyle: 'padding:2px 22px 0px 5px;',
						//                            items: [{
						//                                    xtype: 'fieldset', layout: 'column',
						//                                    title: 'AFECTIVAS',
						//                                    labelWidth: 150,
						//                                    items: [{
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_pers_htp]
						//                                        }, {
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_pers_salamanca]
						//                                        }, {
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_intel_emocional]
						//                                        }, {
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_caracterologia]
						//                                        }]
						//                                }, {
						//                                    xtype: 'fieldset', layout: 'column',
						//                                    title: 'PSICOTECNICO',
						//                                    labelWidth: 150,
						//                                    items: [{
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_precis_destre_reac]
						//                                        }, {
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_antici_bim_mono]
						//                                        }]
						//                                }, {
						//                                    xtype: 'fieldset', layout: 'column',
						//                                    title: 'TIPO DE CONDUCTA',
						//                                    labelWidth: 150,
						//                                    items: [{
						//                                            columnWidth: .999,
						//                                            border: false,
						//                                            layout: 'form',
						////                                            labelAlign: 'top',
						//                                            items: [this.m_psico_inf_actitud_f_trans]
						//                                        }]
						//                                }]
						//                        }
						//SOLO CORRECCIONES DR JUAN DONGO
						//SOLO CORRECCIONES DR JUAN DONGO
						//SOLO CORRECCIONES DR JUAN DONGO
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.5,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									columnWidth: 0.999,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_psico_inf_debilidades],
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
									columnWidth: 0.999,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_psico_inf_conclusiones],
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
									columnWidth: 0.999,
									border: false,
									layout: "form",
									labelAlign: "top",
									items: [this.m_psico_inf_recomendaciones],
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
									title: "COGNITIVAS",
									labelWidth: 220,
									items: [
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_puesto_trabajo],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_brigadista],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_conduc_equip_liviano],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_conduc_equip_pesado],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_trabajo_altura],
										},
										{
											columnWidth: 0.999,
											border: false,
											layout: "form",
											//                                            labelAlign: 'top',
											items: [this.m_psico_inf_trab_esp_confinado],
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
						mod.psicologia.inform_psicologia.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_psico_informe"
										: "save_psico_informe",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								if (action.result.success === true) {
									if (action.result.total === 1) {
										//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
										mod.psicologia.formatos.st.reload();
										mod.psicologia.st.reload();
										mod.psicologia.inform_psicologia.win.el.unmask();
										mod.psicologia.inform_psicologia.win.close();
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
								mod.psicologia.inform_psicologia.win.el.unmask();
								mod.psicologia.inform_psicologia.win.close();
								mod.psicologia.formatos.st.reload();
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
			title: "EXAMEN PSICOLOGICO: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.psicologia.examen_psicologia = {
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
		this.frm.getForm().load({
			waitMsg: "Recuperando Informacion...",
			waitTitle: "Espere",
			params: {
				acction: "load_psico_examen",
				format: "json",
				adm: mod.psicologia.examen_psicologia.record.get("adm"),
				//                ,examen: mod.psicologia.examen_psicologia.record.get('ex_id')
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.psicologia.examen_psicologia.val_medico.setValue(r.val_medico);
				//                mod.psicologia.examen_psicologia.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_stores: function () {},
	crea_controles: function () {
		this.empresa = new Ext.form.TextField({
			fieldLabel: "<b>EMPRESA</b>",
			name: "empresa",
			value: mod.psicologia.formatos.record.get("emp_desc"),
			anchor: "95%",
		});
		//m_psico_exam_activ_empresa
		this.m_psico_exam_activ_empresa = new Ext.form.TextField({
			fieldLabel: "<b>ACTIVIDAD DE LA EMPRESA</b>",
			name: "m_psico_exam_activ_empresa",
			anchor: "95%",
		});
		//m_psico_exam_area_trabajo
		this.m_psico_exam_area_trabajo = new Ext.form.TextField({
			fieldLabel: "<b>AREA DE TRABAJO</b>",
			name: "m_psico_exam_area_trabajo",
			anchor: "95%",
		});
		//m_psico_exam_tiempo_labor
		this.m_psico_exam_tiempo_labor = new Ext.form.TextField({
			fieldLabel: "<b>TIEMPO DE TRABAJO</b>",
			name: "m_psico_exam_tiempo_labor",
			anchor: "95%",
		});
		//m_psico_exam_puesto
		this.m_psico_exam_puesto = new Ext.form.TextField({
			fieldLabel: "<b>PUESTO DE TRABAJO</b>",
			name: "m_psico_exam_puesto",
			anchor: "95%",
		});
		//m_psico_exam_princ_riesgos
		this.m_psico_exam_princ_riesgos = new Ext.form.TextField({
			fieldLabel: "<b>PRINCIPALES RIESGOS</b>",
			name: "m_psico_exam_princ_riesgos",
			value: "Accidentes en zonas de trabajo",
			anchor: "95%",
		});
		//        this.m_psico_exam_princ_riesgos = new Ext.form.TextArea({
		//            name: 'm_psico_exam_princ_riesgos',
		//            fieldLabel: '<b>PRINCIPALES RIESGOS</b>',
		//            anchor: '99%',
		//            value: 'El paciente ' + mod.psicologia.formatos.record.get('nombre') + '; se encuentra APTO para laborar.',
		//            height: 70
		//        });
		//m_psico_exam_medi_seguridad
		this.m_psico_exam_medi_seguridad = new Ext.form.TextField({
			fieldLabel: "<b>MEDIDAS DE SEGURIDAD</b>",
			name: "m_psico_exam_medi_seguridad",
			value: "Reglamento, charlas y equipos de seguridad.",
			anchor: "95%",
		});
		//m_psico_exam_histo_familiar
		this.m_psico_exam_histo_familiar = new Ext.form.TextArea({
			name: "m_psico_exam_histo_familiar",
			fieldLabel: "<b>HISTORIA FAMILIAR</b>",
			anchor: "99%",
			height: 70,
		});
		//m_psico_exam_accid_enfermedad
		this.m_psico_exam_accid_enfermedad = new Ext.form.TextArea({
			name: "m_psico_exam_accid_enfermedad",
			fieldLabel:
				"<b>ACIDENTES Y ENFERMEDADES(DURANTE EL TIEMPO DEL TRABAJO)</b>",
			anchor: "99%",
			value: "NINGUNA",
			height: 70,
		});
		//m_psico_exam_habitos
		this.m_psico_exam_habitos = new Ext.form.TextArea({
			name: "m_psico_exam_habitos",
			fieldLabel:
				"<b>HABITOS (PASATIEMPOS, CONSUMO DE TABACO, ALCOHOL DROGAS)</b>",
			anchor: "99%",
			value: "Tabaco no. Alcohol no. Drogas no.",
			height: 70,
		});
		//m_psico_exam_otras_obs
		this.m_psico_exam_otras_obs = new Ext.form.TextArea({
			name: "m_psico_exam_otras_obs",
			fieldLabel: "<b>OTRAS OBSERVACIONES</b>",
			anchor: "99%",
			value: "NINGUNA",
			height: 70,
		});
		//m_psico_exam_presentacion
		this.m_psico_exam_presentacion = new Ext.form.RadioGroup({
			fieldLabel: "<b>PRESENTACIÓN</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ADECUADO",
					name: "m_psico_exam_presentacion",
					inputValue: "ADECUADO",
					checked: true,
				},
				{
					boxLabel: "INADECUADO",
					name: "m_psico_exam_presentacion",
					inputValue: "INADECUADO",
				},
			],
		});
		//m_psico_exam_postura
		this.m_psico_exam_postura = new Ext.form.RadioGroup({
			fieldLabel: "<b>POSTURA</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ERGUIDA",
					name: "m_psico_exam_postura",
					inputValue: "ERGUIDA",
					checked: true,
				},
				{
					boxLabel: "ENCORVADA",
					name: "m_psico_exam_postura",
					inputValue: "ENCORVADA",
				},
			],
		});
		//m_psico_exam_ritmo
		this.m_psico_exam_ritmo = new Ext.form.RadioGroup({
			fieldLabel: "<b>RITMO</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{ boxLabel: "LENTO", name: "m_psico_exam_ritmo", inputValue: "LENTO" },
				{
					boxLabel: "RÁPIDO",
					name: "m_psico_exam_ritmo",
					inputValue: "RÁPIDO",
				},
				{
					boxLabel: "FLUIDO",
					name: "m_psico_exam_ritmo",
					inputValue: "FLUIDO",
					checked: true,
				},
			],
		});
		//m_psico_exam_tono
		this.m_psico_exam_tono = new Ext.form.RadioGroup({
			fieldLabel: "<b>TONO</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{ boxLabel: "BAJO", name: "m_psico_exam_tono", inputValue: "BAJO" },
				{
					boxLabel: "MODERADO",
					name: "m_psico_exam_tono",
					inputValue: "MODERADO",
					checked: true,
				},
				{ boxLabel: "ALTO", name: "m_psico_exam_tono", inputValue: "ALTO" },
			],
		});
		//m_psico_exam_articulacion
		this.m_psico_exam_articulacion = new Ext.form.RadioGroup({
			fieldLabel: "<b>ARTICULACION</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "CON DIFICULTAD",
					name: "m_psico_exam_articulacion",
					inputValue: "CON DIFICULTAD",
				},
				{
					boxLabel: "SIN DIFICULTAD",
					name: "m_psico_exam_articulacion",
					inputValue: "SIN DIFICULTAD",
					checked: true,
				},
			],
		});
		//m_psico_exam_tiempo
		this.m_psico_exam_tiempo = new Ext.form.RadioGroup({
			fieldLabel: "<b>TIEMPO</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ORIENTADO",
					name: "m_psico_exam_tiempo",
					inputValue: "ORIENTADO",
					checked: true,
				},
				{
					boxLabel: "DESORIENTADO",
					name: "m_psico_exam_tiempo",
					inputValue: "DESORIENTADO",
				},
			],
		});
		//m_psico_exam_espacio
		this.m_psico_exam_espacio = new Ext.form.RadioGroup({
			fieldLabel: "<b>ESPACIO</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ORIENTADO",
					name: "m_psico_exam_espacio",
					inputValue: "ORIENTADO",
					checked: true,
				},
				{
					boxLabel: "DESORIENTADO",
					name: "m_psico_exam_espacio",
					inputValue: "DESORIENTADO",
				},
			],
		});
		//m_psico_exam_persona
		this.m_psico_exam_persona = new Ext.form.RadioGroup({
			fieldLabel: "<b>PERSONA</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ORIENTADO",
					name: "m_psico_exam_persona",
					inputValue: "ORIENTADO",
					checked: true,
				},
				{
					boxLabel: "DESORIENTADO",
					name: "m_psico_exam_persona",
					inputValue: "DESORIENTADO",
				},
			],
		});
		//m_psico_exam_lucido_atent
		this.m_psico_exam_lucido_atent = new Ext.form.RadioGroup({
			fieldLabel: "<b>LUCIDO, ATENTO</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ADECUADO",
					name: "m_psico_exam_lucido_atent",
					inputValue: "ADECUADO",
					checked: true,
				},
				{
					boxLabel: "INADECUADO",
					name: "m_psico_exam_lucido_atent",
					inputValue: "INADECUADO",
				},
			],
		});
		//m_psico_exam_pensamiento
		this.m_psico_exam_pensamiento = new Ext.form.RadioGroup({
			fieldLabel: "<b>PENSAMIENTO</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ADECUADO",
					name: "m_psico_exam_pensamiento",
					inputValue: "ADECUADO",
					checked: true,
				},
				{
					boxLabel: "INADECUADO",
					name: "m_psico_exam_pensamiento",
					inputValue: "INADECUADO",
				},
			],
		});
		//m_psico_exam_persepcion
		this.m_psico_exam_persepcion = new Ext.form.RadioGroup({
			fieldLabel: "<b>PERCEPCIÓN</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ADECUADO",
					name: "m_psico_exam_persepcion",
					inputValue: "ADECUADO",
					checked: true,
				},
				{
					boxLabel: "INADECUADO",
					name: "m_psico_exam_persepcion",
					inputValue: "INADECUADO",
				},
			],
		});
		//m_psico_exam_memoria
		this.m_psico_exam_memoria = new Ext.form.RadioGroup({
			fieldLabel: "<b>MEMORIA</b>",
			itemCls: "x-check-group-alt",
			columns: 3,
			items: [
				{
					boxLabel: "CORTO PLAZO",
					name: "m_psico_exam_memoria",
					inputValue: "CORTO PLAZO",
					checked: true,
				},
				{
					boxLabel: "MEDIANO PLAZO",
					name: "m_psico_exam_memoria",
					inputValue: "MEDIANO PLAZO",
				},
				{
					boxLabel: "LARGO PLAZO",
					name: "m_psico_exam_memoria",
					inputValue: "LARGO PLAZO",
				},
			],
		});
		//m_psico_exam_inteligencia
		this.m_psico_exam_inteligencia = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["MUY SUPERIOR", "MUY SUPERIOR"],
					["SUPERIOR", "SUPERIOR"],
					["NORMAL BRILLANTE", "NORMAL BRILLANTE"],
					["PROMEDIO", "PROMEDIO"],
					["N. TORPE", "N. TORPE"],
					["FRONTERIZO", "FRONTERIZO"],
					["RM LEVE", "RM LEVE"],
					["RM MODERADO", "RM MODERADO"],
					["RM SEVERO", "RM SEVERO"],
					["RM PROFUNDO", "RM PROFUNDO"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_exam_inteligencia",
			fieldLabel: "<b>INTELIGENCIA</b>",
			allowBlank: false,
			typeAhead: true,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			selectOnFocus: true,
			anchor: "95%",
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("PROMEDIO");
					descripcion.setRawValue("PROMEDIO");
				},
			},
		});
		//m_psico_exam_apetito
		this.m_psico_exam_apetito = new Ext.form.RadioGroup({
			fieldLabel: "<b>APETITO</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ADECUADO",
					name: "m_psico_exam_apetito",
					inputValue: "ADECUADO",
					checked: true,
				},
				{
					boxLabel: "INADECUADO",
					name: "m_psico_exam_apetito",
					inputValue: "INADECUADO",
				},
			],
		});
		//m_psico_exam_sueño
		this.m_psico_exam_sueno = new Ext.form.RadioGroup({
			fieldLabel: "<b>SUEÑO</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ADECUADO",
					name: "m_psico_exam_sueno",
					inputValue: "ADECUADO",
					checked: true,
				},
				{
					boxLabel: "INADECUADO",
					name: "m_psico_exam_sueno",
					inputValue: "INADECUADO",
				},
			],
		});
		//m_psico_exam_personalidad
		this.m_psico_exam_personalidad = new Ext.form.RadioGroup({
			fieldLabel: "<b>PERSONALIDAD</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ADECUADO",
					name: "m_psico_exam_personalidad",
					inputValue: "ADECUADO",
					checked: true,
				},
				{
					boxLabel: "INADECUADO",
					name: "m_psico_exam_personalidad",
					inputValue: "INADECUADO",
				},
			],
		});
		//m_psico_exam_afectividad
		this.m_psico_exam_afectividad = new Ext.form.RadioGroup({
			fieldLabel: "<b>AFECTIVIDAD</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ADECUADO",
					name: "m_psico_exam_afectividad",
					inputValue: "ADECUADO",
					checked: true,
				},
				{
					boxLabel: "INADECUADO",
					name: "m_psico_exam_afectividad",
					inputValue: "INADECUADO",
				},
			],
		});
		//m_psico_exam_conduc_sexual
		this.m_psico_exam_conduc_sexual = new Ext.form.RadioGroup({
			fieldLabel: "<b>CONDUCTA SEXUAL</b>",
			itemCls: "x-check-group-alt",
			columns: 2,
			items: [
				{
					boxLabel: "ADECUADO",
					name: "m_psico_exam_conduc_sexual",
					inputValue: "ADECUADO",
					checked: true,
				},
				{
					boxLabel: "INADECUADO",
					name: "m_psico_exam_conduc_sexual",
					inputValue: "INADECUADO",
				},
			],
		});
		//m_psico_exam_area_cognitiva
		this.m_psico_exam_area_cognitiva = new Ext.form.TextArea({
			name: "m_psico_exam_area_cognitiva",
			fieldLabel: "<b>ÁREA COGNITIVA</b>",
			anchor: "99%",
			value: "Buena",
			height: 70,
		});
		//m_psico_exam_area_emocional
		this.m_psico_exam_area_emocional = new Ext.form.TextArea({
			name: "m_psico_exam_area_emocional",
			fieldLabel: "<b>ÁREA EMOCIONAL</b>",
			anchor: "99%",
			value: "Estable",
			height: 70,
		});
		//m_psico_exam_ptje_test_01
		this.m_psico_exam_ptje_test_01 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_01",
			fieldLabel: "<b>INVENTARIO MILLON DE ESTILOS DE PERSONALIDAD - MIPS</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_02
		this.m_psico_exam_ptje_test_02 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_02",
			fieldLabel: "<b>CUESTIONARIO DE PERSONALIDAD - EYSENCK</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_03
		this.m_psico_exam_ptje_test_03 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_03",
			fieldLabel: "<b>ESCALA DE MOTIVACIONES PSICOSOCIALES - MPS</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_04
		this.m_psico_exam_ptje_test_04 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_04",
			fieldLabel: "<b>LURIA - DNA DIAGNOSTICO NEUROPSICOLOGICO DE ADULTOS</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_05
		this.m_psico_exam_ptje_test_05 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_05",
			fieldLabel: "<b>ESCALA DE APRECIACION DEL ESTRES - EAE</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_06
		this.m_psico_exam_ptje_test_06 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_06",
			fieldLabel: "<b>INVENTARIO DE BURNOUT DE MASIACH</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_07
		this.m_psico_exam_ptje_test_07 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_07",
			fieldLabel: "<b>CLIMA LABORAL</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_08
		this.m_psico_exam_ptje_test_08 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_08",
			fieldLabel: "<b>BATERIA DE CONDUCTORES</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_09
		this.m_psico_exam_ptje_test_09 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_09",
			fieldLabel: "<b>WAIS</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_10
		this.m_psico_exam_ptje_test_10 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_10",
			fieldLabel: "<b>TEST BENTON</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_11
		this.m_psico_exam_ptje_test_11 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_11",
			fieldLabel: "<b>TEST BENDER</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_12
		this.m_psico_exam_ptje_test_12 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_12",
			fieldLabel: "<b>INVENTARIO DE LA ANCIEDAD ZUNG</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_13
		this.m_psico_exam_ptje_test_13 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_13",
			fieldLabel: "<b>INVENTARIO DE LA DEPRECION ZUNG</b>",
			anchor: "95%",
		});
		//m_psico_exam_ptje_test_14
		this.m_psico_exam_ptje_test_14 = new Ext.form.TextField({
			name: "m_psico_exam_ptje_test_14",
			fieldLabel: "<b>ESCALA DE MEMORIA DE WECHSLER</b>",
			anchor: "95%",
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
						"<b>--->  DATOS OCUPACIONALES - HISTORIA FAMILIAR - ANTECEDENTES Y ENFERMEDADES - HABITOS - EXAMEN MENTAL - DIAGNOSTICO FINAL</b>",
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
									title: "EMPRESA ACTUAL",
									//                                    labelWidth: 220,
									items: [
										{
											columnWidth: 0.45,
											border: false,
											layout: "form",
											labelWidth: 140,
											//                                            labelAlign: 'top',
											items: [
												this.empresa,
												this.m_psico_exam_area_trabajo,
												this.m_psico_exam_tiempo_labor,
												this.m_psico_exam_puesto,
											],
										},
										{
											columnWidth: 0.55,
											border: false,
											layout: "form",
											labelWidth: 180,
											//                                            labelAlign: 'top',
											items: [
												this.m_psico_exam_activ_empresa,
												this.m_psico_exam_princ_riesgos,
												this.m_psico_exam_medi_seguridad,
											],
										},
									],
								},
							],
						},
						{
							columnWidth: 0.5,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelAlign: "top",
							items: [this.m_psico_exam_histo_familiar],
						},
						{
							columnWidth: 0.5,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelAlign: "top",
							items: [this.m_psico_exam_accid_enfermedad],
						},
						{
							columnWidth: 0.5,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelAlign: "top",
							items: [this.m_psico_exam_habitos],
						},
						{
							columnWidth: 0.5,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelAlign: "top",
							items: [this.m_psico_exam_otras_obs],
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
									title: "EXAMEN MENTAL",
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
													title: "OBSERVACION DE CONDUCTAS",
													items: [
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 115,
															items: [this.m_psico_exam_presentacion],
														},
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 115,
															items: [this.m_psico_exam_postura],
														},
														{
															xtype: "panel",
															border: false,
															columnWidth: 0.999,
															bodyStyle: "padding:2px 9px 0px 5px;",
															items: [
																{
																	xtype: "fieldset",
																	layout: "column",
																	title: "DISCURSO",
																	items: [
																		{
																			columnWidth: 0.999,
																			border: false,
																			layout: "form",
																			labelWidth: 110,
																			items: [this.m_psico_exam_ritmo],
																		},
																		{
																			columnWidth: 0.999,
																			border: false,
																			layout: "form",
																			labelWidth: 70,
																			items: [this.m_psico_exam_tono],
																		},
																		{
																			columnWidth: 0.999,
																			border: false,
																			layout: "form",
																			labelWidth: 110,
																			items: [this.m_psico_exam_articulacion],
																		},
																	],
																},
															],
														},
														{
															xtype: "panel",
															border: false,
															columnWidth: 0.999,
															bodyStyle: "padding:2px 9px 0px 5px;",
															items: [
																{
																	xtype: "fieldset",
																	layout: "column",
																	title: "DISCURSO",
																	items: [
																		{
																			columnWidth: 0.999,
																			border: false,
																			layout: "form",
																			labelWidth: 110,
																			items: [this.m_psico_exam_tiempo],
																		},
																		{
																			columnWidth: 0.999,
																			border: false,
																			layout: "form",
																			labelWidth: 110,
																			items: [this.m_psico_exam_espacio],
																		},
																		{
																			columnWidth: 0.999,
																			border: false,
																			layout: "form",
																			labelWidth: 110,
																			items: [this.m_psico_exam_persona],
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
											columnWidth: 0.5,
											bodyStyle: "padding:2px 9px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													layout: "column",
													title: "PROCESOS COGNITIVOS",
													items: [
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 140,
															items: [this.m_psico_exam_lucido_atent],
														},
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 140,
															items: [this.m_psico_exam_pensamiento],
														},
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 140,
															items: [this.m_psico_exam_persepcion],
														},
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 140,
															items: [this.m_psico_exam_memoria],
														},
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 140,
															items: [this.m_psico_exam_inteligencia],
														},
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 140,
															items: [this.m_psico_exam_apetito],
														},
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 140,
															items: [this.m_psico_exam_sueno],
														},
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 140,
															items: [this.m_psico_exam_personalidad],
														},
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 140,
															items: [this.m_psico_exam_afectividad],
														},
														{
															columnWidth: 0.999,
															border: false,
															layout: "form",
															labelWidth: 140,
															items: [this.m_psico_exam_conduc_sexual],
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
							columnWidth: 0.5,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelAlign: "top",
							items: [this.m_psico_exam_area_cognitiva],
						},
						{
							columnWidth: 0.5,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelAlign: "top",
							items: [this.m_psico_exam_area_emocional],
						},
					],
				},
				{
					title:
						"<b>--->  PUNTAJE DE CUSTIONARIOS - INVENTARIO - ESCALA - TEST - CLIMA LABORAL - WAIS - LURIA</b>",
					iconCls: "demo2",
					layout: "column",
					autoScroll: true,
					border: false,
					bodyStyle: "padding:10px 10px 20px 10px;",
					labelWidth: 60,
					items: [
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_01],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_02],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_03],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_04],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_05],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_06],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_07],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_08],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_09],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_10],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_11],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_12],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_13],
						},
						{
							columnWidth: 0.6,
							border: false,
							layout: "form",
							bodyStyle: "padding:2px 22px 0px 5px;",
							labelWidth: 400,
							//                            labelAlign: 'top',
							items: [this.m_psico_exam_ptje_test_14],
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
						mod.psicologia.examen_psicologia.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_psico_examen"
										: "save_psico_examen",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								if (action.result.success === true) {
									if (action.result.total === 1) {
										//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
										mod.psicologia.formatos.st.reload();
										mod.psicologia.st.reload();
										mod.psicologia.examen_psicologia.win.el.unmask();
										mod.psicologia.examen_psicologia.win.close();
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
								mod.psicologia.examen_psicologia.win.el.unmask();
								mod.psicologia.examen_psicologia.win.close();
								mod.psicologia.formatos.st.reload();
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
			title: "EXAMEN PSICOLOGICO: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.psicologia.psicologia_altura = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.crea_controles();
		this.list_conclu_altu_psico.load();
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
				acction: "load_psicologia_altura",
				format: "json",
				adm: mod.psicologia.psicologia_altura.record.get("adm"),
				//                ,examen: mod.psicologia.psicologia_altura.record.get('ex_id')
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.psicologia.psicologia_altura.val_medico.setValue(r.val_medico);
				//                mod.psicologia.psicologia_altura.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_stores: function () {
		this.list_conclu_altu_psico = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_conclu_altu_psico",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"conclu_altu_psico_id",
				"conclu_altu_psico_adm",
				"conclu_altu_psico_desc",
			],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm =
						mod.psicologia.psicologia_altura.record.get("adm");
				},
			},
		});
	},
	crea_controles: function () {
		//m_psico_altura_tec_mod_grave
		this.m_psico_altura_tec_mod_grave = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_tec_mod_grave",
			allowBlank: false,
			fieldLabel: "<b>TEC MODERADO A GRAVE</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_tec_mod_grave_desc
		this.m_psico_altura_tec_mod_grave_desc = new Ext.form.TextField({
			fieldLabel: "<b>DESCRIPCION</b>",
			name: "m_psico_altura_tec_mod_grave_desc",
			anchor: "95%",
		});
		//m_psico_altura_convulsiones
		this.m_psico_altura_convulsiones = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_convulsiones",
			allowBlank: false,
			fieldLabel: "<b>CONVULSIONES</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_convulsiones_desc
		this.m_psico_altura_convulsiones_desc = new Ext.form.TextField({
			fieldLabel: "<b>DESCRIPCION</b>",
			name: "m_psico_altura_convulsiones_desc",
			anchor: "95%",
		});
		//m_psico_altura_mareo
		this.m_psico_altura_mareo = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_mareo",
			allowBlank: false,
			fieldLabel: "<b>MAREOS, MIOCLONIAS, ACATISIA</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_mareo_desc
		this.m_psico_altura_mareo_desc = new Ext.form.TextField({
			fieldLabel: "<b>DESCRIPCION</b>",
			name: "m_psico_altura_mareo_desc",
			anchor: "95%",
		});
		//m_psico_altura_problem_audicion
		this.m_psico_altura_problem_audicion = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_problem_audicion",
			allowBlank: false,
			fieldLabel: "<b>PROBLEMAS DE LA AUDICIÓN</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_problem_audicion_desc
		this.m_psico_altura_problem_audicion_desc = new Ext.form.TextField({
			fieldLabel: "<b>DESCRIPCION</b>",
			name: "m_psico_altura_problem_audicion_desc",
			anchor: "95%",
		});
		//m_psico_altura_problem_equilib
		this.m_psico_altura_problem_equilib = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_problem_equilib",
			allowBlank: false,
			fieldLabel: "<b>PROBLEMAS DEL EQUILIBRIO (MENIER, LABERINTITIS)</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_problem_equilib_desc
		this.m_psico_altura_problem_equilib_desc = new Ext.form.TextField({
			fieldLabel: "<b>DESCRIPCION</b>",
			name: "m_psico_altura_problem_equilib_desc",
			anchor: "95%",
		});
		//m_psico_altura_acrofobia
		this.m_psico_altura_acrofobia = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_acrofobia",
			allowBlank: false,
			fieldLabel: "<b>ACROFOBIA</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_acrofobia_desc
		this.m_psico_altura_acrofobia_desc = new Ext.form.TextField({
			fieldLabel: "<b>DESCRIPCION</b>",
			name: "m_psico_altura_acrofobia_desc",
			anchor: "95%",
		});
		//m_psico_altura_agorafobia
		this.m_psico_altura_agorafobia = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_agorafobia",
			allowBlank: false,
			fieldLabel: "<b>AGORAFOBIA</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_agorafobia_desc
		this.m_psico_altura_agorafobia_desc = new Ext.form.TextField({
			fieldLabel: "<b>DESCRIPCION</b>",
			name: "m_psico_altura_agorafobia_desc",
			anchor: "95%",
		});
		//m_psico_altura_alcohol_tipo
		this.m_psico_altura_alcohol_tipo = new Ext.form.TextField({
			fieldLabel: "<b>TIPO</b>",
			name: "m_psico_altura_alcohol_tipo",
			value: "-",
			width: 275,
		});
		//m_psico_altura_alcohol_cant
		this.m_psico_altura_alcohol_cant = new Ext.form.TextField({
			fieldLabel: "<b>CANTIDAD</b>",
			name: "m_psico_altura_alcohol_cant",
			value: "-",
			width: 450,
		});
		//m_psico_altura_alcohol_frecu
		this.m_psico_altura_alcohol_frecu = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_alcohol_frecu",
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
		//m_psico_altura_tabaco_tipo
		this.m_psico_altura_tabaco_tipo = new Ext.form.TextField({
			fieldLabel: "<b>TIPO</b>",
			name: "m_psico_altura_tabaco_tipo",
			value: "-",
			width: 275,
		});
		//m_psico_altura_tabaco_cant
		this.m_psico_altura_tabaco_cant = new Ext.form.TextField({
			fieldLabel: "<b>CANTIDAD</b>",
			name: "m_psico_altura_tabaco_cant",
			value: "-",
			width: 450,
		});
		//m_psico_altura_tabaco_frecu
		this.m_psico_altura_tabaco_frecu = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_tabaco_frecu",
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
		//m_psico_altura_cafe_tipo
		this.m_psico_altura_cafe_tipo = new Ext.form.TextField({
			fieldLabel: "<b>TIPO</b>",
			name: "m_psico_altura_cafe_tipo",
			value: "-",
			width: 275,
		});
		//m_psico_altura_cafe_cant
		this.m_psico_altura_cafe_cant = new Ext.form.TextField({
			fieldLabel: "<b>CANTIDAD</b>",
			name: "m_psico_altura_cafe_cant",
			value: "-",
			width: 450,
		});
		//m_psico_altura_cafe_frecu
		this.m_psico_altura_cafe_frecu = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NADA", "NADA"],
					["POCO", "POCO"],
					["HABITUAL", "HABITUAL"],
					["EXCESIVO", "EXCESIVO"],
				],
			}),
			fieldLabel: "<b>CAFE</b>",
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_cafe_frecu",
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
		//m_psico_altura_droga_tipo
		this.m_psico_altura_droga_tipo = new Ext.form.TextField({
			fieldLabel: "<b>TIPO</b>",
			name: "m_psico_altura_droga_tipo",
			value: "-",
			width: 275,
		});
		//m_psico_altura_droga_cant
		this.m_psico_altura_droga_cant = new Ext.form.TextField({
			fieldLabel: "<b>CANTIDAD</b>",
			name: "m_psico_altura_droga_cant",
			value: "-",
			width: 450,
		});
		//m_psico_altura_droga_frecu
		this.m_psico_altura_droga_frecu = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NADA", "NADA"],
					["POCO", "POCO"],
					["HABITUAL", "HABITUAL"],
					["EXCESIVO", "EXCESIVO"],
				],
			}),
			fieldLabel: "<b>DROGAS</b>",
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_droga_frecu",
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
		//m_psico_altura_preg_resp_01
		this.m_psico_altura_preg_resp_01 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_preg_resp_01",
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
		//m_psico_altura_preg_ptje_01
		this.m_psico_altura_preg_ptje_01 = new Ext.form.TextField({
			name: "m_psico_altura_preg_ptje_01",
			value: "0",
			anchor: "95%",
		});
		//m_psico_altura_preg_resp_02
		this.m_psico_altura_preg_resp_02 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_preg_resp_02",
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
		//m_psico_altura_preg_ptje_02
		this.m_psico_altura_preg_ptje_02 = new Ext.form.TextField({
			name: "m_psico_altura_preg_ptje_02",
			value: "0",
			anchor: "95%",
		});
		//m_psico_altura_preg_resp_03
		this.m_psico_altura_preg_resp_03 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_preg_resp_03",
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
		//m_psico_altura_preg_ptje_03
		this.m_psico_altura_preg_ptje_03 = new Ext.form.TextField({
			name: "m_psico_altura_preg_ptje_03",
			value: "0",
			anchor: "95%",
		});
		//m_psico_altura_preg_resp_04
		this.m_psico_altura_preg_resp_04 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_preg_resp_04",
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
		//m_psico_altura_preg_ptje_04
		this.m_psico_altura_preg_ptje_04 = new Ext.form.TextField({
			name: "m_psico_altura_preg_ptje_04",
			value: "0",
			anchor: "95%",
		});
		//m_psico_altura_preg_resp_05
		this.m_psico_altura_preg_resp_05 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_preg_resp_05",
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
		//m_psico_altura_preg_ptje_05
		this.m_psico_altura_preg_ptje_05 = new Ext.form.TextField({
			name: "m_psico_altura_preg_ptje_05",
			value: "0",
			anchor: "95%",
		});
		//m_psico_altura_preg_resp_06
		this.m_psico_altura_preg_resp_06 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_preg_resp_06",
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
		//m_psico_altura_preg_ptje_06
		this.m_psico_altura_preg_ptje_06 = new Ext.form.TextField({
			name: "m_psico_altura_preg_ptje_06",
			value: "0",
			anchor: "95%",
		});
		//m_psico_altura_preg_resp_07
		this.m_psico_altura_preg_resp_07 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_preg_resp_07",
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
		//m_psico_altura_preg_ptje_07
		this.m_psico_altura_preg_ptje_07 = new Ext.form.TextField({
			name: "m_psico_altura_preg_ptje_07",
			value: "0",
			anchor: "95%",
		});
		//m_psico_altura_preg_resp_08
		this.m_psico_altura_preg_resp_08 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_preg_resp_08",
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
		//m_psico_altura_preg_ptje_08
		this.m_psico_altura_preg_ptje_08 = new Ext.form.TextField({
			name: "m_psico_altura_preg_ptje_08",
			value: "0",
			anchor: "95%",
		});
		//m_psico_altura_preg_resp_09
		this.m_psico_altura_preg_resp_09 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_preg_resp_09",
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
		//m_psico_altura_preg_ptje_09
		this.m_psico_altura_preg_ptje_09 = new Ext.form.TextField({
			name: "m_psico_altura_preg_ptje_09",
			value: "0",
			anchor: "95%",
		});
		//m_psico_altura_preg_resp_10
		this.m_psico_altura_preg_resp_10 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_preg_resp_10",
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
		//m_psico_altura_preg_ptje_10
		this.m_psico_altura_preg_ptje_10 = new Ext.form.TextField({
			name: "m_psico_altura_preg_ptje_10",
			value: "0",
			anchor: "95%",
		});
		//m_psico_altura_entrena_altura
		this.m_psico_altura_entrena_altura = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_entrena_altura",
			fieldLabel:
				"<b>RECIBIÓ ENTRENAMIENTO PARA TRABAJOS EN ALTURAS MAYORES A 1.8M</b>",
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
		//m_psico_altura_entrena_auxilio
		this.m_psico_altura_entrena_auxilio = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_entrena_auxilio",
			fieldLabel: "<b>¿RECIBIÓ ENTRENAMIENTO EN PRIMEROS AUXILIOS?</b>",
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
		//m_psico_altura_equilibrio_01
		this.m_psico_altura_equilibrio_01 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["ANORMAL", "ANORMAL"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_equilibrio_01",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 200,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NORMAL");
					descripcion.setRawValue("NORMAL");
				},
			},
		});
		//m_psico_altura_equilibrio_02
		this.m_psico_altura_equilibrio_02 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["ANORMAL", "ANORMAL"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_equilibrio_02",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 200,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NORMAL");
					descripcion.setRawValue("NORMAL");
				},
			},
		});
		//m_psico_altura_equilibrio_03
		this.m_psico_altura_equilibrio_03 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["NO REALIZADO", "NO REALIZADO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_equilibrio_03",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 200,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO REALIZADO");
					descripcion.setRawValue("NO REALIZADO");
				},
			},
		});
		//m_psico_altura_equilibrio_04
		this.m_psico_altura_equilibrio_04 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["NO REALIZADO", "NO REALIZADO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_equilibrio_04",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 200,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO REALIZADO");
					descripcion.setRawValue("NO REALIZADO");
				},
			},
		});
		//m_psico_altura_equilibrio_05
		this.m_psico_altura_equilibrio_05 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["NO REALIZADO", "NO REALIZADO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_equilibrio_05",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 200,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO REALIZADO");
					descripcion.setRawValue("NO REALIZADO");
				},
			},
		});
		//m_psico_altura_equilibrio_06
		this.m_psico_altura_equilibrio_06 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["NO REALIZADO", "NO REALIZADO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_equilibrio_06",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 200,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO REALIZADO");
					descripcion.setRawValue("NO REALIZADO");
				},
			},
		});
		//m_psico_altura_equilibrio_07
		this.m_psico_altura_equilibrio_07 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["NO REALIZADO", "NO REALIZADO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_equilibrio_07",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 200,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO REALIZADO");
					descripcion.setRawValue("NO REALIZADO");
				},
			},
		});
		//m_psico_altura_equilibrio_08
		this.m_psico_altura_equilibrio_08 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["NO REALIZADO", "NO REALIZADO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_equilibrio_08",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 200,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO REALIZADO");
					descripcion.setRawValue("NO REALIZADO");
				},
			},
		});
		//m_psico_altura_equilibrio_09
		this.m_psico_altura_equilibrio_09 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["NORMAL", "NORMAL"],
					["NO REALIZADO", "NO REALIZADO"],
					["-", "-"],
				],
			}),
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_altura_equilibrio_09",
			allowBlank: false,
			typeAhead: false,
			editable: false,
			mode: "local",
			forceSelection: true,
			triggerAction: "all",
			emptyText: "Seleccione...",
			selectOnFocus: true,
			anchor: "90%",
			width: 200,
			listeners: {
				afterrender: function (descripcion) {
					descripcion.setValue("NO REALIZADO");
					descripcion.setRawValue("NO REALIZADO");
				},
			},
		});
		//m_psico_altura_nistagmus_esponta
		this.m_psico_altura_nistagmus_esponta = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_nistagmus_esponta",
			allowBlank: false,
			fieldLabel: "<b>NISTAGMUS ESPONTÁNEO</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_nistagmus_provoca
		this.m_psico_altura_nistagmus_provoca = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_nistagmus_provoca",
			allowBlank: false,
			fieldLabel: "<b>NISTAGMUS PROVOCADO</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_pie_plano
		this.m_psico_altura_pie_plano = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_pie_plano",
			allowBlank: false,
			fieldLabel: "<b>PIE PLANO</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_usa_plantillas
		this.m_psico_altura_usa_plantillas = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_usa_plantillas",
			allowBlank: false,
			fieldLabel: "<b>USA PLANTILLAS</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_toulouse
		this.m_psico_altura_toulouse = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_toulouse",
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
		//m_psico_altura_bc_2
		this.m_psico_altura_bc_2 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_bc_2",
			allowBlank: false,
			fieldLabel: "<b>BC-2</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_h_entre_form_est
		this.m_psico_altura_h_entre_form_est = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_h_entre_form_est",
			allowBlank: false,
			fieldLabel: "<b>Hoja de entrevista formato establecido</b>",
			typeAhead: false,
			editable: false,
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
		//m_psico_altura_temores
		this.m_psico_altura_temores = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_altura_temores",
			allowBlank: false,
			fieldLabel: "<b>CUESTIONARIO DE TEMORES</b>",
			typeAhead: false,
			editable: false,
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

		//m_psico_altura_aptitud
		this.m_psico_altura_aptitud = new Ext.form.RadioGroup({
			fieldLabel: "<b>APTITUD</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{
					boxLabel: "APTO",
					name: "m_psico_altura_aptitud",
					inputValue: "APTO",
				},
				{
					boxLabel: "APTO CON RESTRICCIONES",
					name: "m_psico_altura_aptitud",
					inputValue: "APTO CON RESTRICCIONES",
				},
				{
					boxLabel: "NO APTO",
					name: "m_psico_altura_aptitud",
					inputValue: "NO APTO",
				},
				{
					boxLabel: "EN PROCESO DE VALIDACION",
					name: "m_psico_altura_aptitud",
					inputValue: "EN PROCESO DE VALIDACION",
					checked: true,
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
						mod.psicologia.altura_conclu.init(null);
					},
				},
			],
		});
		this.dt_grid4 = new Ext.grid.GridPanel({
			store: this.list_conclu_altu_psico,
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
					mod.psicologia.altura_conclu.init(record2);
				},
			},
			autoExpandColumn: "reco_desc",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "reco_desc",
					header: "CONCLUSIONES",
					dataIndex: "conclu_altu_psico_desc",
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
						"<b>--->  EXAMEN OCUPACIONAL PARA TRABAJOS EN ALTURA MAYOR A  1.80 m</b>",
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
									title: "ANTECEDENTES PSICONEUROLÓGICOS",
									//                                    labelWidth: 220,
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 340,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_tec_mod_grave],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 85,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_tec_mod_grave_desc],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 340,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_convulsiones],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 85,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_convulsiones_desc],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 340,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_mareo],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 85,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_mareo_desc],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 340,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_problem_audicion],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 85,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_problem_audicion_desc],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 340,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_problem_equilib],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 85,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_problem_equilib_desc],
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
									title: "FOBIAS",
									//                                    labelWidth: 220,
									items: [
										{
											columnWidth: 0.25,
											border: false,
											layout: "form",
											labelWidth: 90,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_acrofobia],
										},
										{
											columnWidth: 0.75,
											border: false,
											layout: "form",
											labelWidth: 85,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_acrofobia_desc],
										},
										{
											columnWidth: 0.25,
											border: false,
											layout: "form",
											labelWidth: 90,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_agorafobia],
										},
										{
											columnWidth: 0.75,
											border: false,
											layout: "form",
											labelWidth: 85,
											//                                            labelAlign: 'top',
											items: [this.m_psico_altura_agorafobia_desc],
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
									title: "HABITOS NOCIVOS",
									items: [
										{
											xtype: "compositefield",
											//                                            fieldLabel: 'LENSOMETRIA',
											items: [
												{
													xtype: "displayfield",
													value: "<center><b>TIPO</b></center>",
													width: 275,
												},
												{
													xtype: "displayfield",
													value: "<center><b>CANTIDAD</b></center>",
													width: 450,
												},
												{
													xtype: "displayfield",
													value: "<center><b>FRECUENCIA</b></center>",
													width: 100,
												},
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>ALCOHOL</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_alcohol_tipo,
												this.m_psico_altura_alcohol_cant,
												this.m_psico_altura_alcohol_frecu,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>TABACO</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_tabaco_tipo,
												this.m_psico_altura_tabaco_cant,
												this.m_psico_altura_tabaco_frecu,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>CAFÉ</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_cafe_tipo,
												this.m_psico_altura_cafe_cant,
												this.m_psico_altura_cafe_frecu,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>DROGAS</b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_droga_tipo,
												this.m_psico_altura_droga_cant,
												this.m_psico_altura_droga_frecu,
											],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.99,
							labelWidth: 600,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									title: "TEST DE CAGE",
									items: [
										{
											xtype: "compositefield",
											items: [
												{
													xtype: "displayfield",
													value: "<center><b>RESPUESTA</b></center>",
													width: 100,
												},
												{
													xtype: "displayfield",
													value: "<center><b>PUNTAJE</b></center>",
													width: 150,
												},
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>¿LE GUSTA SALIR A DIVERTIRSE?<b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_preg_resp_01,
												this.m_psico_altura_preg_ptje_01,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>¿SE MOLESTA SI LLEGA TARDE A ALGÚN COMPROMISO?<b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_preg_resp_02,
												this.m_psico_altura_preg_ptje_02,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>¿LE HA MOLESTADO ALGUNA VEZ LA GENTE CRITICÁNDOLE SU FORMA DE BEBER?<b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_preg_resp_03,
												this.m_psico_altura_preg_ptje_03,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>¿HA SENTIDO QUE ESTAR EN UNA REUNIÓN DIVIRTIÉNDOSE LO REANIMA?<b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_preg_resp_04,
												this.m_psico_altura_preg_ptje_04,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>¿HA TENIDO ALGUNA VEZ LA IMPRESIÓN DE QUE DEBERÍA BEBER MENOS?<b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_preg_resp_05,
												this.m_psico_altura_preg_ptje_05,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>¿DUERME BIEN?<b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_preg_resp_06,
												this.m_psico_altura_preg_ptje_06,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>¿SE HA SENTIDO ALGUNA VEZ MAL O CULPABLE POR SU COSTUMBRE DE BEBER?<b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_preg_resp_07,
												this.m_psico_altura_preg_ptje_07,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>¿SE PONE NERVIOSO A MENUDO?<b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_preg_resp_08,
												this.m_psico_altura_preg_ptje_08,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>¿ALGUNA VEZ LO PRIMERO QUE HA HECHO POR LA MAÑANA HA SIDO BEBER PARA CALMAR?<b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_preg_resp_09,
												this.m_psico_altura_preg_ptje_09,
											],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>¿SUFRE DE DOLORES EN LA ESPALDA AL LEVANTARSE?<b>",
											bodyStyle: "padding:3px;",
											items: [
												this.m_psico_altura_preg_resp_10,
												this.m_psico_altura_preg_ptje_10,
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
									title: "EXAMEN MÉDICO DIRIGIDO",
									//                                    labelWidth: 220,
									items: [
										{
											columnWidth: 0.6,
											border: false,
											layout: "form",
											//                                            labelWidth: 340,
											labelAlign: "top",
											items: [this.m_psico_altura_entrena_altura],
										},
										{
											columnWidth: 0.4,
											border: false,
											layout: "form",
											//                                            labelWidth: 85,
											labelAlign: "top",
											items: [this.m_psico_altura_entrena_auxilio],
										},
										{
											xtype: "panel",
											border: false,
											columnWidth: 0.99,
											labelWidth: 500,
											bodyStyle: "padding:2px 6px 0px 5px;",
											items: [
												{
													xtype: "fieldset",
													title: "EQUILIBRIO",
													items: [
														{
															xtype: "compositefield",
															items: [
																{
																	xtype: "displayfield",
																	value: "<center><b>RESPUESTA</b></center>",
																	width: 200,
																},
															],
														},
														{
															xtype: "compositefield",
															fieldLabel: "<b>TÍMPANOS<b>",
															bodyStyle: "padding:3px;",
															items: [this.m_psico_altura_equilibrio_01],
														},
														{
															xtype: "compositefield",
															fieldLabel: "<b>AUDICIÓN<b>",
															bodyStyle: "padding:3px;",
															items: [this.m_psico_altura_equilibrio_02],
														},
														{
															xtype: "compositefield",
															fieldLabel:
																"<b>SUSTENTACIÓN EN UN PIE POR 15 SEGUNDOS<b>",
															bodyStyle: "padding:3px;",
															items: [this.m_psico_altura_equilibrio_03],
														},
														{
															xtype: "compositefield",
															fieldLabel:
																"<b>CAMINAR LIBRE SOBRE UNA RECTA 3M SIN DESVÍO<b>",
															bodyStyle: "padding:3px;",
															items: [this.m_psico_altura_equilibrio_04],
														},
														{
															xtype: "compositefield",
															fieldLabel:
																"<b>CAMINAR LIBRE CON LOS OJOS VENDADOS 3M SIN DESVÍO<b>",
															bodyStyle: "padding:3px;",
															items: [this.m_psico_altura_equilibrio_05],
														},
														{
															xtype: "compositefield",
															fieldLabel:
																"<b>CAMINAR LIBRE CON LOS OJOS VENDADOS EN PUNTA TALÓN 3 M SIN DESVÍO<b>",
															bodyStyle: "padding:3px;",
															items: [this.m_psico_altura_equilibrio_06],
														},
														{
															xtype: "compositefield",
															fieldLabel:
																"<b>ROTAR SOBRE UNA SILLA Y LUEGO VERIFICAR EQUILIBRIO DE PIE<b>",
															bodyStyle: "padding:3px;",
															items: [this.m_psico_altura_equilibrio_07],
														},
														{
															xtype: "compositefield",
															fieldLabel: "<b>ADIADOCOQUINESIA DIRECTA<b>",
															bodyStyle: "padding:3px;",
															items: [this.m_psico_altura_equilibrio_08],
														},
														{
															xtype: "compositefield",
															fieldLabel: "<b>ADIADOCOQUINESIA CRUZADA<b>",
															bodyStyle: "padding:3px;",
															items: [this.m_psico_altura_equilibrio_09],
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
							columnWidth: 0.99,
							labelWidth: 160,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "EVALUACIÓN OCULAR",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											//                                                            labelWidth: 340,
											//                                                            labelAlign: 'top',
											items: [this.m_psico_altura_nistagmus_esponta],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											//                                                            labelWidth: 340,
											//                                                            labelAlign: 'top',
											items: [this.m_psico_altura_nistagmus_provoca],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.99,
							labelWidth: 160,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									layout: "column",
									title: "TRASTORNOS DE PIE",
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											//                                                            labelWidth: 340,
											//                                                            labelAlign: 'top',
											items: [this.m_psico_altura_pie_plano],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											//                                                            labelWidth: 340,
											//                                                            labelAlign: 'top',
											items: [this.m_psico_altura_usa_plantillas],
										},
									],
								},
							],
						},
						{
							xtype: "panel",
							border: false,
							columnWidth: 0.99,
							labelWidth: 160,
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
											labelWidth: 250,
											items: [this.m_psico_altura_toulouse],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 250,
											items: [this.m_psico_altura_bc_2],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 250,
											items: [this.m_psico_altura_h_entre_form_est],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											labelWidth: 250,
											items: [this.m_psico_altura_temores],
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
											//                                            labelAlign: 'top',
											labelWidth: 60,
											items: [this.m_psico_altura_aptitud],
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
						mod.psicologia.psicologia_altura.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_psicologia_altura"
										: "save_psicologia_altura",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								if (action.result.success === true) {
									if (action.result.total === 1) {
										//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
										mod.psicologia.formatos.st.reload();
										mod.psicologia.st.reload();
										mod.psicologia.psicologia_altura.win.el.unmask();
										mod.psicologia.psicologia_altura.win.close();
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
								mod.psicologia.psicologia_altura.win.el.unmask();
								mod.psicologia.psicologia_altura.win.close();
								mod.psicologia.formatos.st.reload();
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
			title: "EXAMEN TRABAJOS EN ALTURA MAYOR A 1.80 m: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

mod.psicologia.altura_conclu = {
	record2: null,
	win: null,
	frm: null,
	conclu_altu_psico_desc: null,
	init: function (r) {
		this.record2 = r;
		this.crea_stores();
		this.st_busca_conclu_altu_psico.load();
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
				acction: "load_conclu_altu_psico",
				format: "json",
				conclu_altu_psico_id: this.record2.get("conclu_altu_psico_id"),
				conclu_altu_psico_adm: this.record2.get("conclu_altu_psico_adm"),
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
			},
		});
	},
	crea_stores: function () {
		this.st_busca_conclu_altu_psico = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "st_busca_conclu_altu_psico",
				format: "json",
			},
			fields: ["conclu_altu_psico_desc"],
			root: "data",
		});
	},
	crea_controles: function () {
		this.resultTpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'<div class="div-table-col">',
			"<h3><span>{conclu_altu_psico_desc}</span></h3>",
			"</div>",
			"</div></tpl>"
		);

		this.conclu_altu_psico_desc = new Ext.form.ComboBox({
			store: this.st_busca_conclu_altu_psico,
			loadingText: "Searching...",
			pageSize: 10,
			tpl: this.resultTpl,
			hideTrigger: true,
			itemSelector: "div.search-item",
			selectOnFocus: true,
			minChars: 3,
			hiddenName: "conclu_altu_psico_desc",
			displayField: "conclu_altu_psico_desc",
			valueField: "conclu_altu_psico_desc",
			allowBlank: false,
			typeAhead: false,
			triggerAction: "all",
			fieldLabel: "<b>RECOMENDACIONES Y CONCLUSIONES</b>",
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
					items: [this.conclu_altu_psico_desc],
				},
			],
			buttons: [
				{
					text: "Guardar",
					iconCls: "guardar",
					formBind: true,
					scope: this,
					handler: function () {
						mod.psicologia.altura_conclu.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						var metodo;
						var conclu_altu_psico_id;
						if (this.record2 !== null) {
							metodo = "update";
							conclu_altu_psico_id = mod.psicologia.altura_conclu.record2.get(
								"conclu_altu_psico_id"
							);
						} else {
							metodo = "save";
							conclu_altu_psico_id = "";
						}

						this.frm.getForm().submit({
							params: {
								acction: metodo + "_conclu_altu_psico",
								conclu_altu_psico_adm:
									mod.psicologia.psicologia_altura.record.get("adm"),
								conclu_altu_psico_id: conclu_altu_psico_id,
							},
							success: function (form, action) {
								obj = Ext.util.JSON.decode(action.response.responseText);
								//                                Ext.MessageBox.alert('En hora buena', 'El paciente se registro correctamente');
								mod.psicologia.altura_conclu.win.el.unmask();
								mod.psicologia.psicologia_altura.list_conclu_altu_psico.reload();
								mod.psicologia.altura_conclu.win.close();
							},
							failure: function (form, action) {
								mod.psicologia.altura_conclu.win.el.unmask();
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
								mod.psicologia.psicologia_altura.list_conclu_altu_psico.reload();
								mod.psicologia.altura_conclu.win.close();
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
			title: "REGISTRO DE RECOMENDACIONES Y CONCLUSIONES",
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

mod.psicologia.psico_confinados = {
	win: null,
	frm: null,
	record: null,
	init: function (r) {
		this.record = r;
		this.crea_stores();
		this.crea_controles();
		this.list_conclu_altu_psico.load();
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
				acction: "load_psico_confinados",
				format: "json",
				adm: mod.psicologia.psico_confinados.record.get("adm"),
				//                ,examen: mod.psicologia.psico_confinados.record.get('ex_id')
			},
			scope: this,
			success: function (frm, action) {
				r = action.result.data;
				//                mod.psicologia.psico_confinados.val_medico.setValue(r.val_medico);
				//                mod.psicologia.psico_confinados.val_medico.setRawValue(r.medico_nom);
			},
		});
	},
	crea_stores: function () {
		this.list_conclu_altu_psico = new Ext.data.JsonStore({
			url: "<[controller]>",
			baseParams: {
				acction: "list_conclu_altu_psico",
				format: "json",
			},
			root: "data",
			totalProperty: "total",
			fields: [
				"conclu_altu_psico_id",
				"conclu_altu_psico_adm",
				"conclu_altu_psico_desc",
			],
			listeners: {
				beforeload: function (store, options) {
					this.baseParams.adm =
						mod.psicologia.psico_confinados.record.get("adm");
				},
			},
		});
	},
	crea_controles: function () {
		// m_psico_confinados_preg01
		this.m_psico_confinados_preg01 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg01",
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
		// m_psico_confinados_preg02
		this.m_psico_confinados_preg02 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg02",
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
		// m_psico_confinados_preg03
		this.m_psico_confinados_preg03 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg03",
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
		// m_psico_confinados_preg04
		this.m_psico_confinados_preg04 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg04",
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
		// m_psico_confinados_preg05
		this.m_psico_confinados_preg05 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg05",
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
		// m_psico_confinados_preg06
		this.m_psico_confinados_preg06 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg06",
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
		// m_psico_confinados_preg07
		this.m_psico_confinados_preg07 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg07",
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
		// m_psico_confinados_preg08
		this.m_psico_confinados_preg08 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg08",
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
		// m_psico_confinados_preg09
		this.m_psico_confinados_preg09 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg09",
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
		// m_psico_confinados_preg10
		this.m_psico_confinados_preg10 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg10",
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
		// m_psico_confinados_preg11
		this.m_psico_confinados_preg11 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg11",
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
		// m_psico_confinados_preg12
		this.m_psico_confinados_preg12 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg12",
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
		// m_psico_confinados_preg13
		this.m_psico_confinados_preg13 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg13",
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
		// m_psico_confinados_preg14
		this.m_psico_confinados_preg14 = new Ext.form.ComboBox({
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
			hiddenName: "m_psico_confinados_preg14",
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
		// m_psico_confinados_entrena_confina
		this.m_psico_confinados_entrena_confina = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			fieldLabel:
				"<b>RECIBIÓ ENTRENAMIENTO PARA TRABAJOS EN ESPACIOS CONFINADOS</b>",
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_confinados_entrena_confina",
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
		// m_psico_confinados_prim_auxilios
		this.m_psico_confinados_prim_auxilios = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["SI", "SI"],
					["NO", "NO"],
					["-", "-"],
				],
			}),
			fieldLabel: "<b>¿RECIBIÓ ENTRENAMIENTO EN PRIMEROS AUXILIOS?</b>",
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_confinados_prim_auxilios",
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
		// m_psico_confinados_fobia_claustro
		this.m_psico_confinados_fobia_claustro = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			fieldLabel: "<b>DESCARTE DE FOBIA - CLAUSTROFOBIA</b>",
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_confinados_fobia_claustro",
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
		// m_psico_confinados_bat7
		this.m_psico_confinados_bat7 = new Ext.form.ComboBox({
			store: new Ext.data.ArrayStore({
				fields: ["campo", "descripcion"],
				data: [
					["APTO", "APTO"],
					["NO APTO", "NO APTO"],
					["-", "-"],
				],
			}),
			fieldLabel: "<b>BAT-7  (SUB ESCALA DE ORIENTACION ESPACIAL)</b>",
			displayField: "descripcion",
			valueField: "campo",
			hiddenName: "m_psico_confinados_bat7",
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
		// m_psico_confinados_formato
		this.m_psico_confinados_formato = new Ext.form.TextField({
			fieldLabel: "<b>Hoja de entrevista formato establecido</b>",
			allowBlank: false,
			name: "m_psico_confinados_formato",
			anchor: "95%",
		});
		// m_psico_confinados_cuest_temores
		this.m_psico_confinados_cuest_temores = new Ext.form.TextField({
			fieldLabel: "<b>Cuestionario de temores</b>",
			allowBlank: false,
			name: "m_psico_confinados_cuest_temores",
			anchor: "95%",
		});
		// m_psico_confinados_aptitud
		this.m_psico_confinados_aptitud = new Ext.form.RadioGroup({
			fieldLabel: "<b>APTITUD</b>",
			itemCls: "x-check-group-alt",
			columns: 1,
			items: [
				{
					boxLabel: "APTO",
					name: "m_psico_confinados_aptitud",
					inputValue: "APTO",
				},
				{
					boxLabel: "APTO CON RESTRICCIONES",
					name: "m_psico_confinados_aptitud",
					inputValue: "APTO CON RESTRICCIONES",
				},
				{
					boxLabel: "NO APTO",
					name: "m_psico_confinados_aptitud",
					inputValue: "NO APTO",
				},
				{
					boxLabel: "EN PROCESO DE VALIDACION",
					name: "m_psico_confinados_aptitud",
					inputValue: "EN PROCESO DE VALIDACION",
					checked: true,
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
						mod.psicologia.altura_conclu.init(null);
					},
				},
			],
		});
		this.dt_grid4 = new Ext.grid.GridPanel({
			store: this.list_conclu_altu_psico,
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
					mod.psicologia.altura_conclu.init(record2);
				},
			},
			autoExpandColumn: "reco_desc",
			columns: [
				new Ext.grid.RowNumberer(),
				{
					id: "reco_desc",
					header: "CONCLUSIONES",
					dataIndex: "conclu_altu_psico_desc",
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
					title: "<b>--->  EXAMEN PARA ESPACIOS CONFINADOS</b>",
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
							columnWidth: 0.99,
							labelWidth: 800,
							bodyStyle: "padding:2px 22px 0px 5px;",
							items: [
								{
									xtype: "fieldset",
									title: "¿CUANDO ESTAS EN UN AMBIENTE CHICO CERRADO SIENTES?",
									items: [
										{
											xtype: "compositefield",
											items: [
												{
													xtype: "displayfield",
													value: "<center><b>RESPUESTA</b></center>",
													width: 100,
												},
											],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>MIEDO INTENSO A MORIR O A ESTAR SUFRIENDO UN ATAQUE CARDÍACO O ALGUNA ENFERMEDAD FÍSICA GRAVE QUE PONGA EN RIESGO LA VIDA<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg01],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>MIEDO INTENSO A VOLVERSE LOCO O A PERDER EL CONTROL DE SI MISMO<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg02],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>PALPITACIONES (PERCEPCIÓN DEL LATIDO CARDÍACO) O PULSACIONES ACELERADAS (TAQUICARDIA)<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg03],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>SUDORACIÓN<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg04],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>PALIDEZ<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg05],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>TEMBLORES O SACUDIDAS MUSCULARES<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg06],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b> SENSACIÓN DE AHOGO O FALTA DE AIRE<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg07],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>OPRESIÓN EN LA GARGANTA (SENSACIÓN DE NO PODER RESPIRAR) O EN EL PECHO<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg08],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>NÁUSEAS, VÓMITOS O MOLESTIAS Y DOLORES ABDOMINALES<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg09],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>INESTABILIDAD, MAREOS O DESMAYOS<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg10],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b> SENSACIÓN DE IRREALIDAD (SENTIR AL MUNDO EXTERNO COMO ALGO EXTRAÑO)<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg11],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>SENSACIÓN DE NO SER UNO MISMO (DESPERSONALIZACIÓN)<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg12],
										},
										{
											xtype: "compositefield",
											fieldLabel: "<b>HORMIGUEOS (PARESTESIAS)<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg13],
										},
										{
											xtype: "compositefield",
											fieldLabel:
												"<b>ESCALOFRÍOS O SENSACIÓN DE SUFRIR FRÍO INTENSO<b>",
											bodyStyle: "padding:3px;",
											items: [this.m_psico_confinados_preg14],
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
									title: "EXAMEN MÉDICO DIRIGIDO",
									//                                    labelWidth: 220,
									items: [
										{
											columnWidth: 0.6,
											border: false,
											layout: "form",
											//                                            labelWidth: 340,
											labelAlign: "top",
											items: [this.m_psico_confinados_entrena_confina],
										},
										{
											columnWidth: 0.4,
											border: false,
											layout: "form",
											//                                            labelWidth: 85,
											labelAlign: "top",
											items: [this.m_psico_confinados_prim_auxilios],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											//                                            labelWidth: 85,
											labelAlign: "top",
											items: [this.m_psico_confinados_fobia_claustro],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											//                                            labelWidth: 85,
											labelAlign: "top",
											items: [this.m_psico_confinados_bat7],
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
									title: "EXAMEN MÉDICO DIRIGIDO",
									//                                    labelWidth: 220,
									items: [
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											//                                            labelWidth: 340,
											labelAlign: "top",
											items: [this.m_psico_confinados_formato],
										},
										{
											columnWidth: 0.5,
											border: false,
											layout: "form",
											//                                            labelWidth: 340,
											labelAlign: "top",
											items: [this.m_psico_confinados_cuest_temores],
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
											//                                            labelAlign: 'top',
											labelWidth: 60,
											items: [this.m_psico_confinados_aptitud],
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
						mod.psicologia.psico_confinados.win.el.mask(
							"Guardando…",
							"x-mask-loading"
						);
						this.frm.getForm().submit({
							params: {
								acction:
									this.record.get("st") >= 1
										? "update_psico_confinados"
										: "save_psico_confinados",
								id: this.record.get("id"),
								adm: this.record.get("adm"),
								ex_id: this.record.get("ex_id"),
							},
							success: function (form, action) {
								if (action.result.success === true) {
									if (action.result.total === 1) {
										//                                        Ext.MessageBox.alert('En hora buena', 'Se registro correctamente ' + action.result.total);
										mod.psicologia.formatos.st.reload();
										mod.psicologia.st.reload();
										mod.psicologia.psico_confinados.win.el.unmask();
										mod.psicologia.psico_confinados.win.close();
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
								mod.psicologia.psico_confinados.win.el.unmask();
								mod.psicologia.psico_confinados.win.close();
								mod.psicologia.formatos.st.reload();
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
			title: "EXAMEN PARA ESPACIOS CONFINADOS: ",
			maximizable: false,
			resizable: false,
			draggable: true,
			closable: true,
			layout: "border",
			items: [this.frm],
		});
	},
};

Ext.onReady(mod.psicologia.init, mod.psicologia);
