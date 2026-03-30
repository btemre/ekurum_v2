<!--begin::Page Popup Alert-->
<?php $this->load->view("includes/alert"); ?>
<!--end::Page Popup Alert-->
<!--begin::AI Modal-->
<?php $this->load->view("ai_v/ai_modal"); ?>
<!--end::AI Modal-->
<!--begin::Page Custom Javascript(used by this page)-->
<script src="<?php echo base_url('assets/js/moduls/dashboard/gelengiden/view.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/moduls/dashboard/cezaiptal/view.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/moduls/dashboard/dosya/view.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/moduls/ai/ai_service.js?tx='.time()); ?>"></script>
<script>AiService.init("<?php echo base_url(); ?>");</script>
<!--end::Page Custom Javascript-->
