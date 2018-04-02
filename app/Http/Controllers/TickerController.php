<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\BSON\ObjectID;
use App\Models\CurrencyInfo;
use App\Models\Currency;
use App\Models\Ticker;
use App\Models\Exchange;
use App\Models\Volume24Hour;

class TickerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $exchanges = Exchange::whereIn('symbol', ['binance', 'huobi'])->get(['_id']);
        $currencyinfos = CurrencyInfo::whereIn('symbol', ['BTC', 'ETH', 'LTC'])->orderby('_id')->paginate(50);
        $exchangeIdArray = [];
        foreach ($exchanges as $exchange) {
            $exchangeIdArray[] = new ObjectID($exchange['_id']);
        }
        foreach ($currencyinfos as $currencyinfo) {
            $priceArrayNow = [];
            $priceArray1Hour = [];
            $priceArray24Hour = [];
            $priceArray7d = [];
            $volumeArray24Hour = [];
            $highArray24Hour = [];
            $lowArray24Hour = [];

            $currencies = Currency::where('symbol', $currencyinfo['symbol'])->whereIn('exchange_id', $exchangeIdArray)->get();
            // get now
            foreach ($currencies as $currency) {
                $ticker = Ticker::where('currency_id', new ObjectID($currency['_id']))
                            ->orderby('ts', 'desc')
                            ->first();
                $ticker['price'] > 0 ? $priceArrayNow[] = $ticker['price'] : false;

                // get 1hour
                $ticker1Hour = Ticker::where('ts', '>', (int)date(strtotime('-1 hour')))
                            ->where('currency_id', new ObjectID($currency['_id']))
                            ->first();
                $ticker1Hour['price'] > 0 ? $priceArray1Hour[] = $ticker1Hour['price'] : false;

                // get 24hour
                $ticker24Hour = Ticker::where('currency_id', new ObjectID($currency['_id']))
                            ->where('ts', '>', (int)date(strtotime('-24 hour')))
                            ->first();
                $ticker24Hour['price'] > 0 ? $priceArray24Hour[] = $ticker24Hour['price'] : false;

                // get 7day
                $ticker7Day = Ticker::where('currency_id', new ObjectID($currency['_id']))
                            ->where('ts', '>', (int)date(strtotime('-7 day')))
                            ->first();
                $ticker7Day['price'] > 0 ? $priceArray7d[] = $ticker7Day['price'] : false;

                // Volume 24hour
                $volume24Hour = Volume24Hour::where('currency_id', new ObjectID($currency['_id']))
                            ->orderby('ts', 'desc')
                            ->first();
                $volume24Hour['amount'] > 0 ? $volumeArray24Hour[] = $volume24Hour['amount'] : false;
                $volume24Hour['high_price_24h'] > 0 ? $highArray24Hour[] = $volume24Hour['high_price_24h'] : false;
                $volume24Hour['low_price_24h'] > 0 ? $lowArray24Hour[] = $volume24Hour['low_price_24h'] : false;
            }

            $price = array_sum($priceArrayNow)/count($priceArrayNow);
            $price1Hour = array_sum($priceArray1Hour)/count($priceArray1Hour);
            $price24Hour = array_sum($priceArray24Hour)/count($priceArray24Hour);
            $priceArray7d = array_sum($priceArray7d)/count($priceArray7d);

            $currencyinfo['price_usd'] = $price;
            $currencyinfo['price_btc'] = $price / 6917;
            $currencyinfo['percent_change_1h'] = ($price - $price1Hour)/$price;
            $currencyinfo['percent_change_24h'] = ($price - $price24Hour)/$price;
            $currencyinfo['percent_change_7d'] = ($price - $priceArray7d)/$price;
            $currencyinfo['24h_volume_usd'] = array_sum($volumeArray24Hour) / 100000;
            $currencyinfo['high_price_24h'] = array_sum($highArray24Hour)/count($highArray24Hour);
            $currencyinfo['low_price_24h'] = array_sum($lowArray24Hour)/count($lowArray24Hour);
        }
        return $this->paginateJsonResponse($currencyinfos);
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
        //
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
