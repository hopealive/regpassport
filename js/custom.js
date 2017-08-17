/**
 * 
 * init Google Recaptchas
 */

var signupRecaptchaWidgetId;
var searchRecaptchaWidgetId;
var onloadCallback = function () {
    signupRecaptchaWidgetId = grecaptcha.render('signup-recaptcha-widget', {
        'sitekey': '6LeY4SwUAAAAAPlUT4eP91bogYmdhsZs9L9wX6hs',
        'hl': 'uk',
        'theme': 'light'
    });
    searchRecaptchaWidgetId = grecaptcha.render('search-recaptcha-widget', {
        'sitekey': '6LeY4SwUAAAAAPlUT4eP91bogYmdhsZs9L9wX6hs',
        'hl': 'uk',
        'theme': 'light'
    });
};

function initCaptcha() {
    $('.form-signup').on('submit', function () {
        if (grecaptcha.getResponse(signupRecaptchaWidgetId).length > 0) {
            return true;
        }
        return false;
    });
    $('.form-search').on('submit', function () {
        if (grecaptcha.getResponse(searchRecaptchaWidgetId).length > 0) {
            return true;
        }
        return false;
    });
}

/**
 * 
 *  init Google Map
 */
function initMap() {
    var uluru = {lat: 50.1776, lng: 30.3198};
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 17,
        center: uluru
    });
    var marker = new google.maps.Marker({
        position: uluru,
        map: map
    });
}

/**
 * 
 *  Autocomplete functions for marking users
 */
function initAutocomplete() {
    function split(val) {
        return val.split(/,\s*/);
    }
    function extractLast(term) {
        return split(term).pop();
    }

    var source = function (request, response) {
        $.ajax({
            type: 'POST',
            url: '/data.php?action=searchForMarking',
            data: {term: extractLast(request.term)},
            dataType: "json",
            success: function (data) {
                response($.map(data, function (item) {
                    return {
                        label: item.name,
                        id: item.id,
                        value: item.name
                    };
                }));
            }
        });
    };

    $("#search-input")
            .on("keydown", function (event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                        $(this).autocomplete("instance").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                source: source,
                focus: function () {
                    return false;
                },
                search: function () {
                    var term = extractLast(this.value);
                    if (term.length < 1) {
                        return false;
                    }
                },
                select: function (event, ui) {
                    $('#user-id').val(ui.item.id);
                },
                delay: 100,
                minLength: 1
            });
}

/**
 * 
 * Render table with users
 */
function initListUsers() {
    $('.block-list-users').DataTable({
        "ajax": "data.php?action=list",
        "columns": [
            {"data": "id"}
            , {"data": "name"}
            , {"data": "marked"}
        ]
    });
}

//testmode
function initPopup() {
    if ( $.cookie('popuped') == undefined) {
        $('.test-mode-modal').modal();
        $.cookie('popuped', '1');
    }
}


$(document).ready(function () {
    initCaptcha();
    initListUsers();
    initAutocomplete();
    initPopup();
});
