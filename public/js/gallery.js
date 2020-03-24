$(document).ready(function(){
    //CONFIRMATION PAGE CODE
    $('.image-thumbnail').on("click",function(e){
        var picture_id = $(this).attr('data-content');
        $.ajax({
            method: 'GET',
            url: '/media/',
            data: {
                'id': picture_id
            },
            success: function(response)
            {
                $('#gallery_modal').modal('show');
                $('.modal-content').html(` <img
                    class="image_full_view"
                    src="`+ response.media + `">`);
                $('.right-container').removeClass('affix');
            }
        });
    });
    $('#gallery_modal').on('hidden.bs.modal',function()
    {
        $('.right-container').addClass('affix');
    });

    let $thumbnail = $('.thumbnail');
    let data = [];
    //Multiple selection buttons
    $('#select-all').on("click",function () {
        $thumbnail.addClass('green');
    });

    $('#unselect-all').on("click",function () {
        $thumbnail.removeClass('green');
    });


    //Global options and conditional buttons Approve, Reject or clear
    $("#approve-all").on("click",function (e) {
        $('.row .thumbnail').each(function()
        {
            if( $(this).hasClass('green'))
            {
                $(this).removeClass('rejected').addClass('approved');
                $(this).find('#thumbs-container').html(`<span id="approved" class="glyphicon glyphicon-thumbs-up approved-symbol" aria-hidden="true"></span>`);

                //Showing the Tags panels
                $('.optional-panel').collapse('show');
                $(this).find('.rating-container').collapse('show');
                $(this).find('.both-schools').collapse('show');

                //Adding the feature to drop the Tag inside
                $(this).attr({ondragover:'allowDrop(event)',ondrop:'drop(event)'});
                // $(this).removeClass('green');
                reject_the_rest();
            }
        })
        picture_selected();
    });

    $("#reject-all").on("click",function(e){
        $('.row .thumbnail').each(function()
        {
            if( $(this).hasClass('green'))
            {
                $(this).removeClass('approved').addClass('rejected').removeAttr('ondrop ondragover');
                $(this).find('#thumbs-container').empty();
                $(this).find('.tag-content').empty();
                $(this).find('.rating-container').collapse('hide');
                $(this).find('.both-schools').collapse('hide');
                $(this).removeClass('green');
            }

        })
        picture_selected();
    });
    $("#clear-all").on("click",function(e){
        $thumbnail.removeClass("approved rejected green").find('#thumbs-container , .tag-content').empty();
        $thumbnail.removeAttr('ondrop ondragover');
        $thumbnail.find('.both-schools').children().remove();
        $thumbnail.find('.both-schools').append(
            '<img class="both-schools_text" ' +
            'src="https://storage.googleapis.com/static.mygspn.com/assets/icons/bothteams.svg" ' +
            'alt="both_container">'
        );
        $('.optional-panel').collapse('hide');
        $('.rating-container').collapse('hide');
        $('.both-schools').collapse('hide');


    });

    //Individual thumbnail selection
    $('.super_caption').on("click",function () {
        if ($(this).parent().parent().hasClass('green'))
        {
            $(this).parent().parent().removeClass('green')
        }else{
            $(this).parent().parent().addClass("green");
        }
    });


    //Collection all data to send into the database

    $('#commit').on("click",function()
    {
        let CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        let response = [];
        $('.thumbnail').each(function(){
            if($(this).hasClass('approved'))
            {
                let tags = [];
                //pull the photo_id
                let photo_id = $(this).children().data('content');
                //pull the radio button data
                let rate = $(this).find("input[type='radio']:checked").val();
                //now have to pull the highlight picture per team
                let highlight = $(this).find('.tag_small_logo').data('content');
                //pulling the tags
                $(this).find(".dropped").each(function(){
                    tags.push($(this).data('content'));
                });
                response.push(new Img_approved(photo_id,rate,tags,highlight));

            }
        });

        console.log(JSON.stringify(response));
        $.ajax({
            method: 'POST',
            url:'/update/',
            data: {
                _token: CSRF_TOKEN,
                data: JSON.stringify(response)},

            error: function (xhr,status,error) {
                console.log(xhr,status);
                console.log(status);
                console.log(error);
            }
        })
    })

});

$(document).bind('DOMSubtreeModified',function () {
    $('.remove-tag').on("click",function(){
        $(this).parent().remove();
    });
    $('.tag_small_logo').click(function()
    {
        $(this).parent().append(
            '<img class="both-schools_text" ' +
            'src="https://storage.googleapis.com/static.mygspn.com/assets/icons/bothteams.svg" ' +
            'alt="both_container">'
        );
        $(this).remove();

    });
});

function Img_approved(id,rate,tags,highligth = null){
    this.id = id;
    this.rate = rate;
    this.tags = tags;
    this.sell = true;
    this.highlight = highligth
}


function allowDrop(ev){
    ev.preventDefault();

}

function drag(ev){
    ev.dataTransfer.setData("text",$(ev.target).text());
    ev.dataTransfer.setData("data",$(ev.target).data('content'));
}

function drag_school(ev){
    ev.dataTransfer.setData("class",$(ev.target).attr('class'));
    ev.dataTransfer.setData('url',$(ev.target).attr('src'));
    ev.dataTransfer.setData("data",$(ev.target).data('content'));
}


function drop(ev){
    let tag = $('.tag-content');
    let currentPicture = $(ev.currentTarget);
    ev.preventDefault();
    let data = ev.dataTransfer.getData("text");
    let data_content = ev.dataTransfer.getData("data");
    let shool_class = ev.dataTransfer.getData('class');
    let source = ev.dataTransfer.getData('url');
    let school_id = ev.dataTransfer.getData('data');
    if(shool_class ==='school_tag_logos'){
        console.log('is a school');
        let NewDiv = '<img src="' + source + '" alt="school_logo" class="tag_small_logo" data-content="' + school_id +'">';

        if(currentPicture.hasClass('green')){
            $('.thumbnail').each(function () {
                if($(this).hasClass('green')){
                    $(this).find('.both-schools').children().remove();
                    $(this).find('.both-schools').append(NewDiv);
                }
            });
        }else{
            currentPicture.find('.both-schools').children().remove();
            currentPicture.find('.both-schools').append(NewDiv);
        }
    }else
    {
        let NewDiv = '<span class="label label-info dropped" data-content="'+ data_content +'">' + data + '<a class="remove-tag">' +
            '<i class="remove glyphicon glyphicon-remove-sign glyphicon-white label-delete"></i></a></span>';
        if(currentPicture.hasClass('green')){
            $('.thumbnail').each(function () {
                if($(this).hasClass('green')){
                    $(this).find(tag).append(NewDiv);
                }
            });
        }else{
            currentPicture.find(tag).append(NewDiv);
        }
    }
}

function picture_selected(){
    let approved = 0;
    $('.thumbnail').each(function(){
        if($(this).hasClass('approved')){
            approved ++;
        }
    });
    $('#total-approved').text(approved);
}

function reject_the_rest(){
    $('.thumbnail').each(function(){
        if(!$(this).hasClass('approved')){
            $(this).addClass('rejected');
        }
    })
}

function toggleIcon(e)
{
    $(e.target)
        .prev('.panel-heading')
        .find('.more-less')
        .toggleClass('glyphicon-minus glyphicon-plus')
}

$('.panel-body').on('hidden.bs.collapse',toggleIcon);
$('.panel-body').on('show.bs.collapse',toggleIcon);