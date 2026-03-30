"use strict";

var inpAddDurusmalarDTarihiFP;
var inpAddDurusmalarDTarihiDP;

// Class definition
var KTModalNewDurusmalarManuel = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;

	var tabButton = false;

	var hostUrl = window.location.host;
	const baseUrlHost = "//"+hostUrl+"/apps/edts/durusmalar/";
	var modulUrl = window.location.href;

	var inpDosyaNo;
	var inpDosyaTuru;
	var inpDurusmaTarihi;

	var inpEsasNo;
	var inpAvukat;
	var inpIlgiliAvukat;
	var inpTaraf;
	var inpIslem;
	var inpTarafBilgisi;
	var inpAciklama;
	var inpIlgiliMemur;
	var inpEtiket;
	var inpDurusmaIslemi;
	var inpTutanakBilgi;
	
	var postData = {
		route: "adddurusmamanuel",
		edtsTutanakDurum:0,
		edtsDosyaNo: "",
		edtsDosyaTur: "",
		edtsDurusmaTarihi: "",
		edtsMahkeme: "",
		edtsEsasNo: "",
		edtsAvukat: "",
		edtsIlgiliAvukat: "",
		edtsTaraf: "",
		edtsIslem: "",
		edtsTarafBilgisi: "",
		edtsAciklama: "",
		edtsIlgiliMemur: "",
		edtsEtiket: "",
		edtsDurusmaIslemi: 0,
	}

	// Init form inputs
	var initForm = function() {
		// Form veya gerekli elementler yoksa (farklı sayfa/bağlam) çık
		if (!form || !form.querySelector('[name="dm_dosyaturu"]')) return;
		
		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/


		$(form.querySelector('[name="dm_avukat"]')).on('change', function () {
			// Revalidate the field when an option is chosen
			validator.revalidateField('dm_avukat');
		});

		
		$(form.querySelector('[name="dm_taraf"]')).on('change', function () {
			// Revalidate the field when an option is chosen
			validator.revalidateField('dm_taraf');
		});

		var dm_dosyaturuEl = form.querySelector('[name="dm_dosyaturu"]');
		var dm_dosyaturu = dm_dosyaturuEl ? new Tagify(dm_dosyaturuEl, {
			whitelist: [],
			placeholder: "Yazınız",
			enforceWhitelist: false
		}) : null;
		if (dm_dosyaturu) dm_dosyaturu.on('input', async function (e) {
			dm_dosyaturu.settings.whitelist.length = 0; // reset current whitelist
			dm_dosyaturu.loading(true).dropdown.hide.call(dm_dosyaturu)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormDosyaTuruSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										dm_dosyaturu.settings.whitelist.push(...newWhitelist);
										dm_dosyaturu.loading(false).dropdown.show.call(dm_dosyaturu, e.detail.value);
									}else{
										dm_dosyaturu.loading(false).dropdown.show.call(dm_dosyaturu, e.detail.value);
									}
								}else{
									dm_dosyaturu.loading(false).dropdown.show.call(dm_dosyaturu, e.detail.value);
								}
							}else{
								dm_dosyaturu.loading(false).dropdown.show.call(dm_dosyaturu, e.detail.value);
							}
						},
						error: function (hata) {
							dm_dosyaturu.loading(false).dropdown.show.call(dm_dosyaturu, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				dm_dosyaturu.loading(false).dropdown.show.call(dm_dosyaturu, e.detail.value);
			}
		});

		if (dm_dosyaturu) dm_dosyaturu.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('dm_dosyaturu');
		});


		var dm_mahkemeEl = form.querySelector('[name="dm_mahkeme"]');
		var dm_mahkeme = dm_mahkemeEl ? new Tagify(dm_mahkemeEl, {
			whitelist: [],//sessionStorage.mahkemeData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		}) : null;
		if (dm_mahkeme) dm_mahkeme.on('input', async function (e) {
			dm_mahkeme.settings.whitelist.length = 0; // reset current whitelist
			dm_mahkeme.loading(true).dropdown.hide.call(dm_mahkeme);
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
										dm_mahkeme.settings.whitelist.push(...newWhitelist);
										dm_mahkeme.loading(false).dropdown.show.call(dm_mahkeme, e.detail.value);
									}else{
										dm_mahkeme.loading(false).dropdown.show.call(dm_mahkeme, e.detail.value);
									}
								}else{
									dm_mahkeme.loading(false).dropdown.show.call(dm_mahkeme, e.detail.value);
								}
							}else{
								dm_mahkeme.loading(false).dropdown.show.call(dm_mahkeme, e.detail.value);
							}
						},
						error: function (hata) {
							dm_mahkeme.loading(false).dropdown.show.call(dm_mahkeme, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				dm_mahkeme.loading(false).dropdown.show.call(dm_mahkeme, e.detail.value);
			}
			
		});

		if (dm_mahkeme) dm_mahkeme.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('dm_mahkeme');
		});

		var dm_ilgiliavukatEl = form.querySelector('[name="dm_ilgiliavukat"]');
		var dm_ilgiliavukat = dm_ilgiliavukatEl ? new Tagify(dm_ilgiliavukatEl, {
			whitelist: localStorage.edtsDurusmalarIlgiliAvukatListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: true,
			dropdown: {
				maxItems: 20,           // <- mixumum allowed rendered suggestions
				classname: "dm_ilgiliavukat__suggestions", // <- custom classname for this dropdown, so it could be targeted
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		}) : null;

		if (dm_ilgiliavukat) dm_ilgiliavukat.on('input', async function (e) {
			dm_ilgiliavukat.settings.whitelist.length = 0; // reset current whitelist
			dm_ilgiliavukat.loading(true).dropdown.hide.call(dm_ilgiliavukat)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormIlgiliAvukatSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										localStorage.edtsDurusmalarIlgiliAvukatListData = response.data.trim();
										var newWhitelist = localStorage.edtsDurusmalarIlgiliAvukatListData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										//var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										//localStorage.cekasGelenGidenHavaleKisiListData = newWhitelist
										dm_ilgiliavukat.settings.whitelist.push(...newWhitelist);
										dm_ilgiliavukat.loading(false).dropdown.show.call(dm_ilgiliavukat, e.detail.value);
									}else{
										dm_ilgiliavukat.loading(false).dropdown.show.call(dm_ilgiliavukat, e.detail.value);
									}
								}else{
									dm_ilgiliavukat.loading(false).dropdown.show.call(dm_ilgiliavukat, e.detail.value);
								}
							}else{
								dm_ilgiliavukat.loading(false).dropdown.show.call(dm_ilgiliavukat, e.detail.value);
							}
						},
						error: function (hata) {
							dm_ilgiliavukat.loading(false).dropdown.show.call(dm_ilgiliavukat, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				dm_ilgiliavukat.loading(false).dropdown.show.call(dm_ilgiliavukat, e.detail.value);
			}
		});




		var dm_ilgilimemurEl = form.querySelector('[name="dm_ilgilimemur"]');
		var dm_ilgilimemur = dm_ilgilimemurEl ? new Tagify(dm_ilgilimemurEl, {
			whitelist: localStorage.edtsDurusmalarIlgiliMemurListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: true,
			dropdown: {
				maxItems: 20,           // <- mixumum allowed rendered suggestions
				classname: "dm_ilgilimemur__suggestions", // <- custom classname for this dropdown, so it could be targeted
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		}) : null;

		if (dm_ilgilimemur) dm_ilgilimemur.on('input', async function (e) {
			dm_ilgilimemur.settings.whitelist.length = 0; // reset current whitelist
			dm_ilgilimemur.loading(true).dropdown.hide.call(dm_ilgilimemur)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormIlgiliMemurSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										localStorage.edtsDurusmalarIlgiliMemurListData = response.data.trim();
										var newWhitelist = localStorage.edtsDurusmalarIlgiliMemurListData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										//var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										//localStorage.cekasGelenGidenHavaleKisiListData = newWhitelist
										dm_ilgilimemur.settings.whitelist.push(...newWhitelist);
										dm_ilgilimemur.loading(false).dropdown.show.call(dm_ilgilimemur, e.detail.value);
									}else{
										dm_ilgilimemur.loading(false).dropdown.show.call(dm_ilgilimemur, e.detail.value);
									}
								}else{
									dm_ilgilimemur.loading(false).dropdown.show.call(dm_ilgilimemur, e.detail.value);
								}
							}else{
								dm_ilgilimemur.loading(false).dropdown.show.call(dm_ilgilimemur, e.detail.value);
							}
						},
						error: function (hata) {
							dm_ilgilimemur.loading(false).dropdown.show.call(dm_ilgilimemur, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				dm_ilgilimemur.loading(false).dropdown.show.call(dm_ilgilimemur, e.detail.value);
			}
		});



		var dm_islemEl = form.querySelector('[name="dm_islem"]');
		var dm_islem = dm_islemEl ? new Tagify(dm_islemEl, {
			whitelist: ["Duruşma","İstinaf","Keşif","Karar","Red","Birleşti","Kaldırıldı"],
			placeholder: "Yazınız",
			enforceWhitelist: true,
			dropdown: {
				maxItems: 20,           // <- mixumum allowed rendered suggestions
				classname: "dm_islem__suggestions", // <- custom classname for this dropdown, so it could be targeted
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		}) : null;



		inpAddDurusmalarDTarihiFP = flatpickr(inpAddDurusmalarDTarihiDP, {
			enableTime: true,
			time_24hr: true,
			dateFormat: "d-m-Y H:i",
			minuteIncrement: 1,
			static: true,
			allowInput: true, // Tarih alanına klavye ile girişe izin verir
			clickOpens: true, // Tarih alanı seçildiğinde klavye girişine izin verir
			//defaultDate: moment().startOf('day').format('DD-MM-YYYY HH:mm'),
			onReady: function(selectedDates, dateStr, instance) {
				instance.input.addEventListener('input', function(event) {
					var input = event.target;
					var value = input.value.replace(/\D/g, ''); // Sadece rakamları alır
					if (value.length >= 12) {
						var formattedValue = value.replace(/(\d{2})(\d{2})(\d{4})(\d{2})(\d{2})/, '$1-$2-$3 $4:$5');
						instance.setDate(formattedValue, false); // false parametresiyle değer değişim olayı tetiklenmez
					}
				});
			}
		});
		




		var input = document.querySelector('#dm_etiket');
		// Init Tagify script on the above inputs
		var dm_etiket = input ? new Tagify(input, {
			whitelist: ["Önemli","Acil","Eksik","Hatırla","Taslak","Silinecek"],
			placeholder: "Yazınız",
			enforceWhitelist: true
		}) : null;
	
		// Suggestions
		var suggestions = document.querySelector('#kt_tagify_etiket_custom_suggestions');
	
		// Suggestion item click
		if (suggestions && dm_etiket) KTUtil.on(suggestions,  '[data-kt-suggestion="true"]', 'click', function(e) {
			dm_etiket.addTags([this.innerText]);
		});
	
		// tabButton = submitButton;

	}

	var initValidator = function(){
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validator = FormValidation.formValidation(
			form,
			{
				fields: {
					dm_dosyano: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					dm_mahkeme: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					dm_durusmatarihi: {
						validators: {
							date: {
								format: 'DD-MM-YYYY HH:mm',
								message: 'Geçerli Tarih ve Saat Giriniz',
							},
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					dm_esasno: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					dm_avukat: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							},
							callback: {
								message: 'Geçerli bir Avukat seçilmedi',
								callback: function (input) {
									// Get the selected options
									const options = input.select2('data');
									return options != null && options.length >= 1 && options.length <= 99;
								}
							}

						}
					},
					dm_taraf: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							},
							callback: {
								message: 'Geçerli bir seçenek seçilmedi',
								callback: function (input) {
									// Get the selected options
									const options = input.select2('data');
									return options != null && options.length >= 1 && options.length <= 99;
								}
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
							
							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							Swal.fire({
								text: "Form başarıyla gönderildi!",
								icon: "success",
								buttonsStyling: false,
								confirmButtonText: "Tamam",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							}).then(function (result) {
								if (result.isConfirmed) {
									modal.hide();
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

	
	const handleNewEvent = () => {

		// Action buttons
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();
			tabButton = false;
			console.log('[Durusmalar] Kaydet butonu tıklandı (Yeni Kayıt formu).');
			// Validate form before submit
			if (validator) {
				validator.validate().then(function (status) {
					console.log('[Durusmalar] Yeni kayıt form validasyonu:', status);
					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple cligck 
						submitButton.disabled = true;
						

						postData.route = "adddurusmamanuel";
						postData.edtsDosyaNo = inpDosyaNo.value;
						postData.edtsDosyaTur = inpDosyaTuru.value;
						postData.edtsDurusmaTarihi = inpAddDurusmalarDTarihiDP.value;
						postData.edtsMahkeme = jQuery("#dm_mahkememulti").select2('val').toString();

						postData.edtsEsasNo = inpEsasNo.value;
						postData.edtsAvukat = inpAvukat.value;
						postData.edtsIlgiliAvukat = inpIlgiliAvukat.value;
						postData.edtsTaraf = inpTaraf.value;
						postData.edtsIslem = inpIslem.value;
						postData.edtsTarafBilgisi = inpTarafBilgisi.value;
						postData.edtsAciklama = inpAciklama.value;
						postData.edtsIlgiliMemur = inpIlgiliMemur.value;
						postData.edtsEtiket = inpEtiket.value;
						postData.edtsDurusmaIslemi = inpDurusmaIslemi.value;
						
						// console.log(inpAddDurusmalarDTarihiDP.value,postData);

						
						setTimeout(function() {
							submitButton.removeAttribute('data-kt-indicator');


							// Enable button
							submitButton.disabled = false;

							
							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							Swal.fire({
                                text: "Yeni Kayıt Eklenecek!",
                                icon: "info",
                                buttonsStyling: false,
                                showCancelButton: true,
                                confirmButtonText: "Tamam",
                                cancelButtonText: "Vazgeç",
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                    cancelButton: "btn btn-danger"
                                }
							}).then(function (result) {
								tabButton = $('.swal2-confirm');
								if (result.isConfirmed) {
									console.log('[Durusmalar] api_newrecord çağrılıyor. Payload:', postData);
									$.ajax({
                                        type: "POST",
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json",
                                        url: baseUrlHost+"api_newrecord",
                                        data: JSON.stringify(postData),
                                        success: function (response) {
                                            console.log('[Durusmalar] api_newrecord yanıtı:', response);

                                            if((typeof response)==="object"){
                                                if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                                                    if(response.code==200){
                                                        console.log('[Durusmalar] Yeni kayıt başarılı (code=200).');
                                                        Swal.fire({
                                                            text:response.description,
                                                            icon: "success",
                                                            buttonsStyling: false,
                                                            confirmButtonText: "Tamam",
                                                            customClass: {
                                                                confirmButton: "btn btn-primary"
                                                            }
                                                            
                                                        }).then(function (result) {
															tabButton = $('.swal2-confirm');
                                                            if (result.isConfirmed) {
																if(modulUrl.indexOf('durusmalar')>0){
																	//KTGelenGidenListServerSide.init();
                                                                    //$('#filtreleButton').click();
                                                                    
                                                                    
                                                                }
																location.href='/apps/edts/durusmalar'
																$('#dm_avukat').val('0').trigger('change');
																$('#dm_taraf').val('0').trigger('change');
																form.reset();
                                                                submitButton.disabled = false;
																tabButton = submitButton;
                                                                //modal.hide();
                                                                //window.location.assign(baseUrlHost);
                                                            }
                                                        });
                                                        //Veriler Kaydedildi Sayfayı Yenile
                                                        //submitButton.disabled = false;
                                                    }else if(response.code==406 && response.error==true){
                                                        console.warn('[Durusmalar] Yeni kayıt validasyon hatası (code=406):', response.description);
                                                        status = "Invalid";
                                                        Swal.fire({
                                                            text:response.description,
                                                            icon: "error",
                                                            buttonsStyling: false,
                                                            confirmButtonText: "Tamam",
                                                            customClass: {
                                                                confirmButton: "btn btn-danger"
                                                            }
                                                        }).then(function (result) {
															tabButton = $('.swal2-confirm');
                                                            if (result.isConfirmed) {
																tabButton = submitButton;
															}
														});
                                                        submitButton.disabled = false;
														
                                                    }else{
                                                        console.error('[Durusmalar] Yeni kayıt hatası (code=' + (response.code || '?') + '):', response.description, response);
                                                        if (response.debug) {
                                                            console.error('[Durusmalar] DB hata detayı:', response.debug);
                                                        }
                                                        Swal.fire({
                                                            text:response.description,
                                                            icon: "error",
                                                            buttonsStyling: false,
                                                            confirmButtonText: "Tamam",
                                                            customClass: {
                                                                confirmButton: "btn btn-danger"
                                                            }
                                                        }).then(function (result) {
															tabButton = $('.swal2-confirm');
                                                            if (result.isConfirmed) {
																tabButton = submitButton;
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
													tabButton = $('.swal2-confirm');
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
												tabButton = $('.swal2-confirm');
                                            }



										},
                                        error: function (xhr, status, error) {
                                            console.error('[Durusmalar] api_newrecord AJAX hatası:', {
                                                status: status,
                                                error: error,
                                                statusCode: xhr && xhr.status,
                                                responseText: xhr && xhr.responseText,
                                                xhr: xhr
                                            });
                                            Swal.fire({
                                                text: "Hata! İşlem Başarısız. Lütfen Sistem Yöneticinize Başvurunuz.",
                                                icon: "error",
                                                buttonsStyling: false,
                                                confirmButtonText: "Tamam",
                                                customClass: {
                                                    confirmButton: "btn btn-danger"
                                                }
                                            }).then(function (result) {
												tabButton = $('.swal2-confirm');
												if (result.isConfirmed) {
													tabButton = submitButton;
												}
											});
                                            submitButton.disabled = false;
                                        }
									});
									//modal.hide();
								}else{
									tabButton = submitButton;
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
							focusConfirm: true,
							allowEnterKey: true,
							buttonsStyling: false,
							returnFocus: true,
							confirmButtonText: "Tamam",
							customClass: {
								confirmButton: "btn btn-danger"
							}
						}).then(function(result){
							tabButton = document.getElementsByClassName('swal2-confirm');
							// tabButton = $('.swal2-confirm');
							if(result.isConfirmed){
								tabButton = submitButton;
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


	var enterDinleme = (form) => {
		form.addEventListener('keyup', function (e) {	
			//console.log("hatayok"+e.keyCode);	
			if(e.keyCode == 13){
				if(tabButton!=false){
					e.preventDefault();
					tabButton.click();
				}
			}
		});
	}


	var mahkemeListAppend = function(){
		var mahkemeSelect = $("#dm_mahkeme");
		var isData = {status:1}
		setTimeout(function() {
			$.ajax({
				type: "POST",
				contentType: "application/json; charset=utf-8",
				dataType: "json",
				url: baseUrlHost+"api_SelectListMahkeme",
				data: JSON.stringify(isData),
				success: function (response) {
					if((typeof response)==="object"){
						if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
							if(response.code==200){

								$("#dm_mahkeme option").remove();

								$.each(response.data, function (index, item) { //jQuery way of iterating through a collection
									console.log("ID:",item.mh_id, "NAME:", item.mh_name);
									$("#dm_mahkeme option").append($('<option>')
										.text(item.mh_name)
										.attr('value', item.mh_id));
								})
							}else{
								$("#dm_mahkeme option").append($('<option>')
								.text('Mahkeme Listesi Boş')
								.attr('value', -1));
							}
						}else{
							$("#dm_mahkeme option").append($('<option>')
							.text('Mahkeme Listesi Boş')
							.attr('value', -1));
						}
					}else{
						$("#dm_mahkeme option").append($('<option>')
						.text('Mahkeme Listesi Boş')
						.attr('value', -1));
					}
				},
				error: function (hata) {
					$("#dm_mahkeme option").append($('<option>')
					.text('Mahkeme Listesi Boş')
					.attr('value', -1));
				}
			});	
		}, 1000);

	}


	var checkedEvent =( )=> {
        inpTutanakBilgi.change(function(){
            var $data = $(this).prop("checked");
            
            if(typeof $data !== "undefined" && $data==true){
                postData.edtsTutanakDurum=1;
            }else{
                postData.edtsTutanakDurum=0;
            }
			// console.log("TutanakDurum:",postData.edtsTutanakDurum);
        })
    }


	return {
		// Public functions
		init: function () {
			// Elements
			modalEl = document.querySelector('#kt_modal_new_durusmalar_manuel');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#kt_modal_new_durusmalar_manuel_form');
			submitButton = document.getElementById('kt_modal_new_durusmalar_manuel_submit');
			cancelButton = document.getElementById('kt_modal_new_durusmalar_manuel_cancel');

			inpDosyaNo = form.querySelector('[name="dm_dosyano"]');
			inpDosyaTuru = form.querySelector('[name="dm_dosyaturu"]');
			inpDurusmaTarihi = form.querySelector('[name="dm_durusmatarihi"]');
			
			inpEsasNo = form.querySelector('[name="dm_esasno"]');
			inpAvukat = form.querySelector('[name="dm_avukat"]');
			inpIlgiliAvukat = form.querySelector('[name="dm_ilgiliavukat"]');
			inpTaraf = form.querySelector('[name="dm_taraf"]');
			inpIslem = form.querySelector('[name="dm_islem"]');
			inpTarafBilgisi = form.querySelector('[name="dm_tarafbilgisi"]');
			inpAciklama = form.querySelector('[name="dm_aciklama"]');
			inpIlgiliMemur = form.querySelector('[name="dm_ilgilimemur"]');
			inpEtiket = form.querySelector('[name="dm_etiket"]');
			inpDurusmaIslemi = form.querySelector('[name="dm_durusmaislemi"]');


			inpAddDurusmalarDTarihiDP = form.querySelector('#dm_durusmatarihi');
			inpTutanakBilgi = $('#dm_tutanakbilgi');

			
			tabButton = submitButton;

			initForm();
			initValidator();
			checkedEvent();
			handleNewEvent();
			enterDinleme(form);
			if (form && submitButton) {
				form.addEventListener('submit', function (e) {
					e.preventDefault();
					if (!submitButton.disabled) submitButton.click();
				});
			}
			//mahkemeListAppend();
		}
	};
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTModalNewDurusmalarManuel.init();
});

function mahkemeEkle() {
//	var txt=$(".select2-search__field[aria-describedby='select2-filterMahkemeSelect-container']");
//	console.log(txt.html());
	
}


jQuery(function() {

	$(document).on("click","#example_mahkeme_ekle",function(){
		var mahkemeAdi=prompt("Mahkeme Adını Girin : ");
		$("")
		
	});

	
	jQuery("#dm_mahkememulti").select2(	
		{
			closeOnSelect: true,
			allowClear: true,
			tags: true,
			createTag: function (params) {
				var term = $.trim(params.term);
			
				if (term === '') {
				  return null;
				}
			
				return {
				  id: 'yeni_'+term,
				  text: '(Yeni)-'+term,
				  newTag: true // add additional parameters
				}
			  },			
			escapeMarkup: function (markup) {
				return markup;
			  }			
		
		});		
});

