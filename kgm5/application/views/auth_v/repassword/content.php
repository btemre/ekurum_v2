<!--begin::Authentication - New password -->
<div class="d-flex flex-column flex-lg-row flex-column-fluid">
  <!--begin::Aside-->
  <div class="d-flex flex-lg-row-fluid">
    <!--begin::Content-->
    <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
      <!--begin::Image-->
      <img class="theme-light-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="<?php echo base_url('assets/media/auth/agency.png'); ?>" alt="" />
      <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="<?php echo base_url('assets/media/auth/agency-dark.png'); ?>" alt="" />
      <!--end::Image-->
      <!--begin::Title-->
      <h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7">Parolanı Belirle ve Erişim Sağla</h1>
      <!--end::Title-->
      <!--begin::Text-->
      <div class="text-gray-600 fs-base text-center fw-semibold">Burada belirlediğiniz şifre ile tüm
        <a href="#" class="opacity-75-hover text-primary me-1">Ekurum</a> uygulamalarına erişebilirsiniz.
        <br />Yetkili olduğunuz uygulamaları kullanabilirsiniz.
        <a href="#" class="opacity-75-hover text-primary me-1">Ekurum</a>ölçeklenebilir
        <br />çalışma yapısıyla hızlı ve üretken bir sistemdir.
      </div>
      <!--end::Text-->
    </div>
    <!--end::Content-->
  </div>
  <!--begin::Aside-->
  <!--begin::Body-->
  <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
    <!--begin::Wrapper-->
    <div class="bg-body d-flex flex-center rounded-4 w-md-600px p-10">
      <!--begin::Content-->
      <div class="w-md-400px">
        <!--begin::Form-->
        <form method="POST" class="form w-100" novalidate="novalidate" id="kt_new_password_form" data-kt-redirect-url="" action="<?php echo base_url('auth/repassword'); ?>">
          <!--begin::Heading-->
          <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">Güvenli Parola Belirle</h1>
            <!--end::Title-->
            <!--begin::Link-->
            <div class="text-gray-500 fw-semibold fs-6">Şifreyi zaten sıfırladınız mı?
              <a href="<?php echo base_url('logout'); ?>" class="link-primary fw-bold">Çıkış Yap</a> yada
              <a href="<?php echo base_url('home'); ?>" class="link-primary fw-bold">Anasayfaya Git</a>
            </div>
            <!--end::Link-->
          </div>
          <!--begin::Heading-->
          <div class="fv-row mb-8">
            <!--begin::Repeat Password-->
            <input type="password" placeholder="Mevcut Parola" name="oldpassword" id="oldpassword" autocomplete="off" class="form-control bg-transparent" />
            <!--end::Repeat Password-->
            <?php if (isset($form_error)) { ?>
              <div class="fv-plugins-message-container invalid-feedback">
                <div data-field="oldpassword"><?php echo @form_error("oldpassword"); ?></div>
              </div>
            <?php } ?>
          </div>
          <!--begin::Input group-->
          <div class="fv-row mb-8" data-kt-password-meter="false">
            <!--begin::Wrapper-->
            <div class="mb-1">
              <!--begin::Input wrapper-->
              <div class="position-relative mb-3">
                <input class="form-control bg-transparent" type="password" placeholder="Yeni Parola" name="password" id="password" autocomplete="off" />
                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                  <i class="bi bi-eye-slash fs-2"></i>
                  <i class="bi bi-eye fs-2 d-none"></i>
                </span>
              </div>
              <!--end::Input wrapper-->
              <!--begin::Meter-->
              <div class="d-flex align-items-center mb-3 d-none" data-kt-password-meter-control="highlight">
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
              </div>
              <!--end::Meter-->
              <?php if (isset($form_error)) { ?>
                <div class="fv-plugins-message-container invalid-feedback">
                  <div data-field="password"><?php echo @form_error("password"); ?></div>
                </div>
              <?php } ?>
            </div>
            <!--end::Wrapper-->
            <!--begin::Hint-->
            <div class="text-muted">12 Karakter, isterseniz sadece harf kullanabilirsiniz</div>
            <!--end::Hint-->
          </div>
          <!--end::Input group=-->
          <!--end::Input group=-->
          <div class="fv-row mb-8">
            <!--begin::Repeat Password-->
            <input type="password" placeholder="Parola Tekrar" name="repassword" id="repassword" autocomplete="off" class="form-control bg-transparent" />
            <!--end::Repeat Password-->
            <?php if (isset($form_error)) { ?>
              <div class="fv-plugins-message-container invalid-feedback">
                <div data-field="repassword"><?php echo @form_error("repassword"); ?></div>
              </div>
            <?php } ?>
          </div>
          <!--end::Input group=-->
          <!--begin::Input group=-->
          <!-- <div class="fv-row mb-8"> -->
          <!-- <label class="form-check form-check-inline"> -->
          <!-- <input class="form-check-input" type="checkbox" name="toc" value="1" /> -->
          <!-- <span class="form-check-label fw-semibold text-gray-700 fs-6 ms-1">I Agree & -->
          <!-- <a href="#" class="ms-1 link-primary">Terms and conditions</a>.</span> -->
          <!-- </label> -->
          <!-- </div> -->
          <!--end::Input group=-->
          <!--begin::Action-->
          <div class="d-grid mb-10">
            <button type="button" id="kt_new_password_submit" class="btn btn-primary">
              <!--begin::Indicator label-->
              <span class="indicator-label">Onayla</span>
              <!--end::Indicator label-->
              <!--begin::Indicator progress-->
              <span class="indicator-progress">Lütfen Bekle...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
              <!--end::Indicator progress-->
            </button>
          </div>
          <!--end::Action-->
        </form>
        <!--end::Form-->
      </div>
      <!--end::Content-->
    </div>
    <!--end::Wrapper-->
  </div>
  <!--end::Body-->
</div>
<!--end::Authentication - New password-->