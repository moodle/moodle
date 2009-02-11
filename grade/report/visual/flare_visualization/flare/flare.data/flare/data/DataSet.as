package flare.data
{	
	/**
	 * A data set is a collection of one or more data tables that represent
	 * a table or graph data structure.
	 */
	public class DataSet
	{
		/**
		 * Creates a new DataSet.
		 * @param nodes a data table of node data
		 * @param edges a data table of edge data (optional, for graphs only)
		 */
		public function DataSet(nodes:DataTable, edges:DataTable=null) {
			this.nodes = nodes;
			this.edges = edges;
		}

		/** A DataTable of nodes (or table rows). */
		public var nodes:DataTable = null;
		
		/** A DataTable of edges. */
		public var edges:DataTable = null;

	} // end of class DataSet
}