import React, { useCallback, useEffect, useMemo, useState } from "react";
import { useQuery } from "react-query";
import { useStrings } from "../lib/hooks";
import { ajaxRequest, getModuleAsync } from "../lib/moodle";
import { ContextLevel, Resource } from "../lib/types";
import { Button } from "./Button";
import Input, { Select } from "./Input";
import { SaveCancelModal } from "./Modal";
import { NumberInputWithButtons } from "./NumberInput";
import { EmptyResult, LoadingResourceList, PlainResourceList } from "./ResourceList";
import { Slide, SlideHeader, SlideHeaderWithFilter, Slider } from "./Slider";
import Str from "./Str";

type CmResourceListProps = {
  courseId: number;
  filterTerm?: string;
  onSelect: (cmid: number) => void;
  resetFilterTerm?: () => void;
  options?: { completionenabled?: boolean; type?: string };
};

const CmResourceList = ({ courseId, filterTerm, onSelect, resetFilterTerm, options = {} }: CmResourceListProps) => {
  const query = useQuery(["cm-resource-list", courseId, options], async () => {
    const Ajax = await getModuleAsync("core/ajax");
    return (await Ajax.call([
      {
        methodname: "block_xp_search_modules",
        args: { courseid: courseId, query: "*", options },
      },
    ])[0]) as Promise<
      [
        {
          name: string; // The section name.
          modules: {
            cmid: number;
            contextid: number;
            name: string;
          }[];
        }
      ]
    >;
  });

  const resources = useMemo(() => {
    const normalisedFilterTerm = (filterTerm || "").trim().toLowerCase();
    const data = query.data || [];
    return data.reduce<Resource[]>((carry, section, idx) => {
      const modules =
        normalisedFilterTerm === ""
          ? section.modules
          : section.modules.filter((module) => {
              return module.name.includes(normalisedFilterTerm);
            });
      if (!modules.length) {
        return carry;
      }
      // Only show headers if we have multiple sections.
      if (data.length > 1) {
        carry.push({ name: idx, label: section.name, type: "header" });
      }
      modules.forEach((module) => {
        carry.push({ name: module.cmid, label: module.name });
      });
      return carry;
    }, []) as Resource<number>[];
  }, [query.data, filterTerm]);

  if (!query.isSuccess || query.isLoading) {
    return <LoadingResourceList />;
  }

  return (
    <PlainResourceList<Resource<number>>
      resources={resources}
      onSelect={(r) => onSelect(r.name)}
      emptyContent={
        <EmptyResult
          message={<Str id="nothingmatchesfilter" />}
          content={
            resetFilterTerm ? (
              <Button onClick={resetFilterTerm}>
                <Str id="clearfilter" />
              </Button>
            ) : null
          }
        />
      }
    />
  );
};

const CmResourceListSlide = ({
  courseId,
  onSelect,
  hasBack,
  onBack,
  cmListOptions,
}: {
  courseId: number;
  hasBack?: boolean;
  onBack?: () => void;
  onSelect: (cmid: number) => void;
  cmListOptions?: CmResourceListProps["options"];
}) => {
  const [filterTerm, setFilterTerm] = useState("");
  return (
    <Slide
      header={
        <SlideHeaderWithFilter
          filterValue={filterTerm}
          onFilterChange={setFilterTerm}
          hasBack={hasBack}
          onBack={onBack}
          title={<Str id="rulefiltercm" />}
        />
      }
    >
      <CmResourceList
        options={cmListOptions}
        courseId={courseId}
        onSelect={onSelect}
        filterTerm={filterTerm}
        resetFilterTerm={() => setFilterTerm("")}
      />
    </Slide>
  );
};

const SectionResourceList = ({ courseId, onSelect, options = {} }: CmResourceListProps) => {
  const query = useQuery(["section-resource-list", courseId, options], async () =>
    ajaxRequest<
      {
        name: string;
        number: number;
      }[]
    >("block_xp_get_sections", { courseid: courseId, options })
  );

  const resources = useMemo(() => {
    const data = query.data || [];
    return data.reduce<Resource[]>((carry, section, idx) => {
      carry.push({ name: section.number, label: section.name });
      return carry;
    }, []) as Resource<number>[];
  }, [query.data]);

  if (!query.isSuccess || query.isLoading) {
    return <LoadingResourceList />;
  }

  return (
    <PlainResourceList<Resource<number>>
      resources={resources}
      onSelect={(r) => onSelect(r.name)}
      emptyContent={<EmptyResult message={<Str id="nothingmatchesfilter" />} />}
    />
  );
};

