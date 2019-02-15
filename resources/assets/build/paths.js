const path = require('path');

const project =  {
  sourcePathBase: path.join(__dirname, '../'),
  distBase: path.join(__dirname, '../../../dist/'),
  node_modules: path.join(__dirname, '../../node_modules/'),
};

module.exports = {
  ...project,
};