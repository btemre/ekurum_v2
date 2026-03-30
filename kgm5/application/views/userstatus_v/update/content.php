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
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
                    <a href="<?php echo base_url("userstatus"); ?>" class="text-gray-700 text-hover-primary me-1">
                        Statüler
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
                <li class="breadcrumb-item text-gray-500">Düzenleme</li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->

            <!--begin::Title-->
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Statü Düzenleme</h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <!--begin::Primary button-->
            <a href="<?php echo base_url('userstatus'); ?>" class="btn btn-sm btn-danger">
                <!--begin::Svg Icon | path: assets/media/icons/duotune/arrows/arr077.svg-->
                <span class="svg-icon svg-icon-muted svg-icon-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.3" x="4" y="11" width="12" height="2" rx="1" fill="currentColor" />
                        <path d="M5.86875 11.6927L7.62435 10.2297C8.09457 9.83785 8.12683 9.12683 7.69401 8.69401C7.3043 8.3043 6.67836 8.28591 6.26643 8.65206L3.34084 11.2526C2.89332 11.6504 2.89332 12.3496 3.34084 12.7474L6.26643 15.3479C6.67836 15.7141 7.3043 15.6957 7.69401 15.306C8.12683 14.8732 8.09458 14.1621 7.62435 13.7703L5.86875 12.3073C5.67684 12.1474 5.67684 11.8526 5.86875 11.6927Z" fill="currentColor" />
                        <path d="M8 5V6C8 6.55228 8.44772 7 9 7C9.55228 7 10 6.55228 10 6C10 5.44772 10.4477 5 11 5H18C18.5523 5 19 5.44772 19 6V18C19 18.5523 18.5523 19 18 19H11C10.4477 19 10 18.5523 10 18C10 17.4477 9.55228 17 9 17C8.44772 17 8 17.4477 8 18V19C8 20.1046 8.89543 21 10 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H10C8.89543 3 8 3.89543 8 5Z" fill="#C4C4C4" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
                Vazgeç
            </a>
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

                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder text-dark"><?php echo $item->us_name; ?> - Düzenleme</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Formu Doldurup Kullanıcı Statüsünü Güncelleyebilirsiniz.</span>
                            </h3>
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end">

                            </div>
                            <!--end::Toolbar-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body px-2 pt-0">
                        <form action="<?php echo base_url("userstatus/update/{$item->us_id}"); ?>" method="POST" name="new_userstatus">
                            <div class="py-5">
                                <div class="rounded border p-0">
                                    <div class="mb-10 p-1">
                                        <label class="form-label">Statü Adı:</label>
                                        <input type="text" class="form-control" placeholder="Statü adını yazınız" name="name" value="<?php echo isset($form_error) ? set_value("name") : $item->us_name; ?>" />
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="name"><?php echo @form_error("name"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="mb-10 p-1">
                                        <label class="form-label">Statü Kodu:</label>
                                        <input type="text" class="form-control" placeholder="Örn:RT" name="code" maxlength="2" value="<?php echo isset($form_error) ? set_value("code") : $item->us_code; ?>" />
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="code"><?php echo @form_error("code"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="mb-10 p-1 col-md-2">


                                        <label class="form-label" for="kt_docs_select2_color">Grup Rengi:</label>
                                        <select class="form-select text-align-center" aria-label="Grup Rengi Seçiniz" data-hide-search="true" name="color" id="kt_docs_select2_color">
                                            <option value="success" data-kt-select2-color="success" <?php if ($colorVal == "success") {
                                                                                                        echo 'selected';
                                                                                                    } ?>></option>
                                            <option value="secondary" data-kt-select2-color="secondary" <?php if ($colorVal == "secondary") {
                                                                                                            echo 'selected';
                                                                                                        } ?>></option>
                                            <option value="primary" data-kt-select2-color="primary" <?php if ($colorVal == "primary") {
                                                                                                        echo 'selected';
                                                                                                    } ?>></option>
                                            <option value="info" data-kt-select2-color="info" <?php if ($colorVal == "info") {
                                                                                                    echo 'selected';
                                                                                                } ?>></option>
                                            <option value="danger" data-kt-select2-color="danger" <?php if ($colorVal == "danger") {
                                                                                                        echo 'selected';
                                                                                                    } ?>></option>
                                            <option value="warning" data-kt-select2-color="warning" <?php if ($colorVal == "warning") {
                                                                                                        echo 'selected';
                                                                                                    } ?>></option>
                                        </select>


                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="color"><?php echo @form_error("color"); ?></div>
                                            </div>
                                        <?php } ?>


                                    </div>

                                    <div class="mb-10 p-1">
                                        <label class="form-label">Statü Açıklaması:</label>
                                        <textarea class="form-control" name="description" style="height: 100px"><?php echo isset($form_error) ? set_value("description") : $item->us_description; ?></textarea>
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="description"><?php echo @form_error("description"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="separator border-secondary my-2 p-1"></div>

                                    <div class="mb-1 p-1">
                                        <!--begin::Primary button-->
                                        <a href="<?php echo base_url('userstatus'); ?>" class="btn btn-sm btn-danger">
                                            <!--begin::Svg Icon | path: assets/media/icons/duotune/arrows/arr077.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.3" x="4" y="11" width="12" height="2" rx="1" fill="currentColor" />
                                                    <path d="M5.86875 11.6927L7.62435 10.2297C8.09457 9.83785 8.12683 9.12683 7.69401 8.69401C7.3043 8.3043 6.67836 8.28591 6.26643 8.65206L3.34084 11.2526C2.89332 11.6504 2.89332 12.3496 3.34084 12.7474L6.26643 15.3479C6.67836 15.7141 7.3043 15.6957 7.69401 15.306C8.12683 14.8732 8.09458 14.1621 7.62435 13.7703L5.86875 12.3073C5.67684 12.1474 5.67684 11.8526 5.86875 11.6927Z" fill="currentColor" />
                                                    <path d="M8 5V6C8 6.55228 8.44772 7 9 7C9.55228 7 10 6.55228 10 6C10 5.44772 10.4477 5 11 5H18C18.5523 5 19 5.44772 19 6V18C19 18.5523 18.5523 19 18 19H11C10.4477 19 10 18.5523 10 18C10 17.4477 9.55228 17 9 17C8.44772 17 8 17.4477 8 18V19C8 20.1046 8.89543 21 10 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H10C8.89543 3 8 3.89543 8 5Z" fill="#C4C4C4" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            Vazgeç
                                        </a>
                                        <!--end::Primary button-->
                                        <!--begin::Primary button-->
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <!--begin::Svg Icon | path: assets/media/icons/duotune/arrows/arr016.svg-->
                                            <span class="svg-icon svg-icon-muted svg-icon-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M10.3 14.3L11 13.6L7.70002 10.3C7.30002 9.9 6.7 9.9 6.3 10.3C5.9 10.7 5.9 11.3 6.3 11.7L10.3 15.7C9.9 15.3 9.9 14.7 10.3 14.3Z" fill="currentColor" />
                                                    <path d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM11.7 15.7L17.7 9.70001C18.1 9.30001 18.1 8.69999 17.7 8.29999C17.3 7.89999 16.7 7.89999 16.3 8.29999L11 13.6L7.70001 10.3C7.30001 9.89999 6.69999 9.89999 6.29999 10.3C5.89999 10.7 5.89999 11.3 6.29999 11.7L10.3 15.7C10.5 15.9 10.8 16 11 16C11.2 16 11.5 15.9 11.7 15.7Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            Güncelle
                                        </button>
                                        <!--end::Primary button-->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->

            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Content container-->
</div>
<!--end::Content-->