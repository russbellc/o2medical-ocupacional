Ext.ns("plataforma");
plataforma = {
	p_header: null,
	tp_content: null,
	tp_window: null,
	p_footer: null,
	p_logo1: null,
	p_logo2: null,
	p_menu: null,
	main: null,
	dv_menu: null,
	dv_menu2: null,
	dv_menu3: null,
	tplmenu: null,
	st_lismenu: null,
	tp_logout: null,
	tp_submodulos: null,
	bt_logout: null,
	win: null,
	init: function () {
		Ext.QuickTips.init();
		Ext.form.Field.prototype.msgTarget = "side";
		Ext.BLANK_IMAGE_URL = "../librerias/extjs/resources/images/default/s.gif";
		this.crea_stores();
		this.crea_controles();
		setInterval("plataforma.cron()", 300000);
	},
	cron: function () {
		Ext.Ajax.request({
			//dispara la petici�n
			url: "code/loader.php",
			method: "POST",
			params: {
				sys_acction: "verify",
			},
			success: function (response, options) {
				var r = Ext.util.JSON.decode(response.responseText);
				if (!r.success) {
					location.reload();
				}
			},
			failure: function (response, options) {
				location.reload();
			},
		});
	},
	logaut: function () {
		Ext.Ajax.request({
			url: "code/sys_logout",
			params: {},
			success: function (response, opts) {
				var dato = Ext.decode(response.responseText);
				location.href = window.location;
			},
			failure: function (response, opts) {
				Ext.Msg.show({
					title: "Error",
					msg: response.status,
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR,
				});
			},
		});
	},
	crea_stores: function () {
		this.st_lismenu = new Ext.data.JsonStore({
			url: "code/sys_usuperfiles/json",
			baseParams: {
				acction: "sys_usuperfiles",
				format: "json",
			},
			fields: ["men_id", "men_desc", "con_usuid"],
			root: "data",
			autoLoad: true,
		});
		this.st_lismenu2 = new Ext.data.JsonStore({
			url: "code/sys_usuperfiles11/json",
			baseParams: {
				acction: "sys_usuperfiles11",
				format: "json",
			},
			fields: ["men_id", "men_desc", "con_usuid"],
			root: "data",
			autoLoad: true,
		});
	},
	crea_controles: function () {
		this.tplmenu = new Ext.XTemplate(
			'<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false">',
			'<div class="thumb-wrap" id="{name}">',
			'<tpl for=".">',
			'<div id="imagenes" class="_men"><IMG SRC="modulos/{men_id}/icon.png" title="{men_desc}">',
			'<p style="margin:0 0 4px 0;">{men_desc}</p>',
			"</div>",
			"</tpl>",
			"</div>",
			"</div>"
		);
		this.dv_menu = new Ext.DataView({
			store: this.st_lismenu2,
			tpl: this.tplmenu,
			autoHeight: true,
			multiSelect: true,
			overClass: "x-view-over",
			itemSelector: "div._men",
			emptyText: "",
			listeners: {
				click: function (dataview, index, item, e) {
					plataforma.agrega_panel(dataview.store.getAt(index));
				},
			},
		});
		this.dv_menu2 = new Ext.DataView({
			store: this.st_lismenu,
			tpl: this.tplmenu,
			autoHeight: true,
			multiSelect: true,
			overClass: "x-view-over",
			itemSelector: "div._men",
			emptyText: "",
			listeners: {
				//                click: function(dataview, index, item, e) {
				//                    plataforma.agrega_panel(dataview.store.getAt(index));
				//                },//dbclick
				click: function (dataview, index, item, e) {
					plataforma.agrega_window(dataview.store.getAt(index));
				}, //dbclick
			},
		});
		this.dv_menu3 = new Ext.DataView({
			store: this.st_lismenu,
			tpl: this.tplmenu,
			autoHeight: true,
			multiSelect: true,
			overClass: "x-view-over",
			itemSelector: "div._men",
			emptyText: "",
			listeners: {
				click: function (dataview, index, item, e) {
					plataforma.agrega_panel(dataview.store.getAt(index));
				}, //dbclick
			},
		});
		this.p_logo1 = new Ext.Panel({
			region: "east", //west
			//            bodyStyle: 'background-color: #023881;',
			width: 265,
			//            items:[this.dv_menu]
			//            html: '<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" \n\
			//                    ondragstart="return false" onmousedown="return false" onselectstart="return false">\n\
			//                    <IMG SRC="images/logo.png" width="200" style="padding: 14px 5px 10px 5px" ></div>',
			//            title: 'INFORMACION DEL USUARIO',
			bodyStyle: "background-color: rgba(2,56,129,0.9);",
			autoLoad: {
				url: "code/sys_infouser/html",
			},
			bbar: {
				items: [
					'<div style="color:#023881"><b>INFORMACIÓN DEL USUARIO</b></div>',
					"->",
					{
						xtype: "button",
						text: "<b>Cerrar Sesión</b>",
						ui: "mask",
						iconCls: "logout",
						formBind: true,
						handler: function () {
							plataforma.logaut();
						},
					},
				],
			},
			//                    buttons: [this.bt_logout]
		});
		this.p_logo2 = new Ext.Panel({
			region: "west",
			width: 190,
			bodyStyle: "background-color: #023881;",
			autoLoad: {
				url: "code/sys_getlogo/html",
			},
		});
		this.p_logo3 = new Ext.Panel({
			width: 200,
			bodyStyle: "background-color: #023881;",
			autoLoad: {
				url: "code/sys_getlogo/html",
			},
		});
		this.p_menu = new Ext.Panel({
			region: "center",
			bodyStyle: "background-color: rgba(2,56,129,1);",
			height: 630,
			items: [this.dv_menu],
			//            html:'menu'
		});
		this.p_header = new Ext.Panel({
			region: "north",
			layout: "border",
			height: 100,
			defaults: {
				border: false,
			},
			items: [this.p_logo2, this.p_menu, this.p_logo1],
		});
		var fecha = new Date();
		this.p_footer = new Ext.Panel({
			region: "south",
			height: 15,
			html:
				'<div class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false"><center><pre>Copyright® 2014-' +
				new Date().getFullYear() +
				" SISTEMA MÉDICO - SALUD OCUPACIONAL V5.0 - <b>Desarrollado por LIVECODE Inc.</b></pre></center></div>",
		});
		this.bt_logout = new Ext.Button({
			text: "Salir",
			formBind: true,
			handler: function () {
				plataforma.logaut();
			},
		});
		this.tp_logout = new Ext.Panel({
			region: "west",
			border: false,
			width: 190,
			collapsible: true,
			collapsed: false,
			animCollapse: true,
			bodyStyle: "background-color: rgba(2,56,129,0);",
			items: [
				new Ext.Panel({
					border: false,
					//                    title: 'Lista de Servicios',
					bodyStyle: "background-color: rgba(2,56,129,0.9);",
					height: 930,
					items: [this.dv_menu3],
				}),
			],
			listeners: {
				scope: this,
				mouseover: function (tp_logout, event) {
					tp_logout.expand(true);
				},
				mouseout: function (tp_logout, event) {
					tp_logout.collapse(true);
				},
			},
		});

		//        this.tp_logout.header.on('mouseover', function (event) {
		//            this.tp_logout.expand(true);
		//        }, this);

		//        this.tp_logout.el.on('mouseout', function (event) {
		//            if (!event.within(this.tp_logout.el, true)) {
		//                this.tp_logout.collapse(true);
		//            }
		//        }, this.tp_logout);

		this.tp_submodulos = new Ext.Panel({
			region: "east",
			title: "Historial de Usuario",
			width: 200,
			collapsible: true,
			split: true,
			animCollapse: true,
			collapsed: true,
			border: false,
			bodyStyle: "background-color: rgba(2,56,129,0);",
			items: [this.p_logo3],

			//            html:"<TABLE > <TR> <TD>ID: </TD> <TD rowspan='3'> <IMG SRC='images/user/user.jpg'> </TD>   </TR>  <TR> <TD>Nombre: </TD>  </TR> <TR>   <TD > </TD> </TR> <TR>  <TD >3 </TD> <TD ><a href='code/sys_logout'>salir</a> </TD></TR></TABLE>"
		});
		this.tp_window = new Ext.Panel({});
		this.tp_content = new Ext.TabPanel({
			region: "center",
			border: false,
			activeTab: 0,
			items: [
				(this.win = new Ext.Panel({
					xtype: "panel",
					title: "Inicio",
					html: '<div style="margin:60px 0 0 0" class="post-body entry-content" expr:id="&quot;post-body-&quot; + data:post.id" itemprop="articleBody" oncontextmenu="return false" ondragstart="return false" onmousedown="return false" onselectstart="return false"><center><img src="images/fondo.svg" /></center></div>',
					bodyStyle: "background-color: #023881;",
					//                    bodyStyle: 'background-color: #023881;',
					border: false,
					layout: {
						type: "table",
						columns: 2,
					},
					listeners: {
						scope: this,
						activate: function () {
							//                            if (this._server.socket.connected) {
							var info = {
								id: "",
								user: "", //con_usuid
								module: "inicio",
							};
							//                                this._server.emit('modAccess', info);
							//                            }
						},
					},
					defaults: {
						// applied to each contained panel
						bodyStyle: "padding:10px; margin:10px;",
					},
				})),
			],
		});
		this.main = new Ext.Viewport({
			layout: "border",
			items: [this.p_header, this.tp_content, this.p_footer, this.tp_logout],
		});
	},
	agrega_panel: function (r) {
		var info = {
			user: r.get("con_usuid"), //con_usuid
			module: r.get("men_desc"),
		};
		plataforma.tp_content
			.add(
				new Ext.Panel({
					id: r.get("men_id"),
					title: r.get("men_desc"),
					border: false,
					bodyStyle: "background-color: rgba(2,56,129,1.8);",
					autoLoad: {
						url: "code/sys_loadmod/html",
						params: {
							sys_modname: r.get("men_id"),
						},
						scripts: true,
					},
					listeners: {
						scope: this,
						// activate: function (panel) {
						// 	if (this._server.socket.connected) {
						// 		this._server.emit("modAccess", info);
						// 	}
						// },
					},
					closable: true,
					tabTip: "Modulo de " + r.get("men_desc"),
				})
			)
			.show();
		// this._server.emit("modAccess", info);
	},
	agrega_window: function (r) {
		plataforma.tp_window
			.add(
				new Ext.Window({
					title: r.get("men_desc"),
					region: "center",
					closable: true,
					//border:false,
					collapsible: true,
					draggable: true,
					resizable: false,
					constrain: true,
					height: 530,
					width: 650,
					layout: "border",
					items: [
						{
							xtype: "panel",
							id: r.get("men_id"),
							border: false,
							height: 520,
							width: 640,
							autoLoad: {
								url: "code/sys_loadmod/html",
								params: {
									sys_modname: r.get("men_id"),
								},
								scripts: true,
							},
						},
					],
				})
			)
			.show();
	},
};
Ext.ns.rpt = {
	win3: null,
	init: function (id, arc) {
		var id = id;
		var arc = arc;
		this.crea_controles(id, arc);
		this.win3.show();
	},
	crea_controles: function (id, arc) {
		var ide = id;
		var arc = arc;
		console.log("<[report]>&sys_report=" + arc + "&id=" + ide);
		this.win3 = new Ext.Window({
			title: "Reportes de Citas",
			width: 400,
			height: 500,
			maximizable: true,
			modal: true,
			closeAction: "close",
			resizable: true,
			html:
				"<iframe width='100%' height='100%' src='system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_adm&format=pdf&sys_report=" +
				arc +
				"&id=" +
				ide +
				"'></iframe>",
		});
	},
};
Ext.onReady(plataforma.init, plataforma);
