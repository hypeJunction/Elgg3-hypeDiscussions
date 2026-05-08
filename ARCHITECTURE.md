# hypediscussions — Architecture

## Overview

Threaded discussion posts for Elgg groups and user profiles. Extends the
Elgg core `discussions` plugin (which provides the `object:discussion` entity
type and `ElggDiscussion` base class) with additional features: reply
threading control, related-post discussions, site-wide discussions (outside
groups), admin-only discussion creation, and integration with hypeInteractions
comment depth settings.

## Entity Model

| Type   | Subtype      | Class                          |
|--------|--------------|--------------------------------|
| object | discussion   | `hypeJunction\Discussion`      |

`hypeJunction\Discussion` extends `ElggDiscussion` (Elgg core). Additional
metadata properties:

- `status` — `'open'` (default) or `'closed'`; closed discussions block new
  comments
- `threads` — `0` or `1`; whether reply threading is enabled (requires
  `hypeInteractions` `max_comment_depth > 1`)
- `discussed_post_guid` — optional GUID of a related post entity; links the
  discussion to a specific blog post, wiki page, etc.

## Bootstrap (`classes/hypeJunction/Discussions/Bootstrap.php`)

Loaded via `elgg-plugin.php` `bootstrap` key.

| Phase      | Responsibility |
|------------|---------------|
| `load()`   | Require `autoloader.php` and `lib/functions.php` |
| `boot()`   | Empty (events declared in elgg-plugin.php) |
| `init()`   | Register hypeLists collections, group_tools option, view extensions, Stash preloader, notification events |
| `ready()`  | Unregister conflicting core hook handlers and widget types |

## Event Handlers

All declared in the `events` section of `elgg-plugin.php`.

| Event                             | Handler                         | Purpose |
|-----------------------------------|---------------------------------|---------|
| `route:rewrite, discussions`      | `SetDiscussionRouteAlias`       | Alias old `/discussions/…` URLs to `/discussion/…` |
| `fields, object:discussion`       | `AddDiscussionFields`           | Add status/threads/discussed_post_guid fields to the edit form |
| `fields, object`                  | `AddObjectFields`               | Add `enable_discussions` field to other object edit forms |
| `register, menu:site`             | `SiteMenu`                      | Add Discussions link to site navigation |
| `register, menu:owner_block`      | `OwnerBlockMenu`                | Add Discussions link to user owner block |
| `register, menu:entity`           | `EntityMenu`                    | Add "Discuss" action to entity menus |
| `permissions_check:comment, object` | `CanThreadReplies`            | Block threaded replies when threading is disabled |
| `permissions_check:comment, object` | `CanCreateReply`              | Block replies on discussions in disabled-forum groups |
| `container_logic_check, object`   | `CanContainReply`               | Block comments on closed discussions |
| `container_permissions_check, object` | `CanCreateDiscussion`       | Enforce group forum setting and admin-only restriction |
| `uses:comments, object:discussion` | `Values::getTrue`             | Enable comments on discussions |
| `uses:cover, object:discussion`   | `Values::getTrue`               | Enable cover images on discussions |
| `uses:river, object:discussion`   | `Values::getTrue`               | Enable river activity for discussions |
| `allow_attachments, object:blog`  | `Values::getTrue`               | Allow attachments on blog posts (for related discussions) |
| `prepare, notification:publish:object:discussion` | `discussion_prepare_notification` | Customize publish notification |

## Routes

| Route ID                              | Path                             | Resource view |
|---------------------------------------|----------------------------------|---------------|
| `add:object:discussion`               | `/discussion/add/{guid}`        | `post/add` |
| `edit:object:discussion`              | `/discussion/edit/{guid}`       | `post/edit` |
| `view:object:discussion`              | `/discussion/view/{guid}/{title?}` | `post/view` |
| `collection:object:discussion:all`    | `/discussion/all`               | `collection/all` |
| `collection:object:discussion:owner`  | `/discussion/owner/{username?}` | `collection/owner` |
| `collection:object:discussion:friends`| `/discussion/friends/{username?}` | `collection/friends` |
| `collection:object:discussion:group`  | `/discussion/group/{guid}`      | `collection/group` |
| `collection:object:discussion:post`   | `/discussion/post/{guid}`       | `discussions/post` |

## Collections (requires `hypeLists`)

| Collection ID                          | Class |
|----------------------------------------|-------|
| `collection:object:discussion:all`     | `DefaultDiscussionsCollection` |
| `collection:object:discussion:owner`   | `OwnedDiscussionsCollection` |
| `collection:object:discussion:friends` | `FriendsDiscussionsCollection` |
| `collection:object:discussion:group`   | `GroupDiscussionsCollection` |
| `collection:object:discussion:post`    | `PostDiscussionsCollection` |

All extend `DefaultDiscussionsCollection` which extends `hypeJunction\Lists\Collection`.

## Plugin Settings

| Setting key           | Plugin ID        | Purpose |
|-----------------------|------------------|---------|
| `site_wide_discussions` | `hypediscussions` | Allow discussions outside groups |
| `post_discussions`    | `hypediscussions` | Allow discussions linked to specific posts |

## Dependencies

| Plugin        | Type     | Notes |
|---------------|----------|-------|
| `discussions` | Required | Provides `ElggDiscussion`, core discussion entity type |
| `groups`      | Required | Group container and forum tools integration |
| `hypeLists`   | Optional | Collection rendering framework |
| `hypeStash`   | Optional | Related discussions counter preloader |
| `hypeInteractions` | Optional | Comment threading depth setting |
| `hypeCapabilities`  | Optional | Role-based access control for post discussions |
| `hypePost`    | Optional | Post/edit form views (`post/add`, `post/edit`, `post/view`) |

## Data Migration

No data migration scripts are required for the 3.x → 4.x or 4.x → 5.x
upgrades. No serialized metadata fields and no schema changes. The `status`,
`threads`, and `discussed_post_guid` metadata are stored as plain strings/integers.
