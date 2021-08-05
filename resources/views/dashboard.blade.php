@extends('layouts.app')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-fw fa-bar-chart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">جداولي اليوم</span>
                    <span class="info-box-number">{{$records_today}}</span>
                    <div class="progress"><div class="progress-bar" style="width: {{$records_today*100/($records_year==0?1:$records_year)}}%"></div></div>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-fw fa-bar-chart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">جداولي للسنة</span>
                    <span class="info-box-number">{{$records_year}}</span>
                    <div class="progress"><div class="progress-bar" style="width: {{$records_year*100/($records_year_all==0?1:$records_year_all)}}%"></div></div>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-fw fa-bar-chart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">جداول اليوم</span>
                    <span class="info-box-number">{{$records_today_all}}</span>
                    <div class="progress"><div class="progress-bar" style="width: {{$records_today_all*100/($records_year_all==0?1:$records_year_all)}}%"></div></div>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-fw fa-bar-chart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">جداول السنة</span>
                    <span class="info-box-number">{{$records_year_all}}</span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                </div><!-- /.info-box-content -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-fw fa-bar-chart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">أحكامي لليوم</span>
                    <span class="info-box-number">{{$judgments_today}}</span>
                    <div class="progress"><div class="progress-bar" style="width: {{$judgments_today*100/($judgments_year==0?1:$judgments_year)}}%"></div></div>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-fw fa-bar-chart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">أحكامي للسنة</span>
                    <span class="info-box-number">{{$judgments_year}}</span>
                    <div class="progress"><div class="progress-bar" style="width: {{$judgments_year*100/($judgments_year_all==0?1:$judgments_year_all)}}%"></div></div>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-fw fa-bar-chart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">أحكام اليوم</span>
                    <span class="info-box-number">{{$judgments_today_all}}</span>
                    <div class="progress"><div class="progress-bar" style="width: {{$judgments_today_all*100/($judgments_year_all==0?1:$judgments_year_all)}}%"></div></div>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-fw fa-bar-chart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">أحكام السنة</span>
                    <span class="info-box-number">{{$judgments_year_all}}</span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                </div><!-- /.info-box-content -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">جدول الورود</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped"  id="dynamic_table">
                    <thead>
                        <tr>
                            <th></th>
                            @for ($i = 0; $i < 12; $i++)
                            <th>{{$months[$i]}}</th>
                            @endfor
                        </tr>
                    </thead>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
    <div class="row">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">مقارنة الدعاوى المفصولة</h3>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="lineChart" style="height:250px"></canvas>
                    <div class="chart_legend"></div>
                </div>
            </div><!-- /.box-body -->
        </div> 


        <script src="{{asset('LTE/plugins/chartjs/Chart.min.js')}}"></script>
        <script>
var ChartData = {
    labels: ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
    datasets: <?php
$colors = [["rgb(144, 237, 125)"], ["rgb(247, 163, 92)"], ["rgb(124, 181, 236)"], ["rgb(67, 67, 72)"]];
$array = array();
$index = 0;

foreach ($line_chart as $speciality => $info)
{
    $array[] = [
        "label" => $info['name'],
        "fillColor" => $colors[$index],
        "lineColor" => $colors[$index],
        "strokeColor" => $colors[$index],
        "pointColor" => $colors[$index],
        "pointStrokeColor" => "#c1c7d1",
        "pointHighlightFill" => "#fff",
        "pointHighlightStroke" => "rgba(220,220,220,1)",
        "data" => isset($info["data"]) ? array_values($info["data"]) : ""
    ];
    $index++;
}
echo( json_encode($array) );
?>

};

