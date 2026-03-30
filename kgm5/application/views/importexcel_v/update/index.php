<!--begin::Modal - New Target-->
<div class="modal fade" id="kt_modal_import_durusmalar" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-1000px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 justify-content-end">
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" >
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Close-->
            </div>
            <!--begin::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <!--begin:Form-->
                <form id="kt_modal_import_durusmalar_form" class="form" action="#">
                    <!--begin::Heading-->
                    <div class="mb-13 text-center">
                        <!--begin::Title-->
                        <h1 class="mb-3">Duruşma Dosyası</h1>
                        <!--end::Title-->
                        <!--begin::Description-->
                        <div class="text-muted fw-bold fs-5">Seçtiğiniz Satırdaki Veriler Eklediğiniz Bilgilerle Birlikte Tutulacaktır.
                        </div>
                        <!--end::Description-->
                    </div>
                    <div class="row g-4 mb-4">
                        <!--begin::Label-->
                        <div class="col-md-2 fv-row">
                            <label class="required fs-6 fw-bold mb-2">Dosya No</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text"><i class="bi bi bi-file-earmark-binary fs-3"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input type="text" class="form-control form-control-solid" name="xls_dm_dosyano" id="xls_dm_dosyano" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md fv-row">
                            <label class="required fs-6 fw-bold mb-2">Esas No</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text"><i class="bi bi bi-file-earmark-binary fs-3"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input type="text" class="form-control form-control-solid" name="xls_dm_esasno" id="xls_dm_esasno" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md fv-row">
                            <label class="required fs-6 fw-bold mb-2">Sorumlu Avukat</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text"><i class="bi bi-person-circle fs-3"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <select class="form-select form-select-solid"  data-control="select2" data-dropdown-parent="#kt_modal_import_durusmalar" data-hide-search="false" data-placeholder="Seçiniz.." name="xls_dm_avukat" id="xls_dm_avukat">
                                        <option value="">Seçiniz..</option>
                                        <?php foreach(FormSelectSorumluAvukatList() as $sAvukatList){ ?>
                                            <option value="<?php echo $sAvukatList->u_id;?>"><?php echo trim($sAvukatList->u_name . ' '.$sAvukatList->u_lastname). ' '.$sAvukatList->u_surname;?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--begin::Col-->
                        <div class="col-md mb-8 fv-row">
								<!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
									<span class="">İlgili Avukat(lar)</span>
									<!-- <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Evrağın Kaynağını Belirtir. Kurum, Birim, Kişi vb."></i> -->
								</label>
								<input class="form-control form-control-solid" value="" name="xls_dm_ilgiliavukat" id="xls_dm_ilgiliavukat" />
                        </div>
                        <!--end::Col-->
                    </div>
                    <div class="row g-4 mb-4">
                        <!--begin::Col-->
                        <div class="col-md fv-row">
                            <label class="required fs-6 fw-bold mb-2">Taraf</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text"><i class="bi bi-person-workspace fs-3"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Seçiniz.." name="xls_dm_taraf" id="xls_dm_taraf">
                                        <option value="">Seçiniz..</option>
                                        <option value="DAVALI">DAVALI</option>
                                        <option value="DAVACI">DAVACI</option>
                                        <option value="DAHİLİ DAVACI">DAHİLİ DAVACI</option>
                                        <option value="KATILAN">KATILAN</option>
                                        <option value="MÜŞTEKİ">MÜŞTEKİ</option>
                                        <option value="İHBAR OLUNAN">İHBAR OLUNAN</option>
                                        <option value="KONTROL">KONTROL</option>
                                        <option value="KEŞİF">KEŞİF</option>
                                        <option value="KEŞİF">GENEL MÜDÜRLÜK</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md fv-row">
                            <label class="fs-6 fw-bold mb-2">Duruşma Takibi</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text"><i class="bi bi-person-workspace fs-3"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Seçiniz.." name="xls_dm_durusmaislemi" id="xls_dm_durusmaislemi">
                                        <option value="">Seçiniz..</option>
                                        <option value="1">Duruşmaya Gidildi</option>
                                        <option value="2">Mazeret Çekildi</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 fv-row">
                                <label class=" fs-6 fw-bold mb-4">Duruşma Tutanağı</label>
                                <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input name="xls_dm_tutanakbilgi" id="xls_dm_tutanakbilgi" class="form-check-input" type="checkbox" value="1">
                                    <span class="form-check-label fw-semibold text-end">Alındı</span>
                                </label>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md fv-row">
                            <div class="d-flex flex-column mb-3">
                                <label class="fs-6 fw-bold mb-2">Açıklama</label>
                                <textarea class="form-control form-control-solid" rows="2" name="xls_dm_aciklama" id="xls_dm_aciklama" placeholder="Açıklamınızı bu alana yazınız.."></textarea>
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--end::Input group-->
                    <div class="row g-4 mb-4">
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-bold mb-2">Sorumlu Memur</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text"><i class="bi bi-person-circle fs-3"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input class="form-control form-control-solid" disabled id="xls_dm_memur" name="xls_dm_memur" value="<?php echo FormSelectSorumluMemurName();?>" />
                                </div>
                            </div>
                        </div>
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
								<!--begin::Label-->
								<label class="d-flex align-items-center fs-6 fw-bold mb-2">
									<span class="">İlgili Memur(lar)</span>
									<!-- <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Evrağın Kaynağını Belirtir. Kurum, Birim, Kişi vb."></i> -->
								</label>
								<input class="form-control form-control-solid" value="" name="xls_dm_ilgilimemur" id="xls_dm_ilgilimemur" />
                        </div>
                        
                        <!--end::Col-->
                        <!--begin::Col-->
                        
                        <!--end::Col-->
                        
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md fv-row">
                            <label class="fs-6 fw-bold mb-2">Dijital Etiketler</label>
                            <input class="form-control form-control-lg form-control-solid" id="dm_excel_etiket" name="dm_etiket" />
                            <div class="pt-3">
                                <span class="text-gray-600">Öneri:</span>
                                <span class="text-info" id="kt_tagify_excel_etiket_custom_suggestions">
                                    <span class="cursor-pointer" data-kt-suggestion="true">Önemli</span>,
                                    <span class="cursor-pointer" data-kt-suggestion="true">Acil</span>,
                                    <span class="cursor-pointer" data-kt-suggestion="true">Eksik</span>,
                                    <span class="cursor-pointer" data-kt-suggestion="true">Hatırla</span>,
                                    <span class="cursor-pointer" data-kt-suggestion="true">Taslak</span>,
                                    <span class="cursor-pointer" data-kt-suggestion="true">Silinecek</span>
                                </span>
                            </div>
                        </div>
                        
                            <!--end::Input-->
                    </div>
                    <!--begin::Actions-->
                    <div class="text-center">
                        <button type="reset" id="kt_modal_import_durusmalar_cancel" class="btn btn-danger btn-hover-rotate-end me-3">
                            <i class="bi bi-x-circle fs-4 me-2"></i>Vazgeç
                        </button>
                        <button type="submit" id="kt_modal_import_durusmalar_submit" class="btn btn-primary btn-hover-scale">
                            <i class="fas fa-envelope-open-text fs-4 me-2"></i>
                            <span class="indicator-label">Kaydet</span>
                            <span class="indicator-progress">Lütfen Bekle...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end:Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - New Target-->