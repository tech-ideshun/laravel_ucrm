<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Order;

use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index()
    {
        $startDate = '2022-08-01';
        $endDate = '2022-08-31';

        // $period2 = Order::betweenDate($startDate, $endDate)->paginate(50);
        // dd($period2);

        // $period = Order::betweenDate($startDate, $endDate)
        // ->groupBy('id') // globalScopeのselectをさらにidでグループ化して
        // ->selectRaw('id, sum(subtotal) as total, customer_name, status, created_at')    // selectのカラムを最低限+小計をpurchases.idごとにsumする
        // ->orderBy('created_at')
        // ->paginate(50);

        // dd($period);

        // ↓ 練習---------
        // ① 購買id(purchasesテーブルのid)毎に売上をまとめ、dateをSQLのmethodでフォーマットした状態でサブクエリを作る
        $subQuery = Order::betweenDate($startDate, $endDate)
        ->where('status', true)
        ->groupBy('id') // purchasesテーブルのIdでグルーピング
        ->selectRaw('id, sum(subtotal) as totalPerPurchase, DATE_FORMAT(created_at, "%Y%m%d") as date');    // DATE_FORMATはSQLのメソッドです。

        // ② ①で作ったサブクエリをgroupByで日毎にまとめる
        $data = DB::table($subQuery)
        ->groupBy('date')
        ->selectRaw('date, sum(totalPerPurchase) as total')
        ->get();

        // dd($data);

        return Inertia::render('Analysis');
    }
}
