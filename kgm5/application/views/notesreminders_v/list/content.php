<?php $this->load->view("includes/alert"); ?>
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 mb-1">
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
                    <a href="<?php echo base_url('dashboard'); ?>" class="text-gray-700 text-hover-primary me-1">
                        <i class="fonticon-home text-gray-700 fs-3"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <span class="svg-icon svg-icon-4 svg-icon-gray-700 mx-n1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6343 12.5657L8.45001 16.75C8.0358 17.1642 8.0358 17.8358 8.45001 18.25C8.86423 18.6642 9.5358 18.6642 9.95001 18.25L15.4929 12.7071C15.8834 12.3166 15.8834 11.6834 15.4929 11.2929L9.95001 5.75C9.5358 5.33579 8.86423 5.33579 8.45001 5.75C8.0358 6.16421 8.0358 6.83579 8.45001 7.25L12.6343 11.4343C12.9467 11.7467 12.9467 12.2533 12.6343 12.5657Z" fill="currentColor" />
                        </svg>
                    </span>
                </li>
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">Not Defteri</li>
            </ul>
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">EDTS | Not Defteri / Hatırlatmalar</h1>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <?php if (isAllowedViewApp("edts")) { ?>
                <button type="button" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_note" onclick="NotesRemindersModal.openAdd();">
                    <i class="bi bi-plus-lg me-1"></i>Yeni Not
                </button>
            <?php } ?>
        </div>
    </div>
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Notlarım</span>
                        </h3>
                    </div>
                    <div class="card-body py-4">
                        <?php if (empty($notes)) { ?>
                            <div class="text-center text-gray-600 py-10">
                                <i class="bi bi-journal-text fs-1"></i>
                                <p class="mb-0 mt-2">Henüz not eklenmemiş.</p>
                                <?php if (isAllowedViewApp("edts")) { ?>
                                    <button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#kt_modal_note" onclick="NotesRemindersModal.openAdd();">İlk notu ekle</button>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="table-responsive">
                                <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted">
                                            <th class="min-w-150px">Başlık</th>
                                            <th class="min-w-120px">Etiket</th>
                                            <th class="min-w-200px">İçerik</th>
                                            <th class="min-w-120px">Hatırlatma</th>
                                            <th class="min-w-100px text-end">İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $notesData = array();
                                        foreach ($notes as $note) {
                                            $reminder = $note->n_reminder_at ? date('d.m.Y H:i', strtotime($note->n_reminder_at)) : '-';
                                            $contentPreview = $note->n_content ? mb_substr(strip_tags($note->n_content), 0, 80) . (mb_strlen(strip_tags($note->n_content)) > 80 ? '...' : '') : '';
                                            $notesData[$note->n_id] = array(
                                                'title' => $note->n_title,
                                                'content' => $note->n_content,
                                                'tag' => $note->n_tag ?? '',
                                                'reminder_at' => $note->n_reminder_at ? date('Y-m-d\TH:i', strtotime($note->n_reminder_at)) : ''
                                            );
                                        ?>
                                        <tr>
                                            <td><span class="text-dark fw-bold"><?php echo htmlspecialchars($note->n_title); ?></span></td>
                                            <td><?php echo getNoteTagBadge($note->n_tag ?? ''); ?></td>
                                            <td><span class="text-gray-700"><?php echo htmlspecialchars($contentPreview); ?></span></td>
                                            <td><span class="text-gray-600"><?php echo $reminder; ?></span></td>
                                            <td class="text-end">
                                                <?php if (isAllowedViewApp("edts")) { ?>
                                                    <button type="button" class="btn btn-icon btn-light-primary btn-sm me-1 btn-note-edit" data-nid="<?php echo (int)$note->n_id; ?>" title="Düzenle">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                <?php } ?>
                                                <?php if (isAllowedViewApp("edts")) { ?>
                                                    <a href="<?php echo base_url('notesreminders/delete/' . (int)$note->n_id); ?>" class="btn btn-icon btn-light-danger btn-sm" title="Sil" onclick="return confirm('Bu notu silmek istediğinize emin misiniz?');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <script>var notesData = <?php echo json_encode(isset($notesData) ? $notesData : array()); ?>;</script>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Content-->
