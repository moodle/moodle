package flare.demos
{
	import flare.animate.Transitioner;
	import flare.util.Button;
	import flare.vis.Visualization;
	import flare.vis.data.Data;
	import flare.vis.data.DataSprite;
	import flare.vis.operator.encoder.ColorEncoder;
	import flare.vis.operator.layout.AxisLayout;
	import flare.vis.scale.ScaleType;
	import flare.vis.util.graphics.Shapes;
	
	import flash.events.MouseEvent;
	
	public class Bars extends Demo
	{
		public function Bars() {
			name = "Bars";
			
			var vis:Visualization = new Visualization(getData(44,20));
			vis.bounds.width = 700;
			vis.bounds.height = HEIGHT - 100;
			vis.data.nodes.setProperties({
				shape: Shapes.HORIZONTAL_BAR,
				lineAlpha: 0,
				size: 1.5
			});

			vis.operators.add(new AxisLayout("data.x", "data.y", true, false));
			vis.operators.add(new ColorEncoder("data.s", 1, "fillColor", ScaleType.CATEGORIES));
			vis.xyAxes.yAxis.showLines = false;
			vis.update();

			addChild(vis);
			
			vis.x = 50; vis.y = 20;
			
			
			// data update
			var btn:Button = new Button("Update");
			btn.addEventListener(MouseEvent.CLICK, function(evt:MouseEvent):void
			{
				updateData(vis.data);
				vis.update(new Transitioner(2)).play();
			});
			btn.x = 10; btn.y = HEIGHT - 10 - btn.height;
			addChild(btn);
		}
		
		public static function getData(N:int, M:int):Data
		{
			var data:Data = new Data();
			for (var i:uint=0; i<N; ++i) {
				for (var j:uint=0; j<M; ++j) {
					var s:String = String(i<10?"0"+i:i);
					data.addNode({
						y:s, s:j, x: int(1 + 10*Math.random())
					});
				}
			}
			return data;
		}
		
		public static function updateData(data:Data):void
		{
			data.nodes.visit(function(d:DataSprite):void {
				d.data.x = int(1 + 10*Math.random());
			});
		}
		
	}
}