package flare.query.methods
{
	import flare.query.Literal;
	
	/**
	 * Returns a new Literal expression for the input object.
	 * @param the input object
	 * @return the new Literal expression
	 */
	public function _(a:*):Literal
	{
		return new Literal(a);
	}
}