package flare.demos
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flare.physics.Simulation;
	import flash.utils.Dictionary;
	import flash.display.Sprite;
	import flash.display.Shape;
	import flare.physics.Particle;
	import flare.physics.Spring;
	import flare.animate.Scheduler;
	import flare.animate.ISchedulable;
	import flare.demos.Demo;
	
	public class Graph extends Demo implements ISchedulable
	{
		private var sim:Simulation;
		private var nodes:Sprite;
		private var edges:Sprite;
		private var dragc:DragControl;
		private var dict:Dictionary;
		
		public function Graph() {
			name = "Graph";
			edges = new Sprite(); addChild(edges);
			nodes = new Sprite(); addChild(nodes);

			sim = new Simulation(0, 0, 0.05, -50);
			dict = new Dictionary();
			
			buildGrid(10, 10);
			
			dragc = new DragControl();
			dragc.attach(nodes);
			
			new PanZoomControl().attach(this);
			
			drawGraph();
			x = 0.5 * (WIDTH  - nodes.width);
			y = 0.5 * (HEIGHT - nodes.height);
		}
		
		private function newNode():Sprite {
			var n:Sprite = new Sprite();
			n.graphics.beginFill(0xaaaaaa, 0.5);
			n.graphics.drawEllipse(-8,-8,16,16);
			n.graphics.endFill();
			n.buttonMode = true;
			return n;
		}
		
		private function buildGrid(rows:uint, cols:uint):void {
			var n:Sprite, e:Shape;
				
			// init graph
			for (var i:uint = 0; i<rows*cols; ++i) {
				n = newNode();
				nodes.addChild(n);
				dict[n] = sim.addParticle(1, 30*(i%cols), 30*int(i/cols));
			}
			for (i = 0; i<rows*cols; ++i) {
				if (i >= cols) {
					e = new Shape();
					edges.addChild(e);
					dict[e] = sim.addSpring(sim.particles[i], sim.particles[i-cols], 30, 0.1, 0.1);
				}
				if (i % cols != 0) {
					e = new Shape();
					edges.addChild(e);
					dict[e] = sim.addSpring(sim.particles[i], sim.particles[i-1], 30, 0.1, 0.1);
				}
			}
		}
		
		private function drawGraph():void {
			var p:Particle, s:Spring, n:Sprite, e:Shape;

			for (var i:uint = 0; i<sim.particles.length; ++i) {
				p = sim.particles[i] as Particle;
				n = nodes.getChildAt(i) as Sprite;
				if (n==dragc.activeItem) {
					p.fixed = true;
					p.x = n.x;
					p.y = n.y;
				} else {
					p.fixed = false;
				}
			}

			sim.tick();
			for (i=0; i<sim.particles.length; ++i) {
				p = sim.particles[i] as Particle;
				if (!p.fixed) {
					n = nodes.getChildAt(i) as Sprite;
					n.x = p.x;
					n.y = p.y;
				}
			}
			for (i=0; i<sim.springs.length; ++i) {
				s = sim.springs[i] as Spring;
				e = edges.getChildAt(i) as Shape;
				e.graphics.clear();
				e.graphics.lineStyle(1, 0xdddddd, 0.8);
				e.graphics.moveTo(s.p1.x, s.p1.y);
				e.graphics.lineTo(s.p2.x, s.p2.y);
			}
		}
		
		private function onNodeDown(event:MouseEvent):void {
			(dict[event.target] as Particle).fixed = true;
			Sprite(event.target).startDrag();
		}
			
		private function onNodeUp(event:MouseEvent):void {
			(dict[event.target] as Particle).fixed = false;
			Sprite(event.target).stopDrag();
		}
		
		public function evaluate(t:Number):Boolean
		{
			drawGraph();
			return false;
		}
		
		override public function play():void
		{
			Scheduler.instance.add(this);
		}
		
		override public function stop():void
		{
			Scheduler.instance.remove(this);
		}
		
	}
}