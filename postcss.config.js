module.exports = ctx => ({
  syntax: 'postcss-scss',
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
    cssnano: ctx.env === 'production' ? {} : false,
  },
});