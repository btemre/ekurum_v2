"use strict";
var tableDosyaList;
var dtDosyaList;
var dosyaPreviewModal;
var dosyaPreviewModalEl;
var previewForm;
var filterFormDosyaList;
var filterButtonDosyaList;
var dosyaPreviewModalEditBtn;
var dosyaPreviewModalArchiveBtn;
var dosyaExcelExportBtn;



var KTDosyaListServerSide = function () {

    var hostX = window.location.host;
    const baseUrlHost = "//"+hostX;


    var previewCloseBtn;
	var calisma_aralik;
    var caStart;
    var caEnd;
    var filter_acilis;
    var filter_karar;
    var tarihPicker = document.querySelector('#kt_table_dosya_datein');

    var araDLText;
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
            caStart = start.format('DD-MM-YYYY');
            caEnd = end.format('DD-MM-YYYY');
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

    var initDatatable = function () {
		calisma_aralik  = tarihPicker.value;
        filterData.dAralik  = calisma_aralik;


		
        dtDosyaList = $("#kt_content_dosya_list").DataTable({
            searchDelay: 1000,
            processing: true,
            serverSide: true,
			// responsive: true,
            select: true,
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
                url: baseUrlHost+"/apps/hedas/dosya/api_list",
                method: 'POST',
                dataSrc: function (json) {
                    if (json == null || typeof json !== 'object') {
                        console.error('[DataTables kt_content_dosya_list] Geçersiz yanıt: yanıt nesne değil.', json);
                        return { recordsTotal: 0, recordsFiltered: 0, data: [] };
                    }
                    if (json.success === false) {
                        console.warn('[DataTables kt_content_dosya_list] API uyarı/hata:', {
                            code: json.code,
                            description: json.description,
                            recordsTotal: json.recordsTotal,
                            recordsFiltered: json.recordsFiltered
                        });
                    }
                    return json;
                },
                error: function (xhr, textStatus, thrown) {
                    var msg = '[DataTables kt_content_dosya_list] İstek hatası: ' + (thrown || textStatus);
                    console.error(msg, {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: (xhr.responseText || '').substring(0, 500),
                        contentType: xhr.getResponseHeader('Content-Type')
                    });
                }
            },
            columns: [
                // { data: null },
                { data: '_id' }, //acilistarihi
                { data: '_acilistarihi' }, //acilistarihi
                { data: '_kurumdosyano' },
                { data: '_davaci' },
                { data: '_davali' },                
                { data: '_davakonuaciklama' },
                { data: '_mahkeme' }, //mahkeme
                { data: '_esasno' }, //esasno
                { data: '_kararno' },
                { data: '_mevkiplaka' },
                { data: '_tags' },
            /*    { data: '_icrano' },
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
				{ data: '_aciklama' },
				{ data: '_mahkemeler' }, */
                
            ],
            columnDefs: [
				{
					targets: 0,
					data: null,
					orderable: true,
					className: 'text-center',
					render: function (data, type, row) { 
						// return row._id;
                        return "";
					}
				},
				{
					targets: 2,
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
                // {
                //     targets: 5,
                //     data: null,
                //     orderable: true,
                //     className: 'text-left',
                //     render: function (data, type, row) {
                //         var shortdata;
                //         if(row._davakonu.length>20){
                //             shortdata = row._davakonu.slice(0,20)+'...';
                //         }else{
                //             shortdata = row._davakonu;
                //         }

                //         var metin = `<span class="" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+row._davakonu+`">`+shortdata+`</span>`;
                //         return metin;
                //     },
                // },
                {
                    targets: 5,
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
                        if(row._kararno!=null && row._kararno.length>10){
                            shortdata = row._kararno.slice(0,10)+'...';
                        }else{
                            shortdata = (row._kararno==null)? '' : row._kararno;
                        }
                        var metin = `<span class="" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="`+row._kararno+`">`+shortdata+`</span>`;
                        return metin;
                    },
                },
            /*    {
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
                },*/
            ],
            // Add data-filter attribute
			/*
            createdRow: function (row, data, dataIndex) {
                $(row).find('td:eq(1)').attr('data-filter', data._tarih);
            }
			*/
        }).columns([0,1,2,3,4,5,6,7,8,9,10])
        .flatten()
        .search(JSON.stringify(filterData));

        tableDosyaList = dtDosyaList.$;

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dtDosyaList.on('draw', function () {
           // initToggleToolbar();
           // toggleToolbars();
            //handleDeleteRows();
            KTMenu.createInstances();
        })
        .on( 'select', function ( e, dt, type, indexes ) {
            var rowD = dtDosyaList.rows( indexes ).data().toArray();
			var rowData = rowD[0];
            // console.log(rowData);
            dosyaPreviewModalEl.querySelector('#pwKlasorNoDiv').innerHTML = rowData._arsivno;
            dosyaPreviewModalEl.querySelector('#pwIcraKayitNoDiv').innerHTML = rowData._icrano;
            dosyaPreviewModalEl.querySelector('#pwKurumDosyaNoDiv').innerHTML = rowData._kurumdosyano;
            dosyaPreviewModalEl.querySelector('#pwDavaciDiv').innerHTML = rowData._davaci;
            dosyaPreviewModalEl.querySelector('#pwDavaliDiv').innerHTML = rowData._davali;
            dosyaPreviewModalEl.querySelector('#pwDavaKonusuDiv').innerHTML = rowData._davakonu;
            dosyaPreviewModalEl.querySelector('#pwDavaKonuAciklamaDiv').innerHTML = rowData._davakonuaciklama;
            dosyaPreviewModalEl.querySelector('#pwMevkiPlakaDiv').innerHTML = rowData._mevkiplaka;
            dosyaPreviewModalEl.querySelector('#pwProjeBilgisiDiv').innerHTML = rowData._proje;
            dosyaPreviewModalEl.querySelector('#pwIcraBilgisiDiv').innerHTML = rowData._icra;
            dosyaPreviewModalEl.querySelector('#pwAciklamaDiv').innerHTML = rowData._aciklama;
            dosyaPreviewModalEl.querySelector('#pwTemyizDiv').innerHTML = rowData._temyiz;
            dosyaPreviewModalEl.querySelector('#pwIstinafKabulDiv').innerHTML = rowData._istinafkabul;
            dosyaPreviewModalEl.querySelector('#pwIstinafRedDiv').innerHTML = rowData._istinafred;
            dosyaPreviewModalEl.querySelector('#pwIstinafTemyizDiv').innerHTML = rowData._istinaftemyiz;
            dosyaPreviewModalEl.querySelector('#pwBozmaDiv').innerHTML = rowData._bozmailami;
            dosyaPreviewModalEl.querySelector('#pwOnamaDiv').innerHTML = rowData._onamailami;
            dosyaPreviewModalEl.querySelector('#pwKesinlestirmeDiv').innerHTML = rowData._kesinlestirme;
            dosyaPreviewModalEl.querySelector('#pwMirascilikDiv').innerHTML = rowData._mirascilik;
            dosyaPreviewModalEl.querySelector('#pwTapuBilgisiDiv').innerHTML = rowData._tapubilgisi;
            dosyaPreviewModalEl.querySelector('#pwIdariAlacagiDiv').innerHTML = rowData._idarialacagi;
            dosyaPreviewModalEl.querySelector('#pwVekaletAlacagiDiv').innerHTML = rowData._vekaletalacagi;
            dosyaPreviewModalEl.querySelector('#pwYargilamaGideriDiv').innerHTML = rowData._yargilamagideri;
            dosyaPreviewModalEl.querySelector('#pwYargilamaGideriDiv').innerHTML = rowData._yargilamagideri;
            var mahkemelerList = mahkemelerTablePreview(rowData._mahkemeler);
            dosyaPreviewModalEl.querySelector('#mahkemelerTd').innerHTML = mahkemelerList;
            var suanTarih = moment().format('DD-MM-YYYY HH:mm');
            dosyaPreviewModalEl.querySelector('#pwZamanDamgaDiv').innerHTML = suanTarih.toString();
            $('#kt_modal_dosya_preview_edit_btn').attr('data-id',rowData._id);
            $('#kt_modal_dosya_preview_archive_btn').attr('data-id',rowData._id);
            dosyaPreviewModal.show();
		} )
        .on( 'deselect', function ( e, dt, type, indexes ) {
            var rowData = dtDosyaList.rows( indexes ).data().toArray();

        });
    }


	const handleCloseButton = () => {

		previewCloseBtn.addEventListener('click', function (e) {
            dosyaPreviewModal.hide(); // Hide modal
			
            /*
            e.preventDefault();
			Swal.fire({
				text: "Vazgeçmek istediğinizden emin misiniz?",
				icon: "warning",
				showCancelButton: true,
				buttonsStyling: false,
				confirmButtonText: "Evet, Vazgeç!",
				cancelButtonText: "Kapatma",
				customClass: {
					confirmButton: "btn btn-primary",
					cancelButton: "btn btn-danger"
				}
			}).then(function (result) {
				if (result.value) {
					dosyaPreviewModal.hide(); // Hide modal				
				}
			});
            */
		});
		
	}

    var mahkemelerTablePreview = (data) => {
        var htmlText = "";
        data.forEach(mahkeme => {
            htmlText += '<table class="w-100 mw-100 m-1 my-1 p-0 border border-gray-500">';
            htmlText += '<tbody><tr>';
            htmlText += '<td class="tdBaslik1" style="width:119px;height:22px;line-height:17px;text-align:left;vertical-align:top; font-weight:bold">Mahkemesi :</td>';
            htmlText += '<td colspan="3" rowspan="1" class="tdIcerik1" style="width:550px;height:22px;line-height:16px;text-align:left;vertical-align:middle;font-weight:bold"><div id="pwMahkemeDiv">'+mahkeme._mahkeme+'</div></td>';
            htmlText += '</tr><tr>';
            htmlText += '<td class="tdBaslik1" style="width:119px;height:22px;line-height:17px;text-align:center;vertical-align:middle; font-weight:bold">Esas No</td>';
            htmlText += '<td class="tdBaslik1" style="width:119px;height:22px;line-height:17px;text-align:center;vertical-align:middle; font-weight:bold">Karar No</td>';
            htmlText += '<td class="tdBaslik1" style="width:119px;height:22px;line-height:17px;text-align:center;vertical-align:middle; font-weight:bold">A&ccedil;ılış Tarihi</td>';
            htmlText += '<td class="tdBaslik1" style="width:119px;height:22px;line-height:17px;text-align:center;vertical-align:middle; font-weight:bold">Karar Tarihi</td>';
            htmlText += '</tr><tr>';
            htmlText += '<td class="tdIcerik1" style="text-align:center;vertical-align:middle;">'+mahkeme._esasno+'</td>';
            htmlText += '<td class="tdIcerik1" style="text-align:center;vertical-align:middle;">'+mahkeme._kararno+'</td>';
            htmlText += '<td class="tdIcerik1" style="text-align:center;vertical-align:middle;">'+mahkeme._acilistarihi+'</td>';
            htmlText += '<td class="tdIcerik1" style="text-align:center;vertical-align:middle;">'+mahkeme._kayittarihi+'</td>';
            htmlText += '</tr><tr>';
            htmlText += '<td colspan="4"><div class="separator border-dark"></div></td>';
            htmlText += '</tr><tr>';
            htmlText += '<td class="tdBaslik1 p-1" style="width:119px;height:22px;line-height:17px;text-align:left;vertical-align:top; font-weight:bold">A&ccedil;ıklama</td>';
            htmlText += '<td colspan="3" rowspan="1" class="p-1">'+mahkeme._maciklama+'</td>';
            htmlText += '</tr></tbody></table>';
        });

        return htmlText;
    }


    // Filter Datatable
    var handleFilterDatatable = () => {
        // Select filter options
        // console.log("filterButton", filterButton);
        // Filter datatable on submit
        filterButtonDosyaList.addEventListener('click', function (e) {
            e.preventDefault();
            calisma_aralik = tarihPicker.value;
            filterData.dAralik = calisma_aralik;

            // var araTapuSelected = document.querySelector( 'input[name="dlara_tapu"]:checked');
            filterData.dText                = araDLText.value;
            // filterData.dTags                = araDLTags.value;
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
            // filterData.dTapuBilgisi         = araTapuSelected.value;//araDLTapu.value;
            filterData.dMahkemeData.dmMahkeme = araDLMahkeme.value;
            filterData.dMahkemeData.dmEsasNo = araDLEsasno.value;
                

			handleFilterSubmit();
        });
    }
	
	
	var handleFilterSubmit = () => {
        dtDosyaList
        .columns([0,1,2,3,4,5,6,7,8,9,10])
        .flatten()
        .search(JSON.stringify(filterData))
        .draw();		
    }

    var handlePreviewEditButton = () => {
        dosyaPreviewModalEditBtn.addEventListener('click', function (e) {
            e.preventDefault();
            KTModalEditDosya.viewModal();
        });
    }


    var handlePreviewArchiveButton = () => {
        dosyaPreviewModalArchiveBtn.addEventListener('click', function (e) {
            e.preventDefault();
            KTDosyaListServerSide.copeAtModal();
        });
    }

    var handleExcelExportButton = () => {

        dosyaExcelExportBtn.addEventListener('click', function (e) {
            e.preventDefault();

            calisma_aralik = tarihPicker.value;
            filterData.dAralik = calisma_aralik;
            var tarihD = calisma_aralik.split(' & ');
            var ilkTarih = tarihD[0].split('-').reverse().join('/');
            var sonTarih = tarihD[1].split('-').reverse().join('/');
            

            var tarihFarki = zamanFarkiHesapla(ilkTarih, sonTarih);

            if(tarihFarki<1 || tarihFarki>91){

                Swal.fire({
                    text: "Rapor Oluşturmak İçin Çalışma Aralığı Maksimum 3 Ay Olmalıdır.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Tamam!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });

            }else{

                dosyaExcelExportBtn.setAttribute('data-kt-indicator', 'on');
                dosyaExcelExportBtn.disabled = true;
    
                var myform = document.createElement("form");
                document.body.appendChild(myform);
                myform.method = "POST";
                myform.target = "_blank";
                myform.action = baseUrlHost+"/apps/hedas/dosya/api_excelexport";
                var element1 = document.createElement("INPUT");         
                element1.name="data"
                element1.value = JSON.stringify(filterData);
                element1.type = 'hidden'
                myform.appendChild(element1);            
                myform.submit();
                // console.log(myform);
                dosyaExcelExportBtn.disabled = false;
                dosyaExcelExportBtn.removeAttribute('data-kt-indicator');
    
            }

        });
    }

    var zamanFarkiHesapla = (ilk, son) => {
        // Date fonksiyonu  aa/gg/yyyy formatında zamanı almaktadır.
        var tarih1 = new Date(ilk);
        var tarih2 = new Date(son);
        //iki tarih arasındaki saat farkını hesaplamak için aşağıdaki yöntemi kullanabiliriz.
        var zamanFark = Math.abs(tarih2.getTime() - tarih1.getTime());
        
        //zamanFark değişkeni ile elde edilen saati güne çevirmek için aşağıdaki yöntem kullanılabilir.
        var gunFark = Math.ceil(zamanFark / (1000 * 3600 * 24)); 
        
        return gunFark;
    }

    return {
        init: function () {

			dosyaPreviewModalEl = document.querySelector('#kt_modal_dosya_preview_target');

			if (!dosyaPreviewModalEl) {
				return;
			}
			dosyaPreviewModal = new bootstrap.Modal(dosyaPreviewModalEl);

            previewCloseBtn = document.getElementById('kt_modal_dosya_preview_target_close');
            dosyaPreviewModalEditBtn = document.getElementById('kt_modal_dosya_preview_edit_btn');
            dosyaPreviewModalArchiveBtn = document.getElementById('kt_modal_dosya_preview_archive_btn');


            filterFormDosyaList = document.querySelector('#kt_modal_list_dosya_filter_form');
            filterButtonDosyaList = document.getElementById('kt_modal_dosya_list_ara_submit');

            araDLText           = filterFormDosyaList.querySelector('[name="dlara_text"]');
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

            dosyaExcelExportBtn = document.getElementById('dosya_content_list_excel_export');
            

            handleDatatimePicker();
            initDatatable();
            handleCloseButton();
            handleFilterDatatable();
            handlePreviewEditButton();
            handlePreviewArchiveButton();
            handleExcelExportButton();

        },
        reload: function(){
            handleFilterSubmit();
        },
        copeAtModal: function(){
            var hostUrl = window.location.host;
            const baseUrlHost = "//"+hostUrl+"/apps/hedas/dosya/";
            var modulUrl = window.location.href;
            var dosyaId = $('#kt_modal_dosya_preview_archive_btn').attr('data-id');
            var postData = {
                id: dosyaId
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
                                                    dosyaPreviewModal.hide();

                                                    
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