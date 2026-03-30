<?php
$lastDate=date('d-m-Y', strtotime('+6 years',time()));

?>
<!--begin::Page Popup Alert-->
<?php $this->load->view("includes/alert"); ?>
<!--end::Page Popup Alert-->
<!--begin::AI Modal-->
<?php $this->load->view("ai_v/ai_modal"); ?>
<!--end::AI Modal-->
<!--begin::Page Custom Javascript(used by this page)-->
<!--begin::Page Custom Javascript(used by this page)-->

<script src="<?php echo base_url('assets/js/moduls/ai/ai_service.js'); ?>?v=<?php echo asset_ver(); ?>"></script>
<script>AiService.init("<?php echo base_url(); ?>");</script>
<script src="<?php echo base_url('assets/js/moduls/durusmalar/apex.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/moduls/durusmalar/apexcharts.js'); ?>"></script>
<!--end::Page Custom Javascript-->
<!--end::Page Custom Javascript-->

<script>

function updateIstatisikFiltreler(resetBtn) {
    if (resetBtn=='reset') {
        var redirectUrl='<?=base_url("durusmalar/istatistik");?>';
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
    
    var redirectUrl='<?=base_url("durusmalar/istatistik");?>?'+urlx;
    
    location.href=redirectUrl;
}

$(function() {

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

    
    $("#current_durusma_aralik,.istFilterComboList").on("change",function(){
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

<script>//Avukat Bazlı Duruşma Grafiği
    var KTChartsWidget22avukat = function () {
        var e = function (e, t, a, l) {
            var r = document.querySelector(t); if (r) {
                parseInt(KTUtil.css(r, "height"));
                var o = {
                    series: a, chart: { fontFamily: "inherit", type: "donut", width: 350 },
                    plotOptions: { pie: { donut: { size: "50%", labels: { value: { fontSize: "12px" } } } } },
                    colors:
                        [
                            KTUtil.getCssVariableValue("--kt-info"),
                            KTUtil.getCssVariableValue("--kt-success"),
                            KTUtil.getCssVariableValue("--kt-primary"),
                            KTUtil.getCssVariableValue("--kt-danger"),
                            KTUtil.getCssVariableValue("--kt-warning"),
                            KTUtil.getCssVariableValue("--kt-dark"),
                            KTUtil.getCssVariableValue("--kt-secondary"),
                            KTUtil.getCssVariableValue("--kt-light"),
                            KTUtil.getCssVariableValue("--kt-white bg-dark")
                        ],
                    stroke: { width: 0 }, labels: [<?php foreach ($durusmaavukatbazli2 as $durusmaavukatbazli) { ?> "<?= @$durusmaavukatbazli->d_avukat; ?>",
                <?php } ?>], legend: { show: !1 }, fill: { type: "false" }
                },
                    i = new ApexCharts(r, o), s = !1, n = document.querySelector(e); !0 === l && (i.render(), s = !0),
                        n.addEventListener("shown.bs.tab", (function (e) { 0 == s && (i.render(), s = !0) }))
            }
        };
        return {
            init: function () {
                e("#kt_chart_widgets_22avukat_tab_1", "#kt_chart_widgets_22avukat_chart_1", [<?php foreach ($durusmaavukatbazli2 as $durusmaavukatbazli) { ?><?= @$durusmaavukatbazli->sayi; ?>,
                <?php } ?>], !0)
            }
        }
    }();
    "undefined" != typeof module && (module.exports = KTChartsWidget22avukat), KTUtil.onDOMContentLoaded((function () { KTChartsWidget22avukat.init() }));
</script>

<script>//Memur Bazlı Duruşma Grafiği
    var KTChartsWidget22memur = function () {
        var e = function (e, t, a, l) {
            var r = document.querySelector(t); if (r) {
                parseInt(KTUtil.css(r, "height"));
                var o = {
                    series: a, chart: { fontFamily: "inherit", type: "donut", width: 350 },
                    plotOptions: { pie: { donut: { size: "50%", labels: { value: { fontSize: "12px" } } } } },
                    colors:
                        [
                            KTUtil.getCssVariableValue("--kt-info"),
                            KTUtil.getCssVariableValue("--kt-success"),
                            KTUtil.getCssVariableValue("--kt-primary"),
                            KTUtil.getCssVariableValue("--kt-danger"),
                            KTUtil.getCssVariableValue("--kt-warning"),
                            KTUtil.getCssVariableValue("--kt-dark"),
                            KTUtil.getCssVariableValue("--kt-secondary"),
                            KTUtil.getCssVariableValue("--kt-light"),
                            KTUtil.getCssVariableValue("--kt-white bg-dark")
                        ],
                    stroke: { width: 0 }, labels: [<?php foreach ($durusmamemurbazli2 as $durusmamemurbazli) { ?> "<?= @$durusmamemurbazli->d_memur; ?>",
                <?php } ?>], legend: { show: !1 }, fill: { type: "false" }
                },
                    i = new ApexCharts(r, o), s = !1, n = document.querySelector(e); !0 === l && (i.render(), s = !0),
                        n.addEventListener("shown.bs.tab", (function (e) { 0 == s && (i.render(), s = !0) }))
            }
        };
        return {
            init: function () {
                e("#kt_chart_widgets_22memur_tab_2", "#kt_chart_widgets_22memur_chart_2", [<?php foreach ($durusmamemurbazli2 as $durusmamemurbazli) { ?><?= @$durusmamemurbazli->sayi; ?>,
                <?php } ?>], !0)
            }
        }
    }();
    "undefined" != typeof module && (module.exports = KTChartsWidget22memur), KTUtil.onDOMContentLoaded((function () { KTChartsWidget22memur.init() }));
</script>

<script>//Taraf Bazlı Duruşma Grafiği
    var KTChartsWidget22taraf = function () {
        var e = function (e, t, a, l) {
            var r = document.querySelector(t); if (r) {
                parseInt(KTUtil.css(r, "height"));
                var o = {
                    series: a, chart: { fontFamily: "inherit", type: "donut", width: 350 },
                    plotOptions: { pie: { donut: { size: "50%", labels: { value: { fontSize: "12px" } } } } },
                    colors:
                        [
                            KTUtil.getCssVariableValue("--kt-info"),
                            KTUtil.getCssVariableValue("--kt-success"),
                            KTUtil.getCssVariableValue("--kt-primary"),
                            KTUtil.getCssVariableValue("--kt-danger"),
                            KTUtil.getCssVariableValue("--kt-warning"),
                            KTUtil.getCssVariableValue("--kt-dark"),
                            KTUtil.getCssVariableValue("--kt-secondary"),
                            KTUtil.getCssVariableValue("--kt-light"),
                            KTUtil.getCssVariableValue("--kt-white bg-dark")
                        ],
                    stroke: { width: 0 }, labels: [<?php foreach ($durusmatarafbazli2 as $durusmatarafbazli) { ?> "<?= @$durusmatarafbazli->d_taraf; ?>",
                <?php } ?>], legend: { show: !1 }, fill: { type: "false" }
                },
                    i = new ApexCharts(r, o), s = !1, n = document.querySelector(e); !0 === l && (i.render(), s = !0),
                        n.addEventListener("shown.bs.tab", (function (e) { 0 == s && (i.render(), s = !0) }))
            }
        };
        return {
            init: function () {
                e("#kt_chart_widgets_22taraf_tab_3", "#kt_chart_widgets_22taraf_chart_3", [<?php foreach ($durusmatarafbazli2 as $durusmatarafbazli) { ?><?= @$durusmatarafbazli->sayi; ?>,
                <?php } ?>], !0)
            }
        }
    }();
    "undefined" != typeof module && (module.exports = KTChartsWidget22taraf), KTUtil.onDOMContentLoaded((function () { KTChartsWidget22taraf.init() }));
</script>

<script>//Mahkeme Bazlı Duruşma Grafiği
    var KTChartsWidget22mahkeme = function () {
        var e = function (e, t, a, l) {
            var r = document.querySelector(t); if (r) {
                parseInt(KTUtil.css(r, "height"));
                var o = {
                    series: a, chart: { fontFamily: "inherit", type: "donut", width: 350 },
                    plotOptions: { pie: { donut: { size: "50%", labels: { value: { fontSize: "12px" } } } } },
                    colors:
                        [
                            KTUtil.getCssVariableValue("--kt-info"),
                            KTUtil.getCssVariableValue("--kt-success"),
                            KTUtil.getCssVariableValue("--kt-primary"),
                            KTUtil.getCssVariableValue("--kt-danger"),
                            KTUtil.getCssVariableValue("--kt-warning"),
                            KTUtil.getCssVariableValue("--kt-dark"),
                            KTUtil.getCssVariableValue("--kt-secondary"),
                            KTUtil.getCssVariableValue("--kt-light"),
                            KTUtil.getCssVariableValue("--kt-white bg-dark")
                        ],
                    stroke: { width: 0 }, labels: [<?php foreach ($durusmamahkemebazli2 as $durusmamahkemebazli) { ?> "<?= substr(etAyracsizYazdir(@$durusmamahkemebazli->d_mahkeme), 0, 50); ?>",
                <?php } ?>], legend: { show: !1 }, fill: { type: "false" }
                },
                    i = new ApexCharts(r, o), s = !1, n = document.querySelector(e); !0 === l && (i.render(), s = !0),
                        n.addEventListener("shown.bs.tab", (function (e) { 0 == s && (i.render(), s = !0) }))
            }
        };
        return {
            init: function () {
                e("#kt_chart_widgets_22mahkeme_tab_4", "#kt_chart_widgets_22mahkeme_chart_4", [<?php foreach ($durusmamahkemebazli2 as $durusmamahkemebazli) { ?><?= @$durusmamahkemebazli->sayi; ?>,
                <?php } ?>], !0)
            }
        }
    }();
    "undefined" != typeof module && (module.exports = KTChartsWidget22mahkeme), KTUtil.onDOMContentLoaded((function () { KTChartsWidget22mahkeme.init() }));
</script>

<script>//İşlem Bazlı Duruşma İstatistiği
    var KTChartsWidget27 = function () {
        var e = { self: null, rendered: !1 }, t = function (e) {
            var t = document.getElementById("kt_charts_widget_27-islem");
            if (t) {
                var a = KTUtil.getCssVariableValue("--kt-gray-800"), l = KTUtil.getCssVariableValue("--kt-border-dashed-color"), r = {
                    series: [{
                        name: "Toplam",
                        data: [<?php foreach ($durusmaislembazli2 as $durusmaislembazli) { ?>                         <?= @$durusmaislembazli->sayi; ?>,
                            <?php } ?>]
                    }], chart: { fontFamily: "inherit", type: "bar", height: 350, toolbar: { show: !1 } },
                    plotOptions: { bar: { borderRadius: 8, horizontal: !0, distributed: !0, barHeight: 50, dataLabels: { position: "bottom" } } },
                    dataLabels: {
                        enabled: !0, textAnchor: "start", offsetX: 0, formatter: function (e, t) {
                            e *= 1; //1e3 bu şekılde kusurat saglar
                            return wNumb({ thousand: "," }).to(e)
                        }, style: { fontSize: "12px", fontWeight: "400", align: "left" }
                    },
                    legend: { show: !1 }, colors: ["#3E97FF", "#F1416C", "#50CD89", "#FFC700", "#7239EA", "#7233EA"],
                    xaxis: {
                        categories: [<?php foreach ($durusmaislembazli2 as $durusmaislembazli) { ?>    "--        <?= substr(etAyracsizYazdir(@$durusmaislembazli->d_islem), 0, 16); ?>",
                            <?php } ?>],
                        labels: { formatter: function (e) { return e + "" }, style: { colors: a, fontSize: "12px", fontWeight: "600", align: "left" } },
                        axisBorder: { show: !1 }
                    }, yaxis: {
                        labels: {
                            formatter: function (e, t) { return Number.isInteger(e) ? e + " - " + parseInt(100 * e / 18).toString() + "%" : e },
                            style: { colors: a, fontSize: "10px", fontWeight: "600" }, offsetY: 2, align: "left"
                        }
                    }, grid: {
                        borderColor: l, xaxis: { lines: { show: !0 } },
                        yaxis: { lines: { show: !1 } }, strokeDashArray: 4
                    }, tooltip: { style: { fontSize: "12px" }, y: { formatter: function (e) { return e } } }
                };
                e.self = new ApexCharts(t, r), setTimeout((function () { e.self.render(), e.rendered = !0 }), 200)
            }
        };
        return { init: function () { t(e), KTThemeMode.on("kt.thememode.change", (function () { e.rendered && e.self.destroy(), t(e) })) } }
    }();
</script>
<style>
#SvgjsG1356 {}
</style>

<?php

$aylar=array();
for ($zx=1;$zx<=12;$zx++) {
    $aylar[$zx]=getTrAy("",$zx);
}

$durusmaArr=array();
$kararArr=array();
$durusmaTxt=array();
$kararTxt=array();

foreach ($durusmalistesiaylik as $durusmalistesiaylik0) { 
    $ayId=(!empty($durusmalistesiaylik0->ayid))?$durusmalistesiaylik0->ayid:0;
    
    $durusmaArr[$ayId]=$durusmalistesiaylik0->sayi;
}

foreach ($kararlistesiaylik as $kararlistesiaylik0) { 
    $ayId=(!empty($kararlistesiaylik0->ayid))?$kararlistesiaylik0->ayid:0;
    $kararArr[$ayId]=$kararlistesiaylik0->sayi;

}


$durusmaTxt0=array();
$kararTxt0=array();
$aylarTxt0=array();
foreach ($aylar as $ayId=>$ayName) { 
    $did=(!empty($durusmaArr[$ayId]))?$durusmaArr[$ayId]:0;
    $kid=(!empty($kararArr[$ayId]))?$kararArr[$ayId]:0;
    $durusmaTxt0[]=$did;
    $kararTxt0[]=$kid;
    $aylarTxt0[]=$ayName;
   
}
$durusmaTxt=implode(",",$durusmaTxt0);
$kararTxt=implode(",",$kararTxt0);
$aylarTxt="'".implode("','",$aylarTxt0)."'";


?>
<script>//Duruşma - Karar Aylık İstatistiği
    var e = document.getElementById("kt_charts_widget_1_chart-drsma");
    if (e) {
        var t = { self: null, rendered: !1 }, a = function () {
            var a = parseInt(KTUtil.css(e, "height")),
                o = KTUtil.getCssVariableValue("--kt-gray-500"), r = KTUtil.getCssVariableValue("--kt-gray-200"),
                s = {
                    series: [
                        {
                            name: "Duruşma", data: [<?=$durusmaTxt;?>]
                        },
                        {
                            name: "Karar", data: [<?=$kararTxt;?>]
                        }],
                    chart: { fontFamily: "inherit", type: "bar", height: a, toolbar: { show: !1 } },
                    plotOptions: { bar: { horizontal: !1, columnWidth: ["50%"], borderRadius: 4 } },
                    legend: { show: !1 }, dataLabels: { enabled: !1 }, stroke: {
                        show: !0, width: 2,
                        colors: ["transparent"]
                    },
                    xaxis: {
                        categories: [<?=$aylarTxt;?>],
                        axisBorder: { show: !1 }, axisTicks: { show: !1 }, labels: { style: { colors: o, fontSize: "12px" } }
                    },
                    yaxis: { labels: { style: { colors: o, fontSize: "12px" } } }, fill: { opacity: 1 },
                    states: {
                        normal: { filter: { type: "none", value: 0 } }, hover: { filter: { type: "none", value: 0 } },
                        active: { allowMultipleDataPointsSelection: !1, filter: { type: "none", value: 0 } }
                    },
                    tooltip: { style: { fontSize: "12px" }, y: { formatter: function (e) { return " " + e + " Adet" } } },
                    colors: [KTUtil.getCssVariableValue("--kt-primary"), KTUtil.getCssVariableValue("--kt-success")],
                    grid: { borderColor: r, strokeDashArray: 4, yaxis: { lines: { show: !0 } } }
                };
            t.self = new ApexCharts(e, s), t.self.render(), t.rendered = !0
        }; a(),
            KTThemeMode.on("kt.thememode.change", (function () { t.rendered && t.self.destroy(), a() }))
    }
</script>

