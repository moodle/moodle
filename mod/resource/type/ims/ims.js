window.onresize = function() {
    resizeiframe(imsdata.jsarg, imsdata.customcorners);
};
window.name='ims-cp-page';

// Set Interval until ims-containerdiv and (ims-contentframe or ims-contentframe-no-nav) is available
function waiting() {
    var cd   = document.getElementById('ims-containerdiv');
    var cf   = document.getElementById('ims-contentframe');
    var cfnv = document.getElementById('ims-contentframe-no-nav');

    if (cd && (cf || cfnv)) {
        resizeiframe(imsdata.jsarg, imsdata.customcorners);
        clearInterval(ourInterval);
        return true;
    }
    return false;
}

var ourInterval = setInterval('waiting()', 100);