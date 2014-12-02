#Searchly ElasticSearch

Don't bother with the administrative operations or reliability issues of a search platform. Searchly is a hosted, managed and scalable search as a service powered by ElasticSearch, the final frontier of search engines.

##Adding the Searchly ElasticSearch Add-on
To add the Searchly ElasticSearch Add-on use the addon.add command.

```
$ cctrlapp APP_NAME/DEP_NAME addon.add searchly.OPTION
```
Replace searchly.OPTION with a valid option, e.g. searchly.micro.

When added, Searchly ElasticSearch automatically creates a new user account. You can manage the Add-on within the web console (go to the specific deployment and click the link "searchly.OPTION").

## Getting Started With ElasticSearch Clients

### Using Tire with Rails

[Tire](https://github.com/karmi/tire) is a Ruby client for the ElasticSearch search engine. It provides Ruby-like API for fluent communication with the ElasticSearch server and blends with ActiveModel class for convenient usage in Rails applications.
It allows to delete and create indices, define mapping for them, supports the bulk API, and presents an easy-to-use DSL for constructing your queries.
It has full ActiveRecord/ActiveModel compatibility, allowing you to index your models (incrementally upon saving, or in bulk), searching and paginating the results.

#### Configuration

Ruby on Rails applications will need to add the following entry into their `Gemfile`.

```ruby
gem 'tire'
```
Update application dependencies with bundler.
```shell
$ bundle install
```
Configure Tire in `configure/application.rb` or `configure/environment/production.rb`

```ruby
Tire.configure do
  url ENV['SEARCHLY_URL']
end
```

#### Search

Make your model searchable:

```ruby
class Document < ActiveRecord::Base
  include Tire::Model::Search
  include Tire::Model::Callbacks
end
```

When you now save a record:

```ruby
Document.create :name => "Cost",
               :text => "Cost is claimed to be reduced and in a public cloud delivery model capital expenditure is converted."
````

The included callbacks automatically add the document to a `documents` index, making the record searchable:

```ruby
@documents = Document.search 'Cost'
```

Tire has very detailed documentation at it's [github page](https://github.com/karmi/tire).

### Using Jest with Java

[Jest](https://github.com/searchbox-io/Jest) is a Java HTTP Rest client for ElasticSearch.It is actively developed and tested by Searchly.

#### Configuration

Ensure you have added Sonatype repository to your pom.xml

     <repositories>
     .
     .
       <repository>
         <id>sonatype</id>
         <name>Sonatype Groups</name>
         <url>https://oss.sonatype.org/content/groups/public/</url>
       </repository>
     .
     .
     </repositories>


With Maven add Jest dependency to your pom.xml

     <dependency>
       <groupId>io.searchbox</groupId>
       <artifactId>jest</artifactId>
       <version>0.0.5</version>
     </dependency>

Install Jest via Maven

```term
$ mvn clean install
```
#### Configuration

Create a Jest Client:

```java
// Configuration
ClientConfig clientConfig = new ClientConfig.Builder("SEARCHLY_URL")
.multiThreaded(true).build();

// Construct a new Jest client according to configuration via factory
JestClientFactory factory = new JestClientFactory();
factory.setClientConfig(clientConfig);
JestClient client = factory.getObject();
```

#### Indexing

Create an index via Jest with ease;

```java
client.execute(new CreateIndex.Builder("articles").build());
```
Create new document.

```java
Article source = new Article();
source.setAuthor("John Ronald Reuel Tolkien");
source.setContent("The Lord of the Rings is an epic high fantasy novel");
```

Index article to "articles" index with "article" type.

```java
Index index = new Index.Builder(source).index("articles").type("article").build();
client.execute(index);
```

#### Searching

Search queries can be either JSON String or ElasticSearch SearchSourceBuilder object
(You need to add ElasticSearch dependency for SearchSourceBuilder).

```java
String query = "{\n" +
    "    \"query\": {\n" +
    "        \"filtered\" : {\n" +
    "            \"query\" : {\n" +
    "                \"query_string\" : {\n" +
    "                    \"query\" : \"Lord\"\n" +
    "                }\n" +
    "            }\n"+
    "        }\n" +
    "    }\n" +
    "}";

Search search = (Search) new Search.Builder(query)
// multiple index or types can be added.
.addIndexName("articles")
.addIndexType("article")
.build();

List<Article> result = client.getSourceAsObjectList(Article.class);
```

Jest has very detailed documentation at it's github [page](https://raw.github.com/searchbox-io/Jest).

### Using Haystack with Django

[Haystack](http://haystacksearch.org/) provides modular search for Django. It features a unified, familiar API that allows you to plug in different search backends
without having to modify your code. Currently Haystack 2.0.0-beta can be integrated to SearchBox.io ElasticSearch.

#### Configuration

Under the hood Haystack uses [pyelasticsearch](https://github.com/rhec/pyelasticsearch) (A Lightweight ElasticSearch client) to integrate with ElasticSearch.

Django applications will need to add following entries into their `requirements.txt`;

```shell
pyelasticsearch==0.2
-e git+https://github.com/toastdriven/django-haystack.git#egg=django_haystack-dev
```
or install via pip;

```shell
pip install pyelasticsearch==0.2
pip install -e git+https://github.com/toastdriven/django-haystack.git@master#egg=django-haystack
```

As with most Django applications, you should add Haystack to the INSTALLED_APPS within your `settings.py`.

```python
INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.sites',

    # Added.
    'haystack',

    # Then your usual apps...
]
```

Add Haystack connection string to integrate with Searchly into `settings.py` and set a default index name.

```python
import os

