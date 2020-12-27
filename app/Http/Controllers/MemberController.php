<?php

namespace App\Http\Controllers;

use App\Member;
use App\UrlShort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use voku\helper\HtmlDomParser;
use Illuminate\Support\Facades\Http;
use function PHPUnit\Framework\isEmpty;

class MemberController extends Controller
{
    /**
     * @return Member[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        $members = Member::all();

        foreach ($members as $member) {
            $result[] = [
                'Name'              => $member->name,
                'Short URL'         => UrlShort::where('id', $member->id_short_url)->first()->short_url,
                'Number Of Friends' => $this->friendsInfo($member->id),
            ];
        }

        return $result;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getMember($id)
    {
        $mem     = Member::where('id', $id)->first();
        $urlData = UrlShort::where('id', $mem->id_short_url)->first();

        return response()->json([
            'Name'              => $mem->name,
            'Website Address'   => $urlData->url,
            'Short URL'         => $urlData->short_url,
            'Website Heading'   => $mem->headings,
            'Number Of Friends' => $this->friendsInfo($id)->count(),
            'Links To Friends'  => $this->getLinksToFriends($this->friendsInfo($id)),
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Support\Collection
     */
    public function friendsInfo($id)
    {
        return DB::table('members')
            ->join('friends', 'members.id', '=', 'friends.member_id')
            ->where('members.id', '=', $id)
            ->get();
    }

    public function getLinksToFriends($friends)
    {
        $links = array();
        foreach ($friends as $friend) {
            $links[] = UrlShort::where('id', $friend->id_short_url)->first()->url;
        }

        if ($links != null){
            return $links;
        }

        return null;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $url            = new UrlShort();
        $url->url       = $request->input('url');
        $url->short_url = $this->generateShortUrl();
        $url->save();

        $mem               = new Member();
        $mem->name         = $request->input('name');
        $mem->id_short_url = UrlShort::where('url', $request->input('url'))->first()->id;
        $mem->headings     = serialize($this->callUrl($request->input('url')));
        $mem->save();

        return response()->json(['Result' => 'Success!']);
    }

    /**
     * @param $url
     * @return array
     */
    public function callUrl($url)
    {
        $response = Http::get($url)->body();

        return $this->getTextBetweenTags($response, array('h1', 'h2', 'h3'));
    }

    /**
     * @param $string
     * @param $tagnames
     * @return array
     */
    function getTextBetweenTags($string, $tagnames)
    {
        $html   = HtmlDomParser::str_get_html($string);
        $titles = array();

        foreach ($tagnames as $tagname) {
            foreach ($html->find($tagname) as $element) {
                $titles[] = $element->plaintext;
            }
        }

        return $titles;
    }

    /**
     * @return string
     */
    private function generateShortUrl()
    {
        $result = base_convert(rand(1000, 99999), 10, 36);
        $data   = UrlShort::where('short_url', $result)->first();

        if ($data != null) {
            $this->generateShortUrl();
        }

        return $result;
    }
}
