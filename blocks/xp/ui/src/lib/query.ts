import { QueryClient } from "react-query";
import { getModule } from "./moodle";

export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60,
      onError: (err) => getModule("core/notification").exception(err),
    },
    mutations: {
      onError: (err) => getModule("core/notification").exception(err),
    },
  },
});
