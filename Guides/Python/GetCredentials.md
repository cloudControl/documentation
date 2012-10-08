# Getting Add-on credentials

Every deployment gets different credentials for each Add-on. Providers can change these credentials at any time. It is therefor required to read  the credentials from the provided JSON file to keep the application running in case the credentials change.

The path to the JSON file can be found in the CRED_FILE environment variable. To see the format and contents of the creds.json file locally use the addon.creds command, `cctrlapp APP_NAME/DEP_NAME addon.creds`.

You can add this code wherever you want within your Python application to get these credentials.

~~~python
  import os
  import json

  try:
      cred_file = open(os.environ["CRED_FILE"])
      data = json.load(cred_file)
      creds = data['ADDON_NAME']
      config = {
        'var1_name': creds['ADDON_NAME_PARAMETER1'],
        'var2_name': creds['ADDON_NAME_PARAMETER2'],
        'var3_name': creds['ADDON_NAME_PARAMETER3']
        # e.g. for MYSQLS: 'hostname': creds[MYSQLS_HOSTNAME]
      }
  except IOError:
      print 'Could not open file'
~~~
