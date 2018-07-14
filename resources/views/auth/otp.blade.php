@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Verify Code - {{env('APP_NAME')}}</div>
                    <div class="panel-body">
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

                        <form role="form" method="POST" action="{{ route('handleOtpVerify') }}">
                            {{ csrf_field() }}
                            <br />
                            <div class="row">
                                <div class="col-md-3 text-right">
                                    <label for="code" class="control-label">Six digits code</label>
                                </div>

                                <div class="col-md-6">
                                    <input id="code" type="text" class="form-control" name="code" value="{{ old('code') }}" required autofocus>

                                    @if ($errors->has('code'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('code') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </div>
                            <br />
                            <br />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection