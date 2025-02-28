<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\UserCustomList;
use App\Models\Host;
use App\Models\User;

class UserCustomListController extends Controller
{
    public function index()
    {
        $customUsers = UserCustomList::with(['user','host'])->orderBy('id','desc')->get();
        $users = User::all();
        $hosts = Host::all();
        return view('custom-list.index', compact('customUsers', 'users', 'hosts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'domain' => 'required|string',
        ]);
        $domainName = $request->domain;
        $userId = $request->user_id;
        $descriptionCategory = 'Custom category automatically created.';
        $category = Category::firstOrCreate(['name' => 'custom'], ['description' => $descriptionCategory]);
        $host = Host::firstOrCreate(
            ['domain' => $domainName],  // Check if domain exists
            ['category_id' => $category->id, 'source' => 'manual'] // Assign category_id = 1 and source
        );

        $hostId = $host->id;
        $rezCreate = UserCustomList::create([ 'user_id' => $userId, 'host_id' => $hostId]);

        return redirect()->route('custom-list.index');
    }

    public function destroy($id)
    {
        UserCustomList::findOrFail($id)->delete();
        return redirect()->route('custom-list.index');
    }
}
