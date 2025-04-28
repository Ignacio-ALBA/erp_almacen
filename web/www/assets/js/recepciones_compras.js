document.addEventListener('DOMContentLoaded', () => {
    const btnConectarBalanza = document.getElementById('btn_conectar_balanza');
    const pesoBascula = document.getElementById('peso_bascula');
    const btnGenerarQR = document.getElementById('btn_generar_qr');
    const btnGenerarPDF = document.getElementById('btn_generar_pdf');
    let qrCanvas = null; // Variable para almacenar el canvas del QR generado

    // Comprobación de las bibliotecas
    if (typeof QRCode === 'undefined') {
        console.error('La biblioteca QRCode no está disponible. Verifica que se haya importado correctamente.');
        alert('Error: La biblioteca QRCode no está disponible. Contacta al administrador del sistema.');
        return; // Detener la ejecución si QRCode no está presente
    }
    if (typeof window.jspdf === 'undefined') {
        console.error('La biblioteca jsPDF no está disponible. Verifica que se haya importado correctamente.');
        alert('Error: La biblioteca jsPDF no está disponible. Contacta al administrador del sistema.');
        return; // Detener la ejecución si jsPDF no está presente
    }

    // Lógica para conectar con la báscula
    btnConectarBalanza.addEventListener('click', async () => {
        try {
            const port = await navigator.serial.requestPort();
            await port.open({
                baudRate: 9600,
                dataBits: 8,
                stopBits: 1,
                parity: "none",
                flowControl: "none"
            });

            console.log('Conectado al puerto serial.');
            btnConectarBalanza.innerHTML = '<i class="bi bi-check-circle"></i> Conectado';
            btnConectarBalanza.classList.remove('btn-info');
            btnConectarBalanza.classList.add('btn-success');

            const decoder = new TextDecoderStream();
            const inputStream = decoder.readable;
            const reader = inputStream.getReader();

            while (true) {
                const { value, done } = await reader.read();
                if (done) break;
                if (value) pesoBascula.value = value.trim();
            }

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