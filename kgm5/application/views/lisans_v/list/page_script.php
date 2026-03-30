<?php $this->load->view("includes/alert"); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-action="renew"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var ubId = this.getAttribute('data-ub-id');
            var period = this.getAttribute('data-period') || 'yearly';
            if (!ubId) return;
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo base_url("lisans/renew"); ?>';
            var i1 = document.createElement('input'); i1.name = 'ub_id'; i1.value = ubId; i1.type = 'hidden'; form.appendChild(i1);
            var i2 = document.createElement('input'); i2.name = 'period'; i2.value = period; i2.type = 'hidden'; form.appendChild(i2);
            document.body.appendChild(form);
            form.submit();
        });
    });
    document.querySelectorAll('[data-action="set-status"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var ubId = this.getAttribute('data-ub-id');
            var status = this.getAttribute('data-status');
            if (!ubId || !status) return;
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo base_url("lisans/set_status"); ?>';
            var i1 = document.createElement('input'); i1.name = 'ub_id'; i1.value = ubId; i1.type = 'hidden'; form.appendChild(i1);
            var i2 = document.createElement('input'); i2.name = 'status'; i2.value = status; i2.type = 'hidden'; form.appendChild(i2);
            document.body.appendChild(form);
            form.submit();
        });
    });
});
</script>
