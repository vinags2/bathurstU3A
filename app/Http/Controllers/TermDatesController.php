<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use App\Traits\Allowable;
use App\Helpers\Utils;

class TermDatesController extends Controller
{
    use Allowable;
    /**
     * custom error messages when incorrect data is entered
     */
    private $messages = [
            'total_number_of_weeks.max' => 'The total number of weeks cannot exceed 45.',
            'rejoin_start_date.after_or_equal' => 'The date when members may rejoin for next year must be a date this year.',
            'rejoin_start_date.before_or_equal' => 'The date when members may rejoin for next year must be a date this year.',
            'date' => 'The dates must be sequential, not overlap, and be this year',
            'after_or_equal' => 'The dates must be sequential, not overlap, and be this year',
            'before_or_equal' => 'The dates must be sequential, not overlap, and be this year',
            'before' => 'The dates must be sequential, not overlap, and be this year',
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
    public function store(Request $request)
    {
       $validatedData = $request->validate($this->rules($request), $this->messages);
       if ($request->sessionId) {
           dd('save course dates');
       } else {
            Setting::myUpdate($this->formattedValidatedData($validatedData));
            return redirect()->route('home')
                ->withSuccess('The term dates have been updated successfully');
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $sessionId = null)
    // public function edit($id)
    {
        $request->request->add(['sessionId' => $sessionId]);
        return static::userAllowable('editTermDates');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
        /**
         * for i = 1 to number_of_terms
         *      term{i} is a date
         *      term{i} is required
         *      term{i} is a date this year
         *      term{i}_enddate is a date
         *      term{i}_enddate is required
         *      term{i}_enddate is a date this year
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
            $rule = 'bail|required|date|after_or_equal:'.$day1.'|before_or_equal:'.$lastDay.'|before:term'.$i.'_enddate';
            $extraRule = 'bail|required|date|after_or_equal:'.$day1.'|before_or_equal:'.$lastDay;
            if ( $i < $number_of_terms) {
                $extraRule.= '|before:term'.($i+1);
            }
            $rules = $rules + [ 'term'.$i => $rule];
            $rules = $rules + ['term'.$i.'_enddate' => $extraRule];
        }
        return $rules;
    }

    private function termDatesToJson($termDates) {
        $term = [];
        $index = 0;
        $newIndex = 0;
        $start = true;
        foreach ($termDates as $key => $value) {
            if ($start) {
                $term[$newIndex] = ['start' => $value];
            } else {
                $term[$newIndex] = $term[$newIndex] + ['end' => $value];
                $newIndex++;
            }
            $index++;
            $start = !$start;
        }
        return json_encode($term);
    }

    private function formattedValidatedData($validatedData) {
        $formatted = array_splice($validatedData,3);
        return array_splice($validatedData,0,2) + ['terms' => $this->termDatesToJson($formatted)];
    }
}
