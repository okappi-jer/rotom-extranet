<?php

namespace App\Imports;

use App\Mail\NewCode;
use App\Models\Gatecontrol;
use Maatwebsite\Excel\Concerns\ToModel;

class GatecontrolImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $name = $row[0];
        $location = $row[1];
        $code = $row[2];
        $email = $row[4];
        $validtill = (intval($row[5]) - 25569) * 86400;

        if($email != "" && (filter_var($email, FILTER_VALIDATE_EMAIL) || str_contains($email, ";"))){

            if(str_contains(str_replace(" ", "", $email), ";")){
                $emails = explode(" ", $email);

                foreach($emails as $email){
                    \Mail::to(str_replace(";", "", $email))->queue(new NewCode($name, $location, $code, $email, date('d/m/Y', $validtill)));
                }
            }else{
                \Mail::to($email)->queue(new NewCode($name, $location, $code, $email, date('d/m/Y', $validtill)));
            }

        }
    }
}
