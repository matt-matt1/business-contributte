
Skip to content
All gists
Back to GitHub
Sign in
Sign up

Instantly share code, notes, and snippets.
@esperlu
esperlu/mysql2sqlite.sh
Created April 27, 2011 05:46

Code
Revisions 11
Stars 848
Forks 275
Clone this repository at &lt;script src=&quot;https://gist.github.com/esperlu/943776.js&quot;&gt;&lt;/script&gt;
MySQL to Sqlite converter
mysql2sqlite.sh
#!/bin/sh

# Converts a mysqldump file into a Sqlite 3 compatible file. It also extracts the MySQL `KEY xxxxx` from the
# CREATE block and create them in separate commands _after_ all the INSERTs.

# Awk is choosen because it's fast and portable. You can use gawk, original awk or even the lightning fast mawk.
# The mysqldump file is traversed only once.

# Usage: $ ./mysql2sqlite mysqldump-opts db-name | sqlite3 database.sqlite
# Example: $ ./mysql2sqlite --no-data -u root -pMySecretPassWord myDbase | sqlite3 database.sqlite

# Thanks to and @artemyk and @gkuenning for their nice tweaks.

mysqldump  --compatible=ansi --skip-extended-insert --compact  "$@" | \

awk '
BEGIN {
	FS=",$"
	print "PRAGMA synchronous = OFF;"
	print "PRAGMA journal_mode = MEMORY;"
	print "BEGIN TRANSACTION;"
}
# CREATE TRIGGER statements have funny commenting.  Remember we are in trigger.
/^\/\*.*CREATE.*TRIGGER/ {
	gsub( /^.*TRIGGER/, "CREATE TRIGGER" )
	print
	inTrigger = 1
	next
}
# The end of CREATE TRIGGER has a stray comment terminator
/END \*\/;;/ { gsub( /\*\//, "" ); print; inTrigger = 0; next }
# The rest of triggers just get passed through
inTrigger != 0 { print; next }
# Skip other comments
/^\/\*/ { next }
# Print all `INSERT` lines. The single quotes are protected by another single quote.
/INSERT/ {
	gsub( /\\\047/, "\047\047" )
	gsub(/\\n/, "\n")
	gsub(/\\r/, "\r")
	gsub(/\\"/, "\"")
	gsub(/\\\\/, "\\")
	gsub(/\\\032/, "\032")
	print
	next
}
# Print the `CREATE` line as is and capture the table name.
/^CREATE/ {
	print
	if ( match( $0, /\"[^\"]+/ ) ) tableName = substr( $0, RSTART+1, RLENGTH-1 ) 
}
# Replace `FULLTEXT KEY` or any other `XXXXX KEY` except PRIMARY by `KEY`
/^  [^"]+KEY/ && !/^  PRIMARY KEY/ { gsub( /.+KEY/, "  KEY" ) }
# Get rid of field lengths in KEY lines
/ KEY/ { gsub(/\([0-9]+\)/, "") }
# Print all fields definition lines except the `KEY` lines.
/^  / && !/^(  KEY|\);)/ {
	gsub( /AUTO_INCREMENT|auto_increment/, "" )
	gsub( /(CHARACTER SET|character set) [^ ]+ /, "" )
	gsub( /DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP|default current_timestamp on update current_timestamp/, "" )
	gsub( /(COLLATE|collate) [^ ]+ /, "" )
	gsub(/(ENUM|enum)[^)]+\)/, "text ")
	gsub(/(SET|set)\([^)]+\)/, "text ")
	gsub(/UNSIGNED|unsigned/, "")
	if (prev) print prev ","
	prev = $1
}
# `KEY` lines are extracted from the `CREATE` block and stored in array for later print 
# in a separate `CREATE KEY` command. The index name is prefixed by the table name to 
# avoid a sqlite error for duplicate index name.
/^(  KEY|\);)/ {
	if (prev) print prev
	prev=""
	if ($0 == ");"){
		print
	} else {
		if ( match( $0, /\"[^"]+/ ) ) indexName = substr( $0, RSTART+1, RLENGTH-1 ) 
		if ( match( $0, /\([^()]+/ ) ) indexKey = substr( $0, RSTART+1, RLENGTH-1 ) 
		key[tableName]=key[tableName] "CREATE INDEX \"" tableName "_" indexName "\" ON \"" tableName "\" (" indexKey ");\n"
	}
}
# Print all `KEY` creation lines.
END {
	for (table in key) printf key[table]
	print "END TRANSACTION;"
}
'
exit 0
@dumblob
dumblob commented Aug 28, 2015

