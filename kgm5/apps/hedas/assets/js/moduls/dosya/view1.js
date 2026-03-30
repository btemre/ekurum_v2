"use strict";
var tableDosyaList;
var dtDosyaList;
var filterFormDosyaList;
var filterButtonDosyaList;
// Class definition
var KTDosyaListServerSide = function () {
    // Shared variables

    var araDLDosyano;
    var araDLMahkeme;
    var araDLEsasno;
    var araDLDavaci;
    var araDLDavali;
    var araDLDavakonusu;
    var araDLKonuaciklama;
    var araDLMevki;
    var araDLIcrano;
    var araDLIcra;
    var araDLIstinafkabul;
    var araDLIstinafred;
    var araDLOnama;
    var araDLBozma;
    var araDLIstinaf;
    var araDLTemyiz;
    var araDLMirascilik;
    var araDLTags;
    var araDLTapu;


    var filterData = {
        dText: '',
        dAralik: '',
        dTags: '',
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
        dTapuBilgisi: 2,
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
    
    const filterSearch = document.querySelector('[data-kt-dosyalist-table-filter="searchx"]');
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


		
        dtDosyaList = $("#kt_content_dosya_list").DataTable({
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
                url: baseUrlHost+"/apps/hedas/dosya/api_list",
				method: 'POST',
				//dataType: 'json',
				//contentType: 'application/json',
            },
            columns: [
                { data: null },
                { data: '_kurumdosyano' },
                { data: '_acilistarihi' }, //acilistarihi
                { data: '_davaci' },
                { data: '_davali' },
                { data: '_davakonu' },
                { data: '_mahkeme' }, //mahkeme
                { data: '_esasno' }, //esasno
                { data: '_arsivno' },
                { data: '_icrano' },
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
                
            ],
            columnDefs: [
				{
					targets: 1,
					data: null,
					orderable: true,
					className: 'text-center',
					render: function (data, type, row) { 
						return row._kurumdosyano;
					}
				},
                {
                    targets: 3,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var shortdata;
                        if(row._davaci.length>20){
                            shortdata = row._davaci.slice(0,20)+'...';
                        }else{
                            shortdata = row._davaci;
                        }
                        var metin = `<span class="" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+row._davaci+`">`+shortdata+`</span>`;
                        return metin;
                    },
                },
                {
                    targets: 4,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var shortdata;
                        if(row._davali.length>20){
                            shortdata = row._davali.slice(0,20)+'...';
                        }else{
                            shortdata = row._davali;
                        }

                        var metin = `<span class="" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+row._davali+`">`+shortdata+`</span>`;
                        return metin;
                    },
                },
                {
                    targets: 5,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var shortdata;
                        if(row._davakonu.length>20){
                            shortdata = row._davakonu.slice(0,20)+'...';
                        }else{
                            shortdata = row._davakonu;
                        }

                        var metin = `<span class="" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+row._davakonu+`">`+shortdata+`</span>`;
                        return metin;
                    },
                },
                {
                    targets: 6,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var shortdata;
                        if(row._mahkeme.length>20){
                            shortdata = row._mahkeme.slice(0,20)+'...';
                        }else{
                            shortdata = row._mahkeme;
                        }
                        var metin = `<span class="" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+row._mahkeme+`">`+shortdata+`</span>`;
                        return metin;
                    },
                },
                {
                    targets: 7,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var shortdata = "";
                        if(row._esasno!=null && row._esasno.length>10){
                            shortdata = row._esasno.slice(0,10)+'...';
                        }else{
                            shortdata = (row._esasno==null)? '' : row._esasno;
                        }
                        var metin = `<span class="" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+row._esasno+`">`+shortdata+`</span>`;
                        return metin;
                    },
                },
                {
                    targets: 8,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        var shortdata;
                        if(row._mevkiplaka.length>20){
                            shortdata = row._mevkiplaka.slice(0,20)+'...';
                        }else{
                            shortdata = row._mevkiplaka;
                        }

                        var metin = `<span class="" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+row._mevkiplaka+`">`+shortdata+`</span>`;
                        return metin;
                    },
                },
                {
                    targets: 9,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        return row._arsivno;
                    },
                },
                {
                    targets: 10,
                    data: null,
                    orderable: true,
                    className: 'text-left',
                    render: function (data, type, row) {
                        return row._icrano;
                    },
                },
                {
                    targets: 11,
                    data: null,
                    orderable: true,
                    className: '',
                    render: function (data, type, row) {
                        var shortdata;
                        if(row._davakonuaciklama.length>20){
                            shortdata = row._davakonuaciklama.slice(0,20)+'...';
                        }else{
                            shortdata = row._davakonuaciklama;
                        }

                        var metin = `<span class="" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+row._davakonuaciklama+`">`+shortdata+`</span>`;
                        return metin;
                    },
                },
                {
                    targets: 28,
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
                    targets: 0,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <a href="#" class="btn btn-info btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">İşlemler
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
								<a href="#" class="menu-link px-3 dEditButton" data-id="`+row._id+`" id="edit`+row._id+`" onclick="KTModalEditDosya.viewModal('`+row._id+`');">Düzenle</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-1">
								<a href="#" class="menu-link px-3 popup-btn dCopButton" data-id="`+row._id+`" id="arsivle`+row._id+`" onclick="KTDosyaListServerSide.copeAtModal('`+row._id+`');">Çöpe At
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
        }).columns([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27])
        .flatten()
        .search(JSON.stringify(filterData));

        tableDosyaList = dtDosyaList.$;

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dtDosyaList.on('draw', function () {
           // initToggleToolbar();
           // toggleToolbars();
            //handleDeleteRows();
            KTMenu.createInstances();
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    // var handleSearchDatatable = function () {
	// 	var filterDurum 		= document.querySelectorAll('[data-kt-dosya-table-filter="filterDurum"]');

    //     filterSearch.addEventListener('keyup', function (e) {
    //         filterData.dAralik 		= calisma_aralik;f
    //         filterData.dText        = e.target.value;

    //         handleFilterSubmit();
    //     });
		
    // }

    // Filter Datatable
    var handleFilterDatatable = () => {
        // Select filter options
        // console.log("filterButton", filterButton);
        // Filter datatable on submit
        filterButtonDosyaList.addEventListener('click', function (e) {
            e.preventDefault();
            calisma_aralik = tarihPicker.value;
            filterData.dAralik = calisma_aralik;

            var araTapuSelected = document.querySelector( 'input[name="dlara_tapu"]:checked');

            filterData.dTags                = araDLTags.value;
            filterData.dIcraNo              = araDLIcrano.value;
            filterData.dKurumDosyaNo        = araDLDosyano.value;
            filterData.dDavaci              = araDLDavaci.value;
            filterData.dDavali              = araDLDavali.value;
            filterData.dDavaKonusu          = araDLDavakonusu.value;
            filterData.dDavaKonuAciklama    = araDLKonuaciklama.value;
            filterData.dMevkiPlaka          = araDLMevki.value;
            filterData.dIcra                = araDLIcra.value;
            filterData.dTemyiz              = araDLTemyiz.value;
            filterData.dIstinafTemyiz       = araDLIstinaf.value;
            filterData.dIstinafKabul        = araDLIstinafkabul.value;
            filterData.dIstinafRed          = araDLIstinafred.value;
            filterData.dBozmaIlami          = araDLBozma.value;
            filterData.dOnamaIlami          = araDLOnama.value;
            filterData.dMirascilik          = araDLMirascilik.value;
            filterData.dTapuBilgisi         = araTapuSelected.value;//araDLTapu.value;
            filterData.dMahkemeData.dmMahkeme = araDLMahkeme.value;
            filterData.dMahkemeData.dmEsasNo = araDLEsasno.value;
                

			handleFilterSubmit();
        });
    }
	
	var handleFilterSubmit = () => {
			dtDosyaList
			.columns([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27])
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


            calisma_aralik = tarihPicker.value;
            filterData.dAralik = calisma_aralik;


            handleFilterSubmit();
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

        $("#filter_acilistarihi").daterangepicker({
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
            filter_acilis = start.format('DD-MM-YYYY') + ' & ' + end.format('DD-MM-YYYY');
            //console.log("calisma_aralik", calisma_aralik);
        });

        $("#filter_karartarihi").daterangepicker({
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
            filter_karar = start.format('DD-MM-YYYY') + ' & ' + end.format('DD-MM-YYYY');
        });

    }
	
    // var getItemEdit = function () {
    //     $(".isEdit").click(function(e){
    //         $data_id = $(this).data("id");
    //         KTModalEditDosya.viewModal($data_id);
    //     })
    //   }
    // Public methods
    return {
        init: function () {

            filterFormDosyaList = document.querySelector('#kt_modal_list_dosya_filter_form');
            filterButtonDosyaList = document.getElementById('kt_modal_dosya_list_ara_submit');

            araDLDosyano        = filterFormDosyaList.querySelector('[name="dlara_dosyano"]');
            araDLMahkeme        = filterFormDosyaList.querySelector('[name="dlara_mahkeme"]');
            araDLEsasno         = filterFormDosyaList.querySelector('[name="dlara_esasno"]');
            araDLDavaci         = filterFormDosyaList.querySelector('[name="dlara_davaci"]');
            araDLDavali         = filterFormDosyaList.querySelector('[name="dlara_davali"]');
            araDLDavakonusu     = filterFormDosyaList.querySelector('[name="dlara_davakonusu"]');
            araDLKonuaciklama   = filterFormDosyaList.querySelector('[name="dlara_konuaciklama"]');
            araDLMevki          = filterFormDosyaList.querySelector('[name="dlara_mevki"]');
            araDLIcrano         = filterFormDosyaList.querySelector('[name="dlara_icrano"]');
            araDLIcra           = filterFormDosyaList.querySelector('[name="dlara_icra"]');
            araDLIstinafkabul   = filterFormDosyaList.querySelector('[name="dlara_istinafkabul"]');
            araDLIstinafred     = filterFormDosyaList.querySelector('[name="dlara_istinafred"]');
            araDLOnama          = filterFormDosyaList.querySelector('[name="dlara_onamailami"]');
            araDLBozma          = filterFormDosyaList.querySelector('[name="dlara_bozmailami"]');
            araDLIstinaf        = filterFormDosyaList.querySelector('[name="dlara_istinaf"]');
            araDLTemyiz         = filterFormDosyaList.querySelector('[name="dlara_temyiz"]');
            araDLMirascilik     = filterFormDosyaList.querySelector('[name="dlara_mirascilik"]');
            araDLTags           = filterFormDosyaList.querySelector('[name="dlara_tags"]');
            
        


			handleDatatimePicker();		
            initDatatable();
			
            //handleSearchDatatable();
            //initToggleToolbar();
            handleFilterDatatable();
           // handleDeleteRows();
            // handleResetForm();
			// getItemEdit();
        },
        reload: function(){
            handleFilterSubmit();
        },
        copeAtModal: function(id){
            var hostUrl = window.location.host;
            const baseUrlHost = "//"+hostUrl+"/apps/hedas/dosya/";
            var modulUrl = window.location.href;
            var postData = {
                id: id
            }

            Swal.fire({
                text: "İlgili Kayıt Çöp Kutusuna Taşınacak. Emin misiniz?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Taşı",
                cancelButtonText: "Vazgeç",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger"
                }
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        url: baseUrlHost+"api_ejectdata",
                        data: JSON.stringify(postData),
                        success: function (response) {
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
                                                
                                                if(modulUrl.indexOf('dosya')>0){
                                                    //KTGelenGidenListServerSide.init();
                                                    handleFilterSubmit();
                                                    
                                                }
                                                //window.location.assign(baseUrlHost);
                                            }
                                        });

                                    }else if(response.code==406 && response.error==true){
                                        
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
                        }
                    });
                }
            });

        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDosyaListServerSide.init();
});