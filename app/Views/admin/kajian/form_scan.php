<!DOCTYPE html>
<html>
<head>
    <title>Scan QR untuk Hadir</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        #reader {
            width: 300px;
            margin: auto;
            padding-top: 20px;
        }
        #camera-select {
            display: block;
            margin: 10px auto;
            padding: 5px;
            font-size: 16px;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Silakan Scan QR</h2>
    <select id="camera-select"></select>
    <div id="reader"></div>

    <script>
        let scanning = false;
        const html5QrCode = new Html5Qrcode("reader");
        const cameraSelect = document.getElementById("camera-select");

        function onScanSuccess(decodedText, decodedResult) {
            if (scanning) return;
            scanning = true;

            console.log("QR Terdeteksi:", decodedText);

            if (decodedText.startsWith("http://") || decodedText.startsWith("https://")) {
                window.location.href = decodedText;
            } else {
                alert("QR bukan URL yang valid:\n" + decodedText);
                scanning = false;
            }
        }

        function onScanError(errorMessage) {
            // debug error jika perlu
        }

        function startCamera(cameraId) {
            html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanError
            ).then(() => {
                console.log("Scanner dimulai dengan kamera:", cameraId);
            }).catch(err => {
                console.error("Gagal memulai kamera:", err);
                alert("Tidak dapat memulai kamera");
            });
        }

        function switchCamera(cameraId) {
            html5QrCode.stop().then(() => {
                scanning = false;
                startCamera(cameraId);
            }).catch(err => {
                console.error("Gagal berhenti dari kamera lama:", err);
                alert("Gagal beralih kamera");
            });
        }

        Html5Qrcode.getCameras().then(devices => {
            if (devices.length === 0) {
                alert("Kamera tidak ditemukan.");
                return;
            }

            // Isi dropdown kamera
            devices.forEach((device, index) => {
                const option = document.createElement("option");
                option.value = device.id;
                option.text = device.label || `Camera ${index + 1}`;
                cameraSelect.appendChild(option);
            });

            // Default kamera belakang jika ada
            const defaultCamera = devices.find(d =>
                d.label.toLowerCase().includes('back') ||
                d.label.toLowerCase().includes('environment')
            ) || devices[0];

            cameraSelect.value = defaultCamera.id;
            startCamera(defaultCamera.id);

            // Event saat ganti kamera
            cameraSelect.addEventListener("change", () => {
                const selectedId = cameraSelect.value;
                switchCamera(selectedId);
            });

        }).catch(err => {
            console.error("Gagal mengakses kamera:", err);
            alert("Tidak dapat mendeteksi kamera.");
        });
    </script>
</body>
</html>
