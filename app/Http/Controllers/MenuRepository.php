<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuRepository extends Controller
{
    public static function getMenu($security) {
        $user = auth()->user();

        if (empty($user)) {
            return MenuRepository::menu(0);
        }

        if ($user->hasPermissionTo('admin')) {
            return MenuRepository::menu(3);
        }
        elseif ($user->hasPermissionTo('basic member')) {
            return MenuRepository::menu(1);
        }
        elseif ($user->hasPermissionTo('data entry')) {
            return MenuRepository::menu(2);
        } else {
            return MenuRepository::menu(0);
        }
    }

    // A multidimensional array of the menuitems for a specific menu structure.
    private static function menu($menuId) {
        switch ($menuId):
            case 0: return [
                42 => [19,6,7,32, 16,17, 34, 39],// Members Menu
                43 => [25,3,4,5,9, 35, 26, 37],  // Courses Menu
                45 => [20,18,21,22,3,4,5,32],    // Facilitators Menu
                46 => [4,5,16,17],               // Newsletters Menu
                47 => [20, 18, 21],              // Forms Menu
                48 => [55, 38, 40, 58]               // Utilities Menu
                ];
                break;
            case 1: return [57 => [56, 33]];
            case 2:
            case 3: return [
                42 => [19,6,7,32, 16,17, 34, 39],// Members Menu
                43 => [25,3,4,5,9, 35, 26, 37],  // Courses Menu
                45 => [20,18,21,22,3,4,5,32],    // Facilitators Menu
                46 => [4,5,16,17],               // Newsletters Menu
                47 => [20, 18, 21],              // Forms Menu
                48 => [55, 38, 40, 58, 60],              // Utilities Menu
                49 => [59,62,61,29,30,31, 33, 36, 63]      // Data Entry Menu
                // 49 => [59,29,30,31, 33, 36]      // Data Entry Menu
                ];
                break;
            break;
            default:
                break;
        endswitch;
    }

    // The menu items that can be called in the order specified by the menus above.
    // 'type' can be 'link', 'submenu', 'report', 'error'
    public static function menuItem($menuItemId) {
        switch ($menuItemId):
            case 3:
                return [
                    'text' => 'Class Rolls',
                    'type' => 'report',
                    'href' => '3'
                ];
                break;
            case 4:
                return [
                    'text' => 'Timetable',
                    'type' => 'report',
                    'href' => '4'
                ];
                break;
            case 5:
                return [
                    'text' => 'Course Information',
                    'type' => 'report',
                    'href' => '5'
                ];
                break;
            case 6:
                return [
                    'text' => 'Member List',
                    'type' => 'report',
                    'href' => '6'
                ];
                break;
            case 7:
                return [
                    'text' => 'Committee',
                    'type' => 'report',
                    'href' => '7'
                ];
                break;
            case 9:
                return [
                    'text' => 'Course Details',
                    'type' => 'report',
                    'href' => '9'
                ];
                break;
            case 16:
                return [
                    'text' => 'Newsletters by email',
                    'type' => 'report',
                    'href' => '16'
                ];
                break;
            case 17:
                return [
                    'text' => 'Newsletters by post',
                    'type' => 'report',
                    'href' => '17'
                ];
                break;
            case 18:
                return [
                    'text' => 'Generic Class Roll',
                    'type' => 'link',
                    'newPage' => true,
                    'href' => 'http://bathurst.u3anet.org.au/wp-content/uploads/2017/03/Generic-attendance-Sheet224.pdf'
                ];
                break;
            case 19:
                return [
                    'text' => 'Individual Member Report',
                    'type' => 'report',
                    'href' => '19'
                ];
                break;
            case 20:
                return [
                    'text' => 'Accident Report Form',
                    'type' => 'link',
                    'newPage' => true,
                    'href' => 'http://bathurst.u3anet.org.au/wp-content/uploads/2020/03/Accident-Report-Form17.pdf'
                ];
                break;
            case 21:
                return [
                    'text' => 'Honorary Membership Form',
                    'type' => 'link',
                    'newPage' => true,
                    'href' => 'http://bathurst.u3anet.org.au/wp-content/uploads/2017/03/Bathurst-U3A-Honorary-Membership-Form.pdf'
                ];
                break;
            case 22:
                return [
                    'text' => 'The Facilitator\'s Handbook',
                    'type' => 'link',
                    'newPage' => true,
                    'href' => 'http://bathurst.u3anet.org.au/wp-content/uploads/2018/01/20180128-U3A-Facilitators-Handbook.pdf'
                ];
                break;
            case 23:
                return [
                    'text' => 'Bathurst U3A Letterhead',
                    'type' => 'link',
                    'newPage' => true,
                    'href' => 'http://bathurst.u3anet.org.au/wp-content/uploads/2017/04/U3A-Letterhead.pdf'
                ];
                break;
            case 24:
                return [
                    'text' => 'With complements\'slip',
                    'type' => 'link',
                    'newPage' => true,
                    'href' => 'http://bathurst.u3anet.org.au/wp-content/uploads/2017/04/U3A-With-Compliments-slip.pdf'
                ];
                break;
            case 25:
                return [
                    'text' => 'Individual Course Report',
                    'type' => 'report',
                    'href' => '25'
                ];
                break;
            case 26:
                return [
                    'text' => 'Individual Venue Report',
                    'type' => 'report',
                    'href' => '26'
                ];
                break;
            case 27:
                return [
                    'text' => 'Address Labels for all members',
                    'type' => 'report',
                    'href' => '27'
                ];
                break;
            case 29:
                return [
                    'text' => 'Enrolments',
                    'type' => 'report',
                    'href' => '29'
                ];
                break;
            case 30:
                return [
                    'text' => 'Venues',
                    'type' => 'report',
                    'href' => '30'
                ];
                break;
            case 31:
                return [
                    'text' => 'Courses',
                    'type' => 'report',
                    'href' => '31'
                ];
                break;
            case 32:
                return [
                    'text' => 'Facilitator List',
                    'type' => 'report',
                    'href' => '32'
                ];
                break;
            case 33:
                return [
                    'text' => 'Enrolment Form',
                    'type' => 'report',
                    'href' => '33'
                ];
                break;
            case 34:
                return [
                    'text' => 'New Members',
                    'type' => 'report',
                    'href' => '34'
                ];
                break;
                // View the waiting lists
            case 35:
                return [
                    'text' => 'Waiting Lists',
                    'type' => 'report',
                    'href' => '35'
                ];
                break;
                // Process the waiting lists
            case 36:
                return [
                    'text' => 'Waiting Lists',
                    'type' => 'report',
                    'href' => '36'
                ];
                break;
            case 37:
                return [
                    'text' => 'Venue Address List',
                    'type' => 'report',
                    'href' => '37'
                ];
                break;
            case 38:
                return [
                    'text' => 'Statistics',
                    'type' => 'report',
                    'href' => '38'
                ];
                break;
            case 39:
                return [
                    'text' => 'Payers by Direct Credit',
                    'type' => 'report',
                    'href' => '39'
                ];
                break;
            case 40:
                return [
                    'text' => 'Dump the Database',
                    'type' => 'report',
                    'href' => '40'
                ];
                break;
            case 42:
                return [
                    'text' => 'Members',
                    'type' => 'submenu',
                    'href' => '#'
                ];
                break;
            case 43:
                return [
                    'text' => 'Courses',
                    'type' => 'submenu',
                    'href' => '43'
                ];
                break;
            case 44:
                return [
                    'text' => 'Venues',
                    'type' => 'submenu',
                    'href' => '44'
                ];
                break;
            case 45:
                return [
                    'text' => 'Facilitators',
                    'type' => 'submenu',
                    'href' => '45'
                ];
                break;
            case 46:
                return [
                    'text' => 'Newsletters',
                    'type' => 'submenu',
                    'href' => '46'
                ];
                break;
            case 47:
                return [
                    'text' => 'Forms',
                    'type' => 'submenu',
                    'href' => '#'
                ];
                break;
            case 48:
                return [
                    'text' => 'Utilities',
                    'type' => 'submenu',
                    'href' => '48'
                ];
                break;
            case 49:
                return [
                    'text' => 'Data Entry',
                    'type' => 'submenu',
                    'href' => '49'
                ];
                break;
            case 55:
                return [
                    'text' => 'About',
                    'type' => 'report',
                    'href' => '55'
                ];
                break;
            case 56:
                return [
                    'text' => 'My details',
                    'type' => 'report',
                    'href' => '19'
                ];
                break;
            case 57:
                return [
                    'text' => 'My menu',
                    'type' => 'submenu',
                    'href' => '57'
                ];
                break;
            case 58:
                return [
                    'text' => 'Logs',
                    'type' => 'route',
                    'href' => 'log-viewer::dashboard',
                    'newPage' => true
                    // 'href' => asset("index.php/log-viewer")
                    // 'type' => 'link',
                    // 'href' => url("log-viewer")
                ];
                break;
            case 59:
                return [
                    'text' => 'Memberships',
                    'type' => 'route',
                    'href' => 'person.edit'
                ];
                break;
            case 60:
                return [
                    'text' => 'Test Page',
                    'type' => 'route',
                    'href' => 'testPage'
                ];
                break;
            case 61:
                return [
                    'text' => 'Contact details',
                    'type' => 'route',
                    'href' => 'person.editContactDetails'
                ];
                break;
            case 62:
                return [
                    'text' => 'Membership Fees',
                    'type' => 'report',
                    'href' => '29'
                ];
                break;
            case 63:
                return [
                    'text' => 'Settings',
                    'type' => 'route',
                    'href' => 'settings'
                ];
                break;
            default:
                return [
                    'text' => 'Error',
                    'type' => 'error',
                    'href' => 'There is an error accessing the correct menu item'
                ];
                break;
        endswitch;
    }
}
