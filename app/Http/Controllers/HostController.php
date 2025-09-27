<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCustomList;
use App\Models\Host;

class HostController extends Controller
{
    public function admin()
    {
        // Get total counts
        $totalHosts = Host::count();
        $totalCustomHosts = UserCustomList::count();

        // Get all hosts
        $hosts = Host::limit(10)->where('source', '<>', 'manual')->orderBy("id",'desc')->get();
        $customHosts = UserCustomList::with('host')
            ->limit(10)
            ->orderBy("id",'desc')->get();

        return view('admin.dashboard', compact('totalHosts', 'totalCustomHosts', 'hosts', 'customHosts'));
    }
    public function index()
    {
        // Get total counts
        $totalHosts = Host::count();
        $totalCustomHosts = UserCustomList::count();

        // Get all hosts
        $hosts = Host::limit(10)->where('source', '<>', 'manual')->orderBy("id",'desc')->get();
        $customHosts = UserCustomList::with('host')
                        ->limit(10)
                        ->orderBy("id",'desc')->get();

        return view('welcome', compact('totalHosts', 'totalCustomHosts', 'hosts', 'customHosts'));
    }
}
