const calculateValue = ev => {
  const inp = ev.target;
  const value = inp.value;

  console.log(value, 'return (' + value + ')');
  const calculatedValue = Function('return (' + value + ')')();

  console.log(calculatedValue);
};

const exec = (win) => {
  const doc = win.document;

  const editor = doc.querySelector('.template-editor');

  if (!editor) {
    return;
  }

  editor.addEventListener('change', function() {
    console.log('change');
  }, false);

  // const calcInputs = [...editor.querySelectorAll('[data-features="calc"]')];
  //
  // calcInputs.forEach(inp => {
  //   inp.addEventListener('keyup', calculateValue, false);
  //   inp.addEventListener('change', calculateValue, false);
  // });
};

export default exec;
