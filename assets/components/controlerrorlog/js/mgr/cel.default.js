function showLog(){
	var w = new Ext.Window({
		id: "errorlog-window",
		height: 400,
		width: 1200,
		title: _("error_log"),
		buttonAlign: "right",
		items: [{
			xtype: "textarea",
			name: "log",
			hideLabel: true,
			id: "window-error-log-content",
			value: config.log,
			readOnly: true,
			height: "97%",
			width: "99%",
			hidden: config.tooLarge ? true : false
		},{
			html: "<p>"+_("error_log_too_large",{name: config.name})+"</p>",
			id: "too-large-text",
			border: false,
			hidden: config.tooLarge ? false : true
		},{
			xtype: "button",
			text: _("error_log_download",{size: config.size}),
			cls: "primary-button",
			id: "error-log-download-btn",
			style: "margin-top: 15px;",
			hidden: config.tooLarge ? false : true,
			handler: function () {location.href = MODx.config.connectors_url+"?action=system/errorlog/download&HTTP_MODAUTH="+MODx.siteId;},
			scope: this
		}],
		buttons: [{
			text: _("error_log"),
			handler: function () { location.href = "?a=system/event" },
			scope: this
		}, {
			text: _("refresh"),
			id: "error-log-refresh-btn",
			handler: function () {
				MODx.Ajax.request({
					url: config.connector_url
					,params: {
						action: "mgr/errorlog/get"
					}
					,listeners: {
						"success": {fn:function(r) {
							config = r.object;
							Ext.getCmp("window-error-log-content").setValue(config.log);
							//document.getElementById("errorlog-result").innerHTML = config.empty ? _("no") : _("yes");
							if (config.empty) {
								document.getElementById("errorlog-result").innerHTML = _("no");
								Ext.getCmp("error-log-clear-btn").disable();
							} else {
								document.getElementById("errorlog-result").innerHTML = _("yes");
								Ext.getCmp("error-log-clear-btn").enable();
							}
							if (config.tooLarge) {
								Ext.getCmp("window-error-log-content").hide();
								Ext.getCmp("too-large-text").show();
								Ext.getCmp("error-log-download-btn").setText(_("error_log_download",{size: config.size})).show();
							} else {
								Ext.getCmp("window-error-log-content").show();
								Ext.getCmp("too-large-text").hide();
								Ext.getCmp("error-log-download-btn").hide();
							}
						}}
					}
				});
			}, scope: this
		},{
			text: _("clear"),
			id: "error-log-clear-btn",
			disabled: config.empty ? true : false,
			handler: function () {
				MODx.Ajax.request({
					url: MODx.config.connectors_url
					,params: {
						action: "system/errorlog/clear"
					}
					,listeners: {
						"success": {fn:function(r) {
							var log = Ext.getCmp("window-error-log-content");
							log.setValue(" ");
							config.log = " ";
							Ext.getCmp("error-log-clear-btn").disable();
							if (config.tooLarge) {
								log.show();
								Ext.getCmp("too-large-text").hide();
								Ext.getCmp("error-log-download-btn").hide();
								config.tooLarge = false;
							}
							document.getElementById("errorlog-result").innerHTML = _("no");
						}}
					}
				});
			}, scope: this
		},{
			text: _("close"),
			handler: function () { w.close(); },
			scope: this
		}]
	}).show();

}

Ext.onReady(function() {
	function checkErrors(empty){
		return empty ? _("no") : _("yes");
	}
	var usermenuUl = document.getElementById("modx-user-menu"),
		firstLi = usermenuUl.firstChild,
		errorlogLi = document.createElement("LI");

	errorlogLi.innerHTML = "<a href=\"javascript:void(0)\" onclick=\"return false;\">"+_("errors")+":</a><a id=\"errorlog-result\" href=\"javascript:showLog()\" title=\""+_("errors_title")+"\">"+checkErrors(config.empty)+"</a>";
	errorlogLi.className = "errorlog-li";
	usermenuUl.insertBefore(errorlogLi, firstLi);
});