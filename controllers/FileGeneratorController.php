<?php
/**
 * Generardor de documentos para la app
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class FileGeneratorController extends ExportPDF{
    # GENERAR AVATAR
    public function generarAvatar($ruta, $foto = null, $nombre){
        if(!$foto){

            // eliminar carracteres acentualdos
            $search = array('Á', 'É', 'Í', 'Ó', 'Ú');
            $replace = array('A', 'E', 'I', 'O', 'U');
            $nombre = str_replace($search, $replace, $nombre);
            # split
            $arr_nombre = explode(' ', $nombre);
            $iniciales = '';
            for($j = 0; $j < count($arr_nombre); $j++){
                $iniciales .= substr( $arr_nombre[$j], 0, 1);
            }
            
            if ((preg_match("/^[A-G]+$/",substr($iniciales, 0, 1))) ) {
                $color_avatar = 'bg-green-lt';
            } else if ((preg_match("/^[H-N]+$/",substr($iniciales, 0, 1))) ) {
                $color_avatar = 'bg-blue-lt';
            } else if ((preg_match("/^[O-T]+$/",substr($iniciales, 0, 1))) ) {
                $color_avatar = 'bg-yellow-lt';
            }else{
                $color_avatar = ''; 
            }

            /*if(strlen($iniciales) <= 1){
                $color_avatar = 'bg-green-lt';
            }else if(strlen($iniciales) == 2){
                $color_avatar = 'bg-blue-lt';
            }else if(strlen($iniciales) == 3){
                $color_avatar = 'bg-yellow-lt';
            }else{
                $color_avatar = 'custom-text-avatar';
         
            }*/

            $avatar = '<span class="avatar rounded-circle '.$color_avatar.' me-2">'.$iniciales.'</span>';
            return $avatar;
        }else{
            $url = $ruta.$foto;
            $avatar = '<span class="avatar rounded-circle me-2 bg-transparent" style="background-image: url('.$url.')"></span>';
            return $avatar;
        }

    }
    //subir archivo al servidor
    public function subirArchivo($carpeta = null, $archivo = null){
        if(!$carpeta || !$archivo){
            return false;
        }

        $directorio = "./public/avatars/$carpeta/";
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $nombre_dividido = explode('.', $archivo['name']);
        $extension = end($nombre_dividido);
        $nombre_archivo = uniqid(rand()).'.'.$extension;

        if(move_uploaded_file($archivo['tmp_name'], $directorio .$nombre_archivo)) {
          return $nombre_archivo;
        }else{
            return false;
        }

    }
    // eliminar archivo del servidor
    public function eliminarArchivo($ruta = null, $nombre = null){
        if(!$ruta || !$nombre){
            return false;
        }

        if(unlink(realpath($ruta.$nombre))){
            return true;
        }else{
            return false;
        }
    }

    // Exportar registros a excel o csv
    public function generarExcel($datos){
        $documento = false;
        if ($datos && is_array($datos)){
            $tipo_documento = $datos['tipo_documento'];
            $encabezados = $datos['encabezados'];
            $valores = $datos['registros'];
            $titulo = $datos['titulo']; 
            $nombre_archivo = ($datos['nombre_documento']) ? $datos['nombre_documento']: 'Registros';
            $bg_titulo = ($datos['color_titulo']) ? $datos['color_titulo'] : 'FABF8F';
            $bg_cols = ($datos['color_encabezados']) ? $datos['color_encabezados'] : 'FDE9D9';

            # empezar a generar el documento
            $excel = new Spreadsheet();
            

            $excel->getDefaultStyle()->getFont()->setName('Consolas'); // fuente
            $excel->getDefaultStyle()->getFont()->setSize(11); // tamaño fuente
            $hoja_activa = $excel->getActiveSheet();
        
            # generar el nombre las columnas
            foreach( range('A', 'Z') as $letra) $columnas[] = $letra;
            // combinar las celdas de la primera fila dependiendo del ultimo encabezado
            $rango = 'A1:'.$columnas[ count($encabezados) -1 ].'1';
            $hoja_activa->setCellValue('A1', $titulo);
            $hoja_activa->mergeCells($rango);

            // color de fondo para la primera fila
            $hoja_activa->getStyle($rango)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB($bg_titulo);

            // color de fondo para la fila 2
            $rango = 'A2:'.$columnas[ count($encabezados) - 1 ].'2';
            $hoja_activa->getStyle($rango)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB($bg_cols);

            // estilo de fuente negrita para las dos primeras filas
            $rango = 'A1:'.$columnas[ count($encabezados) -1 ].'2'; // rango
            $styleArray = ['font' => ['bold' => true,],];
            $hoja_activa->getStyle($rango)->applyFromArray($styleArray);

            // encabezados
            $fila = 2;
            //poner los encabezados
            foreach ($encabezados as $key => $encabezado){
                $hoja_activa->setCellValue($columnas[$key].$fila, strtoupper($encabezado));
            }

            // poner los registros de cada encabezado o columna
            $fila_inicio = 3; // fila inicio o fila actual
            //$valores = array(0 =>[0, 1, ...], 1 => [0, 1, ...], ....)
            for ($i = 0; $i < count($valores); $i++){
                for($j = 0; $j < count($valores[$i]); $j++){
                    #poner los valores de cada celda
                    $hoja_activa->setCellValue( $columnas[$j].$fila_inicio, $valores[$i][$j] ); //>set(col(A3), val(1))
                }
                $fila_inicio = $fila_inicio + 1;
            }

            // columnas con ancho automatiico
            $rango = 'A1:'.$columnas[ count($encabezados) -1 ].$fila_inicio.''; // fila inicio sera el ultimo valor del bucle
            // TEXT WRAP para las columnas
            $hoja_activa->getStyle($rango)->getAlignment()->setWrapText(true);
        
            // centrado de texto para todas las filas y cols
            $hoja_activa->getStyle($rango)->getAlignment()->setHorizontal('center');
        
            # autoajustado de ancho de las columnas
            foreach ($hoja_activa->getColumnIterator() as $columna) {
                $hoja_activa->getColumnDimension($columna->getColumnIndex())->setAutoSize(true);
            }
        
            // ALINEACION DE CONTENIDO
            $hoja_activa->getStyle($rango)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // alto de la primera fila
            $hoja_activa->getRowDimension('1')->setRowHeight(30, 'pt'); 
            
            // fuente y tamaño de letra // alto de la SEG fila
            $hoja_activa->getRowDimension('2')->setRowHeight(20, 'pt'); 
            
            $nombre = $nombre_archivo.'_'.time().'.'.strtolower($tipo_documento);

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, $tipo_documento);

            if($tipo_documento == 'Csv'){
                $link = 'data:application/text-csv;base64,';
            }else{
                $link = 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,';
            }
            ob_start();
                $writer->save('php://output');
                $documento['url'] = $link.base64_encode(ob_get_contents());
                
                $documento['nombre'] = $nombre;
            ob_end_clean();
            
        }
        return $documento;
        
    }

    // EXcel de registros invalidos
    public function generarExcelRegistrosInvalidos($registros){
        // 
        $encabezados = $registros['encabezados'];
        $encabezados[] = 'Errores'; // añadir errores como encabezaso
        
        $datos = []; 
        for($i = 0; $i < count($registros['campos']); $i++){
            $campos = $registros['campos'][$i]['registros'];
            $errores = $registros['campos'][$i]['errores'];
            //for($j = 0; $j < count($campos); $j++){
            foreach ($campos as $key => $campo){$datos[$i][] = $campo; }
            $datos[$i][] = implode(";\n", $errores);
            //}
        }
    
        $excel = new Spreadsheet();
        $excel->getDefaultStyle()->getFont()->setName('Consolas'); // fuente
        $excel->getDefaultStyle()->getFont()->setSize(11); // tamaño fuente
        $hoja_activa = $excel->getActiveSheet();
    
        # generar el nombre las columnas
        foreach( range('A', 'Z') as $letra) $columnas[] = $letra;
    
        // combinar las celdas de la primera fila dependiendo del ultimo encabezado
        $rango = 'A1:'.$columnas[ count($encabezados) -1 ].'1';
        $hoja_activa->setCellValue('A1', 'REGISTROS CON ERRORES QUE HAN SIDO DESCARTADOS Y NO HAN SIDO REGISTRADOS');
        $hoja_activa->mergeCells($rango);
    
        // color de fondo para la primera fila
        $hoja_activa->getStyle($rango)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FABF8F');
    
        // color de fondo para la fila 2
        $rango = 'A2:'.$columnas[ count($encabezados) -1 ].'2';
        $hoja_activa->getStyle($rango)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FDE9D9');
    
        // estilo de fuente negrita para las dos primeras filas
        $rango = 'A1:'.$columnas[ count($encabezados) -1 ].'2';
        $styleArray = ['font' => ['bold' => true,],];
        $hoja_activa->getStyle($rango)->applyFromArray($styleArray);
    
        // encabezados
        $fila = 2;
        //poner los encabezados
        foreach ($encabezados as $key => $encabezado){
           $hoja_activa->setCellValue($columnas[$key].$fila, $encabezado);
        }
        
        $fila_inicio = 3; // fila inicio o fila actual
        for ($i = 0; $i < count($datos); $i++){
            for($j = 0; $j < count($datos[$i]); $j++){
                #poner los valores de cada celda
                $hoja_activa->setCellValue( $columnas[$j].$fila_inicio, $datos[$i][$j] );
            }
            $fila_inicio = $fila_inicio + 1;
        }
    
        // columnas con ancho automatiico
        $rango = 'A1:'.$columnas[ count($encabezados) -1 ].$fila_inicio.''; // fila inicio sera el ultimo valor del bucle
        // TEXT WRAP para las columnas
        $hoja_activa->getStyle($rango)->getAlignment()->setWrapText(true);
    
        // centrado de texto para todas las filas y cols
        $hoja_activa->getStyle($rango)->getAlignment()->setHorizontal('center');
    
        # autoajustado de ancho de las columnas
        foreach ($hoja_activa->getColumnIterator() as $columna) {
            $hoja_activa->getColumnDimension($columna->getColumnIndex())->setAutoSize(true);
        }
    
        // ALINEACION DE CONTENIDO
        $hoja_activa->getStyle($rango)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // alto de la primera fila
        $hoja_activa->getRowDimension('1')->setRowHeight(30, 'pt'); 
        
        // fuente y tamaño de letra // alto de la SEG fila
        $hoja_activa->getRowDimension('2')->setRowHeight(20, 'pt'); 
    
        $tipo_documento = "Xlsx";
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, $tipo_documento);
        $nombre_archivo = 'Registros_con_errores'.time().'.'.$tipo_documento;
    
        
        ob_start();
        $writer->save('php://output');
        $documento['url'] = base64_encode(ob_get_contents());
        $documento['nombre'] = $nombre_archivo;
        ob_end_clean();
    
        //unlink($filename);
        //return json_encode($content);
        
        return   $documento ;
         //urlencode($nombre_archivo);
    }

    // IMPRESION DE TODOS LOS REGISTROS
    public function generarHTML(Array $informacion = null){
        if($informacion && is_array($informacion)){
            $encabezados = ($informacion['encabezados']) ? $informacion['encabezados'] : [];
            $registros = ($informacion['registros']) ? $informacion['registros'] : [];
            $titulo = ($informacion['titulo']) ? $informacion['titulo'] : 'Lista de registros';
            $descripcion = ($informacion['descripcion']) ? $informacion['descripcion'] : 'Estos son los registros encontrados';

            if (count($encabezados) > 0 && count($registros) > 0){
                if( count($encabezados) == count($registros[0]) ){
                    $html = '
                        <table>
                            <h2>'.$titulo.'</h2>
                            <p>'.$descripcion.'</p>
                            <thead>
                                <tr>';
                                #columnas, encabezados o headers
                                for ($i=0; $i < count($encabezados); $i++) { 
                                $html .= '
                                    <th>
                                        '.$encabezados[$i].'
                                    </th>';
                                }
                    $html .=    '</tr>
                            </thead>
                            <tbody>';
                                 #registros
                                 $fill = '';
                                for ($i=0; $i < count($registros) ; $i++) {
                                    $fila = $registros[$i];
                                    #fila de registro
                                    //( $i/2 != 0 ) ? $fill = 'style="background-color:#f5f7fb"' : $fill = '';
                                  $html .='<tr>';
                                    for ($j=0; $j < count($fila); $j++) { 
                                        #columna de registro
                                        $html .= '
                                            <td>
                                                '.$fila[$j].'
                                            </td>';
                                    }

                                  $html .='</tr>';
                                }
                    $html .= '        
                            </tbody>
                        </table>';
                }
            }

        }

        return $html;
    }
}

