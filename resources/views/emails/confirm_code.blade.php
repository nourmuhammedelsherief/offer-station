<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2 class="text-center"> {{trans('messages.welcome')}} </h2>
<div>
    <h4 class="text-center">{{trans('messages.confirmation_code')}}</h4><br>
    <h3 class="text-center" style="background-color: #0c91e5; color: #4f0a0f; width: 100px;"> {{trans('messages.code')}} : {{ $code }}</h3><br>
    <h4 class="text-center"> {{trans('messages.thanksUsingApp')}}  </h4>
</div>
</body>
</html>
