<?php
$t = &get_instance();
$dueReminders = array();
$t->load->model('auth_model');
$userData = isset($t->auth_model->userData) ? $t->auth_model->userData : null;
$userId = ($userData && isset($userData->userB->u_id)) ? (int) $userData->userB->u_id : 0;
if ($userId && function_exists('isAllowedViewApp') && isAllowedViewApp("edts")) {
    try {
        if (!function_exists('getNoteTagBadge')) {
            $t->load->helper('notes');
        }
        $t->load->model('notes_model');
        $dueReminders = $t->notes_model->getDueReminders($userId);
        $dueReminders = $dueReminders ?: array();
    } catch (Exception $e) {
        $dueReminders = array();
    } catch (Throwable $e) {
        $dueReminders = array();
    }
}
?>
<?php if (!empty($dueReminders)) { ?>
<!--begin::Modal - Hatırlatma-->
<div class="modal fade" id="kt_modal_note_reminder" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h2 class="fw-bolder d-flex align-items-center">
                    <i class="bi bi-bell-fill text-warning fs-1 me-2"></i>Hatırlatma
                </h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" id="kt_modal_note_reminder_btn_close">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 pt-0">
                <div id="kt_modal_note_reminder_content">
                    <?php $first = $dueReminders[0]; ?>
                    <input type="hidden" id="note_reminder_n_id" value="<?php echo (int) $first->n_id; ?>" />
                    <div class="fw-bold fs-4 text-dark mb-2"><?php echo htmlspecialchars($first->n_title); ?></div>
                    <?php if (!empty($first->n_tag)) { ?>
                    <div class="mb-3"><?php echo getNoteTagBadge($first->n_tag); ?></div>
                    <?php } ?>
                    <?php if (!empty($first->n_content)) { ?>
                    <div class="text-gray-700"><?php echo nl2br(htmlspecialchars($first->n_content)); ?></div>
                    <?php } ?>
                    <div class="text-gray-500 mt-2">
                        <i class="bi bi-clock me-1"></i>Hatırlatma: <?php echo $first->n_reminder_at ? date('d.m.Y H:i', strtotime($first->n_reminder_at)) : ''; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-center border-0 pt-0">
                <button type="button" class="btn btn-light me-3" id="kt_modal_note_reminder_btn_dismiss">Kapat</button>
                <button type="button" class="btn btn-primary" id="kt_modal_note_reminder_btn_snooze">
                    <i class="bi bi-bell-slash me-1"></i>Tekrar Hatırlatma
                </button>
            </div>
        </div>
    </div>
</div>
<script>
(function() {
    var dueReminders = <?php
$arr = array();
foreach ($dueReminders as $r) {
    $arr[] = array(
        'id' => (int)$r->n_id,
        'title' => $r->n_title,
        'content' => $r->n_content,
        'tag' => $r->n_tag ?? '',
        'reminder_at' => $r->n_reminder_at ? date('d.m.Y H:i', strtotime($r->n_reminder_at)) : ''
    );
}
echo json_encode($arr);
?>;
    var dismissUrl = '<?php echo base_url("notesreminders/dismiss_reminder"); ?>';
    var currentIndex = 0;

    function showReminderModal() {
        if (typeof bootstrap === 'undefined' || !dueReminders.length) return;
        var el = document.getElementById('kt_modal_note_reminder');
        if (!el) return;
        var modal = new bootstrap.Modal(el);
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (!dueReminders.length) return;

        var btnDismiss = document.getElementById('kt_modal_note_reminder_btn_dismiss');
        var btnSnooze = document.getElementById('kt_modal_note_reminder_btn_snooze');
        var btnClose = document.getElementById('kt_modal_note_reminder_btn_close');
        var nidInput = document.getElementById('note_reminder_n_id');

        if (btnDismiss) {
            btnDismiss.addEventListener('click', function() {
                var modalEl = document.getElementById('kt_modal_note_reminder');
                if (modalEl) {
                    bootstrap.Modal.getInstance(modalEl).hide();
                }
            });
        }

        if (btnClose) {
            btnClose.addEventListener('click', function() {
                var modalEl = document.getElementById('kt_modal_note_reminder');
                if (modalEl) {
                    bootstrap.Modal.getInstance(modalEl).hide();
                }
            });
        }

        if (btnSnooze && nidInput) {
            btnSnooze.addEventListener('click', function() {
                var nid = parseInt(nidInput.value, 10);
                if (!nid) return;
                btnSnooze.disabled = true;
                var formData = new FormData();
                formData.append('n_id', nid);
                formData.append('<?php echo $t->security->get_csrf_token_name(); ?>', '<?php echo $t->security->get_csrf_hash(); ?>');
                fetch(dismissUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var modalEl = document.getElementById('kt_modal_note_reminder');
                    if (modalEl) {
                        bootstrap.Modal.getInstance(modalEl).hide();
                    }
                })
                .catch(function() { btnSnooze.disabled = false; });
            });
        }

        showReminderModal();
    });
})();
</script>
<!--end::Modal-->
<?php } ?>
