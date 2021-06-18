<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenericPdfReportController extends Controller
{
    public function show($reportId) {
           return view('welcome');
    }
}
