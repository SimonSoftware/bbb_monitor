# BBB Webhooks Monitor #

#### This a FIRST ALPHA VERSION, pls test on a test enviromnment before!

This plugin logs the event readed by [bbb_webhook](https://docs.bigbluebutton.org/dev/webhooks.html) from the Redis BigBlueButton topics
and requires [bigbluebuttonbn](https://moodle.org/plugins/mod_bigbluebuttonbn) moodle plugin.

This plugin is forced to log only the user-joined and user-left events.

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/mod/bbbmonitor

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## Add webservices
Follow this steps:
- enable [webservices](https://moodle/admin/settings.php?section=webservicesoverview)
- enable [REST protocol](https://moodle/admin/settings.php?section=webserviceprotocols)
- create a user with Manager role
- check that the [BBB Monitor service is present](https://moodle/admin/settings.php?section=externalservices)
- add a [token](https://moodle/admin/settings.php?section=webservicetokens) and apply to the user created before linked to the BBB Monitor service

## Configure bbb_webhook
This is a node.js application that listens for all events on BigBlueButton and sends POST requests with details about these events to hooks registered via an API. A hook is any external URL that can receive HTTP POST requests.

Following the bbb_webhook installation you have to install it with
`$ sudo apt-get install bbb-webhooks`
Then go to the config folder with

`cd /usr/local/bigbluebutton/bbb-webhooks/config` 

and in the `default.yml` file leave uncommented only the channel `from-akka-apps-redis-channel`

On `permanentUrl` parameter add `- url: 'https://{yourmoodle}/webservice/rest/server.php?wsfunction=mod_bbbmonitor_log&wstoken={your_moodle_user_token}'`

If your moodle server is on HTTPS you must edit the `/usr/local/bigbluebutton/bbb-webhooks/hook.js` adding this line
`process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";` at the top of the file.

Finally start the bbb_webhook service with `service bbb-webhooks restart`

## Display logs on Moodle
Now you can add the activity on your course choosing the BigBlueButton Meeting to see the logs.
You can export in CSV and Excel format the list (experimental).

## License ##

2021 Simon Software & Services <paolo@simonsoftware.it>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.

[bbb_webhook]: https://docs.bigbluebutton.org/dev/webhooks.html