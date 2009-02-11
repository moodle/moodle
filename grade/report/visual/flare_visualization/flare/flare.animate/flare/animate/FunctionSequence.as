package flare.animate
{
	import flare.util.Arrays;
	import flare.util.Maths;
	
	/**
	 * Transition that runs sub-transitions in sequential order while also
	 * invoking a function before each sub-transition is run. Each function
	 * must take a Transition instance (often a Transitioner) as input.
	 * Function sequences can only be played forwards; any attempt to play
	 * one in reverse will result in an error.
	 * 
	 * <p>Function sequences are useful for ensuring a particular function
	 * is run before a sub-transition begins. For example, the function may
	 * populate the values of a Transitioner on the fly, or may be used to
	 * control other variables or side-effects that affect the subsequent
	 * sub-transition(s).</p>
	 */
	public class FunctionSequence extends Sequence
	{
		/** @private */
		protected var _funcs:/*Function*/Array = [];
		/** @private */
		protected var _offsetStart:Boolean = true;
		
		/** Flag indicating if extra time should be added to the transition to
		 *  offset the running time of invoked functions. True by default. */
		public function get offsetStartTime():Boolean { return _offsetStart; }
		public function set offsetStartTime(b:Boolean):void { _offsetStart = b; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new FunctionSequence transition.
		 */
		public function FunctionSequence()
		{
			super();
		}
		
		/**
		 * Adds a function call and corresponding transition to this sequence.
		 * As the sequence plays, functions will be called at the beginning of
		 * their subsequence, with the provided transition passed as the
		 * function's sole argument.
		 * @param f a function to call at the beginning of a sub-sequence
		 * @param t the transition to run after the function call. This
		 *  transition will be passed in as an input to the function.
		 */
		public function addFunction(f:Function, t:Transition):void
		{
			super.add(t);
			_funcs.push(f);
		}
		
		/**
		 * Removes a sub-transition function from this sequence. The
		 * corresponding transition instance will also be removed.
		 * @param t the transition to remove
		 * @return true if the transition was found and removed, false
		 *  otherwise
		 */ 
		public function removeFunction(f:Function):Boolean
		{
			if (running) throw new Error("Transition is running!");
			var idx:int = Arrays.remove(_funcs, f);
			var rem:Boolean = idx > 0;
			if (rem) {
				_trans.splice(idx, 1);
				_dirty = true;
			}
			return rem;
		}
		
		/** @inheritDoc */
		public override function add(t:Transition):void
		{
			super.add(t);
			_funcs.push(null);
		}
		
		/**
		 * Removes a sub-transition from this sequence. Any corresponding
		 * function will also be removed.
		 * @param t the transition to remove
		 * @return true if the transition was found and removed, false
		 *  otherwise
		 */ 
		public override function remove(t:Transition):Boolean
		{
			if (running) throw new Error("Transition is running!");
			var idx:int = Arrays.remove(_trans, t);
			var rem:Boolean = idx > 0;
			if (rem) {
				_funcs.splice(idx, 1);
				_dirty = true;
			}
			return rem;
		}
		
		/** @inheritDoc */
		public override function dispose():void {
			super.dispose();
			Arrays.clear(_funcs);
		}
	
		/**
		 * Plays this function sequence. Function sequences can not be played
		 * in reverse. 
		 * @param reverse If true, an error will be thrown and the sequence
		 *  will not play.
		 */
		public override function play(reverse:Boolean=false):void {
			if (reverse) throw new Error(
				"Function sequences can't be played in reverse.");
			super.play(false);
		}
		
		private function invoke(idx:int, t:Transition):void {
			var f:Function = _funcs[idx];
			if (f != null) {
				if (_offsetStart) {
					var d:Number = new Date().time;
					f(t);
					d = new Date().time - d;
					_start += d;
				} else {
					f(t);
				}
			}
		}
		
		// --------------------------------------------------------------------
		
		/**
		 * Sets up each sub-transition.
		 */
		protected override function setup():void
		{
		}
		
		/**
		 * Starts this sequence transition, starting the first sub-transition
		 * to be played.
		 */
		protected override function start():void
		{
			if (_trans.length > 0) {
				var t:Transition = _trans[_idx];
				invoke(_idx, t); t.doSetup(); t.doStart(false);
			}
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
				if (i != _idx) {
					invoke(i, t); t.doSetup(); t.doStart(false);
				}
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
	} // end of class FunctionSequence
}