function authorize_jump_to_mypayments(e, args) {
    var locationtogo = moodle_cfg.wwwroot + '/enrol/authorize/index.php?status=' + args.status;
    locationtogo += '&user=' + (this.checked ? args.userid : '0');
    top.location.href = locationtogo;
}
