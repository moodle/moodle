package flare.physics
{
	/**
	 * Force simulating a global gravitational pull on Particle instances.
	 */
	public class GravityForce implements IForce
	{
		private var _gx:Number;
		private var _gy:Number;
		
		/** The gravitational acceleration in the horizontal dimension. */
		public function get gravityX():Number { return _gx; }
		public function set gravityX(gx:Number):void { _gx = gx; }
		
		/** The gravitational acceleration in the vertical dimension. */
		public function get gravityY():Number { return _gy; }
		public function set gravityY(gy:Number):void { _gy = gy; }
		
		/**
		 * Creates a new gravity force with given acceleration values.
		 * @param gx the gravitational acceleration in the horizontal dimension
		 * @param gy the gravitational acceleration in the vertical dimension
		 */
		public function GravityForce(gx:Number=0, gy:Number=0) {
			_gx = gx;
			_gy = gy;
		}
		
		/**
		 * Applies this force to a simulation.
		 * @param sim the Simulation to apply the force to
		 */
		public function apply(sim:Simulation):void
		{
			if (_gx == 0 && _gy == 0) return;
			
			var p:Particle;
			for (var i:uint=0; i<sim.particles.length; ++i) {
				p = sim.particles[i];
				p.fx += _gx * p.mass;
				p.fy += _gy * p.mass;
			}
		}
		
	} // end of class GravityForce
}