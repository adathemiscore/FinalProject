@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">  
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
                <div class="card-header">{{ __('FeedBack Message') }}</div>

                <div class="card-body">

                    <form method="POST" action="{{route('feedbackstore')}}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Title') }}</label>

                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}"  required>

                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                          <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Message') }}</label>

                          <div class="col-md-6">
                              {{-- <input id="message" type="text" class="form-control @error('message') is-invalid @enderror" name="message" value="{{ old('message') }}"  required> --}}
                                <textarea name="message" id="message" cols="20" class="form-control @error('message') is-invalid @enderror" rows="5" required>{{ old('message')}}</textarea>
                              @error('message')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                          </div>
                      </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                
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
                                <a class="btn btn-secondary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    View
                                </a>
                              {{-- <div class="dropdown">
                                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                  Action
                                </a>
                              
                                <ul class="dropdown-menu">
                                  <li><a class="dropdown-item" href="#">Delete</a></li>
                                  <li><a class="dropdown-item" href="#">View</a></li>
                                </ul>
                              </div> --}}
                            </td>
                        </tr>
                    @endforeach
                  </tbody>
              </table>
              {{$feedback->onEachSide(1)->links()}}
        </div>
    </div>
</div>
@endsection
