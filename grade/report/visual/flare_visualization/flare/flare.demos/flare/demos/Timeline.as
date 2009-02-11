package flare.demos
{
	import flare.animate.Transitioner;
	import flare.display.TextSprite;
	import flare.util.Button;
	import flare.util.Maths;
	import flare.vis.Visualization;
	import flare.vis.controls.DragControl;
	import flare.vis.data.Data;
	import flare.vis.operator.encoder.ColorEncoder;
	import flare.vis.operator.layout.AxisLayout;
	import flare.vis.operator.layout.ForceDirectedLayout;
	import flare.vis.scale.ScaleType;
	import flare.vis.util.Filters;
	
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	
	public class Timeline extends Demo
	{
		public function Timeline() {
			name = "Timeline";
			
			var vis:Visualization = new Visualization(getTimeline(50, 3));
			vis.bounds = new Rectangle(0, 0, 600, 100);
			vis.operators.add(new AxisLayout("data.date", "data.count"));
			vis.operators.add(new ColorEncoder(
				"data.series", Data.EDGES, "lineColor", ScaleType.CATEGORIES));
			vis.operators.add(new ColorEncoder(
				"data.series", Data.NODES, "fillColor", ScaleType.CATEGORIES));
			vis.data.nodes.setProperty("alpha", 0.5);
			
			with (vis.xyAxes.xAxis) {
				horizontalAnchor = TextSprite.LEFT;
				verticalAnchor = TextSprite.MIDDLE;
				labelAngle = Math.PI / 2;
				axisScale.flush = true;	
			}
			vis.update();
			addChild(vis);
			
			vis.data.edges.setProperty("lineWidth", 2);
			vis.data.nodes.setProperty("lineAlpha", 0);
			vis.data.nodes.setProperty("size", 0.5);
			
			vis.x = vis.y = 40;
			
			var btn:Button = new Button("Bad Idea!");
			btn.x = 10; btn.y = HEIGHT - 10 - btn.height;
			btn.addEventListener(MouseEvent.CLICK, function(evt:MouseEvent):void
			{
				// create new force-directed layout that enforces the bounds
				var fdl:ForceDirectedLayout = new ForceDirectedLayout(true);
				fdl.defaultSpringLength = 20;
				fdl.layoutBounds = new Rectangle(0, 0, 600, 400);
				vis.operators.setOperatorAt(0, fdl);
				vis.continuousUpdates = true;
				
				var t:Transitioner = new Transitioner(1);
				vis.data.nodes.setProperties({buttonMode:true, scaleX:2, scaleY:2}, t);
				fdl.hideAxes(t).play();
				
				new DragControl(vis, Filters.isNodeSprite);
				removeChild(btn);
			});
			addChild(btn);
		}
		
		public static function getTimeline(N:int, M:int):Data
		{
			var MAX:Number = 60;
			var t0:Date = new Date(1979,5,15);
			var t1:Date = new Date(1982,2,19);
			var x:Number, f:Number;
			
			var data:Data = new Data();
			for (var i:uint=0; i<N; ++i) {
				for (var j:uint=0; j<M; ++j) {
					f = i/(N-1);
					x = t0.time + f*(t1.time - t0.time);
					data.addNode({
						series: int(j),
						date: new Date(x),
						count:int((j*MAX/M) + MAX/M * (1+Maths.noise(13*f,j)))
					});
				}
			}
			data.createEdges("data.date", "data.series");

			return data;
		}
	}
}