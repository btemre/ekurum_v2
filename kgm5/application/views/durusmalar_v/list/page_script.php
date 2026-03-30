<!--begin::Page Popup Alert-->
<?php $this->load->view("includes/alert"); ?>
<!--end::Page Popup Alert-->
<!--begin::AI Modal-->
<?php $this->load->view("ai_v/ai_modal"); ?>
<!--end::AI Modal-->
<!--begin::Page Custom Javascript(used by this page)-->
<script src="<?php echo base_url('assets/js/moduls/durusmalar/list.js?tx='.time()); ?>"></script>
<script src="<?php echo base_url('assets/js/moduls/durusmalar/update.js?tx='.time()); ?>"></script>
<script src="<?php echo base_url('assets/js/moduls/ai/ai_service.js'); ?>?v=<?php echo asset_ver(); ?>"></script>
<script>AiService.init("<?php echo base_url(); ?>");</script>
<script>document.addEventListener("DOMContentLoaded",function(){if(document.getElementById("ai_capacity_forecast_text"))AiService.requestCapacityForecast(false);});</script>

<script>
$('.autoFilters').select2({

closeOnSelect: true,
allowClear: true
});
</script>

<!--end::Page Custom Javascript-->