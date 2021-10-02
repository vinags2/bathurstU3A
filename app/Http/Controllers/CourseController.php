<?php

namespace App\Http\Controllers;

use App\Course;
use App\Session;
use App\Traits\Allowable;
use App\Helpers\Utils;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use Allowable;

    /**
     * validation rules
     * 
     * Course name must be unique, and required (to be done in another version)
     * Course description < 401 characters
     * Course notes < 101 characters
     * Course name < 41 characters
     * 
     * Session name < 41 characters, and required
     * Session description < 101 characters
     * Class size: min < max, min >= 0
     * Start time < End time
     * Session notes < 41 characters
     * 
     * nullable for all other fields
     */
    private $rules = [
        'course_name' => 'required|max:40',
        'course_comment' => 'nullable|max:100',
        'course_description' => 'nullable|max:400',
        'sessionNames.*' => 'required|max:40',
        'sessionDescriptions.*' => 'max:100',
        'sessionComments.*' => 'max:40',
        'sessionMinClassSizes.*' => 'nullable|lte:sessionMaxClassSizes.*',
        'sessionMaxClassSizes.*' => 'nullable',
        'sessionEndTimes.*' => 'date_format:H:i|after:sessionStartTimes.*',
        'sessionStartTimes.*' => 'required',
        'sessionIds.*' => 'nullable',
        'facilitatorid.*' => 'nullable',
        'alternatefacilitatorid.*' => 'nullable',
        'venueid.*' => 'nullable',
        'sessionDayOfTheWeeks.*' => 'numeric|max:6',
        'sessionWeekOfTheMonths.*' => 'numeric|max:5',
        'sessionActiveTerms.*' => 'required',
    ];

    private $messages = [
        'course_name.required' => 'The course name is required',
        'sessionMinClassSizes.*.lte' => 'Minimum class size must be less than maximum class size',
        'sessionEndTimes.*.after' => 'Start time must be before the end time of the session',
        'sessionNames.*.required' => 'The session name is required',
        'sessionNames.*.max' => 'The session name cannot be longer than 40 characters',
        'sessionComments.*.max' => 'The session notes cannot be longer than 40 characters',
        'sessionDescriptions.*.max' => 'The session description cannot be longer than 100 characters',
        'course_comment.max' => 'The course notes cannot be longer than 100 characters',
        'course_description.max' => 'The course description cannot be longer than 400 characters',
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
        // 2. firstly delete the sessions marked 'deleted'
        //      then to the validation with rules like ['venues.*.name' => 'required']
        // 3. don't forget that venues and facilitators may be null...they will be printed as 'to be confirmed'

        /**
         * Savimg routine:
         *
         * If course deleted
         *      - delete course from courses table
         *      - set the effective_to of the latest course record in course_histories to the effective_from of the form
         *      - delete the sessions from the sessions table
         *      - set the effective_to of the latest sessions record in session_histories to the effective_from of the form
         *  Update (if existing) or create (if new course) the latest course record in the courses table
         *  If today is later than the effective_from date in the course_histories table for the latest course record, then create a new record
         *  Otherwise update the latest record.
         *  Same for the Sessions and session_histories tables
         *  Think about the following situations:
         *      - mid term and effective_date in the form is immediately, or other's date is before end of term
         *  
         */ 

        $request = $this->massageRequestData($request);
        if ($request->filled('new')) {
            return back()->withInput();
        }
        $validatedData      = $request->validate($this->rules, $this->messages);
        dd($request->input(), $validatedData);
    }

    // Convert any 0 values in Class Sizes to nulls, so that the validation rules will work
    private function massageRequestData($request) {
        $newRequestData = $this->massageClassSizes($request);
        $request->merge($newRequestData);
        $newRequestData = $this->massageCheckBoxes($request);
        $request->merge($newRequestData);
        return $request;
    }

    // Convert any 0 values in Class Sizes to nulls, so that the validation rules will work
    private function massageClassSizes($request) {
        $classSizes = [$request->sessionMinClassSizes, $request->sessionMaxClassSizes] ;
        $classNames = ['sessionMinClassSizes', 'sessionMaxClassSizes'];
        foreach ( $classSizes as $outerKey =>$minArray) {
            foreach ($minArray as $key => $item) {
                $t[$classNames[$outerKey]][$key] = $item == 0 ? null : $item;
            }
        }
        return $t;
    }

    // Fill any unclicked checkboxes with a 0 value
    private function massageCheckBoxes($request) {
        $totalNumberOfSessions = $request->numberOfSessions + $request->numberOfNewSessions;
        $numberOfTerms = $request->numberOfTerms;
        $activeTerms = $request->sessionActiveTerms;
        for ($i=0; $i<$totalNumberOfSessions; $i++) {
            for ($j=0; $j<$numberOfTerms; $j++) {
                $t['sessionActiveTerms'][$i][$j] = isset($activeTerms[$i][$j]) ? $activeTerms[$i][$j] : "0";
            }
        }
        return $t;
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
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        return static::userAllowable('course.edit');
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
     * Return a json of close-matching addresses
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $course = $request->input('name');
        $courses = Course::select('id','name', 'description')
            ->where('name','like','%'.$course.'%')
            ->limit(5)
            ->orderBy('name')
            ->get();
        return response()->json($courses);
    }

}
