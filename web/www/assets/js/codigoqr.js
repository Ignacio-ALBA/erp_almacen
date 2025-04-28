document.addEventListener('DOMContentLoaded', () => {
    const btnConectarBalanza = document.getElementById('btn_conectar_balanza');
    const pesoBascula = document.getElementById('peso_bascula');
    const btnGenerarQR = document.getElementById('btn_generar_qr');
    const btnGenerarPDF = document.getElementById('btn_generar_pdf');

    // Lógica para conectar con la balanza
    btnConectarBalanza.addEventListener('click', async () => {
        try {
            // Solicitar permisos para acceder al puerto serial
            const port = await navigator.serial.requestPort();

            // Verificar si el puerto seleccionado es "COM4"
            const portInfo = port.getInfo();
            const isCOM4 = portInfo?.usbProductId === 4 || portInfo?.usbVendorId === 4; // Ajustar según cómo se identifique COM4 en tu entorno

            if (!isCOM4) {
                alert('Seleccione el puerto COM4 para conectar la balanza por favor.');
                return;
            }

            // Configurar el puerto serial
            await port.open({
                baudRate: 9600,
                dataBits: 8,
                stopBits: 1,
                parity: "none",
                flowControl: "none"
            });

            const decoder = new TextDecoderStream();
            const inputDone = port.readable.pipeTo(decoder.writable);
            const inputStream = decoder.readable;

            const reader = inputStream.getReader();

            console.log('Conectado al puerto serial.');

            // Cambiar el estado del botón para indicar conexión activa
            btnConectarBalanza.innerHTML = '<i class="bi bi-check-circle"></i> Conectado';
            btnConectarBalanza.classList.remove('btn-info');
            btnConectarBalanza.classList.add('btn-success');

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
                document.body.appendChild(canvas); // Si necesitas visualizar el QR
                alert('Código QR generado con el peso actual.');
            }
        });
    });

    // Lógica para generar el archivo PDF con el Código QR
    btnGenerarPDF.addEventListener('click', () => {
        const pesoActual = pesoBascula.value || '0.00';
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();

        QRCode.toCanvas(pesoActual, { width: 200 }, (error, canvas) => {
            if (error) {
                console.error('Error al generar el QR:', error);
                alert('No se pudo generar el archivo PDF. Inténtalo de nuevo.');
            } else {
                const imgData = canvas.toDataURL('image/png');
                pdf.text('Código QR para el peso actual', 10, 10);
                pdf.addImage(imgData, 'PNG', 10, 20, 50, 50);
                pdf.save('codigo_qr_peso.pdf');
            }
        });
    });
});