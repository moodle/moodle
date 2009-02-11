package flare.query.methods
{
	import flare.query.Average;
	
	/**
	 * Creates a new 'Average' aggregate query expression.
	 * @param expr the input expression
	 * @return the new query operator
	 */
	public function average(expr:*):Average
	{
		return new Average(expr);
	}
}