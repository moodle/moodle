package flare.query.methods
{
	import flare.query.Variance;
	
	/**
	 * Creates a new 'Variance' aggregate query expression that computes
	 * the population standard deviation.
	 * @param expr the input expression
	 * @return the new query operator
	 */
	public function stddev(expr:*):Variance
	{
		return new Variance(expr, Variance.DEVIATION);
	}
}