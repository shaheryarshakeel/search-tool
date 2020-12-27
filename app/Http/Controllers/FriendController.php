<?php

namespace App\Http\Controllers;

use App\Friend;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function addAsFriend(Request $request)
    {
        $friend1            = new Friend();
        $friend1->member_id = $request->input('memberId');
        $friend1->friend_id = $request->input('friendId');
        $friend1->status    = 1;
        $friend1->save();
        $friend2            = new Friend();
        $friend2->member_id = $request->input('friendId');
        $friend2->friend_id = $request->input('memberId');
        $friend2->status    = 1;
        $friend2->save();

        return response()->json(['Result' => 'Successfully added!']);
    }
}
