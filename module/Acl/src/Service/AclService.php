<?php

namespace Acl\Service;

use Acl\Entity\Resource;
use Acl\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

class AclService
{
    /**
     * @var Acl
     */
    private $acl;

    /**
     * @var EntityRepository
     */
    private $roleRepository;

    /**
     * @var EntityRepository
     */
    private $resourceRepository;

    public function __construct(
        EntityRepository $roleRepository,
        EntityRepository $resourceRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * @return Acl
     */
    public function getAcl()
    {
        if ($this->acl === null) {
            $this->acl = $this->loadAcl();
        }

        return $this->acl;
    }

    /**
     * @return Acl
     */
    private function loadAcl()
    {
        /** @var Acl $acl */
        $acl = new Acl();
        $roles = $this->roleRepository->findAll();
        $resources = $this->resourceRepository->findAll();

        /** @var Role $role */
        foreach ($roles as $role) {
            $acl->addRole(new GenericRole($role->getName()));
        }

        /** @var Resource $resource */
        foreach ($resources as $resource) {
            $acl->addResource(new GenericResource($resource->getName()));
        }

        /** @var Role $role */
        foreach ($roles as $role) {
            /** @var Resource $resource */
            foreach ($role->getResources() as $resource) {
                $acl->allow($role->getName(), $resource->getName());
            }
        }

        return $acl;
    }
}
