package flare.query.methods
{
	import flare.query.Func;
	
	/**
	 * Creates a new Func expression for a function in a query.
	 * @param name the name of the function. This should be a function
	 *  registered with the Func class.
	 * @param args a list of arguments to the function
	 * @return the new Func operator
	 */
	public function func(name:String, ...args):Func
	{
		var f:Func = new Func(name);
		f.setChildren(args);
		return f;
	}
}