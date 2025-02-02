<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCustomList;
use App\Models\Host;

class HostController extends Controller
{
    public function index()
    {
        // Get total counts
        $totalHosts = Host::count();
        $totalCustomHosts = UserCustomList::count();

        // Get all hosts
        $hosts = Host::limit(10)->get();
        $customHosts = UserCustomList::with('host')->get();

        return view('welcome', compact('totalHosts', 'totalCustomHosts', 'hosts', 'customHosts'));
    }
}
