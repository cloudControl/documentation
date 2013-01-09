# IndexDepot

IndexDepot furnishes search solutions for numerous applications based on the latest technologies. Benefit from the advantages of the Apache Solr and ElasticSearch search server in the cloud.

## Adding IndexDepot

The IndexDepot Add-on can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add indexdepot.OPTION
~~~
When added, Indexdepot automatically creates a new user account with a default search index. You can manage your IndexDepot Add-on easily within the web console (go to the specific deployment and click the link "Login" next to "indexdepot.OPTION").

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.

The JSON file has the following structure:

~~~
{
   "INDEXDEPOT":{
      "INDEXDEPOT_URL:"https://USERNAME:PASSWORD@www.indexdepot.com/solr/INDEX_ID/,
  }
}
~~~

## Upgrade IndexDepot

Upgrading to another version of IndexDepot is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade indexdepot.OPTION_OLD indexdepot.OPTION_NEW 
~~~

## Downgrade IndexDepot

Downgrading to another version of IndexDepot is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade indexdepot.OPTION_OLD indexdepot.OPTION_NEW 
~~~

## Removing IndexDepot

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove indexdepot.OPTION
~~~

## IndexDepot Apache Solr Code Example

Here is an example on how to use an IndexDepot Apache Solr search index with PHP. The example uses the Zend HTTP client. There is another library [solr-php-client](http://code.google.com/p/solr-php-client/), but it didn't support SSL and HTTP authentication for secured search indexes. If you like to use [solr-php- client](http://code.google.com/p/solr-php-client/) for secured indexes, please contact [support@indexdepot.com](http://support@indexdepot.com/) for further information.

## Step 1: Loading Data into Apache Solr

Before indexing some sample data into Apache Solr make sure to configure new fields in schema.xml. When the add-on is added to your deployment, IndexDepot automatically creates a new user account and Solr search index in an Amazon EC2 cloud. You can manage your search index easily from the [web console](https://console.cloudcontrolled.com/) by clicking the IndexDepot add-on entry on your app's deployment page, and you gain immediate access to IndexDepot control panel. Then you can configure new fields for indexing by clicking on *Edit configuration files*. Add a new field name to schema.xml.

~~~
 <fields>
<fieldname="id" type="string" indexed="true" stored="true" required="true" />
<fieldname="name" type="string" indexed="true" stored="true" />
</fields> 
~~~

Don\92t forget to reload your search index in the control panel after adding the new field.

## Step 2: Installing Zend

Let's use the Zend Framework to index and search our first documents. This example assumes that you've downloaded and extracted it to the vendor folder inside the main directory of the cloudControl tutorial app - so you should get similar output if you use *nix:

~~~
$ cd ~/cctrl_tutorial_app/ $ ~/cctrl_tutorial_app: ls vendor/Zend/ Acl Crypt Form Log.php Pdf Test
~~~

To make the Zend Framework usable, we need to add the vendor folder to the PHP include path of our application:

~~~
<?php
// Update include path to include our vendor folder
$path = dirname(__DIR__) . '/vendor/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
?>
~~~

## Step 3: Getting access to IndexDepot Apache Solr credentials

When you install the add-on, IndexDepot populates your app environment with the access credentials. Here's how you access them from the source code:

~~~
<?php
// Parse the json file with add-on credentials
$string = file_get_contents($_ENV['CRED_FILE'], false);
if ($string == false) {
die('FATAL: Could not read credentials file');
}
$creds = json_decode($string, true); // Create a HTTP Client
require_once('Zend/Http/Client.php');
$solrHttpClient = new Zend_Http_Client();
?>
~~~

## Step 4: Index documents

~~~
<?php
$xml = <<<'EOD'
<add>
<doc>
<field name="id">1</field>
<field name="name">cloudControl</field>
</doc>
<doc>
<field name="id">2</field>
<field name="name">IndexDepot</field>
</doc>
</add>
EOD;
$solrHttpClient->setUri($creds['INDEXDEPOT']['INDEXDEPOT_URL'] . 'update');
$solrHttpClient->setParameterPost(array('commit' => 'true'))
->setRawData($xml)
->setEncType('text/xml');
$response = $solrHttpClient->request('POST');
?>
~~~

## Step 5: Search documents

~~~
<?php
$solrHttpClient->setUri($creds['INDEXDEPOT']['INDEXDEPOT_URL'] . 'select');
$solrHttpClient->setParameter(array('q' => 'IndexDepot'));
$response = $solrHttpClient->request('GET');
?>
~~~

## Step 6: Delete documents

~~~
<?php
$xml = '<delete><query>*:*</query></delete>';
$solrHttpClient->setUri($creds['INDEXDEPOT']['INDEXDEPOT_URL'] . 'update');
$solrHttpClient->setParameterPost(array('commit' => 'true'))
->setRawData($xml)
->setEncType('text/xml');
$response = $solrHttpClient->request('POST');
?>
~~~

Read more on indexing and search in [Solr documentation](http://wiki.apache.org/solr/) and [Solr Tutorial](http://lucene.apache.org/solr/api/doc-files/tutorial.html).

