module.exports = ctx => ({
  syntax: 'postcss-scss',
  plugins: {
    'postcss-nested': {},
    tailwindcss: {},
    autoprefixer: {},
    cssnano: ctx.env === 'production' ? {} : false,
  },
});