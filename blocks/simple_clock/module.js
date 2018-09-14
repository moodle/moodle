M.block_simple_clock = {
    serverClockShown: true,
    userClockShown: true,
    showSeconds: false,
    showDay: false,
    twentyFourHourTime: false,
    timeDifference: 0,

    initSimpleClock: function (YUIObject, server, user, seconds, day, tfh, y,mo,d,h,m,s) {
        var serverTimeStart = new Date(y,mo,d,h,m,s);
        var currentTime = new Date();

        // Set up object properties
        this.timeDifference = currentTime.getTime() - serverTimeStart.getTime();
        this.serverClockShown = server;
        this.userClockShown = user;
        this.showSeconds = seconds;
        this.showDay = day;
        this.twentyFourHourTime = tfh;

        // Refresh clock display in 1 second
        this.updateTime();
    },

    updateTime: function () {
        var serverTime;
        var youTime;

        // Update the server clock if shown
        if(this.serverClockShown) {
            serverTime = new Date();
            serverTime.setTime(serverTime.getTime() - this.timeDifference);
            document.getElementById('block_progress_serverTime').value = this.getClockString(serverTime);
        }

        // Update the user clock if shown
        if(this.userClockShown) {
            youTime = new Date();
            document.getElementById('block_progress_youTime').value = this.getClockString(youTime);
        }

        // Refresh clock display in 1 second
        setTimeout('M.block_simple_clock.updateTime()',1000);
    },

    getClockString: function (clockTime) {
        var clockString = '';
        var day = M.str.block_simple_clock.day_names.split(',')[clockTime.getDay()];
        var hours = clockTime.getHours();
        var minutes = clockTime.getMinutes();
        var seconds = clockTime.getSeconds();

        // Add the day name
        if(this.showDay) {
            clockString += day + ' ';
        }

        // Add the hours
        if(this.twentyFourHourTime) {
            clockString += hours;
        }
        else if(hours > 12) {
            clockString += hours - 12;
        }
        else if (hours == 0) {
            clockString += 12;
        }
        else {
            clockString += hours;
        }

        // Append a separator
        clockString += M.str.block_simple_clock.clock_separator;

        // Add the minutes
        if(minutes < 10) {
            clockString += '0';
        }
        clockString += minutes;

        // Add the seconds if desired
        if(this.showSeconds) {
            clockString += M.str.block_simple_clock.clock_separator;
            if(seconds < 10) {
                clockString += '0';
            }
            clockString += seconds;
        }

        // Add the am/pm suffix if not using 24hr time
        if(!this.twentyFourHourTime) {
            if(hours < 12) {
                clockString += M.str.block_simple_clock.before_noon;
            }
            else {
                clockString += M.str.block_simple_clock.after_noon;
            }
        }

        return clockString;
    }
};