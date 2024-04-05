@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="d-grid gap-2 col-8">
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

            <button class="btn btn-light" type="button"><a href="/home">Back</a></button>
          </div>
        <div class="col-md-12">
            <br><br>
            <div class="card">
                <table class="table table-striped justify-content-center">
                    <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Purchase Id</th>
                          <th scope="col">Amount</th>
                          <th scope="col">Email</th>
                          <th scope="col">Phone</th>
                          <!-- <th scope="col">Delivery Status</th> -->

                          <!-- <th>Action</th> -->
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($purchases as $purchase)
                        <tr>
                          <td>{{$purchase->id}}</td>
                          <td>{{$purchase->purchase_id}}</td>
                          <td>${{$purchase->total_amount}}</td>
                          <td>{{$purchase->email}}</td>
                          <td>{{$purchase->phone}}</td>
                          <!-- <td>{{$purchase->status == 'N' ? 'Not Deliverd' : 'Deliverd'}}</td> -->

                          <!-- <td>
                            <div class="dropdown">
                              <a class="btn btn-success dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                              </a>
                            
                              <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">View</a></li>
                              </ul>
                            </div>
                          </td> -->
                        </tr>
                       @endforeach
                       
                      </tbody>
                  </table>
                </div>
              </div>
            </div>  
            <br>
              {{$purchases->onEachSide(1)->links()}}
          </div>    
@endsection