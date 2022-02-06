<?php

namespace App\Http\Controllers;

use App\Course;
use App\Course_history;
use App\Session;
use App\Session_history;
use App\Setting;
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
        /**
         * Savimg routine:
         * 
         * 1. If course deleted, delete the course and all related sessions in the courses and sessions tables.
         *      Set the effective_to dates in the histories tables.
         * 
         * 3. if course changed:
         *      a. save course to courses table (using updateOrCreate -- course_id and course_name must be unique)
         *      b. updateorcreate entry in course_histories table with the effective_from date (course_id, effective_from date must be unique).
         *          Set effective_to to null.
         * 4. For each session:
         *      a. if deleted, delete from sessions table, and complete effective_to date in session_histories table
         *      b. updateorcreate entry in session_histories table with the effective_from date (session_id, effective_from date must be unique).
         *          Set effective_to to null.
         *
         *  If the session runs all year, set the session->terms to one term which starts on the first day of the normal term 1,
         *          and ends on the last day of the normal last term of the year.
         * 
         */ 

        $courseName = $request->course_name;

        // If user selected 'new session', add a new session
        $request = $this->massageRequestData($request);
        if ($request->filled('new')) {
            return back()->withInput();
        }

         // If user selects 'Delete course', delete course
        if ($request->filled('course_deleted')) {
            $this->destroy($request);
            return redirect()->route('course.edit')->with('success', $courseName.' has been deleted successfully');
        }

        $validatedData = $request->validate($this->rules, $this->messages);

        // Save course details if there are changes
        $this->storeCourse($request, $validatedData);

        // Save sessions including deleted sessions if requested
        $this->storeSessions($request, $validatedData);

        return redirect()->route('course.edit')->with('success', $courseName.' changes have been saved successfully');
    }

    // Ensure the data is uniform and consistent in the Request variable.
    private function massageRequestData($request) {
        $newRequestData = $this->massageClassSizes($request);
        $request->merge($newRequestData);
        $newRequestData = $this->massageTermCheckBoxes($request);
        $request->merge($newRequestData[0]);
        $request->merge($newRequestData[1]);
        $newRequestData = $this->massageAllYearCheckBoxes($request);
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

    // Fill any unclicked term checkboxes with a 0 value
    private function massageTermCheckBoxes($request) {
        $totalNumberOfSessions = $request->numberOfSessions + $request->numberOfNewSessions;
        $numberOfTerms = $request->numberOfTerms;
        $activeTerms = $request->sessionActiveTerms;
        $allYearInstead = $request->sessionAllYearInstead;
        for ($i=0; $i<$totalNumberOfSessions; $i++) {
            for ($j=0; $j<$numberOfTerms; $j++) {
                if (isset($activeTerms[$i]) or isset($allYearInstead[$i])) {
                    $t['sessionActiveTerms'][$i][$j] = isset($activeTerms[$i][$j]) ? intval($activeTerms[$i][$j]) : 0;
                    $u['sessionAllYearInstead'][$i] = isset($allYearInstead[$i]) ? intval($allYearInstead[$i]) : 0;
                }
            }
        }
        return [$t,$u];
    }

    // Fill any unclicked allyear checkboxes with a 0 value
    private function massageAllYearCheckBoxes($request) {
        $totalNumberOfSessions = $request->numberOfSessions + $request->numberOfNewSessions;
        $allYears = $request->sessionAllYearInstead;
        for ($i=0; $i<$totalNumberOfSessions; $i++) {
            if (isset($allYears[$i])) {
                $t['sessionAllYearInstead'][$i] = intval($allYears[$i]);
            } else {
                $t['sessionAllYearInstead'][$i] = 0;
            }
        }
        return $t;
    }

    /**
     * save the Course data if there are changes
     */
    private function storeCourse($request, $validatedData) {

    }

    /**
     * save the Session data if there are changes
     * including deleting sessions if requested
     */
    private function storeSessions($request, $validatedData) {

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
     * Remove the Course and associated Sessions from the course, session, course_histories and session_histories tables
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     *  - set the effective_to of the latest course record in course_histories to the effectiveTo from Sessions::effectiveToDate, and check that effectiveTo >= effectiveFrom
     *  - delete course from courses table
     *  - set the effective_to of the latest sessions record in session_histories to that of the effectiveTo of the courses_histories table
     *  - delete the sessions from the sessions table
     * 
     */
    public function destroy(Request $request)
    {
        $courseId = $request->id;
        $sessionIds = collect($request->sessionIds);
        $effectiveToDate = Setting::effectiveToDate($request->effective_from);
        $this->destroySessions($sessionIds, $effectiveToDate);
        $this->destroyCourse($courseId, $effectiveToDate);
    }

    private function destroyCourse($id, $effectiveToDate) {
        $course = Course_history::
            where(['course_id' => $id, 'effective_to' => null])->first();
        $course->effective_to = $effectiveToDate;
        $course->save();
        $course = Course::find($id);
        $course->delete();
    }

    private function destroySessions($sessionIds, $effectiveToDate) {
        foreach ($sessionIds as $sessionId) {
            $session = Session_history::
                where(['session_id' => $sessionId, 'effective_to' => null])->first();
            $session->effective_to = $effectiveToDate;
            $session->save();
            $session = Session::find($sessionId);
            $session->delete();
        }
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
