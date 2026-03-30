"use strict";

// Class definition
var KTSigninGeneral = function() {
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
					'username': {
                        validators: {
							notEmpty: {
								message: 'Kullanıcı Adı Gerekli.'
							},
                            stringLength: {
								min		: 4,
								max		: 25,
								message: 'Değer geçerli bir kullanıcı adı değil.'
							}
						}
					},
          'password': {
              validators: {
                  notEmpty: {
                      message: 'Parola gerekli.'
                  },
                  stringLength: {
    								min		: 12,
    								max		: 30,
    								message: 'Minimum 12 karakter maksimum 30 karakter içermeli.'
    							}
              }
          }
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
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
                    submitButton.disabled = false;
                }
            });
		});
    }

    // Public functions
    return {
        // Initialization
        init: function() {
            form = document.querySelector('#kt_sign_in_form');
            submitButton = document.querySelector('#kt_sign_in_submit');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTSigninGeneral.init();
});
