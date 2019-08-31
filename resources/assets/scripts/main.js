/** import global dependencies */
/* eslint-disable no-new */
import LazyLoader from 'blazy';
import MobileNav from './components/MobileNav';

(() => {
  /** Begin scripting global functionality. */
  console.log('main.js');
  const lazyloader = new LazyLoader({ selector: '.lazy' });
  // make available globally
  window.lazyloader = lazyloader;

  // handle Nav button.
  new MobileNav();

  // check for accordions.
  const accordions = document.querySelectorAll('.accordion');
  if (accordions.length >= 1) {
    window.accordions = new Accordion();
  }
})();
