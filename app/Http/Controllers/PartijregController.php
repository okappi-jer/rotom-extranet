<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Custom\CreatioComm\CreatioComm;

class PartijregController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBorderels(){
        $path = public_path() . '/partijreg/borderels/' . \Auth::user()->supplier_code . "/";
        $borderels = \File::allFiles($path);

        $documents = [];

        foreach($borderels as $borderel){
            $file = (object) [
                'filename' => $borderel->getFilename(),
                'date' => date('d/m/Y', $borderel->getCTime()),
                'url' => '/partijreg/borderels/' . \Auth::user()->supplier_code . '/' . $borderel->getFilename()
            ];

            array_push($documents, $file);
        }

        return response()->json([
            'borderels' => $documents,
        ], 200);
    }

    public function getLoten(){
        $path = public_path() . '/partijreg/loten/' . \Auth::user()->supplier_code . "/";
        $loten = \File::allFiles($path);

        $documents = [];

        foreach($loten as $lot){
            $file = (object) [
                'filename' => $lot->getFilename(),
                'date' => date('d/m/Y', $lot->getCTime()),
                'url' => '/partijreg/loten/' . \Auth::user()->supplier_code . '/' . $lot->getFilename()
            ];

            array_push($documents, $file);
        }

        return response()->json([
            'loten' => $documents,
        ], 200);
    }

}
