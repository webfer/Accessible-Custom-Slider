<?php

namespace Drupal\accessible_custom_slider\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Accessible Custom Slider settings.
 */
class AccessibleCustomSliderSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'accessible_custom_slider_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['accessible_custom_slider.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'accessible_custom_slider/admin_styles';
    $config = $this->config('accessible_custom_slider.settings');
    $saved_configurations = $config->get('slider_configurations') ?? [];

    $slider_configurations = $form_state->get('slider_configurations');
    if ($slider_configurations === NULL) {
      $form_state->set('slider_configurations', $saved_configurations);
      $slider_configurations = $saved_configurations;
    }

    $form['sliders'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Slider Configurations'),
      '#tree' => TRUE,
      '#prefix' => '<div id="sliders-wrapper">',
      '#suffix' => '</div>',
    ];

    $config_mode_default = 'basic';
    $languages = \Drupal::languageManager()->getLanguages();

    foreach ($slider_configurations as $index => $slider) {
      $config_mode = $slider['advanced_settings']['config_mode'] ?? $config_mode_default;
      $default_title = $slider['admin_title'] ?? 'Slider';

      // Build multilingual title fields
      $slider_title_per_lang = [];
      foreach ($languages as $langcode => $lang) {
        $slider_title_per_lang[$langcode] = [
          '#type' => 'textfield',
          '#title' => $this->t('Title (@lang)', ['@lang' => $lang->getName()]),
          '#default_value' => $slider['admin_title_per_lang'][$langcode] ?? $default_title,
          '#description' => $this->t('<strong>Enter a descriptive title for this slider.</strong> This will be used as an <code>&lt;h2&gt;</code> heading on the page to provide screen readers and other assistive technologies with accessible context.'),
          '#maxlength' => 255,
        ];
      }

      $form['sliders']['slider_configurations'][$index] = [
        'admin_title_per_lang' => $slider_title_per_lang,
        'show_heading' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Display Title as Heading (for accessibility)'),
          '#default_value' => $slider['show_heading'] ?? TRUE,
          '#description' => $this->t('If checked, the title will be output as an <code>&lt;h2&gt;</code> element and used for accessibility via aria-labelledby.'),
        ],
        'view_machine_name' => [
          '#type' => 'textfield',
          '#title' => $this->t('View ID'),
          '#default_value' => $slider['view_machine_name'] ?? '',
          '#description' => $this->t('Enter the machine name of the View you want to display as a slider.'),
        ],
        'view_display_id' => [
          '#type' => 'textfield',
          '#title' => $this->t('Display ID'),
          '#default_value' => $slider['view_display_id'] ?? '',
          '#description' => $this->t('Enter the display ID of the selected View (e.g., "page_1", "block_1").'),
        ],
        'slider_selector' => [
          '#type' => 'textfield',
          '#title' => $this->t('Selector'),
          '#default_value' => $slider['slider_selector'] ?? '',
          '#description' => $this->t('Enter the CSS selector that targets the slider container.'),
        ],
        'advanced_settings' => [
          '#type' => 'details',
          '#title' => $this->t('Advanced Settings'),
          '#open' => FALSE,
          'config_mode' => [
            '#type' => 'radios',
            '#title' => $this->t('Configuration Mode'),
            '#options' => ['basic' => $this->t('Basic'), 'json' => $this->t('JSON')],
            '#default_value' => $config_mode,
          ],
        ],
      ];

      $advanced = &$form['sliders']['slider_configurations'][$index]['advanced_settings'];

      // Basic fields
      $basic_fields = [
        'type' => [
          '#type' => 'select',
          '#title' => $this->t('Slider Type'),
          '#options' => ['loop' => 'Loop', 'fade' => 'Fade'],
          '#default_value' => $slider['advanced_settings']['type'] ?? 'loop',
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'per_page' => [
          '#type' => 'number',
          '#title' => $this->t('Per Page'),
          '#default_value' => $slider['advanced_settings']['per_page'] ?? 1,
          '#min' => 1,
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'autoplay' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Autoplay'),
          '#default_value' => $slider['advanced_settings']['autoplay'] ?? FALSE,
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'keyboard' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Enable Keyboard Navigation'),
          '#default_value' => $slider['advanced_settings']['keyboard'] ?? FALSE,
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'pagination' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Enable Pagination'),
          '#default_value' => $slider['advanced_settings']['pagination'] ?? TRUE,
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'arrows' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Show Arrows'),
          '#default_value' => $slider['advanced_settings']['arrows'] ?? TRUE,
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'pause_on_hover' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Pause on Hover'),
          '#default_value' => $slider['advanced_settings']['pause_on_hover'] ?? TRUE,
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'pause_on_focus' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Pause on Focus'),
          '#default_value' => $slider['advanced_settings']['pause_on_focus'] ?? TRUE,
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'interval' => [
          '#type' => 'number',
          '#title' => $this->t('Interval (ms)'),
          '#default_value' => $slider['advanced_settings']['interval'] ?? 3000,
          '#min' => 0,
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'speed' => [
          '#type' => 'number',
          '#title' => $this->t('Speed (ms)'),
          '#default_value' => $slider['advanced_settings']['speed'] ?? 400,
          '#min' => 0,
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'rewind' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Rewind'),
          '#default_value' => $slider['advanced_settings']['rewind'] ?? FALSE,
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'direction' => [
          '#type' => 'select',
          '#title' => $this->t('Direction'),
          '#options' => ['ltr' => 'Left to Right', 'rtl' => 'Right to Left'],
          '#default_value' => $slider['advanced_settings']['direction'] ?? 'ltr',
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'gap' => [
          '#type' => 'textfield',
          '#title' => $this->t('Gap Between Slides'),
          '#default_value' => $slider['advanced_settings']['gap'] ?? '1rem',
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
        'breakpoints' => [
          '#type' => 'textarea',
          '#title' => $this->t('Breakpoints'),
          '#default_value' => is_array($slider['advanced_settings']['breakpoints'] ?? null)
            ? json_encode($slider['advanced_settings']['breakpoints'])
            : ($slider['advanced_settings']['breakpoints'] ?? ''),
          '#description' => $this->t('Example: {"1200":{"perPage":6},"900":{"perPage":4}}'),
          '#states' => ['visible' => [":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'basic']]],
        ],
      ];

      $advanced += $basic_fields;

      $advanced['custom_json'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Custom Splide Options (JSON)'),
        '#default_value' => $slider['advanced_settings']['custom_json'] ?? '',
        '#description' => $this->t('Paste a valid JSON object of Splide options. This will override all other configuration fields above. To see all available options, refer to the <a href=":url" target="_blank" rel="noopener noreferrer">Splide Options Guide</a>.', [
          ':url' => 'https://splidejs.com/guides/options/',
        ]),
        '#states' => [
          'visible' => [
            ":input[name='sliders[slider_configurations][$index][advanced_settings][config_mode]']" => ['value' => 'json'],
          ],
        ],
      ];

      $form['sliders']['slider_configurations'][$index . '_separator'] = [
        '#markup' => '<div style="margin: 5rem 0 2rem;"><hr></div>',
      ];
    }

    $form['sliders']['add_slider'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add another slider'),
      '#submit' => ['::addSliderCallback'],
      '#ajax' => [
        'callback' => '::ajaxSliderCallback',
        'wrapper' => 'sliders-wrapper',
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Submit callback to add another slider config row.
   */
  public function addSliderCallback(array &$form, FormStateInterface $form_state) {
    $slider_configurations = $form_state->get('slider_configurations') ?? [];
    $slider_configurations[] = [];
    $form_state->set('slider_configurations', $slider_configurations);
    $form_state->setRebuild(TRUE);
    return $form;
  }

  /**
   * Ajax callback for sliders fieldset.
   */
  public function ajaxSliderCallback(array &$form, FormStateInterface $form_state) {
    return $form['sliders'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $values = $form_state->getValue(['sliders', 'slider_configurations']) ?? [];
    $languages = \Drupal::languageManager()->getLanguages();

    foreach ($values as &$slider) {
      // Remove stale JSON if basic mode
      if (($slider['advanced_settings']['config_mode'] ?? 'basic') === 'basic') {
        $slider['advanced_settings']['custom_json'] = '';
      }

      // Decode breakpoints if JSON string
      if (!empty($slider['advanced_settings']['breakpoints'])) {
        $decoded = json_decode($slider['advanced_settings']['breakpoints'], TRUE);
        if (json_last_error() === JSON_ERROR_NONE) {
          $slider['advanced_settings']['breakpoints'] = $decoded;
        } else {
          $this->messenger()->addError($this->t('Invalid JSON in breakpoints for one of the sliders.'));
        }
      }

      // Ensure all languages are present in admin_title_per_lang
      foreach ($languages as $langcode => $lang) {
        if (!isset($slider['admin_title_per_lang'][$langcode])) {
          $slider['admin_title_per_lang'][$langcode] = $slider['admin_title'] ?? 'Slider';
        }
      }
    }

    $this->config('accessible_custom_slider.settings')
      ->set('slider_configurations', $values)
      ->save();

    $this->messenger()->addStatus($this->t('Slider settings have been saved.'));
  }

}
