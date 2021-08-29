<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\Utils;
use App\Traits\Allowable;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    use Allowable;
    /**
     * custom error messages when incorrect data is entered
     */
    private $messages = [
            'total_number_of_weeks.max' => 'The total number of weeks cannot exceed 45.',
            'rejoin_start_date.after_or_equal' => 'The date when members may rejoin for next year must be a date this year.',
            'rejoin_start_date.before_or_equal' => 'The date when members may rejoin for next year must be a date this year.',
            'date' => 'The dates must be sequential and not overlap',
        ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if ($request->filled('SettingsPage1')) {
            $validatedData = $request->validate($this->rules($request), $this->messages);
            Setting::myUpdate($validatedData);
            $request->session()->now('success','The settings have been updated successfully');
            return view('editTermDates');
        } elseif ($request->filled('editTermDates')) {

        $validator = Validator::make($request->all(), $this->rules($request));
            // 'title' => 'required|unique:posts|max:255',
            // 'body' => 'required',
        // ]);

        if ($validator->fails()) {
            // Input::old();
            // $request->flash();
            return view('editTermDates');
            // return redirect('post/create')
                        // ->withErrors($validator)
                        // ->withInput();
        }

            // dd($request, $this->rules($request));
            // $validatedData = $request->validate($this->rules($request), $this->messages);
            $request->session()->now('success','The settings have been updated successfully');
            return view('welcome');
        } else {
            return view('welcome');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        return static::userAllowable('editSettings');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
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
    private function termDates() {
        $setting = Setting::currentSetting();
        $number_of_terms = $setting->number_of_terms;
        $weeks_in_term = $setting->weeks_in_term;
        $total_number_of_weeks = $number_of_terms * $weeks_in_term;
        if ($total_number_of_weeks > (44 - $number_of_terms)) {
            $weeks_in_term = round((44 - $number_of_terms)/$number_of_terms);
            $total_number_of_weeks = $number_of_terms * $weeks_in_term;
        }
        $weeks_between_terms = round((43-$total_number_of_weeks)/$number_of_terms);
        $termDate = (new Carbon('first monday of January'))->subDays(3)->addWeeks(5);
        $term_dates = [];
        for ($i = 1; $i <= $number_of_terms; $i++) {
            $term_dates[$i] = ['from' => $termDate->addDays(3)->toDateString(), 'to' => $termDate->addWeeks($weeks_in_term)->subDays(3)->toDateString()];
            $termDate->addWeeks($weeks_between_terms);
        }
        return $term_dates;
    }

    /**
     * validation rules
     * 
     * memberships for next year start date must be this year
     * 'number of terms' times 'weeks per term' must be less then 45 weeks
     */
    private function rules($request) {
        $day1 = Utils::currentYear().'/01/01';
        $lastDay = Utils::currentYear().'/12/31';
        if ($request->filled('SettingsPage1')) {
            $rules = [
                'rejoin_start_date' => 'required|after_or_equal:'.$day1.'|before_or_equal:'.$lastDay,
            ];
        } else {
            /**
             * for i = 1 to number_of_terms
             *      term{i} is a date
             *      term{i} is required
             *      term{i}_enddate is a date
             *      term{i}_enddate is required
             *      term{i} < term{i}_enddate
             *      for i < number_of_terms, term{i}_enddate < term{i+1}
             */
            $rules = [
                'number_of_terms'       => 'required|numeric|max:9',
                'weeks_in_term'         => 'required|numeric',
                'total_number_of_weeks' => 'required|numeric|max:45',
            ];

            $number_of_terms = $request->input('number_of_terms');
            for ($i = 1; $i <= $number_of_terms; $i++) {
                // $rule = 'bail|required|date|before:2021-12-12';
                $rule = 'required|date|before:term'.$i.'_enddate';
                $extraRule = 'bail|required|date';
                if ( $i < $number_of_terms) {
                    // $extraRule.= '|before:2021-12-12';
                    $extraRule.= '|before:term'.($i+1);
                }
                $rules = $rules + [ 'term'.$i => $rule];
                $rules = $rules + ['term'.$i.'_enddate' => $extraRule];
            }
            // dd($rules);
        }
        return $rules;
    }
}
