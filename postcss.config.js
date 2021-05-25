module.exports = ctx => ({
  syntax: 'postcss-scss',
  plugins: {
    'postcss-nested': {},
    'postcss-import': {

    },
    tailwindcss: {},
    autoprefixer: {},
    cssnano: ctx.env === 'production' ? {} : false,
  },
});