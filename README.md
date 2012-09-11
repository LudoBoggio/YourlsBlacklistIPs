YourlsBlacklistIPs

Plugin for Yourls allowing to blacklist IPs

This plugin is intended to be used with YOURLS (cf. http://yourls.org)

It has been tested on YOURLS v1.5 and v1.5.1

Current version is 1.2, updated on 11/09/2012

Contact : Ludo at Ludo.Boggio+GitHub@GMail.com

INSTALL :
- In /user/plugins, create a new folder named BlackListIP
- In this new directory, copy the plugin.php file from this repository
- Go to the Plugins administration page and activate the plugin

You will see in the admin section a new admin page where you can add the IP addresses you want to blacklist.

Please enter one IP address per line. Other syntax should provide unexpected behaviours.

v1.0 : initialization
v1.1 : Add admin page
v1.2 : Add some checks on IP format and some warnings for use

Roadmap contains the possibility to use regexp to specify the IP addresses. No, no date provided :)
