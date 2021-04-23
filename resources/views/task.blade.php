@extends('layouts.app')
@section('scripts')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        @php
            if(isset($invoice_details)) {
                $details_array = array();
                foreach ($invoice_details as $details) {
                    $details_array[$details->item_index]['id'] = $details->id;
                    $details_array[$details->item_index]['product_id'] = $details->product_id;
                    $details_array[$details->item_index]['weight'] = $details->weight;
                    $details_array[$details->item_index]['qty'] = $details->qty;
                    $details_array[$details->item_index]['price'] = $details->price;
                    $details_array[$details->item_index]['total'] = $details->total;
                }
            }

            $arr = array();
            foreach ($products as $value) {
                $arr[$value->id]['name'] = $value->name;
                $arr[$value->id]['weight'] = $value->weight;
                $arr[$value->id]['price'] = $value->price;
            }
        @endphp
        var products = {!! json_encode($arr) !!};

        @php
            $arr = array();
            foreach ($courier_list as $value) {
                $arr[$value->id] = $value->fee;
            }
        @endphp
        var courier_fee = {!! json_encode($arr) !!};
        $( function() {
            $( ".datepicker" ).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd/mm/yy'
            });
        });
        $( ".total_item" ).change(function() {
            var $inputs = $('.total_item');
            var total=0;
            var ids = {};
            $inputs.each(function (index)
            {
                var temp = parseInt($(this).val());
                if(isNaN(temp)) {
                    temp = 0;
                }
                total+= temp;
            });
            $('#subtotal').text(total);
            $('#input_sub_total').val(total);

            var fee = courier_fee[jQuery('#courier_id').val()];
            var total_fee = 0;
            for (i = 0; i < 9; i++) {
                var temp_weight = $('#weight_' + i).val();
                if(isNaN(temp_weight)) {
                    temp_weight = 0;
                }
                var temp_qty = $('#qty_' + i).val()
                if(isNaN(temp_qty)) {
                    temp_qty = 0;
                }
                total_fee += temp_weight * temp_qty * fee;
            }

            $('#courierfee').text(total_fee);
            $('#input_courier_fee').val(total_fee);

            var grand = parseInt($('#courierfee').text()) + total;
            $('#grandtotal').text(grand);
            $('#input_grand_total').val(grand);
        });
        $( ".item_drop" ).change(function() {
            var row = $(this).attr("id").split("_")[1];
            if ($(this).val() != 0 ) {
                var product_id = $(this).val();
                $('#weight_' + row).val(products[product_id]['weight']);
                $('#price_' + row).val(products[product_id]['price']);
                if($('#qty_' + row).val() > 0) {
                    $('#total_' + row).val($('#qty_' + row).val() * products[product_id]['price']);
                }
                else {
                    $('#total_' + row).val('');
                }
                $('#total_' + row).trigger("change");
            }
            else {
                $('#weight_' + row).val('');
                $('#price_' + row).val('');
                $('#qty_' + row).val('');
                $('#total_' + row).val('');
                $('#total_' + row).trigger("change");
            }
        });

        $( ".qty_item" ).change(function() {
            var row = $(this).attr("id").split("_")[1];
            $('#item_' + row).trigger("change");
        });

        $( ".affect_grand" ).change(function() {
            $('#total_0').trigger("change");
        });

        $(document).ready(function() {
            $('#total_0').trigger("change");
        });
    </script>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if (session()->has('success'))
                        <div class="alert alert-success" role="alert">
                        @if (is_array(session('success')))
                            <ul>
                                @foreach (session('success') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ session('success') }}
                        @endif
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            {{$errors->first()}}
                        </div>
                    @endif
                    <form action="/find" method="POST">
                        @csrf
                        <label for="no_invoice">No Invoice</label>
                        <input type="text" id="no_invoice" name="no_invoice" value="{{ $invoice->id ?? '' }}">
                        <button class="btn btn-primary" type="submit">View</button>
                    </form>
                    <br />
                    <form action="/invoice" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-header">Invoice Detail</div>
                            <div class="card-body">
                                <div class="row form-group">
                                    <div class="col-sm-2">
                                        <label for="invoice_date">Invoice Date</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" id="invoice_date" name="invoice_date" class="form-control datepicker" value="{{ ($invoice ?? '')? $invoice->invoice_date->format('d/m/Y') : ''}}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2">
                                        <label for="customer">To</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <textarea type="text" id="customer" name="customer" class="form-control">{{ $invoice->customer ?? '' }}</textarea>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="shipment">Ship To</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <textarea type="text" id="shipment" name="shipment" class="form-control">{{ $invoice->shipment ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2">
                                        <label for="sales_name">Sales Name</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select id="sales_id" name="sales_id" class="form-control">
                                            @foreach ($salesmans as $sales)
                                                <option value="{{ $sales->id }}" {{ ($invoice ?? '')?  ($invoice->sales_id==$sales->id)? 'selected' : '' : '' }}> {{ $sales->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="payment_type">Payment Type</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select id="payment_type_id" name="payment_type_id" class="form-control">
                                            @foreach ($payment_types as $payment)
                                                <option value="{{ $payment->id }}"  {{ ($invoice ?? '')?  ($invoice->payment_type_id==$payment->id)? 'selected' : '' : '' }}> {{ $payment->type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2">
                                        <label for="courier">Courier</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select id="courier_id" name="courier_id" class="form-control affect_grand">
                                            @foreach ($courier_list as $courier)
                                                <option value="{{ $courier->id }}" {{ ($invoice ?? '')?  ($invoice->courier_id==$courier->id)? 'selected' : '' : '' }}> {{ $courier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-sm-12">
                                <table width="100%">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-right">Weight(kg)</th>
                                            <th class="text-right">QTY</th>
                                            <th class="text-right">Unit Price</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @for($i=0 ; $i < 9 ; $i++)
                                          <tr>
                                              <input type="hidden" id="item_index_{{ $i }}" name="item_index_{{ $i }}" value="{{ $i }}">
                                              <input type="hidden" id="item_id_{{ $i }}" name="item_id_{{ $i }}" value="{{ $details_array[$i]['id'] ?? 0 }}">
                                              <td>
                                                  <select id="item_{{ $i }}" name="item_{{ $i }}" class="form-control item_drop">
                                                          <option value="0" >Please select an item</option>
                                                      @foreach ($products as $product)
                                                          <option value="{{ $product->id }}" {{ ($details_array[$i]['product_id'] ?? '')?  ($details_array[$i]['product_id']==$product->id)? 'selected' : '' : '' }}> {{ $product->name }}</option>
                                                      @endforeach
                                                  </select>
                                              </td>
                                              <td><input type="text" id="weight_{{ $i }}" name="weight_{{ $i }}" class="form-control weight" value="{{ $details_array[$i]['weight'] ?? '' }}" readonly></td>
                                              <td><input type="text" id="qty_{{ $i }}" name="qty_{{ $i }}" class="form-control qty_item" value="{{ $details_array[$i]['qty'] ?? '' }}"></td>
                                              <td><input type="text" id="price_{{ $i }}" name="price_{{ $i }}" class="form-control" value="{{ $details_array[$i]['price'] ?? '' }}" readonly></td>
                                              <td><input type="text" id="total_{{ $i }}" name="total_{{ $i }}" class="form-control total_item" value="{{ $details_array[$i]['total'] ?? '' }}" readonly></td>
                                          </tr>
                                         @endfor
                                   </tbody>
                               </table>
                           </div>
                       </div>
                       <br />
                       <div class="row">
                           <div class="col-sm-7">
                           </div>
                           <div class="col-sm-2">
                               <label for="invoice_date">Subtotal </label>
                           </div>
                           <div class="col-sm-3 text-right">
                               <span id="subtotal">{{ $invoice->sub_total ?? 0 }}</span>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-sm-7">
                           </div>
                           <div class="col-sm-2">
                               <label for="invoice_date">Courier Fee</label>
                           </div>
                           <div class="col-sm-3 text-right">
                               <span id="courierfee">{{ $invoice->courier_fee ?? 0 }}</span>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-sm-7">
                           </div>
                           <div class="col-sm-4">
                               <hr>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-sm-7">
                           </div>
                           <div class="col-sm-2">
                               <label for="invoice_date">Total</label>
                           </div>
                           <div class="col-sm-3 text-right">
                               <span id="grandtotal">{{ $invoice->grand_total ?? 0 }}</span>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-sm-7">
                               <input type="hidden" id="invoice_id" name="invoice_id" value="{{ $invoice->id ?? '' }}">
                               <input type="hidden" id="input_sub_total" name="input_sub_total" value="{{ $invoice->sub_total ?? 0 }}">
                               <input type="hidden" id="input_courier_fee" name="input_courier_fee" value="{{ $invoice->courier_fee ?? 0 }}">
                               <input type="hidden" id="input_grand_total" name="input_grand_total" value="{{ $invoice->grand_total ?? 0 }}">
                               <button class="btn btn-primary" type="submit">SAVE</button>
                           </div>
                       </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
