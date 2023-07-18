<?php

namespace App\Http\Controllers;

use App\Models\invoices;
use Illuminate\Http\Request;

class Invoices_Report extends Controller
{
    public function index()
    {
        return view('reports.invoices_report');
    }

    public function Search_invoices(Request $request)
    {
        $rdio = $request->rdio;

        if ($rdio == 1) {
            if ($request->type && $request->start_at == '' && $request->end_at == '') {
                $invoices = invoices::where('Status', '=', $request->type)->get();
                $type = $request->type;
                return view('reports.invoices_report', compact('type'))->withDetails($invoices);
            } else {
                $start_at = date($request->start_at);
                $end_at = date($request->end_at);
                $invoices = invoices::whereBetween('invoice_Date', [$start_at, $end_at])->withDetails($invoices);
                $type = $request->type;
                return view('reports.invoices_report', compact('type' , 'start_at' , 'end_at'))->withDetails($invoices);
            }
        } else {
            $invoices = invoices::where('invoice_number' , '=' , $request->invoice_number)->get();
            return view('reports.invoices_report')->withDetails($invoices);
        }
    }
}
