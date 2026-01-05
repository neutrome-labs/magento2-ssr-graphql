# Agent Guidelines for magento2-ssr-graphql

> **⚠️ IMPORTANT**: After making code changes, always review and update this file if your changes affect the documented structure, key files, configuration, architecture, or important notes. Keep this file in sync with the codebase.

## Project Overview

This is a **Magento 2 module** that enables Server-Side Rendering (SSR) of GraphQL queries. It allows GraphQL data to be resolved during PHP page render and passed to JavaScript, eliminating the initial Ajax request while maintaining client-side refresh capabilities.

## Directory Structure

```
src/SsrGraphql/
├── Api/                    # Service contracts (interfaces)
├── Controller/             # Frontend controllers
├── etc/                    # Module configuration
│   ├── adminhtml/          # Admin panel config (system.xml)
│   └── frontend/           # Frontend routes
├── Model/                  # Business logic implementations
│   └── Framework/          # ObjectManager customizations
├── Service/                # Service layer classes
├── ViewModel/              # View models for templates
└── view/frontend/          # Frontend assets
    ├── layout/             # Layout XML files
    └── templates/          # PHTML templates
```

## Key Files

| File | Purpose |
|------|---------|
| `Api/ResolverInterface.php` | Contract for GraphQL resolution |
| `Model/Resolver.php` | Core SSR GraphQL resolver |
| `ViewModel/SsrGraphqlViewModel.php` | Exposes `makeSsrGqlCall()` to templates |
| `view/frontend/templates/script.phtml` | JS runtime (fetchMagento2Gql, createMagento2SsrGqlStub) |
| `Service/ConfigManager.php` | Admin config accessor |

## Coding Standards

- **PHP**: Follow Magento 2 coding standards (PSR-12 based)
- **Namespace**: `NeutromeLabs\SsrGraphql\`
- **Module Name**: `NeutromeLabs_SsrGraphql`
- **Config Path**: `neutromelabs_ssrgraphql/`

## Architecture Decisions

### Separate ObjectManager for GraphQL
The module creates a dedicated ObjectManager configured for `Area::AREA_GRAPHQL` to properly resolve GraphQL queries from within a frontend context. See `Model/Framework/SsrGraphqlObjectManagerFactory.php`.

### ViewModel Pattern
All template data should flow through `SsrGraphqlViewModel`. Do not inject services directly into templates.

### JavaScript API
The JS runtime provides:
- `window.fetchMagento2Gql(query, variables, init)` - Raw GraphQL fetch
- `window.createMagento2SsrGqlStub(query, variables, response)` - Creates SSR stub object

## Configuration

| Path | Type | Description |
|------|------|-------------|
| `neutromelabs_ssrgraphql/general/debug` | bool | Enables debug logging and example route |

## Common Tasks

### Adding a New SSR GraphQL Call in a Template
```php
/** @var SsrGraphqlViewModel $gql */
$gql = $block->getData('ssrGqlViewModel');
?>
<script>
var myData = <?= $gql->makeSsrGqlCall($query, $variables) ?>;
</script>
```

### Modifying GraphQL Resolution
Extend or replace `NeutromeLabs\SsrGraphql\Api\ResolverInterface` via `di.xml` preference.

## Testing

- Example page available at `/ssrgql/example` (requires debug mode enabled)
- Check browser console for SSR GraphQL debug output when debug mode is on

## Dependencies

- `magento/framework`
- `magento/module-graph-ql`

## Important Notes

1. **Do not modify** `script.phtml` JS functions signature - external code depends on them
2. **GraphQL queries** must be valid Magento GraphQL schema queries
3. **Security**: All data passed to `makeSsrGqlCall` is JSON encoded and output to page
4. **Performance**: SSR queries execute synchronously during page render - keep them lightweight
5. **The example controller** at `Controller/Example/Index.php` has incomplete 404 handling (known issue)

## Version

Current: `0.1.1` (see composer.json)
