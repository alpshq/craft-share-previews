const handleChange = (win, ev) => {
  const input = ev.target;

  if (input.nodeName.toLowerCase() !== 'input') {
    return;
  }

  const name = input.getAttribute('name');

  if (name.indexOf('_tabs[') < 0) {
    return;
  }

  ev.stopPropagation();
  triggerChangeEvent(win, input);
}

const triggerChangeEvent = (win, input) => {
  const id = input.getAttribute('id');
  const wrapper = input.parentNode;

  const detail = {
    id,
    tabs: wrapper,
    index: Number(input.getAttribute('value')),
    section: win.document.querySelector(`#${id}-section`),
  };

  let event;

  try {
    event = new CustomEvent('tab-change', {detail});
  } catch (e) {
    event = win.document.createEvent('Event');
    event.initEvent('tab-change', true, true);
    event.detail = detail;
  }

  wrapper.dispatchEvent(event);
};

export const getActiveSection = tab => {
  const id = tab.dataset.id;
  const selectedInput = tab.querySelector(`input[name="_tabs[${id}]"]:checked`);

  if (!selectedInput) {
    return null;
  }
  const inputId = selectedInput.getAttribute('id');

  return tab.querySelector(`#${inputId}-section`);
};

export const registerChangeEvents = win => {
  const tabs = [...win.document.querySelectorAll('.alps-tabs[data-event-required]')];

  tabs.forEach(tab => {
    tab.removeAttribute('data-event-required');

    // const id = tab.dataset.id;
    //
    // console.log(id);
    // console.log(`input[name="_tabs[${id}]"][checked]`);
    //
    // const selectedInput = tab.querySelector(`input[name="_tabs[${id}]"]:checked`);
    //
    // console.log(selectedInput);
    // if (selectedInput) {
    //   triggerChangeEvent(win, selectedInput);
    // }

    tab.addEventListener('change', ev => {
      handleChange(win, ev);
    }, false)
  });
};