///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
  
 /**
 * This is the flex with flare based visualizer for the Moodle 2.x visual
 * grade book plug-in. This should load the grade book data for a given
 * visualization from the report/visual plug-in based on a set of flashvars
 * passed to it from Moodle and display a visual repersenation.
 */
package {
	//Flare imports
    import flare.animate.Transitioner;
    import flare.data.DataSet;
    import flare.data.DataSource;
    import flare.display.TextSprite;
    import flare.vis.Visualization;
    import flare.vis.controls.HoverControl;
    import flare.vis.data.Data;
    import flare.vis.data.DataSprite;
    import flare.vis.data.EdgeSprite;
    import flare.vis.data.NodeSprite;
    import flare.vis.legend.Legend;
    import flare.vis.legend.LegendItem;
    import flare.vis.operator.encoder.ColorEncoder;
    import flare.vis.operator.encoder.Encoder;
    import flare.vis.operator.encoder.ShapeEncoder;
    import flare.vis.operator.encoder.SizeEncoder;
    import flare.vis.operator.layout.AxisLayout;
    import flare.vis.operator.layout.CircleLayout;
    import flare.vis.operator.layout.DendrogramLayout;
    import flare.vis.operator.layout.ForceDirectedLayout;
    import flare.vis.operator.layout.IndentedTreeLayout;
    import flare.vis.operator.layout.Layout;
    import flare.vis.operator.layout.NodeLinkTreeLayout;
    import flare.vis.operator.layout.PieLayout;
    import flare.vis.operator.layout.RadialTreeLayout;
    import flare.vis.operator.layout.RandomLayout;
    import flare.vis.operator.layout.StackedAreaLayout;
    import flare.vis.operator.layout.TreeMapLayout;
    import flare.vis.scale.Scale;
    import flare.vis.util.Filters;
    import flare.vis.util.graphics.Shapes;
    
    import flash.display.DisplayObject;
    import flash.display.DisplayObjectContainer;
    import flash.display.Sprite;
    import flash.errors.IOError;
    import flash.events.Event;
    import flash.events.IOErrorEvent;
    import flash.events.MouseEvent;
    import flash.filters.GlowFilter;
    import flash.geom.Rectangle;
    import flash.net.URLLoader;
    import flash.net.URLRequest;
    import flash.text.TextField;
    import flash.text.TextFormat;
    import flash.utils.Dictionary;

    [SWF(width="800", height="600", backgroundColor="#ffffff", frameRate="30")]
    /**
    * Main class for handling grade book data and greatating a visualization.
    */
    public class flare_visualization extends Sprite
    {   
    	/**
    	 * The visualization object to be used in creating the visualization.
    	 */
    	private var vis:Visualization;
    	
    	/**
    	 * A refernce to the currently displayed dialog box. If null no dialog
    	 * box is currently being displayed.
    	 */
    	private var lastBox:Sprite = null;
    	
    	/**
    	 * A refernce to the data sprite witch contains the data for witch the 
    	 * currently displayed dialog box is based on and a child of.
    	 */
    	private var lastBoxData:DataSprite = null;
    	
    	/**
    	 * A container for the legends witch will be displayed on the righ hand
    	 * side.
    	 */
    	private var legends:Sprite;
    	
    	private var sideBar:Sprite;
    	
    	private var layout:Layout;
    	
    	private var encoders:Array;
    	
    	private var selectors:Sprite;
    	
    	private var controls:Sprite;
    	
    	/**
    	 * The hover control for the dialog box.
    	 */
    	private var boxhc:HoverControl = new HoverControl();
    	
    	private var settings:XML = new XML();
    	
    	private var dataURL:String;
    	
    	private var settingsURL:String;
    	
    	private var loadingMessage:TextSprite;
    	
    	private var legendNodes:Dictionary;
    	private var legendEdges:Dictionary;
    	
    	private var invertTransitioner:Transitioner;
    	private var legendItemTransitioner:Transitioner;
    	
    	private var printVersion:Boolean;
    	
    	private var nodeSize:int = 1;
    	
    	private var errors:Sprite = new Sprite();
    	
    	private var debug:Boolean = false;
    	
    	private var debug_sessionid:String = "ad896bf3c1af448c05f292e7b14433e1";
        private var debug_sessiontest:String = "nepQJfZ9pN";
        private var debug_sessioncookie:String = "";
        private var debug_visid:String = "grades_vs_items";
    	private var debug_wwwroot:String = "http://localhost/moodle";
    	private var debug_courseid:String = "3";
    	
    	
    	/**
    	 * The constucter for the flare_visualization class.
    	 * Calls on harvest_data and sets up the varibles from the flashvars.
    	 */	
        public function flare_visualization()
        {
        	loadingMessage = new TextSprite("Loading....", new TextFormat("monospace", 20, 0x0000FF, true));
        	addChild(loadingMessage);
        	addChild(errors);
        	
        	// Call harvest_data, loading needed visualization data from moodle.
        	// The Moodle wwwroot, course id, users sessionid, users session cookie 
        	// and session test data are needed to get the data from moodle are 
        	// loaded threw flashvars.
        	loaderInfo.addEventListener(Event.COMPLETE, function(evt:Event):void {
        		var wwwroot:String = loaderInfo.parameters['wwwroot'];
        		var courseID:String = loaderInfo.parameters['courseid'];
        		var sessioncookie:String = loaderInfo.parameters['sessioncookie'];
        		var sessionid:String = loaderInfo.parameters['sessionid'];
        		var sessiontest:String = loaderInfo.parameters['sessiontest'];
        		var visid:String = loaderInfo.parameters['visid'];
        		printVersion = booleanify(loaderInfo.parameters['printerversion']);
        		
        		if(debug) {
        			trace("Debug mode on.");
        			wwwroot = debug_wwwroot;
        			courseID = debug_courseid; 
        			sessioncookie = debug_sessioncookie;
        			sessionid = debug_sessionid; 
        			sessiontest = debug_sessiontest; 
        			visid = debug_visid;
        		}
        			
        		dataURL = wwwroot + '/grade/report/visual/data.php?id=' + escape(courseID) + '&sessioncookie=' +  escape(sessioncookie) + '&sessionid=' +  escape(sessionid) + '&sessiontest=' + escape(sessiontest) + '&visid=' + escape(visid);
        		settingsURL = wwwroot + '/grade/report/visual/visual_settings.php?id=' + escape(courseID) + '&sessioncookie=' +  escape(sessioncookie) + '&sessionid=' +  escape(sessionid) + '&sessiontest=' + escape(sessiontest) + '&visid=' + escape(visid);
        		
        		harvest_data();
        	});
        }
        
        /**
        * Harvests the data from Moodle and calls on buildVis to build the
        * visualization once the data has been loaded.
        * TODO: Add a loading bar and more feed back about the loading process.
        * @param url The url from witch to load the tab formated data for the visualization.
        */
        public function harvest_data():void
        {
        		loadingMessage.text ="Loading Settings....";
        		loadingMessage.x = loaderInfo.width/2 - loadingMessage.width/2;
        		loadingMessage.y = loaderInfo.height/2 - loadingMessage.height/2;
        		 
        		
        		try{
        			var ds:DataSource = new DataSource(dataURL, "tab");
        			var settingsRequest:URLRequest = new URLRequest(settingsURL);
   					var settingsLoader:URLLoader = new URLLoader(settingsRequest);
        	    
        	  	  settingsLoader.addEventListener(IOErrorEvent.IO_ERROR, function(evt:IOErrorEvent):void {
        	  	  	error("Loading", evt.text);	
        	  	  });
        	    
        	  	  settingsLoader.addEventListener(Event.COMPLETE, function(evt:Event):void {
        	  	  	try{
        	  	  		settings = XML(settingsLoader.data);	
        	  	  		loadingMessage.text = "Loading Data....";
        	    		
        	    		if(isnull(settings) || settings.length() < 1 || isnull(settings.layout)) {
        	    			error("Loading", "Failed to load settings.");
        	    			return;
        	    		}
        	    		
        	   		 	var dataLoader:URLLoader = ds.load();
        	    
        	   	 		dataLoader.addEventListener(IOErrorEvent.IO_ERROR, function(evt:IOErrorEvent):void {
        	    			error("Loading", evt.text);
        	    		});
        	    
        	    		dataLoader.addEventListener(Event.COMPLETE, function(evt:Event):void {
        	        		removeChild(loadingMessage);
        	        		var data:DataSet = dataLoader.data as DataSet;
        	        		buildVis(Data.fromDataSet(data));
        	    		});
        	  	  	} catch(e:Error) {
        	  	  		error("", e.message);
        	  	  	}
        		  });
        		} catch(e:IOError) {
        			error("IO", e.message);
        		} catch(e:Error) {
        			error("", e.message) 				
        		}
		}
        
        public function updateVis(data:Data):void {
        	var t:Transitioner = new Transitioner(2);
        	makeEdges(data);
        	vis.data = data;
        	setUpEncoders();
        	setDataProperties();
        	setUpLegends();
        	setUpLayout();
        	//for(var i:int = 0; i < legends.numChildren; i++){ 
        		//Legend(legends.getChildAt(i)).update(t);
        	//}
        	t.$(selectors).y = legends.height;
        	t.$(controls).y = legends.height + selectors.height + 10;
        	vis.update(t).play();
        }
        
        public function reharvest_data(url:String):void {
        	loadingMessage.text ="Loading Data....";
        	addChild(loadingMessage);
        	
        	try{
        		var ds:DataSource = new DataSource(url, "tab");
        		var dataLoader:URLLoader = ds.load();
        		
        		dataLoader.addEventListener(IOErrorEvent.IO_ERROR, function(evt:IOErrorEvent):void {
        	    	error("Loading", evt.text);
        	    });
        	    
        	    dataLoader.addEventListener(Event.COMPLETE, function(evt:Event):void {
        	    	removeChild(loadingMessage);
        	    	var data:DataSet = dataLoader.data as DataSet;
        	        updateVis(Data.fromDataSet(data));
        	    });
        	}catch(e:IOError) {
        		error("IO", e.message);
        	} catch(e:Error) {
        		error("", e.message) 				
        	}
        }
        
        private function error(type:String = "", text:String = ""):void {
        	trace(type + " Error: " + text);
        	
        	var textfield:TextField = new TextSprite(type + " Error: " + text, new TextFormat("monospace", 12, 0xFF0000, true)).textField;
        	textfield.wordWrap = true;
        	textfield.x = 0;
        	textfield.y = errors.height;
        	
        	errors.addChild(textfield);	
        }
     
     	/**
     	 * Find the max width between a container and all of it's decendence
     	 * This dose not find the width of a container but the greatest width
     	 * of an invdual component in it's decenedences.
     	 * @param d The display container to find the max width of.
     	 * @return the max width value of the display objects.
     	 */ 
     	private function getMaxWidth(d:DisplayObjectContainer):int {
     		var max:int = d.width;
     		  
            for(var k:uint = 0; k < d.numChildren; k++ ) {  
            	var width:int = 0;
            	
                if(d.getChildAt(k) is DisplayObjectContainer) {
                	width = getMaxWidth(DisplayObjectContainer(d.getChildAt(k)));  
                } else {
                	width = d.getChildAt(k).width;
                }
  				
  				if(width > max) {
  					max = width;
  				}
            }
            
            return max;
        }
     
     	/**
     	 * Simple function to retrun the greatest of two ints.
     	 * @param num1 the first number to test
     	 * @param num2 the second number to test
     	 * @return the largest value between num1 and num2.
     	 */
     	private function max(num1:int, num2:int):int {
     		if(num1 > num2) {
     			return num1;
     		} else {
     			return num2;
     		}
     	}
     
     	/**
     	 * Find the max height between a container and all of it's decendence
     	 * This dose not find the width of a container but the greatest height
     	 * of an invdual component in it's decenedences.
     	 * @param d The display container to find the max height of.
     	 * @return the max height value of the display objects.
     	 */ 
     	private function getMaxHeight(d:DisplayObjectContainer):int {
     		var max:int = d.height;
     		  
            for(var k:uint = 0; k < d.numChildren; k++ ) {  
            	var height:int = 0;
            	
                if(d.getChildAt(k) is DisplayObjectContainer) {
                	height = getMaxHeight(DisplayObjectContainer(d.getChildAt(k)));  
                } else {
                	height = d.getChildAt(k).height;
                }
  				
  				if(height > max) {
  					max = height;
  				}
            }
            
            return max;
        }  
     
     	private function nullify(o:*):* {
     		if(isnull(o)) {
     			return null;
     		} else {
     			return o;
     		}
     	}
     	
     	private function isnull(o:*):Boolean {
     		if(o == null || o.length == 0 || String(o).length == 0) {
     			return true;
     		} else {
     			return false;
     		}
     	}
     	
     	private function booleanify(o:*):* {
     		var ob:Object = nullify(o);
     		
     		if(String(ob).toLocaleLowerCase() == "false") {
     			return false;
     		} else if (String(ob).toLocaleLowerCase() == "true") {
     			return true;
     		}
     		
     		return ob;	
     	}
     	
     	private function passSettings(theClass:Class, XMLSettings:XMLList, ... args):* {
     		var params:Array = new Array();
     		var cleanParam:*;
     		
     		for each(var arg:* in args) {
     			params.push(arg);
     		}
     		
     		for each(var param:* in XMLSettings) {
     			cleanParam = booleanify(param);
     			if(cleanParam != null) {
     				params.push(cleanParam);
     			} 
     		}
     		
     		switch(params.length) {
     			case 1: return new theClass(params[0]);
     			case 2: return new theClass(params[0], params[1]);
     			case 3: return new theClass(params[0], params[1], params[2]);
     			case 4: return new theClass(params[0], params[1], params[2], params[3]);
     			case 5: return new theClass(params[0], params[1], params[2], params[3], params[4]);
     			case 6: return new theClass(params[0], params[1], params[2], params[3], params[4], params[5]);
     			case 7: return new theClass(params[0], params[1], params[2], params[3], params[4], params[5], params[6]);
     			case 8: return new theClass(params[0], params[1], params[2], params[3], params[4], params[5], params[6], params[7]);
     			case 9: return new theClass(params[0], params[1], params[2], params[3], params[4], params[5], params[6], params[7], params[8]);
     			case 10: return new theClass(params[0], params[1], params[2], params[3], params[4], params[5], params[6], params[7], params[8], params[9]);
     			default: return new theClass();
     		}
     	}
     
     	private function toStringArray(xmllist:XMLList):Object {
     		if(xmllist.length() > 1) {
     			var array:Array = new Array;
     			
     			for each(var element:XML in xmllist) {
     				array.push(String(element));
     			}
     			
     			return array;
     		} else {
     			return String(xmllist);
     		}
     	}
     	
     	private function toNumberArray(xmllist:XMLList):Object {
     		if(xmllist.length() > 1) {
     			var array:Array = new Array;
     			
     			for each(var element:XML in xmllist) {
     				array.push(Number(element));
     			}
     			
     			return array;
     		} else {
     			return Number(xmllist);
     		}
     	}
     	
     	private function makeEdges(data:Data):void {
     		if(!isnull(settings.edge)) {
            	for each(var edge:XML in settings.edge) {
            		data.createEdges(toStringArray(edge.sortby), toStringArray(edge.groupby));
            	}
            }
     	}
     	
     	private function setDataProperties():void {
     		vis.data.nodes.visit(function(d:DataSprite):void {
            	if(!isnull(settings.style.nodeshape)) {
            		d.shape = settings.style.nodeshape;
            	}
				
				if(d.shape == Shapes.HORIZONTAL_BAR || d.shape == Shapes.VERTICAL_BAR) {
					nodeSize = 2;	
				}
				
				d.size = nodeSize;
				d.fillColor = 0x018888ff;
				d.fillAlpha = 0.2;
				d.addEventListener(MouseEvent.CLICK, mouseClicked);
				d.lineWidth = 2;
			});
			
			vis.data.edges.visit(function(d:DataSprite):void {
				if(!isnull(settings.style.edgeshape)) {
					d.shape = settings.style.edgeshape;
				}
				d.lineWidth = 2;
				d.fillAlpha = 1;
			});
     	}
     	
     	private function setUpLayout():void {
     		vis.operators.remove(layout);
     		layout = null;
     		
     		if(isnull(settings.layout.type)) {
     			error("Bad Settings", "Missing layout type value.");
				return;
     		}
     		
     		// Set up the layout
			switch(int(settings.layout.type)) {
				case 1: 
						if(!isnull(settings.layout.xaxis.field) && !isnull(settings.layout.yaxis.field)) {
							layout = passSettings(AxisLayout, settings.layout.setting, settings.layout.xaxis.field, settings.layout.yaxis.field);
						} else {
							error("Bad Settings", "Missing x or y axis feild for AxisLayout");
							return;
						}  
						break;
				case 2:	layout = passSettings(CircleLayout, settings.layout.setting);
						break;
				case 3: layout = passSettings(DendrogramLayout, settings.layout.setting);
						break;
				case 4: layout = passSettings(ForceDirectedLayout, settings.layout.setting);
						break;
				case 5: layout = passSettings(IndentedTreeLayout, settings.layout.setting);
						break;
				case 6: layout = passSettings(NodeLinkTreeLayout, settings.layout.setting);
						break;
				case 7: layout = passSettings(PieLayout, settings.layout.setting);
						break;
				case 8: layout = passSettings(RadialTreeLayout, settings.layout.setting);
						break;
				case 9: layout = new RandomLayout();
						break;
				case 10: layout = passSettings(StackedAreaLayout, settings.layout.setting);
						 break;
				case 11: layout = new TreeMapLayout();
						 break;
				default:
						if(!isnull(settings.layout.xaxis.field) && !isnull(settings.layout.yaxis.field)) {
							layout = passSettings(AxisLayout, settings.layout.setting, settings.layout.xaxis.field, settings.layout.yaxis.field);
						} else {
							error("Bad Settings", "Missing x or y axis feild for AxisLayout");
							return;
						}
						break;
			}
			
			vis.operators.add(layout);
     	}
     	
     	private function setUpEncoders():void {
     		var e:Encoder;
     		
     		for each(var enc:Encoder in encoders) {
     			vis.operators.remove(enc);
     		}
     		
     		// Set up the encoders
			encoders = new Array();
			
			for each(var encoder:XML in settings.encoder) {
				switch(int(encoder.type)) {
					case 1: e = passSettings(ColorEncoder, encoder.setting, encoder.datafield);
							break;
					case 2: e = passSettings(ShapeEncoder, encoder.setting, encoder.datafield);
							break;
					case 3: e = passSettings(SizeEncoder, encoder.setting, encoder.datafield);
							break;
					default: e = passSettings(ColorEncoder, encoder.setting, encoder.datafield);
							break;
				}
				
				encoders[encoder.id] = e;
				vis.operators.add(e);
			}
     	}
     	
     	private function setUpLegends():void {
     		var dataName:String;
     		
     		for(var i:int = 0; i < legends.numChildren; i++){ 
        		for(var k:int = 0; k < Legend(legends.getChildAt(i)).items.numChildren; k++) {
        			Legend(legends.getChildAt(i)).items.removeChildAt(k);	
        		}
        		legends.removeChildAt(i);
        	}
     		
     		legendNodes = new Dictionary();
     		legendEdges = new Dictionary();
     		
			if(!isnull(settings.legend)) {
				var nextLegendY:int = 0;
				for each(var legend:XML in settings.legend) {
					var en:Encoder = encoders[legend.encoderid];
					var l:Legend;
					
					switch(int(settings.encoder.(id == int(legend.encoderid)).type)) {
						case 1: l = new Legend(en.source, en.scale, ColorEncoder(en).colors);
								break;
						case 2: l = new Legend(en.source, en.scale, null, ShapeEncoder(en).shapes);
								break;
						case 3: l = new Legend(en.source, en.scale, null, null, SizeEncoder(en).sizes);
								break;
						default: l = new Legend(en.source, en.scale, ColorEncoder(en).colors);
								break;
					} 
				
					l.x = 0;
					l.y = nextLegendY;
					nextLegendY += l.height;
					
					//l.items.addEventListener(MouseEvent.CLICK, legendClick);
				
					var lhc:HoverControl = new HoverControl(l.items);
            		lhc.onRollOver = legendRollOver;
					lhc.onRollOut = legendRollOut;
					
					legends.addChild(l);
					
					dataName = l.dataField.substr(l.dataField.lastIndexOf('.') + 1);
					for(var j:int = 0; j < l.items.numChildren; j++) {
						LegendItem(l.items.getChildAt(j)).addEventListener(MouseEvent.CLICK, legendClick);
						//LegendItem(l.items.getChildAt(j)).label.textMode = TextSprite.DEVICE;
						//LegendItem(l.items.getChildAt(j)).label.textField.y -= 8;
						
						legendNodes[LegendItem(l.items.getChildAt(j))] = new Array();
						legendEdges[LegendItem(l.items.getChildAt(j))] = new Array();

						vis.data.nodes.visit(function(n:NodeSprite):void {
							if(n.data.hasOwnProperty(dataName) && LegendItem(l.items.getChildAt(j)).value == n.data[dataName]) {
								(legendNodes[LegendItem(l.items.getChildAt(j))] as Array).push(n);
							}				
						});
						
						vis.data.edges.visit(function(e:EdgeSprite):void {
							if(e.data.hasOwnProperty(dataName) && LegendItem(l.items.getChildAt(j)).value == e.data[dataName]) {
								(legendEdges[LegendItem(l.items.getChildAt(j))] as Array).push(e);
							}				
						});
						
						if(XMLList(legend.show).length() > 0 && !XMLList(legend.show).contains(LegendItem(l.items.getChildAt(j)).value)) {
							LegendItem(l.items.getChildAt(j)).alpha = 0.4;
						}
					}
				}
				
				removeLegenedItemsNodes();
			}
     	}
     	
     	/**
     	 * Builds the visualization based on the loaded data.
     	 * Also sets up the legends, buttons and controls.
     	 * @param data The data that was loaded in from moodle.
     	 */
     	private function buildVis(data:Data):void
        {
            makeEdges(data);
         
            vis = new Visualization(data);
            legends = new Sprite();
            sideBar = new Sprite();
            
            // Set the functions to be called when a dialog box is hovered over.
            boxhc.onRollOver = boxRollOver;
			boxhc.onRollOut = boxRollOut;
            
			// Set up the properitys of the data sprites and add a eventlistener to check for
            // clicks on them.
			setDataProperties();
			
			setUpEncoders();
			
			// Set up the legends.
			setUpLegends();
			
			setUpLayout();
			
			if(!isnull(settings.layout.yaxis.labelformat)) {
				vis.xyAxes.yAxis.labelFormat = settings.layout.yaxis.labelformat;
			} else {
				vis.xyAxes.yAxis.labelFormat = "0";
			}
			
			if(!isnull(settings.layout.xaxis.labelformat)) {
				vis.xyAxes.xAxis.labelFormat = settings.layout.xaxis.labelformat;
			} else {
				vis.xyAxes.xAxis.labelFormat = "0";
			}
			
			if(!isnull(settings.layout.xaxis.min)) {
				vis.xyAxes.xAxis.axisScale.min = settings.layout.xaxis.min;
				vis.xyAxes.xAxis.axisScale.flush = true;
			}
			
			if(!isnull(settings.layout.xaxis.max)) {
				vis.xyAxes.xAxis.axisScale.max = settings.layout.xaxis.max;
				vis.xyAxes.xAxis.axisScale.flush = true;
			}
			
			if(!isnull(settings.layout.yaxis.min)) {
				vis.xyAxes.yAxis.axisScale.min = settings.layout.yaxis.min;
			}
			
			if(!isnull(settings.layout.yaxis.max)) {
				vis.xyAxes.yAxis.axisScale.max = settings.layout.yaxis.max;
			}
			
			if(!isnull(settings.layout.yaxis.yoffset)) {
				vis.xyAxes.yAxis.labelOffsetY = settings.layout.yaxis.yoffset;
			}
			
			if(!isnull(settings.layout.yaxis.xoffset)) {
				vis.xyAxes.yAxis.labelOffsetX = settings.layout.yaxis.xoffset;
			}
			
			if(!isnull(settings.layout.xaxis.yoffset)) {
				vis.xyAxes.xAxis.labelOffsetY = settings.layout.xaxis.yoffset;
			}
			
			if(!isnull(settings.layout.xaxis.xoffset)) {
				vis.xyAxes.xAxis.labelOffsetX = settings.layout.xaxis.xoffset;
			}
			
			// Set up the layout of the axes.
            vis.xyAxes.xAxis.horizontalAnchor = TextSprite.LEFT; 
			vis.xyAxes.xAxis.verticalAnchor = TextSprite.MIDDLE; 
			vis.xyAxes.xAxis.labelAngle = Math.PI / 2;
			vis.xyAxes.xAxis.fixLabelOverlap = false;
			vis.xyAxes.yAxis.fixLabelOverlap = false;
			//vis.xyAxes.yAxis.labelTextMode = TextSprite.DEVICE;
		
		
			// Update the visualization so the widths and other values are correct.
            vis.update();
			
			// Initalize the X and Y axis labels and the visualizations title.
			var labelX:TextSprite = new TextSprite(settings.labels.xaxis, new TextFormat(settings.style.text.font, settings.style.text.size)); 
			var labelY:TextSprite = new TextSprite(settings.labels.yaxis, new TextFormat(settings.style.text.font, settings.style.text.size));
			var title:TextSprite = new TextSprite(settings.labels.title, new TextFormat(settings.style.text.font, int(settings.style.text.size) + 5));
			
			// Find the largest width out of the X axis labels so it can used for positing sprites.
           	var xLabelsHeight:int = getMaxHeight(vis.xyAxes.xAxis.labels);
            var yLabelsWidth:int = getMaxWidth(vis.xyAxes.yAxis.labels);
            
			// Position the visualization.
			vis.y = title.height + 10;
            vis.x = labelY.height + -vis.xyAxes.yAxis.labelOffsetX + yLabelsWidth;
			
			
			
			legendItemTransitioner = new Transitioner(0.5);
			sideBar.addChild(legends);
			
			selectors = new Sprite();
			if(!isnull(settings.selector)) {
				for each(var selector:XML in settings.selector) {
					var selectorSprite:Selector = new Selector(selector.param, selector.option, selectorClick, selector.active, legends.width);
					selectorSprite.x = 0;
					selectorSprite.y = selectors.height;
					selectors.addChild(selectorSprite);
				}
			}
			sideBar.addChild(selectors);
			
			//vis.update();

            // Set the bounds of the visualization based on the hieght and width of the flash application,
            // and the other components so the visualization is takes up the unused space.
            vis.bounds = new Rectangle(0, 0, loaderInfo.width - (sideBar.width + 15 + vis.x), loaderInfo.height - (vis.y + xLabelsHeight + labelX.height + vis.xyAxes.xAxis.labelOffsetY)); 
            
            // Add the visualization to the main sprite.
            addChild(vis);
            
            // Position the legends.
            //legends.x = vis.bounds.width + 10;
            legends.x = 0;
            legends.y = 0;
            
            sideBar.x = vis.bounds.width + 10;
            sideBar.y = 0;
            
            // Position and add the labels and title to the axes.
			labelX.x = vis.bounds.width/2 - labelX.width/2;
			labelX.y = vis.bounds.height + vis.xyAxes.xAxis.labelOffsetY + xLabelsHeight;
            vis.xyAxes.xAxis.addChild(labelX);
            
			labelY.rotation = -90;
			labelY.x = -vis.x;
			labelY.y = (vis.bounds.height/2) + (labelY.height/2);
			vis.xyAxes.yAxis.addChild(labelY);
			
			title.x = vis.bounds.width/2 - title.width/2;
			title.y = -vis.y;
			vis.xyAxes.addChild(title);
            
      		// Add the legeneds container to the visualization.
            //vis.addChild(legends);
            vis.addChild(sideBar);
            
            selectors.x = 0;
			selectors.y = legends.y + legends.height;
			//vis.addChild(selectors);
            
            // Set up the hovercontrol for the marks on the chart
            var hc:HoverControl = new HoverControl(vis, Filters.isDataSprite);
            hc.onRollOver = rollOver;
			hc.onRollOut = rollOut;
			
			// Set up the buttons and a container for them.
			controls = new Sprite();
			var bInvert:Button = new Button(settings.lang.invertaxes, settings.style.button);
			var bHideAxis:Button = new Button(settings.lang.hide + " " + settings.lang.axes, settings.style.button);
			var bHideXLabel:Button = new Button(settings.lang.hide + " " + settings.lang.xlabels, settings.style.button);
			var bHideYLabel:Button = new Button(settings.lang.hide + " " + settings.lang.ylabels, settings.style.button);
			
			var hideXLabelTransitioner:Transitioner = new Transitioner(2);
			//hideXLabelTransitioner.onEnd = updateMarkVisiblity;
			//hideXLabelTransitioner.onStart = updateMarkVisiblity;
			
			bHideXLabel.addEventListener(MouseEvent.CLICK, function(evt:MouseEvent):void {
				if(!hideXLabelTransitioner.running) {
					hideXLabelTransitioner.reset();
					
					if(bHideXLabel.text == settings.lang.show + " " +  settings.lang.xlabels) {
						bHideXLabel.text = settings.lang.hide + " " + settings.lang.xlabels;
						vis.xyAxes.xAxis.showLabels = true;
						vis.bounds = new Rectangle(0, 0, loaderInfo.width - (sideBar.width + 15 + vis.x), loaderInfo.height - (vis.y + xLabelsHeight + labelX.height + vis.xyAxes.xAxis.labelOffsetY));
					} else {
						bHideXLabel.text = settings.lang.show + " " +  settings.lang.xlabels;
						vis.xyAxes.xAxis.showLabels = false;
						vis.bounds = new Rectangle(0, 0, loaderInfo.width - (sideBar.width + 15 + vis.x), loaderInfo.height - (vis.y + labelX.height));
					}
					
					hideXLabelTransitioner.$(labelY).x = -vis.x;
					hideXLabelTransitioner.$(labelY).y = vis.bounds.height/2 + labelY.height/2;
				
					vis.update(hideXLabelTransitioner).play();
				}
			});
			
			var hideYLabelTransitioner:Transitioner = new Transitioner(2);
			//hideYLabelTransitioner.onEnd = updateMarkVisiblity;
			//hideYLabelTransitioner.onStart = updateMarkVisiblity;
			
			bHideYLabel.addEventListener(MouseEvent.CLICK, function(evt:MouseEvent):void {
				if(!hideYLabelTransitioner.running) {
					var t:Transitioner = new Transitioner(2);
					var newX:int;
					
					hideYLabelTransitioner.reset();
					
					if(bHideYLabel.text == settings.lang.show + " " +  settings.lang.ylabels) {
						bHideYLabel.text = settings.lang.hide + " " +  settings.lang.ylabels;
						vis.xyAxes.yAxis.showLabels = true;
						newX = labelY.width + -vis.xyAxes.yAxis.labelOffsetX + yLabelsWidth;
					} else {
						bHideYLabel.text = settings.lang.show + " " +  settings.lang.ylabels;
						vis.xyAxes.yAxis.showLabels = false;
						newX = labelY.width;
					}
				
					t.$(vis).x = newX;
					vis.bounds = new Rectangle(0, 0, loaderInfo.width - (sideBar.width + 15 + newX), loaderInfo.height - (vis.y + xLabelsHeight + labelX.height + vis.xyAxes.xAxis.labelOffsetY));
					
					// Reposition the labels and title. 
					t.$(title).x = vis.bounds.width/2 - title.width/2;
					t.$(labelX).x = vis.bounds.width/2 - labelX.width/2;
					t.$(labelX).y = vis.bounds.height + vis.xyAxes.xAxis.labelOffsetY + xLabelsHeight;
					t.$(labelY).x = -newX;
					t.$(labelY).y = vis.bounds.height/2 + labelY.height/2;
				
					// Keep the legends in there place.
					t.$(sideBar).x = vis.bounds.width + 10;
					
					t.play();
					vis.update(hideYLabelTransitioner).play();
				}
			});
			
			// Set up the transitioner to be used when inverting the axes
			invertTransitioner = new Transitioner(2);
			invertTransitioner.onEnd = function():void {
				//updateMarkVisiblity();
				vis.xyAxes.xAxis.labels.visible = true;
				vis.xyAxes.yAxis.labels.visible = true;
   			};
            invertTransitioner.onStart = function():void {
            	//updateMarkVisiblity();
            	vis.xyAxes.xAxis.labels.visible = false;
				vis.xyAxes.yAxis.labels.visible = false;
            }
			
			// The function to invert the axes.
			bInvert.addEventListener(MouseEvent.CLICK, function(evt:MouseEvent):void {
				// If we are not allready in the process of inverting the axes.
				if(!invertTransitioner.running && !legendItemTransitioner.running) {
					var t:Transitioner = new Transitioner(2);
					var tempText:String = labelX.text;
					var tempOffset:int = vis.xyAxes.xAxis.labelOffsetX;
					var tempWidth:uint = vis.bounds.width;
					var tempLabelFormat:String = vis.xyAxes.xAxis.labelFormat;
					var tempLabels:int = xLabelsHeight;
					var tempScale:Scale = vis.xyAxes.xAxis.axisScale;
					var tempLabelOffsetY:Number = vis.xyAxes.xAxis.labelOffsetY;
					var tempLabelOffsetX:Number = vis.xyAxes.xAxis.labelOffsetX;
					var currentXLabelsHeight:int = getMaxWidth(vis.xyAxes.yAxis.labels);
					
					var tempShowLabels:Boolean = vis.xyAxes.xAxis.showLabels;
				
					// Rest the transitioner for a clean transition.
					invertTransitioner.reset();
					
					vis.xyAxes.xAxis.axisScale = vis.xyAxes.yAxis.axisScale;
					vis.xyAxes.yAxis.axisScale = tempScale;
					vis.xyAxes.yAxis.axisScale.flush = true;
					vis.xyAxes.xAxis.axisScale.flush = true
					
					// Flip the axis feilds.
					if(settings.layout.type == 1) {
						AxisLayout(layout).xField = settings.layout.yaxis.field;
						AxisLayout(layout).yField = settings.layout.xaxis.field;
						settings.layout.xaxis.field = AxisLayout(layout).xField;
						settings.layout.yaxis.field = AxisLayout(layout).yField;
					
						var tempStack:Boolean = AxisLayout(layout).xStacked;
						AxisLayout(layout).xStacked = AxisLayout(layout).yStacked;
						AxisLayout(layout).yStacked = tempStack;
						
						if(XMLList(settings.layout.setting).length() >= 2 && !isnull(settings.layout.setting[0]) && !isnull(settings.layout.setting[0])) {
							var tempStackSetting:String = settings.layout.setting[0].toString();
							settings.layout.setting[0] = settings.layout.setting[1].toString();
							settings.layout.setting[1] = tempStackSetting;
						} else if(XMLList(settings.layout.setting).length() == 1 && !isnull(settings.layout.setting[0])) {
							settings.layout.setting[1] = settings.layout.setting[0].toString();
							settings.layout.setting[0] = "false";
							
						}
					}
					
					vis.xyAxes.xAxis.labelFormat = vis.xyAxes.yAxis.labelFormat;
					vis.xyAxes.yAxis.labelFormat = tempLabelFormat;
					
					
					vis.xyAxes.xAxis.labelOffsetX = vis.xyAxes.yAxis.labelOffsetY * -1;
					vis.xyAxes.yAxis.labelOffsetY = tempLabelOffsetX * -1;
					vis.xyAxes.xAxis.labelOffsetY = vis.xyAxes.yAxis.labelOffsetX * -1;
					vis.xyAxes.yAxis.labelOffsetX = tempLabelOffsetY * -1;
					
					xLabelsHeight = yLabelsWidth;
					yLabelsWidth = tempLabels;
				
				
					vis.xyAxes.xAxis.showLabels = vis.xyAxes.yAxis.showLabels;
					vis.xyAxes.yAxis.showLabels = tempShowLabels;
				
					if(vis.xyAxes.yAxis.showLabels) {
						bHideYLabel.text = settings.lang.hide + " " +  settings.lang.ylabels;
					} else {
						bHideYLabel.text = settings.lang.show + " " +  settings.lang.ylabels;
					}
					
					if(vis.xyAxes.xAxis.showLabels) {
						bHideXLabel.text = settings.lang.hide + " " +  settings.lang.xlabels;
					} else {
						bHideXLabel.text = settings.lang.show + " " +  settings.lang.xlabels;
					}
					
					// Flip the labels
					labelX.text = labelY.text;
					labelY.text = tempText;
				
					if(settings.style.nodeshape == Shapes.VERTICAL_BAR || settings.style.nodeshape == Shapes.HORIZONTAL_BAR) {
						vis.data.nodes.visit(function(d:NodeSprite):void {
							if(d.shape == Shapes.VERTICAL_BAR) {
								t.$(d).shape = Shapes.HORIZONTAL_BAR;
							} else {
								t.$(d).shape = Shapes.VERTICAL_BAR;
							}
						});
					
						if(settings.style.nodeshape == Shapes.VERTICAL_BAR) {
							settings.style.nodeshape = Shapes.HORIZONTAL_BAR;
						} else {
							settings.style.nodeshape = Shapes.VERTICAL_BAR;
						}
				
					
						for(var li:Object in legendNodes) {
							for each(var node:NodeSprite in legendNodes[LegendItem(li)] as Array) {
								if(node.shape == Shapes.VERTICAL_BAR) {
									node.shape = Shapes.HORIZONTAL_BAR;
								} else {
									node.shape = Shapes.VERTICAL_BAR;
								}
							}
						}
					}
				
					// Find the new X value for the visualization.
					var newX:int = labelY.width + vis.xyAxes.xAxis.labelOffsetY + getMaxHeight(vis.xyAxes.xAxis.labels);
				
					// Reposition and set the bounds of the visualization.
					t.$(vis).x = newX;
					vis.bounds = new Rectangle(0, 0, loaderInfo.width - (sideBar.width + 15 + newX), loaderInfo.height - (vis.y + currentXLabelsHeight + labelX.height + vis.xyAxes.xAxis.labelOffsetY));
				
					// Reposition the labels and title. 
					t.$(title).x = vis.bounds.width/2 - title.width/2;
					t.$(labelX).x = vis.bounds.width/2 - labelX.width/2;
					t.$(labelX).y = vis.bounds.height + vis.xyAxes.xAxis.labelOffsetY + currentXLabelsHeight;
					t.$(labelY).x = -newX;
					t.$(labelY).y = vis.bounds.height/2 + labelY.height/2;
				
					// Keep the legends in there place.
					t.$(sideBar).x = vis.bounds.width + 10;
				
					
					//Play the transition.
            		t.play();
            		vis.update(invertTransitioner).play();
    			}
			});
			
			// Set up the transitioner for the hide axes button.
			var hideAxisTrans:Transitioner = new Transitioner(1);
			
			// Function for hidding the axes.
			bHideAxis.addEventListener(MouseEvent.CLICK, function(evt:MouseEvent):void {
				// If we are not allready in the process of hidding the axes
				if(!hideAxisTrans.running) {
					// Reset the transitoner for a clean transiton.
					hideAxisTrans.reset();
					
					// Hide or show the axes.
					if(bHideAxis.text == settings.lang.show + " " + settings.lang.axes) {
						hideAxisTrans.$(bHideAxis).text = settings.lang.hide + " " + settings.lang.axes;
						layout.showAxes(hideAxisTrans).play();
					} else {
						hideAxisTrans.$(bHideAxis).text = settings.lang.show + " " + settings.lang.axes;
						layout.hideAxes(hideAxisTrans).play();
					}
				}
			});
			
			// Position the buttons inside there container.
			bHideXLabel.x = 0;
			bHideXLabel.y = 0;
			
			bHideAxis.x = sideBar.width - bHideAxis.width - 5;
			bHideAxis.y = bHideXLabel.y;
			
			bInvert.x = sideBar.width - bInvert.width - 5;
			bInvert.y = bHideXLabel.y + bHideXLabel.height + 2;
			
			bHideYLabel.x = 0;
			bHideYLabel.y = bHideXLabel.y + bHideXLabel.height + 2;
			
			// Poistion the buttons container.
			controls.x = 0; 
			controls.y = sideBar.height + 10; 
			
			// Add the buttons to the container and the container to the main sprite.
			controls.addChild(bInvert);
			controls.addChild(bHideAxis);
			controls.addChild(bHideXLabel);
			controls.addChild(bHideYLabel);
			
			if(!printVersion){
				sideBar.addChild(controls);
			}
			
			// Set the marks on the chart to the higest deepth.
            vis.setChildIndex(vis.marks, vis.numChildren - 1);
	
			// Update.
			vis.update();
			//updateMarkVisiblity();
        }
        
        /**
        * Roll over function witch makes the object 0.5 units bigger and adds a glow filter.
        * @param ob the object witch was rolled over.
        */
        private function rollOver(ob:Object):void {
        	ob.filters = [new GlowFilter(0xFFFF55, 0.8, 6, 6, 10)];
        	ob.size = nodeSize + 0.5;
        }
        
        /**
        * Roll out function witch removes the filters and makes the object 0.5 units smaller.
        * @param ob the object witch was rolled out of.
        */
        private function rollOut(ob:Object):void {
        	ob.filters = null;
        	ob.size = nodeSize;
        }
        
        /**
        * Roll over function for the dialog box.
        * Adds a glow filter to the curently active dialog box.
        * @param ob a child of the dialog box.
        */
        private function boxRollOver(ob:Object):void {
        	if(lastBoxData != null) {
        		lastBoxData.filters = [new GlowFilter(0xFFFF55, 0.8, 6, 6, 10)];
        	}
        }
        
        /**
        * Roll out function for the dialog box.
        * Removes filters on the curently active dialog box.
        * @param ob a child of the dialog box.
        */
        private function boxRollOut(ob:Object):void {
        	if(lastBoxData != null) {
        		lastBoxData.filters = null;
        	}
        }
        
        /**
        * Finds the Legend belonging to the LegendItem passed.
        * TODO: See if this can be replaced by a .parent call.
        * @param item a LegendItem to find the Legend of.
        * @return the Legend that contains the passed LegendItem.
        */
        private function findLegendByItem(item:LegendItem):Legend {
       		for(var i:uint = 0; i < legends.numChildren; i++ ) {
        		if(Legend(legends.getChildAt(i)).items.contains(item)) {
        			return Legend(legends.getChildAt(i));
        		}
        	}
        	
        	return null;
        }
        
        /**
        * Roll over function for legends.
        * Adds a glow filter to the legend's item aswell as all the markers on the chart
        * that are realted to the legend item and incrases there size by 1 unit.
        * @param ob the LegendItem being rolled over.
        */ 
        private function legendRollOver(ob:Object):void {
        	var item:LegendItem;
        	
        	if(ob is LegendItem) {
        		item = LegendItem(ob);
        	} else if(ob is TextField) {
        		ob.filters = [new GlowFilter(0xFFFF55, 0.8, 6, 6, 10)];
        		item = LegendItem(TextField(ob).parent.parent);
        	} else {
        		return;
        	}
        	
        	var legend:Legend = Legend(item.parent.parent);
        		
        	if(legend) {
        		var dataName:String = legend.dataField.substr(legend.dataField.lastIndexOf('.') + 1);
        		
        		item.filters = [new GlowFilter(0xFFFF55, 0.8, 6, 6, 10)];
        		
        		vis.data.visit(function(d:DataSprite):void {
					if(d.data.hasOwnProperty(dataName) && item.value == d.data[dataName]) {
						d.filters = [new GlowFilter(0xFFFF55, 0.8, 6, 6, 10)];
						d.size = nodeSize + 1;
					}
				}, 3, Filters.isDataSprite);
        	}
        }
        
        /**
        * Roll out function for legends.
        * Removes filters to the legend's item aswell as all the markers on the chart
        * that are realted to the legend item and decrases there size by 1 unit.
        * @param ob the LegendItem being rolled out of.
        */ 
        private function legendRollOut(ob:Object):void {
        	var item:LegendItem;
        	
        	if(ob is LegendItem) {
        		item = LegendItem(ob);
        	} else if(ob is TextField) {
        		ob.filters = null;
        		item = LegendItem(TextField(ob).parent.parent);
        	} else {
        		return;
        	}
        	
        	var legend:Legend = Legend(item.parent.parent);
        	
        	if(legend) {
        		var dataName:String = legend.dataField.substr(legend.dataField.lastIndexOf('.') + 1);
        		
        		item.filters = null;
        		
        		vis.data.visit(function(d:DataSprite):void {
					if(d.data.hasOwnProperty(dataName) && item.value == d.data[dataName]) {
						d.filters = null;
						d.size = nodeSize;
					}
				}, 3, Filters.isDataSprite);
        	}
        }
        
        /**
        * Creates and returns a dialog box containing information on the passed data sprite.
        * @param data the DataSprite containing the information to display.
        * @returns the Sprite containing the dialog box.
        */
        private function dataDialogBox(data:DataSprite):Sprite {
        	var box:Sprite = new Sprite;
        	
        	var backGround:Sprite = new Sprite;	
        	backGround.graphics.beginFill(parseInt(settings.style.popup.bgcolor, 16), settings.style.popup.alpha);
        	backGround.graphics.lineStyle(settings.style.popup.line.size, parseInt(settings.style.popup.line.color, 16), settings.style.popup.line.alpha);
        	 
        	var text:Sprite = new Sprite;
        	var x:int = 5;
        	var y:int = 0;
        	
        	for(var property:Object in data.data) {
        		var temp:TextSprite = new TextSprite(property.toString() + ": " + data.data[property], new TextFormat(settings.style.popup.text.font, settings.style.popup.text.size, null, true));
        		temp.x = x;
        		temp.y = y;
        		text.addChild(temp);
        		y += temp.height;
        	}
   
        	backGround.graphics.drawRoundRect(0, 0, text.width + 10, text.height, 30, 30);     	
        	
        	box.addChild(backGround);
        	box.addChild(text);
        	
        	return box;
        }
        
        
        private function removeLegenedItemsNodes():void {
        	for(var i:int = 0; i < legends.numChildren; i++) {
        		for(var k:int = 0; k < Legend(legends.getChildAt(i)).items.numChildren; k++) {
        			var legendItem:LegendItem = LegendItem(Legend(legends.getChildAt(i)).items.getChildAt(k));
        		
        			if(legendItem.alpha < 1) {
        				removeLegendNodes(legendItem);
        			}
        		}
        	}
        }
        
        /**
        * Check if a mark on the chart is visible based on the related LegendItems states.
        * @param d the DataSprite to check the visiblility of.
        * @returns true if the mark is visible.
        */
        private function markIsVisible(d:DataSprite):Boolean {
        	var items:Array = getLegendItems(d);
        	
        	for each(var item:LegendItem in items) {
        		if(item.alpha != 1) {
        			return false;
        		}
        	}
        	
        	return true;
        }
        
        /**
        * Gets all LegenedItems realted to a given DataSprite/mark.
        * @params d the DataSprite on the chart.
        * @returns Array of LegendItems that are realted to the given DataSprite.
        */
        private function getLegendItems(d:DataSprite):Array {
        	var items:Array = new Array();
        	var legend:Legend;
        	var item:LegendItem;
        	var dataField:String;
        	
        	for(var i:uint = 0; i < legends.numChildren; i++) {
        		legend = Legend(legends.getChildAt(i));
				dataField = legend.dataField.substr(legend.dataField.lastIndexOf('.') + 1);

				if(d.data.hasOwnProperty(dataField)) {
        			for(var k:uint = 0; k < legend.items.numChildren; k++) {
        				item = LegendItem(legend.items.getChildAt(k));
        				
        				if(d.data[dataField] == item.value) {
        					items.push(item);
        					break;
        				}
        			}
    			}
        	}
        	
        	return items;
        }
        
        private function removeLegendNodes(item:LegendItem):void {
        	if(item != null) {
        		var legend:Legend = Legend(item.parent.parent);
        		var dataName:String = legend.dataField.substr(legend.dataField.lastIndexOf('.') + 1);
        		var nodes:Array = legendNodes[item] as Array;
        	
        		for each(var node:NodeSprite in nodes) {
        			vis.data.removeNode(node);
        		}
        	}
        }
        
        private function removeLegendEdges(item:LegendItem):void {
        	if(item != null) {
        		var legend:Legend = Legend(item.parent.parent);
        		var dataName:String = legend.dataField.substr(legend.dataField.lastIndexOf('.') + 1);
        		var edges:Array = legendEdges[item] as Array;
        	
        		for each(var edge:EdgeSprite in edges) {
        			vis.data.removeEdge(edge);
        		}
        	}
        }
        
        private function addLegendNodes(item:LegendItem):void {
        	if(item != null) {
        		var nodes:Array = legendNodes[item] as Array;
        		
        		for each(var node:NodeSprite in nodes) {
        			if(markIsVisible(node)) {
        				vis.data.addNode(node);
        			}
        		}
        	}
        }
        
        /*private function dirtyEdges():void {
        	vis.data.edges.visit(function(e:EdgeSprite):void{
        		e.dirty();
        	});
        }*/
        
        private function addLegendEdges(item:LegendItem):void {
        	if(item != null) {
        		var edges:Array = legendEdges[item] as Array;
        		
        		for each(var edge:EdgeSprite in edges) {
        			if(markIsVisible(edge)) {
        				edge.source.addOutEdge(edge);
        				edge.target.addInEdge(edge);
        				vis.data.addEdge(edge);
        			}
        		}
        	}	
        }
        
        /**
        * Function to be called when a LegendItem is clicked.
        * Changes the legendItems alpah value and updates mark visiblity.
        * @param evt the mouse event.
        */
        private function legendClick(evt:MouseEvent):void {
        	var ob:Object = evt.target;
        	var item:LegendItem;
        	
        	if(ob is LegendItem) {
        		item = LegendItem(ob);
        	} else if(ob is TextField) {
        		item = LegendItem(TextField(ob).parent.parent);
        	} else {
        		return;
        	}

        	if(item != null && !invertTransitioner.running && !legendItemTransitioner.running) {
        		legendItemTransitioner.reset();
        		
        		if(item.alpha >= 1) {
        			item.alpha = 0.4;
        			//removeLegendEdges(item);
        			removeLegendNodes(item);
        		} else {
        			item.alpha = 1.0;
        			addLegendNodes(item);
        			addLegendEdges(item);
        		}
        		
        		setUpLayout();
        		vis.update(legendItemTransitioner).play();
        	}
        }
        
        /**
        * Function called when a click happens on a mark on the chart.
        * Creates and adds a dialog box for that mark/DataSprite when clicked or removes the dialog box if
        * the mark allready has one.
        * @param the mouse event.
        */
        private function mouseClicked(evt:MouseEvent):void {
        	if(DisplayObject(evt.target).parent == vis.marks) {
        		if(lastBox != null && lastBoxData != null) {
        			lastBoxData.removeChild(lastBox);
        			boxhc.detach();
        		}
        		
        		if(evt.target != lastBoxData) {
        			lastBox = dataDialogBox(DataSprite(evt.target));
        			lastBoxData = DataSprite(evt.target);
        			Sprite(evt.target).addChild(lastBox);
    				vis.marks.setChildIndex(Sprite(evt.target), vis.marks.numChildren - 1);
    				boxhc.attach(Sprite(evt.target));
        		} else {
        			lastBoxData = null;
        			lastBox = null;
        		}
        	}
        }
        
        private function selectorClick(evt:MouseEvent):void {
        	var selectorOption:SelectorOption = SelectorOption(evt.target);
        	
        	if(!selectorOption.active) {
        		reharvest_data(dataURL + "&" + escape(selectorOption.param) + "=" + escape(selectorOption.value));
        		selectorOption.active = true;
        		selectorOption.alpha = 1;
        		Selector(selectorOption.parent).active.active = false;
        		Selector(selectorOption.parent).active.alpha = 0.4;		
        		Selector(selectorOption.parent).active = selectorOption;
        	}
        }
    }
}