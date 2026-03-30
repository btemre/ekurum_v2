"use strict";

var mahkemeList;

// Class definition
var KTModalCezaIptalNewTarget = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;

	var inpDosyaNo;
	var inpItirazEden;
	var inpAcilisTarihi;
	var inpMahkeme;
	var inpDavaKonusu;
	var inpCezaKonusu;
	var inpEsasNo;
	var inpKararNo;
	var inpKararTarihi;
	var inpPlaka;
	var inpSeriNo;
	var inpEvrakDurum;
	var inpIcra;
	var inpAciklama;
	var inpTags;


	var ciKararTarih;
	var ciAcilisTarih;
	var ciKararTarihAddDataP;


	var hostUrl = window.location.host;
	const baseUrlHost = "//"+hostUrl+"/apps/hedas/cezaiptal/";
	var modulUrl = window.location.href;
	//console.log("modulUrl", modulUrl, baseUrlHost);



	var postData = {
		route: "",
        ciDosyaNo: '',
        ciItirazEden: '',
        ciAcilisTarih: '',
        ciMahkeme: '',
        ciDavaKonusu: '',
		ciCezaKonusu: '',
		ciEsasNo: '',
		ciKararNo: '',
		ciKararTarihi: '',
		ciPlaka: '',
		ciSeriNo: '',
		ciEvrakDurum: '',
		ciIcra: '',
		ciAciklama: '',
		ciTags: ''
    };

	// Init form inputs
	var initForm = function() {
		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/

		//var ci_itirazeden = new Tagify(form.querySelector('[name="ci_itirazeden"]') NAME OLARAK DA KULLANILABİLİR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

		var ci_itirazeden = new Tagify(form.querySelector('[name="ci_itirazeden"]'), {
			whitelist: [],//sessionStorage.itirazciListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		ci_itirazeden.on('input', async function (e) {
			ci_itirazeden.settings.whitelist.length = 0; // reset current whitelist
			ci_itirazeden.loading(true).dropdown.hide.call(ci_itirazeden);
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormItirazciSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										ci_itirazeden.settings.whitelist.push(...newWhitelist);
										ci_itirazeden.loading(false).dropdown.show.call(ci_itirazeden, e.detail.value);
									}else{
										ci_itirazeden.loading(false).dropdown.show.call(ci_itirazeden, e.detail.value);
									}
								}else{
									ci_itirazeden.loading(false).dropdown.show.call(ci_itirazeden, e.detail.value);
								}
							}else{
								ci_itirazeden.loading(false).dropdown.show.call(ci_itirazeden, e.detail.value);
							}
						},
						error: function (hata) {
							ci_itirazeden.loading(false).dropdown.show.call(ci_itirazeden, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				ci_itirazeden.loading(false).dropdown.show.call(ci_itirazeden, e.detail.value);
			}
			
		});

		ci_itirazeden.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('ci_itirazeden');
		});


		var ci_mahkeme = new Tagify(form.querySelector('[name="ci_mahkeme"]'), {
			whitelist: [],//sessionStorage.mahkemeData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		ci_mahkeme.on('input', async function (e) {
			ci_mahkeme.settings.whitelist.length = 0; // reset current whitelist
			ci_mahkeme.loading(true).dropdown.hide.call(ci_mahkeme);
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormMahkemeSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										ci_mahkeme.settings.whitelist.push(...newWhitelist);
										ci_mahkeme.loading(false).dropdown.show.call(ci_mahkeme, e.detail.value);
									}else{
										ci_mahkeme.loading(false).dropdown.show.call(ci_mahkeme, e.detail.value);
									}
								}else{
									ci_mahkeme.loading(false).dropdown.show.call(ci_mahkeme, e.detail.value);
								}
							}else{
								ci_mahkeme.loading(false).dropdown.show.call(ci_mahkeme, e.detail.value);
							}
						},
						error: function (hata) {
							ci_mahkeme.loading(false).dropdown.show.call(ci_mahkeme, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				ci_mahkeme.loading(false).dropdown.show.call(ci_mahkeme, e.detail.value);
			}
			
		});

		ci_mahkeme.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('ci_mahkeme');
		});


		var ci_davakonusu = new Tagify(form.querySelector('[name="ci_davakonusu"]'), {
			whitelist: [],//sessionStorage.davakonuListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		ci_davakonusu.on('input', async function (e) {
			ci_davakonusu.settings.whitelist.length = 0; // reset current whitelist
			ci_davakonusu.loading(true).dropdown.hide.call(ci_davakonusu);
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormDavaKonusuSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										ci_davakonusu.settings.whitelist.push(...newWhitelist);
										ci_davakonusu.loading(false).dropdown.show.call(ci_davakonusu, e.detail.value);
									}else{
										ci_davakonusu.loading(false).dropdown.show.call(ci_davakonusu, e.detail.value);
									}
								}else{
									ci_davakonusu.loading(false).dropdown.show.call(ci_davakonusu, e.detail.value);
								}
							}else{
								ci_davakonusu.loading(false).dropdown.show.call(ci_davakonusu, e.detail.value);
							}
						},
						error: function (hata) {
							ci_davakonusu.loading(false).dropdown.show.call(ci_davakonusu, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				ci_davakonusu.loading(false).dropdown.show.call(ci_davakonusu, e.detail.value);
			}
			
		});

		ci_davakonusu.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('ci_davakonusu');
		});


		var ci_cezakonu = new Tagify(form.querySelector('[name="ci_cezakonu"]'), {
			whitelist: [],//sessionStorage.cezakonuListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		ci_cezakonu.on('input', async function (e) {
			ci_cezakonu.settings.whitelist.length = 0; // reset current whitelist
			ci_cezakonu.loading(true).dropdown.hide.call(ci_cezakonu);
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormCezaKonusuSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										ci_cezakonu.settings.whitelist.push(...newWhitelist);
										ci_cezakonu.loading(false).dropdown.show.call(ci_cezakonu, e.detail.value);
									}else{
										ci_cezakonu.loading(false).dropdown.show.call(ci_cezakonu, e.detail.value);
									}
								}else{
									ci_cezakonu.loading(false).dropdown.show.call(ci_cezakonu, e.detail.value);
								}
							}else{
								ci_cezakonu.loading(false).dropdown.show.call(ci_cezakonu, e.detail.value);
							}
						},
						error: function (hata) {
							ci_cezakonu.loading(false).dropdown.show.call(ci_cezakonu, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				ci_cezakonu.loading(false).dropdown.show.call(ci_cezakonu, e.detail.value);
			}
			
		});

		ci_cezakonu.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('ci_cezakonu');
		});


		var ci_plaka = new Tagify(form.querySelector('[name="ci_plaka"]'), {
			whitelist: [],//sessionStorage.plakaListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		ci_plaka.on('input', async function (e) {
			ci_plaka.settings.whitelist.length = 0; // reset current whitelist
			ci_plaka.loading(true).dropdown.hide.call(ci_plaka);
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormPlakaSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										ci_plaka.settings.whitelist.push(...newWhitelist);
										ci_plaka.loading(false).dropdown.show.call(ci_plaka, e.detail.value);
									}else{
										ci_plaka.loading(false).dropdown.show.call(ci_plaka, e.detail.value);
									}
								}else{
									ci_plaka.loading(false).dropdown.show.call(ci_plaka, e.detail.value);
								}
							}else{
								ci_plaka.loading(false).dropdown.show.call(ci_plaka, e.detail.value);
							}
						},
						error: function (hata) {
							ci_plaka.loading(false).dropdown.show.call(ci_plaka, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				ci_plaka.loading(false).dropdown.show.call(ci_plaka, e.detail.value);
			}
			
		});



		var ci_icra = new Tagify(form.querySelector('[name="ci_icra"]'), {
			whitelist: [],//sessionStorage.mahkemeData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		ci_icra.on('input', async function (e) {
			ci_icra.settings.whitelist.length = 0; // reset current whitelist
			ci_icra.loading(true).dropdown.hide.call(ci_icra);
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormMahkemeSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										ci_icra.settings.whitelist.push(...newWhitelist);
										ci_icra.loading(false).dropdown.show.call(ci_icra, e.detail.value);
									}else{
										ci_icra.loading(false).dropdown.show.call(ci_icra, e.detail.value);
									}
								}else{
									ci_icra.loading(false).dropdown.show.call(ci_icra, e.detail.value);
								}
							}else{
								ci_icra.loading(false).dropdown.show.call(ci_icra, e.detail.value);
							}
						},
						error: function (hata) {
							ci_icra.loading(false).dropdown.show.call(ci_icra, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				ci_icra.loading(false).dropdown.show.call(ci_icra, e.detail.value);
			}
			
		});




		// Due date. For more info, please visit the official plugin site: https://flatpickr.js.org/
		ciAcilisTarih = $(form.querySelector('[name="ci_acilistarihi"]'));
		ciAcilisTarih.flatpickr({
			enableTime: false,
			dateFormat: "d-m-Y",
		});

		ciKararTarih = $(form.querySelector('[name="ci_karartarihi"]'));
		ciKararTarih.flatpickr({
			enableTime: false,
			dateFormat: "d-m-Y",
		});


		// Team assign. For more info, plase visit the official plugin site: https://select2.org/
        //$(form.querySelector('[name="team_assign"]')).on('change', function() {
            // Revalidate the field when an option is chosen
            //validator.revalidateField('team_assign');
       // });
		var input = document.querySelector('#ci_tags'),
		// Init Tagify script on the above inputs
		tagify = new Tagify(input, {
			whitelist: (localStorage.hedasCezaIptalTagListData==undefined)? [] : localStorage.hedasCezaIptalTagListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: true
		});
		tagify.on('input', async function (e) {
			tagify.settings.whitelist.length = 0; // reset current whitelist
			tagify.loading(true).dropdown.hide.call(tagify)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormTagSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										tagify.settings.whitelist.push(...newWhitelist);
										tagify.loading(false).dropdown.show.call(tagify, e.detail.value);
									}else{
										tagify.loading(false).dropdown.show.call(tagify, e.detail.value);
									}
								}else{
									tagify.loading(false).dropdown.show.call(tagify, e.detail.value);
								}
							}else{
								tagify.loading(false).dropdown.show.call(tagify, e.detail.value);
							}
						},
						error: function (hata) {
							tagify.loading(false).dropdown.show.call(tagify, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				tagify.loading(false).dropdown.show.call(tagify, e.detail.value);
			}
		});

		// Suggestions
		var suggestions = document.querySelector('#kt_cezaiptal_tags_suggests');

		// Suggestion item click
		KTUtil.on(suggestions,  '[data-kt-suggestion="true"]', 'click', function(e) {
			tagify.addTags([this.innerText]);
		});
		
	}

