package flare.data.converters
{
	import com.adobe.serialization.json.JSON;
	
	import flare.data.DataField;
	import flare.data.DataSchema;
	import flare.data.DataSet;
	import flare.data.DataTable;
	import flare.data.DataUtil;
	import flare.util.Property;
	
	import flash.utils.ByteArray;
	import flash.utils.IDataInput;
	import flash.utils.IDataOutput;

	/**
	 * Converts data between JSON (JavaScript Object Notation) strings and
	 * flare DataSet instances.
	 */
	public class JSONConverter implements IDataConverter
	{
		/**
		 * @inheritDoc
		 */
		public function read(input:IDataInput, schema:DataSchema=null):DataSet
		{
			var data:Array;
			return new DataSet(new DataTable(
				data = parse(input.readUTFBytes(input.bytesAvailable), schema),
				schema ? schema : DataUtil.inferSchema(data)
			));
		}
		
		/**
		 * Converts data from a JSON string into ActionScript objects.
		 * @param input the loaded input data
		 * @param schema a data schema describing the structure of the data.
		 *  Schemas are optional in many but not all cases.
		 * @param data an array in which to write the converted data objects.
		 *  If this value is null, a new array will be created.
		 * @return an array of converted data objects. If the <code>data</code>
		 *  argument is non-null, it is returned.
		 */
		public function parse(text:String, schema:DataSchema):Array
		{
			var json:Object = JSON.decode(text) as Object;
			var list:Array = json as Array;
			
			if (schema != null) {
				if (schema.dataRoot) {
					// if nested, extract data array
					list = Property.$(schema.dataRoot).getValue(json);
				}
				// convert value types according to schema
				for each (var t:Object in list) {
					for each (var f:DataField in schema.fields) {
						t[f.name] = DataUtil.parseValue(t[f.name], f.type);
					}
				}
			}
			return list;
		}
		
		/**
		 * @inheritDoc
		 */
		public function write(data:DataSet, output:IDataOutput=null):IDataOutput
		{
			var tuples:Array = data.nodes.data;
			if (output==null) output = new ByteArray();
			output.writeUTFBytes(JSON.encode(tuples));
			return output;
		}
		
	} // end of class JSONConverter
}