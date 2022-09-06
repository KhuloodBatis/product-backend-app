<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::select('id', 'title', 'description', 'image')->paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            // 'image' => 'required|image'
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        try {

            $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('product/image', $request->image, $imageName);
            Product::create($request->post() + ['image' => $imageName]);
            return response()->json([
                'message' => 'item added successfully'
            ]);


            throw new Exception("Some error message");
        } catch (Exception $e) {

            return $e->getMessage();
        };
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' =>'nullable'
        ]);

        try {

        $product->fill($request->post())->update();

            if ($request->hasFile('image')) {
                if ($product->image) {
                    $exist = Storage::disk('public')->exists("product/image{$product->image}");
                    if ($exist) {
                        Storage::disk('public')->delete("product/image{$product->image}");
                    }
                }
                $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('product/image', $request->image, $imageName);
                $product->image = $imageName;
                $product->save();
            }


            return response()->json([
                'message' => 'item updated successfully'
            ]);
            throw new Exception("Some error message");
            } catch (Exception $e) {

            return $e->getMessage();
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy( Product $product)
    {
        if ($product->image) {
            $exist = Storage::disk('public')->exists("product/image{$product->image}");
            if ($exist) {
                Storage::disk('public')->delete("product/image{$product->image}");
            }
        }
        $product->delete();
        return response()->json([
            'message' => 'item deleted successfully'
        ]);
    }
}
