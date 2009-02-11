package flare.query.methods
{
	import flare.query.Query;
	
	/**
	 * Create a new Query with the given filter expression.
	 * @param expr the filter expression
	 * @return the created query.
	 */
	public function where(expr:*):Query
	{
		return new Query().where(expr);
	}
}