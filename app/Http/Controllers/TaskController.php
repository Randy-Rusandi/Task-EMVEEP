<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Courier;
use App\Models\Payment;

class TaskController extends Controller
{
    public function index() {
        return view('task',[
            'salesmans' => Sales::all(),
            'courier_list' => Courier::all(),
            'payment_types' => Payment::all()
        ]);
    }
}
