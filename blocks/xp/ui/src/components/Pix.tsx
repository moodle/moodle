import React from 'react';
import { imageUrl } from '../lib/moodle';

const Pix: React.FC<{
  id: string;
  component?: string;
  className?: string;
  alt?: string;
}> = ({ id, component = 'block_xp', className, alt = '' }) => {
  return <img src={imageUrl(id, component)} alt={alt} className={className} />;
};

export default Pix;
