$(function () {
    var start = moment().subtract(6, 'days');
    var end = moment();
    var chart;

    function visitor(start, end) {
        $('#visitor-date-range span').html(start.format('DD MMM') + ' - ' + end.format('DD MMM'));

        $.ajax({
            url: getVisitorUrl,
            type: 'POST',
            data: {
                start: start.format('DD MMM Y'),
                end: end.format('DD MMM Y'),
            },
            success: function (result) {
                // console.log(result);

                if (!chart) {
                    chart = new ApexCharts(document.querySelector("#visitor-chart"), {
                        series: [{
                            name: "Visitors",
                            data: result.dateWiseVisitorCount,
                        }],
                        chart: {
                            type: 'area',
                            height: 250,
                            fontFamily: 'inherit',
                            parentHeightOffset: 0,
                            zoom: { enabled: false },
                            toolbar: { show: false, },
                        },
                        xaxis: {
                            categories: result.dateWise,
                            tooltip: { enabled: false },
                            axisBorder: { show: false },
                        },
                        stroke: {
                            width: 3,
                            lineCap: "round",
                            curve: "smooth",
                        },
                        colors: ["#FF4F99"],
                        grid: { show: false },
                        yaxis: { show: false },
                        dataLabels: { enabled: false, },
                    });
                    chart.render();
                } else {
                    chart.updateSeries([{
                        name: "Visitors",
                        data: result.dateWiseVisitorCount,
                    }]);
                    chart.updateOptions({
                        xaxis: {
                            categories: result.dateWise
                        }
                    });
                }
            }
        });
    }

    $('#visitor-date-range').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, visitor);

    visitor(start, end);
});
