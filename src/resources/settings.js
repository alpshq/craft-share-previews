const getTemplateId = node => {
  while (node.hasAttribute('data-template') === false) {
    node = node.parentNode;
  }

  return Number(node.getAttribute('data-template'));
};

const loadTemplates = doc => {
  const selector = '.share-preview-settings div[data-template]';

  return [...doc.querySelectorAll(selector)].map(template => ({
    node: template,
    idx: Number(template.getAttribute('data-template')),

    layers: [...template.querySelectorAll('div[data-layer]')].map(layer => {
      return {
        node: layer,
        idx: Number(layer.getAttribute('data-layer')),
        inputs: [...layer.querySelectorAll('select,input')].filter(inp => {
          return (inp.getAttribute('name') || '').indexOf('[layers]') > -1;
        }),
      };
    }),
  }));
};

const exec = (win) => {
  return;
  const doc = win.document;

  const templates = loadTemplates(doc);

  const removeLayer = (templateIdx, layerIdx) => {

  };

  console.log(templates);

  const getTemplateNode = templateId => {
    return doc.querySelector(`.share-preview-settings div[data-template="${templateId}"`);
  };

  // const reindexLayers = (templateId) => {
  //   const template = getTemplateNode(templateId);
  //   const layers = [...template.querySelectorAll('div[data-layer]')];
  //   let layerIdx = layers.length;
  //
  //   layers.forEach(layer => {
  //     layerIdx -= 1;
  //
  //     [...layer.querySelectorAll('input,select')]
  //       .filter(inp => {
  //         const name = inp.getAttribute('name') || '';
  //         return name.indexOf('[layers]') > -1;
  //       })
  //       .forEach(inp => {
  //         let name = inp.getAttribute('name');
  //
  //         const parts = name.split('[layers]');
  //
  //         name = parts[1].substr(name.indexOf(']'));
  //
  //         const newName = `${parts[0]}[layers][${layerIdx}]${name}`;
  //
  //
  //         inp.setAttribute('name', newName);
  //       });
  //   });
  // };

  templates.forEach(({idx: templateIdx, layers}) => {
    layers.forEach(({idx: layerIdx, node}) => {
      node.querySelector('.btn.delete-layer').addEventListener('click', ev => {
        removeLayer(templateIdx, layerIdx);
      }, false);
    });
  });


  const button = doc.querySelector('#settings-btn-preview');

  const deleteLayerButtons = [...doc.querySelectorAll('.btn.delete-layer')];

  deleteLayerButtons.forEach(btn => {
    btn.addEventListener('click', ev => {
      ev.preventDefault();

      let layer = ev.target.parentNode;

      while (layer.hasAttribute('data-layer') === false) {
        layer = layer.parentNode;
      }

      const templateId = getTemplateId(layer);

      layer.parentNode.removeChild(layer);

      reindexLayers(templateId);
    }, false);
  });

  const generatePreview = (ev) => {
    ev.preventDefault();

    let formEl = ev.target.parentNode;

    while (formEl.nodeName.toLowerCase() !== 'form') {
      formEl = formEl.parentNode;
    }

    // let idx = 0;
    // [...doc.querySelectorAll('div[data-layer]')].forEach(el => {
    //   [...el.querySelectorAll('input,select')].forEach(el => {
    //     const name = el.getAttribute('name');
    //
    //     if (!name || name.indexOf('layers') < 0) {
    //       return;
    //     }
    //
    //     const newName = name
    //       .split('[layers][]')
    //       .join(`[layers][${idx}]`);
    //
    //     el.setAttribute('name', newName);
    //   });
    //
    //   idx++;
    // });


    // [...formEl.querySelectorAll('input,select')].forEach(el => {
    //   if (el.indexOf('layers') < 0) {
    //     return;
    //   }
    // })

    const form = new FormData(formEl);
    const values = [...form.entries()].filter(([key]) => key.indexOf('settings') > -1);


    console.log(formEl, values);
  };

  button.addEventListener('click', generatePreview, false);

  generatePreview({
    target: button,
    preventDefault: () => {},
  });
};

export default exec;
