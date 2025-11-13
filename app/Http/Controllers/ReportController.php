<?php

namespace App\Http\Controllers;

use App\Models\BorrowRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function borrow()
    {
        $requests = BorrowRequest::with('user', 'vehicle')->get();
        return view('report.borrow', compact('requests'));
    }

    public function vehicleCondition()
    {
        $vehicles = Vehicle::all();
        return view('report.vehicle', compact('vehicles'));
    }
    
}
