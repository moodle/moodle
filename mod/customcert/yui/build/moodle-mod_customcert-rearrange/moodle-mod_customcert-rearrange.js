YUI.add('moodle-mod_customcert-rearrange', function (Y, NAME) {

/**
 * Rearrange elements in the custom certificate
 *
 * @class Y.M.mod_customcert.rearrange
 * @constructor
 */
var Rearrange = function() {
    Rearrange.superclass.constructor.apply(this, [arguments]);
};
Y.extend(Rearrange, Y.Base, {

    /**
     * The template id.
     */
    templateid: 0,

    /**
     * The customcert page we are displaying.
     */
    page: [],

    /**
     * The custom certificate elements to display.
     */
    elements: [],

    /**
     * Store the X coordinates of the top left of the pdf div.
     */
    pdfx: 0,

    /**
     * Store the Y coordinates of the top left of the pdf div.
     */
    pdfy: 0,

    /**
     * Store the width of the pdf div.
     */
    pdfwidth: 0,

    /**
     * Store the height of the pdf div.
     */
    pdfheight: 0,

    /**
     * Store the location of the element before we move.
     */
    elementxy: 0,

    /**
     * Store the left boundary of the pdf div.
     */
    pdfleftboundary: 0,

    /**
     * Store the right boundary of the pdf div.
     */
    pdfrightboundary: 0,

    /**
     * The number of pixels in a mm.
     */
    pixelsinmm: 3.779527559055, // 3.779528.

    /**
     * Initialise.
     *
     * @param {Array} params
     */
    initializer: function(params) {
        // Set the course module id.
        this.templateid = params[0];
        // Set the page.
        this.page = params[1];
        // Set the elements.
        this.elements = params[2];

        // Set the PDF dimensions.
        this.setPdfDimensions();

        // Set the boundaries.
        this.setBoundaries();

        this.setpositions();
        this.createevents();
        window.addEventListener("resize", this.checkWindownResize.bind(this));
    },

    /**
     * Sets the current position of the elements.
     */
    setpositions: function() {
        // Go through the elements and set their positions.
        for (var key in this.elements) {
            var element = this.elements[key];
            var posx = this.pdfx + element.posx * this.pixelsinmm;
            var posy = this.pdfy + element.posy * this.pixelsinmm;
            var nodewidth = parseFloat(Y.one('#element-' + element.id).getComputedStyle('width'));
            var maxwidth = element.width * this.pixelsinmm;

            if (maxwidth && (nodewidth > maxwidth)) {
                nodewidth = maxwidth;
            }

            switch (element.refpoint) {
                case '1': // Top-center.
                    posx -= nodewidth / 2;
                    break;
                case '2': // Top-right.
                    posx = posx - nodewidth + 2;
                    break;
            }

            Y.one('#element-' + element.id).setX(posx);
            Y.one('#element-' + element.id).setY(posy);
        }
    },

    /**
     * Sets the PDF dimensions.
     */
    setPdfDimensions: function() {
        this.pdfx = Y.one('#pdf').getX();
        this.pdfy = Y.one('#pdf').getY();
        this.pdfwidth = parseFloat(Y.one('#pdf').getComputedStyle('width'));
        this.pdfheight = parseFloat(Y.one('#pdf').getComputedStyle('height'));
    },

    /**
     * Sets the boundaries.
     */
    setBoundaries: function() {
        this.pdfleftboundary = this.pdfx;
        if (this.page.leftmargin) {
            this.pdfleftboundary += parseInt(this.page.leftmargin * this.pixelsinmm, 10);
        }

        this.pdfrightboundary = this.pdfx + this.pdfwidth;
        if (this.page.rightmargin) {
            this.pdfrightboundary -= parseInt(this.page.rightmargin * this.pixelsinmm, 10);
        }
    },

    /**
     * Check browser resize and reset position.
     */
    checkWindownResize: function() {
        this.setPdfDimensions();
        this.setBoundaries();
        this.setpositions();
    },

    /**
     * Creates the JS events for changing element positions.
     */
    createevents: function() {
        // Trigger a save event when save button is pushed.
        Y.one('.savepositionsbtn [type=submit]').on('click', function(e) {
            this.savepositions(e);
        }, this);

        // Trigger a save event when apply button is pushed.
        Y.one('.applypositionsbtn [type=submit]').on('click', function(e) {
            this.savepositions(e);
            e.preventDefault();
        }, this);

        // Define the container and the elements that are draggable.
        var del = new Y.DD.Delegate({
            container: '#pdf',
            nodes: '.element'
        });

        // When we start dragging keep track of it's position as we may set it back.
        del.on('drag:start', function() {
            var node = del.get('currentNode');
            this.elementxy = node.getXY();
        }, this);

        // When we finish the dragging action check that the node is in bounds,
        // if not, set it back to where it was.
        del.on('drag:end', function() {
            var node = del.get('currentNode');
            if (this.isoutofbounds(node)) {
                node.setXY(this.elementxy);
            }
        }, this);
    },

    /**
     * Returns true if any part of the element is placed outside of the PDF div, false otherwise.
     *
     * @param {Node} node
     * @returns {boolean}
     */
    isoutofbounds: function(node) {
        // Get the width and height of the node.
        var nodewidth = parseFloat(node.getComputedStyle('width'));
        var nodeheight = parseFloat(node.getComputedStyle('height'));

        // Store the positions of each edge of the node.
        var left = node.getX();
        var right = left + nodewidth;
        var top = node.getY();
        var bottom = top + nodeheight;

        this.pdfx = Y.one('#pdf').getX();
        this.pdfy = Y.one('#pdf').getY();

        // Check if it is out of bounds horizontally.
        if ((left < this.pdfleftboundary) || (right > this.pdfrightboundary)) {
            return true;
        }

        // Check if it is out of bounds vertically.
        if ((top < this.pdfy) || (bottom > (this.pdfy + this.pdfheight))) {
            return true;
        }

        return false;
    },

    /**
     * Perform an AJAX call and save the positions of the elements.
     *
     * @param {Event} e
     */
    savepositions: function(e) {
        // The parameters to send the AJAX call.
        var params = {
            tid: this.templateid,
            values: []
        };

        this.pdfx = Y.one("#pdf").getX();
        this.pdfy = Y.one("#pdf").getY();

        // Go through the elements and save their positions.
        for (var key in this.elements) {
            var element = this.elements[key];
            var node = Y.one('#element-' + element.id);

            // Get the current X and Y positions and refpoint for this element.
            var posx = node.getX() - this.pdfx;
            var posy = node.getY() - this.pdfy;
            var refpoint = node.getData('refpoint');

            var nodewidth = parseFloat(node.getComputedStyle('width'));

            switch (refpoint) {
                case '1': // Top-center.
                    posx += nodewidth / 2;
                    break;
                case '2': // Top-right.
                    posx += nodewidth;
                    break;
            }

            // Set the parameters to pass to the AJAX request.
            params.values.push({
                id: element.id,
                posx: Math.round(parseFloat(posx / this.pixelsinmm)),
                posy: Math.round(parseFloat(posy / this.pixelsinmm))
            });
        }

        params.values = JSON.stringify(params.values);

        // Save these positions.
        Y.io(M.cfg.wwwroot + '/mod/customcert/ajax.php', {
            method: 'POST',
            data: params,
            on: {
                failure: function(tid, response) {
                    this.ajaxfailure(response);
                },
                success: function() {
                    var formnode = e.currentTarget.ancestor('form', true);
                    var baseurl = formnode.getAttribute('action');
                    var pageinput = formnode.one('[name=pid]');
                    if (pageinput) {
                        var pageid = pageinput.get('value');
                        window.location = baseurl + '?pid=' + pageid;
                    } else {
                        var templateid = formnode.one('[name=tid]').get('value');
                        window.location = baseurl + '?tid=' + templateid;
                    }
                }
            },
            context: this
        });

        e.preventDefault();
    },

    /**
     * Handles any failures during an AJAX call.
     *
     * @param {XMLHttpRequest} response
     * @returns {M.core.exception}
     */
    ajaxfailure: function(response) {
        var e = {
            name: response.status + ' ' + response.statusText,
            message: response.responseText
        };
        return new M.core.exception(e);
    }
});

Y.namespace('M.mod_customcert.rearrange').init = function(templateid, page, elements) {
    new Rearrange(templateid, page, elements);
};


}, '@VERSION@', {"requires": ["dd-delegate", "dd-drag"]});
