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
        var baseUrlHost = (typeof window.EDTS_BASE_URL !== 'undefined' && window.EDTS_BASE_URL) ? window.EDTS_BASE_URL : ("//" + hostX + "/apps/edts");
        var apiUrl = baseUrlHost + "/durusmalar/api_listDashboard";
        console.log('[EDTS Dashboard] DataTable init – API URL:', apiUrl, '| EDTS_BASE_URL:', typeof window.EDTS_BASE_URL !== 'undefined' ? window.EDTS_BASE_URL : 'yok');

		calisma_aralik = (tarihPicker && tarihPicker.value) ? tarihPicker.value : defaultCalismaAralik;
		durusma_aralik = (tarihPickerDurusma && tarihPickerDurusma.value) ? tarihPickerDurusma.value : defaultDurusmaAralik;

		filterData.dEklemeTarihi = calisma_aralik;
		filterData.dDurusmaAralik = durusma_aralik;

		var filterSearchValue = JSON.stringify(filterData);
        console.log('[EDTS Dashboard] Filtre (ilk 200 karakter):', filterSearchValue.substring(0, 200));
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
			paging: false,
			pageLength: 9999,
            loadingRecords: "Kayıtlar yükleniyor.",
			language: {sDecimal:",",sEmptyTable:"Henüz kayıt yok..",sInfo:"_TOTAL_ Kayıt Bulundu",sInfoEmpty:"Kayıt yok",sInfoFiltered:"(_MAX_ Kayıt İçerisinden)",sInfoPostFix:"",sInfoThousands:".",sLengthMenu:"Sayfada _MENU_ kayıt göster",sLoadingRecords:"Yükleniyor...",sProcessing:"İşleniyor...",sSearch:"Ara:",sZeroRecords:"Eşleşen kayıt bulunamadı",oPaginate:{sFirst:"İlk",sLast:"Son",sNext:"Sonraki",sPrevious:"Önceki"},oAria:{sSortAscending:": artan sütun sıralamasını aktifleştir",sSortDescending:": azalan sütun sıralamasını aktifleştir"},select:{rows:{"_":"%d kayıt seçildi","0":"","1":"1 kayıt seçildi"}}},
			oLanguage: {
                sInfo : "_START_ ile _END_ arasıda _TOTAL_ kayıt gösteriliyor",
                infoEmpty : "Kayıt Bulunamadı!"
                },
			lengthMenu: [],
            order: [[3, 'asc']],
            ajax: {
                url: apiUrl,
				method: 'POST',
                dataFilter: function (data, type) {
                    if (type === 'json') {
                        try {
                            var parsed = typeof data === 'string' ? JSON.parse(data) : data;
                            console.log('[EDTS Dashboard] api_listDashboard yanıtı:', parsed);
                            if (parsed && (parsed.recordsTotal !== undefined || parsed.data !== undefined)) {
                                console.log('[EDTS Dashboard] Kayıt sayısı:', (parsed.data && parsed.data.length) || 0, '| recordsTotal:', parsed.recordsTotal);
                            }
                            return data;
                        } catch (e) {
                            console.error('[EDTS Dashboard] Geçersiz JSON – sunucu HTML veya hatalı yanıt döndü. Ham yanıt (ilk 500 karakter):', String(data).substring(0, 500));
                            throw e;
                        }
                    }
                    return data;
                },
            },
            columns: [
                { data: '_dosyano', search: { value: filterSearchValue } },
                { data: '_esasno', search: { value: filterSearchValue } },
                { data: '_mahkeme', search: { value: filterSearchValue } },
                { data: '_durusmatarihi', search: { value: filterSearchValue } },
                { data: '_avukat', search: { value: filterSearchValue } },
                { data: '_taraf', search: { value: filterSearchValue } },
                { data: '_islem', search: { value: filterSearchValue } },
                { data: '_dosyaturu', search: { value: filterSearchValue } },
            ],
            createdRow: function (row, data) {
                var id = (data && data._id != null) ? String(data._id) : '';
                var dosyano = (data && data._dosyano != null) ? String(data._dosyano) : '';
                var dosyanoCount = data._dosyano_count != null ? parseInt(data._dosyano_count, 10) : 0;
                var dosyanoEsc = (dosyano || '').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                var dosyanoBadge = (dosyano && dosyanoCount >= 2) ? ' <span class="badge badge-light-primary durusma-dosyano-badge" style="cursor:pointer" data-dosyano="' + dosyanoEsc + '" title="Bu dosya no\'ya ait tüm kayıtları listele">' + dosyanoCount + '</span>' : '';
                $(row).find('td:eq(0)').html('<a href="javascript:void(0)" class="durusma-dosyano-link" data-id="' + id + '">' + (dosyano || '') + '</a>' + dosyanoBadge);

                var esasno = (data && data._esasno != null) ? String(data._esasno) : '';
                var esasnoCount = data._esasno_count != null ? parseInt(data._esasno_count, 10) : 0;
                var esasnoEsc = (esasno || '').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                var esasnoBadge = (esasno && esasnoCount >= 2) ? ' <span class="badge badge-light-primary durusma-esasno-badge" style="cursor:pointer" data-esasno="' + esasnoEsc + '" title="Bu esas no\'ya ait tüm kayıtları listele">' + esasnoCount + '</span>' : '';
                $(row).find('td:eq(1)').html((esasno || '') + esasnoBadge);
            },
        });

        // Konsol: DataTables hata / uyarı – tam mesaj ve konum
        dtdurusmalarw.on('error', function (e, settings, techNote, message) {
            console.error('[EDTS Dashboard] DataTables hatası:', message);
            console.error('[EDTS Dashboard] techNote:', techNote);
            console.error('[EDTS Dashboard] settings.ajax:', settings.ajax);
            console.error('[EDTS Dashboard] Stack:', new Error().stack);
        });
        dtdurusmalarw.on('request', function () {
            console.log('[EDTS Dashboard] api_listDashboard isteği gönderiliyor...');
        });
        dtdurusmalarw.on('requestChild', function () {
            console.log('[EDTS Dashboard] api_listDashboard (child) isteği gönderiliyor...');
        });

        $(document).off('click.durusmaDashboard').on('click.durusmaDashboard', '#durusmalar_content_list .durusma-dosyano-link', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            if (typeof KTModalUpdateDurusmalarManuel !== 'undefined' && KTModalUpdateDurusmalarManuel.viewModal && id) KTModalUpdateDurusmalarManuel.viewModal(id);
        });
        // Badge tıklanınca dashboard tablosunda o esas no / dosya no ile filtrele (sonuçlar dashboard'da kalsın)
        $(document).off('click.durusmaDashboardBadge').on('click.durusmaDashboardBadge', '#durusmalar_content_list .durusma-dosyano-badge', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var dosyano = $(this).attr('data-dosyano') || $(this).data('dosyano') || '';
            if (dosyano && dtdurusmalarw) {
                filterData.dDosyaNo = dosyano;
                filterData.dEsasNo = '';
                dtdurusmalarw.columns([0,1,2,3,4,5,6,7]).search(JSON.stringify(filterData)).draw();
            }
        });
        $(document).off('click.durusmaDashboardBadgeEsas').on('click.durusmaDashboardBadgeEsas', '#durusmalar_content_list .durusma-esasno-badge', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var esasno = $(this).attr('data-esasno') || $(this).data('esasno') || '';
            if (esasno && dtdurusmalarw) {
                filterData.dEsasNo = esasno;
                filterData.dDosyaNo = '';
                dtdurusmalarw.columns([0,1,2,3,4,5,6,7]).search(JSON.stringify(filterData)).draw();
            }
        });
        // Listeleme butonu: filtreyi kaldırıp tabloyu bugün olan tüm duruşmalara göre yenile
        $(document).off('click.durusmaDashboardListeleme').on('click.durusmaDashboardListeleme', '#kt_dashboard_durusmalar_listeleme_btn', function () {
            if (dtdurusmalarw) {
                filterData.dEsasNo = '';
                filterData.dDosyaNo = '';
                dtdurusmalarw.columns([0,1,2,3,4,5,6,7]).search(JSON.stringify(filterData)).draw();
            }
        });
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
        var totalCols = dtdurusmalarw.columns().header().length;
        if (totalCols !== 8) return;
        var hideable = [7, 6, 5, 4, 3, 2, 1];
        var avgColWidth = Math.max(containerWidth / totalCols, 80);
        var visibleCount = Math.max(Math.floor(containerWidth / avgColWidth) - 1, 3);
        var hideCount = Math.max(totalCols - 1 - visibleCount, 0);
        var i;
        try {
            for (i = 0; i < hideable.length; i++) {
                if (hideable[i] < totalCols) dtdurusmalarw.column(hideable[i]).visible(i >= hideCount, false);
            }
            dtdurusmalarw.columns.adjust();
        } catch (e) { /* column adjust may fail in some layouts */ }
    };

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
		var filterMemur 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMemur"]');
		var filterAvukat	= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterAvukat"]');
        if (!filterSearch) return;
        filterSearch.addEventListener('keyup', function (e) {

            filterData.dEklemeTarihi = calisma_aralik;
            filterData.dDurusmaAralik = durusma_aralik;
            filterData.dText = e.target.value;

            // Get filter values
            filterMemur.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.dMemurId  = item.value;
                    //console.log("TUR_>",item.value, item.innerText);
                }else{
                    filterData.dMemurId = -1;
                }
            });
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
        var filterMemur 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMemur"]');
        var filterAvukat 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterAvukat"]');
        const filterButton = document.querySelector('[data-kt-dosc-durusmalar-update-table-filter="filter"]');
        if (!filterButton) return;
        // Filter datatable on submit
        filterButton.addEventListener('click', function () {
			
             // Get filter values
            filterMemur.forEach((item, index) => {
                if (item.innerText.indexOf('selected') && item.value !== '') {
                    // Build filter value options
                    filterData.dMemurId  = item.value;
                    //console.log("TUR_>",item.value, item.innerText);
                }else{
                    filterData.dMemurId  = -1;
                }
            });
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

        var filterMemur 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMemur"]');
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
        filterMemur.forEach((item, index) => {
            if (item.innerText.indexOf('selected') && item.value !== '') {
                // Build filter value options
                filterData.dMemurId  = item.value;
                //console.log("TUR_>",item.value, item.innerText);
            }else{
                filterData.dMemurId = -1;
            }
        });
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
        .columns([0,1,2,3,4,5,6,7])
        .flatten()
        .search(JSON.stringify(filterData))
        .draw();		
	}

	var handleDatatimePicker = function(element) {
		if (!tarihPicker || !tarihPickerDurusma) return;

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
    return {
        init: function () {
			handleDatatimePicker();		
            initDatatable();
			
            handleSearchDatatable();
            handleFilterDatatable();
            handleSearchButtonDatatable();
            handleResetForm();
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