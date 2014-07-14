YUI.add('moodle-gradereport_grader-gradereporttable', function (Y, NAME) {

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
 * Static header and student column for grader table.
 *
 * @package   gradereport_grader
 * @copyright 2014 UC Regents
 * @author    Alfonso Roman <aroman@oid.ucla.edu>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var LOGNS = 'moodle-gradereport_grader-gradereporttable';

M.gradereport_grader = M.gradereport_grader || {};

// Create a gradebook module
M.gradereport_grader.gradereporttable = {
    // Resuable nodes
    node_student_header_cell: {},
    node_student_cell: {},
    node_footer_row: {},
    scrollevent: function() {
        console.log('scrolll event');
    },
    // Init module
    init: function() {

        // Set up some reusable nodes.
        this.node_student_header_cell = Y.one('#studentheader');
        // First student cell.
        this.node_student_cell = Y.one('#user-grades .user.cell');
        // Averages row.
        this.node_footer_row = Y.one('#user-grades .avg');

        // Check if there are any students -- otherwise no need to do anything.
        if (this.node_student_cell) {
            // Generate floating elements.
            this.float_user_column();
            this.float_assignment_header();
            this.float_user_header();

            // Onscroll event updates all the floating header/column positions.
            var onscroll = function() {

                // Get better performance by preventing layout thrashing.  This occurs
                // when the DOM is repeatedly updated and queried for updated values.
                // 
                // To fix this, group reads and writes

                //
                // First get all the readable values needed.

                // Header row
                var headercontainer = document.getElementById('gradebook-header-container');
                var userheadercell = document.getElementById('studentheader');

                var usercolumnheader = document.getElementById('gradebook-user-header-container');

                var headercelltop = userheadercell.offsetTop + userheadercell.offsetParent.offsetTop;

                // User column
                var pageleftcutoff = window.pageXOffset;
                var firstusercell = document.querySelectorAll("#user-grades .user.cell")[0];
                var firstusercellpos = firstusercell.offsetLeft + firstusercell.offsetParent.offsetLeft;

                var usercolumn = document.getElementById('gradebook-user-container');

                // Footer row
                var lastrow = document.querySelectorAll('#user-grades .avg')[0];
                var footer, lastrowpos;

                // Check that Average footer is available.
                if (lastrow !== undefined) {
                    footer = document.getElementById('gradebook-footer-container');
                    lastrowpos = lastrow.offsetTop + lastrow.offsetParent.offsetTop;
                }

                // Viewport values
                var pageYOffset = window.pageYOffset;
                var windowInnerHeight = window.innerHeight;

                // 
                // Next do all the writing.

                // Header position
                headercontainer.style.left = userheadercell.offsetLeft + userheadercell.offsetParent.offsetLeft + 'px';
                if (pageYOffset > headercelltop) {
                    headercontainer.style.top = pageYOffset + 40 + 'px';
                    usercolumnheader.style.top = pageYOffset + 40 + 'px';
                } else {
                    headercontainer.style.top = headercelltop + 'px';
                    usercolumnheader.style.top = headercelltop + 'px';
                }

                // User column position
                if (pageleftcutoff > firstusercellpos) {
                    usercolumn.style.left = pageleftcutoff + 'px';
                    usercolumnheader.style.left = pageleftcutoff + 'px';
                } else {
                    usercolumn.style.left = firstusercellpos + 'px';
                    usercolumnheader.style.left = firstusercellpos + 'px';
                }

                // Update footer
                if (lastrow !== undefined) {
                    footer.style.left = userheadercell.offsetLeft + userheadercell.offsetParent.offsetLeft + 'px';

                    if (pageYOffset + windowInnerHeight < lastrowpos) {
                        footer.style.top = (pageYOffset + windowInnerHeight - 50) + 'px';
                        footer.classList.add('gradebook-footer-row-sticky');
                    } else {
                        footer.style.top = lastrowpos + 'px';
                        footer.classList.remove('gradebook-footer-row-sticky');
                    }
                }
            };

            // Add the floating 'average' footer if available.
            if (this.node_footer_row) {
                this.float_assignment_footer();
            }

            // Set floating element initial positions by simulating scroll.
            onscroll();

            // Generate events.
            // 
            // Use native DOM scroll & resize events instead of YUI synthetic event.
            window.onscroll = onscroll;
            window.onresize = function() {
                
                onscroll();
                
                // Resize headers & footers.
                // This is an expensive operation, not expected to happen often.
                var headers = Y.all('#gradebook-header-container .gradebook-header-cell');
                var resizedcells = Y.all('#user-grades .heading .cell');
                
                var headeroffsetleft = Y.one('#studentheader').getX();
                var newcontainerwidth = 0;
                resizedcells.each(function(cell, idx) {
                    var headercell = headers.item(idx);
                    
                    newcontainerwidth += cell.get('offsetWidth');
                    var styles = {
                        width: cell.get('offsetWidth'),
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
                            width: cell.get('offsetWidth'),
                            left: cell.getX() - headeroffsetleft + 'px'
                        };
                        footercell.setStyles(styles);
                    });
                    Y.one('#gradebook-footer-container').setStyle('width', newcontainerwidth);
                }

                Y.one('#gradebook-header-container').setStyle('width', newcontainerwidth);
                
            };
        }

        // Remove loading screen.  Need to do YUI synthetic event to trigger
        // on all browsers.
        Y.on('domready', function() {
            Y.one('.gradebook-loading-screen').remove(true);
            Y.all('#user-grades .overridden').setAttribute('aria-label', 'Overriden grade');
        });
    },
    float_user_column: function() {

        // Grab the user names column
        var user_column = Y.all('#user-grades .user.cell');

        // Generate a floating table
        var floating_user_column = Y.Node.create('<div aria-hidden="true" id="gradebook-user-container"></div>');
        var floating_user_column_height = 0;
        var user_column_offset = this.node_student_cell.getY();

        user_column.each(function(node) {

            // Create cloned node and container.
            // We'll absolutely position the container to each cell position,
            // this will guarantee that student cells are always aligned.
            var container_node = Y.Node.create('<div class="gradebook-user-cell"></div>');

            // Grab the username
            var usernamenode = node.cloneNode(true);
            container_node.append(usernamenode.getHTML());
            usernamenode = null;

            container_node.setStyles({
                'height': node.get('offsetHeight') + 'px',
                'width': node.get('offsetWidth') + 'px',
                'position': 'absolute',
                'top': (node.getY() - user_column_offset) + 'px'
            });

            floating_user_column_height += node.get('offsetHeight');
            // Retrieve the corresponding row
            var classes = node.ancestor().getAttribute('class').split(' ').join('.');
            // Attach highlight event
            container_node.on('click', function() {
                Y.one('.' + classes).all('.grade').toggleClass('hmarked');
            });
            // Add the cloned nodes to our floating table
            floating_user_column.appendChild(container_node);

        }, this);

        // Style the table
        floating_user_column.setStyles({
            'position': 'absolute',
            'left': this.node_student_cell.getX() + 'px',
            'top': this.node_student_cell.getY() + 'px',
            'width': this.node_student_cell.get('offsetWidth'),
            'height': floating_user_column_height + 'px',
            'background-color': '#f9f9f9'
        });

        Y.one('body').append(floating_user_column);
    },
    float_user_header: function() {

        // Float the 'user name' header cell.
        var floating_user_header_cell = Y.Node.create('<div aria-hidden="true" id="gradebook-user-header-container"></div>');

        // Clone the node
        var cellnode = this.node_student_header_cell.cloneNode(true);
        // Append node contents
        floating_user_header_cell.append(cellnode.getHTML());
        floating_user_header_cell.setStyles({
            'position': 'absolute',
            'left': this.node_student_cell.getX() + 'px',
            'top': this.node_student_header_cell.getY() + 'px',
            'width': '200px',
            'height': this.node_student_header_cell.get('offsetHeight') + 'px'
        });

        // Safe for collection
        cellnode = null;

        Y.one('body').append(floating_user_header_cell);
    },
    float_assignment_header: function() {

        var grade_headers = Y.all('#user-grades tr.heading .cell');

        // Generate a floating headers
        var floating_grade_headers = Y.Node.create('<div aria-hidden="true" id="gradebook-header-container"></div>');

        var floating_grade_headers_width = 0;
        var floating_grade_headers_height = 0;
        var grade_headers_offset = this.node_student_header_cell.getX();

        grade_headers.each(function(node) {

            // Get the target column to highlight.  This is embedded in
            // the column cell #, but it's off by one, so need to adjust for that.
            var col = node.getAttribute('class');

            // Extract the column #
            var search = /c[0-9]+/g;
            var match = search.exec(col);
            match = match[0].replace('c', '');

            // Offset
            var target_col = parseInt(match, 10);
            ++target_col;

            var nodepos = node.getX();

            // We need to clone the node, otherwise we mutate original obj
            var nodeclone = node.cloneNode(true);

            var newnode = Y.Node.create('<div class="gradebook-header-cell"></div>');
            newnode.append(nodeclone.getHTML());
            newnode.addClass(nodeclone.getAttribute('class'));
            nodeclone = null;

            newnode.setStyles({
                'width': node.get('offsetWidth') + 'px',
                'height': node.get('offsetHeight') + 'px',
                'position': 'absolute',
                'left': (nodepos - grade_headers_offset) + 'px'
            });

            // Sum up total width
            floating_grade_headers_width += parseInt(node.get('offsetWidth'), 10);
            floating_grade_headers_height = node.get('offsetHeight');

            // Attach 'highlight column' event to new node
            newnode.on('click', function() {
                Y.all('.cell.c' + target_col).toggleClass('vmarked');
            });

            // Append to floating table.
            floating_grade_headers.appendChild(newnode);
        }, this);

        // Position header table.
        floating_grade_headers.setStyles({
            'position': 'absolute',
            'top': this.node_student_header_cell.getY() + 'px',
            'left': this.node_student_header_cell.getX() + 'px',
            'width': floating_grade_headers_width + 'px',
            'height': floating_grade_headers_height + 'px'
        });

        Y.one('body').append(floating_grade_headers);
    },
    float_assignment_footer: function() {

        // Generate the sticky footer row.
        // Grab the row.
        var footer_row = Y.all('#user-grades .lastrow .cell');
        // Create a container.
        var floating_grade_footers = Y.Node.create('<div aria-hidden="true" id="gradebook-footer-container"></div>');
        var floating_grade_footer_width = 0;
        var footer_row_offset = this.node_footer_row.getX();
        // Copy nodes
        footer_row.each(function(node) {

            var nodepos = node.getX();
            var cellnodeclone = node.cloneNode(true);

            var newnode = Y.Node.create('<div class="gradebook-footer-cell"></div>');
            newnode.append(cellnodeclone.getHTML());
            newnode.setStyles({
                'width': node.get('offsetWidth') + 'px',
                'height': '50px',
                'position': 'absolute',
                'left': (nodepos - footer_row_offset) + 'px'
            });

            floating_grade_footers.append(newnode);
            floating_grade_footer_width += parseInt(node.get('offsetWidth'), 10);
        }, this);

        // Attach 'Update' button.
        var update_button = Y.one('#gradersubmit');
        if (update_button) {
            var button = Y.Node.create('<button class="btn btn-sm btn-default">' + update_button.getAttribute('value') + '</button>');
            button.on('click', function() {
                YUI().use('node-event-simulate', function(Y) {
                    Y.one('#gradersubmit').simulate('click');
                });
            });
            floating_grade_footers.one('.gradebook-footer-cell').append(button);
        }

        // Position the row
        floating_grade_footers.setStyles({
            'position': 'absolute',
            'left': this.node_footer_row.getX() + 'px',
            'bottom': '0',
            'height': '50px',
            'width': floating_grade_footer_width + 'px'
        });

        Y.one('body').append(floating_grade_footers);
    }
};


}, '@VERSION@');
