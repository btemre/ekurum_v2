"use strict";
var hostUrl = window.location.host;
const baseUrlHost = "//"+hostUrl+"/apps/hedas/cezaiptal/";
var modulUrl = window.location.href;

var editId;
var editData = {
    route: 'editcezaiptal',
    ciId: '',
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


var KTModalEditCezaIptal = function () {

    var modal;
    var modalTitle;
    var form;
    var validator;
    var submitButton;
    var cancelButton;
    var closeButton;

    var viewKategori;
    var viewTur;
    var viewDescription;
    var viewDosyaNo;
    var viewSayi;
    var viewKaynak;
    var viewEtiket;
    var viewTarih;
    var tarihDatepicker;
    var tarihFlatpickr;

    var inpDosyaNo;
	var inpItirazEden;
	var inpMahkeme;
	var inpDavaKonusu;
	var inpCezaKonusu;
	var inpEsasNo;
	var inpKararNo;
	var inpPlaka;
	var inpSeriNo;
	var inpEvrakDurum;
	var inpIcra;
	var inpAciklama;
	var inpTags;
    var ciAcilisTarih;
    var ciKararTarih;

    var inpKararDatepicker;
    var inpAcilisDatepicker;
    var inpKararFlatpickr;
    var inpAcilisFlatpickr;
    var viewEvrakDurum;

    const handleEditEvent = () => {
        inpDosyaNo.value = editData.ciDosyaNo;
        inpItirazEden.value = editData.ciItirazEden;
        //inpAcilisTarihi = form.querySelector('[name="gelengiden_tur"]');
        inpMahkeme.value = editData.ciMahkeme;
        inpDavaKonusu.value = editData.ciDavaKonusu;
        inpCezaKonusu.value = editData.ciCezaKonusu;
        inpEsasNo.value = editData.ciEsasNo;
        inpKararNo.value = editData.ciKararNo;
        //inpKararTarihi = form.querySelector('[name="gelengiden_tur"]');
        inpPlaka.value = editData.ciPlaka;
        inpSeriNo.value = editData.ciSeriNo;
        inpIcra.value = editData.ciIcra;
        inpAciklama.value = editData.ciAciklama;
        inpTags.value = editData.ciTags;

        viewEvrakDurum.val(editData.ciEvrakDurum).trigger('change');
        inpAcilisFlatpickr.setDate(editData.ciAcilisTarih, true, 'd-m-Y');
        inpKararFlatpickr.setDate(editData.ciKararTarihi, true, 'd-m-Y');
        
  
        modal.show();

        handleUpdateEvent();        
      

    }

    const initForm = () => {

        //inpAcilisDatepicker = form.querySelector('#ci_acilistarihi');
        //inpKararDatepicker = form.querySelector('#ci_karartarihi');

        inpAcilisFlatpickr = flatpickr(inpAcilisDatepicker, {
            enableTime: false,
            dateFormat: "d-m-Y",
        });

        inpKararFlatpickr = flatpickr(inpKararDatepicker, {
            enableTime: false,
            dateFormat: "d-m-Y",
        });
 


		var ci_itirazeden = new Tagify(form.querySelector('[name="ci_itirazeden"]'), {
			whitelist: [],
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

//---AYRAC--------AYRAC---------AYRAC---------AYRAC--------AYRAC----------AYRAC----------AYRAC-----------AYRAC---------

		var ci_mahkeme = new Tagify(form.querySelector('[name="ci_mahkeme"]'), {
			whitelist: [],
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

//---AYRAC--------AYRAC---------AYRAC---------AYRAC--------AYRAC----------AYRAC----------AYRAC-----------AYRAC---------

		var ci_davakonusu = new Tagify(form.querySelector('[name="ci_davakonusu"]'), {
			whitelist: [],
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

//---AYRAC--------AYRAC---------AYRAC---------AYRAC--------AYRAC----------AYRAC----------AYRAC-----------AYRAC---------

		var ci_cezakonu = new Tagify(form.querySelector('[name="ci_cezakonu"]'), {
			whitelist: [],
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

//---AYRAC--------AYRAC---------AYRAC---------AYRAC--------AYRAC----------AYRAC----------AYRAC-----------AYRAC---------

		var ci_plaka = new Tagify(form.querySelector('[name="ci_plaka"]'), {
			whitelist: [],
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

//---AYRAC--------AYRAC---------AYRAC---------AYRAC--------AYRAC----------AYRAC----------AYRAC-----------AYRAC---------

		var ci_icra = new Tagify(form.querySelector('[name="ci_icra"]'), {
			whitelist: [],
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


        var tagify = new Tagify(form.querySelector('[name="ci_tags"]'), {
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
        var suggestions = document.querySelector('#kt_cezaiptal_edit_tags_suggests');

        // Suggestion item click
        KTUtil.on(suggestions,  '[data-kt-suggestion="true"]', 'click', function(e) {
            tagify.addTags([this.innerText]);
        });


    }


    const handleUpdateEvent = () => {
        submitButton.addEventListener('click', e => {
            e.preventDefault();

            editData.ciDosyaNo = inpDosyaNo.value;
            editData.ciItirazEden = inpItirazEden.value;
            editData.ciAcilisTarih = moment(inpAcilisFlatpickr.selectedDates[0]).format('DD-MM-YYYY');
            editData.ciMahkeme = inpMahkeme.value;
            editData.ciDavaKonusu = inpDavaKonusu.value;
            editData.ciCezaKonusu = inpCezaKonusu.value;
            editData.ciEsasNo = inpEsasNo.value;
            editData.ciKararNo = inpKararNo.value;
            editData.ciKararTarihi  = inpKararDatepicker.value;//moment(inpKararFlatpickr.selectedDates[0]).format('DD-MM-YYYY');
            editData.ciPlaka = inpPlaka.value;
            editData.ciSeriNo = inpSeriNo.value;
            editData.ciEvrakDurum = inpEvrakDurum.value;
            editData.ciIcra = inpIcra.value;
            editData.ciAciklama = inpAciklama.value;
            editData.ciTags = inpTags.value;


            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    //console.log('validated!');

                    if (status == 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click 
                        submitButton.disabled = true;

                    
                        //console.log(editData);

                        setTimeout(function() {
                            

                            // Enable button
                            
                            
                            // Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            Swal.fire({
                                text: "Ceza İptal Başvuru Bilgileri Güncellenecek!",
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
                                        url: baseUrlHost+"api_editrecord",
                                        data: JSON.stringify(editData),
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
                                                                
                                                                if(modulUrl.indexOf('cezaiptal')>0){                                                                    
                                                                    KTCezaIptalListServerSide.reload();                                                                    
                                                                }
                                                                submitButton.disabled = false;
                                                                modal.hide();
                                                                //window.location.assign(baseUrlHost);
                                                            }
                                                        });
                                                        //Veriler Kaydedildi Sayfayı Yenile
                                                        //submitButton.disabled = false;
                                                    }else if(response.code==406 && response.error==true){
                                                        
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

    }

    
    // Init validator
    const initValidator = () => {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
            form,
            {
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



    
    // Reset form validator on modal close
    const resetFormValidator = (element) => {
        // Target modal hidden event --- For more info: https://getbootstrap.com/docs/5.0/components/modal/#events
        element.addEventListener('hidden.bs.modal', e => {
            if (validator) {
                // Reset form validator. For more info: https://formvalidation.io/guide/api/reset-form
                validator.resetForm(true);
            }
        });
    }

    
    // Handle cancel button
    const handleCancelButton = () => {
        // Edit event modal cancel button
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

       // Handle close button
    const handleCloseButton = () => {
        // Edit event modal close button
        closeButton.addEventListener('click', function (e) {
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
                   // form.reset(); // Reset form
                    modal.hide(); // Hide modal
                }
            });
        });
    }




    return {
        init: function () {


            initValidator();
            handleEditEvent();
            handleCancelButton();
            handleCloseButton();

        },
        viewModal: function(id){
            editId = id;
            var postData = {
                id: editId
            }
            $.ajax({
                type: "POST",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                url: baseUrlHost+"api_getBasvuru",
                data: JSON.stringify(postData),
                success: function (response) {
                    if((typeof response)==="object"){
                        if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                            if(response.code==200){

                                //console.log(response.data);
                                editData.ciId = response.data._id;
                                editData.ciDosyaNo = response.data._dosyano;
                                editData.ciItirazEden = response.data._itirazeden;
                                editData.ciAcilisTarih = response.data._acilistarihi;
                                editData.ciMahkeme = response.data._mahkeme;
                                editData.ciDavaKonusu = response.data._davakonusu;
                                editData.ciCezaKonusu = response.data._cezakonusu;
                                editData.ciEsasNo = response.data._esasno;
                                editData.ciKararNo = response.data._kararno;
                                editData.ciKararTarihi  = response.data._karartarihi;
                                editData.ciPlaka = response.data._plaka;
                                editData.ciSeriNo = response.data._serino;
                                editData.ciEvrakDurum = response.data._evrakdurum;
                                editData.ciIcra = response.data._icra;
                                editData.ciAciklama = response.data._aciklama;
                                editData.ciTags = response.data._tags;
                            

                                KTModalEditCezaIptal.init();
                            }else if(response.code==406 && response.error==true){
                                
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
                }
            });
        },
        populate: function(){

            const element = document.getElementById('kt_modal_edit_cezaiptal');
            form = element.querySelector('#kt_modal_edit_cezaiptal_form');

            submitButton = form.querySelector('#kt_modal_edit_cezaiptal_submit');
            cancelButton = form.querySelector('#kt_modal_edit_cezaiptal_cancel');
            closeButton = element.querySelector('#kt_modal_edit_cezaiptal_close');


			inpDosyaNo = form.querySelector('[name="ci_dosyano"]');
			inpItirazEden = form.querySelector('[name="ci_itirazeden"]');
			
			inpMahkeme = form.querySelector('[name="ci_mahkeme"]');
			inpDavaKonusu = form.querySelector('[name="ci_davakonusu"]');
			inpCezaKonusu = form.querySelector('[name="ci_cezakonu"]');
			inpEsasNo = form.querySelector('[name="ci_esasno"]');
			inpKararNo = form.querySelector('[name="ci_kararno"]');
			//inpKararTarihi = form.querySelector('[name="gelengiden_tur"]');
			inpPlaka = form.querySelector('[name="ci_plaka"]');
			inpSeriNo = form.querySelector('[name="ci_serino"]');
			inpIcra = form.querySelector('[name="ci_icra"]');
			inpAciklama = form.querySelector('[name="ci_aciklama"]');
			inpTags = form.querySelector('[name="ci_tags"]');
            inpEvrakDurum = form.querySelector('[name="ci_evrakdurum"]');

            
            inpAcilisDatepicker = form.querySelector('#ci_acilistarihi');
            inpKararDatepicker = form.querySelector('#ci_karartarihi');
            viewEvrakDurum = $('#ci_evrakdurum');            
            //viewTur             = $('#ci_evrakdurum');
            //tarihDatepicker     = form.querySelector('#edt_gelengiden_evraktarih');

            modalTitle = form.querySelector('[data-kt-reminder="title"]');
            modal = new bootstrap.Modal(element);
            
            initForm();
            resetFormValidator(element);
            //console.log("Populate Start");
        },
        copeAtModal: function(id){
            var postData = {
                id: id
            }

            Swal.fire({
                text: "İlgili Kayıt Çöp Kutusuna Taşınacak. Emin misiniz?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Taşı",
                cancelButtonText: "Vazgeç",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger"
                }
            }).then(function (result) {
                if (result.value) {
                   
                    $.ajax({
                        type: "POST",
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        url: baseUrlHost+"api_ejectdata",
                        data: JSON.stringify(postData),
                        success: function (response) {
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
                                                
                                                if(modulUrl.indexOf('cezaiptal')>0){
                                                    //KTGelenGidenListServerSide.init();
                                                    $('#filtreleButton').click();
                                                    
                                                }
                                                //window.location.assign(baseUrlHost);
                                            }
                                        });

                                    }else if(response.code==406 && response.error==true){
                                        
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
                            //console.log(hata);
                        }
                    });
                }
            });

        }
    }    
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalEditCezaIptal.populate();
});