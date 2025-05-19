=== LaunchWP ===
Contributors: rajinsharwar
Tags: cache, caching, performance
Requires at least: 4.7
Tested up to: 6.8
Requires PHP: 7.1
Stable tag: 1.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

LaunchWP is a WordPress-focused modern server management dashboard to help you install and launch WordPress on any VPS or cloud provider.

== Description ==

**This plugin makes sure that the LaunchWP page and object cache are cleared when your site's content changes. Not yet a part of LaunchWP? [Sign up here.](https://launchwp.io/pricing/)**

LaunchWP is a modern WordPress server control, designed to plug into any cloud provider to launch and manage WordPress sites without writing a single line of code or command.

This Helper plugin should be installed on websites powered by LaunchWP to allow the page and object cache to be cleared when your site's content changes. Not yet a part of LaunchWP? [Sign up here.](https://launchwp.io/pricing/)

Launching a WordPress site on your very own VPS or Virtual Private Server at Linode, AWS, GCP, DigitalOcean, or in one of the thousands of cloud providers, is definitely the best option for you because you get a powerful server, and have your WordPress sites run blazing fast, have complete control of the server, that too without having to break the bank. 

The problem is that setting up a VPS, installing packages required to run WordPress, setting up SFTP or PHPMyAdmin, securing your server ports, and then installing WordPress core is not an easy task in total, it's a huge hassle and time-consuming even for experienced developers. You have to manage everything from doing security upgrades, managing server packages, upgrading them, configuring the webserver, Database server, PHP, MySQL, Caching, cURL, and a lot more. Not to mention that if you face any issues, you are on your own!

Solution? Connect your VPS with LaunchWP. LaunchWP is a WordPress-focused modern server management dashboard. LaunchWP gives you the speed your WordPress deserves, the security that you need, and features that you will love, all in your own server without running a single line of code or command. With LaunchWP, you can seamlessly launch your WordPress sites in any VPS or cloud provider, including DigitalOcean, Linode, Vultr, GCP, Hetzner, and a lot more.

Get all the features like being in a managed hosting, plus full control of your Website, because it's your own server. 

Manage all your WordPress sites and servers easily from one place. Easily change PHP versions, add custom Nginx and PHP config right from the UI, and access PHPMyAdmin and SFTP with ease. Get help where you need it, when you need it, from our team of expert Sys Admins and WordPress devs. Launch WordPress sites on your own server in minutes, not hours. You can also host your clients' websites affordably and easily, adding precious recurring revenue to your agency or freelance business.

**Any VPS or Cloud Provider**

LaunchWP supports Linode, DigitalOcean, GCP, AWS, and any other VPS or cloud providers. If your server is SSH-able, and can have a fresh installed Ubuntu 24.04 LTS, you can connect it to LaunchWP.

**Docker powered**

LaunchWP powered websites are Docker Powered. This means that none of your websites will affect the other; on the same server, all your websites are isolated from one another.

**One-Click Staging**

Create STAGING websites on LaunchWP with a click. STAGING sites in LaunchWP come with a Free STAGING domain and a Free SSL so that you can debug your issues with freedom. Anytime, resync your LIVE and STAGING website with a few clicks.

**Free SSL/TLS Certificates**

Serving your site over HTTPS is a must these days, not only for security, but to take advantage of the performance improvements of HTTP/2 as well. When you add a site to LaunchWP, a free Letâ€™s Encrypt SSL/TLS certificate will be acquired, installed, and configured for your site. LaunchWP will handle certificate renewals as well, so you hardly need to think about certificates. (Unless you are using CloudFlare, in which case CloudFlare will manage for you)

**Cache All the Things**

LaunchWP websites are super performant, and our Stack is enterprise-tested. The secret of it is LaunchWP's extensive multi-layer caching. LaunchWP has Object caching, Page Caching, and Proxy caching, requests to your static pages hardly do reach your origin. And no, those all are handled by LaunchWP without any additional plugin.

This Helper plugin is specifically for helping your flush cache whenever your content changes. This plugin won't flush your full Redis cache; instead, it will only flush the cache of the components where the content was changed.

**Unified and Secure SFTP Access**

Easily access or share access to your website's SFTP using SSH keys. You control who can access your SFTP and who can't. And no, SFTP users cannot access anything in your server except your website's files. :)

**Auto-Scheduled Off-Server Backups**

LaunchWP offers both manual on-demand and Auto-Scheduled off-server backups to storage providers like DigitalOcean Spaces, AWS S3, Linode Object Storage, CloudFlare R2, BackBlaze, Wasabi, and more. Set it up once, and forget it!

**Support to Spoil You**

LaunchWP support will actively point you in the right direction and offer suggestions for maintaining your server. It's your website and data, stored on your server, and LaunchWP is your server guide!

**For Teams**

Invite your Team to manage your LaunchWP servers or sites. Access is granular, meaning you control what a user can manage. Share access to individual sites or all sites of a server, the choice is yours.

= Features =

* Launching new sites on a VPS or cloud provider is as easy as submitting a form.
* Multiple layers of page and server-level caching out of the box for a blazing fast loading speed, even without using any caching plugins.
* Easily add unlimited SSH keys for SFTP access.
* One-click access and IP restriction for PHPMyAdmin.
* Full integration with Redis Object Cache and Object Cache Pro plugin
* Ensures debug.log files arenâ€™t accessible publicly.
* Protect website by Basic auth anytime with a click
* Custom php.ini and NGINX config right from the dashboard.
* Disable/Enable SSL with a click
* Pre-configured Server-level WordPress Cron.
* Persistent object caching
* Restart services like Nginx, PHP, and Redis individually
* Team access and User management.
* One-click STAGING sites.
* Scheduled and Manual off-server backups
* Page cache purging


== Changelog ==

= 1.0.1 (2025-03-15) =
* Initial release.

= 1.0.2 (2025-04-27) =
* Adding Cron Control

= 1.1 (2025-05-19) =
* Added cache flush for indiviual URLs.
* Added cache flush programatically via PHP methods. Doc: https://launchwp.io/docs/flush-launchwp-cache-programmatically/
