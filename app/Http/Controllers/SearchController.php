<?php

namespace App\Http\Controllers;

use App\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        if ($request->input('isFriend') == 1) {
            $result = $this->getFriends($request->input('memberId'), $request->input('searchString'),
                $request->input('isFriend'));
        }

        $result = $this->getFriends($request->input('memberId'), $request->input('searchString'),
            $request->input('isFriend'));

        return $result;
    }

    public function getFriends($memberId, $searchPattern, $status = 0)
    {
        if ($status == 1) {
            $members = DB::table('members')
                ->join('friends', 'members.id', '=', 'friends.id')
                ->where([['friends.status', '=', 1], ['members.id', '=', $memberId]])
                ->get();

        } else {
            $members = DB::table('friends AS f1')
                ->join('members', 'f1.member_id', '=', 'members.id')
                ->where([
                    ['f1.member_id', '<>', $memberId],
                    ['f1.friend_id', '<>', $memberId],
                    ['members.headings', 'LIKE', '%'.$searchPattern.'%'],
                ])
                ->get();
        }

        if ($members->count() != 0) {
            return $this->getSearchPath($members, $memberId).'('.$searchPattern.')';
        } else {
            return response()->json(['Result' => 'No Record Found!']);
        }
    }

    public function getSearchPath($memberList, $id)
    {
        foreach ($memberList as $member) {

            if ($this->isMutual($id, $member->member_id)) {
                $memberArr = Member::where('id', $id)->first()->name.'->'.Member::where('id',
                        $member->friend_id)->first()->name.'->'.$member->name;
            } else {
                return response()->json(['Result' => 'There is no mutual friend!']);
            }
        }

        return $memberArr;
    }

    public function isMutual($currentMember, $searchedMember)
    {
        $mutuals = DB::select(DB::raw("SELECT * FROM friends AS f1, friends AS f2 where f1.member_id = f2.member_id 
                                            AND f1.friend_id =".$currentMember." and f2.friend_id = ".$searchedMember." limit 1"));

        if ($mutuals != null) {
            return 1;
        }

        return 0;
    }
}


//SELECT * FROM friends AS f1 INNER JOIN members ON f1.member_id = members.id where 'f1.member_id' <> 1 and 'f1.friend_id' <> 1 AND members.headings LIKE '%student%'
//SELECT * FROM friends AS f1, friends AS f2 where f1.member_id = f2.member_id AND f1.friend_id = 1 and f2.friend_id = 7
