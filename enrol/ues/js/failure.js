(function(){
    M.ues = {};
    M.ues.failures = function(Y) {
        var buttonCheck, grab;
        grab = function(buttonName) {
            return Y.one("input[name='" + buttonName + "']");
        };
        buttonCheck = function() {
            var disabled, selected;
            selected = [];
            Y.all(".ids").each(function(node, i, nl) {
                if ((node.get('checked'))) {
                    return selected.push(node);
                }
            });
            disabled = selected.length === 0;
            grab('reprocess').set('disabled', disabled);
            return grab('delete').set('disabled', disabled);
        };
        Y.all(".ids").on('change', buttonCheck);
        return Y.one('input[name=select_all]').on('change', function() {
            var selected;
            selected = this.get('checked');
            Y.all(".ids").set('checked', selected);
            return buttonCheck();
        });
    };
})();
