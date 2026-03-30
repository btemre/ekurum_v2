/**
 * EDTS Header Bildirimler: dropdown açıldığında API'den veri çeker ve listeleri doldurur.
 */
(function () {
	'use strict';

	var wrapper = document.getElementById('edts_header_notifications_wrapper');
	if (!wrapper) return;

	var apiUrl = wrapper.getAttribute('data-notifications-url');
	if (!apiUrl) return;
	var baseUrl = apiUrl.replace(/\/home\/api_header_notifications\/?$/, '/') || '/';
	if (baseUrl && baseUrl.indexOf('/') === 0 && baseUrl.length > 1) {
		baseUrl = baseUrl.replace(/\/$/, '');
	} else if (baseUrl === '/') {
		baseUrl = '';
	}

	var countEl = document.getElementById('edts_header_notification_count');
	var badgeEl = document.getElementById('edts_header_notification_badge');
	var menuEl = document.getElementById('edts_header_notifications_menu');
	var triggerEl = document.getElementById('edts_header_notifications_trigger');

	var alertList = document.getElementById('edts_notifications_alert_list');
	var alertEmpty = document.getElementById('edts_notifications_alert_empty');
	var updateList = document.getElementById('edts_notifications_update_list');
	var updateEmpty = document.getElementById('edts_notifications_update_empty');
	var logList = document.getElementById('edts_notifications_log_list');
	var logEmpty = document.getElementById('edts_notifications_log_empty');
	var licenseLoading = document.getElementById('edts_notifications_license_loading');
	var licenseBody = document.getElementById('edts_notifications_license_body');
	var licenseEmpty = document.getElementById('edts_notifications_license_empty');

	function escapeHtml(str) {
		if (str == null) return '';
		var div = document.createElement('div');
		div.textContent = str;
		return div.innerHTML;
	}

	function formatDate(dateStr) {
		if (!dateStr) return '';
		var d = new Date(dateStr);
		if (isNaN(d.getTime())) return dateStr;
		var now = new Date();
		var diff = (now - d) / 1000;
		if (diff < 60) return 'Az önce';
		if (diff < 3600) return Math.floor(diff / 60) + ' dakika';
		if (diff < 86400) return Math.floor(diff / 3600) + ' saat';
		if (diff < 604800) return Math.floor(diff / 86400) + ' gün';
		var day = ('0' + d.getDate()).slice(-2);
		var month = ('0' + (d.getMonth() + 1)).slice(-2);
		var year = d.getFullYear();
		return day + '.' + month + '.' + year;
	}

	function renderAlerts(items) {
		if (!alertList || !alertEmpty) return;
		alertList.innerHTML = '';
		if (!items || items.length === 0) {
			alertList.classList.add('d-none');
			alertEmpty.classList.remove('d-none');
			return;
		}
		alertEmpty.classList.add('d-none');
		alertList.classList.remove('d-none');
		items.forEach(function (item) {
			var html = '<div class="d-flex flex-stack py-4">' +
				'<div class="d-flex align-items-center">' +
				'<div class="symbol symbol-35px me-4">' +
				'<span class="symbol-label bg-light-warning">' +
				'<span class="svg-icon svg-icon-2 svg-icon-warning">' +
				'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
				'<path opacity="0.3" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor"/>' +
				'<path d="M12 13C11.4477 13 11 12.5523 11 12V7C11 6.44772 11.4477 6 12 6C12.5523 6 13 6.44772 13 7V12C13 12.5523 12.5523 13 12 13Z" fill="currentColor"/>' +
				'<path d="M12 17C11.4477 17 11 16.5523 11 16C11 15.4477 11.4477 15 12 15C12.5523 15 13 15.4477 13 16C13 16.5523 12.5523 17 12 17Z" fill="currentColor"/>' +
				'</svg></span></span></div>' +
				'<div class="mb-0 me-2">' +
				'<a href="' + escapeHtml(baseUrl + '/notesreminders') + '" class="fs-6 text-gray-800 text-hover-primary fw-bold">' + escapeHtml(item.title) + '</a>' +
				'<div class="text-gray-400 fs-7">' + escapeHtml(item.description) + '</div>' +
				'</div></div>' +
				'<span class="badge badge-light fs-8">' + escapeHtml(item.time_ago || formatDate(item.date)) + '</span>' +
				'</div>';
			alertList.insertAdjacentHTML('beforeend', html);
		});
	}

	function renderUpdates(items) {
		if (!updateList || !updateEmpty) return;
		updateList.innerHTML = '';
		if (!items || items.length === 0) {
			updateList.classList.add('d-none');
			updateEmpty.classList.remove('d-none');
			return;
		}
		updateEmpty.classList.add('d-none');
		updateList.classList.remove('d-none');
		var duyuruBase = baseUrl + '/duyurular';
		items.forEach(function (item) {
			var link = duyuruBase + '?detail=' + item.id;
			var html = '<div class="d-flex flex-stack py-4">' +
				'<div class="d-flex align-items-center">' +
				'<div class="symbol symbol-35px me-4">' +
				'<span class="symbol-label bg-light-info">' +
				'<span class="svg-icon svg-icon-2 svg-icon-info"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
				'<path opacity="0.3" d="M18 21.6C16.6 20.4 9.1 20.3 6.3 21.7C5.7 22 5.3 21.6 5.6 21C6.4 19.6 8.2 19 10 19C10 19 11.6 18.9 13 20L18 21.6Z" fill="currentColor"/>' +
				'<path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor"/>' +
				'</svg></span></span></div>' +
				'<div class="mb-0 me-2">' +
				'<a href="' + escapeHtml(link) + '" class="fs-6 text-gray-800 text-hover-primary fw-bold">' + escapeHtml(item.title) + '</a>' +
				'<div class="text-gray-400 fs-7">' + escapeHtml(item.description) + '</div>' +
				'</div></div>' +
				'<span class="badge badge-light fs-8">' + escapeHtml(formatDate(item.date)) + '</span>' +
				'</div>';
			updateList.insertAdjacentHTML('beforeend', html);
		});
	}

	function logBadgeClass(tur) {
		if (tur === 1) return 'badge-light-danger';
		if (tur === 2) return 'badge-light-primary';
		if (tur === 3) return 'badge-light-info';
		return 'badge-light-success';
	}

	function renderLogs(items) {
		if (!logList || !logEmpty) return;
		logList.innerHTML = '';
		if (!items || items.length === 0) {
			logList.classList.add('d-none');
			logEmpty.classList.remove('d-none');
			return;
		}
		logEmpty.classList.add('d-none');
		logList.classList.remove('d-none');
		items.forEach(function (item) {
			var badgeClass = logBadgeClass(item.tur);
			var html = '<div class="d-flex flex-stack py-4">' +
				'<div class="d-flex align-items-center me-2">' +
				'<span class="w-70px badge ' + badgeClass + ' me-4">' + escapeHtml(item.tur_label) + '</span>' +
				'<span class="text-gray-800 text-hover-primary fw-semibold">' + escapeHtml(item.aciklama) + '</span>' +
				'</div>' +
				'<span class="badge badge-light fs-8">' + escapeHtml(formatDate(item.date)) + '</span>' +
				'</div>';
			logList.insertAdjacentHTML('beforeend', html);
		});
	}

	function formatLicenseDate(dateStr) {
		if (!dateStr) return '–';
		var d = new Date(dateStr);
		if (isNaN(d.getTime())) return dateStr;
		var day = ('0' + d.getDate()).slice(-2);
		var month = ('0' + (d.getMonth() + 1)).slice(-2);
		var year = d.getFullYear();
		return day + '.' + month + '.' + year;
	}

	function statusLabel(status) {
		if (!status) return '–';
		var labels = { 'active': 'Aktif', 'expired': 'Süresi dolmuş', 'grace': 'Grace', 'suspended': 'Askıda', 'cancelled': 'İptal' };
		return labels[status] || status;
	}

	function renderLicense(license) {
		if (licenseLoading) licenseLoading.classList.add('d-none');
		if (licenseEmpty) licenseEmpty.classList.add('d-none');
		if (!licenseBody) return;
		var hasDates = license && (license.subscription_start_date != null || license.subscription_end_date != null || license.demo_end_date != null);
		var hasUnit = license && license.unit_title;
		if (!license || (!hasDates && !hasUnit)) {
			licenseBody.classList.add('d-none');
			if (licenseEmpty) licenseEmpty.classList.remove('d-none');
			return;
		}
		licenseBody.classList.remove('d-none');
		var endDate = license.is_demo && license.demo_end_date ? license.demo_end_date : license.subscription_end_date;
		var startDate = license.subscription_start_date;
		var days = license.days_remaining;
		var period = license.subscription_period === 'monthly' ? 'Aylık' : (license.subscription_period === 'yearly' ? 'Yıllık' : (license.subscription_period || '–'));
		var statusClass = 'primary';
		if (license.subscription_status === 'expired' || (days != null && days < 0)) statusClass = 'danger';
		else if (license.subscription_status === 'suspended' || license.subscription_status === 'cancelled') statusClass = 'warning';
		else if (days != null && days <= 30) statusClass = 'warning';
		var rows = [];
		if (license.unit_title) {
			rows.push('<div class="d-flex justify-content-between py-2 border-bottom border-gray-200"><span class="text-gray-600">Birim</span><span class="fw-semibold">' + escapeHtml(license.unit_title) + '</span></div>');
		}
		rows.push('<div class="d-flex justify-content-between py-2 border-bottom border-gray-200"><span class="text-gray-600">Başlangıç</span><span>' + formatLicenseDate(startDate) + '</span></div>');
		rows.push('<div class="d-flex justify-content-between py-2 border-bottom border-gray-200"><span class="text-gray-600">Bitiş</span><span>' + formatLicenseDate(endDate) + '</span></div>');
		rows.push('<div class="d-flex justify-content-between py-2 border-bottom border-gray-200"><span class="text-gray-600">Dönem</span><span>' + escapeHtml(period) + '</span></div>');
		rows.push('<div class="d-flex justify-content-between py-2 border-bottom border-gray-200"><span class="text-gray-600">Durum</span><span class="badge badge-light-' + statusClass + '">' + escapeHtml(statusLabel(license.subscription_status)) + '</span></div>');
		if (days != null) {
			var daysText = days < 0 ? (Math.abs(days) + ' gün önce bitti') : (days === 0 ? 'Bugün bitiyor' : days + ' gün kaldı');
			rows.push('<div class="d-flex justify-content-between py-2"><span class="text-gray-600">Kalan süre</span><span class="fw-bold text-' + (days < 0 ? 'danger' : (days <= 30 ? 'warning' : 'success')) + '">' + escapeHtml(daysText) + '</span></div>');
		} else if (!hasDates) {
			rows.push('<div class="d-flex justify-content-between py-2"><span class="text-gray-600">Kalan süre</span><span class="text-gray-500">Abonelik süresi tanımlanmamış</span></div>');
		}
		licenseBody.innerHTML = rows.join('');
	}

	function loadNotifications() {
		var xhr = new XMLHttpRequest();
		xhr.open('GET', apiUrl, true);
		xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhr.onreadystatechange = function () {
			if (xhr.readyState !== 4) return;
			var total = 0;
			var data = { total: 0, alerts: [], updates: [], logs: [] };
			if (xhr.status === 200) {
				try {
					data = JSON.parse(xhr.responseText);
					total = data.total || 0;
				} catch (e) {}
			}
			if (countEl) countEl.textContent = total + ' bildirim';
			if (badgeEl) {
				if (total > 0) {
					badgeEl.textContent = total > 99 ? '99+' : total;
					badgeEl.classList.remove('d-none');
				} else {
					badgeEl.classList.add('d-none');
				}
			}
			renderAlerts(data.alerts || []);
			renderUpdates(data.updates || []);
			renderLogs(data.logs || []);
			renderLicense(data.license);
		};
		xhr.send();
	}

	var menuLoaded = false;
	function onMenuShow() {
		if (menuLoaded) return;
		menuLoaded = true;
		loadNotifications();
	}

	function resetMenuLoaded() {
		menuLoaded = false;
	}

	if (triggerEl) {
		triggerEl.addEventListener('click', function () {
			setTimeout(onMenuShow, 50);
		});
	}

	if (menuEl) {
		menuEl.addEventListener('show.kt.menu', onMenuShow);
		menuEl.addEventListener('hide.kt.menu', resetMenuLoaded);
	}

	if (menuEl && typeof MutationObserver !== 'undefined') {
		var observer = new MutationObserver(function (mutations) {
			var isShow = menuEl.classList.contains('show');
			if (isShow) {
				onMenuShow();
			} else {
				resetMenuLoaded();
			}
		});
		observer.observe(menuEl, { attributes: true, attributeFilter: ['class'] });
	}

	if (wrapper) {
		wrapper.addEventListener('mouseenter', function () {
			setTimeout(onMenuShow, 100);
		});
	}
})();
