require(['core/first'], function() { // jshint ignore:line
    require(['theme_essential/anti_gravity', 'core/log'], function(ag, log) { // jshint ignore:line
        log.debug('Essential JavaScript initialised');
    });
});

// Replacement core JS for using FontAwesome icons instead.
M.util.init_block_hider = function(Y, config) {
    Y.use('base', 'node', function(Y) {
        M.util.block_hider = M.util.block_hider || (function(){
            var blockhider = function() {
                blockhider.superclass.constructor.apply(this, arguments);
            };
            blockhider.prototype = {
                initializer : function(config) {
                    this.set('block', '#' + this.get('id'));
                    var b = this.get('block'),
                        t = b.one('.title'),
                        a = null,
                        hide,
                        show;
                    if (t && (a = t.one('.block_action'))) {
                        hide = Y.Node.create('<i>');
                        hide.addClass('block-hider-hide ' + this.get('iconVisible'));
                        hide.setAttrs({
                            'aria-hidden': true,
                            'aria-label':  config.tooltipVisible,
                            tabIndex:      0,
                            'title':       config.tooltipVisible
                        });
                        hide.on('keypress', this.updateStateKey, this, true);
                        hide.on('click', this.updateState, this, true);

                        show = Y.Node.create('<i>');
                        show.addClass('block-hider-show ' + this.get('iconHidden'));
                        show.setAttrs({
                            'aria-hidden': true,
                            'aria-label':  config.tooltipHidden,
                            tabIndex:      0,
                            'title':       config.tooltipHidden
                        });
                        show.on('keypress', this.updateStateKey, this, false);
                        show.on('click', this.updateState, this, false);

                        a.insert(show, 0).insert(hide, 0);
                    }
                },
                updateState : function(e, hide) {
                    M.util.set_user_preference(this.get('preference'), hide);
                    if (hide) {
                        this.get('block').addClass('hidden');
                        this.get('block').one('.block-hider-show').focus();
                    } else {
                        this.get('block').removeClass('hidden');
                        this.get('block').one('.block-hider-hide').focus();
                    }
                },
                updateStateKey : function(e, hide) {
                    if (e.keyCode == 13) { // Allow hide/show via enter key.
                        this.updateState(this, hide);
                    }
                }
            };
            Y.extend(blockhider, Y.Base, blockhider.prototype, {
                NAME : 'blockhider',
                ATTRS : {
                    id : {},
                    preference : {},
                    iconVisible : {
                        value : 'icon fa fa-minus-square-o fa-fw'
                    },
                    iconHidden : {
                        value : 'icon fa fa-plus-square-o fa-fw'
                    },
                    block : {
                        setter : function(node) {
                            return Y.one(node);
                        }
                    }
                }
            });
            return blockhider;
        })();
        new M.util.block_hider(config);
    });
};
