import { exception as displayException } from "core/notification";
import Templates from "core/templates";

const context = {
  login: "",
  autograded: true,
};

const Selectors = {
  elements: {
    tempPlaceholder: '[data-element="mod_jupyter/placeholder"]',
  },
};

export const init = ({ login, autograded }) => {
  // This will call the function to load and render our template.
  context.login = login;
  context.autograded = autograded == 1;
  Templates.renderForPromise("mod_jupyter/manage", context)
    // It returns a promise that needs to be resoved.
    .then(({ html, js }) => {
      // Here eventually I have my compiled template, and any javascript that it generated.
      // The templates object has append, prepend and replace functions.
      Templates.replaceNodeContents(
        Selectors.elements.tempPlaceholder,
        html,
        js
      );
    })
    // Deal with this exception (Using core/notify exception function is recommended).
    .catch((error) => displayException(error));
};
