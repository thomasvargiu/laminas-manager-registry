# Doctrine Manager Registry for Laminas apps

## Install

```
$ composer require thomasvargiu/laminas-manager-registry
```

## Configuration

```php
use TMV\Laminas\ManagerRegistry\EntityRepositoryFactory;
use TMV\Laminas\ManagerRegistry\ManagerRegistryConfigFactory;

return [
    ManagerRegistryConfigFactory::class => [
        'connections' => [
            'connection1' => 'doctrine.connection.orm_default',
            'connection2' => 'doctrine.connection.orm_another',
        ],
        'managers' => [
            'manager1' => 'doctrine.entitymanager.orm_default',
            'manager2' => 'doctrine.entitymanager.orm_another',
        ],
        'default_connection' => 'connection1', // optional, default to first connection
        'default_manager' => 'manager1', // optional, default to first manager
    ],
    'dependencies' => [
        'factories' => [
            // Create an EntityRepository service with a ManagerRegistry (see below)
            MyEntityRepository::class => new EntityRepositoryFactory(MyEntity::class),
        ],
    ],
];
```

## Example on how to use it:

```php
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/** @var ManagerRegistry $managerRegistry */
$managerRegistry = $container->get(ManagerRegistry::class);
/** @var EntityManagerInterface $entityManager */
$entityManager = $managerRegistry->getManager(/* <optional manager name> */);

if (! $entityManager->isOpen()) {
    /** @var EntityManagerInterface $entityManager */
    $entityManager = $managerRegistry->resetManager(/* <optional manager name> */);
}
```

## Repository

If you want to inject repositories in your services, you can extend the `TMV\Laminas\ManagerRegistry\EntityRepository`:


```php
use TMV\Laminas\ManagerRegistry\EntityRepository;

class MyRepository extends EntityRepository
{
    public function findByMyCriteria(string $value)
    {
        return $this->findBy(['my_criteria' => $value]) ;   
    }
    
    public function anotherMethod(): void
    {
        // get the doctrine EntityRepository, using the ManagerRegistry
        $doctrineRepository = $this->getRepository();
        // get the EntityManager, using the ManagerRegistry
        $entityManager = $this->getEntityManager();
    }
}
```

To instantiate your repository you can register a service using the serializable 
`TMV\Laminas\ManagerRegistry\EntityRepositoryFactory` factory:

```php
use TMV\Laminas\ManagerRegistry\EntityRepositoryFactory;
use TMV\Laminas\ManagerRegistry\ManagerRegistryConfigFactory;

return [
    'dependencies' => [
        'factories' => [
            MyRepository::class => new EntityRepositoryFactory(MyEntity::class /*, <optional manager name> */),
        ],
    ],
];
```
