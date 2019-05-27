# OpenContent eZ Tika

eZ Tika is an extension that enables a handler for converting multiple binary file formats to plain text as used by the search engine (if you enabled those attributes as searcheable)

Currently, most common formats are enabled (see also binaryfile.ini.append.php):

 - application/pdf
 - application/msword
 - application/vnd.ms-excel
 - application/vnd.ms-powerpoint
 - application/vnd.visio
 - application/vnd.ms-outlook
 - application/xml
 - application/rtf
 - application/vnd.oasis.opendocument.text
 - application/vnd.oasis.opendocument.presentation
 - application/vnd.oasis.opendocument.spreadsheet
 - application/vnd.oasis.opendocument.formula
 - application/zip
 - application/vnd.openxmlformats-officedocument.wordprocessingml.document
 - application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
 - application/vnd.openxmlformats-officedocument.presentationml.presentation
 - application/octet-stream *(not enabled by default)*

License GNU GPL 2.0 - [Apache Tika](https://tika.apache.org/) is licensed with the ASF License (Apache)

## Install

1. Extract the eztika extension, and place it in the extensions folder.

2. Check if extension/eztika/bin files are executeable by the webserver

   OR

   Copy the eztika shell script and tika-app-*version*.jar from the extension bin folder to a
   location your web server has access to and edit the shell script
   as well to set the path to the tika.jar file (make sure it is executable)

3. Enable the extension in eZ Publish. Do this by opening `settings/override/site.ini.append.php` ,
   and add in the `[ExtensionSettings]` block:
   `ActiveExtensions[]=eztika`

4. Update the class autoloads by running the script: php bin/php/ezpgenerateautoloads.php -e

5. If something is not working you can enable Debugging in `eztika.in.append.php`
```
    [DebugSettings]
    # Debug=enabled|disabled
    # if enabled
    # - write Debug Messages to eztika.log
    #
    # Note: an error message to error.log is always written
    # if eztika can not extract any content from binaryfile
    Debug=disabled

    # KeepTempFiles=enabled|disabled
    # if enabled var/cache/ eztika_xxx.txt tmp files are not unlinked
    # to debug metadata which is extracted from the binaryfile
    # The setting is only active if Debug=enabled
    KeepTempFiles=disabled
```    