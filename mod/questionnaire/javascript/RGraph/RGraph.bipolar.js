    /**
    * o-------------------------------------------------------------------------------o
    * | This file is part of the RGraph package. RGraph is Free software, licensed    |
    * | under the MIT license - so it's free to use for all purposes. Extended        |
    * | support is available if required and donations are always welcome! You can    |
    * | read more here:                                                               |
    * |                         http://www.rgraph.net/support                         |
    * o-------------------------------------------------------------------------------o
    */

    if (typeof(RGraph) == 'undefined') RGraph = {};

    /**
    * The bi-polar/age frequency constructor.
    * 
    * @param string id The id of the canvas
    * @param array  left  The left set of data points
    * @param array  right The right set of data points
    * 
    * REMEMBER If ymin is implemented you need to update the .getValue() method
    */
    RGraph.Bipolar = function (id, left, right)
    {
        // Get the canvas and context objects
        this.id                = id;
        this.canvas            = document.getElementById(typeof id === 'object' ? id.id : id);
        this.context           = this.canvas.getContext('2d');
        this.canvas.__object__ = this;
        this.type              = 'bipolar';
        this.coords            = [];
        this.coordsLeft        = [];
        this.coordsRight       = [];
        this.max               = 0;
        this.isRGraph          = true;
        this.uid               = RGraph.CreateUID();
        this.canvas.uid        = this.canvas.uid ? this.canvas.uid : RGraph.CreateUID();
        this.coordsText        = [];


        /**
        * Compatibility with older browsers
        */
        RGraph.OldBrowserCompat(this.context);

        
        // The left and right data respectively
        this.left       = left;
        this.right      = right;
        this.data       = [left, right];

        this.properties = {
            'chart.margin':                 2,
            'chart.xtickinterval':          null,
            'chart.labels':                 [],
            'chart.labels.above':           false,
            'chart.text.size':              10,
            'chart.text.color':             'black', // (Simple) gradients are not supported
            'chart.text.font':              'Arial',
            'chart.title.left':             '',
            'chart.title.right':            '',
            'chart.gutter.center':          60,
            'chart.gutter.left':            25,
            'chart.gutter.right':           25,
            'chart.gutter.top':             25,
            'chart.gutter.bottom':          25,
            'chart.title':                  '',
            'chart.title.background':       null,
            'chart.title.hpos':             null,
            'chart.title.vpos':             null,
            'chart.title.bold':             true,
            'chart.title.font':             null,
            'chart.title.x':                null,
            'chart.title.y':                null,
            'chart.title.halign':           null,
            'chart.title.valign':           null,
            'chart.colors':                 ['#0f0'],
            'chart.contextmenu':            null,
            'chart.tooltips':               null,
            'chart.tooltips.effect':         'fade',
            'chart.tooltips.css.class':      'RGraph_tooltip',
            'chart.tooltips.highlight':     true,
            'chart.tooltips.event':         'onclick',
            'chart.highlight.stroke':       'rgba(0,0,0,0)',
            'chart.highlight.fill':         'rgba(255,255,255,0.7)',
            'chart.units.pre':              '',
            'chart.units.post':             '',
            'chart.shadow':                 false,
            'chart.shadow.color':           '#666',
            'chart.shadow.offsetx':         3,
            'chart.shadow.offsety':         3,
            'chart.shadow.blur':            3,
            'chart.annotatable':            false,
            'chart.annotate.color':         'black',
            'chart.xmax':                   null,
            'chart.xmin':                   0,
            'chart.scale.decimals':         null,
            'chart.scale.point':            '.',
            'chart.scale.thousand':         ',',
            'chart.axis.color':             'black',
            'chart.zoom.factor':            1.5,
            'chart.zoom.fade.in':           true,
            'chart.zoom.fade.out':          true,
            'chart.zoom.hdir':              'right',
            'chart.zoom.vdir':              'down',
            'chart.zoom.frames':            25,
            'chart.zoom.delay':             16.666,
            'chart.zoom.shadow':            true,
            'chart.zoom.background':        true,
            'chart.zoom.action':            'zoom',
            'chart.resizable':              false,
            'chart.resize.handle.background': null,
            'chart.strokestyle':            'rgba(0,0,0,0)',
            'chart.events.mousemove':       null,
            'chart.events.click':           null,
            'chart.linewidth':              1,
            'chart.noaxes':                 false,
            'chart.xlabels':                true,
            'chart.numyticks':              null,
            'chart.numxticks':              5,
            'chart.axis.linewidth':         1,
            'chart.labels.count':           5
        }

        // Pad the arrays so they're the same size
        while (this.left.length < this.right.length) this.left.push(0);
        while (this.left.length > this.right.length) this.right.push(0);
        
        /**
        * Set the default for the number of Y tickmarks
        */
        this.properties['chart.numyticks'] = this.left.length;

        


        /**
        * Create the dollar objects so that functions can be added to them
        */
        var linear_data = RGraph.array_linearize(this.left, this.right);
        for (var i=0; i<linear_data.length; ++i) {
            this['$' + i] = {};
        }


        /**
        * Translate half a pixel for antialiasing purposes - but only if it hasn't beeen
        * done already
        */
        if (!this.canvas.__rgraph_aa_translated__) {
            this.context.translate(0.5,0.5);
            
            this.canvas.__rgraph_aa_translated__ = true;
        }




        ///////////////////////////////// SHORT PROPERTIES /////////////////////////////////




        var RG   = RGraph;
        var ca   = this.canvas;
        var co   = ca.getContext('2d');
        var prop = this.properties;
        //var $jq  = jQuery;




        //////////////////////////////////// METHODS ///////////////////////////////////////




        /**
        * The setter
        * 
        * @param name  string The name of the parameter to set
        * @param value mixed  The value of the paraneter 
        */
        this.Set = function (name, value)
        {
            name = name.toLowerCase();
    
            /**
            * This should be done first - prepend the propertyy name with "chart." if necessary
            */
            if (name.substr(0,6) != 'chart.') {
                name = 'chart.' + name;
            }
    
            prop[name] = value;
    
            return this;
        }




        /**
        * The getter
        * 
        * @param name string The name of the parameter to get
        */
        this.Get = function (name)
        {
            /**
            * This should be done first - prepend the property name with "chart." if necessary
            */
            if (name.substr(0,6) != 'chart.') {
                name = 'chart.' + name;
            }
    
            return this.properties[name.toLowerCase()];
        }




        /**
        * Draws the graph
        */
        this.Draw = function ()
        {
            /**
            * Fire the onbeforedraw event
            */
            RG.FireCustomEvent(this, 'onbeforedraw');
    
    
            /**
            * Parse the colors. This allows for simple gradient syntax
            */
            if (!this.colorsParsed) {
                this.parseColors();
                
                // Don't want to do this again
                this.colorsParsed = true;
            }
    
    
            /**
            * This is new in May 2011 and facilitates indiviual gutter settings,
            * eg chart.gutter.left
            */
            this.gutterLeft   = prop['chart.gutter.left'];
            this.gutterRight  = prop['chart.gutter.right'];
            this.gutterTop    = prop['chart.gutter.top'];
            this.gutterBottom = prop['chart.gutter.bottom'];
            
    
    
            // Reset the data to what was initially supplied
            this.left  = this.data[0];
            this.right = this.data[1];
            
            // Sequential color index
            this.sequentialColorIndex = 0;
    
    
            /**
            * Reset the coords array
            */
            this.coords = [];
    
            this.GetMax();
            this.DrawAxes();
            this.DrawTicks();
            this.DrawLeftBars();
            this.DrawRightBars();
            
            // Redraw the bars so that shadows on not on top
            this.RedrawBars();
            
            this.DrawAxes();
    
            this.DrawLabels();
            this.DrawTitles();
    
    
            /**
            * Setup the context menu if required
            */
            if (prop['chart.contextmenu']) {
                RG.ShowContext(this);
            }
    
            
            /**
            * This function enables resizing
            */
            if (prop['chart.resizable']) {
                RG.AllowResizing(this);
            }
    
    
            /**
            * This installs the event listeners
            */
            RG.InstallEventListeners(this);
    
    
            /**
            * Fire the RGraph ondraw event
            */
            RG.FireCustomEvent(this, 'ondraw');
            
            return this;
        }




        /**
        * Draws the axes
        */
        this.DrawAxes = function ()
        {
            // Set the linewidth
            co.lineWidth = prop['chart.axis.linewidth'] + 0.001;
    
    
            // Draw the left set of axes
            co.beginPath();
            co.strokeStyle = prop['chart.axis.color'];
    
            this.axisWidth  = (ca.width - prop['chart.gutter.center'] - this.gutterLeft - this.gutterRight) / 2;
            this.axisHeight = ca.height - this.gutterTop - this.gutterBottom;
            
            
            // This must be here so that the two above variables are calculated
            if (prop['chart.noaxes']) {
                return;
            }
    
            co.moveTo(this.gutterLeft, Math.round( ca.height - this.gutterBottom));
            co.lineTo(this.gutterLeft + this.axisWidth, Math.round( ca.height - this.gutterBottom));
            
            co.moveTo(Math.round( this.gutterLeft + this.axisWidth), ca.height - this.gutterBottom);
            co.lineTo(Math.round( this.gutterLeft + this.axisWidth), this.gutterTop);
            
            co.stroke();
    
    
            // Draw the right set of axes
            co.beginPath();
    
            var x = this.gutterLeft + this.axisWidth + prop['chart.gutter.center'];
            
            co.moveTo(Math.round( x), this.gutterTop);
            co.lineTo(Math.round( x), ca.height - this.gutterBottom);
            
            co.moveTo(Math.round( x), Math.round( ca.height - this.gutterBottom));
            co.lineTo(ca.width - this.gutterRight, Math.round( ca.height - this.gutterBottom));
    
            co.stroke();
        }




        /**
        * Draws the tick marks on the axes
        */
        this.DrawTicks = function ()
        {
            // Set the linewidth
            co.lineWidth = prop['chart.axis.linewidth'] + 0.001;
    
            var numDataPoints = this.left.length;
            var barHeight     = ( (ca.height - this.gutterTop - this.gutterBottom)- (this.left.length * (prop['chart.margin'] * 2) )) / numDataPoints;
    
            // Store this for later
            this.barHeight = barHeight;
    
            // If no axes - no tickmarks
            if (prop['chart.noaxes']) {
                return;
            }
    
            // Draw the left Y tick marks
            if (prop['chart.numyticks'] > 0) {
                co.beginPath();
                    for (var i=0; i<prop['chart.numyticks']; ++i) {
                        var y = prop['chart.gutter.top'] + (((ca.height - this.gutterTop - this.gutterBottom) / prop['chart.numyticks']) * i);
                        co.moveTo(this.gutterLeft + this.axisWidth , y);
                        co.lineTo(this.gutterLeft + this.axisWidth + 3, y);
                    }
                co.stroke();
    
                //Draw the right axis Y tick marks
                co.beginPath();
                    for (var i=0; i<prop['chart.numyticks']; ++i) {
                        var y = prop['chart.gutter.top'] + (((ca.height - this.gutterTop - this.gutterBottom) / prop['chart.numyticks']) * i);
                        co.moveTo(this.gutterLeft + this.axisWidth + prop['chart.gutter.center'], y);
                        co.lineTo(this.gutterLeft + this.axisWidth + prop['chart.gutter.center'] - 3, y);
                    }
                co.stroke();
            }
            
            
            
            /**
            * X tickmarks
            */
            if (prop['chart.numxticks'] > 0) {
                var xInterval = this.axisWidth / prop['chart.numxticks'];
        
                // Is chart.xtickinterval specified ? If so, use that.
                if (typeof(prop['chart.xtickinterval']) == 'number') {
                    xInterval = prop['chart.xtickinterval'];
                }
        
                
                // Draw the left sides X tick marks
                for (i=this.gutterLeft; i<(this.gutterLeft + this.axisWidth); i+=xInterval) {
                    co.beginPath();
                    co.moveTo(Math.round( i), ca.height - this.gutterBottom);
                    co.lineTo(Math.round( i), (ca.height - this.gutterBottom) + 4);
                    co.closePath();
                    
                    co.stroke();
                }
        
                // Draw the right sides X tick marks
                var stoppingPoint = ca.width - this.gutterRight;
        
                for (i=(this.gutterLeft + this.axisWidth + prop['chart.gutter.center'] + xInterval); i<=stoppingPoint; i+=xInterval) {
                    co.beginPath();
                        co.moveTo(Math.round(i), ca.height - this.gutterBottom);
                        co.lineTo(Math.round(i), (ca.height - this.gutterBottom) + 4);
                    co.closePath();
                    
                    co.stroke();
                }
            }
        }




        /**
        * Figures out the maximum value, or if defined, uses xmax
        */
        this.GetMax = function()
        {
            var dec  = prop['chart.scale.decimals'];
            
            // chart.xmax defined
            if (prop['chart.xmax']) {
    
                var max = prop['chart.xmax'];
                var min = prop['chart.xmin'];
    
                this.scale2 = RG.getScale2(this, {
                                                      'max':max,
                                                      'min':min,
                                                      'strict': true,
                                                      'scale.thousand':prop['chart.scale.thousand'],
                                                      'scale.point':prop['chart.scale.point'],
                                                      'scale.decimals':prop['chart.scale.decimals'],
                                                      'ylabels.count':prop['chart.labels.count'],
                                                      'scale.round':prop['chart.scale.round'],
                                                      'units.pre': prop['chart.units.pre'],
                                                      'units.post': prop['chart.units.post']
                                                     });
                this.max = this.scale2.max;
                this.min = this.scale2.min;
    
    
            /**
            * Generate the scale ourselves
            */
            } else {
    
                var max = Math.max(RG.array_max(this.left), RG.array_max(this.right));
    
                this.scale2 = RG.getScale2(this, {
                                                      'max':max,
                                                      //'strict': true,
                                                      'min':prop['chart.xmin'],
                                                      'scale.thousand':prop['chart.scale.thousand'],
                                                      'scale.point':prop['chart.scale.point'],
                                                      'scale.decimals':prop['chart.scale.decimals'],
                                                      'ylabels.count':prop['chart.labels.count'],
                                                      'scale.round':prop['chart.scale.round'],
                                                      'units.pre': prop['chart.units.pre'],
                                                      'units.post': prop['chart.units.post']
                                                     });
    
    
                this.max = this.scale2.max;
                this.min = this.scale2.min;
            }
    
            // Don't need to return it as it is stored in this.max
        }




        /**
        * Function to draw the left hand bars
        */
        this.DrawLeftBars = function ()
        {
            // Set the stroke colour
            co.strokeStyle = prop['chart.strokestyle'];
            
            // Set the linewidth
            co.lineWidth = prop['chart.linewidth'];
    
            for (i=0; i<this.left.length; ++i) {

                /**
                * Turn on a shadow if requested
                */
                if (prop['chart.shadow']) {
                    co.shadowColor   = prop['chart.shadow.color'];
                    co.shadowBlur    = prop['chart.shadow.blur'];
                    co.shadowOffsetX = prop['chart.shadow.offsetx'];
                    co.shadowOffsetY = prop['chart.shadow.offsety'];
                }
    
                co.beginPath();
                    
                    // If chart.colors.sequential is specified - handle that
                    if (prop['chart.colors.sequential']) {
                        co.fillStyle = prop['chart.colors'][this.sequentialColorIndex];
                        this.sequentialColorIndex++;

                    } else {
                        co.fillStyle = prop['chart.colors'][0];
                    }
                    
                    /**
                    * Work out the coordinates
                    */
    
                    var width = (( (this.left[i] - this.min) / (this.max - this.min)) *  this.axisWidth);
    
                    var coords = [Math.round( this.gutterLeft + this.axisWidth - width),
                                  Math.round( this.gutterTop + (i * ( this.axisHeight / this.left.length)) + prop['chart.margin']),
                                  width,
                                  this.barHeight];
    
                    // Draw the IE shadow if necessary
                    if (ISOLD && prop['chart.shadow']) {
                        this.DrawIEShadow(coords);
                    }
        
                    
                    if (this.left[i]) {
                        co.strokeRect(coords[0], coords[1], coords[2], coords[3]);
                        co.fillRect(coords[0], coords[1], coords[2], coords[3]);
                    }
    
                co.stroke();
                co.fill();
    
                /**
                * Add the coordinates to the coords array
                */
                this.coords.push([coords[0],coords[1],coords[2],coords[3]]);
                this.coordsLeft.push([coords[0],coords[1],coords[2],coords[3]]);
            }
    
            /**
            * Turn off any shadow
            */
            RG.NoShadow(this);
            
            // Reset the linewidth
            co.lineWidth = 1;
        }




        /**
        * Function to draw the right hand bars
        */
        this.DrawRightBars = function ()
        {
            // Set the stroke colour
            co.strokeStyle = prop['chart.strokestyle'];
            
            // Set the linewidth
            co.lineWidth = prop['chart.linewidth'];
                
            /**
            * Turn on a shadow if requested
            */
            if (prop['chart.shadow']) {
                co.shadowColor   = prop['chart.shadow.color'];
                co.shadowBlur    = prop['chart.shadow.blur'];
                co.shadowOffsetX = prop['chart.shadow.offsetx'];
                co.shadowOffsetY = prop['chart.shadow.offsety'];
            }
    
            for (var i=0; i<this.right.length; ++i) {
    
                co.beginPath();
    
                    // If chart.colors.sequential is specified - handle that
                    if (prop['chart.colors.sequential']) {
                        co.fillStyle = prop['chart.colors'][this.sequentialColorIndex++];
                    } else {
                        co.fillStyle = prop['chart.colors'][0];
                    }
        
        
                    var width = (((this.right[i] - this.min) / (this.max - this.min)) * this.axisWidth);
    
                    var coords = [
                                  Math.round( this.gutterLeft + this.axisWidth + prop['chart.gutter.center']),
                                  Math.round( prop['chart.margin'] + (i * (this.axisHeight / this.right.length)) + this.gutterTop),
                                  width,
                                  this.barHeight
                                ];
        
                        // Draw the IE shadow if necessary
                        if (ISOLD && prop['chart.shadow']) {
                            this.DrawIEShadow(coords);
                        }
                    if (this.right[i]) {
                        co.strokeRect(Math.round( coords[0]), Math.round( coords[1]), coords[2], coords[3]);
                        co.fillRect(Math.round( coords[0]), Math.round( coords[1]), coords[2], coords[3]);
                    }
    
                co.closePath();
            
                /**
                * Add the coordinates to the coords array
                */
                this.coords.push([coords[0],coords[1],coords[2],coords[3]]);
                this.coordsRight.push([coords[0],coords[1],coords[2],coords[3]]);
            }
            
            co.stroke();
    
            /**
            * Turn off any shadow
            */
            RG.NoShadow(this);
            
            // Reset the linewidth
            co.lineWidth = 1;
        }




        /**
        * Draws the titles
        */
        this.DrawLabels = function ()
        {
            co.fillStyle = prop['chart.text.color'];

            //var labelPoints = new Array();
            var font   = prop['chart.text.font'];
            var size   = prop['chart.text.size'];
            var labels = prop['chart.labels'];
            var barAreaHeight = ca.height - this.gutterTop - this.gutterBottom;
            
            for (var i=0,len=labels.length; i<len; i+=1) {
                RG.Text2(this, {'font':font,
                                'size':size,
                                'x':this.gutterLeft + this.axisWidth + (prop['chart.gutter.center'] / 2),
                                'y':this.gutterTop + ((barAreaHeight / labels.length) * (i)) + ((barAreaHeight / labels.length) / 2),
                                'text':String(labels[i] ? String(labels[i]) : ''),
                                'halign':'center',
                                'valign':'center',
                                'marker':false,
                                'tag': 'labels'
                               });
            }
            
/*
* OLD STYLE LABELS
* 
            var max = Math.max(this.left.length, this.right.length);
            
            for (i=0; i<max; ++i) {
                var barAreaHeight = ca.height - this.gutterTop - this.gutterBottom;
                var barHeight     = barAreaHeight / this.left.length;
                var yPos          = (i * barAreaHeight) + this.gutterTop;
    
                labelPoints.push(this.gutterTop + (i * barHeight) + (barHeight / 2) + 5);
            }
    
            for (i=0; i<labelPoints.length; ++i) {
    
                RG.Text2(this, {'font':prop['chart.text.font'],
                                    'size':prop['chart.text.size'],
                                    'x':this.gutterLeft + this.axisWidth + (prop['chart.gutter.center'] / 2),
                                    'y':labelPoints[i],
                                    'text':String(prop['chart.labels'][i] ? prop['chart.labels'][i] : ''),
                                    'halign':'center',
                                    'tag': 'labels'
                                   });
            }
*/




            if (prop['chart.xlabels']) {
            
                var grapharea = (ca.width - prop['chart.gutter.center'] - this.gutterLeft - this.gutterRight) / 2;

                // Now draw the X labels for the left hand side
                for (var i=0; i<this.scale2.labels.length; ++i) {
                    RG.Text2(this, {'font':font,
                                        'size':size,
                                        'x':this.gutterLeft + ((grapharea / this.scale2.labels.length) * i),
                                        'y':ca.height - this.gutterBottom + 3,
                                        'text':this.scale2.labels[this.scale2.labels.length - i - 1],
                                        'valign':'top',
                                        'halign':'center',
                                        'tag': 'scale'
                                       });
                    
                    // Draw the scale for the right hand side
                    RG.Text2(this, {'font':font,
                                        'size':size,
                                        'x':this.gutterLeft+ grapharea + prop['chart.gutter.center'] + ((grapharea / this.scale2.labels.length) * (i + 1)),
                                        'y':ca.height - this.gutterBottom + 3,
                                        'text':this.scale2.labels[i],
                                        'valign':'top',
                                        'halign':'center',
                                        'tag': 'scale'
                                       });
                }
            }
            
            /**
            * Draw above labels
            */
            if (prop['chart.labels.above']) {
                
                // Draw the left sides above labels
                for (var i=0; i<this.coordsLeft.length; ++i) {
    
                    if (typeof(this.left[i]) != 'number') {
                        continue;
                    }
    
                    var coords = this.coordsLeft[i];
                    RG.Text2(this, {'font':font,
                                        'size':size,
                                        'x':coords[0] - 5,
                                        'y':coords[1] + (coords[3] / 2),
                                        'text':RG.number_format(this, this.left[i], prop['chart.units.pre'], prop['chart.units.post']),
                                        'valign':'center',
                                        'halign':'right',
                                        'tag':'labels.above'
                                       });
                }
                
                // Draw the right sides above labels
                for (i=0; i<this.coordsRight.length; ++i) {
    
                    if (typeof(this.right[i]) != 'number') {
                        continue;
                    }
    
                    var coords = this.coordsRight[i];
                    RG.Text2(this, {'font':font,
                                        'size':size,
                                        'x':coords[0] + coords[2] +  5,
                                        'y':coords[1] + (coords[3] / 2),
                                        'text':RG.number_format(this, this.right[i], prop['chart.units.pre'], prop['chart.units.post']),
                                        'valign':'center',
                                        'halign':'left',
                                        'tag': 'labels.above'
                                       });
                }
            }
        }




        /**
        * Draws the titles
        */
        this.DrawTitles = function ()
        {
            RG.Text2(this, {'font':prop['chart.text.font'],
                         'size':prop['chart.text.size'],
                         'x':this.gutterLeft + 5,
                         'y':this.gutterTop - 5,
                         'text':String(prop['chart.title.left']),
                         'halign':'left',
                         'valign':'bottom',
                         'tag': 'title.left'
                        });
    
            RG.Text2(this, {'font':prop['chart.text.font'],
                         'size':prop['chart.text.size'],
                         'x': ca.width - this.gutterRight - 5,
                         'y':this.gutterTop - 5,
                         'text':String(prop['chart.title.right']),
                         'halign':'right',
                         'valign':'bottom',
                         'tag': 'title.right'
                        });
    
    
            
            // Draw the main title for the whole chart
            RG.DrawTitle(this, prop['chart.title'], this.gutterTop, null, prop['chart.title.size'] ? prop['chart.title.size'] : null);
        }




        /**
        * This function is used by MSIE only to manually draw the shadow
        * 
        * @param array coords The coords for the bar
        */
        this.DrawIEShadow = function (coords)
        {
            var prevFillStyle = co.fillStyle;
            var offsetx = prop['chart.shadow.offsetx'];
            var offsety = prop['chart.shadow.offsety'];
            
            co.lineWidth = prop['chart.linewidth'];
            co.fillStyle = prop['chart.shadow.color'];
            co.beginPath();
            
            // Draw shadow here
            co.fillRect(coords[0] + offsetx, coords[1] + offsety, coords[2],coords[3]);
    
            co.fill();
            
            // Change the fillstyle back to what it was
            co.fillStyle = prevFillStyle;
        }




        /**
        * Returns the appropriate focussed bar coordinates
        * 
        * @param e object The event object
        */
        this.getShape = 
        this.getBar = function (e)
        {
            var canvas      = this.canvas;
            var context     = this.context;
            var mouseCoords = RG.getMouseXY(e);
    
            /**
            * Loop through the bars determining if the mouse is over a bar
            */
            for (var i=0; i<this.coords.length; i++) {
    
                var mouseX = mouseCoords[0];
                var mouseY = mouseCoords[1];
                var left   = this.coords[i][0];
                var top    = this.coords[i][1];
                var width  = this.coords[i][2];
                var height = this.coords[i][3];
    
                if (mouseX >= left && mouseX <= (left + width) && mouseY >= top && mouseY <= (top + height) ) {
                
                    var tooltip = RG.parseTooltipText(prop['chart.tooltips'], i);
    
                    return {
                            0: this,1: left,2: top,3: width,4: height,5: i,
                            'object': this, 'x': left, 'y': top, 'width': width, 'height': height, 'index': i, 'tooltip': tooltip
                           };
                }
            }
    
            return null;
        }




        /**
        * Each object type has its own Highlight() function which highlights the appropriate shape
        * 
        * @param object shape The shape to highlight
        */
        this.Highlight = function (shape)
        {
            // Add the new highlight
            RG.Highlight.Rect(this, shape);
        }




        /**
        * When you click on the canvas, this will return the relevant value (if any)
        * 
        * REMEMBER This function will need updating if the Bipolar ever gets chart.ymin
        * 
        * @param object e The event object
        */
        this.getValue = function (e)
        {
            var obj     = e.target.__object__;
            var mouseXY = RG.getMouseXY(e);
            var mouseX  = mouseXY[0];
            
            /**
            * Left hand side
            */
            if (mouseX > this.gutterLeft && mouseX < ( (ca.width / 2) - (prop['chart.gutter.center'] / 2) )) {
                var value = (mouseX - prop['chart.gutter.left']) / this.axisWidth;
                    value = this.max - (value * this.max);
            }
            
            /**
            * Right hand side
            */
            if (mouseX < (ca.width -  this.gutterRight) && mouseX > ( (ca.width / 2) + (prop['chart.gutter.center'] / 2) )) {
                var value = (mouseX - prop['chart.gutter.left'] - this.axisWidth - prop['chart.gutter.center']) / this.axisWidth;
                    value = (value * this.max);
            }
            
            return value;
        }




        /**
        * The getObjectByXY() worker method. Don't call this call:
        * 
        * RGraph.ObjectRegistry.getObjectByXY(e)
        * 
        * @param object e The event object
        */
        this.getObjectByXY = function (e)
        {
            var mouseXY = RG.getMouseXY(e);
    
            if (
                   mouseXY[0] > prop['chart.gutter.left']
                && mouseXY[0] < (ca.width - prop['chart.gutter.right'])
                && mouseXY[1] > prop['chart.gutter.top']
                && mouseXY[1] < (ca.height - prop['chart.gutter.bottom'])
                ) {
    
                return this;
            }
        }




        /**
        * This function positions a tooltip when it is displayed
        * 
        * @param obj object    The chart object
        * @param int x         The X coordinate specified for the tooltip
        * @param int y         The Y coordinate specified for the tooltip
        * @param objec tooltip The tooltips DIV element
        */
        this.positionTooltip = function (obj, x, y, tooltip, idx)
        {
            var coordX     = obj.coords[tooltip.__index__][0];
            var coordY     = obj.coords[tooltip.__index__][1];
            var coordW     = obj.coords[tooltip.__index__][2];
            var coordH     = obj.coords[tooltip.__index__][3];
            var canvasXY   = RG.getCanvasXY(obj.canvas);
            var gutterLeft = obj.Get('chart.gutter.left');
            var gutterTop  = obj.Get('chart.gutter.top');
            var width      = tooltip.offsetWidth;
            var height     = tooltip.offsetHeight;
    
            // Set the top position
            tooltip.style.left = 0;
            tooltip.style.top  = canvasXY[1] + coordY - height - 7 + 'px';
            
            // By default any overflow is hidden
            tooltip.style.overflow = '';
    
            // The arrow
            var img = new Image();
                img.src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAAFCAYAAACjKgd3AAAARUlEQVQYV2NkQAN79+797+RkhC4M5+/bd47B2dmZEVkBCgcmgcsgbAaA9GA1BCSBbhAuA/AagmwQPgMIGgIzCD0M0AMMAEFVIAa6UQgcAAAAAElFTkSuQmCC';
                img.style.position = 'absolute';
                img.id = '__rgraph_tooltip_pointer__';
                img.style.top = (tooltip.offsetHeight - 2) + 'px';
            tooltip.appendChild(img);
            
            // Reposition the tooltip if at the edges:
            
            // LEFT edge
            if ((canvasXY[0] + coordX + (coordW / 2)- (width / 2)) < 0) {
                tooltip.style.left = (canvasXY[0] + coordX - (width * 0.1)) + (coordW / 2) + 'px';
                img.style.left = ((width * 0.1) - 8.5) + 'px';
    
            // RIGHT edge
            } else if ((canvasXY[0] + coordX + width) > document.body.offsetWidth) {
                tooltip.style.left = canvasXY[0] + coordX - (width * 0.9) + (coordW / 2) + 'px';
                img.style.left = ((width * 0.9) - 8.5) + 'px';
    
            // Default positioning - CENTERED
            } else {
                tooltip.style.left = (canvasXY[0] + coordX + (coordW / 2) - (width * 0.5)) + 'px';
                img.style.left = ((width * 0.5) - 8.5) + 'px';
            }
        }




        /**
        * Redraw the bar so that the shadow is NOT on top
        */
        this.RedrawBars = function ()
        {
            var coords = this.coords;
            var len    = coords.length;
            
            // Reset the sequentail color index
            this.sequentialColorIndex = 0;
    
            co.beginPath();
    
                // Turn off shadow
                RG.NoShadow(this);
    
                // Set the stroke color
                co.strokeStyle = prop['chart.strokestyle'];
                
                // Set the linewidth
                co.lineWidth = prop['chart.linewidth'];
    
                for (var i=0; i<len; ++i) {
    
                    // No redrawing occurs if there is no value
                    if (coords[i][2] > 0) {
                        
                        if (prop['chart.colors.sequential']) {
                            co.fillStyle = prop['chart.colors'][this.sequentialColorIndex++];
                        } else {
                            co.fillStyle = prop['chart.colors'][0];
                        }
        
                        // Draw the bar itself
                        co.strokeRect(coords[i][0], coords[i][1], coords[i][2], coords[i][3]);
                        co.fillRect(coords[i][0], coords[i][1], coords[i][2], coords[i][3]);

                    } else {

                        // Even if there's no redrawing - the color index needs incrementing
                        this.sequentialColorIndex++
                    }
                }
            co.stroke();
            co.fill();
        }




        /**
        * Returns the X coords for a value. Returns two coords because there are... two scales.
        * 
        * @param number value The value to get the coord for
        */
        this.getXCoord = function (value)
        {
            if (value > this.max || value < 0) {
                return null;
            }
    
            var ret = [];
            
            // The offset into the graph area
            var offset = ((value / this.max) * this.axisWidth);
            
            // Get the coords (one fo each side)
            ret[0] = (this.gutterLeft + this.axisWidth) - offset;
            ret[1] = (ca.width - this.gutterRight - this.axisWidth) + offset;
            
            return ret;
    
        }




        /**
        * This allows for easy specification of gradients
        */
        this.parseColors = function ()
        {
            var props = this.properties;
            var colors = props['chart.colors'];
    
            for (var i=0; i<colors.length; ++i) {
                colors[i] = this.parseSingleColorForGradient(colors[i]);
            }
            
            props['chart.highlight.stroke'] = this.parseSingleColorForGradient(props['chart.highlight.stroke']);
            props['chart.highlight.fill']   = this.parseSingleColorForGradient(props['chart.highlight.fill']);
            props['chart.axis.color']       = this.parseSingleColorForGradient(props['chart.axis.color']);
            props['chart.strokestyle']      = this.parseSingleColorForGradient(props['chart.strokestyle']);
        }




        /**
        * This parses a single color value
        */
        this.parseSingleColorForGradient = function (color)
        {
            if (!color || typeof(color) != 'string') {
                return color;
            }
    
            if (color.match(/^gradient\((.*)\)$/i)) {
                
                var parts = RegExp.$1.split(':');
    
                // Create the gradient
                var grad = co.createLinearGradient(prop['chart.gutter.left'],0,ca.width - prop['chart.gutter.right'],0);
    
                var diff = 1 / (parts.length - 1);
    
                grad.addColorStop(0, RG.trim(parts[0]));
    
                for (var j=1; j<parts.length; ++j) {
                    grad.addColorStop(j * diff, RG.trim(parts[j]));
                }
            }
                
            return grad ? grad : color;
        }




        /**
        * Objects are now always registered so that when RGraph.Redraw()
        * is called this chart will be redrawn.
        */
        RG.Register(this);
    }