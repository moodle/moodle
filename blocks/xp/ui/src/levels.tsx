import { Menu } from "@headlessui/react";
import React, { useEffect, useMemo, useReducer } from "react";
import ReactDOM from "react-dom";
import { QueryClientProvider, useMutation } from "react-query";
import { AddonRequired, IfAddonActivatedOrPromoEnabled } from "./components/Addon";
import { BulkEditPointsModal, BulkEditPointsState } from "./components/BulkEditPoints";
import { AnchorButton, Button, SaveButton } from "./components/Button";
import Expandable from "./components/Expandable";
import { Bars3BottomLeftIcon, CheckBadgeIconSolid, LanguageIcon, PaperAirplaneIconSolid } from "./components/Icons";
import Input, { Select, Textarea } from "./components/Input";
import Level, { getLevelHtml } from "./components/Level";
import { NumInput, NumberInputWithButtons } from "./components/NumberInput";
import Str from "./components/Str";
import { Tooltip } from "./components/Tooltip";
import { HELP_URL_LEVELS } from "./lib/constants";
import { AddonContext, makeAddonContextValueFromAppProps } from "./lib/contexts";
import { useAddonActivated, useStrings, useUnloadCheck } from "./lib/hooks";
import { computeRequiredPointsWithMethod, getMinimumPointsForLevel, getNextLevel, getPreviousLevel } from "./lib/levels";
import { ajaxRequest, commonStaticModulesToDependOn, getModule, getModuleAsync, makeDependenciesDefinition } from "./lib/moodle";
import { queryClient } from "./lib/query";
import { Level as LevelType, LevelsInfo, PointCalculationMethod } from "./lib/types";
import { classNames, stripTags } from "./lib/utils";

type State = {
  algo: PointCalculationMethod;
  levels: LevelType[];
  nblevels: number;
  pendingSave: boolean;
};

enum BADGE_TYPE {
  Site = 1,
  Course = 2,
}

const optionsStates = [
  {
    id: "name",
    Icon: LanguageIcon,
    yes: "hasname",
    no: "hasnoname",
    checker: (level: LevelType) => level.name && level.name.trim().length > 0,
  },
  {
    id: "description",
    Icon: Bars3BottomLeftIcon,
    yes: "hasdescription",
    no: "hasnodescription",
    checker: (level: LevelType) => level.description && level.description.trim().length > 0,
  },
  {
    id: "popupmessage",
    Icon: PaperAirplaneIconSolid,
    yes: "haspopupmessage",
    no: "hasnopopupmessage",
    checker: (level: LevelType) => level.popupmessage && level.popupmessage.trim().length > 0,
  },
  {
    id: "badgeaward",
    Icon: CheckBadgeIconSolid,
    yes: "hasbadgeaward",
    no: "hasnobadgeaward",
    checker: (level: LevelType) => Boolean(level.badgeawardid),
  },
];

const optionsStatesStringIds = optionsStates.map((o) => o.yes).concat(optionsStates.map((o) => o.no));

const getInitialState = ({ levelsInfo }: { levelsInfo: LevelsInfo }): State => {
  return {
    algo: {
      ...levelsInfo.algo,
      method: levelsInfo.algo.method || "relative",
      incr: levelsInfo.algo.incr || 30,
    },
    levels: levelsInfo.levels.map((level) => ({ ...level })),
    nblevels: levelsInfo.levels.length,
    pendingSave: false,
  };
};

const markPendingSave = (state: State): State => {
  return { ...state, pendingSave: true };
};

const updateLevelPoints = (state: State): State => {
  return {
    ...state,
    levels: state.levels.reduce<State["levels"]>((carry, level, i) => {
      return carry.concat([
        { ...level, xprequired: Math.max(level.xprequired, getMinimumPointsForLevel(carry.concat([level]), level)) },
      ]);
    }, []),
  };
};

