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
			value: cel_config.log,
			readOnly: true,
			height: "97%",
			width: "99%",
			hidden: cel_config.tooLarge ? true : false
		},{
			html: "<p>"+_("error_log_too_large",{name: cel_config.name})+"</p>",
			id: "too-large-text",
			border: false,
			hidden: cel_config.tooLarge ? false : true
		},{
			xtype: "button",
			text: _("error_log_download",{size: cel_config.size}),
			cls: "primary-button",
			id: "error-log-download-btn",
			style: "margin-top: 15px;",
			hidden: cel_config.tooLarge ? false : true,
			handler: function () {location.href = MODx.config.connectors_url+"?action=system/errorlog/download&HTTP_MODAUTH="+MODx.siteId;},
			scope: this
		},{
			html: "<p>"+_("error_log_last_lines",{last: cel_config.last})+"</p>",
			id: "error-log-last-lines",
			style: "margin-top: 15px;",
			border: false,
			hidden: cel_config.tooLarge ? false : true
		},{
			xtype: "textarea",
			name: "log-last",
			hideLabel: true,
			id: "error-log-last-lines-content",
			value: cel_config.log,
			readOnly: true,
			height: "60%",
			width: "99%",
			hidden: cel_config.tooLarge ? false : true
		}],
		buttons: [{
			text: _("refresh"),
			id: "error-log-refresh-btn",
			handler: function () {
				MODx.Ajax.request({
					url: cel_config.connector_url
					,params: {
						action: "mgr/errorlog/get",
						last: cel_config.last
					}
					,listeners: {
						"success": {fn:function(r) {
							cel_config = r.object;
							Ext.getCmp("window-error-log-content").setValue(cel_config.log);
							if (cel_config.empty) {
								document.getElementById("errorlog-link").className = "errorlog-empty";
								Ext.getCmp("error-log-clear-btn").disable();
							} else {
								document.getElementById("errorlog-link").className = "errorlog-notempty";
								Ext.getCmp("error-log-clear-btn").enable();
							}
							if (cel_config.tooLarge) {
								Ext.getCmp("window-error-log-content").hide();
								Ext.getCmp("too-large-text").show();
								Ext.getCmp("error-log-download-btn").setText(_("error_log_download",{size: cel_config.size})).show();
								Ext.getCmp("error-log-last-lines").html = _("error_log_last_lines",{last: cel_config.last});
								Ext.getCmp("error-log-last-lines").show();
								Ext.getCmp("error-log-last-lines-content").setValue(cel_config.log).show();
							} else {
								Ext.getCmp("window-error-log-content").show();
								Ext.getCmp("too-large-text").hide();
								Ext.getCmp("error-log-download-btn").hide();
								Ext.getCmp("error-log-last-lines").hide();
								Ext.getCmp("error-log-last-lines-content").hide();
							}
						}}
					}
				});
			}, scope: this
		},{
			text: _("clear"),
			id: "error-log-clear-btn",
			disabled: cel_config.empty ? true : false,
			handler: function () {
				MODx.Ajax.request({
					url: MODx.config.connectors_url
					,params: {
						action: "system/errorlog/clear"
					}
					,listeners: {
						"success": {fn:function(r) {
							var log = Ext.getCmp("window-error-log-content");
							log.setValue("");
							cel_config.log = "";
							Ext.getCmp("error-log-clear-btn").disable();
							if (cel_config.tooLarge) {
								log.show();
								Ext.getCmp("too-large-text").hide();
								Ext.getCmp("error-log-download-btn").hide();
								cel_config.tooLarge = false;
							}
							document.getElementById("errorlog-link").className = "errorlog-empty";
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
	function getClass(empty){
		return empty ? "errorlog-empty" : "errorlog-notempty";
	}
	var usermenuUl = document.getElementById("modx-user-menu"),
		firstLi = usermenuUl.firstChild,
		errorlogLi = document.createElement("LI");

	errorlogLi.innerHTML = "<a id=\"errorlog-link\" class=\""+getClass(cel_config.empty)+"\" href=\"javascript:showLog()\" title=\""+_("errors_title")+"\"><i class=\"celicon\"></i></a>";
	errorlogLi.className = "errorlog-li";
	usermenuUl.insertBefore(errorlogLi, firstLi);
});