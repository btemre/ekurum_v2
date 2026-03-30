"use strict";
var tagWhiteList;

var KTWhiteListServerSide = function () {
    var loadEDTSDurusmaMemurList   = false;
    var loadEDTSDurusmaAvukatList   = false;

    var edtsDurusmalarMemurList = function() {
        loadEDTSDurusmaMemurList = true;
        var mHost = window.location.host;
        var mHUrl = "//"+mHost+"/apps/edts/durusmalar/";
        var sonuc;
        setTimeout(function() {
            var isData = {
                searchText: ''
            }	
            $.ajax({
                type: "POST",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                url: mHUrl+"api_FormIlgiliMemurSearch",
                data: JSON.stringify(isData),
                success: function (response) {
                    if((typeof response)==="object"){
                        if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                            if(response.code==200){
                                localStorage.edtsDurusmalarIlgiliMemurListData = response.data;
                                loadEDTSDurusmaMemurList = false;
                            }else{
                                localStorage.edtsDurusmalarIlgiliMemurListData = "";
                                loadEDTSDurusmaMemurList = false;
                            }
                        }else{
                            localStorage.edtsDurusmalarIlgiliMemurListData = "";
                            loadEDTSDurusmaMemurList = false;
                    }
                    }else{
                        localStorage.edtsDurusmalarIlgiliMemurListData = "";
                        loadEDTSDurusmaMemurList = false;
                    }
                }});
        }, 1000); 
    }

    var edtsDurusmalarAvukatList = function() {
        loadEDTSDurusmaAvukatList = true;
        var mHost = window.location.host;
        var mHUrl = "//"+mHost+"/apps/edts/durusmalar/";
        var sonuc;
        setTimeout(function() {
            var isData = {
                searchText: ''
            }	
            $.ajax({
                type: "POST",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                url: mHUrl+"api_FormIlgiliAvukatSearch",
                data: JSON.stringify(isData),
                success: function (response) {
                    if((typeof response)==="object"){
                        if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                            if(response.code==200){
                                localStorage.edtsDurusmalarIlgiliAvukatListData = response.data;
                                loadEDTSDurusmaAvukatList = false;
                            }else{
                                localStorage.edtsDurusmalarIlgiliAvukatListData = "";
                                loadEDTSDurusmaAvukatList = false;
                            }
                        }else{
                            localStorage.edtsDurusmalarIlgiliAvukatListData = "";
                            loadEDTSDurusmaAvukatList = false;
                    }
                    }else{
                        localStorage.edtsDurusmalarIlgiliAvukatListData = "";
                        loadEDTSDurusmaAvukatList = false;
                    }
                }});
        }, 1000); 
    }

    var isLoadingAlert = function(){
        if(loadEDTSDurusmaMemurList==true || loadEDTSDurusmaAvukatList==true){

                var uyari =  Swal.fire({
                title: 'Lütfen Bekleyiniz..',
                html: `<strong>Ön Tanımlı Veriler Yükleniyor.</strong> <br><br> 
                Bir Hata Olduğunu Düşünüyorsanız Sistem Yöneticilerine veya <a href="https://ekurum.hipporello.net/desk" target="_blank"><span class="badge badge-info">Yardım Masasından</span></a> Bildiriniz.<br>`,
                icon: "info",
                buttonsStyling: false,
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: "Anladım",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        }

        var sayacBaslat = setInterval(function() {
           // console.log(loadHDDavali ,uyari);
            if(loadEDTSDurusmaMemurList==false && loadEDTSDurusmaAvukatList==false){
                Swal.close();
                clearInterval(sayacBaslat);
            }

        }, 1000);
    }




    return {
        init: function () {
            var Datatext = localStorage.edtsDurusmalarIlgiliMemurListData;
            if(Datatext==undefined){
                edtsDurusmalarMemurList();
            }
            Datatext = localStorage.edtsDurusmalarIlgiliAvukatListData;
            if(Datatext==undefined){
                edtsDurusmalarAvukatList();
            }
            
            isLoadingAlert();
            
        }
    }

}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTWhiteListServerSide.init();
});
