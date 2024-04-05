@extends('layouts.app')

@section('content')
<div class="container pt-2">
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
                <div class="card-header">{{ __('Admin Login Token') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('postadmintoken') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                       

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>

                                {{-- @if (Route::has('password.request')) --}}
                                    <a class="btn btn-link   offset-md-4" href="{{ route('postadmin') }}">
                                        {{ __('Login Admin') }}
                                    </a>
                                {{-- @endif --}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
