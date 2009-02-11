package flare.query.methods
{
	import flare.query.Maximum;
	
	/**
	 * Creates a new 'Maximum' aggregate query expression.
	 * @param expr the input expression
	 * @return the new query operator
	 */
	public function max(expr:*):Maximum
	{
		return new Maximum(expr);
	}
}