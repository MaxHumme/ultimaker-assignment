# Ultimaker Backend Assignment

## Intro
The assignment was not specific on a few subjects, so I had to make some assumptions:
* I decided to go with a RESTful API. It has endpoints for indexing, creating, showing, updating and deleting prints. See
`app/config/routing.yml` and this documentation for the exact urls and methods.
* A print consists of one image, a title and a description.
* Secure authentication was not asked for, so it is done simply through a token sent in the request header. This is
absolutely not secure!! And not meant to be secure.

## Code style
The chosen code style for this assignment is PSR-2.

## Setup
How to get this project up on your machine? It is prepared to work with Docker and be made available on `http://127.0.0.1`.
If you have Docker installed, follow the steps below:
* Decide on a directory to clone this project into, and do the cloning with: `git clone git@github.com:MaxHumme/ultimaker-assignment.git [folder of choice]`
* cd into that folder.
* Build and run docker containers: `docker-compose up -d`
* Install composer dependencies with: `docker run --rm -v $(pwd):/app composer/composer install`
* List running containers with `docker ps` and run the following in the php container (`docker exec -it [php container name] bash`):
* Create the tables: `app/console doctrine:schema:update --force`
* Load a user: `app/console doctrine:fixtures:load`
* Create the tables in the test database: `app/console doctrine:schema:update --force --env=test`
* Load a user in the test database: `app/console doctrine:fixtures:load --env=test`

The site should be accessible now. A user with username `ultimaker` and api-token `my-api-token` was created.
Use Postman or some other API client to fire some request at the app.

## Endpoints

### Index prints
Shows the list of prints for the given user.

#### Request:
* Url: `/api/v1/{username}/prints`
* Method: GET
* Headers:
  * Authorization: `{user's api token}`
  
#### Response:
* status: 200
* content-type: application/json
* content: The print and its details

### Create a print
Creates a print for the authenticated user.

#### Request:
* Url: `/api/v1/{username}/prints/new`
* Method: POST
* Headers:
  * Authorization: `{user's api token}`
* Form-data:
  * title: the title for the new print
  * description: the description for the new print
  * image: the image file to add to the print
  
#### Response:
* status: 201
* content-type: application/json
* content: The newly created print and its details

### Show a print
Shows the asked for print.

#### Request:
* Url: `/api/v1/{username}/prints/{public print id}`
* Method: GET
* Headers:
  * Authorization: `{user's api token}`
  
#### Response:
* status: 200
* content-type: application/json
* content: The prints and their details

### Update a print
Updates the given print with the provided image, title and description.

#### Request:
* Url: `/api/v1/{username}/prints/{public print id}`
* Method: POST
* Headers:
  * Authorization: `{user's api token}`
* Form-data:
  * title: a new title
  * description: a new description
  * image: a new image file
  * _method: put (to simulate a put request)
  
#### Response:
* status: 200
* content-type: application/json
* content: The updated print and its details

### Delete a print
Deletes the given print.

#### Request:
* Url: `/api/v1/{username}/prints/{public print id}`
* Method: DELETE
* Headers:
  * Authorization: `{user's api token}`
  
#### Response:
* status: 200
* content-type: application/json

### Endpoint notes
* The API responds to invalid request input in a consistent way.
* In a real life app I would create separate endpoints for adding, updating and deleting an image instead of just
 uploading it with the final post or update request. It makes for a much better user experience.

## Architecture
I am using a the Onion Architecture and some domain driven design. They work very nicely together. So that means we have
a Domain, which is independent of everything but the classes inside it. It defines some interfaces to be used by the
outer layers. The outer layers are the Infrastructure (database!) and Api layer. Both are wrapped around the Domain
layer and depend on it.

## Testing
I decided to provide acceptance (feature) tests only. They are Behat tests and test the given user story. See the
features in the `features` folder. You can run them by executing `vendor/behat/behat/bin/behat` in the php Docker container.

## Other folders
You'll find most of the code in `src/AppBundle`. Next to the Api, Domain and Infrastructure folders, it has an App,
DataFixtures and Framework folder.
* DataFixtures contains a fixture to load a user with in the database.
* The Framework folder contains two listeners that hook into the Symfony framework.
* The App folder contains a service (JsonComparator) that does not belong in any of the other folders and could best
be described as an App layer service.
