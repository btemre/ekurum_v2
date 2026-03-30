"use strict";
var tabledurusmalarw;
var dtdurusmalarw;
// Class definition
var KTDurusmalarArchiveListServerSide = function () {
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
	};

    const filterSearch = document.querySelector('[data-kt-dosc-durusmalar-update-table-filter="search"]');
	var calisma_aralik;
    var tarihPicker = document.querySelector('#kt_table_durusmalar_datein');

    var defaultCalismaAralik = moment().subtract(6, 'year').startOf('year').format('DD-MM-YYYY') + ' & ' + moment().format('DD-MM-YYYY');

    // Private functions
    var initDatatable = function () {
        var hostX = window.location.host;
        const baseUrlHost = "//"+hostX;
		calisma_aralik = (tarihPicker && tarihPicker.value) ? tarihPicker.value : defaultCalismaAralik;

		filterData.dEklemeTarihi = calisma_aralik
		
	/*	$('#kt_content_archive_durusmalar_list thead tr')
				.clone(true)
				.addClass('filters')
				.appendTo('#kt_content_archive_durusmalar_list thead');		
		*/
		
    
        dtdurusmalarw = $("#kt_content_archive_durusmalar_list").DataTable({
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
                url: baseUrlHost+"/apps/edts/durusmalar/api_ejectlist",
				method: 'POST',
				//dataType: 'json',
				//contentType: 'application/json',
            },
            columns: [
                { data: '_dosyano' },
                { data: '_dosyaturu' },
                { data: '_mahkeme' },
                { data: '_durusmatarihi' },
                { data: '_taraf' },
				{ data: '_avukat' },
				{ data: '_memur' },
                { data: '_esasno' },
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
                            <div class="menu-item px-1">
                                <a href="#" class="menu-link px-3 popup-btn cezalarMWCopButton" data-id="`+row._id+`" id="arsivle`+row._id+`" onclick="KTDurusmalarArchiveListServerSide.geriAlModal('`+row._id+`');">Geri Al
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

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dtdurusmalarw.on('draw', function () {
           // initToggleToolbar();
           // toggleToolbars();
            //handleDeleteRows();
            KTMenu.createInstances();
        });

        

    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
		var filterMemur 		= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterMemur"]');
		var filterAvukat	= document.querySelectorAll('[data-kt-durusmalar-update-table-filter="filterAvukat"]');
        if (!filterSearch) return;
        filterSearch.addEventListener('keyup', function (e) {

            filterData.dEklemeTarihi = calisma_aralik;
            filterData.dText = e.target.value;
            filterData.dDosyaNo = e.target.value;
            filterData.dDosyaTuru = e.target.value;
            filterData.dMahkeme = e.target.value;
            filterData.dDurusmaTarihi = e.target.value;
            filterData.dEsasNo = e.target.value;
            filterData.dTarafBilgisi = e.target.value;
            filterData.dIslem = e.target.value;
            filterData.dMemur = e.target.value;
            filterData.dAvukat = e.target.value;
            filterData.dTaraf = e.target.value;
            filterData.dAciklama = e.target.value;
            filterData.dTags = e.target.value;
            
 
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

			handleFilterSubmit();
            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            //dt.search(paymentValue).draw();
        });
    }
	
	var handleFilterSubmit = () => {
			if (!dtdurusmalarw) return;
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
	}

    // Public methods
    return {
        init: function () {
			handleDatatimePicker();		
            initDatatable();
			
            handleSearchDatatable();
            handleFilterDatatable();
            handleResetForm();
            tabledurusmalarw = document.querySelector('#kt_content_archive_durusmalar_list');

            if ( !tabledurusmalarw) {
                return;
            } 
            //handleResetForm();
			
        },
        reload: function(){
            handleFilterSubmit();
        },
        geriAlModal: function(id){

            var hostUrl = window.location.host;
            const baseUrlHost = "//"+hostUrl+"/apps/edts/durusmalar/";
            var modulUrl = window.location.href;

            var ejectPData = {
                id: id
            }

            Swal.fire({
                text: "İlgili Kayıt Çöp Kutusundan Geri Alınacak. Emin misiniz?",
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
                        url: baseUrlHost+"api_reejectdata",
                        data: JSON.stringify(ejectPData),
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
                                                
                                                if(modulUrl.indexOf('durusmalar')>0){
                                                    //KTGelenGidenListServerSide.init();
                                                    KTDurusmalarArchiveListServerSide.reload();
                                                    
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
                            console.log(hata);
                        }
                    });
                }
            });

        }
        
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDurusmalarArchiveListServerSide.init();
});