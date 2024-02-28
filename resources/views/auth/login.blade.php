@extends('auth.layouts')

@section('content')

<div class="row justify-content-center mt-5">
    <div class="col-md-8">

        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">

                <form action="{{ route('authenticate') }}" method="post" id="loginform">
                    @csrf
                    @if ($errors->has('message'))
                    <span class="text-danger"> {{ $errors->first('message') }}</span>
                    <script>
                        alert("You want to logout from previous sessions?");
                    </script>
                    @endif
                    <input type="hidden" value="" name="confirmed" id="confirmed">
                    <div class="mb-3 row">
                        <label for="email" class="col-md-4 col-form-label text-md-end text-start">Email Address</label>
                        <div class="col-md-6">
                          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="password" class="col-md-4 col-form-label text-md-end text-start">Password</label>
                        <div class="col-md-6">
                          <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            @if ($errors->has('password'))
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <input type="submit" class="col-md-3 offset-md-5 btn btn-primary" value="Login">
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<script>

    var msg = '{{Session::get('alert')}}';
    var exist = '{{Session::has('alert')}}';
    if(exist){
        var result = confirm("Are you sure you want to logout from other devices?");
if (result) {
    //Logic goes here
    document.getElementById("confirmed").value = '1';
}
    }
  </script>
@endsection
