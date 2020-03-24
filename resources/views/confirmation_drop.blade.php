@extends ('layouts.app')

@section('special_header')
    <meta name="csrf-token" content="{{csrf_token()}}"/>
    <meta name="__event" content="{{$data->id}}"/>
@endsection
@section('content')

    <div class="container">
        {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
                {{--@include('partials.confirmationSummary')--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="row">
            <div class="col-md-8">
                @include('partials.confirmationSummary')
                @include('partials.galleryContainer')
            </div>
             <div class="col-md-4">
                 <div class="right-container" data-spy="affix" data-offset-top="10" id="#myAffix">
                     @include('partials.galleryGeneralOptions')
                     @include('partials.galleryTag')
                     @include('partials.gallery_school_tag')
                     @include('partials.galleryIntelligentTag')
                     @include('partials.roster')
                     @include('partials.galleryFinalConfirmation')
                 </div>
             </div>
        </div>
    </div>


@endsection