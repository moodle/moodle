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

/**
 * @module moodle-gradereport_grader-gradereporttable
 * @submodule floatingheaders
 */

/**
 * Provides floating headers to the grader report.
 *
 * See {{#crossLink "M.gradereport_grader.ReportTable"}}{{/crossLink}} for details.
 *
 * @namespace M.gradereport_grader
 * @class FloatingHeaders
 */

var HEIGHT = 'height',
    WIDTH = 'width',
    OFFSETWIDTH = 'offsetWidth',
    OFFSETHEIGHT = 'offsetHeight';

function FloatingHeaders() {}

FloatingHeaders.ATTRS= {
};

FloatingHeaders.prototype = {
    /**
     * The height of the page header if a fixed position, floating header
     * was found.
     *
     * @property pageHeaderHeight
     * @type Number
     * @default 0
     * @protected
     */
    pageHeaderHeight: 0,

    /**
     * A Node representing the container div.
     *
     * Positioning will be based on this element, which must have
     * the CSS rule 'position: relative'.
     *
     * @property container
     * @type Node
     * @protected
     */
    container: null,

    /**
     * A Node representing the header cell.
     *
     * @property headerCell
     * @type Node
     * @protected
     */
    headerCell: null,

    /**
     * A Node representing the header row.
     *
     * @property headerRow
     * @type Node
     * @protected
     */
    headerRow: null,

    /**
     * A Node representing the first cell which contains user name information.
     *
     * @property firstUserCell
     * @type Node
     * @protected
     */
    firstUserCell: null,

    /**
     * A Node representing the original table footer row.
     *
     * @property tableFooterRow
     * @type Node
     * @protected
     */
    tableFooterRow: null,

    /**
     * A Node representing the floating footer row in the grading table.
     *
     * @property footerRow
     * @type Node
     * @protected
     */
    footerRow: null,

    /**
     * A Node representing the floating grade item header.
     *
     * @property gradeItemHeadingContainer
     * @type Node
     * @protected
     */
    gradeItemHeadingContainer: null,

    /**
     * A Node representing the floating user header. This is the header with the Surname/First name
     * sorting.
     *
     * @property userColumnHeader
     * @type Node
     * @protected
     */
    userColumnHeader: null,

    /**
     * A Node representing the floating user column. This is the column containing all of the user
     * names.
     *
     * @property userColumn
     * @type Node
     * @protected
     */
    userColumn: null,

    /**
     * The position of the bottom of the first user cell.
     * This is used when processing the scroll event as an optimisation. It must be updated when
     * additional rows are loaded, or the window changes in some fashion.
     *
     * @property firstUserCellBottom
     * @type Node
     * @protected
     */
    firstUserCellBottom: 0,

    /**
     * The position of the left of the first user cell.
     * This is used when processing the scroll event as an optimisation. It must be updated when
     * additional rows are loaded, or the window changes in some fashion.
     *
     * @property firstUserCellLeft
     * @type Node
     * @protected
     */
    firstUserCellLeft: 0,

    /**
     * The position of the top of the final user cell.
     * This is used when processing the scroll event as an optimisation. It must be updated when
     * additional rows are loaded, or the window changes in some fashion.
     *
     * @property lastUserCellTop
     * @type Node
     * @protected
     */
    lastUserCellTop: 0,

    /**
     * Array of EventHandles.
     *
     * @type EventHandle[]
     * @property _eventHandles
     * @protected
     */
    _eventHandles: [],

    /**
     * Setup the grader report table.
     *
     * @method setupFloatingHeaders
     * @chainable
     */
    setupFloatingHeaders: function() {
        // Grab references to commonly used Nodes.
        this.firstUserCell = Y.one(SELECTORS.USERCELL);
        this.container = Y.one(SELECTORS.GRADEPARENT);

        if (!this.firstUserCell) {
            // No need for floating elements, there are no users.
            return this;
        }

        // Generate floating elements.
        this._setupFloatingUserColumn();
        this._setupFloatingUserHeader();
        this._setupFloatingAssignmentHeaders();
        this._setupFloatingAssignmentFooter();

        // Calculate the positions of edge cells. These are used for positioning of the floating headers.
        // This must be called after the floating headers are setup, but before the scroll event handler is invoked.
        this._calculateCellPositions();

        // Setup the floating element initial positions by simulating scroll.
        this._handleScrollEvent();

        // Setup the event handlers.
        this._setupEventHandlers();

        return this;
    },

    /**
     * Calculate the positions of some cells. These values are used heavily
     * in scroll event handling.
     *
     * @method _calculateCellPositions
     * @protected
     */
    _calculateCellPositions: function() {
        // The header row shows the grade item headers and is floated to the top of the window.
        this.headerRowTop = this.headerRow.getY();

        // The footer row shows the grade averages and will be floated to the page bottom.
        if (this.tableFooterRow) {
            this.footerRowPosition = this.tableFooterRow.getY();
        }

        var userCellList = Y.all(SELECTORS.USERCELL);

        // The left of the user cells matches the left of the headerRow.
        this.firstUserCellLeft = this.headerRow.getX();

        if (userCellList.size() > 1) {
            // Use the top of the second cell for the bottom of the first cell.
            // This is used when scrolling to fix the footer to the top edge of the window.
            var firstUserCell = userCellList.item(1);
            this.firstUserCellBottom = firstUserCell.getY() + parseInt(firstUserCell.getComputedStyle(HEIGHT), 10);

            // Use the top of the penultimate cell when scrolling the header.
            // The header is the same size as the cells.
            this.lastUserCellTop = userCellList.item(userCellList.size() - 2).getY();
        } else {
            var firstItem = userCellList.item(0);
            // We can't use the top of the second row as there is only one row.
            this.lastUserCellTop = firstItem.getY();

            if (this.tableFooterRow) {
                // The footer is present so we can use that.
                this.firstUserCellBottom = this.footerRowPosition + parseInt(this.tableFooterRow.getComputedStyle(HEIGHT), 10);
            } else {
                // No other clues - calculate the top instead.
                this.firstUserCellBottom = firstItem.getY() + firstItem.get('offsetHeight');
            }
        }

        // Check whether a header is present and whether it is floating.
        var header = Y.one('header');
        this.pageHeaderHeight = 0;
        if (header) {
            if (header.getComputedStyle('position') === 'fixed') {
                this.pageHeaderHeight = header.get(OFFSETHEIGHT);
            }
        }
    },

    /**
     * Get the relative XY of the node.
     *
     * @method _getRelativeXY
     * @protected
     * @param {Node} node The node to get the position of.
     * @return {Array} Containing X and Y.
     */
    _getRelativeXY: function(node) {
        return this._getRelativeXYFromXY(node.getX(), node.getY());
    },

    /**
     * Get the relative positioning from coordinates.
     *
     * This gives the position according to the parent of the table, which must
     * be set as position: relative.
     *
     * @method _getRelativeXYFromXY
     * @protected
     * @param {Number} x X position.
     * @param {Number} y Y position.
     * @return {Array} Containing X and Y.
     */
    _getRelativeXYFromXY: function(x, y) {
        var parentXY = this.container.getXY();
        return [Math.floor(x - parentXY[0]), Math.floor(y - parentXY[1])];
    },

    /**
     * Get the relative positioning of an elements from coordinates.
     *
     * @method _getRelativeXFromX
     * @protected
     * @param {Number} pos X position.
     * @return {Number} relative X position.
     */
    _getRelativeXFromX: function(pos) {
        return this._getRelativeXYFromXY(pos, 0)[0];
    },

    /**
     * Get the relative positioning of an elements from coordinates.
     *
     * @method _getRelativeYFromY
     * @protected
     * @param {Number} pos Y position.
     * @return {Number} relative Y position.
     */
    _getRelativeYFromY: function(pos) {
        return this._getRelativeXYFromXY(0, pos)[1];
    },

    /**
     * Return the size of the horizontal scrollbar.
     *
     * @method _getScrollBarHeight
     * @protected
     * @return {Number} Height of the scrollbar.
     */
    _getScrollBarHeight: function() {
        if (Y.UA.ie && Y.UA.ie >= 10) {
            // IE has transparent scrollbars, which sometimes disappear... it's better to ignore them.
            return 0;
        } else if (Y.config.doc.body.scrollWidth > Y.config.doc.body.clientWidth) {
            // The document can be horizontally scrolled.
            return Y.DOM.getScrollbarWidth();
        }
        return 0;
    },

    /**
     * Setup the main event listeners.
     * These deal with things like window events.
     *
     * @method _setupEventHandlers
     * @protected
     */
    _setupEventHandlers: function() {
        this._eventHandles.push(
            // Listen for window scrolls, resizes, and rotation events.
            Y.one(Y.config.win).on('scroll', this._handleScrollEvent, this),
            Y.one(Y.config.win).on('resize', this._handleResizeEvent, this),
            Y.one(Y.config.win).on('orientationchange', this._handleResizeEvent, this)
        );
    },

    /**
     * Create and setup the floating column of user names.
     *
     * @method _setupFloatingUserColumn
     * @protected
     */
    _setupFloatingUserColumn: function() {
        // Grab all cells in the user names column.
        var userColumn = Y.all(SELECTORS.USERCELL),

        // Create a floating table.
            floatingUserColumn = Y.Node.create('<div aria-hidden="true" role="presentation" id="gradebook-user-container"></div>'),

        // Get the XY for the floating element.
            coordinates = this._getRelativeXY(this.firstUserCell);

        // Generate the new fields.
        userColumn.each(function(node) {
            // Create and configure the new container.
            var containerNode = Y.Node.create('<div aria-hidden="true" class="gradebook-user-cell"></div>'),
                height,
                width;

            // IE madness...
            if (Y.UA.ie) {
                var bb = parseInt(node.getComputedStyle('borderBottomWidth'), 10),
                    bt = parseInt(node.getComputedStyle('borderTopWidth'), 10),
                    bl = parseInt(node.getComputedStyle('borderLeftWidth'), 10),
                    br = parseInt(node.getComputedStyle('borderRightWidth'), 10),
                    pb = parseInt(node.getComputedStyle('paddingBottom'), 10),
                    pt = parseInt(node.getComputedStyle('paddingTop'), 10),
                    pl = parseInt(node.getComputedStyle('paddingLeft'), 10),
                    pr = parseInt(node.getComputedStyle('paddingRight'), 10);
                height = node.get(OFFSETHEIGHT) - bb - bt - pb - pt;
                width = node.get(OFFSETWIDTH) - bl - br - pl - pr;
            } else {
                height = node.getComputedStyle(HEIGHT);
                width = node.getComputedStyle(WIDTH);
            }

            containerNode.set('innerHTML', node.get('innerHTML'))
                    .setAttribute('data-uid', node.ancestor('tr').getData('uid'))
                    .setStyles({
                        height: height,
                        width:  width
                    });

            // Add the new nodes to our floating table.
            floatingUserColumn.appendChild(containerNode);
        }, this);

        // Style the floating user container.
        floatingUserColumn.setStyles({
            left:       coordinates[0] + 'px',
            position:   'absolute',
            top:        coordinates[1] + 'px'
        });

        // Append to the grader region.
        this.graderRegion.append(floatingUserColumn);

        // Store a reference to this for later - we use it in the event handlers.
        this.userColumn = floatingUserColumn;
    },

    /**
     * Create and setup the floating username header cell.
     *
     * @method _setupFloatingUserHeader
     * @protected
     */
    _setupFloatingUserHeader: function() {
        // We make various references to the this header cell. Store it for later.
        this.headerRow = Y.one(SELECTORS.HEADERROW);
        this.headerCell = Y.one(SELECTORS.STUDENTHEADER);

        // Float the 'user name' header cell.
        var floatingUserCell = Y.Node.create('<div aria-hidden="true" role="presentation" id="gradebook-user-header-container"></div>'),
            firstUserXY = this._getRelativeXY(this.firstUserCell),
            headerXY = this._getRelativeXY(this.headerRow);

        // Append node contents
        floatingUserCell.set('innerHTML', this.headerCell.getHTML());
        floatingUserCell.setStyles({
            height:     this.headerCell.getComputedStyle(HEIGHT),
            left:       firstUserXY[0] + 'px',
            position:   'absolute',
            top:        headerXY[1] + 'px',
            width:      this.firstUserCell.getComputedStyle(WIDTH)
        });

        // Append to the grader region.
        this.graderRegion.append(floatingUserCell);

        // Store a reference to this for later - we use it in the event handlers.
        this.userColumnHeader = floatingUserCell;
    },

    /**
     * Create and setup the floating grade item header row.
     *
     * @method _setupFloatingAssignmentHeaders
     * @protected
     */
    _setupFloatingAssignmentHeaders: function() {
        this.headerRow = Y.one('#user-grades tr.heading');

        var gradeHeaders = Y.all('#user-grades tr.heading .cell');

        // Generate a floating headers
        var floatingGradeHeaders = Y.Node.create('<div aria-hidden="true" role="presentation" id="gradebook-header-container"></div>');

        var coordinates = this._getRelativeXY(this.headerRow);

        var floatingGradeHeadersWidth = 0;
        var floatingGradeHeadersHeight = 0;
        var gradeHeadersOffset = coordinates[0];

        gradeHeaders.each(function(node) {
            var nodepos = this._getRelativeXY(node)[0];

            var newnode = Y.Node.create('<div class="gradebook-header-cell"></div>');
            newnode.append(node.getHTML())
                    .addClass(node.getAttribute('class'))
                    .setData('itemid', node.getData('itemid'))
                    .setStyles({
                        height:     node.getComputedStyle(HEIGHT),
                        left:       (nodepos - gradeHeadersOffset) + 'px',
                        position:   'absolute',
                        width:      node.getComputedStyle(WIDTH)
                    });

            // Sum up total widths - these are used in the container styles.
            // Use the offsetHeight and Width here as this contains the
            // padding, margin, and borders.
            floatingGradeHeadersWidth += parseInt(node.get(OFFSETWIDTH), 10);
            floatingGradeHeadersHeight = node.get(OFFSETHEIGHT);

            // Append to our floating table.
            floatingGradeHeaders.appendChild(newnode);
        }, this);

        // Position header table.
        floatingGradeHeaders.setStyles({
            height:     floatingGradeHeadersHeight + 'px',
            left:       coordinates[0] + 'px',
            position:   'absolute',
            top:        coordinates[1] + 'px',
            width:      floatingGradeHeadersWidth + 'px'
        });

        // Insert in place before the grader headers.
        this.userColumnHeader.insert(floatingGradeHeaders, 'before');

        // Store a reference to this for later - we use it in the event handlers.
        this.gradeItemHeadingContainer = floatingGradeHeaders;
    },

    /**
     * Create and setup the floating header row of grade item titles.
     *
     * @method _setupFloatingAssignmentFooter
     * @protected
     */
    _setupFloatingAssignmentFooter: function() {
        this.tableFooterRow = Y.one('#user-grades .avg');
        if (!this.tableFooterRow) {
            Y.log('Averages footer not found - unable to float it.', 'warn', LOGNS);
            return;
        }

        // Generate the sticky footer row.
        var footerCells = this.tableFooterRow.all('.cell');

        // Create a container.
        var floatingGraderFooter = Y.Node.create('<div aria-hidden="true" role="presentation" id="gradebook-footer-container"></div>');
        var footerWidth = 0;
        var coordinates = this._getRelativeXY(this.tableFooterRow);
        var footerRowOffset = coordinates[0];

        // Copy cell content.
        footerCells.each(function(node) {
            var newnode = Y.Node.create('<div class="gradebook-footer-cell"></div>');
            var nodepos = this._getRelativeXY(node)[0];
            newnode.set('innerHTML', node.getHTML());
            newnode.setStyles({
                height:     this._getHeight(node),
                left:       (nodepos - footerRowOffset) + 'px',
                position:   'absolute',
                width:      this._getWidth(node)
            });

            floatingGraderFooter.append(newnode);
            footerWidth += parseInt(node.get(OFFSETWIDTH), 10);
        }, this);

        // Attach 'Update' button.
        var updateButton = Y.one('#gradersubmit');
        if (updateButton) {
            // TODO decide what to do with classes here to make them compatible with the base themes.
            var button = Y.Node.create('<button class="btn btn-sm btn-default">' + updateButton.getAttribute('value') + '</button>');
            button.on('click', function() {
                    updateButton.simulate('click');
            });
            floatingGraderFooter.one('.gradebook-footer-cell').append(button);
        }

        // Position the row
        floatingGraderFooter.setStyles({
            position:   'absolute',
            left:       coordinates[0] + 'px',
            bottom:     0,
            height:     this._getHeight(this.tableFooterRow),
            width:      footerWidth + 'px',
        });

        // Append to the grader region.
        this.graderRegion.append(floatingGraderFooter);

        this.footerRow = floatingGraderFooter;
    },

    /**
     * Process a Scroll Event on the window.
     *
     * @method _handleScrollEvent
     * @protected
     */
    _handleScrollEvent: function() {
        // Performance is important in this function as it is called frequently and in quick succesion.
        // To prevent layout thrashing when the DOM is repeatedly updated and queried, updated and queried,
        // updates must be batched.

        // Next do all the calculations.
        var gradeItemHeadingContainerStyles = {},
            userColumnHeaderStyles = {},
            userColumnStyles = {},
            footerStyles = {},
            coord = 0;

        // Header position.
        gradeItemHeadingContainerStyles.left = this._getRelativeXFromX(this.headerRow.getX());
        if (Y.config.win.pageYOffset + this.pageHeaderHeight > this.headerRowTop) {
            if (Y.config.win.pageYOffset + this.pageHeaderHeight < this.lastUserCellTop) {
                coord = this._getRelativeYFromY(Y.config.win.pageYOffset + this.pageHeaderHeight);
                gradeItemHeadingContainerStyles.top = coord + 'px';
                userColumnHeaderStyles.top = coord + 'px';
            } else {
                coord = this._getRelativeYFromY(this.lastUserCellTop);
                gradeItemHeadingContainerStyles.top = coord + 'px';
                userColumnHeaderStyles.top = coord + 'px';
            }
        } else {
            coord = this._getRelativeYFromY(this.headerRowTop);
            gradeItemHeadingContainerStyles.top = coord + 'px';
            userColumnHeaderStyles.top = coord + 'px';
        }

        // User column position.
        if (Y.config.win.pageXOffset > this.firstUserCellLeft) {
            coord = this._getRelativeXFromX(Y.config.win.pageXOffset);
            userColumnStyles.left = coord + 'px';
            userColumnHeaderStyles.left = coord + 'px';
        } else {
            coord = this._getRelativeXFromX(this.firstUserCellLeft);
            userColumnStyles.left = coord + 'px';
            userColumnHeaderStyles.left = coord + 'px';
        }

        // Update footer.
        if (this.footerRow) {
            footerStyles.left = this._getRelativeXFromX(this.headerRow.getX());

            // Determine whether the footer should now be shown as sticky.
            var pageHeight = Y.config.win.innerHeight,
                pageOffset = Y.config.win.pageYOffset,
                bottomScrollPosition = pageHeight - this._getScrollBarHeight() + pageOffset,
                footerRowHeight = parseInt(this.footerRow.getComputedStyle(HEIGHT), 10),
                footerBottomPosition = footerRowHeight + this.footerRowPosition;

            if (bottomScrollPosition < footerBottomPosition && bottomScrollPosition > this.firstUserCellBottom) {
                // We have not scrolled below the footer, nor above the first row.
                footerStyles.bottom = Math.ceil(footerBottomPosition - bottomScrollPosition) + 'px';
            } else {
                // The footer should not float any more.
                footerStyles.bottom = 0;
            }
        }

        // Finally, apply the styles.
        this.gradeItemHeadingContainer.setStyles(gradeItemHeadingContainerStyles);
        this.userColumnHeader.setStyles(userColumnHeaderStyles);
        this.userColumn.setStyles(userColumnStyles);
        this.footerRow.setStyles(footerStyles);
    },

    /**
     * Process a size change Event on the window.
     *
     * @method _handleResizeEvent
     * @protected
     */
    _handleResizeEvent: function() {
        // Recalculate the position of the edge cells for scroll positioning.
        this._calculateCellPositions();

        // Simulate a scroll.
        this._handleScrollEvent();

        // Resize user cells.
        var userWidth = this.firstUserCell.getComputedStyle(WIDTH);
        var userCells = Y.all(SELECTORS.USERCELL);
        this.userColumnHeader.setStyle('width', userWidth);
        this.userColumn.all('.gradebook-user-cell').each(function(cell, idx) {
            cell.setStyles({
                width: userWidth,
                height: userCells.item(idx).getComputedStyle(HEIGHT)
            });
        }, this);

        // Resize headers & footers.
        // This is an expensive operation, not expected to happen often.
        var headers = this.gradeItemHeadingContainer.all(SELECTORS.HEADERCELL);
        var resizedcells = Y.all('#user-grades .heading .cell');

        var headeroffsetleft = this.headerRow.getX();
        var newcontainerwidth = 0;
        resizedcells.each(function(cell, idx) {
            var headercell = headers.item(idx);

            newcontainerwidth += cell.get(OFFSETWIDTH);
            var styles = {
                width: cell.getComputedStyle(WIDTH),
                left: cell.getX() - headeroffsetleft + 'px'
            };
            headercell.setStyles(styles);
        });

        var footers = Y.all('#gradebook-footer-container .gradebook-footer-cell');
        if (footers.size() !== 0) {
            var resizedavgcells = Y.all('#user-grades .avg .cell');

            resizedavgcells.each(function(cell, idx) {
                var footercell = footers.item(idx);
                var styles = {
                    width: cell.getComputedStyle(WIDTH),
                    left: cell.getX() - headeroffsetleft + 'px'
                };
                footercell.setStyles(styles);
            });
        }

        this.gradeItemHeadingContainer.setStyle('width', newcontainerwidth);
    },

    /**
     * Determine the height of the specified Node.
     *
     * With IE, the height used when setting a height is the offsetHeight.
     * All other browsers set this as this inner height.
     *
     * @method _getHeight
     * @protected
     * @param {Node} node
     * @return String
     */
    _getHeight: function(node) {
        if (Y.UA.ie) {
            return node.get(OFFSETHEIGHT) + 'px';
        } else {
            return node.getComputedStyle(HEIGHT);
        }
    },

    /**
     * Determine the width of the specified Node.
     *
     * With IE, the width used when setting a width is the offsetWidth.
     * All other browsers set this as this inner width.
     *
     * @method _getWidth
     * @protected
     * @param {Node} node
     * @return String
     */
    _getWidth: function(node) {
        if (Y.UA.ie) {
            return node.get(OFFSETWIDTH) + 'px';
        } else {
            return node.getComputedStyle(WIDTH);
        }
    }
};

Y.Base.mix(Y.M.gradereport_grader.ReportTable, [FloatingHeaders]);
