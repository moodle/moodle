// Playback guard for local_videowatch.
// Skipping ahead is pulled back to the watched position. The server still
// decides completion; this only stops the ordinary player controls.
(function () {
    var node = document.getElementById('local-videowatch');
    if (!node) {
        return;
    }
    var cfg = {
        cmid: node.getAttribute('data-cmid'),
        sesskey: node.getAttribute('data-sesskey'),
        url: node.getAttribute('data-url'),
        frontier: parseFloat(node.getAttribute('data-frontier') || '0')
    };
    var snapping = false;
    var lastSent = 0;

    function limit(video) {
        if (snapping) {
            return;
        }
        if (video.playbackRate !== 1) {
            video.playbackRate = 1;
        }
        if (video.currentTime > cfg.frontier + 3) {
            snapping = true;
            video.currentTime = cfg.frontier;
            snapping = false;
        }
    }

    function send(video) {
        var now = Date.now();
        if (now - lastSent < 900) {
            return;
        }
        lastSent = now;
        var body = 'sesskey=' + encodeURIComponent(cfg.sesskey)
            + '&cmid=' + encodeURIComponent(cfg.cmid)
            + '&position=' + encodeURIComponent(String(video.currentTime));
        fetch(cfg.url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: body
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            if (data && typeof data.frontier === 'number') {
                cfg.frontier = data.frontier;
            }
        }).catch(function () {
            return null;
        });
    }

    function arm(video) {
        if (video.dataset.videowatchArmed === '1') {
            return;
        }
        video.dataset.videowatchArmed = '1';
        video.controlsList = 'nodownload noplaybackrate';
        video.disablePictureInPicture = true;
        video.addEventListener('contextmenu', function (event) {
            event.preventDefault();
        });
        video.addEventListener('ratechange', function () {
            limit(video);
        });
        video.addEventListener('seeking', function () {
            limit(video);
        });
        video.addEventListener('seeked', function () {
            limit(video);
        });
        video.addEventListener('timeupdate', function () {
            limit(video);
            if (!video.paused && !video.ended) {
                send(video);
            }
        });
        video.addEventListener('ended', function () {
            send(video);
        });
    }

    function scan() {
        document.querySelectorAll('.mediafallbacklink').forEach(function (link) {
            link.hidden = true;
        });
        document.querySelectorAll('video').forEach(function (video) {
            arm(video);
            if (!window.videojs || !video.id || video.dataset.videowatchPlayer === '1') {
                return;
            }
            try {
                var player = videojs.getPlayer(video.id);
                if (!player) {
                    return;
                }
                video.dataset.videowatchPlayer = '1';
                player.playbackRate(1);
                player.on('ratechange', function () {
                    if (player.playbackRate() !== 1) {
                        player.playbackRate(1);
                    }
                });
                player.on('seeking', function () {
                    limit(video);
                });
            } catch (error) {
                return;
            }
        });
    }

    scan();
    document.addEventListener('DOMContentLoaded', scan);
    var ticks = 0;
    var timer = setInterval(function () {
        scan();
        ticks += 1;
        if (ticks > 20) {
            clearInterval(timer);
        }
    }, 500);
    if (window.MutationObserver && document.body) {
        new MutationObserver(scan).observe(document.body, {childList: true, subtree: true});
    }
})();
