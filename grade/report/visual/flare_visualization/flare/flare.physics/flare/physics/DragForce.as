package flare.physics
{
	/**
	 * Force simulating frictional drag forces (e.g., air resistance). For
	 * each particle, this force applies a drag based on the particles
	 * velocity (<code>F = a * v</code>, where a is a drag co-efficient and
	 * v is the velocity of the particle).
	 */
	public class DragForce implements IForce
	{
		private var _dc:Number;
		
		/** The drag co-efficient. */
		public function get drag():Number { return _dc; }
		public function set drag(dc:Number):void { _dc = dc; }
		
		/**
		 * Creates a new DragForce with given drag co-efficient.
		 * @param dc the drag co-efficient.
		 */
		public function DragForce(dc:Number=0.1) {
			_dc = dc;
		}
		
		/**
		 * Applies this force to a simulation.
		 * @param sim the Simulation to apply the force to
		 */
		public function apply(sim:Simulation):void
		{
			if (_dc == 0) return;
			for (var i:uint = 0; i<sim.particles.length; ++i) {
				var p:Particle = sim.particles[i];
				p.fx -= _dc * p.vx;
				p.fy -= _dc * p.vy;
			}
		}
		
	} // end of class DragForce
}