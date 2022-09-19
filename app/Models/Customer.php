<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'kana',
        'tel',
        'email',
        'postcode',
        'address',
        'birthday',
        'gender',
        'memo'
    ];

    // スコープメソッドはメソッド名の頭に【scope】とつけるのがお約束
    // $inputはvue画面で検索画面があり、検索された際に引数として渡されます。
    public function scopeSearchCustomers($query, $input = null)
    {
        if(!empty($input)) {
            // DBのカラムの【kana】【tel】に対して前方一致するか見て、->exist()で存在していればreturnとしてます。
            if(Customer::where('kana', 'like', $input . '%')->orWhere('tel', 'like', $input . '%')->exists()) {
                return $query->where('kana', 'like', $input . '%')->orWhere('tel', 'like', $input . '%');
            }
        }
    }
}
