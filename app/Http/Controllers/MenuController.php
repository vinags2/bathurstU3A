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
    protected $menu;
    // An array of all the menus in the menus table and their attributes (eg url, report number)
    protected $menus;

    public function __construct() 
    {
        $this->menu = MenuRepository::getMenu(0);   
        $this->getMenuAttributes();
    }

    private function getMenuAttributes() {
        foreach ($this->menu as $key => $menu) {
            $isSubmenu = is_array($menu);
            $subMenus =  $isSubmenu ? count($menu) : 0;
            $attributes = $isSubmenu ? MenuRepository::menuItem($key) : MenuRepository::menuItem($menu);
            $this->menus[$key][$key] = ['name' => $attributes['text'], 'type' => $attributes['type'], 'url' => $this->rewriteHref($attributes['href'], $attributes['type']), 'submenus' => $subMenus];
            if ($isSubmenu) {
                $this->getMenuAttributes4OneLevel($menu, $this->menus[$key][$key]);
                // foreach ($menu as $submenu) {
                //     $attributes = MenuRepository::menuItem($submenu);
                //     $this->menus[] = [$attributes['text'], $attributes['type'], $attributes['href']];
                // }
            }
        }
    }

    private function getMenuAttributes4OneLevel($menu, &$arrayLevel) {
        foreach ($menu as $key => $submenu) {
            $isSubmenu = is_array($submenu);
            $subMenus =  $isSubmenu ? count($submenu) : 0;
            $attributes = $isSubmenu ? MenuRepository::menuItem($key) : MenuRepository::menuItem($submenu);
            $arrayLevel[$key][$key] = [
                'name' => $attributes['text'],
                'type' => $attributes['type'],
                'url' => $this->rewriteHref($attributes['href'], $attributes['type']),
                'newPage' => (empty($attributes['newPage']) or !$attributes['newPage']) ? false : true,
                'submenus' => $subMenus
            ];
            if ($isSubmenu) {
                $this->getMenuAttributes4OneLevel($submenu, $arrayLevel[$key][$key]);
            }
        }
    }
    
    private function rewriteHref($href, $type) {
        if ($type == 'report') {
            return route('report',['id' => $href]);
        } elseif ($type == 'route') {
            return route($href);
        } else {
            $type = 'newPage';
            return $href;
        }
    }

    /**
     * Pass an array of menus to a view ($view), which is generally the navigation bar.
     */
    public function compose($view) {
        // dd($this->menus);
        $view->with('menus',$this->menus);
    }
}