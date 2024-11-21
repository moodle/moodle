'use strict';

var assign = require('object-assign');
var XMLNode = require('./xml');

function SVG() {
	this.width = 100;
	this.height = 100;
	this.svg = new XMLNode('svg');
	this.context = []; // Track nested nodes
	this.setAttributes(this.svg, {
		xmlns: 'http://www.w3.org/2000/svg',
		width: this.width,
		height: this.height
	});

	return this;
}

module.exports = SVG;

// This is a hack so groups work.
SVG.prototype.currentContext = function () {
	return this.context[this.context.length - 1] || this.svg;
};

// This is a hack so groups work.
SVG.prototype.end = function () {
	this.context.pop();
	return this;
};

SVG.prototype.currentNode = function () {
	var context = this.currentContext();
	return context.lastChild || context;
};

SVG.prototype.transform = function (transformations) {
	this.currentNode().setAttribute('transform',
		Object.keys(transformations).map(function (transformation) {
			return transformation + '(' + transformations[transformation].join(',') + ')';
		}).join(' ')
	);
	return this;
};

SVG.prototype.setAttributes = function (el, attrs) {
	Object.keys(attrs).forEach(function (attr) {
		el.setAttribute(attr, attrs[attr]);
	});
};

SVG.prototype.setWidth = function (width) {
	this.svg.setAttribute('width', Math.floor(width));
};

SVG.prototype.setHeight = function (height) {
	this.svg.setAttribute('height', Math.floor(height));
};

SVG.prototype.toString = function () {
	return this.svg.toString();
};

SVG.prototype.rect = function (x, y, width, height, args) {
	// Accept array first argument
	var self = this;
	if (Array.isArray(x)) {
		x.forEach(function (a) {
			self.rect.apply(self, a.concat(args));
		});
		return this;
	}

	var rect = new XMLNode('rect');
	this.currentContext().appendChild(rect);
	this.setAttributes(rect, assign({
		x: x,
		y: y,
		width: width,
		height: height
	}, args));

	return this;
};

SVG.prototype.circle = function (cx, cy, r, args) {
	var circle = new XMLNode('circle');
	this.currentContext().appendChild(circle);
	this.setAttributes(circle, assign({
		cx: cx,
		cy: cy,
		r: r
	}, args));

	return this;
};

SVG.prototype.path = function (str, args) {
	var path = new XMLNode('path');
	this.currentContext().appendChild(path);
	this.setAttributes(path, assign({
		d: str
	}, args));

	return this;
};

SVG.prototype.polyline = function (str, args) {
	// Accept array first argument
	var self = this;
	if (Array.isArray(str)) {
		str.forEach(function (s) {
			self.polyline(s, args);
		});
		return this;
	}

	var polyline = new XMLNode('polyline');
	this.currentContext().appendChild(polyline);
	this.setAttributes(polyline, assign({
		points: str
	}, args));

	return this;
};

// group and context are hacks
SVG.prototype.group = function (args) {
	var group = new XMLNode('g');
	this.currentContext().appendChild(group);
	this.context.push(group);
	this.setAttributes(group, assign({}, args));
	return this;
};
