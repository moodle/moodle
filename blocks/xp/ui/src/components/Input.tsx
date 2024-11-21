import React from 'react';

const Input: React.FC<React.InputHTMLAttributes<HTMLInputElement>> = ({ className = '', ...props }) => {
  /** Apply those classes for normalised styling across themes and versions. */
  return <input {...props} className={`xp-m-0 form-control ${className}`} />;
};

export const Select: React.FC<React.SelectHTMLAttributes<HTMLSelectElement>> = ({ className = '', ...props }) => {
  /** Apply those classes for normalised styling across themes and versions. */
  return <select {...props} className={`xp-m-0 xp-max-w-auto form-control ${className}`} />;
};

export const Textarea: React.FC<React.TextareaHTMLAttributes<HTMLTextAreaElement>> = ({ className = '', ...props }) => {
  /** Apply those classes for normalised styling across themes and versions. */
  return <textarea {...props} className={`xp-m-0 form-control ${className}`} />;
};

export default Input;
