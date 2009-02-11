package flare.tests
{
	import flare.util.Property;
	import flare.vis.data.Data;
	import flare.vis.data.EdgeSprite;
	import flare.vis.data.NodeSprite;
	import flare.vis.data.Tree;
	import flare.vis.util.TreeUtil;
	import unitest.TestCase;
	
	public class TreeTests extends TestCase
	{
		public function TreeTests() {
			addTest("bfsTest");
			addTest("dfsTest");
			addTest("mstTest");
			addTest("bfsTwiceTest");
		}
		
		// --------------------------------------------------------------------
		
		private var data:Data;
		private var a:NodeSprite, b:NodeSprite, c:NodeSprite, d:NodeSprite,
					e:NodeSprite, f:NodeSprite, g:NodeSprite;
		private var ab:EdgeSprite, bc:EdgeSprite, cd:EdgeSprite, ae:EdgeSprite,
					bf:EdgeSprite, cg:EdgeSprite, dg:EdgeSprite, ef:EdgeSprite,
					fg:EdgeSprite;
		
		//  a--b--c--d
		//  |  |  | /
		//  e--f--g
		protected override function setup():void
		{
			data = new Data();
			a = data.addNode(); a.name = "a";
			b = data.addNode(); b.name = "b";
			c = data.addNode(); c.name = "c";
			d = data.addNode(); d.name = "d";
			e = data.addNode(); e.name = "e";
			f = data.addNode(); f.name = "f";
			g = data.addNode(); g.name = "g";
			
			ab = data.addEdgeFor(a, b); ab.name = "ab"; ab.data = {w:1};
			bc = data.addEdgeFor(b, c); bc.name = "bc"; bc.data = {w:10};
			cd = data.addEdgeFor(c, d); cd.name = "cd"; cd.data = {w:1};
			ae = data.addEdgeFor(a, e); ae.name = "ae"; ae.data = {w:4};
			bf = data.addEdgeFor(b, f); bf.name = "bf"; bf.data = {w:5};
			cg = data.addEdgeFor(c, g); cg.name = "cg"; cg.data = {w:5};
			dg = data.addEdgeFor(d, g); dg.name = "dg"; dg.data = {w:0.1};
			ef = data.addEdgeFor(e, f); ef.name = "ef"; ef.data = {w:1};
			fg = data.addEdgeFor(f, g); fg.name = "fg"; fg.data = {w:1};
		}
		
		protected override function clean():void
		{
			data = null;
			a=b=c=d=e=f=g=null;
		}
		
		//  a--b  c--d
		//  |       /
		//  e--f--g
		public function mstTest():void
		{
			var p:Property = new Property("data.w");
			data.treeBuilder = TreeUtil.mstBuilder(p.getValue);
			data.root = f;
			var tree:Tree = data.tree;
			
			assertEquals(7, tree.nodes.size);
			assertEquals(6, tree.edges.size);
			assertEquals(f, tree.root);
			assertEquals(f, e.parentNode);
			assertEquals(f, g.parentNode);
			assertEquals(e, a.parentNode);
			assertEquals(a, b.parentNode);
			assertEquals(g, d.parentNode);
			assertEquals(d, c.parentNode);
			assertTrue(tree.contains(ab),  "contains "+ab.name);
			assertTrue(tree.contains(ae),  "contains "+ae.name);
			assertTrue(tree.contains(ef),  "contains "+ef.name);
			assertTrue(tree.contains(fg),  "contains "+fg.name);
			assertTrue(tree.contains(dg),  "contains "+dg.name);
			assertTrue(tree.contains(cd),  "contains "+cd.name);
			assertFalse(tree.contains(bc), "!contains "+bc.name);
			assertFalse(tree.contains(bf), "!contains "+bf.name);
			assertFalse(tree.contains(cg), "!contains "+cg.name);
		}
		
		//  a--b--c--d
		//  |  |  |  
		//  e  f  g
		public function bfsTest():void
		{
			data.treeBuilder = TreeUtil.breadthFirstTree;
			var tree:Tree = data.tree;

			assertEquals(7, tree.nodes.size);
			assertEquals(6, tree.edges.size);			
			assertEquals(a, tree.root);
			assertEquals(a, b.parentNode);
			assertEquals(a, e.parentNode);
			assertEquals(b, c.parentNode);
			assertEquals(b, f.parentNode);
			assertEquals(c, d.parentNode);
			assertEquals(c, g.parentNode);
			assertTrue(tree.contains(ab));
			assertTrue(tree.contains(ae));
			assertTrue(tree.contains(bc));
			assertTrue(tree.contains(bf));
			assertTrue(tree.contains(cd));
			assertTrue(tree.contains(cg));
			assertFalse(tree.contains(ef));
			assertFalse(tree.contains(fg));
			assertFalse(tree.contains(dg));
		}
		
		// a-b-c-d-g-f-e
		public function dfsTest():void
		{
			data.treeBuilder = TreeUtil.depthFirstTree;
			var tree:Tree = data.tree;
			
			assertEquals(7, tree.nodes.size);
			assertEquals(6, tree.edges.size);
			assertEquals(a, tree.root);
			assertEquals(a, b.parentNode);
			assertEquals(b, c.parentNode);
			assertEquals(c, d.parentNode);
			assertEquals(d, g.parentNode);
			assertEquals(g, f.parentNode);
			assertEquals(f, e.parentNode);
			assertTrue(tree.contains(ab));
			assertTrue(tree.contains(bc));
			assertTrue(tree.contains(cd));
			assertTrue(tree.contains(dg));
			assertTrue(tree.contains(fg));
			assertTrue(tree.contains(ef));
			assertFalse(tree.contains(ae));
			assertFalse(tree.contains(bf));
			assertFalse(tree.contains(cg));
		}
		
		//  a--b--c--d
		//  |  |  |  
		//  e  f  g
		public function bfsTwiceTest():void
		{
			data.treeBuilder = TreeUtil.breadthFirstTree;
			data.root = c;
			var tree:Tree = data.tree;
				
			data.root = a;
			tree = data.tree;

			assertEquals(7, tree.nodes.size);
			assertEquals(6, tree.edges.size);			
			assertEquals(a, tree.root);
			assertEquals(a, b.parentNode);
			assertEquals(a, e.parentNode);
			assertEquals(b, c.parentNode);
			assertEquals(b, f.parentNode);
			assertEquals(c, d.parentNode);
			assertEquals(c, g.parentNode);
			assertTrue(tree.contains(ab));
			assertTrue(tree.contains(ae));
			assertTrue(tree.contains(bc));
			assertTrue(tree.contains(bf));
			assertTrue(tree.contains(cd));
			assertTrue(tree.contains(cg));
			assertFalse(tree.contains(ef));
			assertFalse(tree.contains(fg));
			assertFalse(tree.contains(dg));
		}

	}
}