<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {   
        $number_paginate = [10, 25, 50, 100, 300, 999999999];
        $number = $request->input('number', 10);

        $query = Activity::orderBy('created_at', 'desc');

        $logs = $query->paginate($number);
        return view('activity_log.index', compact('logs','number', 'number_paginate'));
    }
}