<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <!--begin::Breadcrumb-->
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 mb-1">
                <!--begin::Item-->
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
                    <a href="<?php echo base_url('dashboard'); ?>" class="text-gray-700 text-hover-primary me-1">
                        <i class="fonticon-home text-gray-700 fs-3"></i>
                    </a>
                </li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr071.svg-->
                    <span class="svg-icon svg-icon-4 svg-icon-gray-700 mx-n1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6343 12.5657L8.45001 16.75C8.0358 17.1642 8.0358 17.8358 8.45001 18.25C8.86423 18.6642 9.5358 18.6642 9.95001 18.25L15.4929 12.7071C15.8834 12.3166 15.8834 11.6834 15.4929 11.2929L9.95001 5.75C9.5358 5.33579 8.86423 5.33579 8.45001 5.75C8.0358 6.16421 8.0358 6.83579 8.45001 7.25L12.6343 11.4343C12.9467 11.7467 12.9467 12.2533 12.6343 12.5657Z" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">Dashboard</li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr071.svg-->
                    <span class="svg-icon svg-icon-4 svg-icon-gray-700 mx-n1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6343 12.5657L8.45001 16.75C8.0358 17.1642 8.0358 17.8358 8.45001 18.25C8.86423 18.6642 9.5358 18.6642 9.95001 18.25L15.4929 12.7071C15.8834 12.3166 15.8834 11.6834 15.4929 11.2929L9.95001 5.75C9.5358 5.33579 8.86423 5.33579 8.45001 5.75C8.0358 6.16421 8.0358 6.83579 8.45001 7.25L12.6343 11.4343C12.9467 11.7467 12.9467 12.2533 12.6343 12.5657Z" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item text-gray-500">Dava Dosyaları</li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->

            <!--begin::Title-->
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Dava Dosyaları</h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <!--begin::Secondary button-->
            <a href="#" class="btn btn-sm fw-bold bg-body btn-color-gray-700 btn-active-color-primary d-none" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app">Button 1</a>
            <!--end::Secondary button-->
            <!--begin::Primary button-->
            <a href="#" class="btn btn-sm fw-bold btn-primary d-none" data-bs-toggle="modal" data-bs-target="#kt_modal_new_cezakayitkdi">Button 2</a>
            <!--end::Primary button-->
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Toolbar container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-fluid">
        <!--begin::Row-->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!--begin::Col-->
            <div class="col-md-12 col-lg-12 col-xl-12 mb-md-5 mb-xl-10">
                
            

    <!--begin::Row-->
    <div class="row g-5 g-xl-10">
        <!--begin::Card-->
        <div class="card px-2">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-1 px-3">
            <!--begin::Advenced Search Form -->
            <!--begin::Card title-->
            <div class="card-title">

            <!--begin::Search-->
            <form method="POST" action="#" id="kt_modal_list_dosya_filter_form">
                <!--begin::Card-->
                <div class="card mb-7">
                <!--begin::Card body-->
                <div class="card-body">
                    <!--begin::Compact form-->
                    <div class="d-flex align-items-center">
                    <!--begin::Input group-->
                    <div class="position-relative w-md-400px me-md-2">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                        </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" class="form-control form-control-solid ps-10" name="dlara_text" value="" placeholder="Genel Arama" />
                    </div>
                    <!--end::Input group-->
                    <!--begin:Action-->
                    <div class="d-flex align-items-center">
                        <button id="kt_modal_dosya_list_ara_submit" type="submit" class="btn btn-primary me-5" data-kt-dosyalist-table-filter="filter">Ara</button>
                        <a id="kt_horizontal_search_advanced_link" class="btn btn-link" data-bs-toggle="collapse" href="#kt_dosya_advanced_search_form">Detaylı Arama</a>
                    </div>
                    <!--end:Action
                
                veriyi çeken yer neresiydi
                
                -->
                    </div>
                    <!--end::Compact form-->
                    <!--begin::Advance form-->
                    <div class="collapse" id="kt_dosya_advanced_search_form">
                        <!--begin::Separator-->
                        <div class="separator separator-dashed mt-9 mb-6"></div>
                        <!--end::Separator-->
                        <!--begin::Row-->
                        <div class="row g-8 mb-1">
                            <!--begin::Col-->
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Dosya No" name="dlara_dosyano" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Mahkeme" name="dlara_mahkeme" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Esas No" name="dlara_esasno" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Davacı" name="dlara_davaci" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Davalı" name="dlara_davali" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Dava Konusu" name="dlara_davakonusu" />
                            </div>
                            
                        </div>
                        <div class="row g-8 mb-1">
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Dava Açıklama" name="dlara_konuaciklama" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Mevki" name="dlara_mevki" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Proje Bilgisi" name="dlara_icrano" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="İcra" name="dlara_icra" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="İstinaf Kabul" name="dlara_istinafkabul" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="İstinaf Red" name="dlara_istinafred" />
                            </div>
                            
                        </div>
                        <div class="row g-8 mb-2">
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Onama İlamı" name="dlara_onamailami" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Bozma İlamı" name="dlara_bozmailami" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="İstinaf Başvuru" name="dlara_istinaf" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Dosya Açıklama" name="dlara_temyiz" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Miras Bel. Tapu Kay.." name="dlara_mirascilik" />
                            </div>
                            <div class="col-md-2">
                            <input type="text" class="form-control form-control form-control-solid" placeholder="Arşiv Klasör No" name="dlara_arsivno" />
                            </div>
                            <!-- <div class="col-md-2">
                            <label class="fs-6 form-label fw-bolder text-dark">Tapu Kaydı</label>
                            <div class="nav-group nav-group-fluid">
                                <label>
                                <input type="radio" class="btn-check" name="dlara_tapu" id="dlara_tapu" value="2" checked="checked" />
                                <span class="btn btn-sm btn-color-muted btn-active btn-active-primary fw-bolder px-4">Hepsi</span>
                                </label>
                                <label>
                                <input type="radio" class="btn-check" name="dlara_tapu" id="dlara_tapu" value="1" />
                                <span class="btn btn-sm btn-color-muted btn-active btn-active-primary fw-bolder px-4">Var</span>
                                </label>
                                <label>
                                <input type="radio" class="btn-check" name="dlara_tapu" id="dlara_tapu" value="0" />
                                <span class="btn btn-sm btn-color-muted btn-active btn-active-primary fw-bolder px-4">Yok</span>
                                </label>
                                <label>
                                <input type="radio" class="btn-check" name="dlara_tapu" id="dlara_tapu" value="-1" />
                                <span class="btn btn-sm btn-color-muted btn-active btn-active-primary fw-bolder px-4">Boş</span>
                                </label>
                            </div>
                            </div> -->
                            <!--end::Col-->
                            <!--begin::Col-->
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Advance form-->
                </div>
                <!--end::Card body-->
                </div>
                <!--end::Card-->

            </form>
            <!--end::Search-->



            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
            <!--begin::Toolbar-->

            <!--begin::Tarih-->
            <div class="d-flex align-items-center position-relative justify-content my-2 mt-3 mb-3 min-w-350px">
                <form method="POST" action="">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">Çalışma Aralığı:</span>
                    <input class="form-control input-group py-2" placeholder="Çalışma Aralığı Seçin" id="kt_table_dosya_datein" aria-describedby="basic-addon1" />
                </div>
                </form>
            </div>
            <!--end::Tarih-->

            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                <button type="button" id="dosya_content_list_excel_export" class="btn btn-sm btn-primary hover-scale" >
                <i class="bi bi-filetype-xlsx fs-3"></i>
                    <span class="indicator-label">Excele Aktar</span>
                    <span class="indicator-progress">Lütfen Bekle...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
            <!--end::Toolbar-->
            <!--begin::Group actions-->
            <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                <div class="fw-bolder me-5">
                <span class="me-2" data-kt-user-table-select="selected_count"></span>Seçilen
                </div>
                <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Seçilenleri Sil</button>
            </div>
            <!--end::Group actions-->
            <!--begin::Modal - Adjust Balance-->
            <div class="modal fade" id="kt_modal_export_users" tabindex="-1" aria-hidden="true">
                <!--begin::Modal dialog-->
                <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bolder">Export Users</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
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
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <!--begin::Form-->
                    <form id="kt_modal_export_users_form" class="form" action="#">
                        <!--begin::Input group-->
                        <div class="fv-row mb-10">
                        <!--begin::Label-->
                        <label class="fs-6 fw-bold form-label mb-2">Select Roles:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select name="group" data-control="select2" data-placeholder="Select a role" data-hide-search="true" class="form-select form-select-solid fw-bolder">
                            <option></option>
                            <option value="Administrator">Administrator</option>
                            <option value="Analyst">Analyst</option>
                            <option value="Developer">Developer</option>
                            <option value="Support">Support</option>
                            <option value="Trial">Trial</option>
                        </select>
                        <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-10">
                        <!--begin::Label-->
                        <label class="required fs-6 fw-bold form-label mb-2">Select Export Format:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select name="format" data-control="select2" data-placeholder="Select a format" data-hide-search="true" class="form-select form-select-solid fw-bolder">
                            <option></option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                            <option value="cvs">CVS</option>
                            <option value="zip">ZIP</option>
                        </select>
                        <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Actions-->
                        <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Vazgeç</button>
                        <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
                            <span class="indicator-label">Onayla</span>
                            <span class="indicator-progress">Lütfen Bekleyin...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        </div>
                        <!--end::Actions-->
                    </form>
                    <!--end::Form-->
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
                </div>
                <!--end::Modal dialog-->
            </div>
            <!--end::Modal - New Card-->
            <!--begin::Modal - Add task-->
            <div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
                <!--begin::Modal dialog-->
                <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bolder">Kullanıcı Ekle</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
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
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <!--begin::Form-->
                    <form id="kt_modal_add_user_form" class="form" action="#">
                        <!--begin::Scroll-->
                        <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="d-block fw-bold fs-6 mb-5">Avatar</label>
                            <!--end::Label-->
                            <!--begin::Image input-->
                            <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('assets/media/svg/avatars/blank.svg')">
                            <!--begin::Preview existing avatar-->
                            <div class="image-input-wrapper w-125px h-125px" style="background-image: url(assets/media/avatars/300-6.jpg);"></div>
                            <!--end::Preview existing avatar-->
                            <!--begin::Label-->
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <!--begin::Inputs-->
                                <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                <input type="hidden" name="avatar_remove" />
                                <!--end::Inputs-->
                            </label>
                            <!--end::Label-->
                            <!--begin::Cancel-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Cancel-->
                            <!--begin::Remove-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Remove-->
                            </div>
                            <!--end::Image input-->
                            <!--begin::Hint-->
                            <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-bold fs-6 mb-2">Full Name</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="user_name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Full name" value="Emma Smith" />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-bold fs-6 mb-2">Email</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="email" name="user_email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="example@domain.com" value="smith@kpmg.com" />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="mb-7">
                            <!--begin::Label-->
                            <label class="required fw-bold fs-6 mb-5">Role</label>
                            <!--end::Label-->
                            <!--begin::Roles-->
                            <!--begin::Input row-->
                            <div class="d-flex fv-row">
                            <!--begin::Radio-->
                            <div class="form-check form-check-custom form-check-solid">
                                <!--begin::Input-->
                                <input class="form-check-input me-3" name="user_role" type="radio" value="0" id="kt_modal_update_role_option_0" checked='checked' />
                                <!--end::Input-->
                                <!--begin::Label-->
                                <label class="form-check-label" for="kt_modal_update_role_option_0">
                                <div class="fw-bolder text-gray-800">Administrator</div>
                                <div class="text-gray-600">Best for business owners and company administrators</div>
                                </label>
                                <!--end::Label-->
                            </div>
                            <!--end::Radio-->
                            </div>
                            <!--end::Input row-->
                            <div class='separator separator-dashed my-5'></div>
                            <!--begin::Input row-->
                            <div class="d-flex fv-row">
                            <!--begin::Radio-->
                            <div class="form-check form-check-custom form-check-solid">
                                <!--begin::Input-->
                                <input class="form-check-input me-3" name="user_role" type="radio" value="1" id="kt_modal_update_role_option_1" />
                                <!--end::Input-->
                                <!--begin::Label-->
                                <label class="form-check-label" for="kt_modal_update_role_option_1">
                                <div class="fw-bolder text-gray-800">Developer</div>
                                <div class="text-gray-600">Best for developers or people primarily using the API</div>
                                </label>
                                <!--end::Label-->
                            </div>
                            <!--end::Radio-->
                            </div>
                            <!--end::Input row-->
                            <div class='separator separator-dashed my-5'></div>
                            <!--begin::Input row-->
                            <div class="d-flex fv-row">
                            <!--begin::Radio-->
                            <div class="form-check form-check-custom form-check-solid">
                                <!--begin::Input-->
                                <input class="form-check-input me-3" name="user_role" type="radio" value="2" id="kt_modal_update_role_option_2" />
                                <!--end::Input-->
                                <!--begin::Label-->
                                <label class="form-check-label" for="kt_modal_update_role_option_2">
                                <div class="fw-bolder text-gray-800">Analyst</div>
                                <div class="text-gray-600">Best for people who need full access to analytics data, but don't need to update business settings</div>
                                </label>
                                <!--end::Label-->
                            </div>
                            <!--end::Radio-->
                            </div>
                            <!--end::Input row-->
                            <div class='separator separator-dashed my-5'></div>
                            <!--begin::Input row-->
                            <div class="d-flex fv-row">
                            <!--begin::Radio-->
                            <div class="form-check form-check-custom form-check-solid">
                                <!--begin::Input-->
                                <input class="form-check-input me-3" name="user_role" type="radio" value="3" id="kt_modal_update_role_option_3" />
                                <!--end::Input-->
                                <!--begin::Label-->
                                <label class="form-check-label" for="kt_modal_update_role_option_3">
                                <div class="fw-bolder text-gray-800">Support</div>
                                <div class="text-gray-600">Best for employees who regularly refund payments and respond to disputes</div>
                                </label>
                                <!--end::Label-->
                            </div>
                            <!--end::Radio-->
                            </div>
                            <!--end::Input row-->
                            <div class='separator separator-dashed my-5'></div>
                            <!--begin::Input row-->
                            <div class="d-flex fv-row">
                            <!--begin::Radio-->
                            <div class="form-check form-check-custom form-check-solid">
                                <!--begin::Input-->
                                <input class="form-check-input me-3" name="user_role" type="radio" value="4" id="kt_modal_update_role_option_4" />
                                <!--end::Input-->
                                <!--begin::Label-->
                                <label class="form-check-label" for="kt_modal_update_role_option_4">
                                <div class="fw-bolder text-gray-800">Trial</div>
                                <div class="text-gray-600">Best for people who need to preview content data, but don't need to make any updates</div>
                                </label>
                                <!--end::Label-->
                            </div>
                            <!--end::Radio-->
                            </div>
                            <!--end::Input row-->
                            <!--end::Roles-->
                        </div>
                        <!--end::Input group-->
                        </div>
                        <!--end::Scroll-->
                        <!--begin::Actions-->
                        <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
                        <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        </div>
                        <!--end::Actions-->
                    </form>
                    <!--end::Form-->
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
                </div>
                <!--end::Modal dialog-->
            </div>
            <!--end::Modal - Add task-->
            </div>
            <!--end::Card toolbar-->
            <!--end::Advenced Search Form -->
        </div>
        <!--end::Card header-->


        <!--begin::Card body-->
        <div class="card-body py-1 px-1" id="dosya_content_list">

            <!--begin::Datatable-->
            <table id="kt_content_dosya_list" class="table align-middle table-row-dashed table-striped min-h-400px fs-5 gy-5">
            <thead>
            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                <!--th class="text-left min-w-150px" data-priority="1">İşlemler</th-->
                <th></th>
                <th>Açılış T.</th>
                <th data-priority="1">K.Dosya No</th>
                <th class="min-w-100px" data-priority="1">Davacı</th>
                <th class="min-w-100px" data-priority="1">Davalı</th>
                <!-- <th class="min-w-100px" data-priority="1">Dava Konusu</th> -->
                <th class="min-w-200px">Konu Açıklaması</th>
                <th class="min-w-100px" data-priority="1">Mahkeme</th>
                <th data-priority="1">Esas No</th>
                <th data-priority="1">Karar No</th>
                <th data-priority="1">Mevki Plaka</th>
                <th class="min-w-100px" data-priority="1">Etiket</th>
            </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold"></tbody>
            </table>
            <!--end::Datatable-->


        </div>
        <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Row-->



            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Content container-->
</div>
<!--end::Content-->