# Getting Add-on credentials

Every deployment gets different credentials for each Add-on. Providers can change these credentials at any time. It is therefore required to read  the credentials from the provided JSON file to keep the application running in case the credentials change.

The path to the JSON file can be found in the CRED_FILE environment variable. To see the format and contents of the creds.json file locally use the addon.creds command, `cctrlapp APP_NAME/DEP_NAME addon.creds`.

Follow these steps, so you can get and use these credentials within your Java application.

* ### Prepare your app for reading JSON files

    In this case we'll use [json-simple](http://code.google.com/p/json-simple/), a simple Java tookit to encode or decode JSON text easily. You have to add the privided [JAR file](http://code.google.com/p/json-simple/downloads/detail?name=json_simple-1.1.jar) to your project build path ([how to](http://www.wikihow.com/Add-JARs-to-Project-Build-Paths-in-Eclipse-%28Java%29)).
    You may also need to add the dependency to your 'pom.xml' file:
~~~xml
<dependencies>
        <dependency>
                <groupId>com.googlecode.json-simple</groupId>
                <artifactId>json-simple</artifactId>
                <version>1.1</version>
        </dependency>
</dependencies>
~~~
    Run `mvn install` to apply changes.

* ### Create a class Credentials with all the necessary functions

    Add this class to any package. You will able you to get the credentials from any place in your application. 

~~~java
package com.NAME.SPACE.PACKAGE;

import java.io.FileReader;
import java.io.IOException;
import java.util.Map;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

public class Credentials {
	private JSONObject credFile;

	private static Credentials INSTANCE;

	private Credentials() throws IOException {
		this.credFile = getCredFile();
	}

	public static Credentials getInstance() throws IOException {
		if (INSTANCE == null) {
			INSTANCE = new Credentials();
		}
		return INSTANCE;
	}

	public Object getCredential(String param, String addon) {
		String upper_param = param.toUpperCase();
		String upper_addon = addon.toUpperCase();
		JSONObject addonJSON = getAddon(credFile, upper_addon);
		Object credential = addonJSON.get(upper_addon + "_" + upper_param);
		return credential;
	}

	private JSONObject getAddon(JSONObject credFile, String addonName) {
		JSONObject addon = (JSONObject) credFile.get(addonName);
		return addon;
	}

	private JSONObject getCredFile() throws IOException {
		JSONObject credFile = new JSONObject();
		JSONParser parserJSON = new JSONParser();
		Map<String, String> env = System.getenv();
		String credFilePath = (String) env.get("CRED_FILE");
		Object obj;
		try {
			obj = parserJSON.parse(new FileReader(credFilePath));
		} catch (ParseException parseException) {
			throw new IOException(parseException);
		}
		credFile = (JSONObject) obj;

		return credFile;
	}
}

~~~

* ### Use the Credentials class to get Add-on credentials

   Using our new class, we can get and use the credentials in an easy way storing them in a HashMap structure.

~~~java
Credentials cr = Credentials.getInstance();
String addon = "ADDON_NAME"; // capital letters not required
HashMap<String, Object> creds = new HashMap<String, Object>();
creds.put("var1_name", cr.getCredential("var1_name", addon));
creds.put("var2_name", cr.getCredential("var2_name", addon));
creds.put("var3_name", cr.getCredential("var3_name", addon));
/*
e.g. for MYSQLS:
addon = "MYSQLS";
creds.put("hostname", getCredential("hostname", addon));
*/
~~~

**Important:** All values are stored in our `creds` HashMap as `Object` type. Given that Java is a strongly typed language, you'll probably need to cast these values to get the right type for each credential. 
*E.g.*
~~~java
String database = (String)creds.get("database");
String password = (String)creds.get("password");
Long port = (Long)creds.get("port");
~~~


