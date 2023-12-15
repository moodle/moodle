// databaseService.js
const { mariadbConfig } = require("../config");
const mariadb = require("mariadb");

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
    const params = [userId, link];

    try {
      await this.query(sql, params);
    } catch (error) {
      console.error("Error inserting/updating into the database:", error);
      throw error;
    }
  }
}

module.exports = DatabaseService;
