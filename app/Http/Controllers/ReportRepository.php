<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

class ReportRepository extends Controller
{
    public static function bladeData($reportId) {
        // $user = auth()->user();
        // Log::info('report '.$reportId,['user' => $user->name]);
        switch ($reportId):
            // Download and print class rolls and contact details
            case 3:
                return [
                    'pageHeading' => 'Class Rolls',
                    'view'        => 'reports.classRolls',
                    'type'        => 'view',
                ];
            break;
            // Download and print the weekly timetable
            case 4:
                return [
                    'pageHeading' => 'Timetable',
                    'view'        => 'reports.timetable',
                    'type'        => 'view',
                ];
            break;
            // Download and print the course information sheet
            case 5:
                return [
                    'pageHeading' => 'Course Information',
                    'view'        => 'reports.courseInformation',
                    'type'        => 'view',
                ];
            break;
            // a list of all members' contact details
            case 6:
                return [
                    'query' =>
                        DB::table('people')
                            ->select('people.id AS id','first_name AS first name', 'last_name AS last name')
                            ->addSelect('address', 'suburb', 'postcode', 'any_phone AS phone', 'email')
                            ->leftJoin('addresses','people.postal_address','=','addresses.id')
                            ->orderByRaw('people.last_name, people.first_name')
                            ->where([
                                ['people.member',true],
                                ['people.deleted',0]
                            ]),
                    'pageHeading' => 'Members\' Contact Details',
                    'type'        => 'generic',
                    'columnAttributes' => ['email' => 'email', 'id' => 'hidden, memberSource', 'last name' => 'member', 'address' => 'address'],
                    'markupSource' => ['member' => 'id']
                ];
                break;

            // a list of the committee members and their position and contact details,
            // ordered appropriately
            case 7:
                return [
                    'query' =>
                        DB::table('people')
                            ->select('people.id AS id','first_name AS first name', 'last_name AS last name', 'committee_position AS position')
                            ->addSelect('address', 'suburb', 'postcode', 'any_phone AS phone', 'email')
                            ->leftJoin('addresses','people.postal_address','=','addresses.id')
                            ->orderBy('committee_member')
                            ->where([
                                ['people.member',true],
                                ['people.deleted',0],
                                ['committee_member', '<>', 0]
                            ]),
                    'pageHeading' => 'Committee Contact Details',
                    'type'        => 'generic',
                    'columnAttributes' => ['email' => 'email', 'id' => 'hidden, memberSource', 'last name' => 'member', 'address' => 'address'],
                    'markupSource' => ['member' => 'id']
                ];
                break;
                
            // a list of the courses and pertinent details (eg facilitator, venue, etc)
            // ordered appropriately
            case 9:
                return [
                    'query' =>
                        DB::table('sessions')
                            ->select('people.id AS id','sessions.name AS course', 'sessions.course_id AS courseId', 'venues.id AS venueId', 'venues.name AS venue','people.name AS facilitator', 'people2.name AS backup facilitator')
                            ->addSelect('people.any_phone AS phone', 'people.email', 'day', 'start', 'end')
                            ->leftJoin('venues','venues.id','=','sessions.venue_id')
                            ->leftJoin('people','people.id','=','sessions.facilitator')
                            ->leftJoin('people AS people2','people.id','=','sessions.alternate_facilitator')
                            ->leftJoin('courses','courses.id','=','sessions.course_id')
                            ->where('sessions.deleted',0)
                            ->where('sessions.suspended',0)
                            ->where('courses.suspended',0)
                            ->where('courses.no_longer_offerred',0)
                            ->orderBy('sessions.name'),
                    'pageHeading' => 'Courses\' Details',
                    'type'        => 'generic',
                    'columnAttributes' => ['email' => 'email', 'id' => 'hidden, memberSource', 'facilitator' => 'member', 'backup facilitator' => 'member', 'venue' => 'venue', 'venueId' => 'hidden, venueSource', 'course' => 'course', 'courseId' => 'hidden, courseSource'],
                    'markupSource' => ['member' => 'id', 'venue' => 'venueId', 'course' => 'courseId']
                ];
                break;

            // a list of the addresses of members who live in urban Bathurst
            // asked for by Andrew Wells (treasurer) for possibly hand-delivering their newsletters
            // Currently not on any menu item.
            case 14:
                return [
                    'query' =>
                        DB::table('people')
                            ->select('people.id AS id','first_name AS first name', 'last_name AS last name')
                            ->addSelect('address', 'suburb', 'postcode')
                            ->leftJoin('addresses','people.residential_address','=','addresses.id')
                            ->orderByRaw('suburb, last_name')
                            ->where([
                                ['people.member',true],
                                ['people.deleted',0],
                                ['people.prefer_email',0],
                            ])
                            ->where(function ($query) {
                                $query->where('suburb', 'kelso')
                                    ->orWhere('suburb', 'bathurst')
                                    ->orWhere('suburb', 'west bathurst')
                                    ->orWhere('suburb', 'abercrombie')
                                    ->orWhere('suburb', 'eglinton')
                                    ->orWhere('suburb', 'gormans hill')
                                    ->orWhere('suburb', 'Laffing Waters')
                                    ->orWhere('suburb', 'Llanarth')
                                    ->orWhere('suburb', 'Mt Panorama')
                                    ->orWhere('suburb', 'Robin Hill')
                                    ->orWhere('suburb', 'South Bathurst')
                                    ->orWhere('suburb', 'Windradyne');
                            }),
                    'pageHeading' => 'Members who live in the Bathurst Urban Area',
                    'type'        => 'generic',
                    'columnAttributes' => ['email' => 'email', 'id' => 'hidden, memberSource', 'last name' => 'member', 'address' => 'address'],
                    'markupSource' => ['member' => 'id']
                ];
                break;
                
            // a list of members in the database who may have not yet paid their subs.
            // case 15:
            //     return [
            //         'query' =>
            //             DB::table('people')
            //                 ->select('first_name AS first name', 'last_name AS last name')
            //                 ->addSelect('address', 'suburb', 'postcode', 'any_phone AS phone', 'email')
            //                 ->leftJoin('addresses','people.postal_address','=','addresses.id')
            //                 ->orderBy('last_name')
            //                 ->where('people.member',null),
            //         'pageHeading' => 'Are these \'members\' financial?',
            //         'type'        => 'generic'
            //     ];
            //     break;
                
            // a list of members and their emails of those who wish to receive their newsletters by email
            // this list is used to generate a CSV for input into Mailchimp for example
            case 16:
                return [
                    'query' =>
                        DB::table('people')
                            ->select('people.id AS id','first_name AS first name', 'last_name AS last name', 'email')
                            ->orderByRaw('people.last_name, people.first_name')
                            ->where('people.prefer_email','<>', 0)
                            ->whereNotNull('email')
                            ->where(function ($query) {
                                $query->where('member','<>',0)
                                ->orWhereNull('member');
                            }),
                    'pageHeading' => 'Members who have opted to receive their newsletters by email',
                    'type'        => 'generic',
                    'columnAttributes' => ['email' => 'email', 'id' => 'hidden, memberSource', 'last name' => 'member'],
                    'markupSource' => ['member' => 'id']
                ];
                break;

            // a list of members and their addresses of those who wish to receive their newsletters by post
            // this list is used to generate a CSV for the printer to create mailing address labels
            case 17:
                return [
                    'query' =>
                        DB::table('people')
                            ->select('people.id AS id','first_name AS first name', 'last_name AS last name')
                            ->addSelect('address', 'suburb', 'postcode')
                            ->leftJoin('addresses','people.postal_address','=','addresses.id')
                            ->orderByRaw('people.last_name, people.first_name')
                            ->where(function ($query) {
                                $query->where('member','<>',0)
                                ->orWhereNull('member');
                            })
                            ->where(function ($query) {
                                $query->where('prefer_email',0)
                                ->orWhereNull('email');
                            }),
                    'pageHeading' => 'Members who have opted to receive their newsletters by post',
                    'type'        => 'generic',
                    'columnAttributes' => ['email' => 'email', 'id' => 'hidden, memberSource', 'last name' => 'member', 'address' => 'address'],
                    'markupSource' => ['member' => 'id']
                ];
                break;

            // all about an individual member
            case 19:
                return [
                    'pageHeading' => 'Members details',
                    'view'        => 'reports.memberDetails',
                    'type'        => 'view'   
                ];
            break;

            // all about an individual venue
            case 25:
                return [
                    'pageHeading' => 'Courses details',
                    'view'        => 'reports.courseDetails',
                    'type'        => 'view'   
                ];
            break;

            // all about an individual venue
            case 26:
                return [
                    'pageHeading' => 'Venues details',
                    'view'        => 'reports.venueDetails',
                    'type'        => 'view'   
                ];
            break;

            // a list of members and their addresses, for the printer to create address labels
            case 27:
                return [
                    'query' =>
                        DB::table('people')
                            ->select('people.id AS id','name')
                            ->addSelect('address', 'suburb', 'postcode')
                            ->leftJoin('addresses','people.postal_address','=','addresses.id')
                            ->orderBy('people.last_name')
                            ->where('people.deleted',0)
                            ->where(function ($query) {
                                $query->where('member','<>',0)
                                ->orWhereNull('member');
                            }),
                    'pageHeading' => 'Mailing List for all members',
                    'type'        => 'generic',
                    'columnAttributes' => ['email' => 'email', 'id' => 'hidden, memberSource', 'name' => 'member', 'address' => 'address'],
                    'markupSource' => ['member' => 'id']
                ];
                break;


            // a list of new members this year
            // TODO: This is not working!
            // case 34:
            //     return [
            //         'query' =>
            //             DB::table('people')
            //                 ->select('first_name as first name', 'last_name as last name')
            //                 ->leftJoin('membership_histories as mh1','people.id','=','mh1.person_id')
            //                 ->orderBy('people.last_name')
            //                 ->where('mh1.year',date('Y'))
            //                 ->whereNotExists(function ($query) {
            //                     $query->selectRaw(1)
            //                         ->from('membership_histories as mh2')
            //                         ->where('mh2.year','=',date('Y') - 1)
            //                         ->where('mh1.person_id','=','mh2.person_id')
            //                         ;
            //                 })
            //                 // ->where(function ($query) {
            //                 //     $query->where('member','<>',0)
            //                 //     ->orWhereNull('member');
            //                 // }),
            //                 ,
            //         'pageHeading' => 'New members this year',
            //         'type'        => 'generic'
            //     ];

            // a list of the facilitators and their contact information
            case 32:
                return [
                    'query' =>
                        DB::table('sessions')
                            ->select('people.id AS id','sessions.name AS course', 'sessions.course_id AS courseId', 'people.first_name AS first name', 'people.last_name AS last name')
                            ->addSelect('people.any_phone AS phone', 'people.email')
                            ->leftJoin('people','people.id','=','sessions.facilitator')
                            ->where('sessions.deleted',0)
                            ->where('sessions.suspended',0)
                            ->orderByRaw('sessions.name', 'people.last_name', 'people.first_name'),
                    'pageHeading' => 'Facilitators\' Contact Details',
                    'type'        => 'generic',
                    'columnAttributes' => ['email' => 'email', 'id' => 'hidden, memberSource', 'last name' => 'member', 'course' => 'course', 'courseId' => 'hidden, courseSource'],
                    'markupSource' => ['member' => 'id', 'course' => 'courseId']
                ];
                break;
            // a list of courses and the members on their waiting lists
            case 35:
                return [
                    'query' =>
                        DB::table('session_attendee')
                            ->select('people.id AS id','sessions.name AS course', 'sessions.course_id AS courseId', 'people.first_name AS first name', 'people.last_name AS last name')
                            ->addSelect('people.any_phone AS phone', 'people.email')
                            ->leftJoin('people','people.id','=','session_attendee.person_id')
                            ->leftJoin('sessions','sessions.id','=','session_attendee.session_id')
                            ->leftJoin('courses','courses.id','=','sessions.course_id')
                            ->where('sessions.deleted',0)
                            ->where('sessions.suspended',0)
                            ->where('courses.suspended',0)
                            ->where('courses.no_longer_offerred',0)
                            ->where('session_attendee.confirmed',0)
                            ->orderByRaw('sessions.name', 'people.last_name', 'people.first_name'),
                    'pageHeading' => 'Course Waiting Lists',
                    'type'        => 'generic',
                    'columnAttributes' => ['email' => 'email', 'id' => 'hidden, memberSource', 'last name' => 'member', 'course' => 'course', 'courseId' => 'hidden'],
                    'markupSource' => ['member' => 'id', 'course' => 'courseId']
                ];
                break;
            // a list of venues and their addresses
            case 37:
                return [
                    'query' =>
                        DB::table('venues')
                            ->select('venues.id AS venueId','venues.name AS venue','addresses.id AS addressId','addresses.address','addresses.suburb','addresses.postcode')
                            ->leftJoin('addresses','addresses.id','venues.address_id')
                            ->where('venues.deleted',0)
                            ->orderBy('venues.name'),
                    'pageHeading' => 'Venues Address List',
                    'type'        => 'generic',
                    'columnAttributes' => ['venueId' => 'hidden, venueSource', 'addressId' => 'hidden, addressSource','address' => 'address', 'venue' => 'venue'],
                    'markupSource' => ['venue' => 'venueId', 'address' => 'addressId']
                ];
                break;

            // statistics about the database
            case 38:
                return [
                    'pageHeading' => 'Statistics',
                    'view'        => 'reports.statistics',
                    'type'        => 'view'   
                ];
            break;


            // a list of members who have paid by direct credit
            // maybe of use to the treasurer to reconcile accounts?
            case 39:
                return [
                    'query' =>
                        DB::table('membership_histories as mh')
                            ->selectRaw('date_format(`mh`.`date_of_admission`,\'%D %b %Y\') AS `date of admission`')
                            ->addSelect('people.id AS id','first_name as first name', 'last_name as last name', 'any_phone as phone')
                            ->addSelect('email')
                            ->leftJoin('people', function($join) {
                                $join->on('people.id','=','mh.person_id')
                                    ->where('mh.year','=',date('Y'));
                            })
                            ->orderByRaw('date_of_admission','people.last_name','people.first_name')
                            ->where([
                                ['people.deleted',0],
                                ['people.member',true],
                                ['people.payment_method',3]
                            ]),
                    'pageHeading' => 'List of members who have paid by direct credit',
                    'type'        => 'generic',
                    'columnAttributes' => ['email' => 'email', 'id' => 'hidden, memberSource', 'last name' => 'member'],
                    'markupSource' => ['member' => 'id']
                ];
                break;

            // The 'About' screen
            case 55:
                return [
                    'view' => 'welcome',
                    'type' => 'view'
                ];
                break;

            // all about an individual member
            case 56:
                return [
                    'pageHeading' => 'Members details',
                    'view'        => 'reports.briefMemberDetails',
                    'type'        => 'view'   
                ];
            break;

            // Hopefully this never gets called.
            default:
                return [
                    'query' =>
                        DB::table('people')
                            ->select(DB::raw("'Incorrect reportId (".$reportId.") passed to ReportRepository' as Error"))
                            ->distinct(),
                    'pageHeading' => 'Error in class ReportRepository',
                    'type'        => 'unspecified'
                ];
        endswitch;
    }

    public static function isGeneric(int $reportId) {
        return ReportRepository::isType($reportId, 'generic');
    }

    public static function isPdf(int $reportId) {
        return ReportRepository::isType($reportId, 'pdf');
    }

    public static function isView(int $reportId) {
        return ReportRepository::isType($reportId, 'view');
    }

    public static function isViewWithController(int $reportId) {
        return ReportRepository::isType($reportId, 'viewWithController');
    }

    private static function isType(int $reportId, $type) {
        return (ReportRepository::bladeData($reportId)['type'] == $type);
    }
}
