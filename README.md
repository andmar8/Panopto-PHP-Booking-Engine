Panopto-PHP-Booking-Engine
==========================

This is the Lecture Capture self service booking engine for staff at Newcastle University. It links our timetabling system (Scientia's Syllabus+), Panopto and our Shibboleth enabled PHP server together so that a member of staff may login, browse the upcoming timetable activities (that occur in lecture capture enabled locations, or "ReCAP"'ed locations to use the parlance here at Newcastle University), select which activities that want captured and submit, they can then review and delete individual scheduled recordings in the "bookings" page.

This code was originally backed by a Lectopia system but was re-written for Panopto.

There are a couple of known "nuances/deficiencies" to the code which will be addresses in later releases but our overall goal was to mirror our existing functionality that was backed by Lectopia.

Known nuances/deficiencies
==========================

This list is not exhaustive

* Booking an activity schedules a set of recordings, however Panopto does not have an awareness of the "schedule" after the recordings have been created, so deleting any 1 recording will result in the activities page still showing that activity as being entirely scheduled to be recorded, we are working on a next iteration of the code that will display "Full/Partial" bookings.
* The bookings page shows all recordings scheduled from now onwards, once recorded they will no longer appear.

What are external Id's?
=======================

Throughout this code and the client code we have made extensive use of "external Id's", these are Id's that link Panopto's awareness of a given entity to an external system's. This was a piece of functionality we insisted Panopto add as it is vital in a web service style solution to decouple how you refer to an entity.

"Huh????" I hear you say!...

Let me explain with an example, we have many systems that must act as one complete end to end system, timetabling provides the events occuring in lecture capture enabled venues, Panopto records them, Blackboard (et al) displays them:

* In our timetable system we have locations, activities and users
* In our lecture theatres we have remote recorders to capture lectures
* In our Blackboard system we have courses, organisations (a.k.a. communities) and more users.
* In our Panopto system we have folders, yet more users(!), remote recorders, sessions and recordings

That's a whole heck of a lot of concepts/entities/things, but many are the same thing and "need" to be referred to as the same thing so different systems know, for example, that "AMARTINBLDG-ROOM101" in timetabling or blackboard or panopto or any other on campus system is lecture theatre in the esteemed "Andrew Martin Lecture Theatre", taking the above list and concetrating on the entities first not the systems it would look like this...

* Users: Blackboard/Panopto/Timetabling
* Locations: Panopto (Where the Remote Recorder is, denoted by naming)/Timetabling
* Activities to capture: Panopto (Recordings/Sessions)/Timetabling
* Pedagogic grouping: Blackboard (Course/Organisation)/Panopto (Folders)/Timetabling (Module/Programme/Academic Year/Stage)

We then agreed upon and used one Id for each entity in the form of...

* Users: <auth>\<username> e.g. campus\ntestid, or, cas\nmedicshooltestid
* Locations: This uses our timetable system's "host key" for a location which is basically a primary key, e.g. ARMB.1.01
* Activities to capture: Again, we used our timetable system's "host key" for an activity, e.g. #SPLUS-1269467316439437
* Pedagogic grouping: Theoretically we used our SAP (ultimately our [SIS](http://en.wikipedia.org/wiki/Student_information_system]) ) system, in practice this data is fed from blackboard (but blackboard is fed from the SIS), our blackboard instance also has an academic year identifier appended e.g. Q1213-COM1001

Once you have this and added them as external id's in the panopto system, any one system can refer to the same entity in another system using the external Id, so for example, panopto's folder Q1213-COM1001 is the same as blackboard's Q1213-COM1001 course and the same as the timetable's COM1001 module (the timetable is updated every year so prefix's are not needed).

How to use the code
===================

Several things are required...

* Shib protected PHP server
* Scientia Timetabling system with reporting engine, OR, you will need to write an interconnect to your own timetabling system to pass the activities as XML
* Panopto system
* Panopto-PHP-Client

Then...

#Place this and the client code in the same directory on your PHP server
#Point the timetabling client at your timetable's XML feed
#Point the php client code at your Panopto instance, this can be specified in the Booking Engine code

It's highly likely for your institution you're going to have to review the academic year prefixing and the client for the timetabling system, but most of the heavy lifting is done for you by this code. You can review what format the expected incoming XML takes by looking at the objects in /includes/clients/scientia/entities/LocationActivitySchedule and /includes/clients/scientia/entities/module, one object is the xml, the other is encapsulating an array of that object (e.g. module->modules)