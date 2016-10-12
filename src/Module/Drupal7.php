<?php

namespace Codeception\Module;

use Codeception\Lib\Framework;
use Codeception\TestInterface;
use Drupal\Driver\DriverInterface;
use Drupal\Driver\DrupalDriver;
use Codeception\Lib\Connector\Drupal7 as Drupal7Connector;

/**
 * Class Drupal7
 * @package Codeception\Module
 */
class Drupal7 extends Framework implements DriverInterface
{

    protected $requiredFields = ['uri'];

    protected $config = [
        'cleanup' => true,
        'path' => '',
    ];

    /**
     * @var DrupalDriver
     */
    protected $driver;

    /**
     * @var DatabaseTransaction
     */
    protected $dbTransaction;

    /**
     * @inheritdoc
     */
    public function _requires()
    {
        return ['Drupal\Driver' => '"drupal/drupal-driver": "~1.0"'];
    }

    public function _initialize()
    {
        $this->driver = new DrupalDriver($this->config['path'], $this->config['uri']);
        $this->driver->setCoreFromVersion();
    }

    /**
     * @param TestInterface $test
     */
    public function _before(TestInterface $test)
    {
        $this->client = new Drupal7Connector();

        $this->driver->bootstrap();

        if ($this->config['cleanup']) {
            $this->dbTransaction = Database::getConnection()->startTransaction();
        }
    }

    /**
     * @param TestInterface $test
     */
    public function _after(TestInterface $test)
    {
        if ($this->config['cleanup']) {
            unset($this->dbTransaction);
        }
        parent::_after($test);
    }

    /**
     * Returns a random generator.
     */
    public function getRandom()
    {
        return $this->driver->getRandom();
    }

    /**
     * Bootstraps operations, as needed.
     */
    public function bootstrap()
    {
        return $this->driver->bootstrap();
    }

    /**
     * Determines if the driver has been bootstrapped.
     */
    public function isBootstrapped()
    {
        return $this->driver->isBootstrapped();
    }

    /**
     * Creates a user.
     * @param \stdClass $user
     */
    public function userCreate(\stdClass $user)
    {
        return $this->driver->userCreate($user);
    }

    /**
     * Deletes a user.
     * @param \stdClass $user
     */
    public function userDelete(\stdClass $user)
    {
        return $this->driver->userDelete($user);
    }

    /**
     * Processes a batch of actions.
     */
    public function processBatch()
    {
        return $this->driver->processBatch();
    }

    /**
     * Adds a role for a user.
     *
     * @param \stdClass $user
     *   A user object.
     * @param string $role
     *   The role name to assign.
     */
    public function userAddRole(\stdClass $user, $role)
    {
        return $this->driver->userAddRole($user, $role);
    }

    /**
     * Retrieves watchdog entries.
     *
     * @param int $count
     *   Number of entries to retrieve.
     * @param string $type
     *   Filter by watchdog type.
     * @param string $severity
     *   Filter by watchdog severity level.
     *
     * @return string
     *   Watchdog output.
     */
    public function fetchWatchdog($count = 10, $type = null, $severity = null)
    {
        return $this->driver->fetchWatchdog();
    }

    /**
     * Clears Drupal caches.
     *
     * @param string $type
     *   Type of cache to clear defaults to all.
     */
    public function clearCache($type = null)
    {
        return $this->driver->clearCache();
    }

    /**
     * Clears static Drupal caches.
     */
    public function clearStaticCaches()
    {
        return $this->driver->clearStaticCaches();
    }

    /**
     * Creates a node.
     *
     * @param object $node
     *   Fully loaded node object.
     *
     * @return object
     *   The node object including the node ID in the case of new nodes.
     */
    public function createNode($node)
    {
        return $this->driver->createNode($node);
    }

    /**
     * Deletes a node.
     *
     * @param object $node
     *   Fully loaded node object.
     */
    public function nodeDelete($node)
    {
        return $this->driver->nodeDelete($node);
    }

    /**
     * Runs cron.
     */
    public function runCron()
    {
        return $this->driver->runCron();
    }

    /**
     * Creates a taxonomy term.
     *
     * @param \stdClass $term
     *   Term object.
     *
     * @return object
     *   The term object including the term ID in the case of new terms.
     */
    public function createTerm(\stdClass $term)
    {
        return $this->driver->createTerm($term);
    }

    /**
     * Deletes a taxonomy term.
     *
     * @param \stdClass $term
     *   Term object to delete.
     *
     * @return bool
     *   Status constant indicating deletion.
     */
    public function termDelete(\stdClass $term)
    {
        return $this->driver->termDelete($term);
    }

    /**
     * Creates a role.
     *
     * @param array $permissions
     *   An array of permissions to create the role with.
     *
     * @return string
     *   Role name of newly created role.
     */
    public function roleCreate(array $permissions)
    {
        return $this->driver->roleCreate($permissions);
    }

    /**
     * Deletes a role.
     *
     * @param string $rid
     *   A role name to delete.
     */
    public function roleDelete($rid)
    {
        return $this->driver->roleDelete($rid);
    }

    /**
     * Check if the specified field is an actual Drupal field.
     *
     * @param string $entity_type
     *   The entity type to which the field should belong.
     * @param string $field_name
     *   The name of the field.
     *
     * @return bool
     *   TRUE if the field exists in the entity type, FALSE if not.
     */
    public function isField($entity_type, $field_name)
    {
        return $this->driver->isField($entity_type, $field_name);
    }

    /**
     * Returns a configuration item.
     *
     * @param string $name
     *   The name of the configuration object to retrieve.
     * @param string $key
     *   A string that maps to a key within the configuration data.
     *
     * @return mixed
     *   The data that was requested.
     */
    public function configGet($name, $key)
    {
        return $this->driver->configGet($name, $key);
    }

    /**
     * Sets a value in a configuration object.
     *
     * @param string $name
     *   The name of the configuration object.
     * @param string $key
     *   Identifier to store value in configuration.
     * @param mixed $value
     *   Value to associate with identifier.
     */
    public function configSet($name, $key, $value)
    {
        return $this->driver->configSet($name, $key, $value);
    }
}
