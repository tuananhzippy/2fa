@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Verify Code - {{env('APP_NAME')}}</div>
                    <div class="panel-body">

                        <form role="form" method="POST" action="{{ route('handleTokenVerify') }}">
                            {{ csrf_field() }}
                            <br />
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <p>You must set up your <strong>Google Authenticator</strong> app before continuing. You will be unable to login otherwise</p>
                                    <div>
                                        <img src="{{ $QRImage }}" alt="qr-code">
                                    </div>
                                </div>
                                <div class="col-md-3 text-right">
                                    <label for="token-code" class="control-label">Six digits code</label>
                                </div>

                                <div class="col-md-6">
                                    <input id="token-code" type="number" class="form-control" name="token-code" required autofocus>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </div>
                            <br />
                            @if (count($errors) > 0)
                                <div class="alert alert-danger ">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection