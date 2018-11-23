<?php

declare(strict_types=1);

namespace Kaby\Component\Dci;

use ReflectionMethod;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractContext
{
    /**
     * Add role to specific data
     *
     * @param mixed        $data
     * @param AbstractRole $role
     *
     * @return BoundedRoleInterface|mixed
     */
    final protected function addRole($data, AbstractRole $role)
    {
        return new class($role, $data) implements BoundedRoleInterface
        {
            use DelegatorTrait;

            /**
             * Constructor.
             *
             * @param AbstractRole $role
             * @param              $data
             */
            public function __construct(AbstractRole $role, $data)
            {
                $method = new ReflectionMethod(get_class($role), 'attach');
                $method->setAccessible(true);
                $method->invoke($role, $data);

                $this->delegated = $role;
            }

            /**
             * {@inheritdoc}
             */
            public function extract()
            {
                $method = new ReflectionMethod(get_class($this->delegated), 'extract');
                $method->setAccessible(true);

                return $method->invoke($this->delegated);
            }
        };
    }

    /**
     * Add multiple roles to data
     *
     * @param mixed          $data
     * @param AbstractRole[] $roles
     *
     * @return BoundedRoleInterface|mixed
     */
    final protected function addRoles($data, array $roles)
    {
        foreach ($roles as $role) {
            $data = $this->addRole($data, $role);
        }

        return $data;
    }
}