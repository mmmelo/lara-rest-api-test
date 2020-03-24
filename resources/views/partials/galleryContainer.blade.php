<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Pictures</h3>
    </div>
    <div class="panel-body">
        @if($data->media)
            @php($total_media = $data->media->count())
            @php($i = 0)
            @foreach($data->media->chunk(3) as $chunk)
                <div class="row">
                    @foreach($chunk as $picture)
                        @php(++$i)
                        @include('partials.galleryThumbnailContainer')
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
</div>
@include('partials.modal')