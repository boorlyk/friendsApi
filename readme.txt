Required:

php55, redis, phpunit, phpredis, phalcon framework

How to install phalcon framework:
https://docs.phalconphp.com/en/latest/reference/install.html

To install the rest of stuff run "composer install --dev"

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

I started with redis to store users and relations. While testing big numbers of relations (10k users and 100k relations)
it took quite long to get friends of friends on N depth. I decided to try graph database neo4j to compare the measures.
After implementing, I figured out that neo4j works slower (~4 times) than redis on the same data (the issue may be related
to the environment).
