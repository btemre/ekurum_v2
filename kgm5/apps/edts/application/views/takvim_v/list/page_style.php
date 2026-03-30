<link href="<?php echo base_url('assets/css/custom.css'); ?>?v=<?php echo asset_ver(); ?>" rel="stylesheet" type="text/css" />
<style>
#takvim_calendar { min-height: 500px; }
#takvim_calendar .fc-toolbar-title { font-size: 1.25rem; }
#takvim_calendar .fc-event { cursor: pointer; font-size: 0.75rem; padding: 4px 6px; line-height: 1.3; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; border-radius: 4px; }
#takvim_calendar .fc-event .fc-event-main { overflow: hidden; text-overflow: ellipsis; }
/* Haftalık görünüm: gün sütunları, liste tarzı etkinlikler */
#takvim_calendar .fc-daygrid-day-frame { min-height: 80px; }
#takvim_calendar .fc-daygrid-day-events { margin-top: 4px; }
#takvim_calendar .fc-daygrid-event { margin-bottom: 4px; }
#takvim_calendar .fc-daygrid-more-link { font-size: 0.75rem; }
</style>