const reducer = (state: State, [action, payload]: [string, any]): State => {
  let nextLevel;
  switch (action) {
    case "bulkEdit":
      return markPendingSave({
        ...state,
        algo: payload,
        levels: state.levels.map((level) => ({
          ...level,
          xprequired: computeRequiredPointsWithMethod(level.level, payload),
        })),
      });
    case "levelDescChange":
      return markPendingSave({
        ...state,
        levels: state.levels.map((level) => {
          if (level !== payload.level) {
            return level;
          }
          return { ...level, description: stripTags(payload.desc) || null };
        }),
      });
    case "levelNameChange":
      return markPendingSave({
        ...state,
        levels: state.levels.map((level) => {
          if (level !== payload.level) {
            return level;
          }
          return { ...level, name: stripTags(payload.name) || null };
        }),
      });
    case "levelBadgeAwardIdChange":
      return markPendingSave({
        ...state,
        levels: state.levels.map((level) => {
          if (level !== payload.level) {
            return level;
          }
          return { ...level, badgeawardid: payload.badgeawardid || null };
        }),
      });
    case "levelPopupMessageChange":
      return markPendingSave({
        ...state,
        levels: state.levels.map((level) => {
          if (level !== payload.level) {
            return level;
          }
          return { ...level, popupmessage: payload.popupmessage || null };
        }),
      });
    case "levelPointsChange":
      nextLevel = getNextLevel(state.levels, payload.level, state.nblevels);
      if (isNaN(payload.points) || payload.points <= 2 || payload.points >= Infinity) {
        return state;
      } else if (payload.points <= getPreviousLevel(state.levels, payload.level).xprequired) {
        return state;
      }
      return markPendingSave(
        updateLevelPoints({
          ...state,
          levels: state.levels.map((level) => {
            if (level !== payload.level) {
              return level;
            }
            return { ...level, xprequired: payload.points };
          }),
        })
      );
    case "nbLevelsChange":
      if (typeof payload?.n === "undefined" || isNaN(payload.n) || payload.n < 2 || payload.n > 99) {
        return state;
      }
      return markPendingSave({
        ...state,
        nblevels: payload.n,
        levels: state.levels.concat(
          Array.from({ length: Math.max(0, payload.n - state.levels.length) }).map((_, i) => {
            const l = state.levels.length + i + 1;
            return {
              level: l,
              name: null,
              description: null,
              badgeurl: (payload?.defaultBadgeUrls || {})[l] || null,
              xprequired: computeRequiredPointsWithMethod(l, state.algo),
            };
          })
        ),
      });
    case "markSaved":
      return {
        ...state,
        pendingSave: false,
      };
  }
  return state;
};

const OptionField: React.FC<{ label: React.ReactNode; note?: React.ReactNode; xpPlusRequired?: boolean }> = ({
  label,
  children,
  note,
  xpPlusRequired,
}) => {
  return (
    <div>
      <label className="xp-m-0 xp-block xp-font-normal">
        <div className="xp-flex">
          <div className="xp-grow xp-uppercase xp-text-xs">{label}</div>
          <div>{xpPlusRequired ? <AddonRequired /> : null}</div>
        </div>
        <div className="xp-mt-1">{children}</div>
      </label>
      {note ? <div className="xp-text-gray-500 xp-mt-1">{note}</div> : null}
    </div>
  );
};

const showLevelUpNotificationPreview = async (level: LevelType, prevLevel: LevelType) => {
  const PopupModule = await getModuleAsync("block_xp/popup-notification");
  PopupModule.show({
    courseid: 0,
    levelnum: level.level,
    levelname: level.name,
    levelbadge: getLevelHtml(level),
    prevlevelbadge: getLevelHtml(prevLevel),
    message: level.popupmessage,
  });
};

