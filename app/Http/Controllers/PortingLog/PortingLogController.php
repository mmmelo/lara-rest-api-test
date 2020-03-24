<?php

namespace App\Http\Controllers\PortingLog;

use App\Http\Controllers\ApiController;
use App\PortingLog;
use Illuminate\Http\Request;

class PortingLogController extends ApiController
{

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->printAll(PortingLog::orderBy('created_at', 'desc')->get());
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
     * @param  \App\PortingLog  $portingLog
     * @return \Illuminate\Http\Response
     */
    public function show(PortingLog $portingLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PortingLog  $portingLog
     * @return \Illuminate\Http\Response
     */
    public function edit(PortingLog $portingLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PortingLog  $portingLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PortingLog $portingLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PortingLog  $portingLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(PortingLog $portingLog)
    {
        //\\\\\
    }
}
