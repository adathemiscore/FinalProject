@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">  
            @if(Session::has('flashMessage'))
                <div class="alert alert-danger">
                    {{ Session::get('flashMessage') }}
                </div>
            @endif
            
            @if(Session::has('flashMessageSuccess'))
                <div class="alert alert-success">
                    {{ Session::get('flashMessageSuccess') }}
            </div>

            @endif
        <div class="card">
            <div class="card-header">{{ __('Cart Items') }}
                
            </div>
            <div class="card-body">
                <table class="table text-center">
                    <thead>
                      <tr>
                        <th style="width: 34%;"></th>
                        <th style="width: 22%;">Quantity</th>
                        <th style="width: 22%;">Price</th>
                        <th style="width: 22%;"></th>
                      </tr>
                    </thead>
                    <tbody>
                        
                        @foreach($purchases as $purchase)


                        @php
                            $itempicture = DB::table('products')->where('id', '=', $purchase->product_id)->get();
                            // dd($itempicture);
                        @endphp

                        <tr>
                            @foreach($itempicture as $picture)
                                <td> <img src="{{asset("products/".$picture->picture)}}" width="60" height="60"/></td>
                            @endforeach
                            <td>{{$purchase->quantity}}</td>
                            @php
                             $totalprice = $purchase->price * $purchase->quantity
                            @endphp
                            <td>${{number_format($totalprice, 2, '.', '')}}</td>
                            <td>
                                <a href="{{route('deletecart', [Sentinel::getUser()->id, $purchase->id])}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-x-square-fill" viewBox="0 0 16 16">
                                    <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($purchases->count() > 0)
                <a href="{{route('checkout', [Sentinel::getUser()->id])}}" class="btn btn-success ">
                    CheckOut
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-check" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                        <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>
        <br>
         {{$purchases->onEachSide(1)->links()}}
    </div>
</div>
@endsection