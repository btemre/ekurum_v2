<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof TakvimCalendar !== 'undefined') {
        TakvimCalendar.init({
            baseUrl: '<?php echo base_url(); ?>',
            eventsUrl: '<?php echo base_url('takvim/events'); ?>',
            eventDetailUrl: '<?php echo base_url('takvim/eventDetail'); ?>',
            densityUrl: '<?php echo base_url('takvim/density'); ?>',
            prefsUrl: '<?php echo base_url('takvim/getNotificationPrefs'); ?>',
            savePrefsUrl: '<?php echo base_url('takvim/saveNotificationPrefs'); ?>',
            feedTokenUrl: '<?php echo base_url('takvim/getFeedToken'); ?>',
            canWrite: <?php echo !empty($canWrite) ? 'true' : 'false'; ?>,
            updateBaseUrl: '<?php echo base_url('durusmalar/update'); ?>'
        });
    }
});
</script>
