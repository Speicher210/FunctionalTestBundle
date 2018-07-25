<?php

declare(strict_types=1);

namespace Speicher210\FunctionalTestBundle\Test\Loader;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

/**
 * Abstract fixture loader.
 */
abstract class AbstractLoader extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var ObjectManager */
    private $manager;

    /**
     * Get the container.
     */
    protected function getContainer() : ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * Code to run before loading the fixtures.
     */
    protected function beforeLoad() : void
    {
    }

    public function load(ObjectManager $manager) : void
    {
        $this->manager = $manager;

        $this->beforeLoad();
        $this->doLoad();
        $this->afterLoad();
    }

    /**
     * Code to run after loading the fixtures.
     */
    protected function afterLoad() : void
    {
        $this->manager->flush();
        $this->manager->clear();
    }

    /**
     * Load data fixtures.
     */
    abstract protected function doLoad() : void;

    public function getManager() : ObjectManager
    {
        return $this->manager;
    }

    /**
     * Remove the ACL for a resource.
     *
     * @param mixed $resource The resource for which to remove the ACL.
     */
    protected function removeResourceAcl($resource) : void
    {
        $this->manager->flush();

        /** @var ObjectIdentityRetrievalStrategyInterface $objectIdentityRetrievalStrategy */
        $objectIdentityRetrievalStrategy = $this->container->get('security.acl.object_identity_retrieval_strategy');

        $objectIdentity = $objectIdentityRetrievalStrategy->getObjectIdentity($resource);
        /** @var MutableAclProviderInterface $aclProvider */
        $aclProvider = $this->container->get('security.acl.provider');
        $aclProvider->deleteAcl($objectIdentity);

        $this->manager->flush();
    }
}
