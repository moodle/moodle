package flare.query.methods
{
	import flare.query.Distinct;
	
	/**
	 * Creates a new 'Distinct' count aggregate query expression.
	 * @param expr the input expression
	 * @return the new query operator
	 */
	public function distinct(expr:*):Distinct
	{
		return new Distinct(expr);
	}
}