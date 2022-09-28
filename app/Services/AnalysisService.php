<?php   // サービスファイルのひな形はないので一から手動で入力

namespace App\Services;
use Illuminate\Support\Facades\DB;

class AnalysisService
{
    // static → クラス名::perDayと呼べるようにしている
    public static function perDay($subQuery)
    {
        // ①で取得したデータをさらに絞り込み
        $query = $subQuery->where('status', true)
        ->groupBy('id') // purchasesテーブルのIdでグルーピング
        ->selectRaw('id, sum(subtotal) as totalPerPurchase, DATE_FORMAT(created_at, "%Y%m%d") as date');    // DATE_FORMATはSQLのメソッドです。

        // ② ①で作ったサブクエリをgroupByで日毎にまとめる
        $data = DB::table($query)
        ->groupBy('date')
        ->selectRaw('date, sum(totalPerPurchase) as total')
        ->get();

        // グラフように上記$dataで日別に絞りこんだデータ群から【data】【total】だけを変数に抽出
        $labels = $data->pluck('date');
        $totals = $data->pluck('total');

        // コントローラーで呼ばれた際に配列で返す意図がある
        return [$data, $labels, $totals];
    }

    /**
     * selectRawでDATE_FORMATを"%Y%m"年月にして月別で抽出する
     */
    public static function perMonth($subQuery)
    {
        // ①で取得したデータをさらに絞り込み
        $query = $subQuery->where('status', true)
        ->groupBy('id') // purchasesテーブルのIdでグルーピング
        ->selectRaw('id, sum(subtotal) as totalPerPurchase, DATE_FORMAT(created_at, "%Y%m") as date');    // DATE_FORMATはSQLのメソッドです。

        // ② ①で作ったサブクエリをgroupByで日毎にまとめる
        $data = DB::table($query)
        ->groupBy('date')
        ->selectRaw('date, sum(totalPerPurchase) as total')
        ->get();

        // グラフように上記$dataで日別に絞りこんだデータ群から【data】【total】だけを変数に抽出
        $labels = $data->pluck('date');
        $totals = $data->pluck('total');

        // コントローラーで呼ばれた際に配列で返す意図がある
        return [$data, $labels, $totals];
    }

    /**
     * selectRawでDATE_FORMATを"%Y"年にして年別で抽出する
     */
    public static function perYear($subQuery)
    {
        // ①呼び出されたコントローラーで取得したデータをさらに絞り込み
        $query = $subQuery->where('status', true)
        ->groupBy('id') // purchasesテーブルのIdでグルーピング
        ->selectRaw('id, sum(subtotal) as totalPerPurchase, DATE_FORMAT(created_at, "%Y") as date');    // DATE_FORMATはSQLのメソッドです。

        // ② ①で作ったサブクエリをgroupByで日毎にまとめる
        $data = DB::table($query)
        ->groupBy('date')
        ->selectRaw('date, sum(totalPerPurchase) as total')
        ->get();

        // グラフように上記$dataで日別に絞りこんだデータ群から【data】【total】だけを変数に抽出
        $labels = $data->pluck('date');
        $totals = $data->pluck('total');

        // コントローラーで呼ばれた際に配列で返す意図がある
        return [$data, $labels, $totals];
    }
}