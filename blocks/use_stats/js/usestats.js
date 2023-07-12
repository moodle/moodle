/**
 *
 *
 */
// jshint unused:false, undef:false


function toggleusestatsto(blockid) {
    fid = '#date-ts_to' + blockid;
    $(fid).prop('disabled', function(i, v) { return !v; });
}

function initusestatsto(blockid, isdisabled, date) {
    fid = '#date-ts_to' + blockid;
    if (isdisabled) {
        $(fid).attr('value', date);
    }
    $(fid).prop('disabled', isdisabled);
}
