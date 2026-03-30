<!DOCTYPE html>
<html lang="tr">
  <head>
    <base href="<?php echo base_url();?>">
    <title>Lisans süresi sona erdi | eKurum.com</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="<?php echo base_url('assets/media/logos/favicon.ico'); ?>" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="<?php echo base_url('assets/plugins/global/plugins.bundle.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/style.bundle.css'); ?>" rel="stylesheet" type="text/css" />
  </head>
  <body id="kt_body" class="bgi-size-cover bgi-position-center bgi-no-repeat" background="<?php echo base_url('assets/media/patterns/bg8.jpg'); ?>">
    <?php $this->load->view("{$viewFolder}/{$subViewFolder}/content"); ?>
    <?php $this->load->view("{$viewFolder}/{$subViewFolder}/page_script"); ?>
  </body>
</html>
