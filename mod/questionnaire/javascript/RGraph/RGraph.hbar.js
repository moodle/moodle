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
    * The horizontal bar chart constructor. The horizontal bar is a minor variant
    * on the bar chart. If you have big labels, this may be useful as there is usually
    * more space available for them.
    * 
    * @param object canvas The canvas object
    * @param array  data   The chart data
    */
    RGraph.HBar = function (id, data)
    {
        // Get the canvas and context objects
        this.id                = id;
        this.canvas            = document.getElementById(typeof id === 'object' ? id.id : id);
        this.context           = this.canvas.getContext ? this.canvas.getContext("2d") : null;
        this.canvas.__object__ = this;
        this.data              = data;
        this.type              = 'hbar';
        this.isRGraph          = true;
        this.uid               = RGraph.CreateUID();
        this.canvas.uid        = this.canvas.uid ? this.canvas.uid : RGraph.CreateUID();
        this.colorsParsed      = false;
        this.coords            = [];
        this.coords2           = [];
        this.coordsText        = [];


        /**
        * Compatibility with older browsers
        */
        RGraph.OldBrowserCompat(this.context);

        
        this.max = 0;
        this.stackedOrGrouped  = false;

        // Default properties
        this.properties = {
            'chart.gutter.left':            75,
            'chart.gutter.right':           25,
            'chart.gutter.top':             25,
            'chart.gutter.bottom':          25,
            'chart.background.grid':        true,
            'chart.background.grid.color':  '#ddd',
            'chart.background.grid.width':  1,
            'chart.background.grid.hsize':  25,
            'chart.background.grid.vsize':  25,
            'chart.background.barcolor1':   'rgba(0,0,0,0)',
            'chart.background.barcolor2':   'rgba(0,0,0,0)',
            'chart.background.grid.hlines': true,
            'chart.background.grid.vlines': true,
            'chart.background.grid.border': true,
            'chart.background.grid.autofit':true,
            'chart.background.grid.autofit.numhlines': 14,
            'chart.background.grid.autofit.numvlines': 20,
            'chart.background.grid.dashed': false,
            'chart.background.grid.dotted': false,
            'chart.linewidth':              1,
            'chart.title':                  '',
            'chart.title.background':       null,
            'chart.title.xaxis':            '',
            'chart.title.xaxis.bold':       true,
            'chart.title.xaxis.size':       null,
            'chart.title.xaxis.font':       null,
            'chart.title.yaxis':            '',
            'chart.title.yaxis.bold':       true,
            'chart.title.yaxis.size':       null,
            'chart.title.yaxis.font':       null,
            'chart.title.yaxis.color':      null,
            'chart.title.xaxis.pos':        null,
            'chart.title.yaxis.pos':        0.8,
            'chart.title.yaxis.x':          null,
            'chart.title.yaxis.y':          null,
            'chart.title.xaxis.x':          null,
            'chart.title.xaxis.y':          null,
            'chart.title.hpos':             null,
            'chart.title.vpos':             null,
            'chart.title.bold':             true,
            'chart.title.font':             null,
            'chart.title.x':                null,
            'chart.title.y':                null,
            'chart.title.halign':           null,
            'chart.title.valign':           null,
            'chart.text.size':              10,
            'chart.text.color':             'black',
            'chart.text.font':              'Arial',
            'chart.colors':                 ['Gradient(white:red)', 'Gradient(white:blue)', 'Gradient(white:green)', 'Gradient(white:pink)', 'Gradient(white:yellow)', 'Gradient(white:cyan)', 'Gradient(white:navy)', 'Gradient(white:gray)', 'Gradient(white:black)'],
            'chart.colors.sequential':      false,
            'chart.xlabels.specific':       null,
            'chart.labels':                 [],
            'chart.labels.above':           false,
            'chart.labels.above.decimals':  0,
            'chart.labels.above.specific':  null,
            'chart.xlabels':                true,
            'chart.xlabels.count':          5,
            'chart.contextmenu':            null,
            'chart.key':                    null,
            'chart.key.background':         'white',
            'chart.key.position':           'graph',
            'chart.key.halign':             'right',
            'chart.key.shadow':             false,
            'chart.key.shadow.color':       '#666',
            'chart.key.shadow.blur':        3,
            'chart.key.shadow.offsetx':     2,
            'chart.key.shadow.offsety':     2,
            'chart.key.position.gutter.boxed': false,
            'chart.key.position.x':         null,
            'chart.key.position.y':         null,
            'chart.key.color.shape':        'square',
            'chart.key.rounded':            true,
            'chart.key.linewidth':          1,
            'chart.key.colors':             null,
            'chart.key.interactive':        false,
            'chart.key.interactive.highlight.chart.stroke': 'black',
            'chart.key.interactive.highlight.chart.fill':'rgba(255,255,255,0.7)',
            'chart.key.interactive.highlight.label':'rgba(255,0,0,0.2)',
            'chart.key.text.color':         'black',
            'chart.units.pre':              '',
            'chart.units.post':             '',
            'chart.units.ingraph':          false,
            'chart.strokestyle':            'rgba(0,0,0,0)',
            'chart.xmin':                   0,
            'chart.xmax':                   0,
            'chart.axis.color':             'black',
            'chart.shadow':                 false,
            'chart.shadow.color':           '#666',
            'chart.shadow.blur':            3,
            'chart.shadow.offsetx':         3,
            'chart.shadow.offsety':         3,
            'chart.vmargin':                2,
            'chart.vmargin.grouped':        2,
            'chart.grouping':               'grouped',
            'chart.tooltips':               null,
            'chart.tooltips.event':         'onclick',
            'chart.tooltips.effect':        'fade',
            'chart.tooltips.css.class':     'RGraph_tooltip',
            'chart.tooltips.highlight':     true,
            'chart.highlight.fill':         'rgba(255,255,255,0.7)',
            'chart.highlight.stroke':       'rgba(0,0,0,0)',
            'chart.annotatable':            false,
            'chart.annotate.color':         'black',
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
            'chart.resize.handle.adjust':   [0,0],
            'chart.resize.handle.background': null,
            'chart.scale.point':            '.',
            'chart.scale.thousand':         ',',
            'chart.scale.decimals':         null,
            'chart.noredraw':               false,
            'chart.events.click':           null,
            'chart.events.mousemove':       null,
            'chart.noxaxis':                false,
            'chart.noyaxis':                false,
            'chart.noaxes':                 false,
            'chart.noxtickmarks':           false,
            'chart.noytickmarks':           false,
            'chart.numyticks':              data.length,
            'chart.numxticks':              10
        }

        // Check for support
        if (!this.canvas) {
            alert('[HBAR] No canvas support');
            return;
        }

        for (i=0; i<this.data.length; ++i) {
            if (typeof(this.data[i]) == 'object') {
                this.stackedOrGrouped = true;
            }
        }


        /**
        * Create the dollar objects so that functions can be added to them
        */
        var linear_data = RGraph.array_linearize(data);
        for (var i=0; i<linear_data.length; ++i) {
            this['$' + i] = {};
        }



        /**
        * Create the linear data array
        */
        this.data_arr = RGraph.array_linearize(this.data);


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
        * A setter
        * 
        * @param name  string The name of the property to set
        * @param value mixed  The value of the property
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
    
            if (name == 'chart.labels.abovebar') {
                name = 'chart.labels.above';
            }
    
            prop[name] = value;
    
            return this;
        }




        /**
        * A getter
        * 
        * @param name  string The name of the property to get
        */
        this.Get = function (name)
        {
            /**
            * This should be done first - prepend the property name with "chart." if necessary
            */
            if (name.substr(0,6) != 'chart.') {
                name = 'chart.' + name;
            }
    
            if (name == 'chart.labels.abovebar') {
                name = 'chart.labels.above';
            }
    
            return prop[name];
        }




        /**
        * The function you call to draw the bar chart
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
    
            /**
            * Stop the coords array from growing uncontrollably
            */
            this.coords  = [];
            this.coords2 = [];
            this.max     = 0;
    
            /**
            * Check for chart.xmin in stacked charts
            */
            if (prop['chart.xmin'] > 0 && prop['chart.grouping'] == 'stacked') {
                alert('[HBAR] Using chart.xmin is not supported with stacked charts, resetting chart.xmin to zero');
                this.Set('chart.xmin', 0);
            }
    
            /**
            * Work out a few things. They need to be here because they depend on things you can change before you
            * call Draw() but after you instantiate the object
            */
            this.graphwidth     = ca.width - this.gutterLeft - this.gutterRight;
            this.graphheight    = ca.height - this.gutterTop - this.gutterBottom;
            this.halfgrapharea  = this.grapharea / 2;
            this.halfTextHeight = prop['chart.text.size'] / 2;
    
    
    
    
    
    
            // Progressively Draw the chart
            RG.background.Draw(this);
    
            this.Drawbars();
            this.DrawAxes();
            this.DrawLabels();
    
    
            // Draw the key if necessary
            if (prop['chart.key'] && prop['chart.key'].length) {
                RG.DrawKey(this, prop['chart.key'], prop['chart.colors']);
            }
    
    
    
            /**
            * Setup the context menu if required
            */
            if (prop['chart.contextmenu']) {
                RG.ShowContext(this);
            }


    
            /**
            * Draw "in graph" labels
            */
            RG.DrawInGraphLabels(this);
    
            
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
        * This draws the axes
        */
        this.DrawAxes = function ()
        {
            var halfway = Math.round((this.graphwidth / 2) + this.gutterLeft);
    
            co.beginPath();
                
                co.lineWidth   = prop['chart.axis.linewidth'] ? prop['chart.axis.linewidth'] + 0.001 : 1.001;
                co.strokeStyle = prop['chart.axis.color'];
    
                // Draw the Y axis
                if (prop['chart.noyaxis'] == false && prop['chart.noaxes'] == false) {
                    if (prop['chart.yaxispos'] == 'center') {
                        co.moveTo(halfway, this.gutterTop);
                        co.lineTo(halfway, ca.height - this.gutterBottom);
                    } else {
                        co.moveTo(this.gutterLeft, this.gutterTop);
                        co.lineTo(this.gutterLeft, ca.height - this.gutterBottom);
                    }
                }
    
                // Draw the X axis
                if (prop['chart.noxaxis'] == false && prop['chart.noaxes'] == false) {
                    co.moveTo(this.gutterLeft +0.001, ca.height - this.gutterBottom + 0.001);
                    co.lineTo(ca.width - this.gutterRight + 0.001, ca.height - this.gutterBottom + 0.001);
                }
    
                // Draw the Y tickmarks
                if (   prop['chart.noytickmarks'] == false
                    && prop['chart.noyaxis'] == false
                    && prop['chart.numyticks'] > 0
                    && prop['chart.noaxes'] == false
                   ) {
        
                    var yTickGap = (ca.height - this.gutterTop - this.gutterBottom) / (prop['chart.numyticks'] > 0 ? prop['chart.numyticks'] : this.data.length);
            
                    for (y=this.gutterTop; y<(ca.height - this.gutterBottom - 1); y+=yTickGap) {
                        if (prop['chart.yaxispos'] == 'center') {
                            co.moveTo(halfway + 3, Math.round(y));
                            co.lineTo(halfway  - 3, Math.round(y));
                        } else {
                            co.moveTo(this.gutterLeft, Math.round(y));
                            co.lineTo( this.gutterLeft  - 3, Math.round(y));
                        }
                    }
                    
                    // If the X axis isn't being shown draw the end tick
                    if (prop['chart.noxaxis'] == true) {
                        if (prop['chart.yaxispos'] == 'center') {
                            co.moveTo(halfway + 3, Math.round(y));
                            co.lineTo(halfway  - 3, Math.round(y));
                        } else {
                            co.moveTo(this.gutterLeft, Math.round(y));
                            co.lineTo( this.gutterLeft  - 3, Math.round(y));
                        }
                    }
                }
        
        
                // Draw the X tickmarks
                if (   prop['chart.noxtickmarks'] == false
                    && prop['chart.noxaxis'] == false
                    && prop['chart.numxticks'] > 0
                    && prop['chart.noaxes'] == false) {
    
                    xTickGap = (ca.width - this.gutterLeft - this.gutterRight ) / prop['chart.numxticks'];
                    
                    yStart   = ca.height - this.gutterBottom;
                    yEnd     = (ca.height - this.gutterBottom) + 3;
            
                    for (x=(ca.width - this.gutterRight), i=0; prop['chart.yaxispos'] == 'center' ? x>=this.gutterLeft : x>this.gutterLeft; x-=xTickGap) {
            
                        if (prop['chart.yaxispos'] != 'center' || i != 5) {
                            co.moveTo(Math.round(x), yStart);
                            co.lineTo(Math.round(x), yEnd);
                        }
                        i++;
                    }
    
                    // If the Y axis isn't being shown draw the end tick
                    if (prop['chart.noyaxis'] == true) {
                        co.moveTo(this.gutterLeft, Math.round(yStart));
                        co.lineTo( this.gutterLeft, Math.round(yEnd));
                    }
                }
            co.stroke();
                
            /**
            * Reset the linewidth
            */
            co.lineWidth = 1;
        }




        /**
        * This draws the labels for the graph
        */
        this.DrawLabels = function ()
        {
            var units_pre  = prop['chart.units.pre'];
            var units_post = prop['chart.units.post'];
            var text_size  = prop['chart.text.size'];
            var font       = prop['chart.text.font'];
    
    
    
            /**
            * Set the units to blank if they're to be used for ingraph labels only
            */
            if (prop['chart.units.ingraph']) {
                units_pre  = '';
                units_post = '';
            }
    
    
            /**
            * Draw the X axis labels
            */
            if (prop['chart.xlabels']) {
            
                /**
                * Specific X labels
                */
                if (RGraph.is_array(prop['chart.xlabels.specific'])) {
                    
                    if (prop['chart.yaxispos'] == 'center') {

                        var halfGraphWidth = this.graphwidth / 2;
                        var labels         = prop['chart.xlabels.specific'];
                        var interval       = (this.graphwidth / 2) / (labels.length - 1);

                        co.fillStyle = prop['chart.text.color'];
                        
                        for (var i=0; i<labels.length; i+=1) {
                                RG.Text2(this, {'font':font,
                                                'size':text_size,
                                                'x':this.gutterLeft + halfGraphWidth + (interval * i),
                                                'y':ca.height - this.gutterBottom,
                                                'text':labels[i],
                                                'valign':'top',
                                                'halign':'center',
                                                'tag': 'scale'});
                        }
                        
                        for (var i=(labels.length - 1); i>0; i-=1) {
                                RG.Text2(this, {'font':font,
                                                'size':text_size,
                                                'x':this.gutterLeft + (interval * (labels.length - i - 1)),
                                                'y':ca.height - this.gutterBottom,
                                                'text':labels[i],
                                                'valign':'top',
                                                'halign':'center',
                                                'tag': 'scale'});
                        }

                    } else {

                        var labels   = prop['chart.xlabels.specific'];
                        var interval = this.graphwidth / (labels.length - 1);
                        
                        co.fillStyle = prop['chart.text.color'];
                        
                        for (var i=0; i<labels.length; i+=1) {
                                RG.Text2(this, {'font':font,
                                                'size':text_size,
                                                'x':this.gutterLeft + (interval * i),
                                                'y':ca.height - this.gutterBottom,
                                                'text':labels[i],
                                                'valign':'top',
                                                'halign':'center',
                                                'tag': 'scale'});
                        }
                    }

                /**
                * Draw an X scale
                */
                } else {
    
                    var gap = 7;
        
                    co.beginPath();
                    co.fillStyle = prop['chart.text.color'];
        
        
                    if (prop['chart.yaxispos'] == 'center') {
        
                        for (var i=0; i<this.scale2.labels.length; ++i) {
                            RG.Text2(this, {'font':font,
                                                'size':text_size,
                                                'x':this.gutterLeft + (this.graphwidth / 2) - ((this.graphwidth / 2) * ((i+1)/this.scale2.labels.length)),
                                                'y':this.gutterTop + this.halfTextHeight + this.graphheight + gap,
                                                'text':this.scale2.labels[i],
                                                'valign':'center',
                                                'halign':'center',
                                        'tag': 'scale'});
                        }
        
                        for (var i=0; i<this.scale2.labels.length; ++i) {
                            RG.Text2(this, {'font':font,
                                                'size':text_size,
                                                'x':this.gutterLeft + ((this.graphwidth / 2) * ((i+1)/this.scale2.labels.length)) + (this.graphwidth / 2),
                                                'y':this.gutterTop + this.halfTextHeight + this.graphheight + gap,
                                                'text':this.scale2.labels[i],
                                                'valign':'center',
                                                'halign':'center',
                                        'tag': 'scale'});
                        }
            
                    } else {
                    
                        for (var i=0; i<this.scale2.labels.length; ++i) {
                            RG.Text2(this, {'font':font,
                                                'size':text_size,
                                                'x':this.gutterLeft + (this.graphwidth * ((i+1)/this.scale2.labels.length)),
                                                'y':this.gutterTop + this.halfTextHeight + this.graphheight + gap,
                                                'text':this.scale2.labels[i],
                                                'valign':'center',
                                                'halign':'center',
                                                'tag': 'scale'
                                               });
                        }
                    }
        
                    /**
                    * If xmin is not zero - draw that
                    */
                    if (prop['chart.xmin'] > 0 || prop['chart.noyaxis'] == true) {
        
                        var x = prop['chart.yaxispos'] == 'center' ?  this.gutterLeft + (this.graphwidth / 2): this.gutterLeft;
        
                        RG.Text2(this, {'font':font,
                                            'size':text_size,
                                            'x':x,
                                            'y':this.gutterTop + this.halfTextHeight + this.graphheight + gap,
                                            'text':RG.number_format(this, prop['chart.xmin'].toFixed(prop['chart.scale.decimals']), units_pre, units_post),
                                            'valign':'center',
                                            'halign':'center',
                                            'tag': 'scale'
                                           });
                    }
        
                    co.fill();
                    co.stroke();
                }
            }
    
            /**
            * The Y axis labels
            */
            if (typeof(prop['chart.labels']) == 'object') {
            
                var xOffset = 5;
                var font    = prop['chart.text.font'];
    
                // Draw the X axis labels
                co.fillStyle = prop['chart.text.color'];
                
                // How wide is each bar
                var barHeight = (ca.height - this.gutterTop - this.gutterBottom ) / prop['chart.labels'].length;
                
                // Reset the xTickGap
                yTickGap = (ca.height - this.gutterTop - this.gutterBottom) / prop['chart.labels'].length
    
                // Draw the X tickmarks
                var i=0;
                for (y=this.gutterTop + (yTickGap / 2); y<=ca.height - this.gutterBottom; y+=yTickGap) {
                
                    RG.Text2(this, {'font':font,
                                       'size':prop['chart.text.size'],
                                       'x':this.gutterLeft - xOffset,
                                       'y':y,
                                       'text':String(prop['chart.labels'][i++]),
                                       'halign':'right',
                                       'valign':'center',
                                       'tag': 'labels'
                                      });
                }
            }
        }




        /**
        * This function draws the bars
        */
        this.Drawbars = function ()
        {
            co.lineWidth   = prop['chart.linewidth'];
            co.strokeStyle = prop['chart.strokestyle'];
            co.fillStyle   = prop['chart.colors'][0];
            var prevX      = 0;
            var prevY      = 0;
    
            /**
            * Work out the max value
            */
            if (prop['chart.xmax']) {
                this.scale2 = RG.getScale2(this, {'max':prop['chart.xmax'],
                                                      'min':prop['chart.xmin'],
                                                      'scale.decimals':Number(prop['chart.scale.decimals']),
                                                      'scale.point':prop['chart.scale.point'],
                                                      'scale.thousand':prop['chart.scale.thousand'],
                                                      'scale.round':prop['chart.scale.round'],
                                                      'units.pre':prop['chart.units.pre'],
                                                      'units.post':prop['chart.units.post'],
                                                      'ylabels.count':prop['chart.xlabels.count'],
                                                      'strict':true
                                                     });
                this.max = this.scale2.max;
    
            } else {
            
                var grouping = prop['chart.grouping'];
    
                for (i=0; i<this.data.length; ++i) {
                    if (typeof(this.data[i]) == 'object') {
                        var value = grouping == 'grouped' ? Number(RG.array_max(this.data[i], true)) : Number(RG.array_sum(this.data[i])) ;
                    } else {
                        var value = Number(Math.abs(this.data[i]));
                    }
    
                    this.max = Math.max(Math.abs(this.max), Math.abs(value));
                }
    
                this.scale2 = RG.getScale2(this, {'max':this.max,
                                                      'min':prop['chart.xmin'],
                                                      'scale.decimals':Number(prop['chart.scale.decimals']),
                                                      'scale.point':prop['chart.scale.point'],
                                                      'scale.thousand':prop['chart.scale.thousand'],
                                                      'scale.round':prop['chart.scale.round'],
                                                      'units.pre':prop['chart.units.pre'],
                                                      'units.post':prop['chart.units.post'],
                                                      'ylabels.count':prop['chart.xlabels.count']
                                                     });
    
    
                this.max = this.scale2.max;
                this.min = this.scale2.min;
            }
    
            if (prop['chart.scale.decimals'] == null && Number(this.max) == 1) {
                this.Set('chart.scale.decimals', 1);
            }
            
            /**
            * This is here to facilitate sequential colors
            */
            var colorIdx = 0;
    
            /**
            * The bars are drawn HERE
            */
            var graphwidth = (ca.width - this.gutterLeft - this.gutterRight);
            var halfwidth  = graphwidth / 2;
    
            for (i=0; i<this.data.length; ++i) {
    
                // Work out the width and height
                var width  = (this.data[i] / this.max) *  graphwidth;
                var height = this.graphheight / this.data.length;
    
                var orig_height = height;
    
                var x       = this.gutterLeft;
                var y       = this.gutterTop + (i * height);
                var vmargin = prop['chart.vmargin'];
    
                // Account for negative lengths - Some browsers (eg Chrome) don't like a negative value
                if (width < 0) {
                    x -= width;
                    width = Math.abs(width);
                }
    
                /**
                * Turn on the shadow if need be
                */
                if (prop['chart.shadow']) {
                    co.shadowColor   = prop['chart.shadow.color'];
                    co.shadowBlur    = prop['chart.shadow.blur'];
                    co.shadowOffsetX = prop['chart.shadow.offsetx'];
                    co.shadowOffsetY = prop['chart.shadow.offsety'];
                }
    
                /**
                * Draw the bar
                */
                co.beginPath();
                    if (typeof(this.data[i]) == 'number') {
    
                        var barHeight = height - (2 * vmargin);
                        var barWidth  = ((this.data[i] - prop['chart.xmin']) / (this.max - prop['chart.xmin'])) * this.graphwidth;
                        var barX      = this.gutterLeft;
    
                        // Account for Y axis pos
                        if (prop['chart.yaxispos'] == 'center') {
                            barWidth /= 2;
                            barX += halfwidth;
                            
                            if (this.data[i] < 0) {
                                barWidth = (Math.abs(this.data[i]) - prop['chart.xmin']) / (this.max - prop['chart.xmin']);
                                barWidth = barWidth * (this.graphwidth / 2);
                                barX = ((this.graphwidth / 2) + this.gutterLeft) - barWidth;
                            }
                        }
    
                        // Set the fill color
                        co.strokeStyle = prop['chart.strokestyle'];
                        co.fillStyle = prop['chart.colors'][0];
                        
                        // Sequential colors
                        if (prop['chart.colors.sequential']) {
                            co.fillStyle = prop['chart.colors'][colorIdx++];
                        }
    
                        co.strokeRect(barX, this.gutterTop + (i * height) + prop['chart.vmargin'], barWidth, barHeight);
                        co.fillRect(barX, this.gutterTop + (i * height) + prop['chart.vmargin'], barWidth, barHeight);
    
                        this.coords.push([barX,
                                          y + vmargin,
                                          barWidth,
                                          height - (2 * vmargin),
                                          co.fillStyle,
                                          this.data[i],
                                          true]);
    
                    /**
                    * Stacked bar chart
                    */
                    } else if (typeof(this.data[i]) == 'object' && prop['chart.grouping'] == 'stacked') {
    
                        if (prop['chart.yaxispos'] == 'center') {
                            alert('[HBAR] You can\'t have a stacked chart with the Y axis in the center, change it to grouped');
                        }
    
                        var barHeight = height - (2 * vmargin);

                        if (typeof this.coords2[i] == 'undefined') {
                            this.coords2[i] = [];
                        }
    
                        for (j=0; j<this.data[i].length; ++j) {
                        
    
                            // Set the fill/stroke colors
                            co.strokeStyle = prop['chart.strokestyle'];
                            co.fillStyle = prop['chart.colors'][j];
                            
    
                            // Sequential colors
                            if (prop['chart.colors.sequential']) {
                                co.fillStyle = prop['chart.colors'][colorIdx++];
                            }
                            
    
                            var width = (((this.data[i][j]) / (this.max))) * this.graphwidth;
                            var totalWidth = (RG.array_sum(this.data[i]) / this.max) * this.graphwidth;
    
                            co.strokeRect(x, this.gutterTop + prop['chart.vmargin'] + (this.graphheight / this.data.length) * i, width, height - (2 * vmargin) );
                            co.fillRect(x, this.gutterTop + prop['chart.vmargin'] + (this.graphheight / this.data.length) * i, width, height - (2 * vmargin) );
    
                            /**
                            * Store the coords for tooltips
                            */
    
                            // The last property of this array is a boolean which tells you whether the value is the last or not
                            this.coords.push([x,
                                              y + vmargin,
                                              width,
                                              height - (2 * vmargin),
                                              co.fillStyle,
                                              RG.array_sum(this.data[i]),
                                              j == (this.data[i].length - 1)
                                             ]);
                            this.coords2[i].push([x,
                                                  y + vmargin,
                                                  width,
                                                  height - (2 * vmargin),
                                                  co.fillStyle,
                                                  RG.array_sum(this.data[i]),
                                                  j == (this.data[i].length - 1)
                                                 ]);
    
                            x += width;
                        }
    
                    /**
                    * A grouped bar chart
                    */
                    } else if (typeof(this.data[i]) == 'object' && prop['chart.grouping'] == 'grouped') {
    
                        var vmarginGrouped      = prop['chart.vmargin.grouped'];
                        var individualBarHeight = ((height - (2 * vmargin) - ((this.data[i].length - 1) * vmarginGrouped)) / this.data[i].length)
                        
                        if (typeof this.coords2[i] == 'undefined') {
                            this.coords2[i] = [];
                        }
                        
                        for (j=0; j<this.data[i].length; ++j) {
    
    
                            /**
                            * Turn on the shadow if need be
                            */
                            if (prop['chart.shadow']) {
                                RG.SetShadow(this, prop['chart.shadow.color'], prop['chart.shadow.offsetx'], prop['chart.shadow.offsety'], prop['chart.shadow.blur']);
                            }
    
                            // Set the fill/stroke colors
                            co.strokeStyle = prop['chart.strokestyle'];
                            co.fillStyle   = prop['chart.colors'][j];
    
                            // Sequential colors
                            if (prop['chart.colors.sequential']) {
                                co.fillStyle = prop['chart.colors'][colorIdx++];
                            }
    
    
    
                            var startY = this.gutterTop + (height * i) + (individualBarHeight * j) + vmargin + (vmarginGrouped * j);
                            var width = ((this.data[i][j] - prop['chart.xmin']) / (this.max - prop['chart.xmin'])) * (ca.width - this.gutterLeft - this.gutterRight );
                            var startX = this.gutterLeft;
    
    
    
    
                            // Account for the Y axis being in the middle
                            if (prop['chart.yaxispos'] == 'center') {
                                width  /= 2;
                                startX += halfwidth;
                            }
                            
                            if (width < 0) {
                                startX += width;
                                width *= -1;
                            }
    
                            co.strokeRect(startX, startY, width, individualBarHeight);
                            co.fillRect(startX, startY, width, individualBarHeight);
    
                            this.coords.push([startX,
                                              startY,
                                              width,
                                              individualBarHeight,
                                              co.fillStyle,
                                              this.data[i][j],
                                              true]);
    
                            this.coords2[i].push([startX,
                                                  startY,
                                                  width,
                                                  individualBarHeight,
                                                  co.fillStyle,
                                                  this.data[i][j],
                                                  true]);
                        }
                        
                        startY += vmargin;
                    }
    
                co.closePath();
            }
    
            co.stroke();
            co.fill();
    
    
    
            /**
            * Now the bars are stroke()ed, turn off the shadow
            */
            RG.NoShadow(this);
            
            this.RedrawBars();
        }




        /**
        * This function goes over the bars after they been drawn, so that upwards shadows are underneath the bars
        */
        this.RedrawBars = function ()
        {
            if (prop['chart.noredraw']) {
                return;
            }
    
            var coords = this.coords;
    
            var font   = prop['chart.text.font'];
            var size   = prop['chart.text.size'];
            var color  = prop['chart.text.color'];
    
            RG.NoShadow(this);
            co.strokeStyle = prop['chart.strokestyle'];
    
            for (var i=0; i<coords.length; ++i) {
    
                if (prop['chart.shadow']) {
                    co.beginPath();
                        co.strokeStyle = prop['chart.strokestyle'];
                        co.fillStyle = coords[i][4];
                        co.lineWidth = prop['chart.linewidth'];
                        co.strokeRect(coords[i][0], coords[i][1], coords[i][2], coords[i][3]);
                        co.fillRect(coords[i][0], coords[i][1], coords[i][2], coords[i][3]);
                    co.fill();
                    co.stroke();
                }
    
                /**
                * Draw labels "above" the bar
                */
                if (prop['chart.labels.above'] && coords[i][6]) {
    
                    co.fillStyle   = prop['chart.text.color'];
                    co.strokeStyle = 'black';
                    RG.NoShadow(this);
    
                    var border = (coords[i][0] + coords[i][2] + 7 + co.measureText(prop['chart.units.pre'] + this.coords[i][5] + prop['chart.units.post']).width) > ca.width ? true : false;
                    
                    /**
                    * Default to the value - then check for specific labels
                    */
                    var text = RG.number_format(this, (this.coords[i][5]).toFixed(prop['chart.labels.above.decimals']), prop['chart.units.pre'], prop['chart.units.post']);
                    if (typeof prop['chart.labels.above.specific'] == 'object' && prop['chart.labels.above.specific'] && prop['chart.labels.above.specific'][i]) {
                        text = prop['chart.labels.above.specific'][i];
                    }

                    RG.Text2(this, {'font':font,
                                        'size':size,
                                        'x':coords[i][0] + coords[i][2] + 5,
                                        'y':coords[i][1] + (coords[i][3] / 2),
                                        'text': text,
                                        'valign':'center',
                                        'halign':'left',
                                        'tag': 'labels.above'
                                       });
                }
            }
        }




        /**
        * This function can be used to get the appropriate bar information (if any)
        * 
        * @param  e Event object
        * @return   Appriate bar information (if any)
        */
        this.getShape =
        this.getBar = function (e)
        {
            var mouseCoords = RG.getMouseXY(e);
    
            /**
            * Loop through the bars determining if the mouse is over a bar
            */
            for (var i=0,len=this.coords.length; i<len; i++) {
    
                var mouseX = mouseCoords[0];  // In relation to the canvas
                var mouseY = mouseCoords[1];  // In relation to the canvas
                var left   = this.coords[i][0];
                var top    = this.coords[i][1];
                var width  = this.coords[i][2];
                var height = this.coords[i][3];
                var idx    = i;
    
                if (mouseX >= left && mouseX <= (left + width) && mouseY >= top && mouseY <= (top + height) ) {
    
                    var tooltip = RG.parseTooltipText(prop['chart.tooltips'], i);
    
                    return {
                            0: this,   'object': this,
                            1: left,   'x': left,
                            2: top,    'y': top,
                            3: width,  'width': width,
                            4: height, 'height': height,
                            5: idx,    'index': idx,
                                       'tooltip': tooltip
                           };
                }
            }
        }




        /**
        * When you click on the chart, this method can return the X value at that point. It works for any point on the
        * chart (that is inside the gutters) - not just points within the Bars.
        * 
        * @param object e The event object
        */
        this.getValue = function (arg)
        {
            if (arg.length == 2) {
                var mouseX = arg[0];
                var mouseY = arg[1];
            } else {
                var mouseCoords = RG.getMouseXY(arg);
                var mouseX      = mouseCoords[0];
                var mouseY      = mouseCoords[1];
            }
            
            if (   mouseY < this.gutterTop
                || mouseY > (ca.height - this.gutterBottom)
                || mouseX < this.gutterLeft
                || mouseX > (ca.width - this.gutterRight)
               ) {
                return null;
            }
            
            if (prop['chart.yaxispos'] == 'center') {
                var value = ((mouseX - this.gutterLeft) / (this.graphwidth / 2)) * (this.max - prop['chart.xmin']);
                    value = value - this.max
                    
                    // Special case if xmin is defined
                    if (prop['chart.xmin'] > 0) {
                        value = ((mouseX - this.gutterLeft - (this.graphwidth / 2)) / (this.graphwidth / 2)) * (this.max - prop['chart.xmin']);
                        value += prop['chart.xmin'];
                        
                        if (mouseX < (this.gutterLeft + (this.graphwidth / 2))) {
                            value -= (2 * prop['chart.xmin']);
                        }
                    }
            } else {
                var value = ((mouseX - this.gutterLeft) / this.graphwidth) * (this.max - prop['chart.xmin']);
                    value += prop['chart.xmin'];
            }
    
            return value;
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
        * The getObjectByXY() worker method. Don't call this call:
        * 
        * RG.ObjectRegistry.getObjectByXY(e)
        * 
        * @param object e The event object
        */
        this.getObjectByXY = function (e)
        {
            var mouseXY = RG.getMouseXY(e);
    
            if (
                   mouseXY[0] > this.gutterLeft
                && mouseXY[0] < (ca.width - this.gutterRight)
                && mouseXY[1] > this.gutterTop
                && mouseXY[1] < (ca.height - this.gutterBottom)
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
            var gutterLeft = obj.gutterLeft;
            var gutterTop  = obj.gutterTop;
            var width      = tooltip.offsetWidth;
            var height     = tooltip.offsetHeight;
    
            // Set the top position
            tooltip.style.left = 0;
            tooltip.style.top  = canvasXY[1] + coordY + (coordH / 2) - height + 'px';
            
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
            if ((canvasXY[0] + coordX  + (coordW / 2) - (width / 2)) < 10) {
                tooltip.style.left = (canvasXY[0] + coordX - (width * 0.1)) + (coordW / 2) + 'px';
                img.style.left = ((width * 0.1) - 8.5) + 'px';
    
            // RIGHT edge
            } else if ((canvasXY[0] + (coordW / 2) + coordX + (width / 2)) > document.body.offsetWidth) {
                tooltip.style.left = canvasXY[0] + coordX - (width * 0.9) + (coordW / 2) + 'px';
                img.style.left = ((width * 0.9) - 8.5) + 'px';
    
            // Default positioning - CENTERED
            } else {
                tooltip.style.left = (canvasXY[0] + coordX + (coordW / 2) - (width * 0.5)) + 'px';
                img.style.left = ((width * 0.5) - 8.5) + 'px';
            }
        }




        /**
        * Returns the appropriate Y coord for the given value
        * 
        * @param number value The value to get the coord for
        */
        this.getXCoord = function (value)
        {
    
            if (prop['chart.yaxispos'] == 'center') {
        
                // Range checking
                if (value > this.max || value < (-1 * this.max)) {
                    return null;
                }
    
                var width = (ca.width - prop['chart.gutter.left'] - prop['chart.gutter.right']) / 2;
                var coord = (((value - prop['chart.xmin']) / (this.max - prop['chart.xmin'])) * width) + width;
    
                    coord = prop['chart.gutter.left'] + coord;
            } else {
            
                // Range checking
                if (value > this.max || value < 0) {
                    return null;
                }
    
                var width = ca.width - prop['chart.gutter.left'] - prop['chart.gutter.right'];
                var coord = ((value - prop['chart.xmin']) / (this.max - prop['chart.xmin'])) * width;
    
                    coord = prop['chart.gutter.left'] + coord;
            }
    
            return coord;
        }




        /**
        * 
        */
        this.parseColors = function ()
        {
            var colors = prop['chart.colors'];
    
            for (var i=0; i<colors.length; ++i) {
                colors[i] = this.parseSingleColorForGradient(colors[i]);
            }
            
            prop['chart.background.grid.color'] = this.parseSingleColorForGradient(prop['chart.background.grid.color']);
            prop['chart.background.barcolor1']  = this.parseSingleColorForGradient(prop['chart.background.barcolor1']);
            prop['chart.background.barcolor2']  = this.parseSingleColorForGradient(prop['chart.background.barcolor2']);
            prop['chart.text.color']            = this.parseSingleColorForGradient(prop['chart.text.color']);
            prop['chart.labels.colors']         = this.parseSingleColorForGradient(prop['chart.labels.colors']);
            prop['chart.strokestyle']           = this.parseSingleColorForGradient(prop['chart.strokestyle']);
            prop['chart.axis.color']            = this.parseSingleColorForGradient(prop['chart.axis.color']);
            prop['chart.highlight.fill']        = this.parseSingleColorForGradient(prop['chart.highlight.fill']);
            prop['chart.highlight.stroke']      = this.parseSingleColorForGradient(prop['chart.highlight.stroke']);
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
        * This function handles highlighting an entire data-series for the interactive
        * key
        * 
        * @param int index The index of the data series to be highlighted
        */
        this.interactiveKeyHighlight = function (index)
        {
            var obj = this;

            this.coords2.forEach(function (value, idx, arr)
            {
                var shape = obj.coords2[idx][index]
                var pre_linewidth = co.lineWidth;
                co.lineWidth = 2;
                co.fillStyle   = prop['chart.key.interactive.highlight.chart.fill'];
                co.strokeStyle = prop['chart.key.interactive.highlight.chart.stroke'];
                co.fillRect(shape[0], shape[1], shape[2], shape[3]);
                co.strokeRect(shape[0], shape[1], shape[2], shape[3]);
                
                // Reset the lineWidth
                co.lineWidth = pre_linewidth;
            });
        }




        /**
        * Charts are now always registered
        */
        RG.Register(this);
    }