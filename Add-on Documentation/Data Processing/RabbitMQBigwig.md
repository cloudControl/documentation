[RabbitMQ Bigwig](http://www.cloudcontrol.com/add-ons/rabbitmq-bigwig) is an [Add-on](http://www.cloudcontrol.com/add-ons/) for adding an AMQP message broker to  your application.

RabbitMQ provides robust messaging for applications. It is easy to use and supported on all major operating systems and developer platforms.

Messaging enables software applications to connect and scale. Applications can connect to each other, as components of a larger application, or to user devices and data. Messaging is asynchronous, decoupling applications by separating the sending and receiving of data.

The RabbitMQ Bigwig add-on is brought to you by LShift. We are a software development company with expertise in VMware's vFabric RabbitMQ and a VMware partner. We were also one of the founding companies behind the original development of RabbitMQ.

RabbitMQ was built from the ground up to interoperate with other technologies: it is the leading implementation of AMQP, the open standard for business messaging.

RabbitMQ can carry any type of message and there are many [design patterns](http://www.rabbitmq.com/getstarted.html) you can use to fulfil your use case.

RabbitMQ Bigwig is accessible via an API and has supported client libraries for Ruby, Java, Python, Clojure, Erlang and C#.

## Provisioning the add-on

You can attach RabbitMQ Bigwig to a cloudControl application via the CLI:

<div class="callout">
You can find a list of all plans available <a href="http://www.cloudcontrol.com/add-ons/rabbitmq-bigwig">here</a>.
</div>

    :::term
    $ cctrlapp bigwigrailssample/default addon.add rabbitmq_bigwig.pipkin

Once you have added RabbitMQ Bigwig you will find `RABBITMQ_BIGWIG_TX_URL` and `RABBITMQ_BIGWIG_RX_URL` settings in the app configuration. These contain the canonical URLs used to access the newly provisioned RabbitMQ Bigwig service instance. You will find these settings in your cloudControl console, at `https://www.cloudcontrol.com/console/app/<app name>/dep/<deploy ID>/addons`.

We give you two URLs to ease separating your producers from your consumers. Producers connect to the URL in `RABBITMQ_BIGWIG_TX_URL`, and we shape this connection to provide consistent throughput. Consumers connect to the URL in `RABBITMQ_BIGWIG_RX_URL`. We optimise connections to this URL for the consumer case. This separation of producers and consumers follows [RabbitMQ best practice](http://www.rabbitmq.com/memory.html). You can read more about how and why we shape [here](http://bigwig.lshift.net/message-throughput).

After installing RabbitMQ Bigwig you should configure the application to fully integrate with the add-on.

## Using with Rails 3.x

Two popular AMQP client libraries for Ruby are the `bunny` and `amqp` gems. The `amqp` gem uses the asynchronous [`EventMachine`](http://rubyeventmachine.com/) framework, and so is not a good fit for a Rails application. Thus, use the `bunny` gem. Add it to your `Gemfile`:

    :::ruby
    source 'https://rubygems.org'

    gem 'rails', '3.0.10'
    gem 'sqlite3'
    gem 'bunny'

    [...]

After modifying `Gemfile`, run `bundle install` to update `Gemfile.lock`:

    :::term
    $ bundle install
    Fetching source index for https://rubygems.org/
    Using rake (0.9.2)
    [...]
    Using rails (3.0.10)
    Your bundle is complete! Use `bundle show [gemname]` to see where
    a bundled gem is installed.

For a full example of using Bigwig and the Bunny gem with Rails, please check out our [example rails application](https://github.com/lshift/rabbitmq-service-rails-sample). Once that's checked out, you'll be able to deploy that to cloudControl by running the following shell commands:

    $ git clone git://github.com/lshift/rabbitmq-service-rails-sample.git
    ...
    $ cd rabbitmq-service-rails-sample
    $ cctrlapp bigwigrailssample/default create ruby
    $ cctrlapp bigwigrailssample/default addon.add rabbitmq_bigwig.pipkin
    $ cctrlapp bigwigrailssample/default open

The command `cctrlapp bigwigrailssample/default open` should open the sample application in your web-browser. If it can't figure out how to open a browser on your platform, then visiting the URL previously output by `cctrlapp bigwigrailssample/default create ruby` with any browser will work fine.

## Using with Ruby (more generally)

The `amqp` gem is especially appropriate when running on a worker dyno, as it supports writing software that reacts to external events. For example, the following example will listen for messages on the `amqpgem.examples.hello_world` queue, and exit 60 seconds after it receives the first message.

    :::ruby
    require "rubygems"
    require 'amqp'
    require "amqp/extensions/rabbitmq"

    EventMachine.run do
      connection = AMQP.connect(:host => '127.0.0.1', :port => 5672, :vhost => 'a')
      puts "Connecting to AMQP broker. Running #{AMQP::VERSION} version of the gem..."

      channel  = AMQP::Channel.new(connection)
      channel.queue("amqpgem.examples.hello_world", :auto_delete => true, :nowait => true) do |queue|
        exchange = channel.default_exchange

        queue.subscribe do |payload|
          puts "Received a message: #{payload}. Disconnecting..."

          EventMachine::Timer.new(60) do
            connection.close {
              EventMachine.stop { exit }
            }
          end
        end

        exchange.publish "Hello, world!", :routing_key => queue.name
      end
    end

### Development environment

You can install RabbitMQ Bigwig for use in a local development environment.  Typically this entails installing RabbitMQ and pointing the `RABBITMQ_BIGWIG_TX_URL` and `RABBITMQ_BIGWIG_RX_URL` URLs to this local service via `export RABBITMQ_BIGWIG_TX_URL=amqp://guest:guest@localhost/ RABBITMQ_BIGWIG_RX_URL=amqp://guest:guest@localhost/`.

RabbitMQ Bigwig uses version 3.1.5. In order to rule out possible differences in behaviour between RabbitMQ versions, you should install version 3.1.5.

<table>
  <tr>
    <th>If you have...</th>
    <th>Install with...</th>
  </tr>
  <tr>
    <td>Mac OS X</td>
    <td style="text-align: left"><pre><code>cd /usr/local/
git checkout 181e445c5701070adb63ac3365c68040f26f6a6a Library/Formula/rabbitmq.rb
brew install rabbitmq</code></pre></td>
  </tr>
  <tr>
    <td>Windows</td>
    <td style="text-align: left">http://www.rabbitmq.com/install-windows.html but use this installer instead: http://www.rabbitmq.com/releases/rabbitmq-server/v3.1.5/rabbitmq-server-3.1.5.exe</td>
  </tr>
  <tr>
    <td>Debian-like Linux (Debian, Ubuntu, ...)</td>
    <td style="text-align: left">Run as root:<pre><code>$ export DEBIAN_FRONTEND=noninteractive
$ wget http://www.rabbitmq.com/rabbitmq-signing-key-public.asc -O /tmp/rabbitmq-signing-key-public.asc
$ apt-key add /tmp/rabbitmq-signing-key-public.asc
$ rm /tmp/rabbitmq-signing-key-public.asc
$ apt-get -y update # Report any bad checksums
$ apt-get -y install erlang-nox=1:14.b.4-dfsg-1ubuntu1
$ wget -O /tmp/package-rmq.deb http://www.rabbitmq.com/releases/rabbitmq-server/v3.1.5/rabbitmq-server_3.1.5-1_all.deb
$ dpkg -i /tmp/package-rmq.deb
$ rm /tmp/package-rmq.deb</code></pre>
Note that Lucid Lynx's latest Erlang version is `1:13.b.3-dfsg-2ubuntu2.1`.
</td>
  </tr>
  <tr>
    <td>Other (BSD, Solaris)</td>
    <td style="text-align: left">http://www.rabbitmq.com/releases/rabbitmq-server/v3.1.5/rabbitmq-server-generic-unix-3.1.5.tar.gz</td>
  </tr>
</table>

While RabbitMQ will run on Erlang version R12 or above, following RabbitMQ's advice on [which Erlang](http://www.rabbitmq.com/which-erlang.html) to use, we strongly suggest using R15 or R16. If you're running a recent Linux distribution (e.g., Ubuntu Oneiric Ocelot or Precise Pangolin), you shouldn't have any trouble here, nor should you encounter problems using the installers for either Windows or Mac OS X.

## Dashboard

<div class="callout">
For more information on the features available within the RabbitMQ Bigwig dashboard please see the docs on <a href="http://www.rabbitmq.com/management.html">RabbitMQ's management plugin</a>.
</div>

The RabbitMQ Bigwig dashboard allows you to keep close watch over your broker, controlling your exchanges, queues, bindings, users, and so on.

![RabbitMQ Bigwig Dashboard](https://www.rabbitmq.com/img/management/overview.png "RabbitMQ Bigwig Dashboard")

You can access the dashboard via the CLI:

    :::term
    $ cctrlapp bigwigrailssample/default open
    Opening rabbitmq-bigwig for sharp-mountain-4005... [[[FIXME]]]

or by visiting the [cloudControl apps web interface](https://www.cloudcontrol.com/console) and selecting the application in question. Select RabbitMQ Bigwig from the Add-ons menu.

## Migrating between plans

<div class="note">Application owners should carefully manage the migration timing to ensure proper application function during the migration process.</div>

We will publish details on how to migrate between plans with plan details.

Use the `cctlapp bigwigrailssample/default addons.upgrade` command to migrate to a new plan.

    :::term
    $ cctlapp bigwigrailssample/default addons.upgrade rabbitmq_bigwig.speedwell

## Removing the add-on

You can remove RabbitMQ Bigwig via the CLI.

<div class="warning">This will destroy all associated data and cannot be undone!</div>

    :::term
    $ cctlapp bigwigrailssample/default addons.remove rabbitmq_bigwig.pipkin

Please consume all your messages from your queues before removing the add-on as removal will destroy all data and cannot be undone. Use the web dashboard to confirm.

## Support

Please submit all RabbitMQ Bigwig support and runtime issues via one of the [cloudControl Support channels](https://www.cloudcontrol.com/dev-center/support). Any non-support related issues or product feedback is welcome at rabbitmq-bigwig@lshift.net.

## Additional resources

Additional resources are available at:

* [RabbitMQ website](http://www.rabbitmq.com/)
* [Bigwig documentation[(http://bigwig.io/)
