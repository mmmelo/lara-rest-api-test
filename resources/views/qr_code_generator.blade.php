@extends ('layouts.app')
<div class="content">
    <div>
        <label>Event: </label>
        {{$event}}
    </div>
    <div>
        <label>Type: </label>
        {{$type}}
    </div>
    <div>
        <label>School: </label>
        {{$school}}
    </div>
    <div>{{$data}}</div>
    <img src="data:image/svg+xml;base64, {!! base64_encode(QrCode::format('svg')->size(300)->generate($data)) !!}">
</div>
