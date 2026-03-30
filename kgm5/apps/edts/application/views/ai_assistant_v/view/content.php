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
                <li class="breadcrumb-item"><span class="svg-icon svg-icon-4 svg-icon-gray-700 mx-n1">→</span></li>
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">AI Asistan</li>
            </ul>
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                <i class="bi bi-robot me-2"></i>EDTS AI Asistan
            </h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge badge-light-primary fs-7" id="kt_ai_mode_badge">Veri Analizi (RAG)</span>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="kt_ai_sql_mode" />
                <label class="form-check-label fs-7" for="kt_ai_sql_mode">SQL Sorgusu</label>
            </div>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <!-- Özet kartları -->
        <div class="row g-5 g-xl-10 mb-5" id="kt_ai_summary_cards">
            <div class="col-md-4">
                <div class="card card-flush h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-person-badge fs-2x text-primary me-3"></i>
                        <div>
                            <span class="text-muted fs-7 d-block">En Çok Duruşma</span>
                            <span class="fw-bold fs-5" id="kt_ai_summary_avukat">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-flush h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-people fs-2x text-success me-3"></i>
                        <div>
                            <span class="text-muted fs-7 d-block">En Yoğun Memur</span>
                            <span class="fw-bold fs-5" id="kt_ai_summary_memur">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-flush h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-calendar-week fs-2x text-info me-3"></i>
                        <div>
                            <span class="text-muted fs-7 d-block">Bu Hafta Duruşma</span>
                            <span class="fw-bold fs-5" id="kt_ai_summary_hafta">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı aksiyonlar -->
        <div class="row g-5 g-xl-10 mb-5">
            <div class="col-12">
                <div class="card card-flush">
                    <div class="card-header">
                        <h3 class="card-title">Hızlı Erişim</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-wrap gap-3">
                            <a href="<?php echo base_url('durusmalar'); ?>" class="kt-ai-action-card card card-bordered flex-grow-1" style="max-width: 180px;">
                                <div class="card-body py-4 text-center">
                                    <i class="bi bi-list-ul fs-1 text-primary"></i>
                                    <div class="fw-bold mt-2">Duruşmalar</div>
                                    <div class="text-muted fs-8">Tüm listeyi gör</div>
                                </div>
                            </a>
                            <a href="<?php echo base_url('durusmalar/istatistik'); ?>" class="kt-ai-action-card card card-bordered flex-grow-1" style="max-width: 180px;">
                                <div class="card-body py-4 text-center">
                                    <i class="bi bi-bar-chart fs-1 text-success"></i>
                                    <div class="fw-bold mt-2">İstatistikler</div>
                                    <div class="text-muted fs-8">Grafikler</div>
                                </div>
                            </a>
                            <a href="<?php echo base_url('durusmalar/ara'); ?>" class="kt-ai-action-card card card-bordered flex-grow-1" style="max-width: 180px;">
                                <div class="card-body py-4 text-center">
                                    <i class="bi bi-search fs-1 text-info"></i>
                                    <div class="fw-bold mt-2">Arama</div>
                                    <div class="text-muted fs-8">Gelişmiş arama</div>
                                </div>
                            </a>
                            <a href="<?php echo base_url('dashboard'); ?>" class="kt-ai-action-card card card-bordered flex-grow-1" style="max-width: 180px;">
                                <div class="card-body py-4 text-center">
                                    <i class="bi bi-house fs-1 text-warning"></i>
                                    <div class="fw-bold mt-2">Dashboard</div>
                                    <div class="text-muted fs-8">Ana sayfa</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ana sohbet alanı -->
        <div class="row g-5 g-xl-10">
            <div class="col-12">
                <div class="card card-flush">
                    <div class="card-header align-items-center py-5">
                        <div class="card-title">
                            <span class="fw-bold">Sohbet</span>
                            <span class="text-muted fs-7 ms-2" id="kt_ai_mode_desc">Verilere dayalı sorular sorun</span>
                        </div>
                        <div class="card-toolbar">
                            <button type="button" id="kt_ai_clear_btn" class="btn btn-sm btn-light-danger">
                                <i class="bi bi-trash me-1"></i>Temizle
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Önerilen sorular -->
                        <div class="mb-4">
                            <span class="text-muted fs-7 me-2">Önerilen:</span>
                            <div class="d-flex flex-wrap gap-2" id="kt_ai_quick_btns">
                                <button type="button" class="kt-ai-quick btn btn-sm btn-light-primary">Bu ay kaç duruşma var?</button>
                                <button type="button" class="kt-ai-quick btn btn-sm btn-light-primary">En çok duruşma yapan avukat kim?</button>
                                <button type="button" class="kt-ai-quick btn btn-sm btn-light-primary">Bu hafta hangi duruşmalar var?</button>
                                <button type="button" class="kt-ai-quick btn btn-sm btn-light-primary">Taraf bazlı dağılım nasıl?</button>
                                <button type="button" class="kt-ai-quick btn btn-sm btn-light-primary">En yoğun mahkeme hangisi?</button>
                                <button type="button" class="kt-ai-quick btn btn-sm btn-light-success kt-ai-sql-only d-none">Şubat ayı duruşmalarını listele</button>
                                <button type="button" class="kt-ai-quick btn btn-sm btn-light-success kt-ai-sql-only d-none">Davacı tarafı kaç dosya var?</button>
                            </div>
                        </div>

                        <!-- Sohbet -->
                        <div id="kt_ai_chat_area" class="border rounded p-3 bg-light mb-4" style="overflow-y: auto;">
                            <div id="kt_ai_messages"></div>
                            <div id="kt_ai_empty" class="text-center text-muted py-5">
                                <i class="bi bi-chat-dots fs-1"></i>
                                <p class="mb-0 mt-2 fs-7">Sorunuzu yazın veya önerilen sorulardan birini seçin</p>
                            </div>
                        </div>

                        <!-- Giriş -->
                        <div class="d-flex gap-3 align-items-start">
                            <div class="flex-grow-1">
                                <textarea id="kt_ai_input" class="form-control form-control-solid" rows="2" placeholder="Duruşma verileri hakkında soru sorun veya SQL sorgusu yazın..."></textarea>
                            </div>
                            <button type="button" id="kt_ai_send_btn" class="btn btn-primary">
                                <span class="indicator-label">Gönder</span>
                                <span class="indicator-progress d-none">Bekleniyor... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>

                        <div id="kt_ai_error" class="mt-3 d-none">
                            <div class="alert alert-danger py-3"><span id="kt_ai_error_text"></span></div>
                        </div>

                        <!-- SQL sonuç tablosu -->
                        <div id="kt_ai_data_table_wrap" class="mt-4 d-none">
                            <h6 class="fw-bold mb-2">Sorgu Sonucu</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered kt-ai-data-table" id="kt_ai_data_table">
                                    <thead></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
