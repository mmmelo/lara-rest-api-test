let $sport_collapse = $('.sport-collapse');
$("#state-container").change(function () {
    $sport_collapse.collapse('show');
    if($sport_collapse.hasClass('in'))
    {
        request_schedule();
    }
});

$("#sport-container").change(function () {
    $('.schedule-collapse').collapse('show');
    request_schedule();
});

$('.school_sug').typeahead({
    // input: '.school_sug',
    debug: false,
    minLength: 3,
    order: "desc",
    dynamic: true,
    delay: 500,
    backdrop:{
        "background-color": '#fff'
    },
    template: function (query,item)
    {
        let gender = '<i class="fas fa-venus"></i>';
        let gender_v2 = '<span class="girls"> {{group_gender}} </span>';
        if(item.group_gender === "Boys")
        {
            gender = '<i class="fas fa-mars"></i>';
            gender_v2 = '<span class="boys"> {{group_gender}} </span>';
        }
        return '{{full_name}} | {{group_level}} | ' + gender_v2 + gender + '  {{group}} </small>'
    },
    emptyTemplate: "no result for {{query}}",
    source: {
        school: {
            display: "full_name",
            ajax: function (query) {
                let state = $('#states').find(":selected").data('content');
                let sport = $('#sport').find(":selected").text();
                return {
                    type: "GET",
                    url: "/team/search",
                    path:"data",
                    data: {
                        q: "{{query}}",
                        state: state,
                        sport: sport
                    }
                    ,
                    callback: {
                        done: function(data){
                            return data;
                        }
                    }
                }
            }
        }
    },
    callback: {
        onClick: function (node, a, item, event) {
            // You can do a simple window.location of the item.href
            request_schedule(true,item.id);
            console.log(item.id);
        }
    },

});

$('#school').change(function () {
    let school = $('#school').find(":selected").data('content');
    has_scores(school);
});

//---------private------//
function request_schedule(shrink = 0 , group_id = null){
    let sport = $('#sport').find(":selected").text();
    let state = $('#states').find(":selected").data('content');
    let $schedule = $('select#school');
    $schedule.children()
        .remove()
        .end()
        .append('<option value="">Loading...</option>');

    $.ajax({
        method: 'GET',
        url: 'schedule_filter',
        data:{
            'state': state,
            'sport': sport,
            'shrink': shrink,
            'group_id':group_id
        },
        success: function(response){
            $schedule.empty();
            if(response.length !==0){
                $.each(response, function (index, value) {
                    $schedule.append('<option data-content="' + value.id + '">' + value.event_date
                        + ' | ' + value.home_name
                        + '<strong> Vs </strong>' + value.event_opponent_name + '</option>')
                        .data('content', value.id);

                })
            }else
            {
                $schedule.append('<option>No schedule available</option>');
            }

        }
    });
}

function has_scores($id)
{
    $.ajax({
        method: 'GET',
        url:'schedule/find_v2',
        data:{'id':$id},
        success: function (response) {
            //Check if the Schedule is without score
            if(response.group_result == null && response.group_result == null)
            {
                $('.score-collapse').collapse('show');
                $('#score_home_id').empty().append(response.home.full_name);
                $('#score_opponent_id').empty().append(response.opponent.full_name);
            }else{
                $('.score-collapse').collapse('hide');
            }
        }
    })
}

