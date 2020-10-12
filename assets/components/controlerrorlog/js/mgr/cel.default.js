/* Main object */
const ControlErrorLog = function (config) {
	config = config || {};
	ControlErrorLog.superclass.constructor.call(this, config);
};

Ext.extend(ControlErrorLog, Ext.Component, {
	window: {}, combo: {}, config: {},
	fileIndex: 0,
	isEmpty: function(object) {
		for (let key in object)
			if (object.hasOwnProperty(key)) return false;
		return true;
	},
	toggle: function(elem) {
		let messageElem = elem.closest('tr').nextElementSibling;
		if (messageElem) {
			messageElem.classList.toggle('collapsed');
			elem.classList.toggle('celicon-plus-square');
			elem.classList.toggle('celicon-minus-square');
		}

	},
	toggleAll: function(elem) {
		let messages = document.querySelectorAll('table.error-log-table tr.error-description');
		messages.forEach(function (item, i) {
			if (controlErrorLog.collapsed) {
				item.classList.remove('collapsed');
				elem.classList.remove('celicon-plus-square');
				elem.classList.add('celicon-minus-square');
			} else {
				item.classList.add('collapsed');
				elem.classList.remove('celicon-minus-square');
				elem.classList.add('celicon-plus-square');
			}
		});
		let toggles = document.querySelectorAll('table.error-log-table i.toggle');
		toggles.forEach(function (item, i) {
			if (item.id === 'toggle-total') return;
			if (controlErrorLog.collapsed) {
				item.classList.remove('celicon-plus-square');
				item.classList.add('celicon-minus-square');
			} else {
				item.classList.remove('celicon-minus-square');
				item.classList.add('celicon-plus-square');
			}
		});
		controlErrorLog.collapsed = !controlErrorLog.collapsed;
	},
	showLog: function() {
		if (controlErrorLog.isEmpty(controlErrorLog.window)) {
			controlErrorLog.window = new MODx.Window({
				id: "errorlog-window",
				height: 500,
				width: 1000,
				closeAction: 'hide',
				title: _("error_log") ? _("error_log") : 'Error log',
				stateful: false,
				buttonAlign: "left",
				//bodyCssClass: controlErrorLog.config.format_output ? '' : 'raw-output',
				tbar: [{
					xtype: 'controlerrorlog-combo-logfiles',
					width: 250,
					emptyText: '',
					cls: 'cel-files',
					id: 'controlerrorlog-logfiles',
					listeners: {
						select: {
							fn: function (combo, rec, index) {
								if (index > 0 && controlErrorLog.config.allow_copy_deletion) {
									Ext.getCmp('errorlog-clear-btn').setDeleteText();
								} else {
									Ext.getCmp('errorlog-clear-btn').setDefaultText();
								}
								controlErrorLog.fileIndex = index;
								combo.baseParams.file = combo.value;
								controlErrorLog.window._refresh();
							}, scope: this
						}
					}
				}, {
					// xtype: "button",
					text: '<i class="icon icon-download"></i>',
					id: "errorlog-download-btn",
					tooltip: _('errorlog_download'),
					//hidden: !controlErrorLog.config.tooLarge,
					handler: function () {
						location.href = controlErrorLog.config.connector_url + "?action=mgr/download&HTTP_MODAUTH=" + MODx.siteId + "&file=" + Ext.getCmp('controlerrorlog-logfiles').getValue();
					},
					scope: this
				}, {
					xtype: 'label',
					id: 'errorlog-filesize-label',
					style: 'font-weight: bold'
				},  '->', {
					xtype: 'label',
					id: 'errorlog-count-label',
					style: 'font-weight: bold',
					templ: _('errorlog_total_messages'),
					text: _('errorlog_total_messages') + controlErrorLog.config.messages_count,
					hidden: !controlErrorLog.config.format_output

				}],
				items: [
					{
						xtype: controlErrorLog.config.format_output ? "panel" : 'textarea',
						name: "log",
						hideLabel: true,
						id: "window-errorlog-content",
						//style: 'margin-top: 10px;',
						//value: controlErrorLog.config.log,
						readOnly: true,
						height: controlErrorLog.config.format_output ? "99%" : "100%",
						width: "100%",
						hidden: controlErrorLog.config.tooLarge
					}, {
						html: _("errorlog_too_large") ? _("errorlog_too_large") : 'The error log  is too large to be viewed. You can download it via the button below.',
						id: "too-large-text",
						style: "margin-top: 10px;",
						border: false,
						hidden: !controlErrorLog.config.tooLarge
					}, {
						xtype: 'label',
						text: _("errorlog_last_lines") ? _("errorlog_last_lines", {last: controlErrorLog.config.last}) : 'The last '+controlErrorLog.config.last+' lines.',
						id: "errorlog-last-lines",
						style: "display:block;margin-top: 15px;",
						border: false,
						hidden: !(controlErrorLog.config.tooLarge && controlErrorLog.config.last > 0)
					}, {
						xtype: "textarea",
						name: "log-last",
						hideLabel: true,
						id: "errorlog-last-lines-content",
						value: controlErrorLog.config.log,
						readOnly: true,
						width: "99%",
						hidden: !(controlErrorLog.config.tooLarge && controlErrorLog.config.last > 0)
					}],
				buttons: [{
					text: '<i class="icon icon-copy"></i> ' + (_("cel_copy") ? _("cel_copy") : 'Make a copy'),
					id: "errorlog-copy-btn",
					handler: function () {
						controlErrorLog.window._copy();
					},
					scope: this
				}, '->' ,{
					text: '<i class="icon icon-refresh"></i> ' + (_("cel_refresh") ? _("cel_refresh") : 'Refresh'),
					id: "errorlog-refresh-btn",
					handler: function () {
						controlErrorLog.window._refresh();
					},
					scope: this
				}, {
					text: '',
					defaultText: '<i class="icon icon-eraser"></i> ' + (_("cel_clear") ? _("cel_clear") : 'Clear'),
					deleteText: '<i class="icon icon-remove"></i> ' + (_("cel_delete") ? _("cel_delete") : 'Delete'),
					id: "errorlog-clear-btn",
					disabled: controlErrorLog.config.empty,
					setDefaultText: function() {
						this.setText(this.defaultText).removeClass('btn-danger');
						return this;
					},
					setDeleteText: function() {
						this.setText(this.deleteText).addClass('btn-danger');
						return this;
					},
					listeners: {
						beforerender: {
							fn: function (btn) {
								btn.setText(btn.defaultText);
							}, scope: this
						}
					},
					handler: function () {
						MODx.Ajax.request({
							url: controlErrorLog.config.connector_url,
							params: {
								action: "mgr/clear",
								file:  Ext.get('controlerrorlog-logfiles').getValue()
							},
							listeners: {
								"success": {
									fn: function (r) {
										controlErrorLog.window._reset(r.object);
										if (r.object.isDeleted) {
											const combo = Ext.getCmp('controlerrorlog-logfiles');
											combo.setValue('error.log');
											combo.store.removeAt(controlErrorLog.fileIndex);
											Ext.getCmp('errorlog-clear-btn').setDefaultText();
											controlErrorLog.window._refresh();
										} else {
											Ext.getCmp('errorlog-filesize-label').setText(r.object.size);
										}
										if (r.object.name == 'error.log') {
											document.getElementById("errorlog-link").className = "celicon-check-circle";
										}
									}
								},
								"failure": {fn: function (r) {} }
							}
						});
					},
					scope: this
				}, {
					text: '<i class="icon icon-times"></i> ' + (_("cel_close") ? _("cel_close") : 'Close'),
					handler: function (w) {
						controlErrorLog.window.hide();
					},
					scope: this
				}],
				listeners: {
					show: {fn: function(w) {
							w._refresh();
						}, scope: this}
				},
				_reset: function(r) {
					const log = Ext.getCmp("window-errorlog-content");
					if (controlErrorLog.config.format_output) {
						document.querySelectorAll('table.error-log-table tbody tr').forEach(function (item){
							item.remove();
						});
					} else {
						log.setValue(r.log);
					}
					// controlErrorLog.config.log = r.log;
					Ext.getCmp("errorlog-clear-btn").disable();
					Ext.getCmp("errorlog-copy-btn").disable();
					if (controlErrorLog.config.tooLarge) {
						Ext.getCmp('errorlog-window').body.removeClass('raw-output');
						log.update(controlErrorLog.config.tpl);
						log.show();
						Ext.getCmp("too-large-text").hide();
						// Ext.getCmp("errorlog-download-btn").hide();
						Ext.getCmp("errorlog-last-lines").hide();
						Ext.getCmp("errorlog-last-lines-content").hide();
						controlErrorLog.config.tooLarge = false;
					}
					let countLabel = Ext.getCmp('errorlog-count-label');
					countLabel.setText(countLabel.templ + '0')
				},
				_refresh: function() {
					MODx.Ajax.request({
						url: controlErrorLog.config.connector_url,
						params: {
							action: "mgr/get",
							file: Ext.get('controlerrorlog-logfiles').getValue()
						},
						listeners: {
							"success": {
								fn: function (r) {
									controlErrorLog.config = r.object;
									//Ext.getCmp("window-errorlog-content").setValue(r.object.log);
									Ext.getCmp("errorlog-copy-btn").disable();
									if (controlErrorLog.config.empty) {
										Ext.getCmp("errorlog-clear-btn").disable();
										if (r.object.name == 'error.log') {
											document.getElementById("errorlog-link").className = "celicon-check-circle";
										} else if (controlErrorLog.config.allow_copy_deletion) {
											Ext.getCmp("errorlog-clear-btn").enable();
										}
									} else {
										Ext.getCmp("errorlog-clear-btn").enable();
										if (r.object.name == 'error.log') {
											document.getElementById("errorlog-link").className = "celicon-warning";
											Ext.getCmp("errorlog-copy-btn").enable();
										}
									}
									Ext.getCmp('errorlog-filesize-label').setText(r.object.size);
									let countLabel = Ext.getCmp('errorlog-count-label');
									if (controlErrorLog.config.tooLarge) {
										const errorlog_last_lines = _("errorlog_last_lines") ? _("errorlog_last_lines", {last: controlErrorLog.config.last}) : 'The last '+controlErrorLog.config.last+' lines.';
										Ext.getCmp('errorlog-window').body.addClass('raw-output');
										Ext.getCmp("window-errorlog-content").hide();
										Ext.getCmp("too-large-text").show();
										Ext.getCmp("errorlog-last-lines").setText(errorlog_last_lines).show();
										Ext.getCmp("errorlog-last-lines-content").setValue(r.object.log).show();
										countLabel.hide();
									} else {
										Ext.getCmp("window-errorlog-content").show();
										if (controlErrorLog.config.format_output) {
											Ext.getCmp("window-errorlog-content").update(r.object.log);
											Ext.getCmp('errorlog-window').body.removeClass('raw-output');
											countLabel.show();
											countLabel.setText(countLabel.templ + r.object.messages_count)
										} else {
											Ext.getCmp("window-errorlog-content").setValue(r.object.log).show();
										}
										Ext.getCmp("too-large-text").hide();
										Ext.getCmp("errorlog-last-lines").hide();
										Ext.getCmp("errorlog-last-lines-content").hide();
									}
									delete(controlErrorLog.config.log);
								}
							}
						}
					});
				},
				_copy: function() {
					MODx.Ajax.request({
						url: controlErrorLog.config.connector_url,
						params: {
							action: "mgr/copy"
						},
						listeners: {
							"success": {
								fn: function (r) {
									let file = r.object.file,
										Record = Ext.data.Record.create([{name: "id"}, {name: "name"}]);

									Ext.getCmp('controlerrorlog-logfiles').store.add(new Record({id: file, name: file}));
									if (r.message) {
										MODx.msg.alert(_('success'), r.message, Ext.emptyFn);
									}
								}
							},
							"failure": {fn: function (r) {} }
						}
					});
				}
			});
		}
		controlErrorLog.window.hidden ? controlErrorLog.window.show(Ext.EventObject.target) : controlErrorLog.window.hide();
	},
	getClass: function(empty) {
		return empty ? "celicon-check-circle" : "celicon-warning";
	},
	refreshLog: function() {
		MODx.Ajax.request({
			url: controlErrorLog.config.connector_url,
			params: {
				action: "mgr/get"
			},
			listeners: {
				success: {
					fn: function (r) {
						controlErrorLog.config.empty = r.object.empty;
						document.getElementById("errorlog-link").className = controlErrorLog.getClass(controlErrorLog.config.empty);
					}
				}
			}
		});
	}
});
// Ext.reg('control-errorlog', controlErrorLog);
controlErrorLog = new ControlErrorLog();

