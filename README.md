Auth Redux
==========

A modern drop in replacement for [PEAR/Auth](https://pear.php.net/package/Auth/) that covers most of the original public API. But can also be used separately for any simple authentication needs.

Example usage
-------------

```php
// Optionally globally define the legacy PEAR\Auth constants
// like AUTH_WRONG_LOGIN if they're used in your code.
WBC\Auth\LegacyPEARAuth::defineLegacyConstants();

$redux = new WBC\Auth\Redux(
    // Choose a storage that looks up your users, often a database.
    new WBC\Auth\Storage\PDO($db),
    // Choose a hashing layer, several are available.
    new WBC\Auth\Hashing\Standard(),
    // Choose a session layer to store the currently logged in user.
    new WBC\Auth\Session\Standard()
);

// Finally we just wrap $redux in a compatibility layer
// that emulates PEAR/Auth
$auth = new WBC\Auth\LegacyPEARAuth($redux, 'your_login_function');

// You can now just continue to use your existing code that relies
// on PEAR/Auth
```

More examples are available in the [examples](examples) directory.

Features not implemented
------------------------

| Feature | Status | Reason |
| ------- | ------ | ------ |
| Idle and Expiry timers | Not implemented | Not implemented. |
| `setExpire()`, `setIdle()` and `sessionValidThru()` | Not Implemented | Idle and expiry timers not implemented. |
| "Advanced security" checks | Not Implemented | Not implemented. |
| `setAdvancedSecurity()` | Not Implemented | Advanced security checks not implemented. |
| `log()` and `attachLogObserver()` | Outside scope | Logging is outside of scope for this Authentication class. |
| `removeUser()`, `addUser()` and `listUsers()` | Outside scope | Removing, adding and listing of users is outside of scope for this Authentication class. |
| `setSessionName()` | Deprecated | Not relevant, the Session layer replaces this. |
| `setAuth()` | Deprecated | Can't see a good reason for manually setting logged in username. |
| `deleteAuthData()` | Deprecated | Can't see a good reason for deleting session data. |
| `staticCheckAuth()` | Deprecated | Can't see a good reason to bake-in static access the Auth class |

Differences
-----------

- Calling `getAuthData()` with no arguments returns an array of all the "auth data" available for the current user. However unlike PEAR\Auth, the username field will also be included in this array.

Customisation
-------------

You can write your own Storage, Hashing and Session layer by implementing the respective StorageInterface, HashingInterface and SessionInterface interfaces in your own classes.

License
-------

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
