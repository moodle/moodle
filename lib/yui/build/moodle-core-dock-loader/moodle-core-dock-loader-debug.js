YUI.add('moodle-core-dock-loader', function (Y, NAME) {

var LOADERNAME = 'moodle-core-dock-loader';

M.core = M.core || {};
M.core.dock = M.core.dock || {};

/**
 * Creates the move to dock icon for dockable blocks if it doesn't already exist.
 *
 * @static
 * @method M.core.dock.ensureMoveToIconExists
 * @param {Node} blocknode The Blocks node (.block[data-instanceid])
 */
M.core.dock.ensureMoveToIconExists = function(blocknode) {
    if (blocknode.one('.moveto')) {
        return true;
    }

    var commands,
        moveto = Y.Node.create('<input type="image" class="moveto customcommand requiresjs" />'),
        blockaction = blocknode.one('.block_action'),
        icon = 't/block_to_dock',
        titleh2 = blocknode.one('.header .title h2');

    // Must set the image src separately of we get an error with XML strict headers
    if (Y.one(document.body).hasClass('dir-rtl')) {
        icon = icon + '_rtl';
    }
    moveto.setAttribute('alt', M.util.get_string('addtodock', 'block'));
    if (titleh2) {
        moveto.setAttribute('title', Y.Escape.html(M.util.get_string('dockblock', 'block', titleh2.getHTML())));
    }
    moveto.setAttribute('src', M.util.image_url(icon, 'moodle'));

    if (blockaction) {
        blockaction.prepend(moveto);
    } else {
        commands = blocknode.one('.header .title .commands');
        if (!commands && blocknode.one('.header .title')) {
            commands = Y.Node.create('<div class="commands"></div>');
            blocknode.one('.header .title').append(commands);
        }
        commands.append(moveto);
    }
    return true;
};

/**
 * Dock loader.
 *
 * The dock loader is repsponsible for loading and initialising the dock only when required.
 * By doing this we avoid the need to load unnecessary JavaScript into the page for the dock just incase
 * it is being used.
 *
 * @static
 * @namespace M.core.dock
 * @class Loader
 */
M.core.dock.loader = M.core.dock.loader || {};

/**
 * Delegation events
 * @property delegationEvents
 * @protected
 * @type {Array}
 */
M.core.dock.loader.delegationEvents = [];

/**
 * Initialises the dock loader.
 *
 * The dock loader works by either firing the dock immediately if there are already docked blocks.
 * Or if there are not any docked blocks delegating two events and then loading and firing the dock when one of
 * those delegated events is triggered.
 *
 * @method initLoader
 */
M.core.dock.loader.initLoader = function() {
    Y.log('Dock loader initialising', 'debug', LOADERNAME);
    var dockedblocks = Y.all('.block[data-instanceid][data-dockable]'),
        body = Y.one(document.body),
        callback;
    dockedblocks.each(function() {
        var id = parseInt(this.getData('instanceid'), 10);
        Y.log('Dock loader watching block with instance id: ' + id, 'debug', LOADERNAME);
        M.core.dock.ensureMoveToIconExists(this);
    });
    if (dockedblocks.some(function(node) { return node.hasClass('dock_on_load'); })) {
        Y.log('Loading dock module', 'debug', LOADERNAME);
        Y.use('moodle-core-dock', function() {
            M.core.dock.init();
        });
    } else {
        callback = function(e) {
            var i,
                block = this.ancestor('.block[data-instanceid]'),
                instanceid = block.getData('instanceid');
            e.halt();
            for (i in M.core.dock.loader.delegationEvents) {
                if (Y.Lang.isNumber(i) || Y.Lang.isString(i)) {
                    M.core.dock.loader.delegationEvents[i].detach();
                }
            }
            block.addClass('dock_on_load');
            Y.log('Loading dock module', 'debug', LOADERNAME);
            Y.use('moodle-core-dock', function() {
                M.util.set_user_preference('docked_block_instance_' + instanceid, 1);
                M.core.dock.init();
            });
        };
        M.core.dock.loader.delegationEvents.push(body.delegate('click', callback, '.moveto'));
        M.core.dock.loader.delegationEvents.push(body.delegate('key', callback, '.moveto', 'enter'));
    }
};


}, '@VERSION@', {"requires": ["escape"]});
