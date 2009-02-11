package flare.animate
{
	/**
	 * Interface for "schedulable" objects that can be run by
	 * the Scheduler class.
	 */
	public interface ISchedulable
	{
		/**
		 * Evaluate a scheduled call.
		 * @param time the current time in milliseconds
		 * @return true if this item should be removed from the scheduler,
		 * false if it should continue to be run.
		 */
		function evaluate(time:Number) : Boolean;
		
	} // end of interface ISchedulable
}