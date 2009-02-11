package flare.query.methods
{
	import flare.query.And;
	
	/**
	 * Creates a new 'And' query operator
	 * @param rest a list of expressions to include in the and
	 * @return the new query operator
	 */
	public function and(...rest):And
	{
		var a:And = new And();
		a.setChildren(rest);
		return a;
	}	
}