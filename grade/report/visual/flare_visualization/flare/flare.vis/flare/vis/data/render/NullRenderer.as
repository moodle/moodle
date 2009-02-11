package flare.vis.data.render
{
	import flare.vis.data.DataSprite;

	/**
	 * Renderer that draws nothing.
	 */
	public class NullRenderer implements IRenderer
	{
		private static const _instance:NullRenderer = new NullRenderer();
		
		/** Static NullRenderer instance. */
		public static function get instance():NullRenderer { return _instance; }
		
		/** @inheritDoc */
		public function render(d:DataSprite):void
		{
			d.graphics.clear();
		}
		
	} // end of class NullRenderer
}