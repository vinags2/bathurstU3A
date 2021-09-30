<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UpdateByable;
use Carbon\Carbon;
use App\Helpers\Utils;

class Setting extends Model
{

    use UpdateByable;

    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * return the setting for the current year
     * 
     * Check if the settings for the current year exist in the DB
     * If not, then create a new record (see the function static::creating below)
     * Return the setting for the current year from the DB
     */
    static public function currentSetting() {
        $currentYear = Utils::currentYear();
        return Setting::firstOrCreate(['year' => $currentYear],
        [
            'year'              => $currentYear,
            'yearly_reset'      => 0,
            'weeks_in_term'     => 8,
            'title'             => 'The Bathurst U3A Database V2',
            'header_image'      => 'res/cropped-Autumn-29.jpg',
            'db_home'           => 'http://bathurstu3a.com/db2/index.php',
            'db_home_local'     => 'http://127.0.0.1/~gregvinall/bathurstu3a/db2/index.php',
            'number_of_terms'   => 4,
            'terms'             => Setting::termDates(),
            'email_of_dbadmin'  => 'webadmin@bathurstu3a.com',
            'rejoin_start_date' => $currentYear * 10000 + 1200 + 1,
        ]);
    }

    /** 
     * calculate plausible start dates for each term based on
     * $number_terms and $weeks_in_term as provided by user
     * 
     * $total_number_of_weeks = $number_of_terms * $weeks_in_term
     * if (44 - $total_number_of_weeks) > $number_of_terms then
     *      $weeks_in_term = INT((44-$number_of_terms)/$number_of_terms)
     * [That is allowing for 1 week between each term, plus 4 weeks at the end of, and 5 weeks at the beginning of the year]
     * 
     * $weeks_between_terms = INT((43-$total_number_of_weeks)/$number_of_terms)
     * 
     * First term starts the Monday in week 5
     * First term ends the Friday in the week prior to $weeks_in_term after start date of first term
     * 
     * Second term starts the Monday following end of first term + $weeks_between_terms
     * Second term ends the Fruday in the week $weeks_in_term after start date of second term
     * 
     * etc
     * 
    */
    static private function termDates() {
    // static private function termDates($setting) {
        // $setting = Setting::currentSetting();
        $currentYear = Utils::currentYear();
        $number_of_terms = 4; //$setting->number_of_terms;
        $weeks_in_term =  8; //$setting->weeks_in_term;
        // $number_of_terms = 4; //$setting->number_of_terms;
        // $weeks_in_term = 8; // $setting->weeks_in_term;
        $total_number_of_weeks = $number_of_terms * $weeks_in_term;
        if ($total_number_of_weeks > (44 - $number_of_terms)) {
            $weeks_in_term = round((44 - $number_of_terms)/$number_of_terms);
            $total_number_of_weeks = $number_of_terms * $weeks_in_term;
        }
        $weeks_between_terms = round((43-$total_number_of_weeks)/$number_of_terms);
        $termDate = (new Carbon('first monday of January ' . $currentYear))->subDays(3)->addWeeks(5);
        $term_dates = [];
        for ($i = 1; $i <= $number_of_terms; $i++) {
            $term_dates[$i-1] = ['start' => $termDate->addDays(3)->toDateString(), 'end' => $termDate->addWeeks($weeks_in_term)->subDays(3)->toDateString()];
            $termDate->addWeeks($weeks_between_terms);
        }
        // dd($term_dates);
        return json_encode($term_dates);
    }

    static public function myUpdate($attributes) {
        $currentYear = Utils::currentYear();
        Setting::where('year',$currentYear)->update($attributes);
    }

