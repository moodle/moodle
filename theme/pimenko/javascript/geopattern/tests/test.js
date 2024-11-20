/* eslint-env mocha */

'use strict';

var fs = require('fs');
var path = require('path');
var assert = require('assert');
var parse = require('xml-parser');
var GeoPattern = require('../');

var GENERATORS = [
	'concentricCircles',
	'diamonds',
	'hexagons',
	'mosaicSquares',
	'nestedSquares',
	'octogons',
	'overlappingCircles',
	'overlappingRings',
	'plaid',
	'plusSigns',
	'sineWaves',
	'squares',
	'tessellation',
	'triangles',
	'xes'
];

var ASSET_DIR = 'tests/assets';

describe('GeoPattern', function () {

	describe('::generate()', function () {

		it('should derive the color from the hash', function () {
			assert.equal(GeoPattern.generate('GitHub').color, '#455e8a');
		});

		describe('options.color', function () {
			it('should override the hash-derived color', function () {
				assert.equal(GeoPattern.generate('', { color: '#ff7f00' }).color, '#ff7f00');
			});
		});

		it('should derive the pattern from the hash', function () {
			assert.equal(
				GeoPattern.generate('GitHub').toString().slice(200, 250),
				'6666666668" fill="#222" fill-opacity="0.0633333333'
			);
		});

		describe('options.generator', function () {
			it('should override the hash-derived generator', function () {
				assert.equal(
					GeoPattern.generate('GitHub', { generator: 'sineWaves' }).toString().slice(200, 250),
					' 300, 48" fill="none" stroke="#222" opacity="0.063'
				);
			});
		});

	});

});

GENERATORS.forEach(function (generator) {
	describe(generator, function () {
		it('should generate the correct SVG string', function () {
			assert.deepEqual(
				parse(GeoPattern.generate(generator, { generator: generator }).toString()),
				parse(fs.readFileSync(path.join(ASSET_DIR, generator + '.svg'), 'utf8'))
			);
		});
	});
});
