import createPreviewUpdater from './lib/preview-updater';
import createFormSubmitter from './lib/form-submitter';
import attachInteractivePadding from './lib/interactive-padding';

const objEqual = (obj1, obj2) => {
  const keys = Object.keys(obj1);

  if (keys.length !== Object.keys(obj2).length) {
    return false;
  }

  for (let key of keys) {
    if (obj1[key] !== obj2[key]) {
      return false;
    }
  }

  return true;
};

const createFieldValueCollector = editor => {
  return () => {
    const values = {};

    [...editor.querySelectorAll('input,select')].forEach(inp => {
      const name = inp.getAttribute('name');

      if (!name) {
        return;
      }

      if (name.indexOf('template') > -1 || name.indexOf('preview') > -1) {
        values[name] = inp.value;
      }
    });

    return values;
  };
}

const createLoadingStateToggle = (previewWrapper) => {
  return loading => {
    if (loading) {
      return previewWrapper.classList.add('loading');
    }

    previewWrapper.classList.remove('loading');
  };
};

const attachPreviewAnchorHandler = (win, anchor) => {
  anchor.addEventListener('click', ev => {
    const image = anchor.querySelector('img');
    const src = image.getAttribute('src');

    if (src.substring(0, 5) !== 'data:') {
      return;
    }

    ev.preventDefault();

    const newWin = win.open('about:blank');
    newWin.document.body.appendChild(image.cloneNode(true));
    newWin.focus();
  }, false);
};

const attachFormHandler = (win, editor, fieldsPane, form, updatePreview, previewStateToggle) => {
  const fieldStateToggle = createLoadingStateToggle(fieldsPane);

  const formCallback = (isFinished) => {
    fieldStateToggle(!isFinished);

    if (isFinished) {
      updatePreview();
    } else {
      previewStateToggle(true);
    }
  };

  const handleSubmit = createFormSubmitter(
    win,
    editor,
    win._alps.actionUrl,
    fieldsPane,
    formCallback,
  );

  form.addEventListener('submit', handleSubmit, false);
};

const attachPreviewEntryHandler = (garnish, craft, collectFieldValues, updatePreview) => {
  garnish.on(craft.BaseElementSelectInput, 'selectElements', ev => {
    const name = ev.target.settings.name;

    if (name.indexOf('template') > -1 || name.indexOf('preview') > -1) {
      updatePreview();
    }
  });

  garnish.on(craft.BaseElementSelectInput, 'removeElements', ev => {
    const name = ev.target.settings.name;
    const values = collectFieldValues();

    delete(values[name]);
    delete(values[name + '[]']);

    updatePreview(values);
  });
};

// const attachDeleteButtonHandler = (editor) => {
//   const buttons = [...editor.querySelectorAll('button[name="op"][value="delete"]')];
//
//   buttons.forEach()
// };

const exec = (win) => {
  const doc = win.document;

  const editor = doc.querySelector('.sp-template-editor');

  if (!editor) {
    return;
  }

  const garnish = win.Garnish;
  const craft = win.Craft;

  const collectFieldValues = createFieldValueCollector(editor);

  const previewStateToggle = createLoadingStateToggle(editor.querySelector('.preview-pane'));

  let originalFieldValues = collectFieldValues();

  const imageElement = doc.querySelector('#preview-image');

  const previewCallback = (values, isFinished) => {
    previewStateToggle(!isFinished);

    originalFieldValues = values;
  };

  const updatePreview = (() => {
    const updater = createPreviewUpdater(
      win._alps.previewUrl,
      imageElement,
      craft?.csrfTokenName,
      craft?.csrfTokenValue,
      previewCallback,
    );

    return (values = null) => {
      if (!values) {
        values = collectFieldValues();
      }
      updater(values);
    };
  })();

  editor.addEventListener('change', () => {
    const values = collectFieldValues();

    if (objEqual(originalFieldValues, values)) {
      return;
    }

    updatePreview(values);
  }, false);

  let form = editor.parentNode;

  while (form.nodeName.toLowerCase() !== 'form') {
    form = form.parentNode;
  }

  const fieldsPane = editor.querySelector('.fields-pane');
  attachFormHandler(win, editor, fieldsPane, form, updatePreview, previewStateToggle);

  attachPreviewAnchorHandler(win, document.querySelector('#preview-anchor'));

  attachPreviewEntryHandler(garnish, craft, collectFieldValues, updatePreview);

  attachInteractivePadding(win, editor);

  editor.querySelector('#refresh-preview-anchor')
    .addEventListener('click', ev => {
      ev.preventDefault();
      updatePreview();
    }, false);
};

export default exec;