const CmNameSlide = ({
  onBack,
  config,
  setConfig,
}: {
  config: Record<string, any>;
  setConfig: (data: Record<string, any>) => void;
  onBack?: () => void;
}) => {
  const defaultValue = 1;
  const getStr = useStrings(["rule:eq", "rule:contains", "rulefiltercmname"]);
  return (
    <Slide header={<SlideHeader hasBack onBack={onBack} title={getStr("rulefiltercmname")} />}>
      <div className="xp-mb-4">
        <label htmlFor="xp-rule-cmname-name" className="xp-m-0">
          <Str id="activityname" />
        </label>
        <div className="xp-flex xp-gap-2">
          <Select
            value={config.filterint1}
            onChange={(e) => setConfig({ filterint1: parseInt(e.currentTarget.value, 10) || 0 })}
            defaultValue={defaultValue.toString()}
            className="xp-w-auto"
          >
            <option value="1">{getStr("rule:contains")}</option>
            <option value="0">{getStr("rule:eq")}</option>
          </Select>
          <Input
            id="xp-rule-cmname-name"
            value={config.filterchar1 || ""}
            onChange={(e) => setConfig({ filterchar1: e.currentTarget.value, filterint1: config.filterint1 ?? defaultValue })}
            maxLength={255}
          />
        </div>
        <p className="xp-text-gray-500 xp-m-0 xp-mt-1">
          <Str id="activityname_help" />
        </p>
      </div>
      <PointsToAwardInput config={config} setConfig={setConfig} />
    </Slide>
  );
};

type FilterMethodStuff = {
  getSlide: (props: {
    setConfig: (data: Record<string, any>) => void;
    config: Record<string, any>;
    method: Resource<string> & { scope: string; scopeoptions: Record<string, any> };
    hasBack: boolean;
    onBack?: () => void;
    onContinue: () => void;
    courseId: number;
  }) => JSX.Element | null;
  collectsPoints?: boolean;
  hasSlide: boolean;
  isConfigValid: (config: Record<string, any>) => boolean;
  isSlideRequiringSubmit: boolean;
};

const anyFilterMethodStuff = {
  getSlide: () => null,
  hasSlide: false,
  isConfigValid: () => true,
  isSlideRequiringSubmit: false,
};

const filterMethoStuff: Record<string, FilterMethodStuff> = {
  cm: {
    getSlide: (props) => (
      <CmResourceListSlide
        cmListOptions={{
          completionenabled: props.method.scopeoptions?.completionenabled,
          type: props.method.scopeoptions?.type,
        }}
        hasBack={props.hasBack}
        onBack={props.onBack}
        courseId={props.courseId}
        onSelect={(cmid) => {
          props.setConfig({ filtercmid: cmid });
          props.onContinue();
        }}
      />
    ),
    hasSlide: true,
    isConfigValid: () => true,
    isSlideRequiringSubmit: false,
  },
  cmname: {
    getSlide: (props) => <CmNameSlide onBack={props.onBack} config={props.config} setConfig={props.setConfig} />,
    hasSlide: true,
    isConfigValid: (config) =>
      [0, 1].includes(config?.filterint1) &&
      typeof config.filterchar1 === "string" &&
      config.filterchar1.trim() !== "" &&
      typeof config?.points === "number" &&
      !isNaN(config.points),
    isSlideRequiringSubmit: true,
    collectsPoints: true,
  },
  section: {
    getSlide: (props) => (
      <Slide header={<SlideHeader hasBack={props.hasBack} onBack={props.onBack} title={<Str id="rulefiltersection" />} />}>
        <SectionResourceList
          courseId={props.courseId}
          onSelect={(num) => {
            props.setConfig({ filterint1: num });
            props.onContinue();
          }}
        />
      </Slide>
    ),
    hasSlide: true,
    isConfigValid: () => true,
    isSlideRequiringSubmit: false,
  },
  any: anyFilterMethodStuff,
  anycm: anyFilterMethodStuff,
  anycourse: anyFilterMethodStuff,
  anysection: anyFilterMethodStuff,
  thiscourse: anyFilterMethodStuff,
};

// TODO Update this to understand current context and child context, and receive contextlevel and instanceid.
type RuleWizardModalProps = {
  courseid: number;
  contextlevel: ContextLevel;
  method: Resource<string> & { scope: string; scopeoptions: Record<string, any> };
  filters: (Resource<string> & { weight?: number })[];

  onClose: () => void;
  onSave: (data: { filter: string; config: Record<string, any> }) => void;
  isSaving?: boolean;
  show?: boolean;
};

const defaultConfig: Record<string, any> = { points: 10 };

