package flare.query.methods
{
	import flare.query.Count;
	
	/**
	 * Creates a new 'Count' aggregate query expression.
	 * @param expr the input expression
	 * @return the new query operator
	 */
	public function count(expr:*):Count
	{
		return new Count(expr);
	}
}