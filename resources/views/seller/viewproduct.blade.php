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
  
                <button class="btn btn-light" type="button"><a href="/seller">Back</a></button>
              </div>
            <div class="col-md-12">
                <br><br>
                <div class="card">
                    <table class="table table-striped justify-content-center">
                        <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">Product Name</th>
                              <th scope="col">Description</th>
                              <th scope="col">Price</th>
                              <th scope="col">Picture</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($product as $products)

                                <tr>
                                    {{-- {{dd('products/'.$products['picture'])}} --}}
                                    <td>{{$products['id']}}</td>
                                    <td>{{$products['name']}}</td>
                                    <td>{{$products['description']}}</td>
                                    <td>${{$products['price']}}</td>
                                    <td>
                                        <img src="{{ URL('products/'.$products['picture'])}}" alt="" width="50" height="50">
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                              Action
                                            </a>
                                          
                                            <ul class="dropdown-menu">
                                              <li><a class="dropdown-item" href="{{route('deleteproduct', [$products['id']])}}">Delete</a></li>
                                              <!-- <li><a class="dropdown-item" href="#">View</a></li> -->
                                            </ul>
                                          </div>
                                          
                                    </td>
                                    
                                </tr>
                            @endforeach
                          </tbody>

                          {{-- <tfooter>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Price</th>
                                    <th>Action</th>
                                </tr>
                          </tfooter> --}}
                      </table>

                    </div>
                  </div>
                </div>  
                <br>
                  {{$product->onEachSide(1)->links()}}
              </div>    
@endsection