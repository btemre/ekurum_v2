<!--begin::Page Popup Alert-->
<?php $this->load->view("includes/alert"); ?>
<!--end::Page Popup Alert-->
<!--begin::Page Custom Javascript(used by this page)-->
<script src="<?php echo base_url('assets/js/moduls/dosya/list.js?tx='.time()); ?>"></script>
<script src="<?php echo base_url('assets/js/moduls/dosya/update.js?tx='.time()); ?>"></script>

<script type="text/javascript">
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var printHead   = document.head.innerHTML;

        var originalContents = document.body.innerHTML;

        var pencere=window.open();
        pencere.document.head.innerHTML = printHead;
        pencere.document.body.innerHTML=printContents; 
        pencere.print();
        pencere.close();
       // doc.body.innerHTML = originalContents;
    }
</script>
<!--end::Page Custom Javascript-->