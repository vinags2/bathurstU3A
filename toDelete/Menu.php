<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Menu extends Model
{
    public $timestamps = false;
    //

    protected static function boot() {
        parent::boot();

        // Always order by menu and order ASC
        static::addGlobalScope('order', function (Builder $builder) {
            // $builder->orderBy('menu', 'asc');
            $builder->orderBy('menu', 'asc')->orderBy('order', 'asc');
        });

    }

    // public function scopeTopLevelMenu($query) {
    //     $query->where('menu',0);
    // }

    /**
     * Get the menus that follow this menu
     */
    public function sub_menus()
    {
        return $this->hasMany('App\Menu','next_menu','menu');
    }

    /**
     * Get the particulars for the report attached to the menu item
     */
    public function report_details()
    {
        return $this->belongsTo('App\Report','report_id','id');
    }

    /**
     * Get the particulars for the menu item
     */
    public function menu_details()
    {
        return $this->belongsTo('App\Report','id','next_menu');
    }
}
