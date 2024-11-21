import React, { useReducer } from "react";
import { SaveCancelModal } from "./Modal";
import { useString, useStrings } from "../lib/hooks";
import Str from "./Str";
import { NumberInputWithButtons } from "./NumberInput";
import { PointCalculationMethod } from "../lib/types";
import { RadioGroup } from "./RadioGroup";
import { HELP_URL_LEVELS } from "../lib/constants";

export type BulkEditPointsState = PointCalculationMethod;

function calculationMethodReducer(state: BulkEditPointsState, action: { type: string; payload: any }): BulkEditPointsState {
  switch (action.type) {
    case "setMethod":
      return { ...state, method: action.payload };
    case "setBase":
      return { ...state, base: Math.max(1, action.payload) };
    case "setIncrement":
      return { ...state, incr: Math.max(0, action.payload) };
    case "setCoef":
      return { ...state, coef: Math.min(5, Math.max(1, action.payload)) };
  }
  return state;
}

export function getDefaultBulkEditPointsState(props: {
  method?: BulkEditPointsState["method"];
  base?: BulkEditPointsState["base"];
  incr?: BulkEditPointsState["incr"];
  coef?: BulkEditPointsState["coef"];
}) {
  return {
    method: props.method || "relative",
    base: Math.max(1, props.base || 120),
    incr: Math.max(0, props.incr || 40),
    coef: Math.min(5, Math.max(props.coef || 1.3)),
  };
}

const BulkEditPoints: React.FC<{
  method: BulkEditPointsState["method"];
  base: BulkEditPointsState["base"];
  incr: BulkEditPointsState["incr"];
  coef: BulkEditPointsState["coef"];
  onMethodChange: (p: BulkEditPointsState["method"]) => void;
  onIncrementChange: (p: BulkEditPointsState["incr"]) => void;
  onBaseChange: (p: BulkEditPointsState["base"]) => void;
  onCoefChange: (p: BulkEditPointsState["coef"]) => void;
}> = ({ method, base, incr, coef, onBaseChange, onCoefChange, onIncrementChange, onMethodChange }) => {
  const getStr = useStrings(
    [
      "basepoints",
      "basepointslineardesc",
      "basepointsrelativedesc",
      "difficulty",
      "difficultyflat",
      "difficultyflatdesc",
      "difficultylinear",
      "difficultylineardesc",
      "difficultylinearincrdesc",
      "difficultypointincrease",
      "difficultyrelative",
      "difficultyrelativedesc",
      "difficultyrelativeincrdesc",
      "documentation",
      "pointsperlevel",
      "recommended",
    ],
    "block_xp"
  );
  return (
    <div className="xp-space-y-4">
      <div>
        <div className="xp-mb-2 xp-flex xp-items-start xp-flex-wrap">
          <div className="xp-grow xp-font-bold">{getStr("difficulty")}</div>
          <div className="xp-shrink-0">
            <a href={HELP_URL_LEVELS} target="_blank" rel="noopener">
              {getStr("documentation")}
            </a>
          </div>
        </div>
        <RadioGroup
          onChange={onMethodChange}
          value={method}
          items={[
            { value: "flat", label: getStr("difficultyflat"), desc: getStr("difficultyflatdesc") },
            {
              value: "linear",
              label: getStr("difficultylinear"),
              desc: getStr("difficultylineardesc"),
            },
            {
              value: "relative",
              label: (
                <>
                  {getStr("difficultyrelative")}
                  <div className="badge badge-info xp-ml-2">{getStr("recommended")}</div>
                </>
              ),
              desc: getStr("difficultyrelativedesc"),
            },
          ]}
        />
      </div>
      <div>
        <p className="xp-font-bold xp-mb-2">
          <Str id="settings" component="core" />
        </p>
        {method === "flat" ? (
          <>
            <div className="">
              <label htmlFor="xp-calc-bp" className="xp-m-0">
                <Str id="pointsperlevel" />
              </label>
              {/** Save in base, so that value makes sense when switching between method. */}
              <div>
                <NumberInputWithButtons
                  value={base}
                  onChange={onBaseChange}
                  min={1}
                  step={10}
                  inputProps={{ id: "xp-calc-bp", className: "xp-w-24" }}
                />
              </div>
            </div>
          </>
        ) : null}
        {method === "linear" ? (
          <>
            <div className="xp-space-y-2">
              <div className="">
                <label htmlFor="xp-calc-bp" className="xp-m-0">
                  <Str id="basepoints" />
                </label>
                <div>
                  <NumberInputWithButtons
                    value={base}
                    onChange={onBaseChange}
                    min={1}
                    step={10}
                    inputProps={{ id: "xp-calc-bp", className: "xp-w-24" }}
                  />
                </div>
                <p className="xp-text-gray-500 xp-m-0 xp-mt-1">{getStr("basepointslineardesc")}</p>
              </div>
              <div className="">
                <label htmlFor="xp-calc-pi" className="xp-m-0">
                  {getStr("difficultypointincrease")}
                </label>
                <div>
                  <NumberInputWithButtons
                    value={incr}
                    onChange={onIncrementChange}
                    min={0}
                    inputProps={{ id: "xp-calc-pi", className: "xp-w-24" }}
                  />
                </div>
                <p className="xp-text-gray-500 xp-m-0 xp-mt-1">{getStr("difficultylinearincrdesc")}</p>
              </div>
            </div>
          </>
        ) : null}
        {method === "relative" ? (
          <>
            <div className="xp-space-y-2">
              <div className="">
                <label htmlFor="xp-calc-bp" className="xp-m-0">
                  <Str id="basepoints" />
                </label>
                <div>
                  <NumberInputWithButtons
                    value={base}
                    onChange={onBaseChange}
                    min={1}
                    step={10}
                    inputProps={{ id: "xp-calc-bp", className: "xp-w-24" }}
                  />
                </div>
                <p className="xp-text-gray-500 xp-m-0 xp-mt-1">{getStr("basepointsrelativedesc")}</p>
              </div>
              <div className="">
                <label htmlFor="xp-calc-pi" className="xp-m-0">
                  {getStr("difficultypointincrease")}
                </label>
                <div>
                  <NumberInputWithButtons
                    value={Math.floor(coef * 100 - 100)}
                    onChange={(p) => onCoefChange(1 + p / 100)}
                    min={0}
                    max={400}
                    inputProps={{ id: "xp-calc-pi", className: "xp-w-24", maxLength: 3 }}
                    suffix="%"
                  />
                </div>
                <p className="xp-text-gray-500 xp-m-0 xp-mt-1">{getStr("difficultyrelativeincrdesc")}</p>
              </div>
            </div>
          </>
        ) : null}
      </div>
    </div>
  );
};

