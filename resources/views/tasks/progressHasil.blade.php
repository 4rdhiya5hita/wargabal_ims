<!DOCTYPE html>
<html>
<head>
    <title>Progress</title>
</head>
<body>
    <h1>Proses Pemrosesan</h1>
    <div id="progress"></div>

    <script>
        const eventSource = new EventSource('/progress');

        eventSource.onmessage = function (event) {
            const progress = event.data;
            document.getElementById('progress').innerText = `Progress: ${progress}%`;

            // Cek apakah proses sudah selesai
            if (progress >= 100) {
                eventSource.close();
            }
        };
    </script>
</body>
</html>
