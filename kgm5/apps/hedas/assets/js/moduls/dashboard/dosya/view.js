"use strict";
var table;
var dt;
var filterForm;
// Class definition
var KTDosyaListServerSide = function () {
    // Shared variables
   
    var filterData = {
        dText: '',
        dTags: '',
        dAralik: '',
        dArsivNo: '',
        dIcraNo: '',
        dKurumDosyaNo: '',
        dDavaci: '',
        dDavali: '',
        dDavaKonusu: '',
        dDavaKonuAciklama: '',
        dMevkiPlaka: '',
        dProje: '',
        dIcra: '',
        dTemyiz: '',
        dIstinafTemyiz: '',
        dIstinafKabul: '',
        dIstinafRed: '',
        dBozmaIlami: '',
        dOnamaIlami: '',
        dKesinlestirme: '',
        dMirascilik: '',
        dIdariAlacagi: '',
        dVekaletAlacagi: '',
        dYargilamaGideri: '',
        dTapuBilgisi: -1,
        dAciklama: '',
        dMahkemeData: {
            dmAcilisTarihi: '',
            dmEsasNo: '',
            dmKararTarihi: '',
            dmKararNo: '',
            dmMahkeme: '',
            dmAciklama: '',
            dmEklemeTarihi: ''
        }
	};
    
    const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
	var calisma_aralik;
    var filter_acilis;
    var filter_karar;
    var tarihPicker = document.querySelector('#kt_table_dosya_datein');
    var acilisPicker = document.querySelector('#filter_acilistarihi');
    var kararPicker = document.querySelector('#filter_karartarihi');

    // Private functions
    var initDatatable = function () {
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX;
		calisma_aralik  = tarihPicker.value;
        filterData.dAralik  = calisma_aralik;

		
        dt = $("#kt_content_dosya_list").DataTable({
            searchDelay: 1000,
            processing: true,
            serverSide: true,
			responsive: true,
			pageLength: 5,
			language: {
                url: baseUrlHost+"/assets/js/moduls/DatatableTurkish.json"
            },
			oLanguage: {
                sInfo : "_START_ ile _END_ arasıda _TOTAL_ kayıt gösteriliyor",// text you want show for info section
                infoEmpty : "Kayıt Bulunamadı!"
                },
			lengthMenu: [
                [5],
                [5],
            ],
            order: [[0, 'desc']],
            //stateSave: true,
            ajax: {
                url: baseUrlHost+"/apps/hedas/dosya/api_listDashboard",
				method: 'POST',
				//dataType: 'json',
				//contentType: 'application/json',
            },
            columns: [
                { data: '_id' },
                { data: '_arsivno' },
                { data: '_icrano' },
                { data: '_kurumdosyano' },
                { data: '_davaci' },
                { data: '_davali' },
                { data: '_davakonu' },
                { data: '_davakonuaciklama' },
				{ data: '_mevkiplaka' },
				{ data: '_proje' },
				{ data: '_icra' },
				{ data: '_temyiz' },
				{ data: '_istinaftemyiz' },
				{ data: '_istinafkabul' },
				{ data: '_istinafred' },
				{ data: '_bozmailami' },
				{ data: '_onamailami' },
				{ data: '_kesinlestirme' },
				{ data: '_mirascilik' },
				{ data: '_idarialacagi' },
				{ data: '_vekaletalacagi' },
				{ data: '_yargilamagideri' },
				{ data: '_tapubilgisi' },
				{ data: '_tags' },
				{ data: '_aciklama' },
				{ data: '_mahkemeler' },
                { data: null },
            ],
            columnDefs: [
				{
					targets: 0,
					data: null,
					orderable: true,
					className: 'text-center',
					render: function (data, type, row) { 
						return row._id;
					}
				},
                {
                    targets: 25,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        var metin = `<div class="table-responsive"><table class="table table-row-dashed table-row-gray-500 gy-5 gs-5 mb-0">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800 text-center">
                                <th scope="col">Esas No</th>
                                <th scope="col">Karar No</th>
                                <th scope="col">Mahkeme</th>
                                <th scope="col">Açılış Tarihi</th>
                                <th scope="col">Karar Tarihi</th>
                                <th scope="col">Açıklamalar</th>
                            </tr>
                        </thead>
                        <tbody>`;
                        //console.log("row", row);
                        $.each( row._mahkemeler, function( key, value ) {
                            metin += `<tr class="fw-semibold fs-5 text-gray-800 border-dashed">
                            <th scope="row">`+value._esasno+`</th>
                            <td>`+value._kararno+`</td>
                            <td>`+value._mahkeme+`</td>
                            <td>`+value._acilistarihi+`</td>
                            <td>`+value._karartarihi+`</td>
                            <td>`+value._maciklama+`</td>
                        </tr>`;
                        });

                        metin += `</tbody></table></div>`;
                        return metin;
                    },
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                         
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-1" data-kt-menu="true">
							<!--begin::Menu item-->
							
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
        }).columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25])
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
 
	var handleDatatimePicker = function(element) {
		$("#kt_table_dosya_datein").daterangepicker({
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
			//console.log("calisma_aralik", calisma_aralik);
		});

   

    }
	
    // Public methods
    return {
        init: function () {

            filterForm = document.querySelectorAll('[data-kt-dosya-table-filter="form"]');

			handleDatatimePicker();		
            initDatatable();
			
            handleSearchDatatable();
            //initToggleToolbar();
            handleFilterDatatable();
           // handleDeleteRows();
            handleResetForm();
			
        },
        reload: function(){
            $("#kt_content_dosya_list").DataTable();
        },
        geriAlModal: function(id){
            var hostUrl = window.location.host;
            const baseUrlHost = "//"+hostUrl+"/apps/hedas/dosya/";
            var modulUrl = window.location.href;
            var postData = {
                id: id
            }

        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDosyaListServerSide.init();
});