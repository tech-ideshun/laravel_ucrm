<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;

use Inertia\Inertia;

use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Item;

use Illuminate\Support\Facades\DB;

use App\Models\Order;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(Order::paginate(50));

        $orders = Order::groupBy('id')
        ->selectRaw('id, sum(subtotal) as total, customer_name, status, created_at')
        ->orderBy('id', 'asc')
        ->paginate(50);

        // dd($orders);

        return Inertia::render('Purchases/Index' , [
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $customers = Customer::select('id', 'name', 'kana')->get();
        // 販売中のものだけselectしてます。
        $items = Item::select('id', 'name', 'price')
        ->where('is_selling', true)
        ->get();

        return Inertia::render('Purchases/Create', [
            // 'customers' => $customers,
            'items' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseRequest $request)
    {
        // dd($request);

        DB::beginTransaction(); // transactionの開始
        try{
            $purchase = Purchase::create([  // これでpurchasesテーブルに一旦保存できている。後に$purchase->idとして取得できるようになっている。
                'customer_id' => $request->customer_id,
                'status' => $request->status
            ]);
    
            foreach($request->items as $item) {
                $purchase->items()->attach($purchase->id, [
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }
                //$purchase->items()->attach
                // └purchasesテーブルとitemsテーブルの中間テーブルにデータを【追加】したい時には【attach()】を使う
                // 第一引数は中間テーブルに保存するidで指定。item_idは適切ではない。
                // $purchase->idと取得出来ているのは最初のコードで先にpurchasesテーブルに登録をしており、それを変数に入れているからオブジェクトが完成している
            
            DB::commit();   // transactionを完全に実行する
            
            return to_route('dashboard');

        } catch(\Exception $e){
            DB::rollBack(); // 失敗時の処理。try部分で一部成功していた部分を全て白紙にする
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        // 小計(itemごとの小計)
        $items = Order::where('id', $purchase->id)->get();  // グローバルスコープで4つのテーブルを既にLeftJoinしてます。

        // 合計
        $order = Order::groupBy('id')
        ->where('id', $purchase->id)
        ->selectRaw('id, sum(subtotal) as total, customer_name, status, created_at')
        ->orderBy('created_at', 'desc')
        ->get();

        // dd($items, $order);

        return Inertia::render('Purchases/Show', [
            'items' => $items,
            'order' => $order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        // 仕様的にstatus(キャンセル済み→0)が0の場合、編集画面に移行するボタンはないが直リンクできると編集画面に入れるので0だったら404画面を返す
        if($purchase->status === 0) {
            abort(404);
        }
        $purchase = Purchase::find($purchase->id);

        $allItems = Item::select('id', 'name', 'price')->get();

        $items = [];

        foreach($allItems as $allitem) {
            $quantity = 0;
            foreach($purchase->items as $item) {
                if($allitem->id === $item->id) {
                    $quantity = $item->pivot->quantity;
                }
            }
            array_push($items, [
                'id' => $allitem->id,
                'name' => $allitem->name,
                'price' => $allitem->price,
                'quantity' => $quantity,
            ]);
        }
        // dd($items);  // 購入している商品だけ$quantityが入っている状態

        $order = Order::groupBy('id')
        ->where('id', $purchase->id)
        ->selectRaw('id, customer_id, customer_name, status, created_at')
        ->orderBy('created_at', 'desc')
        ->get();

        return Inertia::render('Purchases/Edit', [
            'items' => $items,
            'order' => $order,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePurchaseRequest  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        // dd($request, $purchase);

        DB::beginTransaction(); // transactionの開始
        try{
            $purchase->status = $request->status;
            $purchase->save();

            $items = [];

            foreach($request->items as $item){  // 中間テーブルへ個数を変更、更新するためオリジナルの配列を作成
                $items = $items + [
                    $item['id'] => [
                        'quantity' => $item['quantity']
                    ]
                ];
            }
            // dd($items);

            $purchase->items()->sync($items);   // syncメソッド→中間テーブルへ値を【更新】するメソッド
            // storeで中間テーブルへ値を【追加】する際は【->attach()】で処理(多分syncでもいける)
            // 今回は中間テーブルの値を【更新】する際なので【->sync()】で処理
            // 元となるModel->リレーション先テーブルメソッド名->->sync([配列]);で値を更新させれる
            // 参考サイト:https://blog.capilano-fw.com/?p=7407


            DB::commit();   // transactionを完全に実行する
            return to_route('dashboard');
        } catch(\Exception $e){
            DB::rollBack(); // 失敗時の処理。try部分で一部成功していた部分を全て白紙にする
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
