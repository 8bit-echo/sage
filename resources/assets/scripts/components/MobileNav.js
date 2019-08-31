export default class MobileNav {
  button = document.getElementById('mobile-menu-toggle');
  nav = document.querySelector('.nav');
  navOpen = false;

  constructor() {
    if (this.nav.maxHeight === 'none') {
      this.navOpen = true;
    }
    this.bindEventListeners();
  }

  bindEventListeners = () => {
    if (this.button instanceof HTMLElement) {
      this.button.addEventListener('click', () => {
        this.toggleMenu();
      });
    }

    window.addEventListener('resize', () => {
      if (window.innerWidth >= 768) {
        this.toggleMenu('open');
      } else {
        this.toggleMenu('closed');
      }
    }, { passive: true });
  }

  toggleMenu = (override) => {
    if (override) {
      if (override === 'open') {
        this.nav.style.maxHeight = `${this.nav.scrollHeight}px`;
        this.navOpen = true;
        // this.nav.parentNode.style.height = 'auto';
      } else {
        this.nav.style.maxHeight = 0;
        this.navOpen = false;
        // this.nav.parentNode.style.height = '0px';
      }

      return;
    }
    this.nav.style.maxHeight = this.navOpen ? 0 : `${this.nav.scrollHeight}px`;
    this.navOpen = !this.navOpen;
    // this.nav.parentNode.style.height = this.navOpen ? 'auto' : 0;
  }
}
