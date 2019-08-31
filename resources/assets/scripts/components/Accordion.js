/**
 * Creates accordion(s) of content with minimal to no styling based on CSS selector option.
 * @class Accordion
 */
export default class Accordion {
  // defaults
  options = {
    selector: '.accordion',
    transition: '.75s',
    activeClass: 'active',
  };

  headers = [];

  /**
   *Creates an instance of Accordion.
   * @param {Object} _options {selector: String - 'css class selector for elements',
   * transition : String - 'css property for transition duration only',
   * activeClass: String - 'the string name to be applied to an open accordion' }
   * @memberof Accordion
   */
  constructor(_options) {
    this.mergeOptions(_options);
    this.bindEvents();
  }

  /**
   * merges default options with any options passed by user
   *
   * @memberof Accordion
   * @param {Object} _options private, passed automatically.
   */
  mergeOptions = _options => {
    for (const key in _options) {
      if (_options.hasOwnProperty(key)) {
        this.options[key] = _options[key];
      }
    }
  };

  /**
   * log info and options from this object.
   *
   * @static
   * @memberof Accordion
   */
  info = () => {
    console.log(this);
  };

  /**
   * adds event listener for click event to the elements.
   *
   * @memberof Accordion
   */
  bindEvents = () => {
    this.headers = document.querySelectorAll(
      `${this.options.selector} > *:first-child`,
    );

    for (const header of this.headers) {
      const content = header.nextElementSibling;
      this.addBaseStyles(header, content);
      header.addEventListener('click', e => {
        e.preventDefault();
        header.classList.toggle(this.options.activeClass);

        content.style.maxHeight =
          content.style.maxHeight === '0px'
            ? `${content.scrollHeight}px`
            : '0px';
      });
    }
  };

  /**
   * adds base styles to the accordion(s) and nothing else.
   *
   * @memberof Accordion
   */
  addBaseStyles = (headerEl, contentEl) => {
    headerEl.style.cursor = 'pointer';
    headerEl.style.padding = '.5em';
    contentEl.style.overflow = 'hidden';
    contentEl.style.maxHeight = 0;
    contentEl.style.transition = `max-height ${this.options.transition} ease`;
  };
}
