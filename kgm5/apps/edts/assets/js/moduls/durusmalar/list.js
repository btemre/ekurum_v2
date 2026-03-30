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
        dMahkemeId: -1,
        dTaraf:'',
        dAciklama:'',
        dTags:'',
        dDurusmaAralik: '',
        dTutanak:-1,
        dTakip:-1,
        dListTur: "ara"
	};

    const filterSearch = document.querySelector('[data-kt-dosc-durusmalar-update-table-filter="search"]');
	var calisma_aralik;
    var tarihPicker = document.querySelector('#kt_table_durusmalar_datein');
	var durusma_aralik;
    var tarihPickerDurusma = document.querySelector('#kt_table_durusmalar2_datein');
    
    // Varsayılan tarih aralıkları (element yoksa kullanılır)
    var defaultCalismaAralik = moment().subtract(6, 'year').startOf('year').format('DD-MM-YYYY') + ' & ' + moment().format('DD-MM-YYYY');
    var defaultDurusmaAralik = moment().subtract(6, 'year').startOf('day').format('DD-MM-YYYY HH:mm') + ' & ' + moment().add(6, 'year').endOf('day').format('DD-MM-YYYY HH:mm');
    // Bugün Olan Duruşmalar (dashboard) için: sadece bugünün tarih aralığı
    var defaultDurusmaAralikBugun = moment().startOf('day').format('DD-MM-YYYY HH:mm') + ' & ' + moment().endOf('day').format('DD-MM-YYYY HH:mm');


    // Private functions
    var initDatatable = function () {
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX;
        var isDashboard = (jQuery("#mainModuleCustom").val() === "dashboard");
		calisma_aralik = (tarihPicker && tarihPicker.value) ? tarihPicker.value : defaultCalismaAralik;
		durusma_aralik = (tarihPickerDurusma && tarihPickerDurusma.value) ? tarihPickerDurusma.value : (isDashboard ? defaultDurusmaAralikBugun : defaultDurusmaAralik);

		filterData.dEklemeTarihi = calisma_aralik;
		filterData.dDurusmaAralik = durusma_aralik;
        filterData.mainModuleCustom =jQuery("#mainModuleCustom").val();
        filterData.subModuleCustom =jQuery("#subModuleCustom").val();
		
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
            select: true,
			pageLength: $("#defaultPageLength").val(),
            loadingRecords: "Kayıtlar yükleniyor.",
			language: {sDecimal:",",sEmptyTable:"Henüz kayıt yok..",sInfo:"_TOTAL_ Kayıt Bulundu",sInfoEmpty:"Kayıt yok",sInfoFiltered:"(_MAX_ Kayıt İçerisinden)",sInfoPostFix:"",sInfoThousands:".",sLengthMenu:"Sayfada _MENU_ kayıt göster",sLoadingRecords:"Yükleniyor...",sProcessing:"İşleniyor...",sSearch:"Ara:",sZeroRecords:"Eşleşen kayıt bulunamadı",oPaginate:{sFirst:"İlk",sLast:"Son",sNext:"Sonraki",sPrevious:"Önceki"},oAria:{sSortAscending:": artan sütun sıralamasını aktifleştir",sSortDescending:": azalan sütun sıralamasını aktifleştir"},select:{rows:{"_":"%d kayıt seçildi","0":"","1":"1 kayıt seçildi"}}},
			oLanguage: {
                sInfo : "_START_ ile _END_ arasıda _TOTAL_ kayıt gösteriliyor",// text you want show for info section
                infoEmpty : "Kayıt Bulunamadı!"
                },
			lengthMenu: [
                [10, 25, 50, 100, 500],
                [10, 25, 50, 100, 500],
            ],
            order: [[13, 'desc']],
            //stateSave: true,
            ajax: {
                url: baseUrlHost+"/apps/edts/durusmalar/api_list",
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
                        return `
                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">İşlemler
                            <span class="svg-icon svg-icon-5 m-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                </svg>
                            </span>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-1" data-kt-menu="true">
                                <div class="menu-item px-1">
                                    <a href="#" class="menu-link px-3" data-id="`+row._id+`" id="edit`+row._id+`" onclick="KTModalUpdateDurusmalarManuel.viewModal('`+row._id+`');">Düzenle</a>
                                </div>
                                <div class="menu-item px-1">
                                    <a href="#" class="menu-link px-3 popup-btn" data-id="`+row._id+`" id="arsivle`+row._id+`" onclick="KTModalUpdateDurusmalarManuel.copeAtModal('`+row._id+`');">Çöpe At</a>
                                </div>
                            </div>`;
                    },
                },
                {
                    targets: 0,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var val = row._esasno || '';
                        var escapedVal = (val + '').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        var textPart = val.length > 20
                            ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="` + escapedVal + `">` + val.slice(0, 20) + `...</span>`
                            : val;
                        var playBtn = val
                            ? `<button type="button" class="btn btn-icon btn-sm btn-light-primary durusma-esasno-play me-1" data-esasno="${escapedVal}" title="Aynı Esas No'daki kayıtları listele" aria-label="Aynı Esas No'ya göre listele"><i class="bi bi-play-circle"></i></button>`
                            : '';
                        var count = row._esasno_count != null ? parseInt(row._esasno_count, 10) : 0;
                        var badge = (val && count > 0)
                            ? ` <span class="badge badge-light-primary">` + count + `</span>`
                            : '';
                        return playBtn + textPart + badge;
                    },
                },
                {
                    targets: 1,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var val = row._mahkeme || '';
                        if(val.length > 20){
                            return `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+val+`">`+val.slice(0,20)+`...</span>`;
                        }
                        return val;
                    },
                },
                {
                    targets: 2,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var val = row._dosyano || '';
                        var escapedVal = (val + '').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        var linkPart = `<a href="#" onclick="KTModalUpdateDurusmalarManuel.viewModal('` + row._id + `');">` + val + `</a>`;
                        var playBtn = val
                            ? `<button type="button" class="btn btn-icon btn-sm btn-light-primary durusma-dosyano-play me-1" data-dosyano="${escapedVal}" title="Aynı Dosya No'daki kayıtları listele" aria-label="Aynı Dosya No'ya göre listele"><i class="bi bi-play-circle"></i></button>`
                            : '';
                        var count = row._dosyano_count != null ? parseInt(row._dosyano_count, 10) : 0;
                        var badge = (val && count > 0)
                            ? ` <span class="badge badge-light-primary">` + count + `</span>`
                            : '';
                        return playBtn + linkPart + badge;
                    },
                },
                {
                    targets: 3,
                    data: null,
                    orderable: true,
                    className: 'text-center text-nowrap',
                    render: function (data, type, row) {
                        return row._durusmatarihi || '';
                    },
                },
                {
                    targets: 4,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var val = row._avukat || '';
                        if(val.length > 20){
                            return `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+val+`">`+val.slice(0,20)+`...</span>`;
                        }
                        return val;
                    },
                },
                {
                    targets: 5,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var val = row._memur || '';
                        if(val.length > 20){
                            return `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+val+`">`+val.slice(0,20)+`...</span>`;
                        }
                        return val;
                    },
                },
                {
                    targets: 6,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var val = row._dosyaturu || '';
                        if(val.length > 20){
                            return `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+val+`">`+val.slice(0,20)+`...</span>`;
                        }
                        return val;
                    },
                },
                {
                    targets: 7,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var val = row._taraf || '';
                        if(val.length > 20){
                            return `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+val+`">`+val.slice(0,20)+`...</span>`;
                        }
                        return val;
                    },
                },
                {
                    targets: 8,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var val = row._islem || '';
                        if(val.length > 20){
                            return `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+val+`">`+val.slice(0,20)+`...</span>`;
                        }
                        return val;
                    },
                },
                {
                    targets: 9,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var val = row._tarafbilgisi || '';
                        if(val.length > 20){
                            return `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+val+`">`+val.slice(0,20)+`...</span>`;
                        }
                        return val;
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
        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dtdurusmalarw.on('draw', function () {
            KTMenu.createInstances();
            if (!columnVisibilityInitialized) {
                columnVisibilityInitialized = true;
                setTimeout(handleDurusmalarColumnVisibility, 100);
            }
        });

        // Ekrana sığmayan sütunları sağdan sola gizle (İşlemler sütunu her zaman görünür)
        var resizeTimeout;
        $(window).on('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleDurusmalarColumnVisibility, 150);
        });

        // Esas No play butonu: aynı Esas No'ya sahip kayıtları listele
        $(document).off('click.durusmaEsasNoPlay').on('click.durusmaEsasNoPlay', '#durusmalar_content_list .durusma-esasno-play', function (e) {
            e.preventDefault();
            var esasNo = $(this).data('esasno') || $(this).attr('data-esasno') || '';
            var formsearch = document.querySelector('#kt_modal_list_dosya_filter_form');
            if (formsearch) {
                var inp = formsearch.querySelector('[name="dlara_esasno"]');
                if (inp) inp.value = esasNo;
            }
            handleFilterSubmit();
        });

        // Dosya No play butonu: aynı Dosya No'ya sahip kayıtları listele
        $(document).off('click.durusmaDosyanoPlay').on('click.durusmaDosyanoPlay', '#durusmalar_content_list .durusma-dosyano-play', function (e) {
            e.preventDefault();
            var dosyano = $(this).data('dosyano') || $(this).attr('data-dosyano') || '';
            var formsearch = document.querySelector('#kt_modal_list_dosya_filter_form');
            if (formsearch) {
                var inp = formsearch.querySelector('[name="dlara_dosyano"]');
                if (inp) inp.value = dosyano;
            }
            handleFilterSubmit();
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
		var filterMemur 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMemur"]');
		var filterAvukat	= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterAvukat"]');
        var filterMahkeme	= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMahkeme"]');

        if (!filterSearch) return;
        filterSearch.addEventListener('keyup', function (e) {

            filterData.dEklemeTarihi = calisma_aralik;
            filterData.dDurusmaAralik = durusma_aralik;
            filterData.dText = e.target.value;

     
            filterData.dMemurId=$("#filterMemurSelect").val().toString();
            filterData.dAvukatId=$("#filterAvukatSelect").val().toString();
            filterData.dMahkemeId=$("#filterMahkemeSelect").val().toString();
            filterData.dIslem=$("#filterIslemSelect").val().toString();
           

            filterData.dListTur = "ara";
            //dt.search(e.target.value).draw();
            handleFilterSubmit();
        });
		
    }

    var handleSearchButtonDatatable = function () {
        const searchButton = document.getElementById('kt_modal_durusmalar_list_ara_submit');
        if (!searchButton) return;
        searchButton.addEventListener('click', function (e) {
            e.preventDefault();
            filterData.dListTur = "ara";
            handleFilterSubmit();
        });
		
    }

    
    var handleResetForm = () => {
        // Select reset button
        /*
        const resetButton = document.querySelector('[data-kt-dosc-durusmalar-update-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Reset payment type

            $('#filterMemurSelect').val('-1').trigger('change'); // Select the option with a value of '1'
            $('#filterAvukatSelect').val('-1').trigger('change'); // Select the option with a value of '1'
            $('#filterMahkemeSelect').val('-1').trigger('change'); // Select the option with a value of '1'

            filterData.dAvukatId  = -1;
            filterData.dMahkemeId  = -1;
            filterData.dMemurId  = -1;
            calisma_aralik = tarihPicker.value;
            filterData.dEklemeTarihi = calisma_aralik;
            durusma_aralik = tarihPickerDurusma.value;
            filterData.dDurusmaAralik = durusma_aralik;

            handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
        */
    }
    
    // Filter Datatabled
    var handleFilterDatatable = () => {
        // Select filter options
        var filterMemur 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMemur"]');
        var filterAvukat 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterAvukat"]');
        var filterMahkeme 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMahkeme"]');

        
        jQuery( ".filterComboList" ).on( "change", function() {

                
			console.log("filter changed:");


            filterData.dMemurId=$("#filterMemurSelect").val().toString();
            filterData.dAvukatId=$("#filterAvukatSelect").val().toString();
            filterData.dMahkemeId=$("#filterMahkemeSelect").val().toString();
            filterData.dIslem=$("#filterIslemSelect").val().toString();
            
            
            var isDashboard = (jQuery("#mainModuleCustom").val() === "dashboard");
            calisma_aralik = (tarihPicker && tarihPicker.value) ? tarihPicker.value : defaultCalismaAralik;
            filterData.dEklemeTarihi = calisma_aralik;
            durusma_aralik = (tarihPickerDurusma && tarihPickerDurusma.value) ? tarihPickerDurusma.value : (isDashboard ? defaultDurusmaAralikBugun : defaultDurusmaAralik);
            filterData.dDurusmaAralik = durusma_aralik;
            filterData.dListTur = "filter";
			handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
    }
	
	var handleFilterSubmit = () => {

        var filterMemur 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMemur"]');
        var filterAvukat	= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterAvukat"]');
        var filterMahkeme	= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMahkeme"]');
        var filterIslem	= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterIslem"]');

        var formsearch = document.querySelector('#kt_modal_list_dosya_filter_form');
        if (!formsearch) return;
    
        var srcSearchText = formsearch.querySelector('[name="searchText"]');
        // var srcIslem = formsearch.querySelector('[name="dlara_islem"]');
        var srcEsasno = formsearch.querySelector('[name="dlara_esasno"]');
        var srcDosyano = formsearch.querySelector('[name="dlara_dosyano"]');
        var srcTarafBilgisi = formsearch.querySelector('[name="dlara_tarafbilgisi"]');
        var srcTaraf = $('#dlara_taraf');
        var srcDtakip = $('#dlara_dtakip');
        var srcDtutanak = document.querySelector( 'input[name="dlara_dtutanak"]:checked');

        filterData.dEklemeTarihi = calisma_aralik;
        filterData.dDurusmaAralik = durusma_aralik;

        filterData.dText = srcSearchText ? srcSearchText.value : '';
        // filterData.dIslem = srcIslem.value;
        filterData.dEsasNo = srcEsasno ? srcEsasno.value : '';
        filterData.dDosyaNo = srcDosyano ? srcDosyano.value : '';
        filterData.dTarafBilgisi = srcTarafBilgisi ? srcTarafBilgisi.value : '';
        filterData.dTutanak = srcDtutanak ? srcDtutanak.value : -1;
        filterData.dTaraf = srcTaraf.val();
        filterData.dTakip = srcDtakip.val();
        

     
        dtdurusmalarw
        .columns([0,1,2,3,4,5,6,7,8,9,10,11,12])
        .flatten()
        .search(JSON.stringify(filterData))
        .draw();		
	}

	var handleDatatimePicker = function(element) {
		if (!tarihPicker || !tarihPickerDurusma) return;
        
		$("#kt_table_durusmalar_datein").daterangepicker({
			opens: 'left',
			showDropdowns: true,
			// minYear: parseInt(moment().subtract(6, 'year').format("YYYY"), 10),
			// maxYear: parseInt(moment().subtract(-6, 'year').format("YYYY"), 10),
			startDate: moment().subtract(6, 'year').startOf('year').format("DD-MM-YYYY"),//moment().subtract(29, 'days').format("DD-MM-YYYY"),
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

        var datecheckEl = document.getElementById("kt_table_durusmalar2_datecheck");
        if (datecheckEl && datecheckEl.value!="") {
            var startDtx=moment().startOf('day').format("DD-MM-YYYY HH:mm");
            var endDtx=moment().endOf('day').format("DD-MM-YYYY HH:mm");
        }
        else {
            var startDtx=moment().subtract(6, 'year').startOf('day').format("DD-MM-YYYY HH:mm");
            var endDtx=moment().add(6, 'year').endOf('day').format("DD-MM-YYYY HH:mm");

        }

		$("#kt_table_durusmalar2_datein").daterangepicker({
			opens: 'left',
			showDropdowns: true,
            timePicker: true,
            timePicker24Hour: true,
			// minYear: parseInt(moment().subtract(6, 'year').format("YYYY"), 10),
			// maxYear: parseInt(moment().subtract(-6, 'year').format("YYYY"), 10),
			startDate: startDtx,
			endDate: endDtx,
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
        const documentTitle = 'Durusmalar-'+(durusma_aralik || defaultDurusmaAralik);
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
        const exportButtons = document.querySelectorAll('#kt_datatable_durusmalar_export_menu [data-kt-export]');
        if (exportButtons.length === 0) return;
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
    };

    var getExportFilters = function () {
        var formsearch = document.querySelector('#kt_modal_list_dosya_filter_form');
        if (!formsearch) return filterData;
        var srcSearchText = formsearch.querySelector('[name="searchText"]');
        var srcEsasno = formsearch.querySelector('[name="dlara_esasno"]');
        var srcDosyano = formsearch.querySelector('[name="dlara_dosyano"]');
        var srcTarafBilgisi = formsearch.querySelector('[name="dlara_tarafbilgisi"]');
        var srcTaraf = $('#dlara_taraf');
        var srcDtakip = $('#dlara_dtakip');
        var srcDtutanak = document.querySelector('input[name="dlara_dtutanak"]:checked');
        var f = {
            dEklemeTarihi: calisma_aralik,
            dDurusmaAralik: durusma_aralik,
            dText: srcSearchText ? srcSearchText.value : '',
            dEsasNo: srcEsasno ? srcEsasno.value : '',
            dDosyaNo: srcDosyano ? srcDosyano.value : '',
            dTarafBilgisi: srcTarafBilgisi ? srcTarafBilgisi.value : '',
            dTutanak: srcDtutanak ? srcDtutanak.value : -1,
            dTaraf: srcTaraf && srcTaraf.length ? srcTaraf.val() : -1,
            dTakip: srcDtakip && srcDtakip.length ? srcDtakip.val() : -1,
            dListTur: filterData.dListTur || 'filter',
            mainModuleCustom: jQuery("#mainModuleCustom").val(),
            subModuleCustom: jQuery("#subModuleCustom").val(),
            dMemurId: $("#filterMemurSelect").length ? $("#filterMemurSelect").val() : -1,
            dAvukatId: $("#filterAvukatSelect").length ? $("#filterAvukatSelect").val() : -1,
            dMahkemeId: $("#filterMahkemeSelect").length ? $("#filterMahkemeSelect").val() : -1,
            dIslem: $("#filterIslemSelect").length ? $("#filterIslemSelect").val() : -1
        };
        if (typeof f.dMemurId === 'object') f.dMemurId = f.dMemurId && f.dMemurId.length ? f.dMemurId.join(',') : -1;
        if (typeof f.dAvukatId === 'object') f.dAvukatId = f.dAvukatId && f.dAvukatId.length ? f.dAvukatId.join(',') : -1;
        if (typeof f.dMahkemeId === 'object') f.dMahkemeId = f.dMahkemeId && f.dMahkemeId.length ? f.dMahkemeId.join(',') : -1;
        if (typeof f.dIslem === 'object') f.dIslem = f.dIslem && f.dIslem.length ? f.dIslem.join(',') : -1;
        return f;
    };

    var pendingExportType = null;
    var pendingExportFormat = null;

    function doExportRequest(exportType, format, filters) {
        var hostX = window.location.host;
        var baseUrlHost = '//' + hostX;
        var exportUrl = baseUrlHost + '/apps/edts/durusmalar/api_list_export';
        var body = 'export_type=' + encodeURIComponent(exportType) + '&format=' + encodeURIComponent(format) + '&filters=' + encodeURIComponent(JSON.stringify(filters));
        var isPdfOrPrint = (format === 'pdf' || format === 'print');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', exportUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = isPdfOrPrint ? 'text' : 'blob';
        xhr.onload = function () {
            if (xhr.status !== 200) {
                try {
                    var r = JSON.parse(xhr.responseText);
                    if (r.description) alert(r.description);
                } catch (err) {
                    alert('Dışa aktarma başarısız.');
                }
                return;
            }
            if (isPdfOrPrint) {
                var win = window.open('', '_blank');
                if (!win) {
                    alert('Açılır pencere engellendi. Lütfen tarayıcıda açılır pencerelere izin verin.');
                    return;
                }
                win.document.write(xhr.responseText);
                win.document.close();
                win.focus();
                setTimeout(function () { win.print(); }, 250);
                return;
            }
            var blob = xhr.response;
            var disp = xhr.getResponseHeader('Content-Disposition');
            var filename = 'durusmalar_export.' + (format === 'excel' ? 'xlsx' : 'csv');
            if (disp && disp.indexOf('filename=') !== -1) {
                var m = disp.match(/filename="?([^";]+)"?/);
                if (m && m[1]) filename = m[1];
            }
            var a = document.createElement('a');
            a.href = window.URL.createObjectURL(blob);
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(a.href);
        };
        xhr.onerror = function () { alert('Dışa aktarma isteği gönderilemedi.'); };
        xhr.send(body);
    }

    function yyyyMmDdToDdMmYyyy(str) {
        if (!str || str.length < 10) return str;
        var parts = str.substring(0, 10).split('-');
        if (parts.length !== 3) return str;
        return parts[2] + '-' + parts[1] + '-' + parts[0];
    }

    var handleExportClick = function () {
        var links = document.querySelectorAll('.durusmalar-export-link');
        if (!links.length) return;
        var modal = document.getElementById('durusmalar_export_modal');
        var modalSubmit = document.getElementById('durusmalar_export_modal_submit');
        var dateStart = document.getElementById('durusmalar_export_date_start');
        var dateEnd = document.getElementById('durusmalar_export_date_end');

        links.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var exportType = link.getAttribute('data-export-type');
                var format = link.getAttribute('data-export-format');
                if (!exportType || !format) return;
                pendingExportType = exportType;
                pendingExportFormat = format;
                if (dateStart && dateEnd) {
                    var durusmaInput = document.getElementById('kt_table_durusmalar2_datein');
                    if (durusmaInput && durusmaInput.value && durusmaInput.value.indexOf(' & ') !== -1) {
                        var parts = durusmaInput.value.split(' & ');
                        if (parts.length >= 2) {
                            var p0 = parts[0].trim().split(/[\s-/:]+/);
                            var p1 = parts[1].trim().split(/[\s-/:]+/);
                            if (p0.length >= 3 && p1.length >= 3) {
                                dateStart.value = (p0[2] || '') + '-' + (p0[1] || '') + '-' + (p0[0] || '');
                                dateEnd.value = (p1[2] || '') + '-' + (p1[1] || '') + '-' + (p1[0] || '');
                            }
                        }
                    } else {
                        dateStart.value = '';
                        dateEnd.value = '';
                    }
                }
                if (modal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var inst = bootstrap.Modal.getOrCreateInstance(modal);
                    inst.show();
                }
            });
        });

        if (modalSubmit && modal) {
            modalSubmit.addEventListener('click', function () {
                if (pendingExportType == null || pendingExportFormat == null) return;
                var filters = pendingExportType === 'full' ? {} : getExportFilters();
                if (pendingExportType === 'filtered' && dateStart && dateEnd && dateStart.value && dateEnd.value) {
                    var startStr = yyyyMmDdToDdMmYyyy(dateStart.value) + ' 00:00';
                    var endStr = yyyyMmDdToDdMmYyyy(dateEnd.value) + ' 23:59';
                    filters.dDurusmaAralik = startStr + ' & ' + endStr;
                }
                doExportRequest(pendingExportType, pendingExportFormat, filters);
                var inst = bootstrap.Modal.getInstance(modal);
                if (inst) inst.hide();
                pendingExportType = null;
                pendingExportFormat = null;
            });
        }
    }   


    // Public methods
    return {
        init: function () {
            handleDatatimePicker();		
            initDatatable();
            var secondSearchBtn = document.getElementById("secondSearchButton");
            if (secondSearchBtn) {
                jQuery("#secondSearchButton").on('click', function () {
                    $(".filterComboList").eq(0).trigger("change");
                });
            }
            jQuery("#resetSearchButton").on('click', function () {
                document.location.reload();
            });

            var triggerSearchOnEnter = function () {
                var btn = document.getElementById('secondSearchButton');
                if (!btn) return;
                ['dlara_esasno', 'dlara_dosyano'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el) el.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter' || e.keyCode === 13) { e.preventDefault(); btn.click(); }
                    });
                });
                var tarafBilgisi = document.querySelector('input[name="dlara_tarafbilgisi"]');
                if (tarafBilgisi) tarafBilgisi.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.keyCode === 13) { e.preventDefault(); btn.click(); }
                });
            };
            triggerSearchOnEnter();

              			
            handleSearchDatatable();
            handleFilterDatatable();
            handleSearchButtonDatatable();
            handleResetForm();
            handleExportClick();
            tabledurusmalarw = document.querySelector('#kt_content_durusmalar_list');

            if ( !tabledurusmalarw) {
                return;
            }
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


jQuery(function() {
		
 

});
