<div class="panel panel-default collapse optional-panel">
    <div class="panel-heading">
        <h3 class="panel-title">Intelligent Tags
            <a data-toggle="collapse"
               data-target="#intelligent_tag"
               aria-expanded="true"
               aria-controls="intelligent_tag">
                <span class="more-less glyphicon glyphicon-minus push-right"></span>
            </a>
        </h3>
    </div>
    <div id="intelligent_tag" class="collapse in">
    <div class="panel-body">
        {{--Search for tags in the Database--}}
        <div class="typeahead__container">
            <div class="typeahead__field">
                <div class="typeahead__query input-group">
                    <input class="js-typeahead-country_v1 form-control"
                           id="tag_input"
                           name="country_v1[query]"
                           type="search"
                           placeholder="Search"
                           autocomplete="off"
                           aria-describedby="tag-addOn-v1">
                    <span class="input-group-addon add_tags hide" id="tag-addOn-v1" >
                <i id="add_more_Tags" class="glyphicon glyphicon-search " aria-hidden="true"></i>
                    </span>
                </div>
            </div>
        </div>
        {{--End of search tags--}}
    </div>
    {{--Show the warning before the tag adding--}}
    <div class="panel panel-default collapse tag_add_warning ">
        <div class="panel-body bg-warning">
            <h4>Are you sure want to add a new Tag?</h4>
            <hr>
            <div class="text-center">
                <button type="button" class="btn btn-primary" id="confirm_a_new_tag" >Yes</button>
                <button type="button" class="btn btn-danger" id="cancel_a_confirmation_new_tag">Cancel</button>
            </div>

        </div>
    </div>
    {{--End of tag confirmation--}}
    {{--Collapsable to Show Panel to add more Tags--}}
    <div class="spinner" hidden>
        <div class="rect1"></div>
        <div class="rect2"></div>
        <div class="rect3"></div>
        <div class="rect4"></div>
        <div class="rect5"></div>
    </div>
    <div class="panel-body collapse new_tag_optional">
        <h4>Add <strong>New</strong> Tag</h4>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>Did you want to add <span class="text-underline text-uppercase" id="new-tag-container-display"></span> to all the Sports?</label>
                    <div class="form-group">
                        <label class="radio-inline new_tag_options">
                            <input type="radio" name="tagRadioOptions" id="inlineRadio1" value="no" checked> No
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="tagRadioOptions" id="inlineRadio2" value="yes"> Yes
                        </label>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" id="submit_new_tag" >Submit</button>
                    <button type="button" class="btn btn-danger" id="cancel_new_tag">Cancel</button>
                </div>
            </div>        </div>
    </div>


    <div class="panel-body">

        <div class="intelligent_tag">
            <h4>Available Tags</h4>
            @foreach($super_tags as $super_tag)
                <span id="{{$loop->iteration}}"
                      class="label label-info int-state "
                      draggable="true"
                      ondragstart="drag(event)"
                      data-content="{{$super_tag->id}}">{{ucwords($super_tag->description)}}</span>
            @endforeach
        </div>
    </div>
    </div>
</div>