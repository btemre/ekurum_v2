"use strict";

var tagsWhiteList;

var divDosyaBilgileri;
var divMahkemeBilgileri;
var cardDosyaBilgi;
var cardMahkemeBilgi;
var inpProjeBilgi;
var inpMevki;
var inpAciklama;
var dosyaId = 0;
var kurumDosyaNo = -1;

var table;
var dt;
var dmKararTarih;
var dmAcilisTarih;
var dmKararTarihAddDataP;



// Class definition
var KTModalDosyaNewTarget = function () {
	var submitButton;
	var submitButtonM;
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
	var inpTags;


	var inpMahkemeJq;
	
	var inpEsasNo;
	var inpKararNo;
	var inpMahkemeAciklama;
	


	var hostUrl = window.location.host;
	const baseUrlHost = "//"+hostUrl+"/apps/hedas/dosya/";
	var modulUrl = window.location.href;
	//console.log("modulUrl", modulUrl, baseUrlHost);

	var postData ={
		route: "",
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

	var filterMahkemeData = {
        dmText: '',
        dmDosyaId: '',
		dmAcilisTarihi: '',
		dmEsasNo: '',
		dmKararTarihi: '',
		dmKararNo: '',
		dmMahkeme: '',
		dmAciklama: ''
	};


	var mahkemePostData = {
		route: '',
		dmDosyaId: '',
		dmAcilisTarihi: '',
		dmKararTarihi: '',
		dmEsasNo: '',
		dmKararNo: '',
		dmMahkeme: '',
		dmAciklama: ''
	}

 
	// Init form inputs
	var initForm = function() {
		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/
		//var ci_itirazeden = new Tagify(form.querySelector('[name="ci_itirazeden"]') NAME OLARAK DA KULLANILABİLİR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		var d_davaci = new Tagify(form.querySelector('[name="d_davaci"]'), {
			whitelist: [],//sessionStorage.hedasDosyaDavaciListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		d_davaci.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('d_davaci');
		});

		d_davaci.on('input', async function (e) {
			d_davaci.settings.whitelist.length = 0; // reset current whitelist
			d_davaci.loading(true).dropdown.hide.call(d_davaci);
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
										d_davaci.settings.whitelist.push(...newWhitelist);
										d_davaci.loading(false).dropdown.show.call(d_davaci, e.detail.value);
									}else{
										d_davaci.loading(false).dropdown.show.call(d_davaci, e.detail.value);
									}
								}else{
									d_davaci.loading(false).dropdown.show.call(d_davaci, e.detail.value);
								}
							}else{
								d_davaci.loading(false).dropdown.show.call(d_davaci, e.detail.value);
							}
						},
						error: function (hata) {
							d_davaci.loading(false).dropdown.show.call(d_davaci, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_davaci.loading(false).dropdown.show.call(d_davaci, e.detail.value);
			}
			
		});

		

		var d_davali = new Tagify(form.querySelector('[name="d_davali"]'), {
			whitelist: [],//sessionStorage.hedasDosyaDavaliListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		d_davali.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('d_davali');
		});


		d_davali.on('input', async function (e) {
			d_davali.settings.whitelist.length = 0; // reset current whitelist
			d_davali.loading(true).dropdown.hide.call(d_davali);
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
										d_davali.settings.whitelist.push(...newWhitelist);
										d_davali.loading(false).dropdown.show.call(d_davali, e.detail.value);
									}else{
										d_davali.loading(false).dropdown.show.call(d_davali, e.detail.value);
									}
								}else{
									d_davali.loading(false).dropdown.show.call(d_davali, e.detail.value);
								}
							}else{
								d_davali.loading(false).dropdown.show.call(d_davali, e.detail.value);
							}
						},
						error: function (hata) {
							d_davali.loading(false).dropdown.show.call(d_davali, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_davali.loading(false).dropdown.show.call(d_davali, e.detail.value);
			}
		});


		
		var d_davakonusu = new Tagify(form.querySelector('[name="d_davakonusu"]'), {
			whitelist: [],//localStorage.hedasDosyaDavaKonusuListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		d_davakonusu.on("change", function(){
			// Revalidate the field when an option is chosen
			validator.revalidateField('d_davakonusu');
		});

		d_davakonusu.on('input', async function (e) {
			d_davakonusu.settings.whitelist.length = 0; // reset current whitelist
			d_davakonusu.loading(true).dropdown.hide.call(d_davakonusu);
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
										d_davakonusu.settings.whitelist.push(...newWhitelist);
										d_davakonusu.loading(false).dropdown.show.call(d_davakonusu, e.detail.value);
									}else{
										d_davakonusu.loading(false).dropdown.show.call(d_davakonusu, e.detail.value);
									}
								}else{
									d_davakonusu.loading(false).dropdown.show.call(d_davakonusu, e.detail.value);
								}
							}else{
								d_davakonusu.loading(false).dropdown.show.call(d_davakonusu, e.detail.value);
							}
						},
						error: function (hata) {
							d_davakonusu.loading(false).dropdown.show.call(d_davakonusu, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_davakonusu.loading(false).dropdown.show.call(d_davakonusu, e.detail.value);
			}
		});

		
		var d_konuaciklamasi = new Tagify(form.querySelector('[name="d_konuaciklamasi"]'), {
			whitelist: [],//localStorage.hedasDosyaKonuAciklamasiListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		d_konuaciklamasi.on('input', async function (e) {
			d_konuaciklamasi.settings.whitelist.length = 0; // reset current whitelist
			d_konuaciklamasi.loading(true).dropdown.hide.call(d_konuaciklamasi)
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
										d_konuaciklamasi.settings.whitelist.push(...newWhitelist);
										d_konuaciklamasi.loading(false).dropdown.show.call(d_konuaciklamasi, e.detail.value);
									}else{
										d_konuaciklamasi.loading(false).dropdown.show.call(d_konuaciklamasi, e.detail.value);
									}
								}else{
									d_konuaciklamasi.loading(false).dropdown.show.call(d_konuaciklamasi, e.detail.value);
								}
							}else{
								d_konuaciklamasi.loading(false).dropdown.show.call(d_konuaciklamasi, e.detail.value);
							}
						},
						error: function (hata) {
							d_konuaciklamasi.loading(false).dropdown.show.call(d_konuaciklamasi, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_konuaciklamasi.loading(false).dropdown.show.call(d_konuaciklamasi, e.detail.value);
			}
		});

		var d_onamailami = new Tagify(form.querySelector('[name="d_onamailami"]'), {
			whitelist: [],//sessionStorage.hedasDosyaOnamaIlamiListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		d_onamailami.on('input', async function (e) {
			d_onamailami.settings.whitelist.length = 0; // reset current whitelist
			d_onamailami.loading(true).dropdown.hide.call(d_onamailami)
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
										d_onamailami.settings.whitelist.push(...newWhitelist);
										d_onamailami.loading(false).dropdown.show.call(d_onamailami, e.detail.value);
									}else{
										d_onamailami.loading(false).dropdown.show.call(d_onamailami, e.detail.value);
									}
								}else{
									d_onamailami.loading(false).dropdown.show.call(d_onamailami, e.detail.value);
								}
							}else{
								d_onamailami.loading(false).dropdown.show.call(d_onamailami, e.detail.value);
							}
						},
						error: function (hata) {
							d_onamailami.loading(false).dropdown.show.call(d_onamailami, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_onamailami.loading(false).dropdown.show.call(d_onamailami, e.detail.value);
			}
		});

		var d_bozmailami = new Tagify(form.querySelector('[name="d_bozmailami"]'), {
			whitelist: [],//sessionStorage.hedasDosyaBozmaIlamiListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		d_bozmailami.on('input', async function (e) {
			d_bozmailami.settings.whitelist.length = 0; // reset current whitelist
			d_bozmailami.loading(true).dropdown.hide.call(d_bozmailami)
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
										d_bozmailami.settings.whitelist.push(...newWhitelist);
										d_bozmailami.loading(false).dropdown.show.call(d_bozmailami, e.detail.value);
									}else{
										d_bozmailami.loading(false).dropdown.show.call(d_bozmailami, e.detail.value);
									}
								}else{
									d_bozmailami.loading(false).dropdown.show.call(d_bozmailami, e.detail.value);
								}
							}else{
								d_bozmailami.loading(false).dropdown.show.call(d_bozmailami, e.detail.value);
							}
						},
						error: function (hata) {
							d_bozmailami.loading(false).dropdown.show.call(d_bozmailami, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_bozmailami.loading(false).dropdown.show.call(d_bozmailami, e.detail.value);
			}
		});

		var d_istinafkabul = new Tagify(form.querySelector('[name="d_istinafkabul"]'), {
			whitelist: [],//sessionStorage.hedasDosyaIstinafKabulListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		d_istinafkabul.on('input', async function (e) {
			d_istinafkabul.settings.whitelist.length = 0; // reset current whitelist
			d_istinafkabul.loading(true).dropdown.hide.call(d_istinafkabul)
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
										d_istinafkabul.settings.whitelist.push(...newWhitelist);
										d_istinafkabul.loading(false).dropdown.show.call(d_istinafkabul, e.detail.value);
									}else{
										d_istinafkabul.loading(false).dropdown.show.call(d_istinafkabul, e.detail.value);
									}
								}else{
									d_istinafkabul.loading(false).dropdown.show.call(d_istinafkabul, e.detail.value);
								}
							}else{
								d_istinafkabul.loading(false).dropdown.show.call(d_istinafkabul, e.detail.value);
							}
						},
						error: function (hata) {
							d_istinafkabul.loading(false).dropdown.show.call(d_istinafkabul, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_istinafkabul.loading(false).dropdown.show.call(d_istinafkabul, e.detail.value);
			}
		});

		var d_istinafred = new Tagify(form.querySelector('[name="d_istinafred"]'), {
			whitelist: [],//sessionStorage.hedasDosyaIstinafRedListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		d_istinafred.on('input', async function (e) {
			d_istinafred.settings.whitelist.length = 0; // reset current whitelist
			d_istinafred.loading(true).dropdown.hide.call(d_istinafred)
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
										d_istinafred.settings.whitelist.push(...newWhitelist);
										d_istinafred.loading(false).dropdown.show.call(d_istinafred, e.detail.value);
									}else{
										d_istinafred.loading(false).dropdown.show.call(d_istinafred, e.detail.value);
									}
								}else{
									d_istinafred.loading(false).dropdown.show.call(d_istinafred, e.detail.value);
								}
							}else{
								d_istinafred.loading(false).dropdown.show.call(d_istinafred, e.detail.value);
							}
						},
						error: function (hata) {
							d_istinafred.loading(false).dropdown.show.call(d_istinafred, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_istinafred.loading(false).dropdown.show.call(d_istinafred, e.detail.value);
			}
		});

		var d_icra = new Tagify(form.querySelector('[name="d_icra"]'), {
			whitelist: [],//sessionStorage.hedasDosyaMahkemeData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		d_icra.on('input', async function (e) {
			d_icra.settings.whitelist.length = 0; // reset current whitelist
			d_icra.loading(true).dropdown.hide.call(d_icra);
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
										d_icra.settings.whitelist.push(...newWhitelist);
										d_icra.loading(false).dropdown.show.call(d_icra, e.detail.value);
									}else{
										d_icra.loading(false).dropdown.show.call(d_icra, e.detail.value);
									}
								}else{
									d_icra.loading(false).dropdown.show.call(d_icra, e.detail.value);
								}
							}else{
								d_icra.loading(false).dropdown.show.call(d_icra, e.detail.value);
							}
						},
						error: function (hata) {
							d_icra.loading(false).dropdown.show.call(d_icra, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_icra.loading(false).dropdown.show.call(d_icra, e.detail.value);
			}
		});

		var d_kesinlestirme = new Tagify(form.querySelector('[name="d_kesinlestirme"]'), {
			whitelist: [],//sessionStorage.hedasDosyaKesinlestirmeListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		d_kesinlestirme.on('input', async function (e) {
			d_kesinlestirme.settings.whitelist.length = 0; // reset current whitelist
			d_kesinlestirme.loading(true).dropdown.hide.call(d_kesinlestirme)
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
										d_kesinlestirme.settings.whitelist.push(...newWhitelist);
										d_kesinlestirme.loading(false).dropdown.show.call(d_kesinlestirme, e.detail.value);
									}else{
										d_kesinlestirme.loading(false).dropdown.show.call(d_kesinlestirme, e.detail.value);
									}
								}else{
									d_kesinlestirme.loading(false).dropdown.show.call(d_kesinlestirme, e.detail.value);
								}
							}else{
								d_kesinlestirme.loading(false).dropdown.show.call(d_kesinlestirme, e.detail.value);
							}
						},
						error: function (hata) {
							d_kesinlestirme.loading(false).dropdown.show.call(d_kesinlestirme, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_kesinlestirme.loading(false).dropdown.show.call(d_kesinlestirme, e.detail.value);
			}
		});

		var d_mirascilik = new Tagify(form.querySelector('[name="d_mirascilik"]'), {
			whitelist: [],//sessionStorage.hedasDosyaMirascilikListData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});

		d_mirascilik.on('input', async function (e) {
			d_mirascilik.settings.whitelist.length = 0; // reset current whitelist
			d_mirascilik.loading(true).dropdown.hide.call(d_mirascilik)
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
										d_mirascilik.settings.whitelist.push(...newWhitelist);
										d_mirascilik.loading(false).dropdown.show.call(d_mirascilik, e.detail.value);
									}else{
										d_mirascilik.loading(false).dropdown.show.call(d_mirascilik, e.detail.value);
									}
								}else{
									d_mirascilik.loading(false).dropdown.show.call(d_mirascilik, e.detail.value);
								}
							}else{
								d_mirascilik.loading(false).dropdown.show.call(d_mirascilik, e.detail.value);
							}
						},
						error: function (hata) {
							d_mirascilik.loading(false).dropdown.show.call(d_mirascilik, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				d_mirascilik.loading(false).dropdown.show.call(d_mirascilik, e.detail.value);
			}
		});

		Inputmask("999999999,99", {
            "numericInput": true
        }).mask("#d_idarialacagi");


		Inputmask("999999999,99", {
            "numericInput": true
        }).mask("#d_vekaletalacagi");

		Inputmask("999999999,99", {
            "numericInput": true
        }).mask("#d_yargilamagideri");

		//console.log(tagWhiteList);
		var inputTags = document.querySelector('#d_tags'),
		// Init Tagify script on the above inputs

		tagify = new Tagify(inputTags, {
			
			whitelist: (localStorage.hedasDosyaTagListData==undefined)? [] : localStorage.hedasDosyaTagListData.trim().split(/\s*,\s*/),
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
		var suggestions = document.querySelector('#kt_dosya_add_suggestions');
		// Suggestion item click
		KTUtil.on(suggestions,  '[data-kt-suggestion="true"]', 'click', function(e) {
			tagify.addTags([this.innerText]);
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
					d_davaci: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					d_davali: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					d_davakonusu: {
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
						var htmlProjeBilgi = inpProjeBilgi.root.innerHTML;//htmlProjeBilgi.getElementsByClassName('ql-editor');
						var htmlMevkiBilgi = inpMevki.root.innerHTML;
						var htmlAciklama = inpAciklama.root.innerHTML;

						
						setTimeout(function() {
							submitButton.removeAttribute('data-kt-indicator');

							// Enable button
							
							postData = {
								route: "adddavadosya",
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
								//ciAcilisTarih: moment(ciAcilisTarih[0]._flatpickr.selectedDates[0]).format('DD-MM-YYYY'),
							};
						
							//console.log("postData",postData);
							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							
							Swal.fire({
								text: "Dosya bilgisinden sonra Mahkeme bilgisi de girmek istiyor musunuz?",
								icon: "info",
								buttonsStyling: false,
								confirmButtonText: "Evet",
								cancelButtonText:   "Hayır",
								showCancelButton: true,
								cancelButtonColor: '#d33',
								customClass: {
									confirmButton: "btn btn-primary",
									cancelButton: "btn btn-danger"
								}
							}).then(function (result) {
								var mahkemeContinue=0;
								if (result.isConfirmed) {
									mahkemeContinue=1;
								}
								
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
													if(isNaN(response.dosyaid)==false && response.dosyaid>0){
														dosyaId = response.dosyaid;
														kurumDosyaNo = response.kurumdosyano;
														inpDosyaNo.value = response.kurumdosyano;
														var inpList = form.querySelectorAll('[type="text"]');
														//console.log("inpList", inpList);
														inpList.forEach(il => {
															//il.classList.add('disabled');
															il.setAttribute('disabled', 'true');
														});
														if(modulUrl.indexOf('dosya')>0){
															//KTDosyaListServerSide.reload();
															$('#filtreleButton').click();
															if (mahkemeContinue!=1)
																document.location.reload();
															
															
														}
														toastrAlert('success','Başarılı!','Dosya Başarıyla Oluşturuldu.');
														cardDosyaBilgi.classList.remove('show');
														divMahkemeBilgileri.classList.remove('d-none');
														cardMahkemeBilgi.classList.add('show');
														// KTModalDosyaNewTarget.whiteListUpdate();
														KTModalDosyaNewTarget.mahkemeinit();
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
														submitButton.disabled = false;
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


	/* BEGIN::MAHKEME FORM FONKSIYONLARI */

	// Init form inputs
	var initMahkemeForm = function() {
		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/

		//var ci_itirazeden = new Tagify(form.querySelector('[name="ci_itirazeden"]') NAME OLARAK DA KULLANILABİLİR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		var dm_mahkeme = new Tagify(mahkemeform.querySelector('[name="dm_mahkeme"]'), {
			whitelist: [],//localStorage.hedasDosyaMahkemeData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		});
		dm_mahkeme.on('input', async function (e) {
			dm_mahkeme.settings.whitelist.length = 0; // reset current whitelist
			dm_mahkeme.loading(true).dropdown.hide.call(dm_mahkeme)
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
		dm_mahkeme.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('dm_mahkeme');
		});





		// Due date. For more info, please visit the official plugin site: https://flatpickr.js.org/
		dmAcilisTarih = $(mahkemeform.querySelector('[name="dm_acilistarihi"]'));
		dmAcilisTarih.flatpickr({
			enableTime: false,
			dateFormat: "d-m-Y",

		});

		dmAcilisTarih.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('dm_acilistarihi');
		});


		dmKararTarih = $(mahkemeform.querySelector('[name="dm_karartarihi"]'));
		dmKararTarih.flatpickr({
			enableTime: false,
			dateFormat: "d-m-Y",
		});

		
		
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
					dm_esasno: {
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
					dm_acilistarihi: {
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


	// Handle form validation and submittion
	var handleMahkemeForm = function() {
		// Stepper custom navigation

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
							

							mahkemePostData = {
								route: 'adddosyamahkeme',
								dmDosyaId: dosyaId,
								dmAcilisTarihi: moment(dmAcilisTarih[0]._flatpickr.selectedDates[0]).format('DD-MM-YYYY'),
								dmKararTarihi: dmKararTarihAddDataP.value,//moment(dmKararTarih[0]._flatpickr.selectedDates[0]).format('DD-MM-YYYY'),
								dmEsasNo: inpEsasNo.value,
								dmKararNo: inpKararNo.value,
								dmMahkeme: inpMahkemeJq.val().toString(),
								dmAciklama: inpMahkemeAciklama.value
							}
						
							//console.log("mahkemePostData",mahkemePostData);
							
							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							/*
							Swal.fire({
								text: "Mahkeme Bilgisi Dosyaya Eklenecek!",
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
                                        url: baseUrlHost+"api_newmahkemerecord",
                                        data: JSON.stringify(mahkemePostData),
                                        success: function (response) {
                                            if((typeof response)==="object"){
                                                if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                                                    if(response.code==200){
														if(isNaN(response.mahkemeid)==false && response.mahkemeid>0){
															toastrAlert('success','Başarılı!','Mahkeme Bilgisi Dosyaya Başarıyla Eklendi.');
															handleDataTableReload();
															//KTModalDosyaNewTarget.loadedFunctionList();
															mahkemeform.reset();
															$("#newedt_dm_mahkeme").val([""]).select2();
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
									
									//modal.hide();
							//	}
							//});

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


    // Private functions
    var initMahkemeDatatable = function () {
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX;

		filterMahkemeData.dmDosyaId = dosyaId;

        dt = $("#kt_content_dosya_mahkeme_list").DataTable({
            searchDelay: 1000,
            processing: true,
            serverSide: true,
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
        .search(JSON.stringify(filterMahkemeData));

        table = dt.$;

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dt.on('draw', function () {
           // initToggleToolbar();
           // toggleToolbars();
            //handleDeleteRows();
            KTMenu.createInstances();
        });
    }

	
	var handleDataTableReload = () => {
		dt
		.columns([0,1,2,3,4,5])
		.flatten()
		.search(JSON.stringify(filterMahkemeData))
		.draw();		
	}



	/* END::MAHKEME FORM FONKSIYONLARI */

	return {
		// Public functions
		init: function () {
			//this.loadedFunctionList();
			
			// Elements
			modalEl = document.querySelector('#kt_modal_new_dosya');

			if (!modalEl) {
				return;
			}


			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#kt_modal_new_dosya_form');
			submitButton = document.getElementById('kt_modal_new_dosya_submit');
			cancelButton = document.getElementById('kt_modal_new_dosya_cancel');

			inpKlasorNo = form.querySelector('[name="d_klasorno"]');
			inpDosyaNo = form.querySelector('[name="d_dosyano"]');
			inpDavaci = form.querySelector('[name="d_davaci"]');
			inpDavali = form.querySelector('[name="d_davali"]');
			inpDavaKonusu = form.querySelector('[name="d_davakonusu"]');
			inpKonuAciklamasi = form.querySelector('[name="d_konuaciklamasi"]');
			inpIstinafBilgi = form.querySelector('[name="d_istinafbilgi"]');
			inpTemyizBilgi = form.querySelector('[name="d_temyizbilgi"]');
			inpOnamaIlami = form.querySelector('[name="d_onamailami"]');
			inpBozmaIlami = form.querySelector('[name="d_bozmailami"]');
			inpIstinafKabul = form.querySelector('[name="d_istinafkabul"]');
			inpIstinafRed = form.querySelector('[name="d_istinafred"]');
			inpIcraNo = form.querySelector('[name="d_icrano"]');
			inpIcra = form.querySelector('[name="d_icra"]');
			inpKesinlestirme = form.querySelector('[name="d_kesinlestirme"]');
			inpMirascilik = form.querySelector('[name="d_mirascilik"]');
			inpIdariAlacagi = form.querySelector('[name="d_idarialacagi"]');
			inpVekaletAlacagi = form.querySelector('[name="d_vekaletalacagi"]');
			inpYargilamaGideri = form.querySelector('[name="d_yargilamagideri"]');
			inpTapuBilgi = form.querySelector('[name="d_tapubilgi"]');
			inpTags = form.querySelector('[name="d_tags"]');

			divDosyaBilgileri = document.querySelector('#dosyaBilgileri');
			divMahkemeBilgileri = document.querySelector('#dosyaMahkemeBilgileri');
			cardDosyaBilgi = document.querySelector('#kt_docs_card_dosyabilgi');
			cardMahkemeBilgi = document.querySelector('#kt_docs_card_mahkemebilgi');
		

			initForm();
			initValidator();
			handleForm();
			
		},
		tagsWhiteList: function(){
			setTimeout(function() {
				var isData = {
					searchText: ''
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
									localStorage.hedasDosyaTagListData = response.data;
								}
							}
						}
					}
				});			
			}, 1000); 

		},
		whiteListUpdate: function (){
			var itemText;
			var Datatext;
			console.log(postData);
            Datatext = localStorage.hedasDosyaTagListData;
            if(Datatext!==undefined){
				var itemArray;
                itemText = postData.dTags.replace("[{", "");
				itemText = itemText.replace("}]", "");
				itemText = itemText.replace("},{", "");
				itemText = itemText.replace('\"', '');
				itemArray = itemText.split('value:');

				itemArray.forEach(function(text){
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaTagListData = Datatext;
            }

            Datatext = localStorage.hedasDosyaDavaciListData;
            if(Datatext!==undefined){
                itemText = postData.dDavaci.replace("[{", "");
				itemText = itemText.replace("}]", "");
				itemText = itemText.replace("},{", "");
				itemText = itemText.replace('"', '');
				itemText = itemText.split('value:');

				itemText.forEach(function(text){
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaDavaciListData = Datatext;
            }

            Datatext = localStorage.hedasDosyaDavaKonusuListData;
            if(Datatext!==undefined){
                itemText = postData.dDavaKonusu.replace("[{", "");
				itemText = itemText.replace("}]", "");
				itemText = itemText.replace("},{", "");
				itemText = itemText.replace('"', '');
				itemText = itemText.split('value:');

				itemText.forEach(function(text){
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaDavaKonusuListData = Datatext;
            }

            Datatext = localStorage.hedasDosyaKonuAciklamasiListData;
            if(Datatext!==undefined){
                itemText = postData.dKonuAciklamasi.replace("[{", "");
				itemText = itemText.replace("}]", "");
				itemText = itemText.replace("},{", "");
				itemText = itemText.replace('"', '');
				itemText = itemText.split('value:');

				itemText.forEach(function(text){
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaKonuAciklamasiListData = Datatext;
            }

            Datatext = localStorage.hedasDosyaOnamaIlamiListData;
            if(Datatext!==undefined){
                itemText = postData.dOnamaIlami.replace("[{", "");
				itemText = itemText.replace("}]", "");
				itemText = itemText.replace("},{", "");
				itemText = itemText.replace('"', '');
				itemText = itemText.split('value:');

				itemText.forEach(function(text){
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaOnamaIlamiListData = Datatext;
            }

            Datatext = localStorage.hedasDosyaBozmaIlamiListData;
            if(Datatext!==undefined){
                itemText = postData.dBozmaIlami.replace("[{", "");
				itemText = itemText.replace("}]", "");
				itemText = itemText.replace("},{", "");
				itemText = itemText.replace('"', '');
				itemText = itemText.split('value:');

				itemText.forEach(function(text){
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaBozmaIlamiListData = Datatext;
            }

            Datatext = localStorage.hedasDosyaIstinafKabulListData;
            if(Datatext!==undefined){
                itemText = postData.dIstinafKabul.replace("[{", "");
				itemText = itemText.replace("}]", "");
				itemText = itemText.replace("},{", "");
				itemText = itemText.replace('"', '');
				itemText = itemText.split('value:');

				itemText.forEach(function(text){
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaIstinafKabulListData = Datatext;
            }

            Datatext = localStorage.hedasDosyaIstinafRedListData;
            if(Datatext!==undefined){
				var itemArray;
                itemText = postData.dIstinafRed.replace("[{", "");
				itemText = itemText.replace("}]", "");
				itemText = itemText.replace("},{", "");
				itemText = itemText.replace('"', '');
				itemArray = itemText.split('value:');

				itemArray.forEach(function(text){
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaIstinafRedListData = Datatext;
            }

            Datatext = localStorage.hedasDosyaKesinlestirmeListData;
            if(Datatext!==undefined){
                itemText = postData.dKesinlestirme.replace("[{", "");
				itemText = itemText.replace("}]", "");
				itemText = itemText.replace("},{", "");
				itemText = itemText.replace('"', '');
				itemText = itemText.split('value:');

				itemText.forEach(function(text){
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaKesinlestirmeListData = Datatext;
            }

            Datatext = localStorage.hedasDosyaMirascilikListData;
            if(Datatext!==undefined){
                itemText = postData.dMirascilik.replace("[{", "");
				itemText = itemText.replace("}]", "");
				itemText = itemText.replace("},{", "");
				itemText = itemText.replace('"', '');
				itemText = itemText.split('value:');

				itemText.forEach(function(text){
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaMirascilikListData = Datatext;
            }

            Datatext = localStorage.hedasDosyaMahkemelerData;
            if(Datatext!==undefined){
				var itemArray;
                itemText = JSON.stringify(postData.dIcra);//.replace("[{", "");
				// itemText = itemText.replace("}]", "");
				// itemText = itemText.replace("},{", "");
				// itemText = itemText.replace("\"", "");

				//itemArray = itemText.split('value:');
				//console.log(itemArray);
				itemText.forEach(function(text){
					console.log("text", text);
					if(Datatext.indexOf(text.trim())==-1){
						Datatext += text+',';
					}
				});
				localStorage.hedasDosyaMahkemelerData = Datatext;
            }

		},
		mahkemeinit: function () {

			mahkemeform = document.querySelector('#kt_modal_new_dosya_mahkeme_form');
			submitButtonM = document.getElementById('kt_modal_new_dosya_mahkeme_submit');

			inpEsasNo = mahkemeform.querySelector('[name="dm_esasno"]');
			inpKararNo = mahkemeform.querySelector('[name="dm_kararno"]');
			inpMahkemeJq = $("#newedt_dm_mahkeme");
			$("#newedt_dm_mahkeme").select2();
			inpMahkemeAciklama = mahkemeform.querySelector('[name="dm_aciklama"]');

			dmKararTarihAddDataP = mahkemeform.querySelector('#dm_karartarihi');

			initMahkemeForm();
			initMahkemeValidator();
			handleMahkemeForm();
			initMahkemeDatatable();
		},

	};

}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTModalDosyaNewTarget.tagsWhiteList();
	KTModalDosyaNewTarget.init();
});
const COLORS = [
	"#000000", "#e60000", "#ff9900", "#ffff00", "#008a00", "#0066cc", "#9933ff",
	"#ffffff", "#facccc", "#ffebcc", "#ffffcc", "#cce8cc", "#cce0f5", "#ebd6ff",
	"#bbbbbb", "#f06666", "#ffc266", "#ffff66", "#66b966", "#66a3e0", "#c285ff",
	"#888888", "#a10000", "#b26b00", "#b2b200", "#006100", "#0047b2", "#6b24b2",
	"#444444", "#5c0000", "#663d00", "#666600", "#003700", "#002966", "#3d1466"
  ];
inpProjeBilgi = new Quill('#d_projebilgi', {
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

inpMevki = new Quill('#d_mevkiplaka', {
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


inpAciklama = new Quill('#d_aciklama', {
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



