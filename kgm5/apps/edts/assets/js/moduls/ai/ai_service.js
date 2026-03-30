/**
 * EDTS AI Service - Frontend
 */
var AiService = (function () {

    var baseUrl = "";

    var _allowedTags = ['h1','h2','h3','h4','h5','h6','p','br','hr',
        'strong','b','em','i','u','small','mark',
        'ul','ol','li','dl','dt','dd',
        'table','thead','tbody','tfoot','tr','th','td',
        'div','span','blockquote','pre','code',
        'a','img'];

    var _allowedAttrs = ['class','style','href','target','colspan','rowspan','src','alt','width','height'];

    function _sanitizeHtml(html) {
        if (!html) return "";
        var temp = document.createElement("div");
        temp.innerHTML = html;

        var all = temp.querySelectorAll("*");
        for (var i = 0; i < all.length; i++) {
            var el = all[i];
            var tag = el.tagName.toLowerCase();
            if (_allowedTags.indexOf(tag) === -1) {
                el.outerHTML = _escapeHtml(el.textContent);
                continue;
            }
            var attrs = el.attributes;
            for (var a = attrs.length - 1; a >= 0; a--) {
                var attrName = attrs[a].name.toLowerCase();
                if (_allowedAttrs.indexOf(attrName) === -1) {
                    el.removeAttribute(attrs[a].name);
                } else if (attrName === 'href' || attrName === 'src') {
                    var val = attrs[a].value.trim().toLowerCase();
                    if (val.indexOf('javascript:') === 0 || val.indexOf('data:') === 0) {
                        el.removeAttribute(attrs[a].name);
                    }
                }
            }
            if (tag === 'table') {
                if (!el.className || el.className.indexOf('table') === -1) {
                    el.className = (el.className ? el.className + " " : "") + "table table-sm table-bordered";
                }
            }
        }
        return temp.innerHTML;
    }

    function init(url) {
        baseUrl = url;
        console.log("[AiService] init baseUrl:", baseUrl);
    }

    function _doRequest(endpoint, payload, onSuccess, onError) {
        var url = baseUrl + endpoint;
        console.log("[AiService] POST ->", url, "payload:", payload);

        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        })
        .then(function (res) {
            console.log("[AiService] HTTP status:", res.status, "statusText:", res.statusText);
            console.log("[AiService] Content-Type:", res.headers.get("content-type"));
            return res.text();
        })
        .then(function (text) {
            console.log("[AiService] Raw response (first 500 chars):", text.substring(0, 500));
            try {
                var resp = JSON.parse(text);
                console.log("[AiService] Parsed JSON:", resp);
                if (resp.success) {
                    onSuccess(resp);
                } else {
                    onError(resp.description || "Bilinmeyen hata.");
                }
            } catch (e) {
                console.error("[AiService] JSON parse error:", e.message);
                console.error("[AiService] Full response text:", text);
                onError("Sunucu geçersiz yanıt döndü. Detaylar console'da.");
            }
        })
        .catch(function (err) {
            console.error("[AiService] Network error:", err);
            onError("Bağlantı hatası: " + err.message);
        });
    }

    function showModal(title) {
        var modal = document.getElementById("kt_modal_ai_summary");
        if (!modal) return;
        document.getElementById("ai_modal_title").textContent = title || "AI Özet";
        document.getElementById("ai_loading").classList.remove("d-none");
        document.getElementById("ai_error").classList.add("d-none");
        document.getElementById("ai_result").classList.add("d-none");
        document.getElementById("ai_copy_btn").style.display = "none";
        if (!modal._aiAriaFix) {
            modal._aiAriaFix = true;
            modal.addEventListener("show.bs.modal", function () {
                modal.setAttribute("aria-hidden", "false");
            });
            modal.addEventListener("hidden.bs.modal", function () {
                modal.setAttribute("aria-hidden", "true");
            });
        }
        var bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    function showResult(data) {
        document.getElementById("ai_loading").classList.add("d-none");
        document.getElementById("ai_error").classList.add("d-none");
        document.getElementById("ai_result").classList.remove("d-none");
        document.getElementById("ai_summary_content").innerHTML = _sanitizeHtml(data.summary || "");
        document.getElementById("ai_copy_btn").style.display = "inline-block";
        if (data.remaining_quota !== undefined) {
            document.getElementById("ai_quota_badge").textContent = "Kalan hak: " + data.remaining_quota;
        }
    }

    function showError(message) {
        document.getElementById("ai_loading").classList.add("d-none");
        document.getElementById("ai_result").classList.add("d-none");
        document.getElementById("ai_error").classList.remove("d-none");
        document.getElementById("ai_error_message").textContent = message;
    }

    function requestSummary(filters) {
        showModal("EDTS Duruşma Özeti");
        _showRefreshWeekButton(false);
        var payload = { period: "month" };
        if (filters) payload.filters = filters;

        _doRequest("ai/api_summary", payload,
            function (resp) { showResult(resp.data); },
            function (msg) { showError(msg); }
        );
    }

    function _getISOWeekKey() {
        var d = new Date();
        d.setHours(0, 0, 0, 0);
        d.setDate(d.getDate() + 4 - (d.getDay() || 7));
        var y = d.getFullYear();
        var weekStart = new Date(y, 0, 1);
        var weekNum = Math.ceil((((d - weekStart) / 86400000) + 1) / 7);
        return y + "-W" + (String(100 + weekNum).slice(1));
    }

    function _getWeekSummaryCacheKey() {
        return "edts_ai_week_summary";
    }

    function _getCachedWeekSummary() {
        try {
            var key = _getWeekSummaryCacheKey();
            var raw = sessionStorage.getItem(key);
            if (!raw) return null;
            var data = JSON.parse(raw);
            if (data.weekKey !== _getISOWeekKey()) return null;
            return data;
        } catch (e) {
            return null;
        }
    }

    function _setCachedWeekSummary(data) {
        try {
            var payload = {
                weekKey: _getISOWeekKey(),
                summary: data.summary,
                remaining_quota: data.remaining_quota
            };
            sessionStorage.setItem(_getWeekSummaryCacheKey(), JSON.stringify(payload));
        } catch (e) {}
    }

    function requestWeekSummary(forceRefresh) {
        var isWeekSummary = true;
        showModal("Bu Hafta Duruşma Özeti");

        if (!forceRefresh) {
            var cached = _getCachedWeekSummary();
            if (cached && cached.summary) {
                showResult({ summary: cached.summary, remaining_quota: cached.remaining_quota });
                _showRefreshWeekButton(true);
                return;
            }
        }

        _showRefreshWeekButton(false);
        _doRequest("ai/api_weekSummary", {},
            function (resp) {
                _setCachedWeekSummary(resp.data);
                showResult(resp.data);
                _showRefreshWeekButton(true);
            },
            function (msg) {
                showError(msg);
                _showRefreshWeekButton(false);
            }
        );
    }

    function _showRefreshWeekButton(show) {
        var btn = document.getElementById("ai_refresh_week_btn");
        if (btn) btn.style.display = show ? "inline-block" : "none";
    }

    function refreshWeekSummaryFromButton() {
        var btn = document.getElementById("ai_refresh_week_btn");
        if (btn) btn.disabled = true;
        document.getElementById("ai_loading").classList.remove("d-none");
        document.getElementById("ai_error").classList.add("d-none");
        document.getElementById("ai_result").classList.add("d-none");

        _doRequest("ai/api_weekSummary", {},
            function (resp) {
                _setCachedWeekSummary(resp.data);
                showResult(resp.data);
                _showRefreshWeekButton(true);
                if (btn) btn.disabled = false;
            },
            function (msg) {
                showError(msg);
                _showRefreshWeekButton(true);
                if (btn) btn.disabled = false;
            }
        );
    }

    function _typewriter(el, text, speedMs, onComplete) {
        if (!el) { if (onComplete) onComplete(); return; }
        var str = String(text || "");
        el.textContent = "";
        var i = 0;
        function tick() {
            if (i < str.length) {
                el.textContent += str.charAt(i);
                i++;
                setTimeout(tick, speedMs);
            } else {
                if (onComplete) onComplete();
            }
        }
        tick();
    }

    function requestCapacityForecast(another, scopeDaily) {
        var el = document.getElementById("ai_capacity_forecast_text");
        var btn = document.getElementById("ai_capacity_forecast_btn");
        var row = document.getElementById("dashboard_ai_asistan_row");
        if (!el) return;
        if (another && btn) btn.disabled = true;
        el.textContent = "düşünüyor..";
        if (row) row.classList.add("ai-loading");
        var payload = scopeDaily ? { scope: "daily" } : {};
        _doRequest("ai/api_capacityForecast", payload,
            function (resp) {
                var fullText = (resp.data && resp.data.forecast) ? resp.data.forecast : "—";
                _typewriter(el, fullText, 28, function () {
                    if (btn) btn.disabled = false;
                    if (row) row.classList.remove("ai-loading");
                });
            },
            function () {
                el.textContent = "Tahmin alınamadı.";
                if (btn) btn.disabled = false;
                if (row) row.classList.remove("ai-loading");
            }
        );
    }

    function openSearchModal() {
        var modal = document.getElementById("kt_modal_ai_search");
        if (!modal) return;
        document.getElementById("ai_search_loading").classList.add("d-none");
        document.getElementById("ai_search_error").classList.add("d-none");
        document.getElementById("ai_search_results").classList.add("d-none");
        document.getElementById("ai_search_sql_box").classList.add("d-none");
        if (!modal._aiAriaFix) {
            modal._aiAriaFix = true;
            modal.addEventListener("show.bs.modal", function () {
                modal.setAttribute("aria-hidden", "false");
            });
            modal.addEventListener("hidden.bs.modal", function () {
                modal.setAttribute("aria-hidden", "true");
            });
        }
        var bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        setTimeout(function () {
            document.getElementById("ai_search_input").focus();
        }, 500);
    }

    function requestTextToSQL() {
        var input = document.getElementById("ai_search_input");
        var query = input ? input.value.trim() : "";

        if (query.length < 5) {
            _showSearchError("Lütfen en az 5 karakterlik bir soru giriniz.");
            return;
        }

        document.getElementById("ai_search_loading").classList.remove("d-none");
        document.getElementById("ai_search_error").classList.add("d-none");
        document.getElementById("ai_search_results").classList.add("d-none");
        document.getElementById("ai_search_sql_box").classList.add("d-none");

        _doRequest("ai/api_textToSQL", { query: query },
            function (resp) {
                document.getElementById("ai_search_loading").classList.add("d-none");
                _renderSearchResults(resp.data);
            },
            function (msg) {
                document.getElementById("ai_search_loading").classList.add("d-none");
                _showSearchError(msg);
            }
        );
    }

    function _showSearchError(message) {
        document.getElementById("ai_search_loading").classList.add("d-none");
        document.getElementById("ai_search_results").classList.add("d-none");
        document.getElementById("ai_search_error").classList.remove("d-none");
        document.getElementById("ai_search_error_message").textContent = message;
    }

    function _renderSearchResults(data) {
        document.getElementById("ai_search_error").classList.add("d-none");
        document.getElementById("ai_search_results").classList.remove("d-none");

        document.getElementById("ai_search_total").textContent = data.total + " sonuç";
        document.getElementById("ai_search_sql").textContent = data.sql || "";

        if (data.remaining_quota !== undefined) {
            document.getElementById("ai_search_quota").textContent = "Kalan hak: " + data.remaining_quota;
        }

        var thead = document.getElementById("ai_search_thead");
        var tbody = document.getElementById("ai_search_tbody");
        thead.innerHTML = "";
        tbody.innerHTML = "";

        if (!data.columns || data.columns.length === 0 || data.total === 0) {
            tbody.innerHTML = '<tr><td class="text-center text-muted py-5" colspan="99">Sonuç bulunamadı.</td></tr>';
            return;
        }

        var headerRow = "<tr>";
        for (var c = 0; c < data.columns.length; c++) {
            headerRow += "<th class='min-w-80px'>" + _escapeHtml(data.columns[c]) + "</th>";
        }
        headerRow += "</tr>";
        thead.innerHTML = headerRow;

        var maxRows = Math.min(data.rows.length, 100);
        for (var i = 0; i < maxRows; i++) {
            var row = data.rows[i];
            var tr = "<tr>";
            for (var c = 0; c < data.columns.length; c++) {
                var val = row[data.columns[c]];
                if (val === null || val === undefined) val = "";
                tr += "<td>" + _escapeHtml(String(val)) + "</td>";
            }
            tr += "</tr>";
            tbody.innerHTML += tr;
        }

        if (data.total > 100) {
            tbody.innerHTML += '<tr><td class="text-center text-muted fst-italic" colspan="' + data.columns.length + '">... ve ' + (data.total - 100) + ' sonuç daha (ilk 100 gösteriliyor)</td></tr>';
        }
    }

    function _escapeHtml(text) {
        var div = document.createElement("div");
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    return {
        init: init,
        requestSummary: requestSummary,
        requestWeekSummary: requestWeekSummary,
        refreshWeekSummaryFromButton: refreshWeekSummaryFromButton,
        requestCapacityForecast: requestCapacityForecast,
        openSearchModal: openSearchModal,
        requestTextToSQL: requestTextToSQL,
        showModal: showModal,
        showResult: showResult,
        showError: showError
    };
})();

function aiRefreshWeekSummary() {
    if (typeof AiService !== "undefined" && AiService.refreshWeekSummaryFromButton) {
        AiService.refreshWeekSummaryFromButton();
    }
}

function aiCopyResult() {
    var el = document.getElementById("ai_summary_content");
    var text = el.innerText || el.textContent;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function () {
            var btn = document.getElementById("ai_copy_btn");
            var origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Kopyalandı!';
            setTimeout(function () { btn.innerHTML = origHtml; }, 2000);
        });
    }
}

function aiToggleSQL() {
    var box = document.getElementById("ai_search_sql_box");
    if (box) {
        box.classList.toggle("d-none");
    }
}
