<!DOCTYPE html>
<html lang="tr" data-theme="light">
<!--begin::Head-->

<head>
  <base href="<?php echo base_url(); ?>" />
  <title>eKurum | Şifre Belirleme</title>
  <meta charset="utf-8" />
  <meta name="description" content="Kurum içi ihiyaca yönelik geliştirilebilen modüler yapıya sahip iş akışınızı tek noktadan yönetebleceğiniz yönetim paneli" />
  <meta name="keywords" content="Kgm, ekurum, senkronize panel, yonetim paneli, hukuk yonetim sistemi, heks, hukuk evrak kayıt sistemi" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta property="og:locale" content="tr_TR" />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="Kgm, ekurum, senkronize panel, yonetim paneli, hukuk yonetim sistemi, heks, hukuk evrak kayıt sistemi" />
  <meta property="og:url" content="<?php echo base_url(); ?>" />
  <meta property="og:site_name" content="eKurum | Şifre Belirleme" />


  <link rel="shortcut icon" href="<?php echo base_url('assets/media/logos/favicon.ico'); ?>" />
  <!--begin::Fonts(mandatory for all pages)-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
  <!--end::Fonts-->
  <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
  <link href="<?php echo base_url('assets/plugins/global/plugins.bundle.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url('assets/css/style.bundle.css'); ?>" rel="stylesheet" type="text/css" />
  <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="app-blank bgi-size-cover bgi-position-center bgi-no-repeat">
  <!--begin::Theme mode setup on page load-->
  <script>
    var defaultThemeMode = "light";
    var themeMode;
    if (document.documentElement) {
      if (document.documentElement.hasAttribute("data-theme-mode")) {
        themeMode = document.documentElement.getAttribute("data-theme-mode");
        console.log("themeMode", themeMode);
      } else {
        if (localStorage.getItem("data-theme") !== null) {
          themeMode = localStorage.getItem("data-theme");
        } else {
          themeMode = defaultThemeMode;
        }
      }
      if (themeMode === "system") {
        themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
      }
      document.documentElement.setAttribute("data-theme", themeMode);
    }
  </script>
  <!--end::Theme mode setup on page load-->
  <!--begin::Main-->
  <!--begin::Root-->
  <div class="d-flex flex-column flex-root">
    <!--begin::Page bg image-->
    <style>
      body {
        background-image: url('assets/media/auth/bg10.jpeg');
      }

      [data-theme="dark"] body {
        background-image: url('assets/media/auth/bg10-dark.jpeg');
      }
    </style>
    <!--end::Page bg image-->
    <?php $this->load->view("{$viewFolder}/{$subViewFolder}/content"); ?>
  </div>
  <!--end::Root-->
  <!--end::Main-->
  <?php $this->load->view("{$viewFolder}/{$subViewFolder}/page_script"); ?>
</body>
<!--end::Body-->

</html>