export const App = ({ courseId, levelsInfo, resetToDefaultsUrl, defaultBadgeUrls, badges = [] }: AppProps) => {
  const hasXpPlus = useAddonActivated();
  const [state, dispatch] = useReducer(reducer, { levelsInfo }, getInitialState);
  const levels = state.levels.slice(0, state.nblevels);
  const [expanded, setExpanded] = React.useState<number[]>([]);
  const [bulkEdit, setBulkEdit] = React.useState(false);
  const getStr = useStrings(optionsStatesStringIds.concat(["levelssaved", "unknownbadgea", "levelx", "previewpopupnotification"]));
  const getBadgeStr = useStrings(["coursebadges", "sitebadges"], "core_badges");
  const getCoreStr = useStrings(["other", "none"], "core");

  useUnloadCheck(state.pendingSave);

  // Prepare the save mutation.
  const mutation = useMutation(() => {
    // An falsy course ID means admin config.
    const method = courseId ? "block_xp_set_levels_info" : "block_xp_set_default_levels_info";
    return ajaxRequest(method, {
      courseid: courseId ? courseId : undefined,
      levels: levels.map((level) => {
        const { level: levelnum, xprequired, ...metadata } = level;
        return {
          level: levelnum,
          xprequired: xprequired,
          metadata: Object.entries(metadata).reduce<{}[]>((carry, [name, value]) => carry.concat([{ name, value }]), []),
        };
      }),
      algo: state.algo,
    });
  });

  // Reset mutation after success.
  useEffect(() => {
    if (!mutation.isSuccess) {
      return;
    }
    const t = setTimeout(() => {
      mutation.reset();
    }, 2500);
    return () => clearTimeout(t);
  });

  const siteBadges = useMemo(
    () => badges.filter((b) => b.type === BADGE_TYPE.Site).sort((a, b) => a.name.localeCompare(b.name)),
    [badges]
  );
  const courseBadges = useMemo(
    () => badges.filter((b) => b.type === BADGE_TYPE.Course).sort((a, b) => a.name.localeCompare(b.name)),
    [badges]
  );

  const allExpanded = expanded.length === levels.length;
  const handleCollapseExpandAll = () => {
    setExpanded(allExpanded ? [] : levels.map((l) => l.level));
  };

  const handleSave = () => {
    mutation.mutate(undefined, {
      onSuccess: () => {
        const Toast = getModule("core/toast");
        Toast && Toast.add(getStr("levelssaved"));
        dispatch(["markSaved", true]);
      },
    });
  };

  const handleBulkEdit = (state: BulkEditPointsState) => {
    dispatch(["bulkEdit", state]);
  };

  const handleNumLevelsChange = (n: number) => {
    dispatch(["nbLevelsChange", { n, defaultBadgeUrls }]);
  };

  const handleLevelDescChange = (level: LevelType, desc: string) => {
    if (level.description === desc) return;
    dispatch(["levelDescChange", { level, desc: desc }]);
  };

  const handleLevelNameChange = (level: LevelType, name: string) => {
    if (level.name === name) return;
    dispatch(["levelNameChange", { level, name: name }]);
  };

  const handleXpChange = (level: LevelType, xp: number) => {
    if (level.xprequired === xp) return;
    dispatch(["levelPointsChange", { level, points: xp }]);
  };

  return (
    <div className="xp-flex xp-flex-col">
      <div className="xp-mb-4 xp-flex xp-items-end xp-justify-end xp-flex-wrap xp-gap-4">
        <div className="xp-flex xp-flex-1 xp-gap-4 xp-items-end xp-flex-wrap">
          <div className="">
            <label htmlFor="label-x" className="xp-block xp-m-0">
              <Str id="numberoflevels" />
            </label>
            <NumberInputWithButtons
              value={state.nblevels}
              onChange={handleNumLevelsChange}
              min={2}
              max={99}
              inputProps={{ id: "label-x", maxLength: 2 }}
            />
          </div>
          <div className="">
            <Button onClick={() => setBulkEdit(true)}>
              <Str id="quickeditpoints" />
            </Button>
            <BulkEditPointsModal
              show={bulkEdit}
              onClose={() => setBulkEdit(false)}
              onSave={handleBulkEdit}
              method={state.algo.method}
              coef={state.algo.coef}
              base={state.algo.base}
              incr={state.algo.incr}
            />
          </div>
        </div>
        <div className="xp-flex xp-gap-1">
          <SaveButton
            statePosition="before"
            onClick={handleSave}
            mutation={mutation}
            disabled={!state.pendingSave || mutation.isLoading}
          />
          <Menu as="div" className="xp-relative xp-inline-block xp-text-left">
            <div>
              <Menu.Button className="xp-text-inherit xp-bg-transparent xp-border-0 xp-p-2 xp-flex xp-items-center xp-rounded-full hover:xp-bg-gray-100">
                <span className="sr-only">
                  <Str id="options" component="core" />
                </span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  className="xp-w-5 xp-h-5"
                  aria-hidden="true"
                >
                  <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                </svg>
              </Menu.Button>
            </div>

            <Menu.Items className="xp-absolute xp-right-0 xp-z-10 xp-mt-2 xp-w-56 xp-origin-top-right xp-rounded-md xp-bg-white xp-border xp-border-solid xp-border-gray-300 xp-shadow-sm xp-divide-y xp-divide-gray-100">
              <div className="xp-py-1">
                <Menu.Item>
                  {({ active, close }) => (
                    <a
                      href="#"
                      role="button"
                      onClick={(e) => {
                        e.preventDefault();
                        handleCollapseExpandAll();
                        close();
                      }}
                      className={classNames(
                        active ? "xp-bg-gray-100" : null,
                        "xp-text-inherit xp-block xp-px-6 xp-py-1 xp-no-underline"
                      )}
                    >
                      {allExpanded ? <Str id="collapseall" component="core" /> : <Str id="expandall" component="core" />}
                    </a>
                  )}
                </Menu.Item>
                <Menu.Item>
                  {({ active, close }) => (
                    <a
                      href={HELP_URL_LEVELS}
                      target="_blank"
                      rel="noopener"
                      className={classNames(
                        active ? "xp-bg-gray-100" : null,
                        "xp-text-inherit xp-block xp-px-6 xp-py-1 xp-no-underline"
                      )}
                    >
                      <Str id="documentation" />
                    </a>
                  )}
                </Menu.Item>
              </div>
              {resetToDefaultsUrl ? (
                <div className="xp-py-1">
                  <Menu.Item>
                    {({ active, close }) => (
                      <a
                        href={resetToDefaultsUrl}
                        className={classNames(
                          active ? "xp-bg-gray-100" : null,
                          "xp-text-red-600 xp-block xp-px-6 xp-py-1 xp-no-underline"
                        )}
                      >
                        <Str id="resettodefaults" />
                      </a>
                    )}
                  </Menu.Item>
                </div>
              ) : null}
            </Menu.Items>
          </Menu>
        </div>
      </div>

      <div className="xp-flex xp-flex-col xp-flex-1 xp-gap-4">
        {Array.from({ length: state.nblevels }).map((_, idx) => {
          const level = levels[idx] || { level: idx + 1, xprequired: 0 };
          const prevLevel = levels[idx - 1];
          const nextLevel = levels[idx + 1];
          const pointsInLevel = nextLevel ? nextLevel.xprequired - level.xprequired : 0;
          const isExpanded = expanded.includes(level.level);

          let optionStates: ((typeof optionsStates)[0] | null)[] =
            level.level <= 1
              ? optionsStates.filter((o) => ["name", "description", courseId ? null : "badgeawardid"].includes(o.id))
              : optionsStates;
          optionStates = optionStates.concat(
            Array.from({ length: Math.max(0, optionsStates.length - optionStates.length) }).map((_) => null)
          );

          const isBadgeValueMissing =
            levelsInfo.levels[idx]?.badgeawardid && !badges.find((b) => b.id === levelsInfo.levels[idx].badgeawardid);

          const handleBadgeAwardIdChange = (e: React.FocusEvent<HTMLSelectElement>) => {
            dispatch(["levelBadgeAwardIdChange", { level, badgeawardid: parseInt(e.target.value, 10) || null }]);
          };
          const handlePopupMessageChange = (e: React.FocusEvent<HTMLTextAreaElement>) => {
            dispatch(["levelPopupMessageChange", { level, popupmessage: e.target.value }]);
          };

          return (
            <React.Fragment key={`l${level.level}`}>
              <div className="xp-relative xp-min-h-28 xp-rounded-lg xp-border xp-border-solid xp-border-gray-200 xp-p-3 xp-overflow-hidden">
                <div className="xp-absolute xp--top-4 xp--left-8 xp-text-[10rem] xp-text-gray-50 xp-leading-none xp-pointer-events-none">
                  {level.level}
                </div>

                {/** Actual level */}
                <div className="xp-flex xp-items-center xp-flex-grow xp-gap-4 sm:xp-gap-8 xp-flex-col sm:xp-flex-row xp-relative">
                  <div className="xp-flex-0">
                    <Tooltip content={getStr("levelx", level.level)}>
                      <Level level={level} />
                    </Tooltip>
                  </div>
                  <div className="xp-shrink-0 xp-basis-auto sm:xp-basis-52 sm:xp--mt-3.5">
                    <div className="xp-grid xp-grid-cols-2">
                      <label
                        className="xp-m-0 xp-flex xp-items-end xp-text-xs xp-font-normal xp-uppercase"
                        htmlFor={`xp-level-${level.level}-start`}
                      >
                        <Str id="levelpointsstart" />
                      </label>
                      <label
                        className="xp-m-0 xp-flex xp-items-end xp-text-xs xp-font-normal xp-uppercase"
                        htmlFor={`xp-level-${level.level}-length`}
                      >
                        <Str id="levelpointslength" />
                      </label>
                    </div>
                    <div className="xp-grid xp-grid-cols-2 xp-border xp-border-solid xp-border-gray-300 xp-rounded">
                      <div>
                        <NumInput
                          value={level.xprequired}
                          onChange={(xp) => handleXpChange(level, xp)}
                          disabled={level.level <= 1}
                          className="xp-h-full xp-min-w-[4ch] xp-w-full xp-rounded-none xp-rounded-l xp-border-0 xp-relative focus:xp-z-10"
                          id={`xp-level-${level.level}-start`}
                        />
                      </div>
                      <div className="">
                        <div className="xp-relative xp-w-full x-h-full">
                          <div className="xp-pointer-events-none xp-absolute xp-inset-y-0 xp-left-0 xp-flex xp-items-center xp-pl-2 xp-z-20">
                            <span className="xp-text-gray-500">+</span>
                          </div>
                          <NumInput
                            value={pointsInLevel}
                            onChange={(xp) => handleXpChange(nextLevel, level.xprequired + xp)}
                            disabled={pointsInLevel <= 0}
                            className="xp-h-full xp-min-w-[4ch] xp-w-full xp-border-0 xp-rounded-none xp-border-l xp-border-gray-300 xp-rounded-r xp-pl-6 xp-relative focus:xp-z-10"
                            id={`xp-level-${level.level}-length`}
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="xp-flex xp-grow xp-items-center xp-justify-center  xp-gap-4">
                    {optionStates.map((o, idx) => {
                      if (!o) {
                        return <div key={idx} className="xp-w-6 xp-h-6 xp-hidden sm:xp-block" />;
                      }
                      const state = o.checker(level);
                      const label = getStr(state ? o.yes : o.no);
                      return (
                        <Tooltip content={label} key={idx}>
                          <div className={classNames("xp-w-6 xp-h-6", !state ? "xp-text-gray-300" : null)}>
                            <span className="xp-sr-only">{label}</span>
                            <o.Icon className="xp-w-full xp-h-full" />
                          </div>
                        </Tooltip>
                      );
                    })}
                  </div>
                  <div className="xp-flex-0 sm:xp--mr-3">
                    <AnchorButton
                      aria-expanded={isExpanded}
                      aria-controls={`xp-level-${level.level}-options`}
                      onClick={() => {
                        setExpanded(isExpanded ? expanded.filter((e) => e != level.level) : [level.level, ...expanded]);
                      }}
                      className="xp-p-2 xp-inline-block sm:xp-mr-1"
                    >
                      <span className="xp-sr-only">
                        {isExpanded ? <Str id="collapse" component="core" /> : <Str id="expand" component="core" />}
                      </span>
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        strokeWidth={1.5}
                        stroke="currentColor"
                        className={classNames(
                          "xp-w-6 xp-h-6 xp-transition-transform xp-duration-300",
                          isExpanded ? "xp-rotate-90" : null
                        )}
                      >
                        <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                      </svg>
                    </AnchorButton>
                  </div>
                </div>

                {/** Expanded */}
                <Expandable expanded={isExpanded} id={`xp-level-${level.level}-options`}>
                  <div className={classNames("sm:xp-ml-[100px] sm:xp-pl-8 xp-space-y-4")}>
                    <div className="xp-flex xp-items-end xp-gap-4">
                      <div className="xp-flex-1">
                        <OptionField label={<Str id="name" />}>
                          <Input
                            className="xp-min-w-48 x-w-full sm:xp-w-2/3 xp-max-w-full"
                            onBlur={(e) => handleLevelNameChange(level, e.target.value)}
                            defaultValue={level.name || ""}
                            maxLength={40}
                            type="text"
                          />
                        </OptionField>
                      </div>
                      {prevLevel ? (
                        <div className="xp-mb-1.5 xp-h-6 xp-w-6">
                          <Tooltip content={getStr("previewpopupnotification")}>
                            <div>
                              <AnchorButton onClick={() => showLevelUpNotificationPreview(level, prevLevel)}>
                                <span className="xp-sr-only">{getStr("previewpopupnotification")}</span>
                                <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  fill="none"
                                  viewBox="0 0 24 24"
                                  strokeWidth={1.5}
                                  stroke="currentColor"
                                  className="xp-w-6 xp-h-6"
                                >
                                  <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75-1.5.75a3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0L3 16.5m15-3.379a48.474 48.474 0 0 0-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 0 1 6 13.12M12.265 3.11a.375.375 0 1 1-.53 0L12 2.845l.265.265Zm-3 0a.375.375 0 1 1-.53 0L9 2.845l.265.265Zm6 0a.375.375 0 1 1-.53 0L15 2.845l.265.265Z"
                                  />
                                </svg>
                              </AnchorButton>
                            </div>
                          </Tooltip>
                        </div>
                      ) : null}
                    </div>
                    <OptionField label={<Str id="description" />} note={<Str id="leveldescriptiondesc" />}>
                      <Textarea
                        className="xp-w-full"
                        onBlur={(e) => handleLevelDescChange(level, e.target.value)}
                        defaultValue={level.description || ""}
                        maxLength={280}
                        rows={2}
                      />
                    </OptionField>

                    <IfAddonActivatedOrPromoEnabled>
                      {level.level > 1 ? (
                        <>
                          <OptionField
                            label={<Str id="popupnotificationmessage" />}
                            note={<Str id="popupnotificationmessagedesc" />}
                            xpPlusRequired={!hasXpPlus}
                          >
                            <Textarea
                              className="xp-w-full"
                              onBlur={handlePopupMessageChange}
                              defaultValue={level.popupmessage || ""}
                              maxLength={280}
                              rows={2}
                              disabled={!hasXpPlus}
                            />
                          </OptionField>
                          <OptionField
                            label={<Str id="badgeaward" />}
                            note={<Str id="badgeawarddesc" />}
                            xpPlusRequired={!hasXpPlus}
                          >
                            {courseId ? (
                              <Select
                                disabled={!hasXpPlus}
                                className="xp-max-w-full xp-w-auto"
                                value={level.badgeawardid ?? ""}
                                onChange={handleBadgeAwardIdChange}
                              >
                                <option>{getCoreStr("none")}</option>
                                {courseBadges.length ? (
                                  <optgroup label={getBadgeStr("coursebadges")}>
                                    {courseBadges.map((b) => (
                                      <option value={b.id} key={b.id}>
                                        {b.name}
                                      </option>
                                    ))}
                                  </optgroup>
                                ) : null}
                                {siteBadges.length ? (
                                  <optgroup label={getBadgeStr("sitebadges")}>
                                    {siteBadges.map((b) => (
                                      <option value={b.id} key={b.id}>
                                        {b.name}
                                      </option>
                                    ))}
                                  </optgroup>
                                ) : null}
                                {isBadgeValueMissing ? (
                                  <optgroup label={getCoreStr("other")}>
                                    <option value={level.badgeawardid || ""}>{getStr("unknownbadgea", level.badgeawardid)}</option>
                                  </optgroup>
                                ) : null}
                              </Select>
                            ) : (
                              <div className="alert alert-info xp-m-0">
                                <Str id="cannotbesetindefaults" />
                              </div>
                            )}
                          </OptionField>
                        </>
                      ) : (
                        <div>
                          <div className="xp-text-sm xp-text-gray-500 xp-italic">
                            <Str id="levelupoptionsunavailableforlevelone" />
                          </div>
                        </div>
                      )}
                    </IfAddonActivatedOrPromoEnabled>
                  </div>
                </Expandable>
              </div>
            </React.Fragment>
          );
        })}
      </div>

      <div className="xp-flex xp-flex-1 xp-gap-4 xp-items-start xp-flex-wrap xp-mt-4">
        <div className="xp-grow">
          <Button onClick={() => handleNumLevelsChange(state.nblevels + 1)}>
            <Str id="addlevel" />
          </Button>
        </div>
        <div className="">
          <SaveButton
            statePosition="before"
            onClick={handleSave}
            mutation={mutation}
            disabled={!state.pendingSave || mutation.isLoading}
          />
        </div>
      </div>
    </div>
  );
};

type AppProps = {
  courseId: number;
  levelsInfo: LevelsInfo;
  resetToDefaultsUrl?: string;
  defaultBadgeUrls: { [index: number]: null | string };
  badges?: { id: number; name: string; type: BADGE_TYPE }[];
  addon: any;
};

function startApp(node: HTMLElement, props: any) {
  ReactDOM.render(
    <AddonContext.Provider value={makeAddonContextValueFromAppProps(props)}>
      <QueryClientProvider client={queryClient}>
        <App {...props} />
      </QueryClientProvider>
    </AddonContext.Provider>,
    node
  );
}

const dependencies = makeDependenciesDefinition(commonStaticModulesToDependOn);

export { dependencies, startApp };
