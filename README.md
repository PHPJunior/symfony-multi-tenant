# Symfony Multi-tenant Example [WIP]

This is a simple example of a multi-tenant application using Symfony 6.2

## Installation

1. Clone the repository
2. Run `composer install`
3. Add hostname in `services.yaml`
4. Run `bin/console doctrine:database:create`
5. Run `bin/console doctrine:migrations:migrate`

## Create a new tenant

1. Run `bin/console app:create-tenant` to create a new tenant
2. Run `bin/console app:tenant-migrate {tenantId}` to run migrations for a tenant
