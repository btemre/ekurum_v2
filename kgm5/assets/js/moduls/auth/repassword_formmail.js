"use strict";

// Class definition
var KTSigninGeneralMail = function() {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleForm = function(e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
			form,
			{
				fields: {
					'kodmail': {
                        validators: {
                            notEmpty: {
                                message: 'Doğrulama Kodunuzu Girin!'
                            },
                            stringLength: {
                                min		: 6,
                                max		: 10,
                                message: 'Doğrulama kodu geçerli değil!'
                            }
                        }
					},
                    'passwordmail': {
                        validators: {
                            notEmpty: {
                            message: 'Yeni Parolanız Gerekli!'
                            },
                            stringLength: {
                            min		: 12,
                            max		: 30,
                            message: 'Minimum 12 karakter maksimum 30 karakter içermeli!'
                            }
                        }
                    },
                    'repasswordmail': {
                        validators: {
                            notEmpty: {
                                message: 'Parola Tekrarı Gerekli!'
                            },
                            identical: {
                                compare: function () {
                                    return form.querySelector('[name="passwordmail"]').value;
                                },
                                message: 'Parola ile tekrarı uyuşmuyor!'
                            }
                        }
                    }
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',                            
                        eleInvalidClass:"",
                        eleValidClass:""
                    })
				}
			}
		);
        // Handle form submit
        submitButton.addEventListener('click', function (e) {
            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    // Show loading indication
                    submitButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click
                    submitButton.disabled = true;


                    // Simulate ajax request
                    setTimeout(function() {
                        // Hide loading indication
                        submitButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton.disabled = false;
                        form.submit();

                    }, 2000);
                } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        text: "Üzgünüz! Bazı eksiklikler tespit edildi. Lütfen girdiğiniz bilgileri kontrol ediniz.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Anladım.",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
		});
    }

    // Public functions
    return {
        // Initialization
        init: function() {
            form = document.querySelector('#kt_new_passwordmail_form');
            submitButton = document.querySelector('#kt_new_passwordmail_submit');
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTSigninGeneralMail.init();
});
