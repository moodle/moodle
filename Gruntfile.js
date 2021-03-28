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
 * Grunt configuration for Moodle.
 *
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Setup the Grunt Moodle environment.
 *
 * @param   {Grunt} grunt
 * @returns {Object}
 */
const setupMoodleEnvironment = grunt => {
    const fs = require('fs');
    const path = require('path');
    const ComponentList = require(path.join(process.cwd(), '.grunt', 'components.js'));

    const getAmdConfiguration = () => {
        // If the cwd is the amd directory in the current component then it will be empty.
        // If the cwd is a child of the component's AMD directory, the relative directory will not start with ..
        let inAMD = !path.relative(`${componentDirectory}/amd`, cwd).startsWith('..');

        // Globbing pattern for matching all AMD JS source files.
        let amdSrc = [];
        if (inComponent) {
            amdSrc.push(
                componentDirectory + "/amd/src/*.js",
                componentDirectory + "/amd/src/**/*.js"
            );
        } else {
            amdSrc = ComponentList.getAmdSrcGlobList();
        }

        return {
            inAMD,
            amdSrc,
        };
    };

    const getYuiConfiguration = () => {
        let yuiSrc = [];
        if (inComponent) {
            yuiSrc.push(componentDirectory + "/yui/src/**/*.js");
        } else {
            yuiSrc = ComponentList.getYuiSrcGlobList(gruntFilePath + '/');
        }

        return {
            yuiSrc,
        };
    };

    const getStyleConfiguration = () => {
        const ComponentList = require(path.join(process.cwd(), '.grunt', 'components.js'));
        // Build the cssSrc and scssSrc.
        // Valid paths are:
        // [component]/styles.css; and either
        // [theme/[themename]]/scss/**/*.scss; or
        // [theme/[themename]]/style/*.css.
        //
        // If a theme has scss, then it is assumed that the style directory contains generated content.
        let cssSrc = [];
        let scssSrc = [];

        const checkComponentDirectory = componentDirectory => {
            const isTheme = componentDirectory.startsWith('theme/');
            if (isTheme) {
                const scssDirectory = `${componentDirectory}/scss`;

                if (fs.existsSync(scssDirectory)) {
                    // This theme has an SCSS directory.
                    // Include all scss files within it recursively, but do not check for css files.
                    scssSrc.push(`${scssDirectory}/*.scss`);
                    scssSrc.push(`${scssDirectory}/**/*.scss`);
                } else {
                    // This theme has no SCSS directory.
                    // Only hte CSS files in the top-level directory are checked.
                    cssSrc.push(`${componentDirectory}/style/*.css`);
                }
            } else {
                // This is not a theme.
                // All other plugin types are restricted to a single styles.css in their top level.
                cssSrc.push(`${componentDirectory}/styles.css`);
            }
        };

        if (inComponent) {
            checkComponentDirectory(componentDirectory);
        } else {
            ComponentList.getComponentPaths(`${gruntFilePath}/`).forEach(componentPath => {
                checkComponentDirectory(componentPath);
            });
        }

        return {
            cssSrc,
            scssSrc,
        };
    };

    /**
     * Calculate the cwd, taking into consideration the `root` option (for Windows).
     *
     * @param {Object} grunt
     * @returns {String} The current directory as best we can determine
     */
    const getCwd = grunt => {
        let cwd = fs.realpathSync(process.env.PWD || process.cwd());

        // Windows users can't run grunt in a subdirectory, so allow them to set
        // the root by passing --root=path/to/dir.
        if (grunt.option('root')) {
            const root = grunt.option('root');
            if (grunt.file.exists(__dirname, root)) {
                cwd = fs.realpathSync(path.join(__dirname, root));
                grunt.log.ok('Setting root to ' + cwd);
            } else {
                grunt.fail.fatal('Setting root to ' + root + ' failed - path does not exist');
            }
        }

        return cwd;
    };

    // Detect directories:
    // * gruntFilePath          The real path on disk to this Gruntfile.js
    // * cwd                    The current working directory, which can be overridden by the `root` option
    // * relativeCwd            The cwd, relative to the Gruntfile.js
    // * componentDirectory     The root directory of the component if the cwd is in a valid component
    // * inComponent            Whether the cwd is in a valid component
    // * runDir                 The componentDirectory or cwd if not in a component, relative to Gruntfile.js
    // * fullRunDir             The full path to the runDir
    const gruntFilePath = fs.realpathSync(process.cwd());
    const cwd = getCwd(grunt);
    const relativeCwd = path.relative(gruntFilePath, cwd);
    const componentDirectory = ComponentList.getOwningComponentDirectory(relativeCwd);
    const inComponent = !!componentDirectory;
    const inTheme = !!componentDirectory && componentDirectory.startsWith('theme/');
    const runDir = inComponent ? componentDirectory : relativeCwd;
    const fullRunDir = fs.realpathSync(gruntFilePath + path.sep + runDir);
    const {inAMD, amdSrc} = getAmdConfiguration();
    const {yuiSrc} = getYuiConfiguration();
    const {cssSrc, scssSrc} = getStyleConfiguration();

    let files = null;
    if (grunt.option('files')) {
        // Accept a comma separated list of files to process.
        files = grunt.option('files').split(',');
    }

    grunt.log.debug('============================================================================');
    grunt.log.debug(`= Node version:        ${process.versions.node}`);
    grunt.log.debug(`= grunt version:       ${grunt.package.version}`);
    grunt.log.debug(`= process.cwd:         '` + process.cwd() + `'`);
    grunt.log.debug(`= process.env.PWD:     '${process.env.PWD}'`);
    grunt.log.debug(`= path.sep             '${path.sep}'`);
    grunt.log.debug('============================================================================');
    grunt.log.debug(`= gruntFilePath:       '${gruntFilePath}'`);
    grunt.log.debug(`= relativeCwd:         '${relativeCwd}'`);
    grunt.log.debug(`= componentDirectory:  '${componentDirectory}'`);
    grunt.log.debug(`= inComponent:         '${inComponent}'`);
    grunt.log.debug(`= runDir:              '${runDir}'`);
    grunt.log.debug(`= fullRunDir:          '${fullRunDir}'`);
    grunt.log.debug('============================================================================');

    if (inComponent) {
        grunt.log.ok(`Running tasks for component directory ${componentDirectory}`);
    }

    return {
        amdSrc,
        componentDirectory,
        cwd,
        cssSrc,
        files,
        fullRunDir,
        gruntFilePath,
        inAMD,
        inComponent,
        inTheme,
        relativeCwd,
        runDir,
        scssSrc,
        yuiSrc,
    };
};

