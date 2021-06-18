<?php
namespace App\Exports;

use PDF;
use App\SessionAttendee;

class CourseInformationPdfExport extends ReportPdfExport

{
    private $linesPerPage;
    private $shadeRow;
    private $header;
    private $largerFont;
    private $columnSizes;
    private $fontSize;

    protected function formatData() {
        $this->preFormatData();
        $text = '';
        $text .= $this->tableHeading();
        $text .= $this->tableBody();
        $text .= $this->closeTable();
        PDF::startPageGroup();
        PDF::AddPage();
        PDF::writeHTML($text, true, false, true, false, '');
    }

    private function preFormatData() {
        $this->readUrl();
        $this->configureLargerFont();
        $this->linesPerPage = 40;
        $this->shadeRow     = false;
        $this->pageHeading  = 'Course Information Sheet';
        $this->header();
        $this->header       = array('Course', 'Time', 'Description');
    }

    private function configureLargerFont() {
        if ($this->largerFont) {
            PDF::SetPageOrientation('L');
            $this->columnSizes[0] = '25%';
            $this->columnSizes[1] = '25%';
            $this->columnSizes[2] = '50%';
            $this->fontSize       = '+3';
        } else {
            PDF::SetPageOrientation('P');
            $this->columnSizes[0] = '25%';
            $this->columnSizes[1] = '25%';
            $this->columnSizes[2] = '50%';
            $this->fontSize       = '-2';
        }
    }

    private function readUrl() {
        $options = request()->input('options');
        $this->largerFont = ($options == 'largerFont');
    }

    private function tableHeading() {
        $text  = '<table border="1" cellpadding="3">';
        $text .= '<thead><tr nobr="true" bgcolor="salmon" >';
        foreach ($this->header as $key => $cell) {
            $text .= '<th width="'.$this->columnSizes[$key].'"><b>'.ucfirst($cell).'</b></th>';
        }
        $text .= '</tr></thead>';
        $text .= '<tbody>';
        return $text;
    }

    private function closeTable() {
        $text = '</tbody>';
        $text .= '</table>';
        return $text;
    }

    private function tableBody() {
        $text = '';
        foreach ($this->data as $row) {
            $text .= $this->newRow();
            $text .= $this->printRow($row);
            $text .= $this->endRow();
        }
        return $text;
    }

    private function newRow() {
        $text = '<tr nobr="true"';
        if ($this->shadeRow) {
            $text     .= ' bgcolor="lightcyan" ';
        }
        $this->shadeRow = !$this->shadeRow;
        $text         .= '>';
        return $text;
    }

    private function endRow() {
        return '</tr>';
    }

    private function printRow($row) {
        $text = '';
        $text .= $this->printCell(0,'<b>'.$row->name.'</b>'.'<br>'.$row->facilitator.'<br>'.$row->phone);
        $text .= $this->printCell(1,$row->day.'<br>'.$row->start.' - '.$row->end.'<br>'.$row->venueName);
        $text .= $this->printCell(2,$row->description);
        return $text;
    }

    private function printCell($column, $cell) {
        $text = '';
        $text .= '<td width="'.$this->columnSizes[$column].'">'.'<font size="'.$this->fontSize.'">';
        $text .= $cell;
        $text .= '</font></td>';
        return $text;
    }
}