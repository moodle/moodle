package flare.demos
{
	import flare.animate.Transitioner;
	import flare.util.Button;
	import flare.util.Stats;
	import flare.vis.Visualization;
	import flare.vis.controls.HoverControl;
	import flare.vis.controls.SelectionControl;
	import flare.vis.data.Data;
	import flare.vis.data.DataSprite;
	import flare.vis.operator.distortion.BifocalDistortion;
	import flare.vis.operator.encoder.ColorEncoder;
	import flare.vis.operator.encoder.ShapeEncoder;
	import flare.vis.operator.encoder.SizeEncoder;
	import flare.vis.operator.layout.AxisLayout;
	import flare.vis.palette.ColorPalette;
	import flare.vis.scale.ScaleType;
	import flare.vis.util.Filters;
	
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Point;
	
	public class Chart extends Demo
	{
		private var vis:Visualization;
		private var distort:flare.vis.operator.distortion.Distortion;
		
		public function Chart() {
			name = "Chart";
			
			vis = new Visualization(getData(100));
			vis.bounds.width = WIDTH-65;
			vis.bounds.height = HEIGHT-90;
			addChild(vis);
			
			var field1:String = "data.value1";
			var field2:String = "data.value2";
			
			vis.data.visit(function(d:DataSprite):void {
				d.fillColor = 0x018888ff;
				d.lineColor = 0xcc000088;
				d.lineWidth = 3;
			});
			vis.operators.add(new AxisLayout(field1, field2));
			vis.operators.add(new ShapeEncoder(field1));
			vis.operators.add(new SizeEncoder(field2, Data.NODES, ScaleType.QUANTILE, 5));
			vis.operators.add(new ColorEncoder(field1,Data.NODES, "lineColor", ScaleType.CATEGORIES));
			vis.xyAxes.xAxis.fixLabelOverlap = false; // keep overlapping labels
			vis.update();
			
			vis.x = 45;
			vis.y = 15;
			
			// add mouse over
			var hc:HoverControl = new HoverControl(vis, Filters.isDataSprite);
			hc.onRollOver = function(d:DataSprite):void {
				d.filters = [new GlowFilter(0xFFFF55, 0.8, 6, 6, 10)];
			};
			hc.onRollOut = function(d:DataSprite):void {
				d.filters = null;
			}
			
			var sc:SelectionControl = new SelectionControl(null, Filters.isDataSprite);
			sc.onSelect = hc.onRollOver;
			sc.onDeselect = hc.onRollOut;
			vis.controls.add(sc);

			// add scale update button
			var bs:Button = new Button("Change Scale");
			bs.addEventListener(MouseEvent.CLICK, function(evt:MouseEvent):void {
				// change the x-axis scale and animate the result
				var al:AxisLayout = vis.operators[0] as AxisLayout;
				al.xScaleType = 
					al.xScaleType==ScaleType.LOG ? ScaleType.LINEAR : ScaleType.LOG;
				vis.update(new Transitioner(2)).play();
			});
			bs.x = 10; bs.y = HEIGHT - 10 - bs.height;
			addChild(bs);
			
			// add scale distortion button
			var bd:Button = new Button("Distortion");
			bd.addEventListener(MouseEvent.CLICK, function(evt:MouseEvent):void {
				if (distort != null) {
					// remove distortion operator and frame listener
					vis.operators.remove(distort);
					distort = null;
					removeEventListener(Event.ENTER_FRAME, mouseUpdate);
					// animate back to non-distorted view
					vis.update(new Transitioner(1)).play();
				} else {
					// add and initialize distortion operator 
					vis.operators.add(distort=new BifocalDistortion());
					distort.distortSize = false;
					distort.layoutAnchor = new Point(vis.mouseX, vis.mouseY);
					// animate into distorted view, add frame listener
					var t:Transitioner = vis.update(new Transitioner(1));
					t.onEnd = function():void {
						addEventListener(Event.ENTER_FRAME, mouseUpdate);	
					};
					t.play();
				}
			});
			bd.x = 10 + bs.x + bs.width; bd.y = HEIGHT - 10 - bd.height;
			addChild(bd);
		}
		
		private function mouseUpdate(evt:Event):void
		{
			// get current anchor, run update if changed
			var p1:Point = distort.layoutAnchor;
			distort.layoutAnchor = new Point(vis.mouseX, vis.mouseY);
			// distortion might snap the anchor to the layout bounds
			// so we need to re-retrieve the point to get an accurate point
			var p2:Point = distort.layoutAnchor;
			if (p1.x != p2.x || p1.y != p2.y) vis.update();
		}
		
		public static function getData(n:int):Data
		{
			var data:Data = new Data();
			var d:DataSprite;
			var i:uint = 0;
			
			for (; i<10 && i<n; ++i) {
				d = data.addNode({
					value1: int(1 + 9*Math.random()),
					value2: int(200*(Math.random()-0.5))
				});
			}
			for (; i<n; ++i) {
				d = data.addNode({
					value1: int(1 + 99*Math.random()),
					value2: int(200*(Math.random()-0.5))
				});
			}
			return data;
		}
	}
}