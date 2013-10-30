#!/usr/bin/env node

var fs = require('fs'),
    path = require('path'),
    util = require('util');

if (process.argv.length < 3) {
    console.error('You must specify the name of the module when running the backporter');
    console.error('./scripts/backport.js moodle-core-tooltip');
    process.exit(1);
}

// Retrieve the full module name
var fullmodname = process.argv[2];
var modname = fullmodname.split('-').pop();

var sourcefile = path.resolve(process.cwd(), '../../build',
        fullmodname, fullmodname + '-min.js');

var targetdir = path.resolve(process.cwd(), '../../', modname);

if (!fs.existsSync(targetdir)) {
    fs.mkdirSync(targetdir);
}

var targetfile = path.resolve(targetdir, modname + '.js');

var inputfile   = fs.createReadStream(sourcefile),
    outputfile  = fs.createWriteStream(targetfile);

util.pump(inputfile, outputfile, function() {
  console.log("Copied " + sourcefile + " to " + targetfile);
  process.exit(0);
});
