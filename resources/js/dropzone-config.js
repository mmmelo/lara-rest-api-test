var token =  $('#csrf_token').attr('value');
var total_photo_counter = 0;
var bulksize = 0;

//This function would convert the size to the conventional name size

function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Byte';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
};

Dropzone.options.myDropzone= {
    url:"./upload/dropzone",
    maxFiles:30,
    timeout:180000,
    parallelUploads:1,
    acceptedFiles: '.jpg',
    addRemoveLinks: true,
    autoProcessQueue:false,
    chunking:true,
    forceChunking:true,
    chunkSize:256000,
    parallelChunkUploads:false,
    retryChunks:true,
    retryChunksLimit:3,

    init: function ()
    {
        let button = $('#submit-all');
        let total_photo_counter = 0;
        let totalPercentage = 0;
        myDropzone = this;
        let success = true;
        button.on("click",function(e)
        {
            e.preventDefault();
            e.stopPropagation();
            let $school = $('#school').find(":selected").data('content');
            if($school){
                //Process the Score and save in DB
                save_score_board();
                myDropzone.processQueue();
            }else{
                $('.schedule-container').addClass('error');
                $('#states').focus();
                $('#error-message-schedule').collapse('show');
            }

        });

        this.on("addedfile",function(file){
            //Adding the number of files to be added into the upload
            total_photo_counter++;
            totalPercentage = total_photo_counter;
            $("#counter").text(total_photo_counter);

            //Showing the total amount of MB in the bulk process
            bulksize = bulksize + file.size;
            $("#bulk_size").text(bytesToSize(bulksize));

            //This would enable the button in order to submit the pictures
            $('#submit-all').removeClass('disabled');

        });
        this.on("removedfile",function(file)
        {
            total_photo_counter--;
            $("#counter").text(total_photo_counter);
        });
        this.on("sending",function (file,xhr,formData) {

            let school = jQuery('#school');
            formData.append('school_name',school.val());
            formData.append('school_key',school.find(':selected').attr('data-content'));
            formData.append('disk_selector',jQuery('#disk-selector').val());
            formData.append("_token", token);
            formData.append("filezzide",file.size);
            formData.append("watermark",jQuery("input[name='inlineRadioOptions']:checked").val());



        });
        this.on("error",function (errorMessage) {

            console.log(errorMessage.xhr.responseText);
            button.attr('class','btn btn-danger').text('Error').prepend(`<span class='glyphicon glyphicon-ban-circle processing'/>`);

            return success = false;
        });
        this.on("processing",function ()
        {
            $('.progress').removeClass('hide');
            button.attr('class','btn btn-warning disabled').text('Processing').prepend(`<i class="fa fa-refresh fa-spin processing"></i>`);
        });
        this.on("success",function()
        {
            myDropzone.options.autoProcessQueue = true;
            totalPercentage --;
            $("#myprogressbar").css('width',remaining(total_photo_counter,totalPercentage) +'%');
        });

        this.on("queuecomplete",function(file)
        {
            if(success) {
                window.location.replace("drop_zone/confirmation/" + jQuery('#school').find(':selected').attr('data-content'));
                // myDropzone.removeFile(file);
            }
        });

        function remaining(total,remaining) {
            data = (1-(remaining/total))*100;
            return data;
        }
    }
};

//private

function check_out()
{
    let $school = $('#school').find(":selected").data('content');
    if($school){
        console.log('ok');
        //Process the Score and save in DB
        save_score_board();
        myDropzone.processQueue();
    }else{
        $('.schedule-container').addClass('error');
        $('#states').focus();
        $('#error-message-schedule').collapse('show');
    }
}



function save_score_board()
{
    let CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    //pull the id of the game selected
    let $school = $('#school').find(":selected").data('content');
    //pull the score home
    let $home = $('#home_ID').val();
    //pull the score away
    let $away = $('#away_ID').val();
    // console.log([$school,$home,$away]);
    //save into the db
    $.ajax({
        method:'POST',
        url:'schedule/save_score',
        data:{
            _token: CSRF_TOKEN,
            id:$school,
            home: $home,
            away: $away
        },
        success: function(re){
            //cleaning the container
            $('.score-collapse').empty().append('<div class="alert alert-success" role="alert">A new score has been processed</div>');
        },
        error:function (request,status,error) {
            $('.score-collapse').empty().append('<div class="alert alert-alert" role="alert">' + error + '</div>');
        }
    })


}

