"use strict";
var tablecezakayitmh;
var dtcezakayitmh;
// Class definition
var KTCezalarMHareketlerServerSide = function () {
    // Shared variables
    
    var filterData = {
        chText: '',
		chId: '',
		chUserId: '',
		chKayitTarih: '',
		chPlaka: '',
		chSeriNo: '',
		chSiraNo: '',
		chAciklama: '',
	};

    const filterSearch = document.querySelector('[data-kt-dosc-ceza-hareketler-m-table-filter="search"]');
	var calisma_aralik;
    var tarihPicker = document.querySelector('#kt_table_ceza_hareketler_m_datein');


    // Private functions
    var initDatatable = function () {
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX;
		calisma_aralik = tarihPicker.value;

		filterData.chKayitTarih = calisma_aralik
		
		
       
        dtcezakayitmh = $("#kt_content_durusmalar_hareketler_list").DataTable({
            searchDelay: 1000,
            processing: true,
            serverSide: true,
            deferRender: true,
			responsive: true,
			pageLength: 20,
            loadingRecords: "Kayıtlar yükleniyor.",
			language: {sDecimal:",",sEmptyTable:"Henüz kayıt yok..",sInfo:"_TOTAL_ Kayıt Bulundu",sInfoEmpty:"Kayıt yok",sInfoFiltered:"(_MAX_ Kayıt İçerisinden)",sInfoPostFix:"",sInfoThousands:".",sLengthMenu:"Sayfada _MENU_ kayıt göster",sLoadingRecords:"Yükleniyor...",sProcessing:"İşleniyor...",sSearch:"Ara:",sZeroRecords:"Eşleşen kayıt bulunamadı",oPaginate:{sFirst:"İlk",sLast:"Son",sNext:"Sonraki",sPrevious:"Önceki"},oAria:{sSortAscending:": artan sütun sıralamasını aktifleştir",sSortDescending:": azalan sütun sıralamasını aktifleştir"},select:{rows:{"_":"%d kayıt seçildi","0":"","1":"1 kayıt seçildi"}}},
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
                url: baseUrlHost+"/apps/edts/durusmalar/api_mahkemelerlist",
				method: 'POST',
				//dataType: 'json',
				//contentType: 'application/json',
            },
            columns: [
                { data: '_id' },
                { data: '_mhadi' },
                { data: null }
            ],
            columnDefs: [
				
                {
                    targets: 2,
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
            
            
        }).columns([0,1])
        .flatten()
        .search(JSON.stringify(filterData));
        // console.log(dtcezakayitmh);

        // const tableRows = tablecezakayitmh.querySelectorAll('tbody tr');

        // tableRows.forEach(row => {
        //     const dateRow = row.querySelectorAll('td');
        //     const realDate = moment(dateRow[3].innerHTML, "DD MMM YYYY, LT").format(); // select date from 4th column in table
        //     dateRow[3].setAttribute('data-order', realDate);
        // });

        tablecezakayitmh = dtcezakayitmh.$;

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dtcezakayitmh.on('draw', function () {
           // initToggleToolbar();
           // toggleToolbars();
            //handleDeleteRows();
            KTMenu.createInstances();
        });

        

    }

    //İNİTDATATABLE SON

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
		var filterTur 		= document.querySelectorAll('[data-kt-cezalar-update-m-table-filter="filterTur"]');
		var filterKategori	= document.querySelectorAll('[data-kt-cezalar-update-m-table-filter="filterKategori"]');

        filterSearch.addEventListener('keyup', function (e) {
            filterData.chKayitTarih 		= calisma_aralik
            filterData.chText = e.target.value;
            filterData.chId = e.target.value;
            filterData.chUserId = e.target.value;
            filterData.chPlaka = e.target.value;
            filterData.chSeriNo = e.target.value;
            filterData.chSiraNo = e.target.value;
            filterData.chAciklama = e.target.value;
    
            /* 
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
            */
            //dt.search(e.target.value).draw();
            handleFilterSubmit();
        });
		
    }

    var handleResetForm = () => {
        // Select reset button
        const resetButton = document.querySelector('[data-kt-dosc-ceza-hareketler-m-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Reset payment type

            // $('#filterIstasyonSelect').val('-1').trigger('change'); // Select the option with a value of '1'

            // filterData.cIstasyonid  = -1;
            calisma_aralik = tarihPicker.value;
            filterData.cKayitTarih = calisma_aralik;

            handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
    }
    
    // Filter Datatabled
    var handleFilterDatatable = () => {
        // Select filter options
        // var filterIstasyon 		= document.querySelectorAll('[data-kt-cezalar-update-m-table-filter="filterIstasyon"]');
        const filterButton = document.querySelector('[data-kt-dosc-ceza-hareketler-m-table-filter="filter"]');

        // Filter datatable on submit
        filterButton.addEventListener('click', function () {
			
             // Get filter values
            //  filterIstasyon.forEach((item, index) => {
            //     if (item.innerText.indexOf('selected') && item.value !== '') {
            //         // Build filter value options
            //         filterData.cIstasyonid  = item.value;
            //         //console.log("TUR_>",item.value, item.innerText);
            //     }else{
            //         filterData.cIstasyonid  = -1;
            //     }
            // });
            // Get filter values
            
            calisma_aralik = tarihPicker.value;
            filterData.cKayitTarih = calisma_aralik;

			handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
    }
	
	var handleFilterSubmit = () => {
			dtcezakayitmh
			.columns([0,1,2,3,4,5,6])
			.flatten()
			.search(JSON.stringify(filterData))
			.draw();		
	}

	var handleDatatimePicker = function(element) {
		$("#kt_table_ceza_hareketler_m_datein").daterangepicker({
			opens: 'left',
			showDropdowns: true,
			minYear: parseInt(moment().subtract(10, 'year').format("YYYY"), 10),
			maxYear: parseInt(moment().subtract(-3, 'year').format("YYYY"), 10),
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
			//console.log("calisma_aralik", calisma_aralikg);
		  });
	}
	
    var exportButtons = () => {
        const documentTitle = 'Ceza_Kayitlari-'+calisma_aralik;
        var buttons = new $.fn.dataTable.Buttons(tablecezakayitmh, {
            buttons: [
                {
                    extend: 'copyHtml5',
                    title: documentTitle
                },
                {
                    extend: 'excelHtml5',
                    title: documentTitle
                },
                {
                    extend: 'csvHtml5',
                    title: documentTitle
                },
                {
                    extend: 'pdfHtml5',
                    title: documentTitle
                }
            ]
        }).container().appendTo($('#kt_datatable_example_1_export'));

        // Hook dropdown menu click event to datatable export buttons
        const exportButtons = document.querySelectorAll('#kt_datatable_example_1_export_menu [data-kt-export]');
        exportButtons.forEach(exportButton => {
            exportButton.addEventListener('click', e => {
                e.preventDefault();

                // Get clicked export value
                const exportValue = e.target.getAttribute('data-kt-export');
                const target = document.querySelector('.dt-buttons .buttons-' + exportValue);

                // Trigger click event on hidden datatable export buttons
                if (target) target.click();
            });
        });
    }   


    // Public methods
    return {
        init: function () {
			handleDatatimePicker();		
            initDatatable();
			
            handleSearchDatatable();
            handleFilterDatatable();
            handleResetForm();
            tablecezakayitmh = document.querySelector('#kt_content_durusmalar_hareketler_list');

            if ( !tablecezakayitmh) {
                return;
            } 
            exportButtons();
            //handleResetForm();
			
        },
        reload: function(){
            handleFilterSubmit();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTCezalarMHareketlerServerSide.init();
});