//----function bitişş----------


	// Handle form validation and submittion
	var initValidator = function() {
		// Stepper custom navigation
		
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validator = FormValidation.formValidation(
			form,
			{
				framework: 'bootstrap',
				icon: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					ci_itirazeden: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					ci_mahkeme: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					ci_acilistarihi: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					ci_esasno: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					ci_dosyano: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					ci_cezakonu: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					ci_davakonusu: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					}
				},
			plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap5({
						rowSelector: '.fv-row',
						eleInvalidClass: '',
						eleValidClass: ''
					})
				}
			}
		);
	}



	// Handle form validation and submittion
	var handleForm = function() {
		// Stepper custom navigation

		// Action buttons
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();

			// Validate form before submit
			if (validator) {
				validator.validate().then(function (status) {
					console.log('validated!');

					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple click 
						submitButton.disabled = true;

						setTimeout(function() {
							submitButton.removeAttribute('data-kt-indicator');

							// Enable button
							submitButton.disabled = false;
							
							postData = {
								route: "addcezaiptal",
								ciDosyaNo: inpDosyaNo.value,
								ciItirazEden: inpItirazEden.value,
								ciAcilisTarih: moment(ciAcilisTarih[0]._flatpickr.selectedDates[0]).format('DD-MM-YYYY'),
								ciMahkeme: inpMahkeme.value,
								ciDavaKonusu: inpDavaKonusu.value,
								ciCezaKonusu: inpCezaKonusu.value,
								ciEsasNo: inpEsasNo.value,
								ciKararNo: inpKararNo.value,
								ciKararTarihi: ciKararTarihAddDataP.value,//moment(ciKararTarih[0]._flatpickr.selectedDates[0]).format('DD-MM-YYYY'),
								ciPlaka: inpPlaka.value,
								ciSeriNo: inpSeriNo.value,
								ciEvrakDurum: inpEvrakDurum.value,
								ciIcra: inpIcra.value,
								ciAciklama: inpAciklama.value,
								ciTags: inpTags.value
							};
						
							// console.log("postData",postData);
							
							
							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							Swal.fire({
								text: "Yeni Kayıt Oluşturulacak!",
								icon: "info",
								buttonsStyling: false,
								confirmButtonText: "Tamam",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							}).then(function (result) {
								if (result.isConfirmed) {
									$.ajax({
                                        type: "POST",
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json",
                                        url: baseUrlHost+"api_newrecord",
                                        data: JSON.stringify(postData),
                                        success: function (response) {

                                            if((typeof response)==="object"){
                                                if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                                                    if(response.code==200){

														toastrAlert('success','Başarılı!',response.description);
														form.reset();
														location.reload();//sayfa yenilenerek dosyasira otomatik atması sağlandı
														if(modulUrl.indexOf('cezaiptal')>0){
															KTCezaIptalListServerSide.reload();
														}
                                                    }else if(response.code==406 && response.error==true){
                                                        status = "Invalid";
                                                        Swal.fire({
                                                            text:response.description,
                                                            icon: "error",
                                                            buttonsStyling: false,
                                                            confirmButtonText: "Tamam",
                                                            customClass: {
                                                                confirmButton: "btn btn-danger"
                                                            }
                                                        });
                                                        //Form verilerindeki hataları bas
                                                        submitButton.disabled = false;
                                                    }else{
                                                        Swal.fire({
                                                            text:response.description,
                                                            icon: "error",
                                                            buttonsStyling: false,
                                                            confirmButtonText: "Tamam",
                                                            customClass: {
                                                                confirmButton: "btn btn-danger"
                                                            }
                                                        });
                                                        submitButton.disabled = false;
                                                    }
                                                }else{
                                                    Swal.fire({
                                                        text: "Hata! İşlem durumu bilgisi alınamadı. Lütfen sistem yöneticinize Başvurunuz",
                                                        icon: "error",
                                                        buttonsStyling: false,
                                                        confirmButtonText: "Tamam",
                                                        customClass: {
                                                            confirmButton: "btn btn-danger"
                                                        }
                                                    });
                                                    submitButton.disabled = false;
                                                }
                                            }else{
                                                Swal.fire({
                                                    text: "Hata! İşlem durumu kontrol edilemedi. Lütfen sistem yöneticinize Başvurunuz",
                                                    icon: "error",
                                                    buttonsStyling: false,
                                                    confirmButtonText: "Tamam",
                                                    customClass: {
                                                        confirmButton: "btn btn-danger"
                                                    }
                                                });
                                                submitButton.disabled = false;
                                            }



										},
                                        error: function (hata) {
                                            Swal.fire({
                                                text: "Hata! İşlem Başarısız. Lütfen Sistem Yöneticinize Başvurunuz.",
                                                icon: "error",
                                                buttonsStyling: false,
                                                confirmButtonText: "Tamam",
                                                customClass: {
                                                    confirmButton: "btn btn-danger"
                                                }
                                            });
                                            submitButton.disabled = false;

                                            //console.log(hata);
                                        }
									});
									
									//modal.hide();
								}
							});

							//form.submit(); // Submit form
						}, 2000);   						
					} else {
						// Show error message.
						Swal.fire({
							text: "Maalesef bazı hatalar tespit edildi, lütfen tekrar deneyin.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Tamam!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});
					}
				});
			}
		});

		cancelButton.addEventListener('click', function (e) {
			e.preventDefault();

			Swal.fire({
				text: "İptal etmek istediğinizden emin misiniz?",
				icon: "warning",
				showCancelButton: true,
				buttonsStyling: false,
				confirmButtonText: "Evet, iptal et!",
				cancelButtonText: "Hayır, geri dön",
				customClass: {
					confirmButton: "btn btn-primary",
					cancelButton: "btn btn-active-light"
				}
			}).then(function (result) {
				if (result.value) {
					form.reset(); // Reset form	
					modal.hide(); // Hide modal				
				}
			});
		});
	}

	return {
		// Public functions
		init: function () {
			// Elements
			modalEl = document.querySelector('#kt_modal_new_cezaiptal');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#kt_modal_new_cezaiptal_form');
			submitButton = document.getElementById('kt_modal_new_cezaiptal_submit');
			cancelButton = document.getElementById('kt_modal_new_cezaiptal_cancel');

			inpDosyaNo = form.querySelector('[name="ci_dosyano"]');
			inpItirazEden = form.querySelector('[name="ci_itirazeden"]');
			//inpAcilisTarihi = form.querySelector('[name="gelengiden_tur"]');
			inpMahkeme = form.querySelector('[name="ci_mahkeme"]');
			inpDavaKonusu = form.querySelector('[name="ci_davakonusu"]');
			inpCezaKonusu = form.querySelector('[name="ci_cezakonu"]');
			inpEsasNo = form.querySelector('[name="ci_esasno"]');
			inpKararNo = form.querySelector('[name="ci_kararno"]');
			//inpKararTarihi = form.querySelector('[name="gelengiden_tur"]');
			inpPlaka = form.querySelector('[name="ci_plaka"]');
			inpSeriNo = form.querySelector('[name="ci_serino"]');
			inpEvrakDurum = form.querySelector('[name="ci_evrakdurum"]');
			inpIcra = form.querySelector('[name="ci_icra"]');
			inpAciklama = form.querySelector('[name="ci_aciklama"]');
			inpTags = form.querySelector('[name="ci_tags"]');
		
			ciKararTarihAddDataP = form.querySelector('#ci_karartarihi');

			initForm();
			initValidator();
			handleForm();
		}
	};

}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTModalCezaIptalNewTarget.init();
});
