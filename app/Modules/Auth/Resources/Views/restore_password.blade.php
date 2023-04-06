<h1 class="page-title">{{ trans('auth::password_reset') }}</h1>

{!! Form::errors($errors) !!}

@section('auth-restore-pw-form')
{!! Form::open(['url' => 'auth/restore']) !!}
    {!! Form::smartEmail() !!}
    
    {!! Form::smartCaptcha() !!}

    {!! Form::actions(['submit' => trans('app.send')], false) !!}
{!! Form::close() !!}
@show
