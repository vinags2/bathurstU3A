<?php
namespace App\Exports;

use PDF;
use App\SessionAttendee;

class TimetablePdfExport extends ReportPdfExport

{
    private $linesPerPage;
    private $shadeRow;
    private $header;
    private $sessionsPerDay;
    private $maximumSessionsPerDay;
    private $includeVenue;

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
        PDF::SetPageOrientation('P');
        $this->linesPerPage = 40;
        $this->shadeRow     = false;
        $this->readUrl();
        $this->pageHeading  = 'Weekly Timetable';
        $this->header();
        $this->header       = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday');
        $this->getMaximumSessionsPerDay();
    }

    private function readUrl() {
        $options = request()->input('options');
        $this->includeVenue = ($options == 'includeVenues');
    }

    private function tableHeading() {
        $text  = '<table border="1" cellpadding="3">';
        $text .= '<thead><tr nobr="true" bgcolor="salmon" >';
        foreach ($this->header as $cell) {
            $text .= '<th><b>'.ucfirst($cell).'</b></th>';
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
        $row = 0;
        $moreCourses = true;
        $text = '';
        while ($moreCourses) {
            $text .= $this->newRow();
            $text .= $this->printRow($row);
            $text .= $this->endRow();
            $row++;
            if ($row == $this->maximumSessionsPerDay) { $moreCourses = false; }
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
        foreach ($this->header as $day) {
            $text .= $this->startCell();
            $text .= $this->printCell($row, $day);
            $text .= $this->endCell();
        }
        return $text;
    }

    private function startCell() {
        return '<td>';
    }

    private function endCell() {
        return '</td>';
    }

    private function printCell($row, $day) {
        $text = '';
        if ($row < $this->sessionsPerDay[$day]) {
            if ($this->includeVenue) {
                $text .= '<font size="-2">';
            }
            $text .= $this->data[$day][$row]->name;
            $text .= "<br>".$this->data[$day][$row]->start;
            $text .= " - ".$this->data[$day][$row]->end;
            if ($this->includeVenue) {
                $text .= "<br>".$this->data[$day][$row]->venueName;
                $text .= '</font>';
            }
        } else {
            $text .= NULL;
        }
        return $text;
    }

    private function getMaximumSessionsPerDay() {
        $this->maximumSessionsPerDay = 0;
        $this->sessionsPerDay = array();
        foreach ($this->data as $key => $day) {
            $this->sessionsPerDay[$key] = count($day);
            if ($this->sessionsPerDay[$key] > $this->maximumSessionsPerDay) {
                $this->maximumSessionsPerDay = $this->sessionsPerDay[$key];
            }
        }
    }
}