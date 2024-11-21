import { Tab } from "@headlessui/react";
import React, { Fragment, useCallback, useMemo, useState } from "react";
import ReactDOM from "react-dom";
import { QueryClientProvider, UseMutationOptions, useMutation, useQuery, useQueryClient } from "react-query";
import { Dropdown } from "./components/Dropdown";
import { AppLoading } from "./components/Loading";
import { DeleteModal, ModalForm } from "./components/Modal";
import { NotificationError } from "./components/Notification";
import { RuleWizardModal } from "./components/RuleWizard";
import Str from "./components/Str";
import { AddonContext, makeAddonContextValueFromAppProps } from "./lib/contexts";
import { useAnchorButtonProps, useStrings } from "./lib/hooks";
import { ajaxRequest, commonStaticModulesToDependOn, getModule, getModuleAsync, makeDependenciesDefinition } from "./lib/moodle";
import { queryClient } from "./lib/query";
import { ContextLevel, ResourceItem } from "./lib/types";
import { classNames } from "./lib/utils";

const useRules = (contextid: number, types: string[], childcontextid: number | null) => {
  return useQuery<Rule[]>(["rules", contextid, types, childcontextid], async () => {
    const data = await ajaxRequest<
      {
        id: number;
        points: number;
        typename: string;
        filtername: string;
        label: string;
      }[]
    >("block_xp_get_rules", {
      contextid,
      types,
      childcontextid,
    });
    return data.map((data) => ({
      ...data,
      method: data.typename,
      filter: data.filtername,
    }));
  });
};

const useAddRuleMutation = (
  contextid: number,
  childcontextid: number | null,
  { onSuccess }: { onSuccess: UseMutationOptions<any, any, any, any>["onSuccess"] }
) => {
  return useMutation(
    async ({ method, filter, config }: { method: string; filter: string; config: Record<string, any> }) => {
      return ajaxRequest<Rule>("block_xp_create_rule", {
        contextid,
        childcontextid: childcontextid ?? 0,
        points: config.points ?? 0,
        type: {
          name: method,
          char1: config.typechar1 ?? null,
        },
        filter: {
          name: filter,
          courseid: config.filtercourseid ?? null,
          cmid: config.filtercmid ?? null,
          int1: config.filterint1 ?? null,
          char1: config.filterchar1 ?? null,
        },
      });
    },
    {
      onSuccess,
    }
  );
};

const useDeleteRuleMutation = () => {
  return useMutation(async ({ id }: { id: number }) => {
    return ajaxRequest<Rule>("block_xp_delete_rule", { id });
  });
};

const AppContext = React.createContext({
  rules: [] as Rule[],
  addRule: () => {},
  editRule: (id: number) => {},
  removeRule: (id: number) => {},
});

