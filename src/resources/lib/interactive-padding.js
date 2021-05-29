import interact from 'interactjs';
import { getActiveSection } from './tabs';

const triggerChangeEvent = (win, editor) => {
  let event;

  try {
    event = new InputEvent('change');
  } catch (e) {
    event = win.document.createEvent('Event');
    event.initEvent('change', true, true);
  }

  editor.dispatchEvent(event);
};

const setUpInteractivePadding = (win, editor, area) => {
  const wrapper = area.parentNode;
  const namePrefix = area.getAttribute('data-name-prefix');
  const fieldWrapper = editor.querySelector(`.padding[data-name-prefix="${namePrefix}"]`);
  const dimensionsWrapper = area.firstElementChild;

  const paddingInputs = {
    left: editor.querySelector(`input[name="${namePrefix}[paddingLeft]"]`),
    top: editor.querySelector(`input[name="${namePrefix}[paddingTop]"]`),
    right: editor.querySelector(`input[name="${namePrefix}[paddingRight]"]`),
    bottom: editor.querySelector(`input[name="${namePrefix}[paddingBottom]"]`),
  };

  let width = wrapper.offsetWidth;
  let height = wrapper.offsetHeight;

  let multiplierWidth = 1200 / width;
  let multiplierHeight = 630 / height;

  let padding = {};

  const collectFromFields = () => {
    padding = {
      left: parseInt(paddingInputs.left.value, 10) / multiplierWidth,
      top: parseInt(paddingInputs.top.value, 10) / multiplierHeight,
      right: parseInt(paddingInputs.right.value, 10) / multiplierWidth,
      bottom: parseInt(paddingInputs.bottom.value, 10) / multiplierHeight,
    }
  }

  collectFromFields();

  const applyPadding = padding => {
    area.style.transform = `translate(${padding.left}px, ${padding.top}px)`;
    area.style.width = `${width - padding.left - padding.right}px`;
    area.style.height = `${height - padding.top - padding.bottom}px`;

    const upscaled = upscale(padding);

    const realWidth = 1200 - upscaled.left - upscaled.right;
    const realHeight = 630 - upscaled.top - upscaled.bottom;

    dimensionsWrapper.textContent = `${realWidth}x${realHeight}`;
  };

  const upscale = values => ({
    left: Math.round(values.left * multiplierWidth),
    top: Math.round(values.top * multiplierHeight),
    right: Math.round(values.right * multiplierWidth),
    bottom: Math.round(values.bottom * multiplierHeight),
  });

  const setPadding = (values, fromInputs = false) => {
    padding = {...padding, ...values};

    Object.keys(padding).forEach(key => {
      if (padding[key] <= 0.5) {
        padding[key] = 0;
      }
    });

    applyPadding(padding);

    if (fromInputs) {
      return;
    }

    const upscaled = upscale(padding);

    Object.keys(upscaled).forEach(key => paddingInputs[key].value = Math.round(upscaled[key]));

    triggerChangeEvent(win, editor);
  };

  fieldWrapper.addEventListener('change', () => {
    collectFromFields();
    setPadding(padding, true);
  }, false);

  applyPadding(padding);

  let resizeDelta = {};

  interact(area)
    .draggable({
      modifiers: [
        interact.modifiers.restrict({
          restriction: 'parent',
        }),
      ],
      listeners: {
        move: ev => {
          let left = padding.left + ev.dx;
          let top = padding.top + ev.dy;

          if (left < 0) {
            left = 0;
          }

          if (top < 0) {
            top = 0;
          }

          let right = padding.right + (padding.left - left);
          let bottom = padding.bottom + (padding.top - top);

          if (right < 0) {
            left = left + right;
            right = 0;
          }

          if (bottom < 0) {
            top = top + bottom;
            bottom = 0;
          }

          setPadding({
            left,
            top,
            right,
            bottom,
          });
        },
      },
    })
    .resizable({
      edges: {
        top: true,
        left: true,
        bottom: true,
        right: true,
      },
      invert: 'reposition',
      listeners: {
        start: () => {
          resizeDelta = {...padding};
        },
        move: ev => {
          resizeDelta.left = resizeDelta.left + ev.deltaRect.left;
          resizeDelta.top = resizeDelta.top + ev.deltaRect.top;
          resizeDelta.right = resizeDelta.right + ev.deltaRect.right * -1;
          resizeDelta.bottom = resizeDelta.bottom + ev.deltaRect.bottom * -1;

          setPadding({
            left: resizeDelta.left >= 0 ? resizeDelta.left : 0,
            top: resizeDelta.top >= 0 ? resizeDelta.top : 0,
            right: resizeDelta.right >= 0 ? resizeDelta.right : 0,
            bottom: resizeDelta.bottom >= 0 ? resizeDelta.bottom : 0,
          });
        },
      },
    });
};

const attachInteractivePadding = (win, editor) => {
  const tabs = [...editor.querySelectorAll('.alps-tabs')];

  const registered = {};

  const setUpProxy = (id, section) => {
    if (registered?.[id] === section) {
      return;
    }

    const area = section.querySelector('.interactive-padding > div');

    if (!area) {
      return;
    }

    registered[id] = section;

    setUpInteractivePadding(win, editor, area);
  };

  tabs.forEach(tab => {
    const section = getActiveSection(tab);

    if (section) {
      setUpProxy(tab.dataset.id, section);
    }

    tab.addEventListener('tab-change', ev => {
      const {id, section} = ev.detail;

      setUpProxy(id, section);
    }, false);
  });
};

export default attachInteractivePadding;