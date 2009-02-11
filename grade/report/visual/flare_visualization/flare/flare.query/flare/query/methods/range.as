package flare.query.methods
{
	import flare.query.Range;
	
	/**
	 * Creates a new 'Range' query operator
	 * @param min the minimum range value.
	 *  This value can be an expression or a literal value.
	 *  Literal values are parsed using the Expression.expr method.
	 * @param max the maximum range value.
	 *  This value can be an expression or a literal value.
	 *  Literal values are parsed using the Expression.expr method.
	 * @param val the value to test for range inclusion.
	 *  This value can be an expression or a literal value.
	 *  Literal values are parsed using the Expression.expr method.
	 * @return the new query operator
	 */
	public function range(min:*, max:*, val:*):Range
	{
		return new Range(min, max, val);
	}
}
