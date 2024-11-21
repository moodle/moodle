import React, { useState } from "react";

type Items = {
  value: string | number;
  label: React.ReactNode;
  desc?: React.ReactNode;
}[];

export const RadioGroup: React.FC<{ items: Items; value: any; onChange: (value: any) => void }> = ({ items, value, onChange }) => {
  const [uniqid] = useState(() => Math.random().toString(12).slice(2));
  return (
    <div className="xp-space-y-2">
      {items.map((item, idx) => (
        <label className="xp-relative xp-flex xp-items-start xp-cursor-pointer xp-m-0 xp-font-normal" key={item.value}>
          <div className="xp-h-6 xp-flex xp-items-center">
            <input
              type="radio"
              aria-describedby={`xp-radiogroup-${uniqid}-${idx}`}
              checked={value === item.value}
              onChange={() => onChange(item.value)}
            />
          </div>
          <div className="xp-ml-3">
            <div className="xp-font-medium">{item.label}</div>
            {item.desc ? (
              <p id={`xp-radiogroup-${uniqid}-${idx}`} className="xp-text-gray-500 xp-m-0">
                {item.desc}
              </p>
            ) : null}
          </div>
        </label>
      ))}
    </div>
  );
};
