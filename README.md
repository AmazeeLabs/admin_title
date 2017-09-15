# Admin title

A Drupal 8 module providing the admin title support for content entities.

## Module features

- Improves titles of content entity forms
- Uses the admin title field value in content entity form titles
- Alters Entity Reference, Link, Menu, etc. widgets to use admin title in the autocomplete
  - If views reference method is required: change it to "Views: Filter by an entity reference view (with admin title support)"
- Alters [Linkit](https://www.drupal.org/project/linkit) (8.x-5.x) autocomplete to search in both title and admin title fields.
- Fallbacks to the entity label if the admin title field does not exist or is empty
- Adds "Languages" column to taxonomy overview pages

## Requirements for the admin title field

The module does not create the admin title field automatically. You have to create and configure it manually:

- Machine name is `field_admin_title`
- Should be a text field

## Tips for /admin/content view

If you like "Title" column to display admin title with a title fallback:

- Edit "Title" field:
  - check "Exclude from display"
  - uncheck "Link to the Content"
- Add "Admin title / Title" field pointing to your admin title field:
  - "Rewrite results" > check "Output this field as a custom link" > set "Link path" to "node/{{ nid }}" (may require a patch from https://www.drupal.org/node/2610236)
  - "No results behavior" > set "No results text" to "{{ title }}"

If you like "Title" filter to search in both admin title and normal title:

- Remove "Title" filter
- Add new "Combine fields filter" filter and make it search by two fields

The similar modifications can be done for other entity types.
