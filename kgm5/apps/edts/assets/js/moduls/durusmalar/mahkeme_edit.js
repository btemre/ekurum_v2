
var hostUrl = window.location.host;
const baseUrlHost = "//"+hostUrl+"/apps/edts/durusmalar/";
var modulUrl = window.location.href;




var editId;
var editData = {
    route: "editmahkeme",
    mh_id: '',
    mh_name: '',

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

    var mhId;
    var mhName;
    const element = document.getElementById('kt_modal_edit_ggevrak');
    form = element.querySelector('#kt_modal_edit_ggevrak_form');
    modal = new bootstrap.Modal(element);

    
    submitButton = form.querySelector('#kt_modal_edit_ggevrak_submit');
    cancelButton = form.querySelector('#kt_modal_edit_ggevrak_cancel');
    closeButton = element.querySelector('#kt_modal_edit_ggevrak_close');
    mhId             = form.querySelector('#mh_id');
    mhName            = form.querySelector('#mh_name');


    const handleEditEvent = () => {
        //modalTitle.innerText = "Gelen/Giden Evrak Düzenle";
        //viewDescription.value     = editData.ggAciklama;
        $("#mh_id").val(editData.mh_id);
        $("#mh_name").val(editData.mh_name);

        
        modal.show();

        handleUpdateEvent();        
      

    }

    const initForm = () => {

        $("#mh_id").val(0);
        $("#mh_name").val("");



    }

    const handleUpdateEvent = () => {
        submitButton.addEventListener('click', e => {
            e.preventDefault();
            editData.mh_id = mhId.value;
            editData.mh_name =  mhName.value;

            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {

                    if (status == 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click 
                        submitButton.disabled = true;

                    
                

                        setTimeout(function() {
                            

                            // Enable button
                            
                            
                            // Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            Swal.fire({
                                text: "Mahkeme Bilgileri Güncellenecek!",
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
                                        url: baseUrlHost+"api_mahkemeEditrecord",
                                        data: JSON.stringify(editData),
                                        success: function (response) {
                                            //submitButton.disabled = false;

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
                                                                if(modulUrl.indexOf('mahkemeler')>0){
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

        var newClicked=$("#new_mahkeme_click").val();
        
        if (newClicked==1) {
            $("#mh_id").val("0");
            $("#mh_name").val("");
        }
    
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
					mh_name: {
						validators: {
							notEmpty: {
								message: 'Gerekli'
							}
						}
					},
					

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


            initForm();
            initValidator();
            var newClicked=$("#new_mahkeme_click").val();
            if (newClicked==1) {
                $("#mh_id-div").hide();
                console.log("new clickedx");
                $("#kt_modal_edit_ggevrak_submit .indicator-label").html("Ekle");
            }
            else  {
                $("#mh_id-div").show();
                $("#kt_modal_edit_ggevrak_submit .indicator-label").html("Güncelle");
                
                
            }
            
            handleEditEvent();      
            
            handleCancelButton();
            handleCloseButton();
           


        },
        viewModal: function(id){
            editId = id;
            $("#new_mahkeme_click").val(0);
            var postData = {
                id: editId
            }
            $.ajax({
                type: "POST",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                url: baseUrlHost+"api_getMahkeme",
                data: JSON.stringify(postData),
                success: function (response) {

                    if((typeof response)==="object"){
                        if(response.hasOwnProperty('success') && response.hasOwnProperty('code')){
                            if(response.code==200){

                                editData.mh_id       = response.data._id;
                                editData.mh_name      = response.data._mhadi;

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
                   
                }
            });
            
        },
        populate: function(){

            //const element = document.getElementById('kt_modal_edit_ggevrak');
            //form = element.querySelector('#kt_modal_edit_ggevrak_form');

            submitButton = form.querySelector('#kt_modal_edit_ggevrak_submit');
            cancelButton = form.querySelector('#kt_modal_edit_ggevrak_cancel');
            closeButton = element.querySelector('#kt_modal_edit_ggevrak_close');
            mhId             = form.querySelector('#mh_id');
            mhName            = form.querySelector('#mh_name');


            modalTitle = form.querySelector('[data-kt-reminder="title"]');
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
                        url: baseUrlHost+"api_mahkemeejectdata",
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
                                                
                                                if(modulUrl.indexOf('mahkemeler')>0){
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
                           
                        }
                    });
         
                   
                }
            });

        }
    }     




   
}();

$( document ).ready(function() {

    $("#new_mahkeme_click").val(0);
    $( "#newRecordMahkeme").on( "click", function() {
        $("#new_mahkeme_click").val(1);
        $("#mh_id").val("0");
        $("#mh_name").val("");
        console.log("new mahkeme reset");
        console.log("new mahkeme reset");
        
    });    
    
})



