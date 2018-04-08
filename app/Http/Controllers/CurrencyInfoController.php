<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CurrencyInfo;
use MongoDB\BSON\ObjectID;
use App\Models\Currency;
use App\Models\Exchange;

class CurrencyInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arr = [
            'BTC', 'ETH', 'LTC', 'NEO', 'BNB', 'QTUM', 'BCC',
        ];
        $currencyInfos = CurrencyInfo::whereIn('symbol', $arr)->orderby('_id')->get(['symbol']);
        $arr = [];
        foreach ($currencyInfos as $currencyInfo) {
            $arr[] = $currencyInfo['symbol'];
        }
        return $this->successJsonResponse(['list' => $arr]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $arr = [
            'BTC', 'ETH', 'LTC', 'NEO', 'BNB', 'QTUM', 'BCC'
        ];
        // $exchanges = Exchange::whereIn('symbol', ['binance', 'huobi'])->get(['_id']);
        // $currencyInfos = CurrencyInfo::whereIn('symbol', $arr)->orderby('_id')->get();
        // foreach ($exchanges as $exchange) {
        //     foreach ($currencyInfos as $currencyInfo) {
        //         $currency = Currency::where('symbol', $currencyInfo['symbol'])->first();
        //         if (!$currency) {
        //             $newcurrency = new Currency;
        //             $newcurrency['name'] = $currencyInfo['name'];
        //             $newcurrency['symbol'] = $currencyInfo['symbol'];
        //             $newcurrency['identity'] = $currencyInfo['identity'];
        //             $newcurrency['exchange_id'] = new ObjectID($exchange['_id']);
        //             $newcurrency->save();
        //         } else {
        //             info($currency['symbol']);
        //         }
        //     }
        // }
        return $this->successJsonResponse(['list' => $arr]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
