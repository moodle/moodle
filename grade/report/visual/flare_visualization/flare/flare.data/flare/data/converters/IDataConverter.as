package flare.data.converters
{
	import flare.data.DataSchema;
	import flare.data.DataSet;
	
	import flash.utils.IDataInput;
	import flash.utils.IDataOutput;
	
	/**
	 * Interface for data converters that map between an external data file
	 * format and ActionScript objects (e.g., Arrays and Objects).
	 */
	public interface IDataConverter
	{
		/**
		 * Converts data from an external format into ActionScript objects.
		 * @param input the loaded input data
		 * @param schema a data schema describing the structure of the data.
		 *  Schemas are optional in many but not all cases.
		 * @return a DataSet instance containing converted data objects.
		 */
		function read(input:IDataInput, schema:DataSchema=null):DataSet;
		
		/**
		 * Converts data from ActionScript objects into an external format.
		 * @param data the data set to write.
		 * @param output an object to which to write the output. If this value
		 *  is null, a new <code>ByteArray</code> will be created.
		 * @return the converted data. If the <code>output</code> parameter is
		 *  non-null, it is returned. Otherwise the return value will be a
		 *  newly created <code>ByteArray</code>
		 */
		function write(data:DataSet, output:IDataOutput=null):IDataOutput;
		
	} // end of interface IDataConverter
}