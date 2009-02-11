package flare.query.methods
{
	import flare.query.Or;
	
	/**
	 * Creates a new 'Or' query operator
	 * @param rest a list of expressions to include in the or
	 * @return the new query operator
	 */
	public function or(...rest):Or
	{
		var o:Or = new Or();
		o.setChildren(rest);
		return o;
	}	
}