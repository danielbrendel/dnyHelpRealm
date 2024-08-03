<h1 align="center">
    <img src="public/gfx/logo.png" width="100"/><br/>
    HelpRealm
</h1>

<p align="center">
    The free SaaS support ticket system<br/>
    (C) 2019 - 2024 by Daniel Brendel<br/>
    Released under the MIT license
</p>

## Information

**Version**: 1.0\
**Codename**: dnyHelpRealm\
**Contact**: dbrendel1988(at)gmail(dot)com\
**GitHub**: https://github.com/danielbrendel

## Description:
HelpRealm is a lightweight SaaS service support system for customers of entities. Customers can create support requests 
via a personal workspace contact form, specifying text content and attachments, or via E-Mail. For each support request 
there is a ticket created which is then handled by a registered agent. Tickets can be routed into different groups where 
initial tickets are routed to a defined index group. Superadmins can manage agents, groups, FAQ and system settings. 
Customers and agents get notified about ticket events by e-mail. Communication is possible via e-mail or a secret ticket 
thread form. There is also the possibility to create tickets via API. The support system is especially suitable for 
freelancers and small teams.

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
+ E-Mail tickets
+ Friendly installer
+ Security (Protection against XSS, SQL Injection, CSRF, Spam)
+ Responsive layout
+ SaaS solution
+ API (REST / Widget)
+ Endpoint for client apps
+ Stripe payment
+ Ajax requests

## Documentation
A documentation resource can be linked to by setting the APP_DOCUMENTATION_LINK environment variable.

## System requirements
The product is being developed with the following engine versions:
+ PHP ^8.2
+ MySQL 10.4.27-MariaDB
+ Default PHP extensions
+ E-Mail Server

## Testing
Before running tests the .env.testing must be adjusted to match the test data of the database.
Therefore the DATA_* environment variables must be adjusted. Then open the command prompt, go 
to the project root and run PHPUnit. The following variables must be adjusted:
+ DATA_USERID: ID of a test user
+ DATA_WORKSPACE: ID of a test workspace
+ DATA_WORKSPACENAME: Namehash of that workspace
+ DATA_WORKSPACEAPITOKEN: API token for the API endpoint
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
+ DATA_TICKETTYPEEXISTING: ID of an existing ticket type
+ DATA_TICKETTYPEEXISTINGNAME: Name of the existing ticket type
+ DATA_TICKETTYPENONEXISTING: ID of a non-existing ticket type

## Mailservice
Agents and customers can post to a ticket thread by replying to the notification emails. For this to work the
mailservice must be configured.
Using a mailservice can either happen by using the systems own mailservice or the users custom mailservice. 
Therefore the related mailservice settings must be configurated in the .env file. SMTP is for sending e-mails and IMAP
for retrieving e-mails from an inbox. When users choose their own mailservice then the mailservice will try to connect
to their SMTP and IMAP hosts to send and process e-mails. There are two cronjobs available for dealing with
mail inboxes:
1. /mailservice/self/{password}
	This one is used to process the mail inbox of the system
2. /mailservice/custom/{password}
	This one is used to process the mail inboxes of workspaces that use a custom mailservice

To protect the cronjob routes from public access, the environment variable MAILSERV_CRONPW must be set to a secure token.

