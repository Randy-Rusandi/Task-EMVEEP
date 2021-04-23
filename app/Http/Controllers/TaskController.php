<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Courier;
use App\Models\Payment;
use App\Models\Invoice;

class TaskController extends Controller
{
    public function index() {
        return view('task',[
            'salesmans' => Sales::all(),
            'courier_list' => Courier::all(),
            'payment_types' => Payment::all()
        ]);
    }

    public function load($id) {
        $invoice = Invoice::find($id);
        return view('task',[
            'salesmans' => Sales::all(),
            'courier_list' => Courier::all(),
            'payment_types' => Payment::all(),
            'invoice' => $invoice
        ]);
    }

    public function find() {
        $invoice = Invoice::find(request('no_invoice'));
        if ($invoice && $invoice->id) {
            return redirect('/'.$invoice->id)->withSuccess('Load Invoice Number '.$invoice->id);
        }
        else {
            return redirect('/')->withErrors("Invoice number not found!");
        }
    }

    public function invoice() {
        $invoice = new Invoice();
        $message = 'Invoice Saved with Invoice Number is ';
        try {
           if(request('invoice_id')) {
               $temp = Invoice::find(request('invoice_id'));
               if ($temp && $temp->id) {
                   $invoice = $temp;
                   $message = 'Invoice Updated and the Invoice Number is ';
               }
           }
           $date = \DateTime::createFromFormat('d/m/Y', request('invoice_date'));
           $invoice->invoice_date = $date->format('Y-m-d H:i:s');
           $invoice->customer = request('customer');
           $invoice->shipment = request('shipment');
           $invoice->sales_id = request('sales_id');
           $invoice->payment_type_id = request('payment_type_id');
           $invoice->courier_id = request('courier_id');
           $invoice->sub_total = 0;
           $invoice->grand_total = 0;
           $invoice->courier_fee = 0;
           $invoice->save();
        } catch (\Exception $e) {
             return redirect('/')->withErrors([$e->getMessage()]);
        }
        return redirect('/'.$invoice->id)->withSuccess($message.$invoice->id);
    }
}
