document.addEventListener('DOMContentLoaded', () => {
    const btnConectarBalanza = document.getElementById('btn_conectar_balanza');
    const pesoBascula = document.getElementById('peso_bascula');
    const btnGenerarQR = document.getElementById('btn_generar_qr');
    const btnGenerarPDF = document.getElementById('btn_generar_pdf');
    let qrCanvas = null; // Variable para almacenar el canvas del QR generado


       // Lógica para conectar con la báscula
       btnConectarBalanza.addEventListener('click', async () => {
        try {
            // Solicitar permisos para acceder al puerto serial
            const port = await navigator.serial.requestPort();

            // Configurar el puerto serial
            await port.open({
                baudRate: 9600,
                dataBits: 8,
                stopBits: 1,
                parity: "none",
                flowControl: "none"
            });

            console.log('Conectado al puerto serial.');

            // Cambiar el estado del botón para indicar conexión activa
            btnConectarBalanza.innerHTML = '<i class="bi bi-check-circle"></i> Conectado';
            btnConectarBalanza.classList.remove('btn-info');
            btnConectarBalanza.classList.add('btn-success');

            const decoder = new TextDecoderStream();
            const inputDone = port.readable.pipeTo(decoder.writable);
            const inputStream = decoder.readable;

            const reader = inputStream.getReader();

            // Leer continuamente desde el puerto
            while (true) {
                const { value, done } = await reader.read();
                if (done) {
                    console.log('Conexión cerrada.');
                    break;
                }
                if (value) {
                    // Actualizar el valor del input con los datos recibidos
                    pesoBascula.value = value.trim();
                }
            }

            // Liberar el lector una vez que termine
            await reader.releaseLock();
        } catch (err) {
            console.error('Error:', err.message);
            alert('Error al conectar con la balanza: ' + err.message);
        }
    });
    // Lógica para generar el Código QR
    btnGenerarQR.addEventListener('click', () => {
        const pesoActual = pesoBascula.value || '0.00';

        QRCode.toCanvas(pesoActual, { width: 200 }, (error, canvas) => {
            if (error) {
                console.error('Error al generar el QR:', error);
                alert('No se pudo generar el código QR. Inténtalo de nuevo.');
            } else {
                qrCanvas = canvas; // Guardar el canvas generado
                document.body.appendChild(canvas); // Mostrar el QR en la página (opcional)
                alert('Código QR generado con el peso actual.');
            }
        });
    });

    // Lógica para generar el archivo PDF con el Código QR
    btnGenerarPDF.addEventListener('click', () => {
        if (!qrCanvas) {
            alert('Primero debes generar el código QR.');
            return;
        }

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();
        const imgData = qrCanvas.toDataURL('image/png'); // Convertir el canvas a imagen

        pdf.text('Código QR para el peso actual', 10, 10);
        pdf.addImage(imgData, 'PNG', 10, 20, 50, 50); // Insertar la imagen del QR en el PDF
        pdf.save('codigo_qr_peso.pdf'); // Descargar el archivo PDF
    });
});