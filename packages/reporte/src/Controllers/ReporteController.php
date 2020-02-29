<?php

namespace Sagicc\Reporte\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\Prueba;
use DB;

class ReporteController extends Controller
{
    /**
    * Establece los parámetros requeridos para la generación de cada REPORTE.
    * Se encarga de llamar a la función específica dependiendo del tipo de REPORTE a descargar.
    */
    public function generarReporte(Request $request) {
        $tipo = $request->get('tipo');
        $campanas = $request->get('campanas');
        $agentes = $request->get('agentes');

        // Ajustando parámetros Fecha Desde y Fecha Hasta para la generación de los reportes
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        if(!$fechaDesde) $fechaDesde = date("Y-m-d");
        if(!$fechaHasta) $fechaHasta = date("Y-m-d");

        $horaDesde = $request->get('hora_desde');
        $horaHasta = $request->get('hora_hasta');
        if ($horaDesde && $fechaDesde) { $fechaDesde .= ' '.$horaDesde; }
        else { $fechaDesde .= ' 00:00:00'; }
        if ($horaHasta && $fechaDesde) { $fechaHasta .= ' '.$horaHasta; }
	else { $fechaHasta .= ' 23:59:59'; }

	switch($tipo) {
	    case "reporte_prueba": 
	        $this->reportePrueba($campanas, $agentes, $fechaDesde, $fechaHasta);
                break;
	}
    }

    /**
     * Reporte de Prueba
     */
    public function reportePrueba($campanas, $agentes, $fechaDesde, $fechaHasta) {
        $headerArr = [
	   "NOMBRE",
	   "EDAD",
	   "ID"
       ];

        $dataTypeArr = [
	    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING,
	    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC,
	    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
        ];
/*
	$bodyArr = Prueba::select("prueba.nombre", "prueba.edad", "prueba.id")
		->whereBetween("prueba.created_at",[$fechaDesde,$fechaHasta])
		->get()->all();
 */
	$bodyArr = DB::select(<<<EOT
             SELECT nombre, edad, id
             FROM prueba
             WHERE created_at BETWEEN :fechaDesde AND :fechaHasta		 
EOT
	, ["fechaDesde" => $fechaDesde, "fechaHasta" => $fechaHasta]);

	$bodyArr = json_decode(json_encode($bodyArr), true);
	$tituloReporte = "REPORTE DE PRUEBAS";

	$this->descargarReporte($headerArr, $dataTypeArr, $bodyArr, $tituloReporte, $fechaDesde, $fechaHasta);
    } 

    /**
     * Genera el reporte en formato EXCEL con la información obtenida.
     * Genera la descarga del reporte en el navegador.
     *
     * @param Array Encabezados de cada una de las columnas del reporte.
     * @param Array Contenido del reporte.
     * @param String Título del reporte.
     */
    public function descargarReporte($headerArr, $dataTypeArr, $bodyArr, $tituloReporte, $fechaDesde, $fechaHasta ) {
        //Generando EXCEL
        \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($tituloReporte, 0, 31));
        $spreadsheet->setActiveSheetIndex(0);

        //Header and Body
        $spreadsheet->getActiveSheet()->setCellValue('A1', $tituloReporte);
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Desde: '.$fechaDesde. ' - Hasta: '.$fechaHasta);
        $spreadsheet->getActiveSheet()->fromArray($headerArr, null, 'A3');

        // Agregando DATOS al REPORTE en el formato correcto
        for ($i = 0; $i < sizeof($headerArr); $i++) {
            $column = Coordinate::stringFromColumnIndex($i+1);

            for($j = 0; $j < sizeof($bodyArr); $j++) {
                $row = array_values($bodyArr[$j]);

                if (isset($row[$i])) {
                    $spreadsheet->getActiveSheet()->setCellValueExplicit(
                        $column.($j+4),
                        $row[$i],
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    );
                }
            }
        }

        //Styling Header
        $maxColumnLetter = Coordinate::stringFromColumnIndex(sizeof($headerArr));

        $spreadsheet->getActiveSheet()->mergeCells('A1:'.$maxColumnLetter.'1');
        $spreadsheet->getActiveSheet()->mergeCells('A2:'.$maxColumnLetter.'2');
        $spreadsheet->getActiveSheet()->getStyle('A1:'.$maxColumnLetter.'1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D63344');
        $spreadsheet->getActiveSheet()->getStyle('A2:'.$maxColumnLetter.'2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('1C344C');
        $spreadsheet->getActiveSheet()->getStyle('A1:'.$maxColumnLetter.'3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $spreadsheet->getActiveSheet()->getStyle('A1:'.$maxColumnLetter.'3')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:'.$maxColumnLetter.'1')->getFont()->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('A2:'.$maxColumnLetter.'2')->getFont()->setSize(12);
        $spreadsheet->getActiveSheet()->getStyle('A1:'.$maxColumnLetter.'2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('A1:'.$maxColumnLetter.'2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('A3:'.$maxColumnLetter.'3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('757575');
        $spreadsheet->getActiveSheet()->setAutoFilter('A3:'.$maxColumnLetter.'3');
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(25);

        for ($i = 0; $i < sizeof($headerArr); $i++) {
            $column = Coordinate::stringFromColumnIndex($i+1);
            $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        }

        // Adding logo
        /*$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path().'/images/lettering.png'); // put your path and image here
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawing->setHeight(18);

        $drawing->setWorksheet($spreadsheet->getActiveSheet());*/

        // Styling footer
        $spreadsheet->getActiveSheet()->setCellValue('A'.strval(sizeof($bodyArr)+4), 'Este reporte ha sido generado el '.date('Y-m-d H:i:s').' por SAGICC - Plataforma de Contact Center Omnicanal');
        $spreadsheet->getActiveSheet()->mergeCells('A'.strval(sizeof($bodyArr)+4).':'.$maxColumnLetter.strval(sizeof($bodyArr)+4));
        $spreadsheet->getActiveSheet()->getStyle('A'.strval(sizeof($bodyArr)+4).':'.$maxColumnLetter.strval(sizeof($bodyArr)+4))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('757575'
);
        $spreadsheet->getActiveSheet()->getStyle('A'.strval(sizeof($bodyArr)+4).':'.$maxColumnLetter.strval(sizeof($bodyArr)+4))->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $spreadsheet->getActiveSheet()->getStyle('A'.strval(sizeof($bodyArr)+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('A'.strval(sizeof($bodyArr)+4))->getFont()->setSize(9);

        // Guargando y descargando
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(true);
        $fileName = "SAGICC_".str_replace(" ","_",$tituloReporte)."_".date('Ymd').".xlsx";
        $writer->save(public_path().'/storage/'.$fileName);
        echo "/storage/".$fileName;
        exit;
    }
}
