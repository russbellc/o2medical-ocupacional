Ext.ns('Ext.ux.form');
        Ext.ux.form.SelectBox = Ext.extend(Ext.form.ComboBox, {
        constructor: function(config){
        this.searchResetDelay = 1000;
                config = config || {};
                config = Ext.apply(config || {}, {
                editable: false,
                        forceSelection: true,
                        rowHeight: false,
                        lastSearchTerm: false,
                        triggerAction: 'all',
                        mode: 'local'
                });
                Ext.ux.form.SelectBox.superclass.constructor.apply(this, arguments);
                this.lastSelectedIndex = this.selectedIndex || 0;
        },
                initEvents : function(){
                Ext.ux.form.SelectBox.superclass.initEvents.apply(this, arguments);
                        this.el.on('keydown', this.keySearch, this, true);
                        this.cshTask = new Ext.util.DelayedTask(this.clearSearchHistory, this);
                },
                keySearch : function(e, target, options) {
                var raw = e.getKey();
                        var key = String.fromCharCode(raw);
                        var startIndex = 0;
                        if (!this.store.getCount()) {
                return;
                }
                switch (raw) {
                case Ext.EventObject.HOME:
                        e.stopEvent();
                        this.selectFirst();
                        return;
                        case Ext.EventObject.END:
                        e.stopEvent();
                        this.selectLast();
                        return;
                        case Ext.EventObject.PAGEDOWN:
                        this.selectNextPage();
                        e.stopEvent();
                        return;
                        case Ext.EventObject.PAGEUP:
                        this.selectPrevPage();
                        e.stopEvent();
                        return;
                }
                if ((e.hasModifier() && !e.shiftKey) || e.isNavKeyPress() || e.isSpecialKey()) {
                return;
                }
                if (this.lastSearchTerm == key) {
                startIndex = this.lastSelectedIndex;
                }
                this.search(this.displayField, key, startIndex);
                        this.cshTask.delay(this.searchResetDelay);
                },
                onRender : function(ct, position) {
                this.store.on('load', this.calcRowsPerPage, this);
                        Ext.ux.form.SelectBox.superclass.onRender.apply(this, arguments);
                        if (this.mode == 'local') {
                this.initList();
                        this.calcRowsPerPage();
                }
                },
                onSelect : function(record, index, skipCollapse){
                if (this.fireEvent('beforeselect', this, record, index) !== false){
                this.setValue(record.data[this.valueField || this.displayField]);
                        if (!skipCollapse) {
                this.collapse();
                }
                this.lastSelectedIndex = index + 1;
                        this.fireEvent('select', this, record, index);
                }
                },
                afterRender : function() {
                Ext.ux.form.SelectBox.superclass.afterRender.apply(this, arguments);
                        if (Ext.isWebKit) {
                this.el.swallowEvent('mousedown', true);
                }
                this.el.unselectable();
                        this.innerList.unselectable();
                        this.trigger.unselectable();
                        this.innerList.on('mouseup', function(e, target, options) {
                        if (target.id && target.id == this.innerList.id) {
                        return;
                        }
                        this.onViewClick();
                        }, this);
                        this.mun(this.view, 'containerclick', this.onViewClick, this);
                        this.mun(this.view, 'click', this.onViewClick, this);
                        this.innerList.on('mouseover', function(e, target, options) {
                        if (target.id && target.id == this.innerList.id) {
                        return;
                        }
                        this.lastSelectedIndex = this.view.getSelectedIndexes()[0] + 1;
                                this.cshTask.delay(this.searchResetDelay);
                        }, this);
                        this.trigger.un('click', this.onTriggerClick, this);
                        this.trigger.on('mousedown', function(e, target, options) {
                        e.preventDefault();
                                this.onTriggerClick();
                        }, this);
                        this.on('collapse', function(e, target, options) {
                        Ext.getDoc().un('mouseup', this.collapseIf, this);
                        }, this, true);
                        this.on('expand', function(e, target, options) {
                        Ext.getDoc().on('mouseup', this.collapseIf, this);
                        }, this, true);
                },
                clearSearchHistory : function() {
                this.lastSelectedIndex = 0;
                        this.lastSearchTerm = false;
                },
                selectFirst : function() {
                this.focusAndSelect(this.store.data.first());
                },
                selectLast : function() {
                this.focusAndSelect(this.store.data.last());
                },
                selectPrevPage : function() {
                if (!this.rowHeight) {
                return;
                }
                var index = Math.max(this.selectedIndex - this.rowsPerPage, 0);
                        this.focusAndSelect(this.store.getAt(index));
                },
                selectNextPage : function() {
                if (!this.rowHeight) {
                return;
                }
                var index = Math.min(this.selectedIndex + this.rowsPerPage, this.store.getCount() - 1);
                        this.focusAndSelect(this.store.getAt(index));
                },
                search : function(field, value, startIndex) {
                field = field || this.displayField;
                        this.lastSearchTerm = value;
                        var index = this.store.find.apply(this.store, arguments);
                        if (index !== - 1) {
                this.focusAndSelect(index);
                }
                },
                focusAndSelect : function(record) {
                var index = Ext.isNumber(record) ? record : this.store.indexOf(record);
                        this.select(index, this.isExpanded());
                        this.onSelect(this.store.getAt(index), index, this.isExpanded());
                },
                calcRowsPerPage : function() {
                if (this.store.getCount()) {
                this.rowHeight = Ext.fly(this.view.getNode(0)).getHeight();
                        this.rowsPerPage = this.maxHeight / this.rowHeight;
                } else {
                this.rowHeight = false;
                }
                }

        });
        Ext.reg('selectbox', Ext.ux.form.SelectBox);
        Ext.ux.SelectBox = Ext.ux.form.SelectBox; Ext.ns('Ext.ux.form');
        Ext.ux.form.SearchField = Ext.extend(Ext.form.TwinTriggerField, {
        initComponent : function(){
        Ext.ux.form.SearchField.superclass.initComponent.call(this);
                this.on('specialkey', function(f, e){
                if (e.getKey() == e.ENTER){
                this.onTrigger2Click();
                }
                }, this);
        },
                validationEvent:false,
                validateOnBlur:false,
                trigger1Class:'x-form-clear-trigger',
                trigger2Class:'x-form-search-trigger',
                hideTrigger1:true,
                width:180,
                hasSearch : false,
                paramName : 'query',
                onTrigger1Click : function(){
                if (this.hasSearch){
                this.el.dom.value = '';
                        var o = {start: 0};
                        this.store.baseParams = this.store.baseParams || {};
                        this.store.baseParams[this.paramName] = '';
                        this.store.reload({params:o});
                        this.triggers[0].hide();
                        this.hasSearch = false;
                }
                },
                onTrigger2Click : function(){
                var v = this.getRawValue();
                        if (v.length < 1){
                this.onTrigger1Click();
                        return;
                }
                var o = {start: 0};
                        this.store.baseParams = this.store.baseParams || {};
                        this.store.baseParams[this.paramName] = v;
                        this.store.reload({params:o});
                        this.hasSearch = true;
                        this.triggers[0].show();
                }
        }); Ext.ns('Ext.ux.grid');
        Ext.ux.grid.RowExpander = Ext.extend(Ext.util.Observable, {
        expandOnEnter : true,
                expandOnDblClick : true,
                header : '',
                width : 20,
                sortable : false,
                fixed : true,
                hideable: false,
                menuDisabled : true,
                dataIndex : '',
                id : 'expander',
                lazyRender : true,
                enableCaching : true,
                constructor: function(config){
                Ext.apply(this, config);
                        this.addEvents({
                        beforeexpand: true,
                                expand: true,
                                beforecollapse: true,
                                collapse: true
                        });
                        Ext.ux.grid.RowExpander.superclass.constructor.call(this);
                        if (this.tpl){
                if (typeof this.tpl == 'string'){
                this.tpl = new Ext.Template(this.tpl);
                }
                this.tpl.compile();
                }
                this.state = {};
                        this.bodyContent = {};
                },
                getRowClass : function(record, rowIndex, p, ds){
                p.cols = p.cols - 1;
                        var content = this.bodyContent[record.id];
                        if (!content && !this.lazyRender){
                content = this.getBodyContent(record, rowIndex);
                }
                if (content){
                p.body = content;
                }
                /*console.log(record.data.emp_sexo);
                 console.log(rowIndex);*/
                //console.log(record.data.validado);
                //return this.state[record.id] ? 'x-grid3-row-expanded' : 'x-grid3-row-collapsed';
                if (record.data.validado == 1){ return 'Gri-val'; }
                else if (record.data.validado == 2){ return 'Gri-obs'; }
                else if (record.data.validado == 3){ return 'Gri-noap'; }
                else return this.state[record.id] ? 'x-grid3-row-expanded' : 'x-grid3-row-collapsed';
                },
                init : function(grid){
                this.grid = grid;
                        var view = grid.getView();
                        view.getRowClass = this.getRowClass.createDelegate(this);
                        view.enableRowBody = true;
                        grid.on('render', this.onRender, this);
                        grid.on('destroy', this.onDestroy, this);
                },
                onRender: function() {
                var grid = this.grid;
                        var mainBody = grid.getView().mainBody;
                        mainBody.on('mousedown', this.onMouseDown, this, {delegate: '.x-grid3-row-expander'});
                        if (this.expandOnEnter) {
                this.keyNav = new Ext.KeyNav(this.grid.getGridEl(), {
                'enter' : this.onEnter,
                        scope: this
                });
                }
                if (this.expandOnDblClick) {
                grid.on('rowdblclick', this.onRowDblClick, this);
                }
                },
                onDestroy: function() {
                if (this.keyNav){
                this.keyNav.disable();
                        delete this.keyNav;
                }
                var mainBody = this.grid.getView().mainBody;
                        if (mainBody){
                mainBody.un('mousedown', this.onMouseDown, this);
                }
                },
                onRowDblClick: function(grid, rowIdx, e) {
                this.toggleRow(rowIdx);
                },
                onEnter: function(e) {
                var g = this.grid;
                        var sm = g.getSelectionModel();
                        var sels = sm.getSelections();
                        for (var i = 0, len = sels.length; i < len; i++) {
                var rowIdx = g.getStore().indexOf(sels[i]);
                        this.toggleRow(rowIdx);
                }
                },
                getBodyContent : function(record, index){
                if (!this.enableCaching){
                return this.tpl.apply(record.data);
                }
                var content = this.bodyContent[record.id];
                        if (!content){
                content = this.tpl.apply(record.data);
                        this.bodyContent[record.id] = content;
                }
                return content;
                },
                onMouseDown : function(e, t){
                e.stopEvent();
                        var row = e.getTarget('.x-grid3-row');
                        this.toggleRow(row);
                },
                renderer : function(v, p, record){
                p.cellAttr = 'rowspan="2"';
                        return '<div class="x-grid3-row-expander">&#160;</div>';
                },
                beforeExpand : function(record, body, rowIndex){
                if (this.fireEvent('beforeexpand', this, record, body, rowIndex) !== false){
                if (this.tpl && this.lazyRender){
                body.innerHTML = this.getBodyContent(record, rowIndex);
                }
                return true;
                } else{
                return false;
                }
                },
                toggleRow : function(row){
                if (typeof row == 'number'){
                row = this.grid.view.getRow(row);
                }
                this[Ext.fly(row).hasClass('x-grid3-row-collapsed') ? 'expandRow' : 'collapseRow'](row);
                },
                expandRow : function(row){
                if (typeof row == 'number'){
                row = this.grid.view.getRow(row);
                }
                var record = this.grid.store.getAt(row.rowIndex);
                        var body = Ext.DomQuery.selectNode('tr:nth(2) div.x-grid3-row-body', row);
                        if (this.beforeExpand(record, body, row.rowIndex)){
                this.state[record.id] = true;
                        Ext.fly(row).replaceClass('x-grid3-row-collapsed', 'x-grid3-row-expanded');
                        this.fireEvent('expand', this, record, body, row.rowIndex);
                }
                },
                collapseRow : function(row){
                if (typeof row == 'number'){
                row = this.grid.view.getRow(row);
                }
                var record = this.grid.store.getAt(row.rowIndex);
                        var body = Ext.fly(row).child('tr:nth(1) div.x-grid3-row-body', true);
                        if (this.fireEvent('beforecollapse', this, record, body, row.rowIndex) !== false){
                this.state[record.id] = false;
                        Ext.fly(row).replaceClass('x-grid3-row-expanded', 'x-grid3-row-collapsed');
                        this.fireEvent('collapse', this, record, body, row.rowIndex);
                }
                }
        });
        Ext.preg('rowexpander', Ext.ux.grid.RowExpander);
        Ext.grid.RowExpander = Ext.ux.grid.RowExpander; var isIE = (navigator.appVersion.indexOf("MSIE") != - 1
                ) ? true : false;
        var isWin = (navigator.appVersion.toLowerCase().indexOf("win") != - 1) ? true : false;
        var isOpera = (navigator.userAgent.indexOf("Opera") != - 1) ? true : false;
        function ControlVersion()
                {
                var version;
                        var axo;
                        var e;
                        try {
                        axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
                                version = axo.GetVariable("$version");
                        } catch (e) {
                }

                if (!version)
                {
                try {
                axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
                        version = "WIN 6,0,21,0";
                        axo.AllowScriptAccess = "always";
                        version = axo.GetVariable("$version");
                } catch (e) {
                }
                }
                if (!version)
                {
                try {
                axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
                        version = axo.GetVariable("$version");
                } catch (e) {
                }
                }
                if (!version)
                {
                try {
                axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
                        version = "WIN 3,0,18,0";
                } catch (e) {
                }
                }
                if (!version)
                {
                try {
                axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
                        version = "WIN 2,0,0,11";
                } catch (e) {
                version = - 1;
                }
                }

                return version;
                        }
        function GetSwfVer(){
        var flashVer = - 1;
                if (navigator.plugins != null && navigator.plugins.length > 0) {
        if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
        var swVer2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
                var flashDescription = navigator.plugins["Shockwave Flash" + swVer2].description;
                var descArray = flashDescription.split(" ");
                var tempArrayMajor = descArray[2].split(".");
                var versionMajor = tempArrayMajor[0];
                var versionMinor = tempArrayMajor[1];
                var versionRevision = descArray[3];
                if (versionRevision == "") {
        versionRevision = descArray[4];
        }
        if (versionRevision[0] == "d") {
        versionRevision = versionRevision.substring(1);
        } else if (versionRevision[0] == "r") {
        versionRevision = versionRevision.substring(1);
                if (versionRevision.indexOf("d") > 0) {
        versionRevision = versionRevision.substring(0, versionRevision.indexOf("d"));
        }
        }
        var flashVer = versionMajor + "." + versionMinor + "." + versionRevision;
        }
        }
        else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != - 1) flashVer = 4;
                else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != - 1) flashVer = 3;
                else if (navigator.userAgent.toLowerCase().indexOf("webtv") != - 1) flashVer = 2;
                else if (isIE && isWin && !isOpera) {
        flashVer = ControlVersion();
        }
        return flashVer;
                }
        function DetectFlashVer(reqMajorVer, reqMinorVer, reqRevision)
                {
                versionStr = GetSwfVer();
                        if (versionStr == - 1) {
                return false;
                } else if (versionStr != 0) {
                if (isIE && isWin && !isOpera) {
                tempArray = versionStr.split(" "); // ["WIN", "2,0,0,11"]
                        tempString = tempArray[1]; // "2,0,0,11"
                        versionArray = tempString.split(","); // ['2', '0', '0', '11']
                } else {
                versionArray = versionStr.split(".");
                }
                var versionMajor = versionArray[0];
                        var versionMinor = versionArray[1];
                        var versionRevision = versionArray[2];
                        if (versionMajor > parseFloat(reqMajorVer)) {
                return true;
                } else if (versionMajor == parseFloat(reqMajorVer)) {
                if (versionMinor > parseFloat(reqMinorVer))
                        return true;
                        else if (versionMinor == parseFloat(reqMinorVer)) {
                if (versionRevision >= parseFloat(reqRevision))
                        return true;
                }
                }
                return false;
                }
                }
        function AC_AddExtension(src, ext)
                {
                if (src.indexOf('?') != - 1)
                        return src.replace(/\?/, ext + '?');
                        else
                        return src + ext;
                        }
        function AC_Generateobj(objAttrs, params, embedAttrs)
                {
                var str = '';
                        if (isIE && isWin && !isOpera)
                {
                str += '<object ';
                        for (var i in objAttrs)
                {
                str += i + '="' + objAttrs[i] + '" ';
                }
                str += '>';
                        for (var i in params)
                {
                str += '<param name="' + i + '" value="' + params[i] + '" /> ';
                }
                str += '</object>';
                }
                else
                {
                str += '<embed ';
                        for (var i in embedAttrs)
                {
                str += i + '="' + embedAttrs[i] + '" ';
                }
                str += '> </embed>';
                }

                document.write(str);
                        }
        function AC_FL_RunContent(){
        var ret =
                AC_GetArgs
                (arguments, ".swf", "movie", "clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
                        , "application/x-shockwave-flash"
                        );
                AC_Generateobj(ret.objAttrs, ret.params, ret.embedAttrs);
                }
        function AC_SW_RunContent(){
        var ret =
                AC_GetArgs
                (arguments, ".dcr", "src", "clsid:166B1BCA-3F9C-11CF-8075-444553540000"
                        , null
                        );
                AC_Generateobj(ret.objAttrs, ret.params, ret.embedAttrs);
                }
        function AC_GetArgs(args, ext, srcParamName, classid, mimeType){
        var ret = new Object();
                ret.embedAttrs = new Object();
                ret.params = new Object();
                ret.objAttrs = new Object();
                for (var i = 0; i < args.length; i = i + 2){
        var currArg = args[i].toLowerCase();
                switch (currArg){
        case "classid":
                break;
                case "pluginspage":
                ret.embedAttrs[args[i]] = args[i + 1];
                break;
                case "src":
                case "movie":
                args[i + 1] = AC_AddExtension(args[i + 1], ext);
                ret.embedAttrs["src"] = args[i + 1];
                ret.params[srcParamName] = args[i + 1];
                break;
                case "onafterupdate":
                case "onbeforeupdate":
                case "onblur":
                case "oncellchange":
                case "onclick":
                case "ondblClick":
                case "ondrag":
                case "ondragend":
                case "ondragenter":
                case "ondragleave":
                case "ondragover":
                case "ondrop":
                case "onfinish":
                case "onfocus":
                case "onhelp":
                case "onmousedown":
                case "onmouseup":
                case "onmouseover":
                case "onmousemove":
                case "onmouseout":
                case "onkeypress":
                case "onkeydown":
                case "onkeyup":
                case "onload":
                case "onlosecapture":
                case "onpropertychange":
                case "onreadystatechange":
                case "onrowsdelete":
                case "onrowenter":
                case "onrowexit":
                case "onrowsinserted":
                case "onstart":
                case "onscroll":
                case "onbeforeeditfocus":
                case "onactivate":
                case "onbeforedeactivate":
                case "ondeactivate":
                case "type":
                case "codebase":
                case "id":
                ret.objAttrs[args[i]] = args[i + 1];
                break;
                case "width":
                case "height":
                case "align":
                case "vspace":
                case "hspace":
                case "class":
                case "title":
                case "accesskey":
                case "name":
                case "tabindex":
                ret.embedAttrs[args[i]] = ret.objAttrs[args[i]] = args[i + 1];
                break;
                default:
                ret.embedAttrs[args[i]] = ret.params[args[i]] = args[i + 1];
        }
        }
        ret.objAttrs["classid"] = classid;
                if (mimeType) ret.embedAttrs["type"] = mimeType;
                return ret;
                }QoDesk.OdontoFlas = Ext.extend(Ext.app.Module, {
        id: 'odonF-win'
                , type: 'odonF/win'
                , init : function(){this.locale = QoDesk.OdontoFlas.Locale; },
                createWindow : function(){
                var myRut = new Array(); myRut[0] = 0; var tmp = 0;
                        var d = this.app.getDesktop();
                        var winIag = d.getWindow(this.id);
                        if (winIag){} else{
                var store_empr = new Ext.data.JsonStore({
                remoteSort: true,
                        url : this.app.connection,
                        baseParams:{ method: 'load', moduleId: this.id },
                        root:'data',
                        totalProperty: 'Total',
                        method:'POST',
                        listeners: {
                        'beforeload' : function(){
                        if (tmp == 0) { tmp++; }
                        else { this.baseParams.qt = Ext.getCmp('cbo_odonf').getValue(); }
                        }
                        },
                        fields:[{name:'validado'},
                        {name:'cf_id'},
                        {name:'cf_fecha'},
                        {name:'cf_ruc'},
                        {name:'nombre'},
                        {name:'te_descripcion'},
                        {name:'id_ruta'},
                        {name:'car_razon_social'},
                        {name:'emp_nro_documento'},
                        {name:'emp_telef_contacto'},
                        {name:'emp_telef_emergencia'},
                        {name:'emp_lugar_nacim'},
                        {name:'est_civil_descripcion'},
                        {name:'grado_descripcion'},
                        {name:'emp_sexo'},
                        {name:'emp_cel_contact'},
                        {name:'emp_domicilio'},
                        {name:'cf_acti_realizar'},
                        {name:'emp_fecha_nacim'},
                        {name:'EDAD_ACTUAL'}]
                });
                        store_empr.setDefaultSort('cf_id', 'DESC');
                        store_empr.load({params:{start:0, limit:35}});
                        var expander = new Ext.ux.grid.RowExpander({
                        tpl : new Ext.XTemplate(
                                '<img src="resources/images/MO.png" />',
                                '<p><b>Actividad:</b> "{cf_acti_realizar}"</p>',
                                '<p><b>Tipo de Ficha:</b> "{te_descripcion}"</p>',
                                '<p><b>Fecha:</b> {cf_fecha}  </p>',
                                '<p><b>Telefono:</b> {emp_telef_contacto}</p>',
                                '<p><b>Telefono de Emergencia:</b> {emp_telef_emergencia}</p>',
                                '<p><b>Ruc Emplesa:</b> {cf_ruc}</p>')
                        });
                        winIag = d.createWindow({
                        animCollapse: false
                                , id: this.id
                                , height: 320
                                , iconCls: 'odonF-win'
                                , layout: 'fit'
                                , title:  'Odontologia'
                                , width: 680
                                , items:  new Ext.grid.GridPanel({
                                store: store_empr
                                        , loadMask : true
                                        , listeners : {
                                        'rowclick': function(grid, index, rec){
                                        cur_row = grid.getStore().getAt(index);
                                                myRut[0] = cur_row.get('cf_id');
                                                myRut[1] = cur_row.get('emp_fecha_nacim');
                                                myRut[2] = cur_row.get('nombre');
                                                myRut[3] = cur_row.get('emp_actividad');
                                                myRut[4] = cur_row.get('emp_cel_contact');
                                                myRut[5] = cur_row.get('te_descripcion');
                                                myRut[6] = cur_row.get('car_razon_social');
                                                myRut[7] = cur_row.get('emp_nro_documento');
                                                myRut[8] = cur_row.get('emp_telef_contacto');
                                                myRut[9] = cur_row.get('emp_telef_emergencia');
                                                myRut[10] = cur_row.get('est_civil_descripcion');
                                                myRut[11] = cur_row.get('grado_descripcion');
                                                myRut[12] = cur_row.get('EDAD_ACTUAL');
                                                myRut[13] = cur_row.get('emp_domicilio');
                                                myRut[14] = cur_row.get('cf_acti_realizar');
                                                myRut[15] = cur_row.get('emp_fecha_nacim');
                                                myRut[17] = "var0=" + myRut[0] + "&var1=" + myRut[1] + "&var2=" + myRut[2] + "&var3=" + myRut[3] + "&var4=" + myRut
                                                [4] + "&var5=" + myRut[5] + "&var6=" + myRut[6] + "&var7=" + myRut[7] + "&var8=" + myRut[8] + "&var9=" + myRut[9] + "&var10
                                                = "+myRut[10]+" & var11 = "+myRut[11]+" & var12 = "+myRut[12];
                                        }
                                        }
                                , disabled: this.app.isAllowedTo('load', this.id) ? false : true
                                        , cm: new Ext.grid.ColumnModel({
                                        defaults: { width: 20, sortable: true },
                                                columns: [
                                                        expander
                                                        , {width : 10, sortable : true, dataIndex: 'validado'
                                                                , renderer: function(val, meta, record, rowIndex, colIndex, store){
                                                                if (val == 'x') { meta.css += 'Styley'; } else{ meta.css += 'Stylex'; }
                                                                }
                                                        }, {id :'cf_id', header : 'Ficha', width : 35, sortable : true, dataIndex: 'cf_id'}
                                                , {header : 'Dni', width : 40, sortable : true, dataIndex: 'emp_nro_documento'}
                                                , {header : 'Nombre', width : 80, sortable : true, dataIndex: 'nombre'}
                                                , {header : 'Sexo', width : 15, sortable : true, dataIndex: 'emp_sexo'
                                                        , renderer: function(val, meta, record, rowIndex, colIndex, store){
                                                        if (val == 'M') { meta.css += 'M'; } else{ meta.css += 'F'; }
                                                        }
                                                }, {header : 'Emplesa', width : 80, sortable : true, dataIndex: 'car_razon_social'}
                                                , {header : 'Validacion', width : 80, sortable : true, dataIndex: 'validado'
                                                        , renderer: function(val, meta, record, rowIndex, colIndex, store){
                                                        if (val == 0) { return 'Ninguno'; } if (val == 1){ return 'Validado'; }if (val == 2){ return 'Observado'
                                                                ; }if (val == 3){ return 'No Apto'; }
                                                        }
                                                }]
                                        }),
                                        viewConfig: { forceFit:true }
                                , tbar : [ 'Buscar Registor por '
                                        , {xtype: 'tbseparator'}
                                , new Ext.ux.SelectBox({
                                listClass : 'x-combo-list-small'
                                        , width : 120
                                        , id : 'cbo_odonf'
                                        , name:'cbo_odonf'
                                        , store: new Ext.data.ArrayStore({ fields: ['data', 'text'], data : [['empl', 'Empleado'], ['doc'
                                                , 'Dni'], ['fic', 'Ficha']]})
                                        , displayField : 'text'
                                        , valueField:'data'
                                })
                                        , new Ext.ux.form.SearchField({ width : 240, store : store_empr, paramName : 'q'})
                                        , {xtype: 'tbfill'}
                                , {text: 'Registrar', iconCls: 'dav-modif', handler : function(){
                                if (myRut[0] == 0) { Ext.MessageBox.alert('Error', 'Seleccione Un Empleado') }
                                else{
                                var venodon;
                                        //alert(myRut[17]);
                                        if (!venodon){
                                venodon = new Ext.Window({
                                title:'Paciente ' + myRut[2], /*modal:true,*/closeAction:'hide', iconCls: 'odonF-win',
                                        html: "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download
                                        .macromedia.com / pub / shockwave / cabs / flash / swflash.cab#version = 9, 0, 28, 0' width='720' height='420'>	<param
                                        name = 'movie' value = 'Odonto/Odonto.swf?"+myRut[17]+"' / > < param name = 'quality' value = 'high' / > < param name
                                        = 'allowFullScreen' value = 'true' / > < embed src = 'Odonto/Odonto.swf?"+myRut[17]+"' width = '700' height = '400'
                                        quality = 'high' pluginspage = 'http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash'
                                        type = 'application/x-shockwave-flash' allowfullscreen = 'true' > < /embed></object > "
                                });
                                }venodon.show();
                                }
                                }}, {text: 'Reporte', iconCls: 'dav-repor', handler : function(){
                                if (myRut[0] == 0){ Ext.MessageBox.alert('Error', 'Seleccione Un Empleado') }
                                else{
                                var webR = new Ext.Window({
                                width : 650,
                                        height : 400,
                                        //modal : true,						
                                        title : 'Reporte Odontologico',
                                        border : false,
                                        closable : true,
                                        maximizable : true,
                                        resizable : true,
                                        html: "<iframe width='100%' height='100%' src='Reporte/odon-win/reporte2.php?id=" + myRut[0] + "'
                                        > < /iframe>" 
                                });
                                        webR.show();
                                }
                                }}, {text: 'Reporte2', iconCls: 'dav-repor', handler : function(){
                                if (myRut[0] == 0){ Ext.MessageBox.alert('Error', 'Seleccione Un Empleado') }
                                else{
                                var webR = new Ext.Window({
                                width : 650,
                                        height : 400,
                                        //modal : true,						
                                        title : 'Reporte Odontologico',
                                        border : false,
                                        closable : true,
                                        maximizable : true,
                                        resizable : true,
                                        html: "<iframe width='100%' height='100%' src='Reporte/odon-win/reporte.php?id=" + myRut[0] + "'
                                        > < /iframe>" 
                                });
                                        webR.show();
                                }
                                }}]
                                        , bbar : new Ext.PagingToolbar({
                                        store : store_empr
                                                , displayInfo : true
                                                , displayMsg : '{0} - {1} de {2} Pacientes'
                                                , emptyMsg : 'No hay Registros'
                                                , pageSize : 35
                                        })
                                        , columnLines: true
                                        , plugins: expander
                                        , autoScroll : true
                                })
                        });
                }
                winIag.show();
                }
        });