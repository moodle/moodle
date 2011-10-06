YUI.add('moodle-qtype_ddwtos-dd', function(Y) {
    var DDWTOSDDNAME = 'ddwtos_dd';
    var DDWTOS_DD = function() {
        DDWTOS_DD.superclass.constructor.apply(this, arguments);
    }
    /**
     * This is the class for ddwtos question rendering.
     * A DDWTOS_DD class is created for each question.
     */
    Y.extend(DDWTOS_DD, Y.Base, {
        initializer : function(params) {
            console.log(params);
        },
        update_padding_sizes_all : function () {
            for (var groupno = 1; groupno <= 8; groupno++) {
                this.update_padding_size_for_group(groupno);
            }
        },
        update_padding_size_for_group : function (groupno) {
            var groupitems = this.doc.top_node().all('.draghome.group'+groupno);
            if (groupitems.size() !== 0) {
                var maxwidth = 0;
                var maxheight = 0;
                groupitems.each(function(item){
                    maxwidth = Math.max(maxwidth, item.get('clientWidth'));
                    maxheight = Math.max(maxheight, item.get('clientHeight'));
                }, this);
                groupitems.each(function(item) {
                    var margintopbottom = Math.round((10 + maxheight - item.get('clientHeight')) / 2);
                    var marginleftright = Math.round((10 + maxwidth - item.get('clientWidth')) / 2);
                    item.setStyle('padding', margintopbottom+'px '+marginleftright+'px '
                                            +margintopbottom+'px '+marginleftright+'px');
                }, this);
                this.doc.drop_zone_group(groupno).setStyles({'width': maxwidth + 10,
                                                                'height': maxheight + 10});
            }
        }
    }, {
        NAME : DDWTOSDDNAME,
        ATTRS : {
            readonly : {value : false},
            topnode : {value : null}
        }
    });
    M.qtype_ddwtos = M.qtype_ddwtos || {};
    M.qtype_ddwtos.init_question = function(config) {
        return new DDWTOS_DD(config);
    }
}, '@VERSION@', {
      requires:['node', 'dd', 'dd-drop', 'dd-constrain']
});
