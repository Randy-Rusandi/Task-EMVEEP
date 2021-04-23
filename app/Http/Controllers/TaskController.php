<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Courier;


class TaskController extends Controller
{
    public function index() {
        return view('task',['salesmans' => Sales::all(), 'courier_list' => Courier::all()]);
    }
}