var ChartOptions = {
    //Boolean - If we should show the scale at all
    showScale: true,
    //Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines: true,
    //String - Colour of the grid lines
    scaleGridLineColor: "rgba(0,0,0,.05)",
    //Number - Width of the grid lines
    scaleGridLineWidth: 1,
    //Boolean - Whether to show horizontal lines (except X axis)
    scaleShowHorizontalLines: true,
    //Boolean - Whether to show vertical lines (except Y axis)
    scaleShowVerticalLines: true,
    //Boolean - Whether the line is curved between points
    bezierCurve: true,
    //Number - Tension of the bezier curve between points
    bezierCurveTension: 0.3,
    //Boolean - Whether to show a dot for each point
    pointDot: false,
    //Number - Radius of each point dot in pixels
    pointDotRadius: 4,
    //Number - Pixel width of point dot stroke
    pointDotStrokeWidth: 1,
    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
    pointHitDetectionRadius: 20,
    //Boolean - Whether to show a stroke for datasets
    datasetStroke: true,
    //Number - Pixel width of dataset stroke
    datasetStrokeWidth: 2,
    //Boolean - Whether to fill the dataset with a color
    datasetFill: true,
    //String - A legend template
    legendTemplate: "\
            <ul class=\"<%=name.toLowerCase()%>-legend\" style=\"list-style:none;position:absolute;top:0\">\n\
                <% for (var i=0; i<datasets.length; i++){%>\n\
                    <li style=\"color:<%=ChartData.datasets[i].fillColor%>\">\n\
                        <i class=\"fa fa-fw fa-dot-circle-o\"></i>\n\
                        <%if(datasets[i].label){%>\n\
                            <b style=\"color:black\"><%=datasets[i].label%></b>\n\
                        <%}%>\n\
                    </li>\n\
                <%}%>\n\
            </ul>",
    //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: true,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true
};
ChartOptions.datasetFill = false;
var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
var lineChart = new Chart(lineChartCanvas).Line(ChartData, ChartOptions);
//then you just need to generate the legend
var legend = lineChart.generateLegend();
//and append it to your page somewhere
$('.chart_legend').html(legend);
        </script>
    </div>
    
    <script>
        $(function () {
            $("#temp_table").DataTable();
            $("#dynamic_table").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": $.fn.dataTable.pipeline( {
                    url: "{{route('register.data')}}",
                    pages: 5 // number of pages to cache
                } ),
                "language": {
                    "url": "{{asset('LTE/plugins/datatables/language/Arabic.json')}}"
                }
            });


        });
        
        $.fn.dataTable.Api.register( 'clearPipeline()', function () {
            return this.iterator( 'table', function ( settings ) {
                settings.clearCache = true;
            } );
        } );
        
        $.fn.dataTable.pipeline = function ( opts ) {
            // Configuration options
            var conf = $.extend( {
                pages: 5,     // number of pages to cache
                url: '',      // script url
                data: null,   // function or object with parameters to send to the server
                              // matching how `ajax.data` works in DataTables
                method: 'GET' // Ajax HTTP method
            }, opts );

            // Private variables for storing the cache
            var cacheLower = -1;
            var cacheUpper = null;
            var cacheLastRequest = null;
            var cacheLastJson = null;

            return function ( request, drawCallback, settings ) {
                var ajax          = false;
                var requestStart  = request.start;
                var drawStart     = request.start;
                var requestLength = request.length;
                var requestEnd    = requestStart + requestLength;

                if ( settings.clearCache ) {
                    // API requested that the cache be cleared
                    ajax = true;
                    settings.clearCache = false;
                }
                else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
                    // outside cached data - need to make a request
                    ajax = true;
                }
                else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                          JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                          JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
                ) {
                    // properties changed (ordering, columns, searching)
                    ajax = true;
                }

                // Store the request for checking next time around
                cacheLastRequest = $.extend( true, {}, request );

                if ( ajax ) {
                    // Need data from the server
                    if ( requestStart < cacheLower ) {
                        requestStart = requestStart - (requestLength*(conf.pages-1));

                        if ( requestStart < 0 ) {
                            requestStart = 0;
                        }
                    }

                    cacheLower = requestStart;
                    cacheUpper = requestStart + (requestLength * conf.pages);

                    request.start = requestStart;
                    request.length = requestLength*conf.pages;

                    // Provide the same `data` options as DataTables.
                    if ( $.isFunction ( conf.data ) ) {
                        // As a function it is executed with the data object as an arg
                        // for manipulation. If an object is returned, it is used as the
                        // data object to submit
                        var d = conf.data( request );
                        if ( d ) {
                            $.extend( request, d );
                        }
                    }
                    else if ( $.isPlainObject( conf.data ) ) {
                        // As an object, the data given extends the default
                        $.extend( request, conf.data );
                    }

                    settings.jqXHR = $.ajax( {
                        "type":     conf.method,
                        "url":      conf.url,
                        "data":     request,
                        "dataType": "json",
                        "cache":    false,
                        "success":  function ( json ) {
                            cacheLastJson = $.extend(true, {}, json);

                            if ( cacheLower != drawStart ) {
                                json.data.splice( 0, drawStart-cacheLower );
                            }
                            if ( requestLength >= -1 ) {
                                json.data.splice( requestLength, json.data.length );
                            }

                            drawCallback( json );
                        }
                    } );
                }
                else {
                    json = $.extend( true, {}, cacheLastJson );
                    json.draw = request.draw; // Update the echo for each response
                    json.data.splice( 0, requestStart-cacheLower );
                    json.data.splice( requestLength, json.data.length );

                    drawCallback(json);
                }
            }
        };
    </script>
</section>
@endsection