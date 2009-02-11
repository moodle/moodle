package flare.query
{
	import flare.util.Property;
	import flare.util.Sort;
	
	/**
	 * Performs query processing over a collection of ActionScript objects.
	 * Queries can perform filtering, sorting, grouping, and aggregation
	 * operations over a data collection. Arbitrary data collections can
	 * be queried by providing a visitor function similar to the
	 * <code>Array.forEach<code> method to the query <code>eval</code> method.
	 * 
	 * <p>The <code>select</code> and <code>where</code> methods in the
	 * <code>flare.query.methods</code> package are useful shorthands
	 * for helping to construct queries in code.</p>
	 * 
	 * <p>Here is an example of a query. It uses helper methods defined in the
	 * <code>flare.query.methods</code> package. For example, the
	 * <code>sum</code> method creates a <code>Sum</code> query operator and
	 * the <code>_</code> method creates as a <code>Literal</code> expression
	 * for its input value.</p>
	 * 
	 * <pre>
	 * import flare.query.methods.*;
	 * 
	 * var data:Array = [
	 *  {cat:"a", val:1}, {cat:"a", val:2}, {cat:"b", val:3}, {cat:"b", val:4},
	 *  {cat:"c", val:5}, {cat:"c", val:6}, {cat:"d", val:7}, {cat:"d", val:8}
	 * ];
	 * 
	 * var r:Array = select("cat", {sum:sum("val")}) // sum of values
	 *               .where(neq("cat", _("d"))       // exclude category "d"
	 *               .groupby("cat")                 // group by category
	 *               .eval(data);                    // evaluate with data array
	 * 
	 * // r == [{cat:"a", sum:3}, {cat:"b", sum:7}, {cat:"c", sum:11}]
	 * </pre>
	 */
	public class Query
	{
		private var _select:Array;
		private var _orderby:Array;
		private var _groupby:Array;
		private var _where:Expression;
		private var _sort:Sort;
		private var _aggrs:Array;
		
		/**
		 * Creates a new Query.
		 * @param select an array of select clauses. A select clause consists
		 *  of either a string representing the name of a variable to query or
		 *  an object of the form <code>{name:expr}</code>, where
		 *  <code>name</code> is the name of the query variable to include in
		 *  query result objects and <code>expr</code> is an Expression for
		 *  the actual query value. Expressions can be any legal expression, 
		 *  including aggregate operators.
		 * @param where a where expression for filtering an object collection
		 * @param orderby directives for sorting query results, using the
		 *  format of the <code>flare.util.Sort</code> class methods.
		 * @param groupby directives for grouping query results, using the
		 *  format of the <code>flare.util.Sort</code> class methods.
		 * @see flare.util.Sort
		 */
		public function Query(select:Array=null, where:Expression=null,
							  orderby:Array=null, groupby:Array=null)
		{
			if (select != null) setSelect(select);
			_where = where;
			_orderby = orderby;
			_groupby = groupby;
		}
		
		// -- public methods --------------------------------------------------
		
		/**
		 * Sets the select clauses used by this query. A select clause consists
		 * of either a string representing the name of a variable to query or
		 * an object of the form <code>{name:expr}</code>, where
		 * <code>name</code> is the name of the query variable to include in
		 * query result objects and <code>expr</code> is an Expression for
		 * the actual query value.
		 * @param terms a list query terms (select clauses)
		 * @return this query object
		 */
		public function select(...terms):Query
		{
			setSelect(terms);
			return this;
		}
		
		/**
		 * Sets the where clause (filter conditions) used by this query.
		 * @param e the filter expression. This can be a string, a literal
		 *  value, or an <code>Expression</code> instance. This input value
		 *  will be run through the <code>Expression.expr</code> method.
		 * @return this query object
		 */
		public function where(e:*):Query
		{
			_where = Expression.expr(e);
			return this;
		}
				
		/**
		 * Sets the sort order for query results.
		 * @param terms the sort terms as a list of field names to sort on.
		 *  Each name can optionally be followed by a boolean value indicating
		 *  if ascending (true) or descending (false) sort order should be
		 *  used.
		 * @return this query object
		 */
		public function orderby(...terms):Query
		{
			_orderby = (terms.length > 0 ? terms : null);
			return this;
		}
		
		/**
		 * Sets the group by terms for aggregate queries.
		 * @param terms an ordered list of terms to group by.
		 * @return this query object
		 */
		public function groupby(...terms):Query
		{
			_groupby = (terms.length > 0 ? terms : null);
			return this;
		}
		
		// -- helper methods --------------------------------------------------
		
		private function setSelect(a:Array):void {
			_select = [];
			for each (var o:Object in a) {
				if (o is String) {
					_select.push({
						name: o as String,
						expression: new Variable(o as String)
					});
				} else {
					for (var n:String in o) {
						_select.push({
							name: n,
							expression: Expression.expr(o[n])
						});
					}
				}
			}
		}
		
		private function sorter():Sort
		{
			var s:Array = [], i:int;
			if (_groupby != null) {
				for (i=0; i<_groupby.length; ++i)
					s.push(_groupby[i]);
			}
			if (_orderby != null) {
				for (i=0; i<_orderby.length; ++i)
					s.push(_orderby[i]);
			}
			return s.length==0 ? null : new Sort(s);
		}
		
		private function aggregates():Array
		{
			var aggrs:Array = [];
			for each (var pair:Object in _select) {
				var expr:Expression = pair.expression;
				expr.visit(function(e:Expression):void {
					if (e is AggregateExpression)
						aggrs.push(e);
				});
			}
			return aggrs.length==0 ? null : aggrs;
		}
		
		// -- query processing ------------------------------------------------
		
		/**
		 * Evaluates this query on an object collection. The input argument can
		 * either be an array of objects or a visitor function that takes 
		 * another function as input and applies it to all items in a
		 * collection.
		 * @param input either an array of objects or a visitor function
		 * @return an array of processed query results
		 */
		public function eval(input:*):Array
		{
			// check for initialization
			if (_sort  == null) _sort  = sorter();
			if (_aggrs == null) _aggrs = aggregates();
			
			// TODO -- evaluate any sub-queries in WHERE clause
			var results:Array = [];
			var visitor:Function;
			if (input is Array) {
				visitor = (input as Array).forEach;
			} else if (input is Function) {
				visitor = input as Function;
			} else {
				throw new ArgumentError("Illegal input argument: "+input);
			}
			
			// collect and filter
			if (_where != null) {
				visitor(function(item:Object, ...rest):void {
					if (_where.predicate(item)) {
						results.push(item);
					}
				});
			} else {
				visitor(function(item:Object, ...rest):void {
					results.push(item);
				});
			}
			
			// sort the result set
			if (_sort != null) {
				_sort.sort(results);
			}
			
			if (_select == null) return results;
			if (_aggrs == null && _groupby==null) return project(results);
			return group(results);
		}
		
		/**
		 * Performs a projection of query results, removing any properties
		 * not specified by the select clause.
		 * @param results the filtered query results array
		 * @return query results array of projected objects
		 */
		protected function project(results:Array):Array
		{			
			for (var i:int=0; i<results.length; ++i) {
				var item:Object = {};
				for each (var pair:Object in _select) {
					var name:String = pair.name;
					var expr:Expression = pair.expression;
					item[name] = expr.eval(results[i]);
				}
				results[i] = item;
			}
			return results;
		}
		
		// -- group-by and aggregation ----------------------------------------
		
		/**
		 * Performs grouping and aggregation of query results.
		 * @param items the filtered query results array
		 * @return aggregated query results array
		 */
		protected function group(items:Array):Array
		{
			var i:int, item:Object;
			var results:Array = [], props:Array = [];
			
			// get group-by properties as key
			if (_groupby != null) {
				for (i=_groupby.length; --i>=0;) {
					if (_groupby[i] is String) {
						props.push(Property.$(_groupby[i]));
					}
				}
			}
			
			// process all groups
			reset(_aggrs);
			for (i=1, item=items[0]; i<=items.length; ++i) {
				// update the aggregate functions
				for each (var aggr:AggregateExpression in _aggrs) {
					aggr.aggregate(items[i-1]);
				}
				// handle change of group
				if (i==items.length || !sameGroup(props, item, items[i])) {
					results.push(endGroup(item));
					item = items[i];
					reset(_aggrs);
				}
			}
			
			return results;
		}
		
		private function reset(aggrs:Array):void
		{
			for each (var aggr:AggregateExpression in aggrs) {
				aggr.reset();
			}
		}
		
		private function endGroup(item:Object):Object
		{
			var result:Object = {};
			for each (var pair:Object in _select) {
				var name:String = pair.name;
				var expr:Expression = pair.expression;
				result[name] = expr.eval(item);
			}
			return result;
		}
		
		private static function sameGroup(props:Array, x:Object, y:Object):Boolean
		{
			var a:*, b:*;
			for each (var p:Property in props) {
				a = p.getValue(x);
				b = p.getValue(y);
				
				if (a is Date && b is Date) {
					if ((a as Date).time != (b as Date).time)
						return false;
				} else if (a != b) {
					return false;
				}
			}
			return true;
		}
		
	} // end of class Query
}