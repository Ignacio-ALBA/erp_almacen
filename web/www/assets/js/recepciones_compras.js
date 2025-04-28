document.addEventListener('DOMContentLoaded', () => {
    const btnConectarBalanza = document.getElementById('btn_conectar_balanza');
    const pesoBascula = document.getElementById('peso_bascula');

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
});