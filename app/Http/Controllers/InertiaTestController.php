<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\inertiaTest;

class InertiaTestController extends Controller
{
    public function index()
    {
        return Inertia::render('Inertia/Index');    // resources/js/Pages/Inertia/Index.vue
    }

    public function show($id)
    {
        // dd($id);
        return Inertia::render('Inertia/show', ['id' => $id]);
    }

    public function store(Request $request)
    {
        $inertiaTest = new inertiaTest;
        $inertiaTest->title = $request->title;
        $inertiaTest->content = $request->content;
        $inertiaTest->save();

        return to_route('inertia.index');
    }
}
