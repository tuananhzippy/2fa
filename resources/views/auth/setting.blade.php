@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Two Factor Authentication</strong></div>
                       <div class="panel-body">
                           <p>Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.</p>
                           <br/>
                           <p>To Enable Two Factor Authentication on your Account, you need to choose common type ?</p>
                           <br/>
                           <div class="text-center">
                                <form class="form-inline" action="{{ route('change2fa') }}" method="POST">
                                    {{ csrf_field() }}
                                    @if(isset($google2faSecret))
                                    <input type="hidden" value="{{$google2faSecret}}" name="google-2fa-secret" />
                                    @endif
                                    <select name="2fa" class="form-control" id="2fa" required>
                                        @if(empty($user->tfa))
                                        <option value="" selected>---  No choose  ---</option>
                                        @endif
                                        @foreach($types as $key => $value)
                                            @if($key == $user->tfa)
                                            <option value="{{$key}}" selected>{{$value}}</option>
                                            @else
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </form>
                            </div>
                            <br/>
                            <br/>
                            @if(isset($google2faSecret) && isset($QRImage))
                            <div class="row" id="software-token" style="display:none">
                                <div class="col-md-12 text-center">
                                    <p>Set up your two factor authentication by scanning the barcode below. Alternatively, you can use the code <strong>{{ $google2faSecret }}</strong></p>
                                    <div>
                                        <img src="{{ $QRImage }}" alt="qr-code">
                                    </div>
                                    <p>You must set up your <strong>Google Authenticator</strong> app before continuing. You will be unable to login otherwise</p>
                                </div>
                            </div>
                            @endif
                            @if(isset($status) && $status)
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <strong>Successfully</strong>. 2FA is Enable for your account...
                            </div>
                            @elseif(isset($status) && !$status)
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <strong>Error</strong>. Please check again...
                                </div>
                            @endif
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        window.onload = function () {
            if(document.getElementById("2fa").value != "software")
                document.getElementById("software-token").style.display = "none";
            else
                document.getElementById("software-token").style.display = "block";

            document.getElementById("2fa").addEventListener('change', function () {
                if(document.getElementById("2fa").value != "software")
                    document.getElementById("software-token").style.display = "none";
                else
                    document.getElementById("software-token").style.display = "block";
            });
        }
    </script>
@endsection