function submit_attempts_form(e, args) {
    if (e.target.selectedIndex > 0) {
        submit_form_by_id(null, {id: 'attemptsform'});
    }
}
