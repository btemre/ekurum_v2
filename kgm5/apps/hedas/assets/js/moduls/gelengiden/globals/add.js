"use strict";

var ggEvrakAciklama;
// Class definition
var KTModalNewGGiden = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;

	var ggTur;
	var ggKategori;
	var ggKaynak;
	
	var ggDosyaNo;
	var ggSayi;
	var ggTags;
	var gelengiden_evraktarih;
	


	var hostUrl = window.location.host;
	const baseUrlHost = "//"+hostUrl+"/apps/hedas/gelengiden/";
	var modulUrl = window.location.href;
	//console.log("modulUrl", modulUrl, baseUrlHost);



	var postData = {
		route: "addgelengiden",
        ggTur: '',
        ggKategori: '',
        ggKaynak: '',
        ggAciklama: '',
        ggDosyaNo: '',
		ggSayi: '',
		ggEvrakTarih: '',
		ggTags: ''
    };

	// Init form inputs
	var initForm = function() {

		var inputElm = form.querySelector('[name="gelengiden_tags_whitelist"]');
		var gelengiden_tags = new Tagify(form.querySelector('[name="gelengiden_tags"]'), {
			whitelist: (localStorage.hedasGelenGidenTagListData==undefined)? [] : localStorage.hedasGelenGidenTagListData.trim().split(/\s*,\s*/),//inputElm.value.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: true
		});

		gelengiden_tags.on('input', async function (e) {
			gelengiden_tags.settings.whitelist.length = 0; // reset current whitelist
			gelengiden_tags.loading(true).dropdown.hide.call(gelengiden_tags)
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
										gelengiden_tags.settings.whitelist.push(...newWhitelist);
										gelengiden_tags.loading(false).dropdown.show.call(gelengiden_tags, e.detail.value);
									}else{
										gelengiden_tags.loading(false).dropdown.show.call(gelengiden_tags, e.detail.value);
									}
								}else{
									gelengiden_tags.loading(false).dropdown.show.call(gelengiden_tags, e.detail.value);
								}
							}else{
								gelengiden_tags.loading(false).dropdown.show.call(gelengiden_tags, e.detail.value);
							}
						},
						error: function (hata) {
							gelengiden_tags.loading(false).dropdown.show.call(gelengiden_tags, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				gelengiden_tags.loading(false).dropdown.show.call(gelengiden_tags, e.detail.value);
			}
		});

		

	// Suggestions
		var suggestions = document.querySelector('#kt_tagify_custom_suggestions');

		// Suggestion item click
		KTUtil.on(suggestions,  '[data-kt-suggestion="true"]', 'click', function(e) {
			gelengiden_tags.addTags([this.innerText]);
		});


		// var inputElm1 = form.querySelector('[name="gelengiden_kaynak_whitelist"]');
		var gelengiden_kaynak = new Tagify(form.querySelector('[name="gelengiden_kaynak"]'), {
			whitelist: [], //inputElm1.value.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		gelengiden_kaynak.on('input', async function (e) {
			gelengiden_kaynak.settings.whitelist.length = 0; // reset current whitelist
			gelengiden_kaynak.loading(true).dropdown.hide.call(gelengiden_kaynak)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormKaynakSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										gelengiden_kaynak.settings.whitelist.push(...newWhitelist);
										gelengiden_kaynak.loading(false).dropdown.show.call(gelengiden_kaynak, e.detail.value);
									}else{
										gelengiden_kaynak.loading(false).dropdown.show.call(gelengiden_kaynak, e.detail.value);
									}
								}else{
									gelengiden_kaynak.loading(false).dropdown.show.call(gelengiden_kaynak, e.detail.value);
								}
							}else{
								gelengiden_kaynak.loading(false).dropdown.show.call(gelengiden_kaynak, e.detail.value);
							}
						},
						error: function (hata) {
							gelengiden_kaynak.loading(false).dropdown.show.call(gelengiden_kaynak, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				gelengiden_kaynak.loading(false).dropdown.show.call(gelengiden_kaynak, e.detail.value);
			}
		});
	
		gelengiden_kaynak.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('gelengiden_kaynak');
		});

		
		// Due date. For more info, please visit the official plugin site: https://flatpickr.js.org/
		gelengiden_evraktarih = $(form.querySelector('[name="gelengiden_evraktarih"]'));
		gelengiden_evraktarih.flatpickr({
			enableTime: false,
			dateFormat: "d-m-Y",
		});
		gelengiden_evraktarih.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('gelengiden_evraktarih');
		});
		
	
	}


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
					gelengiden_title: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					gelengiden_tur: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					gelengiden_evraktarih: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					gelengiden_kaynak: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					gelengiden_kategori: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					gelengiden_dosyano: {
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



	const handleNewEvent = () => {
		// Action buttons
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();

			// Validate form before submit
			if (validator) {
				validator.validate().then(function (status) {
					//console.log('validated!');

					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple click 
						submitButton.disabled = true;
						//console.log(ggKaynak);
						//console.log(gelengiden_evraktarih[0]._flatpickr.selectedDates);
						//console.log(moment(gelengiden_evraktarih[0]._flatpickr.selectedDates[0]).format('DD-MM-YYYY'));
						postData = {
							route: "addgelengiden",
							ggTur: ggTur.value,
							ggKategori: ggKategori.value,
							ggKaynak: ggKaynak.value,
							ggAciklama: ggEvrakAciklama.root.innerHTML,
							ggDosyaNo: ggDosyaNo.value,
							ggSayi: ggSayi.value,
							ggEvrakTarih: moment(gelengiden_evraktarih[0]._flatpickr.selectedDates[0]).format('DD-MM-YYYY'),
							ggTags: ggTags.value
						};

						//console.log(data);


						setTimeout(function() {
							

							// Enable button
							
							
							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							Swal.fire({
                                text: "Yeni Gelen/Giden Evrak Eklenecek!",
                                icon: "warning",
                                buttonsStyling: false,
                                showCancelButton: true,
                                confirmButtonText: "Tamam",
                                cancelButtonText: "Vazgeç",
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                    cancelButton: "btn btn-danger"
                                }
                            }).then(function (result) {
								if (result.isConfirmed) {
									//Veriyi Kaydetmeyi Onaylarsa Burası Çalışır
									//Veriler Ajax İleJson Post Yapılacak Alınan Cevaba Göre Aksiyon Alınacak
									$.ajax({
                                        type: "POST",
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json",
                                        url: baseUrlHost+"api_newrecord",
                                        data: JSON.stringify(postData),
                                        success: function (response) {
                                            //submitButton.disabled = false;
                                           // console.log(response);

                                            if((typeof response)==="object"){
                                                if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                                                    if(response.code==200){
                                                        Swal.fire({
                                                            text:response.description,
                                                            icon: "success",
                                                            buttonsStyling: false,
                                                            confirmButtonText: "Tamam",
                                                            customClass: {
                                                                confirmButton: "btn btn-primary"
                                                            }
                                                            
                                                        }).then(function (result) {
                                                            if (result.isConfirmed) {
																form.reset();
																if(modulUrl.indexOf('gelengiden')>0){
																	//KTGelenGidenListServerSide.init();
																	$('#filtreleButton').click();
																	
																}
                                                                //window.location.assign(baseUrlHost);
                                                            }
                                                        });
                                                        //Veriler Kaydedildi Sayfayı Yenile
                                                        //submitButton.disabled = false;
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
							submitButton.removeAttribute('data-kt-indicator');
							submitButton.disabled = false;
							//form.submit(); // Submit form
						}, 1000);   						
					} else {
						// Show error message.
						Swal.fire({
							text: "Lütfen form bilgilerini doğru şekilde girdiğinizden emin olunuz.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Tamam",
							customClass: {
								confirmButton: "btn btn-danger"
							}
						});
					}
				});
			}
		});

		cancelButton.addEventListener('click', function (e) {
			e.preventDefault();

			Swal.fire({
                text: "İptal Etmek İstediğinize Emin misiniz?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Kapat",
                cancelButtonText: "Vazgeç",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger"
                }
            }).then(function (result) {
				if (result.value) {
					//form.reset(); // Reset form	
					modal.hide(); // Hide modal				
				}
			});
		});
	}

	
		

	return {
		// Public functions
		init: function () {
			// Elements
			modalEl = document.querySelector('#kt_modal_new_ggevrak');

			if (!modalEl) {
				return;
			}


			modal = new bootstrap.Modal(modalEl);
			
			form = document.querySelector('#kt_modal_new_ggevrak_form');
			submitButton = document.getElementById('kt_modal_new_ggevrak_submit');
			cancelButton = document.getElementById('kt_modal_new_ggevrak_cancel');

			ggTur = form.querySelector('[name="gelengiden_tur"]');
			ggKategori = form.querySelector('[name="gelengiden_kategori"]');
			ggKaynak = form.querySelector('[name="gelengiden_kaynak"]');
			//ggAciklama = form.querySelector('[name="gelengiden_aciklama"]');
			ggDosyaNo = form.querySelector('[name="gelengiden_dosyano"]');
			ggSayi = form.querySelector('[name="gelengiden_sayi"]');
			ggTags = form.querySelector('[name="gelengiden_tags"]');
	
			
			initForm();
			initValidator();
			handleNewEvent();
			
		}
	};
}();


// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTModalNewGGiden.init();
});



ggEvrakAciklama = new Quill('#gelengiden_aciklama', {
	modules: {
		toolbar: [
			[{
				header: [2,3, false]
			}],
			['bold', 'italic', 'underline']
		]
	},
	placeholder: 'Açıklamanızı buraya yazınız...',
	theme: 'snow' // or 'bubble'
});
