<div class="panel panel-default  collapse optional-panel">
    <div class="panel-heading">
        <h3 class="panel-title">Schools
            <a data-toggle="collapse"
               data-target="#school_collapsible"
               aria-expanded="true"
               aria-controls="school_collapsible">
                <span class="more-less glyphicon glyphicon-minus push-right"></span>
            </a>
        </h3>
    </div>
    <div class="panel-body text-center collapse in" id="school_collapsible" >
        <div class="row">
            <div class="col-xs-6">
                <img class="school_tag_logos"
                     src="{{'https://media.mygspn.com/players_uploads/'.$data->group_id.'/'.$data['home']['media_logo']->thumb_file}}"
                     alt="Home_logo"
                     {{--I'm data-content the oposit school id because i would REMOVE the id from the pivot table--}}
                     {{--The logic said i would remove the picture would look me look bad --}}
                     data-content="{{$data->event_opponent_id}}"
                     draggable="true"
                     ondragstart="drag_school(event)">
            </div>
            <div class="col-xs-6">
                <img class="school_tag_logos"
                     src="{{'https://media.mygspn.com/players_uploads/'.$data->event_opponent_id.'/'.$data['opponent']['media_logo']->thumb_file}}"
                     alt="opponent_logo"
                     {{--I'm data-content the oposit school id because i would REMOVE the id from the pivot table--}}
                     {{--The logic said i would remove the picture would look me look bad --}}
                     data-content="{{$data->group_id}}"
                     draggable="true"
                     ondragstart="drag_school(event)">
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-6">

                {{$school_regex['home_regex']}}
            </div>
            <div class="col-xs-6">
                {{$school_regex['opponent_regex']}}
            </div>
        </div>
    </div>
</div>