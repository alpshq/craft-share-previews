const colors = require('tailwindcss/colors');

const scale = {
  '0': '0px',
  'px': '1px',
  '0.5': '0.125rem',
  '1': '0.25rem',
  '1.5': '0.375rem',
  '2': '0.5rem',
  '2.5': '0.625rem',
  '3': '0.75rem',
  '3.5': '0.875rem',
  '4': '1rem',
  '5': '1.25rem',
  '6': '1.5rem',
  '7': '1.75rem',
  '8': '2rem',
  '9': '2.25rem',
  '10': '2.5rem',
  '11': '2.75rem',
  '12': '3rem',
  '14': '3.5rem',
  '16': '4rem',
  '20': '5rem',
  '24': '6rem',
  '28': '7rem',
  '32': '8rem',
  '36': '9rem',
  '40': '10rem',
  '44': '11rem',
  '48': '12rem',
  '52': '13rem',
  '56': '14rem',
  '60': '15rem',
  '64': '16rem',
  '72': '18rem',
  '80': '20rem',
  '96': '24rem',

  '124': '30rem',
  '136': '34rem',
  '152': '38rem',
  '168': '42rem',
  '184': '46rem',
  '200': '50rem',
  '216': '54rem',
  '232': '58rem',
};

module.exports = {
  future: {
    // removeDeprecatedGapUtilities: true,
    // purgeLayersByDefault: true,
    // defaultLineHeights: true,
    // standardFontWeights: true
  },
  purge: [
    './src/templates/**/*.twig',
    './src/resources/**/*.js',
  ],
  corePlugins: {
    preflight: false,
  },
  important: '#global-container',
  theme: {
    fontFamily: {
      // sans: ['Inter', 'sans-serif'],
    },
    // colors: {
    //   teal: colors.teal,
    //   yellow: colors.yellow,
    //   amber: colors.amber,
    // },
    extend: {
      colors: {
        'blue-gray': colors.blueGray,
        'cool-gray': colors.coolGray,
        gray: colors.gray,
        'true-gray': colors.trueGray,
        'warm-gray': colors.warmGray,
        red: colors.red,
        orange: colors.orange,
        amber: colors.amber,
        yellow: colors.yellow,
        lime: colors.lime,
        green: colors.green,
        emerald: colors.emerald,
        teal: colors.teal,
        cyan: colors.cyan,
        'light-blue': colors.lightBlue,
        blue: colors.blue,
        indigo: colors.indigo,
        violet: colors.violet,
        purple: colors.purple,
        fuchsia: colors.fuchsia,
        pink: colors.pink,
        rose: colors.rose,

        transparent: 'transparent',
        inherit: 'inherit',
      },

      maxWidth: {
        ...scale,

        '1/6': '16.66%',
        '1/4': '25%',
        '1/3': '33%',
        '2/3': '66%',
        '3/4': '75%',

        '1/10': '10%',
        '2/10': '20%',
        '3/10': '30%',
        '4/10': '40%',
        '5/10': '50%',
        '6/10': '60%',
        '7/10': '70%',
        '8/10': '80%',
        '9/10': '90%',

        '1/1': '100%',
      },
      minWidth: scale,
      width: {
        '9/10': '90%',
        '95/100': '95%',
      }
    }
  },
  variants: {
    extend: {
      backgroundColor: ['active'],
      borderWidth: ['first'],
      gradientColorStops: ['active'],
      outline: ['active'],
      scale: ['group-hover'],
      translate: ['group-hover'],
    },
  },
  plugins: []
}