<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    /**
     * apiにGet通信した際のレスポンスを返すメソッド
     */
    public function index(Request $request)
    {
        // ①準備段階
        $subQuery = Order::betweenDate($request->startDate, $request->endDate);

        if($request->type === 'perDay')
        {
            // ①で取得したデータをさらに絞り込み
            $subQuery->where('status', true)
            ->groupBy('id') // purchasesテーブルのIdでグルーピング
            ->selectRaw('id, sum(subtotal) as totalPerPurchase, DATE_FORMAT(created_at, "%Y%m%d") as date');    // DATE_FORMATはSQLのメソッドです。

            // ② ①で作ったサブクエリをgroupByで日毎にまとめる
            $data = DB::table($subQuery)
            ->groupBy('date')
            ->selectRaw('date, sum(totalPerPurchase) as total')
            ->get();
        }


        // Ajax通信なのでJson形式で返却する必要がある→ response()->json([~...])
        return response()->json([
            'data' => $data,
            'type' => $request->type
        ], Response::HTTP_OK);   // Response::HTTP_OK → 通信成功時の【200】番がLaravel側で定数として設定されている
    }
}
