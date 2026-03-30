"use strict";
var tabledurusmalarw;
var dtdurusmalarw;
// Class definition
var KTDurusmalarListServerSide = function () {
    // Shared variables
    
    var filterData = {
        dEklemeTarihi: '',
        dText: '',
		dDosyaNo: '',
        dDosyaTuru: '',
		dMahkeme: '',
		dDurusmaTarihi: '',
		dEsasNo: '',
		dTarafBilgisi: '',
		dIslem: '',
		dMemur: '',
        dMemurId: -1,
        dAvukat:'',
        dAvukatId: -1,
        dTaraf:'',
        dAciklama:'',
        dTags:'',
        dDurusmaAralik: '',
        dTutanak:-1,
        dTakip:-1
	};

    const filterSearch = document.querySelector('[data-kt-dosc-durusmalar-update-table-filter="search"]');
	var calisma_aralik;
    var tarihPicker = document.querySelector('#kt_table_durusmalar_datein');
	var durusma_aralik;
    var tarihPickerDurusma = document.querySelector('#kt_table_durusmalar2_datein');

    var defaultCalismaAralik = moment().subtract(6, 'year').startOf('year').format('DD-MM-YYYY') + ' & ' + moment().format('DD-MM-YYYY');
    var defaultDurusmaAralik = moment().subtract(6, 'year').startOf('day').format('DD-MM-YYYY HH:mm') + ' & ' + moment().add(6, 'year').endOf('day').format('DD-MM-YYYY HH:mm');

    // Private functions
    var initDatatable = function () {
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX;
		calisma_aralik = (tarihPicker && tarihPicker.value) ? tarihPicker.value : defaultCalismaAralik;
		durusma_aralik = (tarihPickerDurusma && tarihPickerDurusma.value) ? tarihPickerDurusma.value : defaultDurusmaAralik;

		filterData.dEklemeTarihi = calisma_aralik;
		filterData.dDurusmaAralik = durusma_aralik;
		
	/*	$('#kt_content_durusmalar_list thead tr')
				.clone(true)
				.addClass('filters')
				.appendTo('#kt_content_durusmalar_list thead');		
		*/
		
    
        dtdurusmalarw = $("#kt_content_durusmalar_list").DataTable({
            searchDelay: 1000,
            processing: true,
            serverSide: true,
            deferRender: true,
			pageLength: 25,
            loadingRecords: "Kayıtlar yükleniyor.",
			language: {sDecimal:",",sEmptyTable:"Henüz kayıt yok..",sInfo:"_TOTAL_ Kayıt Bulundu",sInfoEmpty:"Kayıt yok",sInfoFiltered:"(_MAX_ Kayıt İçerisinden)",sInfoPostFix:"",sInfoThousands:".",sLengthMenu:"Sayfada _MENU_ kayıt göster",sLoadingRecords:"Yükleniyor...",sProcessing:"İşleniyor...",sSearch:"Ara:",sZeroRecords:"Eşleşen kayıt bulunamadı",oPaginate:{sFirst:"İlk",sLast:"Son",sNext:"Sonraki",sPrevious:"Önceki"},oAria:{sSortAscending:": artan sütun sıralamasını aktifleştir",sSortDescending:": azalan sütun sıralamasını aktifleştir"},select:{rows:{"_":"%d kayıt seçildi","0":"","1":"1 kayıt seçildi"}}},
			oLanguage: {
                sInfo : "_START_ ile _END_ arasıda _TOTAL_ kayıt gösteriliyor",// text you want show for info section
                infoEmpty : "Kayıt Bulunamadı!"
                },
			lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100],
            ],
            order: [[13, 'desc']],
            //stateSave: true,
            ajax: {
                url: baseUrlHost+"/apps/edts/durusmalar/api_mylist",
				method: 'POST',
				//dataType: 'json',
				//contentType: 'application/json',
            },
            columns: [
                { data: '_esasno' },
                { data: '_mahkeme' },
                { data: '_dosyano' },
                { data: '_durusmatarihi' },
				{ data: '_avukat' },
				{ data: '_memur' },
                { data: '_dosyaturu' },
                { data: '_taraf' },
				{ data: '_islem' },
                { data: '_tarafbilgisi' },
                { data: '_takip' },
                { data: '_tutanak' },
                { data: '_tags' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        var menuHtml = "";
                        //if(row._onay==0){
                            menuHtml = `
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
                                    <a href="#" class="menu-link px-3" data-id="`+row._id+`" id="edit`+row._id+`" onclick="KTModalUpdateDurusmalarManuel.viewModal('`+row._id+`');">Düzenle</a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-1">
                                    <a href="#" class="menu-link px-3 popup-btn" data-id="`+row._id+`" id="arsivle`+row._id+`" onclick="KTModalUpdateDurusmalarManuel.copeAtModal('`+row._id+`');">Çöpe At
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu-->
                            `;
                       // }
                        return menuHtml;
                    },
                },
                {
                    targets: 2,
                    data: null,
                    orderable: false,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var menuHtml = "";
                        //if(row._onay==0){
                            menuHtml = `
                            <a href="#" onclick="KTModalUpdateDurusmalarManuel.viewModal('`+row._id+`');">`+row._dosyano+`</a>
                            `;
                       // }
                        return menuHtml;
                    },
                },

            ],
            // Add data-filter attribute
			/*
            createdRow: function (row, data, dataIndex) {
                $(row).find('td:eq(1)').attr('data-filter', data._tarih);
            }
			*/
        }).columns([0,1,2,3,4,5,6,7,8,9,10,11,12])
        .flatten()
        .search(JSON.stringify(filterData));
        // console.log(dtdurusmalarw);

        // const tableRows = tabledurusmalarw.querySelectorAll('tbody tr');

        // tableRows.forEach(row => {
        //     const dateRow = row.querySelectorAll('td');
        //     const realDate = moment(dateRow[3].innerHTML, "DD MMM YYYY, LT").format(); // select date from 4th column in table
        //     dateRow[3].setAttribute('data-order', realDate);
        // });

        tabledurusmalarw = dtdurusmalarw.$;

        var columnVisibilityInitialized = false;
        dtdurusmalarw.on('draw', function () {
            KTMenu.createInstances();
            if (!columnVisibilityInitialized) {
                columnVisibilityInitialized = true;
                setTimeout(handleDurusmalarColumnVisibility, 100);
            }
        });

        var resizeTimeout;
        $(window).on('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleDurusmalarColumnVisibility, 150);
        });
    }

    var handleDurusmalarColumnVisibility = function () {
        if (!dtdurusmalarw || dtdurusmalarw.columns === undefined) return;
        var container = document.getElementById('durusmalar_content_list');
        if (!container) return;
        var containerWidth = container.clientWidth;
        if (containerWidth <= 0) return;
        var hideable = [12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0];
        var totalCols = dtdurusmalarw.columns().header().length;
        var avgColWidth = Math.max(containerWidth / totalCols, 80);
        var visibleCount = Math.max(Math.floor(containerWidth / avgColWidth) - 1, 3);
        var hideCount = Math.max(totalCols - 1 - visibleCount, 0);
        var i;
        for (i = 0; i < hideable.length; i++) {
            dtdurusmalarw.column(hideable[i]).visible(i >= hideCount, false);
        }
        dtdurusmalarw.columns.adjust();
    };

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
		// var filterMemur 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMemur"]');
		var filterAvukat	= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterAvukat"]');
        if (!filterSearch) return;
        filterSearch.addEventListener('keyup', function (e) {

            filterData.dEklemeTarihi = calisma_aralik;
            filterData.dDurusmaAralik = durusma_aralik;
            filterData.dText = e.target.value;

            // Get filter values
            // filterMemur.forEach((item, index) => {
            //     if (item.innerText.indexOf('selected') && item.value !== '') {
            //         // Build filter value options
            //         filterData.dMemurId  = item.value;
            //         //console.log("TUR_>",item.value, item.innerText);
            //     }else{
            //         filterData.dMemurId = -1;
            //     }
            // });
            // Get filter values
            filterAvukat.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.dAvukatId  = item.value;
                    //console.log("KATEGORI>",item.value, item.innerText);
                }else{
                    filterData.dAvukatId = -1;
                }
            });
            
            //dt.search(e.target.value).draw();
            handleFilterSubmit();
        });
		
    }

    var handleSearchButtonDatatable = function () {
        const searchButton = document.getElementById('kt_modal_durusmalar_list_ara_submit');
        if (!searchButton) return;
        searchButton.addEventListener('click', function (e) {
            e.preventDefault();
            
            handleFilterSubmit();
        });
		
    }

    
    var handleResetForm = () => {
        // Select reset button
        const resetButton = document.querySelector('[data-kt-dosc-durusmalar-update-table-filter="reset"]');
        if (!resetButton) return;
        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Reset payment type

            $('#filterMemurSelect').val('-1').trigger('change'); // Select the option with a value of '1'
            $('#filterAvukatSelect').val('-1').trigger('change'); // Select the option with a value of '1'

            filterData.dAvukatId  = -1;
            filterData.dMemurId  = -1;
            calisma_aralik = (tarihPicker && tarihPicker.value) ? tarihPicker.value : defaultCalismaAralik;
            filterData.dEklemeTarihi = calisma_aralik;
            durusma_aralik = (tarihPickerDurusma && tarihPickerDurusma.value) ? tarihPickerDurusma.value : defaultDurusmaAralik;
            filterData.dDurusmaAralik = durusma_aralik;

            handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
    }
    
    // Filter Datatabled
    var handleFilterDatatable = () => {
        // Select filter options
        // var filterMemur 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMemur"]');
        var filterAvukat 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterAvukat"]');
        const filterButton = document.querySelector('[data-kt-dosc-durusmalar-update-table-filter="filter"]');
        if (!filterButton) return;
        // Filter datatable on submit
        filterButton.addEventListener('click', function () {
			
             // Get filter values
            // filterMemur.forEach((item, index) => {
            //     if (item.innerText.indexOf('selected') && item.value !== '') {
            //         // Build filter value options
            //         filterData.dMemurId  = item.value;
            //         //console.log("TUR_>",item.value, item.innerText);
            //     }else{
            //         filterData.dMemurId  = -1;
            //     }
            // });
            // Get filter values
            filterAvukat.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.dAvukatId  = item.value;
                    //console.log("TUR_>",item.value, item.innerText);
                }else{
                    filterData.dAvukatId  = -1;
                }
            });
            
            
            calisma_aralik = (tarihPicker && tarihPicker.value) ? tarihPicker.value : defaultCalismaAralik;
            filterData.dEklemeTarihi = calisma_aralik;
            durusma_aralik = (tarihPickerDurusma && tarihPickerDurusma.value) ? tarihPickerDurusma.value : defaultDurusmaAralik;
            filterData.dDurusmaAralik = durusma_aralik;

			handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
    }
	
	var handleFilterSubmit = () => {

        // var filterMemur 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMemur"]');
        var filterAvukat	= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterAvukat"]');
        var filterIslem	= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterIslem"]');

        var formsearch = document.querySelector('#kt_modal_list_dosya_filter_form');
        if (!formsearch) return;
    
        var srcSearchText = formsearch.querySelector('[name="searchText"]');
        // var srcIslem = formsearch.querySelector('[name="dlara_islem"]');
        var srcEsasno = formsearch.querySelector('[name="dlara_esasno"]');
        var srcTarafBilgisi = formsearch.querySelector('[name="dlara_tarafbilgisi"]');
        var srcTaraf = $('#dlara_taraf');
        var srcDtakip = $('#dlara_dtakip');
        var srcDtutanak = document.querySelector( 'input[name="dlara_dtutanak"]:checked');

        filterData.dEklemeTarihi = calisma_aralik;
        filterData.dDurusmaAralik = durusma_aralik;

        filterData.dText = srcSearchText ? srcSearchText.value : '';
        // filterData.dIslem = srcIslem.value;
        filterData.dEsasNo = srcEsasno ? srcEsasno.value : '';
        filterData.dTarafBilgisi = srcTarafBilgisi ? srcTarafBilgisi.value : '';
        filterData.dTutanak = srcDtutanak ? srcDtutanak.value : -1;
        filterData.dTaraf = srcTaraf.val();
        filterData.dTakip = srcDtakip.val();
        

        // Get filter values
        // filterMemur.forEach((item, index) => {
        //     if (item.innerText.indexOf('selected') && item.value !== '') {
        //         // Build filter value options
        //         filterData.dMemurId  = item.value;
        //         //console.log("TUR_>",item.value, item.innerText);
        //     }else{
        //         filterData.dMemurId = -1;
        //     }
        // });
        // Get filter values
        filterAvukat.forEach((item, index) => {
            if (item.innerText.indexOf('selected') && item.value !== '') {
                // Build filter value options
                filterData.dAvukatId  = item.value;
                //console.log("KATEGORI>",item.value, item.innerText);
            }else{
                filterData.dAvukatId = -1;
            }
        });
        filterIslem.forEach((item, index) => {
            if (item.innerText.indexOf('selected') && item.value !== '') {
                // Build filter value options
                filterData.dIslem  = item.value;
                //console.log("KATEGORI>",item.value, item.innerText);
            }else{
                filterData.dIslem = -1;
            }
        });

        dtdurusmalarw
        .columns([0,1,2,3,4,5,6,7,8,9,10,11,12])
        .flatten()
        .search(JSON.stringify(filterData))
        .draw();		
	}

	var handleDatatimePicker = function(element) {

		$("#kt_table_durusmalar_datein").daterangepicker({
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



		$("#kt_table_durusmalar2_datein").daterangepicker({
			opens: 'left',
			showDropdowns: true,
            timePicker: true,
            timePicker24Hour: true,
			minYear: parseInt(moment().subtract(10, 'year').format("YYYY"), 10),
			maxYear: parseInt(moment().subtract(-3, 'year').format("YYYY"), 10),
			startDate: moment().subtract(5, 'month').startOf('day').format("DD-MM-YYYY HH:mm"),//moment().subtract(29, 'days').format("DD-MM-YYYY"),
			endDate: moment().add(5, 'month').endOf('day').format("DD-MM-YYYY HH:mm"),//moment().subtract(0, 'month').endOf('month'),//
			ranges: {
                'Bugün': [moment().startOf('day'), moment().endOf('day')],
                'Dün': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
                'Gelecek Ay': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
                'Gelecek 7Gün': [moment().startOf('day'), moment().add(6, 'days').endOf('day')],
                'Gelecek 15Gün': [moment().startOf('day'), moment().add(14, 'days').endOf('day')],
                'Gelecek 6Ay': [moment().startOf('day'), moment().add(5, 'month').endOf('day')]                
			},
			locale: {
				applyLabel: 'Aralığı Seç',
				cancelLabel: 'Vazgeç',
				format: 'DD-MM-YYYY HH:mm',
				customRangeLabel: 'Diğer',
				separator: ' & ',
				fromLabel: 'From',
				toLabel: '&',
				weekLabel: 'W',
				daysOfWeek: ['Pzr', 'Pts', 'Sal', 'Çar', 'Per', 'Cum', 'Cts'],
				monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
				firstDay: 1
			}
		}, function(start, end, label) {
			durusma_aralik = start.format('DD-MM-YYYY HH:mm') + ' & ' + end.format('DD-MM-YYYY HH:mm');
			//console.log("calisma_aralik", calisma_aralikg);
        });
        
	}
	
    var exportButtons = () => {
        if (!dtdurusmalarw) return;
        const documentTitle = 'Durusmalar-'+durusma_aralik;
        var buttons = new $.fn.dataTable.Buttons(dtdurusmalarw, {
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
        }).container().appendTo($('#kt_datatable_durusmalar_export'));

        // Hook dropdown menu click event to datatable export buttons
        const exportButtonsEl = document.querySelectorAll('#kt_datatable_durusmalar_export_menu [data-kt-export]');
        exportButtonsEl.forEach(exportButton => {
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
    var attachEnterSearchInAdvanced = function () {
        var btn = document.getElementById('kt_modal_durusmalar_list_ara_submit');
        if (!btn) return;
        var esasnoEl = document.getElementById('dlara_esasno');
        if (esasnoEl) esasnoEl.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) { e.preventDefault(); btn.click(); }
        });
        var dosyanoEl = document.getElementById('dlara_dosyano');
        if (dosyanoEl) dosyanoEl.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) { e.preventDefault(); btn.click(); }
        });
        var tarafBilgisi = document.querySelector('input[name="dlara_tarafbilgisi"]');
        if (tarafBilgisi) tarafBilgisi.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) { e.preventDefault(); btn.click(); }
        });
    };

    return {
        init: function () {
			handleDatatimePicker();		
            initDatatable();
			
            handleSearchDatatable();
            handleFilterDatatable();
            handleSearchButtonDatatable();
            handleResetForm();
            attachEnterSearchInAdvanced();
            tabledurusmalarw = document.querySelector('#kt_content_durusmalar_list');

            if ( !tabledurusmalarw) {
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
    KTDurusmalarListServerSide.init();
});