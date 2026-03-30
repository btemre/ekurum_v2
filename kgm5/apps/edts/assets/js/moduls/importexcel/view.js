"use strict";
var tablexlsdurusmalarw;
var dtxlsdurusmalarw;



var hostUrl = window.location.host;
var baseUrlHost = "//"+hostUrl+"/apps/edts/importexcel/";
var baseUrl = "//"+hostUrl+"/apps/edts/importexcel/";
var modulUrl = window.location.href;

var xlsEsasNo;

// Class definition
var KTImportExcelListServerSide = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	
    // Shared variables
	var xlsDosyaNo;
	var xlsDosyaTuru;
	var xlsMahkeme;
	var xlsAvukat;
	var xlsIlgiliAvukat;
	var xlsTaraf;
	var xlsIslem;
	var xlsTarafBilgisi;
	var xlsAciklama;
	var xlsIlgiliMemur;
	var xlsEtiket;
	var xlsDurusmaIslemi;
    var xlsTutanakBilgi;
	

    var filterData = {
        dText: '',
        dDosyaTuru: '',
		dMahkeme: '',
		dDurusmaTarihi: '',
		dEsasNo: '',
		dTarafBilgisi: '',
		dIslem: '',
	};


    // EXCEL ADD MODAL FONKSİYONLARI - BEGİN ------------------------------------------- !! 
	var postData = {
		route: "addexceldurusma",
		edtsId:0,
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
		if (!form) return;
		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/

		var xlsAvukatEl = form.querySelector('[name="xls_dm_avukat"]');
		if (xlsAvukatEl) $(xlsAvukatEl).on('change', function () {
			// Revalidate the field when an option is chosen
			validator.revalidateField('xls_dm_avukat');
		});

		var xlsTarafEl = form.querySelector('[name="xls_dm_taraf"]');
		if (xlsTarafEl) $(xlsTarafEl).on('change', function () {
			// Revalidate the field when an option is chosen
			validator.revalidateField('xls_dm_taraf');
		});

		var xlsIlgiliavukatEl = form.querySelector('[name="xls_dm_ilgiliavukat"]');
		var ilgiliAvukatWhitelist = (localStorage.edtsDurusmalarIlgiliAvukatListData || '').trim().split(/\s*,\s*/).filter(Boolean);
		var xls_dm_ilgiliavukat = xlsIlgiliavukatEl ? new Tagify(xlsIlgiliavukatEl, {
			whitelist: ilgiliAvukatWhitelist,
			placeholder: "Yazınız",
			enforceWhitelist: true,
			dropdown: {
				maxItems: 20,           // <- mixumum allowed rendered suggestions
				classname: "xls_dm_ilgiliavukat__suggestions", // <- custom classname for this dropdown, so it could be targeted
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		}) : null;

		if (xls_dm_ilgiliavukat) xls_dm_ilgiliavukat.on('input', async function (e) {
			xls_dm_ilgiliavukat.settings.whitelist.length = 0; // reset current whitelist
			xls_dm_ilgiliavukat.loading(true).dropdown.hide.call(xls_dm_ilgiliavukat)
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
										xls_dm_ilgiliavukat.settings.whitelist.push(...newWhitelist);
										xls_dm_ilgiliavukat.loading(false).dropdown.show.call(xls_dm_ilgiliavukat, e.detail.value);
									}else{
										xls_dm_ilgiliavukat.loading(false).dropdown.show.call(xls_dm_ilgiliavukat, e.detail.value);
									}
								}else{
									xls_dm_ilgiliavukat.loading(false).dropdown.show.call(xls_dm_ilgiliavukat, e.detail.value);
								}
							}else{
								xls_dm_ilgiliavukat.loading(false).dropdown.show.call(xls_dm_ilgiliavukat, e.detail.value);
							}
						},
						error: function (hata) {
							xls_dm_ilgiliavukat.loading(false).dropdown.show.call(xls_dm_ilgiliavukat, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				xls_dm_ilgiliavukat.loading(false).dropdown.show.call(xls_dm_ilgiliavukat, e.detail.value);
			}
		});

		var xlsIlgilimemurEl = form.querySelector('[name="xls_dm_ilgilimemur"]');
		var ilgiliMemurWhitelist = (localStorage.edtsDurusmalarIlgiliMemurListData || '').trim().split(/\s*,\s*/).filter(Boolean);
		var xls_dm_ilgilimemur = xlsIlgilimemurEl ? new Tagify(xlsIlgilimemurEl, {
			whitelist: ilgiliMemurWhitelist,
			placeholder: "Yazınız",
			enforceWhitelist: true,
			dropdown: {
				maxItems: 20,           // <- mixumum allowed rendered suggestions
				classname: "xls_dm_ilgilimemur__suggestions", // <- custom classname for this dropdown, so it could be targeted
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		}) : null;

		if (xls_dm_ilgilimemur) xls_dm_ilgilimemur.on('input', async function (e) {
			xls_dm_ilgilimemur.settings.whitelist.length = 0; // reset current whitelist
			xls_dm_ilgilimemur.loading(true).dropdown.hide.call(xls_dm_ilgilimemur)
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
										xls_dm_ilgilimemur.settings.whitelist.push(...newWhitelist);
										xls_dm_ilgilimemur.loading(false).dropdown.show.call(xls_dm_ilgilimemur, e.detail.value);
									}else{
										xls_dm_ilgilimemur.loading(false).dropdown.show.call(xls_dm_ilgilimemur, e.detail.value);
									}
								}else{
									xls_dm_ilgilimemur.loading(false).dropdown.show.call(xls_dm_ilgilimemur, e.detail.value);
								}
							}else{
								xls_dm_ilgilimemur.loading(false).dropdown.show.call(xls_dm_ilgilimemur, e.detail.value);
							}
						},
						error: function (hata) {
							xls_dm_ilgilimemur.loading(false).dropdown.show.call(xls_dm_ilgilimemur, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				xls_dm_ilgilimemur.loading(false).dropdown.show.call(xls_dm_ilgilimemur, e.detail.value);
			}
		});

		var etiketinput = document.querySelector('#dm_excel_etiket');
		var dm_excel_etiket = etiketinput ? new Tagify(etiketinput, {
			whitelist: ["Önemli","Acil","Eksik","Hatırla","Taslak","Silinecek"],
			placeholder: "Yazınız",
			enforceWhitelist: true
		}) : null;
	
		// Suggestions
		var suggestions = document.querySelector('#kt_tagify_excel_etiket_custom_suggestions');
	
		// Suggestion item click
		if (suggestions && dm_excel_etiket) KTUtil.on(suggestions,  '[data-kt-suggestion="true"]', 'click', function(e) {
			dm_excel_etiket.addTags([this.innerText]);
		});
	
	


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

	var initValidator = function(){
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validator = FormValidation.formValidation(
			form,
			{
				fields: {
					xls_dm_dosyano: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					xls_dm_esasno: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					xls_dm_avukat: {
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
					xls_dm_taraf: {
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

	
	const handleUpdateEvent = () => {

		// Action buttons
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();

			// Validate form before submit
			if (validator) {
				validator.validate().then(function (status) {
				
					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple cligck 
						submitButton.disabled = true;
						

						postData.route = "addexceldurusma";
						postData.edtsDosyaNo = xlsDosyaNo.value;//INPUTLARI POPULATE DEN KONTROL ET !!!!!!!!!!!!!!!!
						postData.edtsAvukat = xlsAvukat.value;
						postData.edtsIlgiliAvukat = xlsIlgiliAvukat.value;
						postData.edtsTaraf = xlsTaraf.value;
						postData.edtsDurusmaIslemi = xlsDurusmaIslemi.value;
						postData.edtsAciklama = xlsAciklama.value;
						postData.edtsIlgiliMemur = xlsIlgiliMemur.value;
						postData.edtsEtiket = xlsEtiket.value;
						// postData.edtsDosyaTur = xlsDosyaTuru.value;
						// postData.edtsDurusmaTarihi = inpDurusmalarDTarihiDP.value;
						// postData.edtsMahkeme = xlsMahkeme.value;
						// postData.edtsEsasNo = xlsEsasNo.value;
						// postData.edtsIslem = xlsIslem.value;
						// postData.edtsTarafBilgisi = xlsTarafBilgisi.value;
				
						
						setTimeout(function() {
							submitButton.removeAttribute('data-kt-indicator');


							// Enable button
							submitButton.disabled = false;

							
							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							Swal.fire({
                                text: "Yeni Kayıt Aktarılacak. Ön bellekten Silinecektir!",
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
								if (result.isConfirmed) {
									//Veriyi Kaydetmeyi Onaylarsa Burası Çalışır
									//Veriler Ajax İleJson Post Yapılacak Alınan Cevaba Göre Aksiyon Alınacak
									$.ajax({
                                        type: "POST",
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json",
                                        url: baseUrlHost+"api_excelnewrecord",
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
																$('#xls_dm_avukat').val('0').trigger('change');
																$('#xls_dm_taraf').val('0').trigger('change');
																form.reset();
																if(modulUrl.indexOf('importexcel')>0){
                                                                    //KTGelenGidenListServerSide.init();
                                                                    //$('#filtreleButton').click();
                                                                    KTImportExcelListServerSide.reload();
                                                                    
                                                                }
                                                                submitButton.disabled = false;
                                                                modal.hide();
                                                                //window.location.assign(baseUrlHost);
                                                            }
                                                        });
                                                        //Veriler Kaydedildi Sayfayı Yenile
                                                        //submitButton.disabled = false;
                                                    }else if(response.code==409 && response.duplicate===true){
                                                        submitButton.disabled = false;
                                                        var existingId = response.existingId;
                                                        var durusmalarBase = "//"+hostUrl+"/apps/edts/durusmalar/";
                                                        Swal.fire({
                                                            title: "Kayıt zaten mevcut",
                                                            text: response.description || "Aynı esas no ve dosya no ile kayıt bulundu.",
                                                            icon: "warning",
                                                            showCancelButton: true,
                                                            showDenyButton: true,
                                                            confirmButtonText: "Eski kaydı görüntüle",
                                                            denyButtonText: "Eski kaydı sil",
                                                            cancelButtonText: "İptal",
                                                            buttonsStyling: false,
                                                            customClass: {
                                                                confirmButton: "btn btn-primary",
                                                                denyButton: "btn btn-danger",
                                                                cancelButton: "btn btn-light"
                                                            }
                                                        }).then(function(res){
                                                            if (res.isConfirmed) {
                                                                window.location.href = durusmalarBase + "update/" + existingId;
                                                            } else if (res.isDenied) {
                                                                $.ajax({
                                                                    type: "POST",
                                                                    contentType: "application/json; charset=utf-8",
                                                                    dataType: "json",
                                                                    url: durusmalarBase + "api_ejectdata",
                                                                    data: JSON.stringify({ id: existingId }),
                                                                    success: function(delRes) {
                                                                        if (delRes && delRes.code === 200) {
                                                                            Swal.fire({ text: "Eski kayıt silindi. Tekrar aktarmayı deneyebilirsiniz.", icon: "success", buttonsStyling: false, confirmButtonText: "Tamam", customClass: { confirmButton: "btn btn-primary" } });
                                                                            if (typeof KTImportExcelListServerSide !== 'undefined' && KTImportExcelListServerSide.reload) KTImportExcelListServerSide.reload();
                                                                        } else {
                                                                            Swal.fire({ text: (delRes && delRes.description) || "Silme işlemi başarısız.", icon: "error", buttonsStyling: false, confirmButtonText: "Tamam", customClass: { confirmButton: "btn btn-danger" } });
                                                                        }
                                                                    },
                                                                    error: function() {
                                                                        Swal.fire({ text: "Silme isteği gönderilemedi.", icon: "error", buttonsStyling: false, confirmButtonText: "Tamam", customClass: { confirmButton: "btn btn-danger" } });
                                                                    }
                                                                });
                                                            }
                                                        });
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

	}



    const handleEditEvent = () => {

        /* 
        postData.edtsId       
        postData.edtsDosyaTur       
        postData.edtsDurusmaTarihi       
        postData.edtsMahkeme       
        postData.edtsEsasNo       
        postData.edtsIslem       
        postData.edtsTarafBilgisi
        */
		
        // xlsDosyaTuru.value         = postData.edtsDosyaTur;
        // xlsMahkeme.value         = postData.edtsMahkeme;
        xlsEsasNo.value         = postData.edtsEsasNo;
        // xlsIslem.value         = postData.edtsIslem;
        // xlsTarafBilgisi.value         = postData.edtsTarafBilgisi;
        
        // inpDurusmalarDTarihiFP.setDate(editDataDurusmalarMUpdate.edtsDurusmaTarihi, true, 'd-m-Y H:i');

        modal.show();

        handleUpdateEvent();        

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
    

	var checkedEvent =( )=> {
        xlsTutanakBilgi.change(function(){
            var $data = $(this).prop("checked");
            
            if(typeof $data !== "undefined" && $data==true){
                postData.edtsTutanakDurum=1;
            }else{
                postData.edtsTutanakDurum=0;
            }
            // console.log("tb:"+postData.edtsTutanakDurum);
        })
    }



    // EXCEL ADD MODAL FONKSİYONLARI  - END ------------------------------------------- !! 





    // Private functions
    var initDatatable = function () {
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX;

		
    
        dtxlsdurusmalarw = $("#kt_content_durusmalar_list").DataTable({
            searchDelay: 1000,
            processing: true,
            serverSide: true,
			responsive: true,
			pageLength: 20,
            loadingRecords: "Kayıtlar yükleniyor.",
			language: {sDecimal:",",sEmptyTable:"Henüz kayıt yok..",sInfo:"_TOTAL_ Kayıt Bulundu",sInfoEmpty:"Kayıt yok",sInfoFiltered:"(_MAX_ Kayıt İçerisinden)",sInfoPostFix:"",sInfoThousands:".",sLengthMenu:"Sayfada _MENU_ kayıt göster",sLoadingRecords:"Yükleniyor...",sProcessing:"İşleniyor...",sSearch:"Ara:",sZeroRecords:"Eşleşen kayıt bulunamadı",oPaginate:{sFirst:"İlk",sLast:"Son",sNext:"Sonraki",sPrevious:"Önceki"},oAria:{sSortAscending:": artan sütun sıralamasını aktifleştir",sSortDescending:": azalan sütun sıralamasını aktifleştir"},select:{rows:{"_":"%d kayıt seçildi","0":"","1":"1 kayıt seçildi"}}},
			oLanguage: {
                sInfo : "_START_ ile _END_ arasıda _TOTAL_ kayıt gösteriliyor",// text you want show for info section
                infoEmpty : "Kayıt Bulunamadı!"
                },
			lengthMenu: [
                [5, 10, 15, 20, 25, 30, 50, 100],
                [5, 10, 15, 20, 25, 30, 50, 100],
            ],
            order: [[0, 'desc']],
            //stateSave: true,
            ajax: {
                url: baseUrlHost+"/apps/edts/importexcel/api_list",
				method: 'POST',
				//dataType: 'json',
				//contentType: 'application/json',
            },
            columns: [
                { data: '_mahkeme' },
                { data: '_esasno' },
                { data: '_dosyaturu' },
                { data: '_durusmatarihi' },
                { data: '_tarafbilgisi' },
				{ data: '_islem' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        var menuHtml = "";
                        //if(row._onay==0){
                            menuHtml = `
                            <!--begin::Edit-->
                            <div class="btn btn-icon btn-sm btn-color-gray-400 btn-active-icon-primary me-2"  onclick="KTImportExcelListServerSide.addModal('`+row._id+`');" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Ekle" id="addButton`+row._id+`">
                                <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor" />
                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </div>
                            <!--end::Edit-->
                            <!--begin::Edit-->
                            <div class="btn btn-icon btn-sm btn-color-gray-400 btn-active-icon-danger me-2"  onclick="KTImportExcelListServerSide.deleteModal('`+row._id+`');" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Sil" id="deleteButton`+row._id+`">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor" />
                                        <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor" />
                                        <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </div>
                            <!--end::Edit-->
                            `;
                       // }
                        return menuHtml;
                    },
                },
            ],
            // Add data-filter attribute
			/*
            createdRow: function (row, data, dataIndex) {
                $(row).find('td:eq(1)').attr('data-filter', data._tarih);
            }
			*/
        }).columns([0,1,2,3,4,5])
        .flatten()
        .search(JSON.stringify(filterData));
        // console.log(dtxlsdurusmalarw);

        // const tableRows = tablexlsdurusmalarw.querySelectorAll('tbody tr');

        // tableRows.forEach(row => {
        //     const dateRow = row.querySelectorAll('td');
        //     const realDate = moment(dateRow[3].innerHTML, "DD MMM YYYY, LT").format(); // select date from 4th column in table
        //     dateRow[3].setAttribute('data-order', realDate);
        // });

        tablexlsdurusmalarw = dtxlsdurusmalarw.$;

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dtxlsdurusmalarw.on('draw', function () {
           // initToggleToolbar();
           // toggleToolbars();
            //handleDeleteRows();
            KTMenu.createInstances();
        });

        

    }


    
	var handleFilterSubmit = () => {
			if (!dtxlsdurusmalarw) return;
			dtxlsdurusmalarw
			.columns([0,1,2,3,4,5])
			.flatten()
			.search(JSON.stringify(filterData))
			.draw();		
	}


    // Public methods
    return {
        init: function () {

			initValidator();
			handleCancelButton();
            handleEditEvent();
            checkedEvent();
			
        },
        reload: function(){
            handleFilterSubmit();
        },
        addModal: function(id){
			postData.edtsId = id;
			var postItem = {
				id: postData.edtsId
			}
			$.ajax({
                type: "POST",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                url: baseUrlHost+"api_getDurusma",
                data: JSON.stringify(postItem),
                success: function (response) {
                    //submitButton.disabled = false;
                    // console.log(response);
                    //console.log(response.success);
                    //console.log("Type:", typeof response, isValidJsonString(response));
                    //return;
                    if((typeof response)==="object"){
                        if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                            if(response.code==200){

                                postData.edtsId       = response.data._id;
                                postData.edtsDosyaTur       = response.data._dosyaturu;
                                postData.edtsDurusmaTarihi       = response.data._durusmatarihi;
                                postData.edtsMahkeme       = response.data._mahkeme;
                                postData.edtsEsasNo       = response.data._esasno;
                                postData.edtsIslem       = response.data._islem;
                                postData.edtsTarafBilgisi       = response.data._tarafbilgisi;

                                KTImportExcelListServerSide.init();
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
                    // console.log(hata);
                }
            });


		},
		populate: function(){

			const element = document.getElementById('kt_modal_import_durusmalar');
            form = element.querySelector('#kt_modal_import_durusmalar_form');

            submitButton = form.querySelector('#kt_modal_import_durusmalar_submit');
            cancelButton = form.querySelector('#kt_modal_import_durusmalar_cancel');



			modal = new bootstrap.Modal(element);

			// submitButton = document.getElementById('kt_modal_import_durusmalar_submit');
			// cancelButton = document.getElementById('kt_modal_import_durusmalar_cancel');

			xlsDosyaNo = form.querySelector('[name="xls_dm_dosyano"]');
			xlsAvukat = form.querySelector('[name="xls_dm_avukat"]');
			xlsIlgiliAvukat = form.querySelector('[name="xls_dm_ilgiliavukat"]');
			xlsTaraf = form.querySelector('[name="xls_dm_taraf"]');
			xlsAciklama = form.querySelector('[name="xls_dm_aciklama"]');
			xlsIlgiliMemur = form.querySelector('[name="xls_dm_ilgilimemur"]');
			xlsEtiket = form.querySelector('[name="dm_etiket"]');
			xlsDurusmaIslemi = form.querySelector('[name="xls_dm_durusmaislemi"]');
			xlsEsasNo = form.querySelector('[name="xls_dm_esasno"]');
			

            xlsTutanakBilgi = $('#xls_dm_tutanakbilgi');

			initForm();
            resetFormValidator(element);
			initDatatable();
			
            tablexlsdurusmalarw = document.querySelector('#kt_content_durusmalar_list');

            if ( !tablexlsdurusmalarw) {
                return;
            } 


		},
        deleteModal: function(id){
            var postData = {
                id: id
            }

            Swal.fire({
                text: "İlgili Kayıt Önbellekten Silinecek. Emin misiniz?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sil",
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
                        url: baseUrl+"api_deleteitem",
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
                                                
                                                if(modulUrl.indexOf('importexcel')>0){
                                                    //KTGelenGidenListServerSide.init();
                                                    KTImportExcelListServerSide.reload();
                                                }
                                                //window.location.assign(baseUrlHostDurusmalarMUpdate);
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
                            // console.log(hata);
                        }
                    });
                }
            });            
        },
		cleanAll: function(){
            var postData = {
                id: 0
            }

			
			Swal.fire({
                text: "Önbellek Kayıtları Silinecek. Emin misiniz?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sil",
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
                        url: baseUrl+"api_cleanall",
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
                                                
                                                if(modulUrl.indexOf('importexcel')>0){
                                                    //KTGelenGidenListServerSide.init();
                                                    KTImportExcelListServerSide.reload();
                                                }
                                                //window.location.assign(baseUrlHostDurusmalarMUpdate);
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
                            // console.log(hata);
                        }
                    });
                }
            });            

		}
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTImportExcelListServerSide.populate();
});