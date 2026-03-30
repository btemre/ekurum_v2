<?php

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
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">HEDAS | İstatistikler</h1>
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
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!--begin::Col istatistik-->
            <div class="col-xxl-12 mb-5 mb-xl-10">

                
                <div class="align-items-center">                                               
                    <a id="kt_horizontal_search_advanced_link" class="btn btn-secondary  mb-3" data-bs-toggle="collapse" href="#kt_durusma_advanced_search_form">FİLTRELER <i class="fas fa-caret-down"></i></a>    
                    <a id="kt_horizontal_search_advanced_update"  class="btn btn-primary  mb-3 ml-5">ARA</i></a>
                    <a id="kt_horizontal_search_advanced_reset"  class="btn btn-success  mb-3 ml-5">RESET</i></a>
                </div>

                <div class="collapse show" id="kt_durusma_advanced_search_form">
                    <!--begin::Separator-->
                    <div class="separator separator-dashed mt-2 mb-1"></div>
                    <!--end::Separator-->
                    <!--begin::Row-->
                    <div class="row g-8 mb-1">
                        <!--begin::Col-->

                        <div class="col-md-4 fv-row">
                            <label class="form-label fs-6 fw-bold">Çalışma Aralığı:</label>    
                             <input class="form-control input-group" style="min-width:20rem !important;" placeholder="Duruşma Aralığı Seçin" id="kt_table_durusmalar_datein" aria-describedby="basic-addon2" />
                             <input type="hidden" id="current_durusma_aralik" name="current_durusma_aralik" value="">
                             <input type="hidden" id="current_durusma_start" name="current_durusma_start" value="<?=$current_durusma_start;?>">
                             <input type="hidden" id="current_durusma_end" name="current_durusma_end" value="<?=$current_durusma_end;?>">


                        </div>
                        
                        <div class="col-md-4 fv-row">
                            <label class="form-label fs-6 fw-bold">Mahkeme:</label>
                            <select multiple class="form-select form-select-solid fw-bolder istFilterComboList" data-kt-select2="true" data-placeholder="Lütfen Seçiniz" data-allow-clear="true" data-hide-search="false" data-close-on-select="false" data-kt-durusmalar-update-table-filter="filterMahkeme" data-extra-filter="filter" id="filterMahkemeSelect">
                                <option value="-1">Hepsi</option>
                                <?php 
                                $filterMahkemeSelectArr=explode(",",$filterMahkemeSelect);
                                foreach(FormSelectMahkemeList() as $sMemurList){ 
                                    
                                    $slcx=(in_array($sMemurList["mh_id"],$filterMahkemeSelectArr))?"selected":"";
                                    ?>
                                    <option value="<?php echo $sMemurList["mh_id"];?>" <?=$slcx;?>><?php echo trim($sMemurList["mh_name"]);?></option>
                                <?php } ?>
                            </select>
                        </div>


                        <?php 
                        
                        
                        foreach ($dosyaStats?$dosyaStats:array() as $kDosya=>$vDosya) {
                            $defSelectedOptions="";

                            $filterVal=$this->input->get("filtre_".$kDosya);

                            $filterVal=(!empty($filterVal) and $filterVal!="null" and $filterVal<>-1)?$filterVal:"Hepsi";
                            $filterKey=(!empty($filterVal) and $filterVal!="null" and $filterVal<>-1  and $filterVal!="Hepsi")?$filterVal:"";


                            if ($filterKey) {
                                $filterArr=explode(",",$filterKey);

                                foreach ($filterArr as $kf=>$vf) {
                                    if (empty($vf)) continue;
                                    $vfx=dosyaIstatistikGetNamebyId($kDosya,$vf);

                                    $defSelectedOptions.="<option value='$kf' selected>$vfx</option>";
                                }
                            }
                            
                            
                            
                           
                        ?>

                        <div class="col-md-4 fv-row">
                            <label class="form-label fs-6 fw-bold"><?=$vDosya;?>:</label>
                            <select data-filter-id="<?=$kDosya;?>" multiple class="form-select form-select-solid fw-bolder istFilterComboList autoFilters" data-kt-select2="true" data-placeholder="Lütfen Seçiniz" data-allow-clear="true" data-hide-search="false"  id="filtre_<?=$kDosya;?>">
                                <option value="-1">Hepsi</option>
                                <?=$defSelectedOptions;?>
                                
                                
                                
                                
                                
                            </select>
                        </div>
                            

                        <?php                        
                        }

                        ?>

                       
                        
                    <!--end::Radio group-->
                    </div>                             

                </div>
                
                
        <div class="row g-5 g-xl-8 mt-3">
            <!--begin::Col davacı sayısı-->
            <div class="col-xl-6">
                <!--begin::Mixed Widget 1-->
                <div class="card card-xl-stretch mb-xl-8">
                    <!--begin::Body-->
                    <div class="card-body p-0">
                        <!--begin::Header-->
                        <div class="px-9 pt-7 card-rounded h-275px w-100 bg-danger">
                            <!--begin::Heading-->
                            <div class="d-flex flex-stack">
                                <h3 class="m-0 text-white fw-bold fs-3">Davacı Sayısı</h3>
                                <div class="ms-1">

                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Balance-->
                            <div class="d-flex text-center flex-column text-white pt-8">
                                <span class="fw-semibold fs-7">Toplam Dava Sayısı</span>
                                <span class="fw-bold fs-2x pt-1">
                                <?= isset($dosyadavaci[0]->toplam) ? $dosyadavaci[0]->toplam : '0' ?>
                                </span>
                            </div>
                            <!--end::Balance-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Items-->
                        
                        <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
                            style="margin-top:-120px;">
                            <div style="position:relative;max-height:40rem;overflow:auto;">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center pe-2 mb-5">
                                <span class="text-muted fw-bold fs-5 flex-grow-1">
                                    Davacı </span>
                                <div class="symbol symbol-50px">
                                    <span class="text-muted fw-bold fs-5 flex-grow-1">
                                        Sayısı</span>
                                </div>
                            </div>
                            
                            <?php 
                            $total=@$dosyadavaci[0]->toplam;
                            
                            foreach ($dosyadavaci as $dosyadavaci) { ?>
                                <div class="d-flex align-items-center mb-3">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-45px w-40px me-5">
                                        <span class="symbol-label bg-lighten">
                                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                        fill="currentColor"></path>
                                                    <path opacity="0.3"
                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center flex-wrap w-100">
                                        <!--begin::Title-->
                                        <div class="mb-1 pe-3 flex-grow-1">
                                            <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">
                                                <?= substr(etAyracsizYazdir(@$dosyadavaci->davaci), 0, 35); ?>
                                            </a>
                                            <div class="text-gray-400 fw-semibold fs-7 d-none">Yazılan ceza sayısı :
                                                <?=@$dosyadavaci->sayi; ?>
                                            </div>
                                        </div>
                                        <!--end::Title-->
                                        <!--begin::Label-->
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold fs-5 text-gray-800 pe-1">
                                                <?=@$dosyadavaci->sayi; ?>
                                            </div>
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                            <?php 
                            $total=$total-@$dosyadavaci->sayi;                   
                            
                            } ?>
                                <div class="d-flex align-items-center mb-6">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-45px w-40px me-5">
                                        <span class="symbol-label bg-lighten">
                                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                        fill="currentColor"></path>
                                                    <path opacity="0.3"
                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center flex-wrap w-100">
                                        <!--begin::Title-->
                                        <div class="mb-1 pe-3 flex-grow-1">
                                            <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">
                                                Diğer
                                            </a>
                                            
                                        </div>
                                        <!--end::Title-->
                                        <!--begin::Label-->
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold fs-5 text-gray-800 pe-1">
                                                <?=$total; ?>
                                            </div>
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                            
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Items-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>
          <!--begin::Col davalı sayısı-->
          <div class="col-xl-6">
                <!--begin::Mixed Widget 1-->
                <div class="card card-xl-stretch mb-xl-8">
                    <!--begin::Body-->
                    <div class="card-body p-0">
                        <!--begin::Header-->
                        <div class="px-9 pt-7 card-rounded h-275px w-100 bg-success">
                            <!--begin::Heading-->
                            <div class="d-flex flex-stack">
                                <h3 class="m-0 text-white fw-bold fs-3">Davalı Sayısı</h3>
                                <div class="ms-1">

                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Balance-->
                            <div class="d-flex text-center flex-column text-white pt-8">
                                <span class="fw-semibold fs-7">Toplam Dava Sayısı</span>
                                <span class="fw-bold fs-2x pt-1">
                                <?= isset($dosyadavali[0]->toplam) ? $dosyadavali[0]->toplam : '0' ?>
                                </span>
                            </div>
                            <!--end::Balance-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Items-->
                        <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
                            style="margin-top: -120px">
                            <div style="position:relative;max-height:40rem;overflow:auto;">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center pe-2 mb-5">
                                <span class="text-muted fw-bold fs-5 flex-grow-1">
                                    Davalı </span>
                                <div class="symbol symbol-50px">
                                    <span class="text-muted fw-bold fs-5 flex-grow-1">
                                        Sayısı</span>
                                </div>
                            </div>
                            
                            <?php 
                            $total=@$dosyadavali[0]->toplam;
                            foreach ($dosyadavali as $dosyadavali) { ?>
                                <div class="d-flex align-items-center mb-6">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-45px w-40px me-5">
                                        <span class="symbol-label bg-lighten">
                                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                        fill="currentColor"></path>
                                                    <path opacity="0.3"
                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center flex-wrap w-100">
                                        <!--begin::Title-->
                                        <div class="mb-1 pe-3 flex-grow-1">
                                            <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">
                                                <?= substr(etAyracsizYazdir(@$dosyadavali->davali), 0, 35); ?>
                                            </a>
                                            <div class="text-gray-400 fw-semibold fs-7 d-none">Yazılan ceza sayısı :
                                                <?=@$dosyadavali->sayi; ?>
                                            </div>
                                        </div>
                                        <!--end::Title-->
                                        <!--begin::Label-->
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold fs-5 text-gray-800 pe-1">
                                                <?=@$dosyadavali->sayi; ?>
                                            </div>
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                            <?php 
                        $total=$total-@$dosyadavali->sayi;
                        } ?>

                            <div class="d-flex align-items-center mb-6">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-45px w-40px me-5">
                                        <span class="symbol-label bg-lighten">
                                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                        fill="currentColor"></path>
                                                    <path opacity="0.3"
                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center flex-wrap w-100">
                                        <!--begin::Title-->
                                        <div class="mb-1 pe-3 flex-grow-1">
                                            <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">
                                                Diğer
                                            </a>
                                           
                                        </div>
                                        <!--end::Title-->
                                        <!--begin::Label-->
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold fs-5 text-gray-800 pe-1">
                                                <?=$total; ?>
                                            </div>
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Description-->
                                </div>

                            <!--end::Item-->
                            <!--begin::Item-->
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Items-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>
          <!--begin::Col mahkeme sayısı-->
          <div class="col-xl-6">
                <!--begin::Mixed Widget 1-->
                <div class="card card-xl-stretch mb-xl-8">
                    <!--begin::Body-->
                    <div class="card-body p-0">
                        <!--begin::Header-->
                        <div class="px-9 pt-7 card-rounded h-275px w-100 bg-primary">
                            <!--begin::Heading-->
                            <div class="d-flex flex-stack">
                                <h3 class="m-0 text-white fw-bold fs-3">Mahkeme Sayısı</h3>
                                <div class="ms-1">

                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Balance-->
                            <div class="d-flex text-center flex-column text-white pt-8">
                                <span class="fw-semibold fs-7">Toplam Mahkeme Sayısı</span>
                                <span class="fw-bold fs-2x pt-1">
                                <?= $dosyamahkemeTotal;?>
                                </span>
                            </div>
                            <!--end::Balance-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Items-->
                        <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
                            style="margin-top: -120px">
                            <div style="position:relative;max-height:40rem;overflow:auto;">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center pe-2 mb-5">
                                <span class="text-muted fw-bold fs-5 flex-grow-1">
                                    Mahkeme </span>
                                <div class="symbol symbol-50px">
                                    <span class="text-muted fw-bold fs-5 flex-grow-1">
                                        Sayısı</span>
                                </div>
                            </div>
                            
                            <?php 
                            
                            $total=@$dosyamahkemeTotal;
                            foreach ($dosyamahkeme as $dosyamahkeme) { ?>
                                <div class="d-flex align-items-center mb-6">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-45px w-40px me-5">
                                        <span class="symbol-label bg-lighten">
                                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                        fill="currentColor"></path>
                                                    <path opacity="0.3"
                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center flex-wrap w-100">
                                        <!--begin::Title-->
                                        <div class="mb-1 pe-3 flex-grow-1">
                                            <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">
                                                <?= substr(etAyracsizYazdir(@$dosyamahkeme->mahkeme), 0, 36); ?>
                                            </a>
                                            <div class="text-gray-400 fw-semibold fs-7 d-none">Yazılan ceza sayısı :
                                                <?=@$dosyamahkeme->sayi; ?>
                                            </div>
                                        </div>
                                        <!--end::Title-->
                                        <!--begin::Label-->
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold fs-5 text-gray-800 pe-1">
                                                <?=@$dosyamahkeme->sayi; ?>
                                            </div>
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                            <?php 
                            $total=$total-@$dosyamahkeme->sayi;
                        } ?>

                            <div class="d-flex align-items-center mb-6">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-45px w-40px me-5">
                                        <span class="symbol-label bg-lighten">
                                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                        fill="currentColor"></path>
                                                    <path opacity="0.3"
                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center flex-wrap w-100">
                                        <!--begin::Title-->
                                        <div class="mb-1 pe-3 flex-grow-1">
                                            <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">
                                                Diğer
                                            </a>
                                            <div class="text-gray-400 fw-semibold fs-7 d-none">Yazılan ceza sayısı :
                                                <?=$total; ?>
                                            </div>
                                        </div>
                                        <!--end::Title-->
                                        <!--begin::Label-->
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold fs-5 text-gray-800 pe-1">
                                                <?=$total; ?>
                                            </div>
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Description-->
                                </div> 

                            <!--end::Item-->
                            <!--begin::Item-->
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Items-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>

            <!--begin::Col Dava konsusu açıkalaları sayısı-->
            <div class="col-xl-6">
                <!--begin::Mixed Widget 1-->
                <div class="card card-xl-stretch mb-xl-8">
                    <!--begin::Body-->
                    <div class="card-body p-0">
                        <!--begin::Header-->
                        <div class="px-9 pt-7 card-rounded h-275px w-100 bg-info">
                            <!--begin::Heading-->
                            <div class="d-flex flex-stack">
                                <h3 class="m-0 text-white fw-bold fs-3">Dava Konusu Sayısı</h3>
                                <div class="ms-1">

                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Balance-->
                            <div class="d-flex text-center flex-column text-white pt-8">
                                <span class="fw-semibold fs-7">Toplam Dava Sayısı</span>
                                <span class="fw-bold fs-2x pt-1">
                                <?= isset($dosyadavaaciklama[0]->toplam) ? $dosyadavaaciklama[0]->toplam : '0' ?>
                                </span>
                            </div>
                            <!--end::Balance-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Items-->
                        <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
                            style="margin-top: -120px">
                            <div style="position:relative;max-height:40rem;overflow:auto;">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center pe-2 mb-5">
                                <span class="text-muted fw-bold fs-5 flex-grow-1">
                                    Konu </span>
                                <div class="symbol symbol-50px">
                                    <span class="text-muted fw-bold fs-5 flex-grow-1">
                                        Sayısı</span>
                                </div>
                            </div>
                            
                            <?php 
                            $total=@$dosyadavaaciklama[0]->toplam;
                            foreach ($dosyadavaaciklama as $dosyadavaaciklama) { ?>
                                <div class="d-flex align-items-center mb-6">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-45px w-40px me-5">
                                        <span class="symbol-label bg-lighten">
                                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                        fill="currentColor"></path>
                                                    <path opacity="0.3"
                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center flex-wrap w-100">
                                        <!--begin::Title-->
                                        <div class="mb-1 pe-3 flex-grow-1">
                                            <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">
                                                <?= substr(etAyracsizYazdir(@$dosyadavaaciklama->aciklama), 0, 35); ?>
                                            </a>
                                            <div class="text-gray-400 fw-semibold fs-7 d-none">Yazılan ceza sayısı :
                                                <?=@$dosyadavaaciklama->sayi; ?>
                                            </div>
                                        </div>
                                        <!--end::Title-->
                                        <!--begin::Label-->
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold fs-5 text-gray-800 pe-1">
                                                <?=@$dosyadavaaciklama->sayi; ?>
                                            </div>
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                            <?php 
                            $total=$total-@$dosyadavaaciklama->sayi;
                        } ?>
                            <div class="d-flex align-items-center mb-6">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-45px w-40px me-5">
                                        <span class="symbol-label bg-lighten">
                                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                        fill="currentColor"></path>
                                                    <path opacity="0.3"
                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center flex-wrap w-100">
                                        <!--begin::Title-->
                                        <div class="mb-1 pe-3 flex-grow-1">
                                            <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">
                                                Diğer
                                            </a>

                                        </div>
                                        <!--end::Title-->
                                        <!--begin::Label-->
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold fs-5 text-gray-800 pe-1">
                                                <?=$total;?>
                                            </div>
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                       
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Items-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>

        <!-- begin loop-->


            <?php for ($zx=0;$zx<2;$zx++) { 
                $mainData=$loopData[$zx];
                ?>


            <div class="col-xl-6">
                <!--begin::Mixed Widget 1-->
                <div class="card card-xl-stretch mb-xl-8">
                    <!--begin::Body-->
                    <div class="card-body p-0">
                        <!--begin::Header-->
                        <div class="px-9 pt-7 card-rounded h-275px w-100 bg-info">
                            <!--begin::Heading-->
                            <div class="d-flex flex-stack">
                                <h3 class="m-0 text-white fw-bold fs-3"><?=$mainData["mainTitle"];?></h3>
                                <div class="ms-1">

                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Balance-->
                            <div class="d-flex text-center flex-column text-white pt-8">
                                <span class="fw-semibold fs-7"><?=$mainData["subTitle"]?></span>
                                <span class="fw-bold fs-2x pt-1">
                                <?=$mainData["toplamDava"]?>
                                </span>
                            </div>
                            <!--end::Balance-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Items-->
                        <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
                            style="margin-top: -120px">
                            <div style="position:relative;max-height:40rem;overflow:auto;">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center pe-2 mb-5">
                                <span class="text-muted fw-bold fs-5 flex-grow-1">
                                    Konu </span>
                                <div class="symbol symbol-50px">
                                    <span class="text-muted fw-bold fs-5 flex-grow-1">
                                        Sayısı</span>
                                </div>
                            </div>
                            
                            <?php 
                            $total=0;
                            $dosyadavaaciklama=$mainData["davalar"];
                            foreach ($dosyadavaaciklama as $dosyadavaaciklama) { 
                                $txtDetail=strip_tags(html_entity_decode($dosyadavaaciklama->aciklama));
                                ?>
                                <div class="d-flex align-items-center mb-6">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-45px w-40px me-5">
                                        <span class="symbol-label bg-lighten">
                                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                        fill="currentColor"></path>
                                                    <path opacity="0.3"
                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center flex-wrap w-100">
                                        <!--begin::Title-->
                                        <div class="mb-1 pe-3 flex-grow-1">
                                            <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold" title="<?=$txtDetail;?>">
                                                <?= substr($txtDetail, 0, 60); ?>
                                            </a>
                                            <div class="text-gray-400 fw-semibold fs-7 d-none">Yazılan ceza sayısı :
                                                <?=@$dosyadavaaciklama->sayi; ?>
                                            </div>
                                        </div>
                                        <!--end::Title-->
                                        <!--begin::Label-->
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold fs-5 text-gray-800 pe-1">
                                                <?=@$dosyadavaaciklama->sayi; ?>
                                            </div>
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                            <?php 
                            $total=$total-@$dosyadavaaciklama->sayi;
                        } ?>
                            <div class="d-flex align-items-center mb-6">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-45px w-40px me-5">
                                        <span class="symbol-label bg-lighten">
                                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                        fill="currentColor"></path>
                                                    <path opacity="0.3"
                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center flex-wrap w-100">
                                        <!--begin::Title-->
                                        <div class="mb-1 pe-3 flex-grow-1">
                                            <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">
                                                Diğer
                                            </a>

                                        </div>
                                        <!--end::Title-->
                                        <!--begin::Label-->
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold fs-5 text-gray-800 pe-1">
                                                <?=$total;?>
                                            </div>
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                       
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Items-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>        


            <?php } ?>
           
        


        </div> <!-- end loopfb -->

                <!--end::Chart widget 22-->
            </div>
            <!--end::Col-->
            
               <!--end::Col-->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Content container-->
</div>
<!--end::Content-->

