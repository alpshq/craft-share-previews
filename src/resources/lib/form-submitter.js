import attachInteractivePadding from './interactive-padding';

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

const handleDeletionConfirmation = (ev, win, title) => {
  const message = win._alps.deleteConfirmation
    .split('{name}').join(title || win._alps.templateName);

  const confirm = win.confirm(message);

  if (!confirm) {
    ev.preventDefault();
  }
};

const createFormSubmitter = (win, editor, action, formWrapper, callback = (isFinished) => {}) => {
  return async ev => {
    const form = ev.target;

    const formData = new FormData(form);

    if (formData.has('op') && formData.get('op') === 'delete') {
      handleDeletionConfirmation(ev, win, formData.get('template[name]'));
      return;
    }

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

    attachInteractivePadding(win, editor)

    callback(true);
  };
};

export default createFormSubmitter;