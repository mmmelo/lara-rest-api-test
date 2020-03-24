<div class="panel panel-default collapse optional-panel">
    <div class="panel-heading">
        <h3 class="panel-title ">Roster
            <a data-toggle="collapse"
               data-target="#roster_collapsible_tag"
               aria-expanded="true"
               aria-controls="intelligent_tag">
                <i class="more-less glyphicon glyphicon-minus push-right"></i>
            </a>
        </h3>
    </div>
    <div  class="panel-body collapse in" id="roster_collapsible_tag">
        <div class="row">
            <div class="col-md-6">
                <small>{{$data->home['full_name']}}</small>
            </div>
            <div class="col-md-6">
               <small>{{{$data->opponent['full_name']}}}</small>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-xs-6 w-margin-rigth">
                @foreach($team_a as $hometeam)
                    <div class="roster-label-container">
                        <span class="label label-info roster-label">{{$hometeam}}</span>
                    </div>
                @endforeach
            </div>

            <div class="col-xs-6">
                <div class="col-xs-6">
                    @foreach($team_b as $visitorteam)
                        <div class="roster-label-container">
                            <span class="label label-info roster-label">{{$visitorteam}}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>