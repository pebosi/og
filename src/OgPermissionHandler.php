<?php

/**
 * @file
 * Contains \Drupal\og\OgPermissionHandler.
 */

namespace Drupal\og;

use Drupal\Component\Discovery\YamlDiscovery;
use Drupal\user\PermissionHandler;
use Drupal\user\PermissionHandlerInterface;

/**
 * Provides permissions for groups based on YNL files.
 *
 * The permissions file should be constructed by the next format(with comments):
 * @code
 * # The key is the permission machine name, and is required.
 * update group:
 *   # (required) Human readable name of the permission used in the UI.
 *   title: 'Edit group'
 *   # (optional) Additional description fo the permission used in the UI.
 *   description: 'Edit the group. Note: This permission controls only node entity type groups.'
 *   # (optional) Boolean, when set to true a warning about site security will
 *   # be displayed on the Permissions page. Defaults to false.
 *   restrict access: false
 *   # Determine to which roles the permissions will be enabled by default.
 *   'default role':
 *     - OG_AUTHENTICATED_ROLE
 *   # Determine to which role to limit the permission. For example the
 *   # "subscribe" can be assigned only to a non-member, as a member doesn't
 *   # need it
 *   'roles':
 *     - OG_ANONYMOUS_ROLE
 * @endcode
 *
 * @see \Drupal\user\PermissionHandler
 */
class OgPermissionHandler extends PermissionHandler {

  /**
   * {@inheritdoc}
   */
  protected function getYamlDiscovery() {
    if (!isset($this->yamlDiscovery)) {
      $this->yamlDiscovery = new YamlDiscovery('og_permissions', $this->moduleHandler->getModuleDirectories());
    }
    return $this->yamlDiscovery;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildPermissionsYaml() {
    $permissions = parent::buildPermissionsYaml();

    foreach ($permissions as &$permission) {
      // Add default values.
      $permission += [
        'role' => [OG_ANONYMOUS_ROLE, OG_AUTHENTICATED_ROLE],
        'default role' => [],
      ];

      $permission['role'] = $this->parseRoles($permission['role']);
      $permission['default role'] = $this->parseRoles($permission['default role']);
    }

    return $permissions;
  }

  /**
   * Convert the roles special name, into the actual string value.
   *
   * @param array $roles
   *   Array with roles name.
   *
   * @return array
   *   The parsed array with the roles names.
   */
  protected function parseRoles(array $roles = array()) {
    $parsed = [];
    foreach ($roles as $role) {
      if ($role === 'OG_ANONYMOUS_ROLE') {
        $parsed[] = OG_ANONYMOUS_ROLE;
      }
      elseif ($role === 'OG_AUTHENTICATED_ROLE') {
        $parsed[] = OG_AUTHENTICATED_ROLE;

      }
      elseif ($role === 'OG_ADMINISTRATOR_ROLE') {
        $parsed[] = OG_ADMINISTRATOR_ROLE;
      }
      else {
        $parsed[] = $role;
      }
    }

    return $parsed;
  }

}
