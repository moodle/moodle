YUI.add('moodle-course-coursebase', function(Y) {

    /**
     * The coursebase class
     */
    var COURSEBASENAME = 'course-coursebase';

    var COURSEBASE = function() {
        COURSEBASE.superclass.constructor.apply(this, arguments);
    }

    Y.extend(COURSEBASE, Y.Base, {
        // Registered Modules
        registermodules : [],

        /**
         * Initialize the coursebase module
         */
        initializer : function(config) {
            // We don't actually perform any work here
        },

        /**
         * Register a new Javascript Module
         *
         * @param object The instantiated module to call functions on
         */
        register_module : function(object) {
            this.registermodules.push(object);
        },

        /**
         * Invoke the specified function in all registered modules with the given arguments
         *
         * @param functionname The name of the function to call
         * @param args The argument supplied to the function
         */
        invoke_function : function(functionname, args) {
            for (module in this.registermodules) {
                if (functionname in this.registermodules[module]) {
                    this.registermodules[module][functionname](args);
                }
            }
        }
    },
    {
        NAME : COURSEBASENAME,
        ATTRS : {}
    }
    );

    // Ensure that M.course exists and that coursebase is initialised correctly
    M.course = M.course || {};
    M.course.coursebase = M.course.coursebase || new COURSEBASE();
},
'@VERSION@', {
    requires : ['base', 'node']
}
);
