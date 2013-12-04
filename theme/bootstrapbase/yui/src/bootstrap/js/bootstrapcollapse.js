YUI.add('gallery-bootstrap-collapse', function(Y) {

/**
A Plugin which provides collapsing/expanding behaviors on a Node with
compatible syntax and markup from Twitter's Bootstrap project.

@module gallery-bootstrap-collapse
**/

/**
A Plugin which provides collapsing and expanding behaviors on a Node with
compatible syntax and markup from Twitter's Bootstrap project.

It possible to have dynamic behaviors without incorporating any
JavaScript by setting <code>data-toggle=collapse</code> on any element.

However, it can be manually plugged into any node or node list.

@example

    var node = Y.one('.someNode');
    node.plug( Y.Bootstrap.Collapse, config );

    node.collapse.show();

@class Bootstrap.Collapse
**/

function CollapsePlugin() {
    CollapsePlugin.superclass.constructor.apply(this, arguments);
}

CollapsePlugin.NAME = 'Bootstrap.Collapse';
CollapsePlugin.NS   = 'collapse';

Y.extend(CollapsePlugin, Y.Plugin.Base, {
    defaults : {
        duration  : 0.25,
        easing    : 'ease-in',
        showClass : 'in',
        hideClass : 'out',

        groupSelector : '> .accordion-group > .in'
    },

    transitioning: false,

    initializer : function(config) {
        this._node = config.host;

        this.config = Y.mix( config, this.defaults );

        this.publish('show', { preventable : true, defaultFn : this.show });
        this.publish('hide', { preventable : true, defaultFn : this.hide });

        this._node.on('click', this.toggle, this);
    },

    _getTarget: function() {
        var node = this._node,
            container;

        if ( node.getData('target') ) {
            container = Y.one( node.getData('target') );
        }
        else if ( node.getAttribute('href').indexOf('#') >= 0 ) {
            Y.log('No target, looking at href: ' + node.getAttribute('href'), 'debug', 'Bootstrap.Collapse');
            container = Y.one( node.getAttribute('href').substr( node.getAttribute('href').indexOf('#') ) );
        }
        return container;
    },

    /**
    * @method hide
    * @description Hide the collapsible target, specified by the host's
    * <code>data-target</code> or <code>href</code> attribute.
    */
    hide: function() {
        var node      = this._getTarget();

        if ( this.transitioning ) {
            return;
        }

        if ( node ) {
            this._hideElement(node);
        }
    },

    /**
    * @method show
    * @description Show the collapsible target, specified by the host's
    * <code>data-target</code> or <code>href</code> attribute.
    */
    show: function() {
        var node      = this._getTarget(),
            host      = this._node,
            self      = this,
            parent,
            group_selector = this.config.groupSelector;

        if ( this.transitioning ) {
            return;
        }

        if ( host.getData('parent') ) {
            parent = Y.one( host.getData('parent') );
            if ( parent ) {
                parent.all(group_selector).each( function(el) {
                    Y.log('Hiding element: ' + el, 'debug', 'Bootstrap.Collapse');
                    self._hideElement(el);
                });
            }
        }
        this._showElement(node);
    },

    /**
    @method toggle
    @description Toggle the state of the collapsible target, specified
    by the host's <code>data-target</code> or <code>href</code>
    attribute. Calls the <code>show</code> or <code>hide</code> method.
    **/
    toggle : function(e) {
        if ( e && Y.Lang.isFunction(e.preventDefault) ) {
            e.preventDefault();
        }

        var target = this._getTarget();

        if ( target.hasClass( this.config.showClass ) ) {
            this.fire('hide');
        } else {
            this.fire('show');
        }
    },

    /**
    @method _transition
    @description Handles the transition between showing and hiding.
    @protected
    @param node {Node} node to apply transitions to
    @param method {String} 'hide' or 'show'
    **/
    _transition : function(node, method) {
        var self        = this,
            config      = this.config,
            duration    = config.duration,
            easing      = config.easing,
            // If we are hiding, then remove the show class.
            removeClass = method === 'hide' ? config.showClass : config.hideClass,
            // And if we are hiding, add the hide class.
            addClass    = method === 'hide' ? config.hideClass : config.showClass,

            to_height   = method === 'hide' ? 0 : null,
            event       = method === 'hide' ? 'hidden' : 'shown',

            complete = function() {
                node.removeClass(removeClass);
                node.addClass(addClass);
                self.transitioning = false;
                this.fire( event );
            };

        if ( to_height === null ) {
            to_height = 0;
            node.all('> *').each(function(el) {
                to_height += el.get('scrollHeight');
            });
        }

        this.transitioning = true;

        node.transition({
            height   : to_height +'px',
            duration : duration,
            easing   : easing
        }, complete);
    },

    /**
    @method _hideElement
    @description Calls the <code>_transition</code> method to hide a node.
    @protected
    @param node {Node} node to hide.
    **/
    _hideElement : function(node) {
        this._transition(node, 'hide');
/*
        var showClass = this.showClass,
            hideClass = this.hideClass;

        node.removeClass(showClass);
        node.addClass(hideClass);
*/
    },

    /**
    @method _showElement
    @description Calls the <code>_transition</code> method to show a node.
    @protected
    @param node {Node} node to show.
    **/
    _showElement : function(node) {
        this._transition(node, 'show');
/*
        var showClass = this.showClass,
            hideClass = this.hideClass;
        node.removeClass(hideClass);
        node.addClass(showClass);
*/
    }
});

Y.namespace('Bootstrap').Collapse = CollapsePlugin;



}, '@VERSION@' ,{requires:['plugin','transition','event','event-delegate']});
