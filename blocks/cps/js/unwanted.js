(function(){
  $(document).ready(function() {
    var apply_event, make_selected;
    make_selected = function(courseid, checked) {
      return function() {
        $("input[id*='course" + courseid + "_']").attr('checked', checked);
        return false;
      };
    };
    apply_event = function(checked) {
      return function(index, elem) {
        var id;
        id = $(elem).attr('id').split('_')[1];
        return $(elem).click(make_selected(id, checked));
      };
    };
    $("a[id^='all_']").each(apply_event(true));
    return $("a[id^='none_']").each(apply_event(false));
  });
})();
