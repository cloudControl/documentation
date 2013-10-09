# Found Elasticsearch

[Elasticsearch](http://www.elasticsearch.org) is an open source, distributed, REST-ful search engine. In addition to being an excellent search engine, it is also great for analytics, storing logs, etc. — a general "NoSQL" store.

[Found Elasticsearch](http://www.found.no/) provides dedicated clusters with reserved memory and storage, ensuring predictable performance. You can easily scale your search cluster up or down using the web console, with no downtime. We provide replication and automatic failover for production and mission critical environments, protecting your cluster against unplanned downtime.

## Installing the add-on

To use Found Elasticsearch on cloudControl, install the add-on using the `cctrlapp` command:

    $ cctrlapp APP_NAME/DEP_NAME addon.add foundelasticsearch.option
    
Replace `option` with a valid plan name, such as `dachs`. A list of all plans available can be found [here](https://www.cloudcontrol.com/add-ons/foundelasticsearch).

Once Found Elasticsearch has been added, a `FOUNDELASTICSEARCH_URL` setting will be available in the app configuration and will contain the canonical URL used to access the newly provisioned cluster. 

### Specifying version and plugins

We provide many Elasticsearch versions and plugins.

After the addon has been added, version upgrades and plugin changes can be done through the add-on dashboard.

### Supported versions and plugins

* 0.19.12: analysis-icu, analysis-smartcn, analysis-stempel, analysis-phonetic, analysis-morphology, river-couchdb, river-jdbc, river-rabbitmq, rssriver
* 0.20.4 to 0.20.6: analysis-icu, analysis-morphology, analysis-smartcn, analysis-stempel, analysis-phonetic, river-couchdb, river-jdbc, river-rabbitmq, rssriver</li>
* 0.90.0 to 0.90.5</b>: analysis-icu, analysis-morphology, analysis-smartcn, analysis-stempel, analysis-phonetic, inquisitor, mapper-attachments, river-couchdb, river-jdbc, river-rabbit

New versions are made available for provisioning soon after they're released.

Contact [support@found.no](mailto:support@found.no) if you want the ["attachments"-plugin](http://www.elasticsearch.org/guide/reference/mapping/attachment-type/) enabled.

## Accessing the add-on dashboard

The Found Elasticsearch dashboard allows you to manage the cluster, like upgrading versions, enabling plugins, editing the access control lists (ACLs), and viewing the logs emitted from the nodes.

![Found Elasticsearch Dashboard](https://s3.amazonaws.com/heroku.devcenter/heroku_assets/images/167-original.jpg "Found Elasticsearch Dashboard")

You can access the dashboard through the [cloudControl console](https://www.cloudcontrol.com/console/).

## Access control

With the default configuration, since not all Elasticsearch clients support basic authentication, **anyone knowing the cluster-ID has full access to your cluster**.

We highly recommend using the access control feature to at least require authentication. Authentication uses HTTP Basic-authentication. Most, but not all, HTTP- and Elasticsearch-libraries support this.

You can limit access based on path, source IP, method, username/password and whether SSL is used. The access control-section of the dashboard has annotated samples to use as templates for your own ACLs.

## Using the add-on

In this section, we will briefly go through the indexing, updating, retrieving, searching and deleting documents in an Elasticsearch cluster. We will use [curl](http://curl.haxx.se/) as our client from the command line.

### Indexing

To index documents, simply `POST` documents to Elasticsearch:

    $ curl http://<cluster_id>.foundcluster.com:9200/my_index/my_type -XPOST -d '{
        "title": "One", "tags": ["ruby"]
    }'
    {"ok":true,"_index":"my_index","_type":"my_type","_id":"HAJppjLLTROm8i35IJEQWQ","_version":1}

In the above example, the index `my_index` is created dynamically when the first document is inserted into it.
All documents in Elasticsearch have a `type` and an `id`, which is echoed as `_type` and `_id` in the JSON responses.
If no `id` is specified during indexing, a random `id` is generated.

#### Bulk indexing

To achieve the best possible performance, using the <a href="http://www.elasticsearch.org/guide/reference/api/bulk.html">Bulk API</a> is highly recommended. So let us index a couple more documents using the bulk API:

    $ curl http://<cluster_id>.foundcluster.com:9200/my_index/my_type/_bulk -XPOST -d '
    {"index": {}}
    {"title": "Two", "tags": ["ruby", "python"] }
    {"index": {}}
    {"title": "Three", "tags": ["java"] }
    {"index": {}}
    {"title": "Four", "tags": ["ruby", "php"] }
    '

Elasticsearch should then give us output similar to this:

    {"took":10, "items": [
        {"create":{"_index":"my_index","_type":"my_type","_id":"v7ufoXxSSuOTckcyL7hg4Q","_version":1,"ok":true}},
        {"create":{"_index":"my_index","_type":"my_type","_id":"wOzT31EnTPiOw1ICTGX-qA","_version":1,"ok":true}},
        {"create":{"_index":"my_index","_type":"my_type","_id":"_b-kbI1MREmi9SeixFNEVw","_version":1,"ok":true}}
    ]}
    
    
### Updating

To update an existing document in Elasticsearch, simply `POST` the updated document to `http://<cluster_id>.foundcluster.com:9200/my_index/my_type/<id>`, where `<id>` is the `id` of the document. For example, to update the last document indexed above:


    $ curl http://<cluster_id>.foundcluster.com:9200/my_index/my_type/_b-kbI1MREmi9SeixFNEVw -XPOST -d '{
        "title": "Four updated", "tags": ["ruby", "php"]
    }'
    {"ok":true,"_index":"my_index","_type":"my_type","_id":"_b-kbI1MREmi9SeixFNEVw","_version":2}


As you can see, the document is updated and the `_version` counter is automatically incremented.


### Retrieving documents

We can take a look at the data we indexed by simply issuing a `GET` request to the document:

    $ curl http://<cluster_id>.foundcluster.com:9200/my_index/my_type/_b-kbI1MREmi9SeixFNEVw
    {"exists":true,"_index":"my_index","_type":"my_type","_id":"_b-kbI1MREmi9SeixFNEVw","_version":2,"_source":{"title": "Four updated", "tags": ["ruby", "php"]}}

If Elasticsearch find the document, it returns a HTTP status code of `200 OK` and sets `exists: true` in the result. Otherwise, a HTTP status code of `404 Not Found` is used and the result will contain `exists: false`.

### Searching

Search requests may be sent to the following Elasticsearch endpoints:

1. `http://<cluster_id>.foundcluster.com:9200/_search`
1. `http://<cluster_id>.foundcluster.com:9200/{index_name}/_search`
1. `http://<cluster_id>.foundcluster.com:9200/{index_name}/{type_name}/_search`

We can search using a `HTTP GET` or `HTTP POST` requests. To search using a `HTTP GET` request, we use URI parameters to specify our query:

    $ curl http://<cluster_id>.foundcluster.com:9200/my_index/my_type/_search?q=title:T*

A full explanation of allowed parameters is found in the [Elasticsearch URI Request documentation](http://www.elasticsearch.org/guide/reference/api/search/uri-request.html)

In order to perform more complicated queries, we have to use `HTTP POST` requests to search. In the next example, we create a facet on the `tags` field:

<div class="callout" markdown="1">Note that we added `?pretty=true` to the request, which makes Elasticsearch return a more human readable JSON response. Due to performance reasons, this is not recommended in production.</div>

    $ curl http://<cluster_id>.foundcluster.com:9200/my_index/my_type/_search?pretty=true -XPOST -d '{
        "query": {
            "query_string": {"query": "*"}
        },
        "facets": {
            "tags": {
                "terms": {"field": "tags"}
            }
        }
    }'

A full explanation of how the request body is structured is found in the [Elasticsearch Request Body documentation](http://www.elasticsearch.org/guide/reference/api/search/request-body.html)

To execute multiple queries in one request, use the [Multi Search API](http://www.elasticsearch.org/guide/reference/api/multi-search.html).

### Deleting

Documents are deleted from Elasticsearch by sending `HTTP DELETE` requests.

1. Delete a single document:

        $ curl http://<cluster_id>.foundcluster.com:9200/{index}/{type}/{id} -XDELETE

1. Delete all documents of a given type:

        $ curl http://<cluster_id>.foundcluster.com:9200/{index}/{type} -XDELETE

1. Delete a whole index:

        $ curl http://<cluster_id>.foundcluster.com:9200/{index} -XDELETE

1. Delete all documents matching a query:

    For example, to delete all documents whose title starts with `T`:

        $ curl http://<cluster_id>.foundcluster.com:9200/{index}/{type}/_query -XDELETE -d '{
            "query_string" : { "query" : "title:T*" }
        }

    See [Elasticsearch Delete By Query](http://www.elasticsearch.org/guide/reference/api/delete-by-query.html) for a complete overview of this functionality.


## Elasticsearch clients

Elasticsearch comes with a REST API, which can be used directly via any HTTP client.

Many higher-level clients have been built on top of this API in various programmling languages. A large list of Elasticsearch clients and integrations are found [here](http://www.elasticsearch.org/guide/appendix/clients.html).


## Tire client (Ruby)

[Tire](http://karmi.github.com/tire/) is a rich and comfortable Ruby API on top of the REST API, with built-in support for Rails.

### Configuring Tire

    require 'rubygems'
    require 'tire'
    
    Tire::Configuration.url ENV['FOUNDELASTICSEARCH_URL']

Remember to update application dependencies with bundler.

    $ bundle install

### Indexing documents

We start by indexing a couple of documents:

    Tire.index 'articles' do
      delete
      create

      store :title => 'One',   :tags => ['ruby']
      store :title => 'Two',   :tags => ['ruby', 'python']
      store :title => 'Three', :tags => ['java']
      store :title => 'Four',  :tags => ['ruby', 'php']

      refresh
    end


### Searching

After indexing the documents, we search for articles that has a title starting with "T":

    s = Tire.search 'articles' do
      query do
        string 'title:T*'
      end
    end

    s.results.each do |document|
      puts "* #{ document.title } [tags: #{document.tags.join(', ')}]"
    end
	
    # * Two [tags: ruby, python]


### ActiveModel integration

See the [Tire documentation](http://karmi.github.com/tire/) for more examples and in-depth explanations on how to use Tire to integrate with ActiveModel.

## Removing the add-on

Found Elasticsearch can be removed via the CLI. This will destroy all associated data and cannot be undone!

    $  cctrlapp APP_NAME/DEP_NAME addon.remove foundelasticsearch.option


## Migrating between plans

Available memory is a very important factor when sizing your Elasticsearch cluster, and replicating across multiple data centers is important for the resilience of production applications. Our plans are differentiated on the available reserved memory and disk quota, as well as on the number of data centers.

Use the `cctrlapp addon.upgrade` command to migrate to a new plan:

    $ cctrlapp APP_NAME/DEP_NAME addon.upgrade foundelasticsearch.newplan


Upgrading to a new plan is done by extending the existing cluster with new nodes and migrating data from the old nodes to the new nodes. When the migration is finished, the old nodes are shut down and removed from the cluster.

## Support

Please mail [support@found.no](mailto:support@found.no) if you have any problems.


## Additional resources

Found Elasticsearch exposes the majority of the Elasticsearch REST API, which means that most valid Elasticsearch API requests will work with your provisioned instance. Please refer to the [Elasticsearch guide](http://www.elasticsearch.org/guide/) for more in-depth explanations of all the possibilities.

* [Foundation – articles on all things Elasticsearch](http://www.found.no/foundation/)
* [Elasticsearch Official Guide](http://www.elasticsearch.org/guide/)
* [Elasticsearch Official Google Groups](http://groups.google.com/forum/#!forum/elasticsearch)
* [Elasticsearch source code on GitHub](https://github.com/elasticsearch/elasticsearch)
