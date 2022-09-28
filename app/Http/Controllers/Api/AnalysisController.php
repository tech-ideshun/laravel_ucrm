<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

use App\Services\AnalysisService;
use App\Services\DecileService;

class AnalysisController extends Controller
{
    /**
     * apiにGet通信した際のレスポンスを返すメソッド
     */
    public function index(Request $request)
    {
        // ①準備段階
        $subQuery = Order::betweenDate($request->startDate, $request->endDate);

        // 年月日
        if($request->type === 'perDay'){
            // AnalysisServiceで定義したreturnが配列で3つの値を返すようにしている
            // list($Arg1, $Arg2, $Arg3) = [value1, value2, value3]みたいな感じで準備にlistの変数に格納される
            list($data, $labels, $totals) = AnalysisService::perDay($subQuery);
        }

        // 年月
        if($request->type === 'perMonth'){
            list($data, $labels, $totals) = AnalysisService::perMonth($subQuery);
        }

        // 年
        if($request->type === 'perYear'){
            list($data, $labels, $totals) = AnalysisService::perYear($subQuery);
        }

        // デシル
        if($request->type === 'decile'){
            list($data, $labels, $totals) = DecileService::decile($subQuery);
        }

        // Ajax通信なのでJson形式で返却する必要がある→ response()->json([~...])
        return response()->json([
            'data' => $data,
            'type' => $request->type,
            'labels' => $labels,
            'totals' => $totals,
        ], Response::HTTP_OK);   // Response::HTTP_OK → 通信成功時の【200】番がLaravel側で定数として設定されている
    }
}
