    /**
    * o-------------------------------------------------------------------------------o
    * | This file is part of the RGraph package. RGraph is Free software, licensed    |
    * | under the MIT license - so it's free to use for all purposes. Extended        |
    * | support is available if required and donations are always welcome! You can    |
    * | read more here:                                                               |
    * |                         http://www.rgraph.net/support                         |
    * o-------------------------------------------------------------------------------o
    */

    /**
    * Initialise the various objects
    */
    if (typeof(RGraph) == 'undefined') RGraph = {isRGraph:true,type:'common'};

    RGraph.Highlight      = {};
    RGraph.Registry       = {};
    RGraph.Registry.store = [];
    RGraph.Registry.store['chart.event.handlers']       = [];
    RGraph.Registry.store['__rgraph_event_listeners__'] = []; // Used in the new system for tooltips
    RGraph.background     = {};
    RGraph.objects        = [];
    RGraph.Resizing       = {};
    RGraph.events         = [];
    RGraph.cursor         = [];
    
    RGraph.HTML = RGraph.HTML || {};

    RGraph.ObjectRegistry                    = {};
    RGraph.ObjectRegistry.objects            = {};
    RGraph.ObjectRegistry.objects.byUID      = [];
    RGraph.ObjectRegistry.objects.byCanvasID = [];


    /**
    * Some "constants"
    */
    PI       = Math.PI;
    HALFPI   = PI / 2;
    TWOPI    = PI * 2;
    ISFF     = navigator.userAgent.indexOf('Firefox') != -1;
    ISOPERA  = navigator.userAgent.indexOf('Opera') != -1;
    ISCHROME = navigator.userAgent.indexOf('Chrome') != -1;
    ISSAFARI = navigator.userAgent.indexOf('Safari') != -1 && !ISCHROME;
    ISWEBKIT = navigator.userAgent.indexOf('WebKit') != -1;
    //ISIE     is defined below
    //ISIE6    is defined below
    //ISIE7    is defined below
    //ISIE8    is defined below
    //ISIE9    is defined below
    //ISIE9    is defined below
    //ISIE9UP  is defined below
    //ISIE10   is defined below
    //ISIE10UP is defined below
    //ISIE11   is defined below
    //ISIE11UP is defined below
    //ISOLD    is defined below


    /**
    * Returns five values which are used as a nice scale
    * 
    * @param  max int    The maximum value of the graph
    * @param  obj object The graph object
    * @return     array   An appropriate scale
    */
    RGraph.getScale = function (max, obj)
    {
        /**
        * Special case for 0
        */
        if (max == 0) {
            return ['0.2', '0.4', '0.6', '0.8', '1.0'];
        }

        var original_max = max;

        /**
        * Manually do decimals
        */
        if (max <= 1) {
            if (max > 0.5) {
                return [0.2,0.4,0.6,0.8, Number(1).toFixed(1)];

            } else if (max >= 0.1) {
                return obj.Get('chart.scale.round') ? [0.2,0.4,0.6,0.8,1] : [0.1,0.2,0.3,0.4,0.5];

            } else {

                var tmp = max;
                var exp = 0;

                while (tmp < 1.01) {
                    exp += 1;
                    tmp *= 10;
                }

                var ret = ['2e-' + exp, '4e-' + exp, '6e-' + exp, '8e-' + exp, '10e-' + exp];


                if (max <= ('5e-' + exp)) {
                    ret = ['1e-' + exp, '2e-' + exp, '3e-' + exp, '4e-' + exp, '5e-' + exp];
                }

                return ret;
            }
        }

        // Take off any decimals
        if (String(max).indexOf('.') > 0) {
            max = String(max).replace(/\.\d+$/, '');
        }

        var interval = Math.pow(10, Number(String(Number(max)).length - 1));
        var topValue = interval;

        while (topValue < max) {
            topValue += (interval / 2);
        }

        // Handles cases where the max is (for example) 50.5
        if (Number(original_max) > Number(topValue)) {
            topValue += (interval / 2);
        }

        // Custom if the max is greater than 5 and less than 10
        if (max < 10) {
            topValue = (Number(original_max) <= 5 ? 5 : 10);
        }
        
        /**
        * Added 02/11/2010 to create "nicer" scales
        */
        if (obj && typeof(obj.Get('chart.scale.round')) == 'boolean' && obj.Get('chart.scale.round')) {
            topValue = 10 * interval;
        }

        return [topValue * 0.2, topValue * 0.4, topValue * 0.6, topValue * 0.8, topValue];
    }
























    /**
    * Returns an appropriate scale. The return value is actualy anm object consiosting of:
    *  scale.max
    *  scale.min
    *  scale.scale
    * 
    * @param  obj object  The graph object
    * @param  prop object An object consisting of configuration properties
    * @return     object  An object containg scale information
    */
    RGraph.getScale2 = function (obj, opt)
    {
        var RG   = RGraph;
        var ca   = obj.canvas;
        var co   = obj.context;
        var prop = obj.properties;
        
        var numlabels    = typeof(opt['ylabels.count']) == 'number' ? opt['ylabels.count'] : 5;
        var units_pre    = typeof(opt['units.pre']) == 'string' ? opt['units.pre'] : '';
        var units_post   = typeof(opt['units.post']) == 'string' ? opt['units.post'] : '';
        var max          = Number(opt['max']);
        var min          = typeof(opt['min']) == 'number' ? opt['min'] : 0;
        var strict       = opt['strict'];
        var decimals     = Number(opt['scale.decimals']); // Sometimes the default is null
        var point        = opt['scale.point']; // Default is a string in all chart libraries so no need to cast it
        var thousand     = opt['scale.thousand']; // Default is a string in all chart libraries so no need to cast it
        var original_max = max;
        var round        = opt['scale.round'];
        var scale        = {'max':1,'labels':[]};



        /**
        * Special case for 0
        * 
        * ** Must be first **
        */
        if (!max) {

            var max   = 1;

            var scale = {max:1,min:0,labels:[]};

            for (var i=0; i<numlabels; ++i) {
                var label = ((((max - min) / numlabels) + min) * (i + 1)).toFixed(decimals);
                scale.labels.push(units_pre + label + units_post);
            }

        /**
        * Manually do decimals
        */
        } else if (max <= 1 && !strict) {

            if (max > 0.5) {

                max  = 1;
                min  = min;
                scale.min = min;

                for (var i=0; i<numlabels; ++i) {
                    var label = ((((max - min) / numlabels) * (i + 1)) + min).toFixed(decimals);

                    scale.labels.push(units_pre + label + units_post);
                }

            } else if (max >= 0.1) {
                
                max   = 0.5;
                min   = min;
                scale = {'max': 0.5, 'min':min,'labels':[]}

                for (var i=0; i<numlabels; ++i) {
                    var label = ((((max - min) / numlabels) + min) * (i + 1)).toFixed(decimals);
                    scale.labels.push(units_pre + label + units_post);
                }

            } else {

                scale = {'min':min,'labels':[]}
                var max_str = String(max);
                
                if (max_str.indexOf('e') > 0) {
                    var numdecimals = Math.abs(max_str.substring(max_str.indexOf('e') + 1));
                } else {
                    var numdecimals = String(max).length - 2;
                }

                var max = 1  / Math.pow(10,numdecimals - 1);

                for (var i=0; i<numlabels; ++i) {
                    var label = ((((max - min) / numlabels) + min) * (i + 1));
                        label = label.toExponential();
                        label = label.split(/e/);
                        label[0] = Math.round(label[0]);
                        label = label.join('e');
                    scale.labels.push(label);
                }

                //This makes the top scale value of the format 10e-2 instead of 1e-1
                tmp = scale.labels[scale.labels.length - 1].split(/e/);
                tmp[0] += 0;
                tmp[1] = Number(tmp[1]) - 1;
                tmp = tmp[0] + 'e' + tmp[1];
                scale.labels[scale.labels.length - 1] = tmp;
                
                // Add the units
                for (var i=0; i<scale.labels.length ; ++i) {
                    scale.labels[i] = units_pre + scale.labels[i] + units_post;
                }
                
                scale.max = Number(max);
            }


        } else if (!strict) {


            /**
            * Now comes the scale handling for integer values
            */


            // This accomodates decimals by rounding the max up to the next integer
            max = Math.ceil(max);

            var interval = Math.pow(10, Math.max(1, Number(String(Number(max) - Number(min)).length - 1)) );

            var topValue = interval;

            while (topValue < max) {
                topValue += (interval / 2);
            }
    
            // Handles cases where the max is (for example) 50.5
            if (Number(original_max) > Number(topValue)) {
                topValue += (interval / 2);
            }

            // Custom if the max is greater than 5 and less than 10
            if (max <= 10) {
                topValue = (Number(original_max) <= 5 ? 5 : 10);
            }
    
    
            // Added 02/11/2010 to create "nicer" scales
            if (obj && typeof(round) == 'boolean' && round) {
                topValue = 10 * interval;
            }

            scale.max = topValue;

            // Now generate the scale. Temporarily set the objects chart.scale.decimal and chart.scale.point to those
            //that we've been given as the number_format functuion looks at those instead of using argumrnts.
            var tmp_point    = prop['chart.scale.point'];
            var tmp_thousand = prop['chart.scale.thousand'];

            obj.Set('chart.scale.thousand', thousand);
            obj.Set('chart.scale.point', point);


            for (var i=0; i<numlabels; ++i) {
                scale.labels.push( RG.number_format(obj, ((((i+1) / numlabels) * (topValue - min)) + min).toFixed(decimals), units_pre, units_post) );
            }

            obj.Set('chart.scale.thousand', tmp_thousand);
            obj.Set('chart.scale.point', tmp_point);
        
        } else if (typeof(max) == 'number' && strict) {

            /**
            * ymax is set and also strict
            */
            for (var i=0; i<numlabels; ++i) {
                scale.labels.push( RG.number_format(obj, ((((i+1) / numlabels) * (max - min)) + min).toFixed(decimals), units_pre, units_post) );
            }
            
            // ???
            scale.max = max;
        }

        
        scale.units_pre  = units_pre;
        scale.units_post = units_post;
        scale.point      = point;
        scale.decimals   = decimals;
        scale.thousand   = thousand;
        scale.numlabels  = numlabels;
        scale.round      = Boolean(round);
        scale.min        = min;


        return scale;
    }












    /**
    * Returns the maximum numeric value which is in an array
    * 
    * @param  array arr The array (can also be a number, in which case it's returned as-is)
    * @param  int       Whether to ignore signs (ie negative/positive)
    * @return int       The maximum value in the array
    */
    RGraph.array_max = function (arr)
    {
        var max       = null;
        var MathLocal = Math;
        
        if (typeof(arr) == 'number') {
            return arr;
        }
        
        if (RGraph.is_null(arr)) {
            return 0;
        }

        for (var i=0,len=arr.length; i<len; ++i) {
            if (typeof(arr[i]) == 'number') {

                var val = arguments[1] ? MathLocal.abs(arr[i]) : arr[i];
                
                if (typeof max == 'number') {
                    max = MathLocal.max(max, val);
                } else {
                    max = val;
                }
            }
        }
        
        return max;
    }




    /**
    * Returns the maximum value which is in an array
    * 
    * @param  array arr The array
    * @param  int   len The length to pad the array to
    * @param  mixed     The value to use to pad the array (optional)
    */
    RGraph.array_pad = function (arr, len)
    {
        if (arr.length < len) {
            var val = arguments[2] ? arguments[2] : null;
            
            for (var i=arr.length; i<len; i+=1) {
                arr[i] = val;
            }
        }
        
        return arr;
    }




    /**
    * An array sum function
    * 
    * @param  array arr The  array to calculate the total of
    * @return int       The summed total of the arrays elements
    */
    RGraph.array_sum = function (arr)
    {
        // Allow integers
        if (typeof(arr) == 'number') {
            return arr;
        }
        
        // Account for null
        if (RGraph.is_null(arr)) {
            return 0;
        }

        var i, sum;
        var len = arr.length;

        for(i=0,sum=0;i<len;sum+=arr[i++]);
        return sum;
    }




    /**
    * Takes any number of arguments and adds them to one big linear array
    * which is then returned
    * 
    * @param ... mixed The data to linearise. You can strings, booleans, numbers or arrays
    */
    RGraph.array_linearize = function ()
    {
        var arr  = [];
        var args = arguments;
        var RG   = RGraph;

        for (var i=0,len=args.length; i<len; ++i) {

            if (typeof(args[i]) == 'object' && args[i]) {
                for (var j=0; j<args[i].length; ++j) {
                    var sub = RG.array_linearize(args[i][j]);
                    
                    for (var k=0; k<sub.length; ++k) {
                        arr.push(sub[k]);
                    }
                }
            } else {
                arr.push(args[i]);
            }
        }

        return arr;
    }




    /**
    * This is a useful function which is basically a shortcut for drawing left, right, top and bottom alligned text.
    * 
    * @param object context The context
    * @param string font    The font
    * @param int    size    The size of the text
    * @param int    x       The X coordinate
    * @param int    y       The Y coordinate
    * @param string text    The text to draw
    * @parm  string         The vertical alignment. Can be null. "center" gives center aligned  text, "top" gives top aligned text.
    *                       Anything else produces bottom aligned text. Default is bottom.
    * @param  string        The horizontal alignment. Can be null. "center" gives center aligned  text, "right" gives right aligned text.
    *                       Anything else produces left aligned text. Default is left.
    * @param  bool          Whether to show a bounding box around the text. Defaults not to
    * @param int            The angle that the text should be rotate at (IN DEGREES)
    * @param string         Background color for the text
    * @param bool           Whether the text is bold or not
    */
    RGraph.Text = function (context, font, size, x, y, text)
    {
        // "Cache" the args as a local variable
        var args = arguments;

        // Handle undefined - change it to an empty string
        if ((typeof(text) != 'string' && typeof(text) != 'number') || text == 'undefined') {
            return;
        }




        /**
        * This accommodates multi-line text
        */
        if (typeof(text) == 'string' && text.match(/\r\n/)) {

            var dimensions = RGraph.MeasureText('M', args[11], font, size);

            /**
            * Measure the text (width and height)
            */

            var arr = text.split('\r\n');

            /**
            * Adjust the Y position
            */
            
            // This adjusts the initial y position
            if (args[6] && args[6] == 'center') y = (y - (dimensions[1] * ((arr.length - 1) / 2)));

            for (var i=1; i<arr.length; ++i) {
    
                RGraph.Text(context,
                            font,
                            size,
                            args[9] == -90 ? (x + (size * 1.5)) : x,
                            y + (dimensions[1] * i),
                            arr[i],
                            args[6] ? args[6] : null,
                            args[7],
                            args[8],
                            args[9],
                            args[10],
                            args[11],
                            args[12]);
            }
            
            // Update text to just be the first line
            text = arr[0];
        }


        // Accommodate MSIE
        if (document.all && ISOLD) {
            y += 2;
        }


        context.font = (args[11] ? 'Bold ': '') + size + 'pt ' + font;

        var i;
        var origX = x;
        var origY = y;
        var originalFillStyle = context.fillStyle;
        var originalLineWidth = context.lineWidth;

        // Need these now the angle can be specified, ie defaults for the former two args
        if (typeof(args[6])  == 'undefined') args[6]  = 'bottom'; // Vertical alignment. Default to bottom/baseline
        if (typeof(args[7])  == 'undefined') args[7]  = 'left';   // Horizontal alignment. Default to left
        if (typeof(args[8])  == 'undefined') args[8]  = null;     // Show a bounding box. Useful for positioning during development. Defaults to false
        if (typeof(args[9])  == 'undefined') args[9]  = 0;        // Angle (IN DEGREES) that the text should be drawn at. 0 is middle right, and it goes clockwise

        // The alignment is recorded here for purposes of Opera compatibility
        if (navigator.userAgent.indexOf('Opera') != -1) {
            context.canvas.__rgraph_valign__ = args[6];
            context.canvas.__rgraph_halign__ = args[7];
        }

        // First, translate to x/y coords
        context.save();

            context.canvas.__rgraph_originalx__ = x;
            context.canvas.__rgraph_originaly__ = y;

            context.translate(x, y);
            x = 0;
            y = 0;

            // Rotate the canvas if need be
            if (args[9]) {
                context.rotate(args[9] / (180 / PI));
            }


            // Vertical alignment - defaults to bottom
            if (args[6]) {

                var vAlign = args[6];

                if (vAlign == 'center') {
                    context.textBaseline = 'middle';
                } else if (vAlign == 'top') {
                    context.textBaseline = 'top';
                }
            }


            // Hoeizontal alignment - defaults to left
            if (args[7]) {

                var hAlign = args[7];
                var width  = context.measureText(text).width;
    
                if (hAlign) {
                    if (hAlign == 'center') {
                        context.textAlign = 'center';
                    } else if (hAlign == 'right') {
                        context.textAlign = 'right';
                    }
                }
            }
            
            
            context.fillStyle = originalFillStyle;

            /**
            * Draw a bounding box if requested
            */
            context.save();
                 context.fillText(text,0,0);
                 context.lineWidth = 1;

                var width = context.measureText(text).width;
                var width_offset = (hAlign == 'center' ? (width / 2) : (hAlign == 'right' ? width : 0));
                var height = size * 1.5; // !!!
                var height_offset = (vAlign == 'center' ? (height / 2) : (vAlign == 'top' ? height : 0));
                var ieOffset = ISOLD ? 2 : 0;

                if (args[8]) {

                    context.strokeRect(-3 - width_offset,
                                       0 - 3 - height - ieOffset + height_offset,
                                       width + 6,
                                       height + 6);
                    /**
                    * If requested, draw a background for the text
                    */
                    if (args[10]) {
                        context.fillStyle = args[10];
                        context.fillRect(-3 - width_offset,
                                           0 - 3 - height - ieOffset + height_offset,
                                           width + 6,
                                           height + 6);
                    }

                    
                    context.fillStyle = originalFillStyle;


                    /**
                    * Do the actual drawing of the text
                    */
                    context.fillText(text,0,0);
                }
            context.restore();
            
            // Reset the lineWidth
            context.lineWidth = originalLineWidth;

        context.restore();
    }




    /**
    * Clears the canvas by setting the width. You can specify a colour if you wish.
    * 
    * @param object canvas The canvas to clear
    */
    RGraph.Clear = function (ca)
    {
        var RG    = RGraph;
        var co    = ca.getContext('2d');
        var color = arguments[1];

        if (!ca) {
            return;
        }
        
        RG.FireCustomEvent(ca.__object__, 'onbeforeclear');

        if (ISIE8 && !color) {
            color = 'white';
        }

        /**
        * Can now clear the canvas back to fully transparent
        */
        if (!color || (color && color == 'rgba(0,0,0,0)' || color == 'transparent')) {

            co.clearRect(0,0,ca.width, ca.height);
            
            // Reset the globalCompositeOperation
            co.globalCompositeOperation = 'source-over';

        } else {

            co.fillStyle = color;
            co.beginPath();

            if (ISIE8) {
                co.fillRect(0,0,ca.width,ca.height);
            } else {
                co.fillRect(-10,-10,ca.width + 20,ca.height + 20);
            }

            co.fill();
        }
        
        //if (RG.ClearAnnotations) {
            //RG.ClearAnnotations(ca.id);
        //}
        
        /**
        * This removes any background image that may be present
        */
        if (RG.Registry.Get('chart.background.image.' + ca.id)) {
            var img = RG.Registry.Get('chart.background.image.' + ca.id);
            img.style.position = 'absolute';
            img.style.left     = '-10000px';
            img.style.top      = '-10000px';
        }
        
        /**
        * This hides the tooltip that is showing IF it has the same canvas ID as
        * that which is being cleared
        */
        if (RG.Registry.Get('chart.tooltip')) {
            RG.HideTooltip(ca);
            //RG.Redraw();
        }

        /**
        * Set the cursor to default
        */
        ca.style.cursor = 'default';

        RG.FireCustomEvent(ca.__object__, 'onclear');
    }




    /**
    * Draws the title of the graph
    * 
    * @param object  canvas The canvas object
    * @param string  text   The title to write
    * @param integer gutter The size of the gutter
    * @param integer        The center X point (optional - if not given it will be generated from the canvas width)
    * @param integer        Size of the text. If not given it will be 14
    */
    RGraph.DrawTitle = function (obj, text, gutterTop)
    {
        var RG           = RGraph;
        var ca = canvas  = obj.canvas;
        var co = context = obj.context;
        var prop         = obj.properties;

        var gutterLeft   = prop['chart.gutter.left'];
        var gutterRight  = prop['chart.gutter.right'];
        var gutterTop    = gutterTop;
        var gutterBottom = prop['chart.gutter.bottom'];
        var size         = arguments[4] ? arguments[4] : 12;
        var bold         = prop['chart.title.bold'];
        var centerx      = (arguments[3] ? arguments[3] : ((ca.width - gutterLeft - gutterRight) / 2) + gutterLeft);
        var keypos       = prop['chart.key.position'];
        var vpos         = prop['chart.title.vpos'];
        var hpos         = prop['chart.title.hpos'];
        var bgcolor      = prop['chart.title.background'];
        var x            = prop['chart.title.x'];
        var y            = prop['chart.title.y'];
        var halign       = 'center';
        var valign       = 'center';

        // Account for 3D effect by faking the key position
        if (obj.type == 'bar' && prop['chart.variant'] == '3d') {
            keypos = 'gutter';
        }

        co.beginPath();
        co.fillStyle = prop['chart.text.color'] ? prop['chart.text.color'] : 'black';





        /**
        * Vertically center the text if the key is not present
        */
        if (keypos && keypos != 'gutter') {
            var valign = 'center';

        } else if (!keypos) {
            var valign = 'center';

        } else {
            var valign = 'bottom';
        }





        // if chart.title.vpos is a number, use that
        if (typeof(prop['chart.title.vpos']) == 'number') {
            vpos = prop['chart.title.vpos'] * gutterTop;

            if (prop['chart.xaxispos'] == 'top') {
                vpos = prop['chart.title.vpos'] * gutterBottom + gutterTop + (ca.height - gutterTop - gutterBottom);
            }

        } else {
            vpos = gutterTop - size - 5;

            if (prop['chart.xaxispos'] == 'top') {
                vpos = ca.height  - gutterBottom + size + 5;
            }
        }




        // if chart.title.hpos is a number, use that. It's multiplied with the (entire) canvas width
        if (typeof(hpos) == 'number') {
            centerx = hpos * ca.width;
        }

        /**
        * Now the chart.title.x and chart.title.y settings override (is set) the above
        */
        if (typeof(x) == 'number') centerx = x;
        if (typeof(y) == 'number') vpos    = y;




        /**
        * Horizontal alignment can now (Jan 2013) be specified
        */
        if (typeof(prop['chart.title.halign']) == 'string') {
            halign = prop['chart.title.halign'];
        }
        
        /**
        * Vertical alignment can now (Jan 2013) be specified
        */
        if (typeof(prop['chart.title.valign']) == 'string') {
            valign = prop['chart.title.valign'];
        }




        
        // Set the colour
        if (typeof(prop['chart.title.color'] != null)) {
            var oldColor = co.fillStyle
            var newColor = prop['chart.title.color']
            co.fillStyle = newColor ? newColor : 'black';
        }




        /**
        * Default font is Arial
        */
        var font = prop['chart.text.font'];




        /**
        * Override the default font with chart.title.font
        */
        if (typeof(prop['chart.title.font']) == 'string') {
            font = prop['chart.title.font'];
        }




        /**
        * Draw the title
        */
        RG.Text2(obj,{'font':font,
                          'size':size,
                          'x':centerx,
                          'y':vpos,
                          'text':text,
                          'valign':valign,
                          'halign':halign,
                          'bounding':bgcolor != null,
                          'bounding.fill':bgcolor,
                          'bold':bold,
                          'tag':'title'
                         });
        
        // Reset the fill colour
        co.fillStyle = oldColor;
    }





    /**
    * This function returns the mouse position in relation to the canvas
    * 
    * @param object e The event object.
    *
    RGraph.getMouseXY = function (e)
    {
        var el = (ISOLD ? event.srcElement : e.target);
        var x;
        var y;

        // ???
        var paddingLeft = el.style.paddingLeft ? parseInt(el.style.paddingLeft) : 0;
        var paddingTop  = el.style.paddingTop ? parseInt(el.style.paddingTop) : 0;
        var borderLeft  = el.style.borderLeftWidth ? parseInt(el.style.borderLeftWidth) : 0;
        var borderTop   = el.style.borderTopWidth  ? parseInt(el.style.borderTopWidth) : 0;
        
        if (ISIE8) e = event;

        // Browser with offsetX and offsetY
        if (typeof(e.offsetX) == 'number' && typeof(e.offsetY) == 'number') {
            x = e.offsetX;
            y = e.offsetY;

        // FF and other
        } else {
            x = 0;
            y = 0;

            while (el != document.body && el) {
                x += el.offsetLeft;
                y += el.offsetTop;

                el = el.offsetParent;
            }

            x = e.pageX - x;
            y = e.pageY - y;
        }

        return [x, y];
    }*/


    RGraph.getMouseXY = function(e)
    {
        var el      = e.target;
        var ca      = el;
        var caStyle = ca.style;
        var offsetX = 0;
        var offsetY = 0;
        var x;
        var y;
        var ISFIXED     = (ca.style.position == 'fixed');
        var borderLeft  = parseInt(caStyle.borderLeftWidth) || 0;
        var borderTop   = parseInt(caStyle.borderTopWidth) || 0;
        var paddingLeft = parseInt(caStyle.paddingLeft) || 0
        var paddingTop  = parseInt(caStyle.paddingTop) || 0
        var additionalX = borderLeft + paddingLeft;
        var additionalY = borderTop + paddingTop;


        if (typeof(e.offsetX) == 'number' && typeof(e.offsetY) == 'number') {

            if (ISFIXED) {
                if (ISOPERA) {
                    x = e.offsetX;
                    y = e.offsetY;
                
                } else if (ISWEBKIT) {
                    x = e.offsetX - paddingLeft - borderLeft;
                    y = e.offsetY - paddingTop - borderTop;
                
                } else if (ISIE) {
                    x = e.offsetX - paddingLeft;
                    y = e.offsetY - paddingTop;
    
                } else {
                    x = e.offsetX;
                    y = e.offsetY;
                }
    
    
    
    
            } else {
    
    
    
    
                if (!ISIE && !ISOPERA) {
                    x = e.offsetX - borderLeft - paddingLeft;
                    y = e.offsetY - borderTop - paddingTop;
                
                } else if (ISIE) {
                    x = e.offsetX - paddingLeft;
                    y = e.offsetY - paddingTop;
                
                } else {
                    x = e.offsetX;
                    y = e.offsetY;
                }
            }   

        } else {

            if (typeof(el.offsetParent) != 'undefined') {
                do {
                    offsetX += el.offsetLeft;
                    offsetY += el.offsetTop;
                } while ((el = el.offsetParent));
            }

            x = e.pageX - offsetX - additionalX;
            y = e.pageY - offsetY - additionalY;

            x -= (2 * (parseInt(document.body.style.borderLeftWidth) || 0));
            y -= (2 * (parseInt(document.body.style.borderTopWidth) || 0));

            //x += (parseInt(caStyle.borderLeftWidth) || 0);
            //y += (parseInt(caStyle.borderTopWidth) || 0);
        }

        // We return a javascript array with x and y defined
        return [x, y];
    }




    /**
    * This function returns a two element array of the canvas x/y position in
    * relation to the page
    * 
    * @param object canvas
    */
    RGraph.getCanvasXY = function (canvas)
    {
        var x  = 0;
        var y  = 0;
        var el = canvas; // !!!

        do {

            x += el.offsetLeft;
            y += el.offsetTop;
            
            // ACCOUNT FOR TABLES IN wEBkIT
            if (el.tagName.toLowerCase() == 'table' && (ISCHROME || ISSAFARI)) {
                x += parseInt(el.border) || 0;
                y += parseInt(el.border) || 0;
            }

            el = el.offsetParent;

        } while (el && el.tagName.toLowerCase() != 'body');


        var paddingLeft = canvas.style.paddingLeft ? parseInt(canvas.style.paddingLeft) : 0;
        var paddingTop  = canvas.style.paddingTop ? parseInt(canvas.style.paddingTop) : 0;
        var borderLeft  = canvas.style.borderLeftWidth ? parseInt(canvas.style.borderLeftWidth) : 0;
        var borderTop   = canvas.style.borderTopWidth  ? parseInt(canvas.style.borderTopWidth) : 0;

        if (navigator.userAgent.indexOf('Firefox') > 0) {
            x += parseInt(document.body.style.borderLeftWidth) || 0;
            y += parseInt(document.body.style.borderTopWidth) || 0;
        }

        return [x + paddingLeft + borderLeft, y + paddingTop + borderTop];
    }




    /**
    * This function determines whther a canvas is fixed (CSS positioning) or not. If not it returns
    * false. If it is then the element that is fixed is returned (it may be a parent of the canvas).
    * 
    * @return Either false or the fixed positioned element
    */
    RGraph.isFixed = function (canvas)
    {
        var obj = canvas;
        var i = 0;

        while (obj && obj.tagName.toLowerCase() != 'body' && i < 99) {

            if (obj.style.position == 'fixed') {
                return obj;
            }
            
            obj = obj.offsetParent;
        }

        return false;
    }




    /**
    * Registers a graph object (used when the canvas is redrawn)
    * 
    * @param object obj The object to be registered
    */
    RGraph.Register = function (obj)
    {
        // Checking this property ensures the object is only registered once
        if (!obj.Get('chart.noregister')) {
            // As of 21st/1/2012 the object registry is now used
            RGraph.ObjectRegistry.Add(obj);
            obj.Set('chart.noregister', true);
        }
    }




    /**
    * Causes all registered objects to be redrawn
    * 
    * @param string An optional color to use to clear the canvas
    */
    RGraph.Redraw = function ()
    {
        var objectRegistry = RGraph.ObjectRegistry.objects.byCanvasID;

        // Get all of the canvas tags on the page
        var tags = document.getElementsByTagName('canvas');

        for (var i=0,len=tags.length; i<len; ++i) {
            if (tags[i].__object__ && tags[i].__object__.isRGraph) {
                
                // Only clear the canvas if it's not Trace'ing - this applies to the Line/Scatter Trace effects
                if (!tags[i].noclear) {
                    RGraph.Clear(tags[i], arguments[0] ? arguments[0] : null);
                }
            }
        }

        // Go through the object registry and redraw *all* of the canvas'es that have been registered
        for (var i=0,len=objectRegistry.length; i<len; ++i) {
            if (objectRegistry[i]) {
                var id = objectRegistry[i][0];
                objectRegistry[i][1].Draw();
            }
        }
    }




    /**
    * Causes all registered objects ON THE GIVEN CANVAS to be redrawn
    * 
    * @param canvas object The canvas object to redraw
    * @param        bool   Optional boolean which defaults to true and determines whether to clear the canvas
    */
    RGraph.RedrawCanvas = function (canvas)
    {
        var objects = RGraph.ObjectRegistry.getObjectsByCanvasID(canvas.id);

        /**
        * First clear the canvas
        */
        if (!arguments[1] || (typeof(arguments[1]) == 'boolean' && !arguments[1] == false) ) {
            
            // TODO This function should really support passing a color as the second optional argument - which is then used in the below
            // call
            RGraph.Clear(canvas);
        }

        /**
        * Now redraw all the charts associated with that canvas
        */
        for (var i=0,len=objects.length; i<len; ++i) {
            if (objects[i]) {
                if (objects[i] && objects[i].isRGraph) { // Is it an RGraph object ??
                    objects[i].Draw();
                }
            }
        }
    }




    /**
    * This function draws the background for the bar chart, line chart and scatter chart.
    * 
    * @param  object obj The graph object
    */
    RGraph.background.Draw = function (obj)
    {
        var RG           = RGraph;
        var ca = canvas  = obj.canvas;
        var co = context = obj.context;
        var prop         = obj.properties;

        var height       = 0;
        var gutterLeft   = obj.gutterLeft;
        var gutterRight  = obj.gutterRight;
        var gutterTop    = obj.gutterTop;
        var gutterBottom = obj.gutterBottom;
        var variant      = prop['chart.variant'];
        
        co.fillStyle = prop['chart.text.color'];
        
        // If it's a bar and 3D variant, translate
        if (variant == '3d') {
            co.save();
            co.translate(10, -5);
        }

        // X axis title
        if (typeof(prop['chart.title.xaxis']) == 'string' && prop['chart.title.xaxis'].length) {
        
            var size = prop['chart.text.size'] + 2;
            var font = prop['chart.text.font'];
            var bold = prop['chart.title.xaxis.bold'];

            if (typeof(prop['chart.title.xaxis.size']) == 'number') {
                size = prop['chart.title.xaxis.size'];
            }

            if (typeof(prop['chart.title.xaxis.font']) == 'string') {
                font = prop['chart.title.xaxis.font'];
            }
            
            var hpos = ((ca.width - gutterLeft - gutterRight) / 2) + gutterLeft;
            var vpos = ca.height - gutterBottom + 25;
            
            if (typeof(prop['chart.title.xaxis.pos']) == 'number') {
                vpos = ca.height - (gutterBottom * prop['chart.title.xaxis.pos']);
            }




            // Specifically specified X/Y positions
            if (typeof prop['chart.title.xaxis.x'] == 'number') {
                hpos = prop['chart.title.xaxis.x'];
            }

            if (typeof prop['chart.title.xaxis.y'] == 'number') {
                vpos = prop['chart.title.xaxis.y'];
            }




            RG.Text2(obj, {'font':font,
                           'size':size,
                           'x':hpos,
                           'y':vpos,
                           'text':prop['chart.title.xaxis'],
                           'halign':'center',
                           'valign':'center',
                           'bold':bold,
                           'tag': 'title xaxis'
                          });
        }

        // Y axis title
        if (typeof(prop['chart.title.yaxis']) == 'string' && prop['chart.title.yaxis'].length) {

            var size  = prop['chart.text.size'] + 2;
            var font  = prop['chart.text.font'];
            var angle = 270;
            var bold  = prop['chart.title.yaxis.bold'];
            var color = prop['chart.title.yaxis.color'];

            if (typeof(prop['chart.title.yaxis.pos']) == 'number') {
                var yaxis_title_pos = prop['chart.title.yaxis.pos'] * gutterLeft;
            } else {
                var yaxis_title_pos = ((gutterLeft - 25) / gutterLeft) * gutterLeft;
            }

            if (typeof(prop['chart.title.yaxis.size']) == 'number') {
                size = prop['chart.title.yaxis.size'];
            }

            if (typeof(prop['chart.title.yaxis.font']) == 'string') {
                font = prop['chart.title.yaxis.font'];
            }

            if (prop['chart.title.yaxis.align'] == 'right' || prop['chart.title.yaxis.position'] == 'right') {
                angle = 90;
                yaxis_title_pos = prop['chart.title.yaxis.pos'] ? (ca.width - gutterRight) + (prop['chart.title.yaxis.pos'] * gutterRight) :
                                                                   ca.width - gutterRight + prop['chart.text.size'] + 5;
            } else {
                yaxis_title_pos = yaxis_title_pos;
            }
            
            var y = ((ca.height - gutterTop - gutterBottom) / 2) + gutterTop;
            
            // Specifically specified X/Y positions
            if (typeof prop['chart.title.yaxis.x'] == 'number') {
                yaxis_title_pos = prop['chart.title.yaxis.x'];
            }

            if (typeof prop['chart.title.yaxis.y'] == 'number') {
                y = prop['chart.title.yaxis.y'];
            }

            co.fillStyle = color;
            RG.Text2(obj, {'font':font,
                           'size':size,
                           'x':yaxis_title_pos,
                           'y':y,
                           'valign':'center',
                           'halign':'center',
                           'angle':angle,
                           'bold':bold,
                           'text':prop['chart.title.yaxis'],
                           'tag':'title yaxis'
                          });
        }

        /**
        * If the background color is spec ified - draw that. It's a rectangle that fills the
        * entire are within the gutters
        */
        var bgcolor = prop['chart.background.color'];
        if (bgcolor) {
            co.fillStyle = bgcolor;
            co.fillRect(gutterLeft, gutterTop, ca.width - gutterLeft - gutterRight, ca.height - gutterTop - gutterBottom);
        }

        /**
        * Draw horizontal background bars
        */
        co.beginPath(); // Necessary?

        co.fillStyle   = prop['chart.background.barcolor1'];
        co.strokeStyle = co.fillStyle;
        height = (ca.height - gutterBottom);

        for (var i=gutterTop; i<height ; i+=80) {
            co.fillRect(gutterLeft, i, ca.width - gutterLeft - gutterRight, Math.min(40, ca.height - gutterBottom - i) );
        }

        co.fillStyle   = prop['chart.background.barcolor2'];
        co.strokeStyle = co.fillStyle;
        height = (ca.height - gutterBottom);

        for (var i= (40 + gutterTop); i<height; i+=80) {
            co.fillRect(gutterLeft, i, ca.width - gutterLeft - gutterRight, i + 40 > (ca.height - gutterBottom) ? ca.height - (gutterBottom + i) : 40);
        }
        
        //context.stroke();
        co.beginPath();
    

        // Draw the background grid
        if (prop['chart.background.grid']) {

            // If autofit is specified, use the .numhlines and .numvlines along with the width to work
            // out the hsize and vsize
            if (prop['chart.background.grid.autofit']) {

                /**
                * Align the grid to the tickmarks
                */
                if (prop['chart.background.grid.autofit.align']) {
                    
                    // Align the horizontal lines
                    obj.Set('chart.background.grid.autofit.numhlines', prop['chart.ylabels.count']);

                    // Align the vertical lines for the line
                    if (obj.type == 'line') {
                        if (prop['chart.labels'] && prop['chart.labels'].length) {
                            obj.Set('chart.background.grid.autofit.numvlines', prop['chart.labels'].length - 1);
                        } else {
                            obj.Set('chart.background.grid.autofit.numvlines', obj.data[0].length - 1);
                        }

                    // Align the vertical lines for the bar
                    } else if (obj.type == 'bar' && prop['chart.labels'] && prop['chart.labels'].length) {
                        obj.Set('chart.background.grid.autofit.numvlines', prop['chart.labels'].length);
                    }
                }

                var vsize = ((ca.width - gutterLeft - gutterRight)) / prop['chart.background.grid.autofit.numvlines'];
                var hsize = (ca.height - gutterTop - gutterBottom) / prop['chart.background.grid.autofit.numhlines'];

                obj.Set('chart.background.grid.vsize', vsize);
                obj.Set('chart.background.grid.hsize', hsize);
            }

            co.beginPath();
            co.lineWidth   = prop['chart.background.grid.width'] ? prop['chart.background.grid.width'] : 1;
            co.strokeStyle = prop['chart.background.grid.color'];

            // Dashed background grid
            if (prop['chart.background.grid.dashed'] && typeof co.setLineDash == 'function') {
                co.setLineDash([3,2]);
            }
            
            // Dotted background grid
            if (prop['chart.background.grid.dotted'] && typeof co.setLineDash == 'function') {
                co.setLineDash([1,2]);
            }


            // Draw the horizontal lines
            if (prop['chart.background.grid.hlines']) {
                height = (ca.height - gutterBottom)
                for (y=gutterTop; y<height; y+=prop['chart.background.grid.hsize']) {
                    context.moveTo(gutterLeft, Math.round(y));
                    context.lineTo(ca.width - gutterRight, Math.round(y));
                }
            }

            if (prop['chart.background.grid.vlines']) {
                // Draw the vertical lines
                var width = (ca.width - gutterRight)
                for (x=gutterLeft; x<=width; x+=prop['chart.background.grid.vsize']) {
                    co.moveTo(Math.round(x), gutterTop);
                    co.lineTo(Math.round(x), ca.height - gutterBottom);
                }
            }

            if (prop['chart.background.grid.border']) {
                // Make sure a rectangle, the same colour as the grid goes around the graph
                co.strokeStyle = prop['chart.background.grid.color'];
                co.strokeRect(Math.round(gutterLeft), Math.round(gutterTop), ca.width - gutterLeft - gutterRight, ca.height - gutterTop - gutterBottom);
            }
        }

        context.stroke();

        // Reset the line dash
        if (typeof co.setLineDash == 'function') {
            co.setLineDash([1,0]);
        }

        // If it's a bar and 3D variant, translate
        if (variant == '3d') {
            co.restore();
        }

        // Draw the title if one is set
        if ( typeof(prop['chart.title']) == 'string') {

            if (obj.type == 'gantt') {
                gutterTop -= 10;
            }

            RG.DrawTitle(obj,
                         prop['chart.title'],
                         gutterTop,
                         null,
                         prop['chart.title.size'] ? prop['chart.title.size'] : prop['chart.text.size'] + 2);
        }

        co.stroke();
    }




    /**
    * Makes a clone of an object
    * 
    * @param obj val The object to clone
    */
    RGraph.array_clone = function (obj)
    {
        var RG = RGraph;

        if(obj == null || typeof(obj) != 'object') {
            return obj;
        }

        var temp = [];

        for (var i=0,len=obj.length;i<len; ++i) {

            if (typeof(obj[i]) == 'number') {
                temp[i] = (function (arg) {return Number(arg);})(obj[i]);
            } else if (typeof(obj[i]) == 'string') {
                temp[i] = (function (arg) {return String(arg);})(obj[i]);
            } else if (typeof(obj[i]) == 'function') {
                temp[i] = obj[i];
            
            } else {
                temp[i] = RG.array_clone(obj[i]);
            }
        }

        return temp;
    }




    /**
    * Formats a number with thousand seperators so it's easier to read
    * 
    * @param  integer obj The chart object
    * @param  integer num The number to format
    * @param  string      The (optional) string to prepend to the string
    * @param  string      The (optional) string to append to the string
    * @return string      The formatted number
    */
    RGraph.number_format = function (obj, num)
    {
        var RG   = RGraph;
        var ca   = obj.canvas;
        var co   = obj.context;
        var prop = obj.properties;

        var i;
        var prepend = arguments[2] ? String(arguments[2]) : '';
        var append  = arguments[3] ? String(arguments[3]) : '';
        var output  = '';
        var decimal = '';
        var decimal_seperator  = typeof(prop['chart.scale.point']) == 'string' ? prop['chart.scale.point'] : '.';
        var thousand_seperator = typeof(prop['chart.scale.thousand']) == 'string' ? prop['chart.scale.thousand'] : ',';
        RegExp.$1   = '';
        var i,j;

        if (typeof(prop['chart.scale.formatter']) == 'function') {
            return prop['chart.scale.formatter'](obj, num);
        }

        // Ignore the preformatted version of "1e-2"
        if (String(num).indexOf('e') > 0) {
            return String(prepend + String(num) + append);
        }

        // We need then number as a string
        num = String(num);
        
        // Take off the decimal part - we re-append it later
        if (num.indexOf('.') > 0) {
            var tmp = num;
            num     = num.replace(/\.(.*)/, ''); // The front part of the number
            decimal = tmp.replace(/(.*)\.(.*)/, '$2'); // The decimal part of the number
        }

        // Thousand seperator
        //var seperator = arguments[1] ? String(arguments[1]) : ',';
        var seperator = thousand_seperator;
        
        /**
        * Work backwards adding the thousand seperators
        */
        var foundPoint;
        for (i=(num.length - 1),j=0; i>=0; j++,i--) {
            var character = num.charAt(i);
            
            if ( j % 3 == 0 && j != 0) {
                output += seperator;
            }
            
            /**
            * Build the output
            */
            output += character;
        }
        
        /**
        * Now need to reverse the string
        */
        var rev = output;
        output = '';
        for (i=(rev.length - 1); i>=0; i--) {
            output += rev.charAt(i);
        }

        // Tidy up
        //output = output.replace(/^-,/, '-');
        if (output.indexOf('-' + prop['chart.scale.thousand']) == 0) {
            output = '-' + output.substr(('-' + prop['chart.scale.thousand']).length);
        }

        // Reappend the decimal
        if (decimal.length) {
            output =  output + decimal_seperator + decimal;
            decimal = '';
            RegExp.$1 = '';
        }

        // Minor bugette
        if (output.charAt(0) == '-') {
            output = output.replace(/-/, '');
            prepend = '-' + prepend;
        }

        return prepend + output + append;
    }




    /**
    * Draws horizontal coloured bars on something like the bar, line or scatter
    */
    RGraph.DrawBars = function (obj)
    {
        var prop  = obj.properties;
        var co    = obj.context;
        var ca    = obj.canvas;
        var RG    = RGraph;
        var hbars = prop['chart.background.hbars'];

        if (hbars === null) {
            return;
        }

        /**
        * Draws a horizontal bar
        */
        co.beginPath();

        for (i=0,len=hbars.length; i<len; ++i) {
        
            var start  = hbars[i][0];
            var length = hbars[i][1];
            var color  = hbars[i][2];
            

            // Perform some bounds checking
            if(RG.is_null(start))start = obj.scale2.max
            if (start > obj.scale2.max) start = obj.scale2.max;
            if (RG.is_null(length)) length = obj.scale2.max - start;
            if (start + length > obj.scale2.max) length = obj.scale2.max - start;
            if (start + length < (-1 * obj.scale2.max) ) length = (-1 * obj.scale2.max) - start;

            if (prop['chart.xaxispos'] == 'center' && start == obj.scale2.max && length < (obj.scale2.max * -2)) {
                length = obj.scale2.max * -2;
            }


            /**
            * Draw the bar
            */
            var x = prop['chart.gutter.left'];
            var y = obj.getYCoord(start);
            var w = ca.width - prop['chart.gutter.left'] - prop['chart.gutter.right'];
            var h = obj.getYCoord(start + length) - y;

            // Accommodate Opera :-/
            if (ISOPERA != -1 && prop['chart.xaxispos'] == 'center' && h < 0) {
                h *= -1;
                y = y - h;
            }

            /**
            * Account for X axis at the top
            */
            if (prop['chart.xaxispos'] == 'top') {
                y  = ca.height - y;
                h *= -1;
            }

            co.fillStyle = color;
            co.fillRect(x, y, w, h);
        }
/*


            


            // If the X axis is at the bottom, and a negative max is given, warn the user
            if (obj.Get('chart.xaxispos') == 'bottom' && (hbars[i][0] < 0 || (hbars[i][1] + hbars[i][1] < 0)) ) {
                alert('[' + obj.type.toUpperCase() + ' (ID: ' + obj.id + ') BACKGROUND HBARS] You have a negative value in one of your background hbars values, whilst the X axis is in the center');
            }

            var ystart = (obj.grapharea - (((hbars[i][0] - obj.scale2.min) / (obj.scale2.max - obj.scale2.min)) * obj.grapharea));
            //var height = (Math.min(hbars[i][1], obj.max - hbars[i][0]) / (obj.scale2.max - obj.scale2.min)) * obj.grapharea;
            var height = obj.getYCoord(hbars[i][0]) - obj.getYCoord(hbars[i][1]);

            // Account for the X axis being in the center
            if (obj.Get('chart.xaxispos') == 'center') {
                ystart /= 2;
                //height /= 2;
            }
            
            ystart += obj.Get('chart.gutter.top')

            var x = obj.Get('chart.gutter.left');
            var y = ystart - height;
            var w = obj.canvas.width - obj.Get('chart.gutter.left') - obj.Get('chart.gutter.right');
            var h = height;

            // Accommodate Opera :-/
            if (navigator.userAgent.indexOf('Opera') != -1 && obj.Get('chart.xaxispos') == 'center' && h < 0) {
                h *= -1;
                y = y - h;
            }
            
            /**
            * Account for X axis at the top
            */
            //if (obj.Get('chart.xaxispos') == 'top') {
            //    y  = obj.canvas.height - y;
            //    h *= -1;
            //}

            //obj.context.fillStyle = hbars[i][2];
            //obj.context.fillRect(x, y, w, h);
        //}
    }




    /**
    * Draws in-graph labels.
    * 
    * @param object obj The graph object
    */
    RGraph.DrawInGraphLabels = function (obj)
    {
        var RG      = RGraph;
        var ca      = obj.canvas;
        var co      = obj.context;
        var prop    = obj.properties;
        var labels  = prop['chart.labels.ingraph'];
        var labels_processed = [];

        // Defaults
        var fgcolor   = 'black';
        var bgcolor   = 'white';
        var direction = 1;

        if (!labels) {
            return;
        }

        /**
        * Preprocess the labels array. Numbers are expanded
        */
        for (var i=0,len=labels.length; i<len; i+=1) {
            if (typeof(labels[i]) == 'number') {
                for (var j=0; j<labels[i]; ++j) {
                    labels_processed.push(null);
                }
            } else if (typeof(labels[i]) == 'string' || typeof(labels[i]) == 'object') {
                labels_processed.push(labels[i]);
            
            } else {
                labels_processed.push('');
            }
        }

        /**
        * Turn off any shadow
        */
        RG.NoShadow(obj);

        if (labels_processed && labels_processed.length > 0) {

            for (var i=0,len=labels_processed.length; i<len; ++i) {
                if (labels_processed[i]) {
                    var coords = obj.coords[i];
                    
                    if (coords && coords.length > 0) {
                        var x      = (obj.type == 'bar' ? coords[0] + (coords[2] / 2) : coords[0]);
                        var y      = (obj.type == 'bar' ? coords[1] + (coords[3] / 2) : coords[1]);
                        var length = typeof(labels_processed[i][4]) == 'number' ? labels_processed[i][4] : 25;
    
                        co.beginPath();
                        co.fillStyle   = 'black';
                        co.strokeStyle = 'black';
                        
    
                        if (obj.type == 'bar') {
                        
                            /**
                            * X axis at the top
                            */
                            if (obj.Get('chart.xaxispos') == 'top') {
                                length *= -1;
                            }
    
                            if (prop['chart.variant'] == 'dot') {
                                co.moveTo(Math.round(x), obj.coords[i][1] - 5);
                                co.lineTo(Math.round(x), obj.coords[i][1] - 5 - length);
                                
                                var text_x = Math.round(x);
                                var text_y = obj.coords[i][1] - 5 - length;
                            
                            } else if (prop['chart.variant'] == 'arrow') {
                                co.moveTo(Math.round(x), obj.coords[i][1] - 5);
                                co.lineTo(Math.round(x), obj.coords[i][1] - 5 - length);
                                
                                var text_x = Math.round(x);
                                var text_y = obj.coords[i][1] - 5 - length;
                            
                            } else {
    
                                co.arc(Math.round(x), y, 2.5, 0, 6.28, 0);
                                co.moveTo(Math.round(x), y);
                                co.lineTo(Math.round(x), y - length);

                                var text_x = Math.round(x);
                                var text_y = y - length;
                            }

                            co.stroke();
                            co.fill();
                            
    
                        } else if (obj.type == 'line') {
                        
                            if (
                                typeof(labels_processed[i]) == 'object' &&
                                typeof(labels_processed[i][3]) == 'number' &&
                                labels_processed[i][3] == -1
                               ) {

                                co.moveTo(Math.round(x), y + 5);
                                co.lineTo(Math.round(x), y + 5 + length);
                                
                                co.stroke();
                                co.beginPath();                                
                                
                                // This draws the arrow
                                co.moveTo(Math.round(x), y + 5);
                                co.lineTo(Math.round(x) - 3, y + 10);
                                co.lineTo(Math.round(x) + 3, y + 10);
                                co.closePath();
                                
                                var text_x = x;
                                var text_y = y + 5 + length;
                            
                            } else {
                                
                                var text_x = x;
                                var text_y = y - 5 - length;

                                co.moveTo(Math.round(x), y - 5);
                                co.lineTo(Math.round(x), y - 5 - length);
                                
                                co.stroke();
                                co.beginPath();
                                
                                // This draws the arrow
                                co.moveTo(Math.round(x), y - 5);
                                co.lineTo(Math.round(x) - 3, y - 10);
                                co.lineTo(Math.round(x) + 3, y - 10);
                                co.closePath();
                            }
                        
                            co.fill();
                        }

                        // Taken out on the 10th Nov 2010 - unnecessary
                        //var width = context.measureText(labels[i]).width;
                        
                        co.beginPath();
                            
                            // Fore ground color
                            co.fillStyle = (typeof(labels_processed[i]) == 'object' && typeof(labels_processed[i][1]) == 'string') ? labels_processed[i][1] : 'black';

                            RG.Text2(obj,{'font':prop['chart.text.font'],
                                          'size':prop['chart.text.size'],
                                          'x':text_x,
                                          'y':text_y,
                                          'text': (typeof(labels_processed[i]) == 'object' && typeof(labels_processed[i][0]) == 'string') ? labels_processed[i][0] : labels_processed[i],
                                          'valign': 'bottom',
                                          'halign':'center',
                                          'bounding':true,
                                          'bounding.fill': (typeof(labels_processed[i]) == 'object' && typeof(labels_processed[i][2]) == 'string') ? labels_processed[i][2] : 'white',
                                          'tag':'labels ingraph'
                                         });
                        co.fill();
                    }
                }
            }
        }
    }




    /**
    * This function "fills in" key missing properties that various implementations lack
    * 
    * @param object e The event object
    */
    RGraph.FixEventObject = function (e)
    {
        if (ISOLD) {
            var e = event;

            e.pageX  = (event.clientX + document.body.scrollLeft);
            e.pageY  = (event.clientY + document.body.scrollTop);
            e.target = event.srcElement;
            
            if (!document.body.scrollTop && document.documentElement.scrollTop) {
                e.pageX += parseInt(document.documentElement.scrollLeft);
                e.pageY += parseInt(document.documentElement.scrollTop);
            }
        }

        
        // Any browser that doesn't implement stopPropagation() (MSIE)
        if (!e.stopPropagation) {
            e.stopPropagation = function () {window.event.cancelBubble = true;}
        }
        
        return e;
    }




    /**
    * Thisz function hides the crosshairs coordinates
    */
    RGraph.HideCrosshairCoords = function ()
    {
        var RG  = RGraph;
        var div = RG.Registry.Get('chart.coordinates.coords.div');

        if (   div
            && div.style.opacity == 1
            && div.__object__.Get('chart.crosshairs.coords.fadeout')
           ) {
            
            var style = RG.Registry.Get('chart.coordinates.coords.div').style;

            setTimeout(function() {style.opacity = 0.9;}, 25);
            setTimeout(function() {style.opacity = 0.8;}, 50);
            setTimeout(function() {style.opacity = 0.7;}, 75);
            setTimeout(function() {style.opacity = 0.6;}, 100);
            setTimeout(function() {style.opacity = 0.5;}, 125);
            setTimeout(function() {style.opacity = 0.4;}, 150);
            setTimeout(function() {style.opacity = 0.3;}, 175);
            setTimeout(function() {style.opacity = 0.2;}, 200);
            setTimeout(function() {style.opacity = 0.1;}, 225);
            setTimeout(function() {style.opacity = 0;}, 250);
            setTimeout(function() {style.display = 'none';}, 275);
        }
    }




    /**
    * Draws the3D axes/background
    */
    RGraph.Draw3DAxes = function (obj)
    {
        var prop = obj.properties;
        var co   = obj.context;
        var ca   = obj.canvas;

        var gutterLeft    = prop['chart.gutter.left'];
        var gutterRight   = prop['chart.gutter.right'];
        var gutterTop     = prop['chart.gutter.top'];
        var gutterBottom  = prop['chart.gutter.bottom'];


        co.strokeStyle = '#aaa';
        co.fillStyle = '#ddd';

        // Draw the vertical left side
        co.beginPath();
            co.moveTo(gutterLeft, gutterTop);
            co.lineTo(gutterLeft + 10, gutterTop - 5);
            co.lineTo(gutterLeft + 10, ca.height - gutterBottom - 5);
            co.lineTo(gutterLeft, ca.height - gutterBottom);
        co.closePath();
        
        co.stroke();
        co.fill();

        // Draw the bottom floor
        co.beginPath();
            co.moveTo(gutterLeft, ca.height - gutterBottom);
            co.lineTo(gutterLeft + 10, ca.height - gutterBottom - 5);
            co.lineTo(ca.width - gutterRight + 10,  ca.height - gutterBottom - 5);
            co.lineTo(ca.width - gutterRight, ca.height - gutterBottom);
        co.closePath();
        
        co.stroke();
        co.fill();
    }





    /**
    * This function attempts to "fill in" missing functions from the canvas
    * context object. Only two at the moment - measureText() nd fillText().
    * 
    * @param object context The canvas 2D context
    */
    RGraph.OldBrowserCompat = function (co)
    {
        if (!co) {
            return;
        }

        if (!co.measureText) {
        
            // This emulates the measureText() function
            co.measureText = function (text)
            {
                var textObj = document.createElement('DIV');
                textObj.innerHTML = text;
                textObj.style.position = 'absolute';
                textObj.style.top = '-100px';
                textObj.style.left = 0;
                document.body.appendChild(textObj);

                var width = {width: textObj.offsetWidth};
                
                textObj.style.display = 'none';
                
                return width;
            }
        }

        if (!co.fillText) {
            // This emulates the fillText() method
            co.fillText    = function (text, targetX, targetY)
            {
                return false;
            }
        }

        // If IE8, add addEventListener()
        if (!co.canvas.addEventListener) {
            window.addEventListener = function (ev, func, bubble)
            {
                return this.attachEvent('on' + ev, func);
            }

            co.canvas.addEventListener = function (ev, func, bubble)
            {
                return this.attachEvent('on' + ev, func);
            }
        }
    }




    /**
    * Draws a rectangle with curvy corners
    * 
    * @param co object The context
    * @param x number The X coordinate (top left of the square)
    * @param y number The Y coordinate (top left of the square)
    * @param w number The width of the rectangle
    * @param h number The height of the rectangle
    * @param   number The radius of the curved corners
    * @param   boolean Whether the top left corner is curvy
    * @param   boolean Whether the top right corner is curvy
    * @param   boolean Whether the bottom right corner is curvy
    * @param   boolean Whether the bottom left corner is curvy
    */
    RGraph.strokedCurvyRect = function (co, x, y, w, h)
    {
        // The corner radius
        var r = arguments[5] ? arguments[5] : 3;

        // The corners
        var corner_tl = (arguments[6] || arguments[6] == null) ? true : false;
        var corner_tr = (arguments[7] || arguments[7] == null) ? true : false;
        var corner_br = (arguments[8] || arguments[8] == null) ? true : false;
        var corner_bl = (arguments[9] || arguments[9] == null) ? true : false;

        co.beginPath();

            // Top left side
            co.moveTo(x + (corner_tl ? r : 0), y);
            co.lineTo(x + w - (corner_tr ? r : 0), y);
            
            // Top right corner
            if (corner_tr) {
                co.arc(x + w - r, y + r, r, PI + HALFPI, TWOPI, false);
            }

            // Top right side
            co.lineTo(x + w, y + h - (corner_br ? r : 0) );

            // Bottom right corner
            if (corner_br) {
                co.arc(x + w - r, y - r + h, r, TWOPI, HALFPI, false);
            }

            // Bottom right side
            co.lineTo(x + (corner_bl ? r : 0), y + h);

            // Bottom left corner
            if (corner_bl) {
                co.arc(x + r, y - r + h, r, HALFPI, PI, false);
            }

            // Bottom left side
            co.lineTo(x, y + (corner_tl ? r : 0) );

            // Top left corner
            if (corner_tl) {
                co.arc(x + r, y + r, r, PI, PI + HALFPI, false);
            }

        co.stroke();
    }




    /**
    * Draws a filled rectangle with curvy corners
    * 
    * @param context object The context
    * @param x       number The X coordinate (top left of the square)
    * @param y       number The Y coordinate (top left of the square)
    * @param w       number The width of the rectangle
    * @param h       number The height of the rectangle
    * @param         number The radius of the curved corners
    * @param         boolean Whether the top left corner is curvy
    * @param         boolean Whether the top right corner is curvy
    * @param         boolean Whether the bottom right corner is curvy
    * @param         boolean Whether the bottom left corner is curvy
    */
    RGraph.filledCurvyRect = function (co, x, y, w, h)
    {
        // The corner radius
        var r = arguments[5] ? arguments[5] : 3;

        // The corners
        var corner_tl = (arguments[6] || arguments[6] == null) ? true : false;
        var corner_tr = (arguments[7] || arguments[7] == null) ? true : false;
        var corner_br = (arguments[8] || arguments[8] == null) ? true : false;
        var corner_bl = (arguments[9] || arguments[9] == null) ? true : false;

        co.beginPath();

            // First draw the corners

            // Top left corner
            if (corner_tl) {
                co.moveTo(x + r, y + r);
                co.arc(x + r, y + r, r, PI, PI + HALFPI, false);
            } else {
                co.fillRect(x, y, r, r);
            }

            // Top right corner
            if (corner_tr) {
                co.moveTo(x + w - r, y + r);
                co.arc(x + w - r, y + r, r, PI + HALFPI, 0, false);
            } else {
                co.moveTo(x + w - r, y);
                co.fillRect(x + w - r, y, r, r);
            }


            // Bottom right corner
            if (corner_br) {
                co.moveTo(x + w - r, y + h - r);
                co.arc(x + w - r, y - r + h, r, 0, HALFPI, false);
            } else {
                co.moveTo(x + w - r, y + h - r);
                co.fillRect(x + w - r, y + h - r, r, r);
            }

            // Bottom left corner
            if (corner_bl) {
                co.moveTo(x + r, y + h - r);
                co.arc(x + r, y - r + h, r, HALFPI, PI, false);
            } else {
                co.moveTo(x, y + h - r);
                co.fillRect(x, y + h - r, r, r);
            }

            // Now fill it in
            co.fillRect(x + r, y, w - r - r, h);
            co.fillRect(x, y + r, r + 1, h - r - r);
            co.fillRect(x + w - r - 1, y + r, r + 1, h - r - r);

        co.fill();
    }




    /**
    * Hides the zoomed canvas
    */
    RGraph.HideZoomedCanvas = function ()
    {
        var interval = 15;
        var frames   = 10;

        if (typeof(__zoomedimage__) == 'object') {
            var obj  = __zoomedimage__.obj;
            var prop = obj.properties;
        } else {
            return;
        }

        if (prop['chart.zoom.fade.out']) {
            for (var i=frames,j=1; i>=0; --i, ++j) {
                if (typeof(__zoomedimage__) == 'object') {
                    setTimeout("__zoomedimage__.style.opacity = " + String(i / 10), j * interval);
                }
            }

            if (typeof(__zoomedbackground__) == 'object') {
                setTimeout("__zoomedbackground__.style.opacity = " + String(i / frames), j * interval);
            }
        }

        if (typeof(__zoomedimage__) == 'object') {
            setTimeout("__zoomedimage__.style.display = 'none'", prop['chart.zoom.fade.out'] ? (frames * interval) + 10 : 0);
        }

        if (typeof(__zoomedbackground__) == 'object') {
            setTimeout("__zoomedbackground__.style.display = 'none'", prop['chart.zoom.fade.out'] ? (frames * interval) + 10 : 0);
        }
    }




    /**
    * Adds an event handler
    * 
    * @param object obj   The graph object
    * @param string event The name of the event, eg ontooltip
    * @param object func  The callback function
    */
    RGraph.AddCustomEventListener = function (obj, name, func)
    {
        var RG = RGraph;

        if (typeof(RG.events[obj.uid]) == 'undefined') {
            RG.events[obj.uid] = [];
        }

        RG.events[obj.uid].push([obj, name, func]);
        
        return RG.events[obj.uid].length - 1;
    }




    /**
    * Used to fire one of the RGraph custom events
    * 
    * @param object obj   The graph object that fires the event
    * @param string event The name of the event to fire
    */
    RGraph.FireCustomEvent = function (obj, name)
    {
        var RG = RGraph;

        if (obj && obj.isRGraph) {
        
            // New style of adding custom events
            if (obj[name]) {
                (obj[name])(obj);
            }
            
            var uid = obj.uid;
    
            if (   typeof(uid) == 'string'
                && typeof(RG.events) == 'object'
                && typeof(RG.events[uid]) == 'object'
                && RG.events[uid].length > 0) {
    
                for(var j=0; j<RG.events[uid].length; ++j) {
                    if (RG.events[uid][j] && RG.events[uid][j][1] == name) {
                        RG.events[uid][j][2](obj);
                    }
                }
            }
        }
    }




    /**
    * If you prefer, you can use the SetConfig() method to set the configuration information
    * for your chart. You may find that setting the configuration this way eases reuse.
    * 
    * @param object obj    The graph object
    * @param object config The graph configuration information
    */
    RGraph.SetConfig = function (obj, config)
    {
        for (i in config) {
            if (typeof(i) == 'string') {
                obj.Set(i, config[i]);
            }
        }
        
        return obj;
    }




    /**
    * Clears all the custom event listeners that have been registered
    * 
    * @param    string Limits the clearing to this object ID
    */
    RGraph.RemoveAllCustomEventListeners = function ()
    {
        var RG = RGraph;
        var id = arguments[0];

        if (id && RG.events[id]) {
            RG.events[id] = [];
        } else {
            RG.events = [];
        }
    }




    /**
    * Clears a particular custom event listener
    * 
    * @param object obj The graph object
    * @param number i   This is the index that is return by .AddCustomEventListener()
    */
    RGraph.RemoveCustomEventListener = function (obj, i)
    {
        var RG = RGraph;

        if (   typeof(RG.events) == 'object'
            && typeof(RG.events[obj.id]) == 'object'
            && typeof(RG.events[obj.id][i]) == 'object') {
            
            RG.events[obj.id][i] = null;
        }
    }




    /**
    * This draws the background
    * 
    * @param object obj The graph object
    */
    RGraph.DrawBackgroundImage = function (obj)
    {
        var prop = obj.properties;
        var ca   = obj.canvas;
        var co   = obj.context;
        var RG   = RGraph;

        if (typeof(prop['chart.background.image']) == 'string') {
            if (typeof(ca.__rgraph_background_image__) == 'undefined') {
                var img = new Image();
                img.__object__  = obj;
                img.__canvas__  = ca;
                img.__context__ = co;
                img.src         = obj.Get('chart.background.image');
                
                ca.__rgraph_background_image__ = img;
            } else {
                img = ca.__rgraph_background_image__;
            }

            // When the image has loaded - redraw the canvas
            img.onload = function ()
            {
                obj.__rgraph_background_image_loaded__ = true;
                RG.Clear(ca);
                RG.RedrawCanvas(ca);
            }
                
            var gutterLeft   = obj.gutterLeft;
            var gutterRight  = obj.gutterRight;
            var gutterTop    = obj.gutterTop;
            var gutterBottom = obj.gutterBottom;
            var stretch      = prop['chart.background.image.stretch'];
            var align        = prop['chart.background.image.align'];
    
            // Handle chart.background.image.align
            if (typeof(align) == 'string') {
                if (align.indexOf('right') != -1) {
                    var x = ca.width - img.width - gutterRight;
                } else {
                    var x = gutterLeft;
                }
    
                if (align.indexOf('bottom') != -1) {
                    var y = ca.height - img.height - gutterBottom;
                } else {
                    var y = gutterTop;
                }
            } else {
                var x = gutterLeft || 25;
                var y = gutterTop || 25;
            }

            // X/Y coords take precedence over the align
            var x = typeof(prop['chart.background.image.x']) == 'number' ? prop['chart.background.image.x'] : x;
            var y = typeof(prop['chart.background.image.y']) == 'number' ? prop['chart.background.image.y'] : y;
            var w = stretch ? ca.width - gutterLeft - gutterRight : img.width;
            var h = stretch ? ca.height - gutterTop - gutterBottom : img.height;
            
            /**
            * You can now specify the width and height of the image
            */
            if (typeof(prop['chart.background.image.w']) == 'number') w  = prop['chart.background.image.w'];
            if (typeof(prop['chart.background.image.h']) == 'number') h = prop['chart.background.image.h'];

            co.drawImage(img,x,y,w, h);
        }
    }




    /**
    * This function determines wshether an object has tooltips or not
    * 
    * @param object obj The chart object
    */
    RGraph.hasTooltips = function (obj)
    {
        var prop = obj.properties;

        if (typeof(prop['chart.tooltips']) == 'object' && prop['chart.tooltips']) {
            for (var i=0,len=prop['chart.tooltips'].length; i<len; ++i) {
                if (!RGraph.is_null(obj.Get('chart.tooltips')[i])) {
                    return true;
                }
            }
        } else if (typeof(prop['chart.tooltips']) == 'function') {
            return true;
        }
        
        return false;
    }




    /**
    * This function creates a (G)UID which can be used to identify objects.
    * 
    * @return string (g)uid The (G)UID
    */
    RGraph.CreateUID = function ()
    {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c)
        {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
    }



    /**
    * This is the new object registry, used to facilitate multiple objects per canvas.
    * 
    * @param object obj The object to register
    */
    RGraph.ObjectRegistry.Add = function (obj)
    {
        var uid = obj.uid;
        var id  = obj.canvas.id;
        var RG = RGraph;

        /**
        * Index the objects by UID
        */
        RG.ObjectRegistry.objects.byUID.push([uid, obj]);
        
        /**
        * Index the objects by the canvas that they're drawn on
        */
        RG.ObjectRegistry.objects.byCanvasID.push([id, obj]);
    }




    /**
    * Remove an object from the object registry
    * 
    * @param object obj The object to remove.
    */
    RGraph.ObjectRegistry.Remove = function (obj)
    {
        var id  = obj.id;
        var uid = obj.uid;
        var RG  = RGraph;

        for (var i=0; i<RG.ObjectRegistry.objects.byUID.length; ++i) {
            if (RG.ObjectRegistry.objects.byUID[i] && RG.ObjectRegistry.objects.byUID[i][1].uid == uid) {
                RG.ObjectRegistry.objects.byUID[i] = null;
            }
        }


        for (var i=0; i<RG.ObjectRegistry.objects.byCanvasID.length; ++i) {
            if (   RG.ObjectRegistry.objects.byCanvasID[i]
                && RG.ObjectRegistry.objects.byCanvasID[i][1]
                && RG.ObjectRegistry.objects.byCanvasID[i][1].uid == uid) {
                
                RG.ObjectRegistry.objects.byCanvasID[i] = null;
            }
        }

    }




    /**
    * Removes all objects from the ObjectRegistry. If either the ID of a canvas is supplied,
    * or the canvas itself, then only objects pertaining to that canvas are cleared.
    * 
    * @param mixed   Either a canvas object (as returned by document.getElementById()
    *                or the ID of a canvas (ie a string)
    */
    RGraph.ObjectRegistry.Clear = function ()
    {
        var RG = RGraph;

        // If an ID is supplied restrict the learing to that
        if (arguments[0]) {
            var id      = (typeof(arguments[0]) == 'object' ? arguments[0].id : arguments[0]);
            var objects = RG.ObjectRegistry.getObjectsByCanvasID(id);

            for (var i=0; i<objects.length; ++i) {
                RG.ObjectRegistry.Remove(objects[i]);
            }

        } else {

            RG.ObjectRegistry.objects            = {};
            RG.ObjectRegistry.objects.byUID      = [];
            RG.ObjectRegistry.objects.byCanvasID = [];
        }
    }




    /**
    * Lists all objects in the ObjectRegistry
    * 
    * @param boolean ret Whether to return the list or alert() it
    */
    RGraph.ObjectRegistry.List = function ()
    {
        var list = [];
        var RG   = RGraph;

        for (var i=0,len=RG.ObjectRegistry.objects.byUID.length; i<len; ++i) {
            if (RG.ObjectRegistry.objects.byUID[i]) {
                list.push(RG.ObjectRegistry.objects.byUID[i][1].type);
            }
        }
        
        if (arguments[0]) {
            return list;
        } else {
            p(list);
        }
    }




    /**
    * Clears the ObjectRegistry of objects that are of a certain given type
    * 
    * @param type string The type to clear
    */
    RGraph.ObjectRegistry.ClearByType = function (type)
    {
        var RG      = RGraph;
        var objects = RG.ObjectRegistry.objects.byUID;

        for (var i=0; i<objects.length; ++i) {
            if (objects[i]) {
                var uid = objects[i][0];
                var obj = objects[i][1];
                
                if (obj && obj.type == type) {
                    RG.ObjectRegistry.Remove(obj);
                }
            }
        }
    }




    /**
    * This function provides an easy way to go through all of the objects that are held in the
    * Registry
    * 
    * @param func function This function is run for every object. Its passed the object as an argument
    * @param string type Optionally, you can pass a type of object to look for
    */
    RGraph.ObjectRegistry.Iterate = function (func)
    {
        var objects = RGraph.ObjectRegistry.objects.byUID;

        for (var i=0; i<objects.length; ++i) {
        
            if (typeof arguments[1] == 'string') {
                
                var types = arguments[1].split(/,/);

                for (var j=0; j<types.length; ++j) {
                    if (types[j] == objects[i][1].type) {
                        func(objects[i][1]);
                    }
                }
            } else {
                func(objects[i][1]);
            }
        }
    }




    /**
    * Retrieves all objects for a given canvas id
    * 
    * @patarm id string The canvas ID to get objects for.
    */
    RGraph.ObjectRegistry.getObjectsByCanvasID = function (id)
    {
        var store = RGraph.ObjectRegistry.objects.byCanvasID;
        var ret = [];

        // Loop through all of the objects and return the appropriate ones
        for (var i=0; i<store.length; ++i) {
            if (store[i] && store[i][0] == id ) {
                ret.push(store[i][1]);
            }
        }

        return ret;
    }




    /**
    * Retrieves the relevant object based on the X/Y position.
    * 
    * @param  object e The event object
    * @return object   The applicable (if any) object
    */
    RGraph.ObjectRegistry.getFirstObjectByXY =
    RGraph.ObjectRegistry.getObjectByXY = function (e)
    {
        var canvas  = e.target;
        var ret     = null;
        var objects = RGraph.ObjectRegistry.getObjectsByCanvasID(canvas.id);

        for (var i=(objects.length - 1); i>=0; --i) {

            var obj = objects[i].getObjectByXY(e);

            if (obj) {
                return obj;
            }
        }
    }




    /**
    * Retrieves the relevant objects based on the X/Y position.
    * NOTE This function returns an array of objects
    * 
    * @param  object e The event object
    * @return          An array of pertinent objects. Note the there may be only one object
    */
    RGraph.ObjectRegistry.getObjectsByXY = function (e)
    {
        var canvas  = e.target;
        var ret     = [];
        var objects = RGraph.ObjectRegistry.getObjectsByCanvasID(canvas.id);

        // Retrieve objects "front to back"
        for (var i=(objects.length - 1); i>=0; --i) {

            var obj = objects[i].getObjectByXY(e);

            if (obj) {
                ret.push(obj);
            }
        }
        
        return ret;
    }




    /**
    * Retrieves the object with the corresponding UID
    * 
    * @param string uid The UID to get the relevant object for
    */
    RGraph.ObjectRegistry.getObjectByUID = function (uid)
    {
        var objects = RGraph.ObjectRegistry.objects.byUID;

        for (var i=0; i<objects.length; ++i) {
            if (objects[i] && objects[i][1].uid == uid) {
                return objects[i][1];
            }
        }
    }




    /**
    * Brings a chart to the front of the ObjectRegistry by
    * removing it and then readding it at the end and then
    * redrawing the canvas
    * 
    * @param object  obj    The object to bring to the front
    * @param boolean redraw Whether to redraw the canvas after the 
    *                       object has been moved
    */
    RGraph.ObjectRegistry.bringToFront = function (obj)
    {
        var redraw = typeof arguments[1] == 'undefined' ? true : arguments[1];

        RGraph.ObjectRegistry.Remove(obj);
        RGraph.ObjectRegistry.Add(obj);
        
        if (redraw) {
            RGraph.RedrawCanvas(obj.canvas);
        }
    }




    /**
    * Retrieves the objects that are the given type
    * 
    * @param  mixed canvas  The canvas to check. It can either be the canvas object itself or just the ID
    * @param  string type   The type to look for
    * @return array         An array of one or more objects
    */
    RGraph.ObjectRegistry.getObjectsByType = function (type)
    {
        var objects = RGraph.ObjectRegistry.objects.byUID;
        var ret     = [];

        for (var i=0; i<objects.length; ++i) {

            if (objects[i] && objects[i][1] && objects[i][1].type && objects[i][1].type && objects[i][1].type == type) {
                ret.push(objects[i][1]);
            }
        }

        return ret;
    }




    /**
    * Retrieves the FIRST object that matches the given type
    *
    * @param  string type   The type of object to look for
    * @return object        The FIRST object that matches the given type
    */
    RGraph.ObjectRegistry.getFirstObjectByType = function (type)
    {
        var objects = RGraph.ObjectRegistry.objects.byUID;
    
        for (var i=0; i<objects.length; ++i) {
            if (objects[i] && objects[i][1] && objects[i][1].type == type) {
                return objects[i][1];
            }
        }
        
        return null;
    }




    /**
    * This takes centerx, centery, x and y coordinates and returns the
    * appropriate angle relative to the canvas angle system. Remember
    * that the canvas angle system starts at the EAST axis
    * 
    * @param  number cx  The centerx coordinate
    * @param  number cy  The centery coordinate
    * @param  number x   The X coordinate (eg the mouseX if coming from a click)
    * @param  number y   The Y coordinate (eg the mouseY if coming from a click)
    * @return number     The relevant angle (measured in in RADIANS)
    */
    RGraph.getAngleByXY = function (cx, cy, x, y)
    {
        var angle = Math.atan((y - cy) / (x - cx));
            angle = Math.abs(angle)

        if (x >= cx && y >= cy) {
            angle += TWOPI;

        } else if (x >= cx && y < cy) {
            angle = (HALFPI - angle) + (PI + HALFPI);

        } else if (x < cx && y < cy) {
            angle += PI;

        } else {
            angle = PI - angle;
        }

        /**
        * Upper and lower limit checking
        */
        if (angle > TWOPI) {
            angle -= TWOPI;
        }

        return angle;
    }




    /**
    * This function returns the distance between two points. In effect the
    * radius of an imaginary circle that is centered on x1 and y1. The name
    * of this function is derived from the word "Hypoteneuse", which in
    * trigonmetry is the longest side of a triangle
    * 
    * @param number x1 The original X coordinate
    * @param number y1 The original Y coordinate
    * @param number x2 The target X coordinate
    * @param number y2 The target Y  coordinate
    */
    RGraph.getHypLength = function (x1, y1, x2, y2)
    {
        var ret = Math.sqrt(((x2 - x1) * (x2 - x1)) + ((y2 - y1) * (y2 - y1)));

        return ret;
    }




    /**
    * This function gets the end point (X/Y coordinates) of a given radius.
    * You pass it the center X/Y and the radius and this function will return
    * the endpoint X/Y coordinates.
    * 
    * @param number cx The center X coord
    * @param number cy The center Y coord
    * @param number r  The lrngth of the radius
    */
    RGraph.getRadiusEndPoint = function (cx, cy, angle, radius)
    {
        var x = cx + (Math.cos(angle) * radius);
        var y = cy + (Math.sin(angle) * radius);
        
        return [x, y];
    }




    /**
    * This installs all of the event listeners
    * 
    * @param object obj The chart object
    */
    RGraph.InstallEventListeners = function (obj)
    {
        var RG   = RGraph;
        var prop = obj.properties;

        /**
        * Don't attempt to install event listeners for older versions of MSIE
        */
        if (ISOLD) {
            return;
        }

        /**
        * If this function exists, then the dynamic file has been included.
        */
        if (RG.InstallCanvasClickListener) {

            RG.InstallWindowMousedownListener(obj);
            RG.InstallWindowMouseupListener(obj);
            RG.InstallCanvasMousemoveListener(obj);
            RG.InstallCanvasMouseupListener(obj);
            RG.InstallCanvasMousedownListener(obj);
            RG.InstallCanvasClickListener(obj);
        
        } else if (   RG.hasTooltips(obj)
                   || prop['chart.adjustable']
                   || prop['chart.annotatable']
                   || prop['chart.contextmenu']
                   || prop['chart.resizable']
                   || prop['chart.key.interactive']
                   || prop['chart.events.click']
                   || prop['chart.events.mousemove']
                   || typeof obj.onclick == 'function'
                   || typeof obj.onmousemove == 'function'
                  ) {

            alert('[RGRAPH] You appear to have used dynamic features but not included the file: RGraph.common.dynamic.js');
        }
    }




    /**
    * Loosly mimicks the PHP function print_r();
    */
    RGraph.pr = function (obj)
    {
        var indent = (arguments[2] ? arguments[2] : '    ');
        var str    = '';

        var counter = typeof arguments[3] == 'number' ? arguments[3] : 0;
        
        if (counter >= 5) {
            return '';
        }
        
        switch (typeof obj) {
            
            case 'string':    str += obj + ' (' + (typeof obj) + ', ' + obj.length + ')'; break;
            case 'number':    str += obj + ' (' + (typeof obj) + ')'; break;
            case 'boolean':   str += obj + ' (' + (typeof obj) + ')'; break;
            case 'function':  str += 'function () {}'; break;
            case 'undefined': str += 'undefined'; break;
            case 'null':      str += 'null'; break;
            
            case 'object':
                // In case of null
                if (RGraph.is_null(obj)) {
                    str += indent + 'null\n';
                } else {
                    str += indent + 'Object {' + '\n'
                    for (j in obj) {
                        str += indent + '    ' + j + ' => ' + RGraph.pr(obj[j], true, indent + '    ', counter + 1) + '\n';
                    }
                    str += indent + '}';
                }
                break;
            
            
            default:
                str += 'Unknown type: ' + typeof obj + '';
                break;
        }


        /**
        * Finished, now either return if we're in a recursed call, or alert()
        * if we're not.
        */
        if (!arguments[1]) {
            alert(str);
        }
        
        return str;
    }




    /**
    * Produces a dashed line
    * 
    * @param object co The 2D context
    * @param number x1 The start X coordinate
    * @param number y1 The start Y coordinate
    * @param number x2 The end X coordinate
    * @param number y2 The end Y coordinate
    */
    RGraph.DashedLine = function(co, x1, y1, x2, y2)
    {
        /**
        * This is the size of the dashes
        */
        var size = 5;

        /**
        * The optional fifth argument can be the size of the dashes
        */
        if (typeof(arguments[5]) == 'number') {
            size = arguments[5];
        }

        var dx  = x2 - x1;
        var dy  = y2 - y1;
        var num = Math.floor(Math.sqrt((dx * dx) + (dy * dy)) / size);

        var xLen = dx / num;
        var yLen = dy / num;

        var count = 0;

        do {
            (count % 2 == 0 && count > 0) ? co.lineTo(x1, y1) : co.moveTo(x1, y1);

            x1 += xLen;
            y1 += yLen;
        } while(count++ <= num);
    }




    /**
    * Makes an AJAX call. It calls the given callback (a function) when ready
    * 
    * @param string   url      The URL to retrieve
    * @param function callback A function that is called when the response is ready, there's an example below
    *                          called "myCallback".
    */
    RGraph.AJAX = function (url, callback)
    {
        // Mozilla, Safari, ...
        if (window.XMLHttpRequest) {
            var httpRequest = new XMLHttpRequest();

        // MSIE
        } else if (window.ActiveXObject) {
            var httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
        }

        httpRequest.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200) {
                this.__user_callback__ = callback;
                this.__user_callback__(this.responseText);
            }
        }

        httpRequest.open('GET', url, true);
        httpRequest.send();
    }




    /**
    * Makes an AJAX POST request. It calls the given callback (a function) when ready
    * 
    * @param string   url      The URL to retrieve
    * @param object   data     The POST data
    * @param function callback A function that is called when the response is ready, there's an example below
    *                          called "myCallback".
    */
    RGraph.AJAX.POST = function (url, data, callback)
    {
        // Used when building the POST string
        var crumbs = [];

        // Mozilla, Safari, ...
        if (window.XMLHttpRequest) {
            var httpRequest = new XMLHttpRequest();

        // MSIE
        } else if (window.ActiveXObject) {
            var httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
        }

        httpRequest.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200) {
                this.__user_callback__ = callback;
                this.__user_callback__(this.responseText);
            }
        }

        httpRequest.open('POST', url, true);
        httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        
        for (i in data) {
            if (typeof i == 'string') {
                crumbs.push(i + '=' + encodeURIComponent(data[i]));
            }
        }

        httpRequest.send(crumbs.join('&'));
    }




    /**
    * Uses the above function but calls the call back passing a number as its argument
    * 
    * @param url string The URL to fetch
    * @param callback function Your callback function (which is passed the number as an argument)
    */
    RGraph.AJAX.getNumber = function (url, callback)
    {
        RGraph.AJAX(url, function ()
        {
            var num = parseFloat(this.responseText);

            callback(num);
        });
    }




    /**
    * Uses the above function but calls the call back passing a string as its argument
    * 
    * @param url string The URL to fetch
    * @param callback function Your callback function (which is passed the string as an argument)
    */
    RGraph.AJAX.getString = function (url, callback)
    {
        RGraph.AJAX(url, function ()
        {
            var str = String(this.responseText);

            callback(str);
        });
    }




    /**
    * Uses the above function but calls the call back passing JSON (ie a JavaScript object ) as its argument
    * 
    * @param url string The URL to fetch
    * @param callback function Your callback function (which is passed the JSON object as an argument)
    */
    RGraph.AJAX.getJSON = function (url, callback)
    {
        RGraph.AJAX(url, function ()
        {

            var json = eval('(' + this.responseText + ')');

            callback(json);
        });
    }




    /**
    * Uses the above RGraph.AJAX function but calls the call back passing an array as its argument.
    * Useful if you're retrieving CSV data
    * 
    * @param url string The URL to fetch
    * @param callback function Your callback function (which is passed the CSV/array as an argument)
    */
    RGraph.AJAX.getCSV = function (url, callback)
    {
        var seperator = arguments[2] ? arguments[2] : ',';

        RGraph.AJAX(url, function ()
        {
            var regexp = new RegExp(seperator);
            var arr = this.responseText.split(regexp);
            
            // Convert the strings to numbers
            for (var i=0,len=arr.length;i<len;++i) {
                arr[i] = parseFloat(arr[i]);
            }

            callback(arr);
        });
    }




    /**
    * Rotates the canvas
    * 
    * @param object canvas The canvas to rotate
    * @param  int   x      The X coordinate about which to rotate the canvas
    * @param  int   y      The Y coordinate about which to rotate the canvas
    * @param  int   angle  The angle(in RADIANS) to rotate the canvas by
    */
    RGraph.RotateCanvas = function (ca, x, y, angle)
    {
        var co = ca.getContext('2d');

        co.translate(x, y);
        co.rotate(angle);
        co.translate(0 - x, 0 - y);    
    }




    /**
    * Measures text by creating a DIV in the document and adding the relevant text to it.
    * Then checking the .offsetWidth and .offsetHeight.
    * 
    * @param  string text   The text to measure
    * @param  bool   bold   Whether the text is bold or not
    * @param  string font   The font to use
    * @param  size   number The size of the text (in pts)
    * @return array         A two element array of the width and height of the text
    */
    RGraph.MeasureText = function (text, bold, font, size)
    {
        // Add the sizes to the cache as adding DOM elements is costly and causes slow downs
        if (typeof(__rgraph_measuretext_cache__) == 'undefined') {
            __rgraph_measuretext_cache__ = [];
        }

        var str = text + ':' + bold + ':' + font + ':' + size;
        if (typeof(__rgraph_measuretext_cache__) == 'object' && __rgraph_measuretext_cache__[str]) {
            return __rgraph_measuretext_cache__[str];
        }
        
        if (!__rgraph_measuretext_cache__['text-div']) {
            var div = document.createElement('DIV');
                div.style.position = 'absolute';
                div.style.top = '-100px';
                div.style.left = '-100px';
            document.body.appendChild(div);
            
            // Now store the newly created DIV
            __rgraph_measuretext_cache__['text-div'] = div;

        } else if (__rgraph_measuretext_cache__['text-div']) {
            var div = __rgraph_measuretext_cache__['text-div'];
        }

        div.innerHTML = text.replace(/\r\n/g, '<br />');
        div.style.fontFamily = font;
        div.style.fontWeight = bold ? 'bold' : 'normal';
        div.style.fontSize = (size || 12) + 'pt';
        
        var size = [div.offsetWidth, div.offsetHeight];

        //document.body.removeChild(div);
        __rgraph_measuretext_cache__[str] = size;
        
        return size;
    }




    /* New text function. Accepts two arguments:
    *  o obj - The chart object
    *  o opt - An object/hash/map of properties. This can consist of:
    *          x                The X coordinate (REQUIRED)
    *          y                The Y coordinate (REQUIRED)
    *          text             The text to show (REQUIRED)
    *          font             The font to use
    *          size             The size of the text (in pt)
    *          bold             Whether the text shouldd be bold or not
    *          marker           Whether to show a marker that indicates the X/Y coordinates
    *          valign           The vertical alignment
    *          halign           The horizontal alignment
    *          bounding         Whether to draw a bounding box for the text
    *          boundingStroke   The strokeStyle of the bounding box
    *          boundingFill     The fillStyle of the bounding box
    */
    RGraph.Text2 = function (obj, opt)
    {
        /**
        * An RGraph object can be given, or a string or the 2D rendering context
        * The coords are placed on the obj.coordsText variable ONLY if it's an RGraph object. The function
        * still returns the cooords though in all cases.
        */
        if (obj && obj.isRGraph) {
            var co = obj.context;
            var ca = obj.canvas;
        } else if (typeof obj == 'string') {
            var ca = document.getElementById(obj);
            var co = ca.getContext('2d');
        } else if (typeof obj.getContext == 'function') {
            var ca = obj;
            var co = ca.getContext('2d');
        } else if (obj.toString().indexOf('CanvasRenderingContext2D') != -1) {
            var co = obj;
            var ca = obj.context;
        }

        var x              = opt.x;
        var y              = opt.y;
        var originalX      = x;
        var originalY      = y;
        var text           = opt.text;
        var text_multiline = text.split(/\r?\n/g);
        var numlines       = text_multiline.length;
        var font           = opt.font ? opt.font : 'Arial';
        var size           = opt.size ? opt.size : 10;
        var size_pixels    = size * 1.5;
        var bold           = opt.bold;
        var halign         = opt.halign ? opt.halign : 'left';
        var valign         = opt.valign ? opt.valign : 'bottom';
        var tag            = typeof opt.tag == 'string' && opt.tag.length > 0 ? opt.tag : '';
        var marker         = opt.marker;
        var angle          = opt.angle || 0;
        
        /**
        * Changed the name of boundingFill/boundingStroke - this allows you to still use those names
        */
        if (typeof opt.boundingFill == 'string')   opt['bounding.fill']   = opt.boundingFill;
        if (typeof opt.boundingStroke == 'string') opt['bounding.stroke'] = opt.boundingStroke;

        var bounding                = opt.bounding;
        var bounding_stroke         = opt['bounding.stroke'] ? opt['bounding.stroke'] : 'black';
        var bounding_fill           = opt['bounding.fill'] ? opt['bounding.fill'] : 'rgba(255,255,255,0.7)';
        var bounding_shadow         = opt['bounding.shadow'];
        var bounding_shadow_color   = opt['bounding.shadow.color'] || '#ccc';
        var bounding_shadow_blur    = opt['bounding.shadow.blur'] || 3;
        var bounding_shadow_offsetx = opt['bounding.shadow.offsetx'] || 3;
        var bounding_shadow_offsety = opt['bounding.shadow.offsety'] || 3;
        var bounding_linewidth      = opt['bounding.linewidth'] || 1;



        /**
        * Initialize the return value to an empty object
        */
        var ret = {};



        /**
        * The text arg must be a string or a number
        */
        if (typeof text == 'number') {
            text = String(text);
        }

        if (typeof text != 'string') {
            alert('[RGRAPH TEXT] The text given must a string or a number');
            return;
        }
        
        
        
        /**
        * This facilitates vertical text
        */
        if (angle != 0) {
            co.save();
            co.translate(x, y);
            co.rotate((Math.PI / 180) * angle)
            x = 0;
            y = 0;
        }


        
        /**
        * Set the font
        */
        co.font = (opt.bold ? 'bold ' : '') + size + 'pt ' + font;



        /**
        * Measure the width/height. This must be done AFTER the font has been set
        */
        var width=0;
        for (var i=0; i<numlines; ++i) {
            width = Math.max(width, co.measureText(text_multiline[i]).width);
        }
        var height = size_pixels * numlines;




        /**
        * Accommodate old MSIE 7/8
        */
        //if (document.all && ISOLD) {
            //y += 2;
        //}



        /**
        * If marker is specified draw a marker at the X/Y coordinates
        */
        if (opt.marker) {
            var marker_size = 10;
            var strokestyle = co.strokeStyle;
            co.beginPath();
                co.strokeStyle = 'red';
                co.moveTo(x, y - marker_size);
                co.lineTo(x, y + marker_size);
                co.moveTo(x - marker_size, y);
                co.lineTo(x + marker_size, y);
            co.stroke();
            co.strokeStyle = strokestyle;
        }



        /**
        * Set the horizontal alignment
        */
        if (halign == 'center') {
            co.textAlign = 'center';
            var boundingX = x - 2 - (width / 2);
        } else if (halign == 'right') {
            co.textAlign = 'right';
            var boundingX = x - 2 - width;
        } else {
            co.textAlign = 'left';
            var boundingX = x - 2;
        }


        /**
        * Set the vertical alignment
        */
        if (valign == 'center') {
            
            co.textBaseline = 'middle';
            // Move the text slightly
            y -= 1;
            
            y -= ((numlines - 1) / 2) * size_pixels;
            var boundingY = y - (size_pixels / 2) - 2;
        
        } else if (valign == 'top') {
            co.textBaseline = 'top';

            var boundingY = y - 2;

        } else {

            co.textBaseline = 'bottom';
            
            // Move the Y coord if multiline text
            if (numlines > 1) {
                y -= ((numlines - 1) * size_pixels);
            }

            var boundingY = y - size_pixels - 2;
        }
        
        var boundingW = width + 4;
        var boundingH = height + 4;



        /**
        * Draw a bounding box if required
        */
        if (bounding) {

            var pre_bounding_linewidth     = co.lineWidth;
            var pre_bounding_strokestyle   = co.strokeStyle;
            var pre_bounding_fillstyle     = co.fillStyle;
            var pre_bounding_shadowcolor   = co.shadowColor;
            var pre_bounding_shadowblur    = co.shadowBlur;
            var pre_bounding_shadowoffsetx = co.shadowOffsetX;
            var pre_bounding_shadowoffsety = co.shadowOffsetY;

            co.lineWidth   = bounding_linewidth;
            co.strokeStyle = bounding_stroke;
            co.fillStyle   = bounding_fill;

            if (bounding_shadow) {
                co.shadowColor   = bounding_shadow_color;
                co.shadowBlur    = bounding_shadow_blur;
                co.shadowOffsetX = bounding_shadow_offsetx;
                co.shadowOffsetY = bounding_shadow_offsety;
            }

            //obj.context.strokeRect(boundingX, boundingY, width + 6, (size_pixels * numlines) + 4);
            //obj.context.fillRect(boundingX, boundingY, width + 6, (size_pixels * numlines) + 4);
            co.strokeRect(boundingX, boundingY, boundingW, boundingH);
            co.fillRect(boundingX, boundingY, boundingW, boundingH);

            // Reset the linewidth,colors and shadow to it's original setting
            co.lineWidth     = pre_bounding_linewidth;
            co.strokeStyle   = pre_bounding_strokestyle;
            co.fillStyle     = pre_bounding_fillstyle;
            co.shadowColor   = pre_bounding_shadowcolor
            co.shadowBlur    = pre_bounding_shadowblur
            co.shadowOffsetX = pre_bounding_shadowoffsetx
            co.shadowOffsetY = pre_bounding_shadowoffsety
        }

        
        
        /**
        * Draw the text
        */
        if (numlines > 1) {
            for (var i=0; i<numlines; ++i) {
                co.fillText(text_multiline[i], x, y + (size_pixels * i));
            }
        } else {
            co.fillText(text, x, y);
        }
        
        
        
        /**
        * If the text is at 90 degrees restore() the canvas - getting rid of the rotation
        * and the translate that we did
        */
        if (angle != 0) {
            if (angle == 90) {
                if (halign == 'left') {
                    if (valign == 'bottom') {boundingX = originalX - 2; boundingY = originalY - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'center') {boundingX = originalX - (height / 2) - 2; boundingY = originalY - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'top')    {boundingX = originalX - height - 2; boundingY = originalY - 2; boundingW = height + 4; boundingH = width + 4;}
                
                } else if (halign == 'center') {
                    if (valign == 'bottom') {boundingX = originalX - 2; boundingY = originalY - (width / 2) - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'center') {boundingX = originalX - (height / 2) -  2; boundingY = originalY - (width / 2) - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'top')    {boundingX = originalX - height -  2; boundingY = originalY - (width / 2) - 2; boundingW = height + 4; boundingH = width + 4;}
                
                } else if (halign == 'right') {
                    if (valign == 'bottom') {boundingX = originalX - 2; boundingY = originalY - width - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'center') {boundingX = originalX - (height / 2) - 2; boundingY = originalY - width - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'top')    {boundingX = originalX - height - 2; boundingY = originalY - width - 2; boundingW = height + 4; boundingH = width + 4;}
                }

            } else if (angle == 180) {

                if (halign == 'left') {
                    if (valign == 'bottom') {boundingX = originalX - width - 2; boundingY = originalY - 2; boundingW = width + 4; boundingH = height + 4;}
                    if (valign == 'center') {boundingX = originalX - width - 2; boundingY = originalY - (height / 2) - 2; boundingW = width + 4; boundingH = height + 4;}
                    if (valign == 'top')    {boundingX = originalX - width - 2; boundingY = originalY - height - 2; boundingW = width + 4; boundingH = height + 4;}
                
                } else if (halign == 'center') {
                    if (valign == 'bottom') {boundingX = originalX - (width / 2) - 2; boundingY = originalY - 2; boundingW = width + 4; boundingH = height + 4;}
                    if (valign == 'center') {boundingX = originalX - (width / 2) - 2; boundingY = originalY - (height / 2) - 2; boundingW = width + 4; boundingH = height + 4;}
                    if (valign == 'top')    {boundingX = originalX - (width / 2) - 2; boundingY = originalY - height - 2; boundingW = width + 4; boundingH = height + 4;}
                
                } else if (halign == 'right') {
                    if (valign == 'bottom') {boundingX = originalX - 2; boundingY = originalY - 2; boundingW = width + 4; boundingH = height + 4;}
                    if (valign == 'center') {boundingX = originalX - 2; boundingY = originalY - (height / 2) - 2; boundingW = width + 4; boundingH = height + 4;}
                    if (valign == 'top')    {boundingX = originalX - 2; boundingY = originalY - height - 2; boundingW = width + 4; boundingH = height + 4;}
                }
            
            } else if (angle == 270) {

                if (halign == 'left') {
                    if (valign == 'bottom') {boundingX = originalX - height - 2; boundingY = originalY - width - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'center') {boundingX = originalX - (height / 2) - 4; boundingY = originalY - width - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'top')    {boundingX = originalX - 2; boundingY = originalY - width - 2; boundingW = height + 4; boundingH = width + 4;}
                
                } else if (halign == 'center') {
                    if (valign == 'bottom') {boundingX = originalX - height - 2; boundingY = originalY - (width/2) - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'center') {boundingX = originalX - (height/2) - 4; boundingY = originalY - (width/2) - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'top')    {boundingX = originalX - 2; boundingY = originalY - (width/2) - 2; boundingW = height + 4; boundingH = width + 4;}
                
                } else if (halign == 'right') {
                    if (valign == 'bottom') {boundingX = originalX - height - 2; boundingY = originalY - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'center') {boundingX = originalX - (height/2) - 2; boundingY = originalY - 2; boundingW = height + 4; boundingH = width + 4;}
                    if (valign == 'top')    {boundingX = originalX - 2; boundingY = originalY - 2; boundingW = height + 4; boundingH = width + 4;}
                }
            }

            co.restore();
        }




        /**
        * Reset the text alignment so that text rendered
        */
        co.textBaseline = 'alphabetic';
        co.textAlign    = 'left';





        /**
        * Fill the ret variable with details of the text
        */
        ret.x      = boundingX;
        ret.y      = boundingY;
        ret.width  = boundingW;
        ret.height = boundingH
        ret.object = obj;
        ret.text   = text;
        ret.tag    = tag;



        /**
        * Save and then return the details of the text (but oly
        * if it's an RGraph object that was given)
        */
        if (obj && obj.isRGraph && obj.coordsText) {
            obj.coordsText.push(ret);
        }

        return ret;
    }




    /**
    * Takes a sequential index abd returns the group/index variation of it. Eg if you have a
    * sequential index from a grouped bar chart this function can be used to convert that into
    * an appropriate group/index combination
    * 
    * @param nindex number The sequential index
    * @param data   array  The original data (which is grouped)
    * @return              The group/index information
    */
    RGraph.sequentialIndexToGrouped = function (index, data)
    {
        var group         = 0;
        var grouped_index = 0;

        while (--index >= 0) {

            if (RGraph.is_null(data[group])) {
                group++;
                grouped_index = 0;
                continue;
            }

            // Allow for numbers as well as arrays in the dataset
            if (typeof data[group] == 'number') {
                group++
                grouped_index = 0;
                continue;
            }
            

            grouped_index++;
            
            if (grouped_index >= data[group].length) {
                group++;
                grouped_index = 0;
            }
        }
        
        return [group, grouped_index];
    }




    /**
    * Similar to the jQuery each() function - this lets you iterate easily over an array. The 'this' variable is set]
    * to the array in the callback function.
    * 
    * @param array    arr The array
    * @param function func The function to call
    * @param object        Optionally you can specify the object that the "this" variable is set to
    */
    RGraph.each = function (arr, func)
    {
        for(var i=0, len=arr.length; i<len; i+=1) {
                
            if (typeof arguments[2] !== 'undefined') {
                var ret = func.call(arguments[2], i, arr[i]);
            } else {
                var ret = func.call(arr, i, arr[i]);
            }
            
            if (ret === false) {
                return;
            }
        }
    }




    /**
    * Checks whether strings or numbers are empty or not. It also
    * handles null or variables set to undefined. If a variable really
    * is undefined - ie it hasn't been declared at all - you need to use
    * "typeof variable" and check the return value - which will be undefined.
    * 
    * @param mixed value The variable to check
    */
    function empty (value)
    {
        if (!value || value.length <= 0) {
            return true;
        }
        
        return false;
    }




    /**
    * This function highlights a rectangle
    * 
    * @param object obj    The chart object
    * @param number shape  The coordinates of the rect to highlight
    */
    RGraph.Highlight.Rect = function (obj, shape)
    {        
        var ca   = obj.canvas;
        var co   = obj.context;
        var prop = obj.properties;

        if (prop['chart.tooltips.highlight']) {
            
        
            // Safari seems to need this
            co.lineWidth = 1;

            /**
            * Draw a rectangle on the canvas to highlight the appropriate area
            */
            co.beginPath();

                co.strokeStyle = prop['chart.highlight.stroke'];
                co.fillStyle   = prop['chart.highlight.fill'];
    
                co.strokeRect(shape['x'],shape['y'],shape['width'],shape['height']);
                co.fillRect(shape['x'],shape['y'],shape['width'],shape['height']);
            co.stroke;
            co.fill();
        }
    }




    /**
    * This function highlights a point
    * 
    * @param object obj    The chart object
    * @param number shape  The coordinates of the rect to highlight
    */
    RGraph.Highlight.Point = function (obj, shape)
    {
        var prop = obj.properties;
        var ca   = obj.canvas;
        var co   = obj.context;

        if (prop['chart.tooltips.highlight']) {
    
            /**
            * Draw a rectangle on the canvas to highlight the appropriate area
            */
            co.beginPath();
                co.strokeStyle = prop['chart.highlight.stroke'];
                co.fillStyle   = prop['chart.highlight.fill'];
                var radius   = prop['chart.highlight.point.radius'] || 2;
                co.arc(shape['x'],shape['y'],radius, 0, TWOPI, 0);
            co.stroke();
            co.fill();
        }
    }




    /**
    * Creates an HTML tag
    * 
    * @param string type
    * @param obj    parent
    * @param obj
    * @param obj
    */
    RGraph.HTML.create = function (type, parent)
    {
        var obj = document.createElement(type);




        // Add the attributes
        if (arguments[2]) {
            this.attr(obj, arguments[2]);
        }




        // Add the styles
        if (arguments[3]) {
            this.css(obj, arguments[3]);
        }




        /**
        * Add the tag to the object that has been provided (usually the document)
        */
        parent.appendChild(obj);


        return obj;
    }




    /**
    * Sets attributes on a HTML object
    * 
    * @param object obj
    * @param object attr
    */
    RGraph.HTML.attr = function (obj, attr)
    {
        for (i in attr) {
            if (typeof i == 'string') {
                obj[i] = attr[i];
            }
        }
    }




    /**
    * Sets CSS on a HTML object
    * 
    * @param object obj
    * @param object css
    */
    RGraph.HTML.css = function (obj, styles)
    {
        var style = obj.style;

        for (i in styles) {
            if (typeof i == 'string') {
                style[i] = styles[i];
            }
        }
    }




    /**
    * This is the same as Date.parse - though a little more flexible.
    * 
    * @param string str The date string to parse
    * @return Returns the same thing as Date.parse
    */
    RGraph.parseDate = function (str)
    {
        str.trim();

        // Allow for: now (just the word "now")
        if (str === 'now') {
            str = (new Date()).toString();
        }

        // Allow for: 2013-11-22 12:12:12 or  2013/11/22 12:12:12
        if (str.match(/^(\d\d\d\d)(-|\/)(\d\d)(-|\/)(\d\d)( |T)(\d\d):(\d\d):(\d\d)$/)) {
            str = RegExp.$1 + '-' + RegExp.$3 + '-' + RegExp.$5 + 'T' + RegExp.$7 + ':' + RegExp.$8 + ':' + RegExp.$9;
        }

        // Allow for: 2013-11-22
        if (str.match(/^\d\d\d\d-\d\d-\d\d$/)) {
            str = str.replace(/-/, '/');
        }

        // Allow for: 12:09:44 (time only using todays date)
        if (str.match(/^\d\d:\d\d:\d\d$/)) {
        
            var dateObj  = new Date();
            var date     = dateObj.getDate();
            var month    = dateObj.getMonth() + 1;
            var year     = dateObj.getFullYear();
            
            str = (year + '-' + month + '-' + date) + ' ' + str;
        }

        return Date.parse(str);
    }




    // Reset all of the color values to their original values
    RGraph.resetColorsToOriginalValues = function (obj)
    {
        if (obj.original_colors) {
            // Reset the colors to their original values
            for (var j in obj.original_colors) {
                if (typeof j === 'string') {
                    obj.properties[j] = RGraph.array_clone(obj.original_colors[j]);
                }
            }
        }
        
        // Reset the colorsParsed flag so that they're parsed for gradients again
        obj.colorsParsed = false;
    }




    /**
    * This function is a short-cut for the canvas path syntax (which can be rather verbose)
    * 
    * @param mixed  obj  This can either be the 2D context or an RGraph object
    * @param array  path The path details
    */
    RGraph.Path = function (obj, path)
    {
        /**
        * Allow either the RGraph object or the context to be used as the first argument
        */
        if (obj.isRGraph && typeof obj.type === 'string') {
            var co = obj.context;
        } else if (obj.toString().indexOf('CanvasRenderingContext2D') > 0) {
            var co = obj;
        }

        /**
        * If the Path information has been passed as a  string - split it up
        */
        if (typeof path == 'string') {
            path = path.split(/ +/);
        }

        /**
        * Go through the path information
        */
        for (var i=0,len=path.length; i<len; i+=1) {
            
            var op = path[i];
            
            // 100,100,50,0,Math.PI * 1.5, false
            switch (op) {
                case 'b':co.beginPath();break;
                case 'c':co.closePath();break;
                case 'm':co.moveTo(parseFloat(path[i+1]),parseFloat(path[i+2]));i+=2;break;
                case 'l':co.lineTo(parseFloat(path[i+1]),parseFloat(path[i+2]));i+=2;break;
                case 's':co.strokeStyle=path[i+1];co.stroke();i+=1;break;
                case 'f':co.fillStyle=path[i+1];co.fill();i+=1;break;
                case 'qc':co.quadraticCurveTo(parseFloat(path[i+1]),parseFloat(path[i+2]),parseFloat(path[i+3]),parseFloat(path[i+4]));i+=4;break;
                case 'bc':co.bezierCurveTo(parseFloat(path[i+1]),parseFloat(path[i+2]),parseFloat(path[i+3]),parseFloat(path[i+4]),parseFloat(path[i+5]),parseFloat(path[i+6]));i+=6;break;
                case 'r':co.rect(parseFloat(path[i+1]),parseFloat(path[i+2]),parseFloat(path[i+3]),parseFloat(path[i+4]));i+=4;break;
                case 'a':co.arc(parseFloat(path[i+1]),parseFloat(path[i+2]),parseFloat(path[i+3]),parseFloat(path[i+4]),parseFloat(path[i+5]),path[i+6]==='true'||path[i+6]===true?true:false);i+=6;break;
                case 'at':co.arcTo(parseFloat(path[i+1]),parseFloat(path[i+2]),parseFloat(path[i+3]),parseFloat(path[i+4]),parseFloat(path[i+5]));i+=5;break;
                case 'lw':co.lineWidth=parseFloat(path[i+1]);i+=1;break;
                case 'lj':co.lineJoin=path[i+1];i+=1;break;
                case 'lc':co.lineCap=path[i+1];i+=1;break;
                case 'sc':co.shadowColor=path[i+1];i+=1;break;
                case 'sb':co.shadowBlur=parseFloat(path[i+1]);i+=1;break;
                case 'sx':co.shadowOffsetX=parseFloat(path[i+1]);i+=1;break;
                case 'sy':co.shadowOffsetY=parseFloat(path[i+1]);i+=1;break;
                case 'fu':(path[i+1])(obj);i+=1;break;
            }
        }
    }



