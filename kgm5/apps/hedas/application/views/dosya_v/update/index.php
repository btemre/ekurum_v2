		<!--begin::Modal - Update Dosya-->
		<div class="modal fade" id="kt_modal_update_dosya" tabindex="-1" aria-hidden="true">
			<!--begin::Modal dialog-->
			<div class="modal-dialog modal-dialog-centered mw-1000px">
				<!--begin::Modal content-->
				<div class="modal-content rounded">
					<!--begin::Modal header-->
					<div class="modal-header pb-0 border-0 justify-content-end">
						<!--begin::Close-->
						<div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
							<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
							<span class="svg-icon svg-icon-1">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
									<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
								</svg>
							</span>
							<!--end::Svg Icon-->
						</div>
						<!--end::Close-->
					</div>
					<!--begin::Modal header-->
					<!--begin::Modal body-->
					<div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">

						<!--begin::Heading-->
						<div class="mb-13 text-center">
							<!--begin::Title-->
							<h1 class="mb-3">Dosya Arşivi </h1>
							<!--end::Title-->
							<!--begin::Description-->
							<div class="text-muted fw-bold fs-5">Daha fazla alan/bilgi eklemek için
								<a href="#" class="fw-bolder link-primary">Yardım Masasına </a>Başvurabilirsiniz.
							</div>
							<!--end::Description-->
						</div>
						<!--end::Heading-->
						<div class="row g-5">
							<!--begin:Form-->
							<form id="kt_modal_update_dosya_form" class="form" action="#">
								<div id="dosyaBilgileriEdit" class="card shadow-sm card-bordered mb-5">
									<div class="card-header collapsible cursor-pointer rotate" data-bs-toggle="collapse" data-bs-target="#kt_docs_card_update_dosyabilgi">
										<h3 class="card-title">Dosya Ön Bilgiler</h3>
										<div class="card-toolbar rotate-180">
											<span class="svg-icon svg-icon-2">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect opacity="0.3" width="12" height="2" rx="1" transform="matrix(0 -1 -1 0 12.75 19.75)" fill="currentColor" />
													<path d="M12.0573 17.8813L13.5203 16.1256C13.9121 15.6554 14.6232 15.6232 15.056 16.056C15.4457 16.4457 15.4641 17.0716 15.0979 17.4836L12.4974 20.4092C12.0996 20.8567 11.4004 20.8567 11.0026 20.4092L8.40206 17.4836C8.0359 17.0716 8.0543 16.4457 8.44401 16.056C8.87683 15.6232 9.58785 15.6554 9.9797 16.1256L11.4427 17.8813C11.6026 18.0732 11.8974 18.0732 12.0573 17.8813Z" fill="currentColor" />
													<path opacity="0.3" d="M18.75 15.75H17.75C17.1977 15.75 16.75 15.3023 16.75 14.75C16.75 14.1977 17.1977 13.75 17.75 13.75C18.3023 13.75 18.75 13.3023 18.75 12.75V5.75C18.75 5.19771 18.3023 4.75 17.75 4.75L5.75 4.75C5.19772 4.75 4.75 5.19771 4.75 5.75V12.75C4.75 13.3023 5.19771 13.75 5.75 13.75C6.30229 13.75 6.75 14.1977 6.75 14.75C6.75 15.3023 6.30229 15.75 5.75 15.75H4.75C3.64543 15.75 2.75 14.8546 2.75 13.75V4.75C2.75 3.64543 3.64543 2.75 4.75 2.75L18.75 2.75C19.8546 2.75 20.75 3.64543 20.75 4.75V13.75C20.75 14.8546 19.8546 15.75 18.75 15.75Z" fill="currentColor" />
												</svg>
											</span>
										</div>
									</div>
									<div id="kt_docs_card_update_dosyabilgi" class="collapse show">
										<div class="card-body">
											<div class="row g-4 mb-4">
												<!--begin::Col-->
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Arşiv Klasör No</label>
													<input type="text" class="form-control form-control-solid" placeholder="Dosya No" name="edt_d_klasorno" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Kurum Dosya No</label>
													<input type="text" class="form-control form-control-solid"  placeholder="Otomatik Atanacak" name="edt_d_dosyano" />
												</div>
											</div>
											<div class="row g-4 mb-4">
												<!--begin::Label-->
												<div class="col-md fv-row">
													<label class="d-flex align-items-center fs-6 fw-bold mb-2">
														<span class="required">Davacı</span>
													</label>
													<input type="text" class="form-control form-control-solid" id="edt_d_davaci" name="edt_d_davaci" />
												</div>
												<div class="col-md fv-row">
													<label class="d-flex align-items-center fs-6 fw-bold mb-2">
														<span class="required">Davalı</span>
													</label>
													<input type="text" class="form-control form-control-solid" id="edt_d_davali" name="edt_d_davali" />
												</div>
											</div>
											<div class="row g-4 mb-4">
												<!--begin::Col-->
												<div class="col-md fv-row">
													<label class="required fs-6 fw-bold mb-2">Dava Konusu</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_davakonusu" id="edt_d_davakonusu" />
													<!--end::Input-->
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Dava Konusu Açıklama</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_konuaciklamasi" id="edt_d_konuaciklamasi" />
												</div>
											</div>
											<div class="row g-4 mb-4">
												<div class="col-md fv-row">
													<div class="d-flex flex-column mb-3">
														<label class="fs-6 fw-bold mb-2">Proje Bilgisi</label>
														<div type="text" class="border border-gray-600" id="edt_d_projebilgi" name="edt_d_projebilgi"></div>
													</div>
												</div>
												<div class="col-md fv-row">
													<div class="d-flex flex-column mb-3">
														<label class="fs-6 fw-bold mb-2">Mevki Bilgisi</label>
														<div type="text" class="border border-gray-600" id="edt_d_mevkiplaka" name="edt_d_mevkiplaka"></div>
													</div>
												</div>
											</div>
											<div class="row g-4 mb-4">
												<!--begin::Col-->
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">İstinaf Başvuru Tarih ve Bilgisi</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_istinafbilgi" id="edt_d_istinafbilgi" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Temyiz Başvuru Tarih ve Bilgisi</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_temyizbilgi" id="edt_d_temyizbilgi" />
												</div>
												<!--end::Col-->
											</div>
											<div class="row g-4 mb-4">
												<!--begin::Col-->
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Onama İlamı</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_onamailami" id="edt_d_onamailami" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Bozma İlamı </label>
													<input type="text" class="form-control form-control-solid" name="edt_d_bozmailami" />
												</div>
												<!--end::Col-->
											</div>
											<div class="row g-4 mb-4">
												<!--begin::Col-->
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">İstinaf Kabul Kararı Bilgisi</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_istinafkabul" id="edt_d_istinafkabul" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">İstinaf Red Kararı Bilgisi</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_istinafred" id="edt_d_istinafred" />
												</div>
												<!--end::Col-->
											</div>
											<div class="row g-4 mb-4">
												<!--begin::Col-->
												<div class="col-md-2 fv-row">
													<label class="fs-6 fw-bold mb-2">İcra Kayıt No</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_icrano" id="edt_d_icrano" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">İcra</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_icra" id="edt_d_icra" />
												</div>
												<!--end::Col-->
											</div>
											<div class="row g-4 mb-4">
												<!--begin::Col-->
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Karar Kesinleştirme</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_kesinlestirme" id="edt_d_kesinlestirme" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Miras Bel. Tapu Kay. Dzl. ve Kay. Dav.</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_mirascilik" id="edt_d_mirascilik" />
												</div>
												<!--end::Col-->
											</div>
											<div class="row g-4 mb-4">
												<!--begin::Col-->
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">İdari Alacağımız</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_idarialacagi" id="edt_d_idarialacagi" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Vekalet Alacağı</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_vekaletalacagi" id="edt_d_vekaletalacagi" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Yargılama Gideri</label>
													<input type="text" class="form-control form-control-solid" name="edt_d_yargilamagideri" id="edt_d_yargilamagideri" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Tapu Bilgisi</label>
													<select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Lütfen Seçiniz" data-allow-clear="true" data-hide-search="true" data-kt-dosya-add-form="edt_d_tapubilgi" id="edt_d_tapubilgi" name="edt_d_tapubilgi">
														<option value="-1" selected>Belirsiz</option>
														<option value="0">Tapu Yok</option>
														<option value="1">Tapu Var</option>
													</select>
												</div>
												<!--end::Col-->
											</div>
											<div class="row g-4 mb-4">
												<!--begin::Col-->
												<div class="col-md-8 fv-row">
													<div class="d-flex flex-column mb-3">
														<label class="fs-6 fw-bold mb-2">Dosya Açıklamaları</label>
														<div type="text" class="border border-gray-600" id="edt_d_aciklama" name="edt_d_aciklama"></div>
													</div>
												</div>
												<!--end::Col-->
												<!--begin::Col-->
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Etiketler</label>
													<input type="text" class="form-control form-control-lg form-control-solid" id="edt_d_tags" name="edt_d_tags" />
													<div class="pt-3">
														<span class="text-gray-600">Öneri:</span>
														<span class="text-info" id="kt_dosya_edt_suggestions">
															<span class="cursor-pointer" data-kt-suggestion="true">Önemli</span>,
															<span class="cursor-pointer" data-kt-suggestion="true">Acil</span>,
															<span class="cursor-pointer" data-kt-suggestion="true">Eksik</span>,
															<span class="cursor-pointer" data-kt-suggestion="true">Hatırla</span>,
															<span class="cursor-pointer" data-kt-suggestion="true">Taslak</span>
														</span>
													</div>
													<!--end::Input-->
												</div>
												<!--end::Col-->
											</div>
											<div class="text-center">
												<button type="reset" id="kt_modal_update_dosya_cancel" class="btn btn-danger btn-hover-rotate-end me-3">
													<i class="bi bi-x-circle fs-4 me-2"></i>Vazgeç
												</button>
												<button type="submit" id="kt_modal_update_dosya_submit" class="btn btn-primary btn-hover-scale">
													<i class="fas fa-envelope-open-text fs-4 me-2"></i>
													<span class="indicator-label">Dosyayı Güncelle</span>
													<span class="indicator-progress">Lütfen Bekle...
														<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
												</button>
											</div>
										</div>
									</div>
								</div>
							</form>
							<!--end:Form-->
							<!--begin:Form-->
							<form id="kt_modal_update_dosya_mahkeme_form" class="form" action="#">
								<div id="dosyaMahkemeBilgileriEdit" class="card shadow-sm card-bordered mb-5">
									<!-- d-none -->
									<div class="card-header collapsible cursor-pointer rotate" data-bs-toggle="collapse" data-bs-target="#kt_docs_card_update_mahkemebilgi">
										<h3 class="card-title">Mahkeme Bilgileri</h3>
										<div class="card-toolbar rotate-180">
											<span class="svg-icon svg-icon-2">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="currentColor" />
													<path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="currentColor" />
													<path opacity="0.3" d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="currentColor" />
												</svg>
											</span>
										</div>
									</div>
									<div id="kt_docs_card_update_mahkemebilgi" class="collapse show">
										<div class="card-body pb-0">
											<div class="row g-4 mb-4">
												<div class="col-md fv-row">
													<label class="required fs-6 fw-bold mb-2">Açılış Tarihi</label>
													<!--begin::Input-->
													<div class="position-relative d-flex align-items-center">
														<!--begin::Icon-->
														<!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
														<span class="svg-icon svg-icon-2 position-absolute mx-4">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
																<path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
																<path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
														<!--end::Icon-->
														<!--begin::Datepicker-->
														<input class="form-control form-control-solid ps-12" placeholder="Tarih Seç" name="edt_dm_acilistarihi" id="edt_dm_acilistarihi" />
														<!--end::Datepicker-->
													</div>
												</div>

												<div class="col-md-2 fv-row">
													<label class="required fs-6 fw-bold mb-2">Esas No</label>
													<input type="text" class="form-control form-control-solid" name="edt_dm_esasno" />
												</div>
												<div class="col-md fv-row">
													<label class="fs-6 fw-bold mb-2">Karar Tarihi</label>
													<!--begin::Input-->
													<div class="position-relative d-flex align-items-center">
														<!--begin::Icon-->
														<!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
														<span class="svg-icon svg-icon-2 position-absolute mx-4">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
																<path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
																<path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
														<!--end::Icon-->
														<!--begin::Datepicker-->
														<input class="form-control form-control-solid ps-12" placeholder="Tarih Seç" name="edt_dm_karartarihi" id="edt_dm_karartarihi" />
														<!--end::Datepicker-->
													</div>
												</div>
												<div class="col-md-2 fv-row">
													<label class="fs-6 fw-bold mb-2">Karar No</label>
													<input type="text" class="form-control form-control-solid" name="edt_dm_kararno" id="edt_dm_kararno" />
												</div>
												<!--end::Col-->
											</div>
											<div class="row g-4 mb-4">
												<!--begin::Label-->
												<div class="col-md-4 fv-row">
													<label class="d-flex align-items-center fs-6 fw-bold mb-2">
														<span class="required">Mahkeme</span>
													</label>
													
													<select class="form-select form-select-solid fw-bolder filterComboList" data-kt-select2="true" data-placeholder="Lütfen Seçiniz" data-allow-clear="false" data-hide-search="false" data-kt-durusmalar-update-table-filter="filterMahkeme" multiple id="edt_dm_mahkeme">
														<?php foreach(FormSelectMahkemeList() as $sMemurList){ ?>
															<option value="<?php echo $sMemurList["mh_id"];?>"><?php echo trim($sMemurList["mh_name"]);?></option>
														<?php } ?>
													</select>

												</div>
												<div class="col-md-5 fv-row">
													<label class="d-flex align-items-center fs-6 fw-bold mb-2">
														<span class="">Açıklama</span>
													</label>
													<input class="form-control form-control-solid" id="edt_dm_aciklama" name="edt_dm_aciklama" />
												</div>
												<div class="col-md-3 text-end fv-row">
													<button type="button" id="kt_modal_update_dosya_mahkeme_add" class="btn btn-sm btn-success mt-9">
														<i class="fas fa-plus fs-5"></i>
														<span class="indicator-label"></span>
														<span class="indicator-progress">..
															<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
													</button>
													<button type="button" id="kt_modal_update_dosya_mahkeme_delete" class="btn btn-sm btn-danger mt-9 me-2 d-none" disabled>
														<i class="fas fa-trash fs-5"></i>
														<span class="indicator-label"></span>
														<span class="indicator-progress">..
															<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
													</button>
													<button type="submit" id="kt_modal_update_dosya_mahkeme_submit" class="btn btn-sm btn-primary mt-9 d-none" disabled>
														<i class="fas fa-pen fs-5"></i>
														<span class="indicator-label"></span>
														<span class="indicator-progress">..
															<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
													</button>
												</div>
											</div>
										</div>
										<div class="row mx-2">
											<table id="kt_content_dosya_mahkeme_edit_list" class="table align-middle table-row-dashed fs-6 gy-5">
												<thead>
													<tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
														<th class="min-w-200px" data-priority="1">Mahkeme</th>
														<th class="min-w-100px" data-priority="1">Açılış Tarihi</th>
														<th data-priority="1">Esas No</th>
														<th data-priority="1">Karar No</th>
														<th class="min-w-100px" data-priority="1">Karar Tarihi</th>
														<th class="min-w-200px">Açıklama</th>
													</tr>
												</thead>
												<tbody class="text-gray-600 fw-bold"></tbody>
											</table>
										</div>
									</div>
								</div>
							</form>
							<!--end:Form-->
						</div>
					</div>
					<!--end::Modal body-->
				</div>
				<!--end::Modal content-->
			</div>
			<!--end::Modal dialog-->
		</div>
		<!--end::Modal - Update Dosya-->