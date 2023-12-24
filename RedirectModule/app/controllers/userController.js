// userController.js
const express = require("express");
const router = express.Router();
const DatabaseService = require("../services/databaseService");

const databaseService = new DatabaseService();

router.post("/addUser", async (req, res) => {
  const { userId, link } = req.body;

  try {
    await databaseService.addUser(userId, link);
    res.status(201).send("User added successfully");
  } catch (error) {
    console.error("Internal Server Error:", error);
    res.status(500).send("Internal Server Error");
  }
});

module.exports = router;
