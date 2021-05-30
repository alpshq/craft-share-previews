let variants;

const setUpHandlers = (win, element) => {
  if (!variants) {
    variants = JSON.parse(win._alps.fontVariants);
  }

  const inputs = [...element.querySelectorAll('select[data-font-family]')];

  inputs.forEach(input => {
    let variantInput;

    input.addEventListener('change', ev => {
      const input = ev.target;

      if (!variantInput && input.dataset['variant-field']) {
        variantInput = win.document.querySelector(`#${input.dataset['variant-field']}`);
      }

      if (!variantInput) {
        return;
      }

      const prevVariantValue = variantInput.value;

      const fragment = win.document.createDocumentFragment();

      const fontVariants = variants[input.value] || [];

      const prevVariantValueExists = fontVariants
        .filter(({value}) => value === prevVariantValue)
        .length;

      fontVariants.forEach(variant => {
        const opt = win.document.createElement('option');
        opt.setAttribute('value', variant.value);
        opt.textContent = variant.label;
        opt.selected = prevVariantValueExists
          ? variant.value === prevVariantValue
          : variant.default;

        fragment.appendChild(opt);
      });

      variantInput.innerHTML = '';
      variantInput.appendChild(fragment);
    }, false);
  });

};

export default setUpHandlers;