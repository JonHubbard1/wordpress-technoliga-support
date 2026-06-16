=== Technoliga Support ===
Contributors: technoliga
Tags: support, tickets, technoliga, bms
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Manage support tickets for your Technoliga BMS products directly from WordPress admin.

== Description ==

Technoliga Support connects your WordPress site to the Technoliga Business Management System (BMS), letting administrators view, create, and reply to support tickets without leaving the WordPress dashboard.

= Features =
* View all current and historic support tickets in a familiar WordPress list table
* Filter tickets by status (Open, In Progress, Waiting Customer, Resolved, Closed)
* View full ticket details including comments and attachments
* Reply to tickets directly from WordPress admin
* Create new support tickets (Support Request, Bug Report, Feature Request, Question)
* Update ticket status
* Built-in caching for fast page loads
* WordPress nonce security on all forms

= Requirements =
* A Technoliga BMS account with an active subscription
* An API key with `tickets.read` and `tickets.write` scopes

== Installation ==

1. Upload the `technoliga-support` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Technoliga → Settings** and enter your API Key and Base URL
4. Visit **Technoliga → Tickets** to view your support tickets

== Frequently Asked Questions ==

= Where do I get an API key? =
Log in to your Technoliga BMS admin panel, navigate to **Tools → API Keys**, and create a new key scoped to your product with `tickets.read` and `tickets.write` permissions.

= Can non-admin users see the tickets? =
No. The menu and all pages require the `manage_options` capability (typically Administrators only).

= Does this work with the Technoliga frontend widget? =
Yes. The widget and this plugin are completely independent. You can use both simultaneously on the same site.

== Changelog ==

= 1.0.0 =
* Initial release
* Ticket listing, detail, creation, and commenting
* Status updates
* WordPress admin-only access
