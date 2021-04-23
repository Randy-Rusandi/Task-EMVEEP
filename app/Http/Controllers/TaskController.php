<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;


class TaskController extends Controller
{
    public function index() {
        return view('task',['salesmans' => Sales::all()]);
    }
}
