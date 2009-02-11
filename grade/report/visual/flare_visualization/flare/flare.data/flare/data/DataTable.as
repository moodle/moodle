package flare.data
{
	/**
	 * A table of data that maintains a collection of data objects, each
	 * representing a row of data, and an optional data schema describing
	 * the data variables.
	 */
	public class DataTable
	{
		/**
		 * Creates a new data table instance.
		 * @param data an array of tuples, each tuple is a row of data
		 * @param schema an optional DataSchema describing the data columns
		 */
		public function DataTable(data:Array, schema:DataSchema=null) {
			this.data = data;
			this.schema = schema;
		}
		
		/** A DataSchema describing the data columns of the table. */
		public var schema:DataSchema;
		
		/** An array of data objects, each representing a row of data. */
		public var data:Array;
		
	} // end of class DataTable
}