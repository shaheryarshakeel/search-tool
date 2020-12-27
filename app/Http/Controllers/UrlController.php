<?php

namespace App\Http\Controllers;

use App\UrlShort;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    /**
     * @param $link
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function shortUrlRedirection($link){
        $url = UrlShort::where('short_url', $link)->first();
        return redirect($url->url);
    }
}
