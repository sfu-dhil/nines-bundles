Configuring the Util Bundle
===========================

Configuring the bundle is covered in the main [Nines Bundles](../../README.md)
documentation. This documentation describes the bundle configuration options.

Requirements
------------

This bundle makes use of other Nines Bundles:
* Util Bundle for various bits that do not fit in other bundles

Configuration Options
--------------------

The configuration options are described below

```yaml
# config/packages/nines_user.yaml
nines_user:
    roles:
        - ROLE_ADMIN
        - ROLE_BLOG_ADMIN
        - ROLE_COMMENT_ADMIN
        - ROLE_CONTENT_ADMIN
        - ROLE_DC_ADMIN
        - ROLE_MEDIA_ADMIN
        - ROLE_USER_ADMIN
    after_login_route: homepage
    after_request_route: homepage
    after_reset_route: nines_user_security_login
    after_logout_route: homepage
```

Roles is a list of roles to present to administrative users when managing user
accounts. The list above includes all roles defined in Nines bundles, but you
can add or remove from the list as needed.

The `after_*` options indicate where a user should be redirected after
performing the named action.

> The user bundle will also record the most recent page visit for anonymous
users and redirect them there after a successful login. If the user hasn't
visited a page, they will be redirected to `after_login_route`.

Security Configuration
----------------------

Set the name of the session cookie in the `framework.yaml` file. In this example
it is "NU_SESSION". Only the relevant framework options are included below.

```yaml
# config/packages/framework.yaml
framework:
    secret: '%env(APP_SECRET)%'
    session:
        handler_id: null
        cookie_secure: auto
        cookie_samesite: lax
        name: NU_SESSION
```

The recommended security configuration is included below, including a "remember
me" functionality with a cookie called "NU_REMEMBER_ME".

```yaml
# config/packages/security.yaml
# config/packages/security.yaml
security:
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'

    providers:
        app_user_provider:
            entity:
                class: Nines\UserBundle\Entity\User
                property: email
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt|error)|css|images|js)/
            security: false
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: nines_user_security_login
                check_path: nines_user_security_login
                post_only: true
                form_only: true
                enable_csrf: true
                username_parameter: email
                password_parameter: password
            user_checker: Nines\UserBundle\Security\UserChecker
            logout:
                path: nines_user_security_logout
                target: homepage
            remember_me:
                secret: '%kernel.secret%'
                lifetime: 604800 # 1 week
                path: '%router.request_context.base_url%'
                domain: '%router.request_context.host%'
                name: NU_REMEMBER_ME
                remember_me_parameter: remember_me
    role_hierarchy:
        ROLE_ADMIN:
            - ROLE_BLOG_ADMIN
            - ROLE_CONTENT_ADMIN
            - ROLE_DC_ADMIN
            - ROLE_FEEDBACK_ADMIN
            - ROLE_MEDIA_ADMIN
            - ROLE_USER_ADMIN
            - ROLE_USER

    # Easy way to control access for large sections of your site
    # Note: Only the *first* access control that matches will be used
    access_control:
        # Default controller stuff - open to the public
        - { path: ^/$, roles: PUBLIC_ACCESS }
        - { path: ^/privacy$, roles: PUBLIC_ACCESS }

        # user controller stuff - open to the public
        - { path: ^/request$, roles: PUBLIC_ACCESS }
        - { path: ^/reset, roles: PUBLIC_ACCESS }
        - { path: ^/login$, roles: PUBLIC_ACCESS }

        - { path: ^/editor/upload, roles: PUBLIC_ACCESS }

        # media bundle
        - { path: ^/audio, roles: PUBLIC_ACCESS }
        - { path: ^/image, roles: PUBLIC_ACCESS }
        - { path: ^/pdf, roles: PUBLIC_ACCESS }

        # keep the rest of the site private
        - { path: ^/, roles: ROLE_USER }
```