Thank you @esperlu for this nice script. Many people asked for a separate proper repository, so I made one:

https://github.com/dumblob/mysql2sqlite

I'd like to ask you @esperlu if you agree with the decisions I made - especially the license MIT with you @esperlu being the licensor. It's enough if you just create a new issue on https://github.com/dumblob/mysql2sqlite/issues describing your opinion. If you don't want to have anything in common with this script any more, there is this option to publicly state that your script is Public domain and I'll change the licensor to me.
@darshanganatra
darshanganatra commented Aug 31, 2015

It is not taking any updated data...
If once name updated in mysql then i am trying to convert it but i am not getting any updated data
@Easygeez
Easygeez commented Oct 31, 2015

Hi Esperlu:

I've used your shell script to convert mysql to sqlite but am having issues. I'm not very familiar with awk but I believe the problem is with the comments embedded in the mysqldump input file. I tried to attach the inpout and output files for your review but this comment section here will not accept the file format for some reason.

I pasted small sections of the input and output files below. Any help you can give me on this issue will be greatly appreciated.

Thanks so much,
Roy (aka Easygeez)

INPUT MYSQL
-- MySQL dump 10.15 Distrib 10.0.21-MariaDB, for Linux (x86_64)

-- Host: localhost Database: testDatabase

-- Server version 10.0.21-MariaDB
/!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/!40103 SET TIME_ZONE='+00:00' /;
/!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 /;
/!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 /;
/!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,ANSI' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
-- Current Database: "testDatabaseSqlite"

CREATE DATABASE /!32312 IF NOT EXISTS/ "testDatabase" /*!40100 DEFAULT CHARACTER SET latin1 */;
USE "testDatabase";
-- Table structure for table "ANSWERS"

DROP TABLE IF EXISTS "ANSWERS";
/!40101 SET @saved_cs_client = @@character_set_client */;
/!40101 SET character_set_client = utf8 */;
CREATE TABLE "ANSWERS" (
"entityDate" varchar(32) NOT NULL,
"entityID" varchar(128) NOT NULL,
"subEntityID" varchar(128) NOT NULL,
"statementId" varchar(32) NOT NULL,
"answerId" varchar(32) NOT NULL,
"answer" varchar(1024) NOT NULL,
"commentRequired" varchar(1) NOT NULL,
"skipNextStatement" varchar(1) NOT NULL
);
/*!40101 SET character_set_client = @saved_cs_client */;
-- Dumping data for table "ANSWERS"

LOCK TABLES "ANSWERS" WRITE;
/!40000 ALTER TABLE "ANSWERS" DISABLE KEYS */;
INSERT INTO "ANSWERS" VALUES ('10/03/2015 10:08:16 PM','testDatabase','testDbLeaf','howRU','Good','I am good','n','n'),('10/03/2015 10:08:29 PM','testDatabase','testDbLeaf','howRU','Bad','I am bad','n','n');
/!40000 ALTER TABLE "ANSWERS" ENABLE KEYS */;
UNLOCK TABLES;
-- Table structure for table "ANSWERSLINK"

