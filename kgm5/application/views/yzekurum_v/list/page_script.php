<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof YzEkurumChat !== 'undefined') {
        YzEkurumChat.init({
            baseUrl: '<?php echo base_url(); ?>',
            endpoints: {
                sessions: 'yzekurum/api_sessions',
                sessionMessages: 'yzekurum/api_session_messages',
                chat: 'yzekurum/api_chat',
                quota: 'yzekurum/api_quota'
            }
        });
    }
});
</script>
