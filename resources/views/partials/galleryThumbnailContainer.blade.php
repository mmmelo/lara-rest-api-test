<div class="col-md-4">
    <div class="thumbnail">
        <img id="thumb_{{$i}}"
             class="image-thumbnail"
             src="{{$picture->media}}"
             alt="{{$picture->name}}"
             data-content="{{$picture->id}}"
             style="width: 100%">
        <div class="caption">
            <div class="super_caption">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div class="col-xs-6 text-left">
                            <small>Id : {{$picture->id}}</small>
                        </div>
                        <div class="col-xs-6 text-right">
                            <small>{{$i}} / {{$total_media}}</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 rating-container collapse">
                        <fieldset id="rating_group_{{$i}}" class="rating">
                            <input type="radio" id="star4_{{$i}}" name="rating_group_{{$i}}" value="4"/>
                            <label class="full" for="star4_{{$i}}" title="Pretty good - 4 stars"></label>
                            <input type="radio" id="star3_{{$i}}" name="rating_group_{{$i}}" value="3"/>
                            <label class="full" for="star3_{{$i}}" title="Meh - 3 stars"></label>
                            <input type="radio" id="star2_{{$i}}" name="rating_group_{{$i}}" value="2"
                                   checked="checked"/>
                            <label class="full" for="star2_{{$i}}" title="Kinda bad - 2 stars"></label>
                            <input type="radio" id="star1_{{$i}}" name="rating_group_{{$i}}" value="1"/>
                            <label class="full" for="star1_{{$i}}" title="Sucks big time - 1 star"></label>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3">
                        <span id="dolar-sell" class="badge"><span class="glyphicon glyphicon-usd"
                                                                  aria-hidden="true"></span></span>
                    </div>
                    <div id="thumbs-container" class="col-xs-9 text-right">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 both-schools collapse">
                        <img class="both-schools_text" src="https://storage.googleapis.com/static.mygspn.com/assets/icons/bothteams.svg" alt="both_container">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 tag-content">
                </div>
            </div>
        </div>
    </div>
</div>