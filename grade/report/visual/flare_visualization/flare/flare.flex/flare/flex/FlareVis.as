package flare.flex
{
	import flare.data.DataSet;
	import flare.display.DirtySprite;
	import flare.vis.Visualization;
	import flare.vis.axis.Axes;
	import flare.vis.axis.CartesianAxes;
	import flare.vis.data.Data;
	
	import mx.containers.Canvas;

	/**
	 * Flex component that wraps a Flare visualization instance. This class can
	 * be used to create Flare visualizations within an MXML file. The
	 * underlying Flare <code>Visualization</code> instance can always be
	 * accessed using the <code>visualization</code> property.
	 */
	public class FlareVis extends Canvas
	{
		private var _vis:Visualization;
		
		/** The visualization operators used by this visualization. This
		 *  should be an array of IOperator instances. */
		public function set operators(a:Array):void {
			_vis.operators.list = a;
			_vis.update();
		}
		
		/** The interactive controls used by this visualization. This
		 *  should be an array of IControl instances. */
		public function set controls(a:Array):void {
			_vis.controls.list = a;
			_vis.update();
		}
		
		/** Sets the data visualized by this instance. The input value can be
		 *  an array of data objects, a Data instance, or a DataSet instance.
		 *  Any existing data will be removed and new NodeSprite instances will
		 *  be created for each object in the input arrary. */
		public function set dataSet(d:*):void {
			var dd:Data;
			
			if (d is Data) {
				dd = Data(d);
			} else if (d is Array) {
				dd = Data.fromArray(d as Array);
			} else if (d is DataSet) {
				dd = Data.fromDataSet(d as DataSet);
			} else {
				throw new Error("Unrecognized data set type: "+d);
			}
			_vis.data = dd;
			_vis.operators.setup();
			_vis.update();
		}
		
		/** Returns the axes for the backing visualization instance. */
		public function get axes():Axes { return _vis.axes; }
		
		/** Returns the CartesianAxes for the backing visualization instance. */
		public function get xyAxes():CartesianAxes { return _vis.xyAxes; }
		
		/** Returns the backing Flare visualization instance. */
		public function get visualization():Visualization {
			return _vis;
		}
		
		public function get visWidth():Number { return _vis.bounds.width; }
		public function set visWidth(w:Number):void {
			_vis.bounds.width = w;
			_vis.update();
			invalidateSize();
		}
		
		public function get visHeight():Number { return _vis.bounds.height; }
		public function set visHeight(h:Number):void {
			_vis.bounds.height = h;
			_vis.update();
			invalidateSize();
		}
		
		// --------------------------------------------------------------------
		
		private var _margin:int = 10;
		
		/**
		 * Creates a new FlareVis component. By default, a new visualization
		 * with an empty data set is created.
		 * @param data the data to visualize. If this value is null, a new
		 *  empty data instance will be used.
		 */
		public function FlareVis(data:Data=null) {
			this.rawChildren.addChild(
				_vis = new Visualization(data==null ? new Data() : data)
			);
			_vis.x = _margin;
		}
		
		// -- Flex Overrides --------------------------------------------------
		
		/** @private */
		public override function getExplicitOrMeasuredWidth():Number {
			DirtySprite.renderDirty(); // make sure everything is current
			var w:Number = _vis.bounds.width;
			if (_vis.width > w) {
				// TODO: this is a temporary hack. fix later!
				_vis.x = _margin + Math.abs(_vis.getBounds(_vis).x);
				w = _vis.width;
			}
			return 2*_margin + Math.max(super.getExplicitOrMeasuredWidth(), w);
		}
		
		/** @private */
		public override function getExplicitOrMeasuredHeight():Number {
			DirtySprite.renderDirty(); // make sure everything is current
			return Math.max(super.getExplicitOrMeasuredHeight(),
							_vis.bounds.height,
							_vis.height);
		}
		
	} // end of class FlareVis
}