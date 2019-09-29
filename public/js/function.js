function getParam(str) {
    var v = window.location.search.match(new RegExp('(?:[\?\&]'+str+'=)([^&]+)'));
    return v ? v[1] : null;
}

/**
 * @link https://muffinman.io/javascript-time-ago-function/
 */
function timeAgo(timestamp) {
    if (!timestamp) {
        return '';
    }

    const date = new Date(timestamp * 1000);
    const today = new Date();
    const seconds = Math.round((today - date) / 1000);

    if (seconds < 60) {
        return '<span class="text-success">几秒前</span>';
    }

    return date.format('Y-m-d H:i');
}

function fancyTimeFormat(time) {
    // Hours, minutes and seconds
    var hrs = ~~(time / 3600);
    var mins = ~~((time % 3600) / 60);
    var secs = ~~time % 60;

    // Output like "1:01" or "4:03:59" or "123:03:59"
    var ret = "";

    if (hrs == 0) {
        hrs = '00';
    } else if (hrs < 10) {
        hrs = '0' + hrs;
    }

    if (mins == 0) {
        mins = '00';
    } else if (mins < 10) {
        mins = '0' + mins;
    }

    if (secs == 0) {
        secs = '00';
    } else if (secs < 10) {
        secs = '0' + secs;
    }

    ret += hrs + ":" + mins + ":" + secs;
    return ret;
}

// https://stackoverflow.com/questions/1634748/how-can-i-delete-a-query-string-parameter-in-javascript
function RemoveParameterFromUrl(url, parameter) {
    return url
        .replace(new RegExp('[?&]' + parameter + '=[^&#]*(#.*)?$'), '$1')
        .replace(new RegExp('([?&])' + parameter + '=[^&]*&'), '$1');
}

function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts = url.split('?');
    if (urlparts.length >= 2) {

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i = pars.length; i-- > 0;) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }

        return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
    }
    return url;
}

function setItem(key, value) {
    var prefix = 'supervisor-';

    return localStorage.setItem(prefix + key, value);
}

function getItem(key, value) {
    var prefix = 'supervisor-';

    return localStorage.getItem(prefix + key);
}