export const RuleWizardModal = (props: RuleWizardModalProps) => {
  const getStr = useStrings(["addacondition"]);
  const getCoreStr = useStrings(["continue", "save"], "core");
  const [selectedMethod, setSelectedMethod] = useState<string | null>(null);
  const [config, setConfig] = useState<Record<string, any>>(defaultConfig);
  const [index, setIndex] = useState(0);

  const handleSelected = (method: string) => {
    if (!(method in filterMethoStuff)) {
      return;
    }
    setSelectedMethod(method);
    setIndex(1);
  };

  const handleIndexChange = (rawIndex: number) => {
    const newIndex = Math.max(0, rawIndex);
    if (newIndex === 0) {
      setConfig(defaultConfig);
    }
    setIndex(newIndex);
  };

  const handleAddToConfig = (data: Record<string, any>) => {
    setConfig({ ...config, ...data });
  };

  const handleBack = () => {
    handleIndexChange(index - 1);
  };

  const resetState = useCallback(() => {
    setSelectedMethod(null);
    setConfig(defaultConfig);
    setIndex(0);
  }, []);

  useEffect(() => {
    // Reset the state when the modal is closed/hidden.
    if (!props.show) {
      resetState();
    }
  }, [props.show]);

  const methodStuff = selectedMethod ? filterMethoStuff[selectedMethod] ?? null : null;
  const isLastStep = index === (methodStuff?.hasSlide && !methodStuff?.collectsPoints ? 2 : 1);
  const isStepContinue = !isLastStep;
  const isStepValid = methodStuff && index === 1 ? methodStuff?.isConfigValid(config) : true;
  const isStepRequiringButton = Boolean(methodStuff?.isSlideRequiringSubmit);

  const canClickSaveButton = (isLastStep || isStepRequiringButton) && isStepValid;
  const handleSave = (e: Event) => {
    e.preventDefault();
    if (!canClickSaveButton) {
      return;
    }
    if (isStepContinue) {
      handleIndexChange(index + 1);
      return;
    }
    if (!selectedMethod) {
      return;
    }
    props.onSave({ filter: selectedMethod, config });
  };

  const handleClose = () => {
    props.onClose();
  };

  const methodSlide = selectedMethod
    ? methodStuff?.getSlide({
        setConfig: handleAddToConfig,
        config,
        hasBack: index === 1,
        onBack: handleBack,
        onContinue: () => handleIndexChange(index + 1),
        method: props.method,
        courseId: props.courseid,
      })
    : null;

  const sortedFilters = useMemo(() => {
    return props.filters.sort((a, b) => {
      const wa = a.weight ?? null;
      const wb = b.weight ?? null;
      if (wa === wb) return 0;
      if (wa === null || wb === null) return 1; // Always show null last.
      if (wa === 0) return -1; // Always show the 0 weight first.
      return wb - wa; // Descending order.
    });
  }, [props.filters]);

  return (
    <SaveCancelModal
      show={props.show}
      large
      defaultHeight={500}
      canSave={canClickSaveButton}
      onSave={handleSave}
      onClose={handleClose}
      saveButtonText={isStepContinue ? getCoreStr("continue") : getCoreStr("save")}
      title={getStr("addacondition")}
    >
      <Slider index={index}>
        <Slide>
          <PlainResourceList onSelect={(r) => handleSelected(r.name)} resources={sortedFilters} />
        </Slide>
        {selectedMethod && methodSlide ? methodSlide : null}
        {selectedMethod && !methodStuff?.collectsPoints ? (
          <Slide
            header={
              <SlideHeader
                hasBack
                onBack={handleBack}
                title={methodStuff?.hasSlide ? <Str id="pointstoaward" /> : <Str id={`rulefilter${selectedMethod}`} />}
              />
            }
          >
            <div className="">
              <PointsToAwardInput config={config} setConfig={setConfig} />
            </div>
          </Slide>
        ) : null}
      </Slider>
    </SaveCancelModal>
  );
};

const PointsToAwardInput = ({
  setConfig,
  config,
}: {
  config: Record<string, any>;
  setConfig: (data: Record<string, any>) => void;
}) => {
  return (
    <div>
      <label htmlFor="xp-rule-pointstoaward" className="xp-m-0">
        <Str id="pointstoaward" />
      </label>
      <div>
        <NumberInputWithButtons
          value={config.points}
          onChange={(points) => setConfig({ ...config, points })}
          min={0}
          max={9999999}
          inputProps={{ id: "xp-rule-pointstoaward", className: "xp-w-24", selectOnFocus: true }}
        />
      </div>
      <p className="xp-text-gray-500 xp-m-0 xp-mt-1">
        <Str id="pointstoaward_help" />
      </p>
    </div>
  );
};
