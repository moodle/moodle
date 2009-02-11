package flare.tests
{
	import flare.vis.data.Data;
	import flare.vis.data.EdgeSprite;
	import flare.vis.data.NodeSprite;
	import flare.vis.util.Filters;
	import unitest.TestCase;
	
	public class DataTests extends TestCase
	{
		public function DataTests()
		{
			addTest("createNodes");
			addTest("createEdges");
			addTest("containsNodes");
			addTest("containsEdges");
			addTest("removeNodes");
			addTest("removeEdges");
			addTest("createWithDefaults");
			addTest("visit");
		}
		
		// --------------------------------------------------------------------
		
		private static const N:int = 100;
		private var data:Data;
		
		public function createNodes():void
		{
			data = new Data();
			for (var i:uint=0; i<N; ++i) {
				data.addNode({id:i});
				assertEquals(data.nodes.size, i+1);
			}
		}
		
		public function createEdges():void
		{
			data = new Data();
			for (var i:uint=0; i<N; ++i) {
				data.addNode({id:i});
				assertEquals(data.nodes.size, i+1);
					
				if (i > 0) {
					var s:NodeSprite = data.nodes[i-1];
					var t:NodeSprite = data.nodes[i];
					var e:EdgeSprite = data.addEdgeFor(s, t);
					assertEquals(data.edges.size, i);
					assertEquals(e.source, s);
					assertEquals(e.target, t);
				}
			}
		}
		
		public function createWithDefaults():void
		{
			var _x:Number = 10, _y:Number = 20, _lw:Number = 3;
			var _na:Number = 1, _ea:Number = 0, _t:String = "hello";
			data = new Data();
			data.nodes.setDefaults({x:_x, y:_y});
			data.edges.setDefaults({lineWidth: _lw});
			data.nodes.setDefault("alpha", _na);
			data.nodes.setDefault("props.temp", _t);
			data.edges.setDefault("alpha", _ea);

			
			var prev:NodeSprite = null, curr:NodeSprite;
			for (var i:uint=0; i<10; ++i) {
				curr = data.addNode({id:i});
				if (prev != null) data.addEdgeFor(prev, curr);
				prev = curr;
			}
			
			for (i=0; i<data.nodes.size; ++i) {
				curr = data.nodes[i];
				assertEquals(_x, curr.x);
				assertEquals(_y, curr.y);
				assertEquals(_t, curr.props.temp);
				assertEquals(_na, curr.alpha);
			}
			for (i=0; i<data.edges.size; ++i) {
				var e:EdgeSprite = data.edges[i];
				assertEquals(_lw, e.lineWidth);
				assertEquals(_ea, e.alpha);
			}
			
			// test default removal
			data.nodes.removeDefault("props.temp");
			data.edges.removeDefault("alpha");
			curr = data.addNode();
			e = data.addEdgeFor(prev, curr);
			assertNotEquals(_t, curr.props.temp);
			assertNotEquals(_ea, e.alpha);
		}
		
		public function containsNodes():void
		{
			createNodes();
			for (var i:uint=0; i<N; ++i) {
				assertTrue(data.contains(data.nodes[i]));
				assertTrue(data.nodes.contains(data.nodes[i]));
			}
			data.nodes.visit(function(n:NodeSprite):void {
				assertTrue(data.contains(n));
				assertTrue(data.nodes.contains(n));
			});
		}
		
		public function containsEdges():void
		{
			createEdges();
			for (var i:uint=0; i<data.edges.size; ++i) {
				assertTrue(data.contains(data.edges[i]));
				assertTrue(data.edges.contains(data.edges[i]));
			}
			data.edges.visit(function(e:EdgeSprite):void {
				assertTrue(data.contains(e));
				assertTrue(data.edges.contains(e));
			});
		}
		
		public function removeNodes():void
		{
			var n:NodeSprite;
			
			createNodes();
			for (var i:uint=N; --i>=0;) {
				n = data.nodes[i];
				data.removeNode(n);
				assertEquals(i, data.nodes.size);
				assertFalse(data.nodes.contains(n));
			}
			
			createNodes(); i=N;
			data.nodes.visit(function(n:NodeSprite):void {
				data.removeNode(n); --i;
				assertEquals(data.nodes.size, i);
			});
			assertEquals(0, i);
			
			createEdges(); i=N;
			data.nodes.visit(function(n:NodeSprite):void {
				data.removeNode(n); --i;
				assertEquals(data.nodes.size, i);
			});
			assertEquals(0, i);
			assertEquals(0, data.edges.size);
		}
		
		public function removeEdges():void
		{
			var e:EdgeSprite;
						
			createEdges();
			for (var i:uint=N-1; --i>=0;) {
				e = data.edges[i];
				data.removeEdge(e);
				assertEquals(i, data.edges.size);
				assertFalse(data.edges.contains(e));
			}
			assertEquals(N, data.nodes.size);
			
			createEdges(); i=N-1;
			data.edges.visit(function(e:EdgeSprite):void {
				data.removeEdge(e); --i;
				assertEquals(data.edges.size, i);
			});
			assertEquals(0, i);
			assertEquals(0, data.edges.size);
			assertEquals(N, data.nodes.size);
		}
		
		public function visit():void
		{
			createEdges();
			
			var id10:Function = function(o:Object):Boolean {
				return o.data.id >= 10;
			};
			var counter:Function = function(o:Object):void {
				++count;
			};
			var count:int = 0;
			
			// visit nodes, count filtered on id
			data.nodes.visit(counter, false, id10);
			assertEquals(data.nodes.size-10, count);
			
			// visit all, count nodes only
			count = 0;
			data.visit(counter, Data.ALL, Filters.isNodeSprite);
			assertEquals(data.nodes.size, count);
			
			// visit all, count edges only
			count = 0;
			data.visit(counter, Data.ALL, Filters.isEdgeSprite);
			assertEquals(data.edges.size, count);
		}
		
	} // end of class DataTests
}