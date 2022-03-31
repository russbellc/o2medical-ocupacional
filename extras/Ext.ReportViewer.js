Ext.ns('Ext.ReportViewer');
Ext.ReportViewer = Ext.extend(Ext.Window, {
    onRender: function() {
        this.bodyCfg = {
            tag: 'iframe',
            src: this.url+'&sys_report='+this.report,
            cls: this.bodyCls,
            style: {
                border: '0px none'
            }
        };
        Ext.ReportViewer.superclass.onRender.apply(this, arguments);
    }
});