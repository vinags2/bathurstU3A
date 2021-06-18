<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;
use App\Report;

class ReportController extends Controller
{
    public function index($reportId)
    {
        // If the report is a generic report, call the Generic Report Generator
        if (ReportRepository::isGeneric($reportId)) {
            $reportGenerator = new GenericReportController($reportId);
            return $reportGenerator->show();

        // If the report is a PDF report, call the PDF Report Generator
        } elseif (ReportRepository::isPdf($reportId)) {
            return view('notDoneYet', ['report' => $reportId]);

        // If the report is a Blade View, route to the view
        } elseif (ReportRepository::isView($reportId)) {
            return view(ReportRepository::bladeData($reportId)['view']);
            // TODO: create a composer for the views in ReportRepository (see doco on Laravel and Views and Composers)
            
        // If the report is unkown, display an 'Under Development' view
        } else {
            return view('notDoneYet', ['report' => $reportId]);
        }
    }
}
