document.addEventListener('DOMContentLoaded', () => {
    const { PDFDocument, rgb, StandardFonts, degrees } = PDFLib;
    const btnConectarBalanza = document.getElementById('btn_conectar_balanza');
    const pesoBascula = document.getElementById('peso_bascula');
    const btnGenerarQR = document.getElementById('btn_generar_qr');
    const btnGenerarPDF = document.getElementById('btn_generar_pdf');
    let qrCanvas = null; // Variable para almacenar el canvas del QR generado
    // Usar la variable global para la URL del logotipo
    const logoUrl = window.logoUrl; // URL definida en recepciones_compras_script.php
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
            const inputDone = port.readable.pipeTo(decoder.writable);
            const inputStream = decoder.readable;
            const reader = inputStream.getReader();

            while (true) {
                const { value, done } = await reader.read();
                if (done) {
                    console.log('Conexión cerrada.');
                    break;
                }
                if (value) {
                    pesoBascula.value = value.trim();
                }
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
    btnGenerarPDF.addEventListener('click', async () => {
        if (!qrCanvas) {
            alert('Primero debes generar el código QR.');
            return;
        }

        const pdfDoc = await PDFDocument.create();
        const page = pdfDoc.addPage([425.25, 283.5]); // Formato horizontal (15 cm x 10 cm en puntos)

        // Rotar el contenido 90 grados para simular un diseño vertical
        const rotation = degrees(90);
        page.setRotation(rotation);

        const { width, height } = page.getSize();
 // Cargar el logotipo desde la URL
 try {
    const logoImageBytes = await fetch(logoBase64).then((res) => res.arrayBuffer());
    const logoImageEmbed = await pdfDoc.embedJpg(logoImageBytes);

    // Dibujar el logotipo en la esquina superior izquierda
    const logoWidth = 80; // Ancho del logotipo
    const logoHeight = 80; // Alto del logotipo
    page.drawImage(logoImageEmbed, {
        x: 10, // Margen izquierdo
        y: height - logoHeight - 10, // Margen superior
        width: logoWidth,
        height: logoHeight,
    });
} catch (error) {
    console.error('No se pudo cargar el logotipo:', error.message);
}

        // Dibujar la barra negra vertical
        //page.drawRectangle({
           // x: 0,
           // y: 0,
           // width: 50, // Ancho de la barra negra
           // height: height, // Altura completa
            //color: rgb(0, 0, 0),
       // });

        const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
        const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold);

        // Título "FORCIP"
        page.drawText('FORCIP', {
            x: (width / 2) - 70, // Centrar horizontalmente
            y: height - 80, // Cerca del borde superior
            size: 40,
            font: fontBold,
            color: rgb(0, 0, 0),
        });

        // Dibujar la barra negra justo después de "FORCIP"
        page.drawRectangle({
            x: 0, // Posición X al inicio de la página
            y: height - 130, // Posición Y justo después del título
            width: width, // Ancho total de la página
            height: 30, // Alto del rectángulo negro
            color: rgb(0, 0, 0), // Color negro
        });

        page.drawText('      Supersaco de polipropileno negro', {
            x: 14, // Texto alineado dentro del rectángulo
            y: height - 120, // Posición del texto
            size: 20, // Tamaño del texto
            font: fontBold,
            color: rgb(1, 1, 1), // Texto en blanco
        });

        // Fecha, hora y peso
        const currentDate = new Date();
        const date = currentDate.toLocaleDateString();
        const time = currentDate.toLocaleTimeString();
        const peso = pesoBascula.value || '0.00';

        page.drawText(`Fecha: ${date}`, {
            x: 60,
            y: height - 170,
            size: 23, // Tamaño de letra estándar
            font: font,
            color: rgb(0, 0, 0),
        });
        page.drawText(`Hora: ${time}`, {
            x: 60,
            y: height - 195,
            size: 23,
            font: font,
            color: rgb(0, 0, 0),
        });
        page.drawText(`Peso: ${peso} kg`, {
            x: 60,
            y: height - 228,
            size: 35,
            font: fontBold, // En negritas
            color: rgb(0, 0, 0),
        });

        // Convertir el QR Canvas a una imagen y agregarlo al PDF
        const qrImage = qrCanvas.toDataURL('image/png');
        const qrImageBytes = await fetch(qrImage).then((res) => res.arrayBuffer());
        const qrImageEmbed = await pdfDoc.embedPng(qrImageBytes);

        const qrSize = 120; // Mantener tamaño del QR
        page.drawImage(qrImageEmbed, {
            x: width - qrSize - 10, // Margen derecho ajustado
            y: 30, // Margen inferior
            width: qrSize,
            height: qrSize,
        });

        const pdfBytes = await pdfDoc.save();

        // Descargar el archivo PDF
        const blob = new Blob([pdfBytes], { type: 'application/pdf' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'codigo_qr_peso_rotado.pdf';
        link.click();
    });
});