# Usersnap
[Usersnap](http://usersnap.com) is an [add-on](https://www.cloudcontrol.com/add-ons/usersnap)
to allow your users to give visual feedback in a comfortable and easy way:
directly in their browser.

Stop wasting time trying to understand unclear bug reports. Screenshots with
comments from your users and testers delivered directly to your bug tracker
help you to fix problems faster and speed up your development cycle.
Understand the reported issues immediately by seeing it. Usersnap
reduces expensive communication overhead significantly.

Usersnap integrates seamlessly with your existing Bug Tracker and causes
no switching costs. Connect Usersnap with one of our supported tools and
you will get issue reports in a familiar place. 

[Learn more about all supported tools](http://usersnap.com/support/docs/apicfg)
Usersnap provides simple and yet powerful tools which allow anybody to
report bugs and issues. All bug reports contain additional information
such as the used browser and the source URL of the screenshot making it
easy to reproduce the reported issues. 
[Learn more about Usersnap's features](http://usersnap.com/support/docs/javascript#tools). 

## Adding or removing the Usersnap Add-on
The Add-on comes in different sizes and prices. It can be added by 
executing the command addon.add:
~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add usersnap.OPTION
~~~

".option" represents the plan size, e.g. usersnap.premium.

You can add initial settings during this process using optional command line parameters:
~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add usersnap.OPTION \
      [--targeturls=http://your-website.com,http://your-website-alias.com] \
      [--targetmails=contact@your-mail.com,user1@your-mail.com] \
      [--senderemail=noreply@usersnap.com] \
      [--subject="[Usersnap] New Usersnap feedback"]
~~~
 + __targeturls__ (comma-separated, optional - default: configured domains for your cloud control app): Specify the list of URLs on which you intend to use Usersnap.
 + __targetmails__ (comma-separated, optional - default: cloud control account owner email): You can add one or more email addresses as recipients for Usersnap screenshots.
 + __senderemail__ (optional - default: "noreply@usersnap.com"): If you plan to deliver Usersnap reports to a ticketing system via email, you can change the sender address to your needs.
 + __subject__ (optional - default: "[Usersnap] New Usersnap feedback"): Particularly useful if you want to create an email filter to organize your screenshots.
 
 
Once Usersnap has been added, a `USERSNAP_APIKEY` setting will be available inside the `CRED_FILE` under the `USERSNAP` key. This will contain the API key to be used in the [Usersnap snippet](https://usersnap.com/support/docs/install). 

You can confirm a valid setup using:
~~~
cctrlapp APPNAME/DEPLOYMENTNAME addon.creds
~~~

After installing Usersnap the application should be configured to fully integrate with the addon.

## Upgrade the Usersnap Add-on
Upgrading to another option can easily be done:
~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade usersnap.OPTION_OLD usersnap.OPTION_NEW
~~~
## Downgrade the Usersnap Add-on
Downgrading to another option can easily be done:
~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade usersnap.OPTION_OLD usersnap.OPTION_NEW
~~~
## Removing the Usersnap Add-on
Similarily, an Add-on can also be removed from the deployment easily. The costs only apply for the time the Add-on was active:
~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove usersnap.OPTION
~~~
## Add-on credentials
The access credentials for the Usersnap snippet are stored in the key `USERSNAP` / `USERSNAP_APIKEY` inside the `CRED_FILE`.

It's recommended to read the Usersnap API key from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about Add-on Credentials in the general documentation.

Usersnap can be integrated easily to any type of web page. The Usersnap [support page](http://usersnap.com/support) offers an overview of [how to integrate Usersnap](https://usersnap.com/support/docs/install) in your site via a simple JavaScript snippet.

On CloudControl, one even doesn't have to take care of the API key because it is accessible via the `CRED_FILE` as mentioned above.

Provisioning Usersnap sets up a default email subscription for screenshots. To set up more advanced delivery methods just use the add-on's administration interface. You can open it in the resources view of your app. One single click on the Usersnap add-on opens the configuration view.

## Install Usersnap to your Web App
Usersnap works with every web project, regardless of the backend language. 
All you need to do is include the Usersnap JavaScript code right before
the closing `</body>` tag of your main template.

 
### Example for PHP web sites
Include this snippet in your base template, right before the closing `</body>`
tag (you can easily configure the snippet to your needs with the 
[Usersnap Configurator](http://usersnap.com/configurator)):

```php
<?php
# read the credentials file
$string = file_get_contents($_ENV['CRED_FILE'], false);
if ($string == false) {
    die('FATAL: Could not read credentials file');
}

# the file contains a JSON string, decode it and return an associative array
$creds = json_decode($string, true);

$USERSNAP_APIKEY = $creds['USERSNAP']['USERSNAP_APIKEY'];
?>

<script type="text/javascript">
   var _usersnapconfig = {
       apiKey: "<?php echo $USERSNAP_APIKEY; ?>",
       valign: 'bottom',
       halign: 'right',
       tools: ["pen", "highlight", "note"],
       lang: 'en',
       commentBox: true,
       emailBox: true
   }; 
   (function() {
       var s = document.createElement('script');
       s.type = 'text/javascript';
       s.async = true;
       s.src = '//api.usersnap.com/usersnap.js';
       var x = document.getElementsByTagName('head')[0];
       x.appendChild(s);
   })();
</script>
</body>
</html>
```
You can learn more about getting addon credentials with PHP in the 
[cloudControl Add-On-Credentials Doc](https://www.cloudcontrol.com/dev-center/Guides/PHP/Add-on%20credentials).

### Example for Python web sites (Example for mako templates)
Include this snippet in your base template, right before the closing `</body>`
tag (you can easily configure the snippet to your needs with the 
[Usersnap Configurator](http://usersnap.com/configurator)):

```python
<%
import os
import json

apikey = ""
try:
    cred_file = open(os.environ['CRED_FILE'])
    creds = json.load(cred_file)
    apikey = creds.get("USERSNAP", {}).get("USERSNAP_APIKEY", "");
except IOError:
    print "Could not open the creds.json file!"

%>

<script type="text/javascript">
   var _usersnapconfig = {
       apiKey: "${apikey}",
       valign: 'bottom',
       halign: 'right',
       tools: ["pen", "highlight", "note"],
       lang: 'en',
       commentBox: true,
       emailBox: true
   }; 
   (function() {
       var s = document.createElement('script');
       s.type = 'text/javascript';
       s.async = true;
       s.src = '//api.usersnap.com/usersnap.js';
       var x = document.getElementsByTagName('head')[0];
       x.appendChild(s);
   })();
</script>
</body>
</html>
```

You can learn more about getting addon credentials with Python in the 
[cloudControl Add-On-Credentials Doc](https://www.cloudcontrol.com/dev-center/Guides/Python/Add-on%20credentials).

### Example for Ruby on Rails websites
Include this snippet in your base template, right before the closing `</body>` 
tag (you can easily configure the snippet to your needs with the 
[Usersnap Configurator](http://usersnap.com/configurator)):

```ruby
<%
require 'json'
apikey = ""
begin
  cred_file = File.open(ENV["CRED_FILE"]).read
  creds = JSON.parse(cred_file)["USERSNAP"]
  apikey = creds["USERSNAP_APIKEY"]
rescue
  puts "Could not open the creds.json file"
end
%>

<script type="text/javascript">
   var _usersnapconfig = {
       apiKey: "<%= apikey %>",
       valign: 'bottom',
       halign: 'right',
       tools: ["pen", "highlight", "note"],
       lang: 'en',
       commentBox: true,
       emailBox: true
   }; 
   (function() {
       var s = document.createElement('script');
       s.type = 'text/javascript';
       s.async = true;
       s.src = '//api.usersnap.com/usersnap.js';
       var x = document.getElementsByTagName('head')[0];
       x.appendChild(s);
   })();
</script>
</body>
</html>
```

Tip: Save this snippet in a file named `_usersnap.html.erb` and you can include
it in other templates by adding this line to your main template:

```ruby
<%= render :partial => "usersnap" %>
```


You can learn more about getting addon credentials with Ruby in the 
[cloudControl Add-On-Credentials Doc](https://www.cloudcontrol.com/dev-center/Guides/Ruby/Add-on%20credentials).

### Example for Java, NodeJS and all other web languages 
Include this snippet in your base template, right before the closing `</body>` tag
(you can easily configure the snippet to your needs with the 
[Usersnap Configurator](http://usersnap.com/configurator)):

```html
<script type="text/javascript">
   var _usersnapconfig = {
       apiKey: "YOUR-APIKEY-HERE",
       valign: 'bottom',
       halign: 'right',
       tools: ["pen", "highlight", "note"],
       lang: 'en',
       commentBox: true,
       emailBox: true
   }; 
   (function() {
       var s = document.createElement('script');
       s.type = 'text/javascript';
       s.async = true;
       s.src = '//api.usersnap.com/usersnap.js';
       var x = document.getElementsByTagName('head')[0];
       x.appendChild(s);
   })();
</script>
</body>
</html>
```

Make sure to replace `YOUR-APIKEY-HERE` with the addon credential value stored in the `USERSNAP.USERSNAP_APIKEY`
option. [How to get add on credentials in cloudControl](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons)

## Support
If you need help installing the Usersnap snippet or you have further questions, 
please get in touch with the [Usersnap team](https://usersnap.com/support). 

