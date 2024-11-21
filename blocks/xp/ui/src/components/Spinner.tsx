import React from 'react';
import { useString } from '../lib/hooks';
import Pix from './Pix';

const Spinner: React.FC<{
  className?: string;
}> = ({ className }) => {
  const alt = useString('loadinghelp', 'core');
  return <Pix id="y/loading" component="core" className={className} alt={alt} />;
};

export default Spinner;
