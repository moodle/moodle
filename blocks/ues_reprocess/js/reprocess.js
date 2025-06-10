(function(){
  M.block_ues_reprocess = {};
  M.block_ues_reprocess.init = function(Y) {
    var courses, pull, sections;
    pull = function(value) {
      return Y.one('input[name=' + value + ']').get('value');
    };
    sections = function() {
      var ret;
      ret = [];
      Y.all('input').each(function(node) {
        var name;
        name = node.get('name');
        if (name.match(/^section_/)) {
          return ret.push(node);
        }
      });
      return ret;
    };
    courses = function() {
      var ret;
      ret = [];
      Y.all('input').each(function(node) {
        var name;
        name = node.get('name');
        if (name.match(/^course_/)) {
          return ret.push(node);
        }
      });
      return ret;
    };
    return Y.one('form[method=POST]').on('submit', function(e) {
      var _a, _b, _c, _d, _e, _f, elem, params, set;
      e.preventDefault();
      params = {
        type: pull('type'),
        id: pull('id')
      };
      set = function(section) {
        var name;
        name = section.get('name');
        params[name] = pull(name);
        return params[name];
      };
      _b = courses();
      for (_a = 0, _c = _b.length; _a < _c; _a++) {
        elem = _b[_a];
        set(elem);
      }
      _e = sections();
      for (_d = 0, _f = _e.length; _d < _f; _d++) {
        elem = _e[_d];
        set(elem);
      }
      Y.one('.buttons').getDOMNode().innerHTML = Y.one('#loading').getDOMNode().innerHTML;
      Y.io('rpc.php', {
        method: 'POST',
        data: params,
        "on": {
          success: function(id, result) {
            Y.one('#notice').getDOMNode().innerHTML = result.responseText;
            return Y.one('#notice').getDOMNode().innerHTML;
          }
        }
      });
      return false;
    });
  };
})();
