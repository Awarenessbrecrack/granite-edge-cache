# Architecture

Granite Edge Cache is one output-buffer filter, one settings array and one REST namespace.

* `source/granite-edge-cache/includes/` — cache, optimizer, database, seo, redirects, transport
* `source/granite-edge-cache/admin/` — nine tabs, one settings model
* `source/granite-edge-cache/assets/` — the small amount of CSS the admin needs

Nothing loads outside the admin screens except the buffer filter and the cron callbacks.
