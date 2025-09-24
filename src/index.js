/**
 * @file
 * JavaScript for Accessible Custom Slider.
 */

import Splide from '@splidejs/splide';
import once from '@drupal/once';
import '@splidejs/splide/css';
import './index.scss';

(function (Drupal, drupalSettings) {
  Drupal.behaviors.accessibleCustomSlider = {
    attach: function (context, settings) {
      if (!drupalSettings.accessibleCustomSlider) {
        return;
      }

      // Determine current language.
      const currentLang =
        drupalSettings.path?.currentLanguage ||
        drupalSettings.language?.current ||
        'en';

      Object.values(drupalSettings.accessibleCustomSlider).forEach(
        (sliderConfig) => {
          const selector = sliderConfig.selector;
          const baseOptions =
            typeof sliderConfig.settings === 'object' &&
            sliderConfig.settings !== null
              ? sliderConfig.settings
              : {};
          const i18n =
            typeof sliderConfig.i18n === 'object' && sliderConfig.i18n !== null
              ? sliderConfig.i18n
              : {};

          const sliderElements = context.querySelectorAll(selector);

          sliderElements.forEach((element) => {
            // Run once per element.
            once('accessibleCustomSlider', element).forEach(() => {
              // Insert heading if enabled.
              if (sliderConfig.showHeading) {
                const adminTitle =
                  sliderConfig.adminTitlePerLang?.[currentLang] ||
                  Object.values(sliderConfig.adminTitlePerLang)[0] ||
                  '';

                if (adminTitle) {
                  const heading = document.createElement('h2');
                  heading.id = `${sliderConfig.uniqueId}-heading`;
                  heading.textContent = adminTitle;
                  heading.classList.add('visually-hidden');

                  // Insert heading before .splide__track.
                  const track = element.querySelector('.splide__track');
                  if (track && track.parentNode) {
                    track.parentNode.insertBefore(heading, track);
                    element.setAttribute('aria-labelledby', heading.id);
                  } else {
                    console.warn(
                      `Could not find .splide__track to insert heading for slider ${sliderConfig.uniqueId}.`
                    );
                  }
                }
              }

              const hasTrack = element.querySelector('.splide__track') !== null;
              const mergedOptions = {
                ...baseOptions,
                html: !hasTrack,
                i18n: {
                  ...(baseOptions.i18n || {}),
                  ...i18n,
                },
              };

              try {
                const splideInstance = new Splide(element, mergedOptions);
                splideInstance.mount();
                element.classList.add('splide-initialized');

                // 🔹 Update the hidden announcement span for perPage.
                // after splideInstance.mount() && element.classList.add('splide-initialized');

                const announce = element.querySelector(
                  'span.visually-hidden[aria-live="polite"]'
                );

                // Grab the slidesInView templates from the server-provided i18n (if present)
                const slidesInViewI18n =
                  sliderConfig.i18n && sliderConfig.i18n.slidesInView
                    ? sliderConfig.i18n.slidesInView
                    : null;

                const formatSlidesInView = (perPage) => {
                  // Prefer server-provided templates (already translated on server).
                  if (slidesInViewI18n) {
                    if (perPage === 1 && slidesInViewI18n.one) {
                      return slidesInViewI18n.one;
                    }
                    // use 'many' template if present; otherwise fall back to 'one' template and replace
                    const tpl =
                      slidesInViewI18n.many ??
                      slidesInViewI18n.one ??
                      'This carousel displays @num slides in each view.';
                    return String(tpl).replace(/@num/g, String(perPage));
                  }

                  // Final fallback: use Drupal.t in JS (only if server template missing)
                  return Drupal.t(
                    'This carousel displays @num slides in each view.',
                    { '@num': perPage }
                  );
                };

                if (announce) {
                  const updateAnnouncement = () => {
                    // Splide applies breakpoints and updates options.perPage accordingly.
                    const perPage = splideInstance.options.perPage || 1;
                    announce.textContent = formatSlidesInView(perPage);
                  };

                  // Update once on mount and on subsequent resize (breakpoint changes).
                  splideInstance.on('mounted resize', updateAnnouncement);
                  updateAnnouncement();
                }
              } catch (error) {
                console.warn(
                  'Failed to initialize Splide on element:',
                  element,
                  error
                );
              }
            });
          });
        }
      );
    },
  };
})(Drupal, drupalSettings);
