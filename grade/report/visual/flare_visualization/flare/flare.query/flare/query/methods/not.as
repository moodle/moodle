package flare.query.methods
{
	import flare.query.Not;

	/**
	 * Creates a new 'Not' query operator
	 * @param x the expression to negate
	 *  This value can be an expression or a literal value.
	 *  Literal values are parsed using the Expression.expr method.
	 * @return the new query operator
	 */
	public function not(x:*):Not
	{
		return new Not(x);
	}	
}