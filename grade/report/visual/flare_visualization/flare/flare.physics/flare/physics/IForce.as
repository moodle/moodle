package flare.physics
{
	/**
	 * Interface representing a force within a physics simulation.
	 */
	public interface IForce
	{
		/**
		 * Applies this force to a simulation.
		 * @param sim the Simulation to apply the force to
		 */
		function apply(sim:Simulation):void;
		
	} // end of interface IForce
}