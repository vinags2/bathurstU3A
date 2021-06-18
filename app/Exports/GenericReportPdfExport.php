<?php
namespace App\Exports;

use PDF;

class GenericReportPdfExport extends ReportPdfExport
{
    private $shadeRow;

    protected function formatData() {
        $this->shadeRow = false;
        $text = '<table border="1" cellpadding="3">';
        $text .= '<thead><tr nobr="true" bgcolor="light grey" >';
        foreach ($this->data[0] as $heading => $cell) {
            $text .= '<th><b>'.$heading.'</b></th>';
        }
        $text .= '</tr></thead>';
        $text .= '<tbody>';
		foreach($this->data as $row) {
            $text         .= '<tr nobr="true"';
            if ($this->shadeRow) {
                $text     .= ' bgcolor="light grey" ';
            }
            $text         .= '>';
            $this->shadeRow = !$this->shadeRow;
            foreach ($row as $cell) {
                $text .= '<td>'.$cell.'</td>';
            }
            $text .= '</tr>';
		}
        $text .= '</tbody>';
        $text .= '</table>';
        PDF::startPageGroup();
        PDF::AddPage();
        PDF::writeHTML($text, true, false, true, false, '');
    }
}