<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Employee;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $visitors = Visitor::get();

        return response()->json([
            'data' => $visitors,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function arrive(Request $request)
    {
        //Validate request
        $request->validate([
            'name' => ['required', 'max: 256'],
            'firstname' => ['required', 'max: 256'],
            'company' => ['required', 'max: 256'],
            'contact' => ['required', 'max: 256'],
            'email' => ['nullable', 'email'],
            'mobile' => ['nullable', 'max: 256'],
            'conditions' => ['required', 'max: 4'],
        ]);

        
        $visitor =  Visitor::create([
            'name' => $request['name'],
            'firstname' => $request['firstname'],
            'company' => $request['company'],
            'email' => $request['email'],
            'mobile' => $request['mobile'],
            'contact' => $request['contact'],
            'arrival' => date('Y-m-d H:i:s')
        ]);

        $employee = Employee::where('name', $request['contact'])->first();

        if($visitor && $employee){
            $twilioSid = "AC070fd130a6e05dc792a38b4af3033182";
            $twilioToken = "64053dc1f547d63965290d341dd5d172";
            $twilioWhatsAppNumber = "+14846528334";
            $recipientNumber = $employee->mobile; 
            $message = "Hallo, " . $request['firstname'] . " " . $request['name'] . " van " . $request['company'] . " is aangekomen 🚀";

            $twilio = new Client($twilioSid, $twilioToken);

            try {
                $twilio->messages->create(
                    $recipientNumber,
                    [
                        "from" => $twilioWhatsAppNumber,
                        "body" => $message,
                    ]
                );

                return response()->json([
                    'visitor' => $visitor,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'data' => "Er ging iets mis, probeer later even opnieuw!"
                ], 400);            
            }
        }else{
            return response()->json([
                'data' => "Er ging iets mis, probeer later even opnieuw!"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Visitor  $visitor
     * @return \Illuminate\Http\Response
     */
    public function depart(Request $request)
    {
         //Validate request
         $request->validate([
            'name' => ['required', 'max: 256'],
            'firstname' => ['required', 'max: 256'],
        ]);

        $visitor = Visitor::where('firstname', $request['firstname'])->where('name', $request['name'])->whereNull('departure')->first();

        if($visitor){
            $visitor->update([
                'departure' => date('Y-m-d H:i:s')
            ]);
        }else{
            return response()->json([
                'data' => "Deze gegevens konden niet gevonden worden"
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Visitor  $visitor
     * @return \Illuminate\Http\Response
     */
    public function closeAll()
    {
        $visitors = Visitor::whereNull('departure')->get();

        if($visitors){
            foreach($visitors as $visitor){
                $visitor->update([
                    'departure' => date('Y-m-d H:i:s')
                ]);
            }
            
        }else{
            return response()->json([
                'data' => "Error"
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Visitor  $visitor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Visitor $visitor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Visitor  $visitor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Visitor $visitor)
    {
        //
    }
}
