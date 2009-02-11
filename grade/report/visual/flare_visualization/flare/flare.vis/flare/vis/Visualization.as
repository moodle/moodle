package flare.vis
{
	import flare.animate.ISchedulable;
	import flare.animate.Scheduler;
	import flare.animate.Transitioner;
	import flare.vis.axis.Axes;
	import flare.vis.axis.CartesianAxes;
	import flare.vis.controls.ControlList;
	import flare.vis.data.Data;
	import flare.vis.data.Tree;
	import flare.vis.events.DataEvent;
	import flare.vis.events.VisualizationEvent;
	import flare.vis.operator.OperatorList;
	
	import flash.display.Sprite;
	import flash.geom.Rectangle;

	/**
	 * The Visualization class represents an interactive data visualization.
	 * A visualization instance consists of
	 * <ul>
	 *  <li>A <code>Data</code> instance containing <code>DataSprite</code>
	 *      objects that visually represent individual data elements</li>
	 *  <li>An <code>OperatorList</code> of visualization operators that
	 *      determine visual encodings for position, color, size and other
	 *      properties.</li>
	 *  <li>A <code>ControlList</code> of interactive controls that enable
	 *      interaction with the visualized data.</li>
	 *  <li>An <code>Axes</code> instance for presenting axes for metric
	 *      data visualizations. Axes are often configuring automatically by
	 *      the visualization's operators.</li>
	 * </ul>
	 * 
	 * <p>Visual objects are added to the display list within the
	 * <code>marks</code> property of the visualization, as the
	 * <code>Data</code> object is not a <code>DisplayObjectContainer</code>.
	 * </p>
	 * 
	 * <p>To create a new Visualization, load in a data set, construct
	 * a <code>Data</code> instance, and instantiate a new
	 * <code>Visualization</code> with the input data. Then add the series
	 * of desired operators to the <code>operators</code> property to 
	 * define the visual encodings.</p>
	 * 
	 * @see flare.vis.operator
	 */
	public class Visualization extends Sprite
	{	
		// -- Properties ------------------------------------------------------
		
		private var _bounds:Rectangle = new Rectangle(0,0,500,500);
		
		private var _marks:Sprite;
		private var _axes:Axes;
		private var _data:Data;
		
		private var _operators:OperatorList;
		private var _controls:ControlList;
		private var _rec:ISchedulable; // for running continuous updates
		
		/** The layout bounds of the visualization. This determines the layout
		 *  region for data elements. For example, with an axis layout, the
		 *  bounds determined the data layout region--this does not include
		 *  space used by axis labels.
		 */
		public function get bounds():Rectangle { return _bounds; }
		public function set bounds(r:Rectangle):void { _bounds = r; }
		
		/**
		 * The axes for this visualization. May be null if no axes are needed.
		 */
		public function get axes():Axes { return _axes; }
		public function set axes(a:Axes):void {
			_axes = a;
			_axes.visualization = this;
			_axes.name = "_axes";
			addChildAt(_axes, 0);
		}
		/** The axes as an x-y <code>CartesianAxes</code> instance. Returns
		 *  null if <code>axes</code> is null or not a cartesian axes instance.
		 */
		public function get xyAxes():CartesianAxes { return _axes as CartesianAxes; }
		
		/** Sprite containing the visualization's <code>DataSprite</code>
		 *  instances. */
		public function get marks():Sprite { return _marks; }
		
		/** The visual data elements in this visualization. */
		public function get data():Data { return _data; }
		/** Tree structure of visual data elements in this visualization.
		 *  Generates a spanning tree over a graph structure, if necessary. */
		public function get tree():Tree { return _data.tree; }
		public function set data(d:Data):void
		{
			if (_data != null) {
				_data.visit(_marks.removeChild);
				_data.removeEventListener(DataEvent.DATA_ADDED, dataAdded);
				_data.removeEventListener(DataEvent.DATA_REMOVED, dataRemoved);
			}
			_data = d;
			if (_data != null) {
				_data.visit(_marks.addChild);
				_data.addEventListener(DataEvent.DATA_ADDED, dataAdded);
				_data.addEventListener(DataEvent.DATA_REMOVED, dataRemoved);
			}
		}

		/** The operator list for defining the visual encodings. */
		public function get operators():OperatorList { return _operators; }
		
		/** The control list containing interactive controls. */
		public function get controls():ControlList { return _controls; }
		
		/** Flag indicating if the visualization should update with every
		 *  frame. False by default. */
		public function get continuousUpdates():Boolean { return _rec != null; }
		public function set continuousUpdates(b:Boolean):void
		{
			if (b && _rec==null) {
				_rec = new Recurrence(this);
				Scheduler.instance.add(_rec);
			}
			else if (!b && _rec!=null) {
				Scheduler.instance.remove(_rec);
				_rec = null;
			}
		}
		
		// -- Methods ---------------------------------------------------------
		
		/**
		 * Creates a new Visualization with the given data and axes.
		 * @param data the <code>Data</code> instance containing the
		 *  <code>DataSprite</code> elements in this visualization.
		 * @param axes the <code>Axes</code> to use with this visualization.
		 *  Null by default; layout operators may re-configure the axes.
		 */
		public function Visualization(data:Data=null, axes:Axes=null) {
			addChild(_marks = new Sprite());
			_marks.name = "_marks";
			if (data != null) this.data = data;
			if (axes != null) this.axes = axes;
			
			_operators = new OperatorList();
			_operators.visualization = this;
			
			_controls = new ControlList();
			_controls.visualization = this;
		}
		
		/**
		 * Update this visualization, re-calculating axis layout and running
		 * the operator chain. The input transitioner is used to actually
		 * perform value updates, enabling animated transitions. This method
		 * also issues a <code>VisualizationEvent.UPDATE</code> event to any
		 * registered listeners.
		 * @param t a transitioner or time span for updating object values. If
		 *  the input is a transitioner, it will be used to store the updated
		 *  values. If the input is a number, a new Transitioner with duration
		 *  set to the input value will be used. The input is null by default,
		 *  in which case object values are updated immediately.
		 * @return the transitioner used to store updated values.
		 */
		public function update(t:*=null):Transitioner
		{
			var trans:Transitioner = Transitioner.instance(t);
			if (_axes != null) _axes.update(trans);
			_operators.operate(trans);
			if (_axes != null) _axes.update(trans);
			fireEvent(VisualizationEvent.UPDATE, trans);
			return trans;
		}

		// -- Event Handling --------------------------------------------------

		/**
		 * Fires a visualization event of the given type.
		 * @param type the type of the event
		 * @param t a transitioner that listeners should use for any value
		 *  updates performed in response to this event
		 */
		protected function fireEvent(type:String, t:Transitioner):void
		{			
			// fire event, if anyone is listening
			if (hasEventListener(type)) {
				dispatchEvent(new VisualizationEvent(type, t));
			}
		}
		
		/**
		 * Data listener invoked when new items are added to this
		 * Visualization's <code>data</code> instance.
		 * @param evt the data event
		 */
		protected function dataAdded(evt:DataEvent):void
		{
			if (evt.node != null) {
				_marks.addChild(evt.node);
			} else {
				_marks.addChildAt(evt.item, 0);
			}
		}
		
		/**
		 * Data listener invoked when new items are removed from this
		 * Visualization's <code>data</code> instance.
		 * @param evt the data event
		 */
		protected function dataRemoved(evt:DataEvent):void
		{
			_marks.removeChild(evt.item);
		}

	} // end of class Visualization
}


import flare.animate.ISchedulable;
import flare.vis.Visualization;

/**
 * Simple ISchedulable instance that repeatedly calls a Visualization's
 * <code>update</code> method.
 */
class Recurrence implements ISchedulable {
	private var v:Visualization;
	public function Recurrence(v:Visualization) {
		this.v = v;
	}
	public function evaluate(t:Number):Boolean {
		v.update(); return false;
	}
}