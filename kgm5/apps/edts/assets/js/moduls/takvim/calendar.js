"use strict";

var TakvimCalendar = (function() {
    var calendar = null;
    var options = {
        baseUrl: '', eventsUrl: '', eventDetailUrl: '', canWrite: false, updateBaseUrl: '', densityUrl: '',
        prefsUrl: '', savePrefsUrl: '', feedTokenUrl: ''
    };
    var currentDensityRange = { start: null, end: null };
    var currentGranularity = 'day';

    function init(opts) {
        options = Object.assign({}, options, opts || {});
        var el = document.getElementById('takvim_calendar');
        if (!el) return;

        if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar yüklü değil.');
            return;
        }

        var CalendarClass = (FullCalendar.Calendar && typeof FullCalendar.Calendar === 'function')
            ? FullCalendar.Calendar
            : (typeof FullCalendar === 'function' ? FullCalendar : null);
        if (!CalendarClass) {
            console.error('FullCalendar Calendar constructor bulunamadı.');
            return;
        }
        /* Haftalık görünüm: dayGridWeek (gün sütunları, saat ızgarası yok). timeGridWeek kullanılmıyor. */
        var calendarOpts = {
            initialView: 'dayGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridWeek,dayGridMonth'
            },
            locale: 'tr',
            firstDay: 1,
            height: 'auto',
            dayMaxEvents: 6,
            eventDisplay: 'block',
            eventTimeFormat: { hour: '2-digit', minute: '2-digit' },
            views: {
                dayGridWeek: {
                    dayMaxEvents: false,
                    titleFormat: { year: 'numeric', month: 'short', day: 'numeric' }
                },
                dayGridMonth: {}
            },
            events: function(info, successCallback, failureCallback) {
                var url = options.eventsUrl + '?start=' + encodeURIComponent(info.startStr) + '&end=' + encodeURIComponent(info.endStr);
                var avukatEl = document.getElementById('takvim_avukat_filter');
                if (avukatEl && avukatEl.value) {
                    url += '&avukat_id=' + encodeURIComponent(avukatEl.value);
                }
                fetch(url, { credentials: 'same-origin' })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (Array.isArray(data)) {
                            successCallback(data);
                        } else {
                            successCallback([]);
                        }
                    })
                    .catch(function() {
                        successCallback([]);
                    });
            },
            dateClick: function(arg) {
                if (options.canWrite) {
                    var modal = document.getElementById('kt_modal_new_durusmalar_manuel');
                    if (modal) {
                        var dateStr = arg.dateStr;
                        if (dateStr.indexOf('T') === -1) dateStr += 'T09:00:00';
                        var fpInput = document.getElementById('dm_durusmatarihi');
                        if (fpInput) {
                            fpInput.value = dateStr.replace('T', ' ').substring(0, 16);
                            var inst = (typeof flatpickr !== 'undefined') ? flatpickr(fpInput) : null;
                            if (inst && inst.setDate) inst.setDate(dateStr, true);
                        }
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var m = bootstrap.Modal.getOrCreateInstance(modal);
                            m.show();
                        } else if (typeof $ !== 'undefined') {
                            $(modal).modal('show');
                        }
                    }
                }
            },
            eventClick: function(arg) {
                arg.jsEvent.preventDefault();
                var id = arg.event.id || (arg.event.extendedProps && arg.event.extendedProps.d_id);
                if (id) {
                    openEventDetailModal(id);
                }
            },
            datesSet: function(arg) {
                var startStr = arg.startStr || (arg.start && arg.start.toISOString ? arg.start.toISOString() : null);
                var endStr = arg.endStr || (arg.end && arg.end.toISOString ? arg.end.toISOString() : null);
                if (startStr && endStr) {
                    currentDensityRange.start = startStr;
                    currentDensityRange.end = endStr;
                    loadDensity();
                }
            }
        };

        calendar = new CalendarClass(el, calendarOpts);
        calendar.render();
        /* Haftalık görünümü her zaman gün listesi (dayGridWeek) yap; saat ızgarası (timeGridWeek) kullanma */
        if (calendar.changeView) {
            try { calendar.changeView('dayGridWeek'); } catch (e) {}
        }

        var densityUrl = options.densityUrl || (options.baseUrl + 'takvim/density');
        var densityListEl = document.getElementById('takvim_density_list');
        var granBtns = document.getElementById('takvim_density_granularity');

        function getViewRange() {
            try {
                var data = calendar.getCurrentData && calendar.getCurrentData();
                if (data && data.dateProfile && data.dateProfile.activeRange) {
                    var r = data.dateProfile.activeRange;
                    return {
                        start: r.start && r.start.toISOString ? r.start.toISOString() : null,
                        end: r.end && r.end.toISOString ? r.end.toISOString() : null
                    };
                }
                if (calendar.view && calendar.view.activeStart && calendar.view.activeEnd) {
                    return {
                        start: calendar.view.activeStart.toISOString ? calendar.view.activeStart.toISOString() : String(calendar.view.activeStart),
                        end: calendar.view.activeEnd.toISOString ? calendar.view.activeEnd.toISOString() : String(calendar.view.activeEnd)
                    };
                }
            } catch (e) {}
            return null;
        }

        function loadDensity() {
            if (!densityListEl) return;
            var startStr = currentDensityRange.start;
            var endStr = currentDensityRange.end;
            if (!startStr || !endStr) {
                var range = getViewRange();
                if (range) {
                    startStr = range.start;
                    endStr = range.end;
                    currentDensityRange.start = startStr;
                    currentDensityRange.end = endStr;
                }
            }
            if (!startStr || !endStr) return;
            var url = densityUrl + '?start=' + encodeURIComponent(startStr) + '&end=' + encodeURIComponent(endStr) + '&granularity=' + encodeURIComponent(currentGranularity);
            var avukatEl = document.getElementById('takvim_avukat_filter');
            if (avukatEl && avukatEl.value) {
                url += '&avukat_id=' + encodeURIComponent(avukatEl.value);
            }
            densityListEl.innerHTML = '<p class="text-muted mb-0">Yükleniyor...</p>';
            fetch(url, { credentials: 'same-origin' })
                .then(function(r) {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(function(res) {
                    if (res.success && res.data && res.data.length) {
                        var maxCount = Math.max.apply(null, res.data.map(function(d) { return d.count; }));
                        densityListEl.innerHTML = res.data.map(function(d) {
                            var pct = maxCount > 0 ? Math.round((d.count / maxCount) * 100) : 0;
                            return '<div class="d-flex justify-content-between align-items-center"><span class="text-gray-700">' + d.label + '</span><span class="badge badge-light-primary">' + d.count + '</span></div><div class="progress h-6px mb-2"><div class="progress-bar bg-primary" role="progressbar" style="width:' + pct + '%"></div></div>';
                        }).join('');
                    } else {
                        densityListEl.innerHTML = '<p class="text-muted mb-0">Bu aralıkta duruşma yok.</p>';
                    }
                })
                .catch(function() {
                    densityListEl.innerHTML = '<p class="text-danger mb-0">Yüklenemedi.</p>';
                });
        }

        setTimeout(function() {
            if (!currentDensityRange.start || !currentDensityRange.end) {
                var range = getViewRange();
                if (range) {
                    currentDensityRange.start = range.start;
                    currentDensityRange.end = range.end;
                }
            }
            loadDensity();
        }, 150);

        if (granBtns) {
            granBtns.addEventListener('click', function(e) {
                var btn = e.target.closest('[data-gran]');
                if (btn) {
                    granBtns.querySelectorAll('[data-gran]').forEach(function(b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    currentGranularity = btn.getAttribute('data-gran') || 'day';
                    loadDensity();
                }
            });
        }

        var avukatFilterEl = document.getElementById('takvim_avukat_filter');
        if (avukatFilterEl && calendar) {
            avukatFilterEl.addEventListener('change', function() {
                if (calendar.refetchEvents) calendar.refetchEvents();
                loadDensity();
            });
        }

        function openEventDetailModal(id) {
            var modal = document.getElementById('takvim_modal_event_detail');
            var body = document.getElementById('takvim_event_detail_body');
            var editBtn = document.getElementById('takvim_event_detail_edit_btn');
            if (!modal || !body) return;
            body.innerHTML = '<p class="text-muted mb-0">Yükleniyor...</p>';
            if (editBtn) editBtn.setAttribute('data-durusma-id', id);
            var url = (options.eventDetailUrl || (options.baseUrl + 'takvim/eventDetail')) + '/' + id;
            fetch(url, { credentials: 'same-origin' })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success && res.data) {
                        var d = res.data;
                        var html = '<table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-2 mb-0"><tbody>';
                        html += '<tr><td class="text-muted fw-bold w-35">Dosya No</td><td>' + (d.dosyano || '—') + '</td></tr>';
                        html += '<tr><td class="text-muted fw-bold">Tarih / Saat</td><td>' + (d.durusmatarihi || '—') + '</td></tr>';
                        html += '<tr><td class="text-muted fw-bold">Mahkeme</td><td>' + (d.mahkeme || '—') + '</td></tr>';
                        html += '<tr><td class="text-muted fw-bold">Esas No</td><td>' + (d.esasno || '—') + '</td></tr>';
                        html += '<tr><td class="text-muted fw-bold">Dosya Türü</td><td>' + (d.dosyaturu || '—') + '</td></tr>';
                        html += '<tr><td class="text-muted fw-bold">İşlem</td><td>' + (d.islem || '—') + '</td></tr>';
                        html += '<tr><td class="text-muted fw-bold">Taraf</td><td>' + (d.taraf || '—') + '</td></tr>';
                        html += '<tr><td class="text-muted fw-bold">Avukat</td><td>' + (d.avukat || '—') + '</td></tr>';
                        html += '<tr><td class="text-muted fw-bold">Memur</td><td>' + (d.memur || '—') + '</td></tr>';
                        if (d.tarafbilgisi) html += '<tr><td class="text-muted fw-bold">Taraf Bilgisi</td><td>' + d.tarafbilgisi + '</td></tr>';
                        if (d.aciklama) html += '<tr><td class="text-muted fw-bold">Açıklama</td><td>' + d.aciklama + '</td></tr>';
                        html += '</tbody></table>';
                        body.innerHTML = html;
                    } else {
                        body.innerHTML = '<p class="text-danger mb-0">Kayıt yüklenemedi.</p>';
                    }
                    var m = typeof bootstrap !== 'undefined' && bootstrap.Modal ? bootstrap.Modal.getOrCreateInstance(modal) : null;
                    if (m) m.show();
                })
                .catch(function() {
                    body.innerHTML = '<p class="text-danger mb-0">Kayıt yüklenemedi.</p>';
                    var m = typeof bootstrap !== 'undefined' && bootstrap.Modal ? bootstrap.Modal.getOrCreateInstance(modal) : null;
                    if (m) m.show();
                });
        }

        var editBtnEl = document.getElementById('takvim_event_detail_edit_btn');
        if (editBtnEl) {
            editBtnEl.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var id = this.getAttribute('data-durusma-id');
                if (!id) return;
                var detailModal = document.getElementById('takvim_modal_event_detail');
                if (detailModal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var inst = bootstrap.Modal.getInstance(detailModal);
                    if (inst) inst.hide();
                }
                if (typeof KTModalUpdateDurusmalarManuel !== 'undefined' && KTModalUpdateDurusmalarManuel.viewModal) {
                    KTModalUpdateDurusmalarManuel.viewModal(id);
                } else {
                    var url = (options.updateBaseUrl || '') + '/' + id;
                    if (url && url !== '/' + id) window.location.href = url;
                }
            });
        }

        var prefsModal = document.getElementById('takvim_modal_notification_prefs');
        var prefsBtn = document.getElementById('takvim_btn_notification_prefs');
        if (prefsBtn && prefsModal) {
            prefsBtn.addEventListener('click', function() {
                loadNotificationPrefs();
                var m = typeof bootstrap !== 'undefined' && bootstrap.Modal ? bootstrap.Modal.getOrCreateInstance(prefsModal) : null;
                if (m) m.show();
            });
        }
        var savePrefsBtn = document.getElementById('takvim_btn_save_prefs');
        if (savePrefsBtn) {
            savePrefsBtn.addEventListener('click', function() {
                saveNotificationPrefs();
            });
        }

        var getFeedBtn = document.getElementById('takvim_btn_get_feed_url');
        var feedUrlBox = document.getElementById('takvim_feed_url_box');
        var feedUrlInput = document.getElementById('takvim_feed_url_input');
        var copyFeedBtn = document.getElementById('takvim_btn_copy_feed');
        var icsDownloadLink = document.getElementById('takvim_lnk_ics_download');
        if (getFeedBtn && feedUrlBox) {
            getFeedBtn.addEventListener('click', function() {
                var url = options.feedTokenUrl || (options.baseUrl + 'takvim/getFeedToken');
                fetch(url, { credentials: 'same-origin' })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res.success && res.feed_url) {
                            if (feedUrlInput) feedUrlInput.value = res.feed_url;
                            if (icsDownloadLink) {
                                icsDownloadLink.href = res.feed_url;
                                icsDownloadLink.classList.remove('d-none');
                            }
                            feedUrlBox.classList.remove('d-none');
                        }
                    });
            });
        }
        if (copyFeedBtn && feedUrlInput) {
            copyFeedBtn.addEventListener('click', function() {
                feedUrlInput.select();
                document.execCommand('copy');
                if (typeof Swal !== 'undefined') Swal.fire({ text: 'Link kopyalandı.', icon: 'success', timer: 1500, buttonsStyling: false }); else alert('Link kopyalandı.');
            });
        }

        var exportModal = document.getElementById('takvim_modal_export');
        var exportBtn = document.getElementById('takvim_btn_export');
        var exportDateGroup = document.getElementById('takvim_export_date_group');
        var exportRangeGroup = document.getElementById('takvim_export_range_group');
        var exportMonthGroup = document.getElementById('takvim_export_month_group');
        var exportDateInput = document.getElementById('takvim_export_date');
        var exportStartDateInput = document.getElementById('takvim_export_start_date');
        var exportEndDateInput = document.getElementById('takvim_export_end_date');
        var exportMonthSelect = document.getElementById('takvim_export_month');
        var exportYearSelect = document.getElementById('takvim_export_year');
        var exportSubmitBtn = document.getElementById('takvim_btn_export_submit');
        var exportUrlBase = (options.baseUrl || '').replace(/\/$/, '') + '/takvim/export';

        if (exportBtn && exportModal) {
            exportBtn.addEventListener('click', function() {
                var today = new Date();
                var y = today.getFullYear();
                if (exportDateInput) {
                    exportDateInput.value = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
                }
                if (exportMonthSelect) exportMonthSelect.value = String(today.getMonth() + 1);
                if (exportYearSelect) {
                    exportYearSelect.innerHTML = '';
                    for (var i = y - 2; i <= y + 1; i++) {
                        var opt = document.createElement('option');
                        opt.value = i;
                        opt.textContent = i;
                        if (i === y) opt.selected = true;
                        exportYearSelect.appendChild(opt);
                    }
                }
                toggleExportPeriodVisibility();
                var m = typeof bootstrap !== 'undefined' && bootstrap.Modal ? bootstrap.Modal.getOrCreateInstance(exportModal) : null;
                if (m) m.show();
            });
        }

        function toggleExportPeriodVisibility() {
            var periodMonth = document.getElementById('takvim_export_period_month');
            var periodRange = document.getElementById('takvim_export_period_range');
            if (exportDateGroup && exportMonthGroup) {
                if (periodRange && periodRange.checked) {
                    exportDateGroup.classList.add('d-none');
                    exportMonthGroup.classList.add('d-none');
                    if (exportRangeGroup) exportRangeGroup.classList.remove('d-none');
                } else if (periodMonth && periodMonth.checked) {
                    exportDateGroup.classList.add('d-none');
                    exportMonthGroup.classList.remove('d-none');
                    if (exportRangeGroup) exportRangeGroup.classList.add('d-none');
                } else {
                    exportDateGroup.classList.remove('d-none');
                    exportMonthGroup.classList.add('d-none');
                    if (exportRangeGroup) exportRangeGroup.classList.add('d-none');
                }
            }
        }

        var periodRadios = document.querySelectorAll('input[name="takvim_export_period"]');
        if (periodRadios.length) {
            periodRadios.forEach(function(r) {
                r.addEventListener('change', toggleExportPeriodVisibility);
            });
        }

        if (exportSubmitBtn) {
            exportSubmitBtn.addEventListener('click', function() {
                var periodEl = document.querySelector('input[name="takvim_export_period"]:checked');
                var formatEl = document.querySelector('input[name="takvim_export_format"]:checked');
                if (!periodEl || !formatEl) return;
                var period = periodEl.value;
                var format = formatEl.value;
                var url;
                if (period === 'range') {
                    if (!exportStartDateInput || !exportEndDateInput || !exportStartDateInput.value || !exportEndDateInput.value) {
                        if (typeof Swal !== 'undefined') Swal.fire({ text: 'Lütfen başlangıç ve bitiş tarihini seçin.', icon: 'warning', buttonsStyling: false }); else alert('Lütfen başlangıç ve bitiş tarihini seçin.');
                        return;
                    }
                    url = exportUrlBase + '?start_date=' + encodeURIComponent(exportStartDateInput.value) + '&end_date=' + encodeURIComponent(exportEndDateInput.value) + '&format=' + encodeURIComponent(format);
                } else {
                    var dateStr = '';
                    if (period === 'month' && exportMonthSelect && exportYearSelect) {
                        dateStr = exportYearSelect.value + '-' + String(exportMonthSelect.value).padStart(2, '0') + '-01';
                    } else if (exportDateInput && exportDateInput.value) {
                        dateStr = exportDateInput.value;
                    }
                    if (!dateStr) {
                        if (typeof Swal !== 'undefined') Swal.fire({ text: 'Lütfen tarih seçin.', icon: 'warning', buttonsStyling: false }); else alert('Lütfen tarih seçin.');
                        return;
                    }
                    url = exportUrlBase + '?period=' + encodeURIComponent(period) + '&date=' + encodeURIComponent(dateStr) + '&format=' + encodeURIComponent(format);
                }
                var avukatExportEl = document.getElementById('takvim_avukat_filter');
                if (avukatExportEl && avukatExportEl.value) {
                    url += '&avukat_id=' + encodeURIComponent(avukatExportEl.value);
                }
                var modalInst = exportModal && typeof bootstrap !== 'undefined' && bootstrap.Modal ? bootstrap.Modal.getInstance(exportModal) : null;
                fetch(url, { credentials: 'same-origin', method: 'GET' })
                    .then(function(r) {
                        var ct = r.headers.get('content-type') || '';
                        if (ct.indexOf('application/json') !== -1) {
                            return r.json().then(function(j) {
                                if (!j.success && j.message) {
                                    if (typeof Swal !== 'undefined') Swal.fire({ text: j.message, icon: 'error', buttonsStyling: false }); else alert(j.message);
                                }
                            });
                        }
                        return r.blob().then(function(blob) {
                            if (format === 'pdf') {
                                return blob.text().then(function(html) {
                                    var win = window.open('', '_blank');
                                    win.document.write(html);
                                    win.document.close();
                                    if (modalInst) modalInst.hide();
                                });
                            } else {
                                var a = document.createElement('a');
                                a.href = URL.createObjectURL(blob);
                                a.download = 'takvim_export.' + (format === 'excel' ? 'xlsx' : 'html');
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                                URL.revokeObjectURL(a.href);
                                if (modalInst) modalInst.hide();
                            }
                        });
                    })
                    .catch(function() {
                        if (typeof Swal !== 'undefined') Swal.fire({ text: 'Dışa aktarım sırasında hata oluştu.', icon: 'error', buttonsStyling: false }); else alert('Dışa aktarım sırasında hata oluştu.');
                    });
            });
        }

        function loadNotificationPrefs() {
            var url = options.prefsUrl || (options.baseUrl + 'takvim/getNotificationPrefs');
            fetch(url, { credentials: 'same-origin' })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success && res.data) {
                        var d = res.data;
                        var emailCb = document.getElementById('takvim_pref_email');
                        var pushCb = document.getElementById('takvim_pref_push');
                        var remSel = document.getElementById('takvim_pref_reminder');
                        if (emailCb) emailCb.checked = !!d.email_enabled;
                        if (pushCb) pushCb.checked = !!d.push_enabled;
                        if (remSel) remSel.value = String(d.reminder_minutes || 1440);
                    }
                });
        }

        function saveNotificationPrefs() {
            var url = options.savePrefsUrl || (options.baseUrl + 'takvim/saveNotificationPrefs');
            var emailCb = document.getElementById('takvim_pref_email');
            var pushCb = document.getElementById('takvim_pref_push');
            var remSel = document.getElementById('takvim_pref_reminder');
            var fd = new FormData();
            fd.append('email_enabled', emailCb && emailCb.checked ? '1' : '0');
            fd.append('push_enabled', pushCb && pushCb.checked ? '1' : '0');
            fd.append('reminder_minutes', remSel ? remSel.value : '1440');
            fetch(url, { method: 'POST', credentials: 'same-origin', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success) {
                        if (typeof Swal !== 'undefined') Swal.fire({ text: 'Ayarlar kaydedildi.', icon: 'success', timer: 1500, buttonsStyling: false }); else alert('Ayarlar kaydedildi.');
                        if (prefsModal && bootstrap && bootstrap.Modal) { var m = bootstrap.Modal.getInstance(prefsModal); if (m) m.hide(); }
                    }
                });
        }
    }

    return { init: init };
})();
