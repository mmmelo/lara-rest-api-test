//Tag In

//Open the collapse if you add more tags

$('#tag-addOn-v1').click(function (e) {
    $('.typeahead__result').children().remove();
    let $input = $('#tag_input').val();
    //Here Im making an API call to confirm this tag is not duplicate
    already_on_db($input,function(val){
        val.done(function(res){
            if(res.length === 0 )
            {
                $('.tag_add_warning').collapse('show');
            }
        })
    });
});

$('#tag_input').keypress(function (e) {
    $('#validate-warning').hide();
    if (($(this).val().length) > 1) {
        $('.tag_validate').removeClass('has-error').addClass('has-success');
    } else {
        $('.tag_validate').removeClass('has-success').addClass('has-error');
    }
});
//Clear and collapse the New tag form
$('#cancel_new_tag').click(function(e)
{

    $('.new_tag_optional').collapse('hide');
});

//Cancel the warning to add new tag
$('#cancel_a_confirmation_new_tag').click(function(e)
{
    $('.tag_add_warning').collapse('hide');
});
//Confirmation button to add a new Tag (Warning Panel)
$('#confirm_a_new_tag').click(function(e)
{
    $('.tag_add_warning').collapse('hide');
    $('.new_tag_optional').collapse('show');
    let $tag_input = $('#tag_input').val();

    $('#new-tag-container-display').empty().append($tag_input);
});

//Submit the new tag to DB
$('#submit_new_tag').click(function (e) {
    let CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    let EVENT = $('meta[name="__event"]').attr('content');
    let $new_tag_input = $('#tag_input').val();
    let $new_tag_option = $("input[name='tagRadioOptions']:checked").val();

    //Saving the New tags
    if ($new_tag_input.length > 1) {
        $.ajax({
            method: 'POST',
            url: '../../tag_post',
            data: {
                _token: CSRF_TOKEN,
                event: EVENT,
                tag: $new_tag_input,
                option: $new_tag_option
            },
            beforeSend: function()
            {
                $('.spinner').show();
            },
            complete:function()
            {
                $('.spinner').hide();
            },
            success: function (Res) {
                // console.log(Res);
                if(Res.status === 'success')
                {
                    $('.intelligent_tag').append(
                        '<span id="' + Res.id + '" ' +
                        'class="label label-info int-state" ' +
                        'draggable="true" ' +
                        'ondragstart="drag(event)" ' +
                        'data-content="' + Res.id + '">' +
                        '<span class="new_tag_created">New!  </span>'+
                        Res.description + '</span>'
                    );
                    $('.new_tag_optional').collapse('hide');
                    $('#tag_input').val("");

                }else
                {
                    $('#error-tag-exist').show();
                    setTimeout(function () {
                        $('#error-tag-exist').hide()
                    },5000);
                }

            }
        });
    } else {
        $('.tag_validate').addClass('has-error');
        $('#validate-warning').toggle('show');
    }
    // console.log($new_tag_option);

});

//Autosuggestion
$(document).bind('DOMSubtreeModified',function () {});
$.typeahead({
    input: '.js-typeahead-country_v1',
    order: "desc",
    minLength: 2,
    dynamic:true,
    delay:200,
    display:"description",
    cancelButton:false,

    source: {
        tag: {
            ajax: function (query) {
                return {
                    type: "GET",
                    url: "/tag_list",
                    path: "data",
                    data: {
                        q: "{{query}}"
                    },
                    callback: {
                        done: function (data) {
                            let $show = data.status;
                            if (!$show) {
                                $('#add_more_Tags').removeClass().addClass('glyphicon glyphicon-plus text-danger');
                                $('.add_tags').removeClass('hide');
                            } else {
                                $('#add_more_Tags').removeClass().addClass('typeahead__search-icon');
                                $('.add_tags').addClass('hide');
                            }
                            // console.log(data);
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
            $('.intelligent_tag').append(
                '<span id="' + item.id + '" ' +
                'class="label label-info int-state" ' +
                'draggable="true" ' +
                'ondragstart="drag(event)" ' +
                'data-content="' + item.id + '">' +
                '<span class="new_tag_created">New!  </span>'+
                item.description + '</span>'
            );
            $('#tag_input').val("");
        },
        onCancel: function(){
            $('.add_tags').addClass('hide');
            $('.new_tag_optional').collapse('hide');
        }
        ,
        onPopulateSource: function (node,data)
        {
            $true_match = false;
            $('.tag_add_warning').collapse('hide');


            for(let i = 0; i< data.length; i ++)
            {
                if(data[i].description === this.query)
                {
                    $true_match = true;
                }
            }

            if (!$true_match) {
                setTimeout(function () {
                    $('#add_more_Tags').removeClass().addClass('glyphicon glyphicon-plus text-danger');
                    $('.add_tags').removeClass('hide');

                },5000)
            }
            return data;
        }
    },
    debug:true
});
// ____Private___//

//This function work with a callback due have a async waiting for the response of the API.
function already_on_db($word,callback)
{
     $return = $.ajax({
        method: 'GET',
        url: '../../tag_find_match',
        data: {
            tag: $word
        }
    });
     callback($return);
}
