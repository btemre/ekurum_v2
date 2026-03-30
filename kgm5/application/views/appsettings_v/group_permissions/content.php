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
                    <a href="<?php echo base_url("appsettings"); ?>" class="text-gray-700 text-hover-primary me-1">
                        Uygulamalar
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
                    <a href="<?php echo base_url("appsettings/grouplist/{$app->a_appcode}"); ?>" class="text-gray-700 text-hover-primary me-1">
                        Kullanıcı Grupları
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
                <li class="breadcrumb-item text-gray-500">Yetkilendirme Yönetimi</li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->

            <!--begin::Title-->
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                <?php echo $app->a_shortcode . ' - ' . $group->ug_name; ?> - Modul Yetkilendirmeleri
            </h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <!--begin::Primary button-->
            <a href="<?php echo base_url("appsettings/group_permissions/{$app->a_appcode}/{$group->ug_id}"); ?>" class="btn btn-sm btn-primary">
                <!--begin::Svg Icon | path: assets/media/icons/duotune/arrows/arr029.svg-->
                <span class="svg-icon svg-icon-muted svg-icon-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M14.5 20.7259C14.6 21.2259 14.2 21.826 13.7 21.926C13.2 22.026 12.6 22.0259 12.1 22.0259C9.5 22.0259 6.9 21.0259 5 19.1259C1.4 15.5259 1.09998 9.72592 4.29998 5.82592L5.70001 7.22595C3.30001 10.3259 3.59999 14.8259 6.39999 17.7259C8.19999 19.5259 10.8 20.426 13.4 19.926C13.9 19.826 14.4 20.2259 14.5 20.7259ZM18.4 16.8259L19.8 18.2259C22.9 14.3259 22.7 8.52593 19 4.92593C16.7 2.62593 13.5 1.62594 10.3 2.12594C9.79998 2.22594 9.4 2.72595 9.5 3.22595C9.6 3.72595 10.1 4.12594 10.6 4.02594C13.1 3.62594 15.7 4.42595 17.6 6.22595C20.5 9.22595 20.7 13.7259 18.4 16.8259Z" fill="currentColor" />
                        <path opacity="0.3" d="M2 3.62592H7C7.6 3.62592 8 4.02592 8 4.62592V9.62589L2 3.62592ZM16 14.4259V19.4259C16 20.0259 16.4 20.4259 17 20.4259H22L16 14.4259Z" fill="currentColor" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
                Yenile
            </a>
            <!--end::Primary button-->
            <!--begin::Primary button-->
            <a href="<?php echo base_url("appsettings/grouplist/{$app->a_appcode}"); ?>" class="btn btn-sm btn-danger">
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
                                <span class="card-label fw-bolder text-dark"><?php echo $app->a_shortcode . ' - ' . $group->ug_name; ?> - Yetkilendirmeler</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Uygulamaya Ait Modüllerin Kullanıcı Gruplarıyla İlgili Yetkilendirme Güncellemeleri Yapabilirsiniz</span>
                            </h3>
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-usergroup-table-toolbar="base">

                            </div>
                            <!--end::Toolbar-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body px-0 pt-0">
                        <!--begin:TableDiv-->

                        <?php if ($controllerList == false) { ?>
                            <!--begin::Alert-->
                            <div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                                <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor" />
                                        <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-primary">Kayıt Bulunamadı</h4>
                                    <span>Yetkilendirmesini yapabileceğiniz herhangi bir dosya modülü bulunamadı.</span>
                                </div>
                            </div>



                        <?php } else { ?>
                            <div class="table-responsive">
                                <form action="<?php echo base_url("appsettings/update_group_permissions/{$app->a_appcode}/{$group->ug_id}"); ?>" method="POST">
                                    <!--begin::Table-->
                                    <table class="table table-hover table-sriped align-middle table-row-bordered fs-7 gy-5" id="kt_usergroup_permissions_table">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="border-top text-start text-dark-400 fw-bolder fs-9 text-uppercase gs-0">
                                                <th class="min-w-50px">SN</th>
                                                <th class="min-w-100px">Modül Adı</th>
                                                <th class="min-w-200px">Açıklamalar</th>
                                                <th class="min-w-75px">Admin</th>
                                                <th class="min-w-75px">Listeleme</th>
                                                <th class="min-w-75px">Ekleme</th>
                                                <th class="min-w-75px">Düzenleme</th>
                                                <th class="min-w-75px">Silme</th>
                                                <th class="min-w-100px">Görüntüleme</th>
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="fw-bold text-gray-600 border-bottom border-gray-200">
                                            <?php
                                            $i = 0;
                                            foreach ($controllerList as $controller) {
                                                $i++;
                                                $_code = $controller->gp_controller;
                                                $controllerB = json_decode($controller->gp_json);

                                            ?>
                                                <tr>
                                                    <!--begin::SN=-->
                                                    <td><?php echo $i; ?><input type="hidden" name="permissions[yetki][off]" value="off" /></td>
                                                    <!--end::SN=-->
                                                    <!--begin::Name=-->
                                                    <td>
                                                        <span class="card-label fw-bolder text-dark"><?php echo $controllerB->name; ?></span>
                                                    </td>
                                                    <!--end::Name=-->
                                                    <!--begin::Description=-->
                                                    <td>
                                                        <span class="text-dark mt-1 fs-9"><?php echo $controllerB->description; ?></span>
                                                    </td>
                                                    <!--end::Description=-->
                                                    <!--begin::Admin=-->
                                                    <td>
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input name="permissions[<?php echo $controllerB->code; ?>][adminr]" class="form-check-input h-25px w-35px" type="checkbox" <?php echo ($controller->gp_adminr) ? "checked" : ""; ?> />
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input name="permissions[<?php echo $controllerB->code; ?>][list]" class="form-check-input h-25px w-35px" type="checkbox" <?php echo ($controller->gp_list) ? "checked" : ""; ?> />
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input name="permissions[<?php echo $controllerB->code; ?>][write]" class="form-check-input h-25px w-35px" type="checkbox" <?php echo ($controller->gp_write) ? "checked" : ""; ?> />
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input name="permissions[<?php echo $controllerB->code; ?>][update]" class="form-check-input h-25px w-35px" type="checkbox" <?php echo ($controller->gp_update) ? "checked" : ""; ?> />
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input name="permissions[<?php echo $controllerB->code; ?>][delete]" class="form-check-input h-25px w-35px" type="checkbox" <?php echo ($controller->gp_delete) ? "checked" : ""; ?> />
                                                        </label>
                                                    </td>
                                                    <td class="text-end">
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <input name="permissions[<?php echo $controllerB->code; ?>][read]" class="form-check-input h-25px w-35px" type="checkbox" <?php echo ($controller->gp_read) ? "checked" : ""; ?> />
                                                        </label>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->


                                    <div class="separator border-secondary my-10"></div>

                                    <div class="mb-1">
                                        <!--begin::Primary button-->
                                        <a href="<?php echo base_url("appsettings/grouplist/{$app->a_appcode}"); ?>" class="btn btn-sm btn-danger">
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
                                </form>
                            </div>
                            <!--end:TableDiv-->
                        <?php } ?>
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