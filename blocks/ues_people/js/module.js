M.block_ues_people = {};

M.block_ues_people.init = function(Y) {
    Y.all('form[method=POST] input[type=checkbox]').each(function(checkbox) {
        var name = checkbox.get('name');

        var toggle = function(state) {
            return function(elem) {
                return state ? elem.show() : elem.hide();
            };
        };

        checkbox.on('change', function() {
            var checked = checkbox.getDOMNode().checked;
            Y.all('.' + name).each(toggle(checked));
        });
    });
    Y.one('#export').on('click', function(e) {
        var ferpa       = Y.one('#ferpa');
        var ferpa_warn  = Y.one('#ferpa-warning');
        var agree_ferpa = ferpa.get('checked');
        if(!agree_ferpa){
            e.preventDefault();
            // This sets a style attribute on the element.
            // It should set a css class or id.
            ferpa_warn.setStyle('color', 'red');
        }
    });
}
