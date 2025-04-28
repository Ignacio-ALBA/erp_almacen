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

     // Lógica para generar el archivo PDF con el Código QR para impresión en la Zebra ZD220
     btnGenerarPDF.addEventListener('click', () => {
        if (!qrCanvas) {
            alert('Primero debes generar el código QR.');
            return;
        }

        const { jsPDF } = window.jspdf;

        // Configurando dimensiones del papel: 104mm x 50.8mm (en puntos: 1 mm ≈ 2.83465 puntos)
        const pdfWidth = 104 * 2.83465; // 104 mm a puntos
        const pdfHeight = 50.8 * 2.83465; // 50.8 mm a puntos
        const pdf = new jsPDF({
            unit: 'pt', // Puntos como unidad
            format: [pdfWidth, pdfHeight] // Formato personalizado
        });

        const imgData = qrCanvas.toDataURL('image/png'); // Convertir el canvas a imagen
    // Ajustar la imagen del QR para que sea más pequeña y centrada en el ticket
    const qrWidth = 80; // Ajustar el ancho del QR en puntos
    const qrHeight = 80; // Ajustar el alto del QR en puntos
    const xOffset = (pdfWidth - qrWidth) / 2; // Centrar horizontalmente
    const yOffset = (pdfHeight - qrHeight) / 2; // Centrar verticalmente

    pdf.addImage(imgData, 'PNG', xOffset, yOffset, qrWidth, qrHeight);
        // Descargar el archivo PDF optimizado para el ticket
        pdf.save('codigo_qr_ticket.pdf');
    });
});