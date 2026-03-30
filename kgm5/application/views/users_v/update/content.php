<?php
$cinsiyetList=array(1=>"Erkek","2"=>"Kadın");

?>
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
                    <a href="<?php echo base_url("users"); ?>" class="text-gray-700 text-hover-primary me-1">
                        Kullanıcılar
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
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Kullanıcı Düzenleme</h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <!--begin::Primary button-->
            <a href="<?php echo base_url('users'); ?>" class="btn btn-sm btn-danger">
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
                                <span class="card-label fw-bolder text-dark"><?php echo $item->u_name . ' ' . $item->u_lastname . ' ' . $item->u_surname; ?> - Düzenleme</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Formu Doldurup Kullanıcı Bilgilerini Güncelleyebilirsiniz.</span>
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
                        <form action="<?php echo base_url("users/update/{$item->u_id}"); ?>" novalidate="novalidate" method="POST" name="update_users" id="kt_update_form">
                            <div class="py-5">
                                <div class="rounded border p-0">
                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">Çalıştığı Birim*:</label>
                                        <select class="form-control" data-kt-select2="true" data-placeholder="Lütfen Birim Seçiniz" data-allow-clear="true" name="units" id="units">
                                            <option></option>
                                            <?php if (isset($unitList)) { ?>
                                                <?php foreach ($unitList as $unit) { ?>
                                                    <option value="<?php echo @$unit->ub_id; ?>" <?php if (isset($form_error)) {
                                                                                                        if (set_value("units") == $unit->ub_id) {
                                                                                                            echo " selected";
                                                                                                        }
                                                                                                    } else {
                                                                                                        if ($item->u_unit == $unit->ub_id) {
                                                                                                            echo " selected";
                                                                                                        }
                                                                                                    } ?>><?php echo @$unit->ub_title; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="units"><?php echo @form_error("units"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">Kullanıcı Grubu*:</label>
                                        <select class="form-control" data-kt-select2="true" data-placeholder="Lütfen Grup Seçiniz" data-allow-clear="true" name="group" id="group">
                                            <option></option>
                                            <?php if (isset($groupList)) { ?>
                                                <?php foreach ($groupList as $group) { ?>
                                                    <option value="<?php echo @$group->ug_id; ?>" <?php if (isset($form_error)) {
                                                                                                        if (set_value("group") == $group->ug_id) {
                                                                                                            echo " selected";
                                                                                                        }
                                                                                                    } else {
                                                                                                        if ($item->u_group == $group->ug_id) {
                                                                                                            echo " selected";
                                                                                                        }
                                                                                                    } ?>><?php echo @$group->ug_name; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="group"><?php echo @form_error("group"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">Kullanıcı Statüsü*:</label>
                                        <select class="form-control" data-kt-select2="true" data-placeholder="Lütfen Statü Seçiniz" data-allow-clear="true" name="status" id="status">
                                            <option></option>
                                            <?php if (isset($statuList)) { ?>
                                                <?php foreach ($statuList as $statu) { ?>
                                                    <option value="<?php echo @$statu->us_id; ?>" <?php if (isset($form_error)) {
                                                                                                        if (set_value("status") == $statu->us_id) {
                                                                                                            echo " selected";
                                                                                                        }
                                                                                                    } else {
                                                                                                        if ($item->u_statu == $statu->us_id) {
                                                                                                            echo " selected";
                                                                                                        }
                                                                                                    } ?>><?php echo @$statu->us_name; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="status"><?php echo @form_error("status"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">İstasyon*:</label>
                                        <select class="form-control" data-kt-select2="true" data-placeholder="Lütfen İstasyon Seçiniz" data-allow-clear="true" name="istasyon" id="istasyon">
                                            <option></option>
                                            <?php if (isset($istasyonList)) { ?>
                                                <?php foreach ($istasyonList as $istasyon) { ?>
                                                    <option value="<?php echo @$istasyon->kdi_id; ?>" <?php if (isset($form_error)) {
                                                                                                            if (set_value("istasyon") == $istasyon->kdi_id) {
                                                                                                                echo " selected";
                                                                                                            }
                                                                                                        } else {
                                                                                                            if ($item->u_istasyon == $istasyon->kdi_id) {
                                                                                                                echo " selected";
                                                                                                            }
                                                                                                        } ?>><?php echo @$istasyon->kdi_isim; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="istasyon"><?php echo @form_error("istasyon"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="separator separator-dashed border-primary my-5 mx-5 px-5"></div>

                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">Adı*:</label>
                                        <input type="text" class="form-control" placeholder="Adını yazınız" name="name" value="<?php echo isset($form_error) ? set_value("name") : $item->u_name; ?>" />
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="name"><?php echo @form_error("name"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">İkinci Adı:</label>
                                        <input type="text" class="form-control" placeholder="İkinci adını yazınız" name="lastname" value="<?php echo isset($form_error) ? set_value("lastname") : $item->u_lastname; ?>" />
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="lastname"><?php echo @form_error("lastname"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">Soyadı*:</label>
                                        <input type="text" class="form-control" placeholder="Soyadını yazınız" name="surname" value="<?php echo isset($form_error) ? set_value("surname") : $item->u_surname; ?>" />
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="surname"><?php echo @form_error("surname"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">Cinsiyeti*:</label>

                                        <?php
                                        $itemArr=(array)$item;
                                        
                                        
                                        ?>
                                        <select class="form-control" style="width:200px;"  autocomplete="off"  name="cinsiyet" id="cinsiyet">
                                            <option>Seçiniz</option>
                                            
                                            <?php 
                                            foreach ($cinsiyetList as $kz=>$vz) { 
                                                $slcx=($itemArr["u_cinsiyet"]==$kz)?"selected":"";
                                                echo "<option value='$kz' $slcx>$vz</option>";
                                                ?>
                                                
                                            <?php } ?>
                                        
                                        </select>
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="units"><?php echo @form_error("units"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>                                     


                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">E-mail:</label>
                                        <input type="email" class="form-control" placeholder="Email adresini yazınız" name="email" id="email" value="<?php echo isset($form_error) ? set_value("email") : $item->u_mail; ?>" />
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="email"><?php echo @form_error("email"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>


                                    <div class="separator separator-dashed border-primary my-5 mx-5 px-5"></div>

                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">Kullanıcı Adı*:</label>
                                        <input class="form-control" placeholder="Kullanıcı adınızı yazınız" type="text" name="username" id="username" value="<?php echo isset($form_error) ? set_value("username") : $item->u_username; ?>" />
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="username"><?php echo @form_error("username"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">Parolası:</label>
                                        <input class="form-control" placeholder="Parolasını yazınız" type="password" name="password" id="password" autocomplete="off" />
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="password"><?php echo @form_error("password"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="fv-row mb-10 p-1">
                                        <label class="form-label">Parola Tekrarı:</label>
                                        <input class="form-control" placeholder="Parolasını tekrar yazınız" type="password" name="repassword" id="repassword" autocomplete="off" />
                                        <?php if (isset($form_error)) { ?>
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="repassword"><?php echo @form_error("repassword"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="separator border-secondary my-2 p-1"></div>

                                    <div class="mb-1 p-1">
                                        <!--begin::Primary button-->
                                        <a href="<?php echo base_url('users'); ?>" class="btn btn-sm btn-danger">
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
                                        <button type="submit" class="btn btn-sm btn-success" id="kt_update_form_submit">
                                            <span class="indicator-label">
                                                <span class="svg-icon svg-icon-muted svg-icon-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                                                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor" />
                                                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                Güncelle</span>
                                            <span class="indicator-progress">Lütfen Bekleyin...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
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