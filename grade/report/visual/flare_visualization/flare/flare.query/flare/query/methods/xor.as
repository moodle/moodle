package flare.query.methods
{
	import flare.query.Xor;
	
	/**
	 * Creates a new 'Xor' (exclusive or) query operator
	 * @param rest a list of expressions to include in the exclusive or
	 * @return the new query operator
	 */
	public function xor(...rest):Xor
	{
		var x:Xor = new Xor();
		x.setChildren(rest);
		return x;
	}	
}