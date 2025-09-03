<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grafik Rembesan</title>
    <style>
        /* Membuat iframe responsive */
        .grafik-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 50%; /* rasio tinggi, bisa diubah sesuai kebutuhan */
        }
        .grafik-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>
    <h2>Grafik Rembesan</h2>
<div class="grafik-container">
    <iframe src="http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1704153600000&to=1735689600000&timezone=browser&panelId=4&__feature.dashboardSceneSolo=true" width="450" height="200" frameborder="0"></iframe>
</div>

</body>
</html>
