<!--begin::Main-->
<!--begin::Root-->
<div class="d-flex flex-column flex-root">
  <!--begin::Authentication - Sign-in -->
  <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(<?php echo base_url('assets/media/illustrations/sketchy-1/14--dark.png');?>">
    <!--begin::Content-->
    <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
      <!--begin::Logo-->
      <a href="<?php echo base_url();?>" class="mb-12">
        <img alt="Logo" src="<?php echo base_url('assets/media/logos/logo-2.svg');?>" class="h-40px" />
      </a>
      <!--end::Logo-->
      <!--begin::Wrapper-->
      <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
        <!--begin::Form-->
        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" action="<?php echo base_url('auth/do_login');?>" method="POST" data-kt-redirect-url="<?php echo base_url();?>" action="#">
          <!--begin::Heading-->
          <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark mb-3">eKurum Dünyasına Giriş Yapın.</h1>
            <!--end::Title-->
          </div>
          <!--begin::Heading-->
          <!--begin::Input group-->
          <div class="fv-row mb-10">
            <!--begin::Label-->
            <label class="form-label fs-6 fw-bolder text-dark">Kullanıcı Adı</label>
            <!--end::Label-->
            <!--begin::Input-->
            <input class="form-control form-control-lg form-control-solid" type="text" name="username" id="username" autocomplete="off" />
            <?php if(isset($form_error)){ ?>
              <div class="fv-plugins-message-container invalid-feedback">
                <div data-field="username"><?php echo @form_error("username"); ?></div>
              </div>
            <?php } ?>
            <!--end::Input-->
          </div>
          <!--end::Input group-->
          <!--begin::Input group-->
          <div class="fv-row mb-10">
            <!--begin::Wrapper-->
            <div class="d-flex flex-stack mb-2">
              <!--begin::Label-->
              <label class="form-label fw-bolder text-dark fs-6 mb-0">Parola</label>
              <!--end::Label-->
            </div>
            <!--end::Wrapper-->
            <!--begin::Input-->
            <input class="form-control form-control-lg form-control-solid" type="password" name="password" id="password" autocomplete="off" />
            <?php if(isset($form_error)){ ?>
              <div class="fv-plugins-message-container invalid-feedback">
                <div data-field="password"><?php echo @form_error("password"); ?></div>
              </div>
            <?php } ?>
            <!--end::Input-->
          </div>
          <!--end::Input group-->
          <!--begin::Actions-->
          <div class="text-center">
            <!--begin::Submit button-->
            <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
              <span class="indicator-label">Devam Et</span>
              <span class="indicator-progress">Lütfen Bekleyin...
              <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
            <!--end::Submit button-->
          </div>
          <!--end::Actions-->
        </form>
        <!--end::Form-->
      </div>
      <!--end::Wrapper-->
      <!--begin::Links-->
      <div class="d-flex align-items-center fw-bold fs-6 p-10">
        <a href="#" class="text-muted text-hover-primary px-2">Parolamı Unuttum</a>
      </div>
      <!--end::Links-->

    </div>
    <!--end::Content-->
    <!--begin::Footer-->
    <div class="d-flex flex-center flex-column-auto p-10">
      <!--begin::Links-->
      <div class="d-flex align-items-center fw-bold fs-6">
        <a href="#" class="text-muted text-hover-primary px-2">Hakkımızda</a>
        <a href="mailto:info@ekurum.com" class="text-muted text-hover-primary px-2">İletişim</a>
        <a href="#" class="text-muted text-hover-primary px-2">Bize Ulaşın</a>
      </div>
      <!--end::Links-->
    </div>
    <!--end::Footer-->
  </div>
  <!--end::Authentication - Sign-in-->
</div>
<!--end::Root-->
<!--end::Main-->
