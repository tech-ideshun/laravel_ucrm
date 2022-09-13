<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\inertiaTest;

class InertiaTestController extends Controller
{
    public function index()
    {
        // resources/js/Pages/Inertia/Index.vue
        return Inertia::render('Inertia/Index', [
            'blogs' => inertiaTest::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Inertia/Create');
    }

    public function show($id)
    {
        return Inertia::render('Inertia/show', [
            'id' => $id,
            'blog' => inertiaTest::findOrFail($id)
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => ['required', 'max:20'],
            'content' => ['required'],
        ]);

        $inertiaTest = new inertiaTest;
        $inertiaTest->title = $request->title;
        $inertiaTest->content = $request->content;
        $inertiaTest->save();

        return to_route('inertia.index')
        ->with([
            'message' => '登録しました。'
        ]);
    }

    public function delete($id)
    {
        $book = inertiaTest::findOrFail($id);
        $book->delete();

        return to_route('inertia.index')
        ->with([
            'message' => '削除しました。'
        ]);
    }
}
