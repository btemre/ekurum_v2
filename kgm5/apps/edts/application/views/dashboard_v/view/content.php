<style>
/* Premium AI butonları - belirgin görünüm */
.btn-ai { font-weight: 700 !important; letter-spacing: 0.02em; padding: 0.6rem 1.1rem !important; border-radius: 0.65rem !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04) !important;
  transition: transform 0.15s ease, box-shadow 0.2s ease !important; }
.btn-ai:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,0.12) !important; }
.btn-ai .bi-stars, .btn-ai .bi-search { font-size: 1.1em; vertical-align: -0.15em; }
.btn-ai.btn-primary { background: linear-gradient(135deg, #009ef7 0%, #0095e8 100%) !important; border-color: rgba(255,255,255,0.25) !important; color: #fff !important; }
.btn-ai.btn-primary:hover { background: linear-gradient(135deg, #0095e8 0%, #0081c6 100%) !important; box-shadow: 0 4px 16px rgba(0,158,247,0.45) !important; }
.btn-ai.btn-light-primary { background: linear-gradient(180deg, #f1faff 0%, #e8f4fc 100%) !important; border: 1px solid #009ef7 !important; color: #009ef7 !important; }
.btn-ai.btn-light-primary:hover { background: linear-gradient(180deg, #e8f4fc 0%, #d4edfc 100%) !important; box-shadow: 0 4px 14px rgba(0,158,247,0.3) !important; }
.btn-ai.btn-light-info { background: linear-gradient(180deg, #f1faff 0%, #e8f6fc 100%) !important; border: 1px solid #00bcd4 !important; color: #00a3b8 !important; }
.btn-ai.btn-light-info:hover { background: linear-gradient(180deg, #e8f6fc 0%, #d4f0f8 100%) !important; box-shadow: 0 4px 14px rgba(0,188,212,0.3) !important; }
</style>
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
	<!--begin::Toolbar container-->
	<div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
		<!--begin::Page title-->
		<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
			<!--begin::Breadcrumb-->
			<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 mb-1">
				<!--begin::Item-->
				<li class="breadcrumb-item text-gray-600 fw-bold lh-1">
					<a href="<?php echo base_url('dashboard'); ?>" class="text-gray-700 text-hover-primary me-1">
						<i class="fonticon-home text-gray-700 fs-3"></i>
					</a>
				</li>
				<!--end::Item-->
				<!--begin::Item-->
				<li class="breadcrumb-item">
					<!--begin::Svg Icon | path: icons/duotune/arrows/arr071.svg-->
					<span class="svg-icon svg-icon-4 svg-icon-gray-700 mx-n1">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M12.6343 12.5657L8.45001 16.75C8.0358 17.1642 8.0358 17.8358 8.45001 18.25C8.86423 18.6642 9.5358 18.6642 9.95001 18.25L15.4929 12.7071C15.8834 12.3166 15.8834 11.6834 15.4929 11.2929L9.95001 5.75C9.5358 5.33579 8.86423 5.33579 8.45001 5.75C8.0358 6.16421 8.0358 6.83579 8.45001 7.25L12.6343 11.4343C12.9467 11.7467 12.9467 12.2533 12.6343 12.5657Z"
								fill="currentColor" />
						</svg>
					</span>
					<!--end::Svg Icon-->
				</li>
				<!--end::Item-->
				<!--begin::Item-->
				<li class="breadcrumb-item text-gray-600 fw-bold lh-1">Dashboard</li>
				<!--end::Item-->
			</ul>
			<!--end::Breadcrumb-->
			<!--begin::Title-->
			<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">EDTS
				Dashboard</h1>
			<!--end::Title-->
		</div>
		<!--end::Page title-->
		<!--begin::Actions-->
		<div class="d-flex align-items-center gap-2 gap-lg-3">
			<!--begin::AI Butonları-->
			<button type="button" class="btn btn-sm btn-ai btn-primary" onclick="AiService.requestSummary()">
				<i class="bi bi-stars me-1"></i>Genel AI Özet
			</button>
			<!--end::AI Butonları-->
		</div>
		<!--end::Actions-->
	</div>
	<!--end::Toolbar container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
	<!--begin::Content container-->
	<div id="kt_app_content_container" class="app-container container-fluid">
		<!--begin::Row-->
		<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
			<!--begin::Col-->
			<div class="col-md-12 col-lg-12 col-xl-12 mb-md-5 mb-xl-10">
				<!--begin::Card-->
				<div class="card px-2">
					<!--begin::Card header-->
					<div class="card-header border-0 pt-1 px-3">
						<!--begin::Card title-->
						<div class="card-title">Bugün Olan Duruşmaların Listesi
							<!--begin::Search-->
							<form method="POST" action="#" id="kt_modal_list_dosya_filter_form">
								<!--begin::Card-->
								<div class="card mb-7 d-none">
									<!--begin::Card body-->
									<div class="card-body">
										<!--begin::Compact form-->
										<div class="d-flex align-items-center">
											<!--begin::Input group-->
											<div class="position-relative w-md-400px me-md-2">
												<!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
												<span
													class="svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
														viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
															height="2" rx="1" transform="rotate(45 17.0365 15.1223)"
															fill="currentColor" />
														<path
															d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
															fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
												<input type="text" class="form-control form-control-solid ps-10"
													name="searchText" placeholder="Genel Arama"
													data-kt-dosc-durusmalar-update-table-filter="search"
													id="searchText" />
											</div>
											<!--end::Input group-->
											<!--begin:Action-->
											<div class="d-flex align-items-center">
												<button id="kt_modal_durusmalar_list_ara_submit" type="submit"
													class="btn btn-primary me-5"
													data-kt-dosc-durusmalar-update-table-search="search">Ara</button>
												<a id="kt_horizontal_search_advanced_link" class="btn btn-link"
													data-bs-toggle="collapse"
													href="#kt_durusma_advanced_search_form">Detaylı Arama</a>
											</div>
											<!--end:Action-->
										</div>
										<!--end::Compact form-->
										<!--begin::Advance form-->
										<div class="collapse" id="kt_durusma_advanced_search_form">
											<!--begin::Separator-->
											<div class="separator separator-dashed mt-9 mb-6"></div>
											<!--end::Separator-->
											<!--begin::Row-->
											<div class="row g-8 mb-1">
												<!--begin::Col-->

												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Mahkeme</label>
													<div class="input-group flex-nowrap">
														<span class="input-group-text"><i
																class="bi bi-person-workspace fs-3"></i></span>
														<div class="overflow-hidden flex-grow-1">
															<select class="form-select form-select-solid"
																data-control="select2" data-hide-search="true"
																data-placeholder="Seçiniz.." name="dlara_taraf"
																id="dlara_taraf">
																<option value="-1">Seçiniz..</option>
																<option value="DAVALI">DAVALI</option>
																
															</select>
														</div>
													</div>
												</div>

												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Taraf</label>
													<div class="input-group flex-nowrap">
														<span class="input-group-text"><i
																class="bi bi-person-workspace fs-3"></i></span>
														<div class="overflow-hidden flex-grow-1">
															<select class="form-select form-select-solid"
																data-control="select2" data-hide-search="true"
																data-placeholder="Seçiniz.." name="dlara_taraf"
																id="dlara_taraf">
																<option value="-1">Seçiniz..</option>
																<option value="DAVALI">DAVALI</option>
																<option value="DAVACI">DAVACI</option>
																<option value="DAHİLİ DAVACI">DAHİLİ DAVACI</option>
																<option value="KATILAN">KATILAN</option>
																<option value="MÜŞTEKİ">MÜŞTEKİ</option>
																<option value="İHBAR OLUNAN">İHBAR OLUNAN</option>
																<option value="KONTROL">KONTROL</option>
																<option value="KEŞİF">KEŞİF</option>
																<option value="GENEL MÜDÜRLÜK">GENEL MÜDÜRLÜK</option>
															</select>
														</div>
													</div>
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Esas No</label>
													<div class="input-group flex-nowrap">
														<span class="input-group-text"><i
																class="bi bi bi-file-earmark-binary fs-3"></i></span>
														<div class="overflow-hidden flex-grow-1">
															<input type="text" class="form-control form-control-solid"
																name="dlara_esasno" id="dlara_esasno" />
														</div>
													</div>
												</div>
												<div class="col-md-2">
													<label class="fs-6 form-label fw-bolder text-dark">Taraf
														Bilgisi</label>
													<input type="text"
														class="form-control form-control form-control-solid"
														name="dlara_tarafbilgisi" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Duruşma Takibi</label>
													<div class="input-group flex-nowrap">
														<span class="input-group-text"><i
																class="bi bi-person-workspace fs-3"></i></span>
														<div class="overflow-hidden flex-grow-1">
															<select class="form-select form-select-solid"
																data-control="select2" data-hide-search="true"
																data-placeholder="Seçiniz.." name="dlara_dtakip"
																id="dlara_dtakip">
																<option value="-1">Seçiniz..</option>
																<option value="1">Duruşmaya Gidildi</option>
																<option value="2">Mazeret Çekildi</option>
															</select>
														</div>
													</div>
												</div>
												<div class="col-md-3">
													<label class="fs-6 form-label fw-bolder text-dark">Duruşma
														Tutanağı</label>
													<!--begin::Radio group-->
													<div class="nav-group nav-group-fluid">
														<!--begin::Option-->
														<label>
															<input type="radio" class="btn-check" name="dlara_dtutanak"
																id="dlara_dtutanak" value="-1" checked="checked" />
															<span
																class="btn btn-sm btn-color-muted btn-active btn-active-primary fw-bolder px-4">Hepsi</span>
														</label>
														<!--end::Option-->
														<!--begin::Option-->
														<label>
															<input type="radio" class="btn-check" name="dlara_dtutanak"
																id="dlara_dtutanak" value="1" />
															<span
																class="btn btn-sm btn-color-muted btn-active btn-active-primary fw-bolder px-4">Alındı</span>
														</label>
														<!--end::Option-->
														<!--begin::Option-->
														<label>
															<input type="radio" class="btn-check" name="dlara_dtutanak"
																id="dlara_dtutanak" value="0" />
															<span
																class="btn btn-sm btn-color-muted btn-active btn-active-primary fw-bolder px-4">Alınmadı</span>
														</label>
														<!--end::Option-->
													</div>
													<!--end::Radio group-->
												</div>
											</div>

											<!--end::Row-->
										</div>
										<!--end::Advance form-->
									</div>
									<!--end::Card body-->
								</div>
								<!--end::Card-->
							</form>
							<!--end::Search-->
						</div>
						<!--begin::Card title-->
						<!--begin::AI Asistan - Tahmine dayalı uyarı (premium)-->
						<div id="dashboard_ai_asistan_row" class="ai-asistan-premium d-flex align-items-center flex-wrap gap-3 my-2 mt-3 mb-3 me-md-2 w-100 py-4 px-4">
							<span class="ai-asistan-label d-flex align-items-center shrink-0">
								<i class="bi bi-stars me-2"></i>AI Asistan
							</span>
							<span id="ai_capacity_forecast_text" class="ai-asistan-text flex-grow-1 min-w-0">düşünüyor..</span>
							<button type="button" class="btn btn-sm btn-light-primary fw-bold ai-asistan-btn shrink-0" id="ai_capacity_forecast_btn" onclick="AiService.requestCapacityForecast(true)">
								<i class="bi bi-arrow-repeat me-1"></i>Başka
							</button>
						</div>
						<!--end::AI Asistan-->
						<!--begin::Toolbar (AI ile Ara, Haftalık AI Özet, Dışa Aktar, Filtrele)-->
						<div id="dashboard_ai_buttons_row" class="d-flex justify-content-end align-items-center flex-wrap gap-2" data-kt-dosc-durusmalar-update-table-toolbar="base">
							<button type="button" class="btn btn-sm btn-ai btn-light-info" onclick="AiService.openSearchModal()">
								<i class="bi bi-search me-1"></i>AI ile Ara
							</button>
							<button type="button" class="btn btn-sm btn-ai btn-light-primary" onclick="AiService.requestWeekSummary()">
								<i class="bi bi-stars me-1"></i>Haftalık AI Özet
							</button>
							<button type="button" class="btn btn-sm btn-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
								<span class="svg-icon svg-icon-2">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
										<rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="currentColor" />
										<path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="currentColor" />
										<path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4" />
									</svg>
								</span>Dışa Aktar
							</button>
							<div id="kt_datatable_durusmalar_export_menu" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4" data-kt-menu="true">
								<div class="menu-item px-3">
									<a href="#" class="menu-link px-3" data-kt-export="copy">Tabloyu Kopyala</a>
								</div>
								<div class="menu-item px-3">
									<a href="#" class="menu-link px-3" data-kt-export="excel">Excel'e Çıkar</a>
								</div>
								<div class="menu-item px-3">
									<a href="#" class="menu-link px-3" data-kt-export="csv">CSV'e Çıkar</a>
								</div>
							</div>
							<div id="kt_datatable_durusmalar_export" class="d-none"></div>
							<!--begin::Filter-->
								<button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
									data-kt-menu-placement="bottom-end">
									<!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
									<span class="svg-icon svg-icon-2">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
											viewBox="0 0 24 24" fill="none">
											<path
												d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
												fill="currentColor" />
										</svg>
									</span>
									<!--end::Svg Icon-->Filtrele
								</button>
								<!--begin::Menu 1-->
								<div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
									id="kt-toolbar-filter">
									<!--begin::Header-->
									<div class="px-7 py-5">
										<div class="fs-4 text-dark fw-bolder">Filtreleme Seçenekleri</div>
									</div>
									<!--end::Header-->
									<!--begin::Separator-->
									<div class="separator border-gray-200"></div>
									<!--end::Separator-->
									<!--begin::Content-->
									<div class="px-7 py-5" data-kt-durusmalar-update-table-filter="form">
										<!--begin::Input group-->
										<div class="mb-10">
											<label class="form-label fs-6 fw-bold">Sorumlu Memur:</label>
											<select class="form-select form-select-solid fw-bolder"
												data-kt-select2="true" data-placeholder="Lütfen Seçiniz"
												data-allow-clear="true" data-hide-search="true"
												data-kt-durusmalar-update-table-filter="filterMemur"
												id="filterMemurSelect">
												<option value="-1" selected>Hepsi</option>
												<?php foreach (FormSelectSorumluMemurList() as $sMemurList) { ?>
													<option value="<?php echo $sMemurList->u_id; ?>"><?php echo trim($sMemurList->u_name . ' ' . $sMemurList->u_lastname) . ' ' . $sMemurList->u_surname; ?></option>
												<?php } ?>
											</select>
										</div>
										<!--end::Input group-->
										<!--begin::Input group-->
										<div class="mb-10">
											<label class="form-label fs-6 fw-bold">Sorumlu Avukat:</label>
											<select class="form-select form-select-solid fw-bolder"
												data-kt-select2="true" data-placeholder="Lütfen Seçiniz"
												data-allow-clear="true" data-hide-search="true"
												data-kt-durusmalar-update-table-filter="filterAvukat"
												id="filterAvukatSelect">
												<option value="-1" selected>Hepsi</option>
												<?php foreach (FormSelectSorumluAvukatList() as $sAvukatList) { ?>
													<option value="<?php echo $sAvukatList->u_id; ?>"><?php echo trim($sAvukatList->u_name . ' ' . $sAvukatList->u_lastname) . ' ' . $sAvukatList->u_surname; ?></option>
												<?php } ?>
											</select>
										</div>
										<!--end::Input group-->
										<!--begin::Input group-->
										<div class="mb-10">
											<label class="form-label fs-6 fw-bold">İşlem:</label>
											<select class="form-select form-select-solid fw-bolder"
												data-kt-select2="true" data-placeholder="Lütfen Seçiniz"
												data-allow-clear="true" data-hide-search="true"
												data-kt-durusmalar-update-table-filter="filterIslem"
												id="filterIslemSelect">
												<option value="-1" selected>Hepsi</option>
												<option value="Duruşma">Duruşma</option>
												<option value="İstinaf">İstinaf</option>
												<option value="Keşif">Keşif</option>
												<option value="Karar">Karar</option>
												<option value="Red">Red</option>
												<option value="Birleşti">Birleşti</option>
												<option value="Kaldırıldı">Kaldırıldı</option>
											</select>
										</div>
										<!--end::Input group-->
										<!--begin::Actions-->
										<div class="d-flex justify-content-end">
											<button type="reset" class="btn btn-light btn-active-light-primary me-2"
												data-kt-menu-dismiss="true"
												data-kt-dosc-durusmalar-update-table-filter="reset">Sıfırla</button>
											<button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true"
												data-kt-dosc-durusmalar-update-table-filter="filter"
												name="filtreleButton" id="filtreleButton">Filtrele</button>
										</div>
										<!--end::Actions-->
									</div>
									<!--end::Content-->
								</div>
								<!--end::Menu 1-->
								<!--end::Filter-->
							</div>
							<!--end::Toolbar-->
							<!--begin::Group actions-->
							<div class="d-flex justify-content-end align-items-center d-none"
								data-kt-user-table-toolbar="selected">
								<div class="fw-bolder me-5">
									<span class="me-2" data-kt-user-table-select="selected_count"></span>Seçilen
								</div>
								<button type="button" class="btn btn-danger"
									data-kt-user-table-select="delete_selected">Seçilenleri Sil</button>
							</div>
							<!--end::Group actions-->
							<!--begin::Modal - Adjust Balance-->
							<div class="modal fade" id="kt_modal_export_users" tabindex="-1" aria-hidden="true">
								<!--begin::Modal dialog-->
								<div class="modal-dialog modal-dialog-centered mw-650px">
									<!--begin::Modal content-->
									<div class="modal-content">
										<!--begin::Modal header-->
										<div class="modal-header">
											<!--begin::Modal title-->
											<h2 class="fw-bolder">Export Users</h2>
											<!--end::Modal title-->
											<!--begin::Close-->
											<div class="btn btn-icon btn-sm btn-active-icon-primary"
												data-kt-users-modal-action="close">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
												<span class="svg-icon svg-icon-1">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
														viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
															rx="1" transform="rotate(-45 6 17.3137)"
															fill="currentColor" />
														<rect x="7.41422" y="6" width="16" height="2" rx="1"
															transform="rotate(45 7.41422 6)" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</div>
											<!--end::Close-->
										</div>
										<!--end::Modal header-->
										<!--begin::Modal body-->
										<div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
											<!--begin::Form-->
											<form id="kt_modal_export_users_form" class="form" action="#">
												<!--begin::Input group-->
												<div class="fv-row mb-10">
													<!--begin::Label-->
													<label class="fs-6 fw-bold form-label mb-2">Select Roles:</label>
													<!--end::Label-->
													<!--begin::Input-->
													<select name="group" data-control="select2"
														data-placeholder="Select a role" data-hide-search="true"
														class="form-select form-select-solid fw-bolder">
														<option></option>
														<option value="Administrator">Administrator</option>
														<option value="Analyst">Analyst</option>
														<option value="Developer">Developer</option>
														<option value="Support">Support</option>
														<option value="Trial">Trial</option>
													</select>
													<!--end::Input-->
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="fv-row mb-10">
													<!--begin::Label-->
													<label class="required fs-6 fw-bold form-label mb-2">Select Export
														Format:</label>
													<!--end::Label-->
													<!--begin::Input-->
													<select name="format" data-control="select2"
														data-placeholder="Select a format" data-hide-search="true"
														class="form-select form-select-solid fw-bolder">
														<option></option>
														<option value="excel">Excel</option>
														<option value="pdf">PDF</option>
														<option value="cvs">CVS</option>
														<option value="zip">ZIP</option>
													</select>
													<!--end::Input-->
												</div>
												<!--end::Input group-->
												<!--begin::Actions-->
												<div class="text-center">
													<button type="reset" class="btn btn-light me-3"
														data-kt-users-modal-action="cancel">Vazgeç</button>
													<button type="submit" class="btn btn-primary"
														data-kt-users-modal-action="submit">
														<span class="indicator-label">Onayla</span>
														<span class="indicator-progress">Lütfen Bekleyin...
															<span
																class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
													</button>
												</div>
												<!--end::Actions-->
											</form>
											<!--end::Form-->
										</div>
										<!--end::Modal body-->
									</div>
									<!--end::Modal content-->
								</div>
								<!--end::Modal dialog-->
							</div>
							<!--end::Modal - New Card-->
							<!--begin::Modal - Add task-->
							<div class="modal fade" id="kt_modal_update_durusmalar" tabindex="-1" aria-hidden="true">
								<!--begin::Modal dialog-->
								<div class="modal-dialog modal-dialog-centered mw-650px">
									<!--begin::Modal content-->
									<div class="modal-content">
										<!--begin::Modal header-->
										<div class="modal-header" id="kt_modal_update_durusmalar_header">
											<!--begin::Modal title-->
											<h2 class="fw-bolder">Kullanıcı Ekle</h2>
											<!--end::Modal title-->
											<!--begin::Close-->
											<div class="btn btn-icon btn-sm btn-active-icon-primary"
												data-kt-users-modal-action="close">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
												<span class="svg-icon svg-icon-1">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
														viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
															rx="1" transform="rotate(-45 6 17.3137)"
															fill="currentColor" />
														<rect x="7.41422" y="6" width="16" height="2" rx="1"
															transform="rotate(45 7.41422 6)" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</div>
											<!--end::Close-->
										</div>
										<!--end::Modal header-->
										<!--begin::Modal body-->
										<div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
											<!--begin::Form-->
											<form id="kt_modal_update_durusmalar_form" class="form" action="#">
												<!--begin::Scroll-->
												<div class="d-flex flex-column scroll-y me-n7 pe-7"
													id="kt_modal_update_durusmalar_scroll" data-kt-scroll="true"
													data-kt-scroll-activate="{default: false, lg: true}"
													data-kt-scroll-max-height="auto"
													data-kt-scroll-dependencies="#kt_modal_update_durusmalar_header"
													data-kt-scroll-wrappers="#kt_modal_update_durusmalar_scroll"
													data-kt-scroll-offset="300px">
													<!--begin::Input group-->
													<div class="fv-row mb-7">
														<!--begin::Label-->
														<label class="d-block fw-bold fs-6 mb-5">Avatar</label>
														<!--end::Label-->
														<!--begin::Image input-->
														<div class="image-input image-input-outline"
															data-kt-image-input="true"
															style="background-image: url('assets/media/svg/avatars/blank.svg')">
															<!--begin::Preview existing avatar-->
															<div class="image-input-wrapper w-125px h-125px"
																style="background-image: url(assets/media/avatars/300-6.jpg);">
															</div>
															<!--end::Preview existing avatar-->
															<!--begin::Label-->
															<label
																class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
																data-kt-image-input-action="change"
																data-bs-toggle="tooltip" title="Change avatar">
																<i class="bi bi-pencil-fill fs-7"></i>
																<!--begin::Inputs-->
																<input type="file" name="avatar"
																	accept=".png, .jpg, .jpeg" />
																<input type="hidden" name="avatar_remove" />
																<!--end::Inputs-->
															</label>
															<!--end::Label-->
															<!--begin::Cancel-->
															<span
																class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
																data-kt-image-input-action="cancel"
																data-bs-toggle="tooltip" title="Cancel avatar">
																<i class="bi bi-x fs-2"></i>
															</span>
															<!--end::Cancel-->
															<!--begin::Remove-->
															<span
																class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
																data-kt-image-input-action="remove"
																data-bs-toggle="tooltip" title="Remove avatar">
																<i class="bi bi-x fs-2"></i>
															</span>
															<!--end::Remove-->
														</div>
														<!--end::Image input-->
														<!--begin::Hint-->
														<div class="form-text">Allowed file types: png, jpg, jpeg.</div>
														<!--end::Hint-->
													</div>
													<!--end::Input group-->
													<!--begin::Input group-->
													<div class="fv-row mb-7">
														<!--begin::Label-->
														<label class="required fw-bold fs-6 mb-2">Full Name</label>
														<!--end::Label-->
														<!--begin::Input-->
														<input type="text" name="user_name"
															class="form-control form-control-solid mb-3 mb-lg-0"
															placeholder="Full name" value="Emma Smith" />
														<!--end::Input-->
													</div>
													<!--end::Input group-->
													<!--begin::Input group-->
													<div class="fv-row mb-7">
														<!--begin::Label-->
														<label class="required fw-bold fs-6 mb-2">Email</label>
														<!--end::Label-->
														<!--begin::Input-->
														<input type="email" name="user_email"
															class="form-control form-control-solid mb-3 mb-lg-0"
															placeholder="example@domain.com" value="smith@kpmg.com" />
														<!--end::Input-->
													</div>
													<!--end::Input group-->
													<!--begin::Input group-->
													<div class="mb-7">
														<!--begin::Label-->
														<label class="required fw-bold fs-6 mb-5">Role</label>
														<!--end::Label-->
														<!--begin::Roles-->
														<!--begin::Input row-->
														<div class="d-flex fv-row">
															<!--begin::Radio-->
															<div class="form-check form-check-custom form-check-solid">
																<!--begin::Input-->
																<input class="form-check-input me-3" name="user_role"
																	type="radio" value="0"
																	id="kt_modal_update_role_option_0"
																	checked='checked' />
																<!--end::Input-->
																<!--begin::Label-->
																<label class="form-check-label"
																	for="kt_modal_update_role_option_0">
																	<div class="fw-bolder text-gray-800">Administrator
																	</div>
																	<div class="text-gray-600">Best for business ownerfs
																		and company administrators</div>
																</label>
																<!--end::Label-->
															</div>
															<!--end::Radio-->
														</div>
														<!--end::Input row-->
														<div class='separator separator-dashed my-5'></div>
														<!--begin::Input row-->
														<div class="d-flex fv-row">
															<!--begin::Radio-->
															<div class="form-check form-check-custom form-check-solid">
																<!--begin::Input-->
																<input class="form-check-input me-3" name="user_role"
																	type="radio" value="1"
																	id="kt_modal_update_role_option_1" />
																<!--end::Input-->
																<!--begin::Label-->
																<label class="form-check-label"
																	for="kt_modal_update_role_option_1">
																	<div class="fw-bolder text-gray-800">Developer</div>
																	<div class="text-gray-600">Best for developers or
																		people primarily using the API</div>
																</label>
																<!--end::Label-->
															</div>
															<!--end::Radio-->
														</div>
														<!--end::Input row-->
														<div class='separator separator-dashed my-5'></div>
														<!--begin::Input row-->
														<div class="d-flex fv-row">
															<!--begin::Radio-->
															<div class="form-check form-check-custom form-check-solid">
																<!--begin::Input-->
																<input class="form-check-input me-3" name="user_role"
																	type="radio" value="2"
																	id="kt_modal_update_role_option_2" />
																<!--end::Input-->
																<!--begin::Label-->
																<label class="form-check-label"
																	for="kt_modal_update_role_option_2">
																	<div class="fw-bolder text-gray-800">Analyst</div>
																	<div class="text-gray-600">Best for people who need
																		full access to analytics data, but don't need to
																		update business settings</div>
																</label>
																<!--end::Label-->
															</div>
															<!--end::Radio-->
														</div>
														<!--end::Input row-->
														<div class='separator separator-dashed my-5'></div>
														<!--begin::Input row-->
														<div class="d-flex fv-row">
															<!--begin::Radio-->
															<div class="form-check form-check-custom form-check-solid">
																<!--begin::Input-->
																<input class="form-check-input me-3" name="user_role"
																	type="radio" value="3"
																	id="kt_modal_update_role_option_3" />
																<!--end::Input-->
																<!--begin::Label-->
																<label class="form-check-label"
																	for="kt_modal_update_role_option_3">
																	<div class="fw-bolder text-gray-800">Support</div>
																	<div class="text-gray-600">Best for employees who
																		regularly refund payments and respond to
																		disputes</div>
																</label>
																<!--end::Label-->
															</div>
															<!--end::Radio-->
														</div>
														<!--end::Input row-->
														<div class='separator separator-dashed my-5'></div>
														<!--begin::Input row-->
														<div class="d-flex fv-row">
															<!--begin::Radio-->
															<div class="form-check form-check-custom form-check-solid">
																<!--begin::Input-->
																<input class="form-check-input me-3" name="user_role"
																	type="radio" value="4"
																	id="kt_modal_update_role_option_4" />
																<!--end::Input-->
																<!--begin::Label-->
																<label class="form-check-label"
																	for="kt_modal_update_role_option_4">
																	<div class="fw-bolder text-gray-800">Trial</div>
																	<div class="text-gray-600">Best for people who need
																		to preview content data, but don't need to make
																		any updates</div>
																</label>
																<!--end::Label-->
															</div>
															<!--end::Radio-->
														</div>
														<!--end::Input row-->
														<!--end::Roles-->
													</div>
													<!--end::Input group-->
												</div>
												<!--end::Scroll-->
												<!--begin::Actions-->
												<div class="text-center pt-15">
													<button type="reset" class="btn btn-light me-3"
														data-kt-users-modal-action="cancel">Discard</button>
													<button type="submit" class="btn btn-primary"
														data-kt-users-modal-action="submit">
														<span class="indicator-label">Submit</span>
														<span class="indicator-progress">Please wait...
															<span
																class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
													</button>
												</div>
												<!--end::Actions-->
											</form>
											<!--end::Form-->
										</div>
										<!--end::Modal body-->
									</div>
									<!--end::Modal content-->
								</div>
								<!--end::Modal dialog-->
							</div>
							<!--end::Modal - Add task-->
						</div>
						<!--end::Card toolbar-->
					</div>
					<!--end::Card header-->
					<!--begin::Card body-->
					<div class="card-body py-1 px-1" id="durusmalar_content_list">
						<style>
							#kt_content_durusmalar_list thead th,
							#kt_content_durusmalar_list tbody td {
								padding: 0.25rem 0.4rem;
								line-height: 1.2;
								vertical-align: middle;
							}
							#kt_content_durusmalar_list {
								font-size: 0.9rem;
								white-space: nowrap;
							}
						</style>
						<!--begin::Datatable-->
						<table id="kt_content_durusmalar_list"
							class="table align-middle table-row-dashed table-striped table-compact min-h-400px fs-5 gy-5" style="white-space:nowrap;">
							<thead>
								<tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
									<th class="min-w-100px" data-priority="1">Esas No</th>
									<th class="min-w-100px" data-priority="1">Mahkeme</th>
									<th class="min-w-75px" data-priority="1">Dosya No</th>
									<th class="min-w-125px" data-priority="1">Dur.Tarihi</th>
									<th class="min-w-100px" data-priority="1">Avukat</th>
									<th class="min-w-100px" data-priority="1">Memur</th>
									<th class="min-w-100px" data-priority="1">Dosya Türü</th>
									<th class="min-w-100px" data-priority="1">Taraf</th>
									<th class="min-w-100px" data-priority="1">İşlem</th>
									<th class="min-w-100px" data-priority="1">Taraf Bilgisi</th>
									<th data-priority="1">D.Takip</th>
									<th data-priority="1">D.Tutanak</th>
									<th class="min-w-100px" data-priority="1">Etiketler</th>
									<th class="text-center min-w-100px" data-priority="1">İşlemler</th>
								</tr>
							</thead>
							<tbody class="text-gray-600 fw-bold"></tbody>
						</table>
						<!--end::Datatable-->
					</div>
					<!--end::Card body-->
				</div>
				<!--end::Card-->

			</div>
			<!--end::Col-->
		</div>
		<!--end::Row-->
	</div>
	<!--end::Content container-->
</div>
<!--end::Content-->