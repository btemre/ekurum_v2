"use strict";

var divdosyaBilgileriEdit;
var divMahkemeBilgileriEdit;
var cardDosyaBilgiEdit;
var cardMahkemeBilgiEdit;

var dosyaIdEdit = 0;

var inpProjeBilgiEdit;
var inpMevkiEdit;
var inpAciklamaEdit;

var tableEdit;
var dtEdit;


var dmKararTarihEditFlatP;
var dmKararTarihEditDataP;
var dmAcilisTarihEditFlatP;
var dmAcilisTarihEditDataP;



var KTModalEditDosya = function () {
	var submitButton;
	var submitButtonM;
	var deleteButtonM;
	var addButtonM;
	var cancelButton;
	var validator;
	var validatorMahkeme;
	var form;
	var mahkemeform;
	var modal;
	var modalEl;

	var inpKlasorNo;
	var inpDosyaNo;
	var inpDavaci;
	var inpDavali;
	var inpDavaKonusu;
	var inpKonuAciklamasi;
	var inpIstinafBilgi;
	var inpTemyizBilgi;
	var inpOnamaIlami;
	var inpBozmaIlami;
	var inpIstinafKabul;
	var inpIstinafRed;
	var inpIcraNo;
	var inpIcra;
	var inpKesinlestirme;
	var inpMirascilik;
	var inpIdariAlacagi;
	var inpVekaletAlacagi;
	var inpYargilamaGideri;
	var inpTapuBilgi;
	var viewTapuBilgi;
	var inpTags;

	var inpMahkemeJq;
	var inpEsasNo;
	var inpKararNo;
	var inpMahkemeAciklama;
	
	
	var hostUrl = window.location.host;
	const baseUrlHost = "//"+hostUrl+"/apps/hedas/dosya/";
	var modulUrl = window.location.href;
	//console.log("modulUrl", modulUrl, baseUrlHost);

	var editData ={
		route: "",
		dDosyaId: 0,
		dKlasorNo: '',
		dDosyaNo: '',
		dDavaci: '',
		dDavali: '',
		dDavaKonusu: '',
		dKonuAciklamasi: '',
		dProjeBilgi: '',
		dMevkiBilgi: '',
		dIstinafBilgi: '',
		dTemyizBilgi: '',
		dOnamaIlami: '',
		dBozmaIlami: '',
		dIstinafKabul: '',
		dIstinafRed: '',
		dIcraNo: '',
		dIcra: '',
		dKesinlestirme: '',
		dMirascilik: '',
		dIdariAlacagi: '',
		dVekaletAlacagi: '',
		dYargilamaGideri: '',
		dTapuBilgi: '',
		dAciklama: '',
		dTags: ''
	}


	var filterMahkemeDataEdit = {
        dmDosyaId: '',
	};

	var editMahkemeData = {
		route: '',
		dmMahkemeId: 0,
		dmDosyaId: 0,
		dmAcilisTarihi: '',
		dmKararTarihi: '',
		dmEsasNo: '',
		dmKararNo: '',
		dmMahkeme: '',
		dmAciklama: ''
	}


    // Reset form validator on modal close
    const resetFormValidator = (element) => {
        // Target modal hidden event --- For more info: https://getbootstrap.com/docs/5.0/components/modal/#events
        element.addEventListener('hidden.bs.modal', e => {
            if (validator && form && document.body.contains(form)) {
                try {
                    validator.resetForm(true);
                } catch (err) {
                    console.warn('[Dosya] Validator reset hatası (görmezden gelinebilir):', err.message);
                }
            }
			if (validatorMahkeme && mahkemeform && document.body.contains(mahkemeform)) {
                try {
                    validatorMahkeme.resetForm(true);
                } catch (err) {
                    console.warn('[Dosya] ValidatorMahkeme reset hatası (görmezden gelinebilir):', err.message);
                }
            }
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
					edt_d_davaci: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					edt_d_davali: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					edt_d_davakonusu: {
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
	var initMahkemeValidator = function() {
		// Stepper custom navigation
		
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validatorMahkeme = FormValidation.formValidation(
			mahkemeform,
			{
				framework: 'bootstrap',
				icon: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					edt_dm_esasno: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					edt_dm_mahkeme: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					edt_dm_acilistarihi: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							},
							date:{
								format: 'DD-MM-YYYY',
								message: 'Geçerli Bir Tarih Seçiniz.'
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



	// Init form inputs
	var initForm = function() {
		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/

		//var ci_itirazeden = new Tagify(form.querySelector('[name="ci_itirazeden"]') NAME OLARAK DA KULLANILABİLİR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		var edt_d_davaci = new Tagify(form.querySelector('[name="edt_d_davaci"]'), {
			whitelist: [],//localStorage.hedasDosyaDavaciListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		edt_d_davaci.on('input', async function (e) {
			edt_d_davaci.settings.whitelist.length = 0; // reset current whitelist
			edt_d_davaci.loading(true).dropdown.hide.call(edt_d_davaci);
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormDavaciSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										edt_d_davaci.settings.whitelist.push(...newWhitelist);
										edt_d_davaci.loading(false).dropdown.show.call(edt_d_davaci, e.detail.value);
									}else{
										edt_d_davaci.loading(false).dropdown.show.call(edt_d_davaci, e.detail.value);
									}
								}else{
									edt_d_davaci.loading(false).dropdown.show.call(edt_d_davaci, e.detail.value);
								}
							}else{
								edt_d_davaci.loading(false).dropdown.show.call(edt_d_davaci, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_davaci.loading(false).dropdown.show.call(edt_d_davaci, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_davaci.loading(false).dropdown.show.call(edt_d_davaci, e.detail.value);
			}
			
		});
		edt_d_davaci.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('edt_d_davaci');
		});

		var edt_d_davali = new Tagify(form.querySelector('[name="edt_d_davali"]'), {
			whitelist: [],//localStorage.hedasDosyaDavaliListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		edt_d_davali.on('input', async function (e) {
			edt_d_davali.settings.whitelist.length = 0; // reset current whitelist
			edt_d_davali.loading(true).dropdown.hide.call(edt_d_davali);
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormDavaliSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										edt_d_davali.settings.whitelist.push(...newWhitelist);
										edt_d_davali.loading(false).dropdown.show.call(edt_d_davali, e.detail.value);
									}else{
										edt_d_davali.loading(false).dropdown.show.call(edt_d_davali, e.detail.value);
									}
								}else{
									edt_d_davali.loading(false).dropdown.show.call(edt_d_davali, e.detail.value);
								}
							}else{
								edt_d_davali.loading(false).dropdown.show.call(edt_d_davali, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_davali.loading(false).dropdown.show.call(edt_d_davali, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_davali.loading(false).dropdown.show.call(edt_d_davali, e.detail.value);
			}
		});
		edt_d_davali.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('edt_d_davali');
		});

		var edt_d_davakonusu = new Tagify(form.querySelector('[name="edt_d_davakonusu"]'), {
			whitelist: [],//localStorage.hedasDosyaDavaKonusuListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		edt_d_davakonusu.on('input', async function (e) {
			edt_d_davakonusu.settings.whitelist.length = 0; // reset current whitelist
			edt_d_davakonusu.loading(true).dropdown.hide.call(edt_d_davakonusu);
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormDavaKonuSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										edt_d_davakonusu.settings.whitelist.push(...newWhitelist);
										edt_d_davakonusu.loading(false).dropdown.show.call(edt_d_davakonusu, e.detail.value);
									}else{
										edt_d_davakonusu.loading(false).dropdown.show.call(edt_d_davakonusu, e.detail.value);
									}
								}else{
									edt_d_davakonusu.loading(false).dropdown.show.call(edt_d_davakonusu, e.detail.value);
								}
							}else{
								edt_d_davakonusu.loading(false).dropdown.show.call(edt_d_davakonusu, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_davakonusu.loading(false).dropdown.show.call(edt_d_davakonusu, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_davakonusu.loading(false).dropdown.show.call(edt_d_davakonusu, e.detail.value);
			}
		});
		edt_d_davakonusu.on("change", function(){
			// Revalidate the field when an option is chosen
			validator.revalidateField('edt_d_davakonusu');
		});

		
		var edt_d_konuaciklamasi = new Tagify(form.querySelector('[name="edt_d_konuaciklamasi"]'), {
			whitelist: [],//localStorage.hedasDosyaKonuAciklamasiListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		edt_d_konuaciklamasi.on('input', async function (e) {
			edt_d_konuaciklamasi.settings.whitelist.length = 0; // reset current whitelist
			edt_d_konuaciklamasi.loading(true).dropdown.hide.call(edt_d_konuaciklamasi)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormKonuAciklamaSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										edt_d_konuaciklamasi.settings.whitelist.push(...newWhitelist);
										edt_d_konuaciklamasi.loading(false).dropdown.show.call(edt_d_konuaciklamasi, e.detail.value);
									}else{
										edt_d_konuaciklamasi.loading(false).dropdown.show.call(edt_d_konuaciklamasi, e.detail.value);
									}
								}else{
									edt_d_konuaciklamasi.loading(false).dropdown.show.call(edt_d_konuaciklamasi, e.detail.value);
								}
							}else{
								edt_d_konuaciklamasi.loading(false).dropdown.show.call(edt_d_konuaciklamasi, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_konuaciklamasi.loading(false).dropdown.show.call(edt_d_konuaciklamasi, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_konuaciklamasi.loading(false).dropdown.show.call(edt_d_konuaciklamasi, e.detail.value);
			}
		});



		var edt_d_onamailami = new Tagify(form.querySelector('[name="edt_d_onamailami"]'), {
			whitelist: [],//localStorage.hedasDosyaOnamaIlamiListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		edt_d_onamailami.on('input', async function (e) {
			edt_d_onamailami.settings.whitelist.length = 0; // reset current whitelist
			edt_d_onamailami.loading(true).dropdown.hide.call(edt_d_onamailami)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormOnamaIlamiSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										var oldWhitelist = edt_d_onamailami.settings.whitelist;
										// console.log("oldWhitelist", oldWhitelist);
										// oldWhitelist = newWhitelist.concat(oldWhitelist);
										edt_d_onamailami.settings.whitelist.push(...newWhitelist);
										edt_d_onamailami.loading(false).dropdown.show.call(edt_d_onamailami, e.detail.value);
									}else{
										edt_d_onamailami.loading(false).dropdown.show.call(edt_d_onamailami, e.detail.value);
									}
								}else{
									edt_d_onamailami.loading(false).dropdown.show.call(edt_d_onamailami, e.detail.value);
								}
							}else{
								edt_d_onamailami.loading(false).dropdown.show.call(edt_d_onamailami, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_onamailami.loading(false).dropdown.show.call(edt_d_onamailami, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_onamailami.loading(false).dropdown.show.call(edt_d_onamailami, e.detail.value);
			}
		});

		
		var edt_d_bozmailami = new Tagify(form.querySelector('[name="edt_d_bozmailami"]'), {
			whitelist: [],//localStorage.hedasDosyaBozmaIlamiListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		edt_d_bozmailami.on('input', async function (e) {
			edt_d_bozmailami.settings.whitelist.length = 0; // reset current whitelist
			edt_d_bozmailami.loading(true).dropdown.hide.call(edt_d_bozmailami)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormBozmaIlamiSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										edt_d_bozmailami.settings.whitelist.push(...newWhitelist);
										edt_d_bozmailami.loading(false).dropdown.show.call(edt_d_bozmailami, e.detail.value);
									}else{
										edt_d_bozmailami.loading(false).dropdown.show.call(edt_d_bozmailami, e.detail.value);
									}
								}else{
									edt_d_bozmailami.loading(false).dropdown.show.call(edt_d_bozmailami, e.detail.value);
								}
							}else{
								edt_d_bozmailami.loading(false).dropdown.show.call(edt_d_bozmailami, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_bozmailami.loading(false).dropdown.show.call(edt_d_bozmailami, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_bozmailami.loading(false).dropdown.show.call(edt_d_bozmailami, e.detail.value);
			}
		});

		var edt_d_istinafkabul = new Tagify(form.querySelector('[name="edt_d_istinafkabul"]'), {
			whitelist: [],//localStorage.hedasDosyaIstinafKabulListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		edt_d_istinafkabul.on('input', async function (e) {
			edt_d_istinafkabul.settings.whitelist.length = 0; // reset current whitelist
			edt_d_istinafkabul.loading(true).dropdown.hide.call(edt_d_istinafkabul)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormIstinafKabulSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										edt_d_istinafkabul.settings.whitelist.push(...newWhitelist);
										edt_d_istinafkabul.loading(false).dropdown.show.call(edt_d_istinafkabul, e.detail.value);
									}else{
										edt_d_istinafkabul.loading(false).dropdown.show.call(edt_d_istinafkabul, e.detail.value);
									}
								}else{
									edt_d_istinafkabul.loading(false).dropdown.show.call(edt_d_istinafkabul, e.detail.value);
								}
							}else{
								edt_d_istinafkabul.loading(false).dropdown.show.call(edt_d_istinafkabul, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_istinafkabul.loading(false).dropdown.show.call(edt_d_istinafkabul, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_istinafkabul.loading(false).dropdown.show.call(edt_d_istinafkabul, e.detail.value);
			}
		});

		var edt_d_istinafred = new Tagify(form.querySelector('[name="edt_d_istinafred"]'), {
			whitelist: [],//localStorage.hedasDosyaIstinafRedListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		edt_d_istinafred.on('input', async function (e) {
			edt_d_istinafred.settings.whitelist.length = 0; // reset current whitelist
			edt_d_istinafred.loading(true).dropdown.hide.call(edt_d_istinafred)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormIstinafRedSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										edt_d_istinafred.settings.whitelist.push(...newWhitelist);
										edt_d_istinafred.loading(false).dropdown.show.call(edt_d_istinafred, e.detail.value);
									}else{
										edt_d_istinafred.loading(false).dropdown.show.call(edt_d_istinafred, e.detail.value);
									}
								}else{
									edt_d_istinafred.loading(false).dropdown.show.call(edt_d_istinafred, e.detail.value);
								}
							}else{
								edt_d_istinafred.loading(false).dropdown.show.call(edt_d_istinafred, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_istinafred.loading(false).dropdown.show.call(edt_d_istinafred, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_istinafred.loading(false).dropdown.show.call(edt_d_istinafred, e.detail.value);
			}
		});

		var edt_d_icra = new Tagify(form.querySelector('[name="edt_d_icra"]'), {
			whitelist: [],//localStorage.hedasDosyaMahkemeData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		edt_d_icra.on('input', async function (e) {
			edt_d_icra.settings.whitelist.length = 0; // reset current whitelist
			edt_d_icra.loading(true).dropdown.hide.call(edt_d_icra);
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
										edt_d_icra.settings.whitelist.push(...newWhitelist);
										edt_d_icra.loading(false).dropdown.show.call(edt_d_icra, e.detail.value);
									}else{
										edt_d_icra.loading(false).dropdown.show.call(edt_d_icra, e.detail.value);
									}
								}else{
									edt_d_icra.loading(false).dropdown.show.call(edt_d_icra, e.detail.value);
								}
							}else{
								edt_d_icra.loading(false).dropdown.show.call(edt_d_icra, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_icra.loading(false).dropdown.show.call(edt_d_icra, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_icra.loading(false).dropdown.show.call(edt_d_icra, e.detail.value);
			}
		});

		var edt_d_kesinlestirme = new Tagify(form.querySelector('[name="edt_d_kesinlestirme"]'), {
			whitelist: [],//localStorage.hedasDosyaKesinlestirmeListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		edt_d_kesinlestirme.on('input', async function (e) {
			edt_d_kesinlestirme.settings.whitelist.length = 0; // reset current whitelist
			edt_d_kesinlestirme.loading(true).dropdown.hide.call(edt_d_kesinlestirme)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormKesinlestirmeSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										edt_d_kesinlestirme.settings.whitelist.push(...newWhitelist);
										edt_d_kesinlestirme.loading(false).dropdown.show.call(edt_d_kesinlestirme, e.detail.value);
									}else{
										edt_d_kesinlestirme.loading(false).dropdown.show.call(edt_d_kesinlestirme, e.detail.value);
									}
								}else{
									edt_d_kesinlestirme.loading(false).dropdown.show.call(edt_d_kesinlestirme, e.detail.value);
								}
							}else{
								edt_d_kesinlestirme.loading(false).dropdown.show.call(edt_d_kesinlestirme, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_kesinlestirme.loading(false).dropdown.show.call(edt_d_kesinlestirme, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_kesinlestirme.loading(false).dropdown.show.call(edt_d_kesinlestirme, e.detail.value);
			}
		});

		var edt_d_mirascilik = new Tagify(form.querySelector('[name="edt_d_mirascilik"]'), {
			whitelist: [],//localStorage.hedasDosyaMirascilikListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		edt_d_mirascilik.on('input', async function (e) {
			edt_d_mirascilik.settings.whitelist.length = 0; // reset current whitelist
			edt_d_mirascilik.loading(true).dropdown.hide.call(edt_d_mirascilik)
			if(e.detail.value.length >= 3){
				setTimeout(function() {
					var isData = {
						searchText: e.detail.value
					}	
					$.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						url: baseUrlHost+"api_FormMirascilikSearch",
						data: JSON.stringify(isData),
						success: function (response) {
							if((typeof response)==="object"){
								if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
									if(response.code==200){
										var newWhitelist = response.data.trim().split(/\s*,\s*/);//localStorage.hedasDosyaMahkemelerData.trim().split(/\s*,\s*/);//await getWhitelistFromServer();
										edt_d_mirascilik.settings.whitelist.push(...newWhitelist);
										edt_d_mirascilik.loading(false).dropdown.show.call(edt_d_mirascilik, e.detail.value);
									}else{
										edt_d_mirascilik.loading(false).dropdown.show.call(edt_d_mirascilik, e.detail.value);
									}
								}else{
									edt_d_mirascilik.loading(false).dropdown.show.call(edt_d_mirascilik, e.detail.value);
								}
							}else{
								edt_d_mirascilik.loading(false).dropdown.show.call(edt_d_mirascilik, e.detail.value);
							}
						},
						error: function (hata) {
							edt_d_mirascilik.loading(false).dropdown.show.call(edt_d_mirascilik, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_d_mirascilik.loading(false).dropdown.show.call(edt_d_mirascilik, e.detail.value);
			}
		});

		Inputmask("999999999,99", {
            "numericInput": true
        }).mask("#edt_d_idarialacagi");


		Inputmask("999999999,99", {
            "numericInput": true
        }).mask("#edt_d_vekaletalacagi");

		Inputmask("999999999,99", {
            "numericInput": true
        }).mask("#edt_d_yargilamagideri");

		
		var inputTags = document.querySelector('#edt_d_tags'),
		// Init Tagify script on the above inputs
		tagify = new Tagify(inputTags, {
			whitelist: localStorage.hedasDosyaTagListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: true
		});

		// tagify.on('input', onTagifyInput(tagify))
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
		var suggestions = document.querySelector('#kt_dosya_edt_suggestions');
		// Suggestion item click
		KTUtil.on(suggestions,  '[data-kt-suggestion="true"]', 'click', function(e) {		
			tagify.addTags([this.innerText]);
		});
		
	}	


	// Init form inputs
	var initMahkemeForm = function() {
		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/


		//var ci_itirazeden = new Tagify(form.querySelector('[name="ci_itirazeden"]') NAME OLARAK DA KULLANILABİLİR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		var edt_dm_mahkeme = new Tagify(mahkemeform.querySelector('[name="edt_dm_mahkeme"]'), {
			whitelist: [],//localStorage.hedasDosyaMahkemeData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		edt_dm_mahkeme.on('input', async function (e) {
			edt_dm_mahkeme.settings.whitelist.length = 0; // reset current whitelist
			edt_dm_mahkeme.loading(true).dropdown.hide.call(edt_dm_mahkeme);
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
										// var oldWhitelist = edt_dm_mahkeme.settings.whitelist;
										// oldWhitelist = newWhitelist.concat(oldWhitelist);
										edt_dm_mahkeme.settings.whitelist.push(...newWhitelist);
										edt_dm_mahkeme.loading(false).dropdown.show.call(edt_dm_mahkeme, e.detail.value);
									}else{
										edt_dm_mahkeme.loading(false).dropdown.show.call(edt_dm_mahkeme, e.detail.value);
									}
								}else{
									edt_dm_mahkeme.loading(false).dropdown.show.call(edt_dm_mahkeme, e.detail.value);
								}
							}else{
								edt_dm_mahkeme.loading(false).dropdown.show.call(edt_dm_mahkeme, e.detail.value);
							}
						},
						error: function (hata) {
							edt_dm_mahkeme.loading(false).dropdown.show.call(edt_dm_mahkeme, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_dm_mahkeme.loading(false).dropdown.show.call(edt_dm_mahkeme, e.detail.value);
			}
		});
		edt_dm_mahkeme.on("change", function(){
			// Revalidate the field when an option is chosen
            validatorMahkeme.revalidateField('edt_dm_mahkeme');
		});

        dmAcilisTarihEditFlatP = flatpickr(dmAcilisTarihEditDataP, {
            enableTime: false,
            dateFormat: "d-m-Y",
        });

        dmKararTarihEditFlatP = flatpickr(dmKararTarihEditDataP, {
            enableTime: false,
            dateFormat: "d-m-Y",
        });

		
	}


    // Private functions
    var initMahkemeDatatable = function () {
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX;

		filterMahkemeDataEdit.dmDosyaId = dosyaIdEdit;

        dtEdit = $("#kt_content_dosya_mahkeme_edit_list").DataTable({
            searchDelay: 1000,
            processing: true,
            serverSide: true,
			select: true,
			responsive: true,
			pageLength: 20,
			language: {
                url: baseUrlHost+"/assets/js/moduls/DatatableTurkish.json"
            },
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
                url: baseUrlHost+"/apps/hedas/dosya/api_mahkemelist",
				method: 'POST',
				//dataType: 'json',
				//contentType: 'application/json',
            },
            columns: [
				{ data: '_mahkeme' },
				{ data: '_acilistarihi' },
				{ data: '_esasno' },
				{ data: '_kararno' },
				{ data: '_karartarihi' },
				{ data: '_aciklama' },
            ],
            columnDefs: [
				{
					targets: 0,
					data: null,
					orderable: true,
					className: 'text-left',
					render: function (data, type, row) { 
						return row._mahkeme;
					}
				}
            ],
            // Add data-filter attribute
			/*
            createdRow: function (row, data, dataIndex) {
                $(row).find('td:eq(1)').attr('data-filter', data._tarih);
            }
			*/
        }).columns([0,1,2,3,4,5])
        .flatten()
        .search(JSON.stringify(filterMahkemeDataEdit));

        tableEdit = dtEdit.$;

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dtEdit.on('draw', function () {
           // initToggleToolbar();
           // toggleToolbars();
            //handleDeleteRows();
            KTMenu.createInstances();
        })
        .on( 'select', function ( e, dt, type, indexes ) {
            var rowD = dtEdit.rows( indexes ).data().toArray();
			var rowData = rowD[0];

			editMahkemeData = {
				dmMahkemeId: rowData._id,
				dmDosyaId: dosyaIdEdit,
				dmAcilisTarihi: rowData._acilistarihi,
				dmKararTarihi: rowData._karartarihi,
				dmEsasNo: rowData._esasno,
				dmKararNo: rowData._kararno,
				dmMahkeme: rowData._mahkemeEditId,
				dmAciklama: rowData._aciklama
			}
			handleEditMahkemeForm();

		} )
        .on( 'deselect', function ( e, dt, type, indexes ) {
            var rowData = dtEdit.rows( indexes ).data().toArray();
			addButtonM.disabled = false;
			addButtonM.classList.remove('d-none');
			submitButtonM.classList.add('d-none');
			deleteButtonM.classList.add('d-none');
			submitButtonM.disabled = true;
			deleteButtonM.disabled = true;
			mahkemeform.reset();
			$("#edt_dm_mahkeme").val([""]).select2();
			
			//console.log(JSON.stringify( rowData ));
            //events.prepend( '<div><b>'+type+' <i>de</i>selection</b> - '+JSON.stringify( rowData )+'</div>' );
        });
    }

	
	var handleDataTableReload = () => {
		dtEdit
		.columns([0,1,2,3,4,5])
		.flatten()
		.search(JSON.stringify(filterMahkemeDataEdit))
		.draw();		
	}

	const handleCancelButton = () => {

		cancelButton.addEventListener('click', function (e) {
			//e.preventDefault();
			modal.hide(); // Hide modal				
			/*
			Swal.fire({
				text: "Vazgeçmek istediğinizden emin misiniz?",
				icon: "warning",
				showCancelButton: true,
				buttonsStyling: false,
				confirmButtonText: "Evet, Vazgeç!",
				cancelButtonText: "Hayır, geri dön",
				customClass: {
					confirmButton: "btn btn-primary",
					cancelButton: "btn btn-danger"
				}
			}).then(function (result) {
				if (result.value) {
					modal.hide(); // Hide modal				
				}
			});
			*/
		});
		
	}

	
    const handleEditEvent = () => {

		inpKlasorNo.value = editData.dKlasorNo;
		inpDosyaNo.value = editData.dDosyaNo;
		inpDavaci.value = editData.dDavaci;
		inpDavali.value = editData.dDavali;
		inpDavaKonusu.value = editData.dDavaKonusu;
		inpKonuAciklamasi.value = editData.dKonuAciklamasi;
		inpProjeBilgiEdit.root.innerHTML = editData.dProjeBilgi;
		inpMevkiEdit.root.innerHTML = editData.dMevkiBilgi;
		inpIstinafBilgi.value = editData.dIstinafBilgi;
		inpTemyizBilgi.value = editData.dTemyizBilgi;
		inpOnamaIlami.value = editData.dOnamaIlami;
		inpBozmaIlami.value = editData.dBozmaIlami;
		inpIstinafKabul.value = editData.dIstinafKabul;
		inpIstinafRed.value = editData.dIstinafRed;
		inpIcraNo.value = editData.dIcraNo;
		inpIcra.value = editData.dIcra;
		inpKesinlestirme.value = editData.dKesinlestirme;
		inpMirascilik.value = editData.dMirascilik;
		inpIdariAlacagi.value = editData.dIdariAlacagi;
		inpVekaletAlacagi.value = editData.dVekaletAlacagi;
		inpYargilamaGideri.value = editData.dYargilamaGideri;
		viewTapuBilgi.val(editData.dTapuBilgi).trigger('change');
		inpAciklamaEdit.root.innerHTML = editData.dAciklama;
		inpTags.value = editData.dTags;

		filterMahkemeDataEdit.dmDosyaId = dosyaIdEdit;

		modal.show();

		handleDataTableReload();
		handleCancelButton();

		handleUpdateEvent();        
	}


	const handleUpdateEvent = () => {

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
						var htmlProjeBilgi = inpProjeBilgiEdit.root.innerHTML;//htmlProjeBilgi.getElementsByClassName('ql-editor');
						var htmlMevkiBilgi = inpMevkiEdit.root.innerHTML;
						var htmlAciklama = inpAciklamaEdit.root.innerHTML;

						
						setTimeout(function() {
							submitButton.removeAttribute('data-kt-indicator');

							// Enable button
							
							editData = {
								route: "updatedavadosya",
								dDosyaId: dosyaIdEdit,
								dKlasorNo: inpKlasorNo.value,
								dDosyaNo: inpDosyaNo.value,
								dDavaci: inpDavaci.value,
								dDavali: inpDavali.value,
								dDavaKonusu: inpDavaKonusu.value,
								dKonuAciklamasi: inpKonuAciklamasi.value,
								dProjeBilgi: htmlProjeBilgi,//inpProjeBilgi.getContents(0, inpProjeBilgi.getLength()),
								dMevkiBilgi: htmlMevkiBilgi,//inpMevki.getText(0, inpMevki.getLength()),
								dIstinafBilgi: inpIstinafBilgi.value,
								dTemyizBilgi: inpTemyizBilgi.value,
								dOnamaIlami: inpOnamaIlami.value,
								dBozmaIlami: inpBozmaIlami.value,
								dIstinafKabul: inpIstinafKabul.value,
								dIstinafRed: inpIstinafRed.value,
								dIcraNo: inpIcraNo.value,
								dIcra: inpIcra.value,
								dKesinlestirme: inpKesinlestirme.value,
								dMirascilik: inpMirascilik.value,
								dIdariAlacagi: inpIdariAlacagi.value,
								dVekaletAlacagi: inpVekaletAlacagi.value,
								dYargilamaGideri: inpYargilamaGideri.value,
								dTapuBilgi: inpTapuBilgi.value,
								dAciklama: htmlAciklama,//inpAciklama.getText(0, inpAciklama.getLength()),
								dTags: inpTags.value
							};

							//console.log("postData",postData);
							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							/*
							Swal.fire({
								text: "Dosya Bilgisi Güncellenecek!",
								icon: "info",
								buttonsStyling: false,
								confirmButtonText: "Tamam",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							}).then(function (result) {
								if (result.isConfirmed) {
								*/

									$.ajax({
                                        type: "POST",
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json",
                                        url: baseUrlHost+"api_updaterecord",
                                        data: JSON.stringify(editData),
                                        success: function (response) {

                                            if((typeof response)==="object"){
                                                if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                                                    if(response.code==200){
														if(isNaN(response.dosyaid)==false && response.dosyaid>0){
															if(modulUrl.indexOf('dosya')>0){
																// 			//KTGelenGidenListServerSide.init();
																//$('#filtreleButton').click();
																KTDosyaListServerSide.reload();
																document.location.reload();
															}
															toastrAlert('success','Başarılı!','Dosya Başarıyla Güncellendi.');
															KTModalEditDosya.loadedFunctionList();
															
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
															//Form verilerindeki hataları bas
														}
														submitButton.disabled = false;
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
                                        }
									});
							//	}
							//});
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
	}

	const handleEditMahkemeForm = () => {
		console.log("mahkeme edit acildi : "+editMahkemeData.dmMahkeme);
		inpEsasNo.value = editMahkemeData.dmEsasNo;
		inpKararNo.value = editMahkemeData.dmKararNo;
		var edtTxtAll=editMahkemeData.dmMahkeme;
		var mahkemeVals=edtTxtAll.split(",");

		$("#edt_dm_mahkeme").val(mahkemeVals).select2();
		inpMahkemeAciklama.value = editMahkemeData.dmAciklama;

		dmAcilisTarihEditFlatP.setDate(editMahkemeData.dmAcilisTarihi, true, 'd-m-Y');
        dmKararTarihEditFlatP.setDate(editMahkemeData.dmKararTarihi, true, 'd-m-Y');

		// addButtonM.disabled = true;
		// addButtonM.classList.add('d-none');
		// submitButtonM.classList.remove('d-none');
		// deleteButtonM.classList.remove('d-none');
		// submitButtonM.disabled = false;
		// deleteButtonM.disabled = false;
		addButtonPasiflestir();

		handleUpdateMahkemeEvent();
		handleDeleteMahkemeEvent();

	}

	const handleUpdateMahkemeEvent = () => {

		// Action buttons
		submitButtonM.addEventListener('click', function (e) {
			e.preventDefault();
			
			// Validate form before submit
			if (validatorMahkeme) {
				validatorMahkeme.validate().then(function (status) {
					console.log('validated!');

					if (status == 'Valid') {
						submitButtonM.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple click 
						submitButtonM.disabled = true;
						
						setTimeout(function() {
							submitButtonM.removeAttribute('data-kt-indicator');
							// Enable button
							
							editMahkemeData.route ='updatedosyamahkeme';
							editMahkemeData.dmAcilisTarihi = moment(dmAcilisTarihEditFlatP.selectedDates[0]).format('DD-MM-YYYY');
							editMahkemeData.dmKararTarihi = dmKararTarihEditDataP.value;//moment(dmKararTarihEditFlatP.selectedDates[0]).format('DD-MM-YYYY');
							editMahkemeData.dmEsasNo = inpEsasNo.value;
							editMahkemeData.dmKararNo = inpKararNo.value;
							editMahkemeData.dmMahkeme = inpMahkemeJq.val().toString();
							editMahkemeData.dmAciklama = inpMahkemeAciklama.value;
							
					
							$.ajax({
								type: "POST",
								contentType: "application/json; charset=utf-8",
								dataType: "json",
								url: baseUrlHost+"api_updatemahkemerecord",
								data: JSON.stringify(editMahkemeData),
								success: function (response) {
									if((typeof response)==="object"){
										if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
											if(response.code==200){
												if(isNaN(response.mahkemeid)==false && response.mahkemeid>0){
													toastrAlert('success','Başarılı!','Mahkeme Bilgisi Başarıyla Güncellendi.');
													handleDataTableReload();
													//KTModalEditDosya.loadedFunctionList();
													KTDosyaListServerSide.reload();
													mahkemeform.reset();
													$("#edt_dm_mahkeme").val([""]).select2();
													//submitButtonM.disabled = true;
													addButtonAktiflestir();
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
													//Form verilerindeki hataları bas
													submitButtonM.disabled = false;
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
												submitButtonM.disabled = false;
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
												submitButtonM.disabled = false;
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
											submitButtonM.disabled = false;
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
										submitButtonM.disabled = false;
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
									submitButtonM.disabled = false;

									//console.log(hata);
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
		
	}


	const handleDeleteMahkemeEvent = () => {

		// Action buttons
		deleteButtonM.addEventListener('click', function (e) {
			e.preventDefault();

			deleteButtonM.setAttribute('data-kt-indicator', 'on');

			// Disable button to avoid multiple click 
			deleteButtonM.disabled = true;
			
			setTimeout(function() {
				deleteButtonM.removeAttribute('data-kt-indicator');

				// Enable button
				editMahkemeData.route ='deletedosyamahkeme';
				// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
				Swal.fire({
					text: "Mahkeme Bilgisi Silinecek! Emin misiniz?",
					icon: "warning",
					showCancelButton: true,
					buttonsStyling: false,
					confirmButtonText: "Sil!",
                	cancelButtonText: "Vazgeç",
                	customClass: {
                    	confirmButton: "btn btn-primary",
                    	cancelButton: "btn btn-danger"
                	}
				}).then(function (result) {
					if (result.isConfirmed) {
						$.ajax({
							type: "POST",
							contentType: "application/json; charset=utf-8",
							dataType: "json",
							url: baseUrlHost+"api_deletemahkemerecord",
							data: JSON.stringify(editMahkemeData),
							success: function (response) {
								if((typeof response)==="object"){
									if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
										if(response.code==200){
											if(isNaN(response.mahkemeid)==false && response.mahkemeid>0){
												toastrAlert('success','Başarılı!','Mahkeme Bilgisi Başarıyla Güncellendi.');
												handleDataTableReload();
												KTModalEditDosya.loadedFunctionList();
												KTDosyaListServerSide.reload();
												mahkemeform.reset();
												$("#edt_dm_mahkeme").val([""]).select2();
												//submitButtonM.disabled = true;
												addButtonAktiflestir();
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
												//Form verilerindeki hataları bas
												deleteButtonM.disabled = false;
											}
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
											deleteButtonM.disabled = false;
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
											deleteButtonM.disabled = false;
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
										deleteButtonM.disabled = false;
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
									deleteButtonM.disabled = false;
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
								deleteButtonM.disabled = false;

								//console.log(hata);
							}
						});
						
						//modal.hide();
					}else{
						deleteButtonM.disabled = false;
					}
				});

				//form.submit(); // Submit form
			}, 1000);   						


		});
		
	}


	const handleAddMahkemeEvent = () => {

		// Action buttons
		addButtonM.addEventListener('click', function (e) {
			e.preventDefault();
			
			// Validate form before submit
			if (validatorMahkeme) {
				validatorMahkeme.validate().then(function (status) {
					// console.log('validated!');

					if (status == 'Valid') {
						addButtonM.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple click 
						addButtonM.disabled = true;
						
						setTimeout(function() {
							addButtonM.removeAttribute('data-kt-indicator');

							// Enable button
							editMahkemeData.dmDosyaId = dosyaIdEdit;
							editMahkemeData.route ='adddosyamahkeme';
							editMahkemeData.dmAcilisTarihi = moment(dmAcilisTarihEditFlatP.selectedDates[0]).format('DD-MM-YYYY');
							editMahkemeData.dmKararTarihi = dmKararTarihEditDataP.value;//moment(dmKararTarihEditFlatP.selectedDates[0]).format('DD-MM-YYYY');
							editMahkemeData.dmEsasNo = inpEsasNo.value;
							editMahkemeData.dmKararNo = inpKararNo.value;
							editMahkemeData.dmMahkeme = inpMahkemeJq.val().toString();
							editMahkemeData.dmAciklama = inpMahkemeAciklama.value;
							
		
							$.ajax({
								type: "POST",
								contentType: "application/json; charset=utf-8",
								dataType: "json",
								url: baseUrlHost+"api_newmahkemerecord",
								data: JSON.stringify(editMahkemeData),
								success: function (response) {
									if((typeof response)==="object"){
										if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
											if(response.code==200){
												if(isNaN(response.mahkemeid)==false && response.mahkemeid>0){
													toastrAlert('success','Başarılı!',response.description);
													handleDataTableReload();
													KTModalEditDosya.loadedFunctionList();
													KTDosyaListServerSide.reload();
													mahkemeform.reset();
													$("#edt_dm_mahkeme").val([""]).select2();
													//submitButtonM.disabled = true;
													addButtonAktiflestir();
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
													//Form verilerindeki hataları bas
													addButtonM.disabled = false;
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
												addButtonM.disabled = false;
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
												addButtonM.disabled = false;
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
											addButtonM.disabled = false;
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
										addButtonM.disabled = false;
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
									addButtonM.disabled = false;

									//console.log(hata);
								}
							});
							
							//modal.hide();
				

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
		
	}

	const addButtonAktiflestir = () => {
		addButtonM.disabled = false;
		addButtonM.classList.remove('d-none');
		submitButtonM.classList.add('d-none');
		deleteButtonM.classList.add('d-none');
		submitButtonM.disabled = true;
		deleteButtonM.disabled = true;
	}

	const addButtonPasiflestir = () => {
		addButtonM.disabled = true;
		addButtonM.classList.add('d-none');
		submitButtonM.classList.remove('d-none');
		deleteButtonM.classList.remove('d-none');
		submitButtonM.disabled = false;
		deleteButtonM.disabled = false;
	}


	return {
		populate: function (){
			// Elements
			modalEl = document.querySelector('#kt_modal_update_dosya');

			if (!modalEl) {
				return;
			}


			modal = new bootstrap.Modal(modalEl);
			
			const element = document.getElementById('kt_modal_update_dosya');
			form = document.querySelector('#kt_modal_update_dosya_form');
			submitButton = document.getElementById('kt_modal_update_dosya_submit');
			cancelButton = document.getElementById('kt_modal_update_dosya_cancel');

			inpKlasorNo = form.querySelector('[name="edt_d_klasorno"]');
			inpDosyaNo = form.querySelector('[name="edt_d_dosyano"]');
			inpDavaci = form.querySelector('[name="edt_d_davaci"]');
			inpDavali = form.querySelector('[name="edt_d_davali"]');
			inpDavaKonusu = form.querySelector('[name="edt_d_davakonusu"]');
			inpKonuAciklamasi = form.querySelector('[name="edt_d_konuaciklamasi"]');
			inpIstinafBilgi = form.querySelector('[name="edt_d_istinafbilgi"]');
			inpTemyizBilgi = form.querySelector('[name="edt_d_temyizbilgi"]');
			inpOnamaIlami = form.querySelector('[name="edt_d_onamailami"]');
			inpBozmaIlami = form.querySelector('[name="edt_d_bozmailami"]');
			inpIstinafKabul = form.querySelector('[name="edt_d_istinafkabul"]');
			inpIstinafRed = form.querySelector('[name="edt_d_istinafred"]');
			inpIcraNo = form.querySelector('[name="edt_d_icrano"]');
			inpIcra = form.querySelector('[name="edt_d_icra"]');
			inpKesinlestirme = form.querySelector('[name="edt_d_kesinlestirme"]');
			inpMirascilik = form.querySelector('[name="edt_d_mirascilik"]');
			inpIdariAlacagi = form.querySelector('[name="edt_d_idarialacagi"]');
			inpVekaletAlacagi = form.querySelector('[name="edt_d_vekaletalacagi"]');
			inpYargilamaGideri = form.querySelector('[name="edt_d_yargilamagideri"]');
			inpTapuBilgi = form.querySelector('[name="edt_d_tapubilgi"]');
			viewTapuBilgi = $('#edt_d_tapubilgi'); 
			inpTags = form.querySelector('[name="edt_d_tags"]');

			divdosyaBilgileriEdit = document.querySelector('#dosyaBilgileriEdit');
			divMahkemeBilgileriEdit = document.querySelector('#dosyaMahkemeBilgileriEdit');
			cardDosyaBilgiEdit = document.querySelector('#kt_docs_card_update_dosyabilgi');
			cardMahkemeBilgiEdit = document.querySelector('#kt_docs_card_update_mahkemebilgi');


			mahkemeform = document.querySelector('#kt_modal_update_dosya_mahkeme_form');
			submitButtonM = document.getElementById('kt_modal_update_dosya_mahkeme_submit');
			deleteButtonM = document.getElementById('kt_modal_update_dosya_mahkeme_delete');
			addButtonM	= document.getElementById('kt_modal_update_dosya_mahkeme_add');

			inpEsasNo = mahkemeform.querySelector('[name="edt_dm_esasno"]');
			inpKararNo = mahkemeform.querySelector('[name="edt_dm_kararno"]');
			inpMahkemeJq = $("#edt_dm_mahkeme");
			inpMahkemeAciklama = mahkemeform.querySelector('[name="edt_dm_aciklama"]');

			dmAcilisTarihEditDataP = mahkemeform.querySelector('#edt_dm_acilistarihi');
            dmKararTarihEditDataP = mahkemeform.querySelector('#edt_dm_karartarihi');



			initForm();
			initMahkemeForm();
			initMahkemeDatatable();
			
			initValidator();
			initMahkemeValidator();
			resetFormValidator(element);
			handleDataTableReload();
			handleAddMahkemeEvent();
			addButtonAktiflestir();
			
		},
		viewModal: function(){
            dosyaIdEdit = $('#kt_modal_dosya_preview_edit_btn').attr('data-id');
            var postData = {
                id: dosyaIdEdit
            }
            $.ajax({
                type: "POST",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                url: baseUrlHost+"api_getDosya",
                data: JSON.stringify(postData),
                success: function (response) {
                    if((typeof response)==="object"){
                        if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                            if(response.code==200){

							
								editData.dKlasorNo = response.data._arsivno;
								editData.dDosyaNo = response.data._kurumdosyano;
								editData.dDavaci = response.data._davaci;
								editData.dDavali = response.data._davali;
								editData.dDavaKonusu = response.data._davakonusu;
								editData.dKonuAciklamasi = response.data._davakonuaciklama;
								editData.dProjeBilgi = response.data._projebilgisi;
								editData.dMevkiBilgi = response.data._mevkiplaka;
								editData.dIstinafBilgi = response.data._istinaftemyiz;
								editData.dTemyizBilgi = response.data._temyiz;
								editData.dOnamaIlami = response.data._onamailami;
								editData.dBozmaIlami = response.data._bozmailami;
								editData.dIstinafKabul = response.data._istinafkabul;
								editData.dIstinafRed = response.data._istinafred;
								editData.dIcraNo = response.data._icrakayitno;
								editData.dIcra = response.data._icra;
								editData.dKesinlestirme = response.data._kararkesinlestirme;
								editData.dMirascilik = response.data._mirascilik;
								editData.dIdariAlacagi = response.data._idarialacagi;
								editData.dVekaletAlacagi = response.data._vekaletalacagi;
								editData.dYargilamaGideri = response.data._yargilamagideri;
								editData.dTapuBilgi = response.data._tapubilgisi;
								editData.dAciklama = response.data._aciklama;
								editData.dTags = response.data._tags;

                                KTModalEditDosya.init();

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
		init: function(){

			handleEditEvent();

		},
		loadedFunctionList: function(){
			// this.tagWhitelist();
			// this.davaciWhiteList();
			// this.davaliWhiteList();
			// this.davakonuWhiteList();
			// this.konuaciklamaWhiteList();
			// this.onamailamiWhiteList();
			// this.bozmailamiWhiteList();
			// this.istinafkabulWhiteList();
			// this.istinafredWhiteList();
			// this.mahkemeler();
			// this.kesinlestirmeWhiteList();
			// this.mirascilikWhiteList();
		},
		tagWhitelist: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormTagWhiteList/";
			if(jsisset(localStorage.hedasDosyaTagListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					//sessionStorage.hedasDosyaTagListData = res.data;
					localStorage.hedasDosyaTagListData = res.data;
				});
			}
		},
		davaciWhiteList: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormDavaciWhiteList/";
			if(jsisset(localStorage.hedasDosyaDavaciListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaDavaciListData = res.data;
					localStorage.hedasDosyaDavaciListData = res.data;
				});
			}
		},
		davaliWhiteList: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormDavaliWhiteList/";
			if(jsisset(localStorage.hedasDosyaDavaliListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaDavaliListData = res.data;
					localStorage.hedasDosyaDavaliListData = res.data;
				});
			}
		},
		davakonuWhiteList: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormDavaKonuWhiteList/";
			if(jsisset(localStorage.hedasDosyaDavaKonusuListData)==false){
				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaDavaKonusuListData = res.data;
					localStorage.hedasDosyaDavaKonusuListData = res.data;
				});
			}
		},
		konuaciklamaWhiteList: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormKonuAciklamaWhiteList/";
			if(jsisset(localStorage.hedasDosyaKonuAciklamasiListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaKonuAciklamasiListData = res.data;
					localStorage.hedasDosyaKonuAciklamasiListData = res.data;
				});
			}
		},
		onamailamiWhiteList: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormOnamaIlamiWhiteList/";
			if(jsisset(localStorage.hedasDosyaOnamaIlamiListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaOnamaIlamiListData = res.data;
					localStorage.hedasDosyaOnamaIlamiListData = res.data;
				});
			}
		},
		bozmailamiWhiteList: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormBozmaIlamiWhiteList/";
			if(jsisset(localStorage.hedasDosyaBozmaIlamiListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaBozmaIlamiListData = res.data;
					localStorage.hedasDosyaBozmaIlamiListData = res.data;
				});
			}
		},
		istinafkabulWhiteList: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormIstinafKabulWhiteList/";
			if(jsisset(localStorage.hedasDosyaIstinafKabulListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaIstinafKabulListData = res.data;
					localStorage.hedasDosyaIstinafKabulListData = res.data;
				});
			}
		},
		istinafredWhiteList: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormIstinafRedWhiteList/";
			if(jsisset(localStorage.hedasDosyaIstinafRedListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaIstinafRedListData = res.data;
					localStorage.hedasDosyaIstinafRedListData = res.data;
				});
			}
		},
		mahkemeler: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_mahkemeler/";
			if(jsisset(localStorage.hedasDosyaIstinafRedListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaMahkemeData = res.data;
					localStorage.hedasDosyaMahkemeData = res.data;
				});
			}
		},
		kesinlestirmeWhiteList: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormKesinlestirmeWhiteList/";
			if(jsisset(localStorage.hedasDosyaKesinlestirmeListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaKesinlestirmeListData = res.data;
					localStorage.hedasDosyaKesinlestirmeListData = res.data;
				});
			}
		},
		mirascilikWhiteList: function(){
			var mHost = window.location.host;
			var mHUrl = "//"+mHost+"/apps/hedas/dosya/api_FormMirascilikWhiteList/";
			if(jsisset(localStorage.hedasDosyaMirascilikListData)==false){

				$.get(mHUrl,function(res){
					//console.log(res.data);
					// sessionStorage.hedasDosyaMirascilikListData = res.data;
					localStorage.hedasDosyaMirascilikListData = res.data;
				});
			}
		}
	}

}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTModalEditDosya.populate();
});

inpProjeBilgiEdit = new Quill('#edt_d_projebilgi', {
	modules: {
		toolbar: [
			[{
				header: [2,3, false]
			}],
			[{'color': COLORS},'bold', 'italic', 'underline']
		]
	},
	placeholder: 'Açıklamanızı buraya yazınız...',
	theme: 'snow' // or 'bubble'
});

inpMevkiEdit = new Quill('#edt_d_mevkiplaka', {
	modules: {
		toolbar: [
			[{
				header: [2,3, false]
			}],
			[{'color': COLORS},'bold', 'italic', 'underline']
		]
	},
	placeholder: 'Açıklamanızı buraya yazınız...',
	theme: 'snow' // or 'bubble'
});



inpAciklamaEdit = new Quill('#edt_d_aciklama', {
	modules: {
		toolbar: [
			[{
				header: [2,3, false]
			}],
			[{'color': COLORS},'bold', 'italic', 'underline']
		]
	},
	placeholder: 'Açıklamanızı buraya yazınız...',
	theme: 'snow' // or 'bubble'
});
