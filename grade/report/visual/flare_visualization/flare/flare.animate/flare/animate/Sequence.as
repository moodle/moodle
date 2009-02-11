package flare.animate
{
	import flare.util.Arrays;
	import flare.util.Maths;
	
	/**
	 * Transition that runs multiple transitions one after the other in
	 * sequence. By default, the total duration of the sequence is the sum of
	 * the durations and delays of the sub-transitions. If the duration
	 * of the sequence is set explicitly, the duration and delay for
	 * sub-transitions will be uniformly scaled to fit within in the new
	 * time span.
	 */
	public class Sequence extends Transition
	{
		// -- Properties ------------------------------------------------------
		
		/** Array of sequential transitions */
		protected var _trans:/*Transition*/Array = [];
		/** @private */
		protected var _fracs:/*Number*/Array = [];
		/** @private */
		protected var _autodur:Boolean = true;
		/** @private */
		protected var _dirty:Boolean = false;
		/** @private */
		protected var _idx:int = 0;
		
		/**
		 * If true, the duration of this sequence is automatically determined
		 * by the durations of each sub-transition. This is the default behavior.
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
		 * Creates a new Sequence transition.
		 * @param transitions an ordered list of sub-transitions
		 */
		public function Sequence(...transitions) {
			easing = Easing.none;
			for each (var t:Transition in transitions) {
				_trans.push(t);
			}
			_dirty = true;
		}
		
		/**
		 * Adds a new transition to the end of this sequence.
		 * @param t the transition to add
		 */
		public function add(t:Transition):void
		{
			if (running) throw new Error("Transition is running!");
			_trans.push(t);
			_dirty = true;
		}
		
		/**
		 * Removes a sub-transition from this sequence.
		 * @param t the transition to remove
		 * @return true if the transition was found and removed, false
		 *  otherwise
		 */ 
		public function remove(t:Transition):Boolean
		{
			if (running) throw new Error("Transition is running!");
			var rem:Boolean = Arrays.remove(_trans, t) >= 0;
			if (rem) _dirty = true;
			return rem;
		}
		
		/**
		 * Computes the duration of this sequence transition.
		 */
		protected function computeDuration():void
		{
			var d:Number = 0; _fracs = [0];
			// collect durations and compute sum
			for each (var t:Transition in _trans)
				_fracs.push(d += t.totalDuration);
			// normalize durations to create progress fractions
			for (var i:int=1; i<=_trans.length; ++i)
				_fracs[i] = (d==0 ? 0 : _fracs[i] / d);
			// set duration and scale
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
			// initialize each transition in proper sequence
			for each (var t:Transition in _trans) {
				t.doSetup(); t.step(1.0);
			}
		}
		
		/**
		 * Starts this sequence transition, starting the first sub-transition
		 * to be played.
		 */
		protected override function start():void
		{
			if (_reverse) {
				// init for reverse playback
				for (_idx=0; _idx<_trans.length; ++_idx) _trans[_idx].step(1);
				_idx -= 1;
			} else {
				// init for forward playback
				for (_idx=_trans.length; --_idx>=0;) _trans[_idx].step(0);
				_idx += 1;
			}
			if (_trans.length > 0)
				_trans[_idx].doStart(_reverse);
		}
		
		/**
		 * Steps this sequence transition, ensuring that any sub-transitions
		 * between the previous and current progress fraction are properly
		 * invoked.
		 * @param ef the current progress fraction.
		 */
		internal override function step(ef:Number):void
		{
			// find the right sub-transition
			var t:Transition, f0:Number, f1:Number, i:int, inc:int;
			f0 = _fracs[_idx]; f1 = _fracs[_idx+1]; inc = (ef<=f0 ? -1 : 1);
			
			for (i = _idx; i>=0 && i<_trans.length; i+=inc) {
				// get transition and progress fractions
				t = _trans[i]; f0 = _fracs[i]; f1 = _fracs[i+1];
				// hand-off to new transition
				if (i != _idx) t.doStart(_reverse);
				if ((inc<0 && ef >= f0) || (inc>0 && ef <= f1)) break;
				t.doStep(inc<0 ? 0 : 1);
				t.doEnd();
			}
			_idx = i; // set the transition index
			
			if (_idx >= 0 && _idx < _trans.length) {
				// run transition with mapped fraction
				t.doStep(Maths.invLinearInterp(ef, f0, f1));
			}
		}
		
		/**
		 * Ends this sequence transition, ending the last transition to be
		 * played in the sequence as necessary.
		 */
		protected override function end():void
		{
			if (_idx >= 0 && _idx < _trans.length) {
				_trans[_idx].doStep(_reverse ? 0 : 1);
				_trans[_idx].doEnd();
			}
		}
		
	} // end of class Sequence
}