DROP TABLE IF EXISTS "ANSWERSLINK";
/!40101 SET @saved_cs_client = @@character_set_client */;
/!40101 SET character_set_client = utf8 */;
CREATE TABLE "ANSWERSLINK" (
"entityDate" varchar(32) NOT NULL,
"entityID" varchar(128) NOT NULL,
"subEntityID" varchar(128) NOT NULL,
"statementId" varchar(32) NOT NULL,
"answerId" varchar(32) NOT NULL,
"answerSequence" int(10) unsigned NOT NULL
);

Here is the output sqltie file:

SQLite format 3######@ ####### ##############################################################################################################################################<##9%!###10/03/2015 10:08:29 PMtestDatabasetestDbLeafhowRUBad#<##9%!## 10/03/2015 10:08:16 PMtestDatabasetestDbLeafhowRUGood
##�#Z#########################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################C##9%!####10/03/2015 10:06:42 PMtestDatabasetestDbLeafByeByeInGoodbyeG##9%!###%10/03/2015 10:06:12 PMtestDatabasetestDbLeafhowRUSnHow are You?[##9%!###E10/03/2015 10:05:59 PMtestDatabasetestDbLeafgreetingsInWelcome to Entity Assessment
+##�#w#+#'###########################�I###!!#�]tableSTATEMENTSSTATEMENTS#CREATE TABLE "STATEMENTS" (

"entityDate" varchar(32) NOT NULL,
"entityID" varchar(128) NOT NULL,
"subEntityID" varchar(128) NOT NULL,
"statementId" varchar(32) NOT NULL,
"answerType" varchar(1) DEFAULT NULL,
"answerRequired" varchar(1) NOT NULL,
"statement" varchar(1024) NOT NULL
)�#######�#tableANSWERSLINKANSWERSLINK#CREATE TABLE "ANSWERSLINK" (
"entityDate" varchar(32) NOT NULL,
"entityID" varchar(128) NOT NULL,
"subEntityID" varchar(128) NOT NULL,
"statementId" varchar(32) NOT NULL,
"answerId" varchar(32) NOT NULL,
"answerSequence" int(10) NOT NULL
)�d######�#tableANSWERSANSWERS#CREATE TABLE "ANSWERS" (
"entityDate" varchar(32) NOT NULL,
"entityID" varchar(128) NOT NULL,
"subEntityID" varchar(128) NOT NULL,
"statementId" varchar(32) NOT NULL,
"answerId" varchar(32) NOT NULL,
"answer" varchar(1024) NOT NULL,
"commentRequired" varchar(1) NOT NULL,
"skipNextStatement" varchar(1) NOT NULL
)
@oviniciusfeitosa
oviniciusfeitosa commented Dec 15, 2015

Great initiative! To generate autoincrement values just change to:

#gsub( /AUTO_INCREMENT|auto_increment/, "" )
    gsub( /int\([0-9]+\) NOT NULL AUTO_INCREMENT/, "INTEGER PRIMARY KEY AUTOINCREMENT" )
    gsub( /PRIMARY KEY \("[a-zA-Z]+"\)/, "")

    gsub( /(CHARACTER SET|character set) [^ ]+ /, "" )
    gsub( /DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP|default current_timestamp on update current_timestamp/, "" )
    gsub( /(COLLATE|collate) [^ ]+ /, "" )
    gsub(/(ENUM|enum)[^)]+\)/, "text ")
    gsub(/(SET|set)\([^)]+\)/, "text ")
    gsub(/UNSIGNED|unsigned/, "")
    if (prev) print prev ","
    prev = $1

@Instagit
Instagit commented Jan 18, 2016

gsub(/(ENUM|enum)[^)]+\)/, "text ")

This line breaks the SQL dump if there's a column with the string "enum" in it, for example "housenumber". Changing it to

gsub(/^(ENUM|enum)[^)]+\$)/, "text ")

fixes it for me, but I don't know if this can cause any other problems. It's probably the same for line 73.
@polosson
polosson commented Feb 3, 2016

