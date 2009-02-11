package flare.demos
{
	import flare.vis.data.Data;
	import flare.vis.Visualization;
	import flare.vis.operator.layout.PieLayout;
	import flare.vis.operator.encoder.ColorEncoder;
	import flare.vis.data.DataSprite;
	import flare.vis.util.graphics.Shapes;
	import flash.events.MouseEvent;
	import flare.animate.Tween;
	import flare.animate.Transitioner;
	import flare.util.Button;
	
	public class Pie extends Demo
	{
		public function Pie() {
			name = "Pie";
			
			// create pie chart
			var vis:Visualization = new Visualization(getData(16));
			vis.data.nodes.setProperties({
				shape: Shapes.WEDGE,
				lineAlpha: 0
			});
			vis.operators.add(new PieLayout("data.value", 0.7));
			vis.operators.add(new ColorEncoder("data.value",1,"fillColor"));
			vis.update();
			
			addChild(vis);
			
			vis.x = (WIDTH - vis.bounds.width) / 2;
			vis.y = (HEIGHT - vis.bounds.height) / 2;
			
			// expand / collapse button
			var btn:Button = new Button("Expand / Collapse");
			var collapse:Boolean = true;
			btn.addEventListener(MouseEvent.CLICK, function(evt:MouseEvent):void
			{
				var t:Transitioner = new Transitioner(1);
				vis.data.nodes.visit(function(d:DataSprite):void {
					t.$(d).v = collapse ? 0 : d.h * 0.7;
				});
				collapse = !collapse;
				t.play();
			});
			btn.x = 10;
			btn.y = HEIGHT - 10 - btn.height;
			addChild(btn);
		}
		
		public static function getData(N:int):Data
		{
			var data:Data = new Data();
			for (var i:uint=0; i<N; ++i) {
				data.addNode({id:i, value:Math.random()});
			}
			return data;
		}
	}
}