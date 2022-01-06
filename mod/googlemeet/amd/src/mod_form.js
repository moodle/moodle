define([
  'core/notification',
  'core/str',
  'mod_googlemeet/gapi'
], function(notification, str, gapi) {
  return {
    init: function(clientId, apiKey, userTimeZone) {
      // Array of API discovery doc URLs for APIs used by the quickstart
      var discoveryDocs = ["https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest"];

      // Authorization scopes required by the API
      var scope = 'https://www.googleapis.com/auth/calendar.events';

      // Load strings
      var requiredfield = '';
      var strcheckweekdays = '';
      var invalideventendtime = '';
      var invalideventenddate = '';
      var timeahead = '';
      str.get_strings([
        {key: 'requirednamefield', component: 'mod_googlemeet'},
        {key: 'checkweekdays', component: 'mod_googlemeet'},
        {key: 'invalideventendtime', component: 'mod_googlemeet'},
        {key: 'invalideventenddate', component: 'mod_googlemeet'},
        {key: 'timeahead', component: 'mod_googlemeet'},
      ]).done(function(strs) {
        requiredfield = strs[0];
        strcheckweekdays = strs[1];
        invalideventendtime = strs[2];
        invalideventenddate = strs[3];
        timeahead = strs[4];
      }).fail(notification.exception);

      // Elements references
      var generateUrlRoomButton = document.getElementById('id_generateurlroom');
      var urlFieldHidden = document.getElementById('id_url');
      var urlViewerField = document.getElementById('id_url_viewer');
      var originalNameFieldHidden = document.getElementById('id_originalname');
      var creatorEmailFieldHidden = document.getElementById('id_creatoremail');
      var form = document.querySelector('#region-main .mform');
      /**
       *  Initializes the API client library and sets up sign-in state
       *  listeners.
       */
      function initClient() {
        gapi.client.init({
          apiKey: apiKey,
          clientId: clientId,
          discoveryDocs: discoveryDocs,
          scope: scope
        }).then(function() {
          generateUrlRoomButton.onclick = handleCreateEvent;
          generateUrlRoomButton.disabled = false;
          return;
        }).catch(function(error) {
          generateUrlRoomButton.disabled = true;
          appendPre(JSON.stringify(error, null, 2));
        });
      }

      /**
       * Returns date formatted in yyyy-mm-dd format
       *
       * @param {string} name name of date filed in formdata
       * @param {boolean} format if it is to return the formatted date
       * @return {string} The formatted date
       */
      function getDateString(name, format) {
        var formData = new FormData(form);
        var year = formData.get(name + '[year]');
        var month = formData.get(name + '[month]');
        var day = formData.get(name + '[day]');

        month = ('0' + month).slice(-2);
        day = ('0' + day).slice(-2);

        if (format) {
          return year + '-' + month + '-' + day;
        }

        return year + month + day;
      }

      /**
       * Checks whether the selected day of the week exists within the start and end dates
       * @return {boolean}
       */
      function checkweekdays() {
        var formData = new FormData(form);

        var starthour = formData.get('starthour');
        var startminute = formData.get('startminute');
        var endhour = formData.get('endhour');
        var endminute = formData.get('endminute');

        starthour = ('0' + starthour).slice(-2);
        startminute = ('0' + startminute).slice(-2);
        endhour = ('0' + endhour).slice(-2);
        endminute = ('0' + endminute).slice(-2);

        var starttime = starthour + ':' + startminute + ':00';
        var endtime = endhour + ':' + endminute + ':00';

        var eventdate = getDateString('eventdate', true) + ' ' + starttime;
        var eventenddate = getDateString('eventenddate', true) + ' ' + endtime;

        var daysofweek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        var start = new Date(eventdate);
        var end = new Date(eventenddate);

        var found = false;
        while (start <= end) {
          if (!found) {
            for (var i = 0; i <= daysofweek.length; i++) {
              var currentday = daysofweek[start.getDay()];
              var formday = formData.get('days[' + daysofweek[i] + ']');
              if (formday && currentday === daysofweek[i]) {
                found = true;
                break;
              }
            }
          }
          start = new Date(start.setDate(start.getDate() + 1));
        }

        return found;
      }

      /**
       * Validates form data
       * @return {boolean}
       */
      function validate() {
        var valid = true;
        var formData = new FormData(form);

        // The name is required
        var nameInput = document.getElementById('id_name');
        var nameError = document.getElementById('id_error_name');
        if (formData.get('name').trim().length === 0) {
          nameInput.classList.add('is-invalid');
          nameInput.focus();
          nameError.innerText = '- ' + requiredfield;
          nameError.style.display = "block";
          return false;
        } else {
          nameInput.classList.remove('is-invalid');
          nameError.innerText = '';
          nameError.style.display = "none";
        }

        // The end time cannot be less than the start time
        var eventTimeError = document.getElementById('id_googlemeet_eventtime_error');
        var starttime = formData.get('starthour') * 3600 + formData.get('startminute') * 60;
        var endtime = formData.get('endhour') * 3600 + formData.get('endminute') * 60;
        if (endtime < starttime) {
          eventTimeError.innerText = invalideventendtime;
          eventTimeError.style.display = "block";
          document.getElementById('id_endhour').focus();
          return false;
        } else {
          eventTimeError.innerText = '';
          eventTimeError.style.display = "none";
        }

        // The end date cannot be less than the start date
        var eventdate = Math.floor(new Date(getDateString('eventdate', true)).getTime() / 1000);
        var eventenddate = Math.floor(new Date(getDateString('eventenddate', true)).getTime() / 1000);
        var eventenddateerror = document.getElementById('id_googlemeet_eventenddategroup_error');
        if (
          formData.get('addmultiply') &&
          eventdate !== 0 &&
          eventenddate !== 0 &&
          eventenddate < eventdate
        ) {
          eventenddateerror.innerText = invalideventenddate;
          eventenddateerror.style.display = "block";
          document.getElementById('id_eventenddate_day').focus();
          return false;
        } else if (
          // The event period cannot be longer than one year
          formData.get('addmultiply') &&
          Math.ceil((eventenddate - eventdate) / 31536000) > 1
        ) {
          eventenddateerror.innerText = timeahead;
          eventenddateerror.style.display = "block";
          document.getElementById('id_eventenddate_day').focus();
          return false;
        } else {
          eventenddateerror.innerText = '';
          eventenddateerror.style.display = "none";
        }

        // The days of the week must fall within the date range of the event.
        if (formData.get('addmultiply')) {
          var dayserror = document.getElementById('id_googlemeet_days_error');
          if (!checkweekdays()) {
            dayserror.innerText = strcheckweekdays;
            dayserror.style.display = "block";
            document.getElementById('id_days_Mon').focus();
            return false;
          } else {
            dayserror.innerText = '';
            dayserror.style.display = "none";
          }
        }

        return valid;
      }

      /**
       * Initializes the creation of the event in Google Calendar
       */
      function handleCreateEvent() {
        hidePre();

        if (!validate()) {
          return;
        }

        gapi.auth2.getAuthInstance().signIn({prompt: 'select_account'}).then(function() {
          createEvent();
          return;
        }).catch();
      }

      /**
       * Displays loading
       *
       * @param {boolean} show if it is to show loading
       */
      function showLoading(show) {
        var generateurlroomLoading = document.getElementById('generateurlroomLoading');

        if (show) {
          generateurlroomLoading.style.display = "block";
          generateUrlRoomButton.disabled = true;
        } else {
          generateurlroomLoading.style.display = "none";
          generateUrlRoomButton.disabled = false;
        }
      }

      /**
       * Concatenates with name a random number
       *
       * @param {string} name
       * @return {string} The formatted name
       */
      function formatName(name) {
        var random = (Math.floor(Math.random() * (9999 - 1111)) + 1111);
        return name + ' (' + random + ')';
      }

      /**
       * Append a pre-element to the body that contains the message
       * provided as its text node. Used to display API call errors.
       *
       * @param {string} message Text to be placed in pre element.
       */
      function appendPre(message) {
        var pre = document.getElementById('googlemeetcontentlog');
        var textContent = document.createTextNode(message + '\n');
        pre.style.display = "block";
        pre.appendChild(textContent);
      }

      /**
       * Hide the pre tag
       */
      function hidePre() {
        var pre = document.getElementById('googlemeetcontentlog');
        pre.style.display = "none";
        pre.innerHTML = "";
      }

      /**
       * Creates the event on Google Calendar
       */
      function createEvent() {
        var formData = new FormData(form);

        var starthour = formData.get('starthour');
        var startminute = formData.get('startminute');
        var endhour = formData.get('endhour');
        var endminute = formData.get('endminute');

        starthour = ('0' + starthour).slice(-2);
        startminute = ('0' + startminute).slice(-2);
        endhour = ('0' + endhour).slice(-2);
        endminute = ('0' + endminute).slice(-2);

        var eventstarttime = starthour + ':' + startminute + ':00';
        var eventendtime = endhour + ':' + endminute + ':00';

        var start = {
          dateTime: getDateString('eventdate', true) + 'T' + eventstarttime,
          timeZone: userTimeZone
        };

        var end = {
          dateTime: getDateString('eventdate', true) + 'T' + eventendtime,
          timeZone: userTimeZone
        };

        var recurrence = [];

        if (formData.get('addmultiply')) {
          var interval = 'INTERVAL=' + formData.get('period');
          var until = 'UNTIL=' + getDateString('eventenddate', false) + 'T235959Z';
          var byday = 'BYDAY=';

          var daysofweek = {
            Sun: 'SU',
            Mon: 'MO',
            Tue: 'TU',
            Wed: 'WE',
            Thu: 'TH',
            Fri: 'FR',
            Sat: 'SA'
          };

          for (var day in daysofweek) {
            if (formData.get('days[' + day + ']')) {
              byday += daysofweek[day] + ',';
            }
          }

          recurrence = [
            'RRULE:FREQ=WEEKLY;' + interval + ';' + until + ';' + byday
          ];
        }

        var name = formatName(formData.get('name'));
        var eventResource = {
          summary: name,
          description: formData.get('introeditor[text]'),
          start: start,
          end: end,
          recurrence: recurrence
        };

        showLoading(true);

        gapi.client.calendar.events.insert({
          'calendarId': 'primary',
          'resource': eventResource
        }).then(function(response) {
          var event = response.result;

          var eventPatch = {
            conferenceData: {
              createRequest: {requestId: event.id}
            }
          };

          gapi.client.calendar.events.patch({
            calendarId: "primary",
            eventId: event.id,
            resource: eventPatch,
            sendNotifications: false,
            conferenceDataVersion: 1
          }).then(function(response) {
            var event = response.result;

            generateUrlRoomButton.remove();
            originalNameFieldHidden.value = name;
            urlFieldHidden.value = event.hangoutLink;
            urlViewerField.value = event.hangoutLink;
            creatorEmailFieldHidden.value = event.creator.email;

            document.getElementById('id_googlemeet_generateurlgroup_error').style.display = 'none';

            showLoading(false);
            return;
          }).catch(function(error) {
            appendPre(JSON.stringify(error.result.error, null, 2));
            showLoading(false);
          });
          return;
        }).catch(function(error) {
          appendPre(JSON.stringify(error.result.error, null, 2));
          showLoading(false);
        });
      }

      /**
       *  On load, called to load the auth2 library and API client library.
       */
      gapi.load('client:auth2', initClient);
    }
  };
});
