// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/* jshint node: true, browser: false */
/* eslint-env node */

/**
 * Component Library build tasks.
 *
 * @copyright  2021 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

module.exports = grunt => {

    /**
     * Get a child path of the component library.
     *
     * @param   {string} path
     * @returns {string}
     */
    const getCLPath = path => `admin/tool/componentlibrary/${path}`;

    /**
     * Get a spawn handler.
     *
     * This is a generic function to write the spawn output, and then to exit if required and mark the async task as
     * complete.
     *
     * @param   {Promise} done
     * @returns {function}
     */
    const getSpawnHandler = done => (error, result, code) => {
        grunt.log.write(result);

        if (error) {
            grunt.log.error(result.stdout);
            process.exit(code);
        }
        done();
    };

    /**
     * Spawn a function against Node with the provided args.
     *
     * @param   {array} args
     * @returns {object}
     */
    const spawnNodeCall = (args) => grunt.util.spawn({
        cmd: 'node',
        args,
    }, getSpawnHandler(grunt.task.current.async()));

    /**
     * Build the docs using Hugo.
     *
     * @returns {Object} Reference to the spawned task
     */
    const docsBuild = () => spawnNodeCall([
        'node_modules/hugo-bin/cli.js',
        '--config', getCLPath('config.yml'),
        '--cleanDestinationDir',
    ]);

    /**
     * Build the docs index using the hugo-lunr-indexer.
     *
     * @returns {Object} Reference to the spawned task
     */
    const indexBuild = () => spawnNodeCall([
        'node_modules/hugo-lunr-indexer/bin/hli.js',
        '-i', getCLPath('content/**'),
        '-o', getCLPath('hugo/site/data/my-index.json'),
        '-l', 'yaml',
        '-d', '---',
    ]);

    /**
     * Build the hugo CSS.
     *
     * @returns {Object} Reference to the spawned task
     */
    const cssBuild = () => spawnNodeCall([
        'node_modules/sass/sass.js',
        '--style', 'expanded',
        '--source-map',
        '--embed-sources',
        '--precision', 6,
        '--load-path', process.cwd(),
        getCLPath('hugo/scss/docs.scss'),
        getCLPath('hugo/dist/css/docs.css'),
    ]);

    // Register the various component library tasks.
    grunt.registerTask('componentlibrary:docsBuild', 'Build the component library', docsBuild);
    grunt.registerTask('componentlibrary:cssBuild', 'Build the component library', cssBuild);
    grunt.registerTask('componentlibrary:indexBuild', 'Build the component library', indexBuild);
    grunt.registerTask('componentlibrary', 'Build the component library', [
        'componentlibrary:docsBuild',
        'componentlibrary:cssBuild',
        'componentlibrary:indexBuild',
    ]);

    grunt.config.merge({
        watch: {
            componentLibraryDocs: {
                files: [
                    getCLPath('content/**/*.md'),
                    getCLPath('hugo'),
                ],
                tasks: ['componentlibrary:docsBuild', 'componentlibrary:indexBuild'],
            },
            componentLibraryCSS: {
                files: [
                    getCLPath('hugo/scss/**/*.scss'),
                    'hugo',
                ],
                tasks: ['componentlibrary:cssBuild'],
            },
        },
    });

    // Add the 'componentlibrary' task as a startup task.
    grunt.moodleEnv.startupTasks.push('componentlibrary');

    return {
        docsBuild,
        cssBuild,
        indexBuild,
    };
};
