@extends('layouts.app')
@section('scripts')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
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
            var grand = parseInt($('#courierfee').text()) + total;
            $('#grandtotal').text(grand);
        });
    </script>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="/invoice_parent" method="POST">
                        <label for="no_invoice">No Invoice</label>
                        <input type="text" id="no_invoice" name="no_invoice">
                        <button class="btn btn-primary" type="submit">View</button>
                    </form>
                    <br />
                    <form action="/invoice" method="POST">
                        <div class="card">
                            <div class="card-header">Invoice Detail</div>
                            <div class="card-body">
                                <div class="row form-group">
                                    <div class="col-sm-2">
                                        <label for="invoice_date">Invoice Date</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" id="invoice_date" name="invoice_date" class="form-control datepicker" placeholder="Select Date">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2">
                                        <label for="customer">To</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <textarea type="text" id="customer" name="customer" class="form-control"></textarea>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="shipment">Ship To</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <textarea type="text" id="shipment" name="shipment" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2">
                                        <label for="sales_name">Sales Name</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select id="sales_name" name="sales_name" class="form-control">
                                            @foreach ($salesmans as $sales)
                                                <option value="{{ $sales->id }}"> {{ $sales->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="payment_type">Payment Type</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select id="payment_type" name="payment_type" class="form-control">
                                            <option value="cash">Cash</option>
                                            <option value="cod">COD</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2">
                                        <label for="courier">Courier</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select id="courier" name="courier" class="form-control">
                                            @foreach ($courier_list as $courier)
                                                <option value="{{ $courier->id }}"> {{ $courier->name }}</option>
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
                                              <td><input type="text" id="item{{ $i }}" name="item{{ $i }}" class="form-control"></td>
                                              <td><input type="text" id="weight{{ $i }}" name="weight{{ $i }}" class="form-control"></td>
                                              <td><input type="text" id="qty{{ $i }}" name="qty{{ $i }}" class="form-control"></td>
                                              <td><input type="text" id="price{{ $i }}" name="price{{ $i }}" class="form-control"></td>
                                              <td><input type="text" id="total{{ $i }}" class="form-control total_item" name="total{{ $i }}" class="form-control"></td>
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
                               <span id="subtotal">99999</span>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-sm-7">
                           </div>
                           <div class="col-sm-2">
                               <label for="invoice_date">Courier Fee</label>
                           </div>
                           <div class="col-sm-3 text-right">
                               <span id="courierfee">99999</span>
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
                               <span id="grandtotal">99999</span>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-sm-7">
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
