"use strict";

// Class definition
var KTSigninGeneral = function() {
    // Elements
    var form;
    var submitButton;
    var unitField;
    var groupField;
    var statusField;
    var validator;

    // Handle form
    var handleForm = function(e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
			form,
			{
				fields: {
          'units': {
          validators: {
              notEmpty: {
                message: 'Lütfen Kullanıcının Birimini Seçiniz.'
              },
              callback: {
                message: 'Geçerli bir birim seçilmedi',
                callback: function (input) {
                    // Get the selected options
                    const options = input.select2('data');
                    return options != null && options.length >= 1 && options.length <= 99;
                }
            },
						}
					},
          'group': {
          validators: {
              notEmpty: {
                message: 'Lütfen Kullanıcının Grubunu Seçiniz.'
              },
              callback: {
                message: 'Geçerli bir grup seçilmedi',
                callback: function (input) {
                    // Get the selected options
                    const options = input.select2('data');
                    return options != null && options.length >= 1 && options.length <= 99;
                }
            },
						}
					},
          'status': {
              validators: {
                notEmpty: {
                  message: 'Lütfen Kullanıcının Statüsü Seçiniz.'
                },
                callback: {
                  message: 'Geçerli bir statü seçilmedi',
                  callback: function (input) {
                      // Get the selected options
                      const options = input.select2('data');
                      return options != null && options.length >= 1 && options.length <= 99;
                  }
              },
						}
					},
          'name': {
          validators: {
              notEmpty: {
                message: 'Ad Gerekli!'
              },
              regexp: {
                  regexp: /^[a-zığüşiöçİĞÜŞÖÇ]+$/i,
                  message: 'Sadece Harf Kullanınız. İkinci adınızı yazmayınız.',
              },
              stringLength: {
								min		: 2,
								max		: 30,
								message: 'Geçerli bir ad giriniz!'
							}
						}
					},
          'lastname': {
          validators: {
              regexp: {
                regexp: /^[a-zığüşiöçİĞÜŞÖÇ]+$/i,
                message: 'Sadece Harf Kullanınız. İkinci adınızı yazmayınız.',
              },
              stringLength: {
								min		: 2,
								max		: 30,
								message: 'Geçerli bir ad giriniz!'
							}
						}
					},
          'surname': {
          validators: {
              notEmpty: {
                message: 'Soyad Gerekli!'
              },
              regexp: {
                regexp: /^[a-zığüşiöçİĞÜŞÖÇ]+$/i,
                message: 'Sadece Harf Kullanınız. İkinci adınızı yazmayınız.',
              },
              stringLength: {
								min		: 2,
								max		: 30,
								message: 'Geçerli bir soyad giriniz!'
							}
						}
					},
          'email': {
          validators: {
              emailAddress: {
								message: 'Değer geçerli bir email adresi değil!'
							}
						}
					},
					'username': {
            validators: {
							notEmpty: {
								message: 'Kullanıcı Adı Gerekli!'
							},
              stringLength: {
								min		: 3,
								max		: 25,
								message: 'Değer geçerli bir kullanıcı adı değil!'
							}
						}
					},
          'password': {
              validators: {
                  stringLength: {
    								min		: 12,
    								max		: 30,
    								message: 'Minimum 12 karakter maksimum 30 karakter içermeli!'
    							}
              }
          },
          'repassword': {
              validators: {
                  identical: {
                      compare: function () {
                          return form.querySelector('[name="password"]').value;
                      },
                      message: 'Parola ile tekrarı uyuşmuyor!'
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

        unitField.select2().on('change.select2', function (e) {
          // Revalidate the color field when an option is chosen
          validator.revalidateField('units');
        });
        groupField.select2().on('change.select2', function (e) {
          // Revalidate the color field when an option is chosen
          validator.revalidateField('group');
        });
        statusField.select2().on('change.select2', function (e) {
          // Revalidate the color field when an option is chosen
          validator.revalidateField('status');
        });

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
            form = document.querySelector('#kt_update_form');
            submitButton = document.querySelector('#kt_update_form_submit');
            unitField = jQuery(form.querySelector('[name="units"]'));
            groupField = jQuery(form.querySelector('[name="group"]'));
            statusField = jQuery(form.querySelector('[name="status"]'));
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTSigninGeneral.init();
});
