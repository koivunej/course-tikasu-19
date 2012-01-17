course-tikasu-19
================

this is repo for our work for [TUT] [1] course [OHJ-3321] [2] designing databases.

i guess all code we push is GPL however you are restricted from using it if you are on the same course implementation.

 [1]: http://www.tut.fi
 [2]: http://www.cs.tut.fi/~tikasu/

setup
=====

 1. signup for github
 2. tell joonas your github username 
 3. follow github docs on setting up your ssh keys
 4. clone this repo
 5. push something, anything, like your name below:
   * joonas

structuring
===========

./*/Makefile
----------

i think we should put all our "common tasks" into Makefiles, like: 
 * setting up the database: 
   * starting it
   * dropping everything
   * cleaning everything
   * shutting it down 
   * perhaps a status query?
 * drop-all.sql creation
 * pushing php app to server

./database
----------

lets put our solids sql et al here.

./app
----------

lets put our php app here.
