function set_item_focus(itemid) {
    var item = document.getElementById(itemid);
    if(item){
        item.focus();
    }
}

function feedbackGo2delete(form) {
    form.action = moodle_cfg.wwwroot+'/mod/feedback/delete_completed.php';
    form.submit();
}