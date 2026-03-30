<?php
$lastDate=date('d-m-Y', strtotime('+6 years',time()));

?>
<!--begin::Page Popup Alert-->
<?php $this->load->view("includes/alert"); ?>
<!--end::Page Popup Alert-->
<!--begin::Page Custom Javascript(used by this page)-->
<!--begin::Page Custom Javascript(used by this page)-->

<script src="<?php echo base_url('assets/js/moduls/dosya/apex.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/moduls/dosya/apexcharts.js'); ?>"></script>
<!--end::Page Custom Javascript-->
<!--end::Page Custom Javascript-->

<script>

function updateIstatisikFiltreler(resetBtn) {
    if (resetBtn=='reset') {
        var redirectUrl='<?=base_url("dosya/istatistik");?>';
        location.href=redirectUrl;
        return;
    }
    urlx="search=1";
    var durusmaTarih=$("#current_durusma_aralik").val();
    if (durusmaTarih=="") {
        durusmaTarih=$("#kt_table_durusmalar_datein").val();
    }
    urlx+="&durusma_aralik="+durusmaTarih;
    $("select.istFilterComboList").each(function(i){
        var idx=$(this).attr("id");
        var idVal=$(this).val();
        urlx+="&"+idx+"="+idVal;
        
    })
    
    var redirectUrl='<?=base_url("dosya/istatistik");?>?'+urlx;
    console.log(redirectUrl);
    location.href=redirectUrl;
    
    
}

$(function() {

    $('.autoFilters').select2({

        minimumInputLength: 3,
        closeOnSelect: false,
        language: {
  	    inputTooShort: function() {
  		    return 'En az 3 harf girin..';
  	    }},
        ajax: {
            url: function (params) {
                return '/apps/hedas/dosya/istatistik?api_search=1&from='+$(this).data("filter-id");
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    name: params.term,
                    fullText: true
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (outputFb) {
                        return {
                            id: outputFb.id,
                            text: outputFb.val
                        }
                    })
                };
            },
            cache: true
        },
        placeholder: 'Select a country',
        allowClear: true
    });

    $("#kt_table_durusmalar_datein").daterangepicker({
			opens: 'left',
			showDropdowns: true,
			//startDate: moment().startOf('year').format("DD-MM-YYYY"),//moment().subtract(29, 'days').format("DD-MM-YYYY"),
			//endDate: moment().format("DD-MM-YYYY"),//moment().subtract(0, 'month').endOf('month'),//
            startDate:$("#current_durusma_start").val(),
            endDate:$("#current_durusma_end").val(),
			ranges: {
				'Bugün': [moment(), moment()],
				'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
				'Son 30 Gün': [moment().subtract(29, 'days'), moment()],
				'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
				'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Tüm Kayıtlar': ['01-01-2015','<?=$lastDate;?>'],                
                'Bu Yıl': [moment().startOf('year'), moment()],                
                <?php 
                $lastYear=(date("Y")-1);
                $lastYear5=$lastYear-4;
                for ($ix=$lastYear;$ix>$lastYear5;$ix--) { 
                    $ixStart=date("01-01-$ix");
                    $ixEnd=date("31-12-$ix");
                    ?>
                    '<?=$ix;?> Yılı': ['<?=$ixStart;?>','<?=$ixEnd;?>'],
                <?php }

                ?>
                
                'Son 6 Ay': [moment().subtract(6, 'month').startOf('month'), moment()]
			},
			locale: {
				applyLabel: 'Aralığı Seç',
				cancelLabel: 'Vazgeç',
				format: 'DD-MM-YYYY',
				customRangeLabel: 'Kendim Seçeceğim',
				separator: ' / ',
				fromLabel: 'From',
				toLabel: '/',
				weekLabel: 'W',
				daysOfWeek: ['Pzr', 'Pts', 'Sal', 'Çar', 'Per', 'Cum', 'Cts'],
				monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
				firstDay: 1
			}
		}, function(start, end, label) {
			var durusma_aralik = start.format('DD-MM-YYYY') + ' / ' + end.format('DD-MM-YYYY');
            $("#current_durusma_aralik").val(durusma_aralik).trigger("change");
			
    });

    
    $("#current_durusma_aralik").on("change",function(){
        updateIstatisikFiltreler();
    });

    $("#kt_horizontal_search_advanced_reset").on("click",function(){
        
        updateIstatisikFiltreler("reset");
    });

    $("#kt_horizontal_search_advanced_update").on("click",function(){
        
        updateIstatisikFiltreler();
    });

    
    

});    


</script>


