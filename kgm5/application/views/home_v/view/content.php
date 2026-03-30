<!--begin::Main-->

<!--begin::Root-->
<div class="d-flex flex-column flex-root">
  <!--begin::Page-->
  <div class="page launcher sidebar-enabled d-flex flex-row flex-column-fluid me-lg-5" id="kt_page">
    <!--begin::Content-->
    <div class="d-flex flex-row-fluid">
      <!--begin::Container-->
      <div class="d-flex flex-column flex-row-fluid align-items-center">
        <!--begin::Menu-->
        <div class="d-flex flex-column flex-column-fluid mb-5 mb-lg-10">
          <!--begin::Brand-->
          <div class="d-flex flex-center pt-10 pt-lg-0 mb-10 mb-lg-0 h-lg-175px">
            <!--begin::Sidebar toggle-->
            <div class="btn btn-icon btn-active-color-primary w-30px h-30px d-lg-none me-4 ms-n15" id="kt_sidebar_toggle">
              <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
              <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="currentColor" />
                  <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="currentColor" />
                </svg>
              </span>
              <!--end::Svg Icon-->
            </div>
            <!--end::Sidebar toggle-->
            <!--begin::Logo-->
            <a href="<?php echo base_url(); ?>">
              <img alt="Logo" src="<?php echo base_url('assets/media/logos/logo-3.svg'); ?>" class="h-75px h-lg-80px" />
            </a>
            <!--end::Logo-->
          </div>
          <!--end::Brand-->
          <!--begin::Row-->
          <div class="row g-7 w-xxl-850px">
            <!--begin::Col-->
            <div class="col-xxl-12">
              <!--begin::Row-->
              <div class="row g-lg-7">
                <?php
                $sayac = 0;
                if ($appList) {
                  foreach ($appList as $app) {
                    if (isAllowedViewApp($app->a_appcode)) {
                      $sayac++;
                      $appJson = json_decode($app->a_json);
                ?>
                      <!--begin::Col-HEKSBUTON-->
                      <div class="col-sm-6">
                        <!--begin::Card-->
                        <a href="<?php echo base_url("apps/{$app->a_appcode}/"); ?>" rel="noreferrer noopener" class="card border-0 shadow-none min-h-200px mb-7" style="background-color: <?php echo @$appJson->color; ?>">
                          <!--begin::Card body-->
                          <div class="card-body d-flex flex-column flex-center text-center">
                            <!--begin::Illustrations-->
                            <img class="mw-100 h-100px mb-7 mx-auto" src="<?php echo base_url('apps/' . $app->a_appcode . '/' . $appJson->image); ?>" />
                            <!--end::Illustrations-->
                            <!--begin::Heading-->
                            <h4 class="text-white fw-bolder text-uppercase"><?php echo $appJson->menutext; ?></h4>
                            <h4 class="text-white fw-bolder text-uppercase">[<?php echo strtoupper($app->a_shortcode); ?>]</h4>
                            <!--end::Heading-->
                          </div>
                          <!--end::Card body-->
                        </a>
                        <!--end::Card-->
                      </div>
                      <!--end::Col-HEKSBUTON-->
                    <?php
                    } ## IF END
                  } ##FOREACH END

                  if ($sayac == 0) {

                    ?>
                    <!--begin::Col-HEKSBUTON-->
                    <div class="col-sm-12">

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
                          <span>Kullanımınıza Açık Olan Herhangi Bir Uygulama Bulunamadı.</span>
                          <span>Lütfen Sistem Yöneticinize Başvurunuz.</span>
                        </div>
                      </div>


                    </div>
                    <!--end::Col-HEKSBUTON-->
                  <?php


                  }
                } else {

                  ?>
                  <!--begin::Col-HEKSBUTON-->
                  <div class="col-sm-12">

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
                        <span>Kullanımınıza Açık Olan Herhangi Bir Uygulama Bulunamadı.</span>
                        <span>Lütfen Sistem Yöneticinize Başvurunuz.</span>
                      </div>
                    </div>


                  </div>
                  <!--end::Col-HEKSBUTON-->
                <?php


                }
                ?>

              </div>
              <!--end::Row-->
            </div>
            <!--end::Col-->
          </div>
          <!--end::Row-->
        </div>
        <!--end::Menu-->
        <!--begin::Footer-->
        <?php $this->load->view("{$viewFolder}/{$subViewFolder}/footer"); ?>
        <!--end::Footer-->
      </div>
      <!--begin::Content-->
    </div>
    <!--begin::Content-->
    <!--begin::Sidebar-->
    <?php $this->load->view("{$viewFolder}/{$subViewFolder}/sidebar"); ?>
    <!--end::Sidebar-->
  </div>
  <!--end::Page-->
</div>
<!--end::Root-->
<!--end::Main-->