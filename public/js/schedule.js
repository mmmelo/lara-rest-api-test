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
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */,
/* 1 */,
/* 2 */,
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(4);


/***/ }),
/* 4 */
/***/ (function(module, exports) {

var $sport_collapse = $('.sport-collapse');
$("#state-container").change(function () {
    $sport_collapse.collapse('show');
    if ($sport_collapse.hasClass('in')) {
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
    backdrop: {
        "background-color": '#fff'
    },
    template: function template(query, item) {
        var gender = '<i class="fas fa-venus"></i>';
        var gender_v2 = '<span class="girls"> {{group_gender}} </span>';
        if (item.group_gender === "Boys") {
            gender = '<i class="fas fa-mars"></i>';
            gender_v2 = '<span class="boys"> {{group_gender}} </span>';
        }
        return '{{full_name}} | {{group_level}} | ' + gender_v2 + gender + '  {{group}} </small>';
    },
    emptyTemplate: "no result for {{query}}",
    source: {
        school: {
            display: "full_name",
            ajax: function ajax(query) {
                var state = $('#states').find(":selected").data('content');
                var sport = $('#sport').find(":selected").text();
                return {
                    type: "GET",
                    url: "/team/search",
                    path: "data",
                    data: {
                        q: "{{query}}",
                        state: state,
                        sport: sport
                    },

                    callback: {
                        done: function done(data) {
                            return data;
                        }
                    }
                };
            }
        }
    },
    callback: {
        onClick: function onClick(node, a, item, event) {
            // You can do a simple window.location of the item.href
            request_schedule(true, item.id);
            console.log(item.id);
        }
    }

});

$('#school').change(function () {
    var school = $('#school').find(":selected").data('content');
    has_scores(school);
});

//---------private------//
function request_schedule() {
    var shrink = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
    var group_id = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

    var sport = $('#sport').find(":selected").text();
    var state = $('#states').find(":selected").data('content');
    var $schedule = $('select#school');
    $schedule.children().remove().end().append('<option value="">Loading...</option>');

    $.ajax({
        method: 'GET',
        url: 'schedule_filter',
        data: {
            'state': state,
            'sport': sport,
            'shrink': shrink,
            'group_id': group_id
        },
        success: function success(response) {
            $schedule.empty();
            if (response.length !== 0) {
                $.each(response, function (index, value) {
                    $schedule.append('<option data-content="' + value.id + '">' + value.event_date + ' | ' + value.home_name + '<strong> Vs </strong>' + value.event_opponent_name + '</option>').data('content', value.id);
                });
            } else {
                $schedule.append('<option>No schedule available</option>');
            }
        }
    });
}

function has_scores($id) {
    $.ajax({
        method: 'GET',
        url: 'schedule/find_v2',
        data: { 'id': $id },
        success: function success(response) {
            //Check if the Schedule is without score
            if (response.group_result == null && response.group_result == null) {
                $('.score-collapse').collapse('show');
                $('#score_home_id').empty().append(response.home.full_name);
                $('#score_opponent_id').empty().append(response.opponent.full_name);
            } else {
                $('.score-collapse').collapse('hide');
            }
        }
    });
}

/***/ })
/******/ ]);