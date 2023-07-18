<?php

namespace App\Http\Controllers;

use App\Models\invoice_attachment;
use App\Models\invoices;
use App\Models\invoices_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class InvoicesDetailController extends Controller
{
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\invoices_detail $invoices_detail
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $invoices = invoices::where('id', $id)->first();
        $details = invoices_detail::where('id_Invoice', $id)->get();
        $attachments = invoice_attachment::where('invoice_id', $id)->get();

        return view('invoices.invoices_details', compact('invoices', 'details', 'attachments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\invoices_detail $invoices_detail
     * @return \Illuminate\Http\Response
     */
    public function edit(invoices_detail $invoices_detail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\invoices_detail $invoices_detail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, invoices_detail $invoices_detail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\invoices_detail $invoices_detail
     * @return \Illuminate\Http\Response
     */
    public function open_file($invoice_number, $file_name)
    {
        $path = $invoice_number . '/' . $file_name;
        $path_file = Storage::disk('attachments')->getDriver()->getAdapter()->applyPathPrefix($path);

        return response()->file($path_file);
    }

    public function get_file($invoice_number, $file_name)
    {
        $path = $invoice_number . '/' . $file_name;
        return Storage::disk('attachments')->download($path);

    }

    public function destroy(Request $request)
    {
        $invoices = invoice_attachment::find($request->id_file);
        $invoices->delete();
        $path = $request->invoice_number . '/' . $request->file_name;
        Storage::disk('attachments')->delete($path);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

}
