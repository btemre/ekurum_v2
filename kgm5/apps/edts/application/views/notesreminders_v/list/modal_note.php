<!--begin::Modal - Not Ekle/Düzenle-->
<div class="modal fade" id="kt_modal_note" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder" id="kt_modal_note_title">Yeni Not</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </div>
            </div>
            <form id="kt_modal_note_form" method="post" action="">
                <div class="modal-body scroll-y mx-5 mx-xl-15">
                    <input type="hidden" name="n_id" id="note_n_id" value="" />
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-bold form-label mb-2">Başlık <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-solid" name="n_title" id="note_n_title" placeholder="Not başlığı" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-bold form-label mb-2">İçerik</label>
                        <textarea class="form-control form-control-solid" name="n_content" id="note_n_content" rows="4" placeholder="İçerik (isteğe bağlı)"></textarea>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-bold form-label mb-2">Etiket</label>
                        <select class="form-select form-select-solid" name="n_tag" id="note_n_tag">
                            <option value="">Etiket seçin (isteğe bağlı)</option>
                            <option value="unutma">Unutma</option>
                            <option value="acil">Acil</option>
                            <option value="onemli">Önemli</option>
                            <option value="dikkat_et">Dikkat Et</option>
                            <option value="duzeltilecek">Düzeltilecek</option>
                            <option value="sor">Sor</option>
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-bold form-label mb-2">Hatırlatma tarihi ve saati</label>
                        <input type="datetime-local" class="form-control form-control-solid" name="n_reminder_at" id="note_n_reminder_at" />
                        <div class="form-text">Boş bırakılırsa sadece not olarak kaydedilir.</div>
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->
