<link href="<?php echo base_url('assets/css/custom.css'); ?>?v=<?php echo asset_ver(); ?>" rel="stylesheet" type="text/css" />
<style>
/* Sayfa kaymasın, sadece mesaj listesi scroll olsun; yazma alanı her zaman altta sabit */
#kt_app_content.app-content { display: flex; flex-direction: column; min-height: 0; }
#kt_app_content_container.app-container { flex: 1; display: flex; flex-direction: column; min-height: 0; }
#yz_ekurum_chat_page { display: flex; flex-direction: column; flex: 1; min-height: 0; height: calc(100vh - 120px); max-height: calc(100vh - 120px); overflow: hidden; }
#yz_ekurum_chat_layout { display: flex; flex: 1; min-height: 0; overflow: hidden; }
#yz_ekurum_sessions_col { width: 240px; min-width: 200px; border-right: 1px solid var(--bs-border-color); display: flex; flex-direction: column; background: var(--bs-body-bg); flex-shrink: 0; }
#yz_ekurum_sessions_list { overflow-y: auto; flex: 1; padding: 0.5rem 0; min-height: 0; }
#yz_ekurum_chat_col { flex: 1; display: flex; flex-direction: column; min-width: 0; min-height: 0; overflow: hidden; }
#yz_ekurum_messages { flex: 1; overflow-y: auto; padding: 1rem; min-height: 0; }
#yz_ekurum_input_area { flex-shrink: 0; padding: 0.75rem 1rem; border-top: 1px solid var(--bs-border-color); background: var(--bs-body-bg); }
/* Hızlı aksiyon butonları – premium görünüm */
.yz-quick-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    font-size: 0.8125rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    border-radius: 0.5rem;
    border: 1px solid transparent;
    transition: transform 0.15s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    cursor: pointer;
}
.yz-quick-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
.yz-quick-btn:active { transform: translateY(0); }
.yz-quick-btn-primary {
    background: linear-gradient(135deg, #009ef7 0%, #0095e8 100%);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.2);
}
.yz-quick-btn-primary:hover {
    background: linear-gradient(135deg, #0095e8 0%, #0077c2 100%);
    color: #fff;
    box-shadow: 0 4px 14px rgba(0, 158, 247, 0.4);
}
.yz-quick-btn-secondary {
    background: linear-gradient(180deg, #f1faff 0%, #e8f4fc 100%);
    color: #009ef7;
    border-color: rgba(0, 158, 247, 0.25);
}
.yz-quick-btn-secondary:hover {
    background: linear-gradient(180deg, #e8f4fc 0%, #d0e9fa 100%);
    border-color: #009ef7;
    box-shadow: 0 4px 12px rgba(0, 158, 247, 0.2);
}
.yz-quick-btn-warning {
    background: linear-gradient(180deg, #fff8dd 0%, #ffecb5 100%);
    color: #b8860b;
    border-color: rgba(255, 193, 7, 0.4);
}
.yz-quick-btn-warning:hover {
    background: linear-gradient(180deg, #ffecb5 0%, #ffe082 100%);
    border-color: #ffc107;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.25);
}
.yz-quick-btn-outline {
    background: #fff;
    color: #5e6278;
    border-color: #e4e6ef;
}
.yz-quick-btn-outline:hover {
    background: #f9f9f9;
    border-color: #009ef7;
    color: #009ef7;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}
#yz_ekurum_quick_actions { flex-wrap: wrap; gap: 0.5rem; }
.yz-msg { max-width: 85%; margin-bottom: 0.75rem; }
.yz-msg.user { margin-left: auto; }
.yz-msg .yz-msg-bubble { padding: 0.6rem 0.9rem; border-radius: 0.75rem; display: inline-block; }
.yz-msg.user .yz-msg-bubble { background: var(--bs-primary); color: #fff; }
.yz-msg.model .yz-msg-bubble { background: var(--bs-gray-200); color: var(--bs-body-color); }
.yz-msg-time { font-size: 0.7rem; opacity: 0.8; margin-top: 0.2rem; }
.session-item { cursor: pointer; padding: 0.5rem 0.75rem; border-radius: 0.5rem; margin: 0 0.25rem; }
.session-item:hover { background: var(--bs-gray-200); }
.session-item.active { background: var(--bs-primary-light); color: var(--bs-primary); }
@media (max-width: 768px) { #yz_ekurum_sessions_col { width: 100%; max-height: 180px; border-right: none; border-bottom: 1px solid var(--bs-border-color); } #yz_ekurum_chat_layout { flex-direction: column; } }
</style>
