package flare.vis.operator.encoder
{
	import flare.vis.palette.Palette;
	import flare.vis.palette.ShapePalette;
	import flare.vis.scale.OrdinalScale;
	import flare.vis.scale.Scale;
	import flare.vis.scale.ScaleType;
	
	/**
	 * Encodes a data field into shape values, using an ordinal scale.
	 * Shape values are integer indices that map into a shape palette, which
	 * provides drawing routines for shapes. See the
	 * <code>flare.palette.ShapePalette</code> and 
	 * <code>flare.data.render.ShapeRenderer</code> classes for more.
	 */
	public class ShapeEncoder extends Encoder
	{
		private var _palette:ShapePalette;
		
		/** @inheritDoc */
		public override function get palette():Palette { return _palette; }
		public override function set palette(p:Palette):void {
			_palette = p as ShapePalette;
		}
		/** The palette as a ShapePalette instance. */
		public function get shapes():ShapePalette { return _palette; }
		public function set shapes(p:ShapePalette):void { _palette = p; }
		
		// --------------------------------------------------------------------
		
		/** @inheritDoc */
		public override function set scaleType(st:String):void {
			if (st != ScaleType.CATEGORIES)
				throw new ArgumentError(
					"Shape encoders only use the CATEGORIES scale type");
		}
		
		/** @inheritDoc */
		public override function set scale(s:Scale):void {
			if (!(s is OrdinalScale))
				throw new ArgumentError("Shape encoders only use OrdinalScales");
		}
		
		/**
		 * Creates a new ShapeEncoder.
		 * @param source the source property
		 * @param which flag indicating which group of visual object to process
		 */
		public function ShapeEncoder(field:String=null, which:int=1/*Data.NODES*/)
		{
			super(field, "shape", which);
			_palette = ShapePalette.defaultPalette();
			_scaleType = ScaleType.CATEGORIES;
		}
		
		/** @inheritDoc */
		protected override function encode(val:Object):*
		{
			return (_scale as OrdinalScale).index(val);
		}
		
	} // end of class ShapeEncoder
}