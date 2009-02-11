package flare.display
{	
	/**
	 * Interface for "renderable" objects that can redraw themselves.
	 */
	public interface IRenderable
	{
		/**
		 * Redraw this renderable object.
		 */
		function render():void;
	}
}