<?php

namespace App\Http\Controllers;

use App\Exports\DeliveryExport;
use App\Mail\SendCsv;
use App\Models\Delivery;
use App\Models\Lotnumber;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

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

    public function getTemplates(){
        $templates = Template::where('BTPLLeverCode', \Auth::user()->supplier_code)->get();
        $count = count($templates);

        return response()->json([
            'templates' => $templates,
            'count' => $count,
        ], 200);
    }

    public function storeDelivery(Request $request){
        $request->validate([
            'delivery' => ['required'],
            'delivery_date' => ['required', 'date'],
            'reference' => ['nullable', 'max: 256'],
        ]);

        $user = \Auth::user();

        $delivery = json_decode($request['delivery']);
        $delivery_id =  $user->supplier_code . "-" . random_int(100000, 999999) . "-" . date('ymd');

        foreach($delivery as $item){
            if($item->BTPLArticleCollie != "0"){
                Delivery::create([
                    'unique_id' => $delivery_id,
                    'supplier_code' => $user->supplier_code,
                    'user_id' => $user->id,
                    'user_name' => $user->firstname . ' ' . $user->name,
                    'BTPLArticleCollie' => $item->BTPLArticleCollie,
                    'BTPLArticleWeight' => number_format($item->BTPLArticleWeight, 2, '.', ' '),
                    'BTPLArtikelCode' => $item->BTPLArtikelCode,
                    'BTPLTekst' => $item->BTPLTekst,
                    'BTPLVerpakkingsCode' => $item->BTPLVerpakkingsCode,
                    'BTPLKaliber' => $item->BTPLKaliber,
                    'BTPLOrderDeliveryDate' => $request['delivery_date'],
                    'BTPLOrderDeliveryAt' => $user->delivers_to,
                    'BTPLArticleRemark' => str_replace(';', ' ', $item->BTPLArticleRemark),
                    'BTPLOrderReference' => $request['reference'],
                    'BTPLLijnnr' => $item->BTPLLijnnr,
                    'lotnumber' => \Auth::user()->lotnumber,
                ]);
            }
        }

        $csv = Excel::store(new DeliveryExport($delivery_id), $delivery_id .'.csv', 'excel');
        \Mail::to(ENV('MAIL_CSV_ADDRESS'))->send(new SendCsv($delivery_id));

        //Update lotnumber
        $last_lotnumber = Lotnumber::latest()->first();
        $new_lotnumber = str_replace('E', '', $last_lotnumber->lotnumber) + 1;

        Lotnumber::create([
            'lotnumber' => 'E' . $new_lotnumber,
        ]);

        $user->update([
            'lotnumber' => 'E' . $new_lotnumber,
        ]);

        return response()->json([
            'delivery_id' => $delivery_id,
            'lotnumber' => 'E' . $new_lotnumber,
        ], 200);

    }
}
