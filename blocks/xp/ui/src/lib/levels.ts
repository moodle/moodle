import { PointCalculationMethod, Level } from "./types";

export function computeRequiredPoints(level: number, base: number, coef: number) {
  if (level <= 1) {
    return 0;
  } else if (level == 2) {
    return base;
  }

  if (coef <= 1) {
    return base * (level - 1);
  }

  return Math.round(base * ((1 - Math.pow(coef, level - 1)) / (1 - coef)));
}

export function computeRequiredPointsWithMethod(level: number, method: PointCalculationMethod) {
  if (level <= 1) {
    return 0;
  } else if (level === 2) {
    return method.base;
  }

  if (method.method === "relative") {
    // Refer to the original method that was algorithmic.
    return computeRequiredPoints(level, method.base, method.coef);
  } else if (method.method === "linear") {
    // Each level is worth the base + increment (starting at level 3);
    // Level 1: 0; level 2: 100; Level 3: 210 (100 + (100 + 10)); Level 4: 330 (100 + (100 + 10) + (100 + 10 + 10));
    return (
      method.base * (level - 1) +
      Array.from({ length: level }).reduce<number>((carry, _, idx) => carry + Math.max(0, idx - 1) * method.incr, 0)
    );
  }

  // Flat method.
  return (level - 1) * method.base;
}

export const getLevel = (levels: Level[], level: number): Level | undefined => {
  return levels[Math.max(0, level - 1)];
};

export const getMinimumPointsForLevel = (levels: Level[], level: Level) => {
  if (level.level === 1 || !levels.length) {
    return 0;
  }
  return getPreviousLevel(levels, level).xprequired + 1;
};

export const getMinimumPointsAtLevel = (levels: Level[], level: number) => {
  const l = getLevel(levels, level - 1);
  return l ? l.xprequired + 1 : 0;
};

export const getNextLevel = (levels: Level[], level: Level, highest: number = 9999): Level | undefined => {
  let index = Math.min(highest, Math.max(levels.indexOf(level) + 1, 0));
  return levels[index];
};

export const getPreviousLevel = (levels: Level[], level: Level) => {
  return levels[Math.max(levels.indexOf(level) - 1, 0)];
};
