YUI.add('moodle-mod_lightboxgallery-lightbox', function(Y) {

	/**
	 * Inspired by the original Lightbox, this is a port to YUI.
	 * See Lokesh Dhakar's original at http://www.huddletogether.com/projects/lightbox2/.
	 * Currently supports everything that module supports with plans to integrate
	 * additional functionality (i.e. non-images, slideshow mode, etc.) coming soon.
	 *
	 * @module gallery-lightbox
	 */

	var L = Y.Lang,
		Node = Y.Node,
		PX = "px",
		CLICK = "click",
		ANIM = "anim",
		ACTIVE_IMAGE = "activeImage",
		IMAGE_ARRAY = "imageArray",
		OVERLAY_OPACITY = "overlayOpacity",
		OVERLAY_DURATION = "overlayDuration",
		LIGHTBOX = "lightbox",
		OVERLAY = "overlay",
		PREV_LINK = "prevLink",
		NEXT_LINK = "nextLink",
		HOVER_NAV = "hoverNav",

		// global lightbox instance
		lightboxInstance = null;

	/**** BEGIN EXTENDING THE NODE CLASS ****/

	// Add a few helper methods to the Node class that hopefully will be added
	// in a future release of the Node class.  They simplify showing/hiding a given node
	// by manipulating its "display" style.

	Y.mix(
		Node.prototype, {
			/**
		     * Display a node.
		     *
		     * @method show
		     * @chainable
		     */
			show: function () {
				this.setStyle("display", "");
				return this;
			},

			/**
		     * Hide a node.
		     *
		     * @method hide
		     * @chainable
		     */
			hide: function () {
				this.setStyle("display", "none");
				return this;
			},

			/**
		     * Check is a node is being shown. Specifically not called "visible"
		     * so as not to confuse it with the visibility property.
		     *
		     * @method displayed
		     * @return boolean
		     */
			displayed: function() {
				return this.getStyle("display") != "none";
			},

			/**
		     * Toggle the display of an element.
		     *
		     * @method toggle
		     * @chainable
		     */
			toggle: function() {
				this[this.displayed() ? "hide" : "show"]();
				return this;
			}
		}
	);

	/**** END EXTENDING THE NODE CLASS ****/

	/**
	 * The Lightbox class provides the functionality for displaying
	 * images in a panel above an overlay.  It automatically binds to
	 * all anchor tags determined by the "selector" attribute.  It supports
	 * grouping images together to produce a slideshow like effect.
	 *
	 * @class Lightbox
	 * @constructor
	 * @extends Base
	 * @uses Node
	 * @uses Anim
	 *
	 * @param config {Object} object with configuration property name/value pairs
	 */
	var LB = function (config) {
		LB.superclass.constructor.apply(this, arguments);
	};

	/**
     * The identity of the widget.
     *
     * @property Lightbox.NAME
     * @type String
     * @static
     */
	LB.NAME = LIGHTBOX;

	/**
     * Static property used to define the default attribute configuration of
     * the Widget.
     *
     * @property Lightbox.ATTRS
     * @type Object
     * @protected
     * @static
     */
	LB.ATTRS = {
		/**
         * The selector to determine which anchors should be bound to the Lightbox
         * instance.  If an anchor element is bound to Lightbox, it's content will
         * be displayed in a modal panel rather than on a separate page.
         *
         * @attribute selector
         * @type String
         * @default &quot;a[rel^=lightbox]&quot;
         */
		selector: {
			value: "a[rel^=lightbox]",
			validator: L.isString
		},

		/**
         * The width of the border surrounding the displayed content.  This is used during
         * resize operations.
         *
         * @attribute borderWidth
         * @type Number
         * @default 10
         */
		borderWidth: {
			value: 10,
			validator: L.isNumber
		},

		/**
         * The amount of time (in seconds) for the overlay to take to appear when the
         * Lightbox is displayed.
         *
         * @attribute overlayDuration
         * @type Number
         * @default 0.2
         */
		overlayDuration: {
			value: 0.2,
			validator: L.isNumber
		},

		/**
         * The opacity of the overlay element once it is displayed.  This value is used
         * during animation so that the overlay appears to be eased in.
         *
         * @attribute overlayOpacity
         * @type Number
         * @default 0.8
         */
		overlayOpacity: {
			value: 0.8,
			validator: L.isNumber
		},

		/**
         * The amount of time (in seconds) each reisze animation should take.  This is used
         * specifically during Lightbox height and width resize transformations.
         *
         * @attribute resizeDuration
         * @type Number
         * @default 0.5
         */
		resizeDuration: {
			value: 0.5,
			validator: L.isNumber
		},

		/**
         * Whether or the Lighbox module should use animation when displaying, changing images,
         * and hiding.  If set to false, the values of attributes that control animation settings
         * are ignored.
         *
         * @attribute anim
         * @type boolean
         * @default !L.isUndefined(Y.Anim)
         */
		anim: {
			value: !L.isUndefined(Y.Anim),
			validator: L.isBoolean
		},

		/**
         * A managed array of images that Lightbox can currently cycle through. The size of this array
         * is defined by the number of images in a particular image group.  This array determines
         * whether or not there are next and previous options. It's initialized when an image
         * is clicked on.
         *
         * @attribute imageArray
         * @type Array
         */
		imageArray: {
			validator: L.isArray
		},

		/**
         * The index of the currently displayed image in the "imageArray."
         *
         * @attribute activeImage
         * @type Number
         */
		activeImage: {
			validator: L.isNumber
		},

		/**
         * Set of strings to be used when displaying content.  These can be customized
         * (i.e. for internationalization) if necessary.
         *
         * @attribute strings
         * @type Object
         */
		strings: {
			value : {
				labelImage: "Image",
				labelOf: "of"
			}
		}
	};

	Y.extend(LB, Y.Base, {
		/**
	     * Construction logic executed during Lightbox instantiation. This
	     * builds and inserts the markup necessary for the Lightbox to function
	     * as well as binds all of the elements to the necessary events to make
	     * the Lightbox functional.
	     *
	     * @method initializer
	     * @param config (Object) set of configuration name/value pairs
	     * @protected
	     */
		initializer: function (config) {
			// Code inserts html at the bottom of the page that looks similar to this:
	        //
	        //  <div id="overlay"></div>
	        //  <div id="lightbox">
	        //      <div id="outerImageContainer">
	        //          <div id="imageContainer">
	        //              <img id="lightboxImage">
	        //              <div style="" id="hoverNav">
	        //                  <a href="#" id="prevLink"></a>
	        //                  <a href="#" id="nextLink"></a>
	        //              </div>
	        //              <div id="loading"></div>
	        //          </div>
	        //      </div>
	        //      <div id="imageDataContainer">
	        //          <div id="imageData">
	        //              <div id="imageDetails">
	        //                  <span id="caption"></span>
	        //                  <span id="numberDisplay"></span>
	        //              </div>
	        //              <div id="bottomNav">
	        //                  <a href="#" id="bottomNavClose"></a>
	        //              </div>
	        //          </div>
	        //      </div>
	        //  </div>

	        var objBody = Y.one(document.body),
				create = Node.create;

			objBody.append(create('<div id="overlay"></div>'));

	        objBody.append(create('<div id="lightbox"></div>')
				.append(create('<div id="outerImageContainer"></div>')
					.append(create('<div id="imageContainer"></div>')
						.append(create('<img id="lightboxImage" />'))
						.append(create('<div id="hoverNav"></div>')
							.append(create('<a id="prevLink" href="#"></a>'))
							.append(create('<a id="nextLink" href="#"></a>'))
						)
						.append(create('<div id="loading"></div>'))
					)
				)
				.append(create('<div id="imageDataContainer"></div>')
					.append(create('<div id="imageData"></div>')
						.append(create('<div id="imageDetails"></div>')
							.append(create('<span id="caption"></span>'))
							.append(create('<span id="numberDisplay"></span>'))
						)
						.append(create('<div id="bottomNav"></div>')
							.append(create('<a id="bottomNavClose" href="#"></a>'))
                            .append(create('<a id="bottomNavDownload" href="#"></a>'))
						)
					)
				)
			);

			this._bindStartListener();

			Y.one("#overlay").hide().on(CLICK, function () { this.end(); }, this);
			Y.one("#lightbox").hide().on(CLICK, function (evt) {
				if (evt.currentTarget.get("id") === LIGHTBOX) {
					this.end();
				}
			}, this);

			var size = (this.get(ANIM) ? 250 : 1) + PX;

			Y.one("#outerImageContainer").setStyles({ width: size, height: size });
			Y.one("#prevLink").on(CLICK, function (evt) { evt.halt(); this._changeImage(this.get(ACTIVE_IMAGE) - 1); }, this);
			Y.one("#nextLink").on(CLICK, function (evt) { evt.halt(); this._changeImage(this.get(ACTIVE_IMAGE) + 1); }, this);
			Y.one("#bottomNavClose").on(CLICK, function (evt) { evt.halt(); this.end(); }, this);
            Y.one('#bottomNavDownload').on(CLICK, function (evt) {
                evt.halt();
                var active = this.get(ACTIVE_IMAGE);
                window.open(this.get(IMAGE_ARRAY)[active][0] + '?forcedownload=1', '_blank');

            }, this);

			L.later(0, this, function () {
				var ids = "overlay lightbox outerImageContainer imageContainer lightboxImage hoverNav prevLink nextLink loading " +
                	"imageDataContainer imageData imageDetails caption numberDisplay bottomNav bottomNavClose";

				Y.Array.each(ids.split(" "), function (element, index, array) {
					this.addAttr(element, { value: Y.one("#" + element) });
				}, this);
			});
		},

		/**
	     * Display overlay and Lightbox.  If image is part of a set, it
	     * adds those images to an array so that a user can navigate between them.
	     *
	     * @method start
	     * @param selectedLink { Y.Node } the node whose content should be displayed
	     * @protected
	     */
		start: function (selectedLink) {
			Y.all("select, object, embed").each(function() {
				this.setStyle("visibility", "hidden");
			});

			// Stretch overlap to fill page and fade in
			var overlay = this.get(OVERLAY).setStyles({ height: Y.DOM.docHeight() + PX, width: Y.DOM.docWidth() + PX }).show();
			this.get(LIGHTBOX).show();
			if (this.get(ANIM)) {
				var anim = new Y.Anim({
					node: overlay,
					from: { opacity: 0 },
					to: { opacity: this.get(OVERLAY_OPACITY) },
					duration: this.get(OVERLAY_DURATION)
				});
				anim.run();
			} else {
				overlay.setStyle("opacity", this.get(OVERLAY_OPACITY));
			}

			var imageArray = [],
				imageNum = 0;

			if (selectedLink.get("rel") === LIGHTBOX) {
				// If image is NOT part of a set, add single image to imageArray
				imageArray.push([selectedLink.get("href"), selectedLink.get("title")]);
			} else {
				// If image is part of a set...
                targetstring = '.lightbox-gallery a[href][rel="lightbox_gallery"]';
				Y.all(targetstring).each(function () {
					imageArray.push([this.get("href"), this.get("title")]);
				});

				while (imageArray[imageNum][0] !== selectedLink.get("href")) { imageNum++; }
			}

			this.set(IMAGE_ARRAY, imageArray);
			border = this.get("borderWidth");
			var lightboxTop = Y.DOM.docScrollY() + border*2,
				lightboxLeft = Y.DOM.docScrollX();
			this.get(LIGHTBOX).setStyles({ display: "", top: lightboxTop + PX, left: lightboxLeft + PX });

			this._changeImage(imageNum);
		},

		/**
	     * Hide the overlay and Lightbox and unbind any event listeners.
	     *
	     * @method end
	     * @protected
	     */
		end: function () {
			this._disableKeyboardNav();
			this.get(LIGHTBOX).hide();

			var overlay = this.get(OVERLAY);

			if (this.get(ANIM)) {
				var anim = new Y.Anim({
					node: overlay,
					from: { opacity: this.get(OVERLAY_OPACITY) },
					to: { opacity: 0 },
					duration: this.get(OVERLAY_DURATION)
				});
				anim.on("end", function () { overlay.hide(); });
				anim.run();
			} else {
				overlay.setStyles({ opacity: 0 }).hide();
			}

			Y.all("select, object, embed").each(function() {
				this.setStyle("visibility", "visible");
			});
		},

		/**
	     * Helper method responsible for binding listener to the page to process
	     * lightbox anchors and images.
	     *
	     * @method _bindStartListener
	     * @private
	     */
		_bindStartListener: function () {
			Y.delegate(CLICK, Y.bind(function (evt) {
				evt.halt();
				this.start(evt.currentTarget);
			}, this), Y.one(".lightbox-gallery"), this.get("selector"));
		},

		/**
	     * Display the selected index by first showing a loading screen, preloading it
	     * and displaying it once it has been loaded.
	     *
	     * @method _changeImage
	     * @param imageNum { Number } the index of the image to be displayed
	     * @private
	     */
		_changeImage: function (imageNum) {
			this.set(ACTIVE_IMAGE, imageNum);

			// Hide elements during transition
			if (this.get(ANIM)) {
				this.get("loading").show();
			}
			this.get("lightboxImage").hide();
			this.get(HOVER_NAV).hide();
			this.get(PREV_LINK).hide();
			this.get(NEXT_LINK).hide();

			// Hack: Opera9 doesn't support something in scriptaculous opacity and appear fx
			// TODO: Do I need this since we are using YUI? Is this a scriptaculous/Opera
			// bug, or just Opera bug?
			this.get("imageDataContainer").setStyle("opacity", 0.0001);
			this.get("numberDisplay").hide();

			var imagePreloader = new Image();

			// Once image is preloaded, resize image container
			imagePreloader.onload = Y.bind(function () {
				this.get("lightboxImage").set("src", this.get(IMAGE_ARRAY)[imageNum][0]);

                // Get current viewport width and height
                viewportWidth = Y.DOM.winWidth();
                viewportHeight = Y.DOM.winHeight();
                imageresize = Y.one('#region-main .autoresize');
                if (imageresize){
                    border = this.get("borderWidth");
                    widthRatio = (viewportWidth-border*6)/imagePreloader.width;
                    heightRatio = (viewportHeight-border*9)/imagePreloader.height;

                    if (widthRatio > 1 && heightRatio > 1){
                        bestRatio = 1;
                    } else if (widthRatio < heightRatio){
                        var bestRatio = widthRatio;
                    } else {
                        var bestRatio = heightRatio;
                    }

                    imgWidth = imagePreloader.width*bestRatio;
                    imgHeight = imagePreloader.height*bestRatio;
                } else {
                    imgWidth = imagePreloader.width;
                    imgHeight = imagePreloader.height;
                }


				this._resizeImageContainer(imgWidth, imgHeight);
			}, this);
			imagePreloader.src = this.get(IMAGE_ARRAY)[imageNum][0];
		},

		/**
	     * Resize the image container so it is large enough to display the entire image.
	     * Once this is complete it will delegate to another method to actually display the image.
	     *
	     * @method _resizeImageContainer
	     * @param imgWidth { Number } image width
	     * @param imgWidth { Number } image height
	     * @private
	     */
		_resizeImageContainer: function (imgWidth, imgHeight) {

            // Get current width and height
            	var outerImageContainer = this.get("outerImageContainer"),
				widthCurrent = outerImageContainer.get("offsetWidth"),
				heightCurrent = outerImageContainer.get("offsetHeight"),

       		// Get new width and height
				widthNew = imgWidth + this.get("borderWidth") * 2,
				heightNew = imgHeight + this.get("borderWidth") * 2,

			// calculate size difference between new and old image
				wDiff = widthCurrent - widthNew,
				hDiff = heightCurrent - heightNew,

				afterResize = Y.bind(function () {
					this.get(PREV_LINK).setStyles({ height: imgHeight + PX });
					this.get(NEXT_LINK).setStyles({ height: imgHeight + PX });
					this.get("imageDataContainer").setStyles({ width: widthNew + PX });

					this._showImage(imgWidth, imgHeight);
				}, this);

			if (wDiff !== 0 || hDiff !== 0) {
				if (this.get(ANIM)) {
					var resizeDuration = this.get("resizeDuration"),

					anim = new Y.Anim({
						node: outerImageContainer,
						from: { width: widthCurrent + PX },
						to: { width: widthNew + PX },
						duration: resizeDuration
					}),

					onEnd = function () {
						anim.getEvent("end").detach(onEnd);
						this.setAttrs({
							from: { height: heightCurrent + PX },
							to: { height: heightNew + PX },
							duration: resizeDuration
						});
						this.on("end", afterResize);
						this.run();
					};

					anim.on("end", onEnd);

					anim.run();
				} else {
					outerImageContainer.setStyles({ width: widthNew + PX, height: heightNew + PX});
					L.later(0, this, afterResize);
				}
			} else {
				// If new and old image are the same size, and no scaling is necessary,
				// do a quick pause to prevent image flicker.
				L.later(100, this, afterResize);
			}
		},

		/**
	     * Display the currently loaded image and then try to preload any neighboring images.
	     *
	     * @method _showImage
	     * @private
	     */
		_showImage: function (imgWidth, imgHeight) {
			this.get("loading").hide();

			var lightBoxImage = this.get("lightboxImage");

			if (this.get(ANIM)) {

				var startOpacity = lightBoxImage.getStyle("display") === "none" ? 0 : lightBoxImage.getStyle("opacity") || 0,
					anim = new Y.Anim({
						node: lightBoxImage,
						from: { opacity: startOpacity },
						to: { opacity: 1 }
					});

				anim.on("end", this._updateDetails, this);
                lightBoxImage.setStyle("width", imgWidth+'px');
                lightBoxImage.setStyle("height", imgHeight+'px');
				lightBoxImage.setStyle("opacity", startOpacity).show();
				anim.run();
			} else {
				lightBoxImage.setStyle("opacity", 1).show();
				this._updateDetails();
			}

			this._preloadNeighborImages();
		},

		/**
	     * Use the title of the image as a caption and display information
	     * about the current image and it's location in an image set (if applicable).
	     *
	     * @method _updateDetails
	     * @private
	     */
		_updateDetails: function () {

			var imageArray = this.get(IMAGE_ARRAY),
				activeImage = this.get(ACTIVE_IMAGE),
				caption = imageArray[activeImage][1];

			// If caption is not null
			if (caption !== "") {
				this.get("caption").setContent(caption).show();
			}

			// If image is part of a set display "Image x of x"
			if (imageArray.length > 1) {
				this.get("numberDisplay").setContent(this.get("strings.labelImage") + " " + (activeImage + 1) + " " + this.get("strings.labelOf") + "  " + imageArray.length).show();
			}

			var imageDataContainer = this.get("imageDataContainer");

			if (this.get(ANIM)) {

				var startOpacity = imageDataContainer.getStyle("display") === "none" ? 0 : imageDataContainer.getStyle("opacity") || 0,
					anim = new Y.Anim({
						node: imageDataContainer,
						from: { opacity: startOpacity },
						to: { opacity: 1 },
						duration: this.get("resizeDuration")
					});

				anim.on("end", function () {
					// Update overlay size and update nav
					this.get(OVERLAY).setStyle("height", Y.DOM.docHeight() + PX);
					this._updateNav();
				}, this);

				imageDataContainer.setStyle("opacity", startOpacity).show();
				anim.run();
			} else {

				imageDataContainer.setStyle("opacity", 1).show();
				this.get(OVERLAY).setStyle("height", Y.DOM.docHeight() + PX);
				this._updateNav();
			}
		},

		/**
	     * Update the navigation elements to display forward and/or backward
	     * links if they're appropriate.
	     *
	     * @method _updateNav
	     * @private
	     */
		_updateNav: function () {
			var activeImage = this.get(ACTIVE_IMAGE);

			this.get(HOVER_NAV).show();

			// If not first image in set, display previous image button
			if (activeImage > 0) {
				this.get(PREV_LINK).show();
			}

			// If not first image in set, display previous image button
			if (activeImage < (this.get(IMAGE_ARRAY).length - 1)) {
				this.get(NEXT_LINK).show();
			}

			this._enableKeyboardNav();
		},

		/**
	     * Enable keyboard shortcuts for closing Lightbox or switching images.
	     *
	     * @method _enableKeyboardNav
	     * @private
	     */
		_enableKeyboardNav: function () {
			Y.one(document.body).on("keydown", this._keyboardAction, this);
		},

		/**
	     * Disable keyboard shortcuts for closing Lightbox or switching images.
	     *
	     * @method _disableKeyboardNav
	     * @private
	     */
		_disableKeyboardNav: function () {
			Y.one(document.body).unsubscribe("keydown", this._keyboardAction);
		},

		/**
	     * Handle key strokes to allow for users to close Lightbox or switch images.
	     *
	     * @method _keyboardAction
	     * @private
	     */
		_keyboardAction: function (evt) {
			var keyCode = evt.keyCode,
				escapeKey = 27,
				key = String.fromCharCode(keyCode).toLowerCase();

			if (key.match(/x|o|c/) || (keyCode === escapeKey)) { // close lightbox
				this.end();
			} else if ((key === 'p') || (keyCode === 37)) { // Display the previous image
				if (this.get(ACTIVE_IMAGE) !== 0) {
					this._disableKeyboardNav();
					this._changeImage(this.get(ACTIVE_IMAGE) - 1);
				}
			} else if ((key === 'n') || (keyCode === 39)) { // Display the next image
				if (this.get(ACTIVE_IMAGE) !== (this.get(IMAGE_ARRAY).length - 1)) {
					this._disableKeyboardNav();
					this._changeImage(this.get(ACTIVE_IMAGE) + 1);
				}
			}
		},

		/**
	     * Preload images that are adjacent to the current image, if they exist,
	     * to reduce waiting time.
	     *
	     * @method _preloadNeighborImages
	     * @private
	     */
		_preloadNeighborImages: function () {
			var activeImage = this.get(ACTIVE_IMAGE),
				imageArray = this.get(IMAGE_ARRAY),
				preloadNextImage, preloadPrevImage;

			if (imageArray.length > activeImage + 1) {
				preloadNextImage = new Image();
				preloadNextImage.src = imageArray[activeImage + 1][0];
			}

			if (activeImage > 0) {
				preloadPrevImage = new Image();
				preloadPrevImage.src = imageArray[activeImage - 1][0];
			}
		}
	});

	Y.Lightbox = {
		/**
		 * This method returns the single, global LightBox instance.  Upon creation,
		 * the Lightbox instance attaches itself to the page and is ready to be used.
		 *
		 * @method init
		 * @return { Lightbox } global instance
		 * @static
		 */
		init: function(config) {
			if (lightboxInstance === null) {
				lightboxInstance = new LB(config);
			}
			return lightboxInstance;
		}
	};

    M.mod_lightboxgallery = M.mod_lightboxgallery || {
        init:function() {
            /*YUI().use("gallery-lightbox", function (Y) {   */

                var already = Y.one('#lightbox');
                if (already == null){
					Y.Lightbox.init();
                }

            /*} */
        }
    };

}, '@VERSION@' ,{requires:['base', 'node','anim']});
