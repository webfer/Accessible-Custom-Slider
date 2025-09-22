# Accessible Custom Slider

Accessible Custom Slider is a Drupal 10 module that integrates [Splide.js](https://splidejs.com/) to create accessible, customizable carousels. The module allows admin users to configure which Views displays should initialize a Splide slider and provides an admin interface for managing those configurations.

## 🚀 Features

- Easily turn Drupal Views displays into [Splide.js](https://splidejs.com/) sliders
- Admin UI for configuring multiple View/Display pairs
- Built-in accessibility features using Splide's ARIA enhancements
- Frontend and backend assets compiled via Webpack
- Includes starter Twig template for accessible rendering

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/tothomw_admin/Accessible-Custom-Slider.git
```

On your Drupal installation, place the module in the `web/modules/custom/` directory:
`accessible_custom_slider`

### Move to the Drupal custom modules directory

```bash
cd /path/to/your/drupal/web/modules/custom/
```

Then move the cloned repository:

```bash
mv Accessible-Custom-Slider web/modules/custom/accessible_custom_slider
```

### 2. Install Dependencies and Build Assets

```bash
cd web/modules/custom/accessible_custom_slider
npm install
npm run build # For production build

# or

npm run watch # For development with auto-rebuild
```

Ensure node and npm are installed in your environment.

### 3. Enable the Module

Enable the module via Drush or through the Drupal admin UI:

```bash
drush en accessible_custom_slider
```

## ⚙️ Configuration

All slider configuration is managed through the Drupal admin UI. No need to edit JavaScript files or `index.js` directly.

- Go to: `/admin/config/user-interface/accessible-custom-slider`
- Add configurations for each carousel instance by specifying:
  - View machine name
  - Display ID
  - CSS selector (default: `.splide`)
  - Custom Splide options (JSON) if needed
- To remove a configuration, simply delete the values and click "Save".

For more options, you can modify the Splide.js settings for each slider instance using the admin UI. See the [Splide.js options documentation](https://splidejs.com/guides/options/) for all available configuration parameters.

## 🎨 Custom Styling

You can customize the appearance of your carousels by adding your own SCSS code.

The module includes a Sass directory located at:

```bash
accessible_custom_slider/assets/sass/

```

### 🔧 How to Customize

1. Navigate to the Sass directory:

```bash
cd accessible_custom_slider/assets/sass/

```

2. Create your own SCSS partials (e.g., `_my-slider-styles.scss`) and import them in the index.scss file:

```scss
// accessible_custom_slider/src/index.scss
@import 'splide-overrides'; // default styles
@import 'my-slider-styles'; // your custom styles
```

3. Write your custom styles inside your partial, e.g.:

```scss
// _my-slider-styles.scss
.splide {
  border-radius: 1rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
```

4. Rebuild the assets after editing:

```bash
npm run build    # For production
# or
npm run watch    # For development with live rebuilding

```

## 🪄 Tip

Use BEM conventions and scope styles to avoid collisions with other components in your Drupal theme. You can also conditionally style sliders based on custom wrapper classes for different Views displays.

## 🛠️ Admin Configuration

Navigate to:

```bash
/admin/config/user-interface/accessible-custom-slider
```

There, you can add configurations for each carousel instance by specifying:

- View machine name

- Display ID

- CSS selector (default: `.splide`)

To remove a configuration, simply delete the values and click "Save".

## 🧩 Views Twig Template Example

To use the module with Views, override your View template with the following example:

```twig
{#
/**
 * @file
 * Default theme implementation for a view template to display a list of rows.
 *
 * Available variables:
 * - attributes: HTML attributes for the container.
 * - rows: A list of rows for this list.
 *   - attributes: The row's HTML attributes.
 *   - content: The row's contents.
 * - title: The title of this group of rows. May be empty.
 * - list: @todo.
 *   - type: Starting tag will be either a ul or ol.
 *   - attributes: HTML attributes for the list element.
 *
 * @see template_preprocess_views_view_list()
 *
 * @ingroup themeable
 */
#}

{# Define classes #}
{% set wrapper_class = ['splide'] %}
{% set slide_tracker_class = ['splide__track'] %}
{% set custom_ul_classes = ['splide__list'] %}
{% set custom_li_classes = ['splide__slide'] %}

{# Ensure list.attributes is an Attribute object #}
{% if list.attributes is not defined %}
  {% set list = list|merge({'attributes': create_attribute()}) %}
{% endif %}

{# Ensure each row has an Attribute object and apply classes during render #}

{% if attributes -%}
<section{{ attributes.addClass(wrapper_class).setAttribute('id', 'teachingPrograms') }} aria-label="Carousel of Teaching programs">
    <div{{ attributes.removeClass(wrapper_class).addClass(slide_tracker_class) }}>
      {% endif %}
              {% if title %}
                <h3>{{ title }}</h3>
              {% endif %}

              <{{ list.type }}{{ list.attributes.addClass(custom_ul_classes) }}>
                {% for row in rows %}
                  {% if row.attributes is not defined %}
                    {% set row = row|merge({'attributes': create_attribute()}) %}
                  {% endif %}
                  <li{{ row.attributes.addClass(custom_li_classes) }}>
                    {{- row.content -}}
                  </li>
                {% endfor %}
              </{{ list.type }}>

      {% if attributes -%}
    </div>
    <div class="wrp--toggle">
      <button class="splide__toggle" type="button">
        <svg class="splide__toggle__play" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play-circle" viewBox="0 0 16 16">
          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
          <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445"/>
        </svg>
        <svg class="splide__toggle__pause" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pause-circle" viewBox="0 0 16 16">
          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
          <path d="M5 6.25a1.25 1.25 0 1 1 2.5 0v3.5a1.25 1.25 0 1 1-2.5 0zm3.5 0a1.25 1.25 0 1 1 2.5 0v3.5a1.25 1.25 0 1 1-2.5 0z"/>
        </svg>
      </button>
    </div>
  </section>
{% endif %}


```

## Important Note About Carousel IDs

In the Twig template example above, pay special attention to the `teachingPrograms` ID:

```twig
<section{{ attributes.addClass(wrapper_class).setAttribute('id', 'teachingPrograms') }}>
```

### Key Points:

1. Carousel Identification:

- In this example `teachingPrograms` serves as the unique identifier for your carousel.
- This ID will be used to initialize the `Splide.js` slider

2. Admin Configuration:

- When configuring the carousel in the Drupal UI (described in the Admin Settings section)
- You must use an ID as or a class CSS as a selector.
- Example configuration.
  When setting up your carousel in the Drupal admin UI, you'll need these three values:

## View Machine Name

**This is your View's system identifier**

### How to find it:

Look in the Views edit URL:  
`/admin/structure/views/view/teaching_programs/edit`  
→ The value between `view/` and `/edit` is your machine name  
_(Example: `teaching_programs`)_

---

## Display ID

**This identifies the specific display (block, page, etc.) within your View**

### How to find it:

1. Edit your View
2. Go to the "Advanced" tab
3. Under "Other", find "Machine name"  
   _(Common examples: `block_1`, `page_1`)_

---

## CSS Selector

**The HTML ID or class where Splide should initialize**

### Important:

Must match what you set in your Twig template:

```twig
.setAttribute('id', 'teachingPrograms')
```

## Selector Types:

ID selector: `#your-id` (recommended for single carousels)

Class selector: `.your-class` (for multiple carousels)

## Multiple Carousels:

For multiple carousels, ensure each has a unique ID

Update both the Twig template and admin configuration accordingly.

### Example Configuration

| Field            | Example Value       | Notes               |
| ---------------- | ------------------- | ------------------- |
| **View**         | `teaching_programs` | From URL            |
| **Display**      | `block_1`           | View's machine name |
| **CSS Selector** | `#programCarousel`  | Must match template |

## 🌐 Translation

Translation. Find these keys in the Drupal UI and translate it using these values:

### Catalan

| English String         | Catalan Translation                          |
| ---------------------- | -------------------------------------------- |
| Previous slide         | Diapositiva anterior                         |
| Next slide             | Diapositiva següent                          |
| Go to first slide      | Vés a la primera diapositiva                 |
| Go to last slide       | Vés a l'última diapositiva                   |
| Go to slide @num       | Aneu a la diapositiva @num                   |
| Go to page @num        | Vés a la pàgina @num                         |
| Start autoplay         | Inicia la reproducció automàtica             |
| Pause autoplay         | Posa en pausa la reproducció automàtica      |
| carousel               | carrusel                                     |
| Select a slide to show | Seleccioneu una diapositiva per mostrar      |
| slide                  | diapositiva                                  |
| @current of @total     | @current de @total                           |
| Toggle autoplay        | Activa o desactiva la reproducció automàtica |
| Toggle mute            | Activa o desactiva el audio                  |
| Toggle captions        | Activa o desactiva els subtítols             |

### Spanish

| English String         | Spanish Translation                             |
| ---------------------- | ----------------------------------------------- |
| Previous slide         | Diapositiva anterior                            |
| Next slide             | Diapositiva siguiente                           |
| Go to first slide      | Ir a la primera diapositiva                     |
| Go to last slide       | Ir a la última diapositiva                      |
| Go to slide @num       | Ir a la diapositiva @num                        |
| Go to page @num        | Ir a la página @num                             |
| Start autoplay         | Iniciar reproducción automática                 |
| Pause autoplay         | Pausar la reproducción automática               |
| carousel               | carrusel                                        |
| Select a slide to show | Seleccione una diapositiva para mostrar         |
| slide                  | diapositiva                                     |
| @current of @total     | @current de @total                              |
| Toggle autoplay        | Activar o desactivar la reproducción automática |
| Toggle mute            | Activar o desactivar el audio                   |
| Toggle captions        | Activar o desactivar subtítulos                 |

---

## 📜 License

This module is proprietary software developed for [Tothomweb](https://tothomweb.com/). Reuse or modification requires permission from the maintainers.

---

_Maintained by [Tothomweb](https://tothomweb.com/) Development Team_ - [WebFer](https://www.linkedin.com/in/webfer/)
