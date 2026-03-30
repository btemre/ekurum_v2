"use strict";


var hostUrlDurusmalarMUpdate = window.location.host;
var baseUrlHostDurusmalarMUpdate = "//"+hostUrlDurusmalarMUpdate+"/apps/edts/durusmalar/";
var modulUrlDurusmalarMUpdate = window.location.href;

var editIdDurusmalarMUpdate;
var editDataDurusmalarMUpdate = {
    route: "editdurusmamanuel",
    edtsId: "",
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
};

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

var inpDurusmalarDTarihiFP;
var inpDurusmalarDTarihiDP;

var KTModalUpdateDurusmalarManuel = function () {

	var submitButton;
    var fksubmitButton; //farklı kaydet
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;

    var hostUrl = window.location.host;
	const baseUrlHost = "//"+hostUrl+"/apps/edts/durusmalar/";
	var modulUrl = window.location.href;


    
	var inpDosyaNo;
	var inpDosyaTuru;
	var inpDurusmaTarihi;
	var inpMahkeme;
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

    var checkedEvent =( )=> {
        inpTutanakBilgi.change(function(){
            var $data = $(this).prop("checked");
            
            if(typeof $data !== "undefined" && $data==true){
                editDataDurusmalarMUpdate.edtsTutanakDurum=1;
            }else{
                editDataDurusmalarMUpdate.edtsTutanakDurum=0;
            }
			// console.log("TutanakDurum:",editDataDurusmalarMUpdate.edtsTutanakDurum);
        })
    }

    const handleEditEvent = () => {

        if (inpDosyaNo) inpDosyaNo.value = editDataDurusmalarMUpdate.edtsDosyaNo || '';
        if (inpDosyaTuru) inpDosyaTuru.value = editDataDurusmalarMUpdate.edtsDosyaTur || '';
        if (editDataDurusmalarMUpdate.edtsMahkeme && $('#dm_mahkememulti_edit').length) {
            var mahkemeVal = editDataDurusmalarMUpdate.edtsMahkeme;
            var updateVals = typeof mahkemeVal === 'string' ? mahkemeVal.split(',').map(function(x) { return x.trim(); }).filter(Boolean) : (Array.isArray(mahkemeVal) ? mahkemeVal : []);
            $('#dm_mahkememulti_edit').val(updateVals).trigger('change');
        }
        if (inpEsasNo) inpEsasNo.value = editDataDurusmalarMUpdate.edtsEsasNo || '';
        if (inpIlgiliAvukat) inpIlgiliAvukat.value = editDataDurusmalarMUpdate.edtsIlgiliAvukat || '';
        if (inpIslem) inpIslem.value = editDataDurusmalarMUpdate.edtsIslem || '';
        if (inpTarafBilgisi) inpTarafBilgisi.value = editDataDurusmalarMUpdate.edtsTarafBilgisi || '';
        if (inpAciklama) inpAciklama.value = editDataDurusmalarMUpdate.edtsAciklama || '';
        if (inpIlgiliMemur) inpIlgiliMemur.value = editDataDurusmalarMUpdate.edtsIlgiliMemur || '';
        if (inpEtiket) inpEtiket.value = editDataDurusmalarMUpdate.edtsEtiket || '';
        if (inpTutanakBilgi && inpTutanakBilgi.length) inpTutanakBilgi.val(editDataDurusmalarMUpdate.edtsTutanakDurum);

        if (inpDurusmalarDTarihiFP && typeof inpDurusmalarDTarihiFP.setDate === 'function') inpDurusmalarDTarihiFP.setDate(editDataDurusmalarMUpdate.edtsDurusmaTarihi, true, 'd-m-Y H:i');
        if (inpAvukat && inpAvukat.length) inpAvukat.val(editDataDurusmalarMUpdate.edtsAvukat).trigger('change');
        if (inpTaraf && inpTaraf.length) inpTaraf.val(editDataDurusmalarMUpdate.edtsTaraf).trigger('change');
        if (inpDurusmaIslemi && inpDurusmaIslemi.length) inpDurusmaIslemi.val(editDataDurusmalarMUpdate.edtsDurusmaIslemi).trigger('change');

        if (inpTutanakBilgi && inpTutanakBilgi.length) {
            if (editDataDurusmalarMUpdate.edtsTutanakDurum == 1) {
                inpTutanakBilgi.attr("checked", "checked");
            } else {
                inpTutanakBilgi.removeAttr("checked");
            }
        }

        if (modal) modal.show();

        handleUpdateEvent();

    }

    const initForm = () => {
        if (!form || !form.querySelector('[name="edt_dm_dosyaturu"]')) return;

		var edtAvukatEl = form.querySelector('[name="edt_dm_avukat"]');
		if (edtAvukatEl) $(edtAvukatEl).on('change', function () {
			// Revalidate the field when an option is chosen
			validator.revalidateField('edt_dm_avukat');
		});

		var edtTarafEl = form.querySelector('[name="edt_dm_taraf"]');
		if (edtTarafEl) $(edtTarafEl).on('change', function () {
			// Revalidate the field when an option is chosen
			validator.revalidateField('edt_dm_taraf');
		});

		var edtDosyaturuEl = form.querySelector('[name="edt_dm_dosyaturu"]');
		var edt_dm_dosyaturu = edtDosyaturuEl ? new Tagify(edtDosyaturuEl, {
			whitelist: [],
			placeholder: "Yazınız",
			enforceWhitelist: false
		}) : null;
		if (edt_dm_dosyaturu) edt_dm_dosyaturu.on('input', async function (e) {
			edt_dm_dosyaturu.settings.whitelist.length = 0; // reset current whitelist
			edt_dm_dosyaturu.loading(true).dropdown.hide.call(edt_dm_dosyaturu)
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
										edt_dm_dosyaturu.settings.whitelist.push(...newWhitelist);
										edt_dm_dosyaturu.loading(false).dropdown.show.call(edt_dm_dosyaturu, e.detail.value);
									}else{
										edt_dm_dosyaturu.loading(false).dropdown.show.call(edt_dm_dosyaturu, e.detail.value);
									}
								}else{
									edt_dm_dosyaturu.loading(false).dropdown.show.call(edt_dm_dosyaturu, e.detail.value);
								}
							}else{
								edt_dm_dosyaturu.loading(false).dropdown.show.call(edt_dm_dosyaturu, e.detail.value);
							}
						},
						error: function (hata) {
							edt_dm_dosyaturu.loading(false).dropdown.show.call(edt_dm_dosyaturu, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_dm_dosyaturu.loading(false).dropdown.show.call(edt_dm_dosyaturu, e.detail.value);
			}
		});

		if (edt_dm_dosyaturu) edt_dm_dosyaturu.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('edt_dm_dosyaturu');
		});


		var edtMahkemeEl = form.querySelector('[name="edt_dm_mahkeme"]');
		var edt_dm_mahkeme = edtMahkemeEl ? new Tagify(edtMahkemeEl, {
			whitelist: [],//sessionStorage.mahkemeData.trim().split(/\s*,\s*/),
			placeholder: "Yazınız",
			enforceWhitelist: false
		}) : null;
		if (edt_dm_mahkeme) edt_dm_mahkeme.on('input', async function (e) {
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

		if (edt_dm_mahkeme) edt_dm_mahkeme.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('edt_dm_mahkeme');
		});

		var edtIlgiliavukatEl = form.querySelector('[name="edt_dm_ilgiliavukat"]');
		var ilgiliAvukatWhitelist = (localStorage.edtsDurusmalarIlgiliAvukatListData || '').trim().split(/\s*,\s*/).filter(Boolean);
		var edt_dm_ilgiliavukat = edtIlgiliavukatEl ? new Tagify(edtIlgiliavukatEl, {
			whitelist: ilgiliAvukatWhitelist,
			placeholder: "Yazınız",
			enforceWhitelist: true,
			dropdown: {
				maxItems: 20,           // <- mixumum allowed rendered suggestions
				classname: "edt_dm_ilgiliavukat__suggestions", // <- custom classname for this dropdown, so it could be targeted
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		}) : null;

		if (edt_dm_ilgiliavukat) edt_dm_ilgiliavukat.on('input', async function (e) {
			edt_dm_ilgiliavukat.settings.whitelist.length = 0; // reset current whitelist
			edt_dm_ilgiliavukat.loading(true).dropdown.hide.call(edt_dm_ilgiliavukat)
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
										edt_dm_ilgiliavukat.settings.whitelist.push(...newWhitelist);
										edt_dm_ilgiliavukat.loading(false).dropdown.show.call(edt_dm_ilgiliavukat, e.detail.value);
									}else{
										edt_dm_ilgiliavukat.loading(false).dropdown.show.call(edt_dm_ilgiliavukat, e.detail.value);
									}
								}else{
									edt_dm_ilgiliavukat.loading(false).dropdown.show.call(edt_dm_ilgiliavukat, e.detail.value);
								}
							}else{
								edt_dm_ilgiliavukat.loading(false).dropdown.show.call(edt_dm_ilgiliavukat, e.detail.value);
							}
						},
						error: function (hata) {
							edt_dm_ilgiliavukat.loading(false).dropdown.show.call(edt_dm_ilgiliavukat, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_dm_ilgiliavukat.loading(false).dropdown.show.call(edt_dm_ilgiliavukat, e.detail.value);
			}
		});

		var edtIlgilimemurEl = form.querySelector('[name="edt_dm_ilgilimemur"]');
		var ilgiliMemurWhitelist = (localStorage.edtsDurusmalarIlgiliMemurListData || '').trim().split(/\s*,\s*/).filter(Boolean);
		var edt_dm_ilgilimemur = edtIlgilimemurEl ? new Tagify(edtIlgilimemurEl, {
			whitelist: ilgiliMemurWhitelist,
			placeholder: "Yazınız",
			enforceWhitelist: true,
			dropdown: {
				maxItems: 20,           // <- mixumum allowed rendered suggestions
				classname: "edt_dm_ilgilimemur__suggestions", // <- custom classname for this dropdown, so it could be targeted
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		}) : null;

		if (edt_dm_ilgilimemur) edt_dm_ilgilimemur.on('input', async function (e) {
			edt_dm_ilgilimemur.settings.whitelist.length = 0; // reset current whitelist
			edt_dm_ilgilimemur.loading(true).dropdown.hide.call(edt_dm_ilgilimemur)
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
										edt_dm_ilgilimemur.settings.whitelist.push(...newWhitelist);
										edt_dm_ilgilimemur.loading(false).dropdown.show.call(edt_dm_ilgilimemur, e.detail.value);
									}else{
										edt_dm_ilgilimemur.loading(false).dropdown.show.call(edt_dm_ilgilimemur, e.detail.value);
									}
								}else{
									edt_dm_ilgilimemur.loading(false).dropdown.show.call(edt_dm_ilgilimemur, e.detail.value);
								}
							}else{
								edt_dm_ilgilimemur.loading(false).dropdown.show.call(edt_dm_ilgilimemur, e.detail.value);
							}
						},
						error: function (hata) {
							edt_dm_ilgilimemur.loading(false).dropdown.show.call(edt_dm_ilgilimemur, e.detail.value);
						}
					});				
				}, 1000); 
			}else{
				edt_dm_ilgilimemur.loading(false).dropdown.show.call(edt_dm_ilgilimemur, e.detail.value);
			}
		});

		var edtIslemEl = form.querySelector('[name="edt_dm_islem"]');
		var edt_dm_islem = edtIslemEl ? new Tagify(edtIslemEl, {
			whitelist: ["Duruşma","İstinaf","Keşif","Karar","Red","Birleşti","Kaldırıldı"],
			placeholder: "Yazınız",
			enforceWhitelist: true,
			dropdown: {
				maxItems: 20,           // <- mixumum allowed rendered suggestions
				classname: "edt_dm_islem__suggestions", // <- custom classname for this dropdown, so it could be targeted
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		}) : null;


		if (inpDurusmalarDTarihiDP) inpDurusmalarDTarihiFP = flatpickr(inpDurusmalarDTarihiDP, {
			enableTime: true,
			time_24hr: true,
            minuteIncrement: 1,
			static: true,
			dateFormat: "d-m-Y H:i",
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




		var input = document.querySelector('#edt_dm_etiket');
		var edt_dm_etiket = input ? new Tagify(input, {
			whitelist: ["Önemli","Acil","Eksik","Hatırla","Taslak","Silinecek"],
			placeholder: "Yazınız",
			enforceWhitelist: true
		}) : null;
	
		// Suggestions
		var suggestions = document.querySelector('#kt_tagify_update_etiket_custom_suggestions');
	
		// Suggestion item click
		if (suggestions && edt_dm_etiket) KTUtil.on(suggestions,  '[data-kt-suggestion="true"]', 'click', function(e) {
			edt_dm_etiket.addTags([this.innerText]);
		});
	



    }


    const handleUpdateEvent = () => {
        submitButton.addEventListener('click', e => {
            e.preventDefault();

            editDataDurusmalarMUpdate.edtsDosyaNo       = inpDosyaNo.value;
            editDataDurusmalarMUpdate.edtsDosyaTur       = inpDosyaTuru.value;
            editDataDurusmalarMUpdate.edtsDurusmaTarihi       = inpDurusmalarDTarihiDP.value;
            editDataDurusmalarMUpdate.edtsMahkeme       = $('#dm_mahkememulti_edit').length ? ($('#dm_mahkememulti_edit').val() || []).toString() : (inpMahkeme ? inpMahkeme.value : '');
            editDataDurusmalarMUpdate.edtsEsasNo       = inpEsasNo.value;
            editDataDurusmalarMUpdate.edtsIlgiliAvukat       = inpIlgiliAvukat.value;
            editDataDurusmalarMUpdate.edtsIslem       = inpIslem.value;
            editDataDurusmalarMUpdate.edtsTarafBilgisi       = inpTarafBilgisi.value;
            editDataDurusmalarMUpdate.edtsAciklama       = inpAciklama.value;
            editDataDurusmalarMUpdate.edtsIlgiliMemur       = inpIlgiliMemur.value;
            editDataDurusmalarMUpdate.edtsEtiket       = inpEtiket.value;
            editDataDurusmalarMUpdate.edtsDurusmaIslemi       = inpDurusmaIslemi.val();
            editDataDurusmalarMUpdate.edtsAvukat       = inpAvukat.val();
            editDataDurusmalarMUpdate.edtsTaraf       = inpTaraf.val();


            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    //console.log('validated!');

                    if (status == 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click 
                        submitButton.disabled = true;

                    
                        //console.log(editDataDurusmalarMUpdate);

                        setTimeout(function() {
                            

                            // Enable button
                            
                            
                            // Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            Swal.fire({
                                text: "Duruşma Bilgileri Güncellenecek!",
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
                                        url: baseUrlHostDurusmalarMUpdate+"api_editrecord",
                                        data: JSON.stringify(editDataDurusmalarMUpdate),
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
                                                                
                                                                if(modulUrlDurusmalarMUpdate.indexOf('durusmalar')>0){
                                                                    //KTGelenGidenListServerSide.init();
                                                                    //$('#filtreleButton').click();
                                                                    KTDurusmalarListServerSide.reload();
                                                                    
                                                                }
                                                                submitButton.disabled = false;
                                                                modal.hide();
                                                                //window.location.assign(baseUrlHostDurusmalarMUpdate);
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

    const fkhandleNewEvent = () => {

		// Action buttons
		fksubmitButton.addEventListener('click', function (e) {
			e.preventDefault();
			//tabButton = false;
			// Validate form before submit
			if (validator) {
				validator.validate().then(function (status) {
				
					if (status == 'Valid') {
						fksubmitButton.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple cligck 
						fksubmitButton.disabled = true;

						postData.route = "adddurusmamanuel";
						postData.edtsDosyaNo = inpDosyaNo.value;
						postData.edtsDosyaTur = inpDosyaTuru.value;
						postData.edtsDurusmaTarihi = inpDurusmalarDTarihiDP.value;
						postData.edtsMahkeme = $('#dm_mahkememulti_edit').length ? ($('#dm_mahkememulti_edit').val() || []).toString() : (inpMahkeme ? inpMahkeme.value : '');
						postData.edtsEsasNo = inpEsasNo.value;
						postData.edtsIlgiliAvukat = inpIlgiliAvukat.value;
                        postData.edtsIslem       = inpIslem.value;
						postData.edtsTarafBilgisi = inpTarafBilgisi.value;
						postData.edtsAciklama = inpAciklama.value;
						postData.edtsIlgiliMemur = inpIlgiliMemur.value;
						postData.edtsEtiket = inpEtiket.value;
                        postData.edtsDurusmaIslemi       = inpDurusmaIslemi.val();
						postData.edtsAvukat = inpAvukat.val();
						postData.edtsTaraf = inpTaraf.val();

						
						setTimeout(function() {
							fksubmitButton.removeAttribute('data-kt-indicator');


							// Enable button
							fksubmitButton.disabled = false;

							
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
								//tabButton = $('.swal2-confirm');
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
															//tabButton = $('.swal2-confirm');
                                                            if (result.isConfirmed) {
																if(modulUrl.indexOf('durusmalar')>0){
																	//KTGelenGidenListServerSide.init();
                                                                    //$('#filtreleButton').click();
                                                                    KTDurusmalarListServerSide.reload();
                                                                    
                                                                }
																$('#dm_avukat').val('0').trigger('change');
																$('#dm_taraf').val('0').trigger('change');
																form.reset();
                                                                fksubmitButton.disabled = false;
																//tabButton = fksubmitButton;
                                                                //modal.hide();
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
                                                        }).then(function (result) {
															//tabButton = $('.swal2-confirm');
                                                            if (result.isConfirmed) {
																//tabButton = fksubmitButton;
															}
														});
                                                        //Form verilerindeki hataları bas
                                                        fksubmitButton.disabled = false;
														
                                                    }else{
                                                        Swal.fire({
                                                            text:response.description,
                                                            icon: "error",
                                                            buttonsStyling: false,
                                                            confirmButtonText: "Tamam",
                                                            customClass: {
                                                                confirmButton: "btn btn-danger"
                                                            }
                                                        }).then(function (result) {
															//tabButton = $('.swal2-confirm');
                                                            if (result.isConfirmed) {
																//tabButton = fksubmitButton;
															}
														});
                                                        fksubmitButton.disabled = false;
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
                                                    fksubmitButton.disabled = false;
													//tabButton = $('.swal2-confirm');
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
                                                fksubmitButton.disabled = false;
												//tabButton = $('.swal2-confirm');
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
                                            }).then(function (result) {
												//tabButton = $('.swal2-confirm');
												if (result.isConfirmed) {
													//tabButton = fksubmitButton;
												}
											});
                                            fksubmitButton.disabled = false;
                                            //console.log(hata);
                                        }
									});
									//modal.hide();
								}else{
									//tabButton = fksubmitButton;
								}
							});
							fksubmitButton.removeAttribute('data-kt-indicator');
							fksubmitButton.disabled = false;
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
							//tabButton = document.getElementsByClassName('swal2-confirm');
							// tabButton = $('.swal2-confirm');
							if(result.isConfirmed){
								//tabButton = fksubmitButton;
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

    
    // Init validator
    const initValidator = () => {
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




    return {
        init: function () {

            initValidator();
            handleEditEvent();
            fkhandleNewEvent();
            handleCancelButton();
            checkedEvent();

        },
        viewModal: function(id){
            editIdDurusmalarMUpdate = id;
            var postData = {
                id: editIdDurusmalarMUpdate
            }
            if (!modal && document.getElementById('kt_modal_update_durusmalar_manuel')) {
                KTModalUpdateDurusmalarManuel.populate();
            }
            $.ajax({
                type: "POST",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                url: baseUrlHostDurusmalarMUpdate+"api_getDurusma",
                data: JSON.stringify(postData),
                success: function (response) {
                    //submitButton.disabled = false;
                    // console.log(response);
                    //console.log(response.success);
                    //console.log("Type:", typeof response, isValidJsonString(response));
                    //return;
                    if((typeof response)==="object"){
                        if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                            if(response.code==200){

                                editDataDurusmalarMUpdate.edtsId       = response.data._id;
                                editDataDurusmalarMUpdate.edtsTutanakDurum       = response.data._tutanak;
                                editDataDurusmalarMUpdate.edtsDosyaNo       = response.data._dosyano;
                                editDataDurusmalarMUpdate.edtsDosyaTur       = response.data._dosyaturu;
                                editDataDurusmalarMUpdate.edtsDurusmaTarihi       = response.data._durusmatarihi;
                                editDataDurusmalarMUpdate.edtsMahkeme       = response.data._mahkeme;
                                editDataDurusmalarMUpdate.edtsEsasNo       = response.data._esasno;
                                editDataDurusmalarMUpdate.edtsAvukat       = response.data._avukatid;
                                editDataDurusmalarMUpdate.edtsIlgiliAvukat       = response.data._ilgiliavukat;
                                editDataDurusmalarMUpdate.edtsTaraf       = response.data._taraf;
                                editDataDurusmalarMUpdate.edtsIslem       = response.data._islem;
                                editDataDurusmalarMUpdate.edtsTarafBilgisi       = response.data._tarafbilgisi;
                                editDataDurusmalarMUpdate.edtsAciklama       = response.data._aciklama;
                                editDataDurusmalarMUpdate.edtsIlgiliMemur       = response.data._ilgilimemur;
                                editDataDurusmalarMUpdate.edtsEtiket       = response.data._tags;
                                editDataDurusmalarMUpdate.edtsDurusmaIslemi       = response.data._takip;


                                KTModalUpdateDurusmalarManuel.init();
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
           // console.log("A:",a);ss
            
        },
        populate: function(){
            const element = document.getElementById('kt_modal_update_durusmalar_manuel');
            if (!element) return;
            form = element.querySelector('#kt_modal_update_durusmalar_manuel_form');

            submitButton = form.querySelector('#kt_modal_update_durusmalar_manuel_submit');
            cancelButton = form.querySelector('#kt_modal_update_durusmalar_manuel_cancel');
            fksubmitButton = form.querySelector('#kt_modal_update_durusmalar_manuel_new_submit'); //farklı kaydet
            //closeButton = element.querySelector('#kt_modal_update_cezakayitm_close');
            
			inpDosyaNo = form.querySelector('[name="edt_dm_dosyano"]');
			inpDosyaTuru = form.querySelector('[name="edt_dm_dosyaturu"]');
			inpDurusmaTarihi = form.querySelector('[name="edt_dm_durusmatarihi"]');
			inpMahkeme = form.querySelector('[name="edt_dm_mahkeme"]');
			inpEsasNo = form.querySelector('[name="edt_dm_esasno"]');
			
			inpIlgiliAvukat = form.querySelector('[name="edt_dm_ilgiliavukat"]');
			inpIslem = form.querySelector('[name="edt_dm_islem"]');
			inpTarafBilgisi = form.querySelector('[name="edt_dm_tarafbilgisi"]');
			inpAciklama = form.querySelector('[name="edt_dm_aciklama"]');
			inpIlgiliMemur = form.querySelector('[name="edt_dm_ilgilimemur"]');
			inpEtiket = form.querySelector('[name="edt_dm_etiket"]');


			inpDurusmalarDTarihiDP = form.querySelector('#edt_dm_durusmatarihi');
            inpAvukat = $('#edt_dm_avukat');
            inpTaraf = $('#edt_dm_taraf');
			inpTutanakBilgi = $('#edt_dm_tutanakbilgi');
			inpDurusmaIslemi = $('#edt_dm_durusmaislemi');


            modal = new bootstrap.Modal(element);
            
            initForm();
            resetFormValidator(element);
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
                        url: baseUrlHostDurusmalarMUpdate+"api_ejectdata",
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
                                                
                                                if(modulUrlDurusmalarMUpdate.indexOf('durusmalar')>0){
                                                    //KTGelenGidenListServerSide.init();
                                                    KTDurusmalarListServerSide.reload();
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
var _runPopulate = function () { KTModalUpdateDurusmalarManuel.populate(); };
if (typeof KTUtil !== 'undefined' && KTUtil.onDOMContentLoaded) {
    KTUtil.onDOMContentLoaded(_runPopulate);
} else {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', _runPopulate);
    } else {
        _runPopulate();
    }
}