/* Combo */
controlErrorLog.combo.Logfiles = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		hideMode: 'offsets',
		autoScroll: true,
		maxHeight: 200,
		url: controlErrorLog.config.connector_url,
		baseParams: {
			action: 'mgr/getfiles'
		},
		name: 'logfiles',
		hiddenName: 'logfiles',
		// editable: false,
		value: 'error.log'
	});
	controlErrorLog.combo.Logfiles.superclass.constructor.call(this,config);
};
Ext.extend(controlErrorLog.combo.Logfiles, MODx.combo.ComboBox);
Ext.reg('controlerrorlog-combo-logfiles', controlErrorLog.combo.Logfiles);

Ext.onReady(function() {
	let usermenuUl = document.getElementById("modx-user-menu"),
		errorlogLi = document.createElement("LI"),
		title = _("errors_title") ? _("errors_title") : 'Open the error log in a modal window';

	errorlogLi.innerHTML = "<a id=\"errorlog-link\" class=\""+controlErrorLog.getClass(controlErrorLog.config.empty)+"\" href=\"javascript:controlErrorLog.showLog()\" title=\""+ title +"\"><i" +
		" class=\"celicon\"></i></a>";
	errorlogLi.className = "errorlog-li";
	usermenuUl.insertBefore(errorlogLi, usermenuUl.firstChild);
	if (controlErrorLog.config.auto_refresh) setInterval(controlErrorLog.refreshLog, controlErrorLog.config.refresh_freq);
});