function getParam(str) {
    var v = window.location.search.match(new RegExp('(?:[\?\&]'+str+'=)([^&]+)'));
    return v ? v[1] : null;
}

/**
 * @link https://muffinman.io/javascript-time-ago-function/
 */
function timeAgo(dateParam) {
    if (!dateParam) {
        return null;
    }

    const date = typeof dateParam === 'object' ? dateParam : new Date(dateParam);
    const today = new Date();
    const seconds = Math.round((today - date) / 1000);

    if (seconds < 3) {
        return '刚刚';
    }

    return date.format('Y-m-d');
}