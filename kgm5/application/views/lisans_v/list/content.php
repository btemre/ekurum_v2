<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 mb-1">
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
                    <a href="<?php echo base_url('dashboard'); ?>" class="text-gray-700 text-hover-primary me-1"><i class="fonticon-home text-gray-700 fs-3"></i></a>
                </li>
                <li class="breadcrumb-item"><span class="svg-icon svg-icon-4 svg-icon-gray-700 mx-n1"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.6343 12.5657L8.45001 16.75C8.0358 17.1642 8.0358 17.8358 8.45001 18.25C8.86423 18.6642 9.5358 18.6642 9.95001 18.25L15.4929 12.7071C15.8834 12.3166 15.8834 11.6834 15.4929 11.2929L9.95001 5.75C9.5358 5.33579 8.86423 5.33579 8.45001 5.75C8.0358 6.16421 8.0358 6.83579 8.45001 7.25L12.6343 11.4343C12.9467 11.7467 12.9467 12.2533 12.6343 12.5657Z" fill="currentColor" /></svg></span></li>
                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">Lisans Yönetimi</li>
            </ul>
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Lisans / Abonelik Listesi</h1>
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
                    <div class="card-header border-0 pt-1">
                        <div class="card-title">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder text-dark">Birimler – Abonelik Durumu</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Yakında bitecekler, süresi dolmuşlar ve grace durumuna göre filtreleyebilirsiniz</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex gap-2">
                                <a href="<?php echo base_url('lisans'); ?>" class="btn btn-sm btn-light-primary<?php echo ($currentFilter === 'all') ? ' active' : ''; ?>">Tümü</a>
                                <a href="<?php echo base_url('lisans?filter=expiring'); ?>" class="btn btn-sm btn-light-warning<?php echo ($currentFilter === 'expiring') ? ' active' : ''; ?>">Yakında bitecekler</a>
                                <a href="<?php echo base_url('lisans?filter=expired'); ?>" class="btn btn-sm btn-light-danger<?php echo ($currentFilter === 'expired') ? ' active' : ''; ?>">Süresi dolmuşlar</a>
                                <a href="<?php echo base_url('lisans?filter=grace'); ?>" class="btn btn-sm btn-light-info<?php echo ($currentFilter === 'grace') ? ' active' : ''; ?>">Grace / gecikmiş</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <?php if (empty($itemList)) { ?>
                            <div class="alert alert-primary d-flex align-items-center p-5 mb-0">
                                <span class="svg-icon svg-icon-2hx svg-icon-primary me-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor" /></svg></span>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-primary">Kayıt bulunamadı</h4>
                                    <span>Bu filtreye uyan birim kaydı yok.</span>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-row-bordered fs-6 gy-2">
                                    <thead>
                                        <tr class="border-top text-start text-dark-400 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-80px">Birim</th>
                                            <th class="min-w-100px">Başlangıç</th>
                                            <th class="min-w-100px">Bitiş</th>
                                            <th class="min-w-80px">Kalan gün</th>
                                            <th class="min-w-90px">Durum</th>
                                            <th class="min-w-70px">Demo</th>
                                            <th class="min-w-220px">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold text-gray-600 border-bottom border-gray-200">
                                        <?php foreach ($itemList as $item) {
                                            $statusLabel = array('active' => 'Aktif', 'expired' => 'Süresi doldu', 'grace' => 'Gecikmiş', 'suspended' => 'Askıda', 'cancelled' => 'İptal');
                                            $statusClass = array('active' => 'success', 'expired' => 'danger', 'grace' => 'warning', 'suspended' => 'secondary', 'cancelled' => 'dark');
                                            $st = isset($item->subscription_status) && $item->subscription_status !== null && $item->subscription_status !== '' ? $item->subscription_status : 'active';
                                            $label = isset($statusLabel[$st]) ? $statusLabel[$st] : $st;
                                            $class = isset($statusClass[$st]) ? $statusClass[$st] : 'secondary';
                                            $days = $item->days_remaining !== null ? (int)$item->days_remaining : '–';
                                        ?>
                                            <tr>
                                                <td><span class="text-dark"><?php echo htmlspecialchars($item->ub_title); ?></span> <span class="text-muted fs-8">(<?php echo (int)$item->ub_id; ?>)</span></td>
                                                <td><?php echo $item->subscription_start_date ? date('d.m.Y', strtotime($item->subscription_start_date)) : '–'; ?></td>
                                                <td><?php echo $item->subscription_end_date ? date('d.m.Y', strtotime($item->subscription_end_date)) : ($item->demo_end_date ? date('d.m.Y', strtotime($item->demo_end_date)) : '–'); ?></td>
                                                <td><?php echo $days; ?></td>
                                                <td><span class="badge badge-light-<?php echo $class; ?>"><?php echo $label; ?></span></td>
                                                <td><?php echo !empty($item->is_demo) ? 'Evet' : 'Hayır'; ?></td>
                                                <td class="text-end">
                                                    <?php if (isDbAllowedUpdateModule('lisans')) { ?>
                                                        <button type="button" class="btn btn-sm btn-light-primary me-1" data-action="renew" data-ub-id="<?php echo (int)$item->ub_id; ?>" data-period="yearly">Yenile (1 yıl)</button>
                                                        <button type="button" class="btn btn-sm btn-light-primary me-1" data-action="renew" data-ub-id="<?php echo (int)$item->ub_id; ?>" data-period="monthly">Yenile (1 ay)</button>
                                                        <div class="btn-group mt-1">
                                                            <button type="button" class="btn btn-sm btn-light-warning" data-action="set-status" data-ub-id="<?php echo (int)$item->ub_id; ?>" data-status="grace">Grace</button>
                                                            <button type="button" class="btn btn-sm btn-light-info" data-action="set-status" data-ub-id="<?php echo (int)$item->ub_id; ?>" data-status="active">Aktif</button>
                                                            <button type="button" class="btn btn-sm btn-light-danger" data-action="set-status" data-ub-id="<?php echo (int)$item->ub_id; ?>" data-status="suspended">Askıya al</button>
                                                        </div>
                                                    <?php } else { ?>
                                                        <span class="text-muted fs-8">–</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Content-->
