<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Input\Input;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections = section::all();
        $products = Product::all();
        return view('products.products', compact('sections' ,'products'));
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
        $rules = [
            'Product_name' => 'required|max:255',
            'section_id' => 'required|exists:sections,id',
        ];
        $validation = Validator::make($request->all(), $rules, $this->messages());

        if ($validation->fails()) {

            return redirect('/products')->with('errors', $validation->errors());
        }

        Product::create([
            'product_name' => $request->Product_name,
            'section_id' => $request->section_id,
            'description' => $request->description,
        ]);


        session()->flash('Add', 'تم اضافة المنتج بنجاح ');
        return redirect('/products');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
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
    public function update(Request $request)
    {
        $section_name = $request->section_name;
        $section_id = section::where('section_name', $section_name)->first()->id;
        $proudct = Product::findOrFail($request->pro_id);
        
        $rules = [
            'Product_name' => 'required|max:255',
            'section_name' => 'required|exists:sections,section_name',
        ];
        $validation = Validator($request->all(), $rules, $this->messages());

        if ($validation->fails()) {
            return redirect('/products')->with('errors', $validation->errors());
        }

        $proudct->update([
            'product_name' => $request->Product_name,
            'section_id' => $section_id,
            'description' => $request->description,
        ]);

        session()->flash('Edit', 'تم تعديل المنتج بنجاح');
        return back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function messages()
    {
        return [
            'Product_name.required' => 'يرجى ادخال اسم المنتج',
            'section_id.required' => 'يرجى ادخال اسم القسم',
            'section_id.exist' => 'هذا القسم غير موجود',
        ];
    }
}