HAYSTACK_CONNECTIONS = {
    'default': {
        'ENGINE': 'haystack.backends.elasticsearch_backend.ElasticsearchSearchEngine',
        'URL': os.environ['SEARCHLY_URL'],
        'INDEX_NAME': 'documents',
        },
    }
```

#### Creating SearchIndexes

SearchIndex objects are the way Haystack determines what data should be placed in the search index and handles the flow of data in.
You can think of them as being similar to Django Models or Forms in that they are field-based and manipulate/store data.

To build a SearchIndex, all that’s necessary is to subclass both `indexes.RealTimeSearchIndex` & `indexes.Indexable`, define the fields you want to store data with and define a `get_model` method. We’ll create the following `DocumentIndex` to correspond to our `Document` model. This code generally goes in a `search_indexes.py` file within the app it applies to, though that is not required. This allows Haystack to automatically pick it up. The `DocumentIndex` should look like:

```python
from haystack import indexes
from myapp.models import DocumentIndex

class DocumentIndex (indexes.RealTimeSearchIndex, indexes.Indexable):
    text = indexes.CharField(document=True, use_template=True)

    def get_model(self):
        return Document
```
Additionally, we’re providing use_template=True on the text field. This allows us to use a data template (rather than error prone concatenation) to build the document the search engine will use in searching.
You’ll need to create a new template inside your `template` directory called `search/indexes/myapp/document_text.txt` and place the following inside:

```python
{{ object.name }}
{{ object.body }}
```
Also to integrate Haystack with Django admin, create `search_sites.py` inside your application;

 ```python
import haystack

haystack.autodiscover()
```

#### Setup views

Add the `SearchView` To Your `URLconf`

```python
(r'^search/', include('haystack.urls')),
```
#### Search template sample

Your search template with default url configuration is should be placed under your `template` directory and called `search/search.html`.

```python
{% for result in page.object_list %}
   <p>{{ result.object.name }}</p>
   <p>{{ result.object.body }}</p>
{% empty %}
   <p>No results found.</p>
{% endfor %}
```
#### Searching

With default url configuration you need to make a get request with parameter named `q` to action `/search`.

```html
<form action="/search" method="get">
    <input type="text" name="q">
</form>
```
The [Haystack home page](http://haystacksearch.org/) is great resource for additional documentation.


##Upgrading the Searchly ElasticSearch Add-on
To upgrade from a smaller to a more powerful plan use the addon.upgrade command.

```
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade searchly.OPTION_OLD searchly.OPTION_NEW
```

##Downgrading the Searchly ElasticSearch Add-on
To downgrade to a smaller plan use the addon.downgrade command.

```
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade searchly.OPTION_OLD searchly.OPTION_NEW
```

##Removing the Searchly ElasticSearch Add-on
The Searchly ElasticSearch Add-on can be removed from the deployment by using the addon.remove command.

```
$ cctrlapp APP_NAME/DEP_NAME addon.remove searchly.OPTION
```

## Searchly Dashboard

You can find usefull information about your indices and access analytics information about your search queries.

![Dashboard](https://s3.amazonaws.com/searchly-wordpress/assets/dashboard.png)

##Internal Access
It's recommended to the read database credentials from the creds.json file. The location of the file is available in the CRED_FILE environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about Add-on Credentials in the general documentation.

##Searchly ElasticSearch Code Examples
Please ensure to check our [Searchly Github](https://github.com/searchbox-io) account for sample applications with various languages and frameworks.

##Support
All Searchly support and runtime issues should be submitted via one of the dotCloud Support channels](https://www.cloudcontrol.com/dev-center/support).
Any non-support related issues or product feedback is welcome via email at: support@searchly.com
