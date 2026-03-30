<?php $this->load->view("includes/alert"); ?>
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 mb-1">
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
                    <a href="<?php echo base_url('dashboard'); ?>" class="text-gray-700 text-hover-primary me-1">
                        <i class="fonticon-home text-gray-700 fs-3"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <span class="svg-icon svg-icon-4 svg-icon-gray-700 mx-n1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6343 12.5657L8.45001 16.75C8.0358 17.1642 8.0358 17.8358 8.45001 18.25C8.86423 18.6642 9.5358 18.6642 9.95001 18.25L15.4929 12.7071C15.8834 12.3166 15.8834 11.6834 15.4929 11.2929L9.95001 5.75C9.5358 5.33579 8.86423 5.33579 8.45001 5.75C8.0358 6.16421 8.0358 6.83579 8.45001 7.25L12.6343 11.4343C12.9467 11.7467 12.9467 12.2533 12.6343 12.5657Z" fill="currentColor" />
                        </svg>
                    </span>
                </li>
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">YZ Ekürüm</li>
            </ul>
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Gelişmiş AI Asistan - YZ Ekürüm</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span id="yz_quota_badge" class="badge badge-light-primary fs-7">Kota yükleniyor...</span>
        </div>
    </div>
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div id="yz_ekurum_chat_page" class="card">
            <div id="yz_ekurum_chat_layout">
                <!-- Sol: Konuşma geçmişi -->
                <div id="yz_ekurum_sessions_col">
                    <div class="p-3 border-bottom border-gray-200">
                        <button type="button" class="btn btn-sm btn-primary w-100" id="yz_btn_new_chat">
                            <i class="bi bi-plus-lg me-1"></i>Yeni sohbet
                        </button>
                    </div>
                    <div id="yz_ekurum_sessions_list"></div>
                </div>
                <!-- Orta: Mesajlar + giriş -->
                <div id="yz_ekurum_chat_col">
                    <div id="yz_ekurum_messages">
                        <div id="yz_ekurum_messages_placeholder" class="text-center text-muted py-5">
                            <i class="bi bi-chat-dots fs-1"></i>
                            <p class="mb-0 mt-2">Yeni bir sohbet başlatın veya soldan bir konuşma seçin.</p>
                            <p class="small mt-1">Hızlı aksiyonlardan birini seçerek analiz isteyebilirsiniz.</p>
                        </div>
                    </div>
                    <div id="yz_ekurum_input_area">
                        <div id="yz_ekurum_quick_actions" class="d-flex flex-wrap align-items-center gap-2 mb-3">
                            <button type="button" class="yz-quick-btn yz-quick-btn-primary" data-action="haftalik"><i class="bi bi-calendar-week me-2"></i>Haftalık analiz</button>
                            <button type="button" class="yz-quick-btn yz-quick-btn-primary" data-action="aylik"><i class="bi bi-calendar-month me-2"></i>Aylık analiz</button>
                            <button type="button" class="yz-quick-btn yz-quick-btn-secondary" data-action="avukat"><i class="bi bi-person-badge me-2"></i>Avukat bazında</button>
                            <button type="button" class="yz-quick-btn yz-quick-btn-secondary" data-action="memur"><i class="bi bi-person-vcard me-2"></i>Memur bazında</button>
                            <button type="button" class="yz-quick-btn yz-quick-btn-secondary" data-action="mahkeme"><i class="bi bi-building me-2"></i>Mahkeme bazında</button>
                            <button type="button" class="yz-quick-btn yz-quick-btn-secondary" data-action="taraf"><i class="bi bi-people me-2"></i>Taraf bazında</button>
                            <button type="button" class="yz-quick-btn yz-quick-btn-secondary" data-action="islem"><i class="bi bi-list-check me-2"></i>İşlem bazında</button>
                            <button type="button" class="yz-quick-btn yz-quick-btn-warning" data-action="gecmis"><i class="bi bi-clock-history me-2"></i>Geçmiş dönem</button>
                            <button type="button" class="yz-quick-btn yz-quick-btn-outline" data-action="esas_no"><i class="bi bi-hash me-2"></i>Esas No durum</button>
                            <button type="button" class="yz-quick-btn yz-quick-btn-outline" data-action="dosya_no"><i class="bi bi-file-earmark-text me-2"></i>Dosya No durum</button>
                            <button type="button" class="yz-quick-btn yz-quick-btn-outline" data-action="durusma_tarih"><i class="bi bi-calendar-event me-2"></i>Duruşma tarihlerine göre</button>
                        </div>
                        <div class="d-flex gap-2 align-items-end">
                            <textarea id="yz_ekurum_input" class="form-control form-control-solid" rows="2" placeholder="Mesajınızı yazın veya yukarıdaki hızlı aksiyonlardan birini kullanın..." maxlength="4000"></textarea>
                            <button type="button" class="btn btn-primary" id="yz_ekurum_send_btn">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Content-->
