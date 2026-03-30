"use strict";
var table;
var dt;
// Class definition
var KTCezaIptalListServerSide = function () {
    // Shared variables

    var filterData = {
        ciText: '',
		ciId: '',
		ciEsasNo: '',
		ciKararNo: '',
		ciCezaSeriNo: '',
		ciKurumDosyaNo: '',
		ciEvrakDurum: -1,
		ciItirazEden: '',
		ciIcra: '',
		ciMahkeme: '',
		ciCezaKonu: '',
        ciDavaKonu: '',
        ciAciklama: '',
        ciAcilisTarih: '',
        ciKararTarih: '',
        ciTags: '',
        ciAralik: '',
        ciFilter: false
	};
    
    const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
	var calisma_aralik;
    var filter_acilis;
    var filter_karar;
    var tarihPicker = document.querySelector('#kt_table_cezaiptal_datein');
    var acilisPicker = document.querySelector('#filter_acilistarihi');
    var kararPicker = document.querySelector('#filter_karartarihi');

    // Private functions
    var initDatatable = function () {
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX;
		calisma_aralik  = tarihPicker.value;
        filter_acilis   = acilisPicker.value;
        filter_karar    = kararPicker.value;
		filterData.ciAralik         = calisma_aralik;
        filterData.ciAcilisTarih    = filter_acilis;
		filterData.ciKararTarih     = filter_karar;
	/*	$('#kt_content_cezaiptal_list thead tr')
				.clone(true)
				.addClass('filters')
				.appendTo('#kt_content_cezaiptal_list thead');		
		*/

		
        dt = $("#kt_content_cezaiptal_list").DataTable({
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
            order: [[1, 'desc']],
            //stateSave: true,
            ajax: {
                url: baseUrlHost+"/apps/hedas/cezaiptal/api_list",
				method: 'POST',
				//dataType: 'json',
				//contentType: 'application/json',
            },
            columns: [
                { data: null },
                // { data: '_id' },
                { data: '_acilis' },
                { data: '_cezakonu' },
                { data: '_kurumdosyano' },
                { data: '_itirazeden' },
                { data: '_davakonu' },
				{ data: '_mahkeme' },
				{ data: '_esasno' },
				{ data: '_kararno' },
				{ data: '_karartarih' },
				{ data: '_plaka' },
				{ data: '_cezaserino' },
				{ data: '_durum' },
				{ data: '_icra' },
				{ data: '_tags' },
				{ data: '_aciklama' },
                
            ],
            columnDefs: [
				// {
				// 	targets: 0,
				// 	data: null,
				// 	orderable: true,
				// 	className: 'text-center',
				// 	render: function (data, type, row) { 
				// 		return row._id;
				// 	}
				// },
                {
                    targets: 0,
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `
                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">İşlemler
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
								<a href="#" class="menu-link px-3 ciEditButton" data-id="`+row._id+`" id="edit`+row._id+`" onclick="KTModalEditCezaIptal.viewModal('`+row._id+`');">Düzenle</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-1">
								<a href="#" class="menu-link px-3 popup-btn ciCopButton" data-id="`+row._id+`" id="arsivle`+row._id+`" onclick="KTModalEditCezaIptal.copeAtModal('`+row._id+`');">Çöpe At
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
        }).columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14])
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

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()d
    var handleSearchDatatable = function () {
		var filterDurum 		= document.querySelectorAll('[data-kt-cezaiptal-table-filter="filterDurum"]');
		//var filterKategori	= document.querySelectorAll('[data-kt-cezaiptal-table-filter="filterKategori"]');

        filterSearch.addEventListener('keyup', function (e) {
            filterData.ciAralik 		= calisma_aralik
            filterData.ciAcilisTarih = filter_acilis;
            filterData.ciKararTarih = filter_karar;
            filterData.ciText = e.target.value;
            filterData.ciId = e.target.value;
            filterData.ciEsasNo = e.target.value;
            filterData.ciKararNo = e.target.value;
            filterData.ciCezaSeriNo = e.target.value;
            filterData.ciKurumDosyaNo = e.target.value;
            //filterData.ciEvrakDurum = e.target.value;
            filterData.ciItirazEden = e.target.value;
            filterData.ciIcra = e.target.value;
            filterData.ciMahkeme = e.target.value;
            filterData.ciCezaKonu = e.target.value;
            filterData.ciDavaKonu = e.target.value;
            filterData.ciAciklama = e.target.value;
            filterData.ciTags = e.target.value;
            filterData.ciFilter = false;


            
        // Get filter values
            filterDurum.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.ciEvrakDurum  = item.value;
                    //console.log("TUR_>",item.value, item.innerText);
                }else{
                    filterData.ciEvrakDurum = -1;
                }
            });

            filterData.ciAcilisTarih = 0;
            filterData.ciKararTarih = 0;

           /* // Get filter values
            filterKategori.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.ggKategori  = item.value;
                    //console.log("KATEGORI>",item.value, item.innerText);
                }else{
                    filterData.ggKategori = -1;
                }
            });
            */
            //dt.search(e.target.value).draw();
            handleFilterSubmit();
        });
		
    }

    // Filter Datatable
    var handleFilterDatatable = () => {
        // Select filter options
        var filterDurum 		= document.querySelectorAll('[data-kt-cezaiptal-table-filter="filterDurum"]');
		//var filterKategori	= document.querySelectorAll('[data-kt-cezaiptal-table-filter="filterKategori"]');
        const filterButton = document.querySelector('[data-kt-docs-table-filter="filter"]');

        // Filter datatable on submit
        filterButton.addEventListener('click', function () {
			
            // Get filter values
            filterDurum.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.ciEvrakDurum  = item.value;
                    //console.log("TUR_>",item.value, item.innerText);
                }else{
                    filterData.ciEvrakDurum  = -1;
                }
            });
           /* // Get filter values
            filterKategori.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.ggKategori  = item.value;
                    //console.log("KATEGORI>",item.value, item.innerText);
                }else{
                    filterData.ggKategori  = -1;
                }
            });
            */
            calisma_aralik = tarihPicker.value;
            filterData.ciAralik = calisma_aralik;
            filter_acilis = acilisPicker.value;
            filterData.ciAcilisTarih = filter_acilis;
            filter_karar = kararPicker.value;
            filterData.ciKararTarih = filter_karar;
            filterData.ciFilter = true;
            console.log(filterData);
			handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
    }
	
	var handleFilterSubmit = () => {
			dt
			.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15])
			.flatten()
			.search(JSON.stringify(filterData))
			.draw();		
	}

    // Reset Filter
    var handleResetForm = () => {
        // Select reset button
        const resetButton = document.querySelector('[data-kt-docs-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Reset payment type

            $('#filterDurumSelect').val('-1').trigger('change'); // Select the option with a value of '1'

           // $('#filterKategoriSelect').val('-1').trigger('change'); // Select the option with a value of '1'

            filterData.ciEvrakDurum  = -1;
            //filterData.ggKategori  = -1;
            calisma_aralik = tarihPicker.value;
            filterData.ciAralik = calisma_aralik;
            filterData.ciAcilisTarih = 0;
            filterData.ciKararTarih = 0;
            filterData.ciFilter = false;

            handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
    }

    var handleDatatimePicker = function(element) {
		$("#kt_table_cezaiptal_datein").daterangepicker({
			opens: 'left',
			showDropdowns: true,
			minYear: parseInt(moment().subtract(5, 'year').format("YYYY"), 10),
			maxYear: parseInt(moment().subtract(-5, 'year').format("YYYY"), 10),
			startDate: moment().subtract(6, 'month').startOf('month').format("DD-MM-YYYY"),//moment().subtract(29, 'days').format("DD-MM-YYYY"),
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
			//console.log("calisma_aralik", calisma_aralik);
		});

        $("#filter_acilistarihi").daterangepicker({
            opens: 'left',
            showDropdowns: true,
            minYear: parseInt(moment().subtract(5, 'year').format("YYYY"), 10),
            maxYear: parseInt(moment().subtract(-5, 'year').format("YYYY"), 10),
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
            filter_acilis = start.format('DD-MM-YYYY') + ' & ' + end.format('DD-MM-YYYY');
            //console.log("calisma_aralik", calisma_aralik);
        });

        $("#filter_karartarihi").daterangepicker({
            opens: 'left',
            showDropdowns: true,
            minYear: parseInt(moment().subtract(5, 'year').format("YYYY"), 10),
            maxYear: parseInt(moment().subtract(-5, 'year').format("YYYY"), 10),
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
            filter_karar = start.format('DD-MM-YYYY') + ' & ' + end.format('DD-MM-YYYY');
            //console.log("calisma_aralik", calisma_aralik);
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
            handleFilterSubmit();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTCezaIptalListServerSide.init();
});