// extend TCPF with custom functions
class MYPDF extends TCPDF {
    /**
     * calcular el ancho para las columnas
     */
    public function calcularAnchoColumnas($header, $data = array()){
        #maximo espacio   #anchos           #nº columnas
        $maximo = 180; $anchos = array();
        # llenar array anchos
        //for($x=0; $x < $num_cols; $x++) $anchos[$x] = $ancho_col;

        #ancho necesario de string de cada col
        $sumCols = array(); array_unshift($data, $header);
        for ($i = 0; $i < count($data); $i++){
            for($j= 0; $j < count($data[$i]); $j++){
                $sumCols[$j][] = $this->GetStringWidth($data[$i][$j],'helvetica','',12);
            }
        }
        # sacar el maximo de cada col
        $max = array();
        for ($i=0; $i < count($sumCols) ; $i++){$max[$i] = round(max($sumCols[$i]),2); }
        $sumaMax = array_sum($max); // espacio que necesitan las columnas en total juntas
        if(!$sumaMax) return false;
        # calcular el ancho a asignar a cada col
        for($k = 0; $k <count($max); $k++){
            $achoActual = $max[$k];
            $asign = (( (100*$achoActual) / $sumaMax ) * 180)/100;
            $anchos[$k] = round($asign, 2);
        }
        
        return $anchos;
        
        /*
        return [array_sum($anchos), $anchos];
        $free = 0; $need = [];
        for($i= 0; $i < count($max); $i++){
            // quitar ancho si necesita menos de lo asignado
            if($max[$i] < $ancho_col){
                $free += $ancho_col - $max[$i];
                #$anchos[$i] = $max[$i];
            }
            // cols que nesesitan mas espacio
            if($max[$i] > $ancho_col){
                $need[$i] = $i;
            } 
        }
        # si hay espacio libre y hay cols que necesitan espacio
        if ($free && count($need) > 0){
            # asignar ancho a laas cols que necesitan menos que la que tienen
            for($i= 0; $i < count($max); $i++){
                if($max[$i] < $ancho_col){
                    $anchos[$i] = $max[$i];
                }
            }
            # espacio asignado para las que necesitan
            $asign = $free/count($need);
            foreach($need as $key =>$val){
                $anchos[$key] = round($anchos[$key] + $asign,2);
            }
        }*/


        /*array('Libre' => round($free,2), 'Requerido' => $need, 'anchos' => $anchos, 'Max' => $max)*/

        
    }
	/**
     * colorear o dibujar tabla
     * @param Array $header: array de los encabezados de la tabla
     * @param Array $data: registros a ingresar el la tabla del pdf 
     */
	public function dibujarTablaPDF($header,$data) {
		// Colors, line width and bold font
        // ENCABEZADOS: Colores, ancho de linea y fuete en negrita
		$this->setFillColor(29, 96, 176); // Color de fondo para encabezados
		$this->setTextColor(250, 251, 252); // texto encabezados
		$this->setDrawColor(29, 96, 176); // color de las bordes de las celdas
		$this->setLineWidth(0.3);
        $this->setFont('helvetica', 'B', 10);
		//$this->setFont('', 'B');
		// Header
		//$w = array(40, 35, 40, 45);
        $max = 180;
        /*$w = array();
        $ancho = $max/count($header); $x = 0;
		$num_headers = count($header);*/
        $w = $this->calcularAnchoColumnas($header, $data);
		for($i = 0; $i < count($header); ++$i) {

			//$this->Cell($w[$i], 7, strtoupper($header[$i]),1,0, 'C', 1);
            $this->MultiCell($w[$i], 7,  strtoupper($header[$i]),0,'C',1,0,null,null,true,0,false,true,7,'M',true);

		}
		$this->Ln();

		// REGISTROS: para los registros
		//$this->setFillColor(224, 235, 255);
        $this->setFont('helvetica', '', 8);
        $this->setDrawColor(224, 235, 255); // color de las bordes de los registros
        $this->setFillColor(245, 247, 251);
		$this->setTextColor(30,41,59);
        
		//$this->setFont('');
		// Data -> VALORES
		$fill = 0;
        for($i = 0; $i < count($data); $i++ ){
            $row = $data[$i];
            for($j = 0; $j < count($data[$i]); $j++){
                // cell(ancho, alto, texto, bordes)
                /*
                w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='',    $stretch=0, $ignore_min_height=false, $calign='T', $valign='M'
                $this->Cell($w[$j], 6, $row[$j] ,'LR',0, 'C', $fill);
                */
                #$this->MultiCell($w[$j],5,$row[$j],0,'LR', 0,1,null,null,true,1,false, true,'','C',$fill);
                
                /*
                $w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x=null, $y=null, $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false
                */
                #$this->MultiCell(55, 5, '[CENTER] '.$txt, 1, 'C', 0, 0, '', '', true);
                #$this->MultiCell($w[$j], 5, $row[$j], 'LR', 'L', $fill, 0, '', '', true);
                #$this->MultiCell($w[$j], 5, $row[$j], 0, '', 0, 1, '', '', true);
                #$this->MultiCell($w[$j], 5, $row[$j], 'LR', 'C', $fill, 0, $this->GetX() ,$this->GetY(), true, true);
                if($this->GetStringWidth($row[$j],'helvetica','',10) > $w[$j]){
                    $this->MultiCell($w[$j], 7, $row[$j],'LR','C', $fill,0,null,null,true,0,false,true,7,'M',true);

                }else{
                    $this->Cell($w[$j], 7, $row[$j] ,'LR',0, 'C', $fill);  
                }
                #$this->Cell()

            }
			$this->Ln();
			$fill=!$fill;
        }

		/*foreach($data as $row) {
			$this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
			$this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
			$this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R', $fill);
			$this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R', $fill);
			$this->Ln();
			$fill=!$fill;
		}*/
        // LINEA QUE CIERRA LA TABLA
		$this->Cell(array_sum($w), 0, '', 'T');
	}
}

