<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Summary</h3>
    </div>
    <div class="panel-body">
        <p><strong>Event or Game id: </strong>#{{$data->id}}</p>
        <p><strong>Schools: </strong> {{$data->home['full_name']}} <strong>VS </strong> {{{$data->opponent['full_name']}}}</p>
        <p><strong>Date and Time: </strong>{{gmdate('m-d-Y h:i a',$data->event_date)}}</p>
        <p><strong>Photographer: </strong> Name to be determine or probably would be the user</p>
    </div>
</div>