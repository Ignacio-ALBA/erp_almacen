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

    // Crear el PDF en orientación vertical (portrait)
    const pdf = new jsPDF({
        orientation: 'landscape', // Orientación horizontal
        unit: 'pt',
        format: [15 * 28.35, 9 * 28.35] // Tamaño de página de ancho 15 cm por 9 de largo
    });

    const pageWidth = pdf.internal.pageSize.getWidth(); // Ancho de la página
    const pageHeight = pdf.internal.pageSize.getHeight(); // Alto de la página

    const imgData = qrCanvas.toDataURL('image/png'); // Convertir el canvas a imagen

    // Dimensiones del QR
    const qrWidth = 100; // Ancho del QR en puntos
    const qrHeight = 100; // Alto del QR en puntos
    const qrX = 50; // Posición X (a la izquierda)
    const qrY = 150; // Posición Y (un poco más arriba de la información)

    // Encabezado "FORCIP"
    pdf.setFont('helvetica', 'bold');
    pdf.setFontSize(25); // Tamaño de fuente grande para el encabezado
    const titleText = 'FORCIP';
    const titleX = (pageWidth - pdf.getTextWidth(titleText)) / 2; // Centrar el encabezado horizontalmente
    const titleY = 40; // Margen superior
    pdf.text(titleText, titleX, titleY);

    // Subtítulo "Supersaco de polipropileno negro"
    pdf.setFontSize(15);
    pdf.setTextColor(255, 255, 255); // Configurar el color del texto a blanco
    pdf.setFillColor(0, 0, 0); // Fondo negro 
    //linea negra que cubra todo
    pdf.rect(0, 0, pageWidth, 5, 'F');
    const subtitleText = 'Supersaco de polipropileno negro';
    const subtitleX = (pageWidth - pdf.getTextWidth(subtitleText)) / 2; // Centrar el subtítulo horizontalmente
    const subtitleY = titleY + 50; // Debajo del encabezado
    pdf.rect(subtitleX - 10, subtitleY - 25, pdf.getTextWidth(subtitleText) + 20, 40, 'F'); // Dibujar un rectángulo negro
    pdf.text(subtitleText, subtitleX, subtitleY);

    // Fecha y hora
    const currentDate = new Date();
    const date = currentDate.toLocaleDateString(); // Obtener fecha en formato local
    const time = currentDate.toLocaleTimeString(); // Obtener hora en formato local
    pdf.setTextColor(0, 0, 0); // Cambiar color de texto a negro
    pdf.setFontSize(15);

    const dateText = `Fecha: ${date}`;
    const timeText = `Hora: ${time}`;
    const dateX = qrX + qrWidth + 20; // A la derecha del QR
    const dateY = qrY + 20; // Alineado con la parte superior del QR
    pdf.text(dateText, dateX, dateY);

    const timeY = dateY + 30; // Debajo de la fecha
    pdf.text(timeText, dateX, timeY);

    // Peso en negritas
    pdf.setFont('helvetica', 'bold');
    const peso = pesoBascula.value || '0.00'; // Obtener el peso del input
    const pesoText = `Peso: ${peso} kg`;
    const pesoY = timeY + 30; // Debajo de la hora
    pdf.text(pesoText, dateX, pesoY);

    // Insertar el QR en la parte izquierda
    pdf.addImage(imgData, 'PNG', qrX, qrY, qrWidth, qrHeight);

    // Descargar el archivo PDF
    pdf.save('codigo_qr_peso_vertical.pdf');
});
}); 