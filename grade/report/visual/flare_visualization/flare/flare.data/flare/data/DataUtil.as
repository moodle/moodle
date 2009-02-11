package flare.data
{
	/**
	 * Utility class for parsing and representing data field values.
	 */
	public class DataUtil
	{
		/** Constant indicating a numeric data type. */
		public static const NUMBER:int = 0;
		/** Constant indicating an integer data type. */
		public static const INT:int    = 1;
		/** Constant indicating a Date data type. */
		public static const DATE:int   = 2;
		/** Constant indicating a String data type. */
		public static const STRING:int = 3;
		/** Constant indicating an arbitrary Object data type. */
		public static const OBJECT:int = 4;
		/** Constant indicating a boolean data type. */
		public static const BOOLEAN:int = 5;
		
		/**
		 * Parse an input value given its data type.
		 * @param val the value to parse
		 * @param type the data type to parse as
		 * @return the parsed data value
		 */
		public static function parseValue(val:Object, type:int):Object
		{
			switch (type) {
				case NUMBER:
					return Number(val);
				case INT:	
					return int(val);
				case BOOLEAN:
					return Boolean(val);
				case DATE:
					var t:Number = val is Number ? Number(val) 
												 : Date.parse(String(val));
					return isNaN(t) ? null : new Date(t);
				case STRING:
					return String(val);
				default:		return val;
			}
		}
		
		/**
		 * Returns the data type for the input string value. This method
		 * attempts to parse the value as a number of different data types.
		 * If successful, the matching data type is returned. If no parse
		 * succeeds, this method returns the <code>STRING</code> constant.
		 * @param s the string to parse
		 * @return the inferred data type of the string contents
		 */
		public static function type(s:String):int
		{
			if (!isNaN(Number(s))) return NUMBER;
			if (!isNaN(Date.parse(s))) return DATE;
			return STRING;
		}
		
		/**
		 * Infers the data schema by checking values of the input data.
		 * @param lines an array of lines of input text
		 * @return the inferred schema
		 */
		public static function inferSchema(tuples:Array):DataSchema
		{
			if (tuples==null || tuples.length==0) return null;
			
			var header:Array = [];
			for (var name:String in tuples[0]) {
				header.push(name);
			}
			var types:Array = new Array(header.length);
			
			// initialize data types
			for (var col:int=0; col<header.length; ++col) {
				types[col] = DataUtil.type(tuples[0][header[col]]);
			}
			
			// now process data to infer types
			for (var i:int = 2; i<tuples.length; ++i) {
				var tuple:Object = tuples[i];
				for (col=0; col<header.length; ++col) {
					name = header[col];
					var value:Object = tuple[name];
					if (types[col] == -1 || value==null) continue;
					
					var type:int = 
						value is Boolean ? BOOLEAN :
						value is Date ? DATE :
						value is int ? INT :
						value is Number ? NUMBER :
						value is String ? STRING : OBJECT;

					if (types[col] != type) {
						types[col] = -1;
					}
				}
			}
			
			// finally, we create the schema
			var schema:DataSchema = new DataSchema();
			for (col=0; col<header.length; ++col) {
				schema.addField(new DataField(header[col],
					types[col]==-1 ? DataUtil.STRING : types[col]));
			}
			return schema;
		}
		
	} // end of class DataUtil
}