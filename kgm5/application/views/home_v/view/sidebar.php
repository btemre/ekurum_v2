<?php
$userAvatar=(($userData->userB->u_cinsiyet==1))?"blank.jpg":"blank-k.jpg";
?>
<style>
  .hidden {display:none};
</style>
<div id="kt_sidebar" class="sidebar px-5 py-5 py-lg-8 px-lg-11" data-kt-drawer="true" data-kt-drawer-name="sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="375px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_sidebar_toggle">
  <!--begin::Header-->
  <div class="d-flex flex-stack mb-5 mb-lg-8" id="kt_sidebar_header">
    <!--begin::Title-->
    <h2 class="text-white">Güncellemeler</h2>
    <!--end::Title-->
    <!--begin::Menu-->
    <div class="ms-1">
      <button class="btn btn-icon btn-sm btn-color-white btn-active-color-primary me-n5" style="display:none;" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-overflow="true">
        <!--begin::Svg Icon | path: icons/duotune/general/gen023.svg-->
        <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="4" fill="currentColor" />
            <rect x="11" y="11" width="2.6" height="2.6" rx="1.3" fill="currentColor" />
            <rect x="15" y="11" width="2.6" height="2.6" rx="1.3" fill="currentColor" />
            <rect x="7" y="11" width="2.6" height="2.6" rx="1.3" fill="currentColor" />
          </svg>
        </span>
        <!--end::Svg Icon-->
      </button>
      <!--begin::Menu 2-->
      <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px" data-kt-menu="true">
        <!--begin::Menu item-->
        <div class="menu-item px-3">
          <div class="menu-content fs-6 text-dark fw-bolder px-3 py-4">Menüler</div>
        </div>
        <!--end::Menu item-->
        <!--begin::Menu separator-->
        <div class="separator mb-3 opacity-75"></div>
        <!--end::Menu separator-->
        <!--begin::Menu item-->
        <div class="menu-item px-3">
          <a href="#" class="menu-link px-3">Sistem Yönetimi</a>
        </div>
        <!--end::Menu item-->
        <!--begin::Menu item-->
        <div class="menu-item px-3">
          <a href="#" class="menu-link px-3">Deneme</a>
        </div>
        <!--end::Menu item-->
        <!--begin::Menu item-->
        <div class="menu-item px-3" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">
          <!--begin::Menu item-->
          <a href="#" class="menu-link px-3">
            <span class="menu-title">New Group</span>
            <span class="menu-arrow"></span>
          </a>
          <!--end::Menu item-->
          <!--begin::Menu sub-->
          <div class="menu-sub menu-sub-dropdown w-175px py-4">
            <!--begin::Menu item-->
            <div class="menu-item px-3">
              <a href="#" class="menu-link px-3">Admin Group</a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3">
              <a href="#" class="menu-link px-3">Staff Group</a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3">
              <a href="#" class="menu-link px-3">Member Group</a>
            </div>
            <!--end::Menu item-->
          </div>
          <!--end::Menu sub-->
        </div>
        <!--end::Menu item-->
        <!--begin::Menu item-->
        <div class="menu-item px-3">
          <a href="#" class="menu-link px-3">New Contact</a>
        </div>
        <!--end::Menu item-->
        <!--begin::Menu separator-->
        <div class="separator mt-3 opacity-75"></div>
        <!--end::Menu separator-->
        <!--begin::Menu item-->
        <div class="menu-item px-3">
          <div class="menu-content px-3 py-3">
            <a class="btn btn-primary btn-sm px-4" href="<?php echo base_url("logout"); ?>">Çıkış Yap</a>
          </div>
        </div>
        <!--end::Menu item-->
      </div>
      <!--end::Menu 2-->
      <!--end::Menu-->
    </div>

    <!--begin::Toolbar wrapper-->
    <div class="d-flex align-items-stretch flex-shrink-0">

      <!--begin::User menu-->
      <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
        <!--begin::Menu wrapper-->
        <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
          <img src="<?php echo base_url('assets/media/avatars/'.$userAvatar); ?>" alt="user" />
        </div>
        <!--begin::User account menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
          <!--begin::Menu item-->
          <div class="menu-item px-3">
            <div class="menu-content d-flex align-items-center px-3">
              <!--begin::Avatar-->
              <div class="symbol symbol-50px me-5">
                <img alt="Hoşgeldiniz!" src="<?php echo base_url('assets/media/avatars/'.$userAvatar); ?>" />
              </div>
              <!--end::Avatar-->
              <!--begin::Username-->
              <div class="d-flex flex-column">
                <div class="fw-bolder d-flex align-items-center fs-5"><?php echo @$userData->userB->u_name . ' ' . $userData->userB->u_lastname . ' ' . $userData->userB->u_surname; ?>
                  <span class="badge badge-light-<?php echo @$userData->userB->ug_color; ?> fw-bolder fs-8 px-2 py-1 ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $userData->userB->ug_name; ?>"><?php echo $userData->userB->ug_code; ?></span>
                </div>
                <span class="fw-bold text-muted text-hover-primary fs-7"><?php echo @$userData->userB->us_name; ?></span>
              </div>
              <!--end::Username-->
            </div>
          </div>
          <!--end::Menu item-->
          <!--begin::Menu separator-->
          <div class="separator my-2"></div>
          <!--end::Menu separator-->
          <!--begin::Menu item-->
          <div class="menu-item px-5">
            <a href="<?php echo base_url('auth/repassword_form'); ?>" class="menu-link px-5">Şifre Değiştir</a>
          </div>
          <!--end::Menu item-->
          <?php if (isDbAsidePermissions("dashboard")) { ?>
            <!--begin::Menu item-->
            <div class="menu-item px-5 my-1">
              <a href="<?php echo base_url('dashboard'); ?>" class="menu-link px-5">Sistem Yönetimi</a>
            </div>
            <!--end::Menu item-->
          <?php } ?>
          <!--begin::Menu separator-->
          <div class="separator my-2"></div>
          <!--end::Menu separator-->
          <!--begin::Menu item-->
          <div class="menu-item px-5 my-1">
            <a href="https://ekurum.hipporello.net/desk" target=_blank class="menu-link px-5">Destek Bildirimi</a>
          </div>
          <!--end::Menu item-->
          <!--begin::Menu separator-->
          <div class="separator my-2"></div>
          <!--end::Menu separator-->
          <!--begin::Menu item-->
          <div class="menu-item px-5">
            <a href="<?php echo base_url('logout'); ?>" class="menu-link px-5">Çıkış Yap</a>
          </div>
          <!--end::Menu item-->
        </div>
        <!--end::User account menu-->
        <!--end::Menu wrapper-->
      </div>
      <!--end::User menu-->

    </div>
    <!--end::Toolbar wrapper-->

  </div>
  <!--end::Header-->
  <!--begin::Body-->
  <div class="mb-5 mb-lg-8" id="kt_sidebar_body">
    <!--begin::Scroll-->
    <div class="hover-scroll-y me-n6 pe-6" id="kt_sidebar_body" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_sidebar_header, #kt_sidebar_footer" data-kt-scroll-wrappers="#kt_page, #kt_sidebar, #kt_sidebar_body" data-kt-scroll-offset="0">
      <!--begin::Timeline items-->
      <div class="timeline">
        <!--begin::Timeline item-->
    <?php 
    
    foreach ($duyuruList?$duyuruList:array() as $kDuyuru=>$vDuyuru) { 
      $vDuyuru=(array)$vDuyuru;
      ?>
        <!--begin::Timeline item-->
        <div class="timeline-item">
          <!--begin::Timeline line-->
          <div class="timeline-line w-40px"></div>
          <!--end::Timeline line-->
          <!--begin::Timeline icon-->
          <div class="timeline-icon symbol symbol-circle symbol-40px">
            <div class="symbol-label">
              <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
              <span class="svg-icon svg-icon-2 svg-icon-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-megaphone" viewBox="0 0 16 16">
                  <path d="M13 2.5a1.5 1.5 0 0 1 3 0v11a1.5 1.5 0 0 1-3 0v-.214c-2.162-1.241-4.49-1.843-6.912-2.083l.405 2.712A1 1 0 0 1 5.51 15.1h-.548a1 1 0 0 1-.916-.599l-1.85-3.49a68.14 68.14 0 0 0-.202-.003A2.014 2.014 0 0 1 0 9V7a2.02 2.02 0 0 1 1.992-2.013 74.663 74.663 0 0 0 2.483-.075c3.043-.154 6.148-.849 8.525-2.199V2.5zm1 0v11a.5.5 0 0 0 1 0v-11a.5.5 0 0 0-1 0zm-1 1.35c-2.344 1.205-5.209 1.842-8 2.033v4.233c.18.01.359.022.537.036 2.568.189 5.093.744 7.463 1.993V3.85zm-9 6.215v-4.13a95.09 95.09 0 0 1-1.992.052A1.02 1.02 0 0 0 1 7v2c0 .55.448 1.002 1.006 1.009A60.49 60.49 0 0 1 4 10.065zm-.657.975 1.609 3.037.01.024h.548l-.002-.014-.443-2.966a68.019 68.019 0 0 0-1.722-.082z" />
                </svg>
              </span>
              <!--end::Svg Icon-->
            </div>
          </div>

          <div class="timeline-content mb-10 mt-n1" style="min-width:300px">
            <!--begin::Timeline heading-->
            <div class="mb-5 pe-3">
              <!--begin::Title-->
              
              <a href="#1" onclick="$('.detail_duyuru_<?=$vDuyuru['us_id'];?>').toggle();" class="fs-5 fw-bold text-white  mb-2"><?=$vDuyuru["us_name"];?></a>
              <!--end::Title-->
              <!--begin::Description-->
              <div class="d-flex align-items-center mt-1 fs-6">
                <!--begin::Info-->
                <div class="text-white opacity-50 me-2 fs-7"><?=date("d.m.Y H:i",$vDuyuru["us_adddate"]);?> </div>
                <!--end::Info-->
                <!--begin::User-->
                <a href="#1" onclick="$('.detail_duyuru_<?=$vDuyuru['us_id'];?>').toggle();" class="text-success fs-7 fw-bolder"><?=$vDuyuru["us_code"];?></a>
                <!--end::User-->
              </div>
              <!--end::Description-->
            </div>

            <div class="pb-5">
              <!--begin::Item-->
              <div class="d-flex flex-stack border rounded p-4">
                <!--begin::Wrapper-->
                <div class="d-flex align-items-center me-2">
                  <!--begin::Icon-->
                  <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                  <span class="svg-icon svg-icon-2x svg-icon-white me-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-body-text" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M0 .5A.5.5 0 0 1 .5 0h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 0 .5Zm0 2A.5.5 0 0 1 .5 2h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5Zm9 0a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5Zm-9 2A.5.5 0 0 1 .5 4h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5Zm5 0a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5Zm7 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5Zm-12 2A.5.5 0 0 1 .5 6h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5Zm8 0a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5Zm-8 2A.5.5 0 0 1 .5 8h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5Zm7 0a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5Zm-7 2a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8a.5.5 0 0 1-.5-.5Zm0 2a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5Zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5Z" />
                    </svg>

                  </span>
                  <!--end::Svg Icon-->
                  <!--end::Icon-->
                  <!--begin::Info-->
                  <div class="d-flex flex-stack">
                    <!--begin::Info-->
                    <div class="d-flex flex-column me-2">
                      <!--begin::Desc-->
                      <a href="#1" onclick="$('.detail_duyuru_<?=$vDuyuru['us_id'];?>').toggle();" class="fs-7 text-white  fw-bolder">Detaylı Rapor</a>
                      <!--end::Desc-->
                      <!--begin::Number-->
                      <div class="text-gray-400">Versiyon 1.0.0</div>
                      <!--end::Number-->
                    </div>
                    <!--end::Info-->
                  </div>
                  <!--begin::Info-->
                </div>
                <!--end::Wrapper-->
                <!--begin::Action-->
                <a href="#1" onclick="$('.detail_duyuru_<?=$vDuyuru['us_id'];?>').toggle();" class="btn btn-sm  text-white bg-white bg-opacity-10">Göster</a>
                <!--end::Action-->
              </div>

              <div class="hidden text-white mt-3 detail_duyuru_<?=$vDuyuru["us_id"];?>">

              <?=$vDuyuru["us_description"];?>
                
              </div>
              
              <!--end::Item-->
            </div>
            <!--end::Timeline details-->
          </div>
          <!--end::Timeline content-->
        </div>
        
        <?php } ?>

        <!--end::Timeline item-->
      </div>
      <!--end::Timeline items-->
    </div>
    <!--end::Scroll-->
  </div>
  <!--end::Body-->
  <!--begin::Footer-->
  <div class="text-center" id="kt_sidebar_footer">
    <!--begin::Link-->
    <a href="https://ekurum.hipporello.net/desk" target=_blank class="btn  text-white bg-white bg-opacity-10 text-uppercase fs-7 fw-bolder">Destek</a>
    <!--end::Link-->
  </div>
  <!--end::Footer-->
</div>
