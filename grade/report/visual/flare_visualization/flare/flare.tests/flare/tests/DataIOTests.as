package flare.tests
{
	import flare.data.DataSet;
	import flare.data.converters.GraphMLConverter;
	import flare.data.converters.JSONConverter;
	import flare.vis.data.Data;
	
	import flash.utils.ByteArray;
	
	import unitest.TestCase;
	
	public class DataIOTests extends TestCase
	{
		public function DataIOTests()
		{
			addTest("testJSONConverter");
			addTest("testGraphMLConverter");
		}
		
		// --------------------------------------------------------------------
		
		public function testJSONConverter():void
		{
			var jc:JSONConverter = new JSONConverter();
			var data:Array = jc.parse(JSON, null);
			
			assertEquals(DATA.length, data.length);
			for (var i:int=0; i<DATA.length; ++i) {
				for (var name:String in DATA[i]) {
					assertEquals(DATA[i][name], data[i][name]);
				}
			}
		}
		
		private static const DATA:Array = [
			{id:1, cat:"a", val:10, b:true},
			{id:2, cat:"a", val:20, b:true},
			{id:3, cat:"b", val:30, b:true},
			{id:4, cat:"b", val:40, b:false},
			{id:5, cat:"c", val:50, b:false},
			{id:6, cat:"c", val:60, b:false}
		];
		
		private static const JSON:String =
			"[" +
				"{\"id\":1,\"cat\":\"a\",\"val\":10,\"b\":true}," +
				"{\"id\":2,\"cat\":\"a\",\"val\":20,\"b\":true}," +
				"{\"id\":3,\"cat\":\"b\",\"val\":30,\"b\":true}," +
				"{\"id\":4,\"cat\":\"b\",\"val\":40,\"b\":false}," +
				"{\"id\":5,\"cat\":\"c\",\"val\":50,\"b\":false}," +
				"{\"id\":6,\"cat\":\"c\",\"val\":60,\"b\":false}" +
			"]";
		
		// --------------------------------------------------------------------
		
		public function testGraphMLConverter():void
		{
			var gmlc:GraphMLConverter = new GraphMLConverter();
			var data:DataSet = gmlc.parse(GRAPHML);
			var i:int;
			
			// test nodes
			var nids:Array = ["n0", "n1", "n2", "n3", "n4", "n5"];
			var colors:Array = ["green", "yellow", "blue", "red", "yellow", "turquoise"];
			
			assertEquals(nids.length, data.nodes.data.length);
			for (i=0; i<nids.length; ++i) {
				assertEquals(nids[i], data.nodes.data[i].id);
				assertEquals(colors[i], data.nodes.data[i].color);
			}
			
			// test edges
			var eids:Array = ["e0", "e1", "e2", "e3", "e4", "e5", "e6"];
			var srcs:Array = ["n0", "n0", "n1", "n3", "n2", "n3", "n5"];
			var trgs:Array = ["n2", "n1", "n3", "n2", "n4", "n5", "n4"];
			var wgts:Array = [1.0, 1.0, 2.0, null, null, null, 1.1];
			
			assertEquals(7, data.edges.data.length);
			for (i=0; i<eids.length; ++i) {
				assertEquals(eids[i], data.edges.data[i].id);
				assertEquals(srcs[i], data.edges.data[i].source);
				assertEquals(trgs[i], data.edges.data[i].target);
				assertEquals(wgts[i], data.edges.data[i].weight);
				assertEquals(false, data.edges.data[i].directed);
			}
			
			// read in graphml, write graph data back to xml
			var out:ByteArray = gmlc.write(data) as ByteArray;
			out.position = 0;
			var s1:String = out.readUTFBytes(out.length);
			
			// now do a round-trip comparison test
			out = gmlc.write(gmlc.parse(XML(s1))) as ByteArray;
			out.position = 0;
			var s2:String = out.readUTFBytes(out.length);
			
			assertEquals(s1, s2);
			
			// finally, do a test of data construction
			var d:Data = Data.fromDataSet(data);
			assertEquals(data.nodes.data.length, d.nodes.size);
			assertEquals(data.edges.data.length, d.edges.size);
		}
		
		private static const GRAPHML:XML =
			<graphml>
			  <key id="d0" for="node" attr.name="color" attr.type="string">
			  	<default>yellow</default>
			  </key>
			  <key id="d1" for="edge" attr.name="weight" attr.type="double"/>
			  <graph id="G" edgedefault="undirected">
			    <node id="n0">
			      <data key="d0">green</data>
			    </node>
			    <node id="n1"/>
			    <node id="n2">
			      <data key="d0">blue</data>
			    </node>
			    <node id="n3">
			      <data key="d0">red</data>
			    </node>
			    <node id="n4"/>
			    <node id="n5">
			      <data key="d0">turquoise</data>
			    </node>
			    <edge id="e0" source="n0" target="n2">
			      <data key="d1">1.0</data>
			    </edge>
			    <edge id="e1" source="n0" target="n1">
			      <data key="d1">1.0</data>
			    </edge>
			    <edge id="e2" source="n1" target="n3">
			      <data key="d1">2.0</data>
			    </edge>
			    <edge id="e3" source="n3" target="n2"/>
			    <edge id="e4" source="n2" target="n4"/>
			    <edge id="e5" source="n3" target="n5"/>
			    <edge id="e6" source="n5" target="n4">
			      <data key="d1">1.1</data>
			    </edge>
			  </graph>
			</graphml>;

		
	} // end of class DataIOTests
}