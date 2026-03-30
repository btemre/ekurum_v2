<?php
$dNotes = isset($dashboardNotes) ? $dashboardNotes : array('reminders' => array(), 'last_note' => null);
$reminders = isset($dNotes['reminders']) ? $dNotes['reminders'] : array();
$lastNote = isset($dNotes['last_note']) ? $dNotes['last_note'] : null;
$hasAnyNotes = (!empty($reminders) || $lastNote);
$todayCount = isset($dashboardTodayCount) ? (int) $dashboardTodayCount : 0;
$todayByLawyer = isset($dashboardTodayByLawyer) ? $dashboardTodayByLawyer : array();
$todayByCourt = isset($dashboardTodayByCourt) ? $dashboardTodayByCourt : array();
$todayByTaraf = isset($dashboardTodayByTaraf) ? $dashboardTodayByTaraf : array();
$todayByIslem = isset($dashboardTodayByIslem) ? $dashboardTodayByIslem : array();
$todayLabel = date('d.m.Y');
?>
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 mb-1">
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
                    <a href="<?php echo base_url('dashboard'); ?>" class="text-gray-700 text-hover-primary me-1"><i class="fonticon-home text-gray-700 fs-3"></i></a>
                </li>
                <li class="breadcrumb-item"><span class="svg-icon svg-icon-4 svg-icon-gray-700 mx-n1"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.6343 12.5657L8.45001 16.75C8.0358 17.1642 8.0358 17.8358 8.45001 18.25C8.86423 18.6642 9.5358 18.6642 9.95001 18.25L15.4929 12.7071C15.8834 12.3166 15.8834 11.6834 15.4929 11.2929L9.95001 5.75C9.5358 5.33579 8.86423 5.33579 8.45001 5.75C8.0358 6.16421 8.0358 6.83579 8.45001 7.25L12.6343 11.4343C12.9467 11.7467 12.9467 12.2533 12.6343 12.5657Z" fill="currentColor" /></svg></span></li>
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">Anasayfa</li>
            </ul>
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">EDTS | Dashboard</h1>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <button type="button" class="btn btn-sm btn-ai-premium" onclick="typeof AiService !== 'undefined' && AiService.openSearchModal && AiService.openSearchModal()"><i class="bi bi-stars me-1"></i>Yapay Zeka'ya Sor</button>
            <?php if (isDbAllowedWriteModule("durusmalar")) { ?>
                <a href="#" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_durusmalar_manuel">Yeni Duruşma</a>
            <?php } ?>
            <a href="<?php echo base_url('durusmalar'); ?>" class="btn btn-sm fw-bold btn-light-primary">Tüm Duruşmalar</a>
            <a href="<?php echo base_url('durusmalar/durusmalarim'); ?>" class="btn btn-sm fw-bold btn-light">Duruşmalarım</a>
        </div>
    </div>
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <!--begin::Hero-->
        <section class="dashboard-hero mb-6">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="fs-4 fw-bold text-gray-800 mb-1">Bugün, <?php echo $todayLabel; ?></h2>
                    <p class="text-gray-600 mb-0">Bugünkü duruşmalarınız ve özet bilgiler</p>
                </div>
            </div>
        </section>
        <!--end::Hero-->
        <!--begin::AI Asistan (Günlük Analiz)-->
        <div class="row mb-6">
            <div class="col-12">
                <div id="dashboard_ai_asistan_row" class="card card-bordered ai-asistan-premium">
                    <div class="card-body py-4 px-5 d-flex align-items-center flex-wrap gap-3">
                        <span class="d-flex align-items-center shrink-0 fw-bold text-gray-800"><i class="bi bi-stars me-2 text-primary"></i>AI Asistan <span class="badge badge-light-primary ms-2">Günlük Analiz</span></span>
                        <span id="ai_capacity_forecast_text" class="flex-grow-1 min-w-0 text-gray-700">yükleniyor..</span>
                    </div>
                </div>
            </div>
        </div>
        <!--end::AI Asistan-->
        <!--begin::Stat cards-->
        <div class="row g-4 g-xl-6 mb-6">
            <div class="col-sm-6 col-xl-4">
                <div class="card card-bordered dashboard-stat-card h-100">
                    <div class="card-body py-5 px-5">
                        <span class="stat-label d-block mb-1 fw-bold text-dark">Bugünkü Dosya Sayısı</span>
                        <span class="stat-value text-primary"><?php echo $todayCount; ?></span>
                        <span class="text-gray-600 small d-block mt-1">Bu güne ait kayıt</span>
                        <div class="d-flex flex-wrap gap-3 mt-3 pt-2 border-top border-gray-200">
                            <div class="flex-grow-1 min-w-0">
                                <?php if (empty($todayByTaraf)) { ?>
                                    <span class="text-gray-600 small">Taraf bilgisi yok</span>
                                <?php } else { ?>
                                    <?php foreach ($todayByTaraf as $row) {
                                        $tarafAdi = isset($row->d_taraf) ? trim($row->d_taraf) : '';
                                        $cnt = isset($row->cnt) ? (int) $row->cnt : 0;
                                        if ($tarafAdi === '') continue;
                                    ?>
                                    <span class="text-gray-700 small d-block"><?php echo htmlspecialchars($tarafAdi); ?>: <strong class="text-gray-800"><?php echo $cnt; ?></strong></span>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="border-start border-gray-200 ps-3 flex-shrink-0">
                                <?php if (empty($todayByIslem)) { ?>
                                    <span class="text-gray-600 small">İşlem bilgisi yok</span>
                                <?php } else { ?>
                                    <?php foreach ($todayByIslem as $row) {
                                        $islemAdi = isset($row->d_islem) ? trim(rtrim(trim($row->d_islem), '@')) : '';
                                        $cnt = isset($row->cnt) ? (int) $row->cnt : 0;
                                        if ($islemAdi === '') continue;
                                    ?>
                                    <span class="text-gray-700 small d-block"><?php echo htmlspecialchars($islemAdi); ?>: <strong class="text-gray-800"><?php echo $cnt; ?></strong></span>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="card card-bordered dashboard-stat-card h-100">
                    <div class="card-body py-4 px-5">
                        <span class="stat-label d-block mb-3 fw-bold text-dark">Bugün avukat bazında duruşmalar</span>
                        <?php if (empty($todayByLawyer)) { ?>
                            <span class="text-gray-600 small">Bugüne ait duruşma yok</span>
                        <?php } else { ?>
                            <ul class="list-unstyled mb-0 dashboard-lawyer-list">
                                <?php foreach ($todayByLawyer as $row) {
                                    $name = isset($row->d_avukat) ? htmlspecialchars(trim($row->d_avukat)) : '';
                                    $cnt = isset($row->cnt) ? (int) $row->cnt : 0;
                                    if ($name === '') continue;
                                ?>
                                <li class="py-1 border-bottom border-gray-200 border-bottom-dashed text-gray-800"><?php echo $name; ?>: <span class="fw-bold text-info"><?php echo $cnt; ?></span></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="card card-bordered dashboard-stat-card h-100">
                    <div class="card-body py-4 px-5">
                        <span class="stat-label d-block mb-3 fw-bold text-dark">Günlük mahkeme istatistiği</span>
                        <?php if (empty($todayByCourt)) { ?>
                            <span class="text-gray-600 small">Bugüne ait duruşma yok</span>
                        <?php } else { ?>
                            <ul class="list-unstyled mb-0 dashboard-lawyer-list">
                                <?php foreach ($todayByCourt as $row) {
                                    $mahkemeRaw = isset($row->d_mahkeme) ? trim($row->d_mahkeme) : '';
                                    $mahkeme = $mahkemeRaw !== '' ? htmlspecialchars(rtrim($mahkemeRaw, '@')) : '';
                                    $cnt = isset($row->cnt) ? (int) $row->cnt : 0;
                                    if ($mahkeme === '') continue;
                                ?>
                                <li class="py-1 border-bottom border-gray-200 border-bottom-dashed text-gray-800"><?php echo $mahkeme; ?>: <span class="fw-bold text-warning"><?php echo $cnt; ?></span></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Stat cards-->
        <!--begin::Not Defteri / Hatırlatmalar (yan yana kartlar)-->
        <div class="row g-4 g-xl-6 mb-6">
            <div class="col-md-6">
                <div class="card card-bordered h-100">
                    <div class="card-header border-0 pt-5 pb-2 d-flex align-items-center justify-content-between">
                        <span class="card-label fw-bold text-dark"><i class="bi bi-journal me-2"></i>Son Not</span>
                        <a href="<?php echo base_url('notesreminders'); ?>" class="btn btn-sm btn-light-primary">Tümünü gör</a>
                    </div>
                    <div class="card-body pt-2 pb-4">
                        <?php if (!$lastNote) { ?>
                            <p class="text-gray-600 mb-0">Henüz not yok. <a href="<?php echo base_url('notesreminders'); ?>">Not Defteri</a> sayfasından ekleyebilirsiniz.</p>
                        <?php } else {
                            $this->load->helper('notes');
                            $preview = $lastNote->n_content ? mb_substr(strip_tags($lastNote->n_content), 0, 100) . (mb_strlen(strip_tags($lastNote->n_content)) > 100 ? '...' : '') : '';
                        ?>
                            <div class="bg-light rounded p-3">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($lastNote->n_title); ?> <?php echo function_exists('getNoteTagBadge') ? getNoteTagBadge($lastNote->n_tag ?? '') : ''; ?></div>
                                <?php if ($preview) { ?><div class="text-gray-700 small mt-1"><?php echo htmlspecialchars($preview); ?></div><?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-bordered h-100">
                    <div class="card-header border-0 pt-5 pb-2 d-flex align-items-center justify-content-between">
                        <span class="card-label fw-bold text-dark"><i class="bi bi-bell me-2"></i>Hatırlatmalar</span>
                        <a href="<?php echo base_url('notesreminders'); ?>" class="btn btn-sm btn-light-primary">Tümünü gör</a>
                    </div>
                    <div class="card-body pt-2 pb-4">
                        <?php if (empty($reminders)) { ?>
                            <p class="text-gray-600 mb-0">Yaklaşan hatırlatma yok. <a href="<?php echo base_url('notesreminders'); ?>">Not Defteri</a> sayfasından ekleyebilirsiniz.</p>
                        <?php } else { ?>
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($reminders as $r) {
                                    $rDate = $r->n_reminder_at ? date('d.m.Y H:i', strtotime($r->n_reminder_at)) : '';
                                ?>
                                <li class="d-flex align-items-center py-1">
                                    <span class="text-gray-600 me-2"><?php echo htmlspecialchars($rDate); ?></span>
                                    <a href="<?php echo base_url('notesreminders'); ?>" class="text-primary text-hover-primary"><?php echo htmlspecialchars($r->n_title); ?></a>
                                </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Not Defteri / Hatırlatmalar-->
        <!--begin::Bugün tablosu-->
        <div class="row g-4 g-xl-6">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-header border-0 pt-5 px-5 pb-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-light-primary" id="kt_dashboard_durusmalar_listeleme_btn" title="Bugün olan duruşmaları listele">
                                    <i class="bi bi-list-ul"></i>
                                </button>
                                <h3 class="card-title m-0">Bugün Olan Duruşmalar</h3>
                            </div>
                            <?php if (isDbAllowedWriteModule("durusmalar")) { ?>
                                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_durusmalar_manuel">Yeni Kayıt</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body py-3 px-3" id="durusmalar_content_list">
                        <table id="kt_content_durusmalar_list" class="table align-middle table-row-dashed table-striped table-compact min-h-300px fs-6 gy-4" style="white-space:nowrap;">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-75px">Dosya No</th>
                                    <th class="min-w-90px">Esas No</th>
                                    <th class="min-w-100px">Mahkeme</th>
                                    <th class="min-w-120px">Dur.Tarihi</th>
                                    <th class="min-w-100px">Avukat</th>
                                    <th class="min-w-90px">Taraf</th>
                                    <th class="min-w-90px">İşlem</th>
                                    <th class="min-w-90px">Dosya Türü</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 fw-semibold"></tbody>
                        </table>
                        <input type="hidden" id="mainModuleCustom" name="mainModuleCustom" value="dashboard">
                        <input type="hidden" id="subModuleCustom" name="subModuleCustom" value="">
                        <input type="hidden" id="defaultPageLength" name="defaultPageLength" value="9999">
                    </div>
                </div>
            </div>
        </div>
        <!--end::Bugün tablosu-->
    </div>
</div>
<!--end::Content-->
