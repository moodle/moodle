import {MoodleRes} from "./moodle.res";
import {MoodleResKey} from "./moodle-res-key";

export interface CachedMoodleRes {
  timeCreated: number;
  key: MoodleResKey;
  result: MoodleRes[];
}
