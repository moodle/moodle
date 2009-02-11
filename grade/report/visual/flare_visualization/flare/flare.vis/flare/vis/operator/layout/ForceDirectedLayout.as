package flare.vis.operator.layout
{
	import flare.animate.Transitioner;
	import flare.physics.Particle;
	import flare.physics.Simulation;
	import flare.physics.Spring;
	import flare.vis.data.Data;
	import flare.vis.data.DataSprite;
	import flare.vis.data.EdgeSprite;
	import flare.vis.data.NodeSprite;
	
	/**
	 * Layout that positions graph items based on a physics simulation of
	 * interacting forces; by default, nodes repel each other, edges act as
	 * springs, and drag forces (similar to air resistance) are applied. This
	 * algorithm can be run for multiple iterations for a run-once layout
	 * computation or repeatedly run in an animated fashion for a dynamic and
	 * interactive layout (set <code>Visualization.continuousUpdates = true
	 * </code>).
	 * 
	 * <p>The running time of this layout algorithm is the greater of O(N log N)
	 * and O(E), where N is the number of nodes and E the number of edges.
	 * The addition of custom forces to the simulation may affect this.</p>
	 * 
	 * <p>The force directed layout is implemented using the physics simulator
	 * provided by the <code>flare.physics</code> package. The
	 * <code>Simulation</code> used to drive this layout can be set explicitly,
	 * allowing any number of custom force directed layouts to be created
	 * through the selection of <code>IForce</code> modules. Each node in the
	 * layout is mapped to a <code>Particle</code> instance and each edge
	 * to a <code>Spring</code> in the simulation. Once the simulation has been
	 * initialized, you can retrieve these instances through the
	 * <code>node.props.particle</code> and <code>edge.props.spring</code>
	 * properties, respectively.</p>
	 * 
	 * @see flare.physics
	 */
	public class ForceDirectedLayout extends Layout
	{
		private var _sim:Simulation;
		private var _step:Number = 1;
		private var _iter:int = 1;
		private var _gen:uint = 0;
		private var _enforceBounds:Boolean = false;
		
		// simulation defaults
		private var _mass:Number = 1;
		private var _restLength:Number = 30;
		private var _tension:Number = 0.3;
		private var _damping:Number = 0.1;
		
		private var _t:Transitioner;
		
		/** The default mass value for node/particles. */
		public function get defaultParticleMass():Number { return _mass; }
		public function set defaultParticleMass(v:Number):void { _mass = v; }
		
		/** The default spring rest length for edge/springs. */
		public function get defaultSpringLength():Number { return _restLength; }
		public function set defaultSpringLength(v:Number):void { _restLength = v; }
		
		/** The default spring tension for edge/springs. */
		public function get defaultSpringTension():Number { return _tension; }
		public function set defaultSpringTension(v:Number):void { _tension = v; }
		
		/** The number of iterations to run the simulation per invocation
		 *  (default is 1, expecting continuous updates). */
		public function get iterations():int { return _iter; }
		public function set iterations(iter:int):void { _iter = iter; }
		
		/** The number of time ticks to advance the simulation on each
		 *  iteration (default is 1). */
		public function get ticksPerIteration():int { return _step; }
		public function set ticksPerIteration(ticks:int):void { _step = ticks; }
		
		/** The physics simulation driving this layout. */
		public function get simulation():Simulation { return _sim; }
		
		/** Flag indicating if the layout bounds should be enforced. 
		 *  If true, the layoutBounds will limit node placement. */
		public function get enforceBounds():Boolean { return _enforceBounds; }
		public function set enforceBounds(b:Boolean):void { _enforceBounds = b; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new ForceDirectedLayout.
		 * @param iterations the number of iterations to run the simulation
		 *  per invocation
		 * @param sim the physics simulation to use for the layout. If null
		 *  (the default), default simulation settings will be used
		 */
		public function ForceDirectedLayout(enforceBounds:Boolean=false, 
			iterations:int=1, sim:Simulation=null)
		{
			_enforceBounds = enforceBounds;
			_iter = iterations;
			_sim = (sim==null ? new Simulation(0, 0, 0.1, -10) : sim);
		}
		
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			_t = (t!=null ? t : Transitioner.DEFAULT);
			
			++_gen; // update generation counter
			init(); // populate simulation
			
			// run simulation
			_sim.bounds = _enforceBounds ? layoutBounds : null;
			for (var i:uint=0; i<_iter; ++i) {
				_sim.tick(_step);
			}
			visualization.data.nodes.visit(update); // update positions
			
			updateEdgePoints(_t);
			_t = null;
		}
		
		// -- value transfer --------------------------------------------------
		
		/**
		 * Transfer the physics simulation results to an item's layout.
		 * @param d a DataSprite
		 * @return true, to signal a visitor to continue
		 */
		protected function update(d:DataSprite):void
		{
			var p:Particle = d.props.particle;
			if (!p.fixed) {
				var o:Object = _t.$(d);
				o.x = p.x;
				o.y = p.y;
			}
		}
		
		// -- simulation management -------------------------------------------
		
		/**
		 * Initializes the Simulation for this ForceDirectedLayout
		 */
		protected function init():void
		{
			var data:Data = visualization.data;
			var p:Particle, s:Spring, n:NodeSprite, e:EdgeSprite;
			
			// initialize all simulation entries
			for each (n in data.nodes) {
				p = n.props.particle;
				if (p == null) {
					n.props.particle = (p = _sim.addParticle(_mass, n.x, n.y));
					p.fixed = n.fixed;
				} else {
					p.x = n.x;
					p.y = n.y;
					p.fixed = n.fixed;
				}
				p.tag = _gen;
			}
			for each (e in data.edges) {
				s = e.props.spring;
				if (s == null) {
					e.props.spring = (s = _sim.addSpring(
						e.source.props.particle, e.target.props.particle,
						_restLength, _tension, _damping));
				}
				s.tag = _gen;
			}
			
			// set up simulation parameters
			for each (n in data.nodes) {
				p = n.props.particle;
				p.mass = mass(n);
			}
			for each (e in data.edges) {
				s = e.props.spring;
				s.restLength = restLength(e);
				s.tension = tension(e);
				s.damping = damping(e);
			}
			
			// clean-up unused items
			for each (p in _sim.particles)
				if (p.tag != _gen) p.kill();
			for each (s in _sim.springs)
				if (s.tag != _gen) s.kill();
		}
		
		/**
		 * Function for assigning mass values to particles. By default, this
		 * simply returns the default mass value. This function can be replaced
		 * to perform custom mass assignment.
		 */
		public var mass:Function = function(d:DataSprite):Number {
			return _mass;
		}
		
		/**
		 * Function for assigning rest length values to springs. By default,
		 * this simply returns the default rest length value. This function can
		 * be replaced to perform custom rest length assignment.
		 */
		public var restLength:Function = function(e:EdgeSprite):Number {
			return _restLength;
		}
		
		/**
		 * Function for assigning tension values to springs. By default, this
		 * method computes spring tension adaptively, based on the connectivity
		 * of the attached particles, to create more stable layouts. More
		 * specifically, the tension is computed as the default tension value
		 * divided by the square root of the maximum degree of the attached
		 * particles. This function can be replaced to perform custom tension
		 * assignment.
		 */
		public var tension:Function = function(e:EdgeSprite):Number {
			var s:Spring = Spring(e.props.spring);
			var n:Number = Math.max(s.p1.degree, s.p2.degree);
			return _tension / Math.sqrt(n);
		}
		
		/**
		 * Function for assigning damping constant values to springs. By
		 * default, this simply uses the spring's computed tension value
		 * divided by 10. This function can be replaced to perform custom
		 * damping assignment.
		 */
		public var damping:Function = function(e:EdgeSprite):Number {
			return Spring(e.props.spring).tension / 10;
		}
		
	} // end of class ForceDirectedLayout
}