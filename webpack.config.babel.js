import path from 'path';
// import HtmlWebpackPlugin from 'html-webpack-plugin';

// const PATH_DIST = path.resolve(__dirname, 'dist');
const PATH_BUILD = path.resolve(__dirname, 'dist/build');
const PATH_SRC = path.resolve(__dirname, 'src');

export const config = {
  entry: {
    'resources/app.js': PATH_SRC +'/resources/app.js',
  },
  output: {
    path: PATH_BUILD,
    filename: '[name]'
  },
  plugins: [
    // new HtmlWebpackPlugin({
    //   filename: path.resolve(PATH_DIST, 'index.html'),
    //   template: path.resolve(PATH_SRC, 'index.html')
    // })
  ],
  module : {
    rules : [
      {
        test: /\.m?js$/,
        exclude: /node_modules/,
        use: [{
          loader: 'babel-loader',
          options: {
          },
        }],
      },
      {
        test: /\.(scss|css)$/,
        use: ['style-loader', 'css-loader', 'postcss-loader', 'sass-loader'],
      }
    ]
  },
  resolve: {
    extensions: ['.js', '.scss'],
    alias: {
      // ['~lib']: PATH_JS_LIB,
    }
  },
};

export default config;