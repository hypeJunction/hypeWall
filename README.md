hypeWall
=========
![Elgg 2.3](https://img.shields.io/badge/Elgg-2.3-orange.svg?style=flat-square)

Rich user interface for sharing status updates, links and content via user walls.

## Screenshots

![Wall Form](https://raw.github.com/hypeJunction/hypeWall/master/screenshots/wall-form.png "Form")
![Wall View](https://raw.github.com/hypeJunction/hypeWall/master/screenshots/wall-items.png "Wall items")


## Features

- URL-parsing with embeddable content view
- Geotagging (based on browser location)
- Inline multi-file upload
- Friend tagging
- Content attachments


## Extensions

- hypeScraper can be used to generate URL preview cards
https://github.com/hypeJunction/hypeScraper

- hypeDropzone can be used to enable multi-file drag-n-drop uploads
https://github.com/hypeJunction/hypeDropzone

- hypeLists can be used for real-time updates
https://github.com/hypeJunction/hypeLists

- Integration with iZap videos by @iionly
https://github.com/iionly/hypeWall_izap_videos

- Integration with blog, videolist, poll, and event_manager plugins by @nlybe
https://github.com/nlybe/hypeWall_Extended


## Notes

* Reverse geocoding is performed via Nominatim http://wiki.openstreetmap.org/wiki/Nominatim.
Reverse geocoding (i.e. browser position coordinates to human readable address)
will not work in https. Implement a custom solution using a paid/free/proprietary
service that does the same

* Icons are not included with the plugin. You will need to load FontAwesome CSS,
either by registering it in your theme, or using one of the available Elgg plugins.


## Developer Notes

* You can add wall tabs and forms by extending the ```'framework/wall/container/extend'``` view