class ExportPDF {

    public function anchoCols($encabezados, $data){
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        return $pdf->calcularAnchoColumnas($encabezados, $data);
    }
    /**
     * Genera el pdf solicitado con los datos solicitados
     * @param array $informacion: [encabezados = [], registros =[], titulo = string, nombre_documento = string] => array que contiene la informacion a incluir en el  pdf
     * @return array $documento : contiene la url de descaga y el nombre del documento creado
     */
    public function generarPDF($informacion){
        $titulo = $informacion['titulo'];
        $encabezados = $informacion['encabezados'];
        $registros = $informacion['registros'];
        $nombre = $informacion['nombre_documento'].time().'.pdf';

        // create new PDF document 
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->setCreator(PDF_CREATOR);
        $pdf->setAuthor('PASCUAL EBURI');
        $pdf->setTitle($titulo);
        $pdf->setSubject($titulo);
        $pdf->setKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        $pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN,'', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        //$pdf->setFont('helvetica', '', 12);
        // add a page
        $pdf->AddPage();

        // column titles
        $header = $encabezados; 
        // data 
        $data = $registros;
        // IMPRIMIR LA TABLA
        $pdf->dibujarTablaPDF($header, $data);
        #$pdf->lastPage();
        //$pdf->$d;

        // ---------------------------------------------------------
    
        // cerrar y al buffer la info del pdf para ser descargado
        $link = 'data:application/pdf;base64,';
        //$nombre = 'Prueba.pdf';
        ob_start();
            $pdf->Output($nombre, 'I');
            $documento['url'] = $link.base64_encode(ob_get_contents());
            $documento['nombre'] = $nombre;
        ob_end_clean();
        //============================================================+
        // FIN
        //============================================================+

        return $documento;
    }
}



?>