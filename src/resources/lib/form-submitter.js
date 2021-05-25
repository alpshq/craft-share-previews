const createElementWithContent = (doc, type, content) => {
  const el = doc.createElement(type);

  el.innerHTML = content;

  return el;
}

const updateHtml = (doc, formWrapper, response) => {
  const fragment = doc.createDocumentFragment();

  fragment.appendChild(
    createElementWithContent(doc, 'div', response.html)
  );

  if (response.js) {
    fragment.appendChild(
      createElementWithContent(doc, 'script', response.js)
    );
  }

  formWrapper.innerHTML = '';
  formWrapper.appendChild(fragment);
};

const createFormSubmitter = (win, action, formWrapper, callback = (isFinished) => {}) => {
  return async ev => {
    const form = ev.target;

    const formData = new FormData(form);

    if (formData.has('op')) {
      return;
    }

    ev.preventDefault();
    callback(false);

    const body = new URLSearchParams;

    for (const pair of formData) {
      body.append(...pair);
    }

    const response = await fetch(action, {
      method: 'post',
      credentials: 'same-origin',
      headers: {
        'x-requested-with': 'fetch',
      },
      body,
    });

    const json = await response.json();

    updateHtml(win.document, formWrapper, json);

    callback(true);
  };
};

export default createFormSubmitter;