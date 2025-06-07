document.addEventListener('DOMContentLoaded', () => {
    // === INICIO LÓGICA PARA LLENAR LOS INPUTS DE ORDEN, PROVEEDOR Y NOMBRE ===
    const idOrdenCompra = localStorage.getItem('selected_orden_compra_id') || '';
    const nombreOrdenCompra = localStorage.getItem('selected_orden_compra') || '';
    const proveedorOrdenCompra = localStorage.getItem('selected_orden_compra_proveedor') || '';

    // Inputs
    const numPedidoInput      = document.getElementById('num_pedido');
    const nombreOrdenInput    = document.getElementById('nombre_orden');
    const proveedorOrdenInput = document.getElementById('proveedor_orden');
    const insumoSelect        = document.getElementById('insumo_peso');
    const cantidadInput       = document.getElementById('cantidad_insumo');
    const modoPesajeSelect    = document.getElementById('modo_pesaje');
    const contenedorTarima    = document.getElementById('contenedor_valor_tarima');
    const numTarimasInput     = document.getElementById('num_tarimas');
    const btnConectarBalanza  = document.getElementById('btn_conectar_balanza');
    const pesoBasculaInput    = document.getElementById('peso_bascula');
    const btnGuardarPesaje    = document.getElementById('btn_guardar_pesaje');
    const btnFinalizarRecepcion = document.getElementById('btn_finalizar_recepcion');
    // Si existen los botones individuales de QR o PDF los soportamos pero ocultos
    const btnGenerarQR        = document.getElementById('btn_generar_qr');
    const btnGenerarPDF       = document.getElementById('btn_generar_pdf');
    let qrCanvas = null;
    let lastIdRecepcionMP = null; // Para saber cuál fue la última recepción

    // Carga inicial de datos de la orden
    if (numPedidoInput)      numPedidoInput.value      = idOrdenCompra;
    if (nombreOrdenInput)    nombreOrdenInput.value    = nombreOrdenCompra;
    if (proveedorOrdenInput) proveedorOrdenInput.value = proveedorOrdenCompra;

    // Limpia localStorage
    localStorage.removeItem('selected_orden_compra');
    localStorage.removeItem('selected_orden_compra_id');
    localStorage.removeItem('selected_orden_compra_proveedor');

    // Llenar insumos desde API y manejo de errores visual
    function cargarInsumosOrden() {
        if (!idOrdenCompra || !insumoSelect) return;
        insumoSelect.innerHTML = '<option>Cargando insumos...</option>';
         fetch('/vistas/almacen_mp/bd/crudEndpoint.php?api=get_detalles_orden&id=' + encodeURIComponent(idOrdenCompra))
            .then(response => response.json())
            .then(data => {
                insumoSelect.innerHTML = '';
                if (!data.ok || !Array.isArray(data.detalles) || data.detalles.length === 0) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'No hay insumos para esta orden';
                    insumoSelect.appendChild(opt);
                } else {
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Seleccione un insumo';
                    insumoSelect.appendChild(defaultOption);
                    data.detalles.forEach(insumo => {
                        const option = document.createElement('option');
                        option.value = insumo.kid_articulo;
                        option.textContent = insumo.nombre_articulo;
                        option.dataset.cantidad = insumo.cantidad;
                        insumoSelect.appendChild(option);
                    });
                }
                // Disparar change para actualizar la cantidad si ya hay uno seleccionado
                insumoSelect.dispatchEvent(new Event('change'));
            })
            .catch(e => {
                insumoSelect.innerHTML = '<option>Error al cargar insumos</option>';
                alert('Error cargando insumos: ' + e.message);
            });
    }
    cargarInsumosOrden();

    // Mostrar cantidad al seleccionar un insumo
    insumoSelect?.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        cantidadInput.value = opt && opt.dataset.cantidad ? opt.dataset.cantidad : '';
    });

    // Modo de pesaje y tarima dinámico (igual que antes)
    modoPesajeSelect?.addEventListener('change', function () {
        contenedorTarima.innerHTML = '';
        localStorage.removeItem('peso_tarima');
        if (this.value === 'automatico') {
            contenedorTarima.innerHTML = `
                <label for="peso_tarima" class="form-label">Peso de Tarima (kg)</label>
                <input type="number" id="peso_tarima" class="form-control form-control-sm mb-1" readonly>
                <button type="button" class="btn btn-outline-primary btn-sm" id="btn_capturar_tarima">Capturar peso de tarima</button>
            `;
            document.getElementById('btn_capturar_tarima').onclick = function() {
                let peso = obtenerPesoBascula();
                document.getElementById('peso_tarima').value = peso;
                localStorage.setItem('peso_tarima', peso);
            }
        } else if (this.value === 'manual') {
            contenedorTarima.innerHTML = `
                <label for="peso_tarima_manual" class="form-label">Peso de Tarima (kg)</label>
                <input type="number" id="peso_tarima_manual" class="form-control form-control-sm mb-1">
            `;
            document.getElementById('peso_tarima_manual').oninput = function() {
                localStorage.setItem('peso_tarima', this.value);
            }
        } else if (this.value === 'estatico') {
            fetch('/vistas/almacen_mp/bd/crudEndpoint.php?api=get_pesos_tarimas')
                .then(r => r.json())
                .then(data => {
                    let opts = '<option value="">Seleccione un peso</option>';
                    if(data.pesos){
                        data.pesos.forEach(p => {
                            opts += `<option value="${p.valor}">${p.descripcion} (${p.valor} kg)</option>`;
                        });
                    }
                    contenedorTarima.innerHTML = `
                        <label for="peso_tarima_estatico" class="form-label">Peso de Tarima (kg)</label>
                        <select id="peso_tarima_estatico" class="form-control form-control-sm mb-1">${opts}</select>
                    `;
                    document.getElementById('peso_tarima_estatico').onchange = function() {
                        localStorage.setItem('peso_tarima', this.value);
                    }
                });
        }
    });

    // Lógica para conectar con la báscula...
    btnConectarBalanza?.addEventListener('click', async () => {
        try {
            const port = await navigator.serial.requestPort();
            await port.open({
                baudRate: 9600,
                dataBits: 8,
                stopBits: 1,
                parity: "none",
                flowControl: "none"
            });

            btnConectarBalanza.innerHTML = '<i class="bi bi-check-circle"></i> Conectado';
            btnConectarBalanza.classList.remove('btn-info');
            btnConectarBalanza.classList.add('btn-success');

            const decoder = new TextDecoderStream();
            port.readable.pipeTo(decoder.writable);
            const inputStream = decoder.readable;
            const reader = inputStream.getReader();

            while (true) {
                const { value, done } = await reader.read();
                if (done) break;
                if (value) pesoBasculaInput.value = value.trim();
            }

            await reader.releaseLock();
        } catch (err) {
            alert('Error al conectar con la balanza: ' + err.message);
        }
    });

    function getEtiquetaData() {
        const nombreInsumo = insumoSelect.options[insumoSelect.selectedIndex]?.textContent || '';
        const proveedor    = proveedorOrdenInput.value;
        const fechaHora    = (new Date()).toLocaleString('es-MX');
        const pesoKg       = pesoBasculaInput.value || '';
        const pesoTarima   = localStorage.getItem('peso_tarima') || '';
        const modo         = modoPesajeSelect.value;
        const numTarimas   = numTarimasInput.value;

        return {
            numPedido: numPedidoInput.value,
            nombreInsumo,
            proveedor,
            cantidadSolicitada: cantidadInput.value,
            pesoNeto: pesoKg,
            pesoTarima,
            modo,
            numTarimas,
            fechaHora
        };
    }

    function formatoEtiquetaVisual(data) {
        return (
`----------------------------------------
ORDEN:    ${data.numPedido}
INSUMO:   ${data.nombreInsumo}
PROVEEDOR:${data.proveedor}

CANTIDAD SOLICITADA: ${data.cantidadSolicitada}
PESO NETO:          ${data.pesoNeto} kg
PESO TARIMA:        ${data.pesoTarima} kg
MODO PESAJE:        ${data.modo}
TARIMAS / PARTES:   ${data.numTarimas}
FECHA Y HORA:       ${data.fechaHora}
----------------------------------------`
        );
    }

    // === GUARDAR PESAJE (QR, PDF, BD, errores por paso) ===
    btnGuardarPesaje?.addEventListener('click', async () => {
        try {
            // 1. Generar QR
            const datos = getEtiquetaData();
            const etiqueta = formatoEtiquetaVisual(datos);
            let qrDiv = document.getElementById('qr_code');
            if (!qrDiv) {
                qrDiv = document.createElement('div');
                qrDiv.id = 'qr_code';
                document.body.appendChild(qrDiv);
            }
            qrDiv.innerHTML = '';
            new QRCode(qrDiv, {
                text: JSON.stringify({
                    insumo: datos.nombreInsumo,
                    proveedor: datos.proveedor,
                    fecha_hora: datos.fechaHora,
                    peso_kg: datos.pesoNeto,
                    peso_tarima: datos.pesoTarima
                }),
                width: 200,
                height: 200,
                correctLevel: QRCode.CorrectLevel.H
            });
            // Espera a que el canvas exista
            await new Promise((res) => setTimeout(res, 400));
            qrCanvas = qrDiv.querySelector('canvas');
            if (!qrCanvas) throw new Error('No se pudo generar el código QR');

            // 2. Generar PDF
            const { PDFDocument, rgb, StandardFonts, degrees } = PDFLib;
            const pdfDoc = await PDFDocument.create();
            const page = pdfDoc.addPage([425.25, 283.5]);
            const rotation = degrees(90);
            page.setRotation(rotation);
            const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
            let yPos = page.getSize().height - 80;
            for (const line of etiqueta.split('\n')) {
                page.drawText(line, { x: 60, y: yPos, size: 13, font, color: rgb(0, 0, 0) });
                yPos -= 15;
            }
            const qrImage = qrCanvas.toDataURL('image/png');
            const qrImageBytes = await fetch(qrImage).then((res) => res.arrayBuffer());
            const qrImageEmbed = await pdfDoc.embedPng(qrImageBytes);
            page.drawImage(qrImageEmbed, {
                x: page.getSize().width - 120 - 10,
                y: 30,
                width: 120,
                height: 120,
            });
            const pdfBytes = await pdfDoc.save();
            const blob = new Blob([pdfBytes], { type: 'application/pdf' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'etiqueta-recepcion.pdf';
            link.click();

            // 3. Guardar en BD (AJAX)
            const formData = {
                recepcion_mp: 'Pesaje OC ' + datos.numPedido,
                numero_tarimas: parseInt(datos.numTarimas) || 1,
                numero_parets: 0,
                codigo_externo: '',
                grupo_cotizacion: 1,
                kid_proyecto: null,
                kid_proveedor: proveedorOrdenInput.value,
                kid_orden_compras: datos.numPedido,
                kid_almacen: null,
                monto_total: 0,
                monto_neto: 0,
                kid_creacion: null,
                fecha_creacion: new Date().toISOString().slice(0, 19).replace('T',' '),
                kid_estatus: 1,
                kid_ubicacion_almacen: null,
                detalles: [{
                    kid_articulo: insumoSelect.value,
                    cantidad_tarimas: parseFloat(datos.numTarimas) || 1,
                    cantidad_parets: 0,
                    costo_unitario_neto: 0,
                    costo_unitario_total: 0,
                    monto_neto: 0,
                    porcentaje_descuento: 0,
                    monto_total: 0,
                    peso_real: parseFloat(datos.pesoNeto) || 0,
                    valor_codigoqr: JSON.stringify({
                        insumo: datos.nombreInsumo,
                        proveedor: datos.proveedor,
                        fecha_hora: datos.fechaHora,
                        peso_kg: datos.pesoNeto,
                        peso_tarima: datos.pesoTarima
                    }),
                    imagen_codigo_qr: '', // Si necesitas guardar la ruta
                    kid_creacion: null,
                    fecha_creacion: new Date().toISOString().slice(0, 19).replace('T',' '),
                    kid_estatus: 1
                }]
            };

            const response = await fetch('/vistas/almacen_mp/bd/crudSummit.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ modalCRUD: 'recepciones_mp', opcion: 1, formDataJson: formData })
            });
            const result = await response.json();
            if (result.status !== 'success') throw new Error('Error al guardar en la BD');
            alert('Pesaje guardado correctamente');
            // Guarda el id de la recepción para luego mostrar sus detalles
            lastIdRecepcionMP = result.id_recepcion_mp || null;
        } catch (e) {
            alert('Error en el proceso: ' + e.message);
        }
    });

    // Finalizar recepción y mostrar detalles en modal
    btnFinalizarRecepcion?.addEventListener('click', async () => {
        // Si tienes el id de la última recepción, consulta los detalles y muestra el modal
        if (!lastIdRecepcionMP) {
            alert('No se encontró la recepción recién guardada. Guarda un pesaje antes.');
            return;
        }
        try {
           const resp = await fetch(`/vistas/almacen_mp/bd/crudEndpoint.php?api=get_detalles_recepcion_mp&id=${lastIdRecepcionMP}`);
            const data = await resp.json();
            if (!data.ok || !Array.isArray(data.detalles) || data.detalles.length === 0) {
                alert('No hay detalles para esta recepción.');
                return;
            }
            // Llena la tabla del modal
            const tbody = document.getElementById('modal_detalles_recepcion_mp_tbody');
            tbody.innerHTML = '';
            data.detalles.forEach(det => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${det.kid_articulo}</td>
                                <td>${det.cantidad_tarimas}</td>
                                <td>${det.peso_real}</td>
                                <td>${det.valor_codigoqr}</td>`;
                tbody.appendChild(tr);
            });
            // Muestra el modal
            const modal = new bootstrap.Modal(document.getElementById('modal_detalles_recepcion_mp'));
            modal.show();
        } catch (e) {
            alert('No se pudo cargar los detalles: ' + e.message);
        }
    });

    // Utilidad para obtener peso de báscula
    function obtenerPesoBascula() {
        return parseFloat(pesoBasculaInput.value) || 0;
    }

    // Para compatibilidad, deja los eventos de los botones QR/PDF si existen
    btnGenerarQR?.addEventListener('click', () => {
        const datos = getEtiquetaData();
        const etiqueta = formatoEtiquetaVisual(datos);

        let preview = document.getElementById('etiqueta_preview');
        if (!preview) {
            preview = document.createElement('pre');
            preview.id = 'etiqueta_preview';
            document.body.appendChild(preview);
        }
        preview.textContent = etiqueta;

        let qrDiv = document.getElementById('qr_code');
        if (!qrDiv) {
            qrDiv = document.createElement('div');
            qrDiv.id = 'qr_code';
            document.body.appendChild(qrDiv);
        }
        qrDiv.innerHTML = '';
        qrCanvas = document.createElement('canvas');
        new QRCode(qrDiv, {
            text: etiqueta,
            width: 200,
            height: 200,
            correctLevel: QRCode.CorrectLevel.H
        });
        qrCanvas = qrDiv.querySelector('canvas');
    });

    btnGenerarPDF?.addEventListener('click', async () => {
        if (!qrCanvas) {
            alert('Primero debes generar el código QR.');
            return;
        }
        const { PDFDocument, rgb, StandardFonts, degrees } = PDFLib;
        const pdfDoc = await PDFDocument.create();
        const page = pdfDoc.addPage([425.25, 283.5]);
        const rotation = degrees(90);
        page.setRotation(rotation);
        const { width, height } = page.getSize();

        const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
        const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold);

        page.drawText('FORCIP', {
            x: (width / 2) - 70, y: height - 80, size: 40, font: fontBold, color: rgb(0, 0, 0),
        });
        page.drawRectangle({
            x: 0, y: height - 130, width: width, height: 30, color: rgb(0, 0, 0),
        });
        page.drawText(insumoSelect.options[insumoSelect.selectedIndex]?.textContent || '', {
            x: 14, y: height - 120, size: 20, font: fontBold, color: rgb(1, 1, 1),
        });

        const etiqueta = document.getElementById('etiqueta_preview')?.textContent || formatoEtiquetaVisual(getEtiquetaData());
        const etiquetaLines = etiqueta.split('\n');
        let yPos = height - 160;
        for (const line of etiquetaLines) {
            page.drawText(line, { x: 60, y: yPos, size: 13, font, color: rgb(0, 0, 0) });
            yPos -= 15;
        }

        const qrImage = qrCanvas.toDataURL('image/png');
        const qrImageBytes = await fetch(qrImage).then((res) => res.arrayBuffer());
        const qrImageEmbed = await pdfDoc.embedPng(qrImageBytes);
        const qrSize = 120;
        page.drawImage(qrImageEmbed, {
            x: width - qrSize - 10,
            y: 30,
            width: qrSize,
            height: qrSize,
        });

        const pdfBytes = await pdfDoc.save();
        const blob = new Blob([pdfBytes], { type: 'application/pdf' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'etiqueta-recepcion.pdf';
        link.click();
    });
});