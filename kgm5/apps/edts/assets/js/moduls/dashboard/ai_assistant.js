"use strict";

var KTAiAssistant = function () {
    var aiInput = document.getElementById("kt_ai_assistant_input");
    var aiBtn = document.getElementById("kt_ai_assistant_btn");
    var aiClear = document.getElementById("kt_ai_assistant_clear");
    var messagesDiv = document.getElementById("kt_ai_assistant_messages");
    var emptyDiv = document.getElementById("kt_ai_assistant_empty");
    var errorDiv = document.getElementById("kt_ai_assistant_error");
    var errorText = document.getElementById("kt_ai_assistant_error_text");
    var chatContainer = document.getElementById("kt_ai_assistant_chat");

    var hostX = window.location.host;
    var baseUrlHost = "//" + hostX;
    var apiUrl = baseUrlHost + "/apps/edts/ai_assistant/api_ask";

    var history = [];

    var setLoading = function (loading) {
        var label = aiBtn.querySelector(".indicator-label");
        var progress = aiBtn.querySelector(".indicator-progress");
        if (loading) {
            label.classList.add("d-none");
            progress.classList.remove("d-none");
            aiBtn.disabled = true;
        } else {
            label.classList.remove("d-none");
            progress.classList.add("d-none");
            aiBtn.disabled = false;
        }
    };

    var hideError = function () {
        errorDiv.classList.add("d-none");
    };

    var showError = function (text) {
        errorText.textContent = text;
        errorDiv.classList.remove("d-none");
    };

    var escapeHtml = function (str) {
        var div = document.createElement("div");
        div.textContent = str;
        return div.innerHTML;
    };

    var formatText = function (text) {
        if (!text) return "";
        return escapeHtml(text)
            .replace(/\n/g, "<br>")
            .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
            .replace(/\*(.*?)\*/g, "<em>$1</em>");

    };

    var appendMessage = function (role, text) {
        if (!messagesDiv) return;
        emptyDiv.classList.add("d-none");
        var msgDiv = document.createElement("div");
        msgDiv.className = "mb-3";
        if (role === "user") {
            msgDiv.innerHTML = '<div class="d-flex justify-content-end"><div class="bg-primary text-white rounded px-3 py-2" style="max-width: 85%"><span class="text-white">' + formatText(text) + "</span></div></div>";
        } else {
            msgDiv.innerHTML = '<div class="d-flex justify-content-start"><div class="bg-light border rounded px-3 py-2" style="max-width: 85%"><span class="text-gray-700">' + formatText(text) + "</span></div></div>";
        }
        messagesDiv.appendChild(msgDiv);
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    };

    var appendPending = function () {
        var pending = document.createElement("div");
        pending.id = "kt_ai_pending";
        pending.className = "mb-3 d-flex justify-content-start";
        pending.innerHTML = '<div class="bg-light border rounded px-3 py-2"><span class="spinner-border spinner-border-sm text-primary me-2" role="status"></span><span class="text-muted">Yanıtlanıyor...</span></div>';
        messagesDiv.appendChild(pending);
        if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;
    };

    var removePending = function () {
        var p = document.getElementById("kt_ai_pending");
        if (p) p.remove();
    };

    var clearChat = function () {
        history = [];
        if (messagesDiv) messagesDiv.innerHTML = "";
        if (emptyDiv) emptyDiv.classList.remove("d-none");
        hideError();
        if (aiInput) aiInput.value = "";
    };

    var ask = function (question) {
        if (!aiInput || !aiBtn) return;

        question = (question || (aiInput && aiInput.value) || "").trim();
        if (!question) {
            showError("Lütfen bir soru girin.");
            return;
        }

        setLoading(true);
        hideError();
        appendMessage("user", question);
        if (aiInput) aiInput.value = "";
        appendPending();

        fetch(apiUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ question: question, history: history })
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setLoading(false);
                removePending();
                if (data.success) {
                    appendMessage("assistant", data.text || "");
                    history.push({ q: question, a: data.text || "" });
                    if (history.length > 10) history.shift();
                } else {
                    showError(data.error || "Bir hata oluştu.");
                }
            })
            .catch(function (err) {
                setLoading(false);
                removePending();
                showError("Bağlantı hatası: " + (err.message || "Bilinmeyen hata"));
            });
    };

    var init = function () {
        if (!aiInput || !aiBtn) return;

        aiBtn.addEventListener("click", function (e) {
            e.preventDefault();
            ask();
        });

        aiInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                ask();
            }
        });

        if (aiClear) {
            aiClear.addEventListener("click", function (e) {
                e.preventDefault();
                clearChat();
            });
        }

        var quickBtns = document.querySelectorAll(".kt-ai-quick-btn");
        quickBtns.forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                var q = (btn.textContent || "").trim();
                if (q) ask(q);
            });
        });
    };

    return { init: init, clear: clearChat };
}();

KTUtil.onDOMContentLoaded(function () {
    KTAiAssistant.init();
});
