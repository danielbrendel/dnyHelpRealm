# HelpRealm (dnyHelpRealm) developed by Daniel Brendel

(C) 2019 - 2020 by Daniel Brendel

**Version**: 0.1\
**Contact**: dbrendel1988(at)gmail(dot)com\
**GitHub**: https://github.com/danielbrendel

Released under the MIT license

## Description:
HelpRealm is a lightweight SaaS service support system for customers of entities. Customers can create support requests 
via a personal workspace contact form, specifying text content and attachments. For each support request there is 
a ticket created which is then handled by a registered agent. Tickets can be routed into different groups where initial 
tickets are routed to a defined index group. Superadmins can manage agents, groups, FAQ and system settings. Customers and
agents get notified about ticket updates by e-mail. The support system is especially suitable for freelancers and small teams. 
The system is specifically suited for freelancers and small teams.

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
+ Friendly installer
+ Security (Protection against XSS, SQL Injection, CSRF, Spam)
+ Responsive layout
+ SaaS solution
+ Documentation

## Documentation
The documentation is located in the /doc directory. A generated PDF needs to be put to /public/data/documentation.pdf

## System requirements
The product is beeing developed with the following engine versions:
+ PHP 7.4.1 
+ MySQL 10.4.11-MariaDB
+ Default PHP extensions

## Testing
Before running tests the .env.testing must be adjusted to match the test data of the database.
Therefore the DATA_* environment variables must be adjusted. Then open the command prompt, go 
to the project root and run PHPUnit.

## Changelog:
+ Version 0.1:
	- (Initial release)