Hi there, I wrote a little modification to support the "UNIQUE" key at index creation. @hawkmaster, @DubFriend See my fork if you want it.
@chibisov
chibisov commented Feb 7, 2016

I've built a docker container for the sequel one liner database conversion https://blog.chib.me/how-to-convert-databases-with-one-line-of-code/
@Tusharsb
Tusharsb commented Feb 13, 2016

I faced error of 'no such table' appearing repeatedly. Probably it had something to do with the character set of my data. I couldnt get it to work with mysql2sqlite.sh. So I resorted to using a UI tool called Kexi. Read about using Kexi to import from MySQL and export to SQLite from here : http://stackoverflow.com/a/35377403/3143538
@AttitudeMonger
AttitudeMonger commented Feb 21, 2016

I am having some bizarre problems with this script. First of all, the MySQL database I am loading is this: https://sourceforge.net/projects/wnsql/files/latest/download?source=files. It contains many .sql files, I load the schema one first into a MySQL db called wordnet, followed by the data one, and then run some simple queries on the tables created to find that everything has been loaded okay. Next as per this script I try this command:

./sqlite.sh -u <username> -p<password> wordnet | sqlite3 database2.sqlite

Now this goes on for like 2-3 hours, without command prompt returning. After some time, I see the output .sqlite file has attained a size of around 20.5 MB. Finally when I hit CTRL-C to terminate the process, it spits out a mammoth chunk of MySQL commands in the command prompt, and the .sqlite file is reduced to zero size.

How do I make it work?
@pvsune
pvsune commented Feb 26, 2016

you just saved me a lot of hours! thanks for the great work! 👍
@dumblob
dumblob commented Mar 4, 2016

@AttitudeMonger, @Tusharsb, @chibisov, @polosson, @Instagit, @vinnyfs89, @Easygeez this gist is discontinued (see https://gist.github.com/esperlu/943776#gistcomment-1561966).
Development continues on https://github.com/dumblob/mysql2sqlite .

Please contribute there (especially @polosson with the UNIQUE patch, and @Instagit with the ENUM patch - but please first try the developed version if the issue still persists).
@rotexhawk
rotexhawk commented Apr 2, 2016

This doesn't work for "Arabic text" the field declaration in mysql is UTF-8. When I convert the database the arabic text is changed to ????
The development branch gives syntax error.
@knurum
knurum commented Apr 12, 2016

I am totally new to this. I don't know how to do on my app. Anyone please help me to tell me step by step how to do?
@esperlu
Author
esperlu commented May 11, 2016

To all: Sorry for having forgotten this gist. I hereby confirm that my script can be used under the MIT licence.

Once again, my apologies for my slow reaction to your requests. Thanks to all for having improved it.
@esperlu
Author
esperlu commented May 11, 2016

To all: This gist is deprecated. This script continues to be maintained by @dumblog here:
https://github.com/dumblob/mysql2sqlite

Thanks to him for taking it over.
@maxkoryukov
maxkoryukov commented Jun 28, 2016

Hello, @esperlu !

Thank you for the script, but one thing: I've googled this script, have converted mysql->sqlite.
And then I found the last ccomment this gist is deprecated 😆

It is enough funny)) But, please, add the deprecation warning to the top of the gist;) Or to the How to use section;)
@srias
srias commented Jun 30, 2016

hello,
when i use this command below ,empty database.sqlite is created but no tables present in it.
$ chmod +x mysql2sqlite.sh
./mysql2sqlite.sh -u MyUserName -pMySecretPassWord myDbase | sqlite3 database.sqlite

I took DB dump from phpmyadmin (.sqlfile) and followed the steps given above 'but on executing above commands i get result as 'memory' in cmd

Iam using ubuntu,Can any one please expalin how to import .sql file to sqlite
Thanks in Advance.
@kopiro
kopiro commented Jul 12, 2016

On line 69, there is a bug:

gsub( /(CHARACTER SET|character set) [^ ]+/, "" )

