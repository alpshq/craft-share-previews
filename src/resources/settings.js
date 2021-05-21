const exec = (win) => {
  const doc = win.document;
  const button = doc.querySelector('#settings-btn-preview');

  const generatePreview = (ev) => {
    ev.preventDefault();

    let formEl = ev.target.parentNode;

    while (formEl.nodeName.toLowerCase() !== 'form') {
      formEl = formEl.parentNode;
    }

    let idx = 0;
    [...doc.querySelectorAll('div[data-layer]')].forEach(el => {
      [...el.querySelectorAll('input,select')].forEach(el => {
        const name = el.getAttribute('name');

        if (!name || name.indexOf('layers') < 0) {
          return;
        }

        const newName = name
          .split('[layers][]')
          .join(`[layers][${idx}]`);

        el.setAttribute('name', newName);
      });

      idx++;
    });


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
