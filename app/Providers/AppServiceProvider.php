<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        $request = new Request();
        $url = $request->url();
        $param = $request->getRequestUri();
        $token = $request->input('token');
        $source = $request->input('source');

        $uuid = uniqid(time());
        $bdid = uniqid(time());
        $uid = uniqid(time());
        $logData = [
            'uid' => $uid, //判断用户的uid，需要根据自己的业务判断
            'router' => $url, //请求的url
            'time' => date('Y-m-d H:i:s'),
            'currentip' => "127.0.0.1", //IP地址
            'params' => $param  //请求的参数
        ];
        //listen db
        DB::listen(function ($query) use ($logData) {
            $tmp = str_replace('?', '"' . '%s' . '"', $query->sql);
            $message = @vsprintf($tmp, $query->bindings);
            Log::info($message);
        });
    }

}
