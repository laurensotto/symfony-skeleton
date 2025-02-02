> **Disclaimer** - This application was built as a fun project to jump start my own hobby projects and learn more about Symfony. Keep in mind that the choices I made are opinionated and might not be suited to your needs.
> If there is anything you'd like to add or change, feel free to open an issue or pull request. The same goes if you discover any bugs.

## Introduction

This Symfony project is meant to be used as a starting point when building a Symfony backend. It comes equipped with the following features:

- A local development environment using Docker, NGINX and PostgreSQL
- PHPStan at level 9
- PHP_CodeSniffer ([with custom rules](https://github.com/laurensotto/symfony-skeleton/blob/main/phpcs.xml.dist))
- Unit tests with 100% code coverage (except for repositories)
- A prebuilt user implementation using [lexik/jwt-authentication-bundle](https://github.com/lexik/LexikJWTAuthenticationBundle) that offers
  - [UserProvider functionality](https://github.com/laurensotto/symfony-skeleton/blob/main/src/Security/UserProvider.php)
  - Role based authentication
  - Fixtures
  - Preconfigured OpenAPI spec for
    - Authentication
    - User actions
- [Automatic request validation to response conversion](https://github.com/laurensotto/symfony-skeleton/blob/main/src/EventListener/ValidationExceptionListener.php)

## Setting up
The local development environment for this application can be started by running `docker compose up`. For more information on Docker Compose, please refer to [the documentation](https://docs.docker.com/compose/) offered by Docker.

After starting the application, these are some handy commands to get you started:
- `docker exec -it symfony-skeleton-php-1 ash` to attach to the running PHP container. Please note that your container name could differ from this one.
- `php bin/console lexik:jwt:generate-keypair` to configure your JWT keypair. This is a required step before users are able to authenticate.
- `php bin/console doctrine:migrations:migrate` to setup your database with the pre-generated migration files.
- `php bin/console doctrine:fixtures:load ` to insert [pre-written test data](https://github.com/laurensotto/symfony-skeleton/tree/main/src/DataFixtures) into your database.

The web application will bind itself to http://localhost:8084 by default. With the auto-generated OpenAPI documentation available at http://localhost:8084/api/doc or http://localhost:8084/api/doc.json. The database is available at localhost:8432 and the credentials can be found in the [.env](https://github.com/laurensotto/symfony-skeleton/blob/main/.env) or [docker-compose.yml](https://github.com/laurensotto/symfony-skeleton/blob/main/docker-compose.yml). 
## Decisions
### Serialization and Request mapping
Preparing objects to serialize is a manual job in this application. After trying out JMS and Symfony's own serializer accompanied with their concept of serializer groups, I came to the conclusion that these solutions lack 2 major things:
- No control over names given to generated OpenAPI models
- Ever-growing clutter in your Entity classes, something that is a pain to keep track of for me

I found that writing request and response models not only gives me the headspace to see what is going on in my code, but it also made sure that I was more aware of which data would be processed by which user type. To avoid situations where normal users could change passwords for others, or even their own roles. Lastly it brought me to a point where I was able to have my requests pre-mapped and validated, erasing the need for Symfony Forms and working with the standard Request object in controllers. The latter being the biggest upside, as I was free from checks like these:
```php
public function post(Request $request): JsonResponse
{
    $content = json_decode($request->getContent(), true);
    
    if (!$content['name']) {
        /** Handle situation */
    }
    
    /** continue method */
}
```
Please refer to the [UserController](https://github.com/laurensotto/symfony-skeleton/blob/main/src/Controller/V1/UserController.php), [CreateUserRequest](https://github.com/laurensotto/symfony-skeleton/blob/main/src/Model/Request/User/CreateUserRequest.php) and [ValidationExceptionListener](https://github.com/laurensotto/symfony-skeleton/blob/main/src/EventListener/ValidationExceptionListener.php) for the solution to this issue. Note that the Request model is barebones in terms of validation rules and should be expanded on in real life scenarios.

Now I must also address the downside of favoring manual implementation over libraries like JMS, Symfony's serializer groups and Symfony Forms. Writing response and request models yourself takes up some time, especially when you're just getting into it. Making the use of automagic libraries a really appealing solution: just add a few attributes here and there, write yourself a FormType for that one entity and you're done. In this regard, the manual solution absolutely loses out.

However, after some time using both concepts in my code, I did decide that doing these actions manually is worth the extra effort. Even if we're not taking the 2 major issues I described earlier into account. The automatic request validation before entering the first line of written code, control over how things are actually serialized and the way you're forced to think about who gets to change what, weigh up to the extra few minutes you spend on writing the actual classes. That said, in reality building a FormType takes time as well, especially because you should build a separate one for every type of user you have, giving you the control of what certain users can and can't change. Something I (and likely others) tend to overlook when leaving trivial things such as posting and patching up to automagic. Giving even more points to the manual solution.
 
## Other
#### Alternatives for PostgreSQL
I picked PostgreSQL for this project due to the fact that it has native support for the UUID type. Whilst it is possible to run this application with MySQL, MariaDB or something else, keep in mind that certain features could not work out of the box and might require additional configuration.
