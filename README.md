# HelpRealm (dnyHelpRealm) developed by Daniel Brendel

(C) 2019 - 2020 by Daniel Brendel

**Version**: 1.0\
**Contact**: dbrendel1988(at)gmail(dot)com\
**GitHub**: https://github.com/danielbrendel

Released under the MIT license

## Description:
HelpRealm is a lightweight SaaS service support system for customers of entities. Customers can create support requests 
via a personal workspace contact form, specifying text content and attachments. For each support request there is 
a ticket created which is then handled by a registered agent. Tickets can be routed into different groups where initial 
tickets are routed to a defined index group. Superadmins can manage agents, groups, FAQ and system settings. Customers and
agents get notified about ticket events by e-mail. Communication is possible via e-mail or a secret ticket thread form. 
The support system is especially suitable for freelancers and small teams. The system is specifically suited for freelancers
and small teams.

## Feature overview:
+ Tickets
	- Create tickets
	- List tickets
	- Manage tickets
	- Different types and priorities
	- Different ticket status
	- Ticket notes
	- Ticket attachments
+ Groups
	- Manage groups
	- Route tickets through groups
+ Agents
	- Manage agents
	- Manage superadmins
+ Customer infos
+ Dashboard
+ FAQ
+ Multi-language
+ Login management
+ Gravatar support
+ E-Mail notifications
+ E-Mail replies
+ Friendly installer
+ Security (Protection against XSS, SQL Injection, CSRF, Spam)
+ Responsive layout
+ SaaS solution
+ API Endpoint
+ Endpoint for client apps
+ Ajax requests
+ Documentation

## Documentation
The documentation is located in the /doc directory. A generated PDF needs to be put to /public/data/documentation.pdf

## System requirements
The product is being developed with the following engine versions:
+ PHP 7.4.1 
+ MySQL 10.4.11-MariaDB
+ Default PHP extensions

## Testing
Before running tests the .env.testing must be adjusted to match the test data of the database.
Therefore the DATA_* environment variables must be adjusted. Then open the command prompt, go 
to the project root and run PHPUnit. The following variables must be adjusted:
+ DATA_USERID: ID of a test user
+ DATA_WORKSPACE: ID of a test workspace
+ DATA_WORKSPACENAME: Namehash of that workspace
+ DATA_GROUPID: ID of a test group
+ DATA_GROUPNAME: Name of the test group
+ DATA_TICKETID: Ticket ID of a test ticket
+ DATA_TICKETHASH: Ticket hash of that test ticket
+ DATA_TICKETSUBJECT: Ticket subject of that test ticket
+ DATA_TICKETTEXT: Ticket text of that test ticket
+ DATA_TICKETFILE: ID of a ticket file
+ DATA_USEREMAIL: E-Mail address of a test user
+ DATA_USERPW: Password of that test user
+ DATA_FAQID: ID of a workspace FAQ item
+ DATA_INIFILESIZE: Byte size of upload_max_filesize in php.ini

## Mailservice
Agents and customers can post to a ticket thread by replying to the notification emails.
In order for this to work the environment variables MAILSERV_* must be set. Also a cronjob
must be activated on the server system which calls /mailservice/{password} (any request type).
The password must match the one specified in the MAILSERV_CRONPW variable.

## Twitter news
By setting the TWITTER_* environment variables to the news account it will fetch tweets from the 
Twitter timeline.
