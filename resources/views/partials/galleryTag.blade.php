@section('special_scripts')
    <script src="{{ url('js/tag_creator.js') }}"></script>
@endsection


<div class="panel panel-default collapse optional-panel">
    <div class="panel-heading">
                <h3 class="panel-title">Permanent Tags
                    <a data-toggle="collapse" href="#permanent_collapsible">
                        <span class="more-less glyphicon glyphicon-minus push-right"></span>
                    </a>
                </h3>
    </div>
    <div class="panel-body collapse in" id="permanent_collapsible">
        <div class="div">
            <p><small>This tags are already predicted by the system and attached to the pictures</small></p>
            @foreach($hard_tags as $hard_tag)
                <span class="label label-default label-disabled">{{$hard_tag}}</span>
            @endforeach
        </div>
    </div>
</div>





