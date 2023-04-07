# 123Solar Web Logger (PHP/JS Monitoring for Solar Inverters)

[![123solar demo](https://filedn.eu/lA1ykXBhnSe0rOKmNzxOM2H/images/123s/123ss.png)](https://youtu.be/S6DIP39dG7s "123solar demo")

# What can 123Solar do for you ?
123Solar is a lightweight set of PHP/JS files that makes a web logger to monitor your photovoltaic inverter(s).
    
# Prerequisites
123Solar relies on communication(s) application(s) which are not included in this package.
As 123Solar is running on top of a webserver, you must grant the access to your communication(s) application(s) as well as your communication port(s) to the 'http' user.
Json, Calendar and Curl extensions have to be enable in php. Your webserver must allow HTTP authentication.
  
# Warning
Do not open inverter enclosure when under load. High-voltage can cause death or serious injuries !
Both AC and DC power must always be disconnected. Even though, this will not be still 100% safe as internal capacitors may remain charged after disconnecting all sources of power.

# Installation
- Install and test the communication application(s) for your inverter(s) and make sure it is reliable !
- Put the archive on your web server's folder then extract. (tar -xzvf 123solar*.tar.gz)
- Go then in your browser for configuration http://yourIP/123solar/admin/

# Support, Update & Contact
Check the wiki and the 'Help and debugger' section in the administration section. It usually respond to most common(s) issue(s).
  
# License & External copyrights
123Solar is released under the GNU GPLv3 license (General Public License).
This license allows you to freely integrate this library in your applications, modify the code and redistribute it in bundled packages as long as your application is also distributed with the GPL license. 
The GPLv3 license description can be found at http://www.gnu.org/licenses/gpl.html

Highcharts, the javascript charting library is free for non-commercial use only. (http://highcharts.com)

Small-n-flat icons CC0 1.0 Universal (http://paomedia.github.io/small-n-flat/)
