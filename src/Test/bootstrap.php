<?php

declare(strict_types=1);

$method = new ReflectionMethod(\Speicher210\FunctionalTestBundle\Test\KernelTestCase::class, 'createKernel');
$method->setAccessible(true);

/** @var \Symfony\Component\HttpKernel\KernelInterface $kernel */
$kernel = $method->invoke(null);
$kernel->boot();

/** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

$entityManager->beginTransaction();

$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
$schemaTool->dropDatabase();
/** @var array<int, \Doctrine\ORM\Mapping\ClassMetadata> $metaData */
$metaData = $entityManager->getMetadataFactory()->getAllMetadata();
$schemaTool->createSchema($metaData);

$entityManager->commit();
