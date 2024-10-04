/*global H5P*/
H5P.Tooltip = (function () {
  'use strict';

  /**
   * Create an accessible tooltip
   *
   * @param {HTMLElement} triggeringElement The element that should trigger the tooltip
   * @param {Object} options Options for tooltip
   * @param {String} options.text The text to be displayed in the tooltip
   *  If not set, will attempt to set text = aria-label of triggeringElement
   * @param {String[]} options.classes Extra css classes for the tooltip
   * @param {Boolean} options.ariaHidden Whether the hover should be read by screen readers or not (default: true)
   * @param {String} options.position Where the tooltip should appear in relation to the
   *  triggeringElement. Accepted positions are "top" (default), "left", "right" and "bottom"
   *
   * @constructor
   */
  function Tooltip(triggeringElement, options) {

    // Make sure tooltips have unique id
    H5P.Tooltip.uniqueId += 1;
    const tooltipId = 'h5p-tooltip-' + H5P.Tooltip.uniqueId;

    // Default options
    options = options || {};
    options.classes = options.classes || [];
    options.ariaHidden = options.ariaHidden || true;

    // Initiate state
    let hover = false;
    let focus = false;

    // Function used by the escape listener
    const escapeFunction = function (e) {
      if (e.key === 'Escape') {
        tooltip.classList.remove('h5p-tooltip-visible');
      }
    }

    // Create element
    const tooltip = document.createElement('div');

    tooltip.classList.add('h5p-tooltip');
    tooltip.id = tooltipId;
    tooltip.role = 'tooltip';
    tooltip.innerHTML = options.text || triggeringElement.getAttribute('aria-label') || '';
    tooltip.setAttribute('aria-hidden', options.ariaHidden);
    tooltip.classList.add(...options.classes);

    triggeringElement.appendChild(tooltip);

    // Set the initial position based on options.position
    switch (options.position) {
      case 'left':
        tooltip.classList.add('h5p-tooltip-left');
        break;
      case 'right':
        tooltip.classList.add('h5p-tooltip-right');
        break;
      case 'bottom':
        tooltip.classList.add('h5p-tooltip-bottom');
        break;
      default:
        options.position = 'top';
    }

    // Aria-describedby will override aria-hidden
    if (!options.ariaHidden) {
      triggeringElement.setAttribute('aria-describedby', tooltipId);
    }

    // Add event listeners to triggeringElement
    triggeringElement.addEventListener('mouseenter', function () {
      showTooltip(true);
    });
    triggeringElement.addEventListener('mouseleave', function () {
      hideTooltip(true);
    });
    triggeringElement.addEventListener('focusin', function () {
      showTooltip(false);
    });
    triggeringElement.addEventListener('focusout', function () {
      hideTooltip(false);
    });

    // Prevent clicks on the tooltip from triggering onClick listeners on the triggeringElement
    tooltip.addEventListener('click', function (event) {
      event.stopPropagation();
    });

    // Use a mutation observer to listen for aria-label being
    // changed for the triggering element. If so, update the tooltip.
    // Mutation observer will be used even if the original elements
    // doesn't have any aria-label.
    new MutationObserver(function (mutations) {
      const ariaLabel = mutations[0].target.getAttribute('aria-label');
      if (ariaLabel) {
        tooltip.innerHTML = options.text || ariaLabel;
      }
    }).observe(triggeringElement, {
      attributes: true,
      attributeFilter: ['aria-label'],
    });

    // Use intersection observer to adjust the tooltip if it is not completely visible
    new IntersectionObserver(function (entries) {
      entries.forEach((entry) => {
        const target = entry.target;
        const positionClass = 'h5p-tooltip-' + options.position;

        // Stop adjusting when hidden (to prevent a false positive next time)
        if (entry.intersectionRatio === 0) {
          ['h5p-tooltip-down', 'h5p-tooltip-left', 'h5p-tooltip-right']
            .forEach(function (adjustmentClass) {
              if (adjustmentClass !== positionClass) {
                target.classList.remove(adjustmentClass);
              }
            });
        }        
        // Adjust if not completely visible when meant to be
        else if (entry.intersectionRatio < 1 && (hover || focus)) {
          const targetRect = entry.boundingClientRect;
          const intersectionRect = entry.intersectionRect;

          // Going out of screen on left side
          if (intersectionRect.left > targetRect.left) {
            target.classList.add('h5p-tooltip-right');
            target.classList.remove(positionClass);
          }
          // Going out of screen on right side
          else if (intersectionRect.right < targetRect.right) {
            target.classList.add('h5p-tooltip-left');
            target.classList.remove(positionClass);
          }

          // going out of top of screen
          if (intersectionRect.top > targetRect.top) {
            target.classList.add('h5p-tooltip-down');
            target.classList.remove(positionClass);
          }
          // going out of bottom of screen
          else if (intersectionRect.bottom < targetRect.bottom) {
            target.classList.add('h5p-tooltip-up');
            target.classList.remove(positionClass);
          }
        }
      });
    }).observe(tooltip);

    /**
     * Makes the tooltip visible and activates it's functionality
     *
     * @param {Boolean} triggeredByHover True if triggered by mouse, false if triggered by focus
     */
    const showTooltip = function (triggeredByHover) {
      if (triggeredByHover) {
        hover = true;
      }
      else {
        focus = true;
      }

      tooltip.classList.add('h5p-tooltip-visible');

      // Add listener to iframe body, as esc keypress would not be detected otherwise
      document.body.addEventListener('keydown', escapeFunction, true);
    }

    /**
     * Hides the tooltip and removes listeners
     *
     * @param {Boolean} triggeredByHover True if triggered by mouse, false if triggered by focus
     */
     const hideTooltip = function (triggeredByHover) {
      if (triggeredByHover) {
        hover = false;
      }
      else {
        focus = false;
      }

      // Only hide tooltip if neither hovered nor focused
      if (!hover && !focus) {
        tooltip.classList.remove('h5p-tooltip-visible');

        // Remove iframe body listener
        document.body.removeEventListener('keydown', escapeFunction, true);
      }
    }

    /**
     * Change the text displayed by the tooltip
     *
     * @param {String} text The new text to be displayed
     *  Set to null to use aria-label of triggeringElement instead
     */
    this.setText = function (text) {
      options.text = text;
      tooltip.innerHTML = options.text || triggeringElement.getAttribute('aria-label') || '';
    };

    /**
     * Retrieve tooltip
     *
     * @return {HTMLElement}
     */
    this.getElement = function () {
      return tooltip;
    };
  }

  return Tooltip;

})();

H5P.Tooltip.uniqueId = -1;
