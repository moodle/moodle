package flare.animate
{
	import flare.util.Arrays;
	import flare.util.Maths;
	
	/**
	 * Transition that runs multiple transitions simultaneously (in parallel).
	 * The duration of this parallel transition is computed as the maximum
	 * total duration (duration + delay) among the sub-transitions. If the
	 * duration is explicitly set, the sub-transition lengths will be
	 * uniformly scaled to fit within the new time span.
	 */
	public class Parallel extends Transition
	{
		// -- Properties ------------------------------------------------------
		
		/** Array of parallel transitions */
		protected var _trans:/*Transition*/Array = [];
		/** @private */
		protected var _equidur:Boolean;
		/** @private */
		protected var _dirty:Boolean = false;
		/** @private */
		protected var _autodur:Boolean = true;

		/**
		 * If true, the duration of this sequence is automatically determined
		 * by the longest sub-transition. This is the default behavior.
		 */
		public function get autoDuration():Boolean { return _autodur; }
		public function set autoDuration(b:Boolean):void {
			_autodur = b;
			computeDuration();
		}
		
		/** @inheritDoc */
		public override function get duration():Number {
			if (_dirty) computeDuration();
			return super.duration;
		}
		public override function set duration(dur:Number):void {
			_autodur = false;
			super.duration = dur;
			_dirty = true;
		}
		
		// -- Methods ---------------------------------------------------------
		
		/**
		 * Creates a new Parallel transition.
		 * @param transitions a list of sub-transitions
		 */
		public function Parallel(...transitions) {
			easing = Easing.none;
			for each (var t:Transition in transitions) {
				_trans.push(t);
			}
			_dirty = true;
		}
		
		/**
		 * Adds a new sub-transition to this parallel transition.
		 * @param t the transition to add
		 */
		public function add(t:Transition):void {
			if (running) throw new Error("Transition is running!");
			_trans.push(t);
			_dirty = true;
		}
		
		/**
		 * Removes a sub-transition from this parallel transition.
		 * @param t the transition to remove
		 * @return true if the transition was found and removed, false
		 *  otherwise
		 */
		public function remove(t:Transition):Boolean {
			if (running) throw new Error("Transition is running!");
			var rem:Boolean = Arrays.remove(_trans, t) >= 0;
			if (rem) _dirty = true;
			return rem;
		}
		
		/**
		 * Computes the duration of this parallel transition.
		 */
		protected function computeDuration():void {
			var d:Number=0, td:Number;
			if (_trans.length > 0) d = _trans[0].totalDuration;
			_equidur = true;	
			for each (var t:Transition in _trans) {
				td = t.totalDuration;
				if (_equidur && td != d) _equidur = false;
				d = Math.max(d, t.totalDuration);
			}
			if (_autodur) super.duration = d;
			_dirty = false;
		}
		
		/** @inheritDoc */
		public override function dispose():void {
			while (_trans.length > 0) { _trans.pop().dispose(); }
		}
		
		// -- Transition Handlers ---------------------------------------------

		/** @inheritDoc */
		public override function play(reverse:Boolean=false):void
		{
			if (_dirty) computeDuration();
			super.play(reverse);
		}

		/**
		 * Sets up each sub-transition.
		 */
		protected override function setup():void
		{
			for each (var t:Transition in _trans) { t.doSetup(); }
		}
		
		/**
		 * Starts each sub-transition.
		 */
		protected override function start():void
		{
			for each (var t:Transition in _trans) { t.doStart(_reverse); }
		}
		
		/**
		 * Steps each sub-transition.
		 * @param ef the current progress fraction.
		 */
		internal override function step(ef:Number):void
		{
			var t:Transition;
			if (_equidur) {
				// if all durations are the same, we can skip some calculations
				for each (t in _trans) { t.doStep(ef); }
			} else {
				// otherwise, make sure we respect the different lengths
				var d:Number = duration;
				for each (t in _trans) {
					var td:Number = t.totalDuration;
					var f:Number = d==0 || td==d ? 1 : td/d;
					t.doStep(ef>f ? 1 : f==1 ? ef : ef/f);
				}
			}
		}
		
		/**
		 * Ends each sub-transition.
		 */
		protected override function end():void
		{
			for each (var t:Transition in _trans) { t.doEnd(); }
		}
		
	} // end of class Parallel
}