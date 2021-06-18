<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;

/**
 * Control the menu displayed.
 */
class MenuController extends Controller
{
    // An array of all the menus in the menus table
    protected $allMenus;
    // TODO: retrieve $dbHome from the database.
    protected $dbHome = 'http://127.0.0.1/~gregvinall/bathurstu3a/db2/';

    /**
     * Retrieve the menus for the current user
     *
     * @return array() of 
     *       [ 'name'      => string,  // text to appear in menu
     *         'page name' => string,  // the name of the Object to generate the page
     *         'page type' => integer, // 1 = normal PHP, 2 = a webpage, 3 = a PDF
     *         'report id' => integer, // pointer to entry in reports table
     *         'next menu' => integer, // pointer to entry in menus table
     *         'menu'      => integer, // the menu number ('id')
     *         'url'       => string,  // the URL to go to when this menu item is selected by the user
     *         'submenus'  => integer, // the number of submenus under this top level menu, or NULL
     *         '0'         => an array of the submenus for this top level menu. Each element of the array is the same as above
     *                          except without 'submenus' and '0'
     *       ];
     */
    private function menus() {
        $this->allMenus = Menu::with('report_details.page_name')->get();
        
        $topLevelMenu   = $this->getMenu();
        $subMenus       = $this->getSubMenus($topLevelMenu);
        $menus          = $this->almalgamateMenus($topLevelMenu, $subMenus);
        return $menus;
    }

    /**
     * Pass an array of menus to a view ($view), which is generally the navigation bar.
     */
    public function compose($view) {
        dd($this->menus());
        $view->with('menus',$this->menus());
    }

    /**
     * Retrieve the menu with id $menuId
     */
    private function getMenu($menuId = 0) {
        $menuDetails = array();
        foreach ($this->allMenus as $menu) {
            if ($menu->menu == $menuId) {
                $pageName = $this->getPageName($menu);
                $pageType = $this->getPageType($menu);
                $url      = $this->getUrl($menu);
                $aMenuDetails = [
                    'name'      => $menu->report_details->name,
                    'page name' => $pageName,
                    'page type' => $pageType,
                    'report id' => $menu->report_id,
                    'next menu' => $menu->next_menu,
                    'menu'      => $menu->menu,
                    'url'       => $url
                ];
                $menuDetails[] = $aMenuDetails;
            }
        }
        return $menuDetails;
    }

    /**
     * Retrieve the submenus of $toplevelMenu
     */
    private function getSubMenus($topLevelMenu) {
        $subMenus = array();
        foreach ($topLevelMenu as $menu) {
            $subMenus[$menu['next menu']] = $this->getMenu($menu['next menu']);
        }
        return $subMenus;
    }

    /**
     * almalgamate the top Level Menus ($topLevelMenu) and their respective submenus ($subMenus) into one array.
     */
    private function almalgamateMenus($topLevelMenu, $subMenus) {
        $menus = array();
        $i = 0;
        foreach ($topLevelMenu as $menu) {
            $menus[] = $menu;
            foreach ($subMenus as $subMenu) {
                if ($menu['next menu'] == $subMenu[0]['menu']) {
                    $menus[$i]['submenus'] = count($subMenu);
                    $menus[$i][] = $subMenu;
                }
            }
            $i++;
        }
        return $menus;
    }

    /**
     * get the page name to display on the menu for $menu
     */
    private function getPageName($menu) {
        $pageName = $menu->report_details->page_name;
        if ($pageName) {
            $pageName = $pageName->name;
        }
        return $pageName;
    }

    /**
     * get the page type (eg 2 == a direct web link) to display on the menu for $menu
     */
    private function getPageType($menu) {
        $pageType = $menu->report_details->page_name;
        if ($pageType) {
            $pageType = $pageType->type;
        }
        return $pageType;
    }

    /**
     * get the URL for the <anchor> tag of the menu item.
     */
    private function getUrl($menu) {
        $pageType = $this->getPageType($menu);
        $pageName = $this->getPageName($menu);
        if ($pageType == 2) {
            $url = $pageName;
        } else {
            $url = route('report',['id' => $menu['report_id']]);
        }
        return $url;
    }
}