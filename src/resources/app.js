import settings from './settings';
import templateEditor from './template-editor';

(function(win) {

 const ready = () => {
   settings(win);
   templateEditor(win);
 };

  win.addEventListener('DOMContentLoaded', ready, false);

})(window);
