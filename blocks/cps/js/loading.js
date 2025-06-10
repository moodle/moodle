(function(){
  $(document).ready(function() {
    var params;
    params = {};
    $('.cps_loading').ajaxError(function() {
      return $('.cps_loading').html($('.network_failure').html());
    });
    $('.passed').each(function(i, elem) {
      params[$(elem).attr('name')] = $(elem).val();
      return params[$(elem).attr('name')];
    });
    return $.post(window.location.pathname, params, function(html) {
      return $('.cps_loading').html(html);
    });
  });
})();
