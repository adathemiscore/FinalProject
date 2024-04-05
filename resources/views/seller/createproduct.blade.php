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
                <div class="card-header">Create a Product</div>
                    <div class="card-body bg-white">
                        
                    <form action="/storeproduct" method="POST" enctype="multipart/form-data">
                     @csrf
                    <div class="form-group">
                        <label for="title">Product Name:</label>
                        <input type="text" value="{{old('name')}}" name="name" class="form-control @error('name') is-invalid @enderror bg-white">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="description">Product Description:</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror bg-white">
                        {{old('description')}}
                        </textarea>
                        @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </div>
                    <br>

                    <div class="form-group">
                        <label for="role">Price:</label>
                        <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror bg-white" value="{{old('price')}}"/>
                        @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </div>
                    <br>

                    <div class="form-group">
                        <label for="role">Price:</label>
                        <input type="file" name="picture" id="picture" class="form-control @error('picture') is-invalid @enderror bg-white" value="{{old('picture')}}"/>
                        @error('picture')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </div>
                    <br>
                    {{-- <input type="file" name="picture" id="picture" class="form-control"/> --}}

                    <input type="hidden" name="seller_id" value="{{Sentinel::getUser()->id}}"/>
                   
                    <br>
                    <div class="form-group">
                       <button class="btn btn-dark">Submit</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
