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
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">Kullanıcı Grupları</li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->

            <!--begin::Title-->
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Kullanıcı Grupları</h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <?php if (isDbAllowedWriteModule()) { ?>
                <!--begin::Primary button-->
                <a href="<?php echo base_url('usergroup/new_form'); ?>" class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                            <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor" />
                            <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor" />
                        </svg>
                    </span>
                    Yeni Grup Ekle
                </a>
                <!--end::Primary button-->
            <?php } ?>
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
                    <div class="card-header border-0 pt-1">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder text-dark">Kullanıcı Grupları</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Kullanıcı Gruplarıyla İlgili Güncellemeleri Yapabilirsiniz</span>
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
                    <div class="card-body px-1 pt-0">
                        <!--begin:TableDiv-->



                        <?php if (empty($groupList)) { ?>


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
                                    <span>Görüntüleyebileceğiniz herhangi bir kullanıcı grubu kaydı bulunamadı.</span>
                                </div>
                            </div>



                        <?php } else { ?>
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-hover table-sriped align-middle table-row-bordered fs-6 gy-2" id="kt_usergroup_table">
                                    <!--begin::Table head-->
                                    <thead>
                                        <!--begin::Table row-->
                                        <tr class="border-top text-start text-dark-400 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="align-middle">
                                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                                <span class="svg-icon svg-icon-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="currentColor"></path>
                                                        <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="currentColor"></path>
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </th>
                                            <th class="min-w-70px">Sembol</th>
                                            <th class="min-w-100px">Grup Adı</th>
                                            <th class="min-w-200px">Açıklamalar</th>
                                            <th class="min-w-70px">Durum</th>
                                            <th class="min-w-300px">İşlemler</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fw-bold text-gray-600 border-bottom border-gray-200 sortable" data-url="<?php echo base_url("usergroup/rankSetter"); ?>">
                                        <?php foreach ($groupList as $group) { ?>
                                            <tr<?php echo ($group->ug_id !== $userData->userB->ug_id) ? ' id="ord-' . $group->ug_id . '"' : ''; ?>>
                                                <td>
                                                    <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                                    <span class="svg-icon svg-icon-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="currentColor"></path>
                                                            <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </td>
                                                <!--begin::Symbol=-->
                                                <td>
                                                    <!--begin::Symbol-->
                                                    <div class="symbol symbol-30px me-4">
                                                        <div class="symbol-label fs-8 fw-bold bg-<?php echo @$group->ug_color; ?> text-inverse-danger"><?php echo @$group->ug_code; ?></div>
                                                    </div>
                                                    <!--end::Symbol-->
                                                </td>
                                                <!--end::Symbol=-->
                                                <!--begin::Name=-->
                                                <td>
                                                    <span class="card-label fs-8 text-dark"><?php echo $group->ug_name; ?></span>
                                                </td>
                                                <!--end::Name=-->
                                                <!--begin::Description=-->
                                                <td>
                                                    <span class="text-dark mt-1 fs-9"><?php echo $group->ug_description; ?></span>
                                                </td>
                                                <!--end::Description=-->
                                                <!--begin::Status=-->
                                                <td>
                                                    <?php if (isDbAllowedUpdateModule() && isDbAdminViewModule()) { ?>
                                                        <?php if ($group->ug_id == $userData->userB->ug_id) { ?>
                                                            <div class="badge badge-light-<?php echo ($group->ug_status == 1) ? 'success' : 'danger'; ?>"><?php echo ($group->ug_status == 1) ? 'Aktif' : 'Pasif'; ?></div>
                                                        <?php } else { ?>
                                                            <label class="form-check form-switch form-check-custom form-check-solid">
                                                                <input class="form-check-input isActive" type="checkbox" data-url="<?php echo base_url("usergroup/isActiveSetter/$group->ug_id"); ?>" <?php echo ($group->ug_status == 1) ? 'checked' : ''; ?> />
                                                            </label>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <div class="badge badge-light-<?php echo ($group->ug_status == 1) ? 'success' : 'danger'; ?>"><?php echo ($group->ug_status == 1) ? 'Aktif' : 'Pasif'; ?></div>
                                                    <?php } ?>
                                                </td>
                                                <!--end::Status=-->
                                                <!--begin::Action=-->
                                                <td class="text-end">
                                                    <!--begin::Menu-->
                                                    <?php if (isDbAllowedDeleteModule()) { ?>
                                                        <button data-url="<?php echo base_url("usergroup/remove/$group->ug_id"); ?>" class="btn btn-sm btn-light-danger mt-0 mt-md-0 text-gray-900 remove-btn">
                                                            <i class="la la-trash-o fs-3"></i> Sil
                                                        </button>
                                                    <?php } ?>
                                                    <?php if (isDbAllowedUpdateModule()) { ?>
                                                        <a href="<?php echo base_url("usergroup/update_form/$group->ug_id"); ?>" class="btn btn-sm btn-light-primary text-gray-900 mt-0 mt-md-0"><i class="la la-user-edit fs-3"></i> Düzenle</a>
                                                    <?php } ?>
                                                    <!--end::Menu-->
                                                    <!--begin::Menu-->
                                                    <?php if (isDbAllowedUpdateModule() && isDbAdminViewModule()) { ?>

                                                        <button class="btn btn-light-warning text-gray-900 btn-active-light-warning btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"><i class="la la-eye fs-3"></i> Yetkiler
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                                            <span class="svg-icon svg-icon-5 m-0">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                        </button>
                                                        <!--begin::Menu-->
                                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-900 menu-state-bg-light-warning fw-bold fs-7 w-200px py-2" data-kt-menu="true">
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-1">
                                                                <a href="<?php echo base_url("usergroup/permissions/$group->ug_id"); ?>" class="menu-link px-3">Modül Yetkileri</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu separator-->
                                                            <div class="separator my-2"></div>
                                                            <!--end::Menu separator-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-1">
                                                                <a href="<?php echo base_url("usergroup/app_permissions/$group->ug_id"); ?>" class="menu-link px-3">Uygulama Yetkileri</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                        </div>
                                                        <!--end::Menu-->
                                                    <?php } ?>
                                                    <!--end::Menu-->
                                                </td>
                                                <!--end::Action=-->
                                                </tr>
                                            <?php } ?>
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
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