const path = require('path');
const fs = require('fs');
const webpack = require('webpack');
const MinifyPlugin = require('babel-minify-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const BrowserSync = require('browser-sync-webpack-plugin');
const SassPlugin = require('sass-webpack-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const project = require('./paths');
const userConfig = require('../config.json');

/**
 * gets all the files in /src/js and maps them to their own entry objects for code splitting purposes.
 * @param {string} type the type of asset to get 'scripts' || 'styles'
 */
function getEntryPoints(type) {
  const entryPoints = type === 'scripts' ? {} : {};
  const sourcePath = path.join(project.sourcePathBase, `${type}/`);
  const fullSrcPath = sourcePath;

  const files = fs.readdirSync(fullSrcPath);
  files.forEach((file) => {
    if (file[0] === '.') {
      return;
    }
    const stat = fs.statSync(`${fullSrcPath  }/${  file}`);
    // ignore if partial file.
    if (stat.isFile() && file.substr(0, 1) !== '_') {
      const baseName = path.basename(file, path.extname(file));

      switch (type) {
        case 'scripts':
          entryPoints[baseName] = `${project.sourcePathBase  }scripts/${  file}`;
          break;

        case 'styles':
          entryPoints[`resources/assets/styles/${file}`] = `./styles/${file.replace('.scss', '.css')}`;
          // entryPoints.push(`styles/${file.replace('scss', 'css')}`);
          break;

        default:
          break;
      }
    }
  });
  return entryPoints;
}

/**
 * Writes a plain text file to the dist folder to use as a query string for cache busting
 *
 */
function EmitHash() {
  this.options = {
    outputPath: project.distBase,
    outputFileName: 'webpack_hash',
  };
}

EmitHash.prototype.apply = function (compiler) {
  compiler.plugin(
    'after-emit',
    (compilation, callback) => {
      const outputFile = path.join(this.options.outputPath, this.options.outputFileName);

      fs.writeFileSync(outputFile, compilation.getStats().hash);

      callback();

      console.log('Writing hash file: ' + outputFile);
    },
  );
};

const config = {
  entry: getEntryPoints('scripts'),
  output: {
    publicPath: project.sourcePathBase,
    filename: 'scripts/[name].js',
    path: project.distBase,
  },

  module: {
    rules: [
      // this allows glob pattern importing in scss and js files (eg: import ./autoload/**/*)
      {
        enforce: 'pre',
        test: /\.([jt]s|s?[ca]ss)$/,
        include: project.sourcePathBase,
        loader: 'import-glob',
      },
      {
        test: /\.s?css$/,
        use: ExtractTextPlugin.extract({
          use: [{
            loader: 'css-loader',
            options: {
              // sourceMap: process.env.NODE_ENV !== 'production'
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              // sourceMap: process.env.NODE_ENV !== 'production'
            },
          },
          {
            loader: 'sass-loader',
            options: {
              // for making global.scss available in all files.
              // data: '@import \'global\'',
              include: path.resolve(`${project.sourcePathBasestyles}styles/`),
              // sourceMap: process.env.NODE_ENV !== 'production'
            },
          },
          ],
        }),
      },
      {
        test: /\.jsx?$/,
        // supports writing in es 6, 2017 styles.
        exclude: /node_modules/,
        loader: 'babel-loader',
      },
      {
        test: /\.tsx?/,
        exclude: '/node_modules/',
        loader: 'ts-loader',
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: {
            scss: ['vue-style-loader', 'css-loader', 'sass-loader'],
          },
          // other vue-loader options go here
        },
      },
      {
        test: /\.(png|jpe?g|gif|svg|ico)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 10000,
          name: 'images/[name].[ext]',
        },
      },
      {
        test: /\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 10000,
          name: 'media/[name].[ext]',
        },
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 10000,
          name: 'fonts/[name].[ext]',
        },
      },
    ],
  },
  resolve: {
    alias: {
      vue$: 'vue/dist/vue.esm.js',
    },
    extensions: ['*', '.js', '.vue', '.json', '.ts'],
  },
  // Source maps with line numbers only
  devtool: '#cheap-module-eval-source-map',

  plugins: [
    // this is for postcss-loader
    require('autoprefixer'),

    // extract multiple css files.
    new SassPlugin(getEntryPoints('styles'), {
      sourceMap: process.env.NODE_ENV !== 'production',
      sass: {
        outputStyle: 'compressed',
      },
      autoprefixer: true,
    }),

    new StyleLintPlugin({
      failOnError: false,
      syntax: 'scss',
    }),


    // // required for scss -> css compilation within vue templates. maybe dont need this anymore.
    // new ExtractTextPlugin({
    //   filename: '[name].css',
    //   allChunks: false,
    //   publicPath: project.sourcePathBase,
    // })


    // register variables or scripts globally. ()
    // new webpack.ProvidePlugin({
    //   $: 'jquery',
    //   jQuery: 'jquery',
    //   'window.$': 'jquery',
    //   'window.jQuery': 'jquery',
    // }),
    // live reload of webpages on change.
    new EmitHash(),
    // use this to exclude packages from being included.
    new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
  ],
};

if (userConfig.useBrowserSync) {
  config.plugins.push(
    new BrowserSync({
      open: false,
      proxy: userConfig.devUrl,
      files: userConfig.watchGlobs,
      injectChanges: true,
      reloadDelay: 0,
      notify: false,
      https: {
        key: '/usr/local/etc/httpd/ssl/server.key',
        cert: '/usr/local/etc/httpd/ssl/server.crt',
      },
    }) 
);
}

// Change options for Production
if (process.env.NODE_ENV === 'production') {
  console.log('> Building for production...');
  config.plugins.push(
    // js minification
    new MinifyPlugin(),
    // css minification
    new OptimizeCssAssetsPlugin(),
    // vue / react production mode
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: '"production"',
      },
    }),
  );
  // source maps
  delete config.devtool;
}

module.exports = config;
