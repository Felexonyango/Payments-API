<?php

namespace App\Http\Controllers;

use Facade\FlareClient\View;
use Illuminate\Http\Request;

class PageController extends Controller
{
    //
    public function index()
    {
        return view('pages.index');
    }
}
