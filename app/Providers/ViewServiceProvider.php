<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // View::composer(
        //     'reports.memberDetails', function ($view) {
        //         $view->with('memberId',400);
        //     }
        // );
        View::composer(
            'reports.memberDetails', 'App\Http\View\Composers\ReportMemberDetailsComposer'
        );
        View::composer(
            'reports.briefMemberDetails', 'App\Http\View\Composers\ReportBriefMemberDetailsComposer'
        );
        View::composer(
            'reports.venueDetails', 'App\Http\View\Composers\ReportVenueDetailsComposer'
        );
        View::composer(
            'reports.courseDetails', 'App\Http\View\Composers\ReportCourseDetailsComposer'
        );
        View::composer(
            'reports.timetable', 'App\Http\View\Composers\ReportTimetableComposer'
        );
        View::composer(
            'reports.classRolls', 'App\Http\View\Composers\ReportClassRollsComposer'
        );
        View::composer(
            'reports.courseInformation', 'App\Http\View\Composers\ReportCourseInformationComposer'
        );
        View::composer(
            'reports.statistics', 'App\Http\View\Composers\ReportStatisticsComposer'
        );
        View::composer(
            'person.edit', 'App\Http\View\Composers\EditMemberComposer'
        );
        View::composer(
            'person.editContactDetails', 'App\Http\View\Composers\EditMemberComposer'
        );
        View::composer(
            'testPage', 'App\Http\View\Composers\TestPageComposer'
        );
    }
}
