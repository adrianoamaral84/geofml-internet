@extends('layouts.auth')

@section('content')


<style type="text/css">
    .field-icon {
  float: right;
  margin-left: -25px;
  margin-top: -25px;
  position: relative;
  z-index: 2;
}
</style>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<p class="text-center">Acesso ao Sistema</p>
<form id="login-form" action="{{ route('login') }}" method="POST">
    @csrf

    
   
    
    <div class="form-group">
          <label class="control-label">{{ __('CPF') }}</label>
       
        <input type="text" class="form-control underlined cpf @error('cpf') is-invalid @enderror" value="{{ old('cpf') }}" name="cpf" id="cpf" required maxlength="11" 
        data-mask="000.000.000-00" autocomplete="off" placeholder="000.000.000-00" onpaste="return false;">

        @error('cpf')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group">
        <label for="password">{{ __('Senha') }}</label>
        <input type="password" class="form-control underlined @error('password') is-invalid @enderror" name="password" id="password" placeholder="Sua senha" required maxlength="15"><i toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password" style="margin-left: -30px; cursor: pointer;"></i>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <center>

      <div class="g-recaptcha" style="width: 100%;" data-sitekey="6LfaJBomAAAAABrNSJccGrO45tXloRewyySX48S_"></div>
    
    </center>

    <div class="">
        <a href="{{ route('password.request') }}" class="forgot-btn pull-left"><small>{{ __('Esqueci minha senha') }}</small></a>
    </div>
    
    <div class="">
        <a href="{{ route('solicitaacesso') }}" class="forgot-btn pull-right"><small>{{ __('Não sou cadastrado') }}</small></a>
    </div>

    <div class="form-group">
        <button type="submit" 
         class="btn btn-block btn-primary">{{ __('Login') }}</button>
    </div>

</form>

@push('javascript')


</script>
    <script src="{{asset('lib/jquery-mask-plugin/dist/jquery.mask.min.js')}}"></script>
    <script src="{{ asset('js/jquery.min.js') }}" ></script>
    
    </script>
    <script type="text/javascript">
  var onloadCallback = function() {
    alert("grecaptcha is ready!");
  };
</script>

    <script>
        $('#reload').click(function (e) {

            e.preventDefault();

            $.ajax({
                type: 'GET',
                url: 'reload',
                success:function (res){
                    $('#captcha-img').html(res.captcha);
                },


            });
        }); 


    </script>
    
    <script type="text/javascript">
    $(".toggle-password").click(function() {

  $(this).toggleClass("fa-eye fa-eye-slash");
  var input = $($(this).attr("toggle"));
  
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
  



});
</script>
<script>
   function onSubmit(token) {
     document.getElementById("demo-form").submit();
   }
 </script>


@endpush

@endsection