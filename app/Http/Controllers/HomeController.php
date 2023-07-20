<?php

namespace App\Http\Controllers;

use App\Custom\CreatioComm\CreatioComm as CreatioComm;
use App\Models\Blog;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Request the dashboard from Creatio
     *
     * @param  string $guid
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard($guid)
    {
        //Connect with Creatio
        $creatio = new CreatioComm();

        //Request Dashboard Key Figures to Creatio
        $dashboard = $creatio->get_json_entries('UsrVwDashboardCollection?$filter=(UsrAccountId%20eq%20(guid%27' . $guid . '%27))');

        //Request last 3 blogs
        $blogs = Blog::orderBy('created_at', 'desc')->take(3)->get();

        //Check response from Creatio and return data
        if(checkCreatioResponseList($dashboard)){
            return response()->json([
                'data' => $dashboard->d->results,
                'blogs' => $blogs
            ], 200);
        }else{
            return response()->json([
                'data' => "Something went wrong, try again later!"
            ], 400);
        }
    }

}
