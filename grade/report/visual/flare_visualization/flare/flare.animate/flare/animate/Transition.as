package flare.animate
{
	import flare.util.Maths;
		
	/**
	 * Base class representing an animated transition. Provides support for
	 * tracking animation progress over a time duration. The Transition class
	 * also provides set of callback functions (<tt>onStart</tt>,
	 * <tt>onStep</tt>, and <tt>onEnd</tt>) that are useful for tracking and
	 * responding to a transition's progress.
	 * 
	 * <p>Useful subclasses of <code>Transition</code> include the
	 * <code>Tween</code>, <code>Parallel</code>, <code>Sequence</code>,
	 * <code>Pause</code>, and <code>Transitioner</code> classes.</p>
	 */
	public class Transition implements ISchedulable
	{
		/** Default easing function: a cubic slow-in slow-out. */
		public static var DEFAULT_EASING:Function = Easing.easeInOutPoly(3);
		
		/** Constant indicating this Transition needs initialization. */
		protected static const SETUP:int = 0;
		/** Constant indicating this Transition has been initialized. */
		protected static const INIT:int = 1;
		/** Constant indicating this Transition is currently running. */
		protected static const RUN:int = 2;
		
		// -- Properties ------------------------------------------------------

		private var _easing:Function = DEFAULT_EASING; // easing function
				
		private var _duration:Number;         // duration, in seconds
		private var _delay:Number;            // delay, in seconds
		private var _frac:Number;             // animation fraction
		private var _state:int = SETUP;       // initialization flag
		/** @private */
		protected var _start:Number;          // start time	
		/** Flag indicating this Transition is currently running. */
		protected var _running:Boolean = false;
		/** Flag indicating this Transition is running in reverse. */
		protected var _reverse:Boolean = false;
		
		/** Function called each time this Transition steps. */
		public var onStep:Function = null;
		/** Function called when this Transition starts. 
		 *  Even when playing in reverse, this function is called first. */
		public var onStart:Function = null;
		/** Function called when this Transition completes.
		 *  Even when playing in reverse, this function is called last. */
		public var onEnd:Function = null;
		
		/** The total duration, including both delay and active duration. */
		public function get totalDuration():Number { return duration + delay; }
		
		/** The duration (length) of this Transition, in seconds. */
		public function get duration():Number { return _duration; }
		public function set duration(d:Number):void {
			if (d<0) throw new ArgumentError("Negative duration not allowed.");
			_duration = d;
		}

		/** The delay between a call to play and the actual start
		 *  of the transition, in seconds. */
		public function get delay():Number { return _delay; }
		public function set delay(d:Number):void {
			if (d<0) throw new ArgumentError("Negative delay not allowed.");
			_delay = d;
		}
		
		/** Fraction between 0 and 1 indicating the current progress
		 *  of this transition. */
		public function get progress():Number { return _frac; }
		internal function set progress(f:Number):void { _frac = f; }
		
		/** Easing function used to pace this Transition. */
		public function get easing():Function { return _easing; }
		public function set easing(f:Function):void { _easing = f; }
		
		/** Indicates if this Transition is currently running. */
		public function get running():Boolean { return _running; }
		
		// -- Methods ---------------------------------------------------------
		
		/**
		 * Creates a new Transition.
		 * @param duration the duration, in seconds
		 * @param delay the delay, in seconds
		 * @param easing the easing function
		 */
		public function Transition(duration:Number=1, delay:Number=0,
								   easing:Function=null)
		{
			_duration = duration;
			_delay = delay;
			_easing = (easing==null ? DEFAULT_EASING : easing);
		}
		
		/**
		 * Starts running the transition.
		 * @param reverse if true, the transition is played in reverse,
		 *  if false (the default), it is played normally.
		 */
		public function play(reverse:Boolean = false):void
		{
			_reverse = reverse;
			init();
			Scheduler.instance.add(this);
			_running = true;
		}
		
		/**
		 * Stops the transition and completes it.
		 * Any end-of-transition actions will still be taken.
		 * Calling play() after stop() will result in the transition
		 * starting over from the beginning.
		 */
		public function stop():void
		{
			Scheduler.instance.remove(this);
			doEnd();
		}
		
		/**
		 * Resets the transition, so that any cached starting values are
		 * cleared and reset the next time this transition is played.
		 */
		public function reset():void
		{
			_state = SETUP;
		}
		
		/**
		 * Pauses the transition at its current position.
		 * Calling play() after pause() will resume the transition.
		 */
		public function pause():void
		{
			Scheduler.instance.remove(this);
			_running = false;
		}
		
		private function init():void
		{
			if (_state == SETUP) doSetup();
			if (_state == RUN) {
				var f:Number = _reverse ? (1-_frac) : _frac;
				_start = new Date().time - f * 1000 * (duration + delay);
			} else {
				_start = new Date().time;
				doStart(_reverse);
			}
			_state = RUN;
		}

		internal function doSetup():void
		{
			setup();
			_state = INIT;
		}

		internal function doStart(reverse:Boolean):void
		{
			_reverse = reverse;
			_running = true;
			_frac = _reverse ? 1 : 0;
			start(); if (onStart!=null) onStart();
		}
		
		internal function doStep(frac:Number):void
		{
			_frac = frac;
			var f:Number = delay==0 ? frac
				 : Maths.invLinearInterp(frac, delay/totalDuration, 1);
			if (f >= 0) { step(_easing(f)); }
			if (onStep != null) onStep();
		}
		
		internal function doEnd():void
		{
			_frac = _reverse ? 0 : 1;
			end();
			_state = INIT;
			_running = false;
			if (onEnd!=null) onEnd();
		}
		
		/**
		 * Evaluates the Transition, stepping the transition forward.
		 * @param time the current time in milliseconds
		 * @return true if this item should be removed from the scheduler,
		 * false if it should continue to be run.
		 */
		public function evaluate(time:Number):Boolean
		{
			var t:Number = time - _start;
			if (t < 0) return false;
			
			// step the transition forward
			var d:Number = 1000 * (duration + delay);
			t = (d==0 ? 1.0 : t/d);
			if (t > 1) t = 1; // clamp
			doStep(_reverse ? 1-t : t);
			
			// check if we're done
			var _done:Boolean = (t >= 1.0);
			if (_done) { doEnd(); }
			return _done;
		}
		
		/**
		 * Disposes of this transition, freeing up any resources held. This
		 * method is optional, but calling it when a transition is no longer
		 * needed can help improve overall performance.
		 */
		public function dispose():void
		{
			// for sub-classes to implement
		}
		
		// -- abstract methods ------------------------------------------------
		
		/**
		 * Transition setup routine. Subclasses should override this function
		 * to perform custom setup actions.
		 */
		protected function setup():void
		{
			// for sub-classes to implement
		}
		
		/**
		 * Transition start routine. Subclasses should override this function
		 * to perform custom start actions.
		 */
		protected function start():void
		{
			// for sub-classes to implement
		}
		
		/**
		 * Transition step routine. Subclasses should override this function
		 * to perform custom step actions.
		 */
		internal function step(ef:Number):void
		{
			// for sub-classes to implement
		}
		
		/**
		 * Transition end routine. Subclasses should override this function
		 * to perform custom ending actions.
		 */
		protected function end():void
		{
			// for sub-classes to implement
		}
		
	} // end of class Transition
}