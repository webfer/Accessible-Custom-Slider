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
              // Insert heading if enabled
              if (sliderConfig.showHeading) {
                // Pick title for current language or fallback to first available.
                const adminTitle =
                  sliderConfig.adminTitlePerLang?.[currentLang] ||
                  Object.values(sliderConfig.adminTitlePerLang)[0] ||
                  '';

                if (adminTitle) {
                  const heading = document.createElement('h2');
                  heading.id = `${sliderConfig.uniqueId}-heading`;
                  heading.textContent = adminTitle;
                  heading.classList.add('visually-hidden');

                  // Insert heading before .splide__track
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
