<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\Subtotal;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    /**
     * Models/Scopesで設定したグローバルスコープが適用される Order::all()で実行されるクエリを変更できる
     */
    protected static function booted()
    {
        static::addGlobalScope(new Subtotal);
    }

    /**
     * 分析データ用のスコープクエリ
     * 4つのパターンで想定
     */
    public function scopeBetweenDate($query, $startDate = null, $endDate = null)
    {
        // ①$startDateと$endDateが空だったら
        if(is_null($startDate) && is_null($endDate))
        {
            return $query;
        }

        // ②$startDateが空じゃなかったら
        if(!is_null($startDate) && is_null($endDate))
        {
            return $query->where('created_at', ">=", $startDate);
        }

        // ③$endDateが空じゃなかったら
        if(is_null($startDate) && !is_null($endDate))
        {
            // 日付のみの指定で時間指定をしてないので$endDateに入る値が【2022-09-26 00:00:00】になり検索対象に実質入っていないことになっていた
            // プラス1日を施すようにする
            $endDate1 = Carbon::parse($endDate)->addDay(1);
            return $query->where('created_at', "<=", $endDate1);
        }
        
        // ④$startDateと$endDateが空じゃなかったら
        if(!is_null($startDate) && !is_null($endDate))
        {
            $endDate1 = Carbon::parse($endDate)->addDay(1);
            return $query->where('created_at', ">=", $startDate)->where('created_at', "<=", $endDate1);
        }
    }
}
