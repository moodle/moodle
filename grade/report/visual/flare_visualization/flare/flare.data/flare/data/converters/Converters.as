package flare.data.converters
{	
	/**
	 * Factory class for looking up the appropriate IDataConverter for a
	 * given data format.
	 */
	public class Converters
	{
		private static var _lookup:Object = {
			"json":new JSONConverter(),
			"tab":new DelimitedTextConverter("\t"),
			"graphml":new GraphMLConverter()
		};
		
		/**
		 * Returns a data converter for the input format type.
		 * @param type a format string (e.g., "tab" or "json").
		 * @return a data converter for the provided format, or null if no
		 *  matching converter was found.
		 */
		public static function lookup(type:String):IDataConverter
		{
			return _lookup[type.toLowerCase()];
		}
		
	} // end of class Converters
}