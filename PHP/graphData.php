<!DOCTYPE HTML>
<html>

<head>
    <script>
        window.onload = function() {

            var dps = []; // dataPoints
            
            var chart = new CanvasJS.Chart("chartContainer", {
                    theme: "light2",
                    animationEnabled: true,
                    zoomEnabled: true,
                    title: {
                        text: "Données mesurées (en km/h)"
                    },
                    axisY: {
                        title: "km/h",
                    },
                    axisX: {
                        title: "Date de la mesure",
                    },
                    data: [{
                        type: "line",
                        dataPoints: dps
                    }]
                });

            var xVal = 0;
            var yVal = 10;
            var updateInterval = 10000;
            var dataLength = 100; 

            var updateChart = function(count) {

                count = count || 1;

                for (var j = 0; j < count; j++) {
                    yVal = 15 + Math.round(Math.random() * 10);
                    dps.push({
                        x: xVal,
                        y: yVal
                    });
                    xVal++;
                }

                if (dps.length > dataLength) {
                    dps.shift();
                }

                chart.render();
            };

            var updateDb = function(data){
                
            }

            updateChart(dataLength);
            setInterval(function() {
                updateChart()
            }, updateInterval);

        }
    </script>
</head>

<body>
    <div id="chartContainer" style="height: 300px; width: 100%;"></div>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</body>

</html>