const logErrorPanel = document.getElementById('side-panel-wrapper');
document.getElementById('side-button-open').addEventListener("click", () => {
	logErrorPanel.classList.toggle('expanded');
});
document.getElementById('side-panel-close-button').addEventListener("click", () => {
	logErrorPanel.classList.toggle('expanded');
});
document.getElementById('side-button-refresh').addEventListener("click", function() {
	let i = this.children[0];
	i.classList.add('spin');
	setTimeout(() => {
		i.classList.remove('spin');
	}, 300);
	controlErrorLog.request('web/get');
});
document.getElementById('side-button-clear').addEventListener("click", () => {
	if (!controlErrorLog.empty) {
		controlErrorLog.request('web/clear');
	}
});

const logErrorPanelBody = document.querySelector('#side-panel-wrapper .side-panel-body');
const logErrorPanelFooter = document.querySelector('#side-panel-wrapper .side-panel-footer');

controlErrorLog = controlErrorLog || {};
controlErrorLog.toggle = function(elem) {
	let messageElem = elem.closest('tr').nextElementSibling;
	if (messageElem) {
		messageElem.classList.toggle('collapsed');
		elem.classList.toggle('celicon-plus-square');
		elem.classList.toggle('celicon-minus-square');
	}

}
controlErrorLog.toggleAll = function(elem) {
	if (controlErrorLog.empty) return;

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
}
controlErrorLog.setStatusIcons = function(empty) {
	let errorStatusIcon = document.getElementById('side-button-open');
	let errorClearIcon = document.getElementById('side-button-clear');
	if (empty) {
		errorStatusIcon.classList.remove('celicon-warning');
		errorStatusIcon.classList.add('side-button-open');
		errorClearIcon.classList.add('disabled');
	} else {
		errorStatusIcon.classList.remove('side-button-open');
		errorStatusIcon.classList.add('celicon-warning');
		errorClearIcon.classList.remove('disabled');
	}
}
controlErrorLog.request = function request(action) {
	let xhr = new XMLHttpRequest();

	xhr.open("POST", controlErrorLog.connectorUrl)
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
	xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	xhr.responseType = 'json';

	action = action || 'web/get';
	xhr.send("action=" + action + '&token=' + controlErrorLog.token);

	xhr.onload = function () {
		let responseObj = xhr.response;

		if (xhr.status === 200 && responseObj.success) {
			const format = controlErrorLog.config.format_output;
			let content = '';
			let preClass = '';
			let footerContent = '';

			if (responseObj.object.tooLarge) {
				content = responseObj.message;
				preClass = 'too-large'
			}
			content += format && !responseObj.object.tooLarge ? responseObj.object.log : `<pre class="${preClass}">${responseObj.object.log}</pre>`;

			if (format) {
				if (action === 'web/clear') {
					if (controlErrorLog.tooLarge) {
						logErrorPanelBody.innerHTML = controlErrorLog.config.tpl;
					} else {
						let tableBody = document.querySelector('table.error-log-table tbody');
						tableBody.innerHTML = '';
					}
				} else {
					logErrorPanelBody.innerHTML = content;
				}

				if (!responseObj.object.tooLarge) {
					footerContent = '<span>' + logErrorPanelFooter.getAttribute('data-records') + responseObj.object.messages_count + '</span>';
				}
			}
			logErrorPanelFooter.innerHTML = footerContent + responseObj.object.size;
			['tooLarge', 'size', 'empty', 'log', 'messages_count'].forEach(function(item) {
				controlErrorLog[item] = responseObj.object[item];
			});
			controlErrorLog.setStatusIcons(controlErrorLog.empty);
		} else {
			alert(xhr.response.message);
		}
	};
	xhr.onerror = function () {
		alert("Request error!");
	};
}

document.addEventListener("DOMContentLoaded", () => {
	controlErrorLog.request('web/get');
});
