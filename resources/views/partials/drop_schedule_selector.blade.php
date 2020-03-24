@section('special_scripts')

@endsection
<div class="schedule-container">
    <span class="collapse " id="error-message-schedule"><small class="text-danger">* An event must be selected</small></span>
    <div class="form-group" id="state-container">
        <label for="">Please select the State</label>
        <div class="form-group">
            <select class="form-control" id="states" name="states">
                <option>select a state</option>
                @foreach($states as $state)
                    <option data-content="{{$state->state_code}}">{{$state->state_name}}</option>
                @endforeach

            </select>
        </div>
    </div>
    <div class="form-group collapse sport-collapse" id="sport-container">
        <label for="">Please select the type of Sport</label>
        <div class="form-group">
            <select class="form-control" id="sport" name="sport">
                <option>select a sport</option>
                @foreach($sports as $sport)
                    <option data-content="{{$sport->user_type_name}}">{{$sport->user_type_name}}</option>
                @endforeach
                <option>
            </select>
        </div>
    </div>
    <div class="typeahead__container">
        <div class="form-group   collapse schedule-collapse" id="search_school">
            <label>Look for a School</label>
            <small>(*optional)</small>
            <input type="search" name="schools" id="school_v1[query]" class="form-control school_sug" autocomplete="off"
                   placeholder="School Name">
        </div>
    </div>

    <div class="form-group   collapse schedule-collapse" id="schedule-container">
        <label for="">Please select the Event </label>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label"></label>
            <select class="form-control" id="school" name="school">

            </select>
        </div>
    </div>
</div>
@include('partials.drop_score')