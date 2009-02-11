package flare.query.methods
{
	import flare.query.Variable;
	
	/**
	 * Returns a new Variable expression for the given variable name.
	 * @param name the variable name to use
	 * @return the new Variable expression
	 */
	public function $(name:String):Variable
	{
		return new Variable(name);
	}
}