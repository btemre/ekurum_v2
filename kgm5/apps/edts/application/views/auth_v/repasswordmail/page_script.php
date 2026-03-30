<!--begin::Javascript-->
<script>
    var hostUrl = "<?php echo base_url('assets/'); ?>";
</script>
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="<?php echo base_url('assets/plugins/global/plugins.bundle.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/scripts.bundle.js'); ?>"></script>
<!--end::Global Javascript Bundle-->

<!--begin::Page Custom Javascript(used by this page)-->
<script src="<?php echo base_url('assets/js/moduls/auth/repassword_form.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/moduls/auth/repassword_formmail.js'); ?>"></script>
<!--end::Page Custom Javascript-->
<!--begin::Page Popup Alert-->
<?php $this->load->view("includes/alert"); ?>
<!--end::Page Popup Alert-->
<!--end::Javascript-->