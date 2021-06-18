<?php

namespace App\Http\Controllers;

use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Person;
use App\Exports\GenericReportCsvExport;

const PDFPATH = 'report/pdf/*';
const CSVPATH = 'report/csv/*';

class GenericReportController extends Controller
{
    private $reportData;
    private $columnAttributes;
    private $markupSource;
    private $cellData;
    private $cellAttributes;
    private $total;
    private $links;
    private $pageHeading;
    private $downloadLinks;
    private $isDataBeingDownloaded;

    /**
     * Load the data
     */
    public function __construct($reportId)
    {
        $repositoryData         = ReportRepository::bladeData($reportId);
        $this->pageHeading      = $repositoryData['pageHeading'];
        $this->columnAttributes = $repositoryData['columnAttributes'];
        $this->markupSource     = $repositoryData['markupSource'];
        $this->isDataDownloaded = (request()->is(PDFPATH) or request()->is(CSVPATH));
        $this->reportData       = $this->getData($repositoryData['query']);
        $this->cellData         = $this->ExtractDataFromCollection();
        $this->total            = $this->reportData->total();
        $this->links            = $this->reportData->links();
        $this->downloadLinks    = $this->getDownloadLinks($reportId);
    }

    /**
     * The function called to show the report
     */
    public function show() {
        if (auth()->user()->can('basic member')) {
            abort(403);
        }
        $this->convertTableDataForBlade();
        return $this->output($this->pageHeading);
    }

    /**
     * Extract items() from the paginate collection, and convert to an array.`
     */
    private function ExtractDataFromCollection() {
        return json_decode(json_encode($this->reportData->items()), true);
    }

    /**
     * Convert the data into a format required to download or display the data
     */
    private function convertTableDataForBlade() {
        $this->addAllCellAttributes();
        $this->removeHiddenColumns();
    }

    /**
     * For every cell, add attributes required by Blade, etc such as the href's for the anchor html tags
     */
    private function addAllCellAttributes() {
        foreach($this->cellData as $index => $row) {
            foreach($row as $columnName => $cell) {
                if (!empty($this->columnAttributes[$columnName])) {
                    $this->addCellAttributes($index, $columnName, $row, $cell);
                }
            }
        }
    }

    /**
     * add the attributes required by Blade, etc for one cell, such as the href anchor tag.
     */
    private function addCellAttributes($index, $column, $row, $cell) {
        if ($this->addCellAttribute($cell, $index, $column, $row, 'email') or 
            ($this->addCellAttribute($cell, $index, $column, $row, 'member')) or
            ($this->addCellAttribute($cell, $index, $column, $row, 'venue')) or
            ($this->addCellAttribute($cell, $index, $column, $row, 'course')) or
            ($this->addCellAttribute($cell, $index, $column, $row, 'address'))
            ) { ; }
    }

    /**
     * Add the attribute for the $specialColumn, where $specialColumn = 'email' | 'member | 'address' | etc
     */
    private function addCellAttribute($cell, $index,$column, $row, $specialColumn) {
        if (strpos($this->columnAttributes[$column],$specialColumn) !== false) {
            $this->cellAttributes[$index][$column]['href'] = (!empty($this->markupSource[$specialColumn]))
                ? $row[$this->markupSource[$specialColumn]]
                : $cell;
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Remove hidden columns from the data to be displayed/downloaded (eg the 'id' column)
     */
    private function removeHiddenColumns() {
        $keysToRemove = $this->keysToRemove();
        array_walk($keysToRemove, function ($keyToRemove, $key) {
            array_walk($this->cellData,function(&$value, $dummy, $key) {
                unset($value[$key]);
            }, $key);
        });
    }

    /**
     * Return the keys to delete -- used in 'removeHiddenColumns'
     */
    private function keysToRemove() {
        return array_filter($this->columnAttributes, function($columnAttribute) {
            return (strpos($columnAttribute, 'hidden') !== false);
        });
    }

    // return the routes to download data as PDF and CSV, for creating links on the view
    private function getDownloadLinks($reportId) {
        return [
            route('pdf',$reportId),
            route('csv',$reportId)
        ];
    }

    // if downloading as PDF or CSV, get all of the data, otherwise get a page worth of data
    private function getData($query) {
        if ($this->isDataDownloaded) {
            return $query->paginate(100000);
        } else {
            return $query->paginate(50);
        }
    }

    /**
     * Prepend the heading as the first row of the data, which is required by the CSV download
     */
    private function prependHeading($data) {
        $heading = [array_keys($data[0])];
        return $heading + $data;
    }

    // return the file downloads or the view
    private function output($pageHeading) {
        // dd('cellAttributes:', $this->cellAttributes, 'columnAttributes:', $this->columnAttributes,'markupSource:',  $this->markupSource,'cellData:',  $this->cellData,'reportData:',  $this->reportData);
        if (request()->is(PDFPATH)) {
            return (new \App\Exports\GenericReportPdfExport($this->cellData, $pageHeading))->show();
        } elseif (request()->is(CSVPATH)) {
            return \Excel::download(new GenericReportCsvExport($this->prependHeading($this->cellData)),
                $pageHeading.'.csv',
                \Maatwebsite\Excel\Excel::CSV,
                ['Content-Type' => 'text/csv']
            );
        } else {
            return view('reports.generic',[
                'data'        => $this->cellData,
                'attributes'  => $this->cellAttributes,
                'pageHeading' => $pageHeading,
                'total'       => $this->total,
                'links'       => $this->links,
                'pdf'         => $this->downloadLinks[0],
                'csv'         => $this->downloadLinks[1]
            ]);
        };
    }
}
