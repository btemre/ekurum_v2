<script src="<?php echo base_url('assets/js/custom/documentation/forms/select2.js'); ?>"></script>
<!--begin::Page Popup Alert-->
<?php $this->load->view("includes/alert"); ?>
<!--end::Page Popup Alert-->
<script type="text/javascript">
// Format options
    var optionFormat = function(item) {
        if ( !item.id ) {
            return item.text;
        }


        var span = document.createElement('span');
            span.classList.add('p-0');
            span.classList.add('m-0');
        var dataText = item.element.getAttribute('data-kt-select2-color');
        var template = '';

        //template += '<img src="' + imgUrl + '" class="rounded-circle h-20px me-2" alt="image"/>';
        template += '<div class="px-3 py-1 fs-8 bg-' + dataText + ' text-inverse-' + dataText + '">' + dataText.toUpperCase() + '</div>';

        template += item.text;

        span.innerHTML = template;

        return $(span);
    }

    // Init Select2 --- more info: https://select2.org/
    $('#kt_docs_select2_color').select2({
        minimumResultsForSearch: Infinity,
        templateSelection: optionFormat,
        templateResult: optionFormat
    });

</script>
