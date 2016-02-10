Required:

php55, redis, phpunit, phpredis, phalcon framework

Instruction to install phalcon framework:
https://docs.phalconphp.com/en/latest/reference/install.html

All other stuff will be installed with "composer install --dev"

Configuration file path is app/config/config.php

To generate data run "php ./app/commands/generateDb.php"

Routes:

  POST /users — create user with name from $_POST['name']
  GET /users/{user_id}/friends — list of friends
  GET /users/{user_id}/friends-requests — list of friends requests
  GET /users/{user_id}/friends-tree — list of friends requests
  PUT /users/{user_id}/friends/{friend_id]/?accept=true|false
  POST /users/{user_id}/friends/?user_id = {friendId}

Comments

At first for storing users and relations was used Redis, but on big amount of relations(10k users and 100k relations)
it's started to be a problem to get friends of friends on N depth. I Decided to try graph database neo4j to compare,
but after implementing it, i figured out that neo4j works slower than redis on the same data (meybe the problem
in envirement?)