const availableRuleTypes = ["cm_completion", "section_completion", "course_completion"];
const guessMethodFromLocation = () => {
  return Math.max(0, availableRuleTypes.indexOf((window.location.hash ?? "").replace(/^#/, "")));
};
const updateLocationFromMethodIndex = (idx: number) => {
  const hash = "#" + availableRuleTypes[idx];
  window.location.hash = hash;
};

export const App = (props: AppProps) => {
  const queryClient = useQueryClient();
  const [selectedTabIndex, setSelectedTabIndex] = useState(guessMethodFromLocation);
  const [optimisticallyDeleted, setOptimisticallyDeleted] = useState<number[]>([]);
  const childcontextid = props.childcontext?.id ?? null;
  const currentCourseId =
    props.childcontext?.contextlevel === ContextLevel.Course ? props.childcontext.instanceid : props.world.courseid;

  const [isAdding, setIsAdding] = useState(false);
  const [isEditing, setIsEditing] = useState<number | null>(null);
  const [isDeleting, setIsDeleting] = useState<number | null>(null);

  const getStr = useStrings(["deletecondition", "editcondition", "ruleadded"]);
  const rulesQuery = useRules(props.world.contextid, availableRuleTypes, childcontextid);
  const addRuleMutation = useAddRuleMutation(props.world.contextid, childcontextid, {
    onSuccess: () => {
      invalidateCurrentQuery();
      setIsAdding(false);
      const Toast = getModule("core/toast");
      Toast && Toast.add(getStr("ruleadded"));
    },
  });
  const deleteRuleMutation = useDeleteRuleMutation();

  const handleSelectedTabIndexChange = (idx: number) => {
    setSelectedTabIndex(idx);
    setIsAdding(false);
    setIsEditing(null);
    setIsDeleting(null);
    updateLocationFromMethodIndex(idx);
  };

  const currentRuleType = availableRuleTypes[selectedTabIndex];
  const rules = useMemo(() => {
    return (rulesQuery.data || []).filter((r) => !optimisticallyDeleted.includes(r.id));
  }, [rulesQuery.data, optimisticallyDeleted]);

  const invalidateCurrentQuery = useCallback(() => {
    queryClient.invalidateQueries(["rules", props.world.contextid, availableRuleTypes, childcontextid]);
  }, [queryClient, props]);

  const ruleTypesByName = useMemo(() => {
    return props.ruletypes.reduce((acc, ruletype) => {
      acc[ruletype.name] = ruletype;
      return acc;
    }, {} as Record<string, RuleType>);
  }, [props.ruletypes]);

  const groupedRules = useMemo(() => {
    return rules.reduce((acc, rule) => {
      if (!acc[rule.method]) {
        acc[rule.method] = [];
      }
      acc[rule.method].push(rule);
      return acc;
    }, {} as Record<string, Rule[]>);
  }, [rules]);

  const currentMethodFilters = useMemo(() => {
    if (!ruleTypesByName[currentRuleType]?.filters) {
      return [];
    }
    return props.rulefilters
      .filter((filter) => ruleTypesByName[currentRuleType].filters.includes(filter.name))
      .filter((filter) => filter.ismultipleallowed || !groupedRules[currentRuleType]?.some((rule) => rule.filter === filter.name));
  }, [currentRuleType, props.rulefilters, groupedRules]);

  const canAdd = true;
  const showAddBtnInTabs = canAdd && groupedRules[currentRuleType]?.length;

  if (rulesQuery.isLoading || rulesQuery.isError) {
    return <AppLoading />;
  }

  return (
    <AppContext.Provider
      value={{
        rules,
        addRule: () => setIsAdding(true),
        editRule: (ruleId: number) => setIsEditing(ruleId),
        removeRule: (ruleId: number) => setIsDeleting(ruleId),
      }}
    >
      <div>
        <Tab.Group selectedIndex={selectedTabIndex} onChange={handleSelectedTabIndexChange}>
          <Tab.List as="div" className="nav nav-tabs">
            <Tab as={Fragment}>
              {({ selected }) => (
                <button className={classNames("nav-item nav-link", selected ? "active" : null)}>
                  <Str id="activity" component="core" />
                </button>
              )}
            </Tab>
            <Tab as={Fragment}>
              {({ selected }) => (
                <button className={classNames("nav-item nav-link", selected ? "active" : null)}>
                  <Str id="section" component="core" />
                </button>
              )}
            </Tab>
            <Tab as={Fragment}>
              {({ selected }) => (
                <button className={classNames("nav-item nav-link", selected ? "active" : null)}>
                  <Str id="course" component="core" />
                </button>
              )}
            </Tab>
            <div className="xp-flex-1 xp-flex xp-justify-end xp-items-center">
              {showAddBtnInTabs ? (
                <button className="btn btn-primary btn-sm" onClick={() => setIsAdding(true)}>
                  <Str id="add" component="core" />
                </button>
              ) : null}
            </div>
          </Tab.List>
          <Tab.Panels className="xp-mt-4">
            <Tab.Panel>
              {"cm_completion" in ruleTypesByName ? (
                <CompletionRules
                  rules={groupedRules.cm_completion}
                  type={ruleTypesByName["cm_completion"]}
                  filters={props.rulefilters}
                />
              ) : (
                <NotificationError>
                  <Str id="unknowntypea" a="cm_completion" />
                </NotificationError>
              )}
            </Tab.Panel>
            <Tab.Panel>
              {"section_completion" in ruleTypesByName ? (
                <CompletionRules
                  rules={groupedRules.section_completion}
                  type={ruleTypesByName["section_completion"]}
                  filters={props.rulefilters}
                />
              ) : (
                <NotificationError>
                  <Str id="unknowntype" a="section_completion" />
                </NotificationError>
              )}
            </Tab.Panel>
            <Tab.Panel>
              {"course_completion" in ruleTypesByName ? (
                <CompletionRules
                  rules={groupedRules.course_completion}
                  type={ruleTypesByName["course_completion"]}
                  filters={props.rulefilters}
                />
              ) : (
                <NotificationError>
                  <Str id="unknowntype" a="course_completion" />
                </NotificationError>
              )}
            </Tab.Panel>
          </Tab.Panels>
        </Tab.Group>
      </div>
      <RuleWizardModal
        show={isAdding}
        courseid={currentCourseId}
        contextlevel={props.world.contextlevel}
        method={ruleTypesByName[currentRuleType]}
        filters={currentMethodFilters}
        onClose={() => setIsAdding(false)}
        onSave={({ filter, config }) => {
          addRuleMutation.mutate({ method: currentRuleType, filter, config });
        }}
      />
      <DeleteModal
        show={isDeleting !== null}
        onClose={() => setIsDeleting(null)}
        onDelete={() => {
          if (!isDeleting) return;
          setOptimisticallyDeleted([...optimisticallyDeleted, isDeleting]);
          deleteRuleMutation.mutate(
            { id: isDeleting },
            {
              onError: () => {
                setOptimisticallyDeleted(optimisticallyDeleted.filter((id) => id !== isDeleting));
              },
              onSuccess: () => {
                invalidateCurrentQuery();
              },
              onSettled: () => {
                setIsDeleting(null);
              },
            }
          );
        }}
        title={getStr("deletecondition")}
      >
        <Str id="areyousure" component="core" />
      </DeleteModal>
      {isEditing ? (
        <ModalForm
          formClass="block_xp\form\rule"
          formArgs={{ id: isEditing }}
          title={getStr("editcondition")}
          onClose={() => setIsEditing(null)}
          onSubmit={() => {
            setIsEditing(null);
            invalidateCurrentQuery();
          }}
        />
      ) : null}
    </AppContext.Provider>
  );
};

const groupRulesByFilter = (rules?: Rule[]) => {
  if (!rules) return [];
  const filterNames = rules.map((rule) => rule.filter).filter((value, index, self) => self.indexOf(value) === index);
  return filterNames
    .map((filterName) => {
      const rulesForFilter = rules.filter((rule) => rule.filter === filterName);
      return { filter: filterName, rules: rulesForFilter };
    })
    .filter((group) => group.rules.length > 0);
};

const NoRulesZeroState = ({ onClick }: { onClick: () => void }) => {
  return (
    <div className="xp-rounded xp-border-dashed xp-border-2 xp-p-4 xp-py-6 xp-text-center xp-border-gray-200">
      <div className="xp-text-xl xp-font-bold xp-mb-4">
        <Str id="noconditionsyet" />
      </div>
      <div>
        <Str id="noconditionsyetintro" />
      </div>
      <div className="xp-mt-4">
        <button className="btn btn-primary" onClick={onClick}>
          <Str id="add" component="core" />
        </button>
      </div>
    </div>
  );
};

const CompletionRules = ({ rules, type, filters }: { rules?: Rule[]; type: RuleType; filters: RuleFilter[] }) => {
  const filteredRules = useMemo(
    () => rules?.filter((r) => type.filters.includes(r.filter) && filters.find((f) => f.name === r.filter)),
    [rules, type.filters]
  );
  const groupedRules = useMemo(() => groupRulesByFilter(filteredRules), [filteredRules]);
  const { addRule, removeRule, editRule } = React.useContext(AppContext);
  const handleAddClick = () => {
    addRule();
  };

  if (!filteredRules?.length) {
    return <NoRulesZeroState onClick={handleAddClick} />;
  }

  return (
    <div className="xp-space-y-4">
      {groupedRules.map(({ filter, rules }) => {
        const ruleFilter = filters.find((f) => f.name === filter);
        if (!ruleFilter) return null;
        return (
          <RulesSection key={filter} title={ruleFilter?.label} description={ruleFilter?.description}>
            {rules.map((rule) => {
              return (
                <Rule
                  key={rule.id}
                  points={rule.points}
                  label={rule.label}
                  onDelete={() => removeRule(rule.id)}
                  onEdit={() => editRule(rule.id)}
                />
              );
            })}
          </RulesSection>
        );
      })}
    </div>
  );
};

const RulesSection = ({
  children,
  title,
  description,
}: {
  children: React.ReactNode;
  title: React.ReactNode;
  description: React.ReactNode;
}) => {
  return (
    <div>
      <h5 className="xp-font-bold xp-m-0 xp-mb-1 xp-text-base">{title}</h5>
      <p className="xp-mb-2 xp-text-sm xp-text-gray-500 xp-m-0">{description}</p>
      <div className="[&>div]:xp-border-0 [&>div]:xp-border-b [&>div]:xp-border-solid [&>div]:xp-border-gray-200">{children}</div>
    </div>
  );
};

const Rule = ({
  points,
  label,
  onDelete,
  onEdit,
}: {
  points: number | null;
  label: string;
  onDelete: () => void;
  onEdit: () => void;
}) => {
  return (
    <div className="">
      <div className="xp-flex xp-gap-2">
        <div className="xp-shrink-0 xp-flex xp-items-center">
          <div
            className={classNames(
              "xp-min-w-[86px] xp-text-center xp-rounded xp-px-2 xp-py-0.5 xp-font-bold xp-tracking-wide",
              !points ? "xp-bg-gray-200" : "xp-bg-blue-100"
            )}
          >
            {points !== null ? `${points != 0 ? "+" : ""}${points}` : "-"}
          </div>
        </div>
        <div className="xp-grow xp-flex xp-items-center">
          <div className="xp-grow">{label}</div>
        </div>
        <div className="xp-shrink-0">
          <RuleDropdown onDelete={onDelete} onEdit={onEdit} />
        </div>
      </div>
    </div>
  );
};

const RuleDropdown = ({ onEdit, onDelete }: any) => {
  const deleteProps = useAnchorButtonProps(onDelete);
  const editProps = useAnchorButtonProps(onEdit);
  return (
    <Dropdown
      buttonLabel={<Str id="options" component="core" />}
      items={[
        { id: "edit", label: <Str id="edit" component="core" />, props: editProps },
        { id: "delete", label: <Str id="delete" component="core" />, props: deleteProps, danger: true },
      ]}
    />
  );
};

type Rule = {
  id: number;
  points: number;
  method: string;
  filter: string;
  label: string;
};
type RuleType = ResourceItem<string> & { scope: string; scopeoptions: Record<string, any>; filters: string[] };
type RuleFilter = ResourceItem<string> & { weight: number; ismultipleallowed: boolean };
type AppProps = {
  world: {
    contextid: number;
    contextlevel: ContextLevel;
    courseid: number;
  };
  childcontext: null | {
    id: number;
    contextlevel: ContextLevel;
    instanceid: number;
  };
  currentcontext: {
    id: number;
    contextlevel: ContextLevel;
    instanceid: number;
  };
  rulefilters: RuleFilter[];
  ruletypes: RuleType[];
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
