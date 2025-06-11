export class MoodleRes {
  error: boolean | string;
  data?: any[];
  errorcode?: string;
  exception?: ExceptionRes;
  debuginfo?: string;
  reproductionlink?: string;
  stacktrace?: string;
}

export class ExceptionRes {
  errorcode?: string;
  link?: string;
  message?: string;
  moreinfourl?: string;
}
