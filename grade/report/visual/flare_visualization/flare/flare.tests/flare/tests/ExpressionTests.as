package flare.tests
{
	import flare.query.And;
	import flare.query.Arithmetic;
	import flare.query.Comparison;
	import flare.query.Expression;
	import flare.query.If;
	import flare.query.Literal;
	import flare.query.Or;
	import flare.query.Query;
	import flare.query.Range;
	import flare.query.Variable;
	import flare.query.Xor;
	import flare.query.methods.*;
	
	import unitest.TestCase;

	public class ExpressionTests extends TestCase
	{
		public function ExpressionTests() {
			addTest("testParse");
			addTest("testExpressions");
			addTest("testExpressionMethods");
			addTest("testQuery");
		}
		
		public function testParse():void
		{
			assertTrue(Expression.expr(true) is Literal);
			assertTrue(Expression.expr(false) is Literal);
			assertTrue(Expression.expr(1) is Literal);
			assertTrue(Expression.expr(new Date()) is Literal);
			assertTrue(Expression.expr("'a'") is Literal);
			assertTrue(Expression.expr("a") is Variable);
			assertTrue(Expression.expr("{a}") is Variable);
			
			var s:String = "a";
			assertTrue(_(s) is Literal);
			assertEquals("a", _(s).eval());
			assertTrue($(s) is Variable);
			assertEquals("a", $(s).name);
			assertTrue(Expression.expr(s) is Variable);
			assertTrue(Expression.expr(_(s)) is Literal);
			assertTrue(Expression.expr($(s)) is Variable);
		}
		
		// test variables
		private var t1:Date = new Date(1979,5,15);
		private var t2:Date = new Date(1982,2,19);
		private var _lt:Expression;
		private var _gt:Expression;
		private var _eq:Expression;
		private var _neq:Expression;
		private var _lte:Expression;
		private var _gte:Expression;
		private var _add:Expression;
		private var _sub:Expression;
		private var _mul:Expression;
		private var _div:Expression;
		private var _mod:Expression;
		private var _and:Expression;
		private var _xor:Expression;
		private var _or:Expression;
		private var _if:Expression;
		private var _range:Range;
		private var _span:Range;
		
		private function _tests():Array
		{
			return [
				// numbers
				{expr:_lt,   input:{a:0,b:0}, result:false},
				{expr:_gt,   input:{a:0,b:0}, result:false},
				{expr:_eq,   input:{a:0,b:0}, result:true},
				{expr:_neq,  input:{a:0,b:0}, result:false},
				{expr:_lte,  input:{a:0,b:0}, result:true},
				{expr:_gte,  input:{a:0,b:0}, result:true},
				{expr:_lt,   input:{a:1,b:0}, result:false},
				{expr:_gt,   input:{a:1,b:0}, result:true},
				{expr:_eq,   input:{a:1,b:0}, result:false},
				{expr:_neq,  input:{a:1,b:0}, result:true},
				{expr:_lte,  input:{a:1,b:0}, result:false},
				{expr:_gte,  input:{a:1,b:0}, result:true},
				{expr:_lt,   input:{a:0,b:1}, result:true},
				{expr:_gt,   input:{a:0,b:1}, result:false},
				{expr:_eq,   input:{a:0,b:1}, result:false},
				{expr:_neq,  input:{a:0,b:1}, result:true},
				{expr:_lte,  input:{a:0,b:1}, result:true},
				{expr:_gte,  input:{a:0,b:1}, result:false},
				
				{expr:_add,  input:{a:2,b:3}, result:5},
				{expr:_sub,  input:{a:2,b:3}, result:-1},
				{expr:_mul,  input:{a:2,b:3}, result:6},
				{expr:_div,  input:{a:2,b:3}, result:2/3},
				{expr:_mod,  input:{a:2,b:3}, result:2},
				{expr:_add,  input:{a:3,b:2}, result:5},
				{expr:_sub,  input:{a:3,b:2}, result:1},
				{expr:_mul,  input:{a:3,b:2}, result:6},
				{expr:_div,  input:{a:3,b:2}, result:1.5},
				{expr:_mod,  input:{a:3,b:2}, result:1},
				
				{expr:_and,  input:{a:3,b:2}, result:true},
				{expr:_and,  input:{a:0,b:2}, result:false},
				{expr:_xor,  input:{a:3,b:2}, result:true},
				{expr:_xor,  input:{a:1,b:1}, result:true},
				{expr:_xor,  input:{a:0,b:0}, result:false},
				{expr:_or,   input:{a:3,b:2}, result:true},
				{expr:_or,   input:{a:0,b:0}, result:false},
				
				{expr:_if,   input:{a:2,b:3}, result:-1},
				{expr:_if,   input:{a:3,b:3}, result:6},
				
				{expr:_range,input:{a:-2},    result:false},
				{expr:_range,input:{a:-1},    result:true},
				{expr:_range,input:{a:0},     result:true},
				{expr:_range,input:{a:1},     result:true},
				{expr:_range,input:{a:2},     result:false},
				
				// dates
				{expr:_lt,   input:{a:t1,b:t2}, result:true},
				{expr:_gt,   input:{a:t1,b:t2}, result:false},
				{expr:_eq,   input:{a:t1,b:t2}, result:false},
				{expr:_neq,  input:{a:t1,b:t2}, result:true},
				{expr:_lte,  input:{a:t1,b:t2}, result:true},
				{expr:_gte,  input:{a:t1,b:t2}, result:false},	
				{expr:_lt,   input:{a:t1,b:t1}, result:false},
				{expr:_gt,   input:{a:t1,b:t1}, result:false},
				{expr:_eq,   input:{a:t1,b:t1}, result:true},
				{expr:_neq,  input:{a:t1,b:t1}, result:false},
				{expr:_lte,  input:{a:t1,b:t1}, result:true},
				{expr:_gte,  input:{a:t1,b:t1}, result:true},
				{expr:_span, input:{a:new Date(1978,1)}, result:false},
				{expr:_span, input:{a:t1},               result:true},
				{expr:_span, input:{a:new Date(1980,1)}, result:true},
				{expr:_span, input:{a:t2},               result:true},
				{expr:_span, input:{a:new Date(1990,1)}, result:false},
				
				// strings
				{expr:_lt,   input:{a:"a",b:"b"}, result:true},
				{expr:_gt,   input:{a:"a",b:"b"}, result:false},
				{expr:_eq,   input:{a:"a",b:"b"}, result:false},
				{expr:_neq,  input:{a:"a",b:"b"}, result:true},
				{expr:_lte,  input:{a:"a",b:"b"}, result:true},
				{expr:_gte,  input:{a:"a",b:"b"}, result:false},	
				{expr:_lt,   input:{a:"a",b:"a"}, result:false},
				{expr:_gt,   input:{a:"a",b:"a"}, result:false},
				{expr:_eq,   input:{a:"a",b:"a"}, result:true},
				{expr:_neq,  input:{a:"a",b:"a"}, result:false},
				{expr:_lte,  input:{a:"a",b:"a"}, result:true},
				{expr:_gte,  input:{a:"a",b:"a"}, result:true},
			];
		}
		
		private function _runTests():void
		{
			var tests:Array = _tests();
			for (var i:uint=0; i<tests.length; ++i) {
				var e:Expression = tests[i].expr;
				var val:Object = e.eval(tests[i].input);
				assertEquals(tests[i].result, val, i+":"+e.toString());
			}
		}
		
		public function testExpressions():void 
		{
			var l:Variable = new Variable("a");
			var r:Variable = new Variable("b");
			
			_lt  = Comparison.LessThan(l,r);
			_gt  = Comparison.GreaterThan(l,r);
			_eq  = Comparison.Equal(l,r);
			_neq = Comparison.NotEqual(l,r);
			_lte = Comparison.LessThanOrEqual(l,r);
			_gte = Comparison.GreaterThanOrEqual(l,r);
			_add = Arithmetic.Add(l, r);
			_sub = Arithmetic.Subtract(l, r);
			_mul = Arithmetic.Multiply(l, r);
			_div = Arithmetic.Divide(l, r);
			_mod = Arithmetic.Mod(l, r);
			_and = new And(Comparison.GreaterThan(_mul, _add),
						   Comparison.GreaterThan(_add, _sub));
			_xor = new Xor(Comparison.GreaterThan(_add, _mul),
						   Comparison.GreaterThan(_mul, _add));
			_or  = new Or(Comparison.GreaterThan(_add, _mul),
						  Comparison.GreaterThan(_mul, _add));
			_if = new If(_eq, _add, _sub);
			_range = new Range(new Literal(-1), new Literal(+1), l);
			_span  = new Range(new Literal(t1), new Literal(t2), l);

			_runTests();
		}
		
		public function testExpressionMethods():void 
		{
			var a:Variable = $("a");
			var b:Variable = $("b");
			
			_lt  = lt(a, b);
			_gt  = gt(a, b);
			_eq  = eq(a, b);
			_neq = neq(a, b);
			_lte = lte(a, b);
			_gte = gte(a, b);
			_add = add(a, b);
			_sub = sub(a, b);
			_mul = mul(a, b);
			_div = div(a, b);
			_mod = mod(a, b);
			_and = and(gt(_mul, _add), gt(_add, _sub));
			_xor = xor(gt(_add, _mul), gt(_mul, _add));	
			_or  = or(gt(_add, _mul), gt(_mul, _add));
			_if = iff(_eq, _add, _sub);
			_range = range(-1, +1, a);
			_span  = range(t1, t2, a);
			
			_runTests();
		}
		
		public function testQuery():void
		{
			var data:Array = [
				{val:4, cat:"a"},
				{val:4, cat:"a"},
				{val:4, cat:"a"},
				{val:3, cat:"b"},
				{val:3, cat:"b"},
				{val:3, cat:"b"},
				{val:2, cat:"c"},
				{val:2, cat:"c"},
				{val:2, cat:"c"},
				{val:1, cat:"d"}
			];
			
			var r:Array;
			
			r = select({count:count("cat")}).eval(data);
			assertEquals(1, r.length);
			assertEquals(10, r[0].count);
			
			r = select({distinct:distinct("cat")}).eval(data);
			assertEquals(1, r.length);
			assertEquals(4, r[0].distinct);
			
			r = select({min:min("val")}).eval(data);
			assertEquals(1, r.length);
			assertEquals(1, r[0].min);
			
			r = select({max:max("val")}).eval(data);
			assertEquals(1, r.length);
			assertEquals(4, r[0].max);
			
			r = select({avg:average("val")}).eval(data);
			assertEquals(1, r.length);
			assertEquals(2.8, r[0].avg);
			
			r = select({sum:sum("val")}).eval(data);
			assertEquals(1, r.length);
			assertEquals(28, r[0].sum);
			
			r = select({sum:sum("val")})
				.where(eq("cat", _("a"))) // use a as literal
				.eval(data);
			assertEquals(1, r.length);
			assertEquals(12, r[0].sum);
			
			r = select({sum:sum("val")})
				.where(eq("cat", $("a"))) // use a as variable
				.eval(data);
			assertEquals(0, r.length);
			
			r = select("cat", {sum:sum("val")})
				.groupby("cat")
				.eval(data);
			assertEquals( 4, r.length);
			assertEquals(12, r[0].sum); assertEquals("a", r[0].cat);
			assertEquals( 9, r[1].sum); assertEquals("b", r[1].cat);
			assertEquals( 6, r[2].sum); assertEquals("c", r[2].cat);
			assertEquals( 1, r[3].sum); assertEquals("d", r[3].cat);
			
			var q:Query = where(or(eq("cat", _("a")),
			                       eq("cat", _("b"))));
			r = q.eval(data);
			assertEquals(6, r.length);
			
			r = q.orderby("cat", false).eval(data);
			assertEquals(6, r.length);
			assertEquals("b", r[0].cat);
			assertEquals("a", r[5].cat);
			
			r = where(eq(func("sqrt","val"), 2)).eval(data);
			assertEquals(3, r.length);
			assertEquals("a", r[0].cat);
			
			// -----
			
			data = [
	 			{cat:"a", val:1}, {cat:"a", val:2},
	 			{cat:"b", val:3}, {cat:"b", val:4},
	 			{cat:"c", val:5}, {cat:"c", val:6},
	 			{cat:"d", val:7}, {cat:"d", val:8}
	 		];
	 
	 		r = orderby("cat", true, "val", false).eval(data);
	 		assertEquals(8, r.length);
	 		assertEquals(2, r[0].val); assertEquals(1, r[1].val); 
	 		assertEquals(4, r[2].val); assertEquals(3, r[3].val); 
	 		assertEquals(6, r[4].val); assertEquals(5, r[5].val); 
	 		assertEquals(8, r[6].val); assertEquals(7, r[7].val); 
	 
	 		r = select("cat", {sum:sum("val")}) // category + sum of values
	            .where(neq("cat", _("d")))      // exclude category "d"
	            .groupby("cat")                 // group by category
	            .eval(data);                    // evaluate with data array
	  		assertEquals( 3, r.length);
	  		assertEquals("a", r[0].cat); assertEquals( 3, r[0].sum);
	  		assertEquals("b", r[1].cat); assertEquals( 7, r[1].sum);
	  		assertEquals("c", r[2].cat); assertEquals(11, r[2].sum);
		}
		
	}
}