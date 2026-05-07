/* global H5P */
H5P.Tooltip = (function () {
  // Position (allowed and default)
  const Position = {
    allowed: ['top', 'bottom', 'left', 'right'],
    default: 'top',
  };

  /** {number} DELAY_SHOW_MS Delay before tooltip is shown */
  const DELAY_SHOW_MS = 500;

  /** {number} DELAY_HIDE_MS Delay before tooltip is hidden */
  const DELAY_HIDE_MS = 500;

  /**
   * Strips html tags and converts special characters.
   * Example: "<div>Me &amp; you</div>" is converted to "Me & you".
   *
   * @param {String} text The text to be parsed
   * @returns {String} The parsed text
   */
  function parseString(text) {
    if (text === null || text === undefined) {
      return '';
    }
    const div = document.createElement('div');
    div.innerHTML = text;
    return div.textContent;
  }

  /**
   * Keep track of whether the user is using their mouse or keyboard to
   * navigate. Will determine whether tooltip should be shown on focus.
   */
  let usingMouse;

  function debounce(callback, delay) {
    let timeout = null;

    return function (...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        callback(...args);
      }, delay);
    };
  }

  // The mousemove listener is debounced for performance reasons
  document.addEventListener('mousemove', debounce(() => usingMouse = true, 100));
  document.addEventListener('mousedown', () => usingMouse = true);
  document.addEventListener('keydown', () => usingMouse = false);

  /**
   * Create an accessible tooltip
   *
   * @param {HTMLElement} triggeringElement The element that should trigger the tooltip
   * @param {Object} options Options for tooltip
   * @param {String} options.text The text to be displayed in the tooltip
   *  If not set, will attempt to set text = options.tooltipSource of triggeringElement
   * @param {String[]} options.classes Extra css classes for the tooltip
   * @param {Boolean} options.ariaHidden Whether the hover should be read by screen readers or not (default: true)
   * @param {String} options.position Where the tooltip should appear in relation to the
   *  triggeringElement. Accepted positions are "top" (default), "left", "right" and "bottom"
   * @param {String} options.tooltipSource
   *
   * @returns {object} returns all the public functions
   *
   * @constructor
   */

  function Tooltip(triggeringElement, options) {
    // Make sure tooltips have unique id
    H5P.Tooltip.uniqueId += 1;
    const tooltipId = `h5p-tooltip-${H5P.Tooltip.uniqueId}`;

    // Default options
    options = options || {};
    options.classes = options.classes || [];
    options.ariaHidden = options.ariaHidden || true;
    options.tooltipSource = options.tooltipSource || 'aria-label';
    options.position = (options.position && Position.allowed.includes(options.position))
      ? options.position
      : Position.default;

    // Add our internal classes
    options.classes.push('h5p-tooltip');
    if (options.position === 'left' || options.position === 'right') {
      options.classes.push('h5p-tooltip-narrow');
    }

    // Initiate state
    let hover = false;
    let focus = false;

    // Function used by the escape listener
    const hideOnEscape = function (event) {
      if (event.key === 'Escape') {
        tooltip.classList.remove('h5p-tooltip-visible');
      }
    };

    // Create element
    const tooltip = document.createElement('div');
    tooltip.id = tooltipId;
    tooltip.role = 'tooltip';
    tooltip.textContent = parseString(options.text || triggeringElement.getAttribute(options.tooltipSource) || '');
    tooltip.setAttribute('aria-hidden', options.ariaHidden);
    tooltip.classList.add(...options.classes);

    document.body.appendChild(tooltip);

    // Aria-describedby will override aria-hidden
    if (!options.ariaHidden) {
      triggeringElement.setAttribute('aria-describedby', tooltipId);
    }

    // Use a mutation observer to listen for options.tooltipSource being
    // changed for the triggering element. If so, update the tooltip.
    // Mutation observer will be used even if the original elements
    // doesn't have any options.tooltipSource.
    this.observer = new MutationObserver((mutations) => {
      const updatedText = mutations[0].target.getAttribute(options.tooltipSource);

      if (tooltip.parentNode === null) {
        triggeringElement.appendChild(tooltip);
      }

      tooltip.textContent = parseString(options.text || updatedText);

      if (tooltip.textContent.trim().length === 0 && tooltip.classList.contains('h5p-tooltip-visible')) {
        tooltip.classList.remove('h5p-tooltip-visible');
      }
    });
    this.observer.observe(triggeringElement, {
      attributes: true,
      attributeFilter: [options.tooltipSource, 'class'],
    });

    // A reference to the H5P container (if any). If null, it means
    // this tooltip is not whithin an H5P.
    let h5pContainer;

    // Timer responsible for displaying the tooltip x ms after it has been
    // triggered (either by mouseenter or focusin)
    let showTooltipTimer;

    // Timer responsible for hiding the tooltip x ms after it has been untriggered
    let hideTooltipTimer;

    // This timer makes sure the tooltip is not hidden when the mouse
    // moves from the trigger to the tooltip.
    let triggerMouseLeaveTimer;

    /**
     * Makes the tooltip visible and activates it's functionality
     *
     * @param {UIEvent} event The triggering event
     */
    const showTooltip = function (event, wait = true) {
      if (!event.target || event.target.disabled || event.target.getAttribute('aria-disabled') === 'true') {
        return;
      }

      clearTimeout(hideTooltipTimer); // Prevent from hiding while supposed to show

      if (wait === true) {
        // We don't want to show the tooltip right away.
        // Adding a 300 ms waiting period here.
        clearTimeout(showTooltipTimer);
        showTooltipTimer = setTimeout(() => {
          showTooltip(event, false);
        }, DELAY_SHOW_MS);
        return;
      }

      // Don't show tooltip if it is empty
      if (tooltip.textContent.trim().length === 0) {
        return;
      }

      if (event.type === 'mouseenter') {
        hover = true;
      }
      else {
        focus = true;
      }

      // Reset placement
      tooltip.style.left = '';
      tooltip.style.top = '';

      tooltip.classList.add('h5p-tooltip-visible');

      // Add listener to iframe body, as esc keypress would not be detected otherwise
      document.body.addEventListener('keydown', hideOnEscape, true);

      // The section below makes sure the tooltip is completely visible

      // H5P.Tooltip can be used both from within an H5P and elsewhere.
      // The below code is for figuring out the containing element.
      // h5pContainer has to be looked up the first time we show the tooltip,
      // since it might not be added to the DOM when H5P.Tooltip is invoked.
      if (h5pContainer === undefined) {
        // After the below, h5pContainer is either null or a reference to the
        // DOM element
        h5pContainer = triggeringElement.closest('.h5p-container');
      }
      const rootRect = h5pContainer ? h5pContainer.getBoundingClientRect() : document.documentElement.getBoundingClientRect();
      const triggerRect = triggeringElement.getBoundingClientRect();
      let tooltipRect = tooltip.getBoundingClientRect();

      if (options.position === 'top') {
        // Places it centered above
        tooltip.style.left = `${triggerRect.left + (triggerRect.width / 2) - (tooltipRect.width / 2)}px`;
        tooltip.style.top = `${triggerRect.top - tooltipRect.height}px`;
      }
      else if (options.position === 'bottom') {
        // Places it centered below
        tooltip.style.left = `${triggerRect.left + (triggerRect.width / 2) - (tooltipRect.width / 2)}px`;
        tooltip.style.top = `${triggerRect.bottom}px`;
      }
      else if (options.position === 'left') {
        tooltip.style.left = `${triggerRect.left - tooltipRect.width}px`;
        tooltip.style.top = `${triggerRect.top + (triggerRect.height - tooltipRect.height) / 2}px`;
        // We trust this option makes the tooltip being shown
        return;
      }
      else if (options.position === 'right') {
        tooltip.style.left = `${triggerRect.right}px`;
        tooltip.style.top = `${triggerRect.top + (triggerRect.height - tooltipRect.height) / 2}px`;
        // We trust this option makes the tooltip being shown
        return;
      }

      tooltipRect = tooltip.getBoundingClientRect();
      const isVisible = tooltipRect.left >= 0
        && tooltipRect.top >= 0
        && tooltipRect.right <= rootRect.width
        && tooltipRect.bottom <= rootRect.height;

      if (!isVisible) {
        // The tooltip placement needs to be adjusted. This logic will move the
        // tooltip either left or right if it's placed outside the root element
        tooltipRect = tooltip.getBoundingClientRect();
        if (tooltipRect.left < 0) {
          tooltip.style.left = 0;
        }
        else if (tooltipRect.right > rootRect.width) {
          tooltip.style.left = '';
          tooltip.style.right = 0;
        }
      }
    };

    /**
     * Hides the tooltip and removes listeners
     *
     * @param {UIEvent} event The triggering event
     */
    const hideTooltip = function (event) {
      let hide = false;
      let wait = false;

      if (event.type === 'click') {
        hide = true;
      }
      else {
        if (event.type === 'mouseleave') {
          wait = true; // Tooltip should not disappear right away
          hover = false;
        }
        else {
          focus = false;
        }

        hide = (!hover && !focus);
      }

      // Only hide tooltip if neither hovered nor focused
      if (hide) {
        clearTimeout(showTooltipTimer); // Prevent from showing while supposed to hide

        const cleanupTooltip = () => {
          tooltip.classList.remove('h5p-tooltip-visible');
          document.body.removeEventListener('keydown', hideOnEscape, true); // Remove iframe body listener
        };

        if (wait) {
          clearTimeout(hideTooltipTimer);
          hideTooltipTimer = setTimeout(() => {
            cleanupTooltip();
          }, DELAY_HIDE_MS);
        }
        else {
          cleanupTooltip();
        }
      }
    };

    // Add event listeners to triggeringElement
    triggeringElement.addEventListener('mouseenter', showTooltip);
    triggeringElement.addEventListener('mouseleave', (event) => {
      triggerMouseLeaveTimer = setTimeout(() => {
        hideTooltip(event);
      }, 1);
    });
    triggeringElement.addEventListener('focusin', (event) => {
      if (!usingMouse) {
        showTooltip(event);
      }
    });
    triggeringElement.addEventListener('focusout', hideTooltip);
    triggeringElement.addEventListener('click', hideTooltip);
    tooltip.addEventListener('mouseenter', () => {
      clearTimeout(triggerMouseLeaveTimer);
    });
    tooltip.addEventListener('mouseleave', hideTooltip);

    tooltip.addEventListener('click', (event) => {
      // Prevent clicks on the tooltip from triggering click
      // listeners on the triggering element
      event.stopPropagation();
      event.preventDefault();

      // Hide the tooltip when it is clicked
      hideTooltip(event);
    });

    /**
     * Change the text displayed by the tooltip
     *
     * @param {String} text The new text to be displayed
     *  Set to null to use options.tooltipSource of triggeringElement instead
     */
    this.setText = function (text) {
      options.text = text;
      tooltip.textContent = parseString(options.text || triggeringElement.getAttribute(options.tooltipSource) || '');
    };

    /**
     * Hide the tooltip
     */
    this.hide = function () {
      hover = focus = false;
      tooltip.classList.remove('h5p-tooltip-visible');
    };

    /**
     * Retrieve tooltip
     *
     * @return {HTMLElement}
     */
    this.getElement = function () {
      return tooltip;
    };

    /**
     * Remove tooltip
     */
    this.remove = function () {
      this.observer?.disconnect();
      tooltip.remove();
    };

    return {
      setText: this.setText,
      hide: this.hide,
      getElement: this.getElement,
      remove: this.remove,
      observer: this.observer,
    };
  }

  return Tooltip;
}());

H5P.Tooltip.uniqueId = -1;
