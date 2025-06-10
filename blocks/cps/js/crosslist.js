(function(){
  $(document).ready(function() {
    return $("#id_save").click(function() {
      var validated, value;
      value = true;
      validated = [];
      $("input[name^='shell_name_']").each(function(index, name) {
        $(name).attr('type' === 'hidden') ? $(validated).each(function(i, n) {
          if (n === $(name).val()) {
            value = false;
            return value;
          }
        }) : null;
        return validated.push($(name).val());
      });
      !value ? $("#split_error").text("Each shell should have a unique name.") : null;
      return value;
    });
  });
})();
