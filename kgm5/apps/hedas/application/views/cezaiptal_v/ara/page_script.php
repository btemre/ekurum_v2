<!--begin::Page Popup Alert-->
<?php $this->load->view("includes/alert"); ?>
<!--end::Page Popup Alert-->
<!--begin::Page Custom Javascript(used by this page)-->
<!--<script src="<?php echo base_url('assets/js/moduls/durusmalar/list.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/moduls/durusmalar/update.js'); ?>"></script>-->


<script type="text/javascript"
    src="https://cdn.datatables.net/v/bs-3.3.7/jq-2.2.4/jszip-3.1.3/pdfmake-0.1.27/dt-1.10.15/b-1.3.1/b-html5-1.3.1/b-print-1.3.1/r-2.1.1/rg-1.0.0/datatables.min.js"></script>
<script>
    $('#cezaiptal').DataTable({
        dom: 'Bfrtip',
        buttons: ['pageLength',
                'copy',
                {
                    extend: 'excel',
                    title: 'Karayolları 5.Bölge Müdürlüğü',
                    font: 'Times New Roman',
                    messageTop: 'Duruşma Listesi'
                },
                'print',
            ],
            lengthMenu: [
                [10, 25, 50, 100],
                ['10 Kayıt', '25 Kayıt', '50 Kayıt', '100 Kayıt'],
            ],
        order: [[2, 'desc']], // sütuna göre artan sıralama
        responsive: true,
        language: {
            //url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json',
            info: "Toplam(sonuç) _TOTAL_ kayıt. _START_ ve _END_ arası kayıt gösteriliyor.",
            infoEmpty: "Gösterilecek hiç kayıt yok.",
            loadingRecords: "Kayıtlar yükleniyor.",
            lengthMenu: "Sayfada _MENU_ kayıt göster",
            zeroRecords: "Tablo boş",
            search: "Detaylı Arama ve Filtreleme (Boşluk Kullanıp Farklı Arama Yaparak Filtreleyebilirsiniz):",
            infoFiltered: "(toplam _MAX_ kayıttan filtrelenenler)",
            buttons: {
                copyTitle: "Panoya kopyalandı.",
                copySuccess: "Panoya %d satır kopyalandı",
                copy: "Kopyala",
                print: "Yazdır",
            },

            paginate: {
                first: "İlk",
                previous: "Önceki",
                next: "Sonraki",
                last: "Son"
            },
        }
    });
</script>
<!--end::Page Custom Javascript-->