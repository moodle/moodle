package flare.tests
{
	import flare.animate.interpolate.ArrayInterpolator;
	import flare.animate.interpolate.ColorInterpolator;
	import flare.animate.interpolate.DateInterpolator;
	import flare.animate.interpolate.MatrixInterpolator;
	import flare.animate.interpolate.NumberInterpolator;
	import flare.animate.interpolate.PointInterpolator;
	import flare.animate.interpolate.RectangleInterpolator;
	import flare.util.Colors;
	
	import flash.geom.Matrix;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	
	import unitest.TestCase;

	public class AnimationTests extends TestCase
	{
		public function AnimationTests() {
			addTest("testNumberInterp");
			addTest("testDateInterp");
			addTest("testColorInterp");
			addTest("testArrayInterp");
			addTest("testPointInterp");
			addTest("testRectangleInterp");
			addTest("testMatrixInterp");
		}
		
		public function testNumberInterp():void {
			var o:Object = {};
			var ni:NumberInterpolator = new NumberInterpolator(o, "v", 0, 1);
			
			for (var f:Number=0; f<=1.0; f+=0.1) {
				ni.interpolate(f);
				assertEquals(f, o.v);
			}
		}
		
		public function testColorInterp():void {
			var s:uint = 0x00ff0000;
			var t:uint = 0x000000ff;
			var o:Object = {v:s};
			var ci:ColorInterpolator = new ColorInterpolator(o, "v", s, t);
			
			for (var f:Number=0; f<=1.0; f+=0.1) {
				ci.interpolate(f);
				assertEquals(Colors.interpolate(s,t,f), o.v);
			}
			
			s = 0xff00ff00;
			t = 0x00ff0000;
			ci.reset(o, "v", s, t);
			for (f=0; f<=1.0; f+=0.1) {
				ci.interpolate(f);
				assertEquals(Colors.interpolate(s,t,f), o.v);
			}
		}
		
		public function testDateInterp():void {
			var t0:Number = 0;
			var t1:Number = 10000000;
			var s:Date = new Date(t0);
			var t:Date = new Date(t1);
			var o:Object = {};
			var di:DateInterpolator = new DateInterpolator(o, "v", s, t);
			
			for (var f:Number=0; f<=1.0; f+=0.1) {
				di.interpolate(f);
				assertTrue(Math.abs(f*t1 - o.v.time) < 2);
			}
			assertNotEquals(s, o.v);
			assertNotEquals(t, o.v);
			
			di.reset(o, "v", s, o.v);
			for (f=0; f<=1.0; f+=0.1) {
				di.interpolate(f);
				assertTrue(Math.abs(f*t1 - o.v.time) < 2);
			}
			assertNotEquals(s, o.v);
			assertNotEquals(t, o.v);
		}
		
		public function testArrayInterp():void {
			var s:Array = [0, 0, 0, 0, 0];
			var t:Array = [1, 1, 1, 1, 1];
			var o:Object = {v:s};
			var ai:ArrayInterpolator = new ArrayInterpolator(o, "v", o.v, t);
			
			for (var f:Number=0; f<=1.0; f+=0.1) {
				ai.interpolate(f);
				for (var i:int=0; i<s.length; ++i)
					assertTrue(Math.abs(f - o.v[i]) < 0.000001);
			}
			assertNotEquals(s, o.v);
			assertNotEquals(t, o.v);
			
			ai.reset(o, "v", s, o.v);
			for (f=0; f<=1.0; f+=0.1) {
				ai.interpolate(f);
				for (i=0; i<s.length; ++i)
					assertTrue(Math.abs(f - o.v[i]) < 0.000001);
			}
			assertNotEquals(s, o.v);
			assertNotEquals(t, o.v);
		}
		
		public function testPointInterp():void {
			var s:Point = new Point(0, 0);
			var t:Point = new Point(1, 1);
			var o:Object = {v:s};
			var pi:PointInterpolator = new PointInterpolator(o, "v", o.v, t);
			
			for (var f:Number=0; f<=1.0; f+=0.1) {
				pi.interpolate(f);
				assertTrue(Math.abs(f - o.v.x) < 0.000001);
				assertTrue(Math.abs(f - o.v.y) < 0.000001);
			}
			assertNotEquals(s, o.v);
			assertNotEquals(t, o.v);
			
			pi.reset(o, "v", s, o.v);
			for (f=0; f<=1.0; f+=0.1) {
				pi.interpolate(f);
				assertTrue(Math.abs(f - o.v.x) < 0.000001);
				assertTrue(Math.abs(f - o.v.y) < 0.000001);
			}
			assertNotEquals(s, o.v);
			assertNotEquals(t, o.v);
		}
		
		public function testRectangleInterp():void {
			var s:Rectangle = new Rectangle(0, 0, 0, 0);
			var t:Rectangle = new Rectangle(1, 1, 1, 1);
			var o:Object = {v:s};
			var ri:RectangleInterpolator = new RectangleInterpolator(o, "v", o.v, t);
			
			for (var f:Number=0; f<=1.0; f+=0.1) {
				ri.interpolate(f);
				assertTrue(Math.abs(f - o.v.x) < 0.000001);
				assertTrue(Math.abs(f - o.v.y) < 0.000001);
				assertTrue(Math.abs(f - o.v.width) < 0.000001);
				assertTrue(Math.abs(f - o.v.height) < 0.000001);
			}
			assertNotEquals(s, o.v);
			assertNotEquals(t, o.v);
			
			ri.reset(o, "v", s, o.v);
			for (f=0; f<=1.0; f+=0.1) {
				ri.interpolate(f);
				assertTrue(Math.abs(f - o.v.x) < 0.000001);
				assertTrue(Math.abs(f - o.v.y) < 0.000001);
				assertTrue(Math.abs(f - o.v.width) < 0.000001);
				assertTrue(Math.abs(f - o.v.height) < 0.000001);
			}
			assertNotEquals(s, o.v);
			assertNotEquals(t, o.v);
		}
		
		public function testMatrixInterp():void {
			var s:Matrix = new Matrix(0, 0, 0, 0, 0, 0);
			var t:Matrix = new Matrix(1, 1, 1, 1, 1, 1);
			var o:Object = {v:s};
			var mi:MatrixInterpolator = new MatrixInterpolator(o, "v", o.v, t);
			
			for (var f:Number=0; f<=1.0; f+=0.1) {
				mi.interpolate(f);
				assertTrue(Math.abs(f - o.v.a) < 0.000001);
				assertTrue(Math.abs(f - o.v.b) < 0.000001);
				assertTrue(Math.abs(f - o.v.c) < 0.000001);
				assertTrue(Math.abs(f - o.v.d) < 0.000001);
				assertTrue(Math.abs(f - o.v.tx) < 0.000001);
				assertTrue(Math.abs(f - o.v.ty) < 0.000001);
			}
			assertNotEquals(s, o.v);
			assertNotEquals(t, o.v);
			
			mi.reset(o, "v", s, o.v);
			for (f=0; f<=1.0; f+=0.1) {
				mi.interpolate(f);
				assertTrue(Math.abs(f - o.v.a) < 0.000001);
				assertTrue(Math.abs(f - o.v.b) < 0.000001);
				assertTrue(Math.abs(f - o.v.c) < 0.000001);
				assertTrue(Math.abs(f - o.v.d) < 0.000001);
				assertTrue(Math.abs(f - o.v.tx) < 0.000001);
				assertTrue(Math.abs(f - o.v.ty) < 0.000001);
			}
			assertNotEquals(s, o.v);
			assertNotEquals(t, o.v);
		}
		
	} // end of class AnimationTests
}