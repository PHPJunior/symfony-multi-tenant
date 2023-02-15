# Symfony Multi-tenant Example

This is a simple example of a multi-tenant application using Symfony 6.2

## Installation

1. Clone the repository
2. Run `composer install`
3. Update hostname in `.env`
4. Run `bin/console doctrine:database:create`
5. Run `bin/console doctrine:migrations:migrate`

## Create a new tenant

1. Run `bin/console app:tenant-create` to create a new tenant
2. Run `bin/console app:tenant-migrate {tenantId}` to run migrations for a tenant
3. Run `bin/console app:tenant-create-user {tenantId}` to create a new user for a tenant
4. Run `bin/console app:tenant-maintenance {tenantId} {mode}` to enable or disable maintenance mode for a tenant


## Todo

- [ ] Add tenant events
- [ ] Add tests
