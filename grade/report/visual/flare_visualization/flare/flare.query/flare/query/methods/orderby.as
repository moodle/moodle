package flare.query.methods
{
	import flare.query.Query;
	
	/**
	 * Create a new Query with the given sort criteria.
	 * @param terms a list of sort criteria
	 * @return the created query.
	 */
	public function orderby(...terms):Query
	{
		return new Query(null, null, terms);
	}
}