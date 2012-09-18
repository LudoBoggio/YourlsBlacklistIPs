YourlsBlacklistIPs

Plugin for Yourls allowing to blacklist IPs

This plugin is intended to be used with YOURLS (cf. http://yourls.org)

It has been tested on YOURLS v1.5 and v1.5.1

Current version is 1.3, updated on 18/09/2012

Contact : Ludo at Ludo.Boggio+GitHub@GMail.com

INSTALL :
- In /user/plugins, create a new folder named BlackListIP
- In this new directory, copy the plugin.php and ludo_blacklist_ip_Check_IP_Module.php files from this repository
- Go to the Plugins administration page and activate the plugin

You will see in the admin section a new admin page where you can add the IP addresses you want to blacklist.

Please enter one IP address per line. Other syntax should provide unexpected behaviours.

v1.0 : initialization
v1.1 : Add admin page
v1.2 : Add some checks on IP format and some warnings for use
v1.3 : Add several possibilities to provide IP ranges :
       - A.B.C.D-X.Y.Z.T range : all IPs from A.B.C.D to X.Y.Z.T are blacklisted
	   - A.B.C.0 range : all IPs from A.B.C.0 to A.B.C.255 are blacklisted
	   - A.B.0.0 range : all IPs from A.B.0.0 to A.B.255.255 are blacklisted
	   - A.0.0.0 range : all IPs from A.0.0.0 to A.255.255.255 are blacklisted
	   - A.B.C.D/X.Y.Z.T : A.B.C.D is an IP address, X.Y.Z.T is a subnet mask, all IPs addresses corresponding to that IP and mask are blacklisted
	   - A.B.C.D/T, T between 0 TO 32 : CIDR notation.
	   
	   For explanations, feel free to check http://en.wikipedia.org/wiki/IP_address .

Actual roadmap is empty, but I'm open to suggestions. Feel free to contact me.