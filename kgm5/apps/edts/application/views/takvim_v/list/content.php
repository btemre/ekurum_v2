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
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">Takvim</li>
            </ul>
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">EDTS | Takvim</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label class="form-label mb-0 me-2 text-gray-700">Avukat:</label>
            <select id="takvim_avukat_filter" class="form-select form-select-solid form-select-sm" style="min-width: 180px;">
                <option value="">Tümü</option>
                <?php foreach (FormSelectSorumluAvukatList() as $sAvukatList) { ?>
                    <option value="<?php echo (int) $sAvukatList->u_id; ?>"><?php echo trim($sAvukatList->u_name . ' ' . $sAvukatList->u_lastname) . ' ' . $sAvukatList->u_surname; ?></option>
                <?php } ?>
            </select>
            <button type="button" class="btn btn-sm btn-light-primary" id="takvim_btn_export" title="Dışa aktar">
                <i class="bi bi-download"></i> Dışa aktar
            </button>
            <button type="button" class="btn btn-sm btn-light-primary" id="takvim_btn_notification_prefs" title="Bildirim ayarları">
                <i class="bi bi-gear"></i> Bildirim Ayarları
            </button>
            <?php if (isDbAllowedWriteModule('durusmalar')) { ?>
            <a href="#" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_durusmalar_manuel">Yeni Duruşma</a>
            <?php } ?>
        </div>
    </div>
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div id="takvim_calendar"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark"><i class="bi bi-bar-chart me-2"></i>Yoğunluk Analizi</span>
                        </h3>
                        <div class="card-toolbar">
                            <div class="btn-group btn-group-sm" id="takvim_density_granularity">
                                <button type="button" class="btn btn-light-primary active" data-gran="day">Günlük</button>
                                <button type="button" class="btn btn-light-primary" data-gran="week">Haftalık</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div id="takvim_density_list" class="d-flex flex-column gap-2">
                            <p class="text-muted mb-0">Yükleniyor...</p>
                        </div>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark"><i class="bi bi-calendar-check me-2"></i>Google / Outlook</span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <p class="text-muted small mb-3">Duruşmaları harici takvimde görmek için abonelik linkini kullanın.</p>
                        <button type="button" class="btn btn-sm btn-light-primary mb-2" id="takvim_btn_get_feed_url"><i class="bi bi-link-45deg me-1"></i>Abonelik linkini al</button>
                        <div id="takvim_feed_url_box" class="d-none">
                            <input type="text" class="form-control form-control-solid form-control-sm mb-2" id="takvim_feed_url_input" readonly />
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-light-primary" id="takvim_btn_copy_feed"><i class="bi bi-clipboard me-1"></i>Kopyala</button>
                                <a href="#" target="_blank" rel="noopener" class="btn btn-sm btn-light-danger" id="takvim_lnk_ics_download"><i class="bi bi-download me-1"></i>ICS indir</a>
                            </div>
                            <p class="text-muted small mt-2 mb-0">Google Takvim: Ayarlar → Takvimler → URL ile takvim ekle. Outlook: Takvim → Takvim ekle → İnternet takvimi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Duruşma detay modal -->
        <div class="modal fade" id="takvim_modal_event_detail" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i>Duruşma Detayı</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="takvim_event_detail_body">
                        <p class="text-muted mb-0">Yükleniyor...</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-primary" id="takvim_event_detail_edit_btn">Düzenle</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dışa aktar modal -->
        <div class="modal fade" id="takvim_modal_export" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-download me-2"></i>Takvim Dışa Aktar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Dönem</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="takvim_export_period" id="takvim_export_period_day" value="day" checked />
                                    <label class="form-check-label" for="takvim_export_period_day">Günlük</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="takvim_export_period" id="takvim_export_period_week" value="week" />
                                    <label class="form-check-label" for="takvim_export_period_week">Haftalık</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="takvim_export_period" id="takvim_export_period_month" value="month" />
                                    <label class="form-check-label" for="takvim_export_period_month">Aylık</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="takvim_export_period" id="takvim_export_period_range" value="range" />
                                    <label class="form-check-label" for="takvim_export_period_range">Tarih aralığı</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4" id="takvim_export_date_group">
                            <label class="form-label fw-bold" for="takvim_export_date">Tarih</label>
                            <input type="date" class="form-control form-control-solid" id="takvim_export_date" />
                        </div>
                        <div class="mb-4 d-none" id="takvim_export_range_group">
                            <label class="form-label fw-bold">Tarih aralığı</label>
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <input type="date" class="form-control form-control-solid" id="takvim_export_start_date" />
                                <span class="text-gray-600">–</span>
                                <input type="date" class="form-control form-control-solid" id="takvim_export_end_date" />
                            </div>
                        </div>
                        <div class="mb-4 d-none" id="takvim_export_month_group">
                            <label class="form-label fw-bold">Ay / Yıl</label>
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-solid" id="takvim_export_month">
                                    <option value="1">Ocak</option>
                                    <option value="2">Şubat</option>
                                    <option value="3">Mart</option>
                                    <option value="4">Nisan</option>
                                    <option value="5">Mayıs</option>
                                    <option value="6">Haziran</option>
                                    <option value="7">Temmuz</option>
                                    <option value="8">Ağustos</option>
                                    <option value="9">Eylül</option>
                                    <option value="10">Ekim</option>
                                    <option value="11">Kasım</option>
                                    <option value="12">Aralık</option>
                                </select>
                                <select class="form-select form-select-solid" id="takvim_export_year"></select>
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Format</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="takvim_export_format" id="takvim_export_format_excel" value="excel" checked />
                                    <label class="form-check-label" for="takvim_export_format_excel">Excel</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="takvim_export_format" id="takvim_export_format_pdf" value="pdf" />
                                    <label class="form-check-label" for="takvim_export_format_pdf">PDF</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-primary" id="takvim_btn_export_submit"><i class="bi bi-download me-1"></i>İndir</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bildirim ayarları modal -->
        <div class="modal fade" id="takvim_modal_notification_prefs" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bildirim Ayarları</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="takvim_pref_email" />
                                <label class="form-check-label" for="takvim_pref_email">E-posta bildirimleri</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="takvim_pref_push" />
                                <label class="form-check-label" for="takvim_pref_push">Tarayıcı push bildirimleri</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kaç dakika önce hatırlat</label>
                            <select class="form-select form-select-solid" id="takvim_pref_reminder">
                                <option value="60">1 saat önce</option>
                                <option value="360">6 saat önce</option>
                                <option value="1440" selected>24 saat önce</option>
                                <option value="4320">3 gün önce</option>
                                <option value="10080">1 hafta önce</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-primary" id="takvim_btn_save_prefs">Kaydet</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Content-->
