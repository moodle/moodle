package flare.demos
{
	import flash.events.Event;
	import flare.vis.data.NodeSprite;
	import flare.vis.data.Tree;
	import flare.vis.data.EdgeSprite;
	import flare.vis.operator.layout.Layout;
	import flare.vis.operator.layout.TreeMapLayout;
	import flash.geom.Rectangle;
	import flare.util.GraphUtil;
	import flare.vis.data.DataSprite;
	import flash.display.StageQuality;
	import flare.util.Colors;
	import flare.vis.Visualization;
	import flash.events.MouseEvent;
	import flash.display.Sprite;
	import flash.display.DisplayObjectContainer;
	import flare.vis.util.graphics.Shapes;
	import flare.vis.data.Data;
	import flare.vis.controls.HoverControl;
	import flare.vis.util.Filters;
	
	public class TreeMap extends Demo
	{
		public function TreeMap() {
			name = "TreeMap";
			var tree:Tree = GraphUtil.balancedTree(4,5);
			var e:EdgeSprite, n:NodeSprite;
			
			var vis:Visualization = new Visualization(tree);
			vis.tree.nodes.visit(function(n:NodeSprite):void {
				n.size = Math.random();
				n.shape = Shapes.BLOCK;
				n.fillColor = 0xff8888FF; n.lineColor = 0;
				n.fillAlpha = n.lineAlpha = n.depth / 25;
			});
			vis.data.edges.setProperty("visible", false);
			vis.operators.add(new TreeMapLayout());
			vis.bounds = new Rectangle(0, 0, WIDTH, HEIGHT);		
			vis.update();
			addChild(vis);
			
			var hc:HoverControl = new HoverControl(null, 
				Filters.isNodeSprite, HoverControl.MOVE_AND_RETURN);
			hc.onRollOver = function(n:NodeSprite):void {
				n.lineColor = 0xffFF0000; n.lineWidth = 2;
				n.fillColor = 0xffFFFFAAAA; 
			};
			hc.onRollOut = function(n:NodeSprite):void {
				n.lineColor = 0; n.lineWidth = 0;
				n.fillColor = 0xff8888FF;
				n.fillAlpha = n.lineAlpha = n.depth / 25;
			}
			vis.controls.add(hc);
		}
		
		public override function play():void
		{
			stage.quality = StageQuality.LOW;
		}
		
		public override function stop():void
		{
			stage.quality = StageQuality.HIGH;
		}
	}
}