# Upgrade

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
