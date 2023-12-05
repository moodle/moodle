// server.js
const express = require("express");
const https = require("https");
const redirectController = require("./controllers/redirectController");
const userController = require("./controllers/userController");
const { credentials, serverConfig } = require("./config");

const app = express();
const serverPort = serverConfig.port;

const httpsServer = https.createServer(credentials, app);

app.use(express.json());

// Use controllers
app.use("/api/redirect", redirectController);
app.use("/api/user", userController);

httpsServer.listen(serverPort, () => {
  console.log(`Server is running on https://localhost:${serverPort}`);
});
