function getChartColorsArray(e) {
    if (null !== document.getElementById(e)) {
        var r = document.getElementById(e).getAttribute("data-colors");
        if (r) return (r = JSON.parse(r)).map(function (e) {
            var r = e.replace(" ", "");
            if (-1 === r.indexOf(",")) { var t = getComputedStyle(document.documentElement).getPropertyValue(r); return t || r }
            e = e.split(","); return 2 != e.length ? r : "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(e[0]) + "," + e[1] + ")"
        });
        console.warn("data-colors atributes not found on", e)
    }
}
var options, chart, chartDonutBasicColors = getChartColorsArray("store-visits-source"); chartDonutBasicColors &&
    (options = {
        series: [100, 50, 50, 25, 25],
        labels: ["Direct", "Social", "Email", "Other", "Referrals"],
        chart: { height: 333, type: "donut" },
        legend: { position: "bottom" },
        stroke: { show: !1 },
        dataLabels: { dropShadow: { enabled: !1 } },
        colors: chartDonutBasicColors
    },
    (chart = new ApexCharts(document.querySelector("#store-visits-source"), options)).render());