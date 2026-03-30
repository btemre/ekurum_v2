<script>
(function() {
    if (typeof notesData === 'undefined') window.notesData = {};

    var NotesRemindersModal = {
        form: null,
        addAction: '<?php echo base_url("notesreminders/save"); ?>',
        updateAction: '<?php echo base_url("notesreminders/update"); ?>',

        openAdd: function() {
            document.getElementById('kt_modal_note_title').textContent = 'Yeni Not';
            document.getElementById('note_n_id').value = '';
            document.getElementById('note_n_title').value = '';
            document.getElementById('note_n_content').value = '';
            document.getElementById('note_n_tag').value = '';
            document.getElementById('note_n_reminder_at').value = '';
            document.getElementById('kt_modal_note_form').action = this.addAction;
            document.getElementById('kt_modal_note_form').method = 'post';
        },

        openEdit: function(id) {
            var data = notesData[id];
            if (!data) return;
            document.getElementById('kt_modal_note_title').textContent = 'Notu Düzenle';
            document.getElementById('note_n_id').value = id;
            document.getElementById('note_n_title').value = data.title || '';
            document.getElementById('note_n_content').value = data.content || '';
            document.getElementById('note_n_tag').value = data.tag || '';
            document.getElementById('note_n_reminder_at').value = data.reminder_at || '';
            document.getElementById('kt_modal_note_form').action = this.updateAction;
            document.getElementById('kt_modal_note_form').method = 'post';
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('kt_modal_note_form');
        if (form) {
            form.addEventListener('submit', function() {
                var idEl = document.getElementById('note_n_id');
                if (idEl && idEl.value) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'n_id';
                    input.value = idEl.value;
                    form.appendChild(input);
                }
            });
        }

        document.querySelectorAll('.btn-note-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var nid = parseInt(this.getAttribute('data-nid'), 10);
                if (notesData[nid]) {
                    NotesRemindersModal.openEdit(nid);
                    var modal = new bootstrap.Modal(document.getElementById('kt_modal_note'));
                    modal.show();
                }
            });
        });
    });

    window.NotesRemindersModal = NotesRemindersModal;
})();
</script>
