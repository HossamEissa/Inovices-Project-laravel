<?php

namespace App\Http\Controllers;

use App\Models\invoice_attachment;
use App\Traits\UploadImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceAttachmentController extends Controller
{
    use UploadImages;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'file_name' => 'mimes:pdf,png,jpg ,jpeg',
        ];
        $messages = [
            'mimes' => 'صيغة المرفق يجب ان تكون pdf, jpeg , png , jpg',
        ];

        $data = $request->validate($rules, $messages);

        $uploadImage = $this->uploadImage($request, $request->invoice_number , 'file_name');
        invoice_attachment::create([
            'file_name' => $uploadImage[0],
            'path' => $uploadImage[1],
            'invoice_number' => $request->invoice_number,
            'Created_by' => (Auth::user()->name),
            'invoice_id' => $request->invoice_id,
        ]);

        session()->flash('Add', 'تم اضافة المرفق بنجاح');

        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\invoice_attachment $invoice_attachment
     * @return \Illuminate\Http\Response
     */
    public function show(invoice_attachment $invoice_attachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\invoice_attachment $invoice_attachment
     * @return \Illuminate\Http\Response
     */
    public function edit(invoice_attachment $invoice_attachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\invoice_attachment $invoice_attachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, invoice_attachment $invoice_attachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\invoice_attachment $invoice_attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(invoice_attachment $invoice_attachment)
    {
        //
    }
}
