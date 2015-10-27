var map = undefined;

function renderPage()
{
    renderRecentIncidentMap(24);
    
    var currentDate = new Date();
    var currentMonthName = currentDate.getShortMonthName();
    var currentYear = currentDate.getFullYear();
    renderCurrentMonthlySummary(currentMonthName, currentYear);
}

function renderRecentIncidentMap(offset) 
{
    var mapOptions = 
    {
        zoom: 12,
        center: new google.maps.LatLng(43.46519, -80.52237)
    };
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    var myLatlng = new google.maps.LatLng(43.46519, -80.52237);
    
    currentTime = new Date().getTime();
    offsetHours = offset * 3600 * 1000;
    sinceTime = currentTime - offsetHours;
    
    dateParam = new Date(sinceTime).toString();
    dateParam = dateParam.replace(/ /g, "+");
    console.log(dateParam);
    
    requestURI = "http://" + window.location.hostname;
    $.getJSON( requestURI + "/incidents/from/" + dateParam, 
        function( response ) 
        {
            // log each key in the response data
            $.each(response, function() {
                var markerPosition = new google.maps.LatLng(this["lat"], this["lon"]);
                var markerPins = createCustomMarker(this["title"]);
                var marker = new google.maps.Marker({
                    position: markerPosition,
                    map: map,
                    title: this["title"],
                    icon: markerPins[0],
                    shadow: markerPins[1]
                });
                
            });
        });
}

function createCustomMarker(title)
{
    var pinColor = getColorFromString(title); //"FE7569";
    var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
        new google.maps.Size(21, 34),
        new google.maps.Point(0,0),
        new google.maps.Point(10, 34));
        
    var pinShadow = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
        new google.maps.Size(40, 37),
        new google.maps.Point(0, 0),
        new google.maps.Point(12, 35));
     
     return [pinImage, pinShadow];
}

function stringHash(str)
{
    var hash = 0, i;
    if (str.length == 0) 
        return hash;
        
    for (i = 0; i < str.length; i++) 
    {
        hash  = ((hash<<5)-hash)+str.charCodeAt(i);
        hash &= 0x7FFFFFFF;
    }
    
    return hash;
}

function getColorFromString(str)
{
    var hash = stringHash(str);
    var r = (hash % 255).toString(16); hash >>= 8;
    var g = (hash % 255).toString(16); hash >>= 8;
    var b = (hash % 255).toString(16); 
    
    formatHexString(2, r);
    formatHexString(2, g);
    formatHexString(2, b);
    
    return r + g + b;
}

function formatHexString(digits, hStr)
{
    if(hStr.length < digits)
    {
        var i, zeros;
        zeros = "";
        for(i = 0; i < digits; i++)
            zeros += "0";
        hStr = zeros + hStr;
    }
    return hStr;
}

Date.prototype.monthNames = 
[
    "January", "February", "March",
    "April", "May", "June",
    "July", "August", "September",
    "October", "November", "December"
];

Date.prototype.getMonthName = function() 
{
    return this.monthNames[this.getMonth()];
};

Date.prototype.getShortMonthName = function () 
{
    return this.getMonthName().substr(0, 3);
};

function renderCurrentMonthlySummary(month, year)
{
    requestURI = "http://" + window.location.hostname + "/incidents/summary/monthly/";
    dateParam = month + "+" + year.toString();
    requestURI += dateParam;
    
    $.getJSON( requestURI, 
        function( response ) 
        {
            var dataset = [];
            
            console.log(response);
            $.each(response, function() 
            {
                dataset.push( {label: this["_id"]["type"], data: this["count"]} );
            });
            
            console.log(dataset);
                        
            $.plot( $("#current-month-summary"), dataset,
                    {
                        series: {
                            pie: {
                                show: true,
                                label: 
                                {
                                    show: true,
                                    formatter: function(label,point){
                                         return(point.percent.toFixed(2) + '%');
                                    }
                                },
                                combine: {
                                    threshold: 0.02
                                }
                            }
                        },
                        legend: {
                            show: true
                        }
                    }
                  );
        });     
}

function renderTrendPlot()
{
   
    var currentTime = new Date().getTime();
    var offsetHours = 7 * 24 * 3600 * 1000;
    sinceTime = currentTime - offsetHours;
    var dateParam = new Date(sinceTime).toString();
    dateParam = dateParam.replace(/ /g, "+");
    
    var requestURI = "http://" + window.location.hostname + "/incidents/summary/type/all/from/" + dateParam;
    console.log(requestURI);
    $.getJSON( requestURI,
        function( response )
        {
            var dataset = [];
            var hashByIncident = [];
                        
            $.each(response, function() 
            {
                var row = [];
                
                var incidentType = this["_id"]["type"];
                $.each(this["dates"], function()
                {
                    var date = new Date(this["sec"] * 1000);
                    var key = new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();
                    
                    var found_index = -1;
                    
                    $.each(row, function(index, value)
                    {
                        if(value)
                        {
                            if(value[0] == key)
                                found_index = index;
                        }
                    });
                    
                    if(found_index != -1)
                        row[found_index][1]++;
                    else row.push([key, 1]);                    
                });
                dataset.push({data: row, label: incidentType});
            });
            console.log(dataset);            
            
            $.plot( $("#seven-day-trend"), dataset,
                    {
                        series: {
                            stack: true,
                            bars: {
                                show: true
                            }
                        },
                        bars: {
                            align: 'center',
                            barWidth: 24 * 3600 * 800,
                            lineWidth: 2,
                            fill:1
                        },
                        xaxis: {
                            mode: "time",
                            tickSize: [1, "day"],
                            axisLabel: 'Date',
                            axisLabelUseCanvas: true,
                            axisLabelFontSizePixels: 16,
                            axisLabelFontFamily: 'Verdana, Arial',
                            axisLabelPadding: 10
                            
                        },
                        yaxis: {
                            axisLabel: 'Incident Count',
                            axisLabelUseCanvas: true,
                            axisLabelFontSizePixels: 16,
                            axisLabelFontFamily: 'Verdana, Arial',
                            axisLabelPadding: 3,
                        },
                        legend: {
                            show: true,
                            noColumns: 4,
                            container: $("#labeler")
                        },
                         grid: {
                            borderWidth: 1,
                            backgroundColor: {
                                colors: ["#EDF5FF", "#ffffff"]
                            }
                        }
                        
                    }
                  );
        });
}
function buttonAction()
{
    triggerDOMId = event.target.id;
    switch(triggerDOMId)
    {
        case "twhrbutton":
            $("#current-month-summary").hide();
            $("#seven-day-trend").hide();            
            $("#labeler").hide();
            if(!map) renderRecentIncidentMap(24);
            $("#map-canvas").show();
            break;
        case "cur_mon_summary":
            $("#map-canvas").hide();
            $("#seven-day-trend").hide();            
            $("#labeler").hide();
            var currentDate = new Date();
            var currentMonthName = currentDate.getShortMonthName();
            var currentYear = currentDate.getFullYear();
            renderCurrentMonthlySummary(currentMonthName, currentYear);
            $("#current-month-summary").show();
            break;
        case "svdaytrend":
           $("#map-canvas").hide();
           $("#current-month-summary").hide();
           renderTrendPlot();
           $("#seven-day-trend").show();
           $("#labeler").show();
    }
}
 
