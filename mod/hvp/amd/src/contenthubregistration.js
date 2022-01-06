define([], function () {
  return {
    init: function () {
      // Moodle complains if you pass in settings through init (js_call_amd) since they are too large
      const settings = H5PSettings;
      settings.container = document.getElementById('h5p-hub-registration');
      H5PHub.createRegistrationUI(settings);
    },
  };
});
