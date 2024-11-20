import { submitNotebook } from "./repository";
import { exception as displayException } from "core/notification";
import Templates from "core/templates";

const context = {
  message: "error message jajadajajajajajajajaaj",
  closebutton: 0,
  announce: 1,
  points: [],
  error: false,
  errortype: "",
  gradelink: ""
};

const Selectors = {
  elements: {
    submitResponseBody: '[data-element="mod_jupyter/body-placeholder"]',
  },
  actions: {
    submitButton: '[data-action="mod_jupyter/submit-notebook_button"]',
    resetModal: '[data-action="mod_jupyter/reset-modal_button"]',
  },
};


/**
 * Add event listeners to Selectors.
 * @param {*} param0
 */
export const init = async ({ user, courseid, instanceid, filename, token, gradelink }) => {
  document.addEventListener("click", (e) => {
    if (e.target.closest(Selectors.actions.submitButton)) {
      resetModalBody();
      callSubmitNotebook(user, courseid, instanceid, filename, token, gradelink);
    }
  });

  document.addEventListener("click", (e) => {
    if (e.target.closest(Selectors.actions.resetModal)) {
      resetModalBody();
    }
  });
};


/**
 * Call external service from repository to submit notebook to grading service and display graded response.
 *
 * @param {string} user
 * @param {int} courseid
 * @param {int} instanceid
 * @param {string} filename
 * @param {string} token
 * @param {string} gradelink
 */
const callSubmitNotebook = async (
  user,
  courseid,
  instanceid,
  filename,
  token,
  gradelink
) => {
  const response = await submitNotebook(
    user,
    courseid,
    instanceid,
    filename,
    token
  );

  window.console.log(response);
  if (response[0].error) {
    context.error = response[0].error;
    context.message = response[0].errormessage;
    renderErrorNotification();
  } else {
    context.error = false;
    context.points = response;
    context.gradelink = gradelink;
    renderModalTable();
  }


};

/**
 * Render table inside the submit modal to show submit response.
 */
const renderErrorNotification = (
) => {
  Templates.renderForPromise('core/notification_error', context)
    // It returns a promise that needs to be resoved.
    .then(({ html, js }) => {
      // Here eventually I have my compiled template, and any javascript that it generated.
      // The templates object has append, prepend and replace functions.
      Templates.replaceNodeContents(
        Selectors.elements.submitResponseBody,
        html,
        js
      );
    })
    // Deal with this exception (Using core/notify exception function is recommended).
    .catch((error) => displayException(error));
};

/**
 * Render table inside the submit modal to show submit response.
 */
const renderModalTable = (
) => {
  Templates.renderForPromise("mod_jupyter/submit_response_modal_table", context)
    // It returns a promise that needs to be resoved.
    .then(({ html, js }) => {
      // Here eventually I have my compiled template, and any javascript that it generated.
      // The templates object has append, prepend and replace functions.
      Templates.replaceNodeContents(
        Selectors.elements.submitResponseBody,
        html,
        js
      );
    })
    // Deal with this exception (Using core/notify exception function is recommended).
    .catch((error) => displayException(error));
};

/**
 * Replace table with loading template for reset.
 */
const resetModalBody = async (
) => {
  Templates.renderForPromise("mod_jupyter/loading", context)
    // It returns a promise that needs to be resoved.
    .then(({ html, js }) => {
      // Here eventually I have my compiled template, and any javascript that it generated.
      // The templates object has append, prepend and replace functions.
      Templates.replaceNodeContents(
        Selectors.elements.submitResponseBody,
        html,
        js
      );
    })
    // Deal with this exception (Using core/notify exception function is recommended).
    .catch((error) => displayException(error));
};
