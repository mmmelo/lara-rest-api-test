@section('special_scripts')
    <script src="{{ url('js/schedule.js') }}"></script>
@endsection

@section('special_header')
<meta name="csrf-token" content="{{csrf_token()}}"/>
@endsection

<div>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="{{route('dropzone')}}" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" id="csrf_token" value ="{{csrf_token()}}"/>
                    <legend>File Uploader</legend>


                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Configuration</h3>
                        </div>
                        <div class="panel-body">
                            <select class="form-control" id="disk-selector">
                                <option value="google">Google Cloud</option>
                                <option value="dropbox">Dropbox</option>
                            </select>
                            @if(false)
                                <br>
                                <label> Watermark Options: </label>
                                <br>
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="yes" checked="checked"> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="no"> No
                                </label>
                            @endif
                        </div>

                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Instructions</h3>
                        </div>
                        <div class="panel-body">
                            <strong>Number of pictures allowed: </strong> 30<br>
                            <small>Please avoid loading blurry pictures</small> <br/>

                        </div>
                    </div>

                    {{--Selection of the Game--}}
                    @include('partials.drop_schedule_selector')
                    {{--DROP ZONE SECTION--}}

                    <div class="dropzone" id="myDropzone"></div>
                    <hr>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Pictures Bulk information</h3>
                        </div>
                        <div class="panel-body">
                            <strong>Total Amount of Pictures to be uploaded: </strong> <span id="counter"></span><br/>
                            <strong>Total size of the Bulk: </strong><span id="bulk_size"></span><br>

                            {{--Progress Bar--}}

                            <div class="progress hide">
                                <div id="myprogressbar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                    <span class="sr-only">45% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary disabled " id="submit-all">

                        Submit</button>


                </form>
            </div>
        </div>
    </div>

</div>

