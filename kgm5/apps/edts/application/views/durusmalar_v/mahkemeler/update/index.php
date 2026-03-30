		<!--begin::Modal - Edit GelenGiden Evrak-->
		<div class="modal fade" id="kt_modal_edit_ggevrak" tabindex="-1" aria-hidden="true">
			<!--begin::Modal dialog-->
			<div class="modal-dialog modal-dialog-centered mw-900px">
				<!--begin::Modal content-->
				<div class="modal-content rounded">
					<!--begin::Modal header-->
					<div class="modal-header pb-0 border-0 justify-content-end">
						<!--begin::Close-->
						<div class="btn btn-sm btn-icon btn-active-color-primary" id="kt_modal_edit_ggevrak_close">
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
						<!--begin:Form-->
						<form id="kt_modal_edit_ggevrak_form" class="form" action="#">
							<!--begin::Heading-->
							<div class="mb-13 text-center">
								<!--begin::Title-->
								<h1 class="mb-3" data-kt-reminder="title">Mahkeme Ekle/Düzenle</h1>
								<!--end::Title-->
								<!--begin::Description-->
								<div class="text-muted fw-bold fs-5">Daha fazla alan/bilgi eklemek için
									<a href="#" class="fw-bolder link-primary">Yardım Masasına </a>Başvurabilirsiniz.
								</div>
								<!--end::Description-->
							</div>
							
							
								

									<input value="" name="mh_id" id="mh_id" type="hidden"/>
							
							<div class="row g-9 mb-8">
								<!--begin::Col-->
								<div class="col-md-12 fv-row">
									<label class="required fs-6 fw-bold mb-2">Mahkeme Adı</label>
									<input class="form-control form-control-solid" value="" name="mh_name" id="mh_name" type="text"/>
								</div>
								<!--end::Col-->

							</div>							
							<!--end::Heading-->
							
								<!--begin::Actions-->
								<div class="text-center">
									<button type="reset" id="kt_modal_edit_ggevrak_cancel" class="btn btn-danger btn-hover-rotate-end me-3">
										<i class="bi bi-x-circle fs-4 me-2"></i>Vazgeç
									</button>
									<button type="submit" id="kt_modal_edit_ggevrak_submit" class="btn btn-primary btn-hover-scale">
										<i class="fas fa-envelope-open-text fs-4 me-2"></i>
										<span class="indicator-label">Güncelle</span>
										<span class="indicator-progress">Lütfen Bekle...
											<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
									</button>
								</div>
								<!--end::Actions-->
							</div>
						</form>
						<!--end:Form-->
					</div>
					<!--end::Modal body-->
				</div>
				<!--end::Modal content-->
			</div>
			<!--end::Modal dialog-->
		</div>
		<!--end::Modal - Edit GelenGiden Evrak-->