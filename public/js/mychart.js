am4core.useTheme(am4themes_dark);


var main = function(response, chart){
  chartData = [];
  var countData = response.data.length;
  for (var i = 0; i < countData; i++) {

    var datemain = response.data[i].date.match(/[0-9]{2,4}/gm);

    var year = datemain[0];
    var month = datemain[1]-1;
    var day = datemain[2];
    var hour = datemain[3];
    var minute = datemain[4];
    var sekunde = datemain[5];

    
    let date = new Date(year,month,day);

    date.setHours(hour);
    date.setMinutes(minute);
    date.setSeconds(sekunde);

  //  console.log(response.name[i].match(/[0-9]{2,4}/gm));

      chartData.push({
        date:date,
        pampTemp: response.data[i].pamp_temp,
        pampVoltage: response.data[i].pamp_voltage,
        systemVoltage: response.data[i].system_voltage,
        batteryVoltage: response.data[i].battery_voltage
    });
  }
  // console.log(chartData)

  chart.data = chartData;
  // chart.numberFormatter.numberFormat = "#.###";

  // Create axes
  var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
 
  dateAxis.tooltipDateFormat = "yyyy, d MMMM";

  
  var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());


  // valueAxis.title.text = 'Temperature, K';
  valueAxis.renderer.labels.template.disabled = true;
  valueAxis.tooltip.disabled = true;


  // xAxis.dateFormatter = new am4core.DateFormatter();
  // xAxis.dateFormatter.dateFormat = "MM-dd";

  // Create series
  var series1 = chart.series.push(new am4charts.LineSeries());
  series1.dataFields.valueY = "pampTemp";
  series1.dataFields.dateX = "date";
  series1.name = "Температура";
  series1.tooltipText = "{pampTemp} ℃";
  series1.tooltip.pointerOrientation = "vertical";
  
  var series2 = chart.series.push(new am4charts.LineSeries());
  series2.dataFields.valueY = "pampVoltage";
  series2.dataFields.dateX = "date";
  series2.name = "Напряжение";
  // series2.stroke = chart.colors.getIndex(6);
  series2.stroke = "green";
  series2.tooltipText = "{pampVoltage} V";
  series2.tooltip.pointerOrientation = "vertical";

  var series3 = chart.series.push(new am4charts.LineSeries());
  series3.dataFields.valueY = "systemVoltage";
  series3.dataFields.dateX = "date";
  series3.name = "Напряжение системы";
  // series2.stroke = chart.colors.getIndex(6);
  series3.stroke = "blue";
  series3.tooltipText = "{systemVoltage}";
  series3.tooltip.pointerOrientation = "vertical";
  

  var series4 = chart.series.push(new am4charts.LineSeries());
  series4.dataFields.valueY = "batteryVoltage";
  series4.dataFields.dateX = "date";
  series4.name = "Напряжение батареи";
  // series2.stroke = chart.colors.getIndex(6);
  series4.stroke = "red";
  series4.tooltipText = "{batteryVoltage} V";
  series4.tooltip.pointerOrientation = "vertical";

  var bullet1 = series1.bullets.push(new am4charts.CircleBullet());
  bullet1.circle.strokeWidth = 2;
  bullet1.circle.radius = 4;
  bullet1.circle.fill = am4core.color("#fff");
  series1.tooltip.pointerOrientation = "vertical";

  var bullet2 = series2.bullets.push(new am4charts.CircleBullet());
  bullet2.circle.strokeWidth = 2;
  bullet2.circle.radius = 4;
  bullet2.circle.fill = am4core.color("red");
  series2.tooltip.pointerOrientation = "vertical";

  var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
  bullet3.circle.strokeWidth = 2;
  bullet3.circle.radius = 4;
  bullet3.circle.fill = am4core.color("green");
  series3.tooltip.pointerOrientation = "vertical";

  var bullet4 = series4.bullets.push(new am4charts.CircleBullet());
  bullet4.circle.strokeWidth = 2;
  bullet4.circle.radius = 4;
  bullet4.circle.fill = am4core.color("blue");
  series4.tooltip.pointerOrientation = "vertical";

  chart.legend = new am4charts.Legend();

  chart.cursor = new am4charts.XYCursor();
  chart.cursor.lineY.strokeOpacity = 0;

  // chart.cursor.snapToSeries = series;
  // chart.cursor.xAxis = dateAxis;
  
  //chart.scrollbarY = new am4core.Scrollbar();
  chart.scrollbarX = new am4core.Scrollbar();

  chart.scrollbarY = new am4core.Scrollbar();

};

var jsonData = $.ajax({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  },
  type: 'POST',
  url: '/send',
  data: {},
  success:function(response) {
    var chart = am4core.create("chartdiv", am4charts.XYChart);

    main(response, chart);
  }

});

$("#click").click(function(){

  var catalogId = $('input[name=catalogId]').val();

  $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'POST',
    url: '/search',
    data: {catalogId: catalogId},
    success:function(response) {
      $("chartdiv").remove();
      $("py-4").append('<div id="chartdiv"></div>');
      var chart = am4core.create("chartdiv", am4charts.XYChart);

      main(response, chart);
    }
  
  });

});
