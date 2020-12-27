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

/* eslint max-depth: ["error", 8] */

/**
 * Library of classes for handling simple shapes.
 *
 * These classes can represent shapes, let you alter them, can go to and from a string
 * representation, and can give you an SVG representation.
 *
 * @package    qtype_ddmarker
 * @subpackage shapes
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(function() {

    "use strict";

    /**
     * A point, with x and y coordinates.
     *
     * @param {int} x centre X.
     * @param {int} y centre Y.
     * @constructor
     */
    function Point(x, y) {
        this.x = x;
        this.y = y;
    }

    /**
     * Standard toString method.
     * @returns {string} "x;y";
     */
    Point.prototype.toString = function() {
        return this.x + ',' + this.y;
    };

    /**
     * Move a point
     * @param {int} dx x offset
     * @param {int} dy y offset
     */
    Point.prototype.move = function(dx, dy) {
        this.x += dx;
        this.y += dy;
    };

    /**
     * Return a new point that is a certain position relative to this one.
     *
     * @param {(int|Point)} offsetX if a point, offset by this points coordinates, else and int x offset.
     * @param {int} [offsetY] used if offsetX is an int, the corresponding y offset.
     * @return {Point} the new point.
     */
    Point.prototype.offset = function(offsetX, offsetY) {
        if (offsetX instanceof Point) {
            offsetY = offsetX.y;
            offsetX = offsetX.x;
        }
        return new Point(this.x + offsetX, this.y + offsetY);
    };

    /**
     * Make a point from the string representation.
     *
     * @param {String} coordinates "x,y".
     * @return {Point} the point. Throws an exception if input is not valid.
     */
    Point.parse = function(coordinates) {
        var bits = coordinates.split(',');
        if (bits.length !== 2) {
            throw new Error(coordinates + ' is not a valid point');
        }
        return new Point(Math.round(bits[0]), Math.round(bits[1]));
    };


    /**
     * Shape constructor. Abstract class to represent the different types of drop zone shapes.
     *
     * @param {String} [label] name of this area.
     * @param {int} [x] centre X.
     * @param {int} [y] centre Y.
     * @constructor
     */
    function Shape(label, x, y) {
        this.label = label;
        this.centre = new Point(x || 0, y || 0);
    }

    /**
     * Get the type of shape.
     *
     * @return {String} 'circle', 'rectangle' or 'polygon';
     */
    Shape.prototype.getType = function() {
        throw new Error('Not implemented.');
    };

    /**
     * Get the string representation of this shape.
     *
     * @return {String} coordinates as they need to be typed into the form.
     */
    Shape.prototype.getCoordinates = function() {
        throw new Error('Not implemented.');
    };

    /**
     * Update the shape from the string representation.
     *
     * @param {String} coordinates in the form returned by getCoordinates.
     * @param {number} ratio Ratio to scale.
     * @return {boolean} true if the string could be parsed and the shape updated, else false.
     */
    Shape.prototype.parse = function(coordinates, ratio) {
        void (coordinates, ratio);
        throw new Error('Not implemented.');
    };

    /**
     * Move the entire shape by this offset.
     *
     * @param {int} dx x offset.
     * @param {int} dy y offset.
     * @param {int} maxX ensure that after editing, the shape lies between 0 and maxX on the x-axis.
     * @param {int} maxY ensure that after editing, the shape lies between 0 and maxX on the y-axis.
     */
    Shape.prototype.move = function(dx, dy, maxX, maxY) {
        void (maxY);
    };

    /**
     * Move one of the edit handles by this offset.
     *
     * @param {int} handleIndex which handle was moved.
     * @param {int} dx x offset.
     * @param {int} dy y offset.
     * @param {int} maxX ensure that after editing, the shape lies between 0 and maxX on the x-axis.
     * @param {int} maxY ensure that after editing, the shape lies between 0 and maxX on the y-axis.
     */
    Shape.prototype.edit = function(handleIndex, dx, dy, maxX, maxY) {
        void (maxY);
    };

    /**
     * Update the properties of this shape after a sequence of edits.
     *
     * For example make sure the circle radius is positive, of the polygon centre is centred.
     */
    Shape.prototype.normalizeShape = function() {
        void (1); // To make CiBoT happy.
    };

    /**
     * Get the string representation of this shape.
     *
     * @param {SVGElement} svg the SVG graphic to add this shape to.
     * @return {SVGElement} SVG representation of this shape.
     */
    Shape.prototype.makeSvg = function(svg) {
        void (svg);
        throw new Error('Not implemented.');
    };

    /**
     * Update the SVG representation of this shape.
     *
     * @param {SVGElement} svgEl the SVG representation of this shape.
     */
    Shape.prototype.updateSvg = function(svgEl) {
        void (svgEl);
    };

    /**
     * Make a circle similar to this shape.
     *
     * @return {Circle} a circle that is about the same size and position as this shape.
     */
    Shape.prototype.makeSimilarCircle = function() {
        throw new Error('Not implemented.');
    };

    /**
     * Make a rectangle similar to this shape.
     *
     * @return {Rectangle} a rectangle that is about the same size and position as this shape.
     */
    Shape.prototype.makeSimilarRectangle = function() {
        throw new Error('Not implemented.');
    };

    /**
     * Make a polygon similar to this shape.
     *
     * @return {Polygon} a polygon that is about the same size and position as this shape.
     */
    Shape.prototype.makeSimilarPolygon = function() {
        throw new Error('Not implemented.');
    };

    /**
     * Get the handles that should be offered to edit this shape, or null if not appropriate.
     *
     * @return {[Object]} with properties moveHandle {Point} and editHandles {Point[]}
     */
    Shape.prototype.getHandlePositions = function() {
        return null;
    };


    /**
     * A shape that is a circle.
     *
     * @param {String} label name of this area.
     * @param {int} [x] centre X.
     * @param {int} [y] centre Y.
     * @param {int} [radius] radius.
     * @constructor
     */
    function Circle(label, x, y, radius) {
        x = x || 15;
        y = y || 15;
        Shape.call(this, label, x, y);
        this.radius = radius || 15;
    }
    Circle.prototype = new Shape();

    Circle.prototype.getType = function() {
        return 'circle';
    };

    Circle.prototype.getCoordinates = function() {
        return this.centre + ';' + Math.abs(this.radius);
    };

    Circle.prototype.makeSvg = function(svg) {
        var svgEl = createSvgShapeGroup(svg, 'circle');
        this.updateSvg(svgEl);
        return svgEl;
    };

    Circle.prototype.updateSvg = function(svgEl) {
        svgEl.childNodes[0].setAttribute('cx', this.centre.x);
        svgEl.childNodes[0].setAttribute('cy', this.centre.y);
        svgEl.childNodes[0].setAttribute('r', Math.abs(this.radius));
        svgEl.childNodes[1].setAttribute('x', this.centre.x);
        svgEl.childNodes[1].setAttribute('y', this.centre.y + 15);
        svgEl.childNodes[1].textContent = this.label;
    };

    Circle.prototype.parse = function(coordinates, ratio) {
        if (!coordinates.match(/^\d+(\.\d+)?,\d+(\.\d+)?;\d+(\.\d+)?$/)) {
            return false;
        }

        var bits = coordinates.split(';');
        this.centre = Point.parse(bits[0]);
        this.centre.x = this.centre.x * parseFloat(ratio);
        this.centre.y = this.centre.y * parseFloat(ratio);
        this.radius = Math.round(bits[1]) * parseFloat(ratio);
        return true;
    };

    Circle.prototype.move = function(dx, dy, maxX, maxY) {
        this.centre.move(dx, dy);
        if (this.centre.x < this.radius) {
            this.centre.x = this.radius;
        }
        if (this.centre.x > maxX - this.radius) {
            this.centre.x = maxX - this.radius;
        }
        if (this.centre.y < this.radius) {
            this.centre.y = this.radius;
        }
        if (this.centre.y > maxY - this.radius) {
            this.centre.y = maxY - this.radius;
        }
    };

    Circle.prototype.edit = function(handleIndex, dx, dy, maxX, maxY) {
        this.radius += dx;
        var limit = Math.min(this.centre.x, this.centre.y, maxX - this.centre.x, maxY - this.centre.y);
        if (this.radius > limit) {
            this.radius = limit;
        }
        if (this.radius < -limit) {
            this.radius = -limit;
        }
    };

    /**
     * Update the properties of this shape after a sequence of edits.
     *
     * For example make sure the circle radius is positive, of the polygon centre is centred.
     */
    Circle.prototype.normalizeShape = function() {
        this.radius = Math.abs(this.radius);
    };

    Circle.prototype.makeSimilarRectangle = function() {
        return new Rectangle(this.label,
                this.centre.x - this.radius, this.centre.y - this.radius,
                this.radius * 2, this.radius * 2);
    };

    Circle.prototype.makeSimilarPolygon = function() {
        // We make a similar square, so if you go to and from Rectangle afterwards, it is loss-less.
        return new Polygon(this.label, [
                this.centre.offset(-this.radius, -this.radius), this.centre.offset(-this.radius, this.radius),
                this.centre.offset(this.radius, this.radius), this.centre.offset(this.radius, -this.radius)]);
    };

    Circle.prototype.getHandlePositions = function() {
        return {
            moveHandle: this.centre,
            editHandles: [this.centre.offset(this.radius, 0)]
        };
    };


    /**
     * A shape that is a rectangle.
     *
     * @param {String} label name of this area.
     * @param {int} [x] top left X.
     * @param {int} [y] top left Y.
     * @param {int} [width] width.
     * @param {int} [height] height.
     * @constructor
     */
    function Rectangle(label, x, y, width, height) {
        Shape.call(this, label, x, y);
        this.width = width || 30;
        this.height = height || 30;
    }
    Rectangle.prototype = new Shape();

    Rectangle.prototype.getType = function() {
        return 'rectangle';
    };

    Rectangle.prototype.getCoordinates = function() {
        return this.centre + ';' + this.width + ',' + this.height;
    };

    Rectangle.prototype.makeSvg = function(svg) {
        var svgEl = createSvgShapeGroup(svg, 'rect');
        this.updateSvg(svgEl);
        return svgEl;
    };

    Rectangle.prototype.updateSvg = function(svgEl) {
        if (this.width >= 0) {
            svgEl.childNodes[0].setAttribute('x', this.centre.x);
            svgEl.childNodes[0].setAttribute('width', this.width);
        } else {
            svgEl.childNodes[0].setAttribute('x', this.centre.x + this.width);
            svgEl.childNodes[0].setAttribute('width', -this.width);
        }
        if (this.height >= 0) {
            svgEl.childNodes[0].setAttribute('y', this.centre.y);
            svgEl.childNodes[0].setAttribute('height', this.height);
        } else {
            svgEl.childNodes[0].setAttribute('y', this.centre.y + this.height);
            svgEl.childNodes[0].setAttribute('height', -this.height);
        }

        svgEl.childNodes[1].setAttribute('x', this.centre.x + this.width / 2);
        svgEl.childNodes[1].setAttribute('y', this.centre.y + this.height / 2 + 15);
        svgEl.childNodes[1].textContent = this.label;
    };

    Rectangle.prototype.parse = function(coordinates, ratio) {
        if (!coordinates.match(/^\d+(\.\d+)?,\d+(\.\d+)?;\d+(\.\d+)?,\d+(\.\d+)?$/)) {
            return false;
        }

        var bits = coordinates.split(';');
        this.centre = Point.parse(bits[0]);
        this.centre.x = this.centre.x * parseFloat(ratio);
        this.centre.y = this.centre.y * parseFloat(ratio);
        var size = Point.parse(bits[1]);
        this.width = size.x * parseFloat(ratio);
        this.height = size.y * parseFloat(ratio);
        return true;
    };

    Rectangle.prototype.move = function(dx, dy, maxX, maxY) {
        this.centre.move(dx, dy);
        if (this.centre.x < 0) {
            this.centre.x = 0;
        }
        if (this.centre.x > maxX - this.width) {
            this.centre.x = maxX - this.width;
        }
        if (this.centre.y < 0) {
            this.centre.y = 0;
        }
        if (this.centre.y > maxY - this.height) {
            this.centre.y = maxY - this.height;
        }
    };

    Rectangle.prototype.edit = function(handleIndex, dx, dy, maxX, maxY) {
        this.width += dx;
        this.height += dy;
        if (this.width < -this.centre.x) {
            this.width = -this.centre.x;
        }
        if (this.width > maxX - this.centre.x) {
            this.width = maxX - this.centre.x;
        }
        if (this.height < -this.centre.y) {
            this.height = -this.centre.y;
        }
        if (this.height > maxY - this.centre.y) {
            this.height = maxY - this.centre.y;
        }
    };

    /**
     * Update the properties of this shape after a sequence of edits.
     *
     * For example make sure the circle radius is positive, of the polygon centre is centred.
     */
    Rectangle.prototype.normalizeShape = function() {
        if (this.width < 0) {
            this.centre.x += this.width;
            this.width = -this.width;
        }
        if (this.height < 0) {
            this.centre.y += this.height;
            this.height = -this.height;
        }
    };

    Rectangle.prototype.makeSimilarCircle = function() {
        return new Circle(this.label,
                Math.round(this.centre.x + this.width / 2),
                Math.round(this.centre.y + this.height / 2),
                Math.round((this.width + this.height) / 4));
    };

    Rectangle.prototype.makeSimilarPolygon = function() {
        return new Polygon(this.label, [
            this.centre, this.centre.offset(0, this.height),
            this.centre.offset(this.width, this.height), this.centre.offset(this.width, 0)]);
    };

    Rectangle.prototype.getHandlePositions = function() {
        return {
            moveHandle: this.centre.offset(this.width / 2, this.height / 2),
            editHandles: [this.centre.offset(this.width, this.height)]
        };
    };


    /**
     * A shape that is a polygon.
     *
     * @param {String} label name of this area.
     * @param {Point[]} [points] position of the vertices relative to (centreX, centreY).
     *      each object in the array should have two
     * @constructor
     */
    function Polygon(label, points) {
        Shape.call(this, label, 0, 0);
        this.points = points ? points.slice() : [new Point(10, 10), new Point(40, 10), new Point(10, 40)];
        this.normalizeShape();
        this.ratio = 1;
    }
    Polygon.prototype = new Shape();

    Polygon.prototype.getType = function() {
        return 'polygon';
    };

    Polygon.prototype.getCoordinates = function() {
        var coordinates = '';
        for (var i = 0; i < this.points.length; i++) {
            coordinates += this.centre.offset(this.points[i]) + ';';
        }
        return coordinates.slice(0, coordinates.length - 1); // Strip off the last ';'.
    };

    Polygon.prototype.makeSvg = function(svg) {
        var svgEl = createSvgShapeGroup(svg, 'polygon');
        this.updateSvg(svgEl);
        return svgEl;
    };

    Polygon.prototype.updateSvg = function(svgEl) {
        svgEl.childNodes[0].setAttribute('points', this.getCoordinates().replace(/[,;]/g, ' '));
        svgEl.childNodes[0].setAttribute('transform', 'scale(' + parseFloat(this.ratio) + ')');
        svgEl.childNodes[1].setAttribute('x', this.centre.x);
        svgEl.childNodes[1].setAttribute('y', this.centre.y + 15);
        svgEl.childNodes[1].textContent = this.label;
    };

    Polygon.prototype.parse = function(coordinates, ratio) {
        if (!coordinates.match(/^\d+(\.\d+)?,\d+(\.\d+)?(?:;\d+(\.\d+)?,\d+(\.\d+)?)*$/)) {
            return false;
        }

        var bits = coordinates.split(';');
        var points = [];
        for (var i = 0; i < bits.length; i++) {
            points.push(Point.parse(bits[i]));
        }

        this.points = points;
        this.centre.x = 0;
        this.centre.y = 0;
        this.ratio = ratio;
        this.normalizeShape();

        return true;
    };

    Polygon.prototype.move = function(dx, dy, maxX, maxY) {
        this.centre.move(dx, dy);
        var bbXMin = maxX,
            bbXMax = 0,
            bbYMin = maxY,
            bbYMax = 0;
        // Computer centre.
        for (var i = 0; i < this.points.length; i++) {
            bbXMin = Math.min(bbXMin, this.points[i].x);
            bbXMax = Math.max(bbXMax, this.points[i].x);
            bbYMin = Math.min(bbYMin, this.points[i].y);
            bbYMax = Math.max(bbYMax, this.points[i].y);
        }
        if (this.centre.x < -bbXMin) {
            this.centre.x = -bbXMin;
        }
        if (this.centre.x > maxX - bbXMax) {
            this.centre.x = maxX - bbXMax;
        }
        if (this.centre.y < -bbYMin) {
            this.centre.y = -bbYMin;
        }
        if (this.centre.y > maxY - bbYMax) {
            this.centre.y = maxY - bbYMax;
        }
    };

    Polygon.prototype.edit = function(handleIndex, dx, dy, maxX, maxY) {
        this.points[handleIndex].move(dx, dy);
        if (this.points[handleIndex].x < -this.centre.x) {
            this.points[handleIndex].x = -this.centre.x;
        }
        if (this.points[handleIndex].x > maxX - this.centre.x) {
            this.points[handleIndex].x = maxX - this.centre.x;
        }
        if (this.points[handleIndex].y < -this.centre.y) {
            this.points[handleIndex].y = -this.centre.y;
        }
        if (this.points[handleIndex].y > maxY - this.centre.y) {
            this.points[handleIndex].y = maxY - this.centre.y;
        }
    };

    /**
     * Add a new point after the given point, with the same co-ordinates.
     *
     * This does not automatically normalise.
     *
     * @param {int} pointIndex the index of the vertex after which to insert this new one.
     */
    Polygon.prototype.addNewPointAfter = function(pointIndex) {
        this.points.splice(pointIndex, 0,
                new Point(this.points[pointIndex].x, this.points[pointIndex].y));
    };

    Polygon.prototype.normalizeShape = function() {
        var i,
            x = 0,
            y = 0;

        if (this.points.length === 0) {
            return;
        }

        // Computer centre.
        for (i = 0; i < this.points.length; i++) {
            x += this.points[i].x;
            y += this.points[i].y;
        }
        x = Math.round(x / this.points.length);
        y = Math.round(y / this.points.length);

        if (x === 0 && y === 0) {
            return;
        }

        for (i = 0; i < this.points.length; i++) {
            this.points[i].move(-x, -y);
        }
        this.centre.move(x, y);
    };

    Polygon.prototype.makeSimilarCircle = function() {
        return this.makeSimilarRectangle().makeSimilarCircle();
    };

    Polygon.prototype.makeSimilarRectangle = function() {
        var p,
            minX = 0,
            maxX = 0,
            minY = 0,
            maxY = 0;
        for (var i = 0; i < this.points.length; i++) {
            p = this.points[i];
            minX = Math.min(minX, p.x);
            maxX = Math.max(maxX, p.x);
            minY = Math.min(minY, p.y);
            maxY = Math.max(maxY, p.y);
        }
        return new Rectangle(this.label,
                this.centre.x + minX, this.centre.y + minY,
                Math.max(maxX - minX, 10), Math.max(maxY - minY, 10));
    };

    Polygon.prototype.getHandlePositions = function() {
        var editHandles = [];
        for (var i = 0; i < this.points.length; i++) {
            editHandles.push(this.points[i].offset(this.centre.x, this.centre.y));
        }

        this.centre.x = this.centre.x * parseFloat(this.ratio);
        this.centre.y = this.centre.y * parseFloat(this.ratio);

        return {
            moveHandle: this.centre,
            editHandles: editHandles
        };
    };


    /**
     * Not a shape (null object pattern).
     *
     * @param {String} label name of this area.
     * @constructor
     */
    function NullShape(label) {
        Shape.call(this, label);
    }
    NullShape.prototype = new Shape();

    NullShape.prototype.getType = function() {
        return 'null';
    };

    NullShape.prototype.getCoordinates = function() {
        return '';
    };

    NullShape.prototype.makeSvg = function(svg) {
        void (svg);
        return null;
    };

    NullShape.prototype.updateSvg = function(svgEl) {
        void (svgEl);
    };

    NullShape.prototype.parse = function(coordinates) {
        void (coordinates);
        return false;
    };

    NullShape.prototype.makeSimilarCircle = function() {
        return new Circle(this.label);
    };

    NullShape.prototype.makeSimilarRectangle = function() {
        return new Rectangle(this.label);
    };

    NullShape.prototype.makeSimilarPolygon = function() {
        return new Polygon(this.label);
    };


    /**
     * Make a new SVG DOM element as a child of svg.
     *
     * @param {SVGElement} svg the parent node.
     * @param {String} tagName the tag name.
     * @return {SVGElement} the newly created node.
     */
    function createSvgElement(svg, tagName) {
        var svgEl = svg.ownerDocument.createElementNS('http://www.w3.org/2000/svg', tagName);
        svg.appendChild(svgEl);
        return svgEl;
    }

    /**
     * Make a group SVG DOM elements containing a shape of the given type as first child,
     * and a text label as the second child.
     *
     * @param {SVGElement} svg the parent node.
     * @param {String} tagName the tag name.
     * @return {SVGElement} the newly created g element.
     */
    function createSvgShapeGroup(svg, tagName) {
        var svgEl = createSvgElement(svg, 'g');
        createSvgElement(svgEl, tagName).setAttribute('class', 'shape');
        createSvgElement(svgEl, 'text').setAttribute('class', 'shapeLabel');
        return svgEl;
    }

    /**
     * @alias module:qtype_ddmarker/shapes
     */
    return {
        /**
         * A point, with x and y coordinates.
         *
         * @param {int} x centre X.
         * @param {int} y centre Y.
         * @constructor
         */
        Point: Point,

        /**
         * A point, with x and y coordinates.
         *
         * @param {int} x centre X.
         * @param {int} y centre Y.
         * @constructor
         */
        Shape: Shape,

        /**
         * A shape that is a circle.
         *
         * @param {String} label name of this area.
         * @param {int} [x] centre X.
         * @param {int} [y] centre Y.
         * @param {int} [radius] radius.
         * @constructor
         */
        Circle: Circle,

        /**
         * A shape that is a rectangle.
         *
         * @param {String} label name of this area.
         * @param {int} [x] top left X.
         * @param {int} [y] top left Y.
         * @param {int} [width] width.
         * @param {int} [height] height.
         * @constructor
         */
        Rectangle: Rectangle,

        /**
         * A shape that is a polygon.
         *
         * @param {String} label name of this area.
         * @param {Point[]} [points] position of the vertices relative to (centreX, centreY).
         *      each object in the array should have two
         * @constructor
         */
        Polygon: Polygon,

        /**
         * Not a shape (null object pattern).
         *
         * @param {String} label name of this area.
         * @constructor
         */
        NullShape: NullShape,

        /**
         * Make a new SVG DOM element as a child of svg.
         *
         * @param {SVGElement} svg the parent node.
         * @param {String} tagName the tag name.
         * @return {SVGElement} the newly created node.
         */
        createSvgElement: createSvgElement,

        /**
         * Make a shape of the given type.
         *
         * @param {String} shapeType
         * @param {String} label
         * @return {Shape} the requested shape.
         */
        make: function(shapeType, label) {
            switch (shapeType) {
                case 'circle':
                    return new Circle(label);
                case 'rectangle':
                    return new Rectangle(label);
                case 'polygon':
                    return new Polygon(label);
                default:
                    return new NullShape(label);
            }
        },

        /**
         * Make a shape of the given type that is similar to the shape of the original type.
         *
         * @param {String} shapeType the new type of shape to make
         * @param {Shape} shape the shape to copy
         * @return {Shape} the similar shape of a different type.
         */
        getSimilar: function(shapeType, shape) {
            if (shapeType === shape.getType()) {
                return shape;
            }
            switch (shapeType) {
                case 'circle':
                    return shape.makeSimilarCircle();
                case 'rectangle':
                    return shape.makeSimilarRectangle();
                case 'polygon':
                    return shape.makeSimilarPolygon();
                default:
                    return new NullShape(shape.label);
            }
        }
    };
});
