"use strict";
var hostUrl = window.location.host;
const baseUrlHost = "//"+hostUrl+"/apps/hedas/gelengiden/";
var modulUrl = window.location.href;

var editId;
var editData = {
    route: "editgelengiden",
    ggId: '',
    ggTur: '',
    ggTarih: '',
    ggKaynak: '',
    ggTur: -1,
    ggSayi: '',
    ggDosyaNo: '',
    ggKategori: -1,
    ggAciklama: '',
    ggEtiket: ''
};

var ggEvrakAciklamaEdit;


var KTModalEditGelenGiden = function () {

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

	var ggTur;
	var ggKategori;
	var ggKaynak;
	
	var ggDosyaNo;
	var ggSayi;
	var ggEtiket;


    const handleEditEvent = () => {
        //modalTitle.innerText = "Gelen/Giden Evrak Düzenle";
        //viewDescription.value     = editData.ggAciklama;
        viewDosyaNo.value         = editData.ggDosyaNo;
        viewSayi.value            = editData.ggSayi;
        viewKaynak.value          = editData.ggKaynak;
        viewEtiket.value          = editData.ggEtiket;
        //viewTarih.value           = editData.ggTarih;
        ggEvrakAciklamaEdit.root.innerHTML = editData.ggAciklama;

        viewTur.val(editData.ggTur).trigger('change');
        viewKategori.val(editData.ggKategori).trigger('change');
        
        tarihFlatpickr.setDate(editData.ggTarih, true, 'd-m-Y');



  
        modal.show();

        handleUpdateEvent();        
      

    }

    const initForm = () => {
        tarihFlatpickr = flatpickr(tarihDatepicker, {
            enableTime: false,
            dateFormat: "d-m-Y",
        });

		var inputElm = form.querySelector('[name="edt_gelengiden_tags_whitelist"]');
		var gelengiden_tags = new Tagify(form.querySelector('[name="edt_gelengiden_tags"]'), {
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
		var suggestions = document.querySelector('#kt_edt_gelengiden_custom_suggestions');

		// Suggestion item click
		KTUtil.on(suggestions,  '[data-kt-suggestion="true"]', 'click', function(e) {
			gelengiden_tags.addTags([this.innerText]);
		});


		// var inputElm1 = form.querySelector('[name="edt_gelengiden_kaynak_whitelist"]');
		var edt_gelengiden_kaynak = new Tagify(form.querySelector('[name="edt_gelengiden_kaynak"]'), {
			whitelist: [],//inputElm1.value.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		edt_gelengiden_kaynak.on('input', async function (e) {
			edt_gelengiden_kaynak.settings.whitelist.length = 0; // reset current whitelist
			edt_gelengiden_kaynak.loading(true).dropdown.hide.call(edt_gelengiden_kaynak)
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
										edt_gelengiden_kaynak.settings.whitelist.push(...newWhitelist);
										edt_gelengiden_kaynak.loading(false).dropdown.show.call(edt_gelengiden_kaynak, e.detail.value);
									}else{
										edt_gelengiden_kaynak.loading(false).dropdown.show.call(edt_gelengiden_kaynak, e.detail.value);
									}
								}else{
									edt_gelengiden_kaynak.loading(false).dropdown.show.call(edt_gelengiden_kaynak, e.detail.value);
								}
							}else{
								edt_gelengiden_kaynak.loading(false).dropdown.show.call(edt_gelengiden_kaynak, e.detail.value);
							}
						},
						error: function (hata) {
							edt_gelengiden_kaynak.loading(false).dropdown.show.call(edt_gelengiden_kaynak, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_gelengiden_kaynak.loading(false).dropdown.show.call(edt_gelengiden_kaynak, e.detail.value);
			}
		});
		
		edt_gelengiden_kaynak.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('edt_gelengiden_kaynak');
		});


    }

    const handleUpdateEvent = () => {
        submitButton.addEventListener('click', e => {
            e.preventDefault();
            editData.ggTur = ggTur.value;
            editData.ggKategori =  ggKategori.value;
            editData.ggKaynak =  ggKaynak.value;
            editData.ggAciklama =  ggEvrakAciklamaEdit.root.innerHTML;
            editData.ggDosyaNo =  ggDosyaNo.value;
            editData.ggSayi =  ggSayi.value;
            editData.ggTarih =  moment(tarihFlatpickr.selectedDates[0]).format('DD-MM-YYYY');//moment(tarihFlatpickr[0]._flatpickr.selectedDates[0]).format('DD-MM-YYYY');
            editData.ggEtiket =  ggEtiket.value;



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
                                text: "Gelen/Giden Evrak Bilgileri Güncellenecek!",
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
                                                                
                                                                if(modulUrl.indexOf('gelengiden')>0){
                                                                    //KTGelenGidenListServerSide.init();
                                                                    $('#filtreleButton').click();
                                                                    
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
					edt_gelengiden_tur: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					edt_gelengiden_evraktarih: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					edt_gelengiden_kaynak: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					edt_gelengiden_kategori: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					edt_gelengiden_dosyano: {
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


            //console.log("EDITData:",editData);
             
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
                url: baseUrlHost+"api_getEvrak",
                data: JSON.stringify(postData),
                success: function (response) {
                    //submitButton.disabled = false;
                    //console.log(response);
                    //console.log(response.success);
                    //console.log("Type:", typeof response, isValidJsonString(response));
                    //return;
                    if((typeof response)==="object"){
                        if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                            if(response.code==200){

                                editData.ggId       = response.data._id;
                                editData.ggTur      = response.data._tur;
                                editData.ggTarih    = response.data._tarih;
                                editData.ggKaynak   = response.data._ilgili;
                                editData.ggSayi     = response.data._sayi;
                                editData.ggDosyaNo  = response.data._dosyano;
                                editData.ggKategori = response.data._kategori;
                                editData.ggAciklama = response.data._aciklama;
                                editData.ggEtiket   = response.data._tags;

                                KTModalEditGelenGiden.init();
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
                   
                    console.log(hata);
                }
            });
           // console.log("A:",a);
            
        },
        populate: function(){

            const element = document.getElementById('kt_modal_edit_ggevrak');
            form = element.querySelector('#kt_modal_edit_ggevrak_form');

            submitButton = form.querySelector('#kt_modal_edit_ggevrak_submit');
            cancelButton = form.querySelector('#kt_modal_edit_ggevrak_cancel');
            closeButton = element.querySelector('#kt_modal_edit_ggevrak_close');

            
            viewTur             = $('#edt_gelengiden_tur');
            viewKategori        = $('#edt_gelengiden_kategori');
            viewDescription     = form.querySelector('[name="edt_gelengiden_aciklama"]');
            viewDosyaNo         = form.querySelector('[name="edt_gelengiden_dosyano"]');
            viewSayi            = form.querySelector('[name="edt_gelengiden_sayi"]');
            viewKaynak          = form.querySelector('[name="edt_gelengiden_kaynak"]');
            viewEtiket          = form.querySelector('[name="edt_gelengiden_tags"]');
            viewTarih           = form.querySelector('[name="edt_gelengiden_evraktarih"]');
            tarihDatepicker     = form.querySelector('#edt_gelengiden_evraktarih');

			ggTur = form.querySelector('[name="edt_gelengiden_tur"]');
			ggKategori = form.querySelector('[name="edt_gelengiden_kategori"]');
			ggKaynak = form.querySelector('[name="edt_gelengiden_kaynak"]');
			//ggAciklama = form.querySelector('[name="edt_gelengiden_aciklama"]');
			ggDosyaNo = form.querySelector('[name="edt_gelengiden_dosyano"]');
			ggSayi = form.querySelector('[name="edt_gelengiden_sayi"]');
			ggEtiket = form.querySelector('[name="edt_gelengiden_tags"]');
            

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
                                                
                                                if(modulUrl.indexOf('gelengiden')>0){
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
                           
                            console.log(hata);
                        }
                    });
         
                   
                }
            });

        }
    }    
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalEditGelenGiden.populate();
});



ggEvrakAciklamaEdit = new Quill('#edt_gelengiden_aciklama', {
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
