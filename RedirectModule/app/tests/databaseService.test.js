// databaseService.test.js
const DatabaseService = require("../services/databaseService");
const mariadb = require("mariadb");

// Mocking mariadb.createPool
jest.mock("mariadb");

describe("DatabaseService", () => {
  let dbService;

  beforeEach(() => {
    // Mocking createPool to return a mock pool
    mariadb.createPool.mockReturnValue({
      getConnection: jest.fn(),
    });

    dbService = new DatabaseService();
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  describe("getUserLink", () => {
    it("should retrieve user link from the database", async () => {
      // Mocking the query function
      dbService.query = jest
        .fn()
        .mockResolvedValue([{ link: "https://example.com/auth?userId=" }]);

      const userId = 1;
      const result = await dbService.getUserLink(userId);

      expect(result).toBe("https://example.com/auth?userId=");
      expect(dbService.query).toHaveBeenCalledWith(expect.any(String), [
        userId,
      ]);
    });

    it("should return null if user link is not found", async () => {
      // Mocking the query function to return an empty array
      dbService.query = jest.fn().mockResolvedValue([]);

      const userId = 1;
      const result = await dbService.getUserLink(userId);

      expect(result).toBeNull();
      expect(dbService.query).toHaveBeenCalledWith(expect.any(String), [
        userId,
      ]);
    });

    it("should throw an error if there is an issue with the query", async () => {
      // Mocking the query function to throw an error
      dbService.query = jest
        .fn()
        .mockRejectedValue(new Error("Database error"));

      const userId = 1;
      await expect(dbService.getUserLink(userId)).rejects.toThrow(
        "Database error"
      );
      expect(dbService.query).toHaveBeenCalledWith(expect.any(String), [
        userId,
      ]);
    });
  });

  describe("addUser", () => {
    it("should add a new user to the database", async () => {
      // Mocking the query function
      dbService.query = jest.fn().mockResolvedValue([]);

      const userId = 1;
      const link = "https://example.com/auth?userId=";

      await dbService.addUser(userId, link);

      expect(dbService.query).toHaveBeenCalledWith(expect.any(String), [
        userId,
        link,
      ]);
    });

    it("should throw an error if there is an issue with the query", async () => {
      // Mocking the query function to throw an error
      dbService.query = jest
        .fn()
        .mockRejectedValue(new Error("Database error"));

      const userId = 1;
      const link = "https://example.com/auth?userId=";

      await expect(dbService.addUser(userId, link)).rejects.toThrow(
        "Database error"
      );
      expect(dbService.query).toHaveBeenCalledWith(expect.any(String), [
        userId,
        link,
      ]);
    });
  });
});
