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
	 * Converts data between GraphML markup and flare DataSet instances.
	 * <a href="http://graphml.graphdrawing.org/">GraphML</a> is a
	 * standardized XML format supporting graph structure and typed data
	 * schemas for both nodes and edges.
	 */
	public class GraphMLConverter implements IDataConverter
	{    
		// -- reader ----------------------------------------------------------
		
		/** @inheritDoc */
		public function read(input:IDataInput, schema:DataSchema=null):DataSet
		{
			return parse(XML(input.readUTFBytes(input.bytesAvailable)), schema);
		}
		
		/**
		 * Parses a GraphML XML object into a DataSet instance.
		 * @param graphml the XML object containing GraphML markup
		 * @param schema a DataSchema (typically null, as GraphML contains
		 *  schema information)
		 * @return the parsed DataSet instance
		 */
		public function parse(graphml:XML, schema:DataSchema=null):DataSet
		{
			var lookup:Object = {};
			var nodes:Array = [], n:Object;
			var edges:Array = [], e:Object;
			var id:String, sid:String, tid:String;
			var def:Object, type:int;
			var group:String, attrName:String, attrType:String;
			
			var nodeSchema:DataSchema = new DataSchema();
			var edgeSchema:DataSchema = new DataSchema();
			var schema:DataSchema;
			
			// set schema defaults
			nodeSchema.addField(new DataField(ID, DataUtil.STRING));
			edgeSchema.addField(new DataField(ID, DataUtil.STRING));
			edgeSchema.addField(new DataField(SOURCE, DataUtil.STRING));
			edgeSchema.addField(new DataField(TARGET, DataUtil.STRING));
			edgeSchema.addField(new DataField(DIRECTED, DataUtil.BOOLEAN,
									DIRECTED == graphml.graph.@edgedefault));
			
			// parse data schema
			for each (var key:XML in graphml..key) {
				id       = key.@[ID].toString();
				group    = key.@[FOR].toString();
				attrName = key.@[ATTRNAME].toString();
				type     = toType(key.@[ATTRTYPE].toString());
				def = key[DEFAULT].toString();
				def = def != null && def.length > 0
					? DataUtil.parseValue(def, type) : null;
				
				schema = (group==EDGE ? edgeSchema : nodeSchema);
				schema.addField(new DataField(attrName, type, def, id));
			}
			
			// parse nodes
			for each (var node:XML in graphml..node) {
				id = node.@[ID].toString();
				lookup[id] = (n = parseData(node, nodeSchema));
				nodes.push(n);
			}
			
			// parse edges
			for each (var edge:XML in graphml..edge) {
				id  = edge.@[ID].toString();
				sid = edge.@[SOURCE].toString();
				tid = edge.@[TARGET].toString();
				
				// error checking
				if (!lookup.hasOwnProperty(sid))
					error("Edge "+id+" references unknown node: "+sid);
				if (!lookup.hasOwnProperty(tid))
					error("Edge "+id+" references unknown node: "+tid);
								
				edges.push(e = parseData(edge, edgeSchema));
			}
			
			return new DataSet(
				new DataTable(nodes, nodeSchema),
				new DataTable(edges, edgeSchema)
			);
		}
		
		private function parseData(node:XML, schema:DataSchema):Object {
			var n:Object = {};
			var name:String, field:DataField, value:Object;
			
			// set default values
			for (var i:int=0; i<schema.numFields; ++i) {
				field = schema.getFieldAt(i);
				n[field.name] = field.defaultValue;
			}
			
			// get attribute values
			for each (var attr:XML in node.@*) {
				name = attr.name().toString();
				field = schema.getFieldByName(name);
				n[name] = DataUtil.parseValue(attr[0].toString(), field.type);
			}
			
			// get data values in XML
			for each (var data:XML in node.data) {
				field = schema.getFieldById(data.@[KEY].toString());
				name = field.name;
				n[name] = DataUtil.parseValue(data[0].toString(), field.type);
			}
			
			return n;
		}

		// -- writer ----------------------------------------------------------
		
		/** @inheritDoc */
		public function write(data:DataSet, output:IDataOutput=null):IDataOutput
		{			
			// init GraphML
			var graphml:XML = new XML(GRAPHML_HEADER);
			
			// add schema
			graphml = addSchema(graphml, data.nodes.schema, NODE, NODE_ATTR);
			graphml = addSchema(graphml, data.edges.schema, EDGE, EDGE_ATTR);
			
			// add graph data
			var graph:XML = new XML(<graph/>);
			var ed:Object = data.edges.schema.getFieldByName(DIRECTED).defaultValue;
			graph.@[EDGEDEF] = ed==DIRECTED ? DIRECTED : UNDIRECTED;
			addData(graph, data.nodes.data, data.nodes.schema, NODE, NODE_ATTR);
			addData(graph, data.edges.data, data.edges.schema, EDGE, EDGE_ATTR);
			graphml = graphml.appendChild(graph);
			
			if (output == null) output = new ByteArray();
			output.writeUTFBytes(graphml.toXMLString());
			return output;
		}
		
		private static function addSchema(xml:XML, schema:DataSchema,
			group:String, attrs:Object):XML
		{
			var field:DataField;
			
			for (var i:int=0; i<schema.numFields; ++i) {
				field = schema.getFieldAt(i);
				if (attrs.hasOwnProperty(field.name)) continue;
				
				var key:XML = new XML(<key/>);
				key.@[ID] = field.id;
				key.@[FOR] = group;
				key.@[ATTRNAME] = field.name;
				key.@[ATTRTYPE] = fromType(field.type);
			
				if (field.defaultValue != null) {
					var def:XML = new XML(<default/>);
					def.appendChild(toString(field.defaultValue, field.type));
					key.appendChild(def);
				}
				
				xml = xml.appendChild(key);
			}
			return xml;
		}
		
		private static function addData(xml:XML, tuples:Array,
			schema:DataSchema, tag:String, attrs:Object):void
		{
			for each (var tuple:Object in tuples) {
				var x:XML = new XML("<"+tag+"/>");
				
				for (var name:String in tuple) {
					var field:DataField = schema.getFieldByName(name);
					if (tuple[name] == field.defaultValue) continue;
					if (attrs.hasOwnProperty(name)) {
						// add as attribute
						x.@[name] = toString(tuple[name], field.type);
					} else {
						// add as data child tag
						var data:XML = new XML(<data/>);
						data.@[KEY] = field.id;
						data.appendChild(toString(tuple[name], field.type));
						x.appendChild(data);
					}
				}
				
				xml.appendChild(x);
			}
		}	
		
		// -- static helpers --------------------------------------------------
		
		private static function toString(o:Object, type:int):String
		{
			return o.toString(); // TODO: formatting control?
		}
		
		private static function toType(type:String):int {
			switch (type) {
				case INT:
				case INTEGER:
					return DataUtil.INT;
				case LONG:
				case FLOAT:
				case DOUBLE:
				case REAL:
					return DataUtil.NUMBER;
				case BOOLEAN:
					return DataUtil.BOOLEAN;
				case DATE:
					return DataUtil.DATE;
				case STRING:
				default:
					return DataUtil.STRING;
			}
		}
		
		private static function fromType(type:int):String {
			switch (type) {
				case DataUtil.INT: 		return INT;
				case DataUtil.BOOLEAN: 	return BOOLEAN;
				case DataUtil.NUMBER:	return DOUBLE;
				case DataUtil.DATE:		return DATE;
				case DataUtil.STRING:
				default:				return STRING;
			}
		}
		
		private static function error(msg:String):void {
			throw new Error(msg);
		}
		
		// -- constants -------------------------------------------------------
		
		private static const NODE_ATTR:Object = {
			"id":1
		}
		private static const EDGE_ATTR:Object = {
			"id":1, "directed":1, "source":1, "target":1
		};
		
		private static const GRAPHML_HEADER:String = "<graphml/>";
		//	"<graphml xmlns=\"http://graphml.graphdrawing.org/xmlns\"" 
        //    +" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\""
        //    +" xsi:schemaLocation=\"http://graphml.graphdrawing.org/xmlns"
        //    +" http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd\">"
        //    +"</graphml>";
        
        private static const GRAPHML:String    = "graphml";
		private static const ID:String         = "id";
	    private static const GRAPH:String      = "graph";
	    private static const EDGEDEF:String    = "edgedefault";
	    private static const DIRECTED:String   = "directed";
	    private static const UNDIRECTED:String = "undirected";
	    
	    private static const KEY:String        = "key";
	    private static const FOR:String        = "for";
	    private static const ALL:String        = "all";
	    private static const ATTRNAME:String   = "attr.name";
	    private static const ATTRTYPE:String   = "attr.type";
	    private static const DEFAULT:String    = "default";
	    
	    private static const NODE:String   = "node";
	    private static const EDGE:String   = "edge";
	    private static const SOURCE:String = "source";
	    private static const TARGET:String = "target";
	    private static const DATA:String   = "data";
	    private static const TYPE:String   = "type";
	    
	    private static const INT:String = "int";
	    private static const INTEGER:String = "integer";
	    private static const LONG:String = "long";
	    private static const FLOAT:String = "float";
	    private static const DOUBLE:String = "double";
	    private static const REAL:String = "real";
	    private static const BOOLEAN:String = "boolean";
	    private static const STRING:String = "string";
	    private static const DATE:String = "date";
		
	} // end of class GraphMLConverter
}