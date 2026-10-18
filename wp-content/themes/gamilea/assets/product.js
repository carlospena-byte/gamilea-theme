/* Weekly offer countdown: now vs. the coming Sunday 23:59:59 in El Salvador (UTC-6, no DST). */
(function () {
    var nodes = document.querySelectorAll('[data-gamilea-countdown]');
    if (!nodes.length) { return; }
    var OFFSET = 6 * 3600 * 1000;
    var pad = function (n) { return n < 10 ? '0' + n : String(n); };

    function remaining() {
        var now = new Date(Date.now() - OFFSET); // UTC fields now read as El Salvador wall-clock
        var daysToSunday = (7 - now.getUTCDay()) % 7;
        var end = Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate() + daysToSunday, 23, 59, 59);
        return Math.max(0, Math.floor((end - now.getTime()) / 1000));
    }

    function render() {
        var s = remaining();
        var text = pad(Math.floor(s / 86400)) + 'd : ' + pad(Math.floor(s % 86400 / 3600)) + 'h : ' + pad(Math.floor(s % 3600 / 60)) + 'm : ' + pad(s % 60) + 's';
        nodes.forEach(function (node) { node.textContent = text; });
    }

    render();
    setInterval(render, 1000);
})();
