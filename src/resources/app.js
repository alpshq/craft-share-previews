import settings from './settings';

(function(win) {

 const ready = () => {
   settings(win);
 };

  win.addEventListener('DOMContentLoaded', ready, false);

})(window);
