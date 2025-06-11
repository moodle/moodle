module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),

        // Configuration for uglify task to minify JS files.
        uglify: {
            build: {
                files: [
                    {
                        expand: true,
                        cwd: "amd/src",
                        src: "*.js",
                        dest: "amd/build",
                        ext: ".min.js",
                    },
                ],
            },
        },

        // Configuration for watch task to monitor changes in JS files.
        watch: {
            scripts: {
                files: ["amd/src/**/*.js"],
                tasks: ["uglify"],
                options: {
                    spawn: false,
                },
            },
        },
    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks("grunt-contrib-uglify");

    // Load the plugin that provides the "watch" task.
    grunt.loadNpmTasks("grunt-contrib-watch");

    // Default task(s).
    grunt.registerTask("default", ["uglify", "watch"]);
};
