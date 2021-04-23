<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Courier;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\InvoiceDetail;


class TaskController extends Controller
{
    public function index() {
        return view('task',[
            'salesmans' => Sales::all(),
            'courier_list' => Courier::all(),
            'payment_types' => Payment::all(),
            'products' => Product::all()
        ]);
    }

    public function load($id) {
        $invoice = Invoice::find($id);
        $invoice_details = InvoiceDetail::where('parent_id',$id)->get();
        return view('task',[
            'salesmans' => Sales::all(),
            'courier_list' => Courier::all(),
            'payment_types' => Payment::all(),
            'products' => Product::all(),
            'invoice' => $invoice,
            'invoice_details' => $invoice_details
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
            \DB::beginTransaction();
            if(request('invoice_id')) {
                $temp = Invoice::find(request('invoice_id'));
                if ($temp && $temp->id) {
                    $invoice = $temp;
                    $message = 'Invoice Updated and the Invoice Number is ';
                }
            }
            $date = \DateTime::createFromFormat('d/m/Y', request('invoice_date'));
            if (is_bool($date)) {
               throw new \Exception('Please fill the Invoice Date.');
            }
            $invoice->invoice_date = $date->format('Y-m-d H:i:s');
            $invoice->customer = request('customer');
            $invoice->shipment = request('shipment');
            $invoice->sales_id = request('sales_id');
            $invoice->payment_type_id = request('payment_type_id');
            $invoice->courier_id = request('courier_id');
            $invoice->sub_total = request('input_sub_total');;
            $invoice->courier_fee = request('input_courier_fee');;
            $invoice->grand_total = request('input_grand_total');;
            if ($invoice->grand_total == 0) {
                throw new \Exception('Please insert a valid invoice.');
            }
            $invoice->save();

            $check = false;
            for ($i=0 ; $i < 9 ; $i++){
                $detail = new InvoiceDetail();
                if(request('item_id_'.$i)) {
                    $temp = InvoiceDetail::find(request('item_id_'.$i));
                    if ($temp && $temp->id) {
                        $detail = $temp;
                    }
                }
                if(request('item_'.$i) && request('item_'.$i) > 0 && request('qty_'.$i) && request('qty_'.$i) > 0) {
                    $check = true;
                    $detail->parent_id = $invoice->id;
                    $detail->item_index = request('item_index_'.$i);
                    $detail->product_id = request('item_'.$i);
                    $detail->weight = request('weight_'.$i);
                    $detail->qty = request('qty_'.$i);
                    $detail->price = request('price_'.$i);
                    $detail->total = request('total_'.$i);
                    $detail->save();
                }
                else if ($detail->id > 0) {
                    $detail->delete();
                }
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return redirect('/')->withErrors(['Please fill all the field except Invoice Number.']);
        }
        return redirect('/'.$invoice->id)->withSuccess($message.$invoice->id);
    }
}
