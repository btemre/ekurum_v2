"use strict";
var table;
var dt;
// Class definition
var KTGelenGidenListServerSide = function () {
    // Shared variables
   
    
    var filterData = {
        ggText: '',
		ggId: '',
		ggTur: '',
		ggTarih: '',
		ggIlgili: '',
		ggTur: -1,
		ggSayi: '',
		ggDosyaNo: '',
		ggKategori: -1,
		ggAciklama: '',
		ggEtiket: ''
	};
    const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
	var calisma_aralik;
    var tarihPicker = document.querySelector('#kt_table_gelengiden_datein');


    // Private functions
    var initDatatable = function () {
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX;
		calisma_aralik = tarihPicker.value;

		filterData.ggTarih = calisma_aralik
		
	/*	$('#kt_content_gelengiden_list thead tr')
				.clone(true)
				.addClass('filters')
				.appendTo('#kt_content_gelengiden_list thead');		
		*/
		
        dt = $("#kt_content_gelengiden_list").DataTable({
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
                url: baseUrlHost+"/apps/hedas/gelengiden/api_list",
				method: 'POST',
				//dataType: 'json',
				//contentType: 'application/json',d
            },
            columns: [
                { data: null },
                { data: '_tarih' },
                { data: '_ilgili' },
                { data: '_tur' },
                { data: '_sayi' },
                { data: '_dosyano' },
				{ data: '_kategori' },
				{ data: '_tags' },
				{ data: '_aciklama' },
            ],
            columnDefs: [
				
                {
                    targets: 0,
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `
                            <a href="#" class="btn btn-light btn-info btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">İşlemler
							<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
							<span class="svg-icon svg-icon-5 m-0">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
								</svg>
							</span>
							<!--end::Svg Icon-->
						</a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-1" data-kt-menu="true">
							<!--begin::Menu item-->
							<div class="menu-item px-1">
								<a href="#" class="menu-link px-3 ggEditButton" data-id="`+row._id+`" id="edit`+row._id+`" onclick="KTModalEditGelenGiden.viewModal('`+row._id+`');">Düzenle</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-1">
								<a href="#" class="menu-link px-3 popup-btn ggCopButton" data-id="`+row._id+`" id="arsivle`+row._id+`" onclick="KTModalEditGelenGiden.copeAtModal('`+row._id+`');">Çöpe At
								</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
                        `;
                    },
                },
            ],
            // Add data-filter attribute
			/*
            createdRow: function (row, data, dataIndex) {
                $(row).find('td:eq(1)').attr('data-filter', data._tarih);
            }
			*/
        }).columns([1,2,3,4,5,6,7,8])
        .flatten()
        .search(JSON.stringify(filterData));

        table = dt.$;

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dt.on('draw', function () {
           // initToggleToolbar();
           // toggleToolbars();
            //handleDeleteRows();
            KTMenu.createInstances();
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
		var filterTur 		= document.querySelectorAll('[data-kt-gelengiden-table-filter="filterTur"]');
		var filterKategori	= document.querySelectorAll('[data-kt-gelengiden-table-filter="filterKategori"]');

        filterSearch.addEventListener('keyup', function (e) {
            filterData.ggTarih 		= calisma_aralik
            filterData.ggText = e.target.value;
            filterData.ggId = e.target.value;
            filterData.ggIlgili = e.target.value;
            filterData.ggSayi = e.target.value;
            filterData.ggDosyaNo = e.target.value;
            filterData.ggAciklama = e.target.value;
            filterData.ggEtiket = e.target.value;

        // Get filter values
            filterTur.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.ggTur  = item.value;
                    //console.log("TUR_>",item.value, item.innerText);
                }else{
                    filterData.ggTur = -1;
                }
            });
            // Get filter values
            filterKategori.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.ggKategori  = item.value;
                    //console.log("KATEGORI>",item.value, item.innerText);
                }else{
                    filterData.ggKategori = -1;
                }
            });

        //dt.search(e.target.value).draw();
            handleFilterSubmit();
        });
		
    }

    // Filter Datatable
    var handleFilterDatatable = () => {
        // Select filter options
        var filterTur 		= document.querySelectorAll('[data-kt-gelengiden-table-filter="filterTur"]');
		var filterKategori	= document.querySelectorAll('[data-kt-gelengiden-table-filter="filterKategori"]');
        const filterButton = document.querySelector('[data-kt-docs-table-filter="filter"]');

        // Filter datatable on submit
        filterButton.addEventListener('click', function () {
			
            // Get filter values
            filterTur.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.ggTur  = item.value;
                    //console.log("TUR_>",item.value, item.innerText);
                }else{
                    filterData.ggTur  = -1;
                }
            });
            // Get filter values
            filterKategori.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.ggKategori  = item.value;
                    //console.log("KATEGORI>",item.value, item.innerText);
                }else{
                    filterData.ggKategori  = -1;
                }
            });
            calisma_aralik = tarihPicker.value;
            filterData.ggTarih = calisma_aralik;

			handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
    }
	
	var handleFilterSubmit = () => {
			dt
			.columns([1,2,3,4,5,6,7,8])
			.flatten()
			.search(JSON.stringify(filterData))
			.draw();		
	}

    // Delete customer
    var handleDeleteRows = () => {
        // Select all delete buttons
        const deleteButtons = document.querySelectorAll('[data-kt-docs-table-filter="delete_row"]');

        deleteButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');

                // Get customer name
                const customerName = parent.querySelectorAll('td')[1].innerText;

                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Are you sure you want to delete " + customerName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        // Simulate delete request -- for demo purpose only
                        Swal.fire({
                            text: "Deleting " + customerName,
                            icon: "info",
                            buttonsStyling: false,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function () {
                            Swal.fire({
                                text: "You have deleted " + customerName + "!.",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                // delete row data from server and re-draw datatable
                                dt.draw();
                            });
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: customerName + " was not deleted.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            })
        });
    }

    // Reset Filter
    var handleResetForm = () => {
        // Select reset button
        const resetButton = document.querySelector('[data-kt-docs-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Reset payment type

            $('#filterTurSelect').val('-1').trigger('change'); // Select the option with a value of '1'

            $('#filterKategoriSelect').val('-1').trigger('change'); // Select the option with a value of '1'

            filterData.ggTur  = -1;
            filterData.ggKategori  = -1;
            calisma_aralik = tarihPicker.value;
            filterData.ggTarih = calisma_aralik;

            handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
    }

    // Init toggle toolbar
    var initToggleToolbar = function () {
        // Toggle selected action toolbar
        // Select all checkboxes
        const container = document.querySelector('#kt_content_gelengiden_list');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');

        // Select elements
        const deleteSelected = document.querySelector('[data-kt-docs-table-select="delete_selected"]');

        // Toggle delete selected toolbar
        checkboxes.forEach(c => {
            // Checkbox on click event
            c.addEventListener('click', function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        // Deleted selected rows
        deleteSelected.addEventListener('click', function () {
            // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
            Swal.fire({
                text: "Are you sure you want to delete selected customers?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                showLoaderOnConfirm: true,
                confirmButtonText: "Yes, delete!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                },
            }).then(function (result) {
                if (result.value) {
                    // Simulate delete request -- for demo purpose only
                    Swal.fire({
                        text: "Deleting selected customers",
                        icon: "info",
                        buttonsStyling: false,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(function () {
                        Swal.fire({
                            text: "You have deleted all selected customers!.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        }).then(function () {
                            // delete row data from server and re-draw datatable
                            dt.draw();
                        });

                        // Remove header checked box
                        const headerCheckbox = container.querySelectorAll('[type="checkbox"]')[0];
                        headerCheckbox.checked = false;
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Selected customers was not deleted.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });
    }

    // Toggle toolbars
    var toggleToolbars = function () {
        // Define variables
        const container = document.querySelector('#kt_content_gelengiden_list');
        const toolbarBase = document.querySelector('[data-kt-docs-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-docs-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-docs-table-select="selected_count"]');

        // Select refreshed checkbox DOM elements 
        const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');

        // Detect checkboxes state & count
        let checkedState = false;
        let count = 0;

        // Count checked boxes
        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });

        // Toggle toolbars
        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    }
	
	var handleDatatimePicker = function(element) {
		$("#kt_table_gelengiden_datein").daterangepicker({
			opens: 'left',
			showDropdowns: true,
			minYear: parseInt(moment().subtract(10, 'year').format("YYYY"), 10),
			maxYear: parseInt(moment().subtract(-3, 'year').format("YYYY"), 10),
			startDate: moment().subtract(12, 'month').startOf('month').format("DD-MM-YYYY"),//moment().subtract(29, 'days').format("DD-MM-YYYY"),
			endDate: moment().format("DD-MM-YYYY"),//moment().subtract(0, 'month').endOf('month'),//
			ranges: {
				'Bugün': [moment(), moment()],
				'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
				'Son 30 Gün': [moment().subtract(29, 'days'), moment()],
				'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
				'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Son 6 Ay': [moment().subtract(6, 'month').startOf('month'), moment()]
			},
			locale: {
				applyLabel: 'Aralığı Seç',
				cancelLabel: 'Vazgeç',
				format: 'DD-MM-YYYY',
				customRangeLabel: 'Kendim Seçeceğim',
				separator: ' & ',
				fromLabel: 'From',
				toLabel: '&',
				weekLabel: 'W',
				daysOfWeek: ['Pzr', 'Pts', 'Sal', 'Çar', 'Per', 'Cum', 'Cts'],
				monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
				firstDay: 1
			}
		}, function(start, end, label) {
			calisma_aralik = start.format('DD-MM-YYYY') + ' & ' + end.format('DD-MM-YYYY');
			console.log("calisma_aralik", calisma_aralik);
		  });
	}
	
    // Public methods
    return {
        init: function () {
			handleDatatimePicker();		
            initDatatable();
			
           handleSearchDatatable();
            //initToggleToolbar();
            handleFilterDatatable();
           // handleDeleteRows();
            handleResetForm();
			
        },
        reload: function(){
            $("#kt_content_gelengiden_list").DataTable();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTGelenGidenListServerSide.init();
});