function authorize_jump_to_mypayments(e, args) {
    var locationtogo = M.cfg.wwwroot + '/enrol/authorize/index.php?status=' + args.status;
    locationtogo += '&user=' + (this.checked ? args.userid : '0');
    top.location.href = locationtogo;
}
