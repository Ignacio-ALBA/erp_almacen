// === JS para PESO DE MATERIA PRIMA ===
// Versión para davidshimjs/qrcodejs
// Requiere en tu HTML:
// <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
// Además, tu PHP debe inyectar el logo así:
// <script>const logoBase64 = "<?= $logoBase64 ?>";</script>
async function finalizarRecepcionMp(idRecepcionMp) {
    let resp = await fetch('/vistas/almacen_mp/bd/crudSummit.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'modalCRUD=recepciones_mp&opcion=finalizar&formDataJson=' + encodeURIComponent(JSON.stringify({id_recepcion_mp: idRecepcionMp}))
    });
    let data = await resp.json();
    // <-- AGREGA ESTAS DOS LÍNEAS AQUÍ -->
    console.log('Respuesta de finalizar:', data);
    alert('Respuesta al finalizar: ' + (data.message || JSON.stringify(data)));

    if (data.status === 'success') {
        alert('Recepción finalizada correctamente');
        // Puedes actualizar la UI, recargar la tabla, etc.
    } else {
        alert('Error al finalizar: ' + data.message);
    }
}

async function guardarPesajeRecepcionMP(extra = {}) {
    try {
        // 1. Recolectar datos de inputs y selects
        const idOrdenCompra = document.getElementById('num_pedido').value;
        const almacenDestino = document.getElementById('almacen_destino').value;
        // Número de tarimas SIEMPRE de aquí:
        const numTarimas = document.getElementById('num_tarimas').value;
        const pesoTarimas = extra.peso_tarimas || '';
        // campos automáticos
        const insumoSelect = document.getElementById('insumo_peso');
        const kidArticulo = insumoSelect.value;
        const pesoEstimado = document.getElementById('cantidad_insumo').value;
        const pesoReal = document.getElementById('peso_bascula').value;
        const contenedorDestino = document.getElementById('contenedor_destino').value;
        const valorCodigoQR = window.valorCodigoQR || ''; // Debes tener esto generado antes (ver flujo QR)
        const imagenCodigoQR = window.imagenCodigoQR || '';
        const usuarioActual = window.usuarioActual || null; // Puedes guardar el id de colaborador en window al cargar la página
        const fechaCreacion = new Date().toISOString().slice(0, 19).replace('T', ' ');
        //const pdf_generado = extra.pdf_generado || '';
        // Validar datos mínimos
        console.log({
            idOrdenCompra, almacenDestino, kidArticulo, pesoReal, contenedorDestino
        });
        if (!idOrdenCompra || !almacenDestino || !kidArticulo || !pesoReal || !contenedorDestino) {
            alert('Faltan datos obligatorios para guardar el pesaje');
            return;
        }

        // 2. Obtener info de la OC y detalle (para los campos que requieren ser copiados)
        // Puedes obtener estos datos al cargar la OC y guardarlos en un objeto, por ejemplo:
        // window.ordenCompraInfo y window.detallesOrdenCompra (verifica tu flujo)
        const orden = window.ordenCompraInfo || {};
        const detalle = (window.detallesOrdenCompra || []).find(d => d.kid_articulo == kidArticulo) || {};
        // Si no tienes estos objetos, puedes hacer un fetch sincronizado aquí, pero es mejor hacerlo al cargar la OC

        console.log(
            orden,
            orden.monto_total

        )

        // 3. Preparar el objeto de encabezado y detalles
        const encabezado = {
            codigo_externo: orden.codigo_externo,
            grupo_cotizacion: orden.grupo_cotizacion,
            kid_proyecto: orden.kid_proyecto,
            kid_proveedor: orden.kid_proveedor,
            kid_orden_compras: idOrdenCompra,
            monto_total: orden.monto_total,
            monto_neto: orden.monto_neto,
            kid_almacen: almacenDestino,
            kid_recibe: usuarioActual,
            numero_tarimas: parseInt(numTarimas) || 1,
            peso_tarimas: extra.peso_tarimas || '',
            kid_creacion: usuarioActual,
            fecha_creacion: fechaCreacion,
            kid_estatus: 1
        };

        // Diferencia de peso y retención IVA
        const diferenciaPeso = parseFloat(pesoReal) - parseFloat(pesoEstimado);
        const retencionIVA = detalle.porcentaje_descuento ?? 0;

        const detalleObj = {
            kid_articulo: kidArticulo,
            peso_estimado: pesoEstimado,
            peso_real: pesoReal,
            kid_locacion_almacen: contenedorDestino,
            costo_unitario_total: detalle.costo_unitario_total || 0,
            costo_unitario_neto: detalle.costo_unitario_neto || 0,
            monto_total: detalle.monto_total || 0,
            monto_neto: detalle.monto_neto || 0,
            retencion_iva: retencionIVA,
            diferencia_peso: diferenciaPeso,
            valor_codigoqr: valorCodigoQR,
            imagen_codigo_qr: imagenCodigoQR,
            pdf_generado: extra.pdf_generado || '',
            kid_creacion: usuarioActual,
            fecha_creacion: fechaCreacion,
            kid_estatus: 1
        };

        // 4. Armar el payload para el backend
        const payload = {
            modalCRUD: "recepciones_mp",
            opcion: 1, // Insertar
            formDataJson: {
                ...encabezado,
                detalles: [detalleObj]
            }
        };

        // 5. Enviar al backend
        const resp = await fetch('/vistas/almacen_mp/bd/crudSummit.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: Object.entries(payload).map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(typeof v === "object" ? JSON.stringify(v) : v)}`).join('&')
        });
        const data = await resp.json();
        console.log('Respuesta del guardado:', data);
        if (data.status === 'success') {
            alert('Pesaje guardado correctamente');
            // Elimina el insumo del select para evitar duplicidad
            insumoSelect.querySelector(`option[value="${kidArticulo}"]`)?.remove();
            // Bloquea los campos que deben quedar readonly tras la creación de la recepción
            document.getElementById('num_tarimas').readOnly = true;
            document.getElementById('almacen_destino').readOnly = true;
            // Actualiza el id de recepción si lo devuelve el backend
            window.lastIdRecepcionMP = data.id_recepcion_mp || window.lastIdRecepcionMP;
            //Descargar PDF
            //descargarPDF(pdfBase64);
            //localStorage.setItem('selected_orden_compra_id', localStorage.getItem('selected_orden_compra_id'));
            //localStorage.setItem('selected_orden_compra', localStorage.getItem('selected_orden_compra'));
            //localStorage.setItem('selected_orden_compra_proveedor', localStorage.getItem('selected_orden_compra_proveedor'));
            localStorage.setItem('selected_orden_compra_id', document.getElementById('num_pedido').value);
            localStorage.setItem('selected_orden_compra', document.getElementById('nombre_orden').value);
            localStorage.setItem('selected_orden_compra_proveedor', document.getElementById('proveedor_orden').value);
            window.location.reload()

        } else {
            alert("Error al guardar: " + (data.message || 'Error desconocido'));
        }
    } catch (err) {
        alert('Error al guardar pesaje: ' + err.message);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // === INICIO LÓGICA PARA LLENAR LOS INPUTS DE ORDEN, PROVEEDOR Y NOMBRE ===
    const idOrdenCompra = localStorage.getItem('selected_orden_compra_id') || '';
    const nombreOrdenCompra = localStorage.getItem('selected_orden_compra') || '';
    const proveedorOrdenCompra = localStorage.getItem('selected_orden_compra_proveedor') || '';

    // Inputs
    const numPedidoInput = document.getElementById('num_pedido');
    const nombreOrdenInput = document.getElementById('nombre_orden');
    const proveedorOrdenInput = document.getElementById('proveedor_orden');
    const insumoSelect = document.getElementById('insumo_peso');
    const cantidadInput = document.getElementById('cantidad_insumo');
    const modoPesajeSelect = document.getElementById('modo_pesaje');
    let contenedorTarima = document.getElementById('contenedor_valor_tarima');
    const numTarimasInput = document.getElementById('num_tarimas');
    const btnConectarBalanza = document.getElementById('btn_conectar_balanza');
    const pesoBasculaInput = document.getElementById('peso_bascula');
    const btnGuardarPesaje = document.getElementById('btn_guardar_pesaje');
    const btnFinalizarRecepcion = document.getElementById('btn_finalizar_recepcion');
    const btnGenerarQR = document.getElementById('btn_generar_qr');
    const btnGenerarPDF = document.getElementById('btn_generar_pdf');
    let qrCanvas = null;
    //let lastIdRecepcionMP = null; // Para saber cuál fue la última recepción


    // === CARGA DINÁMICA DE ALMACENES Y UBICACIONES ===

// Cargar almacenes al cargar la página
    fetch('/vistas/almacen_mp/bd/crudEndpoint.php?api=get_almacenes')
        .then(r => r.json())
        .then(res => {
            if (res.ok) {
                let sel = document.getElementById('almacen_destino');
                if (!sel) return;
                sel.innerHTML = '';
                res.data.forEach(a => {
                    let opt = document.createElement('option');
                    opt.value = a.kid_almacen;
                    opt.textContent = a.nombre;
                    sel.appendChild(opt);
                });
                // Trigger el evento para cargar ubicaciones del primero por defecto
                sel.dispatchEvent(new Event('change'));
            }
        });

// Cargar ubicaciones al cambiar almacén
    document.getElementById('almacen_destino').addEventListener('change', function () {
        let idAlmacen = this.value;
        fetch('/vistas/almacen_mp/bd/crudEndpoint.php?api=get_ubicaciones&id_almacen=' + encodeURIComponent(idAlmacen))
            .then(r => r.json())
            .then(res => {
                if (res.ok) {
                    let sel = document.getElementById('contenedor_destino');
                    if (!sel) return;
                    sel.innerHTML = '';
                    res.data.forEach(u => {
                        let opt = document.createElement('option');
                        opt.value = u.kid_locacion_almacen;
                        opt.textContent = u.nombre;
                        sel.appendChild(opt);
                    });
                }
            });
    });
    // Carga inicial de datos de la orden
    if (numPedidoInput) numPedidoInput.value = idOrdenCompra;
    if (nombreOrdenInput) nombreOrdenInput.value = nombreOrdenCompra;
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

    // === MODO DE PESAJE Y TARIMA DINÁMICO ===

    // Función para crear el contenedor si no existe
    function getOrCreateContenedorTarima() {
        let cont = document.getElementById('contenedor_valor_tarima');
        if (!cont) {
            cont = document.createElement('div');
            cont.id = 'contenedor_valor_tarima';
            // Inserta después del select
            if (modoPesajeSelect && modoPesajeSelect.parentNode) {
                modoPesajeSelect.parentNode.insertBefore(cont, modoPesajeSelect.nextSibling);
            } else {
                document.body.appendChild(cont);
            }
        }
        return cont;
    }

    modoPesajeSelect?.addEventListener('change', function () {
        contenedorTarima = getOrCreateContenedorTarima();
        contenedorTarima.innerHTML = '';
        localStorage.removeItem('peso_tarima');
        // Asegúrate que estos valores coincidan con los valores del select del HTML/PHP
        if (this.value === 'Pesaje Automatico' || this.value === 'pesaje_automatico') {
            contenedorTarima.innerHTML = `
                <label for="peso_tarima" class="form-label">Peso de Tarima (kg)</label>
                <input type="number" id="peso_tarima" class="form-control form-control-sm mb-1" readonly>
                <button type="button" class="btn btn-outline-primary btn-sm" id="btn_capturar_tarima">Capturar peso de tarima</button>
            `;
            document.getElementById('btn_capturar_tarima').onclick = function () {
                // Puedes agregar aquí la lógica de verificación de conexión real o simulada de balanza
                if (typeof window.balanzaConectada !== "undefined" && !window.balanzaConectada) {
                    alert("Primero conecta la balanza.");
                    return;
                }
                let peso = obtenerPesoBascula();
                document.getElementById('peso_tarima').value = peso;
                localStorage.setItem('peso_tarima', peso);
            }
        } else if (this.value === 'Captura Manual' || this.value === 'captura_manual') {
            contenedorTarima.innerHTML = `
                <label for="peso_tarima_manual" class="form-label">Peso de Tarima (kg)</label>
                <input type="number" id="peso_tarima_manual" class="form-control form-control-sm mb-1">
            `;
            document.getElementById('peso_tarima_manual').oninput = function () {
                localStorage.setItem('peso_tarima', this.value);
            }
        } else if (this.value === 'Captura Estatica' || this.value === 'captura_estatica') {
            contenedorTarima.innerHTML = 'Cargando opciones de peso...';
            fetch('/vistas/almacen_mp/bd/crudEndpoint.php?api=get_pesos_tarimas')
                .then(r => r.json())
                .then(data => {
                    let opts = '<option value="">Seleccione un peso</option>';
                    if (data.pesos) {
                        data.pesos.forEach(p => {
                            opts += `<option value="${p.valor}">${p.descripcion} (${p.valor} kg)</option>`;
                        });
                    }
                    contenedorTarima.innerHTML = `
                        <label for="peso_tarima_estatico" class="form-label">Peso de Tarima (kg)</label>
                        <select id="peso_tarima_estatico" class="form-control form-control-sm mb-1">${opts}</select>
                    `;
                    document.getElementById('peso_tarima_estatico').onchange = function () {
                        localStorage.setItem('peso_tarima', this.value);
                    }
                })
                .catch(error => {
                    console.error('Error cargando pesos de tarimas:', error);
                    contenedorTarima.innerHTML = `
                        <div class="alert alert-warning">Error cargando pesos de tarimas predefinidos</div>
                        <label for="peso_tarima_manual" class="form-label">Peso de Tarima (kg)</label>
                        <input type="number" id="peso_tarima_manual" class="form-control form-control-sm mb-1">
                    `;
                    document.getElementById('peso_tarima_manual').oninput = function () {
                        localStorage.setItem('peso_tarima', this.value);
                    }
                });
        }
    });
    // Ejemplo de uso cuando el usuario presiona el botón finalizar recepción
    document.getElementById('btn_finalizar_recepcion').addEventListener('click', function () {
        if (window.lastIdRecepcionMP) {
            finalizarRecepcionMp(window.lastIdRecepcionMP);
        } else {
            alert('No hay recepción activa.');
        }
    });
    
    // Lógica para conectar con la báscula
    btnConectarBalanza?.addEventListener('click', async () => {
        
        $.get('http://127.0.0.1:5000/estado_puerto', function(data) {
        if (data.estado === 'desconectado') {
            // Si está desconectado, obtener lista de puertos y mostrar modal
            $.get('http://127.0.0.1:5000/listar_puertos', function(resp) {
                let opciones = resp.puertos.map(p => `<option value="${p}">${p}</option>`).join('');
                let modalHtml = `
                    <div id="modalPuertos" style="display:block; position:fixed; top:30%; left:30%; background:#fff; border:1px solid #ccc; padding:20px; z-index:9999;">
                        <h3>Selecciona un puerto COM</h3>
                        <select id="puertoSelect">${opciones}</select>
                        <button id="configurarPuertoBtn">Configurar</button>
                    </div>
                `;
                $('body').append(modalHtml);
                $('#configurarPuertoBtn').on('click', function() {
                    let puerto = $('#puertoSelect').val();
                    $.ajax({
                        url: 'http://127.0.0.1:5000/configurar_puerto',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ puerto }),
                        success: function() {
                            $('#modalPuertos').remove();
                            alert('Puerto configurado correctamente');
                             btnConectarBalanza.innerHTML = '<i class="bi bi-check-circle"></i> Conectado';
                             btnConectarBalanza.classList.remove('btn-info');
                             btnConectarBalanza.classList.add('btn-success');
                             window.balanzaConectada = true;
                        }
                    });
                });
            });
        } else {
            alert('Puerto conectado: ' + data.puerto);
            btnConectarBalanza.innerHTML = '<i class="bi bi-check-circle"></i> Conectado';
            btnConectarBalanza.classList.remove('btn-info');
            btnConectarBalanza.classList.add('btn-success');
            window.balanzaConectada = true;
        }
        });

    });

    // Utilidad para obtener peso de báscula
    function obtenerPesoBascula() {
        $.get('http://127.0.0.1:5000/estado_puerto', function(data) {
            if (data.valor){
               return parseFloat(pesoBasculaInput.value) 
            }else
              return 0;  
        });
    }

    function getEtiquetaData() {
        const nombreInsumo = insumoSelect.options[insumoSelect.selectedIndex]?.textContent || '';
        const proveedor = proveedorOrdenInput.value;
        const fechaHora = (new Date()).toLocaleString('es-MX');
        const pesoKg = pesoBasculaInput.value || '';
        const pesoTarima = localStorage.getItem('peso_tarima') || '';
        const modo = modoPesajeSelect.value;
        const numTarimas = numTarimasInput.value;
        const pesoReal = pesoKg > 0 ? pesoKg : pesoTarima;

        return {
            numPedido: numPedidoInput.value,
            nombreInsumo,
            proveedor,
            cantidadSolicitada: cantidadInput.value,
            pesoNeto: pesoReal,
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


   btnGuardarPesaje?.addEventListener('click', async () => {
    try {
        if (typeof QRCode !== 'function') {
            alert('No se pudo cargar la librería QRCode');
            return;
        }

        // 1. Generar QR con davidshimjs/qrcodejs
        const datos = getEtiquetaData();
        const etiqueta = formatoEtiquetaVisual(datos);
        let qrDiv = document.getElementById('qr_code');
        if (!qrDiv) {
            qrDiv = document.createElement('div');
            qrDiv.id = 'qr_code';
            document.body.appendChild(qrDiv);
        }
        qrDiv.innerHTML = '';
        let jsonQR = JSON.stringify({
                idOrdenCompra: datos.numPedido,
                insumo: datos.nombreInsumo,
                proveedor: datos.proveedor,
                fecha_hora: datos.fechaHora,
                peso_kg: datos.pesoNeto,
                peso_tarima: datos.pesoTarima
            });
        let qr = new QRCode(qrDiv, {
            text: jsonQR,
            width: 200,
            height: 200,
            correctLevel: QRCode.CorrectLevel ? QRCode.CorrectLevel.H : 2 // fallback
        });

        // Espera a que el <img> o <canvas> esté disponible
        await new Promise((res) => setTimeout(res, 400));

        // davidshimjs/qrcodejs genera <img> en la mayoría de navegadores, sino <canvas>
        let qrCanvas = qrDiv.querySelector('canvas');
        let qrImg = qrDiv.querySelector('img');
        let qrDataUrl = '';
        if (qrImg && qrImg.src) {
            qrDataUrl = qrImg.src;
        } else if (qrCanvas) {
            qrDataUrl = qrCanvas.toDataURL('image/png');
        } else {
            throw new Error('No se pudo generar el código QR');
        }
        // Guardar datos del QR en window para usarlos después
        window.valorCodigoQR = jsonQR;
        window.imagenCodigoQR = qrDataUrl;

        // === OBTENER PESO DE TARIMA SEGUN EL TIPO DE PESAJE ===
        let pesoTarimas = '';
        if (document.getElementById('peso_tarima')) {
            pesoTarimas = document.getElementById('peso_tarima').value;
        } else if (document.getElementById('peso_tarima_manual')) {
            pesoTarimas = document.getElementById('peso_tarima_manual').value;
        } else if (document.getElementById('peso_tarima_estatico')) {
            pesoTarimas = document.getElementById('peso_tarima_estatico').value;
        }
        // fallback: si alguna lógica lo puso en localStorage
        if (!pesoTarimas) pesoTarimas = localStorage.getItem('peso_tarima') || '';

        // 4. Ahora llama la función que arma el payload y guarda en BD
        // 2. Generar PDF
        const {PDFDocument, rgb, StandardFonts, degrees} = PDFLib;
        const pdfDoc = await PDFDocument.create();
        const page = pdfDoc.addPage([425.25, 283.5]);
        // ROTAR 90 grados el contenido (vertical)
        const rotation = degrees(90);
        page.setRotation(rotation);
        const {width, height} = page.getSize();

        // LOGO
        try {
            if (typeof logoBase64 !== 'undefined' && logoBase64) {
                const logoImageBytes = await fetch(logoBase64).then(r => r.arrayBuffer());
                let logoImageEmbed;
                if (logoBase64.startsWith('data:image/png')) {
                    logoImageEmbed = await pdfDoc.embedPng(logoImageBytes);
                } else {
                    logoImageEmbed = await pdfDoc.embedJpg(logoImageBytes);
                }
                const logoWidth = 80;
                const logoHeight = 80;
                page.drawImage(logoImageEmbed, {
                    x: 10,
                    y: height - logoHeight - 10,
                    width: logoWidth,
                    height: logoHeight,
                });
            }
        } catch (e) {
            console.error("Error al cargar el logo en el PDF:", e);
        }

        const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
        const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold);

        // Título "FORCIP"
        page.drawText('FORCIP', {
            x: 130,
            y: height - 50,
            size: 40,
            font: fontBold,
            color: rgb(0, 0, 0),
        });

        // Barra negra con el nombre del INSUMO
        page.drawRectangle({
            x: 0, y: height - 90, width: width, height: 30, color: rgb(0, 0, 0),
        });

        // Insumo pesado en barra negra, en blanco
        const insumo = datos.nombreInsumo || (insumoSelect?.selectedOptions[0]?.textContent ?? '');
        page.drawText(insumo, {
            x: 14,
            y: height - 82,
            size: 20,
            font: fontBold,
            color: rgb(1, 1, 1),
        });

        // Proveedor (debajo de la barra negra, en negro)
        const proveedor = datos.proveedor || (proveedorOrdenInput?.value ?? '');
        page.drawText(`Proveedor: ${proveedor}`, {
            x: 14,
            y: height - 110,
            size: 17,
            font: font,
            color: rgb(0, 0, 0),
        });

        // Fecha, hora, peso (peso resalta, grande)
        const currentDate = new Date();
        const date = currentDate.toLocaleDateString();
        const time = currentDate.toLocaleTimeString();
        const peso = datos.pesoNeto || (pesoBasculaInput?.value ?? '0.00');

        page.drawText(`Fecha: ${date}`, {
            x: 14,
            y: height - 150,
            size: 23,
            font: font,
            color: rgb(0, 0, 0),
        });

        page.drawText(`Hora: ${time}`, {
            x: 14,
            y: height - 180,
            size: 23,
            font: font,
            color: rgb(0, 0, 0),
        });

        page.drawText(`Peso:`, {
            x: 14,
            y: height - 220,
            size: 35,
            font: fontBold,
            color: rgb(0, 0, 0),
        });

        page.drawText(`${peso} kg`, {
            x: 120,
            y: height - 220,
            size: 35,
            font: fontBold,
            color: rgb(0, 0, 0),
        });

        // QR a la derecha
        const qrImageBytes = await fetch(qrDataUrl).then((res) => res.arrayBuffer());
        const qrImageEmbed = await pdfDoc.embedPng(qrImageBytes);
        const qrSize = 115; // antes 120
        page.drawImage(qrImageEmbed, {
            x: width - qrSize - 20,  // un poco más pegado al borde
            y: height - 250,         // súbelo o bájalo según necesites
            width: qrSize,
            height: qrSize,
        });

        const pdfBytes = await pdfDoc.save();
        const pdfBase64 = btoa(
            new Uint8Array(pdfBytes).reduce((data, byte) => data + String.fromCharCode(byte), '')
        );
        const blob = new Blob([pdfBytes], { type: "application/pdf" });
        const url = URL.createObjectURL(blob);
        
        // Abrir en nueva pestaña con el visor PDF del navegador
        window.open(url, "_blank");
    } catch (e) {
        console.error('Error completo:', e);
        alert('Error en el proceso: ' + e.message);
        return;
    }
});

    // Finalizar recepción y mostrar detalles en modal
    btnFinalizarRecepcion?.addEventListener('click', async () => {
        if (!window.lastIdRecepcionMP) {
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
            const tbody = document.getElementById('modal_detalles_recepcion_mp_tbody');
            tbody.innerHTML = '';
            data.detalles.forEach(det => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${det.kid_articulo}</td>
                                <td>${det.peso_estimado}</td>
                                <td>${det.peso_real}</td>
                                <td>${det.valor_codigoqr}</td>`;
                tbody.appendChild(tr);
            });
            const modal = new bootstrap.Modal(document.getElementById('modal_detalles_recepcion_mp'));
            modal.show();
        } catch (e) {
            alert('No se pudo cargar los detalles: ' + e.message);
        }
    });

    // Utilidad para obtener peso de báscula (para compatibilidad)
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
        new QRCode(qrDiv, {
            text: etiqueta,
            width: 200,
            height: 200,
            correctLevel: QRCode.CorrectLevel ? QRCode.CorrectLevel.H : 2
        });
    });
});