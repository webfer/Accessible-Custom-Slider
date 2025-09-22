<?php

namespace Drupal\accessible_custom_slider\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Views style plugin to render a Splide slider.
 *
 * @ViewsStyle(
 *   id = "accessible_splide",
 *   title = @Translation("Accessible Splide Slider"),
 *   help = @Translation("Displays rows in a Splide.js slider."),
 *   theme = "views_view_unformatted",
 *   display_types = {"normal"}
 * )
 */
class AccessibleSplideStyle extends StylePluginBase {
  protected $usesRowPlugin = TRUE;
}