// Some other functions. Because they're rarely changed - they're hand minified
RGraph.LinearGradient=function(obj,x1,y1,x2,y2,color1,color2){var gradient=obj.context.createLinearGradient(x1,y1,x2,y2);var numColors=arguments.length-5;for (var i=5;i<arguments.length;++i){var color=arguments[i];var stop=(i-5)/(numColors-1);gradient.addColorStop(stop,color);}return gradient;}
RGraph.RadialGradient=function(obj,x1,y1,r1,x2,y2,r2,color1,color2){var gradient=obj.context.createRadialGradient(x1,y1,r1,x2,y2,r2);var numColors=arguments.length-7;for(var i=7;i<arguments.length; ++i){var color=arguments[i];var stop=(i-7)/(numColors-1);gradient.addColorStop(stop,color);}return gradient;}
RGraph.array_shift=function(arr){var ret=[];for(var i=1;i<arr.length;++i){ret.push(arr[i]);}return ret;}
RGraph.AddEventListener=function(id,e,func){var type=arguments[3]?arguments[3]:'unknown';RGraph.Registry.Get('chart.event.handlers').push([id,e,func,type]);}
RGraph.ClearEventListeners=function(id){if(id&&id=='window'){window.removeEventListener('mousedown',window.__rgraph_mousedown_event_listener_installed__,false);window.removeEventListener('mouseup',window.__rgraph_mouseup_event_listener_installed__,false);}else{var canvas = document.getElementById(id);canvas.removeEventListener('mouseup',canvas.__rgraph_mouseup_event_listener_installed__,false);canvas.removeEventListener('mousemove',canvas.__rgraph_mousemove_event_listener_installed__,false);canvas.removeEventListener('mousedown',canvas.__rgraph_mousedown_event_listener_installed__,false);canvas.removeEventListener('click',canvas.__rgraph_click_event_listener_installed__,false);}}
RGraph.HidePalette=function(){var div=RGraph.Registry.Get('palette');if(typeof(div)=='object'&&div){div.style.visibility='hidden';div.style.display='none';RGraph.Registry.Set('palette',null);}}
RGraph.random=function(min,max){var dp=arguments[2]?arguments[2]:0;var r=Math.random();return Number((((max - min) * r) + min).toFixed(dp));}
RGraph.random.array=function(num,min,max){var arr = [];for(var i=0;i<num;i++)arr.push(RGraph.random(min,max));return arr;}
RGraph.NoShadow=function(obj){obj.context.shadowColor='rgba(0,0,0,0)';obj.context.shadowBlur=0;obj.context.shadowOffsetX=0;obj.context.shadowOffsetY=0;}
RGraph.SetShadow=function(obj,color,offsetx,offsety,blur){obj.context.shadowColor=color;obj.context.shadowOffsetX=offsetx;obj.context.shadowOffsetY=offsety;obj.context.shadowBlur=blur;}
RGraph.array_reverse=function(arr){var newarr=[];for(var i=arr.length-1;i>=0;i--){newarr.push(arr[i]);}return newarr;}
RGraph.Registry.Set=function(name,value){RGraph.Registry.store[name]=value;return value;}
RGraph.Registry.Get=function(name){return RGraph.Registry.store[name];}
RGraph.degrees2Radians=function(degrees){return degrees*(PI/180);}
RGraph.log=(function(n,base){var log=Math.log;return function(n,base){return log(n)/(base?log(base):1);};})();
RGraph.is_array=function(obj){return obj!=null&&obj.constructor.toString().indexOf('Array')!=-1;}
RGraph.trim=function(str){return RGraph.ltrim(RGraph.rtrim(str));}
RGraph.ltrim=function(str){return str.replace(/^(\s|\0)+/, '');}
RGraph.rtrim=function(str){return str.replace(/(\s|\0)+$/, '');}
RGraph.GetHeight=function(obj){return obj.canvas.height;}
RGraph.GetWidth=function(obj){return obj.canvas.width;}
RGraph.is_null=function(arg){if(arg==null||(typeof(arg))=='object'&&!arg){return true;}return false;}
RGraph.Timer=function(label){if(typeof(RGraph.TIMER_LAST_CHECKPOINT)=='undefined'){RGraph.TIMER_LAST_CHECKPOINT=Date.now();}var now=Date.now();console.log(label+': '+(now-RGraph.TIMER_LAST_CHECKPOINT).toString());RGraph.TIMER_LAST_CHECKPOINT=now;}
RGraph.Async=function(func){return setTimeout(func,arguments[1]?arguments[1]:1);}
RGraph.isIE=function(){return navigator.userAgent.indexOf('Trident')>0||navigator.userAgent.indexOf('MSIE')>0;};ISIE=RGraph.isIE();
RGraph.isIE6=function(){return navigator.userAgent.indexOf('MSIE 6')>0;};ISIE6=RGraph.isIE6();
RGraph.isIE7=function(){return navigator.userAgent.indexOf('MSIE 7')>0;};ISIE7=RGraph.isIE7();
RGraph.isIE8=function(){return navigator.userAgent.indexOf('MSIE 8')>0;};ISIE8=RGraph.isIE8();
RGraph.isIE9=function(){return navigator.userAgent.indexOf('MSIE 9')>0;};ISIE9=RGraph.isIE9();
RGraph.isIE10=function(){return navigator.userAgent.indexOf('MSIE 10')>0;};ISIE10=RGraph.isIE10();
RGraph.isIE11=function(){return navigator.userAgent.indexOf('MSIE')==-1&&navigator.userAgent.indexOf('Trident')>0;};ISIE11=RGraph.isIE11();
RGraph.isIE9up=function(){return ISIE9||ISIE10||ISIE11;};ISIE9UP=RGraph.isIE9up();
RGraph.isIE10up=function(){return ISIE10||ISIE11};ISIE10UP=RGraph.isIE10up();
RGraph.isIE11up=function(){return ISIE11};ISIE11UP=RGraph.isIE11up();
RGraph.isOld=function(){return ISIE6||ISIE7||ISIE8;};ISOLD=RGraph.isOld();
RGraph.Reset=function(canvas){canvas.width=canvas.width;RGraph.ObjectRegistry.Clear(canvas);canvas.__rgraph_aa_translated__=false;}
function pd(variable){RGraph.pr(variable);}
function p(variable){RGraph.pr(arguments[0],arguments[1],arguments[3]);}
function a(variable){alert(variable);}
function cl(variable){return console.log(variable);}