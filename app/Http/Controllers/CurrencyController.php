<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Ticker;
use App\Models\CurrencyInfo;
use DB;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = Currency::orderby('_id')->paginate(50);
        return $this->paginateJsonResponse($currencies);
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
        $minutes = 1;
        $currency = Currency::find($id);
        $exchanges = Cache::remember('exchanges', $minutes, function () {
            return Exchange::whereIn('symbol', ['binance', 'huobi'])->get();
        });
        $currencyinfos = Cache::remember('currencyinfos', $minutes, function () {
            return CurrencyInfo::whereIn('symbol', ['BTC', 'ETH', 'LTC'])->orderby('_id')->take(100)->get();
        });
        $tickers = [];
        foreach ($currencyinfos as $currencyinfo) {
            $currencies = Currency::where('symbol', $currencyinfo['symbol'])->whereIn('exchange_id', $this->objectIDFormat($exchanges))->get();
            $tickers[] = Ticker::whereIn('exchange_id', $this->objectIDFormat($exchanges))
            ->whereIn('currency_id', $this->objectIDFormat($currencies))
            ->where('ts', '>', (int)date(strtotime('-1 minute')))
            ->orderby('ts', 'desc')
            ->take(count($this->objectIDFormat($currencies)))
            ->get()
            ->avg('price');
        }
        return $this->successJsonResponse(['data' => $tickers]);
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
