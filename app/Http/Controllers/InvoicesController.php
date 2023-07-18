<?php

namespace App\Http\Controllers;

use App\Models\invoice_attachment;
use App\Models\invoices;
use App\Models\invoices_detail;
use App\Models\section;
use App\Notifications\AddInvoice;
use App\Traits\UploadImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InvoicesController extends Controller
{
    use UploadImages ;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = invoices::all();

        return view('invoices.invoices', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = section::all();
        return view('invoices.add_invoice', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->invoiceInputValidation($request);
        // add inovice
        invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => '2',
            'note' => $request->note,
        ]);

        // add invoices details
        $invoice_id = invoices::latest()->first()->id;
        invoices_detail::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => '2',
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        // add attachment
        if ($request->hasFile('pic')) {
            $rules = [
                'pic' => 'required|max:10000|mimes:pdf,png,jpg ,jpeg',
            ];
            $messages = [
                'pic.mimes' => 'خطا تم حفظ الفاتورة ولم يتم حفظ المرفق ارجو الالتزام بالامتداد',
            ];
            $this->validate($request, $rules, $messages);

            $uploadimage = $this->uploadImage($request, $request->invoice_number, 'pic');

            invoice_attachment::create([
                'file_name' => $uploadimage[0],
                'path' => $uploadimage[1],
                'invoice_number' => $request->invoice_number,
                'Created_by' => Auth::user()->name,
                'invoice_id' => $invoice_id,
            ]);
        }


        $user = Auth::user();
        Notification::send($user, new AddInvoice($invoice_id));
        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\invoices $invoices
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoices = invoices::where('id', $id)->first();

        return view('invoices.status_update', compact('invoices'));
    }

    public function Status_Update($id, Request $request)
    {
        $invoices = invoices::findorfail($id);

        $value_status = $invoices->Value_Status;
        if ($request->Status === 'مدفوعة') {
            $value_status = 1;
        }else{
            $value_status = 3;
        }
        $invoices->update([
            'Value_Status' => $value_status,
            'Status' => $request->Status,
            'Payment_Date' => $request->Payment_Date,
        ]);
        $this->create_new_invoice($value_status , $request);
        session()->flash('Status_Update');
        return redirect('invoices');
    }

    public function create_new_invoice($value_status ,Request $request){
        invoices_detail::create([
            'id_Invoice' =>$request->invoice_id ,
            'invoice_number' => $request->invoice_number,
            'product' =>$request->product ,
            'Section' =>$request->Section ,
            'Status' => $request->Status,
            'Value_Status' => $value_status,
            'Payment_Date' => $request->Payment_Date,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\invoices $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoices = invoices::where('id', $id)->first();
        $sections = section::all();
        return view('invoices.edit_invoices', compact('sections', 'invoices'));
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\invoices $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $invoices = invoices::findorfail($request->invoice_id);
        $this->invoiceInputValidation($request);

        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\invoices $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = invoices::where('id', $id)->first();
        $Details = invoice_attachment::where('invoice_id', $id)->first();

        if(!$request->id_page == 2){
            if (!empty($Details->invoice_number)) {
                Storage::disk('attachments')->deleteDirectory($Details->invoice_number);
            }
            $invoices->forceDelete();
            session()->flash('delete_invoice');
            return redirect()->back();
        }else{
            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('/Archive');
        }

    }

    public function getProduct($id)
    {
        $products = DB::table('products')->where('section_id', $id)->pluck('product_name', 'id');
        return json_encode($products);
    }

    public function invoiceInputValidation(Request $request)
    {
        $rules = [
            'invoice_number' => 'required',
            'invoice_Date' => 'required|date',
            'Due_date' => 'required|date',
            'product' => 'required|string|exists:products,Product_name',
            'Section' => 'required|numeric|exists:sections,id',
            'Amount_collection' => 'required|numeric|max:999999.99',
            'Amount_Commission' => 'required|numeric|max:999999.99',
            'Discount' => 'required|numeric|max:999999.99',
            'Value_VAT' => 'required|numeric|max:999999.99',
            'Rate_VAT' => 'required',
            'Total' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return back()->with('errors', $validation->errors());
        }
    }

    public function paid(){
        $invoices = invoices::where('Value_Status' , 1)->get();
        return view('invoices.invoices_paid' , compact('invoices'));
    }

    public function unpaid(){
        $invoices = invoices::where('Value_Status' , 2)->get();
        return view('invoices.invoices_unpaid' , compact('invoices'));
    }

    public function partial(){
        $invoices = invoices::where('Value_Status' , 3)->get();
        return view('invoices.invoices_Partial' , compact('invoices'));
    }

    public function print($id){
        $invoices = invoices::where('id' , $id)->first();

        return view('invoices.Print_invoice', compact('invoices'));
    }
}
