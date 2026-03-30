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
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">Duyurular</li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->

            <!--begin::Title-->
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Duyurular</h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <?php if (isDbAllowedWriteModule()) { ?>
                <!--begin::Primary button-->
                <a href="<?php echo base_url('duyurular/new_form'); ?>" class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                            <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor" />
                            <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor" />
                        </svg>
                    </span>
                    Yeni Duyuru Ekle
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
                    <div class="card-px-0 pt-0">
                        <!--begin:TableDiv-->
                        <?php if (empty($statuList)) { ?>


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
                                    <span>Görüntüleyebileceğiniz herhangi bir kullanıcı statüsü kaydı bulunamadı.</span>
                                </div>
                            </div>



                        <?php } else { ?>
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-hover table-sriped align-middle table-row-bordered fs-6 gy-2" id="kt_userstatus_table">
                                    <!--begin::Table head-->
                                    <thead>
                                        <!--begin::Table row-->
                                        <tr class="border-top text-start text-dark-400 fw-bolder fs-8 text-uppercase gs-0">
                                            
                                            <th class="min-w-20px">#</th>
                                            <th class="min-w-100px">Duyuru Başlığı</th>
                                            <th class="min-w-70px">Duyuru Tarihi</th>
                                            <th class="min-w-70px">Duyuru Kodu</th>
                                            <th class="min-w-200px">Duyuru Detay</th>
                                            
                                            <th class="min-w-300px text-end">İşlemler</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fw-bold text-gray-600 border-bottom border-gray-200" data-url="<?php echo base_url("duyurular/rankSetter"); ?>">

                                        <?php 
                                        $ix=0;
                                        foreach ($statuList as $statu) { 
                                            $ix++;
                                            ?>
                                            <tr>
                                                
                              
                                                <!--end::Symbol=-->
                                                <!--begin::Name=-->
                                                <td>
                                                    <?=$ix;?>
                                                </td>
                                                <td>
                                                    <span class="card-label fs-8 text-dark"><?php echo $statu->us_name; ?></span>
                                                </td>
                                                <td>
                                                    <span class="card-label fs-8 text-dark"><?php echo $statu->us_adddate; ?></span>
                                                </td>
                                                <td>
                                                    <span class="card-label fs-8 text-dark"><?php echo $statu->us_code; ?></span>
                                                </td>                                                                                                
                                                <!--end::Name=-->
                                                <!--begin::Description=-->
                                                <td>
                                                    <span class="text-dark mt-1 fs-9"><?php echo $statu->us_description; ?></span>
                                                </td>
                                                <!--end::Description=-->
                                                <!--begin::Status=-->
                                               
                                                <!--end::Status=-->
                                                <!--begin::Action=-->
                                                <td class="text-end">
                                                    <!--begin::Menu-->

                                                        <button data-url="<?php echo base_url("duyurular/remove/$statu->us_id"); ?>" class="btn btn-sm btn-light-danger text-gray-900 mt-0 mt-md-0 remove-btn">
                                                            <i class="la la-trash-o fs-3"></i> Sil
                                                        </button>

                                                    
                                                        <a href="<?php echo base_url("duyurular/update_form/$statu->us_id"); ?>" class="btn btn-sm btn-light-primary text-gray-900 mt-0 mt-md-0"><i class="la la-user-edit fs-3"></i> Düzenle</a>
                                                    
                                                    
                                                        
                                                    
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