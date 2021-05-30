const fetch = require('node-fetch');
const path = require('path');
const fs = require('fs');

const exec = async () => {
  const fonts = await (
    await fetch('https://google-fonts.alps.dev/v1/fonts.json')
  ).json();

  const jsonPath = path.resolve(__dirname, '../dist/fonts.json');

  fs.writeFileSync(jsonPath, JSON.stringify(fonts, null, 2));
};

exec();