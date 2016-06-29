IMPORTANT: This project has been moved to https://bitbucket.org/byuhbll/filmmatch.

Film Match
==========

INTRO

Film Match is an application written by Charles Draper of the Harold B. Lee
Library at Brigham Young University to crowdsource the matching of locally
cataloged films to their corresponding films in IMDb. Given a table of catalog
record IDs and titles, Film Match attempts to find that film in IMDb. Films that
are matched are presented to the user for human verification. Once all films
have been processed, the IMDb ID's can be used in conjunction with
themoviedb.org to pull down film poster images.


SETUP

Experience managing a LAMP stack is required.

Film Match was developed to run on a LAMP stack (Linux, Apache, MySQL, and PHP).
Other software bundles are possible, but may require tweaks to the code.

1. Download Film Match from https://bitbucket.org/byuhbll/filmmatch which I'm
   assuming you have already done.

2. Copy or link the contents of filmmatch to where your web application will be
   executed. This will vary depending on the host. I will refer to this
   location as the APPDIR. APPDIR/htdocs is the document root.
   
   Note: the following packages need to be installed on the host machine
   a. php5-curl
   b. php5-mysql

3. Set up a MySQL database with a user that has SELECT, INSERT, and UPDATE
   privileges on that database.

4. Copy APPDIR/config.inc.sample to APPDIR/config.inc and configure config.inc
   to your environment. Additional instructions are found with each setting in
   this file.

5. Initialize the database by executing the sql script found in
   APPDIR/init/init.sql.

6. Populate the Record table in your database with films from your catalog. You
   only need to populate the catId and title fields. You can also optionally 
   populate the notes field with any other additional information you like, such
   as a call number.
      
   a. Record->catId: The catalog record ID for the film
   b. Record->title: The film title
   c. Record->notes: Anything extra you want to add to the entry
   
   Example
   
   catID    title                          notes
   -------  -----------------------------  ---------
   3160949  The emperor's new groove       DVDC 3138
   3152597  Ever after a Cinderella story  DVDC 3102
   3193309  Fantasia 2000                  DVD 1042
   
   All other fields and tables are populated by filmmatch.


RUN

The application is now ready for use. Direct people to the uri of your
application (ie, http://your.server.com/) to start matching.

Note: As long as Record->status is NULL for a given film, the application will
attempt to find the next best IMDb match.


RESULTS

Results from matching are placed in the Result table. You will want to pay
attention to those records with a status of 'yes' in this table. Using the
imdbId of such records, enrich the corresponding catalog record with that
imdbId. For marc you could add an entry to 035/a with (IMDb)tt00112233. You can
also view just the yes results by going to
http://your.server.com/results.php?status=yes .

The History table is a log of all actions taken by users. Result carries the
final say as to whether catalog records match or not with a particular imdbId.
Keep in mind that films are always being added to themoviedb.org and IMDb so you
will want to keep the Result table intact for future rounds of matching as the
Result table carries all the "no match" decisions and you won't want to
continually re-ask those same questions.
