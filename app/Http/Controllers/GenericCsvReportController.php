<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenericCsvReportController extends Controller
{
    public function show($reportId) {
           return view('welcome');
    }
}
