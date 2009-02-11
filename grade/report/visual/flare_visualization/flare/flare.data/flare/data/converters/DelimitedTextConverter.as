package flare.data.converters
{
	import flare.data.DataField;
	import flare.data.DataSchema;
	import flare.data.DataSet;
	import flare.data.DataTable;
	import flare.data.DataUtil;
	
	import flash.utils.ByteArray;
	import flash.utils.IDataInput;
	import flash.utils.IDataOutput;

	/**
	 * Converts data between delimited text (e.g., tab delimited) and
	 * flare DataSet instances.
	 */
	public class DelimitedTextConverter implements IDataConverter
	{
		private var _delim:String;
		
		public function get delimiter():String { return _delim; }
		public function set delimiter(d:String):void { _delim = d; }
		
		/**
		 * Creates a new DelimitedTextConverter.
		 * @param delim the delimiter string separating values (tab by default)
		 */
		public function DelimitedTextConverter(delim:String="\t")
		{
			_delim = delim;
		}
		
		/**
		 * @inheritDoc
		 */
		public function read(input:IDataInput, schema:DataSchema=null):DataSet
		{
			return parse(input.readUTFBytes(input.bytesAvailable), schema);
		}
		
		/**
		 * Converts data from a tab-delimited string into ActionScript objects.
		 * @param input the loaded input data
		 * @param schema a data schema describing the structure of the data.
		 *  Schemas are optional in many but not all cases.
		 * @param data an array in which to write the converted data objects.
		 *  If this value is null, a new array will be created.
		 * @return an array of converted data objects. If the <code>data</code>
		 *  argument is non-null, it is returned.
		 */
		public function parse(text:String, schema:DataSchema=null):DataSet
		{
			var tuples:Array = [];
			var lines:Array = text.split(/\r\n|\r|\n/);
			
			if (schema == null) {
				schema = inferSchema(lines);
			}
			
			var i:int = schema.hasHeader ? 1 : 0;
			for (; i<lines.length; ++i) {
				var line:String = lines[i];
				if (line.length == 0) break;
				var tok:Array = line.split(_delim);
				var tuple:Object = {};
				for (var j:int=0; j<schema.numFields; ++j) {
					var field:DataField = schema.getFieldAt(j);
					tuple[field.name] = DataUtil.parseValue(tok[j], field.type);
				}
				tuples.push(tuple);
			}
			return new DataSet(new DataTable(tuples, schema));
		}
		
		/**
		 * @inheritDoc
		 */
		public function write(data:DataSet, output:IDataOutput=null):IDataOutput
		{
			if (output==null) output = new ByteArray();
			var tuples:Array = data.nodes.data;
			var schema:DataSchema = data.nodes.schema;
			
			for each (var tuple:Object in tuples) {
				var i:int = 0, s:String;
				if (schema == null) {
					for (var name:String in tuple) {
						if (i>0) output.writeUTFBytes(_delim);
						output.writeUTFBytes(String(tuple[name])); // TODO: proper string formatting
						++i;
					}
				} else {
					for (;i<schema.numFields; ++i) {
						var f:DataField = schema.getFieldAt(i);
						if (i>0) output.writeUTFBytes(_delim);
						output.writeUTFBytes(String(tuple[f.name])); // TODO proper string formatting
					}
				}
				output.writeUTFBytes("\n");
			}
			return output;
		}
		
		/**
		 * Infers the data schema by checking values of the input data.
		 * @param lines an array of lines of input text
		 * @return the inferred schema
		 */
		protected function inferSchema(lines:Array):DataSchema
		{
			var header:Array = lines[0].split(_delim);
			var types:Array = new Array(header.length);
			
			// initialize data types
			var tok:Array = lines[1].split(_delim);
			for (var col:int=0; col<header.length; ++col) {
				types[col] = DataUtil.type(tok[col]);
			}
			
			// now process data to infer types
			for (var i:int = 2; i<lines.length; ++i) {
				tok = lines[i].split(_delim);
				for (col=0; col<tok.length; ++col) {
					if (types[col] == -1) continue;
					var type:int = DataUtil.type(tok[col]);
					if (types[col] != type) {
						types[col] = -1;
					}
				}
			}
			
			// finally, we create the schema
			var schema:DataSchema = new DataSchema();
			schema.hasHeader = true;
			for (col=0; col<header.length; ++col) {
				schema.addField(new DataField(header[col],
					types[col]==-1 ? DataUtil.STRING : types[col]));
			}
			return schema;
		}
		
	} // end of class DelimitedTextConverter
}