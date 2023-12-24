// redirectController.js
const express = require("express");
const router = express.Router();
const DatabaseService = require("../services/databaseService");

const databaseService = new DatabaseService();

router.get("/:userId", async (req, res) => {
  const userId = req.params.userId;

  try {
    const userLink = await databaseService.getUserLink(userId);

    if (!userLink) {
      return res.status(404).send("User not found");
    }

    res.redirect(userLink);
  } catch (error) {
    console.error("Internal Server Error:", error);
    res.status(500).send("Internal Server Error");
  }
});

module.exports = router;