/**
 * Verify tha tthe current NodeJS version matches the required version in package.json.
 *
 * @param   {Grunt} grunt
 */
const verifyNodeVersion = grunt => {
    const semver = require('semver');

    // Verify the node version is new enough.
    var expected = semver.validRange(grunt.file.readJSON('package.json').engines.node);
    var actual = semver.valid(process.version);
    if (!semver.satisfies(actual, expected)) {
        grunt.fail.fatal('Node version not satisfied. Require ' + expected + ', version installed: ' + actual);
    }
};

/**
 * Grunt configuration.
 *
 * @param {Grunt} grunt
 */
module.exports = function(grunt) {
    // Verify that the Node version meets our requirements.
    verifyNodeVersion(grunt);

    // Setup the Moodle environemnt within the Grunt object.
    grunt.moodleEnv = setupMoodleEnvironment(grunt);

    /**
     * Add the named task.
     *
     * @param   {string} name
     * @param   {Grunt} grunt
     */
    const addTask = (name, grunt) => {
        const path = require('path');
        const taskPath = path.resolve(`./.grunt/tasks/${name}.js`);

        grunt.log.debug(`Including tasks for ${name} from ${taskPath}`);

        require(path.resolve(`./.grunt/tasks/${name}.js`))(grunt);
    };


    // Add Moodle task configuration.
    addTask('gherkinlint', grunt);
    addTask('ignorefiles', grunt);

    addTask('javascript', grunt);
    addTask('style', grunt);

    addTask('watch', grunt);
    addTask('startup', grunt);

    // Register the default task.
    grunt.registerTask('default', ['startup']);
};
