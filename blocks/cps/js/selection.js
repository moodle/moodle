(function(){
  $(document).ready(function() {
    var available, bucket, changed, move_selected, move_to_available, move_to_bucket, selected;
    $("input[name^='shell_name_']").keyup(function() {
      var id, value;
      value = $(this).val();
      id = $(this).attr("name");
      $("#" + id).text(value);
      return $("input[name='" + id + "_hidden']").val(value);
    });
    $("a[href^='shell_']").click(function() {
      var display, id, name;
      id = $(this).attr("href").split("_")[1];
      name = $("input[name='shell_name_" + id + "']");
      display = $(name).css("display");
      if (display === "none") {
        $(name).css("display", "block");
        $(name).focus();
        $(name).select();
      } else {
        $(name).css("display", "none");
      }
      return false;
    });
    selected = function() {
      return $("input:checked[name='selected_shell']").attr("value");
    };
    available = $("select[name='before[]']");
    bucket = function() {
      return $("select[name='shell_" + selected() + "[]']");
    };
    changed = function() {
      var compressed, id, toValue, values;
      id = selected();
      values = $("input[name='shell_values_" + id + "']");
      toValue = function(i, child) {
        return $(child).val();
      };
      compressed = $(bucket()).children().map(toValue);
      return values.val($(compressed).toArray().join(","));
    };
    move_selected = function(from, to) {
      var children;
      children = $(from).children(":selected");
      $(to).append(children);
      return changed();
    };
    move_to_bucket = function() {
      return move_selected(available, bucket());
    };
    move_to_available = function() {
      return move_selected(bucket(), available);
    };
    $("input[name='move_right']").click(move_to_bucket);
    return $("input[name='move_left']").click(move_to_available);
  });
})();
