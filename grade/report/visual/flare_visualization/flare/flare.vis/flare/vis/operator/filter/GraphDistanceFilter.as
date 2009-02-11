package flare.vis.operator.filter
{
	import flare.vis.operator.Operator;
	import flare.animate.Transitioner;
	import flare.vis.data.NodeSprite;
	import flash.utils.Dictionary;
	import flare.vis.data.EdgeSprite;
	import flare.vis.data.DataSprite;
	import flare.vis.data.Data;

	/**
	 * Filter operator that sets visible all items within a specified graph
	 * distance from a set of focus nodes.
	 */
	public class GraphDistanceFilter extends Operator
	{
		/** An array of focal NodeSprites. */
		public var focusNodes:/*NodeSprite*/Array;
		/** Graph distance within which which items wll be visible. */
		public var distance:int;
		/** Flag indicating which graph links to traverse. */
		public var links:int;
		
		/**
		 * Creates a new GraphDistanceFilter.
		 * @param focusNodes an array of focal NodeSprites. Graph distance is
		 *  measured as the minimum number of edge-hops to one of these nodes.
		 * @param distance graph distance within which items will be visible
		 * @param links flag indicating which graph links to traverse. The
		 *  default value is <code>NodeSprite.GRAPH_LINKS</code>.
		 */		
		public function GraphDistanceFilter(focusNodes:Array, distance:int=1,
			links:int=3/*NodeSprite.GRAPH_LINKS*/)
		{
			this.focusNodes = focusNodes;
			this.distance = distance;
			this.links = links;
		}
		
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			t = (t==null ? Transitioner.DEFAULT : t);
	        
	        // initialize breadth-first traversal
	        var q:Array = [], depths:Dictionary = new Dictionary();
			for each (var fn:NodeSprite in focusNodes) {				
				depths[fn] = 0;
				fn.visitEdges(function(e:EdgeSprite):void {
					depths[e] = 1;
					q.push(e);
				}, links);
			}
			
			// perform breadth-first traversal
			var xe:EdgeSprite, xn:NodeSprite, d:int;
			while (q.length > 0) {
				xe = q.shift(); d = depths[xe];
				xn = (depths[xe.source] == undefined ? xe.source : xe.target);
				depths[xn] = d;
				if (d == distance) continue; // stop traversal at max distance
				
				xn.visitEdges(function(e:EdgeSprite):void {
					if (depths[e] == undefined) {
						depths[e] = d+1;
						q.push(e);
					}
				}, links);
			}
			
			// now set visibility based on traversal results
	        visualization.data.visit(function(ds:DataSprite):void {
	        	var visible:Boolean = (depths[ds] != undefined);
	        	var alpha:Number = visible ? 1 : 0;
				var obj:Object = t.$(ds);
				
				obj.alpha = alpha;
				if (ds is NodeSprite) {
					var ns:NodeSprite = ds as NodeSprite;
					ns.expanded = (visible && depths[ds] < distance);
				}
				if (t.immediate) {
					ds.visible = visible;
				} else {
					obj.visible = visible;
				}
	        }, Data.ALL);
		}
		
	} // end of class GraphDistanceFilter
}