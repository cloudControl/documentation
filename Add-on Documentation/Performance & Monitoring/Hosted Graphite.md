# Hosted Graphite: Graphic metrics made scalable

Graphite is the leading open-source metrics collection, retrieval, and visualization service. Collect thousands of metrics from your application and graph them on easily-readable dashboards. Find the pulse of your application and ensure you see the really important things happening in your technology in real-time.

## Adding the Hosted Graphite Add-on

To add the Hosted Graphite Add-on use the addon.add command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add hostedgraphite.OPTION
~~~
Replace `hostedgraphite.OPTION` with a valid option, e.g. `hostedgraphite.free`.

When added, Hosted Graphite automatically creates a new user account with your email adress. You can manage the Add-on within the [web console](https://www.cloudcontrol.com/console) (go to the specific deployment and click the link "hostedgraphite.OPTION").

## Upgrading the Hosted Graphite Add-on

To upgrade from a smaller to a more powerful plan use the addon.upgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade hostedgraphite.OPTION_OLD hostedgraphite.OPTION_NEW
~~~

## Downgrading the Hosted Graphite Add-on

To downgrade to a smaller plan use the addon.downgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade hostedgraphite.OPTION_OLD hostedgraphite.OPTION_NEW
~~~

## Removing the Hosted Graphite Add-on

The Hosted Graphite Add-on can be removed from the deployment by using the addon.remove command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove hostedgraphite.OPTION
~~~

## Internal Access

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-ons) in the general documentation.

## Code Snippets

## Using with Ruby / Rails
### TCP Connection
    
~~~
    require 'socket'

    apikey = ENV['HOSTEDGRAPHITE_APIKEY']
    conn   = TCPSocket.new 'carbon.hostedgraphite.com', 2003
    conn.puts apikey + ".request.time 1444\n"
    conn.close
~~~

### UDP Connection

~~~
    require 'socket'

    apikey = ENV['HOSTEDGRAPHITE_APIKEY']
    sock   = UDPSocket.new
    sock.send apikey + ".request.time 1444\n", 0, "carbon.hostedgraphite.com", 2003
~~~

## Using with Python
### TCP Connection

~~~
    import socket
    import os

    apikey = os.environ['HOSTEDGRAPHITE_APIKEY']
    conn   = socket.create_connection(("carbon.hostedgraphite.com", 2003))
    conn.send("%s.request.time 1444\n" % apikey)
    conn.close()
~~~

### UDP Connection

~~~
    import socket
    import os

    apikey = os.environ['HOSTEDGRAPHITE_APIKEY']
    sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    sock.sendto("%s.request.time 1444\n" % apikey, ("carbon.hostedgraphite.com", 2003))
~~~

## Using with PHP
### TCP Connection

~~~
	<?
	$apikey = getenv('HOSTEDGRAPHITE_APIKEY');
	$conn   = fsockopen("carbon.hostedgraphite.com", 2003);
	fwrite($conn, $apikey . ".request.time 1444\n");
	fclose($conn);
	?>
~~~

### UDP Connection

~~~
   <?
    $apikey  = getenv('HOSTEDGRAPHITE_APIKEY');
    $sock    = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    $message = $apikey . ".request.time 1444\n";
    socket_sendto($sock, $message, strlen($message), 0, "carbon.hostedgraphite.com", 2003);
   ?>
~~~

## Using with Java
### TCP Connection

~~~
    String apikey = System.getenv("HOSTEDGRAPHITE_APIKEY");
    Socket conn   = new Socket("carbon.hostedgraphite.com", 2003);
    DataOutputStream dos = new DataOutputStream(conn.getOutputStream());
    dos.writeBytes(apikey + ".request.time 1444\n");
    conn.close();
~~~

### UDP Connection

~~~
    String apikey         = System.getenv("HOSTEDGRAPHITE_APIKEY");
    DatagramSocket sock   = new DatagramSocket();
    InetAddress addr      = InetAddress.getByName("carbon.hostedgraphite.com");
    byte[] message        = apikey + ".request.time 1444\n".getBytes()
    DatagramPacket packet = new DatagramPacket(message, message.length, addr, 2003);
    sock.send(packet);
    sock.close();
~~~