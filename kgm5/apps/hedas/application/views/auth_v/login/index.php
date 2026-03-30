<!DOCTYPE html>
<html lang="tr">
  <head>
    <base href="<?php echo base_url();?>">
    <title>eKurum.com | Senkronize Kurum Yönetim Sistemi</title>
    <meta charset="utf-8" />
    <meta name="description" content="Kurum içi ihiyaca yönelik geliştirilebilen modüler yapıya sahip iş akışınızı tek noktadan yönetebleceğiniz yönetim paneli" />
    <meta name="keywords" content="Kgm, ekurum, senkronize panel, yonetim paneli, hukuk yonetim sistemi, heks, hukuk evrak kayıt sistemi" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="tr_TR" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Kgm, ekurum, senkronize panel, yonetim paneli, hukuk yonetim sistemi, heks, hukuk evrak kayıt sistemi" />
    <meta property="og:url" content="<?php echo base_url(); ?>" />
    <meta property="og:site_name" content="eKurum | Senkronize Kurum Yönetim Sistemi" />
    <link rel="canonical" href="<?php echo base_url();?>" />
    <link rel="shortcut icon" href="<?php echo base_url('assets/media/logos/favicon.ico'); ?>" />
    <!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="<?php echo base_url('assets/plugins/global/plugins.dark.bundle.css'); ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url('assets/css/style.dark.bundle.css'); ?>" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
  </head>
  <body id="kt_body" class="bg-body dark-mode">
    <?php $this->load->view("{$viewFolder}/{$subViewFolder}/content"); ?>
    <?php $this->load->view("{$viewFolder}/{$subViewFolder}/page_script"); ?>
  </body>
</html>
