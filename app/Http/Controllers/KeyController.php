<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Key;

class KeyController extends Controller {

    public function store(Request $request) {
        $data = request()->validate([
            'start' => 'required',
            'end' => 'required',
            'text' => 'required',
        ]);
        $begin = new \DateTime($data['start']);
        $end = new \DateTime($data['end']);
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);
        $text = $data['text'];
        $query = "INSERT INTO date_keys (date, key) VALUES (?,?)";
        foreach ($period as $dt) {
            $date = $dt->format("Y.m.d");
            $YY = $dt->format("y");
            $XX = substr($dt->format("Y"), 0, 2);
            $DD = $dt->format("d");
            $MM = $dt->format("m");
            $key = new Key();
            $key-> date = $date;
            $key->key = $MM . $XX . $text . $DD . $YY;
            $key->save();
        }
        return 1;
    }

}
