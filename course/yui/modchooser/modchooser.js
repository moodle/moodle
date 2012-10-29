YUI.add('moodle-course-modchooser', function(Y) {
    var CSS = {
        PAGECONTENT : 'div#page-content',
        SECTION : 'li.section',
        SECTIONMODCHOOSER : 'span.section-modchooser-link',
        SITEMENU : 'div.block_site_main_menu',
        SITETOPIC : 'div.sitetopic'
    };

    var MODCHOOSERNAME = 'course-modchooser';

    var MODCHOOSER = function() {
        MODCHOOSER.superclass.constructor.apply(this, arguments);
    }

    Y.extend(MODCHOOSER, M.core.chooserdialogue, {
        // The current section ID
        sectionid : null,

        // The hidden element holding the jump param
        jumplink : null,

        initializer : function(config) {
            var dialogue = Y.one('.chooserdialoguebody');
            var header = Y.one('.choosertitle');
            var params = {};
            this.setup_chooser_dialogue(dialogue, header, params);

            // Initialize existing sections and register for dynamically created sections
            this.setup_for_section();
            M.course.coursebase.register_module(this);

            // Catch the page toggle
            Y.all('.block_settings #settingsnav .type_course .modchoosertoggle a').on('click', this.toggle_mod_chooser, this);
        },
        /**
         * Update any section areas within the scope of the specified
         * selector with AJAX equivalents
         *
         * @param baseselector The selector to limit scope to
         * @return void
         */
        setup_for_section : function(baseselector) {
            if (!baseselector) {
                var baseselector = CSS.PAGECONTENT;
            }

            // Setup for site topics
            Y.one(baseselector).all(CSS.SITETOPIC).each(function(section) {
                this._setup_for_section(section);
            }, this);

            // Setup for standard course topics
            Y.one(baseselector).all(CSS.SECTION).each(function(section) {
                this._setup_for_section(section);
            }, this);

            // Setup for the block site menu
            Y.one(baseselector).all(CSS.SITEMENU).each(function(section) {
                this._setup_for_section(section);
            }, this);
        },
        _setup_for_section : function(section, sectionid) {
            var chooserspan = section.one(CSS.SECTIONMODCHOOSER);
            if (!chooserspan) {
                return;
            }
            var chooserlink = Y.Node.create("<a href='#' />");
            chooserspan.get('children').each(function(node) {
                chooserlink.appendChild(node);
            });
            chooserspan.insertBefore(chooserlink);
            chooserlink.on('click', this.display_mod_chooser, this);
        },
        /**
         * Display the module chooser
         *
         * @param e Event Triggering Event
         * @param secitonid integer The ID of the section triggering the dialogue
         * @return void
         */
        display_mod_chooser : function (e) {
            // Set the section for this version of the dialogue
            if (e.target.ancestor(CSS.SITETOPIC)) {
                // The site topic has a sectionid of 1
                this.sectionid = 1;
            } else if (e.target.ancestor(CSS.SECTION)) {
                var section = e.target.ancestor(CSS.SECTION);
                this.sectionid = section.get('id').replace('section-', '');
            } else if (e.target.ancestor(CSS.SITEMENU)) {
                // The block site menu has a sectionid of 0
                this.sectionid = 0;
            }
            this.display_chooser(e);
        },
        toggle_mod_chooser : function(e) {
            // Get the add section link
            var modchooserlinks = Y.all('div.addresourcemodchooser');

            // Get the dropdowns
            var dropdowns = Y.all('div.addresourcedropdown');

            if (modchooserlinks.size() == 0) {
                // Continue with non-js action if there are no modchoosers to add
                return;
            }

            // We need to update the text and link
            var togglelink = Y.one('.block_settings #settingsnav .type_course .modchoosertoggle a');

            // The actual text is in the last child
            var toggletext = togglelink.get('lastChild');

            var usemodchooser;
            // Determine whether they're currently hidden
            if (modchooserlinks.item(0).hasClass('visibleifjs')) {
                // The modchooser is currently visible, hide it
                usemodchooser = 0;
                modchooserlinks
                    .removeClass('visibleifjs')
                    .addClass('hiddenifjs');
                dropdowns
                    .addClass('visibleifjs')
                    .removeClass('hiddenifjs');
                toggletext.set('data', M.util.get_string('modchooserenable', 'moodle'));
                togglelink.set('href', togglelink.get('href').replace('off', 'on'));
            } else {
                // The modchooser is currently not visible, show it
                usemodchooser = 1;
                modchooserlinks
                    .addClass('visibleifjs')
                    .removeClass('hiddenifjs');
                dropdowns
                    .removeClass('visibleifjs')
                    .addClass('hiddenifjs');
                toggletext.set('data', M.util.get_string('modchooserdisable', 'moodle'));
                togglelink.set('href', togglelink.get('href').replace('on', 'off'));
            }

            M.util.set_user_preference('usemodchooser', usemodchooser);

            // Prevent the page from reloading
            e.preventDefault();
        },
        option_selected : function(thisoption) {
            // Add the sectionid to the URL
            this.jumplink.set('value', thisoption.get('value') + '&section=' + this.sectionid);
        }
    },
    {
        NAME : MODCHOOSERNAME,
        ATTRS : {
        }
    });
    M.course = M.course || {};
    M.course.init_chooser = function(config) {
        return new MODCHOOSER(config);
    }
},
'@VERSION@', {
    requires:['base', 'overlay', 'moodle-core-chooserdialogue', 'transition']
}
);
