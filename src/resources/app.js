import settings from './settings';
import templateEditor from './template-editor';
import { registerChangeEvents } from './lib/tabs';

(function(win) {

 const ready = () => {
   registerChangeEvents(win);
   settings(win);
   templateEditor(win);
 };

  win.addEventListener('DOMContentLoaded', ready, false);

})(window);
