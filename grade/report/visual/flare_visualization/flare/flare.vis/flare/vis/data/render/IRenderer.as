package flare.vis.data.render
{
	import flare.vis.data.DataSprite;
	
	/**
	 * Interface for DataSprite rendering modules.
	 */
	public interface IRenderer
	{
		/**
		 * Renders drawing content for the input DataSprite.
		 * @param d the DataSprite to draw
		 */
		function render(d:DataSprite):void;
		
	} // end of interface IRenderer
}