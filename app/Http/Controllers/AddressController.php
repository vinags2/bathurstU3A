<?php

namespace App\Http\Controllers;

use App\Address;
use Illuminate\Http\Request;
use App\Rules\CloseMatchingNames;

class AddressController extends Controller
{
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
        //
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
    public function edit($id)
    {
        //
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
        $address = $request->input('address');
        if(($pos = strpos($address, '/')) !== false) {
            $address = substr($address, $pos + 1);
        }
        $addresses1 = Address::select('id','address', 'line_1', 'line_2', 'suburb', 'postcode')
            ->where('address','sounds like', $address);
        $addresses2 = Address::select('id','address', 'line_1', 'line_2', 'suburb', 'postcode')
            ->where('address','like','%'.$address.'%');
        $addresses = Address::select('id','address', 'line_1', 'line_2', 'suburb', 'postcode')
            ->where('address',$address)
            ->union($addresses1)
            ->union($addresses2)
            ->limit(10)
            // ->orderBy('address')
            ->get();
        // $addresses = Address::select('id','address', 'line_1', 'line_2', 'suburb', 'postcode')
        //     ->whereRaw('levenshtein(soundex("'.$address.'"),soundex(`address`)) BETWEEN 0 AND 2')
        //     ->limit(10)
        //     ->orderBy('address')
        //     ->get();
        return response()->json($addresses);
    }
}
