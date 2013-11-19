# QuotaGuard

QuotaGuard is a proxy service for outgoing API calls. By routing your calls through our distributed proxy network we guarantee that your quotas with any IP limiting services are not shared with other CloudControl users and you get consistent, reliable access to critical third party APIs like Google Maps Geocoding.

## Adding QuotaGuard

QuotaGuard can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add quotaguard.OPTION
~~~

## Upgrade QuotaGuard

Upgrading to another version of QuotaGuard is easy and instant:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade quotaguard.OPTION_OLD quotaguard.OPTION_NEW 
~~~

## Downgrade QuotaGuard

Downgrading to another version of QuotaGuard is easy and instant:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade quotaguard.OPTION_OLD quotaguard.OPTION_NEW 
~~~

## Removing QuotaGuard

Removing QuotaGuard will instantly prevent you from accessing our proxy so use with caution:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove quotaguard.OPTION
~~~

## Using QuotaGuard

QuotaGuard provisioning provides you with a unique login to our proxy service exposed via a QUOTAGUARD_URL environment variable. How you use this depends on how you are accessing your external APIs but most HTTP libraries include a way of specifying a proxy server. For Ruby on Rails applications we recommend accessing the QuotaGuard credentials via the ENV variable. This allows you to easily set your proxy server in an initializer statement. For example with the Ruby Geocoder gem:
~~~ruby
# config/initializers/geocoder.rb
Geocoder.configure(
  ...
  :http_proxy => ENV['QUOTAGUARD_URL'].sub(/^http:\/\//, ''),
  :timeout => 5 
)
~~~

Alternatively you can read the QuotaGuard credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.

The JSON file has the following structure:

~~~
{
   "QUOTAGUARD":{
      "QUOTAGUARD_URL":"http://username:password@proxy.quotaguard.com:9292"
   }
}
~~~
## Monitoring QuotaGuard

Most of our plans include analytics via our dashboard. Access this by logging in to your CloudControl console and clicking on the QuotaGuard add-on from within one of your deployments. This will take you to our dashboard on QuotaGuard.com where you will be able to see your real-time and historic usage data.

## QuotaGuard Code Examples
### Using with Rails
Geocoding is the most common usage for QuotaGuard so this tutorial will focus on that use case.

To add geocoding to your Rails project we recommend the [Ruby Geocoder gem](http://www.rubygeocoder.com/).

Once you have completed the standard setup of Ruby Geocoder you can use QuotaGuard by adding the following to your geocoder initializer:

~~~ruby
# config/initializers/geocoder.rb
Geocoder.configure(
  ...
  :http_proxy => ENV['QUOTAGUARD_URL'].sub(/^http:\/\//, ''),
  :timeout => 5 
)
~~~
_RubyGeoder currently expects no protocol in the URL hence why we have to strip it. This restriction will be removed in an upcoming gem release._

### Using with Python/Django
There are many geocoding libraries available for Python but the most used is [geopy](https://github.com/geopy/geopy) which uses [urllib2 environment variables](http://docs.python.org/2.4/lib/urllib2-examples.html) to set a proxy service.

In your application initialization you should set the `http_proxy` variable to match the `QUOTAGUARD_URL`.

~~~python
# Assign QuotaGuard to your environment's http_proxy variable
os.environ['http_proxy'] = os.environ['QUOTAGUARD_URL']
~~~

To test in the Python interpreter

~~~python
import urllib2
os.environ['http_proxy'] = os.environ['QUOTAGUARD_URL']
url = 'http://ip.jsontest.com/'
proxy = urllib2.ProxyHandler()
opener = urllib2.build_opener(proxy)
in_ = opener.open(url)
in_.read()
~~~
## Support
Please email [support@teachmatic.com](mailto:support@teachmatic.com) if you have any problems.

## Additional Resources
More information may be available on our [documentation page](https://www.quotaguard.com/docs).