"use strict";

// Class definition
var KTAppreminder = function () {

    var data = {
        id: '',
        eventTitle: '',
        eventDescription: '',
        eventSubject: '',
        startDate: '',
        endDate: '',
		noReminders: false,
        noReplay: false
    };
    

    var popover;
    var popoverState = false;

    // Add event variables
    var eventTitle;
    var eventDescription;
    var eventSubject;
    var startDatepicker;
    var startFlatpickr;
    var endDatepicker;
    var endFlatpickr;
    var startTimeFlatpickr;
    var endTimepicker
    var endTimeFlatpickr;
    var modal;
    var modalTitle;
    var form;
    var validator;
    var addButton;
    var addButton1;
    var submitButton;
    var cancelButton;
    var closeButton;

    // View event variables
    var vieweventTitle;
    var viewnoReplay;
    var viewEventDescription;
    var vieweventSubject;
    var viewStartDate;
    var viewEndDate;
    var viewModal;
    var viewEditButton;
    var viewDeleteButton;


    // Initialize popovers --- more info: https://getbootstrap.com/docs/4.0/components/popovers/
    const initPopovers = (element) => {
        handleViewButton();
    }


    // Init validator
    const initValidator = () => {
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
                    'reminder_title': {
                        validators: {
                            notEmpty: {
                                message: 'Başlık alanı zorunludur'
                            }
                        }
                    },
                    'reminder_subject': {
                        validators: {
                            stringLength: {
                                min: 3,
                                max: 250,
                                message: 'Konu alanı minimum 3 karakter içermelidir.'
                            },
                        }
                    },
                    'reminder_description': {
                        validators: {
                            stringLength: {
                                min: 3,
                                max: 250,
                                message: 'Açıklama alanı minimum 3 karakter içermelidir.'
                            },
                        }
                    },
                    'reminder_event_start_date': {
                        validators: {
                            notEmpty: {
                                message: 'Başlangıç tarihi zorunludur'
                            }
                        }
                    },
                    'reminder_event_end_date': {
                        validators: {
                            notEmpty: {
                                message: 'Bitiş tarihi zorunludur'
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

    // Initialize datepickers --- more info: https://flatpickr.js.org/
    const initDatepickers = () => {
        startFlatpickr = flatpickr(startDatepicker, {
            enableTime: false,
            dateFormat: "Y-m-d",
            defaultDate: "today",
            minDate: "today",
            maxDate: new Date().fp_incr(120) // 14 days from now
        });

        endFlatpickr = flatpickr(endDatepicker, {
            enableTime: false,
            dateFormat: "Y-m-d",
            defaultDate: new Date().fp_incr(5),
            minDate: "today",
            maxDate: new Date().fp_incr(120) // 14 days from now
        });

     /*   startTimeFlatpickr = flatpickr(startTimepicker, {
            enableTime: true,
            noreminder: true,
			noCalendar: true,
            dateFormat: "H:i",
			time_24hr: true
        });

        endTimeFlatpickr = flatpickr(endTimepicker, {
            enableTime: true,
            noreminder: true,
            dateFormat: "H:i",
			noCalendar: true,
			time_24hr: true
        });
	*/
    }


    // Handle add button
    const handleAddButton = () => {
        addButton.addEventListener('click', e => {

            // Reset form data
            data = {
                id: '',
                eventTitle: '',
                eventDescription: '',
                startDate: new Date(),
                endDate: new Date(),
                noReplay: false
            };
            handleNewEvent();
        });
    }


    // Handle add button
    const handleAddButton1 = () => {
        addButton1.addEventListener('click', e => {

            // Reset form data
            data = {
                id: '',
                eventTitle: '',
                eventDescription: '',
                startDate: new Date(),
                endDate: new Date(),
                noReplay: false
            };
            handleNewEvent();
        });
    }



    // Handle add new event
    const handleNewEvent = () => {
        // Update modal title
        modalTitle.innerText = "Yeni Not/Hatırlatma Ekle";

        modal.show();
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX+"/apps/hedas/notesreminders";
        // Select datepicker wrapper elements
        const datepickerWrappers = form.querySelectorAll('[data-kt-reminder="datepicker"]');

        // Handle all day toggle
        const noReplayToggle = form.querySelector('#kt_reminder_datepicker_noreplay');
        noReplayToggle.addEventListener('click', e => {
            if (e.target.checked) {
                datepickerWrappers.forEach(dw => {
                    dw.classList.add('d-none');
                });
            } else {
                endFlatpickr.setDate(data.startDate, true, 'Y-m-d');
                datepickerWrappers.forEach(dw => {
                    dw.classList.remove('d-none');
                });
            }
        });



        // Select datepicker wrapper elements
        const remindersWrappers = form.querySelectorAll('[data-kt-noreminder="noreminders"]');

        // Handle all day toggle
        const noRemindersToogle = form.querySelector('#kt_reminder_noreminders');
        noRemindersToogle.addEventListener('click', e => {
            if (e.target.checked) {
                remindersWrappers.forEach(dw => {
                    dw.classList.add('d-none');
                });
            } else {
                endFlatpickr.setDate(data.startDate, true, 'Y-m-d');
                remindersWrappers.forEach(dw => {
                    dw.classList.remove('d-none');
                });
            }
        });

        populateForm(data);

        // Handle submit form
        submitButton.addEventListener('click', function (e) {
            // Prevent default button action
            e.preventDefault();

            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    //console.log('validated!');
                    console.log(validator.validate());
                    if (status == 'Valid') {
                        // Show loading indication
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable submit button whilst loading
                        submitButton.disabled = true;

                        // Simulate form submission
                        setTimeout(function () {
                            // Simulate form submission
                            submitButton.removeAttribute('data-kt-indicator');

                            // Show popup confirmation
                            Swal.fire({
                                text: "Hatırlatıcıya yeni ekleme yapılacak!",
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
                                    //modal.hide();

                                    // Enable submit button after loading
                                    //submitButton.disabled = false;

                                    // Detect if is all day event
                                    let noReplayEvent = false;
                                    let noReminderEvent = false;
                                    if (noReplayToggle.checked) { noReplayEvent = true; }
                                    if (noRemindersToogle.checked) { noReminderEvent = true; }
                                //    if (startTimeFlatpickr.selectedDates.length === 0) { noReplayEvent = true; }

                                    // Merge date & time
                                    var startDateTime = moment(startFlatpickr.selectedDates[0]).format();
                                    var endDateTime = moment(endFlatpickr.selectedDates[endFlatpickr.selectedDates.length - 1]).format();
                                    if (!noReplayEvent) {
                                        const startDate = moment(startFlatpickr.selectedDates[0]).format('YYYY-MM-DD');
                                        const endDate = moment(endFlatpickr.selectedDates[0]).format('YYYY-MM-DD');//startDate;
                                 //       const startTime = moment(startTimeFlatpickr.selectedDates[0]).format('HH:mm:ss');
                                 //       const endTime = moment(endTimeFlatpickr.selectedDates[0]).format('HH:mm:ss');

                                        startDateTime = startDate; //+ 'T' + startTime;
                                        endDateTime = endDate; //+ 'T' + endTime;
                                    }

                                    if (!noReminderEvent) {
                                        const startDate = moment(startFlatpickr.selectedDates[0]).format('YYYY-MM-DD');
                                        const endDate = moment(endFlatpickr.selectedDates[0]).format('YYYY-MM-DD');
                                 //       const startTime = moment(startTimeFlatpickr.selectedDates[0]).format('HH:mm:ss');
                                 //       const endTime = moment(endTimeFlatpickr.selectedDates[0]).format('HH:mm:ss');

                                        startDateTime = startDate; //+ 'T' + startTime;
                                        endDateTime = endDate; //+ 'T' + endTime;
                                    }
                                    const postData = {
                                        route           : "addnotes",
                                        title           : eventTitle.value,
                                        subject         : eventSubject.value,
                                        description     : eventDescription.value,
                                        start           : startDateTime,
                                        end             : endDateTime,
                                        noReplay        : noReplayEvent,
                                        noReminder      : noReminderEvent
                                    }
                                    //console.log(postData);

                                    
                                    $.ajax({
                                        type: "POST",
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json",
                                        url: baseUrlHost+"/save_api",
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
                                                                window.location.assign(baseUrlHost);
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
                                    
                                   /*
                                    var jqxhr = $.post( baseUrlHost,postData, function(data) {
                                        console.log( data );
                                      })
                                        .done(function() {
                                            console.log( "second success" );
                                        })
                                        .fail(function() {
                                          console.log( "error" );
                                        })
                                        .always(function() {
                                            console.log( "finished" );
                                        });
                                       
                                      // Perform other work here ...
                                       
                                      // Set another completion function for the request above
                                      jqxhr.always(function() {
                                        console.log( "second finished" );
                                      });
                                      */
                                    // Add new event to reminder
                                   /** reminder.addEvent({
                                        id: uid(),
                                        title: eventTitle.value,
                                        description: eventDescription.value,
                                        location: eventSubject.value,
                                        start: startDateTime,
                                        end: endDateTime,
                                        noReplay: noReplayEvent
                                    });
                                    reminder.render();
									*/

                                    // Reset form for demo purposes only
                                   // form.reset();
                                }
                            });

                            //form.submit(); // Submit form
                        }, 500);
                    } else {
                        // Show popup warning
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
                    form.reset(); // Reset form
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
                    form.reset(); // Reset form
                    modal.hide(); // Hide modal
                }
            });
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

    // Populate form
    const populateForm = () => {
        eventTitle.value = data.eventTitle ? data.eventTitle : '';
        eventDescription.value = data.eventDescription ? data.eventDescription : '';
        eventSubject.value = data.eventSubject ? data.eventSubject : '';
        startFlatpickr.setDate(data.startDate, true, 'Y-m-d');

        // Handle null end dates
        const endDate = data.endDate ? data.endDate : moment(data.startDate).format();
        endFlatpickr.setDate(endDate, true, 'Y-m-d');


    		const noRemindersToogle		= form.querySelector('#kt_reminder_noremindres');
        const noReplayToggle = form.querySelector('#kt_reminder_datepicker_noreplay');

    		const remindersWrappers 	= form.querySelectorAll('[data-kt-noreminder="noreminders"]');
            if (data.noReminders) {
                noRemindersToogle.checked = true;
                remindersWrappers.forEach(dw => {
                    dw.classList.add('d-none');
                });
            } else {
            //    startTimeFlatpickr.setDate(data.startDate, true, 'Y-m-d H:i');
            //    endTimeFlatpickr.setDate(data.endDate, true, 'Y-m-d H:i');
                endFlatpickr.setDate(data.endDate, true, 'Y-m-d');
    			      startFlatpickr.setDate(data.startDate, true, 'Y-m-d');
                remindersWrappers.forEach(dw => {
                    dw.classList.remove('d-none');
                });
            }



        const datepickerWrappers = form.querySelectorAll('[data-kt-reminder="datepicker"]');
          if (data.noReplay) {
              startFlatpickr.setDate(data.startDate, true, 'Y-m-d');
              noReplayToggle.checked = true;
              datepickerWrappers.forEach(dw => {
                  dw.classList.add('d-none');
              });
          } else {
          //    startTimeFlatpickr.setDate(data.startDate, true, 'Y-m-d H:i');
          //    endTimeFlatpickr.setDate(data.endDate, true, 'Y-m-d H:i');
              startFlatpickr.setDate(data.startDate, true, 'Y-m-d');
              endFlatpickr.setDate(data.startDate, true, 'Y-m-d');
              noReplayToggle.checked = false;
              datepickerWrappers.forEach(dw => {
                  dw.classList.remove('d-none');
              });
          }
    }

    // Format Fullreminder reponses
    const formatArgs = (res) => {
        data.id = res.id;
        data.eventTitle = res.title;
        data.eventDescription = res.description;
        data.eventSubject = res.subject;
        data.startDate = res.startStr;
        data.endDate = res.endStr;
		data.noReminders = res.noReminders;
        data.noReplay = res.noReplay;
    }

    // Generate unique IDs for events
    const uid = () => {
        return Date.now().toString() + Math.floor(Math.random() * 1000).toString();
    }

    /*
    const isValidJsonString = (jsonString) => {
    
        if(!(jsonString && typeof jsonString === "string")){
            return false;
        }
    
        try{
           JSON.parse(jsonString);
           return true;
        }catch(error){
            return false;
        }
    
    }
    */

    return {
        // Public Functions
        init: function () {
            // Define variables

            // Add event modal
            const element = document.getElementById('kt_modal_add_notesreminders');
            form = element.querySelector('#kt_modal_add_notesreminders_form');
            eventTitle = form.querySelector('[name="reminder_title"]');
            eventDescription = form.querySelector('[name="reminder_description"]');
            eventSubject = form.querySelector('[name="reminder_subject"]');
            startDatepicker = form.querySelector('#kt_reminder_datepicker_start_date');
            endDatepicker = form.querySelector('#kt_reminder_datepicker_end_date');
         //   startTimepicker = form.querySelector('#kt_reminder_datepicker_start_time');
         //   endTimepicker = form.querySelector('#kt_reminder_datepicker_end_time');
            addButton = document.querySelector('[data-kt-reminder="add"]');
            addButton1 = document.querySelector('[data-kt-reminder="add1"]');
            submitButton = form.querySelector('#kt_modal_add_notesreminders_submit');
            cancelButton = form.querySelector('#kt_modal_add_notesreminders_cancel');
            closeButton = element.querySelector('#kt_modal_add_notesreminders_close');
            modalTitle = form.querySelector('[data-kt-reminder="title"]');
            modal = new bootstrap.Modal(element);

            // View event modal
        //    const viewElement = document.getElementById('kt_modal_view_event');
        //    viewModal = new bootstrap.Modal(viewElement);
        //    vieweventTitle = viewElement.querySelector('[data-kt-reminder="event_name"]');
        //    viewnoReplay = viewElement.querySelector('[data-kt-reminder="all_day"]');
        //    viewEventDescription = viewElement.querySelector('[data-kt-reminder="event_description"]');
        //    vieweventSubject = viewElement.querySelector('[data-kt-reminder="event_location"]');
        //    viewStartDate = viewElement.querySelector('[data-kt-reminder="event_start_date"]');
        //    viewEndDate = viewElement.querySelector('[data-kt-reminder="event_end_date"]');
        //    viewEditButton = viewElement.querySelector('#kt_modal_view_event_edit');
        //    viewDeleteButton = viewElement.querySelector('#kt_modal_view_event_delete');

            initValidator();
            initDatepickers();
          //  handleEditButton();
            handleAddButton();
            handleAddButton1();
           // handleDeleteEvent();
            handleCancelButton();
            handleCloseButton();
            resetFormValidator(element);
        }
    };


}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAppreminder.init();
});
