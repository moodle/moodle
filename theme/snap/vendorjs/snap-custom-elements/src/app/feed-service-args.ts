import {MoodleResArgs} from "./moodle-res-args";

export class FeedServiceArgs extends MoodleResArgs {
  feedid: string;
  page: number;
  pagesize: number;
  courseId: number;

  getHash(): string | number {
    let compoundKey = `${this.feedid}_${this.page}_${this.pagesize}`;
    if (this.courseId) {
      compoundKey = `${compoundKey}_${this.courseId}`;
    }
    return this.stringToHash(compoundKey);
  }
}
