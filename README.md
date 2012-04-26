Drop2Facebook
=============

Facebook sharing of your Dropbox / SkyDrive photos and documents.

About the hack
--------------

This is a fully functional prototype making use of the new Dropbox '''/delta''' API.
The service, triggered by running cron.php will scan known users' online storages and share the newly found files on FaceBook.
Depending on the file type and the folder where it is located it wil either:

* Upload photos to a gallery on FB;
* Create a wall post either on user's wall on in one of his groups (depending on the file's folder name).

What APIs, tools, kits or other amenities did you use?
------------------------------------------------------

* '''Facebook PHP SDK'''
* '''Dropbox PHP SDK'''
* Microsoft Live! REST services

Source code and links
---------------------

Get it from https://github.com/z7/drop2facebook
