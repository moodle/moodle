// config.js
const dotenv = require("dotenv");

// #region Environment Variables

// Load environment variables from .env file
dotenv.config();

const serverPort =
  process.env.NODE_ENV === "production"
    ? process.env.SERVER_PROD_PORT
    : process.env.SERVER_DEV_PORT;

const dbHost =
  process.env.NODE_ENV === "production"
    ? process.env.DB_PROD_HOST
    : process.env.DB_DEV_HOST;

const dbPort =
  process.env.NODE_ENV === "production"
    ? process.env.DB_PROD_PORT
    : process.env.DB_DEV_PORT;

const dbUser =
  process.env.NODE_ENV === "production"
    ? process.env.DB_PROD_USER
    : process.env.DB_DEV_USER;

const dbPassword =
  process.env.NODE_ENV === "production"
    ? process.env.DB_PROD_PASSWORD
    : process.env.DB_DEV_PASSWORD;

// #endregion

const fs = require("fs");
const path = require("path");

const privateKeyPath = path.join(__dirname, "../ssl/server.key");
const certificatePath = path.join(__dirname, "../ssl/server.crt");

const privateKey = fs.readFileSync(privateKeyPath, "utf8");
const certificate = fs.readFileSync(certificatePath, "utf8");

const credentials = { key: privateKey, cert: certificate };

const sslOptions = { key: privateKey, cert: certificate };

const mariadbConfig = {
  host: dbHost,
  port: dbPort,
  user: dbUser,
  password: dbPassword,
  database: "users",
  connectionLimit: 10000,
  waitForConnections: true,
  connectionTimeout: 10000,
};

const serverConfig = {
  port: serverPort,
};

module.exports = { credentials, mariadbConfig, serverConfig };
