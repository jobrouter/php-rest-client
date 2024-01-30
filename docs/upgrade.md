# Upgrade

## From version 2.0 to 3.0

The namespace of the JobRouter REST Client classes have changed from

```
\Brotkrueml\JobRouterClient
```

to

```
\JobRouter\AddOn\RestClient
```

The easiest way to update your code to the new namespace is to use search/replace
in your project.

The package name (used in `composer.json`) has changed from `brotkrueml/jobrouter-client`
to `jobrouter/rest-client`.

## From version 1.x to 2.0

With JobRouter REST Client 2.0 the minimum requirements have changed, supported are
now:

- PHP ≥ 8.1
- JobRouter® ≥ 2022.1

### REST client

On instantiation of the `RestClient` class, no authentication is performed automatically
anymore. Call the `authenticate()` method manually now before sending a request to the
REST API.

The `authenticate()` method returns an instance to the class itself.
This way, one can use a fluent interface:

```php
$restClient = (new RestClient($configuration))->authenticate();

// or:

(new RestClient($configuration))
  ->authenticate()
  ->request($method, $resource);
```

### API changes

- `Incident` class:

  - On instantiation the step number must be passed as argument in the constructor.
  - The `getStep()` method now returns always an integer, previously it
    was an integer or null.
  - The `getPool()` method now returns always an integer, previously it
    was an integer or null.
  - The `isSimulation()` method now returns always a boolean, previously it
    was a boolean or null.
  - The `setPriority()` method accepts only a `Priority` enum, previously it
    was an integer or null.
  - The `getPriority()` method returns a `Priority` enum, previously it was
    an integer or null.
