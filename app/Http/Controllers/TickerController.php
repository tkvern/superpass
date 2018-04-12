<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectID;
use App\Models\CurrencyInfo;
use App\Models\Currency;
use App\Models\Ticker;
use App\Models\TickerCache;
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
        $ticekrCache = Cache::remember('tickercache', now()->addSeconds(4), function () {
            return TickerCache::orderby('_id', 'desc')->first();
        });
        return $this->successJsonResponse(['list' => $ticekrCache['list']]);
    }

    public function cache()
    {
        $minutes = 1440;
        // Cache exchanges
        $exchanges = Cache::remember('exchanges', $minutes, function () {
            return Exchange::whereIn('symbol', ['binance', 'huobi'])->get(['_id']);
        });

        // Cache currencyinfos
        $currencyinfos = Cache::remember('currencyinfos', $minutes, function () {
            $arr = [
                'BTC', 'ETH', 'LTC', 'NEO', 'BNB', 'QTUM', 'BCC'
            ];
            return CurrencyInfo::whereIn('symbol', $arr)->orderby('_id')->take(100)->get();
        });
        $priceArrayBTC = [];
        foreach ($currencyinfos as $currencyinfo) {
            $priceArrayNow = [];
            $priceArray1Hour = [];
            $priceArray24Hour = [];
            $priceArray7d = [];
            $volumeArray24Hour = [];
            $highArray24Hour = [];
            $lowArray24Hour = [];

            $currencies = Currency::where('symbol', $currencyinfo['symbol'])
                ->whereIn('exchange_id', $this->objectIDFormat((object)$exchanges))
                ->get();
            foreach ($currencies as $currency) {
                // get now
                // $ticker = Ticker::where('ts', '>', (int)date(strtotime('-1 minute')))
                $ticker = Ticker::where('currency_id', new ObjectID($currency['_id']))
                        ->orderby('ts', 'desc')
                        ->first();
                $ticker['price'] > 0 ? $priceArrayNow[] = $ticker['price'] : false;


                // get 1hour
                $ticker1Hour = Ticker::whereBetween('ts', [(int)date(strtotime('-1 hour')), (int)date(strtotime('-59 minute'))])
                            ->where('currency_id', new ObjectID($currency['_id']))
                            ->first();
                $ticker1Hour['price'] > 0 ? $priceArray1Hour[] = $ticker1Hour['price'] : false;

                // get 24hour
                $ticker24Hour = Ticker::whereBetween('ts', [(int)date(strtotime('-24 hour')), (int)date(strtotime('-23 hour 59 minute'))])
                        ->where('currency_id', new ObjectID($currency['_id']))
                        ->first();
                $ticker24Hour['price'] > 0 ? $priceArray24Hour[] = $ticker24Hour['price'] : false;

                // get 7day
                $ticker7Day = Ticker::whereBetween('ts', [(int)date(strtotime('-7 day')), (int)date(strtotime('-6 day 23 hour 59 minute'))])
                        ->where('currency_id', new ObjectID($currency['_id']))
                        ->first();
                $ticker7Day['price'] > 0 ? $priceArray7d[] = $ticker7Day['price'] : false;

                // Volume 24hour
                $volume24Hour = Volume24Hour::where('currency_id', new ObjectID($currency['_id']))
                        ->orderby('ts', 'desc')
                        ->first();
                $volume24Hour['amount'] > 0 ? $volumeArray24Hour[] = $volume24Hour['amount'] : false;
                $volume24Hour['high_price_24h'] > 0 ? $highArray24Hour[] = $volume24Hour['high_price_24h'] : false;
                $volume24Hour['low_price_24h'] > 0 ? $lowArray24Hour[] = $volume24Hour['low_price_24h'] : false;

                if ($currency['symbol'] == 'BTC') {
                    $priceArrayBTC[] = $ticker['price'];
                }
            }

            $price = count($priceArrayNow) > 0 & array_sum($priceArrayNow) > 0 ? array_sum($priceArrayNow)/count($priceArrayNow) : 0;
            $price1Hour = count($priceArray1Hour) > 0 ? array_sum($priceArray1Hour)/count($priceArray1Hour) : 0;
            $price24Hour =  count($priceArray24Hour) > 0 & array_sum($priceArray24Hour) > 0 ? array_sum($priceArray24Hour)/count($priceArray24Hour) : 0;
            $priceArray7d = count($priceArray24Hour) > 0 & array_sum($priceArray7d) > 0 ? array_sum($priceArray7d)/count($priceArray7d) : 0;

            $currencyinfo['24h_volume_usd'] = count($highArray24Hour) > 0 ? array_sum($volumeArray24Hour) / 100000 : 0;
            $currencyinfo['high_price_24h'] = count($highArray24Hour) > 0 ? array_sum($highArray24Hour)/count($highArray24Hour) : 0;
            $currencyinfo['low_price_24h'] = count($highArray24Hour) > 0 ? array_sum($lowArray24Hour)/count($lowArray24Hour) : 0;
            $currencyinfo['price_usd'] = $price > 0 ? $price : 0;
            $currencyinfo['price_btc'] = $price / (count($priceArrayBTC) > 0 & array_sum($priceArrayBTC) > 0 ? array_sum($priceArrayBTC)/count($priceArrayBTC) : 1);
            $currencyinfo['percent_change_1h'] = $price1Hour > 0 & $price > 0 ? 100 * ($price - $price1Hour)/ $price : 0;
            $currencyinfo['percent_change_24h'] = $price24Hour > 0 & $price > 0 ? 100 * ($price - $price24Hour)/ $price : 0;
            $currencyinfo['percent_change_7d'] = $priceArray7d > 0 & $price > 0 ? 100 * ($price - $priceArray7d)/ $price : 0;
        }

        $tickerache = new TickerCache();
        $tickerache['list'] = json_decode($currencyinfos);
        $tickerache->save();
        return $this->successJsonResponse(['list' => $tickerache['list']]);
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
