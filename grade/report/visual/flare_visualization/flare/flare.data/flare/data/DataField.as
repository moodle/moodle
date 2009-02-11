package flare.data
{
	/**
	 * Represents metadata for an individual data field.
	 */
	public class DataField
	{
		private var _id:String;
		private var _name:String;
		private var _format:String;
		private var _label:String;
		private var _type:int;
		private var _def:Object;
		
		/** A unique id for the data field, often the name. */
		public function get id():String { return _id; }
		/** The name of the data field. */
		public function get name():String { return _name; }
		/** A formatting string for printing values of this field.
		 *  @see flare.util.Stings#format
		 */
		public function get format():String { return _format; }
		/** A label describing this data field, useful for axis titles. */
		public function get label():String { return _label; }
		/** The data type of this field.
		 *  @see flare.data.DataUtil. */
		public function get type():int { return _type; }
		/** The default value for this data field. */
		public function get defaultValue():Object { return _def; }
		
		/**
		 * Creates a new DataField.
		 * @param name the name of the data field
		 * @param type the data type of this field
		 * @param def the default value of this field
		 * @param id a unique id for the field. If null, the name will be used
		 * @param format a formatting string for printing values of this field
		 * @param label a label describing this data field
		 */
		public function DataField(name:String, type:int, def:Object=null,
		           id:String=null, format:String=null, label:String=null)
		{
			_name = name;
			_type = type;
			_def = def;
			_id = (id==null ? name : id);
			_format = format;
			_label = label==null ? name : _label;
		}
		
	} // end of class DataField
}