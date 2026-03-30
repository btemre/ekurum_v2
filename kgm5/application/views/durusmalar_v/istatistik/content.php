<?php
$islemList=array("Duruşma","İstinaf","Keşif","Karar","Red","Birleşti","Kaldırıldı");

$tarafList=array("DAVALI","DAVACI","DAHİLİ DAVACI","KATILAN","MÜŞTEKİ","İHBAR OLUNAN","KONTROL","KEŞİF","GENEL MÜDÜRLÜK");

$gidildiSlc=($dlara_dtakip==1)?"selected":"";
$mazeretSlc=($dlara_dtakip==2)?"selected":"";
?>
<style>
/* Premium AI butonları */
.btn-ai { font-weight: 700 !important; letter-spacing: 0.02em; padding: 0.6rem 1.1rem !important; border-radius: 0.65rem !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04) !important;
  transition: transform 0.15s ease, box-shadow 0.2s ease !important; }
.btn-ai:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,0.12) !important; }
.btn-ai .bi-stars, .btn-ai .bi-search { font-size: 1.1em; vertical-align: -0.15em; }
.btn-ai.btn-primary { background: linear-gradient(135deg, #009ef7 0%, #0095e8 100%) !important; border-color: rgba(255,255,255,0.25) !important; color: #fff !important; }
.btn-ai.btn-primary:hover { background: linear-gradient(135deg, #0095e8 0%, #0081c6 100%) !important; box-shadow: 0 4px 16px rgba(0,158,247,0.45) !important; }
.btn-ai.btn-light-primary { background: linear-gradient(180deg, #f1faff 0%, #e8f4fc 100%) !important; border: 1px solid #009ef7 !important; color: #009ef7 !important; }
.btn-ai.btn-light-primary:hover { background: linear-gradient(180deg, #e8f4fc 0%, #d4edfc 100%) !important; box-shadow: 0 4px 14px rgba(0,158,247,0.3) !important; }
.btn-ai.btn-light-info { background: linear-gradient(180deg, #f1faff 0%, #e8f6fc 100%) !important; border: 1px solid #00bcd4 !important; color: #00a3b8 !important; }
.btn-ai.btn-light-info:hover { background: linear-gradient(180deg, #e8f6fc 0%, #d4f0f8 100%) !important; box-shadow: 0 4px 14px rgba(0,188,212,0.3) !important; }
</style>
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
                    <a href="<?php echo base_url('durusmalar/istatistik'); ?>"
                        class="text-gray-700 text-hover-primary me-1">
                        İstatistikler
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
                <li class="breadcrumb-item text-gray-500 d-none">Overview</li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->
            <!--begin::Title-->
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">EDTS | İstatistikler</h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <!--begin::AI Butonları-->
            <button type="button" class="btn btn-sm btn-ai btn-light-info" onclick="AiService.openSearchModal()">
                <i class="bi bi-search me-1"></i>AI ile Ara
            </button>
            <button type="button" class="btn btn-sm btn-ai btn-primary" onclick="AiService.requestSummary()">
                <i class="bi bi-stars me-1"></i>AI ile Özetle
            </button>
            <!--end::AI Butonları-->
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
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!--begin::Col istatistik-->
            <div class="col-xxl-12 mb-5 mb-xl-10">

                
                <div class="align-items-center">                                               
                    <a id="kt_horizontal_search_advanced_link" class="btn btn-secondary btn-sm mb-3" data-bs-toggle="collapse" href="#kt_durusma_advanced_search_form">Filtreler <i class="fas fa-caret-down"></i></a>    
                    <a id="kt_horizontal_search_advanced_update"  class="btn btn-primary  btn-sm mb-3 ml-5">Ara</i></a>
                    <a id="kt_horizontal_search_advanced_reset"  class="btn btn-success btn-sm mb-3 ml-5">Reset</i></a>
                </div>

                <div class="collapse show" id="kt_durusma_advanced_search_form">
                    <!--begin::Separator-->
                    <div class="separator separator-dashed mt-2 mb-1"></div>
                    <!--end::Separator-->
                    <!--begin::Row-->
                    <div class="row g-8 mb-1">
                        <!--begin::Col-->

                        <div class="col-md-4 fv-row">
                            <label class="form-label fs-6 fw-bold">Duruşma Aralığı:</label>    
                             <input class="form-control input-group" style="min-width:20rem !important;" placeholder="Duruşma Aralığı Seçin" id="kt_table_durusmalar_datein" aria-describedby="basic-addon2" />
                             <input type="hidden" id="current_durusma_aralik" name="current_durusma_aralik" value="">
                             <input type="hidden" id="current_durusma_start" name="current_durusma_start" value="<?=$current_durusma_start;?>">
                             <input type="hidden" id="current_durusma_end" name="current_durusma_end" value="<?=$current_durusma_end;?>">


                        </div>
                        
                        <div class="col-md-4 fv-row">
                            <label class="form-label fs-6 fw-bold">Mahkeme:</label>
                            <select class="form-select form-select-solid fw-bolder istFilterComboList" data-kt-select2="true" data-placeholder="Lütfen Seçiniz" data-allow-clear="true" data-hide-search="false" data-kt-durusmalar-update-table-filter="filterMahkeme" data-extra-filter="filter" id="filterMahkemeSelect" multiple>
                                
                                <?php foreach(FormSelectMahkemeList() as $sMemurList){ 
                                    $slcx=(in_array($sMemurList["mh_id"],explode(",",($filterMahkemeSelect ?? ''))))?"selected":"";
                                    ?>
                                    <option value="<?php echo $sMemurList["mh_id"];?>" <?=$slcx;?>><?php echo trim($sMemurList["mh_name"]);?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-4 fv-row">
                            <label class="form-label fs-6 fw-bold">Sorumlu Memur:</label>
                            <select class="form-select form-select-solid fw-bolder istFilterComboList" data-kt-select2="true" data-placeholder="Lütfen Seçiniz" data-allow-clear="true" data-hide-search="false" data-kt-durusmalar-update-table-filter="filterMemur"  data-extra-filter="filter" id="filterMemurSelect" multiple>
                                
                                <?php foreach(FormSelectSorumluMemurList() as $sMemurList){ 
                                    
                                    $slcx=(in_array($sMemurList->u_id,explode(",",($filterMemurSelect ?? ''))))?"selected":"";
                                    ?>
                                    <option value="<?php echo $sMemurList->u_id;?>"  <?=$slcx;?>><?php echo trim($sMemurList->u_name . ' '.$sMemurList->u_lastname). ' '.$sMemurList->u_surname;?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="col-md-4 fv-row">
                            <label class="form-label fs-6 fw-bold">Sorumlu Avukat:</label>
                            <select class="form-select form-select-solid fw-bolder istFilterComboList" data-kt-select2="true" data-placeholder="Lütfen Seçiniz" data-allow-clear="true" data-hide-search="false" data-kt-durusmalar-update-table-filter="filterAvukat" data-extra-filter="filter" id="filterAvukatSelect" multiple>
                                
                                <?php foreach(FormSelectSorumluAvukatList() as $sAvukatList){ 
                                    
                                    $slcx=(in_array($sAvukatList->u_id,explode(",",($filterAvukatSelect ?? ''))))?"selected":"";
                                    ?>
                                    <option value="<?php echo $sAvukatList->u_id;?>" <?=$slcx;?>><?php echo trim($sAvukatList->u_name . ' '.$sAvukatList->u_lastname). ' '.$sAvukatList->u_surname;?></option>
                                <?php } ?>
                            </select>
                        </div> 
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="col-md-4 fv-row">
                            <label class="form-label fs-6 fw-bold">İşlem:</label>
                            <select class="form-select form-select-solid fw-bolder istFilterComboList" data-kt-select2="true" data-placeholder="Lütfen Seçiniz" data-allow-clear="true" data-hide-search="false" data-kt-durusmalar-update-table-filter="filterIslem" data-extra-filter="filter" id="filterIslemSelect" multiple>
                                
                                <?php foreach ($islemList as $islem) {
                                    $slcx=(in_array($islem,explode(",",($filterIslemSelect ?? ''))))?"selected":"";
                                    echo "<option value='$islem' $slcx>$islem</option>";
                                }
                                ?>
                            </select>
                        </div> 


                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-bold mb-2">Taraf</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text"><i class="bi bi-person-workspace fs-3"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <select class="form-select form-select-solid istFilterComboList" data-control="select2" data-hide-search="true"   data-extra-filter="filter" data-placeholder="Seçiniz.." name="dlara_taraf" id="dlara_taraf" multiple>
                                        
                                        <?php foreach ($tarafList as $taraf) {
                                            $slcx=(in_array($taraf,explode(",",($dlara_taraf ?? ''))))?"selected":"";
                                            echo "<option value='$taraf' $slcx>$taraf</option>";
                                        }
                                        ?>                                        
                                    </select>
                                </div>
                            </div>
                        </div>

                        
                    <!--end::Radio group-->
                    </div>                             

                </div>
                
                <!--begin::Chart widget 22-->
                <div class="card h-xl-100">
                    <!--begin::Header-->
                    <div class="card-header position-relative py-0 border-bottom-2">
                        <!--begin::Nav-->
                        <ul class="nav nav-stretch nav-pills nav-pills-custom d-flex mt-3">
                            <!--begin::Item-->
                            <li class="nav-item p-0 ms-0 me-8">
                                <!--begin::Link-->
                                <a class="nav-link btn btn-color-muted active px-0" data-bs-toggle="tab" id="kt_chart_widgets_22avukat_tab_1" href="#kt_chart_widgets_22avukat_tab_content_1">
                                    <!--begin::Subtitle-->
                                    <span class="nav-text fw-semibold fs-4 mb-3">Avukat Bazlı Duruşma Sayısı</span>
                                    <!--end::Subtitle-->
                                    <!--begin::Bullet-->
                                    <span class="bullet-custom position-absolute z-index-2 w-100 h-2px top-100 bottom-n100 bg-primary rounded"></span>
                                    <!--end::Bullet-->
                                </a>
                                <!--end::Link-->
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="nav-item p-0 ms-0">
                                <!--begin::Link-->
                                <a class="nav-link btn btn-color-muted px-0" data-bs-toggle="tab" id="kt_chart_widgets_22memur_tab_2" href="#kt_chart_widgets_22memur_tab_content_2">
                                    <!--begin::Subtitle-->
                                    <span class="nav-text fw-semibold fs-4 mb-3">Memur Bazlı Duruşma Sayısı</span>
                                    <!--end::Subtitle-->
                                    <!--begin::Bullet-->
                                    <span class="bullet-custom position-absolute z-index-2 w-100 h-2px top-100 bottom-n100 bg-primary rounded"></span>
                                    <!--end::Bullet-->
                                </a>
                                <!--end::Link-->
                            </li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Nav-->
                        <!--begin::Toolbar-->
                        <div class="card-toolbar d-none">
                            <!--begin::Daterangepicker(defined in src/js/layout/app.js)-->
                            <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left" class="btn btn-sm btn-light d-flex align-items-center px-4">
                                <!--begin::Display range-->
                                <div class="text-gray-600 fw-bold">Tarih Aralığı Yükleniyor...</div>
                                <!--end::Display range-->
                                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                <span class="svg-icon svg-icon-1 ms-2 me-0">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                        <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                        <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </div>
                            <!--end::Daterangepicker-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pb-3">
                        <!--begin::Tab Content-->
                        <div class="tab-content">
                            <!--begin::Tap pane-->
                            <div class="tab-pane fade show active" id="kt_chart_widgets_22avukat_tab_content_1">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-wrap flex-md-nowrap">
                                    <!--begin::Items-->
                                    <div class="me-md-5 w-100">
                                        <!--begin::Item-->
                                        <?php foreach ($durusmaavukatbazli as $durusmaavukatbazli) { ?>
                                            <div class="d-flex border border-gray-300 border-dashed rounded p-6 mb-6">
                                                <!--begin::Block-->
                                                <div class="d-flex align-items-center flex-grow-1 me-2 me-sm-5">
                                                    <!--begin::Symbol-->
                                                    <div class="symbol symbol-50px me-4">
                                                        <span class="symbol-label">
                                                            <!--begin::Svg Icon | path: icons/duotune/general/gen013.svg-->
                                                            <span class="svg-icon svg-icon-2qx svg-icon-primary">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3" d="M20.9 12.9C20.3 12.9 19.9 12.5 19.9 11.9C19.9 11.3 20.3 10.9 20.9 10.9H21.8C21.3 6.2 17.6 2.4 12.9 2V2.9C12.9 3.5 12.5 3.9 11.9 3.9C11.3 3.9 10.9 3.5 10.9 2.9V2C6.19999 2.5 2.4 6.2 2 10.9H2.89999C3.49999 10.9 3.89999 11.3 3.89999 11.9C3.89999 12.5 3.49999 12.9 2.89999 12.9H2C2.5 17.6 6.19999 21.4 10.9 21.8V20.9C10.9 20.3 11.3 19.9 11.9 19.9C12.5 19.9 12.9 20.3 12.9 20.9V21.8C17.6 21.3 21.4 17.6 21.8 12.9H20.9Z" fill="currentColor" />
                                                                    <path d="M16.9 10.9H13.6C13.4 10.6 13.2 10.4 12.9 10.2V5.90002C12.9 5.30002 12.5 4.90002 11.9 4.90002C11.3 4.90002 10.9 5.30002 10.9 5.90002V10.2C10.6 10.4 10.4 10.6 10.2 10.9H9.89999C9.29999 10.9 8.89999 11.3 8.89999 11.9C8.89999 12.5 9.29999 12.9 9.89999 12.9H10.2C10.4 13.2 10.6 13.4 10.9 13.6V13.9C10.9 14.5 11.3 14.9 11.9 14.9C12.5 14.9 12.9 14.5 12.9 13.9V13.6C13.2 13.4 13.4 13.2 13.6 12.9H16.9C17.5 12.9 17.9 12.5 17.9 11.9C17.9 11.3 17.5 10.9 16.9 10.9Z" fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                    </div>
                                                    <!--end::Symbol-->
                                                    <!--begin::Section-->
                                                    <div class="me-2">
                                                        <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">Avukat <?= @$durusmaavukatbazli->d_avukat; ?></a>
                                                        <span class="text-gray-400 fw-bold d-block fs-7 d-none">İçerik eklenebiilir</span>
                                                    </div>
                                                    <!--end::Section-->
                                                </div>
                                                <!--end::Block-->
                                                <!--begin::Info-->
                                                <div class="d-flex align-items-center">
                                                    <span class="text-dark fw-bolder fs-2x"><?= @$durusmaavukatbazli->sayi; ?></span>
                                                    <span class="fw-semibold fs-2 text-gray-600 mx-1 pt-1"> </span>
                                                    <span class="text-gray-600 fw-semibold fs-2 me-3 pt-2 d-none">76</span>
                                                    <span class="badge badge-lg badge-light-success align-self-center px-2"><?= @$durusmaavukatbazli->yuzde; ?>%</span>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                        <?php } ?>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                          <!--end::Item-->
                                        <!--begin::Item--></div>
                                    <!--end::Items-->
                                    <!--begin::Container-->
                                    <div class="d-flex justify-content-between flex-column w-225px w-md-600px mx-auto mx-md-0 pt-3 pb-10">
                                        <!--begin::Title-->
                                        <div class="fs-4 fw-bold text-gray-900 text-center mb-5">Avukat Bazlı
                                        <br />Duruşma Grafiği</div>
                                        <!--end::Title-->
                                        <!--begin::Chart-->
                                        <div id="kt_chart_widgets_22avukat_chart_1" class="mx-auto mb-4"></div>
                                        <!--end::Chart-->
                                        <!--begin::Labels-->
                                        <div class="mx-auto">
                                            <!--begin::Label-->
                                        </div>
                                        <!--end::Labels-->
                                    </div>
                                    <!--end::Container-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Tap pane-->
                            <!--begin::Tap pane-->
                            <div class="tab-pane fade" id="kt_chart_widgets_22memur_tab_content_2">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-wrap flex-md-nowrap">
                                    <!--begin::Items-->
                                    <div class="me-md-5 w-100">
                                        <!--begin::Item-->
                                        <?php foreach ($durusmamemurbazli as $durusmamemurbazli) { ?>
                                    <div class="d-flex border border-gray-300 border-dashed rounded p-6 mb-6">
                                        <!--begin::Block-->
                                        <div class="d-flex align-items-center flex-grow-1 me-2 me-sm-5">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-50px me-4">
                                                <span class="symbol-label">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                                    <span class="svg-icon svg-icon-2qx svg-icon-primary">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                                            <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                                            <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor" />
                                                            <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </span>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Section-->
                                            <div class="me-2">
                                                <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">Memur <?= @$durusmamemurbazli->d_memur; ?></a>
                                                <span class="text-gray-400 fw-bold d-block fs-7 d-none">veri yazılabili</span>
                                            </div>
                                            <!--end::Section-->
                                        </div>
                                        <!--end::Block-->
                                        <!--begin::Info-->
                                        <div class="d-flex align-items-center">
                                            <span class="text-dark fw-bolder fs-2x"><?= @$durusmamemurbazli->sayi; ?></span>
                                            <span class="fw-semibold fs-2 text-gray-600 mx-1 pt-1"> </span>
                                            <span class="text-gray-600 fw-semibold fs-2 me-3 pt-2 d-none">154</span>
                                            <span class="badge badge-lg badge-light-success align-self-center px-2"><?= @$durusmamemurbazli->yuzde; ?>%</span>
                                        </div>
                                        <!--end::Info-->
                                    </div>
                                        <?php } ?>                                      
                                    </div>
                                    <!--end::Items-->
                                    <!--begin::Container-->
                                    <div class="d-flex justify-content-between flex-column w-225px w-md-600px mx-auto mx-md-0 pt-3 pb-10">
                                        <!--begin::Title-->
                                        <div class="fs-4 fw-bold text-gray-900 text-center mb-5">Memur Bazlı
                                        <br />Duruşma Grafiği</div>
                                        <!--end::Title-->
                                        <!--begin::Chart-->
                                        <div id="kt_chart_widgets_22memur_chart_2" class="mx-auto mb-4"></div>
                                        <!--end::Chart-->
                                        <!--begin::Labels-->
                                        <div class="mx-auto">
                                            <!--begin::Label-->
                                        </div>
                                        <!--end::Labels-->
                                    </div>
                                    <!--end::Container-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Tap pane-->
                        </div>
                        <!--end::Tab Content-->
                    </div>
                    <!--end: Card Body-->
                </div>
                <!--end::Chart widget 22-->
            </div>
            <!--end::Col-->
            <!--begin::Col istatistik-->
            <div class="col-xxl-12 mb-5 mb-xl-10">
                <!--begin::Chart widget 22-->
                <div class="card h-xl-100">
                    <!--begin::Header-->
                    <div class="card-header position-relative py-0 border-bottom-2">
                        <!--begin::Nav-->
                        <ul class="nav nav-stretch nav-pills nav-pills-custom d-flex mt-3">
                            <!--begin::Item-->
                            <li class="nav-item p-0 ms-0 me-8">
                                <!--begin::Link-->
                                <a class="nav-link btn btn-color-muted active px-0" data-bs-toggle="tab" id="kt_chart_widgets_22_tab_3" href="#kt_chart_widgets_22_tab_content_3">
                                    <!--begin::Subtitle-->
                                    <span class="nav-text fw-semibold fs-4 mb-3">Taraf Bazlı Duruşma Sayısı</span>
                                    <!--end::Subtitle-->
                                    <!--begin::Bullet-->
                                    <span class="bullet-custom position-absolute z-index-2 w-100 h-2px top-100 bottom-n100 bg-primary rounded"></span>
                                    <!--end::Bullet-->
                                </a>
                                <!--end::Link-->
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="nav-item p-0 ms-0">
                                <!--begin::Link-->
                                <a class="nav-link btn btn-color-muted px-0" data-bs-toggle="tab" id="kt_chart_widgets_22_tab_4" href="#kt_chart_widgets_22_tab_content_4">
                                    <!--begin::Subtitle-->
                                    <span class="nav-text fw-semibold fs-4 mb-3">Mahkeme Bazlı Duruşma Sayısı</span>
                                    <!--end::Subtitle-->
                                    <!--begin::Bullet-->
                                    <span class="bullet-custom position-absolute z-index-2 w-100 h-2px top-100 bottom-n100 bg-primary rounded"></span>
                                    <!--end::Bullet-->
                                </a>
                                <!--end::Link-->
                            </li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Nav-->
                        <!--begin::Toolbar-->
                        <div class="card-toolbar d-none">
                            <!--begin::Daterangepicker(defined in src/js/layout/app.js)-->
                            <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left" class="btn btn-sm btn-light d-flex align-items-center px-4">
                                <!--begin::Display range-->
                                <div class="text-gray-600 fw-bold">Tarih Aralığı Yükleniyor...</div>
                                <!--end::Display range-->
                                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                <span class="svg-icon svg-icon-1 ms-2 me-0">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                        <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                        <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </div>
                            <!--end::Daterangepicker-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pb-3">
                        <!--begin::Tab Content-->
                        <div class="tab-content">
                            <!--begin::Tap pane-->
                            <div class="tab-pane fade show active" id="kt_chart_widgets_22_tab_content_3">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-wrap flex-md-nowrap">
                                    <!--begin::Items-->
                                    <div class="me-md-5 w-100">
                                        <!--begin::Item-->
                                        <?php foreach ($durusmatarafbazli as $durusmatarafbazli) { ?>
                                        <div class="d-flex border border-gray-300 border-dashed rounded p-6 mb-6">
                                            <!--begin::Block-->
                                            <div class="d-flex align-items-center flex-grow-1 me-2 me-sm-5">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-50px me-4">
                                                    <span class="symbol-label">
                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen013.svg-->
                                                        <span class="svg-icon svg-icon-2qx svg-icon-primary">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20.9 12.9C20.3 12.9 19.9 12.5 19.9 11.9C19.9 11.3 20.3 10.9 20.9 10.9H21.8C21.3 6.2 17.6 2.4 12.9 2V2.9C12.9 3.5 12.5 3.9 11.9 3.9C11.3 3.9 10.9 3.5 10.9 2.9V2C6.19999 2.5 2.4 6.2 2 10.9H2.89999C3.49999 10.9 3.89999 11.3 3.89999 11.9C3.89999 12.5 3.49999 12.9 2.89999 12.9H2C2.5 17.6 6.19999 21.4 10.9 21.8V20.9C10.9 20.3 11.3 19.9 11.9 19.9C12.5 19.9 12.9 20.3 12.9 20.9V21.8C17.6 21.3 21.4 17.6 21.8 12.9H20.9Z" fill="currentColor" />
                                                                <path d="M16.9 10.9H13.6C13.4 10.6 13.2 10.4 12.9 10.2V5.90002C12.9 5.30002 12.5 4.90002 11.9 4.90002C11.3 4.90002 10.9 5.30002 10.9 5.90002V10.2C10.6 10.4 10.4 10.6 10.2 10.9H9.89999C9.29999 10.9 8.89999 11.3 8.89999 11.9C8.89999 12.5 9.29999 12.9 9.89999 12.9H10.2C10.4 13.2 10.6 13.4 10.9 13.6V13.9C10.9 14.5 11.3 14.9 11.9 14.9C12.5 14.9 12.9 14.5 12.9 13.9V13.6C13.2 13.4 13.4 13.2 13.6 12.9H16.9C17.5 12.9 17.9 12.5 17.9 11.9C17.9 11.3 17.5 10.9 16.9 10.9Z" fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Section-->
                                                <div class="me-2">
                                                    <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold"> <?= @$durusmatarafbazli->d_taraf; ?></a>
                                                    <span class="text-gray-400 fw-bold d-block fs-7 d-none">İçerik eklenebiilir</span>
                                                </div>
                                                <!--end::Section-->
                                            </div>
                                            <!--end::Block-->
                                            <!--begin::Info-->
                                            <div class="d-flex align-items-center">
                                                <span class="text-dark fw-bolder fs-2x"><?= @$durusmatarafbazli->sayi; ?></span>
                                                <span class="fw-semibold fs-2 text-gray-600 mx-1 pt-1"> </span>
                                                <span class="text-gray-600 fw-semibold fs-2 me-3 pt-2 d-none">76</span>
                                                <span class="badge badge-lg badge-light-success align-self-center px-2"><?= @$durusmatarafbazli->yuzde; ?>%</span>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <?php } ?>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                          <!--end::Item-->
                                        <!--begin::Item-->
                                       
                                    </div>
                                    <!--end::Items-->                                    
                                    <!--begin::Container-->
                                    <div class="d-flex justify-content-between flex-column w-225px w-md-600px mx-auto mx-md-0 pt-3 pb-10">
                                        <!--begin::Title-->
                                        <div class="fs-4 fw-bold text-gray-900 text-center mb-5">Taraf Bazlı
                                        <br />Duruşma Grafiği</div>
                                        <!--end::Title-->
                                        <!--begin::Chart-->
                                        <div id="kt_chart_widgets_22taraf_chart_3" class="mx-auto mb-4"></div>
                                        <!--end::Chart-->
                                        <!--begin::Labels-->
                                        <div class="mx-auto">
                                            <!--begin::Label-->
                                        </div>
                                        <!--end::Labels-->
                                    </div>
                                    <!--end::Container-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Tap pane-->
                            <!--begin::Tap pane-->
                            <div class="tab-pane fade" id="kt_chart_widgets_22_tab_content_4">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-wrap flex-md-nowrap">
                                    <!--begin::Items-->
                                    <div class="me-md-5 w-100">
                                        <!--begin::Item-->
                                        <?php foreach ($durusmamahkemebazli as $durusmamahkemebazli) { ?>
                                            <div class="d-flex border border-gray-300 border-dashed rounded p-6 mb-6">
                                                <!--begin::Block-->
                                                <div class="d-flex align-items-center flex-grow-1 me-2 me-sm-5">
                                                    <!--begin::Symbol-->
                                                    <div class="symbol symbol-50px me-4">
                                                        <span class="symbol-label">
                                                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                                            <span class="svg-icon svg-icon-2qx svg-icon-primary">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                                                    <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                                                    <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor" />
                                                                    <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                    </div>
                                                    <!--end::Symbol-->
                                                    <!--begin::Section-->
                                                    <div class="me-2">
                                                        <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold"> <?= substr(etAyracsizYazdir(@$durusmamahkemebazli->d_mahkeme), 0, 50); ?></a>
                                                        <span class="text-gray-400 fw-bold d-block fs-7 d-none">veri yazılabili</span>
                                                    </div>
                                                    <!--end::Section-->
                                                </div>
                                                <!--end::Block-->
                                                <!--begin::Info-->
                                                <div class="d-flex align-items-center">
                                                    <span class="text-dark fw-bolder fs-2x"><?= @$durusmamahkemebazli->sayi; ?></span>
                                                    <span class="fw-semibold fs-2 text-gray-600 mx-1 pt-1"> </span>
                                                    <span class="text-gray-600 fw-semibold fs-2 me-3 pt-2 d-none">154</span>
                                                    <span class="badge badge-lg badge-light-success align-self-center px-2"><?= @$durusmamahkemebazli->yuzde; ?>%</span>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                        <?php } ?>                                      
                                    </div>
                                    <!--end::Items-->
                                    <!--begin::Container-->
                                    <div class="d-flex justify-content-between flex-column w-225px w-md-600px mx-auto mx-md-0 pt-3 pb-10">
                                        <!--begin::Title-->
                                        <div class="fs-4 fw-bold text-gray-900 text-center mb-5">Mahkeme Bazlı
                                        <br />Duruşma Grafiği</div>
                                        <!--end::Title-->
                                        <!--begin::Chart-->
                                        <div id="kt_chart_widgets_22mahkeme_chart_4" class="mx-auto mb-4"></div>
                                        <!--end::Chart-->
                                        <!--begin::Labels-->
                                        <div class="mx-auto">
                                            <!--begin::Label-->
                                        </div>
                                        <!--end::Labels-->
                                    </div>
                                    <!--end::Container-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Tap pane-->
                        </div>
                        <!--end::Tab Content-->
                    </div>
                    <!--end: Card Body-->
                </div>
                <!--end::Chart widget 22-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xxl-6 mb-5 mb-xl-10">
            <!--begin::Chart widget 27-->
            <div class="card card-flush h-xl-100">
            <!--begin::Header-->
            <div class="card-header py-7">
                <!--begin::Statistics-->
                <div class="m-0">
                    <!--begin::Heading-->
                    <div class="d-flex align-items-center mb-2">
                        <!--begin::Title-->
                        <span class="card-label fw-bold fs-3 mb-1">İşlem Bazlı Duruşma İstatistiği</span>
                        <!--end::Title-->
                    </div>
                    <!--end::Heading-->
                </div>
                <!--end::Statistics-->
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-0 pb-1">
                <div id="kt_charts_widget_27-islem" class="min-h-auto"></div>
            </div>
            <!--end::Body-->
            </div>
            <!--end::Chart widget 27-->
            </div>
            <!--end::Col-->
            <div class="col-xxl-6 mb-5 mb-xl-10">
            <!--begin::Charts Widget 1-->
            <div class="card card-flush h-xl-100">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Duruşma - Karar Aylık İstatistiği</span>
                    </h3>
                    <!--end::Title-->
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body">
                    <!--begin::Chart-->
                    <div id="kt_charts_widget_1_chart-drsma" style="height: 350px"></div>
                    <!--end::Chart-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Charts Widget 1-->
        </div>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Content container-->
</div>
<!--end::Content-->

