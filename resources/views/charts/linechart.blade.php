<style type="text/css">
    ${demo.css}
</style>
<script type="text/javascript">
$(function () {
    $('#container').highcharts({
        title: {
            text: 'مقارنة الدعاوى المفصولة',
            x: -20 //center
        },
        xAxis: {
            categories: ['Sep', 'Oct', 'Nov', 'Dec','Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug']
        },
        yAxis: {
            title: {
                text: 'مفصول'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ''
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: 
            <?php
            $array = array();
            foreach ($line_chart as $speciality => $info)
            {
                $array[] = [ "name" => $info["name"], "data" => isset($info["data"]) ? array_values($info["data"]) : "" ];
            }
            echo( json_encode($array) );
            ?>

    });
});
</script>
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
