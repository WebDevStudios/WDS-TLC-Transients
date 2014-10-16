WDS TLC Transients
==================

A plugin wrapper with helper functions to enhance the [WP TLC Transients](https://github.com/markjaquith/WP-TLC-Transients) library developed by [Mark Jaquith](https://github.com/markjaquith). Easily include WP TLC Transients into your sites WDS style with a few handy functions.

So, what is [WP TLC Transients](https://github.com/markjaquith/WP-TLC-Transients)?

> A WordPress transients interface with support for soft-expiration (use old content until new content is available), background updating of the transients (without having to wait for a cron job), and a chainable syntax that allows for one liners.

### Gotchya's

- As noted in [WP TLC Transients](https://github.com/markjaquith/WP-TLC-Transients), you have no control over the context of your callback. Make no assumptions about what post is queried, what user is logged in, etc. [Read more here](https://github.com/markjaquith/WP-TLC-Transients#context).