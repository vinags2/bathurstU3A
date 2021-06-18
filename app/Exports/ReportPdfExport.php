<?php
namespace App\Exports;

use PDF;

abstract class ReportPdfExport
{
    protected $data;
    protected $pageHeading;

    abstract protected function formatData();

    public function __construct($data, $pageHeading) 
    {
        $this->data        = $data;
        $this->pageHeading = $pageHeading;
    }

    public function show() {

        $this->documentInformation();
        $this->appearance();
        $this->footer();
        $this->header();
        $this->formatData();
        $this->output();
    }

    protected function documentInformation() {
        PDF::SetCreator('Greg Vinall');
        PDF::SetAuthor('Greg Vinall');
        PDF::SetTitle($this->pageHeading);
        PDF::SetSubject($this->pageHeading);
        PDF::SetKeywords('TCPDF, PDF, Bathurst, U3A');
    }

    protected function appearance() {
        PDF::SetAutoPageBreak(TRUE, 20);
        PDF::setImageScale(1.25);
        PDF::SetMargins(15,20,15); // left, top and right margins
        PDF::SetFont(
            'helvetica',
            '',
            10
        );
    }

    protected function footer() {
        PDF::SetPrintFooter(true);
        PDF::setFooterCallBack(function($pdf) { 
            $pdf->SetY(-15);
            $pdf->SetFont('helvetica', 'I', 8);
            $tDate=date('F j, Y');
            $pdf->Cell(0, 10, 'Printed on '.$tDate, 0, false, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 10, 'Page '.$pdf->getPageNumGroupAlias().' of '.$pdf->getPageGroupAlias(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        });
    }

    protected function header() {
        PDF::setHeaderCallback(function($pdf) {
            $img = asset('dist/bathurstU3A.gif');
            $pdf->SetY(+9);
            $pdf->SetX(+55);
            // Set font
            $pdf->SetFont('helvetica', 'B', 14);
            // Header text set to blue
            $pdf->SetTextColor(0, 0, 255);
            // Title
            $pdf->Cell(0, 15, $this->pageHeading, 0, false, 'L', 0, '', 0, false, 'M', 'M');
            $pdf->Image($img, 15,5,30,10);
        });
    }

    protected function output() {
        PDF::Output($this->pageHeading.'.pdf', "D");
    }
}