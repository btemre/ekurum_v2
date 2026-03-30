<!--begin::AI Özet Modal-->
<div class="modal fade" id="kt_modal_ai_summary" tabindex="-1" aria-hidden="true" aria-modal="true" aria-labelledby="ai_modal_title">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light-primary">
                <h3 class="modal-title fw-bold">
                    <i class="bi bi-stars text-primary fs-2 me-2"></i>
                    <span id="ai_modal_title" class="modal-title">AI Özet</span>
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg fs-4"></i>
                </div>
            </div>
            <div class="modal-body overflow-auto" style="max-height: 70vh;">
                <!--begin::Loading-->
                <div id="ai_loading" class="text-center py-10 d-none">
                    <div class="spinner-border text-primary mb-4" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                    <p class="text-gray-600 fs-5 fw-semibold">AI özet oluşturuluyor, lütfen bekleyiniz...</p>
                </div>
                <!--end::Loading-->
                <!--begin::Error-->
                <div id="ai_error" class="d-none">
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-2 me-3"></i>
                        <div id="ai_error_message"></div>
                    </div>
                </div>
                <!--end::Error-->
                <!--begin::Result-->
                <div id="ai_result" class="d-none">
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-light-primary fs-7 fw-semibold me-3">
                            <i class="bi bi-robot me-1"></i>Gemini AI
                        </span>
                        <span id="ai_quota_badge" class="badge badge-light-warning fs-8"></span>
                    </div>
                    <div id="ai_summary_content" class="border rounded p-5 bg-light-primary fs-6 ai-html-content" style="line-height: 1.8;"></div>
                    <style>
                        .ai-html-content h5, .ai-html-content h6 { margin-top: 1rem; margin-bottom: 0.5rem; color: #3f4254; }
                        .ai-html-content ul, .ai-html-content ol { padding-left: 1.5rem; margin-bottom: 0.75rem; }
                        .ai-html-content li { margin-bottom: 0.3rem; }
                        .ai-html-content table { margin-top: 0.5rem; margin-bottom: 1rem; }
                        .ai-html-content table th { background-color: #f5f8fa; font-weight: 600; }
                        .ai-html-content p { margin-bottom: 0.5rem; }
                        .ai-html-content strong { color: #181c32; }
                        .ai-html-content hr { margin: 0.75rem 0; }
                    </style>
                </div>
                <!--end::Result-->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-light-primary" id="ai_copy_btn" onclick="aiCopyResult()" title="Panoya Kopyala" style="display:none;">
                    <i class="bi bi-clipboard me-1"></i>Kopyala
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::AI Özet Modal-->

<!--begin::AI Doğal Dil Arama Modal-->
<div class="modal fade" id="kt_modal_ai_search" tabindex="-1" aria-hidden="true" aria-modal="true" aria-labelledby="ai_search_modal_title">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light-info">
                <h3 class="modal-title fw-bold" id="ai_search_modal_title">
                    <i class="bi bi-search text-info fs-2 me-2"></i>
                    AI ile Doğal Dil Araması
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-info ms-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg fs-4"></i>
                </div>
            </div>
            <div class="modal-body overflow-auto" style="max-height: 75vh;">
                <!--begin::Search Input-->
                <div class="mb-5">
                    <label class="form-label fw-bold fs-6">Sorunuzu doğal dilde yazın:</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light-info border-info"><i class="bi bi-stars text-info fs-3"></i></span>
                        <input type="text" class="form-control border-info" id="ai_search_input"
                            placeholder="Örn: En çok dosyası olan mahkeme hangisi?"
                            maxlength="500"
                            onkeydown="if(event.key==='Enter') AiService.requestTextToSQL();">
                        <button class="btn btn-info fw-bold" type="button" onclick="AiService.requestTextToSQL()">
                            <i class="bi bi-send me-1"></i>Sorgula
                        </button>
                    </div>
                    <div class="form-text text-muted mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Örnek sorular: "Kaç aktif dosya var?", "Kamulaştırma konulu dosyalar kaç tane?", "Hangi mahkemede en çok dosya var?"
                    </div>
                </div>
                <!--end::Search Input-->
                <!--begin::Search Loading-->
                <div id="ai_search_loading" class="text-center py-8 d-none">
                    <div class="spinner-border text-info mb-3" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Sorgulanıyor...</span>
                    </div>
                    <p class="text-gray-600 fs-6 fw-semibold">AI sorgunuzu analiz ediyor ve veritabanını sorguluyor...</p>
                </div>
                <!--end::Search Loading-->
                <!--begin::Search Error-->
                <div id="ai_search_error" class="d-none">
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-2 me-3"></i>
                        <div id="ai_search_error_message"></div>
                    </div>
                </div>
                <!--end::Search Error-->
                <!--begin::Search Results-->
                <div id="ai_search_results" class="d-none">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <span class="badge badge-light-info fs-7 fw-semibold me-2">
                                <i class="bi bi-robot me-1"></i>AI Sorgusu
                            </span>
                            <span id="ai_search_total" class="badge badge-light-success fs-7 fw-semibold me-2"></span>
                            <span id="ai_search_quota" class="badge badge-light-warning fs-8"></span>
                        </div>
                        <button class="btn btn-sm btn-light-info" onclick="aiToggleSQL()" title="Oluşturulan SQL'i göster/gizle">
                            <i class="bi bi-code-slash me-1"></i>SQL
                        </button>
                    </div>
                    <div id="ai_search_sql_box" class="d-none mb-4">
                        <pre class="bg-dark text-light rounded p-4 fs-7" style="white-space: pre-wrap;"><code id="ai_search_sql"></code></pre>
                    </div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-row-bordered table-row-dashed table-striped gy-4 gs-4 align-middle" id="ai_search_table">
                            <thead id="ai_search_thead" class="bg-light-info fs-7 fw-bold text-uppercase"></thead>
                            <tbody id="ai_search_tbody" class="fs-7"></tbody>
                        </table>
                    </div>
                </div>
                <!--end::Search Results-->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
<!--end::AI Doğal Dil Arama Modal-->