    /**
     * Get the person who updated the record
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Person','id','updated_by');
    }

    /**
     * Return a collection of term dates compared to today
     * If $alternateTermDates is not null, this will be used instead of the term dates in settings
     * Returned: relativeTermDates where
     *      relativeTermDates->term = which term today is in
     *      relativeTermDates->thisTermStartDate = the date that this term started
     *      relativeTermDates->thisTermEndDate = the date that this term ends
     *      relativeTermDates->nextTermStartDate = the date that next term starts
     *      relativeTermDates->nextTermEndDate = the date that next term ends
     *      relativeTermDates->startOfYear = the date that the year starts (date of start of term one)
     *      relativeTermDates->endOfYear = the date that the year ends (date of end of the last term)
     *      relativeTermDates->startOfLastTerm = the date of the start of the last term
     *      relativeTermDates->numberOfTerms = session->number_of_terms
     * 
     *  Logic:
     *  - if today is after the last term date of the year, set all settings to null
     *  - else for each term:
     *  - if today is before the start of term, set current term settings to null, and next term to appropriate values
     *  - if today is before (or equal to) the end of term, set this term settings, and last term settings
     *          (alllowing for the last term of the year to not have a next term)
     */
    static public function termDatesComparedToToday($alternateTermDates = NULL) {
        $termDates = json_decode($alternateTermDates ?? Setting::currentSetting()->terms);
        $today = Utils::today();
        $numberOfTerms = count($termDates)-1; // '-1' for zero-based arrays

        $relativeTermDates      = ['startOfYear'        => $termDates[0]->start];
        $relativeTermDates     += ['endOfYear'          => $termDates[$numberOfTerms]->end];
        $relativeTermDates     += ['startOfLastTerm'    => $termDates[$numberOfTerms]->start];
        $relativeTermDates     += ['numberOfTerms'      => $numberOfTerms+1];
        if ($today > $termDates[$numberOfTerms]->end) {
            $relativeTermDates += ['term'               => null];
            $relativeTermDates += ['thisTermStartDate'  => null];
            $relativeTermDates += ['thisTermEndDate'    => null];
            $relativeTermDates += ['nextTermStartDate'  => null];
            $relativeTermDates += ['nextTermEndDate'    => null];
        } else {
            for ($i = 0; $i <= $numberOfTerms; $i++) {
                if ($today < $termDates[$i]->start) {
                    $relativeTermDates += ['term'                   => null];
                    $relativeTermDates += ['thisTermStartDate'      => null];
                    $relativeTermDates += ['thisTermEndDate'        => null];
                    $relativeTermDates += ['nextTermStartDate'      => $termDates[$i]->start];
                    $relativeTermDates += ['nextTermEndDate'        => $termDates[$i]->end];
                    break;
                }
                if ($today <= $termDates[$i]->end) {
                    $relativeTermDates += ['term'                   => $i+1];
                    $relativeTermDates += ['thisTermStartDate'      => $termDates[$i]->start];
                    $relativeTermDates += ['thisTermEndDate'        => $termDates[$i]->end];
                    if ($i = $numberOfTerms) {
                        $relativeTermDates += ['nextTermStartDate'  => null];
                        $relativeTermDates += ['nextTermEndDate'    => null];
                    } else {
                        $relativeTermDates += ['nextTermStartDate'  => $termDates[$i+1]->start];
                        $relativeTermDates += ['nextTermEndDate'    => $termDates[$i+1]->end];
                    }
                    break;
                }
            }
        }
        return (object) $relativeTermDates;
    }

    /**
     * return the most appropriate time for when user changes should take effect
     *      'nextYear' if today is after the start of the last term of the year
     *      'immediately' if not during term 
     *      'nextTerm' otherwise
     */
    static public function effectiveFrom() {
        $relativeTermDates = Setting::termDatesComparedToToday();
        $today = Utils::today();

        if ($today >= $relativeTermDates->startOfLastTerm) {
            return 'nextYear';
        }
        if (!isset($relativeTermDates->term)) {
            return 'immediately';
        }
        return 'nextTerm';
    }

    static public function effectiveFromOptions($includeOther = false) {
        $lav = [];
        $lav[0] = (object) ['label' => 'next term', 'value' => 'nextTerm'];
        $lav[1] = (object) ['label' => 'next year', 'value' => 'nextYear'];
        $lav[2] = (object) ['label' => 'immediately', 'value' => 'immediately'];
        if ($includeOther) {
            $lav[3] = (object) ['label' => 'other', 'value' => 'other'];
        }
        return json_encode($lav);
    }
}
