package flare.physics
{
	/**
	 * Represents a Spring in a physics simulation. A spring connects two
	 * particles and is defined by the springs rest length, spring tension,
	 * and damping (friction) co-efficient.
	 */
	public class Spring
	{
		/** The first particle attached to the spring. */
		public var p1:Particle;
		/** The second particle attached to the spring. */
		public var p2:Particle;
		/** The rest length of the spring. */
		public var restLength:Number;
		/** The tension of the spring. */
		public var tension:Number;
		/** The damping (friction) co-efficient of the spring. */
		public var damping:Number;
		/** Flag indicating if this spring is enabled. */
		public var enabled:Boolean;
		/** Flag indicating that the spring is scheduled for removal. */
		public var die:Boolean;
		/** Tag property for storing an arbitrary value. */
		public var tag:uint;
		
		/**
		 * Creates a new Spring with given parameters.
		 * @param p1 the first particle attached to the spring
		 * @param p2 the second particle attached to the spring
		 * @param restLength the rest length of the spring
		 * @param tension the tension of the spring
		 * @param damping the damping (friction) co-efficient of the spring
		 */
		public function Spring(p1:Particle, p2:Particle, restLength:Number=10,
							   tension:Number=0.1, damping:Number=0.1)
		{
			init(p1, p2, restLength, tension, damping);
		}
		
		/**
		 * Initializes an existing spring instance.
		 * @param p1 the first particle attached to the spring
		 * @param p2 the second particle attached to the spring
		 * @param restLength the rest length of the spring
		 * @param tension the tension of the spring
		 * @param damping the damping (friction) co-efficient of the spring
		 */
		public function init(p1:Particle, p2:Particle, restLength:Number=10,
							 tension:Number=0.1, damping:Number=0.1):void
		{
			this.p1 = p1;
			this.p2 = p2;
			this.restLength = restLength;
			this.tension = tension;
			this.damping = damping;
		}
		
		/**
		 * "Kills" this spring, scheduling it for removal in the next
		 * simulation cycle.
		 */
		public function kill():void {
			this.die = true;
		}
		
	} // end of class Spring
}