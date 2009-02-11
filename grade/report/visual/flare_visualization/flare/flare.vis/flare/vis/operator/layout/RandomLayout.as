package flare.vis.operator.layout
{
	import flare.animate.Transitioner;
	import flash.geom.Rectangle;
	import flare.vis.data.DataSprite;
	
	/**
	 * Layout that places nodes randomly within the layout bounds.
	 */
	public class RandomLayout extends Layout
	{
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			if (t==null) t = Transitioner.DEFAULT;
			var r:Rectangle = layoutBounds;
			visualization.data.nodes.visit(function(d:DataSprite):void {
				t.$(d).x = r.x + r.width * Math.random();
				t.$(d).y = r.y + r.height * Math.random();
			});
		}
		
	} // end of class RandomLayout
}