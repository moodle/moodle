package flare.physics
{
	/**
	 * Force simulating a spring force between two particles. This force
	 * iterates over each <code>Spring</code> instance in a simulation and
	 * computes the spring force between the attached particles. Spring forces
	 * are computed using Hooke's Law plus a damping term modeling frictional
	 * forces in the spring.
	 * 
	 * <p>The actual equation is of the form: <code>F = -k*(d - L) + a*d*(v1 - 
	 * v2)</code>, where k is the spring tension, d is the distance between
	 * particles, L is the rest length of the string, a is the damping
	 * co-efficient, and v1 and v2 are the velocities of the particles.</p>
	 */
	public class SpringForce implements IForce
	{		
		/**
		 * Applies this force to a simulation.
		 * @param sim the Simulation to apply the force to
		 */
		public function apply(sim:Simulation):void
		{
			var s:Spring, p1:Particle, p2:Particle;
			var dx:Number, dy:Number, dn:Number, dd:Number, k:Number, fx:Number, fy:Number;
			
			for (var i:uint=0; i<sim.springs.length; ++i) {
				s = Spring(sim.springs[i]);
				p1 = s.p1;
				p2 = s.p2;				
				dx = p1.x - p2.x;
				dy = p1.y - p2.y;
				dn = Math.sqrt(dx*dx + dy*dy);
				dd = dn<1 ? 1 : dn;
				
				k  = s.tension * (dn - s.restLength);
				k += s.damping * (dx*(p1.vx-p2.vx) + dy*(p1.vy-p2.vy)) / dd;
				k /= dd;
				
				// provide a random direction when needed
				if (dn==0) {
					dx = 0.01 * (0.5-Math.random());
					dy = 0.01 * (0.5-Math.random());
				}
				
				fx = -k * dx;
				fy = -k * dy;
				
				p1.fx += fx; p1.fy += fy;
				p2.fx -= fx; p2.fy -= fy;
			}
		}
		
	} // end of class SpringForce
}