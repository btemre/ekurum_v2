/**
 * YZ Ekürüm - Gelişmiş AI Asistan sohbet arayüzü
 */
var YzEkurumChat = (function () {
    var baseUrl = '';
    var endpoints = { sessions: '', sessionMessages: '', chat: '', quota: '' };
    var currentSessionId = 0;
    var sending = false;

    var _allowedTags = ['h1','h2','h3','h4','h5','h6','p','br','hr',
        'strong','b','em','i','u','small','mark',
        'ul','ol','li','dl','dt','dd',
        'table','thead','tbody','tfoot','tr','th','td',
        'div','span','blockquote','pre','code',
        'a','img'];
    var _allowedAttrs = ['class','style','href','target','colspan','rowspan','src','alt','width','height'];

    function _escapeHtml(text) {
        if (text == null) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(String(text)));
        return div.innerHTML;
    }

    function _sanitizeHtml(html) {
        if (!html) return '';
        var temp = document.createElement('div');
        temp.innerHTML = html;
        var all = temp.querySelectorAll('*');
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
                    var val = (attrs[a].value || '').trim().toLowerCase();
                    if (val.indexOf('javascript:') === 0 || val.indexOf('data:') === 0) {
                        el.removeAttribute(attrs[a].name);
                    }
                }
            }
            if (tag === 'table' && (!el.className || el.className.indexOf('table') === -1)) {
                el.className = (el.className ? el.className + ' ' : '') + 'table table-sm table-bordered';
            }
        }
        return temp.innerHTML;
    }

    function _formatTime(timestamp) {
        if (!timestamp) return '';
        var d = new Date(timestamp * 1000);
        return d.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
    }

    function getMessagesEl() {
        return document.getElementById('yz_ekurum_messages');
    }

    function getPlaceholderEl() {
        return document.getElementById('yz_ekurum_messages_placeholder');
    }

    function appendMessage(role, content, createdAt) {
        var container = getMessagesEl();
        var placeholder = getPlaceholderEl();
        if (placeholder && placeholder.parentNode) {
            placeholder.style.display = 'none';
        }
        var msgDiv = document.createElement('div');
        msgDiv.className = 'yz-msg ' + role;
        var bubble = document.createElement('div');
        bubble.className = 'yz-msg-bubble';
        if (role === 'model') {
            bubble.innerHTML = _sanitizeHtml(content);
        } else {
            bubble.textContent = content;
        }
        msgDiv.appendChild(bubble);
        if (createdAt) {
            var timeSpan = document.createElement('div');
            timeSpan.className = 'yz-msg-time';
            timeSpan.textContent = _formatTime(createdAt);
            msgDiv.appendChild(timeSpan);
        }
        container.appendChild(msgDiv);
        container.scrollTop = container.scrollHeight;
    }

    function showLoadingMessage() {
        var container = getMessagesEl();
        var placeholder = getPlaceholderEl();
        if (placeholder) placeholder.style.display = 'none';
        var div = document.createElement('div');
        div.className = 'yz-msg model';
        div.id = 'yz_loading_msg';
        div.innerHTML = '<div class="yz-msg-bubble"><span class="spinner-border spinner-border-sm me-2"></span>YZ Ekürüm düşünüyor...</div>';
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function removeLoadingMessage() {
        var el = document.getElementById('yz_loading_msg');
        if (el && el.parentNode) el.parentNode.removeChild(el);
    }

    function renderMessagesList(messages) {
        var container = getMessagesEl();
        var placeholder = getPlaceholderEl();
        container.innerHTML = '';
        if (placeholder) {
            placeholder.style.display = messages.length === 0 ? 'block' : 'none';
            if (messages.length === 0) {
                container.appendChild(placeholder);
            }
        }
        var now = Math.floor(Date.now() / 1000);
        messages.forEach(function (m) {
            appendMessage(m.role, m.content, m.created_at || now);
        });
        container.scrollTop = container.scrollHeight;
    }

    function renderSessionsList(sessions) {
        var listEl = document.getElementById('yz_ekurum_sessions_list');
        if (!listEl) return;
        listEl.innerHTML = '';
        sessions.forEach(function (s) {
            var item = document.createElement('div');
            item.className = 'session-item' + (s.session_id === currentSessionId ? ' active' : '');
            item.setAttribute('data-session-id', s.session_id);
            var title = (s.title || 'Sohbet').substring(0, 30);
            if ((s.title || '').length > 30) title += '...';
            item.textContent = title;
            item.title = s.title || '';
            item.addEventListener('click', function () {
                selectSession(s.session_id);
            });
            listEl.appendChild(item);
        });
    }

    function selectSession(sessionId) {
        currentSessionId = sessionId;
        document.querySelectorAll('#yz_ekurum_sessions_list .session-item').forEach(function (el) {
            el.classList.toggle('active', parseInt(el.getAttribute('data-session-id'), 10) === sessionId);
        });
        loadMessages(sessionId);
    }

    function _apiUrl(path) {
        var base = (baseUrl || '').replace(/\/$/, '');
        var p = (path || '').replace(/^\//, '');
        return base ? (base + '/' + p) : p;
    }

    function loadSessions() {
        var url = _apiUrl(endpoints.sessions);
        fetch(url, { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success && data.data && data.data.sessions) {
                    renderSessionsList(data.data.sessions);
                }
            })
            .catch(function () { renderSessionsList([]); });
    }

    function loadMessages(sessionId) {
        var url = _apiUrl(endpoints.sessionMessages + '/' + sessionId);
        fetch(url, { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success && data.data && data.data.messages) {
                    renderMessagesList(data.data.messages);
                }
            })
            .catch(function () { renderMessagesList([]); });
    }

    function updateQuota(remaining) {
        var badge = document.getElementById('yz_quota_badge');
        if (!badge) return;
        if (remaining === 'Limit doldu' || remaining === '-') {
            badge.textContent = remaining;
        } else if (typeof remaining === 'number') {
            badge.textContent = 'Kalan hak: ' + remaining;
        } else {
            badge.textContent = 'Kota yükleniyor...';
        }
    }

    function loadQuota() {
        var url = _apiUrl(endpoints.quota);
        fetch(url, { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success && data.data && data.data.remaining_quota !== undefined) {
                    updateQuota(data.data.remaining_quota);
                }
            })
            .catch(function () { updateQuota('-'); });
    }

    function newChat() {
        currentSessionId = 0;
        document.querySelectorAll('#yz_ekurum_sessions_list .session-item').forEach(function (el) {
            el.classList.remove('active');
        });
        var container = getMessagesEl();
        var placeholder = getPlaceholderEl();
        container.innerHTML = '';
        if (placeholder) {
            placeholder.style.display = 'block';
            container.appendChild(placeholder);
        }
    }

    function sendMessage(message, quickAction) {
        if (sending || !message.trim()) return;
        sending = true;
        var sendBtn = document.getElementById('yz_ekurum_send_btn');
        if (sendBtn) sendBtn.disabled = true;

        var now = Math.floor(Date.now() / 1000);
        appendMessage('user', message, now);
        showLoadingMessage();

        var payload = { message: message.trim() };
        if (currentSessionId > 0) payload.session_id = currentSessionId;
        if (quickAction) payload.quick_action = quickAction;

        var url = _apiUrl(endpoints.chat);
        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(payload)
        })
            .then(function (res) {
                return res.json().then(function (data) { return { status: res.status, data: data }; });
            })
            .then(function (result) {
                var data = result.data;
                var status = result.status;
                removeLoadingMessage();
                if (status === 429) {
                    updateQuota('Limit doldu');
                    appendMessage('model', 'Hata: ' + (data.description || 'Günlük AI kullanım limitiniz dolmuştur. Yarın tekrar deneyiniz.'), null);
                    return;
                }
                if (status === 503) {
                    appendMessage('model', 'Hata: ' + (data.description || 'Çok fazla eşzamanlı istek. Lütfen kısa süre sonra tekrar deneyin.'), null);
                    return;
                }
                if (data.success && data.data) {
                    if (data.data.session_id) {
                        currentSessionId = data.data.session_id;
                        loadSessions();
                    }
                    if (data.data.message) {
                        appendMessage('model', data.data.message, now);
                    }
                    if (data.data.remaining_quota !== undefined) {
                        updateQuota(data.data.remaining_quota);
                    }
                } else {
                    appendMessage('model', 'Hata: ' + (data.description || 'Bilinmeyen hata.'), null);
                }
            })
            .catch(function (err) {
                removeLoadingMessage();
                appendMessage('model', 'Bağlantı hatası: ' + (err.message || 'Yanıt alınamadı.'), null);
            })
            .finally(function () {
                sending = false;
                if (sendBtn) sendBtn.disabled = false;
            });
    }

    function init(options) {
        if (!options) return;
        baseUrl = (options.baseUrl || '').replace(/\/$/, '');
        endpoints = options.endpoints || endpoints;

        loadSessions();
        loadQuota();

        var btnNew = document.getElementById('yz_btn_new_chat');
        if (btnNew) {
            btnNew.addEventListener('click', function () { newChat(); });
        }

        var sendBtn = document.getElementById('yz_ekurum_send_btn');
        var inputEl = document.getElementById('yz_ekurum_input');
        if (sendBtn && inputEl) {
            sendBtn.addEventListener('click', function () {
                var text = inputEl.value.trim();
                if (text) {
                    sendMessage(text, '');
                    inputEl.value = '';
                }
            });
        }
        if (inputEl) {
            inputEl.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    var text = inputEl.value.trim();
                    if (text) {
                        sendMessage(text, '');
                        inputEl.value = '';
                    }
                }
            });
        }

        var quickActionPrompts = {
            haftalik: 'Bu haftanın duruşma özetini ve haftalık analizi verir misin?',
            aylik: 'Aylık duruşma ve karar dağılımı analizi yapar mısın?',
            avukat: 'Avukat bazında duruşma analizi yapar mısın?',
            memur: 'Memur bazında duruşma analizi yapar mısın?',
            mahkeme: 'Mahkeme bazında duruşma analizi yapar mısın?',
            taraf: 'Taraf bazında duruşma analizi yapar mısın?',
            islem: 'İşlem bazında duruşma analizi yapar mısın?',
            gecmis: 'Geçmiş dönem duruşma analizi yapar mısın?',
            esas_no: 'Esas numarasına göre durum analizi yapabilir misin? Lütfen esas numarası belirtin.',
            dosya_no: 'Dosya numarasına göre durum analizi yapabilir misin? Lütfen dosya numarası belirtin.',
            durusma_tarih: 'Duruşma tarihlerine göre durum analizi yapar mısın?',
            ara: 'Duruşmalarda arama yapmak istiyorum. Ne aramak istediğinizi yazın.'
        };
        document.querySelectorAll('#yz_ekurum_quick_actions [data-action]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var action = this.getAttribute('data-action');
                var prompt = inputEl ? inputEl.value.trim() : '';
                if (action === 'ara' && !prompt) {
                    if (inputEl) inputEl.placeholder = 'Örn: Bu ay en çok duruşması olan avukat kim?';
                    if (inputEl) inputEl.focus();
                    return;
                }
                var messageToSend = prompt || (quickActionPrompts[action] || action);
                sendMessage(messageToSend, prompt ? action : action);
                if (inputEl) inputEl.value = '';
            });
        });
    }

    return { init: init };
})();
