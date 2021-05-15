(function(win) {
  const ready = function() {
    const Craft = win.Craft;
    const Garnish = win.Garnish;
    const doc = win.document;

    const wrapper = doc.querySelector('.sp-preview-wrapper');

    if (!wrapper) {
      return;
    }

    const anchor = wrapper.querySelector('a');
    const img = anchor.querySelector('img');

    img.addEventListener('load', function() {
      wrapper.style.opacity = 1;
    }, false);

    img.addEventListener('error', function() {
      wrapper.style.opacity = 1;
      anchor.setAttribute('href', '#');
    });

    Garnish.on(Craft.DraftEditor, 'update', function(event) {
      const draftEditor = event.target;

      const url = Craft.getCpUrl('social-previews/draft', {
        id: draftEditor.settings.draftId,
        ts: new Date().getTime(),
      });

      anchor.setAttribute('href', url);
      wrapper.style.opacity = 0.5;
      img.setAttribute('src', url);
    });
  };

  win.addEventListener('DOMContentLoaded', ready, false);
})(window);
