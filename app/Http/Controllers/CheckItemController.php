<?php

namespace App\Http\Controllers;

use App\Models\CheckItem;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class CheckItemController extends Controller
{
    public function show($id)
    {
        $item = CheckItem::with('vehicle')->findOrFail($id);
        return view('checking.detail_item', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = CheckItem::findOrFail($id);
        $item->update($request->all());

        return back()->with('success', 'Detail pengecekan berhasil diperbarui.');
    }
}
