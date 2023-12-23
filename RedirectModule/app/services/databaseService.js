// databaseService.js
const { mariadbConfig } = require("../config");
const mariadb = require("mariadb");
const crypto = require("crypto");

class DatabaseService {
  constructor() {
    this.pool = mariadb.createPool(mariadbConfig);
  }

  async query(sql, params) {
    let connection;
    try {
      connection = await this.pool.getConnection();
      const results = await connection.query(sql, params);
      return results;
    } finally {
      if (connection) {
        connection.release();
      }
    }
  }

  async getUserLink(userId) {
    const sql = "SELECT link FROM user_links WHERE user_id = ?";
    const params = [userId];

    try {
      const results = await this.query(sql, params);
      return results.length === 0 ? null : results[0].link;
    } catch (error) {
      console.error("Error querying the database:", error);
      throw error;
    }
  }

  async addUser(userId, link) {
    const sql = `
      INSERT INTO user_links (user_id, link)
      VALUES (?, ?)
      ON DUPLICATE KEY UPDATE link = VALUES(link)
    `;
    link = link + this.hashUserId(userId);
    const params = [userId, link];

    try {
      await this.query(sql, params);
    } catch (error) {
      console.error("Error inserting/updating into the database:", error);
      throw error;
    }
  }

  secretKey =
    "YBUXSVAS9xWhLuWrlo79u6F4oltdBKZTzfRp1vIDQm0OVBoHdIfWAvBFq4Vr9WPZPELKkDte6rPmDLQBCEx0ayU3jkpf9A0RNhb6HpIcWZDwrtPZVbXF1WxMRhNd5FW2RtDMTMxOL1CVdfZ4WeflodqIalWWjUvm7FYgebxpdDMRebJnZuIT9qAuZKCAOpzdpuUJvGWnYdNMkMe2LqWj6kGf0w01kdQy8XY2whPJ7rPucpLQXwlM2oVQvYcZ1aId";

  hashUserId(userId) {
    // Create an HMAC-SHA256 hash using the secret key
    const hmac = crypto.createHmac("sha256", this.secretKey);
    hmac.update(userId);

    // Get the digest in Base64 representation
    const base64Digest = hmac.digest("base64");

    return encodeURIComponent(base64Digest);
  }
}

module.exports = DatabaseService;
