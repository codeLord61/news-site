import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Highlight from '@tiptap/extension-highlight';
import TextAlign from '@tiptap/extension-text-align';
import Image from '@tiptap/extension-image';
import { FontSize, TextStyle } from '@tiptap/extension-text-style';
import FontFamily from '@tiptap/extension-font-family';
import { Color } from '@tiptap/extension-color';

const ReporterImage = Image.extend({
  addAttributes() {
    const parentAttributes = this.parent?.() || {};

    return {
      ...parentAttributes,
      dataMediaId: {
        default: null,
        parseHTML: (element) => element.getAttribute('data-media-id'),
        renderHTML: (attributes) => {
          if (!attributes.dataMediaId) {
            return {};
          }

          return {
            'data-media-id': String(attributes.dataMediaId),
          };
        },
      },
    };
  },
});

window.addEventListener('load', () => {
  const form = document.getElementById('reporter-article-form');
  if (!form) {
    return;
  }

  const appBaseUrl = window.appBaseUrl || '';
  const initialArticle =
    window.reporterArticleInitialData && typeof window.reporterArticleInitialData === 'object'
      ? window.reporterArticleInitialData
      : null;

  const titleInput = document.getElementById('titleInput');
  const excerptInput = document.getElementById('excerptInput');
  const categorySelect = document.getElementById('categorySelect');
  const tagSelect = document.getElementById('tagSelect');
  const articleIdInput = document.getElementById('articleId');
  const thumbnailMediaIdInput = document.getElementById('thumbnailMediaId');
  const contentHtmlInput = document.getElementById('contentHtml');
  const alertBox = document.getElementById('articleFormAlert');

  const titleCounter = document.getElementById('titleCounter');
  const excerptCounter = document.getElementById('excerptCounter');
  const thumbnailAltCounter = document.getElementById('thumbnailAltCounter');
  const thumbnailCaptionCounter = document.getElementById('thumbnailCaptionCounter');
  const contentError = document.getElementById('contentError');

  const thumbnailFileInput = document.getElementById('thumbnailFileInput');
  const thumbnailUrlInput = document.getElementById('thumbnailUrlInput');
  const thumbnailAltInput = document.getElementById('thumbnailAltInput');
  const thumbnailCaptionInput = document.getElementById('thumbnailCaptionInput');

  const saveButtons = [
    document.getElementById('saveDraftBtn'),
    document.getElementById('savePublishBtn'),
  ].filter(Boolean);

  const fieldErrors = {
    title: document.getElementById('titleError'),
    excerpt: document.getElementById('excerptError'),
    category_id: document.getElementById('categoryError'),
    tag_id: document.getElementById('tagError'),
    thumbnail_image: document.getElementById('thumbnailFileError'),
    thumbnail_image_url: document.getElementById('thumbnailUrlError'),
    thumbnail_alt_text: document.getElementById('thumbnailAltError'),
    thumbnail_caption: document.getElementById('thumbnailCaptionError'),
    thumbnail_media_id: document.getElementById('thumbnailUrlError'),
    content_html: contentError,
    intent: contentError,
    media_ids: contentError,
  };

  const editorElement = document.getElementById('wysiwyg-example');
  if (!editorElement) {
    return;
  }

  const editor = new Editor({
    element: editorElement,
    extensions: [
      StarterKit,
      Highlight,
      TextAlign.configure({
        types: ['heading', 'paragraph'],
      }),
      TextStyle,
      FontSize,
      Color,
      FontFamily,
      ReporterImage,
    ],
    content: '<p></p>',
    editorProps: {
      attributes: {
        class: 'format lg:format-lg max-w-none focus:outline-none min-h-[18rem] text-heading',
      },
    },
  });

  const ICON_BUTTON_CLASS =
    'p-1.5 text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary';

  /**
   * Ensure a tooltip element exists next to a toolbar button.
   *
   * Input: button element, tooltip id, tooltip text.
   * Output: no return value; creates/updates tooltip DOM node.
   */
  function ensureTooltip(button, tooltipId, tooltipText) {
    if (!button || !tooltipId || !tooltipText) {
      return;
    }

    let tooltip = document.getElementById(tooltipId);
    if (!tooltip) {
      tooltip = document.createElement('div');
      tooltip.id = tooltipId;
      tooltip.setAttribute('role', 'tooltip');
      tooltip.className =
        'absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-dark-strong rounded-base shadow-xs opacity-0 tooltip';
      tooltip.innerHTML = `${tooltipText}<div class="tooltip-arrow" data-popper-arrow></div>`;
      button.insertAdjacentElement('afterend', tooltip);
    } else {
      tooltip.innerHTML = `${tooltipText}<div class="tooltip-arrow" data-popper-arrow></div>`;
    }
  }

  /**
   * Apply consistent icon-button styling and tooltip wiring.
   *
   * Input: config object {id|selector, label, tooltipId?, tooltipText?, svg}.
   * Output: no return value; mutates button class/attributes/innerHTML.
   */
  function applyIconButton(config) {
    const button = config.id
      ? document.getElementById(config.id)
      : document.querySelector(config.selector || '');

    if (!button) {
      return;
    }

    button.className = ICON_BUTTON_CLASS;
    if (config.tooltipId) {
      button.setAttribute('data-tooltip-target', config.tooltipId);
      ensureTooltip(button, config.tooltipId, config.tooltipText || '');
    }
    button.innerHTML = `${config.svg}<span class="sr-only">${config.label}</span>`;
  }

  /**
   * Upgrade default TipTap toolbar buttons with icon-only controls.
   *
   * Output: no return value; updates toolbar DOM and initializes Flowbite helpers.
   */
  function enhanceToolbarUi() {
    const iconConfigs = [
      {
        id: 'toggleBoldButton',
        label: 'Bold',
        tooltipId: 'tooltip-bold',
        tooltipText: 'Toggle bold',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5h4.5a3.5 3.5 0 1 1 0 7H8m0-7v7m0-7H6m2 7h6.5a3.5 3.5 0 1 1 0 7H8m0-7v7m0 0H6"/></svg>',
      },
      {
        id: 'toggleItalicButton',
        label: 'Italic',
        tooltipId: 'tooltip-italic',
        tooltipText: 'Toggle italic',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.874 19 6.143-14M6 19h6.33m-.66-14H18"/></svg>',
      },
      {
        id: 'toggleUnderlineButton',
        label: 'Underline',
        tooltipId: 'tooltip-underline',
        tooltipText: 'Toggle underline',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 19h12M8 5v9a4 4 0 0 0 8 0V5M6 5h4m4 0h4"/></svg>',
      },
      {
        id: 'toggleStrikeButton',
        label: 'Strike',
        tooltipId: 'tooltip-strike',
        tooltipText: 'Toggle strike',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6.2V5h12v1.2M7 19h6m.2-14-1.677 6.523M9.6 19l1.029-4M5 5l6.523 6.523M19 19l-7.477-7.477"/></svg>',
      },
      {
        id: 'toggleHighlightButton',
        label: 'Highlight',
        tooltipId: 'tooltip-highlight',
        tooltipText: 'Toggle highlight',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 20H5.5c-.27614 0-.5-.2239-.5-.5v-3c0-.2761.22386-.5.5-.5h13c.2761 0 .5.2239.5.5v3c0 .2761-.2239.5-.5.5H18m-6-1 1.42 1.8933c.04.0534.12.0534.16 0L15 19m-7-6 3.9072-9.76789c.0335-.08381.1521-.08381.1856 0L16 13m-8 0H7m1 0h1.5m6.5 0h-1.5m1.5 0h1m-7-3.00001h4"/></svg>',
      },
      {
        id: 'toggleCodeButton',
        label: 'Code',
        tooltipId: 'tooltip-code',
        tooltipText: 'Format code',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 8-4 4 4 4m8 0 4-4-4-4m-2-3-4 14"/></svg>',
      },
      {
        id: 'toggleTextSizeButton',
        label: 'Text size',
        tooltipId: 'tooltip-text-size',
        tooltipText: 'Text size',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6.2V5h11v1.2M8 5v14m-3 0h6m2-6.8V11h8v1.2M17 11v8m-1.5 0h3"/></svg>',
      },
      {
        id: 'toggleTextColorButton',
        label: 'Text color',
        tooltipId: 'tooltip-text-color',
        tooltipText: 'Text color',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m6.08169 15.9817 1.57292-4m-1.57292 4h-1.1m1.1 0h1.65m-.07708-4 2.72499-6.92967c.0368-.09379.1673-.09379.2042 0l2.725 6.92967m-5.65419 0h-.00607m.00607 0h5.65419m0 0 .6169 1.569m5.1104 4.453c0 1.1025-.8543 1.9963-1.908 1.9963s-1.908-.8938-1.908-1.9963c0-1.1026 1.908-4.1275 1.908-4.1275s1.908 3.0249 1.908 4.1275Z"/></svg>',
      },
      {
        id: 'toggleFontFamilyButton',
        label: 'Font family',
        tooltipId: 'tooltip-font-family',
        tooltipText: 'Font Family',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m10.5785 19 4.2979-10.92966c.0369-.09379.1674-.09379.2042 0L19.3785 19m-8.8 0H9.47851m1.09999 0h1.65m7.15 0h-1.65m1.65 0h1.1m-7.7-3.9846h4.4M3 16l1.56685-3.9846m0 0 2.73102-6.94506c.03688-.09379.16738-.09379.20426 0l2.50367 6.94506H4.56685Z"/></svg>',
      },
      {
        id: 'toggleLeftAlignButton',
        label: 'Align left',
        tooltipId: 'tooltip-left-align',
        tooltipText: 'Align left',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6h8m-8 4h12M6 14h8m-8 4h12"/></svg>',
      },
      {
        id: 'toggleCenterAlignButton',
        label: 'Align center',
        tooltipId: 'tooltip-center-align',
        tooltipText: 'Align center',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h8M6 10h12M8 14h8M6 18h12"/></svg>',
      },
      {
        id: 'toggleRightAlignButton',
        label: 'Align right',
        tooltipId: 'tooltip-right-align',
        tooltipText: 'Align right',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6h-8m8 4H6m12 4h-8m8 4H6"/></svg>',
      },
      {
        id: 'toggleListButton',
        label: 'Toggle list',
        tooltipId: 'tooltip-list',
        tooltipText: 'Toggle list',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"/></svg>',
      },
      {
        id: 'toggleOrderedListButton',
        label: 'Toggle ordered list',
        tooltipId: 'tooltip-ordered-list',
        tooltipText: 'Toggle ordered list',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h8m-8 6h8m-8 6h8M4 16a2 2 0 1 1 3.321 1.5L4 20h5M4 5l2-1v6m-2 0h4"/></svg>',
      },
      {
        id: 'toggleBlockquoteButton',
        label: 'Toggle blockquote',
        tooltipId: 'tooltip-blockquote-list',
        tooltipText: 'Toggle blockquote',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V8a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1Zm0 0v2a4 4 0 0 1-4 4H5m14-6V8a1 1 0 0 0-1-1h-3a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1Zm0 0v2a4 4 0 0 1-4 4h-1"/></svg>',
      },
      {
        id: 'toggleHRButton',
        label: 'Toggle Horizontal Rule',
        tooltipId: 'tooltip-hr-list',
        tooltipText: 'Toggle Horizontal Rule',
        svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 12h14"/><path stroke="currentColor" stroke-linecap="round" d="M6 9.5h12m-12-2h12m-12-2h12m-12 13h12m-12-2h12m-12-2h12"/></svg>',
      },
    ];

    iconConfigs.forEach((config) => applyIconButton(config));

    applyIconButton({
      selector: 'button[data-modal-target="advanced-image-modal"]',
      label: 'Insert advanced image',
      tooltipId: 'tooltip-advanced-image',
      tooltipText: 'Image with settings',
      svg: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7Zm.394 9.553a1 1 0 0 0-1.817.062l-2.5 6A1 1 0 0 0 8 19h8a1 1 0 0 0 .894-1.447l-2-4A1 1 0 0 0 13.2 13.4l-.53.706-1.276-2.553ZM13 9.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" clip-rule="evenodd"/></svg>',
    });

    const formatButton = document.getElementById('typographyDropdownButton');
    if (formatButton && !formatButton.querySelector('svg')) {
      formatButton.innerHTML =
        'Format <svg class="w-3.5 h-3.5 ms-1.5 -me-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>';
    }

    ['typographyDropdown', 'textSizeDropdown', 'textColorDropdown', 'fontFamilyDropdown'].forEach((id) => {
      const dropdown = document.getElementById(id);
      if (dropdown) {
        dropdown.classList.remove('z-10');
      }
    });

    if (typeof window.initFlowbite === 'function') {
      window.initFlowbite();
    }
  }

  enhanceToolbarUi();

  /**
   * Add open/close behavior to typography dropdown.
   *
   * Output: no return value; binds click/escape/outside-click handlers.
   */
  function initTypographyDropdown() {
    const button = document.getElementById('typographyDropdownButton');
    const dropdown = document.getElementById('typographyDropdown');
    if (!button || !dropdown) {
      return;
    }

    const hide = () => {
      dropdown.classList.add('hidden');
    };

    button.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();
      dropdown.classList.toggle('hidden');
    });

    dropdown.addEventListener('click', (event) => {
      event.stopPropagation();
    });

    document.addEventListener('click', hide);
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        hide();
      }
    });
  }

  initTypographyDropdown();

  const advancedImageForm = document.getElementById('advancedImageForm');
  const advancedImageFile = document.getElementById('advancedImageFile');
  const advancedImageUrl = document.getElementById('image-url');
  const advancedImageAlt = document.getElementById('image-alt');
  const advancedImageTitle = document.getElementById('image-title');
  const advancedImageError = document.getElementById('advancedImageError');

  /**
   * Hide the form-level alert area and clear previous message/classes.
   */
  function clearAlert() {
    if (!alertBox) {
      return;
    }

    alertBox.classList.add('hidden');
    alertBox.textContent = '';
    alertBox.className = 'hidden mb-4 rounded-base border px-4 py-3 text-sm';
  }

  /**
   * Display form-level feedback.
   *
   * Input: message string + success boolean.
   * Output: no return value; updates alert box text/classes.
   */
  function showAlert(message, isSuccess) {
    if (!alertBox) {
      return;
    }

    clearAlert();
    alertBox.textContent = message;

    if (isSuccess) {
      alertBox.classList.add('border-green-200', 'bg-green-50', 'text-green-700');
    } else {
      alertBox.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
    }

    alertBox.classList.remove('hidden');
  }

  /**
   * Clear all per-field validation messages.
   */
  function clearFieldErrors() {
    Object.values(fieldErrors).forEach((element) => {
      if (element) {
        element.textContent = '';
        element.classList.add('hidden');
      }
    });
  }

  /**
   * Set one field-level error message by error key.
   *
   * Input example: key="title", message="Title is required."
   */
  function setFieldError(key, message) {
    const errorEl = fieldErrors[key];
    if (!errorEl) {
      return;
    }

    errorEl.textContent = message;
    errorEl.classList.remove('hidden');
  }

  /**
   * Update "N/255" counters and color state.
   *
   * Input: text input and counter element.
   * Output: no return value; updates counter text and warning colors.
   */
  function updateCounter(input, counter) {
    if (!input || !counter) {
      return;
    }

    const len = input.value.length;
    counter.textContent = `${len}/255`;

    counter.classList.remove('text-body', 'text-orange-500', 'text-red-600');
    if (len > 255) {
      counter.classList.add('text-red-600');
    } else if (len >= 240) {
      counter.classList.add('text-orange-500');
    } else {
      counter.classList.add('text-body');
    }
  }

  /**
   * Validate form fields before submit.
   *
   * Output: true when valid; false when one or more rules fail.
   */
  function validateForm() {
    clearFieldErrors();
    clearAlert();

    let isValid = true;
    const trimmedTitle = titleInput.value.trim();

    if (!trimmedTitle) {
      setFieldError('title', 'Title is required.');
      isValid = false;
    }

    if (titleInput.value.length > 255) {
      setFieldError('title', 'Title may not be greater than 255 characters.');
      isValid = false;
    }

    if (excerptInput.value.length > 255) {
      setFieldError('excerpt', 'Excerpt may not be greater than 255 characters.');
      isValid = false;
    }

    if ((thumbnailAltInput?.value || '').length > 255) {
      setFieldError('thumbnail_alt_text', 'Thumbnail alt text may not be greater than 255 characters.');
      isValid = false;
    }

    if ((thumbnailCaptionInput?.value || '').length > 255) {
      setFieldError('thumbnail_caption', 'Thumbnail caption may not be greater than 255 characters.');
      isValid = false;
    }

    return isValid;
  }

  /**
   * Parse optional positive integer from string/unknown input.
   *
   * Input examples: "12" -> 12, "" -> null, "abc" -> null.
   * Output: positive integer or null.
   */
  function parseOptionalInt(value) {
    if (value === null || value === undefined || value === '') {
      return null;
    }

    const parsed = Number.parseInt(value, 10);
    if (Number.isNaN(parsed) || parsed <= 0) {
      return null;
    }

    return parsed;
  }

  /**
   * Fill form fields from server-provided initial article object (edit mode).
   *
   * Input source: window.reporterArticleInitialData.
   * Output: no return value; populates inputs and editor HTML.
   */
  function hydrateInitialArticle() {
    if (!initialArticle) {
      return;
    }

    const articleId = parseOptionalInt(initialArticle.id);
    if (articleId !== null && articleIdInput) {
      articleIdInput.value = String(articleId);
    }

    if (titleInput && typeof initialArticle.title === 'string') {
      titleInput.value = initialArticle.title;
    }

    if (excerptInput && typeof initialArticle.excerpt === 'string') {
      excerptInput.value = initialArticle.excerpt;
    }

    const categoryId = parseOptionalInt(initialArticle.category_id);
    if (categoryId !== null && categorySelect) {
      categorySelect.value = String(categoryId);
    }

    const tagId = parseOptionalInt(initialArticle.tag_id);
    if (tagId !== null && tagSelect) {
      tagSelect.value = String(tagId);
    }

    const thumbnailMediaId = parseOptionalInt(initialArticle.thumbnail_media_id);
    if (thumbnailMediaId !== null && thumbnailMediaIdInput) {
      thumbnailMediaIdInput.value = String(thumbnailMediaId);
    }

    if (thumbnailUrlInput && typeof initialArticle.thumbnail_image_url === 'string') {
      thumbnailUrlInput.value = initialArticle.thumbnail_image_url;
    }

    if (thumbnailAltInput && typeof initialArticle.thumbnail_alt_text === 'string') {
      thumbnailAltInput.value = initialArticle.thumbnail_alt_text;
    }

    if (thumbnailCaptionInput && typeof initialArticle.thumbnail_caption === 'string') {
      thumbnailCaptionInput.value = initialArticle.thumbnail_caption;
    }

    if (typeof initialArticle.content === 'string' && initialArticle.content.trim() !== '') {
      editor.commands.setContent(initialArticle.content);
      if (contentHtmlInput) {
        contentHtmlInput.value = initialArticle.content;
      }
    }
  }

  /**
   * Toggle disabled/loading state for save buttons.
   *
   * Input: boolean isSaving.
   */
  function setSavingState(isSaving) {
    saveButtons.forEach((button) => {
      button.disabled = isSaving;
      if (isSaving) {
        button.classList.add('opacity-70', 'cursor-not-allowed');
      } else {
        button.classList.remove('opacity-70', 'cursor-not-allowed');
      }
    });
  }

  /**
   * Extract unique media IDs from editor HTML image nodes.
   *
   * Input: HTML string.
   * Output: array of integer media ids from `data-media-id`.
   */
  function collectMediaIds(html) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');

    const ids = [];
    const seen = new Set();

    doc.querySelectorAll('img[data-media-id]').forEach((img) => {
      const raw = img.getAttribute('data-media-id') || '';
      const mediaId = Number.parseInt(raw, 10);
      if (!Number.isInteger(mediaId) || mediaId <= 0 || seen.has(mediaId)) {
        return;
      }

      seen.add(mediaId);
      ids.push(mediaId);
    });

    return ids;
  }

  /**
   * Hide Flowbite dropdown by id (instance API first, class fallback).
   */
  function hideDropdown(id) {
    if (window.FlowbiteInstances && typeof window.FlowbiteInstances.getInstance === 'function') {
      const instance = window.FlowbiteInstances.getInstance('Dropdown', id);
      if (instance && typeof instance.hide === 'function') {
        instance.hide();
        return;
      }
    }

    const el = document.getElementById(id);
    if (el) {
      el.classList.add('hidden');
    }
  }

  /**
   * Hide advanced image modal using Flowbite instance or DOM fallback.
   */
  function hideAdvancedImageModal() {
    if (window.FlowbiteInstances && typeof window.FlowbiteInstances.getInstance === 'function') {
      const instance = window.FlowbiteInstances.getInstance('Modal', 'advanced-image-modal');
      if (instance && typeof instance.hide === 'function') {
        instance.hide();
        return;
      }
    }

    const modal = document.getElementById('advanced-image-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.setAttribute('aria-hidden', 'true');
    }
  }

  /**
   * Show/hide advanced image modal validation message.
   *
   * Input: error text or empty/falsey to clear.
   */
  function setAdvancedImageError(message) {
    if (!advancedImageError) {
      return;
    }

    if (!message) {
      advancedImageError.textContent = '';
      advancedImageError.classList.add('hidden');
      return;
    }

    advancedImageError.textContent = message;
    advancedImageError.classList.remove('hidden');
  }

  /**
   * Upload image to reporter media endpoint.
   *
   * Input:
   * - either a local File object OR a remote image URL
   * - optional metadata (alt/title/isThumbnail)
   * Output: uploaded media object from API (`data.data` payload).
   */
  async function uploadImage({ file = null, imageUrl = '', altText = '', title = '', isThumbnail = false }) {
    const endpoint = `${appBaseUrl}/api/v1/reporter/media/images`;
    let response;

    if (file) {
      const formData = new FormData();
      formData.append('image', file);
      formData.append('alt_text', altText || '');
      formData.append('title', title || '');
      formData.append('is_thumbnail', isThumbnail ? '1' : '0');

      response = await fetch(endpoint, {
        method: 'POST',
        body: formData,
      });
    } else {
      response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify({
          image_url: imageUrl,
          alt_text: altText || '',
          title: title || '',
          is_thumbnail: isThumbnail,
        }),
      });
    }

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
      if (response.status === 422 && data.fields) {
        const firstError = Object.values(data.fields)[0];
        throw new Error(typeof firstError === 'string' ? firstError : 'Invalid image input.');
      }

      throw new Error(data.error || 'Failed to upload image.');
    }

    return data.data;
  }

  /**
   * Insert image node into editor document.
   *
   * Input: media object + optional position.
   * Output: no return value; editor content is mutated.
   */
  function insertImageAt(media, position = null) {
    const attrs = {
      src: media.file_url,
      alt: media.alt_text || '',
      title: media.title || '',
      dataMediaId: media.media_id,
    };

    if (typeof position === 'number') {
      editor
        .chain()
        .focus()
        .insertContentAt(position, {
          type: 'image',
          attrs,
        })
        .run();
      return;
    }

    editor.chain().focus().setImage(attrs).run();
  }

  /**
   * Save article as draft or submit for editorial review.
   *
   * Input: intent string ("draft" or "submit").
   * Output: no return value; sends API request and updates form UI state.
   */
  async function submitArticle(intent) {
    if (!validateForm()) {
      return;
    }

    setSavingState(true);

    try {
      let thumbnailImageUrl = (thumbnailUrlInput?.value || '').trim();
      let thumbnailMediaId = parseOptionalInt(thumbnailMediaIdInput?.value || '');
      const thumbnailAltText = (thumbnailAltInput?.value || '').trim();
      const thumbnailCaption = (thumbnailCaptionInput?.value || '').trim();

      const thumbnailFile = thumbnailFileInput?.files?.[0] || null;
      if (thumbnailFile) {
        const uploadedThumbnail = await uploadImage({
          file: thumbnailFile,
          altText: thumbnailAltText,
          title: thumbnailCaption,
          isThumbnail: true,
        });

        thumbnailImageUrl = uploadedThumbnail.file_url || '';
        thumbnailMediaId = parseOptionalInt(uploadedThumbnail.media_id);

        if (thumbnailUrlInput) {
          thumbnailUrlInput.value = thumbnailImageUrl;
        }
        if (thumbnailMediaIdInput) {
          thumbnailMediaIdInput.value = thumbnailMediaId ? String(thumbnailMediaId) : '';
        }
      }

      if (!thumbnailImageUrl) {
        thumbnailMediaId = null;
        if (thumbnailMediaIdInput) {
          thumbnailMediaIdInput.value = '';
        }
      }

      const contentHtml = editor.getHTML();
      const payload = {
        article_id: parseOptionalInt(articleIdInput.value),
        title: titleInput.value.trim(),
        excerpt: excerptInput.value.trim(),
        category_id: parseOptionalInt(categorySelect.value),
        tag_id: parseOptionalInt(tagSelect.value),
        thumbnail_media_id: thumbnailMediaId,
        thumbnail_image_url: thumbnailImageUrl || null,
        thumbnail_alt_text: thumbnailAltText || null,
        thumbnail_caption: thumbnailCaption || null,
        content_html: contentHtml,
        media_ids: collectMediaIds(contentHtml),
        intent,
      };
      // Data conversion summary:
      // - input strings are trimmed
      // - optional IDs become int|null
      // - editor HTML goes to `content_html`
      // - embedded image references become `media_ids`

      const response = await fetch(`${appBaseUrl}/api/v1/reporter/articles`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        if (response.status === 422 && data.fields) {
          Object.entries(data.fields).forEach(([key, message]) => {
            setFieldError(key, String(message));
          });
          showAlert(data.error || 'Please fix the highlighted fields and try again.', false);
          return;
        }

        showAlert(data.error || 'Unable to save the article right now.', false);
        return;
      }

      if (data?.data?.id) {
        articleIdInput.value = String(data.data.id);
      }
      if (data?.data?.thumbnail_media_id !== undefined && thumbnailMediaIdInput) {
        thumbnailMediaIdInput.value = data.data.thumbnail_media_id
          ? String(data.data.thumbnail_media_id)
          : '';
      }
      contentHtmlInput.value = contentHtml;
      const isExistingArticle = parseOptionalInt(payload.article_id) !== null;
      const successMessage =
        isExistingArticle && intent === 'draft'
          ? 'Article updated successfully.'
          : data.message || 'Article saved & published successfully.';
      showAlert(successMessage, true);
    } catch (error) {
      const message = error instanceof Error ? error.message : 'Unexpected error while saving article.';
      if (thumbnailFileInput?.files?.length) {
        setFieldError('thumbnail_image', message);
      }
      showAlert(message, false);
    } finally {
      setSavingState(false);
    }
  }

  /**
   * Utility: bind click callback only if target button exists.
   */
  function attachClick(id, callback) {
    const button = document.getElementById(id);
    if (!button) {
      return;
    }

    button.addEventListener('click', callback);
  }

  attachClick('toggleBoldButton', () => editor.chain().focus().toggleBold().run());
  attachClick('toggleItalicButton', () => editor.chain().focus().toggleItalic().run());
  attachClick('toggleUnderlineButton', () => editor.chain().focus().toggleUnderline().run());
  attachClick('toggleStrikeButton', () => editor.chain().focus().toggleStrike().run());
  attachClick('toggleCodeButton', () => editor.chain().focus().toggleCode().run());
  attachClick('toggleListButton', () => editor.chain().focus().toggleBulletList().run());
  attachClick('toggleOrderedListButton', () => editor.chain().focus().toggleOrderedList().run());
  attachClick('toggleBlockquoteButton', () => editor.chain().focus().toggleBlockquote().run());
  attachClick('toggleHRButton', () => editor.chain().focus().setHorizontalRule().run());

  attachClick('toggleHighlightButton', () => {
    const isHighlighted = editor.isActive('highlight');
    editor
      .chain()
      .focus()
      .toggleHighlight({ color: isHighlighted ? undefined : '#ffc078' })
      .run();
  });

  attachClick('toggleLeftAlignButton', () => editor.chain().focus().setTextAlign('left').run());
  attachClick('toggleCenterAlignButton', () => editor.chain().focus().setTextAlign('center').run());
  attachClick('toggleRightAlignButton', () => editor.chain().focus().setTextAlign('right').run());

  attachClick('toggleParagraphButton', () => {
    editor.chain().focus().setParagraph().run();
    hideDropdown('typographyDropdown');
  });

  document.querySelectorAll('[data-heading-level]').forEach((button) => {
    button.addEventListener('click', () => {
      const level = Number.parseInt(button.getAttribute('data-heading-level') || '', 10);
      if (!Number.isInteger(level)) {
        return;
      }

      editor.chain().focus().toggleHeading({ level }).run();
      hideDropdown('typographyDropdown');
    });
  });

  document.querySelectorAll('[data-text-size]').forEach((button) => {
    button.addEventListener('click', () => {
      const fontSize = button.getAttribute('data-text-size') || '';
      if (!fontSize) {
        return;
      }

      editor.chain().focus().setFontSize(fontSize).run();
      hideDropdown('textSizeDropdown');
    });
  });

  const colorPicker = document.getElementById('color');
  if (colorPicker) {
    colorPicker.addEventListener('input', (event) => {
      const selectedColor = event.target.value;
      editor.chain().focus().setColor(selectedColor).run();
    });
  }

  document.querySelectorAll('[data-hex-color]').forEach((button) => {
    button.addEventListener('click', () => {
      const selectedColor = button.getAttribute('data-hex-color');
      if (!selectedColor) {
        return;
      }

      editor.chain().focus().setColor(selectedColor).run();
    });
  });

  attachClick('reset-color', () => editor.commands.unsetColor());

  document.querySelectorAll('[data-font-family]').forEach((button) => {
    button.addEventListener('click', () => {
      const fontFamily = button.getAttribute('data-font-family') || '';
      if (!fontFamily) {
        return;
      }

      editor.chain().focus().setFontFamily(fontFamily).run();
      hideDropdown('fontFamilyDropdown');
    });
  });

  if (advancedImageForm) {
    advancedImageForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      setAdvancedImageError('');

      const file = advancedImageFile?.files?.[0] || null;
      const imageUrl = (advancedImageUrl?.value || '').trim();
      const altText = (advancedImageAlt?.value || '').trim();
      const title = (advancedImageTitle?.value || '').trim();

      if (!file && !imageUrl) {
        setAdvancedImageError('Provide an image file or image URL.');
        return;
      }

      const submitButton = advancedImageForm.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.disabled = true;
      }

      try {
        const media = await uploadImage({
          file,
          imageUrl,
          altText,
          title,
        });

        insertImageAt(media);
        advancedImageForm.reset();
        hideAdvancedImageModal();
      } catch (error) {
        setAdvancedImageError(error instanceof Error ? error.message : 'Failed to insert image.');
      } finally {
        if (submitButton) {
          submitButton.disabled = false;
        }
      }
    });
  }

  const dropHint = document.getElementById('editorDropHint');
  /**
   * Toggle drag/drop highlight styles on editor area.
   *
   * Input: boolean active.
   */
  function setDragActive(active) {
    if (!editorElement) {
      return;
    }

    if (active) {
      editorElement.classList.add('ring-2', 'ring-brand');
      if (dropHint) {
        dropHint.classList.add('text-heading');
      }
    } else {
      editorElement.classList.remove('ring-2', 'ring-brand');
      if (dropHint) {
        dropHint.classList.remove('text-heading');
      }
    }
  }

  ['dragenter', 'dragover'].forEach((eventName) => {
    editorElement.addEventListener(eventName, (event) => {
      event.preventDefault();
      setDragActive(true);
    });
  });

  ['dragleave', 'dragend'].forEach((eventName) => {
    editorElement.addEventListener(eventName, () => {
      setDragActive(false);
    });
  });

  editorElement.addEventListener('drop', async (event) => {
    event.preventDefault();
    setDragActive(false);

    const files = Array.from(event.dataTransfer?.files || []).filter((file) =>
      file.type.startsWith('image/')
    );

    if (!files.length) {
      return;
    }

    const dropPosition = editor.view.posAtCoords({
      left: event.clientX,
      top: event.clientY,
    });

    // Convert screen drop coordinates into editor document position.
    let insertionPos = dropPosition?.pos ?? null;

    for (const file of files) {
      try {
        const media = await uploadImage({ file });
        insertImageAt(media, insertionPos);

        if (typeof insertionPos === 'number') {
          insertionPos += 1;
        }
      } catch (error) {
        showAlert(error instanceof Error ? error.message : 'Failed to upload dropped image.', false);
        break;
      }
    }
  });

  if (thumbnailUrlInput && thumbnailMediaIdInput) {
    thumbnailUrlInput.addEventListener('input', () => {
      if ((thumbnailUrlInput.value || '').trim() !== '' && !(thumbnailFileInput?.files?.length > 0)) {
        thumbnailMediaIdInput.value = '';
      }
    });
  }

  if (thumbnailFileInput && thumbnailMediaIdInput) {
    thumbnailFileInput.addEventListener('change', () => {
      if (thumbnailFileInput.files?.length) {
        thumbnailMediaIdInput.value = '';
      }
    });
  }

  hydrateInitialArticle();

  titleInput.addEventListener('input', () => updateCounter(titleInput, titleCounter));
  excerptInput.addEventListener('input', () => updateCounter(excerptInput, excerptCounter));
  if (thumbnailAltInput) {
    thumbnailAltInput.addEventListener('input', () => updateCounter(thumbnailAltInput, thumbnailAltCounter));
  }
  if (thumbnailCaptionInput) {
    thumbnailCaptionInput.addEventListener('input', () =>
      updateCounter(thumbnailCaptionInput, thumbnailCaptionCounter)
    );
  }
  updateCounter(titleInput, titleCounter);
  updateCounter(excerptInput, excerptCounter);
  if (thumbnailAltInput) {
    updateCounter(thumbnailAltInput, thumbnailAltCounter);
  }
  if (thumbnailCaptionInput) {
    updateCounter(thumbnailCaptionInput, thumbnailCaptionCounter);
  }

  saveButtons.forEach((button) => {
    button.addEventListener('click', () => submitArticle(button.dataset.intent || 'draft'));
  });

  const cancelButton = document.getElementById('cancelArticleBtn');
  if (cancelButton) {
    cancelButton.addEventListener('click', () => {
      if (window.history.length > 1) {
        window.history.back();
        return;
      }

      window.location.href = `${appBaseUrl}/dashboard`;
    });
  }
});
