package flare.vis.operator.encoder
{
	import flare.vis.data.Data;
	import flare.vis.palette.Palette;
	import flare.vis.palette.SizePalette;
	import flare.vis.scale.ScaleType;
	
	/**
	 * Encodes a data field into size values, using a scale transform and a
	 * size palette to determines an item's scale. The target property of a
	 * SizeEncoder is assumed to be the <code>DataSprite.size</code> property.
	 */
	public class SizeEncoder extends Encoder
	{
		private var _palette:SizePalette;
		
		/** @inheritDoc */
		public override function get palette():Palette { return _palette; }
		public override function set palette(p:Palette):void {
			_palette = p as SizePalette;
		}
		/** The palette as a SizePalette instance. */
		public function get sizes():SizePalette { return _palette; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new SizeEncoder.
		 * @param source the source property
		 * @param which flag indicating which group of visual object to process
		 * @param scaleType the type of scale to use (QUANTILE by default)
		 * @param scaleParam a parameter for creating the scale (5 by default)
		 */
		public function SizeEncoder(source:String=null, which:int=1/*Data.NODES*/,
			scaleType:String=ScaleType.QUANTILE, scaleParam:Number=5)
		{
			super(source, "size", which);
			_scaleType = scaleType;
			_scaleParam = scaleParam;
			_palette = new SizePalette();
			_palette.is2D = (which != Data.EDGES);
		}
		
		/** @inheritDoc */
		protected override function encode(val:Object):*
		{
			return _palette.getSize(_scale.interpolate(val));
		}
		
	} // end of class SizeEncoder
}