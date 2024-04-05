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
              {{-- <a href="/feedbackmessage" class="btn btn-light">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                  </svg>
                  Add FeedBack Category Message
              </a> --}}
                <div class="justify-content-right">
                </div>
                <table class="table table-striped justify-content-center">
                    <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">UserId</th>
                          <th scope="col">Title</th>
                          <th scope="col">Message</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($feedback as $feedbacks)
                            <tr>
                                <td>{{$feedbacks['id']}}</td>
                                <td>{{$feedbacks['user_id']}}</td>
                                <td>{{$feedbacks['title']}}</td>
                                <td>{{$feedbacks['message']}}</td>
                                <td>
                                  <div class="dropdown">
                                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                      Action
                                    </a>
                                  
                                    <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{route('deletefeedback', [$feedbacks['id']])}}">Delete</a></li>
                                      <!-- <li><a class="dropdown-item" href="#">View</a></li>-->
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
              {{$feedback->onEachSide(1)->links()}}
          </div>    
@endsection