export const BulkEditPointsModal: React.FC<{
  show?: boolean;
  onClose?: () => void;
  onSave?: (state: BulkEditPointsState) => void;
  method?: BulkEditPointsState["method"];
  base?: BulkEditPointsState["base"];
  incr?: BulkEditPointsState["incr"];
  coef?: BulkEditPointsState["coef"];
}> = (props) => {
  const [state, dispatch] = useReducer(calculationMethodReducer, props, getDefaultBulkEditPointsState);
  const getStr = useStrings(["quickeditpoints", "apply"], "block_xp");

  const setMethod = (p: BulkEditPointsState["method"]) => dispatch({ type: "setMethod", payload: p });
  const setIncrement = (p: BulkEditPointsState["incr"]) => dispatch({ type: "setIncrement", payload: p });
  const setBase = (p: BulkEditPointsState["base"]) => dispatch({ type: "setBase", payload: p });
  const setCoef = (p: BulkEditPointsState["coef"]) => dispatch({ type: "setCoef", payload: p });

  const handleClose = () => {
    dispatch({ type: "reset", payload: getDefaultBulkEditPointsState(props) });
    props.onClose && props.onClose();
  };
  const handleSave = () => {
    props.onSave && props.onSave(state);
  };

  return (
    <SaveCancelModal
      show={props.show}
      onClose={handleClose}
      onSave={handleSave}
      title={getStr("quickeditpoints")}
      saveButtonText={getStr("apply")}
    >
      <BulkEditPoints
        coef={state.coef}
        base={state.base}
        incr={state.incr}
        method={state.method}
        onBaseChange={setBase}
        onCoefChange={setCoef}
        onIncrementChange={setIncrement}
        onMethodChange={setMethod}
      />
    </SaveCancelModal>
  );
};
