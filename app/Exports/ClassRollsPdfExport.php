<?php
namespace App\Exports;

use PDF;
use App\SessionAttendee;
use Illuminate\Support\Facades\DB;

class ClassRollsPdfExport extends ReportPdfExport

// TODO: add options to print different type of roll than the default for individual roll print
// TODO: speed up the page loading -- too many database calls I believe

{
    private $allRolls;
    private $weeksInTerm;
    private $weeksHeading;
    private $weekColWidth;
    private $nameColWidth;
    private $rollType;
    private $linesPerPage;
    private $shadeRow;
    private $print;

    protected function formatData() {
        // DB::connection()->enableQueryLog();
        $this->preFormatData();
        foreach ($this->data as $session) {
            $this->setRollTypes($session);
            $this->printClassRoll($session);
            $this->printContactDetails($session);
            }
        $this->postFormatData();
        // dd(DB::getQueryLog());
    }

    private function preFormatData() {
        $this->readUrl();
        PDF::SetPageOrientation('P');
        $this->linesPerPage = 40;
        $this->allRolls     = (count($this->data) > 1) ? true : false;
        $this->shadeRow     = false;
    }

    private function readUrl() {
        $options = request()->input('options');
        $this->print['rolls'] = true;
        $this->print['contact details'] = true;
        if ($options == 'rollsOnly') {
            $this->print['contact details'] = false;
        } elseif ($options == 'contactDetailsOnly') {
            $this->print['rolls'] = false;
        }
    }

    private function postFormatData() {
        if ($this->allRolls) {
            $this->pageHeading = 'Class-rolls';
        }
    }

    private function setRollTypes($session) {
        $this->rollType['print']                   = !($session->roll_type & 16);
        $this->rollType['type']                    = ($session->roll_type & 1) ? 'generic' : 'normal';
        $this->rollType['between terms']           = ($session->roll_type & 2);
        $this->rollType['extra blank pages']       = !($session->roll_type & 4);
        $this->rollType['monthly']                 = ($session->roll_type & 32);
        $this->rollType['include contact details'] = !($session->roll_type & 64);
    }

    // ******************************
    // Print the Class Roll functions
    // ******************************

    private function printClassRoll($session) {
        if ($this->print['rolls']) {
            if ($this->rollType['print'] | !$this->allRolls) {
                $this->setWeeks($session);
                $this->setHeaderClassRoll($session, false);
                $this->formatOnePageClassRoll($session);
                if ($this->rollType['between terms']) {
                    $this->setHeaderClassRoll($session, true);
                    $this->formatOnePageClassRoll($session);
                }
            }
        }
    }

    private function formatOnePageClassRoll($session) {
        $text  = $this->tableHeadingClassRoll();
        $text .= $this->tableBodyClassRoll($session);
        PDF::startPageGroup();
        PDF::AddPage();
        PDF::writeHTML($text, true, false, true, false, '');
    }

    private function setWeeks($session) {
        $this->weeksInTerm  = $session->term_length;
        $this->weekColWidth = '9%';
        $this->nameColWidth = '25%';
        if ($this->rollType['monthly']) {
            $this->weeksHeading = ['Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan'];
            $this->weekColWidth = '6%';
        } elseif ($this->weeksInTerm == 6) {
             $this->weeksHeading = ['Week 1','Week 2','Week 3','Week 4','Week 5','Week 6'];
        } else {
             $this->weeksHeading = ['Week 1','Week 2','Week 3','Week 4','Week 5','Week 6','Week 7','Week 8'];
        }
    }

    private function setHeaderClassRoll($session, $betweenTermsRoll) {
        $headingPrefix     = $betweenTermsRoll ? 'Between Terms Roll' : 'Class Roll';
        $this->pageHeading = $headingPrefix.' for '.$session->name;
        $this->header();
    }

    private function tableHeadingClassRoll() {
        $text  = '<table border="1" cellpadding="3">';
        $text .= '<thead><tr nobr="true" bgcolor="light grey" >';
        $text .= '<th width="'.$this->nameColWidth.'"><b>Name</b></th>';
        foreach ($this->weeksHeading as $cell) {
            $text .= '<th width="'.$this->weekColWidth.'"><b>'.$cell.'</b></th>';
        }
        $text .= '</tr></thead>';
        return $text;
    }

    private function tableBodyClassRoll($session) {
        $attendees = SessionAttendee::join('people','people.id','session_attendee.person_id')
            ->orderBy('people.last_name')->where('session_id', $session->id)->get();
        $text      = '<tbody>';
		foreach($attendees as $attendee) {
            $text         .= '<tr nobr="true"';
            if ($this->shadeRow) {
                $text     .= ' bgcolor="light grey" ';
            }
            $text         .= '>';
            $this->shadeRow = !$this->shadeRow;
            $attendee_name = $attendee->name;
            $text         .= '<td width="'.$this->nameColWidth.'">';
            if ($this->rollType['type'] == 'normal') {
                $text     .= $attendee_name;
            }
            $text         .= '</td>';
            foreach ($this->weeksHeading as $cell) {
                $text     .= '<td width="'.$this->weekColWidth.'"></td>';
            }
            $text         .= '</tr>';
        }
        $text             .= $this->blankLinesToEndOfpageClassRoll($this->linesPerPage - count($attendees));
        return $text;
    }

    private function blankLinesToEndOfpageClassRoll($remainingLines) {
        $text = '';
        for ($i = 0; $i < ($remainingLines); $i++) {
            $text         .= '<tr nobr="true"';
            if ($this->shadeRow) {
                $text     .= ' bgcolor="light grey" ';
            }
            $this->shadeRow = !$this->shadeRow;
            $text         .= '>';
            $text .= '<td width="'.$this->nameColWidth.'"></td>';
            foreach ($this->weeksHeading as $cell) {
                $text .= '<td width="'.$this->weekColWidth.'"></td>';
            }
            $text .= '</tr>';
        }
        $text .= '</tbody>';
        $text .= '</table>';
        return $text;
    }

    // ***********************************
    // Print the Contact Details functions
    // ***********************************

    private function printContactDetails($session) {
        if ($this->print['contact details']) {
            if ($this->rollType['include contact details'] | !$this->allRolls) {
                $this->setHeaderContactDetails($session, false);
                $this->formatOnePageContactDetails($session);
            }
        }
    }

    private function formatOnePageContactDetails($session) {
        $text  = $this->tableHeadingContactDetails();
        $text .= $this->tableBodyContactDetails($session);
        PDF::startPageGroup();
        PDF::AddPage();
        PDF::writeHTML($text, true, false, true, false, '');
    }

    private function setHeaderContactDetails($session) {
        $this->pageHeading = 'Class Contact Details for '.$session->name;
        $this->header();
    }

    private function tableHeadingContactDetails() {
        $text  = '<table border="1" cellpadding="3">';
        $text .= '<thead><tr nobr="true" bgcolor="light grey" >';
        $text .= '<th width="15%"><b>First Name</b></th>';
        $text .= '<th width="15%"><b>Last Name</b></th>';
        $text .= '<th width="15%"><b>Phone</b></th>';
        $text .= '<th width="40%"><b>Email</b></th>';
        $text .= '</tr></thead>';
        return $text;
    }

    private function tableBodyContactDetails($session) {
        $attendees = SessionAttendee::join('people','people.id','session_attendee.person_id')
            ->orderBy('people.last_name')->where('session_id', $session->id)
            ->select('people.*')->get();
        $text      = '<tbody>';
		foreach($attendees as $attendee) {
            $text         .= '<tr nobr="true"';
            if ($this->shadeRow) {
                $text     .= ' bgcolor="light grey" ';
            }
            $text         .= '>';
            $this->shadeRow = !$this->shadeRow;
            $attendee_first_name = $attendee->first_name;
            $attendee_last_name = $attendee->last_name;
            $attendee_phone = $attendee->phone;
            $attendee_email = $attendee->email;
            $text         .= '<td width="15%">'.$attendee_first_name.'</td>';
            $text         .= '<td width="15%">'.$attendee_last_name.'</td>';
            $text         .= '<td width="15%">'.$attendee_phone.'</td>';
            $text         .= '<td width="40%">'.$attendee_email.'</td>';
            $text         .= '</tr>';
        }
        $text             .= $this->blankLinesToEndOfpageContactDetails($this->linesPerPage - count($attendees));
        return $text;
    }

    private function blankLinesToEndOfpageContactDetails($remainingLines) {
        $text = '';
        for ($i = 0; $i < ($remainingLines); $i++) {
            $text         .= '<tr nobr="true"';
            if ($this->shadeRow) {
                $text     .= ' bgcolor="light grey" ';
            }
            $this->shadeRow = !$this->shadeRow;
            $text         .= '>';
            $text .= '<td width="15%"></td>';
            $text .= '<td width="15%"></td>';
            $text .= '<td width="15%"></td>';
            $text .= '<td width="40%"></td>';
            $text .= '</tr>';
        }
        $text .= '</tbody>';
        $text .= '</table>';
        return $text;
    }
}