<script>
(function() {
    var hostX = window.location.host;
    var baseHost = "//" + hostX;
    var apiAsk = baseHost + "/apps/edts/ai_assistant/api_ask";
    var apiSql = baseHost + "/apps/edts/ai_assistant/api_text_to_sql";
    var apiSummary = baseHost + "/apps/edts/ai_assistant/api_summary";

    var history = [];
    var sqlMode = false;

    var $ = function(id) { return document.getElementById(id); };
    var qs = function(s) { return document.querySelector(s); };

    function setLoading(loading) {
        var btn = $("kt_ai_send_btn");
        if (!btn) return;
        var lbl = btn.querySelector(".indicator-label");
        var prg = btn.querySelector(".indicator-progress");
        if (loading) {
            lbl.classList.add("d-none");
            prg.classList.remove("d-none");
            btn.disabled = true;
        } else {
            lbl.classList.remove("d-none");
            prg.classList.add("d-none");
            btn.disabled = false;
        }
    }

    function hideError() {
        var el = $("kt_ai_error");
        if (el) el.classList.add("d-none");
    }

    function showError(txt) {
        var el = $("kt_ai_error");
        var txtEl = $("kt_ai_error_text");
        if (txtEl) txtEl.textContent = txt;
        if (el) el.classList.remove("d-none");
    }

    function escapeHtml(s) {
        var d = document.createElement("div");
        d.textContent = s;
        return d.innerHTML;
    }

    function fmt(txt) {
        if (!txt) return "";
        return escapeHtml(txt).replace(/\n/g, "<br>").replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");
    }

    function appendMsg(role, text) {
        var msgs = $("kt_ai_messages");
        var empty = $("kt_ai_empty");
        if (!msgs) return;
        if (empty) empty.classList.add("d-none");
        var div = document.createElement("div");
        div.className = "mb-3";
        if (role === "user") {
            div.innerHTML = '<div class="d-flex justify-content-end"><div class="bg-primary text-white rounded px-3 py-2" style="max-width:85%">' + fmt(text) + '</div></div>';
        } else {
            div.innerHTML = '<div class="d-flex justify-content-start"><div class="bg-white border rounded px-3 py-2" style="max-width:85%">' + fmt(text) + '</div></div>';
        }
        msgs.appendChild(div);
        var area = $("kt_ai_chat_area");
        if (area) area.scrollTop = area.scrollHeight;
    }

    function appendPending() {
        var msgs = $("kt_ai_messages");
        if (!msgs) return;
        var p = document.createElement("div");
        p.id = "kt_ai_pending";
        p.className = "mb-3 d-flex justify-content-start";
        p.innerHTML = '<div class="bg-white border rounded px-3 py-2"><span class="spinner-border spinner-border-sm text-primary me-2"></span>Yanıtlanıyor...</div>';
        msgs.appendChild(p);
        var area = $("kt_ai_chat_area");
        if (area) area.scrollTop = area.scrollHeight;
    }

    function removePending() {
        var p = $("kt_ai_pending");
        if (p) p.remove();
    }

    function showDataTable(data) {
        var wrap = $("kt_ai_data_table_wrap");
        var tbl = $("kt_ai_data_table");
        if (!wrap || !tbl || !data || !data.length) return;
        var cols = Object.keys(data[0]);
        tbl.querySelector("thead").innerHTML = "<tr>" + cols.map(function(c) { return "<th>" + escapeHtml(c) + "</th>"; }).join("") + "</tr>";
        tbl.querySelector("tbody").innerHTML = data.slice(0, 20).map(function(row) {
            return "<tr>" + cols.map(function(c) { return "<td>" + escapeHtml(String(row[c] || "")) + "</td>"; }).join("") + "</tr>";
        }).join("");
        wrap.classList.remove("d-none");
    }

    function hideDataTable() {
        var wrap = $("kt_ai_data_table_wrap");
        if (wrap) wrap.classList.add("d-none");
    }

    function send() {
        var inp = $("kt_ai_input");
        var q = (inp && inp.value) ? inp.value.trim() : "";
        if (!q) {
            showError("Lütfen bir soru girin.");
            return;
        }

        setLoading(true);
        hideError();
        hideDataTable();
        appendMsg("user", q);
        if (inp) inp.value = "";
        appendPending();

        var url = sqlMode ? apiSql : apiAsk;
        var body = sqlMode ? { question: q } : { question: q, history: history };

        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            setLoading(false);
            removePending();
            if (res.success) {
                appendMsg("assistant", res.text || "");
                if (sqlMode && res.data && res.data.length) {
                    showDataTable(res.data);
                }
                if (!sqlMode) {
                    history.push({ q: q, a: res.text || "" });
                    if (history.length > 10) history.shift();
                }
            } else {
                showError(res.error || "Bir hata oluştu.");
            }
        })
        .catch(function(err) {
            setLoading(false);
            removePending();
            showError("Bağlantı hatası: " + (err.message || "Bilinmeyen"));
        });
    }

    function clearChat() {
        history = [];
        var msgs = $("kt_ai_messages");
        var empty = $("kt_ai_empty");
        if (msgs) msgs.innerHTML = "";
        if (empty) empty.classList.remove("d-none");
        hideError();
        hideDataTable();
        var inp = $("kt_ai_input");
        if (inp) inp.value = "";
    }

    function loadSummary() {
        fetch(apiSummary)
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (!res.success || !res.data) return;
            var d = res.data;
            var av = $("kt_ai_summary_avukat");
            var mem = $("kt_ai_summary_memur");
            var hf = $("kt_ai_summary_hafta");
            if (av && d.en_cok_avukat) av.textContent = (d.en_cok_avukat.d_avukat || "-") + " (" + (d.en_cok_avukat.sayi || 0) + ")";
            if (mem && d.en_cok_memur) mem.textContent = (d.en_cok_memur.d_memur || "-") + " (" + (d.en_cok_memur.sayi || 0) + ")";
            if (hf) hf.textContent = (d.bu_hafta_durusma_sayisi || 0) + " duruşma";
        });
    }

    function toggleSqlMode() {
        sqlMode = qs("#kt_ai_sql_mode") ? qs("#kt_ai_sql_mode").checked : false;
        var badge = $("kt_ai_mode_badge");
        var desc = $("kt_ai_mode_desc");
        var sqlOnly = document.querySelectorAll(".kt-ai-sql-only");
        if (badge) badge.textContent = sqlMode ? "SQL Sorgusu" : "Veri Analizi (RAG)";
        if (desc) desc.textContent = sqlMode ? "Veritabanından doğrudan sorgu (güvenli okuma)" : "Verilere dayalı sorular sorun";
        sqlOnly.forEach(function(el) { el.classList.toggle("d-none", !sqlMode); });
    }

    function init() {
        loadSummary();
        var sendBtn = $("kt_ai_send_btn");
        var clearBtn = $("kt_ai_clear_btn");
        var inp = $("kt_ai_input");
        var sqlChk = qs("#kt_ai_sql_mode");

        if (sendBtn) sendBtn.addEventListener("click", send);
        if (clearBtn) clearBtn.addEventListener("click", clearChat);
        if (inp) {
            inp.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    send();
                }
            });
        }
        if (sqlChk) sqlChk.addEventListener("change", toggleSqlMode);

        document.querySelectorAll(".kt-ai-quick").forEach(function(btn) {
            btn.addEventListener("click", function() {
                var t = (btn.textContent || "").trim();
                if (t && inp) {
                    inp.value = t;
                    send();
                }
            });
        });
    }

    KTUtil.onDOMContentLoaded(init);
})();
</script>
