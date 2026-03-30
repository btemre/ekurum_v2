/**
 * HEDAS AI Service - Frontend
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
    }

    function showModal(title) {
        var modal = document.getElementById("kt_modal_ai_summary");
        if (!modal) return;
        document.getElementById("ai_modal_title").textContent = title || "AI Dosya Özeti";
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

    function requestDosyaSummary(dosyaId) {
        showModal("Dosya AI Özeti");

        fetch(baseUrl + "ai/api_dosyaSummary", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ dosya_id: dosyaId })
        })
            .then(function (res) { return res.json(); })
            .then(function (resp) {
                if (resp.success) {
                    showResult(resp.data);
                } else {
                    showError(resp.description || "Bilinmeyen hata.");
                }
            })
            .catch(function (err) {
                showError("Bağlantı hatası: " + err.message);
            });
    }

    function requestGenelSummary() {
        showModal("HEDAS Genel Dosya Özeti");

        fetch(baseUrl + "ai/api_genelSummary", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({})
        })
            .then(function (res) { return res.json(); })
            .then(function (resp) {
                if (resp.success) {
                    showResult(resp.data);
                } else {
                    showError(resp.description || "Bilinmeyen hata.");
                }
            })
            .catch(function (err) {
                showError("Bağlantı hatası: " + err.message);
            });
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

        fetch(baseUrl + "ai/api_textToSQL", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ query: query })
        })
            .then(function (res) { return res.json(); })
            .then(function (resp) {
                document.getElementById("ai_search_loading").classList.add("d-none");
                if (resp.success) {
                    _renderSearchResults(resp.data);
                } else {
                    _showSearchError(resp.description || "Bilinmeyen hata.");
                }
            })
            .catch(function (err) {
                document.getElementById("ai_search_loading").classList.add("d-none");
                _showSearchError("Bağlantı hatası: " + err.message);
            });
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
        requestDosyaSummary: requestDosyaSummary,
        requestGenelSummary: requestGenelSummary,
        openSearchModal: openSearchModal,
        requestTextToSQL: requestTextToSQL,
        showModal: showModal,
        showResult: showResult,
        showError: showError
    };
})();

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
