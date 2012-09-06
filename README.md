YourlsBlacklistIPs

Plugin for Yourls allowing to blacklist IPs

This plugin is intended to be used with YOURLS (cf. http://yourls.org)

It has been tested on YOURLS v1.5 and v1.5.1

Current version is 1.1, updated on 09/03/2012

Contact : Ludo at Ludo.Boggio+GitHub@GMail.com

INSTALL :
- In /user/plugins, create a new folder named BlackListIP
- In this new directory, copy the plugin.php file from this repository
- Go to the Plugins administration page and activate the plugin

Currently, the IP addresses to blacklist have to be entered in the config.php (should be into the /user directory)with following format :
$yourls_blacklist_ip = array ("149.3.136.114","119.180.110.179"); with one IP per quotes.

I'm currently working on an admin page to enter those IPs.

Roadmap contains the possibility to use regexp to specify the IP addresses. No, no date provided :)
