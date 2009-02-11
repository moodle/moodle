package flare.query.methods
{
	import flare.query.If;
	
	/**
	 * Creates a new 'If' query operator
	 * @param test the if test expression. 
	 *  This value can be an expression or a literal value.
	 *  Literal values are parsed using the Expression.expr method.
	 * @param then the then case expression
	 *  This value can be an expression or a literal value.
	 *  Literal values are parsed using the Expression.expr method.
	 * @param els the else case expression
	 *  This value can be an expression or a literal value.
	 *  Literal values are parsed using the Expression.expr method.
	 * @return the new query operator
	 */
	public function iff(test:*, then:*, els:*):If
	{
		return new If(test, then, els);
	}
}