You have to remove the space after the +!
@ephraimumpan
ephraimumpan commented Aug 24, 2016

can somebody help me how to use mysql2sqlite.sh in converting mysql to sqlite3 because i really don't have any idea...please help me...from scratch tutorial on windows.....
@ephraimumpan
ephraimumpan commented Aug 24, 2016

can somebody help me how to use mysql2sqlite.sh in converting mysql to sqlite3 because i really don't have any idea...please help me...from scratch tutorial on windows.....
@ephraimumpan
ephraimumpan commented Aug 24, 2016

can somebody help me how to use mysql2sqlite.sh in converting mysql to sqlite3 because i really don't have any idea...please help me...from scratch tutorial on windows.....email me at: eumpan@gmail.com
@ephraimumpan
ephraimumpan commented Aug 24, 2016

can somebody help me how to use mysql2sqlite.sh in converting mysql to sqlite3 because i really don't have any idea...please help me...from scratch tutorial on windows.....email me at: eumpan@gmail.com
@kumarpatel
kumarpatel commented Aug 25, 2016

@ephraimumpan
https://github.com/dumblob/mysql2sqlite
Use this instead. Forked from this gist and improved upon.
Has instructions on usage as well.
@joonas-fi
joonas-fi commented Feb 4, 2017

For exporting SQL databases (mysql/postgres/sqlite) to JSON (after which you can write a script to import somewhere else or just process the data), see: https://github.com/function61/sql2json
@mauvm
mauvm commented Nov 10, 2017 •

The script doesn't handle CONSTRAINT(s) after a column where KEY(s) were removed in between. This causes SQLITE_ERROR: near FOREIGN: syntax error. Example output:

CREATE TABLE "foo" (
  "bar" varchar(32) NOT NULL
  CONSTRAINT "..." FOREIGN KEY ...
);

Notice the missing comma after the bar column definition? To fix this add:

    removedKeys = 0 after line 57
    if (prev == "" && removedKeys > 0) print "  ," after line 75 (was 74)
    removedKeys += 1 after line 86 (was 84)

@StarveTheEgo
StarveTheEgo commented Oct 12, 2018

    can somebody help me how to use mysql2sqlite.sh in converting mysql to sqlite3 because i really don't have any idea...please help me...from scratch tutorial on windows.....

ahaha, you spammed here same way too, lmfao
@Satish-A-Wadekar
Satish-A-Wadekar commented Mar 23, 2019 •

i am facing this issue

./mysql2sqlite.sh: line 14: mysqldump: command not found

can anybody give the step by step execution of this script please ? i have never run any bash script file.
@ianarman1
ianarman1 commented Jun 4, 2019 •

Hello,

I ran this script, and received

mysqldump: Error: 'Expression #6 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'information_schema.FILES.EXTRA' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by' when trying to dump tablespaces memory 

after running ls -l on the file, i can confirm there is data in the file.
@R46narok
R46narok commented Oct 4, 2020

    Hello,

    I ran this script, and received

    mysqldump: Error: 'Expression #6 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'information_schema.FILES.EXTRA' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by' when trying to dump tablespaces memory 

    after running ls -l on the file, i can confirm there is data in the file.

Any progress?
@florianschroen
florianschroen commented Mar 30, 2021

If someone has runs into the same problem...

I had a problem with "umlauts" (äüöß...) while exporting a latin1 mysql database and converting to sqlite.

In the mysqldump, all umlauts were displayed as ? or combinations like <fe>.

The cause was awk, which could be fixed by converting the mysql dump from latin1 to utf8 before parsing.
Found the solution here

mysqldump  --compatible=ansi --skip-extended-insert --compact  "$@" | \
iconv -c -f latin1 -t utf8 | \
awk '
[...]

to join this conversation on GitHub. Already have an account? Sign in to comment
Footer
© 2024 GitHub, Inc.
Footer navigation

    Terms
    Privacy
    Security
    Status
    Docs
    Contact


