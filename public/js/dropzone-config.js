/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 5);
/******/ })
/************************************************************************/
/******/ ({

/***/ 5:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(6);


/***/ }),

/***/ 6:
/***/ (function(module, exports) {

var token = $('#csrf_token').attr('value');
var total_photo_counter = 0;
var bulksize = 0;

//This function would convert the size to the conventional name size

function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Byte';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
};

Dropzone.options.myDropzone = {
    url: "./upload/dropzone",
    maxFiles: 30,
    timeout: 180000,
    parallelUploads: 1,
    acceptedFiles: '.jpg',
    addRemoveLinks: true,
    autoProcessQueue: false,
    chunking: true,
    forceChunking: true,
    chunkSize: 256000,
    parallelChunkUploads: false,
    retryChunks: true,
    retryChunksLimit: 3,

    init: function init() {
        var button = $('#submit-all');
        var total_photo_counter = 0;
        var totalPercentage = 0;
        myDropzone = this;
        var success = true;
        button.on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $school = $('#school').find(":selected").data('content');
            if ($school) {
                //Process the Score and save in DB
                save_score_board();
                myDropzone.processQueue();
            } else {
                $('.schedule-container').addClass('error');
                $('#states').focus();
                $('#error-message-schedule').collapse('show');
            }
        });

        this.on("addedfile", function (file) {
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
        this.on("removedfile", function (file) {
            total_photo_counter--;
            $("#counter").text(total_photo_counter);
        });
        this.on("sending", function (file, xhr, formData) {

            var school = jQuery('#school');
            formData.append('school_name', school.val());
            formData.append('school_key', school.find(':selected').attr('data-content'));
            formData.append('disk_selector', jQuery('#disk-selector').val());
            formData.append("_token", token);
            formData.append("filezzide", file.size);
            formData.append("watermark", jQuery("input[name='inlineRadioOptions']:checked").val());
        });
        this.on("error", function (errorMessage) {

            console.log(errorMessage.xhr.responseText);
            button.attr('class', 'btn btn-danger').text('Error').prepend('<span class=\'glyphicon glyphicon-ban-circle processing\'/>');

            return success = false;
        });
        this.on("processing", function () {
            $('.progress').removeClass('hide');
            button.attr('class', 'btn btn-warning disabled').text('Processing').prepend('<i class="fa fa-refresh fa-spin processing"></i>');
        });
        this.on("success", function () {
            myDropzone.options.autoProcessQueue = true;
            totalPercentage--;
            $("#myprogressbar").css('width', remaining(total_photo_counter, totalPercentage) + '%');
        });

        this.on("queuecomplete", function (file) {
            if (success) {
                window.location.replace("drop_zone/confirmation/" + jQuery('#school').find(':selected').attr('data-content'));
                // myDropzone.removeFile(file);
            }
        });

        function remaining(total, remaining) {
            data = (1 - remaining / total) * 100;
            return data;
        }
    }
};

//private

function check_out() {
    var $school = $('#school').find(":selected").data('content');
    if ($school) {
        console.log('ok');
        //Process the Score and save in DB
        save_score_board();
        myDropzone.processQueue();
    } else {
        $('.schedule-container').addClass('error');
        $('#states').focus();
        $('#error-message-schedule').collapse('show');
    }
}

function save_score_board() {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    //pull the id of the game selected
    var $school = $('#school').find(":selected").data('content');
    //pull the score home
    var $home = $('#home_ID').val();
    //pull the score away
    var $away = $('#away_ID').val();
    // console.log([$school,$home,$away]);
    //save into the db
    $.ajax({
        method: 'POST',
        url: 'schedule/save_score',
        data: {
            _token: CSRF_TOKEN,
            id: $school,
            home: $home,
            away: $away
        },
        success: function success(re) {
            //cleaning the container
            $('.score-collapse').empty().append('<div class="alert alert-success" role="alert">A new score has been processed</div>');
        },
        error: function error(request, status, _error) {
            $('.score-collapse').empty().append('<div class="alert alert-alert" role="alert">' + _error + '</div>');
        }
    });
}

/***/ })

/******/ });