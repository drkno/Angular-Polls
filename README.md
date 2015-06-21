# Angular-Polls
SENG365 AngularJS Polls App (21/20 Marks, A+/100%). A web application using AngularJS and CodeIgniter to administer polls (as in voting). Polls can be created, modified, voted on and deleted.

## Installing
1. Make sure you have an appropriate version of PHP installed and a webserver.
2. Copy the files to the web server.
3. Create 3 tables:
  - polls(id:int|pk|ai, title:varchar255, question:varchar255)
  - votes(id:int|pk|ai, poll:int|fk, answer:int, ip:varchar50)
  - answers(id:int|pk|ai, poll:int|fk, answer:varchar255)
4. Setup appropriate database settings in database.php

## License
All code origional to this project is licensed under the MIT license.
