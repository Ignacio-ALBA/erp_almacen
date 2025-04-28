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
    
    // Crear el PDF en formato horizontal (landscape)
    const pdf = new jsPDF({
        orientation: 'landscape', // Cambiar la orientación del PDF a horizontal
        unit: 'pt',
        format: 'letter' // Tamaño de página estándar (puedes ajustarlo si necesitas)
    });

    const imgData = qrCanvas.toDataURL('image/png'); // Convertir el canvas a imagen

    // Dimensiones del QR ampliado para que sea más grande
    const qrWidth = 150; // Ancho del QR en puntos
    const qrHeight = 150; // Alto del QR en puntos
    const qrX = pdf.internal.pageSize.getWidth() - qrWidth - 50; // Posición X (a la derecha)
    const qrY = 50; // Posición Y (margen superior)

    // Insertar el QR en el PDF
    pdf.addImage(imgData, 'PNG', qrX, qrY, qrWidth, qrHeight);

    // Estilo de fuente para encabezados
    pdf.setFont('helvetica', 'bold');
    pdf.setFontSize(18);

    // Encabezado "FORCIP"
    pdf.text('FORCIP', 50, 50); // Posición (X, Y)

    // Subtítulo "Supersaco de polipropileno negro"
    pdf.setFontSize(14);
    pdf.setTextColor(255, 255, 255); // Configurar el color del texto a blanco
    pdf.setFillColor(0, 0, 0); // Fondo negro
    pdf.rect(50, 70, 400, 20, 'F'); // Dibujar un rectángulo negro para el texto
    pdf.text('Supersaco de polipropileno negro', 55, 85); // Texto dentro del rectángulo

    // Fecha y hora
    const currentDate = new Date();
    const date = currentDate.toLocaleDateString(); // Obtener fecha en formato local
    const time = currentDate.toLocaleTimeString(); // Obtener hora en formato local
    pdf.setTextColor(0, 0, 0); // Cambiar color de texto a negro
    pdf.setFontSize(12);
    pdf.text(`Fecha: ${date}`, 50, 120); // Mostrar la fecha
    pdf.text(`Hora: ${time}`, 50, 140); // Mostrar la hora

    // Peso en negritas
    pdf.setFont('helvetica', 'bold');
    pdf.setFontSize(14);
    const peso = pesoBascula.value || '0.00'; // Obtener el peso del input
    pdf.text(`Peso: ${peso} kg`, 50, 160); // Mostrar el peso

    // Descargar el archivo PDF
    pdf.save('codigo_qr_peso.pdf');
});
}); 