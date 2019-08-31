module.exports = {
  'root': true,
  'extends': ['airbnb-base', 'plugin:vue/recommended'],
  'globals': {
    'wp': true,
  },
  'env': {
    'node': true,
    'es6': true,
    'amd': true,
    'browser': true,
    'jquery': true,
  },
  'parserOptions': {
    'ecmaVersion': 2018,
    'sourceType': 'module',
    'parser': 'babel-eslint'
  },
  'plugins': [
    'import',
    'vue'
  ],
  'settings': {
    'import/core-modules': [],
    'import/ignore': [
      'node_modules',
      '\\.(coffee|scss|css|less|hbs|svg|json)$',
    ],
  },
  'rules': {
    "arrow-parens": 0,
    "import/no-named-as-default": 0,
    "import/no-named-as-default-member": 0,
    "lines-between-class-members": 0,
    "no-console": 0,
    "quotes": ["error", "single"],
    "no-restricted-syntax": 0,
    "radix": 0,
    "no-param-reassign": 0,
    "no-prototype-builtins": 0,
    "operator-linebreak": 0,
    "no-unused-vars": 'warn',
    "array-callback-return": 0,
    "no-underscore-dangle": 0
  },
};
