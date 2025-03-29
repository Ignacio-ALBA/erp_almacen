<?php

class MYPDF extends TCPDF {
    /*public function Header() {
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'CCP: Cuadro Comparativo de Proveedores', 0, 1, 'C');
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
    }*/
}
// Crear PDF
$pdf = new MYPDF('H', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage();

// Estilos
$pdf->SetFont('helvetica', '', 10);
//$pdf->SetFont('helvetica', '', 10, '', '', 'L', 'B');

$articulos = '';
$proveedores = [
    'proveedor1' => [
        'nombre' => '',
        'total' => 0
    ],
    'proveedor2' => [
        'nombre' => '',
        'total' => 0
    ],
    'proveedor3' => [
        'nombre' => '',
        'total' => 0
    ]
];

//debug($data);
foreach ($data as $articulo) {
    //debug($articulo);
    $proveedor1 = $articulo['proveedor_1'];
    $proveedor2 = $articulo['proveedor_2'];
    $proveedor3 = $articulo['proveedor_3'];
    $proveedores['proveedor1']['data'] = $proveedor1;
    $proveedores['proveedor2']['data'] = $proveedor2;
    $proveedores['proveedor3']['data'] = $proveedor3;

    $articulos .= "
        <tr>
            <td>{$articulo['kid_articulo']}</td>
            <td>{$articulo['cantidad']}</td>
            <td>{$proveedor1['costo_unitario_total']}</td>
            <td>{$proveedor1['monto_total']}</td>
            <td>{$proveedor2['costo_unitario_total']}</td>
            <td>{$proveedor2['monto_total']}</td>
            <td>{$proveedor3['costo_unitario_total']}</td>
            <td>{$proveedor3['monto_total']}</td>
        </tr>
    ";
    $proveedores['proveedor1']['total'] += $proveedor1['monto_total'];
    $proveedores['proveedor2']['total'] += $proveedor2['monto_total'];
    $proveedores['proveedor3']['total'] += $proveedor3['monto_total'];
}

// Contenido del PDF
$html = '
<style>
    table { border-collapse: collapse; width: 100%; font-size: 10px; }
    th, td { 
        border: 1px solid black; 
        padding: 5px; 
        text-align: center;
        vertical-align:center;
        margin: auto auto;
        min-height: 100%;
    }
    th {
    font-weight: bold;
    }

    .header { 
        background-color: #366092; 
        color: white;
        font-weight: bold;
        valign: middle;
        }

    .signature-container {
        max-width: 10px;  /* Ajusta el ancho */
        text-align: center;
        margin: 0 0; /* Separa las firmas */
        border-top: 1px solid #000;
        padding-top: 10px;
    }

    .signature-wrapper {
        all: revert;
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        width: 50%;
    }


    .signature-container h4, .signature-container p {
        margin: 0 0;
        padding: 0 0;
        font-size: 12px;
    }

</style>

<table>
    <tr>
        <th colspan="8" class="header"> </th>
    </tr>
    <tr>
        <th colspan="2"><div style="height:50px;"><h3>Número de Formato</h3></div></th>
        <th colspan="2"><div style="height:50px;"><h3>FT-ING-FORM-0003</h3></div></th>
        <th colspan="2"><div style="height:50px;"><h3>SGC</h3></div></th>
        <th colspan="3" >
            <div style="width:100%; max-height:40px; margin: 0 auto; text-align: center;">
                <img src="/assets/img/logos/logo.jpeg" height="50px">
            </div>
        </th>
    </tr>
    <tr>
        <th colspan="8"><h1>CCP: Cuadro Comparativo de Proveedores</h1></th>
    </tr>
    <tr>
        <td colspan="2"><b>Fecha de Creación: </b>'.date('d/m/Y H:i:s').'</td>
        <td colspan="3"><b>Revisión:</b> 1</td>
        <td colspan="3"><b>Genero: </b>'.$_SESSION["s_nombre"].'</td>
    </tr>
    <tr class="header">
        <th colspan="8"></th>
    </tr>
    <tr>
        <td colspan="4"><b>Nombre del Proyecto:</b> EFRAINA ROCHA MARTINEZ</td>
        <td colspan="4"><b>Ubicación:</b> Iztapalapa, CDMX</td>
    </tr>';

$html .= '
    <tr class="header">
        <th style="width:30%;" rowspan="2">Nombre del Producto</th>
        <th style="width:10%;" rowspan="2">Cantidad</th>
        <th style="width:20%;" colspan="2">'.'</th>
        <th style="width:20%;" colspan="2">'.'</th>
        <th style="width:20%;" colspan="2">'.'</th>
    </tr>
    <tr class="header">
        <th style="width:10%;">P.U</th>
        <th style="width:10%;">Total</th>
        <th style="width:10%;">P.U</th>
        <th style="width:10%;">Total</th>
        <th style="width:10%;">P.U</th>
        <th style="width:10%;">Total</th>
    </tr>
';

$html .= $articulos;

$html .= '<tr class="header">
            <th colspan="3">Total</th>
            <th>$ ' . $proveedores['proveedor1']['total'] . '</th>
            <th></th>
            <th>$ ' . $proveedores['proveedor2']['total'] . '</th>
            <th></th>
            <th>$ ' . $proveedores['proveedor3']['total'] . '</th>
        </tr>
        </table>
        ';

$html .= '
    <p></p>
    <table>
        <tr class="header">
            <th>Rubros</th>
            <th>Proveedor N°1</th>
            <th>Proveedor N°2</th>
            <th>Proveedor N°3</th>
        </tr>
        <tr>
            <th class="header">Fecha de cotización</th>
            <th>'.'</th>
            <th>'.'</th>
            <th>'.'</th>
        </tr>
        <tr>
            <th class="header">Tiempo de entrega</th>
            <th>'.'</th>
            <th>'.'</th>
            <th>'.'</th>
        </tr>
        <tr>
            <th class="header">Método de pago</th>
            <th>'.'</th>
            <th>'.'</th>
            <th>'.'</th>
        </tr>
        <tr>
            <th class="header">Especificaciones adicionales</th>
            <th>'.'</th>
            <th>'.'</th>
            <th>'.'</th>
        </tr>
    </table>
    <br>
    <br>
    <br>
';

$html .= '
    <table>
        <tr>
            <td class="signature-wrapper">
                <div class="signature-container">
                    <h4>'.'</h4>
                    <p>Encargado de Ingeniería</p>
                </div>
            </td>
            <td class="signature-wrapper">
                <div class="signature-container">
                    <h4>'.'</h4>
                    <p>Encargado de Ingeniería de Servicios</p>
                </div>
            </td>
        </tr>
    </table>
    <div class="signature-wrapper">
        <div class="signature-container">
            <p>'.'</p>
            <p>Encargado de Ingeniería</p>
        </div>
        <div class="signature-container">
            <p>'.'</p>
            <p>Encargado de Ingeniería de Servicios</p>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="signature-wrapper">
        <div class="signature-container">
            <h4>'.'</h4>
            <p>Gerente de Operación</p>
        </div>
        <div class="signature-container">
            <h4>'.'</h4>
            <p>Encargado de Finanzas</p>
        </div>
    </div>
    ';

//echo $html;
// Escribir contenido HTML en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Salida del PDF
$pdf->Output('Cuadro_Comparativo_Proveedores.pdf', 'D');

?>