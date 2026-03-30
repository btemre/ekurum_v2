<?php $av = asset_ver(); ?>
<script>
    var hostUrl = "<?php echo base_url('assets/'); ?>";
</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="<?php echo base_url('assets/plugins/global/plugins.bundle.js'); ?>?v=<?php echo $av; ?>"></script>
<script src="<?php echo base_url('assets/js/scripts.bundle.js'); ?>?v=<?php echo $av; ?>"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script src="<?php echo base_url('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js'); ?>?v=<?php echo $av; ?>"></script>
<script src="<?php echo base_url('assets/plugins/custom/datatables/datatables.bundle.js'); ?>?v=<?php echo $av; ?>"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="<?php echo base_url('assets/js/widgets.bundle.js'); ?>?v=<?php echo $av; ?>"></script>
<script src="<?php echo base_url('assets/js/custom/widgets.js'); ?>?v=<?php echo $av; ?>"></script>
<!--end::Custom Javascript-->

<script src="<?php echo base_url('assets/js/custom/jquery-ui/jquery-ui.min.js'); ?>?v=<?php echo $av; ?>"></script>
<script src="<?php echo base_url('assets/js/custom.js'); ?>?v=<?php echo $av; ?>"></script>

<script src="<?php echo base_url('assets/js/whitelist.js'); ?>?v=<?php echo $av; ?>"></script>
<?php if (function_exists('isAllowedViewApp') && isAllowedViewApp('edts')) { ?>
<script src="<?php echo base_url('assets/js/edts-header-notifications.js'); ?>?v=<?php echo $av; ?>"></script>
<?php } ?>
<?php if (isDbAllowedWriteModule("durusmalar")) { ?>
    <script src="<?php echo base_url('assets/js/moduls/durusmalar/globals/add.js?tx='.time()); ?>"></script>
<?php } ?>
<?php if (isDbAllowedWriteModule("cezakayit")) { ?>
    <script src="<?php echo base_url('assets/js/moduls/cezakayit/globals/add.js?tx='.time()); ?>"></script>
<?php } ?>
<?php if (isDbAllowedWriteModule("cezakayitm")) { ?>
    <script src="<?php echo base_url('assets/js/moduls/cezakayitm/globals/add.js?tx='.time()); ?>"></script>
<?php } ?>