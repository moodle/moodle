'use strict';

const Status = {
  NOT_CONNECTED: 1,
  CONNECTING: 2,
  AUTHENTICATING: 3,
  INIT_CMD: 4,
  CONNECTED: 5,
  CLOSING: 6,
  CLOSED: 7
};

module.exports.Status = Status;
