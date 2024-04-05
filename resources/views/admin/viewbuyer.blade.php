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
                          <th scope="col">First Name</th>
                          <th scope="col">Last Name</th>
                          <th scope="col">Email</th>
                          <th scope="col">Phone</th>
                          <th scope="col">Role</th>

                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($buyer as $buyers)
                        <tr>
                          <td>{{$buyers['id']}}</td>
                          <td>{{$buyers['first_name']}}</td>
                          <td>{{$buyers['last_name']}}</td>
                          <td>{{$buyers['email']}}</td>
                          <td>{{$buyers['phone']}}</td>
                          <td>{{$buyers['role']}}</td>

                          <td>
                            <div class="dropdown">
                              <a class="btn btn-success dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                              </a>
                            
                              <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{route('deleteUser', $buyers['id'])}}">Delete</a></li>
                                <!-- <li><a class="dropdown-item" href="#">View</a></li> -->
                              </ul>
                            </div>
                          </td>
                        </tr>
                       @endforeach
                       
                      </tbody>
                  </table>
                </div>
              </div>
            </div>  
            <br>
              {{$buyer->onEachSide(1)->links()}}
          </div>    
@endsection